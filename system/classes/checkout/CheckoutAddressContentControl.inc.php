<?php
/* --------------------------------------------------------------
  CheckoutAddressContentControl.inc.php 2016-08-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_shipping_address.php,v 1.14 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_shipping_address.php,v 1.14 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_shipping_address.php 867 2005-04-21 18:35:29Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_count_customer_address_book_entries.inc.php');

MainFactory::load_class('CheckoutControl');

class CheckoutAddressContentControl extends CheckoutControl
{
	protected $customer_id;
	protected $coo_address;
	protected $coo_order;
	protected $page_types_array;
	protected $page_type;
	protected $filename_checkout;
	protected $process = false;
	protected $entry_state_has_zones = false;
	protected $error_array = array();
	protected $error = false;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->add_page_type('shipping');
		$this->add_page_type('payment');
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_id']			= array('type'			=> 'int');
		$this->validation_rules_array['filename_checkout']		= array('type'			=> 'string');
		$this->validation_rules_array['page_type']				= array('type'			=> 'string');
		$this->validation_rules_array['page_types_array']		= array('type'			=> 'array');
		$this->validation_rules_array['error_array']			= array('type'			=> 'array');
		$this->validation_rules_array['error']					= array('type'			=> 'bool');
		$this->validation_rules_array['process']				= array('type'			=> 'bool');
		$this->validation_rules_array['entry_state_has_zones']	= array('type'			=> 'bool');
		$this->validation_rules_array['coo_address']			= array('type'			=> 'object',
																		'object_type'	=> 'AddressModel');
		$this->validation_rules_array['coo_order']				= array('type'			=> 'object',
																 	 	'object_type'	=> 'order');
	}
	
	public function proceed()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('page_type', 'coo_order', 'customer_id'));
		
		if(empty($t_uninitialized_array) && $this->check_page_type($this->page_type))
		{
			// if there is nothing in the customers cart, redirect them to the shopping cart page
			$t_perform_redirect = $this->check_cart();
			if($t_perform_redirect)
			{
				// REDIRECT TO SHOPPING CART
				return true;
			}
			
			// SHIPPING: CHECK IF CART IS VIRTUAL -> REDIRECT TO PAYMENT
			$t_perform_redirect = $this->virtual_cart_redirect();
			if($t_perform_redirect)
			{
				// REDIRECT TO PAYMENT
				return true;
			}
			
			// INITIALIZE ADDRESS MODEL
			$this->coo_address = MainFactory::create_object('AddressModel');
			
			// SET CHECKOUT FILENAME
			$this->set_filename_checkout();

			// CHECK SUBMIT
			$t_perform_redirect = $this->check_submit();
			if($t_perform_redirect)
			{
				// REDIRECT
				return true;
			}

			// if no destination address was selected, use their own address as default
			if($this->page_type == 'shipping' && isset($_SESSION['sendto']) == false)
			{
				$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
			}
			elseif($this->page_type == 'payment' && isset($_SESSION['billto']) == false)
			{
				$_SESSION['billto'] = $_SESSION['customer_default_address_id'];
			}

			$coo_checkout_address_view = MainFactory::create_object('CheckoutAddressContentView');
			$t_error_message = '';
			if($GLOBALS['messageStack']->size('checkout_address') > 0)
			{
				$t_error_message = $GLOBALS['messageStack']->output('checkout_address');
			}
			$coo_checkout_address_view->set_error_message($t_error_message);
			$coo_checkout_address_view->set_coo_order($this->coo_order);

			if($this->page_type == 'shipping')
			{
				$coo_checkout_address_view->set_('address_book_id', $_SESSION['sendto']);			
			}
			elseif($this->page_type == 'payment')
			{
				$coo_checkout_address_view->set_('address_book_id', $_SESSION['billto']);			
			}

			$coo_checkout_address_view->set_page_type($this->page_type);
			$coo_checkout_address_view->set_language($_SESSION['language']);
			$coo_checkout_address_view->set_customer_id($_SESSION['customer_id']);
			$coo_checkout_address_view->setProcess($this->process);
			
			$this->v_output_buffer = $coo_checkout_address_view->get_html($this->process);
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return true;
		
	}
	
	protected function check_cart()
	{
		if($_SESSION['cart']->count_contents() <= 0)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_SHOPPING_CART));
			return true;
		}
		return false;
	}
	
	protected function virtual_cart_redirect()
	{
		if($this->page_type == 'shipping')
		{
			// if the order contains only virtual products, forward the customer to the billing page as
			// a shipping address is not needed
			if($this->coo_order->content_type == 'virtual')
			{
				$_SESSION['shipping'] = false;
				$_SESSION['sendto'] = false;
				$this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
				return true;
			}
		}
		return false;
	}
	
	protected function set_filename_checkout()
	{
		if($this->page_type == 'shipping')
		{
			$this->filename_checkout = FILENAME_CHECKOUT_SHIPPING;
		}
		elseif($this->page_type == 'payment')
		{
			$this->filename_checkout = FILENAME_CHECKOUT_PAYMENT;
		}
	}
	
	protected function check_submit()
	{
		if(isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] == 'submit'))
		{
			$t_perform_redirect = $this->check_process();
			if($t_perform_redirect)
			{
				// REDIRECT
				return true;
			}
		}
		return false;
	}
	
	protected function check_process()
	{
		// process a new address
		if(xtc_not_null($this->v_data_array['POST']['firstname'])
		   && xtc_not_null($this->v_data_array['POST']['lastname'])
		   && xtc_not_null($this->v_data_array['POST']['street_address'])
		   && xtc_not_null($this->v_data_array['POST']['postcode'])
		   && xtc_not_null($this->v_data_array['POST']['city'])
		   && xtc_not_null($this->v_data_array['POST']['country']))
		{
			$this->process = true;
			$this->process_new_address();
		}
		elseif(isset($this->v_data_array['POST']['address'])) // process the selected destination
		{
			if($this->page_type == 'shipping')
			{
				$this->set_new_shipping_address();
			}
			elseif($this->page_type == 'payment')
			{
				$this->set_new_payment_address();
			}
		}
		elseif(xtc_count_customer_address_book_entries() == MAX_ADDRESS_BOOK_ENTRIES
			   || (empty($this->v_data_array['POST']['gender'])
				   && empty($this->v_data_array['POST']['firstname'])
				   && empty($this->v_data_array['POST']['lastname'])
				   && empty($this->v_data_array['POST']['company'])
				   && empty($this->v_data_array['POST']['street_address'])
				   && empty($this->v_data_array['POST']['suburb'])
				   && empty($this->v_data_array['POST']['postcode'])
				   && empty($this->v_data_array['POST']['city'])
				   && empty($this->v_data_array['POST']['state']))) // no addresses to select from - customer decided to keep the current assigned address
		{
			if($this->page_type == 'shipping')
			{
				$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];

				$this->set_redirect_url(xtc_href_link($this->filename_checkout, '', 'SSL'));
				return true;
			}
			elseif($this->page_type == 'payment')
			{
				$_SESSION['billto'] = $_SESSION['customer_default_address_id'];

				$this->set_redirect_url(xtc_href_link($this->filename_checkout, '', 'SSL'));
				return true;
			}
		}
		
		return false;
	}
	
	protected function process_new_address()
	{
		$this->get_address_data_from_user_input();
		$this->validate_address_data();
		$this->_validatePrivacy();
		if($this->error == false)
		{
			$this->coo_address->set_('customers_id', $this->customer_id);
			$this->coo_address->save();
			
			if($this->page_type == 'shipping')
			{
				$_SESSION['sendto'] = xtc_db_insert_id();

				$this->set_redirect_url(xtc_href_link($this->filename_checkout, '', 'SSL'));
				return true;
			}
			elseif($this->page_type == 'payment')
			{
				$_SESSION['billto'] = xtc_db_insert_id();

				if(isset($_SESSION['payment']))
				{
					unset($_SESSION['payment']);
				}

				$this->set_redirect_url(xtc_href_link($this->filename_checkout, '', 'SSL'));
				return true;
			}
		}
	}
	
	protected function get_address_data_from_user_input()
	{
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
		$this->coo_address->set_('entry_country_id', xtc_db_prepare_input($this->v_data_array['POST']['country']));

		if(ACCOUNT_STATE == 'true')
		{
			$this->coo_address->set_('entry_zone_id', 0);
			$this->coo_address->set_('entry_state', xtc_db_prepare_input($this->v_data_array['POST']['state']));
			
			$t_query = 'SELECT
							COUNT(*) as total
						FROM
							' . TABLE_ZONES . '
						WHERE
							zone_country_id = "' . (int)$this->address_data['country'] . '"';
			$t_result = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_check = xtc_db_fetch_array($t_result);
				if($t_check['total'] > 0)
				{
					$this->entry_state_has_zones = true;
				}
			}

			if($this->entry_state_has_zones)
			{
				$t_query = 'SELECT DISTINCT
								zone_id
							FROM
								' . TABLE_ZONES . '
							WHERE
								zone_country_id = "' . (int)$this->address_data['country'] . '"
								AND (zone_name like "' . xtc_db_input($this->address_data['state']) . '%" or zone_code like "%' . xtc_db_input($this->address_data['state']) . '%")';
				$t_result = xtc_db_query($t_query);
				if(xtc_db_num_rows($t_result) > 1)
				{
					$t_query = 'SELECT DISTINCT
									zone_id
								FROM
									' . TABLE_ZONES . '
								WHERE
									zone_country_id = "' . (int)$this->address_data['country'] . '" and zone_name = "' . $this->address_data['state'] . '"';
					$t_result = xtc_db_query($t_query);
				}
				if(xtc_db_num_rows($t_result) >= 1)
				{
					$t_zone = xtc_db_fetch_array($t_result);
					$this->coo_address->set_('entry_zone_id', $t_zone['zone_id']);
				}
				else
				{
					$this->coo_address->set_('entry_zone_id', -1);
				}
			}
		}

		if(isset($this->v_data_array['POST']['b2b_status']))
		{
			$this->coo_address->set_('customer_b2b_status', (int)$this->v_data_array['POST']['b2b_status']);
		}
	}
	
	protected function validate_address_data()
	{
		$BlockPackstation = $this->page_type == 'payment';
		$coo_form_validation_control = MainFactory::create_object('FormValidationControl');
		$this->error_array = $coo_form_validation_control->validate_address($this->coo_address, $BlockPackstation);
		
		if(empty($this->error_array) == false)
		{
			$this->error = true;
		}
	}
	
	protected function set_new_shipping_address()
	{
		$reset_shipping = false;
		if(isset($_SESSION['sendto']) && $_SESSION['sendto'] != $this->v_data_array['POST']['address'] && isset($_SESSION['shipping']))
		{
			$reset_shipping = true;
		}

		$_SESSION['sendto'] = (int)$this->v_data_array['POST']['address'];

		$check_address_query = xtc_db_query("SELECT COUNT(*) AS total 
														FROM " . TABLE_ADDRESS_BOOK . " 
														WHERE
															customers_id = '" . $_SESSION['customer_id'] . "' AND 
															address_book_id = '" . $_SESSION['sendto'] . "'");
		$check_address = xtc_db_fetch_array($check_address_query);

		if($check_address['total'] == '1')
		{
			if($reset_shipping == true)
			{
				unset($_SESSION['shipping']);
			}

			if(xtc_count_customer_address_book_entries() == MAX_ADDRESS_BOOK_ENTRIES
			   ||	(empty($this->v_data_array['POST']['gender'])
					  && empty($this->v_data_array['POST']['firstname'])
					  && empty($this->v_data_array['POST']['lastname'])
					  && empty($this->v_data_array['POST']['company'])
					  && empty($this->v_data_array['POST']['street_address'])
					  && empty($this->v_data_array['POST']['suburb'])
					  && empty($this->v_data_array['POST']['postcode'])
					  && empty($this->v_data_array['POST']['city'])
					  && empty($this->v_data_array['POST']['state']))
			)
			{
				$this->set_redirect_url(xtc_href_link($this->filename_checkout, '', 'SSL'));
				return true;
			}
		}
		else
		{
			unset($_SESSION['sendto']);
		}
		return false;
	}
	
	protected function set_new_payment_address()
	{
		$t_new_address_id = $this->get_new_address_id();
		if($t_new_address_id == 0)
		{
			trigger_error('new address_id is empty', E_USER_ERROR);
		}
		
		$t_reset_shipping = false;
		if(isset($_SESSION['billto']) && $_SESSION['billto'] != $t_new_address_id && isset($_SESSION['payment']))
		{
			$t_reset_shipping = true;
		}

		$_SESSION['billto'] = $t_new_address_id;
		
		$t_checked = $this->check_address($t_new_address_id);
		if($t_checked)
		{
			if($t_reset_shipping == true)
			{
				unset($_SESSION['payment']);
			}
			if(xtc_count_customer_address_book_entries() == MAX_ADDRESS_BOOK_ENTRIES
			   ||	(empty($this->v_data_array['POST']['gender'])
					  && empty($this->v_data_array['POST']['firstname'])
					  && empty($this->v_data_array['POST']['lastname'])
					  && empty($this->v_data_array['POST']['company'])
					  && empty($this->v_data_array['POST']['street_address'])
					  && empty($this->v_data_array['POST']['suburb'])
					  && empty($this->v_data_array['POST']['postcode'])
					  && empty($this->v_data_array['POST']['city'])
					  && empty($this->v_data_array['POST']['state']))
			)
			{
				$this->set_redirect_url(xtc_href_link($this->filename_checkout, '', 'SSL'));
				return true;
			}
		}
		else
		{
			unset($_SESSION['billto']);
		}
		return false;
	}
	
	protected function get_new_address_id()
	{
		return (int)$this->v_data_array['POST']['address'];
	}
	
	protected function check_address($p_address_id)
	{
		$c_address_id = (int)$p_address_id;
		
		$t_query = 'SELECT
						COUNT(*) as total
					FROM
						' . TABLE_ADDRESS_BOOK . '
					WHERE
						customers_id = "' . $this->customer_id . '" 
						AND address_book_id = "' . $c_address_id . '"';
		
		$t_result = xtc_db_query($t_query);
		$t_row = xtc_db_fetch_array($t_result);
		if($t_row['total'] == '1')
		{
			return true;	
		}
		return false;
	}

	public function check_page_type($p_page_type)
	{
		if(in_array($p_page_type, $this->page_types_array))
		{
			return true;
		}
		
		return false;
	}
	
	public function add_page_type($p_page_type)
	{
		if(is_array($this->page_types_array) == false)
		{
			$this->page_types_array = array();
		}
		
		$this->page_types_array[] = $p_page_type;
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
				$this->error = true;
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
				$this->error = true;
			}
		}
	}
}
