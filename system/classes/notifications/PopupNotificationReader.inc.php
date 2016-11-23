<?php
/* --------------------------------------------------------------
   PopupNotificationReader.inc.php 2014-10-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PopupNotificationReader
 */
class PopupNotificationReader
{
	/**
	 * @return PopupNotification
	 */
	public function getPopupNotification()
	{
		$mysqlResult = xtc_db_query($this->_getQuery());

		/* @var PopupNotification $popupNotification */
		$popupNotification = $this->_createPopupNotificationByResultSet($mysqlResult);
		$this->_setPopupNotificationActiveStatus($popupNotification, (gm_get_conf('POPUP_NOTIFICATION_STATUS') ? true : false));

		return $popupNotification;
	}


	/**
	 * @param PopupNotification $popupNotification
	 * @param bool               $p_is_active
	 */
	protected function _setPopupNotificationActiveStatus(PopupNotification $popupNotification, $p_is_active)
	{
		$popupNotification->setActive($p_is_active);
	}


	/**
	 * @param $p_mysqlResult
	 *
	 * @return PopupNotification
	 * @throws UnexpectedValueException
	 */
	protected function _createPopupNotificationByResultSet($p_mysqlResult)
	{
		if($p_mysqlResult !== false)
		{
			/** @var PopupNotification $popupNotification */
			$popupNotification = MainFactory::create_object('PopupNotification');

			while($row = xtc_db_fetch_array($p_mysqlResult))
			{
				$popupNotification->setId($row['shop_notice_id']);
				$popupNotification->setContentByLanguageId($row['content'], $row['language_id']);
			}
		}
		else
		{
			throw new UnexpectedValueException('$p_mysqlResult is not a valid mysql resource');
		}

		return $popupNotification;
	}


	/**
	 * @return string
	 */
	protected function _getQuery()
	{
		$query = 'SELECT 
						a.shop_notice_id,
						b.language_id,
						b.content
					FROM
						shop_notices a,
						shop_notice_contents b
					WHERE 
						a.shop_notice_id = b.shop_notice_id AND
						a.notice_type = "popup"';

		return $query;
	}
} 