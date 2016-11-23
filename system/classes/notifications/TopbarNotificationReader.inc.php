<?php
/* --------------------------------------------------------------
   TopbarNotificationReader.inc.php 2014-10-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class TopbarNotificationReader
 */
class TopbarNotificationReader
{
	/**
	 * @return TopbarNotification
	 */
	public function getTopbarNotification()
	{
		$mysqlResult = xtc_db_query($this->_getQuery());
		
		/* @var TopbarNotification $topbarNotification */
		$topbarNotification = $this->_createTopbarNotificationByResultSet($mysqlResult);
		$this->_setTopbarNotificationColor($topbarNotification, gm_get_conf('TOPBAR_NOTIFICATION_COLOR'));
		$this->_setTopbarNotificationActiveStatus($topbarNotification, (gm_get_conf('TOPBAR_NOTIFICATION_STATUS') ? true : false));
		$this->_setTopbarNotificationMode($topbarNotification, gm_get_conf('TOPBAR_NOTIFICATION_MODE'));
		
		return $topbarNotification;
	}


	/**
	 * @param TopbarNotification $topbarNotification
	 * @param string             $p_color
	 */
	protected function _setTopbarNotificationColor(TopbarNotification $topbarNotification, $p_color)
	{
		$topbarNotification->setColor($p_color);
	}


	/**
	 * @param TopbarNotification $topbarNotification
	 * @param bool               $p_is_active
	 */
	protected function _setTopbarNotificationActiveStatus(TopbarNotification $topbarNotification, $p_is_active)
	{
		$topbarNotification->setActive($p_is_active);
	}


	/**
	 * @param TopbarNotification $topbarNotification
	 * @param string             $p_mode
	 */
	protected function _setTopbarNotificationMode(TopbarNotification $topbarNotification, $p_mode)
	{
		$topbarNotification->setMode($p_mode);
	}


	/**
	 * @param $p_mysqlResult
	 *
	 * @return TopbarNotification
	 * @throws UnexpectedValueException
	 */
	protected function _createTopbarNotificationByResultSet($p_mysqlResult)
	{
		if($p_mysqlResult !== false)
		{
			/** @var TopbarNotification $topbarNotification */
			$topbarNotification = MainFactory::create_object('TopbarNotification');
			
			while($row = xtc_db_fetch_array($p_mysqlResult))
			{
				$topbarNotification->setId($row['shop_notice_id']);	
				$topbarNotification->setContentByLanguageId($row['content'], $row['language_id']);
			}
		}
		else
		{
			throw new UnexpectedValueException('$p_mysqlResult is not a valid mysql resource');
		}
		
		return $topbarNotification;
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
						a.notice_type = "topbar"';

		return $query;
	}
} 