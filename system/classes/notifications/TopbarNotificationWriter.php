<?php
/* --------------------------------------------------------------
   TopbarNotificationWriter.inc.php 2014-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

/**
 * Class TopbarNotificationWriter
 */
class TopbarNotificationWriter
{
	/**
	 * @param TopbarNotification $topbar
	 */
	public function save(TopbarNotification $topbar)
	{
		$this->_saveActive($topbar);
		$this->_saveColor($topbar);
		$this->_saveMode($topbar);

		$topbarContentsArray = $topbar->getContentArray(); 
		foreach(array_keys($topbarContentsArray) as $languageId)
		{
			$this->_saveShopNoticeContent($topbar, $languageId);
		}
	}


	/**
	 * @param TopbarNotification $topbar
	 */
	protected function _saveActive(TopbarNotification $topbar)
	{
		gm_set_conf('TOPBAR_NOTIFICATION_STATUS', $topbar->isActive() ? 1 : 0);
	}


	/**
	 * @param TopbarNotification $topbar
	 */
	protected function _saveColor(TopbarNotification $topbar)
	{
		gm_set_conf('TOPBAR_NOTIFICATION_COLOR', $topbar->getColor());
	}


	/**
	 * @param TopbarNotification $topbar
	 */
	protected function _saveMode(TopbarNotification $topbar)
	{
		$oldValue = gm_get_conf('TOPBAR_NOTIFICATION_MODE');
		gm_set_conf('TOPBAR_NOTIFICATION_MODE', $topbar->getMode());

		if ($oldValue !== $topbar->getMode()) {
			$this->_resetSession();
		}

	}


	/**
	 * @param TopbarNotification $topbar
	 * @param int                $languageId
	 */
	protected function _saveShopNoticeContent(TopbarNotification $topbar, $languageId)
	{

		/* @var TopbarNotificationReader $topbarNotificationReader */
		$topbarNotificationReader = MainFactory::create_object('TopbarNotificationReader');

		/* @var TopbarNotification $topbarNotification */
		$topbarNotification = $topbarNotificationReader->getTopbarNotification();

		$oldValue = $topbarNotification->getContentByLanguageId($languageId);
		$newValue = $this->_buildShopNoticeContentDataArray($topbar, $languageId);
		
		xtc_db_perform('shop_notice_contents', $newValue, 'replace');
		
		if ($oldValue !== $newValue['content']) {
			$this->_resetSession();
		}
	}


	/**
	 * @param TopbarNotification $topbar
	 * @param int                $languageId
	 *
	 * @return array
	 */
	protected function _buildShopNoticeContentDataArray(TopbarNotification $topbar, $languageId)
	{
		$contentArray = $topbar->getContentArray();
		
		$dataArray = array(
			'shop_notice_id' => (int)$topbar->getId(),
			'language_id' => (int)$languageId,
			'content' => isset($contentArray[$languageId]) ? $contentArray[$languageId] : ''
		);

		return $dataArray;
	}
	
	
	/**
	 * 
	 */
	protected function _resetSession()
	{
		$_SESSION['hide_topbar'] = false;
	}
}