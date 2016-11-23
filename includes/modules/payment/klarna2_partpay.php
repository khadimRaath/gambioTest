<?php
/* --------------------------------------------------------------
	klarna2_partpay.php 2015-09-10 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
defined('INVALID_ORDERS_STATUS') or define('INVALID_ORDERS_STATUS', -1);

class klarna2_partpay_ORIGIN {
	public $code;
	public $title;
	public $description;
	public $enabled;
	public $tmpOrders = true;
	public $tmpStatus;

	public function __construct() {
		$config_url = GM_HTTP_SERVER.DIR_WS_ADMIN.'klarna_config.php';
		$this->code = 'klarna2_partpay';
		$this->title = MODULE_PAYMENT_KLARNA2_PARTPAY_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_KLARNA2_PARTPAY_TEXT_DESCRIPTION;
		if(strpos($_SERVER['REQUEST_URI'], 'admin/modules.php') !== false) {
			$this->description .= '<div class="add-margin-top-20"><a class="btn" href="' . GM_HTTP_SERVER . DIR_WS_ADMIN . 'admin.php?do=KlarnaModuleCenterModule">Konfiguration</a></div>';
		}
		$this->sort_order = MODULE_PAYMENT_KLARNA2_PARTPAY_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_KLARNA2_PARTPAY_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_KLARNA2_PARTPAY_TEXT_INFO;
		$this->tmpStatus = MODULE_PAYMENT_KLARNA2_PARTPAY_TMPSTATUS;
		$this->order_status = MODULE_PAYMENT_KLARNA2_PARTPAY_ORDERSTATUS;

		if(is_object($GLOBALS['order'])) {
			$this->update_status();
		}

	}

	public function klarna2_partpay() {
		$this->__construct();
	}

	public function update_status() {
		if (($this->enabled == true) && ((int) constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ZONE') > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ZONE')."' and zone_country_id = '".$GLOBALS['order']->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $GLOBALS['order']->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	public function javascript_validation() {
		return false;
	}

	public function selection() {
		$klarna = new GMKlarna();
		if($_SESSION['currency'] != $klarna->getCurrencyString()) {
			$klarna->_log("no partpay: currency mismatch");
			return false;
		}
		$order = $GLOBALS['order'];
		$total_amount = $GLOBALS['order']->info['total'];
		if(!is_numeric($total_amount)) {
			$total_amount = $_SESSION['klarna_total_amount'];
		}
		else {
			$_SESSION['klarna_total_amount'] = $total_amount;
		}
		$max_amount = $klarna->getMaximumPartpayAmount();
		if($total_amount > $max_amount) {
			$klarna->_log("partpay: maximum amount of $max_amount exceeded.");
			return false;
		}
		$pclasses = $klarna->getPClasses($total_amount);
		$check = $klarna->paymentSelectionCheck($GLOBALS['order']);
		if($check['allow_select'] == true && $pclasses !== false) {
			$minfee = $klarna->formatAmount($klarna->getMinimumMonthlyCost($total_amount));
			$country = $order->delivery['country']['iso_code_2'];
			$logo_width = 150;
			$description = '';
			$description .= '<img style="float: left; margin-right: 1em; background: #fff;" src="'.$klarna->getCDNLogoURL('account', $logo_width, $country).'">';
			$description .= '<div style="float:left; width: 400px;">';
			$description .= strtr($this->info, array('#minfee' => $minfee, '#url' => 'http://www.klarna.com/'));
			$description .= ' <span id="klarna_acc_cond"></span><br><br>';
			$description .= '<div class="klarna_phone">';
			$description .= $klarna->get_text('your_phone_number');
			$description .= ': <input name="klarna_ppay_phone" type="text" value="'.$order->customer['telephone'].'">';
			$description .= '</div>';
			$dob = $this->_getDateOfBirth();
			$description .= '<div class="klarna_dob">'.$klarna->get_text('your_date_of_birth').'<br>';
			$description .= '<select name="klarna_ppay_dob_year">';
			for($year = date('Y') - 10; $year >= 1900; $year--) {
				$description .= '<option value="'.$year.'" '.($year == $dob['year'] ? 'selected="selected"':'').'>'.$year.'</option>';
			}
			$description .= '</select>';
			$description .= '<select name="klarna_ppay_dob_month">';
			for($month = 1; $month <= 12; $month++) {
				$description .= '<option value="'.$month.'" '.($month == $dob['month'] ? 'selected="selected"':'').'>'.sprintf('%02d', $month).'</option>';
			}
			$description .= '</select>';
			$description .= '<select name="klarna_ppay_dob_day">';
			for($day = 1; $day <= 31; $day++) {
				$description .= '<option value="'.$day.'" '.($day == $dob['day'] ? 'selected="selected"':'').'>'.sprintf('%02d', $day).'</option>';
			}
			$description .= '</select>';
			$description .= '</div>';
			$num_pno_digits = $klarna->getNumPNODigits();
			if($num_pno_digits > 0) {
				$description .= '<br>'.$klarna->get_text('enter_pno').': ';
				$description .= '<input type="text" name="klarna_ppay_pno" maxlength="'.$num_pno_digits.'" size="'.$num_pno_digits.'" placeholder="'.str_repeat('N', $num_pno_digits).'">';
			}
			$description .= '<br>';
			if(count($pclasses) > 1)
			{
				$description .= '<select name="klarna_pclass_id">';
				foreach($pclasses as $pclass) {
					$description .= '<option value="'.$pclass->getId().'">';
					$description .= $pclass->getDescription() .' ('.$klarna->get_text('starting_at').' '.
						$klarna->formatAmount(KlarnaCalc::calc_monthly_cost($total_amount, $pclass, KlarnaFlags::CHECKOUT_PAGE)). ')';
					$description .= '</option>';
				}
				$description .= '</select>';
			}
			else
			{
				$pclass = array_pop($pclasses);
				$description .= '<input type="hidden" name="klarna_pclass_id" value="'.$pclass->getId().'">';
				$description .= $pclass->getDescription() .' ('.$klarna->get_text('starting_at').' '.
					$klarna->formatAmount(KlarnaCalc::calc_monthly_cost($total_amount, $pclass, KlarnaFlags::CHECKOUT_PAGE)). ')';
			}
			$description .= $klarna->getCreditWarning();
			if(empty($_SESSION['customer_gender'])) {
				$description .= '<br>'.$klarna->get_text('enter_gender').': ';
				$description .= '<select name="klarna_ppay_gender">';
				$description .= '<option value="0">'.$klarna->get_text('gender_female').'</option>';
				$description .= '<option value="1">'.$klarna->get_text('gender_male').'</option>';
				$description .= '</select>';
			}

			$description .= $klarna->getAccountConditionsLink('klarna_acc_cond');

			if($klarna->consentRequired()) {
				$description .= '<div class="klarna_consent">';
				$description .= '<input name="klarna_ppay_consent" type="checkbox" value="1" id="klarna_ppay_consent">';
				$description .= '<label for="klarna_ppay_consent">'.$klarna->getConsentboxText().'</label>';
				$description .= '</div>';
			}

			$description .= '</div>';


			$selection = array(
				'id' => $this->code,
				'module' => $this->title,
				'description' => $description,
			);
		}
		else {
			if($check['is_b2b'] == true) {
				$no_b2b_description = $klarna->get_text('no_b2b_description');
				$no_b2b_description .= '<input type="hidden" name="'.$this->code.'_no_b2b" value="1">';
				$selection = array(
					'id' => $this->code,
					'module' => $this->title,
					'description' => $no_b2b_description,
				);
			}
			else {
				$selection = false;
			}
			if($pclasses == false) {
				$klarna->_log("no partpay: no pclass");
			}
		}
		return $selection;
	}

	protected function _getDateOfBirth() {
		if(isset($_SESSION['klarna_ppay_dob'])) {
			list($session_year, $session_month, $session_day) = explode('-', $_SESSION['klarna_ppay_dob']);
			$dob = array(
				'year' => $session_year,
				'month' => $session_month,
				'day' => $session_day,
			);
		}
		else {
			$query = "SELECT YEAR(`customers_dob`) AS year, MONTH(`customers_dob`) AS month, DAY(`customers_dob`) AS day FROM customers WHERE customers_id = :cid";
			$query = strtr($query, array(':cid' => (int)$_SESSION['customer_id']));
			$result = xtc_db_query($query, 'db_link', false);
			$dob = array('year' => '0000', 'month' => '00', 'day' => '00');
			while($row = xtc_db_fetch_array($result)) {
				$dob = $row;
			}
		}
		return $dob;
	}

	protected function _setDateOfBirth($year, $month, $day, $customers_id = null) {
		if($customers_id === null) {
			$customers_id = $_SESSION['customer_id'];
		}
		$dob = sprintf('%04d-%02d-%02d', $year, $month, $day);
		$query = "UPDATE customers SET customers_dob = ':dob' WHERE customers_id = :cid";
		$query = strtr($query, array(':dob' => $dob, ':cid' => (int)$customers_id));
		xtc_db_query($query);
	}

	protected function _setPhoneNumber($phone) {
		$query = "UPDATE customers SET customers_telephone = '".xtc_db_input($phone)."' WHERE customers_id = ".(int)$_SESSION['customer_id'];
		xtc_db_query($query);
	}

	public function pre_confirmation_check() {
		$klarna = new GMKlarna();

		if(isset($_POST[$this->code.'_no_b2b'])) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'no_b2b', session_name() => session_id())));
		}

		// check for dob
		if(isset($_POST['klarna_ppay_dob_year']) && isset($_POST['klarna_ppay_dob_month']) && isset($_POST['klarna_ppay_dob_day'])) {
			$_SESSION['klarna_ppay_dob'] = $_POST['klarna_ppay_dob_year'] .'-'. $_POST['klarna_ppay_dob_month'] .'-'. $_POST['klarna_ppay_dob_day'];
			$this->_setDateOfBirth($_POST['klarna_ppay_dob_year'], $_POST['klarna_ppay_dob_month'], $_POST['klarna_ppay_dob_day']);
		}
		if(!$this->_checkCustomerAge()) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'too_young', session_name() => session_id())));
		}
		if(!empty($_POST['klarna_ppay_pno'])) {
			$_SESSION['klarna_pno'] = $_POST['klarna_ppay_pno'];
		}
		else {
			$_SESSION['klarna_pno'] = '';
		}

		$_SESSION['klarna_ppay_consent'] = isset($_POST['klarna_ppay_consent']);
		if($klarna->consentRequired() && $_SESSION['klarna_ppay_consent'] == false) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'no_consent', session_name() => session_id())));
		}

		if(isset($_POST['klarna_ppay_phone'])) {
			$phone = trim($_POST['klarna_ppay_phone']);
			$this->_setPhoneNumber($phone);
		}
		else {
			$phone = $GLOBALS['order']->customer['telephone'];
		}
		if(empty($phone)) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'no_phone', session_name() => session_id())));
		}

		if(empty($_SESSION['klarna_pclass_id']) && !isset($_POST['klarna_pclass_id'])) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'select_pclass', session_name() => session_id())));
		}
		$_SESSION['klarna_pclass_id'] = $_POST['klarna_pclass_id'];

		if(isset($_POST['klarna_ppay_gender'])) {
			$gender = $_POST['klarna_ppay_gender'] == '1' ? 'm' : 'f';
			xtc_db_query("UPDATE customers SET customers_gender = '".$gender."' WHERE customers_id = ".$_SESSION['customer_id']);
			$_SESSION['customer_gender'] = $gender;
		}

		if($this->_addressesDiffer($_SESSION['billto'], $_SESSION['sendto'])) {
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'addresses_differ', session_name() => session_id())));
		}

		return false;
	}

	protected function _addressesDiffer($ab_id1, $ab_id2) {
		if($ab_id1 == $ab_id2) {
			return false;
		}
		$address1 = $this->_getAddressBookEntry($ab_id1);
		$address2 = $this->_getAddressBookEntry($ab_id2);
		$fields = array('entry_gender', 'entry_firstname', 'entry_lastname', 'entry_company', 'entry_street_address', 'entry_suburb', 'entry_postcode', 'entry_city', 'entry_state', 'entry_country_id');
		foreach($fields as $field) {
			if(trim($address1[$field] != trim($address2[$field]))) {
				return true;
			}
		}
		return false;
	}

	protected function _getAddressBookEntry($ab_id) {
		$query = "SELECT * FROM address_book WHERE address_book_id = ".(int)$ab_id;
		$result = xtc_db_query($query);
		$entry = false;
		while($row = xtc_db_fetch_array($result)) {
			$entry = $row;
		}
		return $entry;
	}

	protected function _checkCustomerAge() {
		$dob = $this->_getDateOfBirth();
		$dob_time = strtotime(sprintf('%04d-%02d-%02d', $dob['year'], $dob['month'], $dob['day']));
		$dob_max = strtotime('18 years ago');
		$old_enough = $dob_time < $dob_max;
		return $old_enough;
	}

	public function confirmation() {
		$klarna = new GMKlarna();
		$cw = '<div class="klarna_creditwarning_big">'.$klarna->getCreditWarning().'</div>';

		$confirmation = array(
			'title' => constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION').$cw,
		);
		return $confirmation;
	}

	public function refresh() {
	}

	public function process_button() {
		$order = $GLOBALS['order'];
		$pb = '';
		return $pb;
	}

	public function payment_action() {
		$order = $GLOBALS['order'];
		$orders_id = $GLOBALS['insert_id'];
		$klarna = new GMKlarna();
		try {
			$result = $klarna->reserveInvoiceAmount($order, $orders_id, $_SESSION['klarna_pclass_id']);
		}
		catch(Exception $e) {
			$this->_invalidateTempOrder($orders_id);
			$_SESSION['klarna2_error'] = utf8_encode($e->getMessage());
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code)));
		}
		if($result[1] == KlarnaFlags::PENDING || $result[1] == KlarnaFlags::ACCEPTED) {
			$query = "UPDATE orders SET orders_status = ".MODULE_PAYMENT_KLARNA2_PARTPAY_ORDERSTATUS." WHERE orders_id = ".(int)$orders_id;
			xtc_db_query($query);
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PROCESS);
		}
		else { // KlarnaFlags::DENIED
			$this->_invalidateTempOrder($orders_id);
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?'.http_build_query(array('payment_error' => $this->code, 'error_reason' => 'denied')));
		}
	}

	protected function _invalidateTempOrder($orders_id) {
		unset($_SESSION['tmp_oID']);
		unset($_SESSION['payment']);
		$klarna = new GMKlarna();
		$klarna->removeOrder($orders_id);
	}

	public function before_process() {
		return false;
	}

	public function after_process() {
		$insert_id = $GLOBALS['insert_id'];
	}

	public function get_error() {
		if(isset($_GET['error_reason'])) {
			$error_msg = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ERROR_'.strtoupper($_GET['error_reason']));
			if(empty($error_msg)) {
				$error_msg = constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ERROR_UNSPECIFIED');
			}
			$error = array('error' => $error_msg);
			return $error;
		}
		if(isset($_SESSION['klarna2_error'])) {
			$error = array('error' => $_SESSION['klarna2_error']);
			unset($_SESSION['klarna2_error']);
			return $error;
		}
		return false;
	}

	public function check() {
		if(!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	public function install() {
		$config = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_PAYMENT_".strtoupper($this->code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
		}
	}

	public function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	public function keys() {
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		foreach($ckeys as $k) {
			$keys[] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_'.$k;
		}
		return $keys;
	}

	protected function _configuration() {
		$config = array(
			'STATUS' => array(
				'configuration_value' => 'True',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'ALLOWED' => array(
				'configuration_value' => 'AT,DK,FI,DE,NO,SE,NL',
			),
			'ZONE' => array(
				'configuration_value' => '0',
				'use_function' => 'xtc_get_zone_class_title',
				'set_function' => 'xtc_cfg_pull_down_zone_classes(',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '0',
			),
			'TMPSTATUS' => array(
				'configuration_value' => '1',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
			'ORDERSTATUS' => array(
				'configuration_value' => '1',
				'set_function' => 'xtc_cfg_pull_down_order_statuses(',
				'use_function' => 'xtc_get_order_status_name',
			),
		);

		return $config;
	}
}
MainFactory::load_origin_class('klarna2_partpay');