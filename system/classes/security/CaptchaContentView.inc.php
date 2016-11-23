<?php
/* --------------------------------------------------------------
   CaptchaContentView.inc.php 2015-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CaptchaContentView
 */
class CaptchaContentView extends ContentView
{
	/**
	 * @var string $captchaName
	 */
	protected $captchaName = '';
	
	/**
	 * @var string $captchaTheme
	 */
	protected $captchaTheme = '';
	
	/**
	 * @var string $captchaUrl
	 */
	protected $captchaUrl = '';
	
	/**
	 * @var bool $isRecaptcha
	 */
	protected $isRecaptcha = false;
	
	/**
	 * @var string $publicKey
	 */
	protected $publicKey = '';
	
	/**
	 * @var string $recaptchaHtml
	 */
	protected $recaptchaHtml = '';
	
	
	public function prepare_data()
	{
		if($this->isRecaptcha)
		{
			$this->set_content_template('module/recaptcha.html');
			
			$this->set_content_data('SCRIPT', $this->recaptchaHtml);
			$this->set_content_data('PUBLIC_KEY', $this->publicKey);
			$this->set_content_data('THEME', $this->captchaTheme);
		}
		else
		{
			$this->set_content_template('module/captcha.html');
			
			$this->set_content_data('NAME', $this->captchaName);
			$this->set_content_data('URL', $this->captchaUrl);
		}
	}
	
	
	/**
	 * @param string $p_captchaName
	 */
	public function setCaptchaName($p_captchaName)
	{
		$this->captchaName = (string)$p_captchaName;
	}
	
	
	/**
	 * @param string $p_captchaTheme
	 */
	public function setCaptchaTheme($p_captchaTheme)
	{
		$this->captchaTheme = (string)$p_captchaTheme;
	}
	
	
	/**
	 * @param string $p_captchaUrl
	 */
	public function setCaptchaUrl($p_captchaUrl)
	{
		$this->captchaUrl = (string)$p_captchaUrl;
	}
	
	
	/**
	 * @param string $p_isRecaptcha
	 */
	public function setIsRecaptcha($p_isRecaptcha)
	{
		$this->isRecaptcha = (bool)$p_isRecaptcha;
	}
	
	
	/**
	 * @param string $p_publicKey
	 */
	public function setPublicKey($p_publicKey)
	{
		$this->publicKey = (string)$p_publicKey;
	}
	
	
	/**
	 * @param string $p_recaptchaHtml
	 */
	public function setRecaptchaHtml($p_recaptchaHtml)
	{
		$this->recaptchaHtml = (string)$p_recaptchaHtml;
	}
}