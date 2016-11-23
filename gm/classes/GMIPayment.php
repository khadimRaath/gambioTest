<?php
/* --------------------------------------------------------------
   GMIPayment.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMIPayment_ORIGIN {
	protected $_logger;
	protected $_pm_config;
	protected $_code;
	protected $_target_url;

	public function __construct($code) {
		if(!in_array($code, array('ipayment_cc', 'ipayment_elv', 'ipayment_pp'))) {
			throw new GMIPaymentCodeInvalidException();
		}
		$this->_code = $code;
		$this->_logger = LogControl::get_instance();
		$this->_loadPaymentModuleConfiguration();
		$this->_target_url = 'https://ipayment.de/merchant/'.$this->_pm_config['ACCOUNT_ID'].'/processor/2.0/';
	}

	public function log($message) {
		$this->_logger->notice($message, 'payment', 'payment.ipayment');
	}

	protected function _loadPaymentModuleConfiguration() {
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/' . $this->_code . '.php');
		require_once DIR_FS_CATALOG.'includes/modules/payment/'.$this->_code.'.php';
		$classname = $this->_code;
		$pm_ipayment = new $classname();
		$keys = $pm_ipayment->keys();
		$config_query = "SELECT configuration_value FROM configuration WHERE configuration_key = ':config_key'";
		foreach($keys as $key) {
			$key_query = strtr($config_query, array(':config_key' => $key));
			$key_result = xtc_db_query($key_query);
			while($key_row = xtc_db_fetch_array($key_result)) {
				$key = str_replace('MODULE_PAYMENT_'.strtoupper($this->_code).'_', '', $key);
				$this->_pm_config[$key] = $key_row['configuration_value'];
			}
		}
	}

	protected function _getBasicFormData() {
		require_once DIR_FS_CATALOG.'release_info.php';
		$formdata = array(
			'action' => $this->_target_url,
			'trxuser_id' => constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_APPLICATION_ID'),
			'trxpassword' => constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_APPLICATION_PASSWORD'),
			'client_name' => 'Gambio GX2',
			'client_version' => $gx_version,
			'redirect_url'       => GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_ipayment.php',
			'silent_error_url'   => GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_ipayment.php?silent_error=1',
			'backlink'           => GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_ipayment.php?action=back',
			'hidden_trigger_url' => GM_HTTP_SERVER.DIR_WS_CATALOG.'ipayment_htrigger.php',
			'redirect_action' => 'POST',
			'error_lang' => ($_SESSION['language'] == 'german') ? 'de' : 'en',
			'ppcdded' => 'ba888a4f2a1aa9496eef9c8',
		);
		return $formdata;
	}

	public function getFormData($orders_id, $order) {
		$formdata = array(
			'trx_currency' => $order->info['currency'],
			'trx_amount' => round($order->info['pp_total'] * 100),
			'trx_typ' => constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_AUTH_MODE'),
			'shopper_id' => $orders_id.'_'.md5(xtc_session_id().constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_ADMINACTION_PASSWORD')),
			'advanced_strict_id_check' => 1,
			'silent' => 0,
			'trx_paymenttyp' => substr($this->_code, strlen('ipayment_')),
			'return_paymentdata_details' => 1,
		);

		if($this->_code == 'ipayment_pp') {
			$formdata = array_merge($formdata, array(
				'pp_paysafecard_businesstype' => MODULE_PAYMENT_IPAYMENT_PP_BUSINESSTYPE,
				'pp_paysafecard_reportingcriteria' => MODULE_PAYMENT_IPAYMENT_PP_REPORTINGCRITERIA,
			));
		}

		$formdata = array_merge($formdata, array(
			'addr_name'    => $order->customer['name'],
			'addr_email'   => $order->customer['email_address'],
			'addr_street'  => $order->customer['street_address'],
			'addr_city'    => $order->customer['city'],
			'addr_zip'     => $order->customer['postcode'],
			'addr_country' => $this->_findISO3byCountry($order->customer['country']),
		));

		if($this->_code == 'ipayment_cc') {
			$formdata['ignore_cc_typ_mismatch'] = 1;
		}

		$basic_formdata = $this->_getBasicFormData();
		$formdata = array_merge($formdata, $basic_formdata);

		$hash_input = $formdata['trxuser_id'].$formdata['trx_amount'].$formdata['trx_currency'].$formdata['trxpassword'];
		$hash_input .= constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_SECURITY_KEY');
		$formdata['trx_securityhash'] = md5($hash_input);
		return $formdata;
	}

	protected function _getTransactionData($orders_id, $trx_typ) {
		$trx_id_query = "SELECT ret_trx_number, trx_currency FROM ipayment_log WHERE orders_id = :orders_id AND trx_typ = ':trx_typ' AND ret_status = 'SUCCESS' ".
			" ORDER BY ipayment_log_id DESC LIMIT 1";
		$trx_id_query = strtr($trx_id_query, array(':orders_id' => (int)$orders_id, ':trx_typ' => $trx_typ));
		$trx_id_result = xtc_db_query($trx_id_query);
		$trxdata = array();
		while($trx_id_row = xtc_db_fetch_array($trx_id_result)) {
			$trxdata['ret_trx_number'] = $trx_id_row['ret_trx_number'];
			$trxdata['ret_trx_currency'] = $trx_id_row['trx_currency'];
		}
		return $trxdata;
	}

	public function capturePayment($orders_id, $amount) {
		$trx_data = $this->_getTransactionData($orders_id, 'preauth');
		$params = array(
			'trx_typ' => 'capture',
			'adminactionpassword' => constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_ADMINACTION_PASSWORD'),
			'orig_trx_number' => $trx_data['ret_trx_number'],
			'trx_amount' => (int)round($amount * 100),
			'trx_currency' => $trx_data['ret_trx_currency'],
			'gateway' => 1,
		);
		$response = $this->_serviceCall($params, $orders_id);
		return $response;
	}

	public function reversePayment($orders_id, $amount) {
		$trx_data = $this->_getTransactionData($orders_id, 'preauth');
		$params = array(
			'trx_typ' => 'reverse',
			'adminactionpassword' => constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_ADMINACTION_PASSWORD'),
			'orig_trx_number' => $trx_data['ret_trx_number'],
			'trx_amount' => (int)$amount,
			'trx_currency' => $trx_data['ret_trx_currency'],
			'gateway' => 1,
		);
		$response = $this->_serviceCall($params, $orders_id);
		return $response;
	}

	public function refundPayment($orders_id, $amount) {
		$trx_data = $this->_getTransactionData($orders_id, 'auth');
		$params = array(
			'trx_typ' => 'refund_cap',
			'adminactionpassword' => constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_ADMINACTION_PASSWORD'),
			'orig_trx_number' => $trx_data['ret_trx_number'],
			'trx_amount' => (int)round($amount * 100),
			'trx_currency' => $trx_data['ret_trx_currency'],
			'gateway' => 1,
		);
		$response = $this->_serviceCall($params, $orders_id);
		return $response;
	}


	protected function _serviceCall($params, $orders_id) {
		$basic_formdata = $this->_getBasicFormData();
		$params = array_merge($params, $basic_formdata);
		$hash_input = $params['trxuser_id'].$params['trx_amount'].$params['trx_currency'].$params['trxpassword'];
		$hash_input .= constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_SECURITY_KEY');
		$params['trx_securityhash'] = md5($hash_input);
		unset($params['action']);
		$full_url = $this->_target_url.'?'.http_build_query($params);
		$this->log('SERVICE_CALL: '.print_r($params, true));
		$this->log('full_url: '.$full_url);
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_USERAGENT => 'Ipay CURL',
			CURLOPT_URL => $this->_target_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $params,
		)) OR die('error setting cURL options');
		$response = curl_exec($ch);
		$this->log("RESPONSE:\n".$response);
		$error = curl_error($ch);
		curl_close($ch);
		if(!empty($error)) {
			$this->log('cURL error in service call: '.$error);
			return false;
		}
		else {
			$response_data = $this->_parseCGIResponse($response);
			if(!empty($response_data['Params'])) {
				$this->logResponse($orders_id, $response_data['Params']);
			}
			return $response_data;
		}
	}

	protected function _parseCGIResponse($raw) {
		$lines = explode("\n", $raw);
		$data = array();
		foreach($lines as $line) {
			$line = trim($line);
			if(empty($line)) {
				continue;
			}
			list($key, $value) = explode('=', $line, 2);
			if(strpos($value, '=') !== false) {
				parse_str($value, $value_array);
				$data[$key] = $value_array;
			}
			else {
				$data[$key] = $value;
			}
		}
		return $data;
	}

	public function checkURLHash($url, $hash) {
		$stripped_url = preg_replace('/&ret_url_checksum.*/', '', $url);
		$stripped_url .= '&'.constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_SECURITY_KEY');
		$stripped_url = GM_HTTP_SERVER.$stripped_url;
		$url_hash = md5($stripped_url);
		$checksum_correct =  $url_hash == $hash;
		return $checksum_correct;
	}

	public function checkReturnHash($data) {
		if(empty($data['ret_param_checksum'])) {
			return false;
		}
		$fields = array('trxuser_id', 'trx_amount', 'trx_currency', 'ret_authcode', 'ret_booknr');
		$hash_input = '';
		foreach($fields as $field) {
			if(!array_key_exists($field, $data)) {
				return false;
			}
			$hash_input .= $data[$field];
		}
		$hash_input .= constant('MODULE_PAYMENT_'.strtoupper($this->_code).'_SECURITY_KEY');
		$hash = md5($hash_input);
		$correct = $hash == $data['ret_param_checksum'];
		return $correct;
	}

	public function logResponse($orders_id, $request) {
		$fields = array(
			'action' => '',
			'addr_city' => '',
			'addr_email' => '',
			'addr_name' => '',
			'addr_street' => '',
			'addr_zip' => '',
			'client_name' => '',
			'client_version' => '',
			'redirect_needed' => '',
			'ret_additionalmsg' => '',
			'ret_authcode' => '',
			'ret_booknr' => '',
			'ret_errorcode' => '',
			'ret_errormsg' => '',
			'ret_fatalerror' => '',
			'ret_ip' => '',
			'ret_param_checksum' => '',
			'ret_status' => '',
			'ret_transdate' => '',
			'ret_transtime' => '',
			'ret_trx_number' => '',
			'shopper_id' => '',
			'trx_amount' => '',
			'trx_currency' => '',
			'trx_payauth_status' => '',
			'trx_paymentdata_country' => '',
			'trx_paymentmethod' => '',
			'trx_paymenttyp' => '',
			'trx_remoteip_country' => '',
			'trx_typ' => '',
			'trxuser_id' => '',
			'trx_issuer_avs_response' => '',
			'ret_url_checksum' => '',
			'addr_check_result' => '',
		);
		$row = array_intersect_key($request, $fields);
		$paydata_array = array();
		foreach($request as $key => $value) {
			if(strpos($key, 'paydata') !== false) {
				$paydata_array[] = "$key: $value";
			}
		}
		$row['paydata'] = implode(', ', $paydata_array);
		$row['orders_id'] = $orders_id;
		if(empty($row['ret_transdate'])) {
			$row['ret_transdate'] = date('d.m.y');
		}
		if(empty($row['ret_transtime'])) {
			$row['ret_transtime'] = date('H:i:s');
		}
		xtc_db_perform('ipayment_log', $row, 'insert');
	}

	public function getResponseLogs($orders_id) {
		$logs = array();
		$query = "SELECT * FROM ipayment_log WHERE orders_id = :orders_id ORDER BY ipayment_log_id ASC";
		$query = strtr($query, array(':orders_id' => xtc_db_input($orders_id)));
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			$logs[] = $row;
		}
		return $logs;
	}

	public function processHiddenTrigger($request) {
		$this->log("HIDDEN TRIGGER\n".print_r($request, true));
		$hash_correct = $this->checkReturnHash($request);
		if(!$hash_correct) {
			$this->log("ERROR: hash check failed!");
			return false;
		}
		$orders_id = (int)$request['shopper_id'];
		$request['ret_status'] .= ' (HT)';
		$this->logResponse($orders_id, $request);
		return true;
	}

	protected function _findISO3byCountry($country) {
		$query = "SELECT countries_iso_code_3 FROM countries WHERE countries_name = '".xtc_db_input($country)."'";
		$iso = false;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			$iso = $row['countries_iso_code_3'];
		}
		return $iso;
	}

}


class GMIPaymentException extends Exception {}
class GMIPaymentCodeInvalidException extends GMIPaymentException {}

MainFactory::load_origin_class('GMIPayment');