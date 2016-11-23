<?php
/* --------------------------------------------------------------
   ShopNoticeJobContentView.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ShopNoticeJobContentView
 */
class ShopNoticeJobContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/shop_offline/');
		$this->set_content_template('shop_notice_jobs.html');
	}


	public function prepare_data()
	{
		/* @var ShopLanguageReader $shopLanguageReader */
		$shopLanguageReader = MainFactory::create_object('ShopLanguageReader');
		
		/* @var ShopNoticeJobReader $jobReader */
		$jobReader = MainFactory::create_object('ShopNoticeJobReader', array($shopLanguageReader));
		$jobsArray = $jobReader->getAll();

		$jobsDataArray = array();

		/* @var JobQueueReader $jobQueueReader */
		$jobQueueReader = MainFactory::create_object('JobQueueReader');
		
		/* @var ShopNoticeJob $job */
		foreach($jobsArray as $job)
		{
			/* @var WaitingTicket $waitingTicket */
			$waitingTicket = $jobQueueReader->getWaitingTicketById($job->getWaitingNumber());
			
			$jobsDataArray[$job->getShopNoticeJobId()] = array('job' => $job, 'ticket' => $waitingTicket);
		}
		
		$this->set_content_data('jobs_data_array', $jobsDataArray);
		$this->set_content_data('language_reader', $shopLanguageReader->getAll());

		$token = LogControl::get_secure_token();
		$token = md5($token);
		$url   = HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=RunJobs&token=' . $token;

		$this->set_content_data('cronjob_url', $url);
	}
} 