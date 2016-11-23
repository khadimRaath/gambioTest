<?php
/* --------------------------------------------------------------
  CheckoutAddressContentView.inc.php 2014-11-06 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
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
require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'xtc_get_address_format_id.inc.php');
require_once(DIR_FS_INC . 'xtc_address_format.inc.php');

class CheckoutAddressContentView extends ContentView
{
	protected $address_book_id;
	protected $coo_order;
	protected $customer_id;
	protected $error_message;
	protected $language;
	protected $page_type;
	
	protected $filenameCheckoutAddress;
	protected $filenameCheckout;
	protected $process = false;
	protected $unallowedClassesArray;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);

		$this->unallowedClassesArray = array();
		$this->unallowedClassesArray[] = 'packstation';
		$this->unallowedClassesArray[] = 'postfiliale';
	}


	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('address_book_id',
																		  'coo_order',
																		  'customer_id',
																		  'error_message',
																		  'language',
																		  'page_type',
																		  'process'));
		if(empty($t_uninitialized_array))
		{
			$this->initPageData();
			
			$this->_assignFormData();
			$this->_assignErrorMessage();
			
			if($this->process === false)
			{
				$this->_assignCurrentAddress();
				$this->_assignAllAddresses();
			}
			
			$this->_assignNewAddressModule();
			$this->_assignUrls();
			$this->_assignLightboxData();
			
			$this->_assignDeprecated();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}


	public function initPageData()
	{
		if($this->page_type == 'shipping')
		{
			$this->set_content_template('module/checkout_shipping_address.html');
			$this->filenameCheckoutAddress = FILENAME_CHECKOUT_SHIPPING_ADDRESS;
			$this->filenameCheckout = FILENAME_CHECKOUT_SHIPPING;
		}
		elseif($this->page_type == 'payment')
		{
			$this->set_content_template('module/checkout_payment_address.html');
			$this->filenameCheckoutAddress = FILENAME_CHECKOUT_PAYMENT_ADDRESS;
			$this->filenameCheckout = FILENAME_CHECKOUT_PAYMENT;
		}
	}


	protected function _assignFormData()
	{
		$this->set_content_data('FORM_ACTION_URL', xtc_href_link($this->filenameCheckoutAddress, '', 'SSL'));
	}
	
	
	protected function _assignDeprecated()
	{
		$this->set_content_data('FORM_ACTION', xtc_draw_form('checkout_address', xtc_href_link($this->filenameCheckoutAddress, '', 'SSL'), 'post', ''), 2);
		$this->set_content_data('FORM_END', '</form>', 2);

		$this->set_content_data('BUTTON_BACK', '<a href="javascript:history.go(-1)"><img src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $this->language . '/backgr.gif" /></a>', 2);
		$this->set_content_data('BUTTON_CONTINUE', xtc_draw_hidden_field('action', 'submit') . xtc_image_submit('contgr.gif', IMAGE_BUTTON_CONTINUE), 2);
	}
	
	
	protected function _assignDeprecatedAddressesHtml(array $addressesArray)
	{
		$html = '<table class="checkout_addresses" border="0" width="100%" cellspacing="0" cellpadding="0">';
		
		foreach($addressesArray as $addressArray)
		{
			$html .= '<tr>
						<td>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td><strong>' . $addressArray['firstname'] . ' ' . $addressArray['lastname'] . '</strong></td>
									<td align="right">' . xtc_draw_radio_field('address', $addressArray['address_book_id'], ($addressArray['address_book_id'] == $this->address_book_id)) . '</td>
								</tr>
								<tr>
									<td colspan="2">
										<table border="0" cellspacing="0" cellpadding="5">
											<tr>
												<td>' . $addressArray['address'] . '</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>';
		}

		$html .= '</table>';

		$this->set_content_data('BLOCK_ADDRESS', $html, 2);
	}


	protected function _assignCurrentAddress()
	{
		$this->set_content_data('ADDRESS_LABEL', xtc_address_label($this->customer_id, $this->address_book_id, true, ' ', '<br />'));
		$this->set_content_data('CURRENT_ADDRESS_BOOK_ID', $this->address_book_id);
	}


	protected function _assignAllAddresses()
	{
		if(xtc_count_customer_address_book_entries() > 1)
		{
			$addressesDataArray = array();
			
			$query = $this->_buildAllAddressesQuery();
			
			$result = xtc_db_query($query);
			while($row = xtc_db_fetch_array($result))
			{
				if($this->_isUnallowedAddress($row['address_class'], $row['street_address']))
				{
					continue;
				}

				$this->_addAddress($row, $addressesDataArray);
			}

			$this->set_content_data('addresses_data', $addressesDataArray);
			
			$this->_assignDeprecatedAddressesHtml($addressesDataArray);
		}
	}


	/**
	 * @param array $addressDataArray
	 * @param array $addressesDataArray
	 */
	protected function _addAddress(array $addressDataArray, array &$addressesDataArray)
	{
		$formatId = xtc_get_address_format_id($addressDataArray['country_id']);

		$addressesDataArray[] = array(
			'firstname' => $addressDataArray['firstname'],
			'lastname' => $addressDataArray['lastname'],
			'address_book_id' => $addressDataArray['address_book_id'],
			'address' => xtc_address_format($formatId, $addressDataArray, true, ' ', ', '),
		);
	}
	
	

	/**
	 * @return string
	 */
	protected function _buildAllAddressesQuery()
	{
		$query = "SELECT 
							address_class, 
							address_book_id, 
							entry_firstname AS firstname, 
							entry_lastname AS lastname, 
							entry_company AS company, 
							entry_street_address AS street_address, 
							entry_house_number AS house_number, 
							entry_additional_info AS additional_address_info, 
							entry_suburb AS suburb, 
							entry_city AS city, 
							entry_postcode AS postcode, 
							entry_state AS state, 
							entry_zone_id AS zone_id, 
							entry_country_id AS country_id
						FROM " . TABLE_ADDRESS_BOOK . " 
						WHERE customers_id = '" . (int)$this->customer_id . "'";
		
		return $query;
	}


	/**
	 * @param string $addressClass
	 * @param string $streetAddress
	 *
	 * @return bool
	 */
	protected function _isUnallowedAddress($addressClass, $streetAddress)
	{
		if($this->page_type == 'payment' &&
		   (in_array((string)$addressClass, $this->unallowedClassesArray)
			|| preg_match('/.*(' . implode('|', $this->unallowedClassesArray) . ').*/i', (string)$streetAddress) == 1
		   )
		)
		{
			return true;
		}
		
		return false;
	}
	

	protected function _assignNewAddressModule()
	{
		if(xtc_count_customer_address_book_entries() < MAX_ADDRESS_BOOK_ENTRIES)
		{
			/* @var CheckoutNewAddressContentControl $newAddressControl */
			$newAddressControl = MainFactory::create_object('CheckoutNewAddressContentControl');
			$newAddressControl->set_data('GET', $_GET);
			$newAddressControl->set_data('POST', $_POST);
			$newAddressControl->set_('page_type', $this->page_type);
			$newAddressControl->proceed();

			$html = $newAddressControl->get_response();

			$this->set_content_data('MODULE_new_address', $html);
		}
	}
	
	
	protected function _assignUrls()
	{
		$this->set_content_data('BUTTON_BACK_LINK', xtc_href_link($this->filenameCheckout, '', 'SSL'));

		if(function_exists('gm_get_privacy_link'))
		{
			$this->set_content_data('GM_PRIVACY_LINK', gm_get_privacy_link('GM_CHECK_PRIVACY_CHECKOUT_PAYMENT'));
		}
	}


	protected function _assignLightboxData()
	{
		$this->set_content_data('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
	}


	protected function _assignErrorMessage()
	{
		if(empty($this->error_message) == false)
		{
			$this->set_content_data('error', $this->error_message);
		}
	}


	/**
	 * @param bool $p_process
	 */
	public function setProcess($p_process)
	{
		$this->process = (bool)$p_process;
	}


	/**
	 * @return bool
	 */
	public function getProcess()
	{
		return $this->process;
	}


	/**
	 * @param int $p_address_book_id
	 */
	public function set_address_book_id($p_address_book_id)
	{
		$this->address_book_id = (int)$p_address_book_id;
	}


	/**
	 * @return mixed
	 */
	public function get_address_book_id()
	{
		return $this->address_book_id;
	}


	/**
	 * @param order $order
	 */
	public function set_coo_order(order $order)
	{
		$this->coo_order = $order;
	}


	/**
	 * @return mixed
	 */
	public function get_coo_order()
	{
		return $this->coo_order;
	}


	/**
	 * @param int $p_customer_id
	 */
	public function set_customer_id($p_customer_id)
	{
		$this->customer_id = (int)$p_customer_id;
	}


	/**
	 * @return mixed
	 */
	public function get_customer_id()
	{
		return $this->customer_id;
	}


	/**
	 * @param string $p_error_message
	 */
	public function set_error_message($p_error_message)
	{
		$this->error_message = (string)$p_error_message;
	}


	/**
	 * @return mixed
	 */
	public function get_error_message()
	{
		return $this->error_message;
	}


	/**
	 * @param string $p_language
	 */
	public function set_language($p_language)
	{
		$this->language = (string)$p_language;
	}


	/**
	 * @return string
	 */
	public function get_language()
	{
		return $this->language;
	}


	/**
	 * @param string $p_page_type
	 */
	public function set_page_type($p_page_type)
	{
		$this->page_type = (string)$p_page_type;
	}


	/**
	 * @return string
	 */
	public function get_page_type()
	{
		return $this->page_type;
	}
}