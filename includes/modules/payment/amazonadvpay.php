<?php
/* --------------------------------------------------------------
	amazonadvpay.php 2015-09-10 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class amazonadvpay_ORIGIN {
	var $code, $title, $description;
	var $tmpOrders = true;
	var $tmpStatus = 0;
	var $enabled;
	var $_coo_apa;

	public function __construct() {
		$this->_coo_apa = MainFactory::create_object('AmazonAdvancedPayment');
		$t_order = $GLOBALS['order'];
		$this->code = 'amazonadvpay';
		$this->title = MODULE_PAYMENT_AMAZONADVPAY_TEXT_TITLE;
		$t_config_button = '<div class="add-margin-top-20"><a class="btn" href="' . GM_HTTP_SERVER . DIR_WS_ADMIN . 'admin.php?do=AmazonAdvPaymentsModuleCenterModule">' . $this->_coo_apa->get_text('configure') . '</a></div>';
		$this->description = MODULE_PAYMENT_AMAZONADVPAY_TEXT_DESCRIPTION . $t_config_button;
		$this->sort_order = MODULE_PAYMENT_AMAZONADVPAY_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_AMAZONADVPAY_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_AMAZONADVPAY_TEXT_INFO;
		$this->order_status = MODULE_PAYMENT_AMAZONADVPAY_ORDER_STATUS_ID;

		if(is_object($t_order)) {
			$this->update_status();
		}
	}

	function update_status() {
		$t_order = $GLOBALS['order'];
	}

	function javascript_validation() {
		return false;
	}

	function selection() {
		$selection = array(
			'id' => $this->code,
			'module' => $this->title,
			'description' => $this->info,
		);
		return $selection;
	}

	function pre_confirmation_check() {
		if(isset($_SESSION['amazonadvpay_order_ref_id']) === false)
		{
			if($_SESSION['payment'] == $this->code)
			{
				xtc_redirect(xtc_href_link('shopping_cart.php#amazonlogin'));
			}
			else
			{
				xtc_redirect(xtc_href_link('shopping_cart.php'));
			}
		}

		$t_order_reference_data = $this->_coo_apa->get_order_reference_details($_SESSION['amazonadvpay_order_ref_id']);
		if($_SESSION['cart']->get_content_type() != 'virtual')
		{
			$t_shipping_destination_iso2 = (string)$t_order_reference_data->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->CountryCode;
			$t_country_is_allowed = $this->_coo_apa->country_is_allowed($t_shipping_destination_iso2);
			if($t_country_is_allowed !== true)
			{
				xtc_redirect(xtc_href_link('checkout_shipping.php'));
			}
		}
		return false;
	}

	function confirmation() {
		$confirmation = array(
			'title' => MODULE_PAYMENT_AMAZONADVPAY_TEXT_DESCRIPTION,
		);
		return $confirmation;
	}

	function refresh() {
	}

	function process_button() {
		$pb = '';
		$order = $GLOBALS['order'];
		$t_output = print_r($order, true);
		try
		{
			$t_order_reference_details = $this->_coo_apa->set_order_amount($_SESSION['amazonadvpay_order_ref_id'], $order->info['total'], $order->info['currency']);
			//$t_order_reference_details = $this->_coo_apa->get_order_reference_details($_SESSION['amazonadvpay_order_ref_id']);
			$t_output .= $t_order_reference_details->asXML();
		}
		catch(Exception $e)
		{
			$t_output .= $e->getMessage();
		}
		# $pb = '<pre>'.htmlspecialchars($t_output).'</pre>';
		return $pb;
	}

	function payment_action() {
		// order saved, finalize
		$insert_id = $GLOBALS['insert_id'];
		$order = new order($insert_id);
		try
		{
			$this->_coo_apa->confirm_order($_SESSION['amazonadvpay_order_ref_id'], $insert_id, $order->info['pp_total'], $order->info['currency']);
			$t_update_customer_data = $_SESSION['amazonadvpay_guest'] == true;
			$this->_coo_apa->update_delivery_address($_SESSION['amazonadvpay_order_ref_id'], $insert_id, $t_update_customer_data);
			$this->_coo_apa->delete_amazon_address_book_entries($_SESSION['customer_id']);
			if($this->_coo_apa->erp_mode == false && $this->_coo_apa->authorization_mode != 'manual')
			{
				$t_immediate_capture = $this->_coo_apa->capture_mode == 'immediate';
				$t_authorization_timeout = $this->_coo_apa->get_authorization_timeout();
				$t_authorization_note = '';
				if($this->_coo_apa->mode == 'sandbox' && $_SESSION['comments'][0] == '{')
				{
					$t_authorization_note = $_SESSION['comments'];
					$this->_coo_apa->log('Authorization in sandbox simulation mode, SellerAuthorizationNote: '.$t_authorization_note);
				}
				$t_authorization_response = $this->_coo_apa->authorize_payment(
					$_SESSION['amazonadvpay_order_ref_id'],
					$order->info['pp_total'],
					$order->info['currency'],
					$t_authorization_timeout,
					$t_immediate_capture,
					$t_authorization_note
				);
				unset($_SESSION['amazonadvpay_authrejected']);
				$t_authorization_details = $t_authorization_response->AuthorizeResult->AuthorizationDetails;
				if(empty($t_authorization_details->AuthorizationBillingAddress) === true)
				{
					// no billing address in authorization details - this happens if Amazon does not have a valid VAT ID on file for the merchant
					// use delivery address instead
					$_SESSION['billto'] = $_SESSION['sendto'];
					$this->_coo_apa->copy_delivery_address_to_billing_address($insert_id);
				}

				$t_state = (string)$t_authorization_details->AuthorizationStatus->State;
				$t_reason_code = (string)$t_authorization_details->AuthorizationStatus->ReasonCode;
				if($t_state == 'Declined' && $t_reason_code == 'InvalidPaymentMethod')
				{
					$this->_coo_apa->log('authorization Declined/InvalidPaymentMethod in checkout');
					$_SESSION['amazonadvpay_authrejected'] = 'InvalidPaymentMethod';
					$_SESSION['amazonadvpay_rejected_orderref'] = $_SESSION['amazonadvpay_order_ref_id'];
				}
				if($t_state == 'Declined' && $t_reason_code == 'AmazonRejected')
				{
					$this->_coo_apa->log('authorization Declined/AmazonRejected in checkout');
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
					}
					$_SESSION['info_message'] = $this->_coo_apa->get_text('note_authorization_rejected');
					xtc_redirect(xtc_href_link('shopping_cart.php#amazonpaymentsunavailable'));
				}
				if($t_state == 'Declined' && $t_reason_code == 'TransactionTimedOut')
				{
					$this->_coo_apa->log('authorization Declined/TransactionTimedOut in checkout');
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
					}
					$_SESSION['info_message'] = $this->_coo_apa->get_text('note_authorization_timedout');
					xtc_redirect(xtc_href_link('shopping_cart.php#amazonpaymentsunavailable'));
				}
			}
			unset($_SESSION['amazonadvpay_order_ref_id']);
		}
		catch(Exception $e)
		{
			$_SESSION['amazonadvpay_error'] = $e->getMessage();
			xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
		}
		// mark order with final status
		xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");

		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'));
	}

	function before_process() {
		return true;
	}

	function after_process() {
		return true;
	}

	function get_error() {
		$error = false;
		if(isset($_SESSION['amazonadvpay_error']))
		{
			$error = array('error' => $_SESSION['amazonadvpay_error']);
			unset($_SESSION['amazonadvpay_error']);
		}
		return $error;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		$config = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_PAYMENT_".strtoupper($this->code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
		}
	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		foreach($ckeys as $k) {
			$keys[] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_'.$k;
		}
		return $keys;
	}

	function _configuration() {
		$config = array(
			'STATUS' => array(
				'configuration_value' => 'True',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'ALLOWED' => array(
				'configuration_value' => '',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '0',
			),
			'ORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
		);

		return $config;
	}

}

MainFactory::load_origin_class('amazonadvpay');
