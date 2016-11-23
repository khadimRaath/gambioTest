<?php
/* --------------------------------------------------------------
   PopupNotificationWriter.inc.php 2014-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

class PopupNotificationWriter
{
	/**
	 * @param PopupNotification $popup
	 */
	public function save(PopupNotification $popup)
	{
		$this->_saveActive($popup);
		
		foreach(array_keys($popup->getContentArray()) as $languageId)
		{
			$this->_saveShopNoticeContent($popup, $languageId);
		}
	}


	/**
	 * @param PopupNotification $popup
	 */
	protected function _saveActive(PopupNotification $popup)
	{
		gm_set_conf('POPUP_NOTIFICATION_STATUS', $popup->isActive() ? 1 : 0);
	}


	/**
	 * @param PopupNotification $popup
	 * @param int               $languageId
	 */
	protected function _saveShopNoticeContent(PopupNotification $popup, $languageId)
	{

		/* @var PopupNotificationReader $popupNotificationReader */
		$popupNotificationReader = MainFactory::create_object('PopupNotificationReader');

		/* @var PopupNotification $popupNotification */
		$popupNotification = $popupNotificationReader->getPopupNotification();

		$oldValue = $popupNotification->getContentByLanguageId($languageId);
		$newValue = $this->_buildShopNoticeContentDataArray($popup, $languageId);
		
		xtc_db_perform('shop_notice_contents', $newValue, 'replace');

		if ($oldValue !== $newValue['content']) {
			$this->_resetSession();
		}

	}


	/**
	 * @param PopupNotification $popup
	 * @param int               $languageId
	 *
	 * @return array
	 */
	protected function _buildShopNoticeContentDataArray(PopupNotification $popup, $languageId)
	{
		$contentArray = $popup->getContentArray();

		$dataArray = array(
			'shop_notice_id' => (int)$popup->getId(),
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
		$_SESSION['hide_popup_notification'] = false;
	}
}