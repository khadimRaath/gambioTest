<?php
/* --------------------------------------------------------------
   Captcha.inc.php 2015-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Captcha
 */
class Captcha 
{
	protected $captchaType = '';
	protected $publicKey = '';
	protected $privateKey = '';
	protected $vvCode = '';
	protected $vvCodeName = '';
	protected $captchaTheme = '';
	protected $error = '';
	protected $resultHtml = '';
	
	
	// ######### CONSTRUCTOR #########


	/**
	 * @param string $vvCodeName
	 * @param string $captchaTheme
	 * @param int    $vvCodeLength
	 */
	public function __construct($vvCodeName = 'vvcode', $captchaTheme = 'white', $vvCodeLength = 6)
	{
		$this->publicKey = gm_get_conf('GM_RECAPTCHA_PUBLIC_KEY');
		$this->privateKey = gm_get_conf('GM_RECAPTCHA_PRIVATE_KEY');
		$this->vvCodeName = $vvCodeName;
		$this->captchaTheme = $captchaTheme;

		if($this->recaptcha_active())
		{
			$this->captchaType = 'recaptcha';
		}
		
		else
		{
			$this->captchaType = 'vvCode';
			$this->reload_vv_code($vvCodeLength);
		}
	}


	// ######### PUBLIC METHODS #########


	/**
	 * @param int $vvCodeLength
	 */
	public function reload_vv_code($vvCodeLength = 6)
	{
		include_once(DIR_FS_INC . 'xtc_random_charcode.inc.php');
		$this->vvCode = $_SESSION['vvcode'];
		$vvCode = xtc_random_charcode($vvCodeLength);
		$_SESSION['vvcode'] = $vvCode;
	}


	/**
	 * @return bool
	 */
	public function recaptcha_active()
	{
		return gm_get_conf('GM_CAPTCHA_TYPE') == 'recaptcha';
	}


	/**
	 * @param array  $requestData
	 * @param string $section
	 * @param bool   $isAjaxRequest
	 *
	 * @return bool
	 */
	public function is_valid($requestData, $section = '', $isAjaxRequest = false)
	{
		$sectionIsSecured = gm_get_conf($section);

		if($sectionIsSecured === 'false')
		{
			return true;
		}

		switch ($this->captchaType)
		{
			case 'recaptcha':

				return $this->_validateCaptchaTypeRecaptcha($requestData);

			case 'vvCode':

				return $this->_validateCaptchaTypeVvcode($requestData, $isAjaxRequest);

			default:
				return false;
		}
	}


	/**
	 * @return string
	 */
	public function get_html()
	{
		$this->prepare_data();
		return $this->resultHtml;
	}


	// ######### PROTECTED METHODS #########


	/**
	 * @return string
	 */
	protected function prepare_data()
	{
		switch ($this->captchaType)
		{
			case 'recaptcha':
				$this->_getResultHtmlRecaptcha();
				break;

			case 'vvCode':
				$this->_getResultHtmlVvCode();
				break;

			default:
				return '';
		}
	}


	/**
	 * Gets the resulting HTML for reCaptcha and saves it to $resultHtml
	 */
	protected function _getResultHtmlRecaptcha()
	{
		include_once(DIR_FS_CATALOG . 'includes/recaptchalib.php');
		$html = recaptcha_get_html($this->publicKey, $this->error, true);
		$this->error = '';
		
		$contentView = MainFactory::create('CaptchaContentView');
		$contentView->setIsRecaptcha(true);
		$contentView->setCaptchaTheme($this->captchaTheme);
		$contentView->setPublicKey($this->publicKey);
		$contentView->setRecaptchaHtml($html);

		$this->setResultHtml($contentView->get_html());
	}


	/**
	 * Gets the resulting HTML for vvCode Captcha and saves it to $resultHtml
	 */
	protected function _getResultHtmlVvCode()
	{
		$contentView = MainFactory::create('CaptchaContentView');
		$contentView->setCaptchaName($this->vvCodeName);
		$contentView->setCaptchaUrl(xtc_href_link('request_port.php', 'rand=' . rand() . '&module=CreateVVCode', 'SSL', true, false));
		
		$this->setResultHtml($contentView->get_html());
	}


	/**
	 * @param array $requestData
	 *
	 * @return bool
	 */
	protected function _validateCaptchaTypeRecaptcha(array $requestData)
	{
		if(empty($requestData['recaptcha_response_field']) || empty($requestData['recaptcha_challenge_field']))
		{
			return false;
		}

		include_once(DIR_FS_CATALOG . 'includes/recaptchalib.php');
		$response = recaptcha_check_answer($this->privateKey, $_SERVER["REMOTE_ADDR"], $requestData['recaptcha_challenge_field'], $requestData['recaptcha_response_field']);
		
		if(!$response->is_valid)
		{
			$this->error = $response->error;
			return false;
		}
		
		return true;
	}


	/**
	 * @param array $requestData
	 * @param bool $isAjaxRequest
	 * 
	 * @return bool
	 */
	protected function _validateCaptchaTypeVvcode(array $requestData, $isAjaxRequest)
	{
		if(empty($requestData[$this->vvCodeName]))
		{
			return false;
		}
		
		$vvCode = $this->vvCode;
		$this->vvCode = $_SESSION['vvcode'];
		
		if($isAjaxRequest)
		{
			$vvCode = $this->vvCode;
		}
		
		return strtoupper($requestData[$this->vvCodeName]) == $vvCode;
	}


	// ######### SETTER #########
	/**
	 * @param string $resultHtml
	 */
	public function setResultHtml($resultHtml)
	{
		$this->resultHtml = (string)$resultHtml;
	}
}
