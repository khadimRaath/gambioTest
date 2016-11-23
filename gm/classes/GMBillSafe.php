<?php
/* --------------------------------------------------------------
   GMBillSafe.php 2016-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMBillSafe_ORIGIN {
	protected $_logger;
	protected $_shop_is_utf8;
	protected $_curlinfo;
	protected $_debug = true;
	protected $_txt;
	protected $_submodule;

	const MODULE_VERSION = '2016-07-20';
	const APPLICATION_SIGNATURE = 'a619fe3fd7e35da8a57d7efa77c410f1';
	const APPLICATION_VERSION   = 'GX2_BS3';
	const API_VERSION = 'V208';
	const GATEWAY_VERSION = 'V200';
	const URL_PAYMENT_GATEWAY_SANDBOX = 'https://sandbox-payment.billsafe.de/';
	const URL_PAYMENT_GATEWAY_LIVE    = 'https://payment.billsafe.de/';
	const URL_NVP_API_SANDBOX         = 'https://sandbox-nvp.billsafe.de/';
	const URL_NVP_API_LIVE			  = 'https://nvp.billsafe.de/';
	const SHIPPING_MAX_DAYS_AGO = 5;

	const LOGLEVEL_INFO = 0;
	const LOGLEVEL_WARN = 10;
	const LOGLEVEL_ERROR = 20;
 	const LOGLEVEL_DEBUG = 99;

	protected $_error_codes = array(
		// 1xx Mit „1“ beginnende Fehler-Codes behandeln Authentifizierungs-Fehler
		'100' => 'No auth parameter is set',
		'101' => 'Auth failure',
		'102' => 'Not all auth parameters set',
		'103' => 'Invalid merchantId',
		'104' => 'Invalid merchantLicence',
		'105' => 'Invalid applicationSignature',
		'106' => 'Invalid applicationVersion',
		'107' => 'Merchant not found',
		'108' => 'Request to productive system not allowed yet',
		'198' => 'Error during application log',
		'199' => 'Unknown error during authentication',
		// 2xx Mit „2“ beginnende Fehler-Codes behandeln Validierungs-Fehler
		'200' => 'No transaction identifier set',
		'201' => 'Parameter transactionId is invalid',
		'202' => 'Transaction not found',
		'203' => 'Transaction does not belong to merchant',
		'204' => 'OrderNumber matched more than 1 order',
		'215' => 'Ein erforderlicher Parameter wurde nicht angegeben',
		'216' => 'Ein Parameter enthält einen ungültigen Wert',
		'220' => 'Encoding other than UTF8 detected',
		// 3xx Mit „3“ beginnende Fehler-Codes behandeln Ausführungs-Fehler
		'301' => 'An execution error occurred',
		'302' => 'Transaction has a wrong status for this method',
		'303' => 'Customer has not yet completed the transaction',
		'304' => 'Frist für den Aufruf der Operation ist abgelaufen',
		'305' => 'No data to return',
		'306' => 'Transaction already has customer payments',
		'399' => 'Internal error in method',
		// 8xx Mit „8“ beginnende Fehler-Codes behandeln API spezifische Fehler
		'801' => 'No Service found',
		'802' => 'No method set',
		'803' => 'Invalid method set',
		'804' => 'Invalid request',
		'999' => 'Unbekannter Fehler',
	);

	public function __construct($submodule = 'invoice') {
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_lang_file_master->init_from_lang_file('lang/'.$_SESSION['language'].'/modules/payment/billsafe_3_base.php');

		if(class_exists('FileLog')) {
			$this->_logger = new FileLog('payment-billsafe_3', true);
		}
		else {
			$this->_logger = false;
		}
		$this->_shop_is_utf8 = true;
		$this->_txt = new LanguageTextManager('billsafe3', $_SESSION['languages_id']);
		$submodule = str_replace('billsafe_3_', '', $submodule);
		$this->_submodule = $submodule;
	}

	public function _log($message, $loglevel = self::LOGLEVEL_INFO) {
		if($this->_logger !== false) {
			$this->_logger->write(date('c'). ' | '. $message ."\n");
		}
	}

	public function array2NVP(array $input) {
		$nvp_elements = array();
		foreach($input as $key => $value) {
			if(!$this->_shop_is_utf8) {
				$value = utf8_encode($value);
			}
			$value = urlencode($value);
			$key = urlencode($key);
			$nvp_elements[] = $key .'='. $value;
		}
		$nvp = implode('&', $nvp_elements);
		return $nvp;
	}

	public function nvp2Array($nvp) {
		if(get_magic_quotes_gpc()) {
			ini_set('magic_quotes_gpc', false);
		}
		parse_str($nvp, $data);
		$out_data = array();
		foreach($data as $key => $value) {
			$keys = explode('_', $key);
			$current_array =& $out_data;
			for($n_keys = count($keys), $k = 0; $k < ($n_keys - 1); $k++) {
				if(!array_key_exists($keys[$k], $current_array)) {
					$current_array[$keys[$k]] = array();
				}
				$current_array =& $current_array[$keys[$k]];
			}
			$final_key = $keys[count($keys) - 1];
			$value = urldecode($value);
			if(!$this->_shop_is_utf8) {
				$value = utf8_decode($value);
			}
			$current_array[$final_key] = $value;
		}
		return $out_data;
	}

	public function getSubmodule() {
		return $this->_submodule;
	}

	public function orderIsWithinLimits($order_amount) {
		$min_order = constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_MINORDER');
		$max_order = constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_MAXORDER');
		$is_within_limits = $order_amount >= $min_order && $order_amount <= $max_order;
		return $is_within_limits;
	}

	public function _makeServiceCall($call, $params) {
		$call_params = array(
			'method' => $call,
			'merchant_id' => constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_MERCHANT_ID'),
			'merchant_license' => constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_MERCHANT_LICENSE'),
			'application_signature' => self::APPLICATION_SIGNATURE,
			'application_version' => self::APPLICATION_VERSION,
			'format' => 'NVP', // 'XML' and 'JSON' currently unsupported
		);
		$params = array_merge($params, $call_params);
		$post_params = $this->array2NVP($params);
		if(constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_SANDBOX') == 'True') {
			$service_url = self::URL_NVP_API_SANDBOX;
		}
		else {
			$service_url = self::URL_NVP_API_LIVE;
		}
		$service_url .= self::API_VERSION;
		$this->_log("CALL: $call\nURL: $service_url\nData: $post_params", self::LOGLEVEL_DEBUG);
		$ch = curl_init($service_url);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post_params,
			CURLOPT_TIMEOUT => 10,
		));
		$result = @curl_exec($ch);
		if($errno = curl_errno($ch) != 0) {
			$error_text = "COMM ERROR: $errno - ". curl_error($ch);
			$this->_log($error_text, self::LOGLEVEL_ERROR);
			throw new BillSafeException($error_text);
		}
		$this->_curlinfo = curl_getinfo($ch);
		$this->_log("RESPONSE: $result", self::LOGLEVEL_DEBUG);
		//$this->_log("INFO: ". print_r($this->_curlinfo, true), self::LOGLEVEL_DEBUG);
		curl_close($ch);
		$result_array = $this->nvp2Array($result);
		return $result_array;
	}

	public function _getCurlInfo() {
		return $this->_curlinfo;
	}

	public function checkCredentials() {
		if(!defined('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_MERCHANT_ID')) {
			return false;
		}
		$result = $this->_makeServiceCall('getAgreedHandlingCharges', array());
		if($result['ack'] == 'OK') {
			return true;
		}
		return false;
	}

	public function prevalidateOrder($order) {
		static $preval_cache;
		if(!isset($preval_cache)) {
			$preval_cache = array();
		}

		$params = array(
			'order_amount'       => number_format($order->info['total'], 2, '.', ''),
			'order_currencyCode' => $order->info['currency'],
			//'order_containsDigitalGoods'
			'customer_id'        => $order->customer['csID'],
			'customer_company'   => $order->billing['company'],
			'customer_gender'    => ($order->customer['gender'] == 'm' ? 'm' : 'f'),
			'customer_firstname' => $order->billing['firstname'],
			'customer_lastname'  => $order->billing['lastname'],
			'customer_street'    => $order->billing['street_address'],
			'customer_postcode'  => $order->billing['postcode'],
			'customer_city'      => $order->billing['city'],
			'customer_country'   => $order->billing['country']['iso_code_2'],
			'customer_email'     => $order->customer['email_address'],
			'customer_phone'     => $order->customer['telephone'],
			'delivery_company'   => $order->delivery['company'],
			'delivery_gender'    => ($order->customer['gender'] == 'm' ? 'm' : 'f'),
			'delivery_firstname' => $order->delivery['firstname'],
			'delivery_lastname'  => $order->delivery['lastname'],
			'delivery_street'    => $order->delivery['street_address'],
			'delivery_postcode'  => $order->delivery['postcode'],
			'delivery_city'      => $order->delivery['city'],
			'delivery_country'   => $order->delivery['country']['iso_code_2'],
		);

		if(!empty($order->billing['house_number']))
		{
			$params['customer_street'] .= ' ' . $order->billing['house_number'];
		}
		if(!empty($order->delivery['house_number']))
		{
			$params['delivery_street'] .= ' ' . $order->delivery['house_number'];
		}

		$params_hash = md5(serialize($params));
		if(array_key_exists($params_hash, $preval_cache)) {
			$this->_log('using cached answer from prevalidateOrder');
			return $preval_cache[$params_hash];
		}

		try {
			$result_array = $this->_makeServiceCall('prevalidateOrder', $params);
			$valid = array(
				'invoice' => false,
				'hirePurchase' => false,
			);
			if($result_array['ack'] == OK) {
				$valid['invoice'] = strtoupper($result_array['invoice']['isAvailable']) == 'TRUE';
				$valid['hirePurchase'] = strtoupper($result_array['hirePurchase']['isAvailable']) == 'TRUE';
			}
			else {
				$valid['invoice'] = false;
				$valid['hirePurchase'] = false;
			}
			$valid['result'] = $result_array;

			if($valid['hirePurchase'] == true) {
				$valid['installmentAmount'] = $result_array['hirePurchase']['installmentAmount'];
				$valid['installmentCount']  = $result_array['hirePurchase']['installmentCount'];
				$valid['processingFee']     = $result_array['hirePurchase']['processingFee'];
				$valid['currencyCode']      = $result_array['hirePurchase']['currencyCode'];
				$valid['annualPercentageRate'] = $result_array['hirePurchase']['annualPercentageRate'];
			}

			// TEST ONLY!!!
			//$valid['invoice'] = true;
			//$valid['hirePurchase'] = true;

			/*
			$valid = $result_array['ack'] == OK &&
				(strtoupper($result_array['invoice']['isAvailable']) == 'TRUE' || strtoupper($result_array['hirePurchase']['isAvailable']) == 'TRUE');
			*/
			$preval_cache[$params_hash] = $valid;
			return $valid;
		}
		catch(BillSafeException $e) {
			// BillSAFE web service unavailable
			return false;
		}
	}

	public function prepareOrder($order, $orders_id, $order_totals, $mode) {
		if(empty($mode)) {
			$mode = $this->_submodule;
		}
		$modes = array('invoice', 'installment');
		$mode = in_array($mode, $modes) ? $mode : $modes[0];
		$gross_price_mode = $_SESSION['customers_status']['customers_status_show_price_tax'] == 1;
		$show_total_taxes = $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1;
		if($gross_price_mode == true) {
			$amount = number_format($order->info['total'], 2, '.', '');
		}
		else {
			$amount = number_format($order->info['total'] + $order->info['tax'], 2, '.', '');
		}
		$sid = session_name() . '=' . session_id();
		$params = array(
			'order_number'       => $orders_id,
			'order_amount'       => $amount,
			'order_taxAmount'    => number_format($order->info['tax'], 2, '.', ''),
			'order_currencyCode' => $order->info['currency'],
			//'order_containsDigitalGoods'
			'customer_id'        => $order->customer['csID'],
			'customer_company'   => $order->billing['company'],
			'customer_gender'    => ($order->customer['gender'] == 'm' ? 'm' : 'f'),
			'customer_firstname' => $order->billing['firstname'],
			'customer_lastname'  => $order->billing['lastname'],
			'customer_street'    => $order->billing['street_address'],
			'customer_postcode'  => $order->billing['postcode'],
			'customer_city'      => $order->billing['city'],
			'customer_country'   => $order->billing['country']['iso_code_2'],
			'customer_email'     => $order->customer['email_address'],
			'customer_phone'     => $order->customer['telephone'],
			'product'            => $mode,
			'salesChannel'       => 'online',
			'url_return'         => GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_billsafe.php?'.$sid,
			'url_cancel'         => GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.$sid,
			'url_image'          => constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_URL_IMAGE'),
			'sessionId'          => md5(xtc_session_id()),
			'userAction'         => 'CONTINUE',
		);
		if(!empty($order->billing['house_number']))
		{
			$params['customer_street'] .= ' ' . $order->billing['house_number'];
		}
		// add article list
		foreach($order->products as $pidx => $product) {
			if($product['qty'] != (int)$product['qty']) {
				$product_price = $product['qty'] * $product['price'];
				$qty = 1;
				$name_qty = $product['qty'] .' ';
			}
			else {
				$product_price = $product['price'];
				$qty = $product['qty'];
				$name_qty = '';
			}
			$params['articleList_'.$pidx.'_number'] = (int)$product['id'];
			$params['articleList_'.$pidx.'_name'] = $name_qty.$product['name'];
			//$params['articleList_'.$pidx.'_description'] = $product[''];
			$params['articleList_'.$pidx.'_type'] = 'goods';
			$params['articleList_'.$pidx.'_quantity'] = $qty;
			if($gross_price_mode == true) {
				$params['articleList_'.$pidx.'_grossPrice'] = number_format($product_price, 2, '.', '');
			}
			else {
				$params['articleList_'.$pidx.'_netPrice'] = number_format($product_price, 2, '.', '');
			}
			$params['articleList_'.$pidx.'_tax'] = number_format($product['tax'], 2, '.', '');
		}

		// shipping
		list($shipping_class, $shipping_method) = explode('_', $order->info['shipping_class']);
		if($shipping_class != 'selfpickup')
		{
			$pidx++;
			if(defined('MODULE_SHIPPING_'.strtoupper($shipping_class).'_TAX_CLASS'))
			{
				$shipping_tax_class_id = constant('MODULE_SHIPPING_'.strtoupper($shipping_class).'_TAX_CLASS');
				$shipping_tax = $GLOBALS['xtPrice']->TAX[$shipping_tax_class_id];
			}
			else
			{
				$shipping_tax = 0;
			}
			$params['articleList_'.$pidx.'_number'] = 'SHIPMENT';
			$params['articleList_'.$pidx.'_name'] = $order->info['shipping_method'];
			$params['articleList_'.$pidx.'_type'] = 'shipment';
			$params['articleList_'.$pidx.'_quantity'] = '1';
			if($gross_price_mode == true) {
				$params['articleList_'.$pidx.'_grossPrice'] = number_format($order->info['shipping_cost'], 2, '.', '');
			}
			else {
				$params['articleList_'.$pidx.'_netPrice'] = number_format($order->info['shipping_cost'], 2, '.', '');
				if($show_total_taxes == false) {
					$params['order_taxAmount'] += $order->info['shipping_cost'] * ($shipping_tax / 100);
					$params['order_amount'] += $order->info['shipping_cost'] * ($shipping_tax / 100);
				}
			}
			$params['articleList_'.$pidx.'_tax'] = number_format($shipping_tax, 2, '.', '');
		}

		//$this->_log("Order totals:\n".print_r($order_totals, true));
		foreach($order_totals as $ot) {
			// handling
			if($ot['code'] == 'ot_billsafe3') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$handling_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_BILLSAFE3_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$params['articleList_'.$pidx.'_number'] = 'HANDLING';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'handling';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format($ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format($ot['value'], 2, '.', '');
					if($show_total_taxes == false) {
						$params['order_taxAmount'] += $ot['value'] * ($handling_tax / 100);
						$params['order_amount'] += $ot['value'] * ($handling_tax / 100);
					}
				}
				$params['articleList_'.$pidx.'_tax'] = number_format($handling_tax, 2, '.', '');
			}
			if($ot['code'] == 'ot_coupon') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$coupon_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_COUPON_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$params['articleList_'.$pidx.'_number'] = 'COUPON';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'voucher';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format($ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format($ot['value'], 2, '.', '');
				}
				$params['articleList_'.$pidx.'_tax'] = number_format($coupon_tax, 2, '.', '');
			}
			if($ot['code'] == 'ot_gv') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$coupon_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_GV_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$params['articleList_'.$pidx.'_number'] = 'COUPON';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'voucher';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format(-$ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format(-$ot['value'], 2, '.', '');
				}
				$params['articleList_'.$pidx.'_tax'] = number_format($coupon_tax, 2, '.', '');
			}
			if($ot['code'] == 'ot_discount') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$params['articleList_'.$pidx.'_number'] = 'DISCOUNT';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'voucher';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format($ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format($ot['value'], 2, '.', '');
				}
				$params['articleList_'.$pidx.'_tax'] = number_format(0.00, 2, '.', '');
			}
			if($ot['code'] == 'ot_loworderfee') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$lo_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$params['articleList_'.$pidx.'_number'] = 'LOWORDERFEE';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'shipment';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format($ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format($ot['value'], 2, '.', '');
					if($show_total_taxes == false) {
						$params['order_taxAmount'] += $ot['value'] * ($lo_tax / 100);
						$params['order_amount'] += $ot['value'] * ($lo_tax / 100);
					}
				}
				$params['articleList_'.$pidx.'_tax'] = number_format($lo_tax, 2, '.', '');
			}
			if($ot['code'] == 'ot_ps_fee') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$ps_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_PS_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$params['articleList_'.$pidx.'_number'] = 'PS';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'shipment';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format($ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format($ot['value'], 2, '.', '');
					if($show_total_taxes == false) {
						$params['order_taxAmount'] += $ot['value'] * ($ps_tax / 100);
						$params['order_amount'] += $ot['value'] * ($ps_tax / 100);
					}
				}
				$params['articleList_'.$pidx.'_tax'] = number_format($ps_tax, 2, '.', '');
			}
			if($ot['code'] == 'ot_gambioultra') {
				$name = strtr($ot['title'], array(':' => ''));
				$pidx++;
				$gu_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$params['articleList_'.$pidx.'_number'] = 'SHIPMENT_PER_ARTICLE';
				$params['articleList_'.$pidx.'_name'] =  $name;
				$params['articleList_'.$pidx.'_type'] = 'shipment';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				if($gross_price_mode == true) {
					$params['articleList_'.$pidx.'_grossPrice'] = number_format($ot['value'], 2, '.', '');
				}
				else {
					$params['articleList_'.$pidx.'_netPrice'] = number_format($ot['value'], 2, '.', '');
					if($show_total_taxes == false) {
						$params['order_taxAmount'] += $ot['value'] * ($gu_tax / 100);
						$params['order_amount'] += $ot['value'] * ($gu_tax / 100);
					}
				}
				$params['articleList_'.$pidx.'_tax'] = number_format($gu_tax, 2, '.', '');
			}
			if($ot['code'] == 'ot_bonus_fee') {
				//$params['order_amount'] -= $ot['value'];
				$pidx++;
				$params['articleList_'.$pidx.'_number'] = 'DISCOUNT';
				$params['articleList_'.$pidx.'_name'] =  'Bonusverrechnung';
				$params['articleList_'.$pidx.'_type'] = 'voucher';
				$params['articleList_'.$pidx.'_quantity'] = '1';
				$params['articleList_'.$pidx.'_grossPrice'] = number_format((-1 * $ot['value']), 2, '.', '');
				$params['articleList_'.$pidx.'_tax'] = '0.00';
			}
		}

		$params['order_taxAmount'] = number_format($params['order_taxAmount'], 2, '.', '');
		$params['order_amount'] = number_format($params['order_amount'], 2, '.', '');

		$result = $this->_makeServiceCall('prepareOrder', $params);
		if(isset($result['ack']) && $result['ack'] == 'OK' && isset($result['token'])) {
			$this->_log('prepareOrder '.$orders_id. ' token '. $result['token'], self::LOGLEVEL_INFO);
			return $result['token'];
		}
		else {
			$this->_log('prepareOrder '.$orders_id. ' FAILED', self::LOGLEVEL_ERROR);
			return false;
		}
	}

	public function getPaymentURL($token) {
		if(constant('MODULE_PAYMENT_BILLSAFE_3_'.strtoupper($this->_submodule).'_SANDBOX') == 'True') {
			$base_url = self::URL_PAYMENT_GATEWAY_SANDBOX;
		}
		else {
			$base_url = self::URL_PAYMENT_GATEWAY_LIVE;
		}
		$full_url = $base_url . self::GATEWAY_VERSION . '?token='.$token;
		return $full_url;
	}

	public function getTransactionResult($token) {
		$result = $this->_makeServiceCall('getTransactionResult', array('token' => $token));
		$this->_log("Transaction result:\n".print_r($result, true));
		return $result;
	}

	public function saveTransactionId($orders_id, $transaction_id) {
		$this->_setOrderStatus($orders_id, null, "BillSAFE Transaction ID: ".$transaction_id);
		xtc_db_query("REPLACE INTO orders_billsafe (orders_id, transaction_id) VALUES (".(int)$orders_id.", '".$transaction_id."')");
	}

	public function getPaymentInfoCached($orders_id, $force_fetch = false) {
		// try to get from db
		$query = "SELECT * FROM billsafe_paymentinfo WHERE orders_id = ".(int)$orders_id; //." AND `received` > DATE_SUB(NOW(), INTERVAL 1 DAY)";
		$dbresult = xtc_db_query($query);
		$instruction = false;
		while($row = xtc_db_fetch_array($dbresult)) {
			$instruction = $row;
		}

		if($instruction === false || $force_fetch == true) { // too old/not found
			$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
			if($transaction_id === false) {
				throw new GMBillSafeTransactionIdNotFoundException();
			}
			$result = $this->_makeServiceCall('getPaymentInstruction',
				array(
					'transactionId' => $transaction_id,
					'outputType' => 'STRUCTURED',
				));
			if(isset($result['ack']) && $result['ack'] == 'OK' && is_array($result['instruction'])) {
				// write to db
				$dbcols = array('orders_id', 'received', 'transaction_id', 'recipient', 'bankCode', 'accountNumber', 'bankName', 'bic', 'iban', 'reference', 'amount', 'currencyCode', 'paymentPeriod', 'note', 'legalNote');
				$instruction = $result['instruction'];
				$instruction['orders_id'] = $orders_id;
				$instruction['transaction_id'] = $transaction_id;
				$instruction['received'] = date('Y-m-d H:i:s');
				$dbdata = array_intersect_key($instruction, array_flip($dbcols));
				xtc_db_perform('billsafe_paymentinfo', $dbdata, 'replace');
			}
		}
		return $instruction;
	}

	public function getPaymentInfo($orders_id, $txt = false, $force_fetch = false) {
		try {
			$instr = $this->getPaymentInfoCached($orders_id, $force_fetch);
		}
		catch(GMBillSafeTransactionIdNotFoundException $e) {
			return $this->_get_text('no_tid');
		}
		if($instr !== false) {
			$instr['amount'] = number_format($instr['amount'], 2, '.', '');
			//$info = '<pre>'.print_r($result, true).'</pre>';
			$info = '';
			if($instr['amount'] == 0) {
				if($txt) {
					$info .=  $instr['note']."\n\n";
				}
				else { // HTML
					$info .= '<div class="billsafe_pinfo">';
					$info .= '<p>'.$instr['note'].'</p>';
					$info .= '</div>';
				}
			}
			else {
				if($txt) {
					$info .=  $instr['legalNote']."\n\n";
					$info .=  $instr['note']."\n\n";
					$info .= $this->_get_text('payment_recipient').': '.$instr['recipient']."\n";
					/*
					if(!empty($instr['bankCode']) && !empty($instr['accountNumber'])) {
						$info .= $this->_get_text('payment_bankcode').': '.$instr['bankCode']."\n";
						$info .= $this->_get_text('payment_accountno').': '.$instr['accountNumber']."\n";
					}
					*/
					$info .= $this->_get_text('payment_bic').': '.$instr['bic']."\n";
					$info .= $this->_get_text('payment_iban').': '.$instr['iban']."\n";
					$info .= $this->_get_text('payment_reference').': '.$instr['reference']."\n";
					$info .= $this->_get_text('payment_amount').': '.$instr['amount'].' '.$instr['currencyCode']."\n";
					$info .= $this->_get_text('payment_period').': '.$instr['paymentPeriod'].' '.$this->_get_text('payment_days')."\n";
				}
				else { // HTML
					$info .= '<div class="billsafe_pinfo">';
					$info .= '<p>'.$instr['legalNote'].'</p>';
					$info .= '<p>'.$instr['note'].'</p>';
					$info .= '<table class="billsafe_account">';
					$info .= '<tr><td class="label">'.$this->_get_text('payment_recipient').':</td><td>'.$instr['recipient'].'</td></tr>';
					/*
					if(!empty($instr['bankCode']) && !empty($instr['accountNumber'])) {
						$info .= '<tr><td class="label">'.$this->_get_text('payment_bankcode').':</td><td>'.$instr['bankCode'].'</td></tr>';
						$info .= '<tr><td class="label">'.$this->_get_text('payment_accountno').':</td><td>'.$instr['accountNumber'].'</td></tr>';
					}
					*/
					$info .= '<tr><td class="label">'.$this->_get_text('payment_bic').':</td><td>'.$instr['bic'].'</td></tr>';
					$info .= '<tr><td class="label">'.$this->_get_text('payment_iban').':</td><td>'.$instr['iban'].'</td></tr>';
					$info .= '<tr><td class="label">'.$this->_get_text('payment_reference').':</td><td>'.$instr['reference'].'</td></tr>';
					$info .= '<tr><td class="label">'.$this->_get_text('payment_amount').':</td><td>'.$instr['amount'].' '.$instr['currencyCode'].'</td></tr>';
					$info .= '<tr><td class="label">'.$this->_get_text('payment_period').':</td><td>'.$instr['paymentPeriod'].' '.$this->_get_text('payment_days').'</td></tr>';
					$info .= '</table>';
					$info .= '</div>';
				}
			}
		}
		else {
			$info = $this->_get_text('cannot_retrieve_payment_instruction');
		}
		return $info;
	}

	public function getArticleList($orders_id = null, $transaction_id = null) {
		if($orders_id === null && $transaction_id === null) {
			die('invalid call of getArticleList()');
		}
		if($transaction_id === null) {
			$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		}
		$result = $this->_makeServiceCall('getArticleList', array('transactionId' => $transaction_id));
		if(isset($result['ack']) && $result['ack'] == 'OK' && isset($result['articleList'])) {
			return $result['articleList'];
		}
		return false;
	}

	public function updateArticles($orders_id, $articles) {
		$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		$params = array(
			'transactionId' => $transaction_id,
			'order_currencyCode' => 'EUR',
		);
		$sum = 0;
		$taxes = 0;
		foreach($articles as $idx => $article) {
			$params['articleList_'.$idx.'_number'] = $article['number'];
			$params['articleList_'.$idx.'_name'] = $article['name'];
			$params['articleList_'.$idx.'_type'] = $article['type'];
			$params['articleList_'.$idx.'_quantity'] = $article['quantity'];
			$params['articleList_'.$idx.'_quantityShipped'] = $article['quantityShipped'];
			$params['articleList_'.$idx.'_grossPrice'] = $article['grossPrice'];
			$params['articleList_'.$idx.'_tax'] = $article['tax'];
			$articleprice = $article['quantity'] * $article['grossPrice'];
			$sum += $articleprice;
			$taxes += $articleprice - ($articleprice / ((100 + $article['tax']) / 100));
		}
		$params['order_amount'] = number_format($sum, 2, '.', '');
		$params['order_taxAmount'] = number_format($taxes, 2, '.', '');
		$result = $this->_makeServiceCall('updateArticleList', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK' && isset($result['success']) && $result['success'] == 'TRUE')) {
			$message = $this->_get_text('update_articlelist_failed');
			if(isset($result['errorList'])) {
				foreach($result['errorList'] as $error) {
					$message .= '<br>'.$error['code'] .' '. $error['message'];
				}
			}
			throw new BillSafeException($message);
		}
		return true;
	}

	public function getPayoutStatus($orders_id) {
		$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		$params = array(
			'transactionId' => $transaction_id,
		);
		$result = $this->_makeServiceCall('getPayoutStatus', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK') && $result['errorList'][0]['code'] != '305') {
			$message = $this->_get_text('getpayoutstatus_failed');
			throw new BillSafeException($message);
		}
		return $result;
	}

	public function getAgreedHandlingCharges() {
		$params = array(
		);
		$result = $this->_makeServiceCall('getAgreedHandlingCharges', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK')) {
			$message = $this->_get_text('getagreedhandlingcharges_failed');
			throw new BillSafeException($message);
		}
		return $result;
	}

	public function reportShipment($orders_id, $articles, $other_params) {
		$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		$params = array(
			'transactionId' => $transaction_id,
			'shippingDate' => $other_params['shippingdate'],
		);
		if($other_params['parcel_service'] == 'OTHER') {
			$params['parcel_service'] = 'OTHER';
			$params['parcel_company'] = $other_params['parcel_service_other'];
		}
		else if($other_params['parcel_service'] != 'none') {
			$params['parcel_service'] = $other_params['parcel_service'];
		}
		if(!empty($other_params['parcel_trackingid']) && !empty($params['parcel_service'])) {
			$params['parcel_trackingId'] = $other_params['parcel_trackingid'];
		}
		foreach($articles as $idx => $article) {
			$params['articleList_'.$idx.'_number'] = $article['number'];
			$params['articleList_'.$idx.'_name'] = $article['name'];
			$params['articleList_'.$idx.'_type'] = $article['type'];
			$params['articleList_'.$idx.'_quantity'] = $article['quantity'];
			$params['articleList_'.$idx.'_grossPrice'] = $article['grossPrice'];
			$params['articleList_'.$idx.'_tax'] = $article['tax'];
		}
		$result = $this->_makeServiceCall('reportShipment', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK')) {
			$message = $this->_get_text('reportshipment_failed');
			throw new BillSafeException($message);
		}
		else {
			// log shipment locally
			$log_query = "INSERT INTO billsafe_products_shipped (orders_id, transaction_id, shipping_date, parcel_service, parcel_company, parcel_trackingid, article_number, article_name, article_type, article_quantity, article_grossprice, article_tax) ".
				"VALUES (:orders_id, ':transaction_id', ':shipping_date', ':parcel_service', ':parcel_company', ':parcel_trackingid', ':article_number', ':article_name', ':article_type', :article_quantity, ':article_grossprice', :article_tax)";
			foreach($articles as $idx => $article) {
				if($article['quantity'] == 0) {
					continue;
				}
				$log_query_data = strtr($log_query, array(
					':orders_id' => (int)$orders_id,
					':transaction_id' => $transaction_id,
					':shipping_date' => xtc_db_input($other_params['shippingdate']),
					':parcel_service' => xtc_db_input($other_params['parcel_service']),
					':parcel_company' => xtc_db_input($other_params['parcel_service_other']),
					':parcel_trackingid' => xtc_db_input($other_params['parcel_trackingid']),
					':article_number' => xtc_db_input($article['number']),
					':article_name' => xtc_db_input($article['name']),
					':article_type' => xtc_db_input($article['type']),
					':article_quantity' => xtc_db_input($article['quantity']),
					':article_grossprice' => xtc_db_input($article['grossPrice']),
					':article_tax' => xtc_db_input($article['tax']),
				));
				xtc_db_query($log_query_data);
			}
		}
		return true;
	}

	public function getShippedArticles($orders_id) {
		$query = "SELECT * FROM billsafe_products_shipped WHERE orders_id = ".(int)$orders_id." ORDER BY shipping_date ASC";
		$result = xtc_db_query($query);
		$articles = array();
		while($row = xtc_db_fetch_array($result)) {
			$articles[] = $row;
		}
		return $articles;
	}

	public function reportDirectPayment($orders_id, $amount, $date) {
		$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		$timestamp = strtotime($date);
		if($timestamp === false) {
			$message = $this->_get_text('cannot_parse_date');
			throw new BillSafeException($message);
		}
		$date_formatted = date('Y-m-d', $timestamp);
		$params = array(
			'transactionId' => $transaction_id,
			'amount' => number_format($amount, 2, '.', ''),
			'date' => $date_formatted,
			'currencyCode' => 'EUR',
		);
		$result = $this->_makeServiceCall('reportDirectPayment', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK')) {
			$message = $this->_get_text('reportdirectpayment_failed');
			foreach($result['errorList'] as $error) {
				$message .= '<br>'.$error['code'].' - '.$error['message'];
			}
			throw new BillSafeException($message);
		}
		else {
			// log payment locally
			$query = "INSERT INTO billsafe_directpayments (orders_id, amount, date) VALUES (:orders_id, :amount, ':date')";
			$query = strtr($query, array(':orders_id' => (int)$orders_id, ':amount' => (double)$amount, ':date' => date('Y-m-d', strtotime($date))));
			xtc_db_query($query);
		}
		return true;
	}

	public function getDirectPayments($orders_id) {
		$query = "SELECT * FROM billsafe_directpayments WHERE orders_id = ".(int)$orders_id." ORDER BY date ASC";
		$result = xtc_db_query($query);
		$payments = array();
		while($row = xtc_db_fetch_array($result)) {
			$payments[] = $row;
		}
		return $payments;
	}

	public function pauseTransaction($orders_id, $days) {
		$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		$params = array(
			'transactionId' => $transaction_id,
			'pause' => (int)$days,
		);
		$result = $this->_makeServiceCall('pauseTransaction', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK')) {
			$message = $this->_get_text('pausetransaction_failed');
			foreach($result['errorList'] as $error) {
				$message .= '<br>'.$error['code'].' - '.$error['message'];
			}
			throw new BillSafeException($message);
		}
		return true;
	}

	public function setInvoiceNumber($orders_id, $invoice_code) {
		$transaction_id = $this->_getTransactionIdByOrdersId($orders_id);
		$params = array(
			'transactionId' => $transaction_id,
			'invoiceNumber' => $invoice_code,
		);
		$result = $this->_makeServiceCall('setInvoiceNumber', $params);
		if(!(isset($result['ack']) && $result['ack'] == 'OK')) {
			$message = $this->_get_text('setinvoicenumber_failed');
			throw new BillSafeException($message);
		}
		return true;
	}


	/*
	 * Utilities
	 */


	public function markOrderAsAbortedOrDeclined($orders_id, $comment = '')
	{
		$this->_log('marking '.$orders_id.' as aborted/declined');
		$orders_status_id = @constant('MODULE_PAYMENT_BILLSAFE_3_INVOICE_TMPORDER_STATUS_ID');
		$this->_setOrderStatus((int)$orders_id, (int)$orders_status_id, trim($this->get_text('prepareorder_failed')."\n".$comment));
	}

	public function updateOrdersStatusAfterShipment($orders_id, $orders_status_id, $comment = '', $notify = false) {
		if(function_exists('gm_set_conf')) {
			gm_set_conf('BILLSAFE3_ORDERS_STATUS_AFTER_SHIPMENT', $orders_status_id);
		}
		$comment = $this->_get_text('shipment_reported') . (empty($comment) ? '' : "\n".$comment);
		return $this->_setOrderStatus($orders_id, $orders_status_id, $comment, $notify);
	}

	public function getOrderInfo($orders_id) {
		$info = array(
			'orders_id' => $orders_id,
			'transaction_id' => $this->_getTransactionIdByOrdersId($orders_id),
		);
		try {
			$payout = $this->getPayoutStatus($orders_id);
			if(isset($payout['payoutList'])) {
				foreach($payout['payoutList'] as $po) {
					$info['payouts'] .= $po['amount'] .' '. $po['date'] .' '. $po['settlementNumber'] .'<br>';
				}
			}
			if(isset($payout['returnList'])) {
				foreach($payout['returnList'] as $po) {
					$info['returns'] .= $po['amount'] .' '. $po['date'] .' '. $po['settlementNumber'] .'<br>';
				}
			}
		}
		catch(Exception $e) {
			$info['payouts'] = 'n/a';
		}
		/*
		$hcharges = $this->getAgreedHandlingCharges();
		if(isset($hcharges['agreedCharge'])) {
			foreach($hcharges['agreedCharge'] as $charge) {
				$info['handling_charges'] .= $this->_get_text('upto') .' ' .$charge['maxAmount'] .'&nbsp;EUR: '. $charge['charge'] .'&nbsp;EUR';
			}
		}
		*/
		$directpayments = $this->getDirectPayments($orders_id);
		if(!empty($directpayments)) {
			$br = '';
			foreach($directpayments as $dp) {
				$info['direct_payments'] .= $br.$dp['date'].':&nbsp;'.number_format($dp['amount'], 2, '.', '').'&nbsp;EUR';
				$br = '<br>';
			}
		}
		return $info;
	}

	public function getParcelServices() {
		$services = array(
			"DPAG", "DHL", "UPS", "GLS", "DPD", "HERMES", "TNT", "FEDEX",
		);
		return $services;
	}

	public function paymentModuleIsConfigured($submodule = 'invoice') {
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		// $submodule = 'invoice' | 'installment'
		$coo_lang_file_master->init_from_lang_file('lang/'.$_SESSION['language'].'/modules/payment/billsafe_3_'.$submodule.'.php');
		require_once DIR_FS_CATALOG.'/includes/modules/payment/billsafe_3_'.$submodule.'.php';
		$classname = 'billsafe_3_'.$submodule;
		$bs3p = new $classname();
		$is_installed = $bs3p->isConfigured();
		return $is_installed;
	}

	protected function _get_text($key) {
		$const_name = 'BILLSAFE3_'.strtoupper($key);
		if(defined($const_name)) {
			return constant($const_name);
		}
		else {
			return $this->_txt->get_text($key);
		}
	}

	public function get_text($key) {
		return $this->_get_text($key);
	}

	protected function _getTransactionIdByOrdersId($orders_id) {
		$t_id = false;
		$result = xtc_db_query("SELECT transaction_id FROM orders_billsafe WHERE orders_id = ".(int)$orders_id, 'db_link', false);
		while($row = xtc_db_fetch_array($result)) {
			$t_id = $row['transaction_id'];
		}
		return $t_id;
	}

	public function isValidOrder($orders_id) {
		$tid = $this->_getTransactionIdByOrdersId($orders_id);
		$valid = $tid !== false;
		return $valid;
	}

	public function addressesAreEqual($billing, $delivery) {
		$fields = array('firstname', 'lastname', 'company', 'street_address', 'suburb', 'city', 'postcode', 'state', 'zone_id', 'country_id');
		foreach($fields as $f) {
			if($billing[$f] != $delivery[$f]) {
				return false;
			}
		}
		if($billing['country']['id'] != $delivery['country']['id']) {
			return false;
		}
		return true;
	}

	protected function _setOrderStatus($orders_id, $order_status_id = null, $comment = '', $notify = false) {
		if($order_status_id !== null) {
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$order_status_id."' WHERE orders_id='".$orders_id."'");
		}
		else {
			$result = xtc_db_query("SELECT orders_status FROM ".TABLE_ORDERS." WHERE orders_id = '".$orders_id."'");
			$row = xtc_db_fetch_array($result);
			$order_status_id = $row['orders_status'];
		}
		if(!empty($comment)) {
			xtc_db_query("INSERT INTO orders_status_history (`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`) ".
				"VALUES (".$orders_id.", ".$order_status_id.", now(), '0', '".xtc_db_input($comment)."')");
		}
	}

}

class BillSafeException extends Exception { }
class GMBillSafeTransactionIdNotFoundException extends BillSafeException { }

MainFactory::load_origin_class('GMBillSafe');
