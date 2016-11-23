<?php

/* --------------------------------------------------------------
   ShopNoticeJobWriter.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShopNoticeJobWriter
{
	protected $shopLanguageReader;


	public function __construct(ShopLanguageReader $shopLanguageReader)
	{
		$this->shopLanguageReader = $shopLanguageReader;
	}


	public function write(ShopNoticeJob $noticeJob)
	{
		if($noticeJob->getShopNoticeJobId() == null)
		{
			$this->insert($noticeJob);
		}
		else
		{
			$this->update($noticeJob);
		}
	}


	protected function insert(ShopNoticeJob $noticeJob)
	{
		$sql = '
			INSERT INTO shop_notice_jobs
			SET
				waiting_ticket_id  = "' . xtc_db_input($noticeJob->getWaitingNumber()) . '",
				shop_active = "' . xtc_db_input($noticeJob->getShopActive()) . '",
				shop_offline_content = "' . xtc_db_input($noticeJob->getShopOfflineContent()) . '",
				topbar_active = "' . xtc_db_input($noticeJob->getTopbarActive()) . '",
				topbar_color = "' . xtc_db_input($noticeJob->getTopbarColor()) . '",
				topbar_mode = "' . xtc_db_input($noticeJob->getTopbarMode()) . '",
				popup_active = "' . xtc_db_input($noticeJob->getPopupActive()) . '",
				hidden = "' . (int)$noticeJob->getHidden() . '"
		';
		xtc_db_query($sql);

		$shopNoticeJobId = xtc_db_insert_id();
		$noticeJob->setShopNoticeJobId($shopNoticeJobId);


		$shopLanguageArray = $this->shopLanguageReader->getAll();

		foreach($shopLanguageArray as $shopLanguage)
		{
			$languageId   = $shopLanguage->getLanguageId();
			$languageCode = $shopLanguage->getLanguageCode();

			$sql = '
				INSERT INTO shop_notice_job_contents
				SET
					shop_notice_job_id  = "' . xtc_db_input($shopNoticeJobId) . '",
					language_id = "' . xtc_db_input($languageId) . '",
					topbar_content = "' . xtc_db_input($noticeJob->getTopbarContent($languageCode)) . '",
					popup_content = "' . xtc_db_input($noticeJob->getPopupContent($languageCode)) . '"
			';
			xtc_db_query($sql);
		}

		return $noticeJob;
	}


	protected function update(ShopNoticeJob $noticeJob)
	{
		$sql = '
			UPDATE shop_notice_jobs
			SET
				waiting_ticket_id  = "' . xtc_db_input($noticeJob->getWaitingNumber()) . '",
				shop_active = "' . xtc_db_input($noticeJob->getShopActive()) . '",
				shop_offline_content = "' . xtc_db_input($noticeJob->getShopOfflineContent()) . '",
				topbar_active = "' . xtc_db_input($noticeJob->getTopbarActive()) . '",
				topbar_color = "' . xtc_db_input($noticeJob->getTopbarColor()) . '",
				topbar_mode = "' . xtc_db_input($noticeJob->getTopbarMode()) . '",
				popup_active = "' . xtc_db_input($noticeJob->getPopupActive()) . '",
				hidden = "' . (int)$noticeJob->getHidden() . '"
			WHERE
				shop_notice_job_id = "' . xtc_db_input($noticeJob->getShopNoticeJobId()) . '"
		';
		xtc_db_query($sql);


		$shopLanguageArray = $this->shopLanguageReader->getAll();

		foreach($shopLanguageArray as $shopLanguage)
		{
			$languageId   = $shopLanguage->getLanguageId();
			$languageCode = $shopLanguage->getLanguageCode();

			$sql = '
				UPDATE shop_notice_job_contents
				SET
					topbar_content = "' . xtc_db_input($noticeJob->getTopbarContent($languageCode)) . '",
					popup_content = "' . xtc_db_input($noticeJob->getPopupContent($languageCode)) . '"
				WHERE
					shop_notice_job_id  = "' . xtc_db_input($noticeJob->getShopNoticeJobId()) . '" AND
					language_id = "' . xtc_db_input($languageId) . '"
			';
			xtc_db_query($sql);
		}

		return true;
	}

} 