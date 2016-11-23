<?php
/* --------------------------------------------------------------
  PasswordDoubleOptContentControl.inc.php 2014-02-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce www.oscommerce.com
  (c) 2003  nextcommerce www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: password_double_opt.php,v 1.0)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC . 'xtc_render_vvcode.inc.php');
require_once (DIR_FS_INC . 'xtc_random_charcode.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');
require_once (DIR_FS_INC . 'xtc_rand.inc.php');

MainFactory::load_class('DataProcessing');

class PasswordDoubleOptContentControl extends DataProcessing
{
	public function proceed($p_language = null)
	{
		if($p_language === null)
		{
			$p_language = $_SESSION['language'];
		}

		// create smarty elements
		$smarty = new Smarty;

		$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($gm_logo_mail->logo_use == '1')
		{
			$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
		}

		$case = 'double_opt';

		$coo_captcha = MainFactory::create_object('Captcha');

		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'first_opt_in'))
		{

			$check_customer_query = xtc_db_query("select customers_firstname, customers_lastname, customers_gender, customers_email_address, customers_id from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
			$check_customer = xtc_db_fetch_array($check_customer_query);

			$vlcode = xtc_random_charcode(32);
			$link = xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, 'action=verified&customers_id=' . $check_customer['customers_id'] . '&key=' . $vlcode, 'SSL', false);

			// assign language to template for caching
			$smarty->assign('language', $p_language);
			$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
			$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

			if(defined('EMAIL_SIGNATURE'))
			{
				$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
				$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
			}

			$t_customers_name = $check_customer['customers_firstname'] . ' ' . $check_customer['customers_lastname'];

			// assign vars
			$smarty->assign('GENDER', $check_customer['customers_gender']);
			$smarty->assign('NAME', $t_customers_name);
			$smarty->assign('EMAIL', $check_customer['customers_email_address']);
			$smarty->assign('LINK', $link);

			// dont allow cache
			$smarty->caching = false;

			// create mails
			$html_mail = fetch_email_template($smarty, 'password_verification_mail');
			$link = str_replace('&amp;', '&', $link);
			$smarty->assign('LINK', $link);
			$txt_mail = fetch_email_template($smarty, 'password_verification_mail', 'txt');

			if($coo_captcha->is_valid($this->v_data_array['POST'], 'GM_FORGOT_PASSWORD_VVCODE'))
			{
				if(!xtc_db_num_rows($check_customer_query))
				{
					$case = 'first_opt_in';
				}
				else
				{
					$case = 'first_opt_in';
					xtc_db_query("update " . TABLE_CUSTOMERS . " set password_request_key = '" . $vlcode . "' where customers_id = '" . $check_customer['customers_id'] . "'");
					xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_customer['customers_email_address'], '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_PASSWORD_FORGOTTEN, $html_mail, $txt_mail);
				}
			}
			else
			{
				$case = 'code_error';
			}
		}

		// Verification
		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'verified'))
		{
			$check_customer_query = xtc_db_query("select customers_firstname, customers_lastname, customers_gender, customers_id, customers_email_address, password_request_key from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$this->v_data_array['GET']['customers_id'] . "' and password_request_key = '" . xtc_db_input($this->v_data_array['GET']['key']) . "'");
			$check_customer = xtc_db_fetch_array($check_customer_query);
			if(!xtc_db_num_rows($check_customer_query) || $this->v_data_array['GET']['key'] == "")
			{

				$case = 'no_account';
			}
			else
			{
				$newpass = xtc_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
				$crypted_password = xtc_encrypt_password($newpass);

				// sql injection fix 16.02.2011
				xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_password = '" . $crypted_password . "' where customers_email_address = '" . xtc_db_input($check_customer['customers_email_address']) . "'");
				xtc_db_query("update " . TABLE_CUSTOMERS . " set password_request_key = '' where customers_id = '" . $check_customer['customers_id'] . "'");
				// assign language to template for caching
				$smarty->assign('language', $p_language);
				$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
				$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');

				if(defined('EMAIL_SIGNATURE'))
				{
					$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
					$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
				}

				$t_customers_name = $check_customer['customers_firstname'] . ' ' . $check_customer['customers_lastname'];
				// assign vars
				$smarty->assign('GENDER', $check_customer['customers_gender']);
				$smarty->assign('NAME', $t_customers_name);
				$smarty->assign('EMAIL', $check_customer['customers_email_address']);
				$smarty->assign('NEW_PASSWORD', $newpass);
				// dont allow cache
				$smarty->caching = false;
				// create mails
				$html_mail = fetch_email_template($smarty, 'new_password_mail');
				$txt_mail = fetch_email_template($smarty, 'new_password_mail', 'txt');

				xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $check_customer['customers_email_address'], '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_PASSWORD_NEW_PASSWORD, $html_mail, $txt_mail);
				if(!isset($GLOBALS['mail_error']))
				{
					$_SESSION['gm_info_message'] = urlencode(TEXT_PASSWORD_SENT);
					$this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, '', 'SSL', true, false));
				}
			}
		}

		$t_captcha_html = $coo_captcha->get_html();

		$coo_password_double_opt_view = MainFactory::create_object('PasswordDoubleOptContentView');
		$coo_password_double_opt_view->set_('case', $case);
		if(isset($this->v_data_array['POST']['email']))
		{
			$coo_password_double_opt_view->set_('email_address', $this->v_data_array['POST']['email']);
		}
		$coo_password_double_opt_view->set_('captcha_html', $t_captcha_html);
		$this->v_output_buffer = $coo_password_double_opt_view->get_html();

		return true;
	}
}