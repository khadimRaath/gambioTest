<?php
/* --------------------------------------------------------------
   NotificationsContentView.inc.php 2016-06-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class NotificationsContentView
 */
class NotificationsContentView extends ContentView
{
	protected $pageToken;
	protected $languageCode;
	protected $contentDataTopbar;
	protected $contentDataTopbarArray = array();
	protected $contentDataPopupArray = array();
	protected $languagesArray = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/shop_offline/');
		$this->set_content_template('notifications.html');
	}


	public function prepare_data()
	{
		$this->set_content_data('offline_message', xtc_draw_textarea_field('offline_content', 'soft', '100', '10', gm_get_conf('GM_SHOP_OFFLINE_MSG', 'ASSOC', true), 'data-language_switcher-ignore'));
		
		$this->set_content_data('popup_message_content', $this->contentDataPopupArray);
		$this->set_content_data('popup_message', xtc_draw_textarea_field('popup_msg[0]', 'soft', '100', '10', $this->contentDataPopup));

		$this->set_content_data('topbar_message_content', $this->contentDataTopbarArray);
		$this->set_content_data('topbar_message', xtc_draw_textarea_field('topbar_msg[0]', 'soft', '100', '10', $this->contentDataTopbar));

		$this->set_content_data('languages_array', array());
		
		$offlineWysiwyg		= (USE_WYSIWYG == 'true') ? xtc_wysiwyg('offline_content', $this->languageCode, 'offline_content') : "";

		$this->set_content_data('languages_array', $this->languagesArray);
		
		$this->set_content_data('offline_editor', $offlineWysiwyg);
		
		$topbarChecked = (gm_get_conf('TOPBAR_NOTIFICATION_STATUS', 'ASSOC', true) == 1) ? 'checked' : '';
		$popupChecked = (gm_get_conf('POPUP_NOTIFICATION_STATUS', 'ASSOC', true) == 1) ? 'checked' : '';
		
		$this->set_content_data('shop_offline_mode', gm_get_conf('GM_SHOP_OFFLINE', 'ASSOC', true));
		$this->set_content_data('topbar_checked', $topbarChecked);
		$this->set_content_data('popup_checked', $popupChecked);
		$this->set_content_data('topbar_color', gm_get_conf('TOPBAR_NOTIFICATION_COLOR'));
		$this->set_content_data('topbar_mode', gm_get_conf('TOPBAR_NOTIFICATION_MODE'));
		
		// Hide configuration to make topbar permanent on Honeygrid based templates
		if (gm_get_env_info('TEMPLATE_VERSION') >= 3) 
		{
			$this->set_content_data('hide_topbar_mode', true);
		}
		else 
		{
			$this->set_content_data('hide_topbar_mode', false);
		}
		
		$this->set_content_data('page_token', $this->pageToken);
	}


	/**
	 * @return string
	 */
	public function getPageToken()
	{
		return $this->pageToken;
	}

	/**
	 * @param array $p_contentDataTopbarArray
	 */
	public function setContentDataTopbarArray($p_contentDataTopbarArray)
	{
		$this->contentDataTopbarArray = $p_contentDataTopbarArray;
	}

	/**
	 * @param string $p_contentDataTopbar
	 */
	public function setContentDataTopbar($p_contentDataTopbar)
	{
		$this->contentDataTopbar = (string)$p_contentDataTopbar;
	}

	/**
	 * @param array $p_contentDataPopupArray
	 */
	public function setContentDataPopupArray($p_contentDataPopupArray)
	{
		$this->contentDataPopupArray = $p_contentDataPopupArray;
	}

	/**
	 * @param string $p_contentDataPopup
	 */
	public function setContentDataPopup($p_contentDataPopup)
	{
		$this->contentDataPopup = $p_contentDataPopup;
	}

	/**
	 * @param string $p_pageToken
	 */
	public function setPageToken($p_pageToken)
	{
		$this->pageToken = (string)$p_pageToken;
	}


	/**
	 * @return string
	 */
	public function getLanguageCode()
	{
		return $this->languageCode;
	}


	/**
	 * @param string $p_languageCode
	 */
	public function setLanguageCode($p_languageCode)
	{
		$this->languageCode = (string)$p_languageCode;
	}


	/**
	 * @param array $p_languagesArray
	 */
	public function setLanguagesArray(array $p_languagesArray)
	{
		$this->languagesArray = $p_languagesArray;
	}
} 