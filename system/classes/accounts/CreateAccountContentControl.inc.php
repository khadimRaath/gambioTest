<?php
/* --------------------------------------------------------------
   CreateAccountContentControl.inc.php 2014-09-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account.php,v 1.63 2003/05/28); www.oscommerce.com
   (c) 2003  nextcommerce (create_account.php,v 1.27 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: create_account.php 1311 2005-10-18 12:30:40Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Guest account idea by Ingo T. <xIngox@web.de>

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');
require_once (DIR_FS_INC . 'xtc_create_password.inc.php');
require_once (DIR_FS_INC . 'xtc_write_user_info.inc.php');
require_once (DIR_WS_CLASSES . 'vat_validation.php');

MainFactory::load_class('DataProcessing');

class CreateAccountContentControl extends DataProcessing
{
	protected $customer_data_array = array();
	protected $sql_data_array = array();
	protected $mail_data_array = array();
	protected $guest_account = false;
	protected $error = false;
	protected $error_array = array();
	protected $set_customer_session_data = true;
	protected $do_track = true;
	protected $language_id;
	protected $gift_array = false;
	protected $coupon_array = false;
	protected $coo_create_account_content_view;
	protected $process = false;


	public function __construct()
	{
		parent::__construct();
	}


	protected function set_validation_rules()
	{
		$this->validation_rules_array['guest_account']				= array('type' => 'bool');
		$this->validation_rules_array['set_customer_session_data']	= array('type' => 'bool');
		$this->validation_rules_array['do_track']					= array('type' => 'bool');
		$this->validation_rules_array['language_id']				= array('type' => 'int');
	}


	public function proceed()
	{
		if(isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] == 'process'))
		{
			$this->process = true;

			$this->get_customers_data();
			$this->validate_customer_data();
			if($this->error == false)
			{
				$this->save_data();
				return true;
			}
		}

		$this->coo_create_account_content_view = MainFactory::create_object('CreateAccountContentView');
		$this->assign_data_to_content_view();
		$this->v_output_buffer = $this->coo_create_account_content_view->get_html();

		return true;
	}


	protected function get_customers_data()
	{
		if(ACCOUNT_GENDER == 'true')
		{
			$this->customer_data_array['gender'] = xtc_db_prepare_input($this->v_data_array['POST']['gender']);
		}

		$this->customer_data_array['firstname'] = xtc_db_prepare_input($this->v_data_array['POST']['firstname']);
		$this->customer_data_array['lastname'] = xtc_db_prepare_input($this->v_data_array['POST']['lastname']);

		if(ACCOUNT_DOB == 'true')
		{
			$this->customer_data_array['dob'] = xtc_db_prepare_input($this->v_data_array['POST']['dob']);
		}

		$this->customer_data_array['email_address'] = xtc_db_prepare_input($this->v_data_array['POST']['email_address']);
		$this->customer_data_array['email_address_confirm'] = xtc_db_prepare_input($this->v_data_array['POST']['email_address_confirm']);

		if(isset($this->v_data_array['POST']['email_address']) && !isset($this->v_data_array['POST']['email_address_confirm']))
		{
			$this->customer_data_array['email_address_confirm'] = $this->customer_data_array['email_address'];
		}

		if(ACCOUNT_COMPANY == 'true')
		{
			$this->customer_data_array['company'] = xtc_db_prepare_input($this->v_data_array['POST']['company']);
		}

		if(ACCOUNT_COMPANY_VAT_CHECK == 'true')
		{
			$this->customer_data_array['vat'] = xtc_db_prepare_input($this->v_data_array['POST']['vat']);
		}
		
		$this->customer_data_array['street_address'] = xtc_db_prepare_input($this->v_data_array['POST']['street_address']);
		
		if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true')
		{
			$this->customer_data_array['street_address'] = xtc_db_prepare_input($this->v_data_array['POST']['house_number']);
		}
		
		if(ACCOUNT_ADDITIONAL_INFO == 'true')
		{
			$this->customer_data_array['additional_address_info'] = xtc_db_prepare_input($this->v_data_array['POST']['additional_address_info']);
		}
		
		if(ACCOUNT_SUBURB == 'true')
		{
			$this->customer_data_array['suburb'] = xtc_db_prepare_input($this->v_data_array['POST']['suburb']);
		}

		$this->customer_data_array['postcode'] = xtc_db_prepare_input($this->v_data_array['POST']['postcode']);
		$this->customer_data_array['city'] = xtc_db_prepare_input($this->v_data_array['POST']['city']);
		$this->customer_data_array['zone_id'] = xtc_db_prepare_input($this->v_data_array['POST']['zone_id']);

		if(ACCOUNT_STATE == 'true')
		{
			$this->customer_data_array['state'] = xtc_db_prepare_input($this->v_data_array['POST']['state']);
		}

		$this->customer_data_array['country'] = xtc_db_prepare_input($this->v_data_array['POST']['country']);

		if(ACCOUNT_TELEPHONE == 'true')
		{
			$this->customer_data_array['telephone'] = xtc_db_prepare_input($this->v_data_array['POST']['telephone']);
		}

		if(ACCOUNT_FAX == 'true')
		{
			$this->customer_data_array['fax'] = xtc_db_prepare_input($this->v_data_array['POST']['fax']);
		}

		if(isset($this->v_data_array['POST']['newsletter']))
		{
			$this->customer_data_array['newsletter'] = xtc_db_prepare_input($this->v_data_array['POST']['newsletter']);
		}
		if($this->customer_data_array['newsletter'] == false)
		{
			$this->customer_data_array['newsletter'] = 0;
		}

		if($this->guest_account)
		{
			$this->customer_data_array['password'] = xtc_create_password(8);
		}
		else
		{
			$this->customer_data_array['password'] = xtc_db_prepare_input($this->v_data_array['POST']['password']);
		}

		$this->customer_data_array['confirmation'] = xtc_db_prepare_input($this->v_data_array['POST']['confirmation']);


		// vat_validation
		$coo_vat_validation = new vat_validation($this->customer_data_array['vat'], '', '', $this->customer_data_array['country'], $this->guest_account);

		$this->customer_data_array['customers_status'] = $coo_vat_validation->vat_info['status'];
		$this->customer_data_array['customers_vat_id_status'] = $coo_vat_validation->vat_info['vat_id_status'];
		$this->customer_data_array['vat_info_error'] = $coo_vat_validation->vat_info['error'];


		if($this->guest_account === false)
		{
			if($this->customer_data_array['customers_status'] == 0 || !$this->customer_data_array['customers_status'])
			{
				$this->customer_data_array['customers_status'] = DEFAULT_CUSTOMERS_STATUS_ID;
			}

			$this->customer_data_array['account_type'] = '0';
		}
		else
		{
			if($this->customer_data_array['customers_status'] == 0 || !$this->customer_data_array['customers_status'])
			{
				$this->customer_data_array['customers_status'] = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
			}

			$this->customer_data_array['account_type'] = '1';
		}

		$this->customer_data_array['b2b_status'] = 0;
		
		if(isset($this->v_data_array['POST']['b2b_status']))
		{
			$this->customer_data_array['b2b_status'] = (int)$this->v_data_array['POST']['b2b_status'];
		}
		elseif(ACCOUNT_DEFAULT_B2B_STATUS === 'true')
		{
			$this->customer_data_array['b2b_status'] = 1;
		}
	}


	protected function validate_customer_data()
	{
		if(ACCOUNT_GENDER == 'true')
		{
			if(($this->customer_data_array['gender'] != 'm') && ($this->customer_data_array['gender'] != 'f'))
			{
				$this->error = true;
				$this->error_array['error_gender'] = ENTRY_GENDER_ERROR;
			}
		}

		if(strlen_wrapper($this->customer_data_array['firstname']) < ENTRY_FIRST_NAME_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_first_name'] = sprintf(ENTRY_FIRST_NAME_ERROR, ENTRY_FIRST_NAME_MIN_LENGTH);
		}

		if(strlen_wrapper($this->customer_data_array['lastname']) < ENTRY_LAST_NAME_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_last_name'] = sprintf(ENTRY_LAST_NAME_ERROR, ENTRY_LAST_NAME_MIN_LENGTH);
		}

		if(ACCOUNT_DOB == 'true' && (ENTRY_DOB_MIN_LENGTH > 0 || ($this->customer_data_array['dob'] != '' && ENTRY_DOB_MIN_LENGTH == 0)))
		{
			if(!preg_match('/^[0-9]{2}[\.\/]{1}[0-9]{2}[\.\/]{1}[0-9]{4}$/', $this->customer_data_array['dob']) ||
				checkdate(substr(xtc_date_raw($this->customer_data_array['dob']), 4, 2), substr(xtc_date_raw($this->customer_data_array['dob']), 6, 2), substr(xtc_date_raw($this->customer_data_array['dob']), 0, 4)) == false)
			{
				$this->error = true;
				$this->error_array['error_birth_day'] = ENTRY_DATE_OF_BIRTH_ERROR;
			}
		}
		
		if(ACCOUNT_COMPANY == 'true')
		{
			if(strlen_wrapper($this->customer_data_array['company']) > 0 && strlen_wrapper($this->customer_data_array['company']) < ENTRY_COMPANY_MIN_LENGTH)
			{
				$this->error = true;
				$this->error_array['error_company'] = sprintf(ENTRY_COMPANY_ERROR, ENTRY_COMPANY_MIN_LENGTH);
			}
		}

		// New VAT Check
		if($this->customer_data_array['vat_info_error'] == true)
		{
			$this->error = true;
			$this->error_array['error_vat'] = ENTRY_VAT_ERROR;
		}
		// New VAT CHECK END

		$gm_get_existing_account = xtc_db_query("SELECT
													customers_id
												FROM
													" . TABLE_CUSTOMERS . "
												WHERE
													customers_email_address = '" . xtc_db_input($this->customer_data_array['email_address']) . "' AND
													customers_status = '" . DEFAULT_CUSTOMERS_STATUS_ID_GUEST . "' LIMIT 1"
		);
		if(xtc_db_num_rows($gm_get_existing_account) == 1)
		{
			$gm_old_customer_account = xtc_db_fetch_array($gm_get_existing_account);

			$this->delete_account($gm_old_customer_account['customers_id']);
		}

		if(strlen_wrapper($this->customer_data_array['email_address']) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_mail'] = sprintf(ENTRY_EMAIL_ADDRESS_ERROR, ENTRY_EMAIL_ADDRESS_MIN_LENGTH);
		}
		elseif(xtc_validate_email($this->customer_data_array['email_address']) == false)
		{
			$this->error = true;
			$this->error_array['error_mail'] = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
		}
		elseif($this->customer_data_array['email_address'] != $this->customer_data_array['email_address_confirm'])
		{
			$this->error = true;
			$this->error_array['error_mail'] = ENTRY_EMAIL_ADDRESS_CONFIRM_DIFFERENT_ERROR;
		}
		else
		{
			$check_email_query = xtc_db_query("SELECT
												COUNT(*) AS total
											FROM
												" . TABLE_CUSTOMERS . "
											WHERE
												customers_email_address = '" . xtc_db_input($this->customer_data_array['email_address']) . "' AND
												account_type = '0'"
			);
			$check_email = xtc_db_fetch_array($check_email_query);
			if($check_email['total'] > 0)
			{
				$this->error = true;
				$this->error_array['error_mail'] = ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
			}
		}

		if(strlen_wrapper($this->customer_data_array['street_address']) < ENTRY_STREET_ADDRESS_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_street'] = sprintf(ENTRY_STREET_ADDRESS_ERROR, ENTRY_STREET_ADDRESS_MIN_LENGTH);
		}

		if(strlen_wrapper($this->customer_data_array['postcode']) < ENTRY_POSTCODE_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_post_code'] = sprintf(ENTRY_POST_CODE_ERROR, ENTRY_POSTCODE_MIN_LENGTH);
		}

		if(strlen_wrapper($this->customer_data_array['city']) < ENTRY_CITY_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_city'] = sprintf(ENTRY_CITY_ERROR, ENTRY_CITY_MIN_LENGTH);
		}

		if(is_numeric($this->customer_data_array['country']) == false)
		{
			$this->error = true;
			$this->error_array['error_country'] = ENTRY_COUNTRY_ERROR;
		}

		if(ACCOUNT_STATE == 'true')
		{
			$this->customer_data_array['zone_id'] = 0;
			$check_query = xtc_db_query("SELECT
											COUNT(*) AS total
										FROM
											" . TABLE_ZONES . "
										WHERE
											zone_country_id = '" . (int)$this->customer_data_array['country'] . "'"
			);
			$check = xtc_db_fetch_array($check_query);
			$this->customer_data_array['entry_state_has_zones'] = ($check['total'] > 0);
			if($this->customer_data_array['entry_state_has_zones'] == true)
			{
				$zone_query = xtc_db_query("SELECT
												DISTINCT zone_id
											FROM
												" . TABLE_ZONES . "
											WHERE
												zone_country_id = '" . (int)$this->customer_data_array['country'] . "' AND
												(zone_name LIKE '" . xtc_db_input($this->customer_data_array['state']) . "%' OR zone_code LIKE '%" . xtc_db_input($this->customer_data_array['state']) . "%')"
				);
				if(xtc_db_num_rows($zone_query) > 1)
				{
					$zone_query = xtc_db_query("SELECT
													DISTINCT zone_id
												FROM
													" . TABLE_ZONES . "
												WHERE
													zone_country_id = '" . (int)$this->customer_data_array['country'] . "' AND
													zone_name = '" . xtc_db_input($this->customer_data_array['state']) . "'"
					);
				}
				if(xtc_db_num_rows($zone_query) >= 1)
				{
					$zone = xtc_db_fetch_array($zone_query);
					$this->customer_data_array['zone_id'] = $zone['zone_id'];
				}
				else
				{
					$this->error = true;
					$this->error_array['error_state'] = ENTRY_STATE_ERROR_SELECT;
				}

				$this->customer_data_array['zones_array'] = array();
				$zones_query = xtc_db_query("SELECT
												zone_name
											FROM
												" . TABLE_ZONES . "
											WHERE
												zone_country_id = '" . (int)$this->customer_data_array['country'] . "'
											ORDER BY
												zone_name");
				while($zones_values = xtc_db_fetch_array($zones_query))
				{
					$this->customer_data_array['zones_array'][] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
				}
			}
			else
			{
				if(strlen_wrapper($this->customer_data_array['state']) < ENTRY_STATE_MIN_LENGTH)
				{
					$this->error = true;
					$this->error_array['error_state'] = sprintf(ENTRY_STATE_ERROR, ENTRY_STATE_MIN_LENGTH);
				}
			}
		}

		if(ACCOUNT_TELEPHONE == 'true' && strlen_wrapper($this->customer_data_array['telephone']) < ENTRY_TELEPHONE_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_tel'] = sprintf(ENTRY_TELEPHONE_NUMBER_ERROR, ENTRY_TELEPHONE_MIN_LENGTH);
		}

		if($this->guest_account === false)
		{
			if(strlen_wrapper($this->customer_data_array['password']) < ENTRY_PASSWORD_MIN_LENGTH)
			{
				$this->error = true;
				$this->error_array['error_password'] = sprintf(ENTRY_PASSWORD_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
			}
			elseif($this->customer_data_array['password'] != $this->customer_data_array['confirmation'])
			{
				$this->error = true;
				$this->error_array['error_password2'] = ENTRY_PASSWORD_ERROR_NOT_MATCHING;
			}
		}
	}

	protected function save_data()
	{
		$this->create_customer_sql_data_array();
		$this->save_customer();

		if($this->guest_account === false && $this->do_track)
		{
			xtc_write_user_info($this->customer_data_array['customer_id']);
		}

		$this->create_address_book_sql_data_array();
		$this->save_address_book();

		$this->create_customer_info_sql_data_array();
		$this->save_customer_info();

		if(SESSION_RECREATE == 'True' && $this->set_customer_session_data)
		{
			xtc_session_recreate();
		}
		$this->process_session_data();

		if($this->guest_account === false)
		{
			if($this->do_track)
			{
				$this->track($this->customer_data_array['customer_id']);
			}

			$this->process_voucher();

			$this->create_mail_data_array();
			$this->send_mail();
		}

		$t_gm_redirect = FILENAME_SHOPPING_CART;

		if(isset($this->v_data_array['GET']['checkout_started']) && $this->v_data_array['GET']['checkout_started'] == 1)
		{
			$t_gm_redirect = FILENAME_CHECKOUT_SHIPPING;
		}

		$this->set_redirect_url(xtc_href_link($t_gm_redirect, '', 'SSL'));
	}


	protected function create_customer_info_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_CUSTOMERS_INFO]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS_INFO] = array();
		}

		$this->sql_data_array[TABLE_CUSTOMERS_INFO]['customers_info_id']					= (int)$this->customer_data_array['customer_id'];
		$this->sql_data_array[TABLE_CUSTOMERS_INFO]['customers_info_number_of_logons']		= 0;
		$this->sql_data_array[TABLE_CUSTOMERS_INFO]['customers_info_date_account_created']	= 'now()';
	}

	protected function save_customer_info()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_INFO, $this->sql_data_array[TABLE_CUSTOMERS_INFO]);
	}

	protected function create_customer_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_CUSTOMERS]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS] = array();
		}

		$this->sql_data_array[TABLE_CUSTOMERS]['customers_vat_id']			= $this->customer_data_array['vat'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_vat_id_status']	= $this->customer_data_array['customers_vat_id_status'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_status']			= $this->customer_data_array['customers_status'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_firstname']		= $this->customer_data_array['firstname'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_lastname']		= $this->customer_data_array['lastname'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_email_address']	= $this->customer_data_array['email_address'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_telephone']		= $this->customer_data_array['telephone'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_fax']				= $this->customer_data_array['fax'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_newsletter']		= $this->customer_data_array['newsletter'];
		$this->sql_data_array[TABLE_CUSTOMERS]['account_type']				= $this->customer_data_array['account_type'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_password']		= xtc_encrypt_password($this->customer_data_array['password']);
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_date_added']		= 'now()';
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_last_modified']	= 'now()';

		if(ACCOUNT_GENDER == 'true')
		{
			$this->sql_data_array[TABLE_CUSTOMERS]['customers_gender'] = $this->customer_data_array['gender'];
		}
		if(ACCOUNT_DOB == 'true')
		{
			$this->sql_data_array[TABLE_CUSTOMERS]['customers_dob'] = xtc_date_raw($this->customer_data_array['dob']);
		}
	}

	protected function save_customer()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, $this->sql_data_array[TABLE_CUSTOMERS]);
		$this->set_customer_data('customer_id', xtc_db_insert_id());
	}

	protected function create_address_book_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_ADDRESS_BOOK]) == false)
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK] = array();
		}

		$this->sql_data_array[TABLE_ADDRESS_BOOK]['customers_id']				= $this->customer_data_array['customer_id'];
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_firstname']			= $this->customer_data_array['firstname'];
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_lastname']			= $this->customer_data_array['lastname'];
		
		if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true')
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_street_address'] = $this->customer_data_array['street_address'];
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_house_number'] = $this->customer_data_array['house_number'];
		}
		else
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_street_address'] = $this->customer_data_array['street_address'];
		}
		
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_postcode']			= $this->customer_data_array['postcode'];
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_city']				= $this->customer_data_array['city'];
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_country_id']			= $this->customer_data_array['country'];
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['address_date_added']		= 'now()';
		$this->sql_data_array[TABLE_ADDRESS_BOOK]['address_last_modified']	= 'now()';

		if(ACCOUNT_GENDER == 'true')
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_gender'] = $this->customer_data_array['gender'];
		}
		if(ACCOUNT_COMPANY == 'true')
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_company'] = $this->customer_data_array['company'];
		}
		if(ACCOUNT_SUBURB == 'true')
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_suburb'] = $this->customer_data_array['suburb'];
		}
		if(ACCOUNT_ADDITIONAL_INFO == 'true')
		{
			$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_additional_info'] = $this->customer_data_array['additional_address_info'];
		}
		if(ACCOUNT_STATE == 'true')
		{
			if($this->customer_data_array['zone_id'] > 0)
			{
				$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_zone_id'] = $this->customer_data_array['zone_id'];
				$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_state'] = '';
			}
			else
			{
				$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_zone_id'] = '0';
				$this->sql_data_array[TABLE_ADDRESS_BOOK]['entry_state'] = $this->customer_data_array['state'];
			}
		}

		$this->sql_data_array[TABLE_ADDRESS_BOOK]['customer_b2b_status'] = (int)$this->customer_data_array['b2b_status'];
	}

	protected function save_address_book()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_ADDRESS_BOOK, $this->sql_data_array[TABLE_ADDRESS_BOOK]);

		$this->customer_data_array['default_address_id'] = xtc_db_insert_id();

		$t_customers_array = array(
			'customers_cid' => $this->customer_data_array['customer_id'],
			'customers_default_address_id' => $this->customer_data_array['default_address_id']
		);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, $t_customers_array, 'update', 'customers_id = ' . (int)$this->customer_data_array['customer_id']);
	}


	protected function process_voucher()
	{
		if(ACTIVATE_GIFT_SYSTEM == 'true')
		{
			if(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0)
			{
				$this->create_gift_sql_data_array();
				$this->save_gift();

				$this->create_gift_email_track_sql_data_array();
				$this->save_gift_email_track();
			}
			if(NEW_SIGNUP_DISCOUNT_COUPON != '')
			{
				$this->create_coupon_email_track_sql_data_array();
				$this->save_coupon_email_track();
			}
		}
	}


	protected function create_gift_sql_data_array()
	{
		$t_gift_code = create_coupon_code();

		if(isset($this->sql_data_array['gift']) == false)
		{
			$this->sql_data_array['gift'] = array();
		}

		if($this->gift_array == false)
		{
			$this->gift_array = array();
		}

		$this->gift_array['coupon_code']	= $t_gift_code;
		$this->gift_array['coupon_type']	= 'G';
		$this->gift_array['coupon_amount']	= NEW_SIGNUP_GIFT_VOUCHER_AMOUNT;
		$this->gift_array['date_created']	= 'now()';

		$this->sql_data_array['gift'] = $this->gift_array;
	}


	protected function save_gift()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_COUPONS, $this->sql_data_array['gift']);
		$this->gift_array['gift_id'] = xtc_db_insert_id();
	}


	protected function create_gift_email_track_sql_data_array()
	{
		if(isset($this->sql_data_array['gift_email_track']) == false)
		{
			$this->sql_data_array['gift_email_track'] = array();
		}

		$this->sql_data_array['gift_email_track']['coupon_id']		= $this->gift_array['gift_id'];
		$this->sql_data_array['gift_email_track']['customer_id_sent']	= 0;
		$this->sql_data_array['gift_email_track']['sent_firstname']	= 'Admin';
		$this->sql_data_array['gift_email_track']['emailed_to']		= xtc_db_input($this->customer_data_array['email_address']);
		$this->sql_data_array['gift_email_track']['date_sent']		= 'now()';
	}


	protected function save_gift_email_track()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_COUPON_EMAIL_TRACK, $this->sql_data_array['gift_email_track']);
	}


	protected function create_coupon_email_track_sql_data_array()
	{
		if(isset($this->sql_data_array['coupon_email_track']) == false)
		{
			$this->sql_data_array['coupon_email_track'] = array();
		}

		$t_coupon_code = NEW_SIGNUP_DISCOUNT_COUPON;
		$t_coupon_query = xtc_db_query("SELECT *
									FROM
										" . TABLE_COUPONS . "
									WHERE
										coupon_code = '" . $t_coupon_code . "'"
		);
		$t_coupon_result_array = xtc_db_fetch_array($t_coupon_query);

		$t_coupon_desc_query = xtc_db_query("SELECT *
											FROM
												" . TABLE_COUPONS_DESCRIPTION . "
											WHERE
												coupon_id = '" . $t_coupon_result_array['coupon_id'] . "' AND
												language_id = '" . (int)$this->language_id . "'"
		);
		$t_coupon_desc_result_array = xtc_db_fetch_array($t_coupon_desc_query);

		if($this->coupon_array == false)
		{
			$this->coupon_array = array();
		}

		$this->coupon_array['coupon_id']			= $t_coupon_result_array['coupon_id'];
		$this->coupon_array['coupon_code']			= $t_coupon_result_array['coupon_code'];
		$this->coupon_array['coupon_description']	= $t_coupon_desc_result_array['coupon_description'];

		$this->sql_data_array['coupon_email_track']['coupon_id']			= $this->coupon_array['coupon_id'];
		$this->sql_data_array['coupon_email_track']['customer_id_sent']	= 0;
		$this->sql_data_array['coupon_email_track']['sent_firstname']		= 'Admin';
		$this->sql_data_array['coupon_email_track']['emailed_to']			= $this->customer_data_array['email_address'];
		$this->sql_data_array['coupon_email_track']['date_sent']			= 'now()';
	}


	protected function save_coupon_email_track()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_COUPON_EMAIL_TRACK, $this->sql_data_array['coupon_email_track']);
	}


	protected function create_mail_data_array()
	{
		// build the message content
		$t_name = $this->customer_data_array['firstname'] . ' ' . $this->customer_data_array['lastname'];

		// load data into array
		$t_module_content = array(
			'MAIL_NAME' => htmlspecialchars_wrapper($t_name),
			'MAIL_REPLY_ADDRESS' => EMAIL_SUPPORT_REPLY_ADDRESS,
			'MAIL_GENDER' => htmlspecialchars_wrapper($this->customer_data_array['gender'])
		);

		// assign data to smarty
		$this->mail_data_array['language']		= $_SESSION['language'];
		$this->mail_data_array['logo_path']		= HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/';
		$this->mail_data_array['content']		= $t_module_content;
		$this->mail_data_array['GENDER']		= $this->customer_data_array['gender'];
		$this->mail_data_array['NAME']			= $t_name;
		$this->mail_data_array['mail_address']	= $this->customer_data_array['email_address'];

		// bof gm
		$gm_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($gm_logo_mail->logo_use == '1')
		{
			$this->mail_data_array['gm_logo_mail'] = $gm_logo_mail->get_logo();
		}

		if($this->gift_array != false)
		{
			$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
			$this->mail_data_array['SEND_GIFT'] = 'true';
			$this->mail_data_array['GIFT_AMMOUNT'] = $xtPrice->xtcFormat(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT, true);
			$this->mail_data_array['GIFT_CODE'] = $this->gift_array['coupon_code'];
			$this->mail_data_array['GIFT_LINK'] = xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $this->gift_array['coupon_code'], 'NONSSL', false);
		}

		if($this->coupon_array != false)
		{
			$this->mail_data_array['SEND_COUPON'] = 'true';
			$this->mail_data_array['COUPON_DESC'] = $this->coupon_array['coupon_description'];
			$this->mail_data_array['COUPON_CODE'] = $this->coupon_array['coupon_code'];
		}

		if(defined('EMAIL_SIGNATURE'))
		{
			$this->mail_data_array['EMAIL_SIGNATURE_HTML'] = nl2br(EMAIL_SIGNATURE);
			$this->mail_data_array['EMAIL_SIGNATURE_TEXT'] = EMAIL_SIGNATURE;
		}
	}


	protected function send_mail()
	{
		$coo_smarty = new Smarty;

		if(is_array($this->mail_data_array) && count($this->mail_data_array) > 0)
		{
			foreach($this->mail_data_array as $t_key => $t_content)
			{
				$coo_smarty->assign($t_key, $t_content);
			}
		}

		$coo_smarty->caching = 0;
		$t_html_mail = fetch_email_template($coo_smarty, 'create_account_mail');

		if(ACTIVATE_GIFT_SYSTEM == 'true' && NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0)
		{
			$coo_smarty->assign('GIFT_LINK', str_replace('&amp;', '&', $this->mail_data_array['GIFT_LINK']));
		}

		$coo_smarty->caching = 0;
		$t_txt_mail = fetch_email_template($coo_smarty, 'create_account_mail', 'txt');


		if(SEND_EMAILS == 'true')
		{
			xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME,
						$this->mail_data_array['mail_address'],
						$this->mail_data_array['NAME'],
						EMAIL_SUPPORT_FORWARDING_STRING,
						EMAIL_SUPPORT_REPLY_ADDRESS,
						EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
						'',
						'',
						EMAIL_SUPPORT_SUBJECT,
						$t_html_mail,
						$t_txt_mail
			);
		}
	}


	protected function process_session_data()
	{
		if($this->set_customer_session_data)
		{
			$t_session_data_array = array();
			$t_session_data_array['customer_id']					= $this->customer_data_array['customer_id'];
			$t_session_data_array['customer_first_name']			= $this->customer_data_array['firstname'];
			$t_session_data_array['customer_last_name']				= $this->customer_data_array['lastname'];
			$t_session_data_array['customer_default_address_id']	= $this->customer_data_array['default_address_id'];
			$t_session_data_array['customer_country_id']			= $this->customer_data_array['country'];
			$t_session_data_array['customer_zone_id']				= $this->customer_data_array['zone_id'];
			$t_session_data_array['customer_vat_id']				= $this->customer_data_array['vat'];
			$t_session_data_array['account_type']					= $this->customer_data_array['account_type'];

			$this->set_session_data($t_session_data_array);
			require DIR_WS_INCLUDES . 'write_customers_status.php';
		}
	}


	protected function assign_data_to_content_view()
	{
		if($this->guest_account)
		{
			$this->coo_create_account_content_view->set_content_template('module/create_account_guest.html');
		}
		$this->coo_create_account_content_view->set_('customer_data_array', $this->customer_data_array);
		$this->coo_create_account_content_view->set_('error_array', $this->error_array);
		$this->coo_create_account_content_view->set_('guest_account', $this->guest_account);
		$this->coo_create_account_content_view->set_('process', $this->process);

		$t_checkout_started = 0;
		if(isset($this->v_data_array['GET']['checkout_started']) && empty($this->v_data_array['GET']['checkout_started']) == false)
		{
			$t_checkout_started = (int)$this->v_data_array['GET']['checkout_started'];
		}
		$this->coo_create_account_content_view->set_('checkout_started', $t_checkout_started);
	}


	protected function set_session_data($p_session_data_array)
	{
		if(check_data_type($p_session_data_array, 'array'))
		{
			$_SESSION = array_merge($_SESSION, $p_session_data_array);
		}

		// restore cart contents
		$_SESSION['cart']->restore_contents();
	}


	public function set_customer_data($p_key, $p_value)
	{
		$this->customer_data_array[$p_key] = $p_value;
	}


	public function get_customer_data($p_key)
	{
		$t_customer_data = null;
		if(isset($this->customer_data_array[$p_key]))
		{
			$t_customer_data = $this->customer_data_array[$p_key];
		}

		return $t_customer_data;
	}


	public function track($p_customer_id)
	{
		if(isset($_SESSION['tracking']['refID']))
		{
			$campaign_check_query_raw = "SELECT *
										FROM
											" . TABLE_CAMPAIGNS . "
										WHERE
											campaigns_refID = '" . $_SESSION['tracking']['refID'] . "'";
			$campaign_check_query = xtc_db_query($campaign_check_query_raw);
			if(xtc_db_num_rows($campaign_check_query) > 0)
			{
				$campaign = xtc_db_fetch_array($campaign_check_query);
				$refID = $campaign['campaigns_id'];
			}
			else
			{
				$refID = 0;
			}

			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, array('refferers_id' => $refID), 'update', 'customers_id = ' . (int)$p_customer_id);

			$leads = $campaign['campaigns_leads'] + 1;
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CAMPAIGNS, array('campaigns_leads' => $leads), 'update', 'campaigns_id = ' . $refID);
		}
	}


	public function delete_account($p_customers_id)
	{
		$c_customers_id = (int)$p_customers_id;

		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_ADDRESS_BOOK, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_INFO, array(), 'delete', 'customers_info_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_IP, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_STATUS_HISTORY, array(), 'delete', 'customers_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_COUPON_GV_CUSTOMER, array(), 'delete', 'customer_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_COUPON_GV_QUEUE, array(), 'delete', 'customer_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_COUPON_REDEEM_TRACK, array(), 'delete', 'customer_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, TABLE_WHOS_ONLINE, array(), 'delete', 'customer_id = ' . $c_customers_id);
		$this->wrapped_db_perform(__FUNCTION__, 'withdrawals', array('customer_id' => '0'), 'update', 'customer_id = ' . $c_customers_id);
	}
}
