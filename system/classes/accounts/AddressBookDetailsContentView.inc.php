<?php
/* --------------------------------------------------------------
  AddressBookDetailsContentView.inc.php 2016-08-25
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
include_once('inc/xtc_get_zone_name.inc.php');
include_once('inc/xtc_get_country_list.inc.php');

class AddressBookDetailsContentView extends ContentView
{
	protected $action_edit;
	protected $process;
	protected $coo_address;
	protected $customer_country_id;
	protected $entry_state_has_zones;
	protected $customer_default_address_id;
	protected $privacy_accepted = '0';
	protected $error_array = array();

	function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/address_book_details.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['action_edit']					= array('type' => 'bool');
		$this->validation_rules_array['process']						= array('type' => 'bool');
		$this->validation_rules_array['entry_state_has_zones']			= array('type' => 'bool');
		$this->validation_rules_array['customer_country_id']			= array('type' => 'int');
		$this->validation_rules_array['customer_default_address_id']	= array('type' => 'int');
		$this->validation_rules_array['error_array']					= array('type' => 'array');
		$this->validation_rules_array['coo_address']					= array('type' => 'object',
																				'object_type' => 'AddressModel');
	}
	
	function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customer_default_address_id',
																		  'customer_country_id',
																		  'entry_state_has_zones',
																		  'coo_address')
		);

		if(empty($t_uninitialized_array))
		{
			// ADD ERROR MESSAGES
			$this->add_error_messages();

			// ADD FORM DATA
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	protected function add_error_messages()
	{
		if(is_array($this->error_array) && empty($this->error_array) == false)
		{
			foreach($this->error_array AS $t_error => $t_error_text)
			{
				$this->content_array[$t_error] = $t_error_text;
				$GLOBALS['messageStack']->add('address_book_details', $t_error_text);
			}
		}
	}

	protected function add_data()
	{
		if(is_array($this->content_array['form_data']) == false)
		{
			$this->content_array['form_data'] = array();
		}
		
		$this->add_buttons();
		$this->add_gender();
		$this->add_firstname();
		$this->add_lastname();
		$this->add_company();
		$this->add_street_address();
		$this->add_additional_info();
		$this->add_suburb();
		$this->add_postcode();
		$this->add_city();
		$this->add_state();
		$this->add_country();
		$this->add_b2b_status();
		$this->add_privacy();
		$this->add_primary_address();
	}

	protected function add_buttons()
	{
		$t_back_link = xtc_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL');
		$this->content_array['BUTTON_BACK_LINK'] = $t_back_link;
		$this->content_array['HIDDEN_FIELD_NAME'] = 'action';
		if(isset($this->action_edit) && $this->action_edit)
		{
			$this->content_array['HIDDEN_FIELD_VALUE'] = 'update';
		}
		else
		{
			$this->content_array['HIDDEN_FIELD_VALUE'] = 'process';
		}
	}

	protected function add_gender()
	{
		if(ACCOUNT_GENDER == 'true')
		{
			$t_male = ($this->coo_address->get_('entry_gender') == 'm') ? true : false;
			$t_female = ($this->coo_address->get_('entry_gender') == 'f') ? true : false;

			$this->content_array['gender'] = '1';

			if(is_array($this->content_array['form_data']['gender']) == false)
			{
				$this->content_array['form_data']['gender'] = array();
			}
			$this->content_array['form_data']['gender']['name'] = 'gender';
			if(is_array($this->content_array['form_data']['gender']['m']) == false)
			{
				$this->content_array['form_data']['gender']['m'] = array();
			}
			$this->content_array['form_data']['gender']['m']['value'] = 'm';
			$this->content_array['form_data']['gender']['m']['checked'] = '0';
			if(is_array($this->content_array['form_data']['gender']['f']) == false)
			{
				$this->content_array['form_data']['gender']['f'] = array();
			}
			$this->content_array['form_data']['gender']['f']['value'] = 'f';
			$this->content_array['form_data']['gender']['f']['checked'] = '0';

			if($t_male)
			{
				$this->content_array['form_data']['gender']['m']['checked'] = '1';
			}
			if($t_female)
			{
				$this->content_array['form_data']['gender']['f']['checked'] = '1';
			}

			$this->content_array['form_data']['gender']['required'] = 1;
			$this->content_array['form_data']['gender']['required_symbol'] = ENTRY_GENDER_TEXT;
		}
		else
		{
			$this->content_array['gender'] = '0';
		}
	}

	protected function add_firstname()
	{
		if(is_array($this->content_array['form_data']['firstname']) == false)
		{
			$this->content_array['form_data']['firstname'] = array();
		}
		$this->content_array['form_data']['firstname']['name'] = 'firstname';
		$this->content_array['form_data']['firstname']['value'] = $this->coo_address->get_('entry_firstname');
		$this->content_array['form_data']['firstname']['required'] = 0;
		$this->content_array['form_data']['firstname']['required_symbol'] = ENTRY_FIRST_NAME_TEXT;
		if((int)ENTRY_FIRST_NAME_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['firstname']['required'] = 1;
		}
	}

	protected function add_lastname()
	{
		if(is_array($this->content_array['form_data']['lastname']) == false)
		{
			$this->content_array['form_data']['lastname'] = array();
		}
		$this->content_array['form_data']['lastname']['name'] = 'lastname';
		$this->content_array['form_data']['lastname']['value'] = $this->coo_address->get_('entry_lastname');
		$this->content_array['form_data']['lastname']['required'] = 0;
		$this->content_array['form_data']['lastname']['required_symbol'] = ENTRY_LAST_NAME_TEXT;
		if((int)ENTRY_LAST_NAME_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['lastname']['required'] = 1;
		}
	}

	protected function add_company()
	{
		if(ACCOUNT_COMPANY == 'true')
		{
			$this->content_array['company'] = '1';

			if(is_array($this->content_array['form_data']['company']) == false)
			{
				$this->content_array['form_data']['company'] = array();
			}
			$this->content_array['form_data']['company']['name'] = 'company';
			$this->content_array['form_data']['company']['value'] = $this->coo_address->get_('entry_company');
			$this->content_array['form_data']['company']['required'] = 0;
			$this->content_array['form_data']['company']['required_symbol'] = ENTRY_COMPANY_TEXT;
			/*
			  if((int)ENTRY_COMPANY_MIN_LENGTH > 0)
			  {
			  $this->content_array['form_data']['company']['required'] = 1;
			  }
			 */
		}
		else
		{
			$this->content_array['company'] = '0';
		}
	}

	protected function add_street_address()
	{
		if(is_array($this->content_array['form_data']['street_address']) == false)
		{
			$this->content_array['form_data']['street_address'] = array();
		}
		$this->content_array['form_data']['street_address']['name']            = 'street_address';
		$this->content_array['form_data']['street_address']['value']           = $this->coo_address->get_('entry_street_address');
		$this->content_array['form_data']['street_address']['required']        = 0;
		$this->content_array['form_data']['street_address']['required_symbol'] = ENTRY_STREET_ADDRESS_TEXT;
		if((int)ENTRY_STREET_ADDRESS_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['street_address']['required'] = 1;
		}
		
		if(ACCOUNT_SPLIT_STREET_INFORMATION == 'true')
		{
			$this->content_array['split_street_information'] = '1';
			
			if(is_array($this->content_array['form_data']['house_number']) == false)
			{
				$this->content_array['form_data']['house_number'] = array();
			}
			$this->content_array['form_data']['house_number']['name']     = 'house_number';
			$this->content_array['form_data']['house_number']['value']    = $this->coo_address->get_('entry_house_number');
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
			
			if(is_array($this->content_array['form_data']['additional_address_info']) == false)
			{
				$this->content_array['form_data']['additional_address_info'] = array();
			}
			$this->content_array['form_data']['additional_address_info']['name'] = 'additional_address_info';
			$this->content_array['form_data']['additional_address_info']['value'] = $this->coo_address->get_('entry_additional_info');
			$this->content_array['form_data']['additional_address_info']['required'] = 0;
			$this->content_array['form_data']['additional_address_info']['required_symbol'] = ENTRY_ADDITIONAL_INFO_TEXT;
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

			if(is_array($this->content_array['form_data']['suburb']) == false)
			{
				$this->content_array['form_data']['suburb'] = array();
			}
			$this->content_array['form_data']['suburb']['name'] = 'suburb';
			$this->content_array['form_data']['suburb']['value'] = $this->coo_address->get_('entry_suburb');
			$this->content_array['form_data']['suburb']['required'] = 0;
			$this->content_array['form_data']['suburb']['required_symbol'] = ENTRY_SUBURB_TEXT;
		}
		else
		{
			$this->content_array['suburb'] = '0';
		}
	}

	protected function add_postcode()
	{
		if(is_array($this->content_array['form_data']['postcode']) == false)
		{
			$this->content_array['form_data']['postcode'] = array();
		}
		$this->content_array['form_data']['postcode']['name'] = 'postcode';
		$this->content_array['form_data']['postcode']['value'] = $this->coo_address->get_('entry_postcode');
		$this->content_array['form_data']['postcode']['required'] = 0;
		$this->content_array['form_data']['postcode']['required_symbol'] = ENTRY_POST_CODE_TEXT;
		if((int)ENTRY_POSTCODE_MIN_LENGTH > 0)
		{
			$this->content_array['form_data']['postcode']['required'] = 1;
		}
	}

	protected function add_city()
	{
		if(is_array($this->content_array['form_data']['city']) == false)
		{
			$this->content_array['form_data']['city'] = array();
		}
		$this->content_array['form_data']['city']['name'] = 'city';
		$this->content_array['form_data']['city']['value'] = $this->coo_address->get_('entry_city');
		$this->content_array['form_data']['city']['required'] = 0;
		$this->content_array['form_data']['city']['required_symbol'] = ENTRY_CITY_TEXT;
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
			
			if(is_array($this->content_array['form_data']['state']) == false)
			{
				$this->content_array['form_data']['state'] = array();
			}
			$this->content_array['form_data']['state']['required'] = 0;
			$this->content_array['form_data']['state']['required_symbol'] = ENTRY_STATE_TEXT;
			if((int)ENTRY_STATE_MIN_LENGTH > 0)
			{
				$this->content_array['form_data']['state']['required'] = 1;
			}

			if($this->process == true)
			{
				if($this->entry_state_has_zones == true)
				{
					$t_zones_array = array();
					$t_zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($this->customer_country_id) . "' order by zone_name");
					while($t_zones_values = xtc_db_fetch_array($t_zones_query))
					{
						$t_zones_array[] = array('id' => $t_zones_values['zone_name'], 'text' => $t_zones_values['zone_name']);
					}
					
					$this->content_array['form_data']['state']['name'] = 'state';
					$this->content_array['form_data']['state']['type'] = 'selection';
					if($this->coo_address->get_('address_book_id') != 0)
					{
						$this->content_array['form_data']['state']['value'] = $this->coo_address->get_('entry_state');
					}
					else
					{
						$this->content_array['form_data']['state']['value'] = $this->customer_state;
					}

					$this->content_array['zones_data'] = $t_zones_array;
				}
				else
				{
					$this->content_array['form_data']['state']['name'] = 'state';
					$this->content_array['form_data']['state']['value'] = xtc_get_zone_name($this->coo_address->get_('entry_country_id'), $this->coo_address->get_('entry_zone_id'), $this->coo_address->get_('entry_state'));
					$this->content_array['form_data']['state']['type'] = 'input';
				}
			}
			else
			{
				$this->content_array['form_data']['state']['name'] = 'state';
				$this->content_array['form_data']['state']['value'] = xtc_get_zone_name($this->coo_address->get_('entry_country_id'), $this->coo_address->get_('entry_zone_id'), $this->coo_address->get_('entry_state'));
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
		if(is_array($this->content_array['form_data']['country']) == false)
		{
			$this->content_array['form_data']['country'] = array();
		}
		$this->content_array['form_data']['country']['name'] = 'country';
		$this->content_array['form_data']['country']['value'] = $this->coo_address->get_('entry_country_id');
		$this->content_array['form_data']['country']['required'] = 1;
		$this->content_array['form_data']['country']['required_symbol'] = ENTRY_STATE_TEXT;
		$this->content_array['countries_data'] = xtc_get_countriesList();
	}

	protected function add_privacy()
	{
		$this->content_array['GM_PRIVACY_LINK'] = gm_get_privacy_link('GM_CHECK_PRIVACY_ACCOUNT_ADDRESS_BOOK');
		
		$this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_ADDRESS_BOOK');
		$this->content_array['form_data']['privacy_accepted']['value'] = (int)$this->privacy_accepted;
	}

	protected function add_primary_address()
	{
		if((isset($this->action_edit) && ($this->customer_default_address_id != $this->coo_address->get_('address_book_id')) || is_null($this->coo_address->get_('address_book_id'))) || (isset($this->action_edit) == false))
		{
			$this->content_array['new'] = '1';
			
			if(is_array($this->content_array['checkbox_primary_data']) == false)
			{
				$this->content_array['checkbox_primary_data'] = array();
			}
			$this->content_array['checkbox_primary_data']['NAME'] = 'primary';
			$this->content_array['checkbox_primary_data']['VALUE'] = 'on';
			$this->content_array['checkbox_primary_data']['ID'] = 'primary';
		}
		else
		{
			$this->content_array['new'] = '0';
		}
	}

	protected function add_b2b_status()
	{
		$t_default_value = (ACCOUNT_DEFAULT_B2B_STATUS === 'true' ? 1 : 0);
		$this->content_array['show_b2b_status'] = (ACCOUNT_B2B_STATUS === 'true' ? 1 : 0);
		$this->content_array['form_data']['b2b_status'] = array();
		$this->content_array['form_data']['b2b_status']['name'] = 'b2b_status';
		$this->content_array['form_data']['b2b_status']['checked'] = ($this->coo_address->get_('customer_b2b_status') !== null ? $this->coo_address->get_('customer_b2b_status') : $t_default_value);
		$this->content_array['form_data']['b2b_status']['required'] = 1;
	}
}