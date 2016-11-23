<?php
/* --------------------------------------------------------------
  CheckoutNewAddressContentControl.inc.php 2016-08-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_new_address.php,v 1.3 2003/05/19); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_new_address.php,v 1.8 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_new_address.php 1239 2005-09-24 20:09:56Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed clases
MainFactory::load_class('CheckoutControl');

class CheckoutNewAddressContentControl extends CheckoutControl
{
	protected $error_array = array();
	protected $coo_address;
	protected $entry_state_has_zones = false;
	protected $coo_new_address_content_view;
	protected $page_type;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['entry_state_has_zones']			= array('type' => 'bool');
		$this->validation_rules_array['error_array'] 					= array('type' => 'array');
		$this->validation_rules_array['coo_address'] 					= array('type' => 'object',
															 					'object_type' => 'AddressModel');
		$this->validation_rules_array['coo_new_address_content_view']	= array('type' => 'object',
																			  	'object_type' => 'CheckoutNewAddressContentView');
		$this->validation_rules_array['page_type']						= array('type' => 'string');
	}
	
	public function proceed()
	{
		// INITIALIZE ADDRESS MODEL
		$this->coo_address = MainFactory::create_object('AddressModel');
		$this->coo_new_address_content_view = MainFactory::create_object('CheckoutNewAddressContentView');
		
		$this->check_submit();
		
		$this->assign_data_to_new_address_content_view();
		$this->v_output_buffer = $this->coo_new_address_content_view->get_html();
	
		return true;
	}
	
	protected function check_submit()
	{
		if(isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] == 'submit'))
		{
			$this->process_submit();
		}
	}
	
	protected function process_submit()
	{
		// GET USER INPUT
		$this->get_address_data_from_user_input();
		
		// VALIDATE USER INPUT
		$this->validate_address_data();
		$this->_validatePrivacy();
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

		if(isset($this->v_data_array['POST']['b2b_status']))
		{
			$this->coo_address->set_('customer_b2b_status', (int)$this->v_data_array['POST']['b2b_status']);
		}
		else
		{
			$this->coo_address->set_('customer_b2b_status', 1);
		}
		
		$this->coo_new_address_content_view->set_('privacy_accepted', 
			(isset($this->v_data_array['POST']['privacy_accepted']) ? '1' : '0'));
	}
	
	protected function validate_address_data()
	{
		$BlockPackstation = $this->page_type == 'payment';
		$coo_form_validation_control = MainFactory::create_object('FormValidationControl');
		$this->error_array = $coo_form_validation_control->validate_address($this->coo_address, $BlockPackstation);
	}
	
	protected function assign_data_to_new_address_content_view()
	{
		$this->coo_new_address_content_view->set_('error_array', $this->error_array);
		$this->coo_new_address_content_view->set_('coo_address', $this->coo_address);
		
		if($this->page_type === 'shipping')
		{
			$this->coo_new_address_content_view->set_('privacy_html',
			                                          gm_get_privacy_link('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING'));
			$this->coo_new_address_content_view->set_('show_privacy_checkbox',
			                                          gm_get_privacy_link('PRIVACY_CHECKBOX_CHECKOUT_SHIPPING'));
		}
		else
		{
			$this->coo_new_address_content_view->set_('privacy_html',
			                                          gm_get_privacy_link('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT'));
			$this->coo_new_address_content_view->set_('show_privacy_checkbox',
			                                          gm_get_privacy_link('PRIVACY_CHECKBOX_CHECKOUT_PAYMENT'));
		}
	}
	
	
	protected function _validatePrivacy()
	{
		if($this->page_type === 'shipping')
		{
			if(gm_get_conf('GM_CHECK_PRIVACY_CHECKOUT_SHIPPING') === '1'
			   && gm_get_conf('PRIVACY_CHECKBOX_CHECKOUT_SHIPPING') === '1'
			   && (!isset($this->v_data_array['POST']['privacy_accepted'])
			       || $this->v_data_array['POST']['privacy_accepted'] !== '1')
			)
			{
				$this->error_array['error_privacy'] = ENTRY_PRIVACY_ERROR;
			}
		}
		else
		{
			if(gm_get_conf('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT') === '1'
			   && gm_get_conf('PRIVACY_CHECKBOX_CHECKOUT_PAYMENT') === '1'
			   && (!isset($this->v_data_array['POST']['privacy_accepted'])
			       || $this->v_data_array['POST']['privacy_accepted'] !== '1')
			)
			{
				$this->error_array['error_privacy'] = ENTRY_PRIVACY_ERROR;
			}
		}
	}
}