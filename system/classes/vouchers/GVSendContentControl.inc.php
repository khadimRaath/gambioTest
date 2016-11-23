<?php
/* --------------------------------------------------------------
   GVSendContentControl 2014-03-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
   (c) 2002-2003 osCommerce (gv_send.php,v 1.1.2.3 2003/05/12); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_send.php 1034 2005-07-15 15:21:43Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_WS_CLASSES . 'http_client.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');

MainFactory::load_class('DataProcessing');

class GVSendContentControl extends DataProcessing
{
	protected $customers_status_id;
	protected $currency;
	protected $customer_id;
	protected $language;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customers_status_id']	= array('type' => 'int');
		$this->validation_rules_array['currency']				= array('type' => 'string');
		$this->validation_rules_array['customer_id']			= array('type' => 'int');
		$this->validation_rules_array['language']				= array('type' => 'string');
	}

	public function proceed()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customers_status_id',
																		  'currency',
																		  'customer_id',
																		  'language'))
		;
		
		if(empty($t_uninitialized_array))
		{
			$xtPrice = new xtcPrice($this->currency, $this->customers_status_id);
			$coo_gv_send_view = MainFactory::create_object('GVSendContentView');
			$t_action = '';

			if($this->v_data_array['GET']['action'] == 'send' && !$this->v_data_array['POST']['back_x'] && !$this->v_data_array['POST']['back_y'])
			{
				$error = false;

				$coo_gv_send_view->set_('error_email', '');
				if(!xtc_validate_email(trim($this->v_data_array['POST']['email'])))
				{
					$error = true;
					$coo_gv_send_view->set_('error_email', ERROR_ENTRY_EMAIL_ADDRESS_CHECK);
				}

				$t_select = 'SELECT
								amount
							FROM
								' . TABLE_COUPON_GV_CUSTOMER . '
							WHERE
								customer_id = "' . $this->customer_id . '"'
				;
				$gv_query = xtc_db_query($t_select);
				$gv_result = xtc_db_fetch_array($gv_query);
				$customer_amount = $gv_result['amount'];
				$gv_amount = trim(str_replace(",", ".", $this->v_data_array['POST']['amount']));

				$coo_gv_send_view->set_('error_amount', '');
				
				if (preg_match('![^0-9/.]!', $gv_amount))
				{
					$error = true;
					$coo_gv_send_view->set_('error_amount', ERROR_ENTRY_AMOUNT_CHECK);
				}

				if ($gv_amount > $customer_amount || $gv_amount == 0)
				{
					$error = true;
					$coo_gv_send_view->set_('error_amount', ERROR_ENTRY_AMOUNT_CHECK);
				}

				$t_select = 'SELECT
								customers_firstname,
								customers_lastname
							FROM
								' . TABLE_CUSTOMERS . '
							WHERE
								customers_id = "' . $this->customer_id . '"'
				;
				$gv_query = xtc_db_query($t_select);
				$gv_result = xtc_db_fetch_array($gv_query);
				$send_name = $gv_result['customers_firstname'] . ' ' . $gv_result['customers_lastname'];
				$coo_gv_send_view->set_('personal_message', sprintf(PERSONAL_MESSAGE, $gv_result['customers_firstname']));
				$coo_gv_send_view->set_('send_name', $send_name);
				$coo_gv_send_view->set_('main_message', 
										sprintf(MAIN_MESSAGE,
												$xtPrice->xtcFormat(str_replace(",",
																				".",
																				htmlentities_wrapper($this->v_data_array['POST']['amount'])),
																	true),
												stripslashes($this->v_data_array['POST']['to_name']),
												$this->v_data_array['POST']['email'],
												stripslashes($this->v_data_array['POST']['to_name']),
												$xtPrice->xtcFormat(str_replace(",",
																				".",
																				$this->v_data_array['POST']['amount']),
																	true),
												$send_name)
										)
				;
				$coo_gv_send_view->set_('to_name', stripslashes($this->v_data_array['POST']['to_name']));
				$coo_gv_send_view->set_('email', $this->v_data_array['POST']['email']);
				$coo_gv_send_view->set_('message_body', stripslashes($this->v_data_array['POST']['message_body']));

				// validate entries
				$gv_amount = (double) $gv_amount;
				$coo_gv_send_view->set_('amount', (string)$gv_amount);

				if(!$error)
				{
					$t_action = 'send';
				}
			}
			elseif($this->v_data_array['GET']['action'] == 'process') 
			{
				$t_action = 'process';
				$id1 = create_coupon_code($mail['customers_email_address']);
				$t_select = 'SELECT
								amount
							FROM
								' . TABLE_COUPON_GV_CUSTOMER . '
							WHERE
								customer_id = "' . $this->customer_id . '"'
				;
				$gv_query = xtc_db_query($t_select);
				$gv_result = xtc_db_fetch_array($gv_query);
				$new_amount = $gv_result['amount'] - str_replace(",", ".", $this->v_data_array['POST']['amount']);
				$new_amount = str_replace(",", ".", $new_amount);

				if($new_amount < 0)
				{
					$coo_gv_send_view->set_('error_amount', ERROR_ENTRY_AMOUNT_CHECK);					
					$coo_gv_send_view->set_('error_email', '');
					$coo_gv_send_view->set_('to_name', stripslashes($this->v_data_array['POST']['to_name']));
					$coo_gv_send_view->set_('email', $this->v_data_array['POST']['email']);
					$coo_gv_send_view->set_('message_body', stripslashes($this->v_data_array['POST']['message_body']));
				} 
				else
				{
					$t_query = 'UPDATE
									' . TABLE_COUPON_GV_CUSTOMER . '
								SET
									amount = "' . $new_amount . '"
								WHERE
									customer_id = "' . $this->customer_id . '"'
					;
					$gv_query = xtc_db_query($t_query);
					
					$t_query = 'SELECT
									customers_firstname,
									customers_lastname
								FROM
									' . TABLE_CUSTOMERS . '
								WHERE
									customers_id = "' . $this->customer_id . '"'
					;
					$gv_query = xtc_db_query($t_query);
					$gv_customer = xtc_db_fetch_array($gv_query);
					
					$t_query = 'INSERT INTO
									' . TABLE_COUPONS . '
								SET
									coupon_type		= "G",
									coupon_code		= "' . $id1 . '",
									date_created	= NOW(),
									coupon_amount	= "' . str_replace(',', '.', xtc_db_input($this->v_data_array['POST']['amount'])) . '"'
					;
					$gv_query = xtc_db_query($t_query);
					$insert_id = xtc_db_insert_id($gv_query);
					
					$t_query = 'INSERT INTO
									' . TABLE_COUPON_EMAIL_TRACK . '
								SET
									coupon_id			= "' . $insert_id . '",
									customer_id_sent	= "' . $this->customer_id . '",
									sent_firstname		= "' . addslashes($gv_customer['customers_firstname']) . '",
									sent_lastname		= "' . addslashes($gv_customer['customers_lastname']) . '",
									emailed_to			= "' . xtc_db_input($this->v_data_array['POST']['email']) . '",
									date_sent			= NOW()'
					;
					$gv_query = xtc_db_query($t_query);

					$gv_email_subject = sprintf(EMAIL_GV_TEXT_SUBJECT, stripslashes($this->v_data_array['POST']['send_name']));

					$smarty = new Smarty;

					$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
					if($gm_logo_mail->logo_use == '1')
					{
						$smarty->assign('gm_logo_mail', $gm_logo_mail->get_logo());
					} 
					$smarty->assign('language', $this->language);
					$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
					$smarty->assign('logo_path', HTTP_SERVER.DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
					$t_gm_gift_link = xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $id1, 'NONSSL', false);
					$smarty->assign('GIFT_LINK', $t_gm_gift_link);
					$smarty->assign('AMMOUNT', $xtPrice->xtcFormat(str_replace(',', '.', htmlentities_wrapper($this->v_data_array['POST']['amount'])), true));
					$smarty->assign('GIFT_CODE', $id1);
					$smarty->assign('MESSAGE', htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['message_body'], true)));
					$smarty->assign('NAME', htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['to_name'], true)));
					$smarty->assign('FROM_NAME', htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['send_name'], true)));

					// dont allow cache
					$smarty->caching = false;

					if(defined('EMAIL_SIGNATURE'))
					{
						$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
						$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
					}

					$html_mail = fetch_email_template($smarty, 'send_gift_to_friend');
					$t_gm_gift_link = str_replace('&amp;', '&', $t_gm_gift_link);
					$smarty->assign('GIFT_LINK', $t_gm_gift_link);	
					$txt_mail = fetch_email_template($smarty, 'send_gift_to_friend', 'txt');

					// send mail
					xtc_php_mail(EMAIL_BILLING_ADDRESS,
								 EMAIL_BILLING_NAME,
								 $this->v_data_array['POST']['email'],
								 $this->v_data_array['POST']['to_name'],
								 '',
								 EMAIL_BILLING_REPLY_ADDRESS,
								 EMAIL_BILLING_REPLY_ADDRESS_NAME,
								 '',
								 '',
								 $gv_email_subject,
								 $html_mail,
								 $txt_mail
					);
				}
			}
			elseif(isset($this->v_data_array['GET']['action']) == false)
			{
				$coo_gv_send_view->set_('amount', '');					
				$coo_gv_send_view->set_('error_amount', '');					
				$coo_gv_send_view->set_('error_email', '');
				$coo_gv_send_view->set_('to_name', '');
				$coo_gv_send_view->set_('email', '');
				$coo_gv_send_view->set_('message_body', '');
			}
			
			$coo_gv_send_view->set_('action', $t_action);
			$this->v_output_buffer = $coo_gv_send_view->get_html();
		}
		else
		{
			trigger_error("Variable(s) "
						  .implode(', ', $t_uninitialized_array)
						  . " do(es) not exist in class "
						  . get_class($this)
						  . " or are null"
				, E_USER_ERROR
			);
		}
		
		return true;
	}
}