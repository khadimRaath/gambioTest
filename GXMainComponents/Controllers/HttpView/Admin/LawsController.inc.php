<?php
/* --------------------------------------------------------------
   LawsController.inc.php 2016-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class LawsController
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class LawsController extends AdminHttpViewController
{
	/**
	 * Save Law Preferences
	 *
	 * This is a post callback that will need to store the data and then redirect back to the "Rights" page.
	 */
	public function actionSaveLawPreferences()
	{
		$this->_validatePageToken();
		
		if($_POST['GM_CHECK_PRIVACY_CALLBACK'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_CALLBACK', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_CALLBACK', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_GUESTBOOK'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_GUESTBOOK', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_GUESTBOOK', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_CONTACT'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_CONTACT', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_CONTACT', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_TELL_A_FRIEND'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_TELL_A_FRIEND', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_TELL_A_FRIEND', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_FOUND_CHEAPER'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_FOUND_CHEAPER', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_FOUND_CHEAPER', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_REVIEWS'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_REVIEWS', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_REVIEWS', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_ACCOUNT_CONTACT'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_CONTACT', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_CONTACT', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_CHECKOUT_SHIPPING'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING', 0);
		}
		
		if($_POST['GM_CHECK_PRIVACY_CHECKOUT_PAYMENT'] == 1)
		{
			gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT', 0);
		}
		
		if($_POST['GM_WITHDRAWAL_CONTENT_ID'])
		{
			gm_set_conf('GM_WITHDRAWAL_CONTENT_ID', $_POST['GM_WITHDRAWAL_CONTENT_ID']);
		}
		
		if($_POST['GM_SHOW_PRIVACY_REGISTRATION'] == 1)
		{
			gm_set_conf('GM_SHOW_PRIVACY_REGISTRATION', 1);
		}
		else
		{
			gm_set_conf('GM_SHOW_PRIVACY_REGISTRATION', 0);
		}
		
		
		if($_POST['PRIVACY_CHECKBOX_REGISTRATION'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_REGISTRATION', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_REGISTRATION', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_CALLBACK'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_CALLBACK', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_CALLBACK', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_CONTACT'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_CONTACT', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_CONTACT', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_FOUND_CHEAPER'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_FOUND_CHEAPER', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_FOUND_CHEAPER', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_REVIEWS'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_REVIEWS', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_REVIEWS', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_ACCOUNT_EDIT'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_ACCOUNT_EDIT', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_ACCOUNT_EDIT', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_ADDRESS_BOOK'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_ADDRESS_BOOK', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_ADDRESS_BOOK', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_NEWSLETTER'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_NEWSLETTER', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_NEWSLETTER', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_CHECKOUT_SHIPPING'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_CHECKOUT_SHIPPING', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_CHECKOUT_SHIPPING', 0);
		}
		
		if($_POST['PRIVACY_CHECKBOX_CHECKOUT_PAYMENT'] == 1)
		{
			gm_set_conf('PRIVACY_CHECKBOX_CHECKOUT_PAYMENT', 1);
		}
		else
		{
			gm_set_conf('PRIVACY_CHECKBOX_CHECKOUT_PAYMENT', 0);
		}
		
		
		if($_POST['GM_CHECK_WITHDRAWAL'] == 1)
		{
			gm_set_conf('GM_CHECK_WITHDRAWAL', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_WITHDRAWAL', 0);
		}
		
		if($_POST['GM_SHOW_WITHDRAWAL'] == 1)
		{
			gm_set_conf('GM_SHOW_WITHDRAWAL', 1);
		}
		else
		{
			gm_set_conf('GM_SHOW_WITHDRAWAL', 0);
		}
		
		if($_POST['SHOW_ACCOUNT_WITHDRAWAL_LINK'] == 1)
		{
			gm_set_conf('SHOW_ACCOUNT_WITHDRAWAL_LINK', 1);
		}
		else
		{
			gm_set_conf('SHOW_ACCOUNT_WITHDRAWAL_LINK', 0);
		}
		
		if($_POST['ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION'] == 1)
		{
			gm_set_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION', 1);
		}
		else
		{
			gm_set_conf('ATTACH_CONDITIONS_OF_USE_IN_ORDER_CONFIRMATION', 0);
		}
		
		if($_POST['ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION'] == 1)
		{
			gm_set_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION', 1);
		}
		else
		{
			gm_set_conf('ATTACH_WITHDRAWAL_INFO_IN_ORDER_CONFIRMATION', 0);
		}
		
		if($_POST['ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION'] == 1)
		{
			gm_set_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION', 1);
		}
		else
		{
			gm_set_conf('ATTACH_WITHDRAWAL_FORM_IN_ORDER_CONFIRMATION', 0);
		}
		
		if($_POST['CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD'] == 1)
		{
			gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD', 1);
		}
		else
		{
			gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_DOWNLOAD', 0);
		}
		if($_POST['CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE'] == 1)
		{
			gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE', 1);
		}
		else
		{
			gm_set_conf('CHECK_ABANDONMENT_OF_WITHDRAWL_SERVICE', 0);
		}
		
		$coo_download_delay_with_abandomment    = MainFactory::create_object('DownloadDelay');
		$coo_download_delay_without_abandomment = MainFactory::create_object('DownloadDelay');
		
		$coo_download_delay_with_abandomment->convert_days_to_seconds($_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS'],
		                                                              $_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS'],
		                                                              $_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES'],
		                                                              $_POST['DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS']);
		
		$coo_download_delay_without_abandomment->convert_days_to_seconds($_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_DAYS'],
		                                                                 $_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_HOURS'],
		                                                                 $_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_MINUTES'],
		                                                                 $_POST['DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT_SECONDS']);
		
		$t_download_delay_abandomment_seconds         = $coo_download_delay_with_abandomment->get_total_delay_seconds();
		$t_download_delay_without_abandomment_seconds = $coo_download_delay_without_abandomment->get_total_delay_seconds();
		
		gm_set_conf('DOWNLOAD_DELAY_FOR_ABANDONMENT_OF_WITHDRAWL_RIGHT', $t_download_delay_abandomment_seconds);
		gm_set_conf('DOWNLOAD_DELAY_WITHOUT_ABANDONMENT_OF_WITHDRAWL_RIGHT',
		            $t_download_delay_without_abandomment_seconds);
		
		if($_POST['WITHDRAWAL_WEBFORM_ACTIVE'] == 1)
		{
			gm_set_conf('WITHDRAWAL_WEBFORM_ACTIVE', 1);
		}
		else
		{
			gm_set_conf('WITHDRAWAL_WEBFORM_ACTIVE', 0);
		}
		
		if($_POST['WITHDRAWAL_PDF_ACTIVE'] == 1)
		{
			gm_set_conf('WITHDRAWAL_PDF_ACTIVE', 1);
		}
		else
		{
			gm_set_conf('WITHDRAWAL_PDF_ACTIVE', 0);
		}
		
		if($_POST['GM_SHOW_CONDITIONS'] == 1)
		{
			gm_set_conf('GM_SHOW_CONDITIONS', 1);
		}
		else
		{
			gm_set_conf('GM_SHOW_CONDITIONS', 0);
		}
		
		if($_POST['GM_CHECK_CONDITIONS'] == 1)
		{
			gm_set_conf('GM_CHECK_CONDITIONS', 1);
		}
		else
		{
			gm_set_conf('GM_CHECK_CONDITIONS', 0);
		}
		
		if($_POST['GM_SHOW_PRIVACY_CONFIRMATION'] == 1)
		{
			gm_set_conf('GM_SHOW_PRIVACY_CONFIRMATION', 1);
		}
		else
		{
			gm_set_conf('GM_SHOW_PRIVACY_CONFIRMATION', 0);
		}
		
		if($_POST['GM_SHOW_CONDITIONS_CONFIRMATION'] == 1)
		{
			gm_set_conf('GM_SHOW_CONDITIONS_CONFIRMATION', 1);
		}
		else
		{
			gm_set_conf('GM_SHOW_CONDITIONS_CONFIRMATION', 0);
		}
		if($_POST['GM_SHOW_WITHDRAWAL_CONFIRMATION'] == 1)
		{
			gm_set_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION', 1);
		}
		else
		{
			gm_set_conf('GM_SHOW_WITHDRAWAL_CONFIRMATION', 0);
		}
		if($_POST['GM_LOG_IP'] == 1)
		{
			gm_set_conf('GM_LOG_IP', 1);
		}
		else
		{
			gm_set_conf('GM_LOG_IP', 0);
		}
		
		if($_POST['GM_CONFIRM_IP'] == 1)
		{
			gm_set_conf('GM_CONFIRM_IP', 1);
		}
		else
		{
			gm_set_conf('GM_CONFIRM_IP', 0);
		}
		
		if($_POST['GM_LOG_IP_LOGIN'] == 1)
		{
			gm_set_conf('GM_LOG_IP_LOGIN', 1);
		}
		else
		{
			gm_set_conf('GM_LOG_IP_LOGIN', 0);
		}
		
		if($_POST['DISPLAY_TAX'] == 1)
		{
			gm_set_conf('DISPLAY_TAX', 1);
		}
		else
		{
			gm_set_conf('DISPLAY_TAX', 0);
		}
		
		return new RedirectHttpControllerResponse(DIR_WS_ADMIN . 'gm_laws.php?content=laws');
	}
	
	
	/**
	 * Save Cookie Preferences
	 *
	 * This is an AJAX callback that will save the cookies data and send back a success message.
	 */
	public function actionSaveCookiePreferences()
	{
		$this->_validatePageToken();
		
		gm_set_conf('GM_COOKIE_STATUS', $_POST['status']);
		gm_set_conf('GM_COOKIE_POSITION', $_POST['position']);
		gm_set_conf('GM_COOKIE_COLOR', $_POST['color']);
		gm_set_conf('GM_COOKIE_TRANSPARENCY', $this->convertTransparencyToOpacity($_POST['transparency']));
		gm_set_conf('GM_COOKIE_CLOSE_ICON', $_POST['close-icon']);
		gm_set_conf('GM_COOKIE_BUTTON_TEXT_COLOR', $_POST['button-text-color']);
		gm_set_conf('GM_COOKIE_BUTTON_COLOR', $_POST['button-color']);
		
		$cidb             = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$languageProvider = MainFactory::create('LanguageProvider', $cidb);
		
		foreach($languageProvider->getCodes()->getArray() as $languageCode)
		{
			$languageId = $languageProvider->getIdByCode($languageCode);
			$attrCode   = strtolower($languageCode->asString());
			gm_set_content('GM_COOKIE_BUTTON_TEXT', $_POST['button-text'][$attrCode], $languageId);
			gm_set_content('GM_COOKIE_BUTTON_LINK', $_POST['button-link'][$attrCode], $languageId);
			gm_set_content('GM_COOKIE_CONTENT', $_POST['content'][$attrCode], $languageId);
		}

		return new JsonHttpControllerResponse(array('success'));
	}
	
	
	public function actionGetCookiePreferences()
	{
		$cidb             = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$languageProvider = MainFactory::create('LanguageProvider', $cidb);
		
		// Fetch the form data from the database (gm_get_conf). 
		$formData = array(
			'status'            => gm_get_conf('GM_COOKIE_STATUS'),
			'position'          => gm_get_conf('GM_COOKIE_POSITION'),
			'color'             => gm_get_conf('GM_COOKIE_COLOR'),
			'transparency'      => $this->convertOpacityToTransparency(gm_get_conf('GM_COOKIE_TRANSPARENCY')),
			'close-icon'        => gm_get_conf('GM_COOKIE_CLOSE_ICON'),
			'button-text-color' => gm_get_conf('GM_COOKIE_BUTTON_TEXT_COLOR'),
			'button-color'      => gm_get_conf('GM_COOKIE_BUTTON_COLOR')
		);
		
		// Language-specific values from the database (gm_get_content). 
		foreach($languageProvider->getCodes()->getArray() as $languageCode)
		{
			$languageId = $languageProvider->getIdByCode($languageCode);
			$attrCode   = strtolower($languageCode->asString());
			
			$buttonText = gm_get_content('GM_COOKIE_BUTTON_TEXT', $languageId);
			
			$formData['button-text'][$attrCode] = $buttonText !== false ? $buttonText : null;
			
			$buttonLink = gm_get_content('GM_COOKIE_BUTTON_LINK', $languageId);
			
			$formData['button-link'][$attrCode] = $buttonLink !== false ? $buttonLink : null;
			
			$content = gm_get_content('GM_COOKIE_CONTENT', $languageId);
			
			$formData['content'][$attrCode] = $content !== false ? $content : null;
		}
		
		return new JsonHttpControllerResponse($formData);
	}
	

	/**
	 * Converts a transparency value (100% - 0%) to the equal opacity value (0.0 - 1.0).
	 * Note: The transparency value is represented as percentage, the opacity as float and
	 * the opacity is reverted. 0.9-, or 90% opacity means 0.1-, or 10% transparency.
	 *
	 * @param int $transparency Value which should be converted to the equal opacity.
	 *
	 * @return int Converted transparency value.
	 */
	private function convertTransparencyToOpacity($transparency)
	{
		// e.g.: 40 transparency is equal to 0.6 opacity
		// The formula to convert transparency to opacity is 1 - transparency / 100:
		// (1 - 40 / 100) = (1 - 0.4) = (0.6)
		return 1 - (int)$transparency / 100;
	}
	

	/**
	 * Converts a opacity value (0.0 - 1.0) to the equal transparency value (100% - 0%).
	 * Note: The transparency value is represented as percentage, the opacity as float and
	 * the opacity is reverted. 0.9-, or 90% opacity means 0.1-, or 10% transparency.
	 *
	 * @param float $opacity Value which should be converted to the equal transparency.
	 *
	 * @return int Converted opacity value.
	 */
	private function convertOpacityToTransparency($opacity)
	{
		// e.g.: 0.3 opacity is equal to 70 transparency
		// The formula to convert opacity to transparency is 100 - opacity * 100:
		// (100 - 0.3 * 100) = (100 - 30) = (70)
		return 100 - (double)$opacity * 100;
	}
}