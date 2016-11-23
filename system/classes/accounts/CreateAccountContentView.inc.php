<?php
/* --------------------------------------------------------------
   CreateAccountContentView.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
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
require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

class CreateAccountContentView extends ContentView
{
	protected $error_array = array();
	protected $checkout_started = 0;
	protected $checkout_started_param = '';
	protected $guest_account = false;
	protected $customer_data_array = array();
	protected $process = false;


	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/create_account.html');
		$this->set_flat_assigns(true);
	}
	
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_data_array']	= array('type' => 'array');
		$this->validation_rules_array['error_array']			= array('type' => 'array');
		$this->validation_rules_array['checkout_started']		= array('type' => 'int');
		$this->validation_rules_array['guest_account']			= array('type' => 'bool');
		$this->validation_rules_array['process']				= array('type' => 'bool');
	}

	
	public function prepare_data()
	{
		$this->add_error_messages();
		$this->set_checkout_started_param();
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
		$this->add_firstname();
		$this->add_lastname();
		$this->add_birthdate();
		$this->add_email();
		$this->add_email_confirm();
		$this->add_company();
		$this->add_vat();
		$this->add_street_address();
		$this->add_additional_info();
		$this->add_suburb();
		$this->add_postcode();
		$this->add_city();
		$this->add_state();
		$this->add_country();
		$this->add_telephone();
		$this->add_fax();
		$this->add_password();
		$this->add_privacy();
		$this->add_b2b_status();
	}


	protected function add_error_messages()
	{
		if(is_array($this->error_array) && empty($this->error_array) == false)
		{
			foreach($this->error_array AS $t_error => $t_error_text)
			{
				$this->content_array[$t_error] = $t_error_text;
				$GLOBALS['messageStack']->add('create_account', $t_error_text);
			}

			if($GLOBALS['messageStack']->size('create_account') > 0)
			{
				$this->content_array['error'] = $GLOBALS['messageStack']->output('create_account');
			}
		}
	}


	protected function set_checkout_started_param()
	{
		if($this->checkout_started == 1)
		{
			$this->checkout_started_param = 'checkout_started=1';
		}
	}
	

	protected function add_form()
	{
		$t_form_action = xtc_href_link('shop.php', 'do=CreateRegistree/Proceed&' . $this->checkout_started_param, 'SSL');
		if($this->guest_account)
		{
			$t_form_action = xtc_href_link('shop.php', 'do=CreateGuest/Proceed&' . $this->checkout_started_param, 'SSL');
		}
		
		$this->content_array['FORM_ID'] = 'create_account';
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['FORM_ACTION_URL'] = $t_form_action;
		
		$this->content_array['CHECKOUT_STARTED'] = $this->checkout_started;


		$this->content_array['LIGHTBOX'] = gm_get_conf('GM_LIGHTBOX_CREATE_ACCOUNT');
		$this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_LOGIN, $this->checkout_started_param, 'SSL');

		$this->content_array['LIGHTBOX_CLOSE'] = xtc_href_link(FILENAME_LOGIN, '', 'NONSSL');
		
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
		else
		{
			$this->content_array['gender'] = '0';
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
	
	
	protected function add_email_confirm()
	{
		$this->content_array['form_data']['email_confirm'] = array();
		$this->content_array['form_data']['email_confirm']['name'] = 'email_address_confirm';
		$this->content_array['form_data']['email_confirm']['value'] = htmlspecialchars_wrapper($this->customer_data_array['email_address_confirm']);
		$this->content_array['form_data']['email_confirm']['required'] = 0;
		if((int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['email_confirm']['required'] = 1;
		}
	}
	
	
	protected function add_company()
	{
		if(ACCOUNT_COMPANY == 'true')
		{
			$this->content_array['company'] = '1';
			$this->content_array['form_data']['company'] = array();
			$this->content_array['form_data']['company']['name'] = 'company';
			$this->content_array['form_data']['company']['value'] = htmlspecialchars_wrapper($this->customer_data_array['company']);
			$this->content_array['form_data']['company']['required'] = 0;
		}
		else
		{
			$this->content_array['company'] = '0';
		}
	}
	
	
	protected function add_vat()
	{
		if(ACCOUNT_COMPANY_VAT_CHECK == 'true')
		{
			$this->content_array['vat'] = '1';
			$this->content_array['form_data']['vat'] = array();
			$this->content_array['form_data']['vat']['name'] = 'vat';
			$this->content_array['form_data']['vat']['value'] = htmlspecialchars_wrapper($this->customer_data_array['vat']);
			$this->content_array['form_data']['vat']['required'] = 0;
		}
		else
		{
			$this->content_array['vat'] = '0';
		}
	}
	
	protected function add_street_address()
	{
		$this->content_array['form_data']['street_address']             = array();
		$this->content_array['form_data']['street_address']['name']     = 'street_address';
		$this->content_array['form_data']['street_address']['value']    = htmlspecialchars_wrapper($this->customer_data_array['street_address']);
		$this->content_array['form_data']['street_address']['required'] = 0;
		if((int)ENTRY_STREET_ADDRESS_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['street_address']['required'] = 1;
		}
		
		if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true')
		{
			$this->content_array['split_street_information'] = '1';
			
			$this->content_array['form_data']['house_number'] = array();
			$this->content_array['form_data']['house_number']['name'] = 'house_number';
			$this->content_array['form_data']['house_number']['value'] = htmlspecialchars_wrapper($this->customer_data_array['house_number']);
			$this->content_array['form_data']['house_number']['required'] = 0;
		}
		else
		{
			$this->content_array['split_street_information'] = '0';
		}
	}
	
	
	protected function add_additional_info()
	{
		if(ACCOUNT_ADDITIONAL_INFO == 'true')
		{
			$this->content_array['additional_address_info'] = '1';
			$this->content_array['form_data']['additional_address_info'] = array();
			$this->content_array['form_data']['additional_address_info']['name'] = 'additional_address_info';
			$this->content_array['form_data']['additional_address_info']['value'] = htmlspecialchars_wrapper($this->customer_data_array['additional_address_info']);
			$this->content_array['form_data']['additional_address_info']['required'] = 0;
		}
		else
		{
			$this->content_array['additional_address_info'] = '0';
		}
	}
	
	
	protected function add_suburb()
	{
		if(ACCOUNT_SUBURB == 'true')
		{
			$this->content_array['suburb'] = '1';
			$this->content_array['form_data']['suburb'] = array();
			$this->content_array['form_data']['suburb']['name'] = 'suburb';
			$this->content_array['form_data']['suburb']['value'] = htmlspecialchars_wrapper($this->customer_data_array['suburb']);
			$this->content_array['form_data']['suburb']['required'] = 0;
		}
		else
		{
			$this->content_array['suburb'] = '0';
		}
	}
	
	
	protected function add_postcode()
	{
		$this->content_array['form_data']['postcode'] = array();
		$this->content_array['form_data']['postcode']['name'] = 'postcode';
		$this->content_array['form_data']['postcode']['value'] = htmlspecialchars_wrapper($this->customer_data_array['postcode']);
		$this->content_array['form_data']['postcode']['required'] = 0;
		if((int)ENTRY_POSTCODE_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['postcode']['required'] = 1;
		}
	}
	
	
	protected function add_city()
	{
		$this->content_array['form_data']['city'] = array();
		$this->content_array['form_data']['city']['name'] = 'city';
		$this->content_array['form_data']['city']['value'] = htmlspecialchars_wrapper($this->customer_data_array['city']);
		$this->content_array['form_data']['city']['required'] = 0;
		if((int)ENTRY_CITY_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['city']['required'] = 1;
		}
	}
	
	
	protected function add_state()
	{
		if(ACCOUNT_STATE == 'true')
		{
			$this->content_array['state'] = '1';

			$this->content_array['form_data']['state'] = array();
			$this->content_array['form_data']['state']['required'] = 0;
			if((int)ENTRY_STATE_MIN_LENGTH > 0)
			{
				$this->content_array['form_data']['state']['required'] = 1;
			}

			if($this->process == true)
			{
				if($this->customer_data_array['entry_state_has_zones'] == true)
				{
					$this->content_array['form_data']['state']['name'] = 'state';
					$this->content_array['form_data']['state']['type'] = 'selection';
					$this->content_array['form_data']['state']['value'] = htmlspecialchars_wrapper($this->customer_data_array['state']);
					$this->content_array['zones_data'] = $this->customer_data_array['zones_array'];
				}
				else
				{
					$this->content_array['form_data']['state']['name'] = 'state';
					$this->content_array['form_data']['state']['value'] = '';
					$this->content_array['form_data']['state']['type'] = 'input';
				}
			}
			else
			{
				$this->content_array['form_data']['state']['name'] = 'state';
				$this->content_array['form_data']['state']['value'] = '';
				$this->content_array['form_data']['state']['type'] = 'input';
			}
		}
		else
		{
			$this->content_array['state'] = '0';
		}
	}
	
	
	protected function add_country()
	{
		if(isset($this->customer_data_array['country']))
		{
			$t_selected = htmlspecialchars_wrapper($this->customer_data_array['country']);
		}
		else
		{
			$t_selected = htmlspecialchars_wrapper(STORE_COUNTRY);
		}

		$this->content_array['form_data']['country'] = array();
		$this->content_array['form_data']['country']['name'] = 'country';
		$this->content_array['form_data']['country']['value'] = $t_selected;
		$this->content_array['form_data']['country']['required'] = 1;
		$this->content_array['countries_data'] = xtc_get_countriesList();
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
	
	
	protected function add_password()
	{
		if($this->guest_account === false)
		{
			$this->content_array['form_data']['password'] = array();
			$this->content_array['form_data']['password']['name'] = 'password';
			$this->content_array['form_data']['password']['required'] = 1;
			$this->content_array['form_data']['confirmation']['name'] = 'confirmation';
		}
	}


	protected function add_privacy()
	{
		if(gm_get_conf('GM_SHOW_PRIVACY_REGISTRATION'))
		{
			$this->content_array['show_privacy'] = 1;
			$this->content_array['PRIVACY_LINK'] = gm_get_privacy_link('GM_SHOW_PRIVACY_REGISTRATION');
			
			$this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_REGISTRATION');
			$this->content_array['form_data']['privacy_accepted']['value'] = (int)$this->customer_data_array['privacy_accepted'];
		}
	}


	protected function add_b2b_status()
	{
		$t_default_value = (ACCOUNT_DEFAULT_B2B_STATUS === 'true' ? 1 : 0);
		$this->content_array['show_b2b_status'] = (ACCOUNT_B2B_STATUS === 'true' ? 1 : 0);
		$this->content_array['form_data']['b2b_status'] = array();
		$this->content_array['form_data']['b2b_status']['name'] = 'b2b_status';
		$this->content_array['form_data']['b2b_status']['checked'] = (isset($this->customer_data_array['b2b_status']) ? $this->customer_data_array['b2b_status'] : $t_default_value);
		$this->content_array['form_data']['b2b_status']['required'] = 1;
	}
}