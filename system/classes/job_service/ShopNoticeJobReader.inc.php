<?php
/* --------------------------------------------------------------
   ShopNoticeJobReader.inc.php 2014-11-18 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ShopNoticeJobReader
 */
class ShopNoticeJobReader
{
	/**
	 * @var ShopLanguageReader
	 */
	protected $shopLanguageReader;


	/**
	 * @param ShopLanguageReader $shopLanguageReader
	 */
	public function __construct(ShopLanguageReader $shopLanguageReader)
	{
		$this->shopLanguageReader = $shopLanguageReader;
	}


	/**
	 * @param int $p_id
	 *
	 * @return ShopNoticeJob
	 */
	public function getById($p_id)
	{
		$sql      = '
			SELECT *
			FROM shop_notice_jobs
			WHERE
				shop_notice_job_id = "' . (int)$p_id . '"
		';
		$job = $this->_getBySqlQuery($sql);

		return $job;
	}


	/**
	 * @param int $p_waitingNumber
	 *
	 * @return ShopNoticeJob
	 */
	public function getByWaitingNumber($p_waitingNumber)
	{
		$sql      = '
			SELECT *
			FROM shop_notice_jobs
			WHERE
				waiting_ticket_id = "' . (int)$p_waitingNumber . '"
		';
		$job = $this->_getBySqlQuery($sql);

		return $job;
	}


	/**
	 * @param bool $p_include_hidden_jobs
	 *
	 * @return array
	 */
	public function getAll($p_include_hidden_jobs = false)
	{
		if($p_include_hidden_jobs)
		{
			$sql = 'SELECT * FROM shop_notice_jobs';
		}
		else
		{
			$sql = 'SELECT * FROM shop_notice_jobs WHERE hidden = 0';
		}
		
		$jobsArray = $this->_getArrayBySqlQuery($sql);

		return $jobsArray;
	}


	/**
	 * @param string $p_sql
	 *
	 * @return ShopNoticeJob
	 * @throws Exception
	 */
	protected function _getBySqlQuery($p_sql)
	{
		$objectArray = $this->_getArrayBySqlQuery($p_sql);
		
		if(sizeof($objectArray) > 1)
		{
			throw new Exception('Multiple rows found');
		}
		elseif(sizeof($objectArray) === 0)
		{
			throw new Exception("Query\r\n\r\n" . $p_sql . "\r\n\r\n has no result.");
		}
		
		return $objectArray[0];
	}


	/**
	 * @param string $p_sql
	 *
	 * @return array
	 */
	protected function _getArrayBySqlQuery($p_sql)
	{
		$jobArray = array();

		$result = xtc_db_query($p_sql);

		while($jobData = xtc_db_fetch_array($result))
		{
			/* @var ShopNoticeJob $job */
			$job = MainFactory::create_object('ShopNoticeJob', array(MainFactory::create_object('ShopLanguageReader')));

			$job->setShopNoticeJobId($jobData['shop_notice_job_id']);
			$job->setWaitingNumber($jobData['waiting_ticket_id']);

			$job->setShopActive((bool)$jobData['shop_active']);
			$job->setShopOfflineContent($jobData['shop_offline_content']);

			$job->setTopbarActive((bool)$jobData['topbar_active']);
			$job->setTopbarColor($jobData['topbar_color']);
			$job->setTopbarMode($jobData['topbar_mode']);

			$job->setPopupActive((bool)$jobData['popup_active']);


			$shopLanguageArray = $this->shopLanguageReader->getAll();

			foreach($shopLanguageArray as $shopLanguage)
			{
				$languageId   = $shopLanguage->getLanguageId();
				$languageCode = $shopLanguage->getLanguageCode();

				$sql            = '
					SELECT *
					FROM
						shop_notice_job_contents
					WHERE
						shop_notice_job_id  = "' . xtc_db_input($job->getShopNoticeJobId()) . '" AND
						language_id = "' . xtc_db_input($languageId) . '"
				';
				$contentsResult = xtc_db_query($sql);
				$contentsData   = xtc_db_fetch_array($contentsResult);

				$job->setTopbarContent($languageCode, $contentsData['topbar_content']);
				$job->setPopupContent($languageCode, $contentsData['popup_content']);
			}
			$jobArray[] = $job;
		}

		return $jobArray;
	}
} 