<?php
/* --------------------------------------------------------------
  AccountEditContentControl.inc.php 2015-06-25 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(account_edit.php,v 1.63 2003/05/19); www.oscommerce.com
  (c) 2003	 nextcommerce (account_edit.php,v 1.14 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_edit.php 1314 2005-10-20 14:00:46Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_date_short.inc.php');
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_get_geo_zone_code.inc.php');
require_once(DIR_FS_INC . 'xtc_get_customers_country.inc.php');

MainFactory::load_class('DataProcessing');

class AccountEditContentControl extends DataProcessing
{
	protected $customer_id = 0;
	protected $customer_data_array = array();
	protected $sql_data_array = array();
	protected $error = false;
	protected $error_array = array();
	protected $coo_edit_account_content_view;
	protected $process = false;
	protected $set_customer_session_data = true;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_id'] 					= array('type' => 'int');
		$this->validation_rules_array['customer_data_array'] 			= array('type' => 'array');
		$this->validation_rules_array['sql_data_array'] 				= array('type' => 'array');
		$this->validation_rules_array['error_array'] 					= array('type' => 'array');
		$this->validation_rules_array['error'] 							= array('type' => 'bool');
		$this->validation_rules_array['process'] 						= array('type' => 'bool');
		$this->validation_rules_array['set_customer_session_data'] 		= array('type' => 'bool');
		$this->validation_rules_array['coo_edit_account_content_view']	= array('type' => 'object', 
																				'object_type' => 'AccountEditContentView');
	}
	
	public function proceed()
	{
		$t_perform_redirect = $this->check_account();
		if($t_perform_redirect)
		{
			// REDIRECT
			return;
		}

		if($this->check_process())
		{
			$this->process();
		}
		else
		{
			$this->load_customer_data_from_db();
		}

		$this->coo_edit_account_content_view = MainFactory::create_object('AccountEditContentView');
		$this->assign_data_to_content_view();
		$this->v_output_buffer = $this->coo_edit_account_content_view->get_html($this->customer_data_array, $this->error_array);

		return true;
	}
	
	protected function check_process()
	{
		if(isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] == 'process'))
		{
			$this->process = true;
		}
		else
		{
			$this->process = false;
		}
		
		return $this->process;
	}

	protected function process()
	{
		$this->get_customer_data();
		$this->validate_customer_data();

		if($this->error == false)
		{
			$this->save_data();
		}
	}
	
	protected function load_customer_data_from_db()
	{
		$t_account_sql = "SELECT 
								*
							FROM 
								" . TABLE_CUSTOMERS . " 
							WHERE 
								customers_id = '" . $this->customer_id . "'"
		;
		$t_account_result = xtc_db_query($t_account_sql);
		if(xtc_db_num_rows($t_account_result))
		{
			$t_account_result_array = xtc_db_fetch_array($t_account_result);
			foreach($t_account_result_array as $t_key => $t_value)
			{
				$t_key = str_replace('customers_', '', $t_key);
				$this->customer_data_array[$t_key] = $t_value;
			}

			if(ACCOUNT_DOB == 'true')
			{
				$this->customer_data_array['dob'] = xtc_date_short($this->customer_data_array['dob']);
			}
		}
	}
	
	
	protected function check_account()
	{
		if($this->customer_id == 0)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
			return true;
		}
		
		return false;
	}

	
	protected function get_customer_data()
	{
		if(ACCOUNT_GENDER == 'true')
		{
			$this->customer_data_array['gender'] = xtc_db_prepare_input($this->v_data_array['POST']['gender']);
		}
		$this->customer_data_array['firstname'] = xtc_db_prepare_input($this->v_data_array['POST']['firstname']);
		$this->customer_data_array['lastname']  = xtc_db_prepare_input($this->v_data_array['POST']['lastname']);
		if(ACCOUNT_DOB == 'true')
		{
			$this->customer_data_array['dob'] = xtc_db_prepare_input($this->v_data_array['POST']['dob']);
		}
		if(ACCOUNT_COMPANY_VAT_CHECK == 'true')
		{
			$this->customer_data_array['vat'] = xtc_db_prepare_input($this->v_data_array['POST']['vat']);
		}
		$this->customer_data_array['country']       = xtc_db_prepare_input($this->v_data_array['POST']['country']);
		$this->customer_data_array['email_address'] = xtc_db_prepare_input($this->v_data_array['POST']['email_address']);
		$this->customer_data_array['telephone']     = xtc_db_prepare_input($this->v_data_array['POST']['telephone']);
		$this->customer_data_array['fax']           = xtc_db_prepare_input($this->v_data_array['POST']['fax']);
		//bof gm
		$this->customer_data_array['gm_privacy'] = xtc_db_prepare_input($this->v_data_array['POST']['gm_privacy']);
		//eof gm
		
		
		// New VAT Check
//		require_once(DIR_WS_CLASSES . 'vat_validation.php');
		$coo_vat_validation = new vat_validation($this->customer_data_array['vat'], '', '', $this->customer_data_array['country']);
		
		$this->customer_data_array['customers_status'] = $coo_vat_validation->vat_info['status'];
		$this->customer_data_array['customers_vat_id_status'] = $coo_vat_validation->vat_info['vat_id_status'];
		$this->customer_data_array['vat_info_error'] = $coo_vat_validation->vat_info['error'];
		
		$this->customer_data_array['privacy_accepted'] = isset($this->v_data_array['POST']['privacy_accepted']) ? '1' : '0';
	}
	
	
	protected function validate_customer_data()
	{
		$this->error = false;

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

		//@todo display error to frontend (not displayed right now)
		if(ACCOUNT_DOB == 'true') 
		{
			// Fetch the minimum DOB value from the database (stored in the `configuration` table).
			$query = '
				SELECT `configuration_value` 
				FROM `configuration` 
				WHERE `configuration_key` = "ENTRY_DOB_MIN_LENGTH"'; 
			
			$result = xtc_db_query($query);
			$config = xtc_db_fetch_array($result); 
			
			$minDateOfBirthValue = ($config) ? (int)$config['configuration_value'] : -1;   
			
			// Check if DOB field is required and if the user entered a value or not. 
			if($minDateOfBirthValue > 0 && empty($this->customer_data_array['dob'])) 
			{
				$this->error = true;
				$this->error_array['error_birth_day'] = ENTRY_DATE_OF_BIRTH_ERROR;
			}
			
			// Validate DOB field value only if it is not empty.
			if($this->customer_data_array['dob'] !== '')
			{		
				$parsedDate = date_parse_from_format(DATE_FORMAT, $this->customer_data_array['dob']); 
				
				if(count($parsedDate['errors']) > 0 || count($parsedDate['warnings']) > 0)
				{
					$this->error = true;
					$this->error_array['error_birth_day'] = ENTRY_DATE_OF_BIRTH_ERROR;
				}
			}
		}

		// New VAT Check
		if($this->customer_data_array['vat_info_error'] == true)
		{
			$this->error = true;
			$this->error_array['error_vat'] = ENTRY_VAT_ERROR;
		}
		// New VAT CHECK END
		// BOF GM_MOD
		// check if email already exists
		$t_gm_sql = "SELECT 
							customers_email_address
						FROM 
							customers
						WHERE 
							customers_id != '" . $this->customer_id . "' 
						AND 
							customers_email_address	= '" . xtc_db_input($this->customer_data_array['email_address']) . "'"
		;
		$t_gm_result = xtc_db_query($t_gm_sql);

		if(xtc_db_num_rows($t_gm_result) == 1)
		{
			$this->error = true;
			$this->error_array['error_mail'] = GM_ENTRY_EMAIL_ADDRESS_ERROR;
		}

		// EOF GM_MOD

		if(strlen_wrapper($this->customer_data_array['email_address']) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_mail'] = sprintf(ENTRY_EMAIL_ADDRESS_ERROR, ENTRY_EMAIL_ADDRESS_MIN_LENGTH);
		}

		if(xtc_validate_email($this->customer_data_array['email_address']) == false)
		{
			$this->error = true;
			$this->error_array['error_mail'] = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
		}

		if(strlen_wrapper($this->customer_data_array['telephone']) < ENTRY_TELEPHONE_MIN_LENGTH)
		{
			$this->error = true;
			$this->error_array['error_tel'] = sprintf(ENTRY_TELEPHONE_NUMBER_ERROR, ENTRY_TELEPHONE_MIN_LENGTH);
		}
		
		if(gm_get_conf('GM_CHECK_PRIVACY_ACCOUNT_CONTACT') === '1'
		   && gm_get_conf('PRIVACY_CHECKBOX_ACCOUNT_EDIT') === '1'
		   && (!isset($this->v_data_array['POST']['privacy_accepted'])
		       || $this->v_data_array['POST']['privacy_accepted'] !== '1')
		)
		{
			$this->error                        = true;
			$this->error_array['error_privacy'] = ENTRY_PRIVACY_ERROR;
		}
	}
	
	
	protected function save_data()
	{
		$this->create_customer_sql_data_array();
		$this->save_customer();
		
		$this->create_customer_info_sql_data_array();
		$this->save_customer_info();
		
		$this->process_session_data();

		$GLOBALS['messageStack']->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');
		$this->set_redirect_url(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
	}
	
	
	protected function assign_data_to_content_view()
	{
		$this->coo_edit_account_content_view->set_('customer_data_array', $this->customer_data_array);
		$this->coo_edit_account_content_view->set_('error_array', $this->error_array);
		
	}


	protected function process_session_data()
	{
		if($this->set_customer_session_data)
		{
			$t_session_data_array = array();
			$t_session_data_array['customer_id']			= $this->customer_id;
			if(ACCOUNT_GENDER == 'true')
			{
				$t_session_data_array['customer_gender']	= $this->customer_data_array['gender'];
			}
			$t_session_data_array['customer_first_name']	= $this->customer_data_array['firstname'];
			$t_session_data_array['customer_last_name']		= $this->customer_data_array['lastname'];
			$t_session_data_array['customer_country_id']	= $this->customer_data_array['country'];
			$t_session_data_array['customer_vat_id']		= $this->customer_data_array['vat'];
			$t_session_data_array['customer_telephone']		= $this->customer_data_array['telephone'];
			$t_session_data_array['customer_fax']			= $this->customer_data_array['fax'];

			$this->set_session_data($t_session_data_array);
		}
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
	
	
	protected function create_customer_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_CUSTOMERS]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS] = array();
		}

		$this->sql_data_array[TABLE_CUSTOMERS]['customers_vat_id'] 			= $this->customer_data_array['vat'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_vat_id_status']	= $this->customer_data_array['customers_vat_id_status'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_firstname']		= $this->customer_data_array['firstname'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_lastname']		= $this->customer_data_array['lastname'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_email_address']	= $this->customer_data_array['email_address'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_telephone'] 		= $this->customer_data_array['telephone'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_fax'] 			= $this->customer_data_array['fax'];
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_last_modified'] 	= 'now()';

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
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, $this->sql_data_array[TABLE_CUSTOMERS], 'update', 'customers_id = \'' . $this->customer_id . '\'');
	}
	
	
	protected function create_customer_info_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_CUSTOMERS_INFO]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS_INFO] = array();
		}
		
		$this->sql_data_array[TABLE_CUSTOMERS_INFO]['customers_info_date_account_last_modified'] = 'now()';
	}
	
	
	protected function save_customer_info()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_INFO, $this->sql_data_array[TABLE_CUSTOMERS_INFO], 'update', 'customers_info_id = \'' . $this->customer_id . '\'');
	}
}