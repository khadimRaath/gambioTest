<?php
/* --------------------------------------------------------------
  AccountPasswordContentView.inc.php 2015-05-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(account_password.php,v 1.1 2003/05/19); www.oscommerce.com
  (c) 2003	 nextcommerce (account_password.php,v 1.14 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_password.php 1218 2005-09-16 11:38:37Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class AccountPasswordContentView extends ContentView
{
	protected $error_array = array();

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/account_password.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['error_array'] = array('type' => 'array');
	}
	
	public function prepare_data()
	{
		$this->add_error_messages();
		
		$this->add_data();
	}
	
	protected function add_error_messages()
	{
		if(is_array($this->error_array) && empty($this->error_array) == false)
		{
			foreach($this->error_array AS $t_error => $t_error_text)
			{
				$this->content_array[$t_error] = $t_error_text;
				if($t_error == 'error_confirmation' || $t_error == 'error_password_not_matching')
				{
					$GLOBALS['messageStack']->add('account_password', $t_error_text);
				}
			}
		}
		
		if($GLOBALS['messageStack']->size('account_password') > 0)
		{
			$this->content_array['error'] = $GLOBALS['messageStack']->output('account_password');
		}
	}
	
	protected function add_data()
	{
		if(isset($this->content_array['form_data']) == false)
		{
			$this->content_array['form_data'] = array();
		}
		
		$this->add_form();
		$this->add_password_current();
		$this->add_password_new();
		$this->add_password_confirmation();
	}
	
	protected function add_form()
	{
		$this->content_array['FORM_ID'] = 'account_password';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL');
		$this->content_array['FORM_METHOD'] = 'post';
		
		$this->content_array['form_data']['password_hidden_action'] = array();;
		$this->content_array['form_data']['password_hidden_action']['name'] = 'action';
		$this->content_array['form_data']['password_hidden_action']['value'] = 'process';

		$this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_ACCOUNT, '', 'SSL');
	}
	
	protected function add_password_current()
	{
		$this->content_array['form_data']['password_current'] = array();
		$this->content_array['form_data']['password_current']['name'] = 'password_current';
		$this->content_array['form_data']['password_current']['value'] = '';
		$this->content_array['form_data']['password_current']['required'] = 0;
		if((int)ENTRY_PASSWORD_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['password_current']['required'] = 1;
		}
	}
	
	protected function add_password_new()
	{
		$this->content_array['form_data']['password_new'] = array();
		$this->content_array['form_data']['password_new']['name'] = 'password_new';
		$this->content_array['form_data']['password_new']['value'] = '';
		$this->content_array['form_data']['password_new']['required'] = 0;
		if((int)ENTRY_PASSWORD_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['password_new']['required'] = 1;
		}
	}
	
	protected function add_password_confirmation()
	{
		$this->content_array['form_data']['password_confirmation'] = array();
		$this->content_array['form_data']['password_confirmation']['name'] = 'password_confirmation';
		$this->content_array['form_data']['password_confirmation']['value'] = '';
		$this->content_array['form_data']['password_confirmation']['required'] = 0;
		if((int)ENTRY_PASSWORD_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['password_confirmation']['required'] = 1;
		}
	}
}