<?php
/* --------------------------------------------------------------
   AccountEditContentView.inc.php 2016-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(account_edit.php,v 1.63 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (account_edit.php,v 1.14 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_edit.php 1314 2005-10-20 14:00:46Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class AccountEditContentView extends ContentView
{
	protected $error_array = array();
	protected $customer_data_array = array();
	
	
	function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/account_edit.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}
	
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_data_array']	= array('type' => 'array');
		$this->validation_rules_array['error_array']			= array('type' => 'array');
	}
		
	
	public function prepare_data()
	{
		$this->add_error_messages();
		$this->add_data();
	}
	
	
	protected function add_data()
	{
		if(is_array($this->content_array['form_data']) == false)
		{
			$this->content_array['form_data'] = array();
		}
		
		$this->add_form();
		$this->add_gender();
		$this->add_vat();
		$this->add_firstname();
		$this->add_lastname();
		$this->add_birthdate();
		$this->add_email();
		$this->add_telephone();
		$this->add_fax();
		$this->add_privacy();
	}
	
	
	protected function add_form()
	{
		$t_form_action = xtc_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL');
		$this->content_array['FORM_ID'] =  'account_edit';
		$this->content_array['FORM_METHOD'] =  'post';
		$this->content_array['FORM_ACTION_URL'] =  $t_form_action;

		$this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_ACCOUNT, '', 'SSL');
		
		$this->content_array['HIDDEN_FIELD_NAME'] = 'action';
		$this->content_array['HIDDEN_FIELD_VALUE'] = 'process';
	}
	
	
	protected function add_gender()
	{
		if(ACCOUNT_GENDER == 'true')
		{
			$this->content_array['gender'] = '1';
			
			$this->content_array['form_data']['gender'] = array();
			$this->content_array['form_data']['gender']['m'] = array();
			$this->content_array['form_data']['gender']['f'] = array();
			$this->content_array['form_data']['gender']['name'] = 'gender';
			$this->content_array['form_data']['gender']['m']['value'] = 'm';
			$this->content_array['form_data']['gender']['f']['value'] = 'f';
			$this->content_array['form_data']['gender']['m']['checked'] = '0';
			$this->content_array['form_data']['gender']['f']['checked'] = '0';

			if($this->customer_data_array['gender'] == 'm')
			{
				$this->content_array['form_data']['gender']['m']['checked'] = '1';
			}
			if($this->customer_data_array['gender'] == 'f')
			{
				$this->content_array['form_data']['gender']['f']['checked'] = '1';
			}

			$this->content_array['form_data']['gender']['required'] = 1;
		}
	}
	
	
	protected function add_vat()
	{
		if(ACCOUNT_COMPANY_VAT_CHECK == 'true')
		{
			$this->content_array['vat'] = '1';
			$this->content_array['form_data']['vat'] = array();
			$this->content_array['form_data']['vat']['name'] = 'vat';
			$this->content_array['form_data']['vat']['value'] = htmlspecialchars_wrapper($this->customer_data_array['vat_id']);
			$this->content_array['form_data']['vat']['required'] = 0;
		}
		else
		{
			$this->content_array['vat'] = '0';
		}
	}
	
	
	protected function add_firstname()
	{
		$this->content_array['form_data']['firstname'] = array();
		$this->content_array['form_data']['firstname']['name'] = 'firstname';
		$this->content_array['form_data']['firstname']['value'] = htmlspecialchars_wrapper($this->customer_data_array['firstname']);
		$this->content_array['form_data']['firstname']['required'] = 0;
		if((int)ENTRY_FIRST_NAME_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['firstname']['required'] = 1;
		}
	}
	
	
	protected function add_lastname()
	{
		$this->content_array['form_data']['lastname'] = array();
		$this->content_array['form_data']['lastname']['name'] = 'lastname';
		$this->content_array['form_data']['lastname']['value'] = htmlspecialchars_wrapper($this->customer_data_array['lastname']);
		$this->content_array['form_data']['lastname']['required'] = 0;
		if((int)ENTRY_LAST_NAME_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['lastname']['required'] = 1;
		}
	}
	
	
	protected function add_birthdate()
	{
		if(ACCOUNT_DOB == 'true')
		{
			$this->content_array['birthdate'] = '1';
			$this->content_array['form_data']['birthdate'] = array();
			$this->content_array['form_data']['birthdate']['name'] = 'dob';
			$this->content_array['form_data']['birthdate']['value'] = htmlspecialchars_wrapper($this->customer_data_array['dob']);
			$this->content_array['form_data']['birthdate']['default_value'] = date('01.01.Y', strtotime(date('Y-01-01') . ' -10 years'));
			$this->content_array['form_data']['birthdate']['required'] = 0;
			if((int)ENTRY_DOB_MIN_LENGTH > 0)
			{
				$this->content_array['form_data']['birthdate']['required'] = 1;
			}
		}
		else
		{
			$this->content_array['birthdate'] = '0';
		}
	}
	
	
	protected function add_email()
	{
		$this->content_array['form_data']['email'] = array();
		$this->content_array['form_data']['email']['name'] = 'email_address';
		$this->content_array['form_data']['email']['value'] = htmlspecialchars_wrapper($this->customer_data_array['email_address']);
		$this->content_array['form_data']['email']['required'] = 0;
		if((int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['email']['required'] = 1;
		}
	}
	
	
	protected function add_telephone()
	{
		$this->content_array['telephone'] = '0';
		
		if(ACCOUNT_TELEPHONE == 'true')
		{
			$this->content_array['telephone'] = '1';
			$this->content_array['form_data']['telephone'] = array();
			$this->content_array['form_data']['telephone']['name'] = 'telephone';
			$this->content_array['form_data']['telephone']['value'] = htmlspecialchars_wrapper($this->customer_data_array['telephone']);
			$this->content_array['form_data']['telephone']['required'] = 0;
			if((int)ENTRY_TELEPHONE_MIN_LENGTH > 0)
			{
				$this->content_array['form_data']['telephone']['required'] = 1;
			}
		}
	}
	
	
	protected function add_fax()
	{
		$this->content_array['fax'] = '0';
		
		if(ACCOUNT_FAX == 'true')
		{
			$this->content_array['fax'] = '1';
			$this->content_array['form_data']['fax'] = array();
			$this->content_array['form_data']['fax']['name'] = 'fax';
			$this->content_array['form_data']['fax']['value'] = htmlspecialchars_wrapper($this->customer_data_array['fax']);
			$this->content_array['form_data']['fax']['required'] = 0;
		}
	}
	
	
	protected function add_privacy()
	{
		$this->content_array['GM_PRIVACY_LINK'] = gm_get_privacy_link('GM_CHECK_PRIVACY_ACCOUNT_CONTACT');
		
		$this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_ACCOUNT_EDIT');
		$this->content_array['form_data']['privacy_accepted']['value'] = (int)$this->customer_data_array['privacy_accepted'];
	}

	
	protected function add_error_messages()
	{
		if(is_array($this->error_array) && empty($this->error_array) == false)
		{
			foreach($this->error_array as $t_error => $t_error_text)
			{
				$this->content_array[$t_error] = $t_error_text;
				$GLOBALS['messageStack']->add('account_edit', $t_error_text);
			}

			if($GLOBALS['messageStack']->size('account_edit') > 0)
			{
				$this->content_array['error'] = $GLOBALS['messageStack']->output('account_edit');
			}
		}
	}
}