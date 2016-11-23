<?php
/* --------------------------------------------------------------
   FieldReplaceJobContentView.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FieldReplaceJobContentView
 */
class FieldReplaceJobContentView extends ContentView
{
	protected $page_token;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/timer/');
		$this->set_content_template('field_replace_jobs.html');
	}


	public function prepare_data()
	{
		/* @var ShopLanguageReader $shopLanguageReader */
		$shopLanguageReader = MainFactory::create_object('ShopLanguageReader');

		/* @var FieldReplaceJobReader $jobReader */
		$jobReader = MainFactory::create_object('FieldReplaceJobReader', array($shopLanguageReader));
		$jobsArray = $jobReader->getAll();

		$jobsDataArray = array();

		/* @var JobQueueReader $jobQueueReader */
		$jobQueueReader = MainFactory::create_object('JobQueueReader');

		/* @var FieldReplaceJob $job */
		foreach($jobsArray as $job)
		{
			/* @var WaitingTicket $waitingTicket */
			$waitingTicket = $jobQueueReader->getWaitingTicketById($job->getWaitingNumber());

			$jobsDataArray[$job->getFieldReplaceJobId()] = array('job' => $job, 'ticket' => $waitingTicket);
		}

		$this->set_content_data('jobs_data_array', $jobsDataArray);
		$this->set_content_data('language_reader', $shopLanguageReader->getAll());

		$this->set_content_data('shipping_status_array', $this->_buildShippingStatusArray());
		$this->set_content_data('price_status_array', $this->_buildPriceStatusArray());
		$this->set_content_data('page_token', $this->page_token);

		$token = LogControl::get_secure_token();
		$token = md5($token);
		$url   = HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=RunJobs&token=' . $token;
		
		$this->set_content_data('cronjob_url', $url);
	}
	
	
	/**
	 * @return array
	 */
	protected function _buildShippingStatusArray()
	{
		return $this->_buildOptionsArray(xtc_get_shipping_status());
	}


	/**
	 * @return array
	 */
	protected function _buildPriceStatusArray()
	{
		/** @var LanguageTextManager $langFileMaster */
		$langFileMaster = MainFactory::create_object('LanguageTextManager', array(), true);
		$langFileMaster->init_from_lang_file('lang/' . basename($_SESSION['language']) . '/admin/categories.php');

		$statusArray = array();
		$statusArray[] = array('id' => 0, 'text' => GM_PRICE_STATUS_0);
		$statusArray[] = array('id' => 1, 'text' => GM_PRICE_STATUS_1);
		$statusArray[] = array('id' => 2, 'text' => GM_PRICE_STATUS_2);

		return $this->_buildOptionsArray($statusArray);
	}


	/**
	 * @param $dataArray
	 *
	 * @return array
	 */
	protected function _buildOptionsArray($dataArray)
	{
		$optionsArray = array();

		foreach($dataArray as $valueArray)
		{
			$optionsArray[] = array('id' => $valueArray['id'], 'name' => $valueArray['text']);
		}

		return $optionsArray;
	}


	/**
	 * @param $p_pageToken
	 */
	public function setPageToken($p_pageToken)
	{
		$this->page_token = (string)$p_pageToken;
	}
}