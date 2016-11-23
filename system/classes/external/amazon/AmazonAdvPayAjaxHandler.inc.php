<?php
/* --------------------------------------------------------------
	AmazonAdvPayHandler.inc.php 2015-09-22
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class AmazonAdvPayAjaxHandler extends AjaxHandler
{
	protected $_coo_aap;

	public function get_permission_status($p_customers_id = null)
	{
		$this->_coo_aap = MainFactory::create_object('AmazonAdvancedPayment');
		$t_is_enabled = $this->_coo_aap->is_enabled();
		return $t_is_enabled;
	}

	public function proceed()
	{
		$t_success = false;
		$t_output_array = array();

		if(isset($this->v_data_array['POST']['orderrefid']) === false)
		{
			$t_output_array['error_msg'] = 'no order reference id';
		}
		else
		{
			$t_order_reference_id = $this->v_data_array['POST']['orderrefid'];

			if($this->v_data_array['POST']['action'] == 'signIn')
			{
				try
				{
					$t_order_reference_data = $this->_coo_aap->get_order_reference_details($t_order_reference_id);
					$t_output_array['order_ref_id'] = $t_order_reference_id;
					$_SESSION['amazonadvpay_order_ref_id'] = $t_order_reference_id;
					if(empty($_SESSION['customer_id']) === true)
					{
						// create guest account, log in
						$this->_make_guest_account($t_order_reference_data);
						$_SESSION['amazonadvpay_guest'] = true;
					}
					$t_ab_id = $this->_coo_aap->get_amazon_address_book_entry($_SESSION['customer_id']);
					$_SESSION['sendto'] = $t_ab_id;
					$_SESSION['billto'] = $t_ab_id;
					$_SESSION['payment'] = 'amazonadvpay';

					$order = new order();
					$t_order_reference_details = $this->_coo_aap->set_order_amount($_SESSION['amazonadvpay_order_ref_id'], $order->info['total'], $order->info['currency']);

					$t_output_array['continue'] = 'true';
				}
				catch(Exception $e)
				{
					$t_output_array['error_msg'] = $e->getMessage();
				}
			}

			if($this->v_data_array['POST']['action'] == 'signOut')
			{
				unset($_SESSION['amazonadvpay_order_ref_id']);
				unset($_SESSION['sendto']);
				unset($_SESSION['billto']);
				unset($_SESSION['payment']);

				if(isset($_SESSION['amazonadvpay_guest']))
				{
					unset($_SESSION['account_type']);
					unset($_SESSION['customer_id']);
					unset($_SESSION['customer_first_name']);
					unset($_SESSION['customer_last_name']);
					unset($_SESSION['customer_default_address_id']);
					unset($_SESSION['customer_country_id']);
					unset($_SESSION['customer_zone_id']);
					unset($_SESSION['customer_vat_id']);
					unset($_SESSION['amazonadvpay_guest']);
					unset($_SESSION['amazonadvpay_logout_guest']);
					$t_output_array['redirect_url'] = GM_HTTP_SERVER.DIR_WS_CATALOG.'login.php';
				}
				else
				{
					$t_output_array['redirect_url'] = GM_HTTP_SERVER.DIR_WS_CATALOG.'shopping_cart.php';
				}
			}

			if($this->v_data_array['POST']['action'] == 'addressSelect')
			{
				try
				{
					$t_reload_required = false;
					$t_order_reference_data = $this->_coo_aap->get_order_reference_details($t_order_reference_id);
					$t_shipping_destination_iso2 = (string)$t_order_reference_data->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->CountryCode;
					$t_shipping_destination_postal_code = (string)$t_order_reference_data->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->PostalCode;
					$t_shipping_destination_city = (string)$t_order_reference_data->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->City;
					$t_ab_data = array(
							'country_iso2' => $t_shipping_destination_iso2,
							'entry_postcode' => $t_shipping_destination_postal_code,
							'entry_city' => $t_shipping_destination_city,
						);
					$this->_coo_aap->update_amazon_address_book_entry($_SESSION['customer_id'], $t_ab_data);
					$t_country_is_allowed = $this->_coo_aap->country_is_allowed($t_shipping_destination_iso2);
					if($t_country_is_allowed && $t_shipping_destination_iso2 != $_SESSION['delivery_zone'])
					{
						$t_reload_required = true;
					}
					$t_output_array['reload'] = $t_reload_required == true ? 'true' : 'false';
					$t_output_array['country_allowed'] = $t_country_is_allowed == true ? 'true' : 'false';
				}
				catch(Exception $e)
				{
					$t_output_array['error_msg'] = $e->getMessage();
				}
			}

			if($this->v_data_array['POST']['action'] == 'confirm_new_payment' && isset($_SESSION['amazonadvpay_rejected_orderref']))
			{
				$t_order_reference_id = $_SESSION['amazonadvpay_rejected_orderref'];
				try
				{
					$this->_coo_aap->confirm_order($t_order_reference_id);
					$t_output_array['result'] = 'order_confirmed';
					unset($_SESSION['amazonadvpay_rejected_orderref']);
				}
				catch(Exception $e)
				{
					$t_output_array['error_msg'] = $e->getMessage();
				}
			}

		}

		$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$t_output_json = $coo_json->encode($t_output_array);
		$this->v_output_buffer = $t_output_json;

		return $t_success;
	}

	protected function _make_guest_account($p_order_reference_data)
	{
		if(isset($_SESSION['customer_id']) && $_SESSION['customer_id'] > 0) {
			// user already logged in, don't do anything
			return;
		}

		require_once DIR_FS_CATALOG.'inc/xtc_create_password.inc.php';
		require_once DIR_FS_CATALOG.'inc/xtc_encrypt_password.inc.php';

		$this->_coo_aap->log('creating guest account for order reference '.$_SESSION['amazonadvpay_order_ref_id']);

		$customers_status = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
		$password = xtc_create_password(8);
		$vat = '';
		$firstname = 'Amazon';
		$lastname = 'Amazon';
		$email = 'amazonguest-'.uniqid().'@amazonpayments.example.com';
		$phone = '';
		$street_address = 'Amazon';
		$postcode = '00000';
		$city = 'Amazon';

		$sql_data_array = array(
			'customers_vat_id' => $vat,
			'customers_vat_id_status' => 0,
			'customers_status' => $customers_status,
			'customers_firstname' => $firstname,
			'customers_lastname' => $lastname,
			'customers_email_address' => $email,
			'customers_telephone' => $phone,
			'customers_fax' => '',
			'customers_newsletter' => '',
			'account_type' => '1',
			'customers_password' => xtc_encrypt_password($password),
			'customers_gender' => '',
			'customers_dob' => '',
		);

		xtc_db_perform(TABLE_CUSTOMERS, $sql_data_array);

		$_SESSION['account_type'] = '1';
		$_SESSION['customer_id'] = xtc_db_insert_id();

		$sql_data_array = array(
			'customers_id' => $_SESSION['customer_id'],
			'entry_firstname' => $firstname,
			'entry_lastname' => $lastname,
			'entry_street_address' => $street_address,
			'entry_postcode' => $postcode,
			'entry_city' => $city,
			'entry_company' => '',
			'entry_suburb' => '',
			'entry_country_id' => STORE_COUNTRY,
			'entry_zone_id' => STORE_ZONE,
			'entry_state' => '',
			'address_class' => 'amzadvpay_guest',
		);

		xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
		$address_id = xtc_db_insert_id();

		xtc_db_query("update ".TABLE_CUSTOMERS." set customers_cid = '".$_SESSION['customer_id']."', customers_default_address_id = '".$address_id."' where customers_id = '".(int) $_SESSION['customer_id']."'");
		xtc_db_query("insert into ".TABLE_CUSTOMERS_INFO." (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('".(int) $_SESSION['customer_id']."', '0', now())");

		$_SESSION['customer_first_name'] = $firstname;
		$_SESSION['customer_last_name'] = $lastname;
		$_SESSION['customer_default_address_id'] = $address_id;
		$_SESSION['customer_country_id'] = $country;
		$_SESSION['customer_zone_id'] = $zone_id;
		$_SESSION['customer_vat_id'] = $vat;
		$_SESSION['amazonadvpay_guest'] = true;
	}
}