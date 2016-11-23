<?php
/* --------------------------------------------------------------
   paygate.php 2014-07-15 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Pl�nkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

if(!function_exists('hex2bin')) { // stand-in for hex2bin() in PHP 5.4.x+
	function hex2bin($h) {
		if(!is_string($h)) {
			return null;
		}
		$r = '';
		for($a=0; $a<strlen($h); $a+=2) {
			$r .= chr(hexdec($h{$a}.$h{($a+1)}));
		}
		return $r;
	}
}

/**
 * Base class for Paygate modules
 * 
 * This is actually an abstract class (but API is based on PHP4) which has to be extended by classes for specific
 * payment methods. This class implements both the Gambio/xt:Commerce interface for payment modules and parts of
 * the Paygate interface (esp. related to encryption and hashing). 
 */
class paygate_ORIGIN {
	var $code, $title, $description, $enabled, $info;
	var $tmpOrders = true;
	var $tmpStatus = 2; 
	var $logger;
	
	/**
	 * Constructor
	 * @global order $order 
	 */
	public function __construct() {
		global $order;
		$this->logger = LogControl::get_instance();
		$uppercode = strtoupper($this->code);
		$this->title = @constant('MODULE_PAYMENT_'.$uppercode.'_TEXT_TITLE');
		$this->description = @constant('MODULE_PAYMENT_'.$uppercode.'_TEXT_DESCRIPTION');
		$this->description .= $this->_requirementsNotice();
		$this->info = @constant('MODULE_PAYMENT_'.$uppercode.'_TEXT_INFO');
		$this->sort_order = @constant('MODULE_PAYMENT_'.$uppercode.'_SORT_ORDER');
		$this->enabled = ((@constant('MODULE_PAYMENT_'.$uppercode.'_STATUS') == 'True') ? true : false);
		if ((int)@constant('MODULE_PAYMENT_'.$uppercode.'_ORDER_STATUS_ID') > 0) {
			$this->order_status = @constant('MODULE_PAYMENT_'.$uppercode.'_ORDER_STATUS_ID');
		}
		if ((int)@constant('MODULE_PAYMENT_'.$uppercode.'_ORDER_STATUS_ID_ONGOING') > 0) {
			$this->order_status_ongoing = @constant('MODULE_PAYMENT_'.$uppercode.'_ORDER_STATUS_ID_ONGOING');
			$this->tmpStatus = $this->order_status_ongoing;
		}
		if ((int)@constant('MODULE_PAYMENT_'.$uppercode.'_ORDER_STATUS_ID_FAILED') > 0) {
			$this->order_status_ongoing = @constant('MODULE_PAYMENT_'.$uppercode.'_ORDER_STATUS_ID_FAILED');
		}
		if(is_object($order)) {
			$this->update_status();
		}
	}
	

	/**
	 * Return extra parts for the form on checkout_confirmation
	 * Not used in this module b/c temporary orders mechanism is required.
	 * @return string 
	 */
	function process_button() {
		$pbutton = '';
		return $pbutton;
	}

	/**
	 * Determine if module is configured to be used for customer's shipping zone
	 */
	function update_status() {
		global $order;
		if (($this->enabled == true) && ((int) constant('MODULE_PAYMENT_'. strtoupper($this->code) .'_ZONE') > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".constant('MODULE_PAYMENT_'. strtoupper($this->code) .'_ZONE')."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	/**
	 * Javascript for inclusion in the <head> of checkout_payment; unused
	 * @return boolean 
	 */
	function javascript_validation() {
		return false;
	}

	/**
	 * Provides entry for list of available payment modules on checkout_payment
	 * @return type 
	 */
	function selection() {
		return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
	}

	/**
	 * Hook called by checkout_confirmation
	 */
	function pre_confirmation_check() {
		return false;
	}

	/**
	 * Provides information for checkout_confirmation page
	 * @return type 
	 */
	function confirmation() {
		return array ('title' => constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION'));
	}

	/**
	 * Upon redirection from payment service, this methods provides processing according to the result code received from payment service
	 * 
	 * This is called twice in the course of a normal checkout; first just before redirection to the payment service and a second time
	 * when the payment service redirects the customer back to the shop. When checkout_process is first called, a temporary order is saved
	 * and the customer is redirected to Paygate. After the customer has provided his payment information he is redirected back into
	 * the shop and the order is updated accordingly.
	 * 
	 * @return boolean 
	 */
	function before_process() {
		if($_SESSION['payment'] == $this->code && isset($_SESSION['tmp_oID'])) {
			// temporary order for Paygate present
			$data = $this->_decodeRequest();
			
			if($data === false || !isset($data['Code'])) {
				$this->_log("Payment failed (invalid request for checkout_process), redirecting to checkout_payment");
				$_SESSION[$this->code .'_error']['error'] = 'invalid request';
				xtc_redirect(HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code);
			}
			
			if($data['Code'] != 0) {
				$logmsg =  $data['Code'] .' - '. $data['Description'];
				$this->_log("Payment failed ($logmsg), redirecting to checkout_payment");
				//$_SESSION[$this->code]['error'] = $data['Description'];
				$_SESSION[$this->code .'_error']['error'] = constant('MODULE_PAYMENT_'. strtoupper($this->code) .'_PAYMENT_ERROR');
				xtc_redirect(HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code);
			}
		}
		return false;
	}

	/**
	 * Updates orders' status after it has been processed by the shop.
	 * 
	 * As any errors that might occur during payment have been taken care of by before_process(), this method can safely
	 * set the orders' status to 'paid'.
	 * 
	 * @global type $insert_id 
	 */
	function after_process() {
		global $insert_id;
				
		if($this->order_status) {
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
		}
	}

	
	/**
	 * Returns error message (if any) for displaying on checkout_payment
	 * @return boolean 
	 */
	function get_error() {
		if(isset($_SESSION[$this->code.'_error']['error'])) {
			return array('error' => $_SESSION[$this->code.'_error']['error']);
		}
		else {
			return false;
		}
	}

	/**
	 * Check module's status
	 * @return type 
	 */
	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	/**
	 * Provides modules' configuration settings (used by install())
	 * @return array 
	 */
	function _configuration() {
		$config = array(
			'STATUS' => array(
				'configuration_value' => 'True',
				'use_function' => '',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'MERCHANTID' => array(
				'configuration_value' => '',
				'use_function' => '',
				'set_function' => '',
			),
			'PASS' => array(
				'configuration_value' => '',
				'use_function' => '',
				'set_function' => '',
			),
			'HMACKEY' => array(
				'configuration_value' => '',
				'use_function' => '',
				'set_function' => '',
			),
			'ALLOWED' => array(
				'configuration_value' => '',
				'use_function' => '',
				'set_function' => '',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '1',
				'use_function' => '',
				'set_function' => '',
			),
			'ZONE' => array(
				'configuration_value' => '',
				'use_function' => 'xtc_get_zone_class_title',
				'set_function' => 'xtc_cfg_pull_down_zone_classes(',
			),
			'ORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'use_function' =>  'xtc_get_order_status_name',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
			),
			'ORDER_STATUS_ID_ONGOING' => array(
				'configuration_value' => '',
				'use_function' =>  'xtc_get_order_status_name',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
			),
			'ORDER_STATUS_ID_FAILED' => array(
				'configuration_value' => '',
				'use_function' =>  'xtc_get_order_status_name',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
			),
		);
		return $config;
	}

	/**
	 * Install the payment module
	 * 
	 * This will also install three new order statuses and pre-configure the module for their use. 
	 */
	function install() {
		$config = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_PAYMENT_".strtoupper($this->code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
		}

		$osid_r = xtc_db_query("SELECT ((floor(max(orders_status_id)/10)+1) *10) AS next_id FROM orders_status");
		$osid_row = xtc_db_fetch_array($osid_r);
		$next_id = $osid_row['next_id'];
		
		$status = array(
			'ONGOING' => array(
				'de' => 'Paygate - in Zahlung',
				'en' => 'Paygate - in payment procedure'
			),
			'FAILED' => array(
				'de' => 'Paygate - Zahlung fehlgeschlagen',
				'en' => 'Paygate - payment failed',
			),
			'SUCCESS' => array(
				'de' => 'Paygate - Zahlung erfolgreich',
				'en' => 'Paygate - Payment successful'
			),
		);
		
		foreach($status as $st) {
			foreach($st as $language => $text) {
				if($this->_languageInstalled($language) && !$this->_orderStatusExists($language, $text)) {
					$this->_installStatus($next_id, $language, $text);
				}
			}
			$next_id++;
		}
		
		if(($osid = $this->_findOrdersStatus($status['ONGOING'])) !== false) {
			xtc_db_query("UPDATE configuration SET configuration_value = '". $osid ."' WHERE configuration_key = 'MODULE_PAYMENT_".strtoupper($this->code)."_ORDER_STATUS_ID_ONGOING'");
		}
		if(($osid = $this->_findOrdersStatus($status['FAILED'])) !== false) {
			xtc_db_query("UPDATE configuration SET configuration_value = '". $osid ."' WHERE configuration_key = 'MODULE_PAYMENT_".strtoupper($this->code)."_ORDER_STATUS_ID_FAILED'");
		}
		if(($osid = $this->_findOrdersStatus($status['SUCCESS'])) !== false) {
			xtc_db_query("UPDATE configuration SET configuration_value = '". $osid ."' WHERE configuration_key = 'MODULE_PAYMENT_".strtoupper($this->code)."_ORDER_STATUS_ID'");
		}
	}

	/**
	 * Determine if a language is installed in the shop
	 * @param string $language Language code, e.g. 'de' or 'en'
	 * @return boolean
	 */
	function _languageInstalled($language) {
		$query = "SELECT COUNT(*) AS num FROM languages WHERE code = ':code'";
		$query = strtr($query, array(':code' => $language));
		$result = xtc_db_query($query);
		$row = xtc_db_fetch_array($result);
		return $row['num'] > 0;
	}

	/**
	 * Determine if an order status exists, as identified by its text and language code
	 * 
	 * This is useful if orders_status_id is determined automatically.
	 * 
	 * @param string $language
	 * @param string $text
	 * @return boolean 
	 */
	function _orderStatusExists($language, $text) {
		$query = "SELECT COUNT(*) AS num FROM orders_status WHERE language_id = (SELECT languages_id FROM languages WHERE code = ':code') AND orders_status_name LIKE ':text'";
		$query = strtr($query, array(':code' => $language, ':text' => $text));
		$result = xtc_db_query($query);
		$row = xtc_db_fetch_array($result);
		return $row['num'] > 0;
	}

	/**
	 * Add a new orders status
	 * 
	 * @param int $orders_status_id
	 * @param string $language_code
	 * @param string $text 
	 */
	function _installStatus($orders_status_id, $language_code, $text) {
		$insert_query = "insert into orders_status (orders_status_id, language_id, orders_status_name) values (:osid, (select languages_id from languages where code = ':code'), ':text')";
		$insert_query = strtr($insert_query, array(':osid' => (int)$orders_status_id, ':code' => $language_code, ':text' => $text));
		xtc_db_query($insert_query);
	}

	/**
	 * Find an orders status' id by its textual representation
	 * 
	 * @param array $status_names
	 * @return boolean/int orders_status_id or false
	 */
	function _findOrdersStatus($status_names) {
		$query = "SELECT DISTINCT orders_status_id FROM `orders_status` WHERE ";
		$wheres = array();
		foreach($status_names as $status_name) {
			$wheres[] = "orders_status_name = '".$status_name."'";
		}
		$query .= implode(" OR ", $wheres);
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) > 0) {
			$row = xtc_db_fetch_array($result);
			return $row['orders_status_id'];
		}
		else {
			return false;
		}
	}

	/**
	 * Determines the module's configuration keys
	 * @return array
	 */
	function keys() {
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		foreach($ckeys as $k) {
			$keys[] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_'.$k;
		}
		return $keys;
	}

	/**
	 * Deletes the module's configuration from the database
	 * 
	 * Does NOT remove orders statuses added during installation! (Those might still be in use.) 
	 */
	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}


	/**
	 * Joins parts of the data string (required for hashing)
	 * 
	 * @param array $data
	 * @return string 
	 */
	function _makeDataString($data) {
		$string = '';
		$parts = array();
		foreach($data as $name => $value) {
			$parts[] = $name .'='. $value;
		}
		$string = implode("&", $parts);
		return $string;
	}

	/**
	 * Encrypt string (using blowfish-ecb and configured pass phrase) for transmission to Paygate
	 * 
	 * @param string $string
	 * @return string hex dump of encrypted string
	 */
	function _encodeString($string) {
		$key = constant('MODULE_PAYMENT_'.strtoupper($this->code).'_PASS');
		$td = mcrypt_module_open('blowfish', '', 'ecb', '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $key, $iv);
		$encrypted_data = mcrypt_generic($td, $string);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);		
		$encoded = bin2hex($encrypted_data);
		return $encoded;
	}

	/**
	 * Computes SHA256-HMAC of payment data using configured HMAC key
	 * 
	 * @param string $data
	 * @return string 
	 */
	function _computeMAC($data) {
		$payid = isset($data['PayID']) ? $data['PayID'] : '';
		$transid = isset($data['TransID']) ? $data['TransID'] : '';
		$merchantid = constant('MODULE_PAYMENT_'.strtoupper($this->code).'_MERCHANTID');
		$amount = isset($data['Amount']) ? $data['Amount'] : '';
		$currency = isset($data['Currency']) ? $data['Currency'] : '';
		
		$mac_string = "$payid*$transid*$merchantid*$amount*$currency";
		$mac = hash_hmac('sha256', $mac_string, constant('MODULE_PAYMENT_'.strtoupper($this->code).'_HMACKEY'));
		return $mac;
	}

	/**
	 * Decrypts status data provided by Paygate in GET/POST parameters Data and Len
	 * @return boolean/array false if no data provided in request, array containing data otherwise 
	 */
	function _decodeRequest() {
		if(isset($_REQUEST['Data']) && isset($_REQUEST['Len'])) {
			$data = $_REQUEST['Data'];
			$len = (int)$_REQUEST['Len'];
			$decoded = @mcrypt_decrypt('blowfish', constant('MODULE_PAYMENT_'.strtoupper($this->code).'_PASS'), hex2bin($data), 'ecb');
			$decoded = substr($decoded, 0, $len);
			$decoded_data = $this->_qs2array($decoded);
			return $decoded_data;
		}
		else {
			return false;
		}
	}

	/**
	 * Writes an entry to the log file
	 * @param string $text 
	 */
	function _log($text) {
		$this->logger->notice($text, 'payment', 'payment.paygate');
	}

	/**
	 * Dissect a query string into an array
	 * @param string $input
	 * @return array
	 */
	function _qs2array($input) {
		$parts = explode("&", $input);
		$data = array();
		foreach($parts as $part) {
			$entry = explode("=", $part);
			if(count($entry) == 2) {
				$data[$entry[0]] = $entry[1];
			}
		}
		return $data;
	}

	/**
	 * Builds an HTML string w/ additional information regarding system requirements
	 * 
	 * @return string 
	 */
	function _requirementsNotice() {
		$constant_prefix = 'MODULE_PAYMENT_'.strtoupper($this->code);
		$notice = '<br><br>'.@constant($constant_prefix.'_REQUIREMENTS').'<ul>';
		$requirements = array();
		
		$requirements[] = @constant($constant_prefix.'_REQUIREMENTS_SSL');
		
		$extensions = get_loaded_extensions();

		$mcrypt_installed = in_array('mcrypt', $extensions);
		$blowfish_installed = $mcrypt_installed && in_array('blowfish', mcrypt_list_algorithms());
		$requirements[] = 'mcrypt-blowfish: '. ($blowfish_installed ? @constant($constant_prefix.'_REQUIREMENTS_OK') : @constant($constant_prefix.'_REQUIREMENTS_MISSING'));
		
		$hash_installed = in_array('hash', $extensions);
		$sha256_installed = $hash_installed && in_array('sha256', hash_algos());
		$requirements[] = 'hash-sha256: '. ($sha256_installed ? @constant($constant_prefix.'_REQUIREMENTS_OK') : @constant($constant_prefix.'_REQUIREMENTS_MISSING'));
		
		
		foreach($requirements as $req) {
			$notice .= '<li>'.$req.'</li>';
		}
		$notice .= '</ul>';
		return $notice;
	}
}
MainFactory::load_origin_class('paygate');