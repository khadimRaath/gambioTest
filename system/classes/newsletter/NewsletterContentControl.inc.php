<?php
/* --------------------------------------------------------------
  NewsletterContentControl.inc.php 2016-08-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce www.oscommerce.com
  (c) 2003	 nextcommerce www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: newsletter.php,v 1.0)

  XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
  by Matthias Hinsche http://www.gamesempire.de

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC . 'xtc_render_vvcode.inc.php');
require_once (DIR_FS_INC . 'xtc_random_charcode.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');

MainFactory::load_class('DataProcessing');

class NewsletterContentControl extends DataProcessing
{
	protected $v_customer_data_array = array();
	protected $vvCode;

	public function __construct($vvCode, $p_customer_data_array = null)
	{
		$this->vvCode = $vvCode;
		
		if($p_customer_data_array === null && isset($_SESSION['customer_id']))
		{
			$this->set_customer_data('customer_id', $_SESSION['customer_id']);
			$this->set_customer_data('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
			$this->set_customer_data('first_name', $_SESSION['customer_first_name']);
			$this->set_customer_data('last_name', $_SESSION['customer_last_name']);
		}
		elseif(is_array($p_customer_data_array))
		{
			foreach($p_customer_data_array AS $t_key => $t_value)
			{
				$this->set_customer_data($t_key, $t_value);
			}
		}
	}

	public function set_customer_data($p_key, $p_value)
	{
		$this->v_customer_data_array[$p_key] = $p_value;
	}

	public function get_customer_data($p_key)
	{
		if(isset($this->v_customer_data_array[$p_key]))
		{
			return $this->v_customer_data_array[$p_key];
		}

		return false;
	}

	public function proceed()
	{
		$language = $_SESSION['language'];

		// create smarty elements
		$smarty = new Smarty;

		$t_form_send = false;

		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'process'))
		{
			$vlcode = xtc_random_charcode(32);
			$link = xtc_href_link(FILENAME_NEWSLETTER, 'action=activate&email=' . rawurlencode($this->v_data_array['POST']['email']) . '&key=' . $vlcode, 'NONSSL', false);

			// assign language to template for caching
			$smarty->assign('language', $language);
			$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
			$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
			
			$t_customers_query = xtc_db_query("SELECT customers_firstname, customers_lastname, customers_gender FROM " . TABLE_CUSTOMERS . " WHERE customers_email_address = '" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
			if(xtc_db_num_rows($t_customers_query))
			{
				$t_customers_result = xtc_db_fetch_array($t_customers_query);
			}
			// assign vars
			$smarty->assign('GENDER', $t_customers_result['customers_gender']);
			$smarty->assign('NAME', $t_customers_result['customers_firstname'] . ' ' . $t_customers_result['customers_lastname']);
			$smarty->assign('EMAIL', htmlentities_wrapper($this->v_data_array['POST']['email']));
			$smarty->assign('LINK', $link);
			// dont allow cache
			$smarty->caching = false;

			$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
			if($gm_logo_mail->logo_use == '1')
			{
				$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
			}

			if(defined('EMAIL_SIGNATURE'))
			{
				$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
				$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
			}

			// create mails
			$html_mail = fetch_email_template($smarty, 'newsletter_mail');
			$smarty->assign('LINK', str_replace('&amp;', '&', $link));
			$txt_mail = fetch_email_template($smarty, 'newsletter_mail', 'txt');
			
			if(gm_get_conf('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER') === '1'
			   && gm_get_conf('PRIVACY_CHECKBOX_NEWSLETTER') === '1'
			   && (!isset($this->v_data_array['POST']['privacy_accepted'])
			       || $this->v_data_array['POST']['privacy_accepted'] !== '1')
			)
			{
				$info_message = ENTRY_PRIVACY_ERROR;
			}
			else
			{
				// Check if email exists 
				if((strtoupper($this->v_data_array['POST']['vvcode']) == $this->vvCode))
				{
					if($this->v_data_array['POST']['check'] == 'inp')
					{
						$check_mail_query = xtc_db_query("select customers_email_address, mail_status from " . TABLE_NEWSLETTER_RECIPIENTS . " where customers_email_address = '" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
						if(!xtc_db_num_rows($check_mail_query))
						{
							
							if($this->get_customer_data('customer_id'))
							{
								$customers_id = $this->get_customer_data('customer_id');
								$customers_status = $this->get_customer_data('customers_status_id');
								$customers_firstname = $this->get_customer_data('first_name');
								$customers_lastname = $this->get_customer_data('last_name');
							}
							else
							{
								
								$check_customer_mail_query = xtc_db_query("select customers_id, customers_status, customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
								if(!xtc_db_num_rows($check_customer_mail_query))
								{
									$customers_id = '0';
									$customers_status = '1';
									$customers_firstname = TEXT_CUSTOMER_GUEST;
									$customers_lastname = '';
								}
								else
								{
									$check_customer = xtc_db_fetch_array($check_customer_mail_query);
									$customers_id = $check_customer['customers_id'];
									$customers_status = $check_customer['customers_status'];
									$customers_firstname = $check_customer['customers_firstname'];
									$customers_lastname = $check_customer['customers_lastname'];
								}
							}
							
							$sql_data_array = array('customers_email_address' => xtc_db_input($this->v_data_array['POST']['email']), 'customers_id' => xtc_db_input($customers_id), 'customers_status' => xtc_db_input($customers_status), 'customers_firstname' => xtc_db_input($customers_firstname), 'customers_lastname' => xtc_db_input($customers_lastname), 'mail_status' => '0', 'mail_key' => xtc_db_input($vlcode), 'date_added' => 'now()');
							xtc_db_perform(TABLE_NEWSLETTER_RECIPIENTS, $sql_data_array);
							
							$info_message = TEXT_EMAIL_INPUT;
							
							$t_form_send = true;
							
							// BOF GM_MOD:
							xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, xtc_db_input($this->v_data_array['POST']['email']), '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_SUBJECT, $html_mail, $txt_mail);
						}
						else
						{
							$check_mail = xtc_db_fetch_array($check_mail_query);
							
							if($check_mail['mail_status'] == '0')
							{
								$info_message = TEXT_EMAIL_INPUT;
								
								$t_form_send = true;
								
								$t_sql_data_array = array('mail_key' => $vlcode, 'date_added' => 'now()');
								xtc_db_perform(TABLE_NEWSLETTER_RECIPIENTS, $t_sql_data_array, 'update', 'customers_email_address = \'' . xtc_db_input($this->v_data_array['POST']['email']) . '\'');
								
								xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, xtc_db_input($this->v_data_array['POST']['email']), '', '', EMAIL_SUPPORT_REPLY_ADDRESS, EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', TEXT_EMAIL_SUBJECT, $html_mail, $txt_mail);
							}
							else
							{
								$info_message = TEXT_EMAIL_INPUT;
								
								$t_form_send = true;
							}
						}
					}
					elseif($this->v_data_array['POST']['check'] == 'del')
					{
						$check_mail_query = xtc_db_query("select customers_email_address from " . TABLE_NEWSLETTER_RECIPIENTS . " where customers_email_address = '" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
						if(!xtc_db_num_rows($check_mail_query))
						{
							$info_message = TEXT_EMAIL_DEL;
							$t_form_send = true;
						}
						else
						{
							$del_query = xtc_db_query("delete from " . TABLE_NEWSLETTER_RECIPIENTS . " where customers_email_address ='" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
							xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_newsletter = '0' where customers_email_address = '" . xtc_db_input($this->v_data_array['POST']['email']) . "'");
							$info_message = TEXT_EMAIL_DEL;
							$t_form_send = true;
						}
					}
					else
					{
						$info_message = TEXT_NO_CHOICE;
					}
				}
				else
				{
					$info_message = TEXT_WRONG_CODE;
				}
			}
		}

		// Accountaktivierung per Emaillink
		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'activate'))
		{
			$check_mail_query = xtc_db_query("select mail_key from " . TABLE_NEWSLETTER_RECIPIENTS . " where customers_email_address = '" . xtc_db_input(urldecode($this->v_data_array['GET']['email'])) . "'");
			if(!xtc_db_num_rows($check_mail_query))
			{
				$info_message = TEXT_EMAIL_NOT_EXIST;
			}
			else
			{
				$check_mail = xtc_db_fetch_array($check_mail_query);
				if($check_mail['mail_key'] != $this->v_data_array['GET']['key'])
				{
					$info_message = TEXT_EMAIL_ACTIVE_ERROR;
				}
				else
				{
					xtc_db_query("update " . TABLE_NEWSLETTER_RECIPIENTS . " set mail_status = '1' where customers_email_address = '" . xtc_db_input(urldecode($this->v_data_array['GET']['email'])) . "'");
					xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_newsletter = '1' where customers_email_address = '" . xtc_db_input(urldecode($this->v_data_array['GET']['email'])) . "'");
					$info_message = TEXT_EMAIL_ACTIVE;
				}
			}
			$t_form_send = true;
		}

		// Accountdeaktivierung per Emaillink
		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'remove'))
		{
			$check_mail_query = xtc_db_query("select customers_email_address, mail_key from " . TABLE_NEWSLETTER_RECIPIENTS . " where customers_email_address = '" . xtc_db_input(urldecode($this->v_data_array['GET']['email'])) . "' and mail_key = '" . xtc_db_input($this->v_data_array['GET']['key']) . "'");
			if(!xtc_db_num_rows($check_mail_query))
			{
				$info_message = TEXT_EMAIL_DEL_ERROR;
			}
			else
			{
				$check_mail = xtc_db_fetch_array($check_mail_query);

				if($check_mail['mail_key'] != $this->v_data_array['GET']['key'])
				{
					$info_message = TEXT_EMAIL_DEL_ERROR;
				}
				else
				{
					$del_query = xtc_db_query("delete from " . TABLE_NEWSLETTER_RECIPIENTS . " where  customers_email_address ='" . xtc_db_input(urldecode($this->v_data_array['GET']['email'])) . "' and mail_key = '" . xtc_db_input($this->v_data_array['GET']['key']) . "'");
					xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_newsletter = '0' where customers_email_address = '" . xtc_db_input(urldecode($this->v_data_array['GET']['email'])) . "'");
					$info_message = TEXT_EMAIL_DEL;
				}
			}
			$t_form_send = true;
		}

		$coo_newsletter_view = MainFactory::create_object('NewsletterContentView');
		$coo_newsletter_view->set_('form_send', $t_form_send);
		if(isset($this->v_data_array['POST']['email']))
		{
			$coo_newsletter_view->set_('email_address', $this->v_data_array['POST']['email']);
		}
		if(isset($info_message))
		{
			$coo_newsletter_view->set_('info_message', $info_message);
		}
		$coo_newsletter_view->set_('privacy_accepted', (isset($this->v_data_array['POST']['privacy_accepted']) ? '1' : '0'));
		$this->v_output_buffer = $coo_newsletter_view->get_html();

		return true;
	}
}