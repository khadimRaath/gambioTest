<?php
/* --------------------------------------------------------------
   FieldReplaceJob.inc.php 2014-10-09 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJob');

/**
 * Class ShopNoticeJob
 */
class ShopNoticeJob extends AbstractJob
{
	protected $shopNoticeJobId;

	protected $shopActive;
	protected $shopOfflineContent;

	protected $topbarActive;
	protected $topbarColor;
	protected $topbarMode;
	protected $topbarContentArray = array();

	protected $popupActive;
	protected $popupContentArray = array();
	
	protected $hidden;
	
	protected $shopLanguageReader;


	/**
	 * @param ShopLanguageReader $shopLanguageReader
	 */
	public function __construct(ShopLanguageReader $shopLanguageReader)
	{
		$this->shopLanguageReader = $shopLanguageReader;
	}


	/**
	 * @return bool
	 */
	public function execute()
	{
		$this->_executeOfflineJob();
		
		if($this->shopActive === true)
		{
			$this->_executeTopbarJob();
			$this->_executePopupJob();
		}
		
		return true;
	}


	/**
	 * @return bool
	 */
	protected function _executeOfflineJob()
	{
		$offlineStatus = '';
		if($this->shopActive === false)
		{
			$offlineStatus = 'checked';
			gm_set_conf('GM_SHOP_OFFLINE_MSG', $this->getShopOfflineContent());
		}
		
		gm_set_conf('GM_SHOP_OFFLINE', $offlineStatus);
		
		return true;
	}


	/**
	 * @return bool
	 */
	protected function _executeTopbarJob()
	{
		/* @var TopbarNotificationReader $topbarNotificationReader */
		$topbarNotificationReader = MainFactory::create_object('TopbarNotificationReader');

		/* @var TopbarNotification $topbarNotification */
		$topbarNotification = $topbarNotificationReader->getTopbarNotification();

		$topbarNotification->setActive($this->topbarActive);
		
		$topbarNotification->setColor($this->topbarColor);
		$topbarNotification->setMode($this->topbarMode);

		$shopLanguageArray = $this->shopLanguageReader->getAll();

		foreach($shopLanguageArray as $shopLanguage)
		{
			$languageId   = $shopLanguage->getLanguageId();
			$languageCode = $shopLanguage->getLanguageCode();

			$topbarNotification->setContentByLanguageId($this->topbarContentArray[$languageCode], $languageId);
		}

		/* @var TopbarNotificationWriter $topbarNotificationWriter */
		$topbarNotificationWriter = MainFactory::create_object('TopbarNotificationWriter');

		$topbarNotificationWriter->save($topbarNotification);
		
		return true;
	}


	/**
	 * @return bool
	 */
	protected function _executePopupJob()
	{
		/* @var PopupNotificationReader $popupNotificationReader */
		$popupNotificationReader = MainFactory::create_object('PopupNotificationReader');
		
		/* @var PopupNotification $popupNotification */
		$popupNotification = $popupNotificationReader->getPopupNotification();

		$popupNotification->setActive($this->popupActive);
		
		$shopLanguageArray = $this->shopLanguageReader->getAll();

		foreach($shopLanguageArray as $shopLanguage)
		{
			$languageId   = $shopLanguage->getLanguageId();
			$languageCode = $shopLanguage->getLanguageCode();

			$popupNotification->setContentByLanguageId($this->popupContentArray[$languageCode], $languageId);
		}

		/* @var PopupNotificationWriter $popupNotificationWriter */
		$popupNotificationWriter = MainFactory::create_object('PopupNotificationWriter');
		
		$popupNotificationWriter->save($popupNotification);
		
		return true;
	}


	/**
	 * @param $p_shopNoticeJobId
	 *
	 * @throws Exception
	 */
	public function setShopNoticeJobId($p_shopNoticeJobId)
	{
		$c_shopNoticeJobId = (int)$p_shopNoticeJobId;
		if($c_shopNoticeJobId < 1)
		{
			throw new Exception('Invalid Id: '. $p_shopNoticeJobId);
		}
		$this->shopNoticeJobId = $c_shopNoticeJobId;
	}


	/**
	 * @param $p_shopActive
	 *
	 * @throws Exception
	 */
	public function setShopActive($p_shopActive)
	{
		if(is_bool($p_shopActive) == false)
		{
			throw new Exception('Invalid status: '. print_r($p_shopActive, true));
		}
		$this->shopActive = $p_shopActive;
	}


	/**
	 * @param $p_shopOfflineContent
	 */
	public function setShopOfflineContent($p_shopOfflineContent)
	{
		$this->shopOfflineContent = $p_shopOfflineContent;
	}


	/**
	 * @param $p_topbarActive
	 */
	public function setTopbarActive($p_topbarActive)
	{
		$this->topbarActive = $p_topbarActive;
	}


	/**
	 * @param $p_topbarColor
	 */
	public function setTopbarColor($p_topbarColor)
	{
		$this->topbarColor = $p_topbarColor;
	}


	/**
	 * @param $p_topbarMode
	 */
	public function setTopbarMode($p_topbarMode)
	{
		$this->topbarMode = $p_topbarMode;
	}


	/**
	 * @param $p_languageCode
	 * @param $p_content
	 */
	public function setTopbarContent($p_languageCode, $p_content)
	{
		$this->topbarContentArray[$p_languageCode] = $p_content;
	}


	/**
	 * @param $p_popupActive
	 */
	public function setPopupActive($p_popupActive)
	{
		$this->popupActive = $p_popupActive;
	}


	/**
	 * @param $p_languageCode
	 * @param $p_content
	 */
	public function setPopupContent($p_languageCode, $p_content)
	{
		$this->popupContentArray[$p_languageCode] = $p_content;
	}


	/**
	 * @return int
	 */
	public function getShopNoticeJobId()
	{
		return $this->shopNoticeJobId;
	}


	/**
	 * @return bool
	 */
	public function getShopActive()
	{
		return $this->shopActive;
	}


	/**
	 * @return string
	 */
	public function getShopOfflineContent()
	{
		return $this->shopOfflineContent;
	}


	/**
	 * @return bool
	 */
	public function getTopbarActive()
	{
		return $this->topbarActive;
	}


	/**
	 * @return string
	 */
	public function getTopbarColor()
	{
		return $this->topbarColor;
	}


	/**
	 * @return string
	 */
	public function getTopbarMode()
	{
		return $this->topbarMode;
	}


	/**
	 * @param $p_languageCode
	 *
	 * @return string|bool
	 */
	public function getTopbarContent($p_languageCode)
	{
		if(isset($this->topbarContentArray[$p_languageCode]))
		{
			return $this->topbarContentArray[$p_languageCode];
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function getPopupActive()
	{
		return $this->popupActive;
	}


	/**
	 * @param $p_languageCode
	 *
	 * @return string|bool
	 */
	public function getPopupContent($p_languageCode)
	{
		if(isset($this->popupContentArray[$p_languageCode]))
		{
			return $this->popupContentArray[$p_languageCode];
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function getHidden()
	{
		return $this->hidden;
	}


	/**
	 * @param bool $p_hidden
	 */
	public function setHidden($p_hidden)
	{
		$this->hidden = (bool)$p_hidden;
	}
}
