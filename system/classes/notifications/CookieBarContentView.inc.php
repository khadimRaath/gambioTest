<?php

/* --------------------------------------------------------------
   CookieBarContentView.inc.php 2016-04-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

class CookieBarContentView extends ContentView
{
	protected $cookieBarStatus;
	protected $cookieAlreadySet;
	protected $cookieBarPosition;
	protected $cookieBarColor;
	protected $cookieBarTransparency;
	protected $cookieBarCloseIconStatus;
	protected $cookieBarButtonText;
	protected $cookieBarButtonLink;
	protected $cookieBarButtonTextColor;
	protected $cookieBarButtonColor;
	protected $cookieBarContent;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/cookie_bar.html');
	}
	
	
	public function prepare_data()
	{
		$this->_getCookieBarButtonColor();
		$this->_getCookieBarButtonLink();
		$this->_getCookieBarButtonText();
		$this->_getCookieBarButtonTextColor();
		$this->_getCookieBarCloseIconStatus();
		$this->_getCookieBarColor();
		$this->_getCookieBarContent();
		$this->_getCookieBarPosition();
		$this->_getCookieBarStatus();
		$this->_getCookieBarTransparency();
		$this->_getCookieAlreadySet();

		$this->set_content_data('active', $this->cookieBarStatus);
		$this->set_content_data('cookieAlreadySet', $this->cookieAlreadySet);
		$this->set_content_data('position', $this->cookieBarPosition);
		$this->set_content_data('background_color', $this->cookieBarColor);
		$this->set_content_data('background_opacity', $this->cookieBarTransparency);
		$this->set_content_data('button_background_color', $this->cookieBarButtonColor);
		$this->set_content_data('button_text_color', $this->cookieBarButtonTextColor);
		$this->set_content_data('close_button_active', $this->cookieBarCloseIconStatus);
		
		$this->content_array['text'] = $this->cookieBarContent;
		$this->content_array['button_link'] = $this->cookieBarButtonLink;
		$this->content_array['button_text'] = $this->cookieBarButtonText;
	}
	
	
	protected function _getCookieBarStatus()
	{
		$this->cookieBarStatus = gm_get_conf('GM_COOKIE_STATUS') === 'true';
	}


	protected function _getCookieAlreadySet()
	{
		$this->cookieAlreadySet = array_key_exists('hideCookieBar', $_COOKIE);
	}


	protected function _getCookieBarPosition()
	{
		$this->cookieBarPosition = gm_get_conf('GM_COOKIE_POSITION');
	}
	
	
	protected function _getCookieBarColor()
	{
		$this->cookieBarColor = gm_get_conf('GM_COOKIE_COLOR');
	}
	
	
	protected function _getCookieBarTransparency()
	{
		$this->cookieBarTransparency = gm_get_conf('GM_COOKIE_TRANSPARENCY');
	}
	
	
	protected function _getCookieBarCloseIconStatus()
	{
		$this->cookieBarCloseIconStatus = gm_get_conf('GM_COOKIE_CLOSE_ICON') === 'true';
	}
	
	
	protected function _getCookieBarButtonText()
	{
		$this->cookieBarButtonText = gm_get_content('GM_COOKIE_BUTTON_TEXT', $_SESSION['languages_id']);
	}
	
	
	protected function _getCookieBarButtonLink()
	{
		$this->cookieBarButtonLink = gm_get_content('GM_COOKIE_BUTTON_LINK', $_SESSION['languages_id']);
	}
	
	
	protected function _getCookieBarButtonTextColor()
	{
		$this->cookieBarButtonTextColor = gm_get_conf('GM_COOKIE_BUTTON_TEXT_COLOR');
	}
	
	
	protected function _getCookieBarButtonColor()
	{
		$this->cookieBarButtonColor = gm_get_conf('GM_COOKIE_BUTTON_COLOR');
	}
	
	
	protected function _getCookieBarContent()
	{
		$this->cookieBarContent = gm_get_content('GM_COOKIE_CONTENT', $_SESSION['languages_id']);
	}
}