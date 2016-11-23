<?php
/* --------------------------------------------------------------
   LoginContentControl 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(login.php,v 1.79 2003/05/19); www.oscommerce.com
   (c) 2003      nextcommerce (login.php,v 1.13 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: login.php 1143 2005-08-11 11:58:59Z gwinger $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   guest account idea by Ingo T. <xIngox@web.de>
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');
require_once (DIR_FS_INC . 'xtc_array_to_string.inc.php');
require_once (DIR_FS_INC . 'xtc_write_user_info.inc.php');

MainFactory::load_class('DataProcessing');

class LoginContentControl extends DataProcessing
{

	public function __construct()
	{
		parent::__construct();
	}

	public function proceed()
	{
		$gm_log = MainFactory::create_object('GMTracker');
		$gm_log->gm_delete();
		$info_message = '';

		if($gm_log->gm_ban() == false)
		{
			if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'process'))
			{
				$email_address = xtc_db_prepare_input($this->v_data_array['POST']['email_address']);
				$password = xtc_db_prepare_input($this->v_data_array['POST']['password']);
				// Check if email exists
				$check_customer_query = xtc_db_query("SELECT 
														customers_id, 
														customers_vat_id, 
														customers_firstname, 
														customers_lastname, 
														customers_gender, 
														customers_password, 
														customers_email_address, 
														customers_default_address_id 
													FROM 
														" . TABLE_CUSTOMERS . " 
													WHERE 
														customers_email_address = '" . xtc_db_input($email_address) . "' AND 
														account_type = '0'"
				);
				if(!xtc_db_num_rows($check_customer_query))
				{
					$this->v_data_array['GET']['login'] = 'fail';
					$info_message = TEXT_LOGIN_ERROR;

					$gm_log->gm_track();
				}
				else
				{
					$check_customer = xtc_db_fetch_array($check_customer_query);

					// Check that password is good
					$t_valid_password = xtc_validate_password($password, $check_customer['customers_password']);

					// try admin login
					if($t_valid_password === false)
					{
						$t_sql = "SELECT 
									customers_password
								FROM 
									" . TABLE_CUSTOMERS . "
								WHERE 
									customers_id = 1 AND
									customers_status = 0";
						$t_result = xtc_db_query($t_sql);
						if(xtc_db_num_rows($t_result) == 1)
						{
							$t_result_array = xtc_db_fetch_array($t_result);
							$t_valid_password = xtc_validate_password($password, $t_result_array['customers_password']);
						}
					}

					if($t_valid_password === false)
					{
						$this->v_data_array['GET']['login'] = 'fail';

						$gm_log->gm_track();

						$info_message = TEXT_LOGIN_ERROR;
					}
					else
					{
						$gm_log->gm_delete(true);

						if(SESSION_RECREATE == 'True')
						{
							xtc_session_recreate();
						}

						$check_country_query = xtc_db_query("SELECT 
																entry_country_id, 
																entry_zone_id,
																customer_b2b_status
															FROM 
																" . TABLE_ADDRESS_BOOK . " 
															WHERE 
																customers_id = '" . (int)$check_customer['customers_id'] . "' AND 
																address_book_id = '" . $check_customer['customers_default_address_id'] . "'"
						);
						$check_country = xtc_db_fetch_array($check_country_query);

						$_SESSION['customer_gender'] = $check_customer['customers_gender'];
						$_SESSION['customer_first_name'] = $check_customer['customers_firstname'];
						$_SESSION['customer_last_name'] = $check_customer['customers_lastname'];
						$_SESSION['customer_id'] = $check_customer['customers_id'];
						$_SESSION['customer_vat_id'] = $check_customer['customers_vat_id'];
						$_SESSION['customer_default_address_id'] = $check_customer['customers_default_address_id'];
						$_SESSION['customer_country_id'] = $check_country['entry_country_id'];
						$_SESSION['customer_zone_id'] = $check_country['entry_zone_id'];
						update_customer_b2b_status($check_country['customer_b2b_status']);
						
						// write customers status in session
						require DIR_WS_INCLUDES . 'write_customers_status.php';
						
						$t_customers_info_array = array(
							'customers_info_date_of_last_logon' => 'now()',
							'customers_info_number_of_logons' => 'customers_info_number_of_logons + 1'
						);
						$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_INFO, $t_customers_info_array, 'update', 'customers_info_id = ' . (int)$_SESSION['customer_id'], 'db_link', false);
						
						xtc_write_user_info((int)$_SESSION['customer_id']);
						// restore cart contents
						$_SESSION['cart']->restore_contents();
						$_SESSION['wishList']->restore_contents();

						$coo_login_extender_component = MainFactory::create_object('LoginExtenderComponent');
						$coo_login_extender_component->set_data('customers_id', (int)$_SESSION['customer_id']);
						$coo_login_extender_component->proceed();

						if($_SESSION['cart']->count_contents() > 0)
						{
							if(isset($this->v_data_array['GET']['checkout_started']) && $this->v_data_array['GET']['checkout_started'] == 1)
							{
								$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
							}
							else
							{
								$this->set_redirect_url(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
							}
						}
						else
						{
							$this->set_redirect_url(xtc_href_link(FILENAME_DEFAULT));
						}
					}
				}
			}
		}
		else
		{
			// delete banned ips
			$info_message = GM_LOGIN_ERROR;
		}

		if($this->v_data_array['GET']['info_message'])
		{
			$info_message = htmlentities_wrapper($this->v_data_array['GET']['info_message']);
		}
		elseif(isset($_SESSION['gm_info_message']))
		{
			$info_message = htmlentities_wrapper(urldecode($_SESSION['gm_info_message']));
			unset($_SESSION['gm_info_message']);
		}

		$t_checkout_started_get_param = '';
		if(isset($this->v_data_array['GET']['checkout_started']) && $this->v_data_array['GET']['checkout_started'] == 1)
		{
			$t_checkout_started_get_param = 'checkout_started=1';
		}

		$t_input_mail_value = '';
		if(isset($this->v_data_array['POST']['email_address']))
		{
			$t_input_mail_value = htmlentities_wrapper(gm_prepare_string($this->v_data_array['POST']['email_address'], true));
		}

		$coo_login_view = MainFactory::create_object('LoginContentView');
		$coo_login_view->set_('info_message', $info_message);
		$coo_login_view->set_('checkout_started_get_param', $t_checkout_started_get_param);
		$coo_login_view->set_('input_mail_value', $t_input_mail_value);
		$coo_login_view->set_('cart_contents_count', $_SESSION['cart']->count_contents());
		
		$this->v_output_buffer = $coo_login_view->get_html();

		return true;
	}
}
