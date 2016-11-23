<?php
/* --------------------------------------------------------------
   AccountPasswordContentControl.inc.php 2014-02-26 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(account_password.php,v 1.1 2003/05/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (account_password.php,v 1.14 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_password.php 1218 2005-09-16 11:38:37Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC . 'xtc_validate_password.inc.php');
require_once (DIR_FS_INC . 'xtc_encrypt_password.inc.php');

MainFactory::load_class('DataProcessing');

class AccountPasswordContentControl extends DataProcessing
{
	protected $customer_id = 0;
	protected $data_array = array();
	protected $sql_data_array = array();
	protected $error_array = array();

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_id']	= array('type' => 'int');
		$this->validation_rules_array['data_array']		= array('type' => 'array');
		$this->validation_rules_array['sql_data_array']	= array('type' => 'array');
		$this->validation_rules_array['error_array']	= array('type' => 'array');
	}
	
	public function proceed()
	{		
		// CHECK LOGIN
		$t_perform_redirect = $this->check_login();
		if($t_perform_redirect)
		{
			// REDIRECT
			return true;
		}
		
		// CHECK PROCESS
		$this->check_process();

		// CREATE CONTENTVIEW
		$coo_account_password_view = MainFactory::create_object('AccountPasswordContentView');
		$coo_account_password_view->set_('error_array', $this->error_array);
		// GET HTML
		$this->v_output_buffer = $coo_account_password_view->get_html();

		return true;
	}
	
	protected function check_login()
	{
		// CHECK LOGIN
		if($this->customer_id == 0)
		{
			// REDIRECT TO LOGIN PAGE
			$this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
			return true;
		}
		return false;
	}
	
	protected function check_process()
	{
		if(isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] == 'process'))
		{
			// PROCESS
			$this->process();
		}
	}
	
	protected function process()
	{
		// GET USER INPUT
		$this->get_user_input();
		// VALIDATE USER INPUT
		$this->validate_user_input();
		if(sizeof($this->error_array) == 0)
		{
			// SAVE CUSTOMER DATA
			$this->get_customer_sql_data_array();
			$this->save_customer();
			
			// SAVE CUSTOMER INFO DATA
			$this->get_customer_info_sql_data_array();
			$this->save_customer_info();
			
			// SET SUCCESS MESSAGE
			$GLOBALS['messageStack']->add_session('account', SUCCESS_PASSWORD_UPDATED, 'success');
			$this->set_redirect_url(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
		}
	}
	
	protected function get_user_input()
	{
		$this->data_array['password_current'] = xtc_db_prepare_input($this->v_data_array['POST']['password_current']);
		$this->data_array['password_new'] = xtc_db_prepare_input($this->v_data_array['POST']['password_new']);
		$this->data_array['password_confirmation'] = xtc_db_prepare_input($this->v_data_array['POST']['password_confirmation']);
	}
	
	protected function validate_user_input()
	{
		if(strlen_wrapper($this->data_array['password_current']) < ENTRY_PASSWORD_MIN_LENGTH)
		{
			$this->error_array['error_password'] = sprintf(ENTRY_PASSWORD_CURRENT_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
		}
		elseif(strlen_wrapper($this->data_array['password_new']) < ENTRY_PASSWORD_MIN_LENGTH)
		{
			$this->error_array['error_new_password'] = sprintf(ENTRY_PASSWORD_NEW_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
		}
		elseif($this->data_array['password_new'] != $this->data_array['password_confirmation'])
		{
			$this->error_array['error_confirmation'] = ENTRY_PASSWORD_NEW_ERROR_NOT_MATCHING;
		}
		else
		{
			// CHECK IF PASSWORD MATCH
			$t_current_password = $this->get_current_password();
			if(xtc_validate_password($this->data_array['password_current'], $t_current_password) == false)
			{
				$this->error_array['error_password_not_matching'] = ERROR_CURRENT_PASSWORD_NOT_MATCHING;
			}
		}
	}
	
	protected function get_current_password()
	{
		// GET CURRENT PASSWORD TO CHECK
		$t_check_customer_query = xtc_db_query("SELECT 
													customers_password 
												FROM 
													" . TABLE_CUSTOMERS . " 
												WHERE 
													customers_id = '" . $this->customer_id . "'");
		
		if(xtc_db_num_rows($t_check_customer_query) == 1)
		{
			$t_check_customer = xtc_db_fetch_array($t_check_customer_query);
			return $t_check_customer['customers_password'];
		}
		
		return false;
	}
	
	protected function get_customer_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_CUSTOMERS]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS] = array();
		}
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_password'] = xtc_encrypt_password($this->data_array['password_new']);
		$this->sql_data_array[TABLE_CUSTOMERS]['customers_last_modified'] = 'now()';
	}
	
	protected function save_customer()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS, $this->sql_data_array[TABLE_CUSTOMERS], 'update', 'customers_id = ' . $this->customer_id);
	}
	
	protected function get_customer_info_sql_data_array()
	{
		if(isset($this->sql_data_array[TABLE_CUSTOMERS_INFO]) == false)
		{
			$this->sql_data_array[TABLE_CUSTOMERS_INFO] = array();
		}
		$this->sql_data_array[TABLE_CUSTOMERS_INFO]['customers_info_date_account_last_modified'] = 'now()';
	}
	
	protected function save_customer_info()
	{
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_INFO, $this->sql_data_array[TABLE_CUSTOMERS_INFO], 'update', 'customers_info_id = ' . $this->customer_id);
	}
}