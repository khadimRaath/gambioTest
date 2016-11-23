<?php
/* --------------------------------------------------------------
  AddressBookProcessContentControl.inc.php 2016-08-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(address_book_process.php,v 1.77 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (address_book_process.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: address_book_process.php 1218 2005-09-16 11:38:37Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');
require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_country_name.inc.php');

class AddressBookProcessContentControl extends DataProcessing
{
	protected $customer_data_array = array();
	protected $coo_address;
	protected $error_array = array();
	protected $error = false;
	protected $process = false;
	protected $entry_state_has_zones = false;	
	protected $coo_address_book_content_view;
	protected $privacy_accepted = '0';

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_data_array'] 			= array('type' => 'array');
		$this->validation_rules_array['error_array'] 					= array('type' => 'array');
		$this->validation_rules_array['error'] 							= array('type' => 'bool');
		$this->validation_rules_array['process'] 						= array('type' => 'bool');
		$this->validation_rules_array['entry_state_has_zones'] 			= array('type' => 'bool');
		$this->validation_rules_array['coo_edit_account_content_view']	= array('type' 			=> 'object',
																				'object_type' 	=> 'AddressBookProcessContentView');
		$this->validation_rules_array['coo_address']					= array('type' 			=> 'object',
																				'object_type' 	=> 'AddressModel');
	}
	
	public function proceed()
	{
		// CHECK LOGIN
		$t_perform_redirect = $this->login_check();
		if($t_perform_redirect)
		{
			// REDIRECT
			return true;
		}
		
		// GET ADDRESS ID
		$t_address_id = $this->get_address_id();
		
		// LOAD ADDRESS MODEL
		$this->load_address_model($t_address_id);
		
		// CHECK IF NEW ADDRESS IS ALLOWED
		$t_perform_redirect = $this->check_new_address();
		if($t_perform_redirect)
		{
			// REDIRECT
			return true;
		}
		
		// DELETE CONFIRM ADDRESS BOOK ENTRY
		$t_perform_redirect = $this->process_delete_confirm_check();
		if($t_perform_redirect)
		{
			// REDIRECT
			return true;
		}
		
		// DELETE ADDRESS BOOK ENTRY
		$t_perform_redirect = $this->process_delete();
		if($t_perform_redirect)
		{
			// REDIRECT
			return true;
		}
		
		// UPDATE ADDRESS
		$t_perform_redirect = $this->process_update_check();
		if($t_perform_redirect)
		{
			// REDIRECT
			return true;
		}
				
		// CREATE CONTENT VIEW
		$this->coo_address_book_content_view = MainFactory::create_object('AddressBookProcessContentView');
		// ASSIGN DATA
		$this->assign_data_to_content_view();
		// GET HTML
		$this->v_output_buffer = $this->coo_address_book_content_view->get_html();
	}
	
	protected function login_check()
	{
		// CHECK USER LOGIN
		if((int)$this->customer_data_array['customers_id'] == 0)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
			return true;
		}
		return false;
	}
	
	protected function get_address_id()
	{
		$t_address_id = 0;
		// GET ADDRESS ID FROM GET PARAM
		if(isset($this->v_data_array['GET']['edit']) && (int)$this->v_data_array['GET']['edit'] > 0)
		{
			$t_address_id = (int)$this->v_data_array['GET']['edit'];
		}
		if(isset($this->v_data_array['GET']['delete']) && (int)$this->v_data_array['GET']['delete'] > 0)
		{
			$t_address_id = (int)$this->v_data_array['GET']['delete'];
		}
		return $t_address_id;
	}
	
	protected function load_address_model($p_address_id)
	{
		$this->coo_address = MainFactory::create_object('AddressModel', array($p_address_id));
	}
	
	protected function check_new_address()
	{
		// CHECK IF ADDRESS BOOK ID IS SET
		if($this->coo_address->get_('address_book_id') == 0)
		{
			// MAX ADDRESS BOOK ENTRIES
			if(xtc_count_customer_address_book_entries() >= MAX_ADDRESS_BOOK_ENTRIES)
			{
				$GLOBALS['messageStack']->add_session('addressbook', ERROR_ADDRESS_BOOK_FULL);

				$this->set_redirect_url(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
				return true;
			}
		}
		return false;
	}
	
	protected function process_delete_confirm_check()
	{
		if(isset($this->v_data_array['GET']['action']) && ($this->v_data_array['GET']['action'] == 'deleteconfirm') && isset($this->v_data_array['GET']['delete']))
		{
			return $this->process_delete_confirm();
		}
		return false;
	}
	
	
	protected function process_delete_confirm()
	{
		// DELETE ADDRESS BOOK ENTRY FROM DATABASE
		$this->coo_address->delete();

		$GLOBALS['messageStack']->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_DELETED, 'success');

		$this->set_redirect_url(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));

		return true;
	}
	
	protected function process_delete()
	{
		// CHECK IF DELETE ENTRY
		if(isset($this->v_data_array['GET']['delete']))
		{
			// ADDRESS BOOK ENTRY IS DEFAULT ADDRESS
			if($this->coo_address->get_('address_book_id') == $this->customer_data_array['default_address_id'])
			{
				$GLOBALS['messageStack']->add_session('addressbook', WARNING_PRIMARY_ADDRESS_DELETION, 'warning');

				$this->set_redirect_url(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
				return true;
			}
		}
		return false;
	}
		
	protected function process_update_check()
	{
		if(isset($this->v_data_array['POST']['action']) && (($this->v_data_array['POST']['action'] == 'process') || ($this->v_data_array['POST']['action'] == 'update')))
		{
			// INSERT || UPDATE
			$this->process = true;
			return $this->process_update();
		}
		return false;
	}
	
	protected function process_update()
	{
		// GET USER INPUT
		$this->get_address_data_from_user_input();
		
		// VALIDATE USER INPUT
		$this->validate_address_data();
		
		$this->_validate_privacy();

		if($this->error == false)
		{			
			// SET CUSTOMER ID
			$this->coo_address->set_('customers_id', $this->customer_data_array['customers_id']);

			// SAVE ADDRESS BOOK
			$this->coo_address->save();

			// UPDATE CUSTOMER?
			$this->update_customer_check();

			// ADD SUCCESS MESSAGE && REDIRECT
			$GLOBALS['messageStack']->add_session('addressbook', SUCCESS_ADDRESS_BOOK_ENTRY_UPDATED, 'success');
			$this->set_redirect_url(xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
			return true;
		}
		return false;
	}
	
	protected function get_address_data_from_user_input()
	{
		// GET ADDRESS DATA FROM USER INPUT
		if(ACCOUNT_GENDER == 'true')
		{
			$t_gender = '';
			if(isset($this->v_data_array['POST']['gender']))
			{
				$t_gender = $this->v_data_array['POST']['gender'];
			}
			$this->coo_address->set_('entry_gender', xtc_db_prepare_input($t_gender));
		}
		if(ACCOUNT_COMPANY == 'true')
		{
			$this->coo_address->set_('entry_company', xtc_db_prepare_input($this->v_data_array['POST']['company']));
		}
		$this->coo_address->set_('entry_firstname', xtc_db_prepare_input($this->v_data_array['POST']['firstname']));
		$this->coo_address->set_('entry_lastname', xtc_db_prepare_input($this->v_data_array['POST']['lastname']));
		$this->coo_address->set_('entry_street_address', xtc_db_prepare_input($this->v_data_array['POST']['street_address']));
		
		if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true')
		{
			$this->coo_address->set_('entry_house_number', xtc_db_prepare_input($this->v_data_array['POST']['house_number']));
		}
		
		if(ACCOUNT_ADDITIONAL_INFO == 'true')
		{
			$this->coo_address->set_('entry_additional_info', xtc_db_prepare_input($this->v_data_array['POST']['additional_address_info']));
		}
		
		if(ACCOUNT_SUBURB == 'true')
		{
			$this->coo_address->set_('entry_suburb', xtc_db_prepare_input($this->v_data_array['POST']['suburb']));
		}
		$this->coo_address->set_('entry_postcode', xtc_db_prepare_input($this->v_data_array['POST']['postcode']));
		$this->coo_address->set_('entry_city', xtc_db_prepare_input($this->v_data_array['POST']['city']));
		$this->coo_address->set_('entry_country_id', (int)$this->v_data_array['POST']['country']);
		
		$this->coo_address->set_('primary', false);
		if(isset($this->v_data_array['POST']['primary']) && ($this->v_data_array['POST']['primary'] == 'on'))
		{
			$this->coo_address->set_('primary', true);
		}
		
		if(ACCOUNT_STATE == 'true')
		{
			$this->coo_address->set_('entry_zone_id', 0);
			$this->coo_address->set_('entry_state', xtc_db_prepare_input($this->v_data_array['POST']['state']));
			
			// COUNT ZONES
			$t_query = 'SELECT
							COUNT(*) AS total
						FROM
							' . TABLE_ZONES . '
						WHERE
							zone_country_id =  \'' . (int)$this->coo_address->get_('entry_country_id') . '\'';
			$check_query = xtc_db_query($t_query);
			$check = xtc_db_fetch_array($check_query);
			// ZONES EXIST?
			$this->entry_state_has_zones = ($check['total'] > 0);
			if($this->entry_state_has_zones == true)
			{
				// GET ZONE FROM STATE INPUT
				$t_query = 'SELECT DISTINCT
								zone_id
							FROM
								' . TABLE_ZONES . '
							WHERE
								zone_country_id = \'' . (int)$this->coo_address->get_('entry_country_id') . '\'
								AND (zone_name like \'' . xtc_db_input($this->coo_address->get_('entry_state')) . '%\'
									OR zone_code like \'%' . xtc_db_input($this->coo_address->get_('entry_state')) . '%\')';
				$zone_query = xtc_db_query($t_query);
				if(xtc_db_num_rows($zone_query) > 1)
				{
					// GET ZONE DATA
					$zone_query = xtc_db_query('SELECT DISTINCT
													zone_id 
												FROM
													' . TABLE_ZONES . '
												WHERE
													zone_country_id = "' . (int)$this->coo_address->get_('entry_country_id') . '"
													AND zone_name = "' . xtc_db_input($this->coo_address->get_('entry_state')) . '"');
				}
				if(xtc_db_num_rows($zone_query) >= 1)
				{
					// SET ZONE
					$zone = xtc_db_fetch_array($zone_query);
					$this->coo_address->set_('entry_zone_id', $zone['zone_id']);
				}
				else
				{
					// ZONE DOESNT EXIST
					$this->coo_address->set_('entry_zone_id', -1);
				}
			}
			// WORKAROUNG (NO ZONE DROPDOWN)
			$this->entry_state_has_zones = false;
		}
		else {
			$this->coo_address->set_('entry_zone_id', 0);
			$this->coo_address->set_('entry_state', '');
		}

		if(isset($this->v_data_array['POST']['b2b_status']))
		{
			$this->coo_address->set_('customer_b2b_status', (int)$this->v_data_array['POST']['b2b_status']);
		}
		else
		{
			$this->coo_address->set_('customer_b2b_status', 1);
		}
		
		$this->privacy_accepted = isset($this->v_data_array['POST']['privacy_accepted']) ? '1' : '0';
	}
	
	protected function validate_address_data()
	{
		$coo_form_validation_control = MainFactory::create_object('FormValidationControl');
		$this->error_array = $coo_form_validation_control->validate_address($this->coo_address);
		
		if(empty($this->error_array) == false)
		{
			$this->error = true;
		}
	}
	
	protected function update_customer_check()
	{
		if($this->coo_address->get_('primary') || $this->coo_address->get_('address_book_id') == $this->customer_data_array['default_address_id'])
		{
			$this->update_customer();
		}
	}
	
	protected function update_customer()
	{
		// UPDATE DEFAULT ADDRESS
		$this->customer_data_array['default_address_id'] = $this->coo_address->get_('address_book_id');
		// CREATE SQL ARRAY
		$this->create_customer_sql_data_array();
		// SAVE CUSTOMER
		$this->save_customer();
		// UPDATE SESSION
		$this->update_customer_session();
	}
	
	protected function create_customer_sql_data_array()
	{
		// CREATE SQL ARRAY
		if(isset($this->sql_data_array[TABLE_CUSTOMERS]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS] = array();
		}
		
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_firstname'] = $this->coo_address->get_('entry_firstname');
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_lastname'] = $this->coo_address->get_('entry_lastname');
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_default_address_id'] = $this->coo_address->get_('address_book_id');
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_last_modified'] = 'now()';

		if(ACCOUNT_GENDER == 'true')
		{
			$this->sql_data_array[TABLE_CUSTOMERS]['customers_gender'] = $this->coo_address->get_('entry_gender');
		}
		
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_default_address_id'] = $this->coo_address->get_('address_book_id');
	}
	
	protected function save_customer()
	{
		// SAVE CUSTOMER
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, $this->sql_data_array[TABLE_CUSTOMERS], 'update', "customers_id = '" . (int)$this->customer_data_array['customers_id'] . "'");
	}
	
	protected function update_customer_session()
	{
		// UPDATE SESSION
		$_SESSION['customer_first_name'] = $this->coo_address->get_('entry_firstname');
		$_SESSION['customer_last_name'] = $this->coo_address->get_('entry_lastname');
		$_SESSION['customer_country_id'] = $this->coo_address->get_('entry_country_id');
		$_SESSION['customer_zone_id'] = $this->coo_address->get_('entry_zone_id');
		$_SESSION['customer_default_address_id'] = $this->coo_address->get_('address_book_id');
		update_customer_b2b_status($this->coo_address->get_('customer_b2b_status'));
	}
	
	protected function assign_data_to_content_view()
	{
		$t_action_edit = isset($this->v_data_array['GET']['edit']);
		$t_action_delete = isset($this->v_data_array['GET']['delete']);
		$this->coo_address_book_content_view->set_('action_edit', $t_action_edit);
		$this->coo_address_book_content_view->set_('action_delete', $t_action_delete);
		$this->coo_address_book_content_view->set_('process', $this->process);
		$this->coo_address_book_content_view->set_('coo_address', $this->coo_address);
		$this->coo_address_book_content_view->set_('customer_id', $this->customer_data_array['customers_id']);
		$this->coo_address_book_content_view->set_('customer_country_id', $this->customer_data_array['country_id']);
		$this->coo_address_book_content_view->set_('customer_default_address_id', $this->customer_data_array['default_address_id']);
		$this->coo_address_book_content_view->set_('entry_state_has_zones', $this->entry_state_has_zones);
		$this->coo_address_book_content_view->set_('error_array', $this->error_array);
		$this->coo_address_book_content_view->set_('privacy_accepted', $this->privacy_accepted);
	}
	
	protected function _validate_privacy()
	{
		if(gm_get_conf('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK') === '1'
		   && gm_get_conf('PRIVACY_CHECKBOX_ADDRESS_BOOK') === '1'
		   && (!isset($this->v_data_array['POST']['privacy_accepted'])
		       || $this->v_data_array['POST']['privacy_accepted'] !== '1')
		)
		{
			$this->error                        = true;
			$this->error_array['error_privacy'] = ENTRY_PRIVACY_ERROR;
		}
	}
	
	public function set_customers_id($p_customers_id)
	{
		if(check_data_type($p_customers_id, 'int'))
		{
			$this->customer_data_array['customers_id'] = $p_customers_id;
		}
	}

	public function set_customer_default_address_id($p_customer_default_address_id)
	{
		if(check_data_type($p_customer_default_address_id, 'int'))
		{
			$this->customer_data_array['default_address_id'] = $p_customer_default_address_id;
		}
	}

	public function set_customer_country_id($p_customer_country_id)
	{
		if(check_data_type($p_customer_country_id, 'int'))
		{
			$this->customer_data_array['country_id'] = $p_customer_country_id;
		}
	}
}