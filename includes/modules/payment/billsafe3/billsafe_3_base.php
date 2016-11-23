<?php
/* --------------------------------------------------------------
   billsafe_3.php 2015-04-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
defined('MODULE_PAYMENT_BILLSAFE_3_MINORDER') or define('MODULE_PAYMENT_BILLSAFE_3_MINORDER', 0);
defined('MODULE_PAYMENT_BILLSAFE_3_MAXORDER') or define('MODULE_PAYMENT_BILLSAFE_3_MAXORDER', 1000);

class billsafe_3_base_ORIGIN {
	var $code = 'billsafe_3', $title, $description, $enabled;
	//var $form_action_url;
	var $billsafe;
	var $tmpOrders = true;
	var $tmpStatus = MODULE_PAYMENT_BILLSAFE_3_TMPORDER_STATUS_ID;
	var $logo = '';

	public function __construct() {
		global $order;

		$this->title = MODULE_PAYMENT_BILLSAFE_3_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION.'<br><br>'.MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION_LINK;//.'<br><br>'.$this->_checkRequirements();
		$this->sort_order = MODULE_PAYMENT_BILLSAFE_3_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_BILLSAFE_3_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_BILLSAFE_3_TEXT_INFO;
		if ((int) MODULE_PAYMENT_BILLSAFE_3_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_BILLSAFE_3_ORDER_STATUS_ID;
		}

		if(is_object($order)) {
			$this->update_status();
		}
	}

	function _checkRequirements() {
		$out = MODULE_PAYMENT_BILLSAFE_3_SYSTEM_REQUIREMENTS.':<br>';
		if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
			$has_curl = in_array('curl', get_loaded_extensions());
			$out .= "cURL: ". ($has_curl ? '<span style="color:green">'.MODULE_PAYMENT_BILLSAFE_3_OK.'</span>' : '<span style="color:red">'.MODULE_PAYMENT_BILLSAFE_3_MISSING.'</span><br>');
		}
		return $out;
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_BILLSAFE_3_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_BILLSAFE_3_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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

	function javascript_validation() {
		return false;
	}


	function selection() {
		$bs = new GMBillSafe($this->code);
		
		if(isset($_GET['billsafe_close']) && $_GET['billsafe_close'] == 'true')
		{
			ob_clean();
			unset($_SESSION['payment']);
			if(isset($_SESSION['tmp_oID']))
			{
				$bs->markOrderAsAbortedOrDeclined($_SESSION['tmp_oID']);
				unset($_SESSION['tmp_oID']);
			}
			echo '<script>top.location = "' . xtc_href_link('shopping_cart.php') . '"</script>';
			exit;
		}
		if($_SESSION['language'] != 'german') {
			return false;
		}
		if($GLOBALS['order']->info['currency'] != 'EUR') {
			return false;
		}
		if(!empty($_GET['payment_error']) && $_GET['payment_error'] == $this->code)
		{
			return false;
		}
		$billing = $GLOBALS['order']->billing;
		$delivery = $GLOBALS['order']->delivery;
		if(!$bs->orderIsWithinLimits($GLOBALS['order']->info['total'])) {
			return false;
		}
		$addresses_are_equal = $bs->addressesAreEqual($billing, $delivery);
		if(constant('MODULE_PAYMENT_'.strtoupper($this->code).'_PREVALIDATE') == 'True' && strpos($_SERVER['PHP_SELF'], FILENAME_CHECKOUT_PAYMENT) !== false) {
			$valid = $bs->prevalidateOrder($GLOBALS['order']);
			if(($this->code == 'billsafe_3_invoice' && $valid['invoice'] !== true) || ($this->code == 'billsafe_3_installment' && $valid['hirePurchase'] !== true)) {
				return false;
			}
		}

		$selection = array(
			'id' => $this->code,
			'module' => $this->title,
			'description' => $this->logo.$this->info . '<script>if(top.lpg) { top.lpg.close(); }</script>',
			'fields' => array(),
		);

		if($this->code == 'billsafe_3_installment' && $valid['hirePurchase'] == true) {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_BILLSAFE_3_INSTALLMENTAMOUNT.':',
				'field' => $valid['installmentAmount'].'&nbsp;'.$valid['currencyCode'],
			);
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_BILLSAFE_3_INSTALLMENTCOUNT.':',
				'field' => $valid['installmentCount'],
			);
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_BILLSAFE_3_PROCESSINGFEE.':',
				'field' => $valid['processingFee'].'&nbsp;'.$valid['currencyCode'],
			);
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_BILLSAFE_3_ANNUALPERCENTAGERATE.':',
				'field' => $valid['annualPercentageRate'].'%',
			);

		}

		if(!$addresses_are_equal) {
			$selection['description'] .= '<br><strong>'.MODULE_PAYMENT_BILLSAFE_3_ADDRESSES_MUST_MATCH.'</strong>';
		}

		return $selection;
	}

	function pre_confirmation_check() {
		$billing = $GLOBALS['order']->billing;
		$delivery = $GLOBALS['order']->delivery;
		$bs = new GMBillSafe();
		$addresses_are_equal = $bs->addressesAreEqual($billing, $delivery);
		if(!$addresses_are_equal) {
			$_SESSION['billsafe_3_error'] = html_entity_decode(MODULE_PAYMENT_BILLSAFE_3_ADDRESSES_MUST_MATCH, ENT_COMPAT, 'ISO-8859-1');
			$sid = session_name() . '=' . session_id();
			xtc_redirect(HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code.'&'.$sid);
		}
		return false;
	}

	function confirmation() {
		$confirmation = array(
			'title' => MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION,
		);
		return $confirmation;
	}

	function refresh() {
	}

	function process_button() {
		global $order;
		$pb = '';
		return $pb;
	}

	function payment_action() {
		$bs = new GMBillSafe();
		if($this->code == 'billsafe_3_installment') {
			$mode = 'installment';
		}
		else {
			$mode = 'invoice';
		}
		try
		{
			$result = $bs->prepareOrder($GLOBALS['order'], $_SESSION['tmp_oID'], $GLOBALS['order_totals'], $mode);
		}
		catch(BillSafeException $e)
		{
			$result = false;
		}
		$sid = session_name() . '=' . session_id();
		if($result === false) {
			$bs->markOrderAsAbortedOrDeclined($_SESSION['tmp_oID']);
			unset($_SESSION['payment']);
			unset($_SESSION['tmp_oID']);
			$_SESSION['billsafe_3_error'] = MODULE_PAYMENT_BILLSAFE_3_PREPAREORDER_FAILED;
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code.'&'.$sid);
		}
		else {
			$_SESSION['billsafe_token'] = $result;
			if(strtolower(constant('MODULE_PAYMENT_'.strtoupper($this->code).'_LAYER')) == 'true') {
				xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_billsafe.php?mode=layer&'.$sid);
			}
			else {
				xtc_redirect($bs->getPaymentURL($result));
			}
		}
	}

	function before_process() {
		return false;
	}

	function after_process() {
		$insert_id = $GLOBALS['insert_id'];
		try {
			$bs = new GMBillSafe();
			$bs->getPaymentInfoCached($insert_id, true);
		}
		catch(Exception $e) {
		}

		if($this->order_status) {
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
		}

	}

	function get_error() {
		if(isset($_SESSION['billsafe_3_error'])) {
			$error = array('error' => $_SESSION['billsafe_3_error']);
			unset($_SESSION['billsafe_3_error']);
			return $error;
		}
		return false;
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install($augment = false) {
		$config = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data) {
			if($augment === true && defined('MODULE_PAYMENT_'.strtoupper($this->code).'_'.$key))
			{
				continue;
			}
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_PAYMENT_".strtoupper($this->code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
			if($augment === true)
			{
				define('MODULE_PAYMENT_'.strtoupper($this->code).'_'.$key, $data['configuration_value']);
			}
		}
	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
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

	function isInstalled() {
		foreach($this->keys() as $key) {
			if(!defined($key)) {
				return false;
			}
		}
		return true;
	}

	function isConfigured() {
		if(!$this->isInstalled()) {
			return false;
		}
		$required_keys = array('MERCHANT_ID', 'MERCHANT_LICENSE');
		foreach($required_keys as $rkey) {
			$value = trim(constant('MODULE_PAYMENT_'.strtoupper($this->code).'_'.$rkey));
			if(empty($value)) {
				return false;
			}
		}
		return true;
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
			'MERCHANT_ID' => array(
				'configuration_value' => '',
			),
			'MERCHANT_LICENSE' => array(
				'configuration_value' => '',
			),
			'URL_IMAGE' => array(
				'configuration_value' => '',
			),
			'IMAGE_CODE' => array(
				'configuration_value' => '',
			),
			'PREVALIDATE' => array(
				'configuration_value' => 'True',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'SANDBOX' => array(
				'configuration_value' => 'True',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'LAYER' => array(
				'configuration_value' => 'False',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'ZONE' => array(
				'configuration_value' => '0',
				'use_function' => 'xtc_get_zone_class_title',
				'set_function' => 'xtc_cfg_pull_down_zone_classes(',
			),
			'TMPORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
			'ORDER_STATUS_ID' => array(
				'configuration_value' => '',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '0',
			),
		);

		return $config;
	}

}
MainFactory::load_origin_class('billsafe_3_base');
