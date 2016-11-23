<?php
/* --------------------------------------------------------------
   sofort.php 2016-07-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-05-22 15:21:58 +0200 (Wed, 22 May 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort.php 6157 2013-05-22 13:21:58Z rotsch $
 */

require_once(DIR_FS_CATALOG.'callback/sofort/helperFunctions.php');

$language = HelperFunctions::getSofortLanguage($_SESSION['language']);
$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_general.php');

/**
 * Superclass of xt-commerce modules
 */
class sofort {
	//Shop-Vars
	var $form_action_url, $tmpOrders, $tmpStatus, $icons_available, $enabled, $code, $description, $title, $sort_order;
	//SOFORT-Vars
	var $title_extern, $sofort, $invoice, $paymentMethod;

	function sofort(){
		if (!isset($_SESSION['sofort'])) $_SESSION['sofort'] = array();

		$this->form_action_url = '';
		$this->tmpOrders = true;
		$tmpStatusId = (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID') ? HelperFunctions::checkStatusId(MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID) : (int)DEFAULT_ORDERS_STATUS_ID);

		//dont set a new status? -> then use DEFAULT_ORDERS_STATUS_ID
		if (HelperFunctions::statusIsUnchangedStatus($tmpStatusId)) $tmpStatusId = DEFAULT_ORDERS_STATUS_ID;

		$this->tmpStatus = HelperFunctions::checkStatusId($tmpStatusId);
		$this->icons_available = '';
		$this->sofort = '';
		$this->invoice = '';
		//all other class-vars are initialized in child

		//first status in history - set if needed (and add to class-vars) @see class order -> function cart()
		//$this->order_status = 'MyStatus';
	}


	/**
	 * check if payment method is allowed in the payment zone
	 * if not: module will be disabled
	 */
	function update_status() {
		global $order;

		$constantValue = constant ('MODULE_PAYMENT_SOFORT_'.$this->paymentMethod.'_ZONE');

		if (($this->enabled == true) && ((int) $constantValue > 0)) {
			$checkFlag = false;
			$checkQuery = xtc_db_query("SELECT zone_id FROM " . HelperFunctions::escapeSql(TABLE_ZONES_TO_GEO_ZONES) . " WHERE geo_zone_id = '" . HelperFunctions::escapeSql($constantValue) . "' AND zone_country_id = '" . HelperFunctions::escapeSql($order->billing['country']['id']) . "' ORDER BY zone_id");

			while ($check = xtc_db_fetch_array($checkQuery)) {
				if ($check['zone_id'] < 1) {
					$checkFlag = true;
					break;
				} elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$checkFlag = true;
					break;
				}
			}

			if ($checkFlag == false) $this->enabled = false;
		}
	}


	function javascript_validation() {
		return '';
	}


	/**
	 * extended in all modules "by sofort"
	 */
	function pre_confirmation_check($vars = '') {
	}


	/**
	 * call with parent::selection() in child
	 */
	function selection() {
		//$this->_checkCancelOrder(); //DONT ENABLE! - not compatible with Gambio because selection() is (always) called in class payment->constructor!

		//return false, if modulfiles are incompatible with installed version or was not correct updated
		if (!$this->_modulVersionCheck()) {
			if (is_object($this->sofort)) {
				$this->sofort->logWarning("Paymentmodul was updated by seller but not correct installed. Installed-version in DB does not match file-versions. Please take a look at MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE or read the manual!");
			} elseif (is_object($this->invoice)) {
				$this->invoice->logWarning("Paymentmodul was updated by seller but not correct installed. Installed-version in DB does not match file-versions. Please take a look at MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE or read the manual!");
			}

			return false;
		}

		if (isset($_SESSION['sofort']['apiKeyIsValid'])) {
			return $_SESSION['sofort']['apiKeyIsValid'];
		} else if (!isset($_SESSION['sofort']['apiKeyIsValid'])) {
			$apiTestResult = HelperFunctions::apiKeyIsValid(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY);

			if (!$apiTestResult) {
				if (is_object($this->sofort)) {
					$this->sofort->log("Notice: Function apiKeyIsValid() returned false.");
				} elseif (is_object($this->invoice)) {
					$this->invoice->log("Notice: Function apiKeyIsValid() returned false.");
				}
			}

			$_SESSION['sofort']['apiKeyIsValid'] = $apiTestResult;

			return $apiTestResult;
		}
	}


	function confirmation () {
		$this->_checkCancelOrder();

		return false;
	}


	function process_button() {
		return false;
	}


	/**
	 * does nothing
	 * overwritten by SV
	 */
	function before_process() {

	}


	function payment_action() {
		global $order, $insert_id, $order_totals;

		$orderId = $insert_id;
		$customerId = $_SESSION['customer_id'];
		$currency = $_SESSION['currency'];

		//if buyer will not successfully abort the payment (e.g. close the SOFORT-Wizard) and the go to the
		//shop-index.php and want to pay again -> set the orderStatus to "aborted"
		$_SESSION['sofort']['cart_pn_sofortueberweisung_id'] = $_SESSION['cart']->cartID . '-' . $orderId;
		$reasons = $this->_getReasons($this->paymentMethod, $customerId, $order, $orderId);
		$paymentSecret = md5(mt_rand().microtime());
		$userVariable_0 = $orderId;
		$userVariable_1 = $customerId;
		$userVariable_2 = $_SESSION['cart']->cartID;
		$userVariable_3 = $paymentSecret;
		$amount = $this->_getShopTotal($order_totals, $order);
		$successUrl = xtc_href_link('callback/sofort/ressources/scripts/sofortReturn.php', 'sofortaction=success&sofortcode='.$this->code, 'SSL', true, false);
		$successUrl = HelperFunctions::cleanUrlParameter($successUrl);
		$cancelUrl = xtc_href_link('callback/sofort/ressources/scripts/sofortReturn.php', 'sofortaction=cancel&sofortcode='.$this->code, 'SSL', true, false);
		$cancelUrl = HelperFunctions::cleanUrlParameter($cancelUrl);
		$notificationUrl = xtc_href_link('callback/sofort/callback.php', 'paymentSecret='.$paymentSecret.'&action=multipay', 'SSL', true, false);
		$notificationUrl = HelperFunctions::cleanUrlParameter($notificationUrl);

		$this->sofort->setAmount($amount, $currency);
		$this->sofort->setReason(HelperFunctions::convertEncoding($reasons[0],3), HelperFunctions::convertEncoding($reasons[1],3));
		$this->sofort->setSuccessUrl(HelperFunctions::convertEncoding($successUrl,3));
		$this->sofort->setAbortUrl(HelperFunctions::convertEncoding($cancelUrl,3));
		$this->sofort->setTimeoutUrl(HelperFunctions::convertEncoding($cancelUrl,3));
		$this->sofort->setNotificationUrl(HelperFunctions::convertEncoding($notificationUrl,3));
		$this->sofort->addUserVariable(HelperFunctions::convertEncoding($userVariable_0,3));
		$this->sofort->addUserVariable(HelperFunctions::convertEncoding($userVariable_1,3));
		$this->sofort->addUserVariable(HelperFunctions::convertEncoding($userVariable_2,3));
		$this->sofort->addUserVariable(HelperFunctions::convertEncoding($userVariable_3,3));
		$this->sofort->setEmailCustomer(HelperFunctions::convertEncoding($order->customer['email_address'],3));
		$this->sofort->setPhoneNumberCustomer(HelperFunctions::convertEncoding($order->customer['telephone'],3));

		//set special settings for every payment-method
		switch($this->paymentMethod) {
			case 'SU' :
				$this->sofort->setSofortueberweisung($amount);

				// see if customer protection is enabled, set it as parameter to sofortlib
				$this->sofort->setSofortueberweisungCustomerprotection(MODULE_PAYMENT_SOFORT_SU_KS_STATUS == 'True');
				break;
			case 'SL' :
				$this->sofort->setSofortlastschrift();
				$this->sofort->setSenderAccount(
					'',
					'',
					HelperFunctions::convertEncoding($order->billing['firstname'],3) . ' ' . HelperFunctions::convertEncoding($order->billing['lastname'],3)
				);
				break;
			case 'LS' :
				$this->sofort->setLastschrift();
				$this->sofort->setSenderAccount(
					HelperFunctions::convertEncoding($_SESSION['sofort']['ls_bank_code'],3),
					HelperFunctions::convertEncoding($_SESSION['sofort']['ls_account_number'],3),
					HelperFunctions::convertEncoding($_SESSION['sofort']['ls_sender_holder'],3)
				);

				$billingSalutation = $this->_getGenderFromAddressBook(
					$order->billing['firstname'],
					$order->billing['lastname'],
					$order->billing['company'],
					$order->billing['street_address'],
					$order->billing['postcode'],
					$order->billing['city'],
					$order->billing['country_id'],
					$order->billing['zone_id']
				);

				//split street and number
				if (!preg_match('#(.+)[ .](.+)#i', trim($order->billing['street_address']), $streetparts)) {
					$streetparts = array();
					$streetparts[1] = trim($order->billing['street_address']);
					$streetparts[2] = '';
				}

				//currently $order->billing['suburb'] (german: "Adresszusatz") is not supported by sofortLib

				$this->sofort->setLastschriftAddress(
					HelperFunctions::convertEncoding($order->billing['firstname'],3),
					HelperFunctions::convertEncoding($order->billing['lastname'],3),
					HelperFunctions::convertEncoding($streetparts[1],3),
					HelperFunctions::convertEncoding($streetparts[2],3),
					HelperFunctions::convertEncoding($order->billing['postcode'],3),
					HelperFunctions::convertEncoding($order->billing['city'],3),
					HelperFunctions::convertEncoding($billingSalutation,3),
					HelperFunctions::convertEncoding($order->billing['country']['iso_code_2'],3)
				);
				break;
			case 'SV' :
				$this->sofort->setSofortvorkasse();

				// if this is called a 'test transaction', add a sender account
				if (getenv('test_sv') == true) $this->sofort->setSenderAccount('00000', '12345', 'Tester Testaccount');

				//notice: customer-protection with SV is currently not supported and not shown to seller for activation
				//@see function keys()
				$this->sofort->setSofortvorkasseCustomerprotection(MODULE_PAYMENT_SOFORT_SV_KS_STATUS == 'True');
				break;
		}

		//send all data against the SOFORT-API
		$this->sofort->sendRequest();

		if ($this->sofort->isError()) {
			$this->sofort->logWarning("API-Call returned false. Redirect to cancel-URL. API-Errors: ".print_r($this->sofort->getErrors(), true)." Time: ".date("d.m.Y, G:i:s"));
			xtc_redirect(HelperFunctions::getCancelUrl($this->code, $this->sofort->getErrors()));
		} else {
			$url = $this->sofort->getPaymentUrl();
			$transactionId = $this->sofort->getTransactionId();

			//seller and customer comment
			$time = date("d.m.Y, G:i:s");

			//set temp-status (only table orders_history, not table orders)
			$tmpStatusId = MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID;

			//dont set a new status? -> then use the last order status
			if (HelperFunctions::statusIsUnchangedStatus($tmpStatusId)) $tmpStatusId = HelperFunctions::getLastOrderStatus($orderId);

			$tmpStatusId = HelperFunctions::checkStatusId($tmpStatusId);

			//comment only for buyer and seller
			$tmpComment = constant('MODULE_PAYMENT_SOFORT_'.$this->paymentMethod.'_TMP_COMMENT').' '.MODULE_PAYMENT_SOFORT_TRANSLATE_TIME.': '.$time;
			HelperFunctions::insertHistoryEntry((int) $orderId, $tmpStatusId, $tmpComment);

			//comment only for seller
			$tmpCommentSeller = constant('MODULE_PAYMENT_SOFORT_'.$this->paymentMethod.'_TMP_COMMENT_SELLER').' '.MODULE_PAYMENT_SOFORT_TRANSLATE_TIME.': '.$time;
			HelperFunctions::insertHistoryEntry((int) $orderId, -1, $tmpCommentSeller, 0);

			xtc_db_query("UPDATE ".HelperFunctions::escapeSql(TABLE_ORDERS)." SET orders_ident_key='".HelperFunctions::escapeSql($transactionId)."' WHERE orders_id='".HelperFunctions::escapeSql($orderId)."'");

			//save all important data in our sofort-tables
			$sofortOrderId = HelperFunctions::insertSofortOrder($orderId, $paymentSecret, $transactionId, $this->paymentMethod);

			if (!$sofortOrderId) $this->sofort->logWarning("Warning: Saving of orderdetails in table sofort_orders failed. Function insertSofortOrder() returned false.Given params: OrderId: $orderId, PaymentSecret: $paymentSecret, TransId: $transactionId, PaymentMethod:".$this->paymentMethod);

			$_SESSION['sofort']['sofort_payment_url'] = $url;
			$_SESSION['sofort']['sofort_payment_method'] = $this->code;
			//following file will always redirect to SOFORT-Wizard
			xtc_redirect(xtc_href_link('callback/sofort/ressources/scripts/processSofortPayment.php', '', 'SSL', true, false));
		}
	}


	/**
	 * called by core after successful payment
	 * overwritten by SV
	 */
	function after_process() {
		global $insert_id;

		//unset all sofort-session-vars for all payment-methods
		if (isset($_SESSION['sofort']['sofort_conditions_sr']))			 unset($_SESSION['sofort']['sofort_conditions_sr']);
		if (isset($_SESSION['sofort']['sofort_conditions_sv']))			 unset($_SESSION['sofort']['sofort_conditions_sv']);
		if (isset($_SESSION['sofort']['sofort_conditions_ls']))			 unset($_SESSION['sofort']['sofort_conditions_ls']);
		if (isset($_SESSION['sofort']['ls_sender_holder']))				 unset($_SESSION['sofort']['ls_sender_holder']);
		if (isset($_SESSION['sofort']['ls_account_number']))			 unset($_SESSION['sofort']['ls_account_number']);
		if (isset($_SESSION['sofort']['ls_bank_code']))					 unset($_SESSION['sofort']['ls_bank_code']);
		if (isset($_SESSION['sofort']['cart_pn_sofortueberweisung_id'])) unset($_SESSION['sofort']['cart_pn_sofortueberweisung_id']);

		//we dont want to wait until first(!) notification comes: we will set the first status and comments like
		//the callback would do
		//but if the buyer will close the wizard after successful payment, this code here will never be
		//called: in this case, the callback will set the correct status
		//Notice: sometimes the notification is faster than the call of the success-URL!
		$this->_insertPaidStatus();

		return true;
	}


	/**
	 * check if there is an error with this paymentMethod - called in checkout_payment.php
	 * @return array with title and description of the found error(s)
	 */
	function get_error () {
		$this->_checkCancelOrder();

		if (!isset($_GET['payment_error']) || $_GET['payment_error'] != $this->code) return false;

		//there is an error with this paymentMethod...
		$this->enabled = false;
		$errormsgArray = array();

		if (isset($_GET['error_codes'])) {
			$langConstantExist = true;
			$errorCodes = array_unique (explode(',', $_GET['error_codes']));

			foreach ($errorCodes as $errorCode) {
				$errorCode = trim($errorCode);
				$code = substr($errorCode, 0, strpos($errorCode, '.'));

				if ($code === false || empty($code)) $code = $errorCode;

				if (defined ('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_' . strtoupper($errorCode) ) ) {
					$errormsgArray[] = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_'.strtoupper($errorCode) ).' ('.$code.')';
				} else if (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_' . $code) ) {
					$errormsgArray[] = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_'.strtoupper($code)).' ('.$code.')';
				} else {
					$langConstantExist = false;
				}

				//show this paymentMethod again to buyer with the following error-codes
				$dontDisableCodes = array('10000', '10001', '10002');

				if(in_array($code, $dontDisableCodes)) $this->enabled = true;
			}

			if (!$errormsgArray && $langConstantExist == false) $errormsgArray[] = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_'.$this->paymentMethod);
		} else {
			$errormsgArray[] = constant('MODULE_PAYMENT_SOFORT_'.$this->paymentMethod.'_TEXT_ERROR_MESSAGE');
		}

		if (!$errormsgArray) $errormsgArray[] = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_'.$this->paymentMethod);

		$errormsgArray = array_unique($errormsgArray);
		$title = MODULE_PAYMENT_SOFORT_MULTIPAY_TEXT_ERROR_HEADING;

		return array(
				'title' => $title,
				'error' => implode(', ', $errormsgArray)
		);
	}


	/**
	 * Module is installed?
	 */
	function check() {
		if (!isset($this->_check)) {
			$constantName = 'MODULE_PAYMENT_SOFORT_'.$this->paymentMethod.'_STATUS';
			$check_query = xtc_db_query("SELECT configuration_value FROM " . HelperFunctions::escapeSql(TABLE_CONFIGURATION) . " WHERE configuration_key = '".HelperFunctions::escapeSql($constantName)."'");
			$this->_check = xtc_db_num_rows($check_query);
		}

		if ($this->_check) {
			$this->_installSofortOrdersTable();
			$this->_installSofortOrdersNotificationTable();
			$this->_installSofortProductsTable();
		}

		return $this->_check;
	}


	/**
	 * install shared keys, that are used by all/most multipay-modules
	 * called by module with parent::install();
	 */
	function install() {
		//following defined constants are global(!) - that means, they are set only once and are used by most/all payment-methods
		if(!defined('MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY')) {
			$sofortStatuses = $this->_insertAndReturnSofortStatus();
			$tempStatus = (isset($sofortStatuses['temp'])&& !empty($sofortStatuses['temp']))? $sofortStatuses['temp'] : '';
			$abortedStatus = (isset($sofortStatuses['aborted'])&& !empty($sofortStatuses['aborted']))? $sofortStatuses['aborted'] : '';
			$unchangedStatus = (isset($sofortStatuses['unchanged'])&& !empty($sofortStatuses['unchanged']))? $sofortStatuses['unchanged'] : '';
			//$checkStatus = (isset($sofortStatuses['check'])&& !empty($sofortStatuses['check']))? $sofortStatuses['check'] : '';

			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY', '',  '6', '2', now())");
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH', '".MODULE_PAYMENT_SOFORT_KEYTEST_DEFAULT."',  '6', '3', 'xtc_cfg_select_option(array(),', now())");  //hide the input-field with an empty <select>
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1', '-TRANSACTION-',  '6', '8', 'xtc_cfg_select_option(array(\'Kd-Nr. {{customer_id}}\',\'-TRANSACTION-\'), ', now())");
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2', '" . HelperFunctions::escapeSql(STORE_NAME) . "', '6', '9', now())");
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE', 'Infographic',  '6', '7', 'xtc_cfg_select_option(array(\'Infographic\',\'Logo & Text\'), ', now())");
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS', '',  '6', '20', 'xtc_cfg_select_option(array(),', now())");  //hide the input-field with an empty <select>
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID', '".HelperFunctions::escapeSql($tempStatus)."',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID', '".HelperFunctions::escapeSql($abortedStatus)."',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED', 'False', '6', '28', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");

			//only used while setting status - unchangeable and not shown in module-configuration
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_UNCHANGED_STATUS_ID', '".HelperFunctions::escapeSql($unchangedStatus)."',  '6', '35', now())");

			$moduleVersion = HelperFunctions::getSofortmodulVersion();
			xtc_db_query("INSERT INTO ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added)
				VALUES ('MODULE_PAYMENT_SOFORT_MULTIPAY_MODULE_VERSION', '".HelperFunctions::escapeSql($moduleVersion)."',  '6', '2', now())");

			$this->_installSofortOrdersTable();
			$this->_installSofortOrdersNotificationTable();
			$this->_installSofortProductsTable();
		}

		return true;
	}


	/**
	 * if this is the last removing of a multipay-paymentmethod --> we also remove all shared keys, that are used by all/most multipay-modules
	 * called by module with parent::remove()
	 */
	function remove() {
		$check_query = xtc_db_query("SELECT * FROM ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." WHERE configuration_key like 'MODULE_PAYMENT_SOFORT_%_STATUS' AND configuration_key NOT LIKE '%_IDEAL_%'");

		if (xtc_db_num_rows ($check_query) === 0 ) {
			xtc_db_query("DELETE FROM ".HelperFunctions::escapeSql(TABLE_CONFIGURATION)." WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_%' AND configuration_key NOT LIKE '%_IDEAL_%'");

			//We don't want to delete data, but we could in some cases...
			//Notice: callback.php may throw warnings/errors, if they doesnt exist (in case of later notification)
			//$this->_removeSofortOrdersTable();
			//$this->_removeSofortOrdersNotificationTable();
			//$this->_removeSofortProductsTable();
		}

		return true;
	}


	function keys() {
		//unset, important for admin
		if (isset($_SESSION['sofort']['apiKeyIsValid'])) unset($_SESSION['sofort']['apiKeyIsValid']);
	}


	function _getGenderFromAddressBook($firstname, $lastname, $company, $streetAddress, $postcode, $city, $countryId, $zoneId) {
		$query = 'SELECT entry_gender
				  FROM	 '.HelperFunctions::escapeSql(TABLE_ADDRESS_BOOK).'
				  WHERE	 entry_firstname = "'.HelperFunctions::escapeSql($firstname).'"
				  AND	 entry_lastname = "'.HelperFunctions::escapeSql($lastname).'"
				  AND	 entry_company = "'.HelperFunctions::escapeSql($company).'"
				  AND	 entry_street_address = "'.HelperFunctions::escapeSql($streetAddress).'"
				  AND	 entry_postcode = "'.HelperFunctions::escapeSql($postcode).'"
				  AND	 entry_city = "'.HelperFunctions::escapeSql($city).'"
				  AND	 entry_country_id = "'.HelperFunctions::escapeSql($countryId).'"
				  AND	 entry_zone_id = "'.HelperFunctions::escapeSql($zoneId).'" LIMIT 1';

		$sqlResult = xtc_db_query($query);
		$result = xtc_db_fetch_array($sqlResult);

		switch ($result['entry_gender']) {
			case 'm': $salutation = 2;  break;
			case 'f': $salutation = 3;  break;
			default:  $salutation = ''; break;
		}

		return $salutation;
	}


	/**
	 * check if order has temp-Status and buyer didnt finish the payment-process by "normal" way
	 * @return bool true (always)
	 */
	function _checkCancelOrder() {
		if (!empty($_SESSION['sofort']['cart_pn_sofortueberweisung_id'])) {
			$orderId = substr($_SESSION['sofort']['cart_pn_sofortueberweisung_id'], strpos($_SESSION['sofort']['cart_pn_sofortueberweisung_id'], '-') + 1);
			$cartID = substr($_SESSION['sofort']['cart_pn_sofortueberweisung_id'], 0, strlen($_SESSION['cart']->cartID));
			$checkQuery = xtc_db_query("SELECT orders_status FROM ".HelperFunctions::escapeSql(TABLE_ORDERS)." WHERE orders_id = '".(int) $orderId."' AND payment_method LIKE 'sofort_%'");
			$result = xtc_db_fetch_array($checkQuery);
			$tempStatus = MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID;

			if (HelperFunctions::statusIsUnchangedStatus($tempStatus)) {
				if (is_object($this->sofort)) {
					$this->sofort->logWarning("Warning: 'Temporary'-Status is set to 'Unchanged'-status. Don't use this status here and change in modulsettings! Check for 'abort'-status for this order could not be processed. Check order! Order-ID: $orderId");
				} elseif (is_object($this->invoice)) {
					$this->invoice->logWarning("Warning: 'Temporary'-Status is set to 'Unchanged'-status. Don't use this status here and change in modulsettings! Check for 'abort'-status for this order could not be processed. Check order! Order-ID: $orderId");
				}

				return true;
			}

			if ((($result['orders_status'] == $tempStatus) || $_SESSION['cart']->cartID != $cartID)) {
				unset($_SESSION['sofort']['cart_pn_sofortueberweisung_id']);

				if (isset($_SESSION['tmp_oID'])) unset($_SESSION['tmp_oID']);

				$this->_cancelOrder( (int) $orderId, 'on');
			}
		}

		return true;
	}


	/**
	 * Check, if needed sofort-lang-constants exists.
	 * If not, include the english-lang-file(s).
	 * @return always true
	 */
	function _checkExistingSofortConstants($paymentMethod) {
		$paymentMethod = strtoupper($paymentMethod);
		$allowedPaymentMethods = array('SU', 'SL', 'LS', 'SR', 'SV');
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

		if(!in_array($paymentMethod, $allowedPaymentMethods)) return true;

		//security check - constant exists if lang-file exists
		if(defined('MODULE_PAYMENT_SOFORT_'.$paymentMethod.'_TEXT_TITLE')) return true;

		$installedModulLangs = array();
		$lngdir = DIR_FS_CATALOG.'lang/';

		foreach (new DirectoryIterator($lngdir) as $file) {
			if ($file->isDir() && file_exists($lngdir.$file->getFilename().'/modules/payment/sofort_general.php')) {
				$installedModulLangs[] = $file->getFilename();
			}
		}

		//currently installed in this module
		if(!in_array($_SESSION['language'], $installedModulLangs)) {
			switch ($paymentMethod) {
				case 'SU': $coo_lang_file_master->init_from_lang_file('lang/english/modules/payment/sofort_sofortueberweisung.php'); break;
				case 'SV': $coo_lang_file_master->init_from_lang_file('lang/english/modules/payment/sofort_sofortvorkasse.php'); break;
				case 'SL': $coo_lang_file_master->init_from_lang_file('lang/english/modules/payment/sofort_sofortlastschrift.php'); break;
				case 'LS': $coo_lang_file_master->init_from_lang_file('lang/english/modules/payment/sofort_lastschrift.php'); break;
				case 'SR': $coo_lang_file_master->init_from_lang_file('lang/english/modules/payment/sofort_sofortrechnung.php'); break;
				default:
			}
		}

		return true;
	}


	/**
	 * Insert all SOFORT-Status into table orders_status and return them
	 * @return array with all status
	 */
	function _insertAndReturnSofortStatus() {
		require_once('sofortInstall.php');

		//SOFORT-langs in this module
		$sofortLangs = array('GERMAN', 'ENGLISH', 'POLISH', 'DUTCH', 'FRENCH', 'ITALIAN');

		//current installed langs
		$installedLangs = array();
		$orderQuery = xtc_db_query("SELECT languages_id, directory FROM " . HelperFunctions::escapeSql(TABLE_LANGUAGES));

		while ($result = xtc_db_fetch_array($orderQuery)) $installedLangs[$result['languages_id']] = strtoupper($result['directory']);

		//get the current highest orders_status_id
		$orderQuery = xtc_db_query("SELECT MAX(orders_status_id) AS max_orders_status_id FROM orders_status");
		$maxOrdersStatusIdTemp = xtc_db_fetch_array($orderQuery);
		$ordersStatusIdTemp = $maxOrdersStatusIdTemp['max_orders_status_id'] + 1;
		$ordersStatusIdConfirmed = $ordersStatusIdTemp + 1;
		$ordersStatusIdCanceled = $ordersStatusIdConfirmed + 1;
		$ordersStatusIdCheck = $ordersStatusIdCanceled + 1;
		$ordersStatusIdUnconfirmed = $ordersStatusIdCheck + 1;
		$ordersStatusIdInvoiceConfirmed = $ordersStatusIdUnconfirmed + 1;
		$ordersStatusIdSvUnpaid = $ordersStatusIdInvoiceConfirmed + 1;
		$ordersStatusIdAborted = $ordersStatusIdSvUnpaid + 1;
		$ordersStatusIdReturnedDebit = $ordersStatusIdAborted + 1;
		$ordersStatusIdRefunded = $ordersStatusIdReturnedDebit + 1;
		$ordersStatusIdDirectDebit = $ordersStatusIdRefunded + 1;
		$ordersStatusIdSvPaid = $ordersStatusIdDirectDebit + 1;
		$ordersStatusIdTranslateConfirmed = $ordersStatusIdSvPaid + 1;
		$ordersStatusIdUnchanged = $ordersStatusIdTranslateConfirmed + 1;
		$sofortStatuses = array();

		foreach($installedLangs as $installedLang) {
			//insert english for shop-languages, which are not included in this module
			if(in_array($installedLang, $sofortLangs)) {
				$sofortLang = $installedLang;
				$langId = array_search($sofortLang, $installedLangs);
			} else {
				$sofortLang = 'ENGLISH';
				$langId = array_search($installedLang, $installedLangs);
			}

			//insert temp-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('temp', $sofortLang);
			$sofortStatuses['temp'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['temp'] === false || $sofortStatuses['temp'] == '') $sofortStatuses['temp'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdTemp, $langId);

			//insert sv-unpaid-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('sv_unpaid', $sofortLang);
			$sofortStatuses['sv_unpaid'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['sv_unpaid'] === false || $sofortStatuses['sv_unpaid'] == '') $sofortStatuses['sv_unpaid'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdSvUnpaid, $langId);

			//insert sv-paid-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('sv_paid', $sofortLang);
			$sofortStatuses['sv_paid'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['sv_paid'] === false || $sofortStatuses['sv_paid'] == '') $sofortStatuses['sv_paid'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdSvPaid, $langId);

			//insert confirmed-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('confirmed', $sofortLang);
			$sofortStatuses['confirmed'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['confirmed'] === false || $sofortStatuses['confirmed'] == '') $sofortStatuses['confirmed'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdConfirmed, $langId);

			//insert translate_confirmed-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('translate_confirmed', $sofortLang);
			$sofortStatuses['translate_confirmed'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['translate_confirmed'] === false || $sofortStatuses['translate_confirmed'] == '') $sofortStatuses['translate_confirmed'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdTranslateConfirmed, $langId);

			//insert canceled-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('canceled', $sofortLang);
			$sofortStatuses['canceled'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['canceled'] === false || $sofortStatuses['canceled'] == '') $sofortStatuses['canceled'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdCanceled, $langId);

			//insert aborted-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('aborted', $sofortLang);
			$sofortStatuses['aborted'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['aborted'] === false || $sofortStatuses['aborted'] == '') $sofortStatuses['aborted'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdAborted, $langId);

			//insert check-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('check', $sofortLang);
			$sofortStatuses['check'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['check'] === false || $sofortStatuses['check'] == '') $sofortStatuses['check'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdCheck, $langId);

			//insert unconfirmed-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('unconfirmed', $sofortLang);
			$sofortStatuses['unconfirmed'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['unconfirmed'] === false || $sofortStatuses['unconfirmed'] == '') $sofortStatuses['unconfirmed'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdUnconfirmed, $langId);

			//insert invoice-confirmed-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('invoice_confirmed', $sofortLang);
			$sofortStatuses['invoice_confirmed'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['invoice_confirmed'] === false || $sofortStatuses['invoice_confirmed'] == '') $sofortStatuses['invoice_confirmed'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdInvoiceConfirmed, $langId);

			//insert returned-debit-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('returned_debit', $sofortLang);
			$sofortStatuses['returned_debit'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['returned_debit'] === false || $sofortStatuses['returned_debit'] == '') $sofortStatuses['returned_debit'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdReturnedDebit, $langId);

			//insert refunded-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('refunded', $sofortLang);
			$sofortStatuses['refunded'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['refunded'] === false || $sofortStatuses['refunded'] == '') $sofortStatuses['refunded'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdRefunded, $langId);

			//insert direct-debit-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('direct_debit', $sofortLang);
			$sofortStatuses['direct_debit'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['direct_debit'] === false || $sofortStatuses['direct_debit'] == '') $sofortStatuses['direct_debit'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdDirectDebit, $langId);

			//insert unchanged-Status if not exists
			$newOrdersStatusName = $this->_getNewOrdersStatusName('unchanged', $sofortLang);
			$sofortStatuses['unchanged'] = $this->_getStatusIdIfExistInDb($newOrdersStatusName, $langId);

			if($sofortStatuses['unchanged'] === false || $sofortStatuses['unchanged'] == '') $sofortStatuses['unchanged'] = $this->_insertStatusInDb($newOrdersStatusName, $ordersStatusIdUnchanged, $langId);
		}

		return  $sofortStatuses;
	}


	function _getStatusIdIfExistInDb($newOrdersStatusName, $langId) {
		if(!$newOrdersStatusName) return false;

		$checkQuery = xtc_db_query('SELECT orders_status_id
									FROM   orders_status
									WHERE  language_id = "'.HelperFunctions::escapeSql($langId).'"
									AND	   orders_status_name = "'.HelperFunctions::escapeSql($newOrdersStatusName).'"
									LIMIT  1');

		if (xtc_db_num_rows($checkQuery) < 1) {
			return false;
		} else {
			$neededOrdersStatusId = xtc_db_fetch_array($checkQuery);
			return $neededOrdersStatusId['orders_status_id'];
		}
	}


	/**
	 * insert given statusstring into DB - empty strings will not be inserted
	 * @return $orders_status_id from DB OR false
	 */
	function _insertStatusInDb($newOrdersStatusName, $ordersStatusId, $langId) {
		if (!$newOrdersStatusName) return false;

		xtc_db_query("INSERT INTO orders_status (orders_status_id, language_id, orders_status_name)
			values ('".HelperFunctions::escapeSql($ordersStatusId)."', '".HelperFunctions::escapeSql($langId)."', '".HelperFunctions::escapeSql($newOrdersStatusName)."')");
		return $ordersStatusId;
	}


	/**
	 * returns the statusname for the given $status and $lang
	 * return max the first 32 chars! (because db-field = varchar(32) )
	 */
	function _getNewOrdersStatusName($status, $lang) {
		switch ($status) {
			case 'temp': 				$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_TEMP_'.$lang); break;
			case 'sv_unpaid':			$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_SV_UNPAID_'.$lang); break;
			case 'sv_paid':				$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_SV_PAID_'.$lang); break;
			case 'confirmed':			$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_CONFIRMED_'.$lang); break;
			case 'translate_confirmed': $newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_CONFIRMED_2_'.$lang); break;
			case 'invoice_confirmed':	$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_INVOICE_CONFIRMED_'.$lang); break;
			case 'unconfirmed':			$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_UNCONFIRMED_'.$lang); break;
			case 'canceled':			$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_CANCELED_'.$lang); break;
			case 'aborted':				$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_ABORTED_'.$lang); break;
			case 'check':				$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_CHECK_'.$lang); break;
			case 'returned_debit':		$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_RETURNED_DEBIT_'.$lang); break;
			case 'refunded':			$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_REFUNDED_'.$lang); break;
			case 'direct_debit':		$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_DIRECT_DEBIT_'.$lang); break;
			case 'unchanged':			$newOrdersStatusName = constant('MODULE_PAYMENT_SOFORT_MULTIPAY_STATE_UNCHANGED_'.$lang); break;
		}

		// if string is not cut to 32 chars, status will be reinserted with every installation
		$newOrdersStatusName = substr($newOrdersStatusName, 0, 32);

		return $newOrdersStatusName;
	}


	function _installSofortOrdersTable() {
		$sql = '
			CREATE TABLE IF NOT EXISTS `sofort_orders` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`orders_id` int(11) unsigned NOT NULL,
				`transaction_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				`payment_method` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				`payment_secret` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				`date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			)
		';
		xtc_db_query($sql);
	}


	function _removeSofortOrdersTable() {
		$sql = 'DROP TABLE `sofort_orders`';
		xtc_db_query($sql);
	}


	function _installSofortOrdersNotificationTable() {
		$sql = '
			CREATE TABLE IF NOT EXISTS `sofort_orders_notification` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`sofort_orders_id` int(11) unsigned NOT NULL,
				`items` text COLLATE utf8_unicode_ci NOT NULL,
				`amount` float NOT NULL,
				`customer_comment` text COLLATE utf8_unicode_ci NOT NULL,
				`seller_comment` text COLLATE utf8_unicode_ci NOT NULL,
				`status_id` int(11) unsigned NOT NULL,
				`status` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				`status_reason` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				`invoice_status` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				`invoice_objection` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
				`date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`)
			)
		';
		xtc_db_query($sql);
	}


	function _removeSofortOrdersNotificationTable() {
		$sql = 'DROP TABLE `sofort_orders_notification`';
		xtc_db_query($sql);
	}


	function _installSofortProductsTable() {
		$sql = '
			CREATE TABLE IF NOT EXISTS `sofort_products` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`orders_id` int(11) unsigned NOT NULL,
				`orders_products_id` int(11) unsigned NOT NULL,
				`item_id` text COLLATE utf8_unicode_ci NOT NULL,
				PRIMARY KEY (`id`)
			)
		';
		xtc_db_query($sql);
	}


	function _removeSofortProductsTable() {
		$sql = 'DROP TABLE `sofort_products`';
		xtc_db_query($sql);
	}


	/**
	 * get shop order total
	 * @return float $shopEndprice
	 */
	function _getShopTotal($orderTotals, $order) {
		//Frequent sources of errors: shipping-tax, external modules, sort order of shown 'ot_'-modules
		$ot_totalTotal = 0;

		if (MODULE_ORDER_TOTAL_INSTALLED)
			foreach ($orderTotals as $oneTotal)
				if ($oneTotal['code'] == 'ot_total') $ot_totalTotal = $oneTotal['value'];

		$orderObjectTotal = 0;

		$orderObjectTotal = ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) ? $order->info['total'] + $order->info['tax'] : $order->info['total'];

		//use the higher one
		$shopTotal = 0;

		if ($ot_totalTotal >= $orderObjectTotal) {
			$shopTotal = $ot_totalTotal;
		} else if ($orderObjectTotal > $ot_totalTotal) {
			$shopTotal = $orderObjectTotal;
		}

		$shopTotal = number_format($shopTotal, 2, '.','');

		return $shopTotal;
	}


	/**
	 * replace special chars in $reason e.g. �=>ae, �=>ss incl. foreign non-ascii-signs etc.
	 * @param string $reason like 'K�ufer A. M�ller'
	 * @return string - converted String
	 */
	function _convertReason($reason) {
		$oldLocale = setlocale(LC_ALL, 0);
		setlocale(LC_ALL, "de_DE.utf8");
		$shopEncoding = HelperFunctions::getIniValue('shopEncoding');
		//$encoding = mb_detect_encoding('aaa'.$reason, "ISO-8859-1, UTF-8");
		$convertedReason = substr(iconv($shopEncoding, "ASCII//TRANSLIT", 'aaa'.$reason),3);
		setlocale(LC_ALL, $oldLocale);

		return $convertedReason;
	}


	/**
	 * get the reasons for the given payment method
	 * @return array with reason1 and reason2
	 */
	function _getReasons($paymentMethod, $customerId, $order, $orderId) {
		if ($paymentMethod == 'SV') {  //SV has only one reason
			$reason_1 = str_replace('{{order_id}}', $orderId, MODULE_PAYMENT_SOFORT_SV_REASON_2);
			$reason_1 = str_replace('{{customer_id}}', $customerId, $reason_1);
			$reason_1 = str_replace('{{transaction_id}}', '-TRANSACTION-', $reason_1);
			$reason_1 = $this->_convertReason($reason_1);
			$reason_1 = substr($reason_1, 0, 27);
			$reason_2 = ''; //SV has only one reason, the 2nd is set by SOFORT
		} else {
			$reason_1 = str_replace('{{order_id}}', $orderId, MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1);
			$reason_1 = str_replace('{{customer_id}}', $customerId, $reason_1);
			$reason_1 = str_replace('{{transaction_id}}', '-TRANSACTION-', $reason_1);
			$reason_1 = $this->_convertReason($reason_1);
			$reason_1 = substr($reason_1, 0, 27);
			$reason_2 = str_replace('{{order_id}}', $orderId, MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2);
			$reason_2 = str_replace('{{customer_id}}', $customerId, $reason_2);
			$reason_2 = str_replace('{{order_date}}', strftime(DATE_FORMAT_SHORT), $reason_2);
			$reason_2 = str_replace('{{customer_name}}', $order->customer['firstname'] . ' ' . $order->customer['lastname'], $reason_2);
			$reason_2 = str_replace('{{customer_company}}', $order->customer['company'], $reason_2);
			$reason_2 = str_replace('{{customer_email}}', $order->customer['email_address'], $reason_2);
			$reason_2 = str_replace('{{transaction_id}}', '-TRANSACTION-', $reason_2);
			$reason_2 = $this->_convertReason($reason_2);
			$reason_2 = substr($reason_2, 0, 27);
		}

		$reasons = array();
		$reasons[0] = $reason_1;
		$reasons[1] = $reason_2;

		return $reasons;
	}


	/**
	 * delete all data of this buying-proccess - currently only needed for SV;
	 * this function must be compatible with xtc3, modified-shop, comSeo and gam!
	 * @see /path/to/root/checkout_process.php -> code after: "$payment_module->after_process();"
	 * @return bool true (always)
	 */
	function _resetCartAndDeleteSessionData() {
		global $order_total_modules, $insert_id;

		$_SESSION['cart']->reset(true);

		if (isset($_SESSION['sendto'])) 		unset ($_SESSION['sendto']);
		if (isset($_SESSION['billto'])) 		unset ($_SESSION['billto']);
		if (isset($_SESSION['shipping'])) 		unset ($_SESSION['shipping']);
		if (isset($_SESSION['payment'])) 		unset ($_SESSION['payment']);
		if (isset($_SESSION['comments'])) 		unset ($_SESSION['comments']);
		if (isset($_SESSION['last_order']))		unset ($_SESSION['last_order']);
		if (isset($_SESSION['tmp_oID'])) 		unset ($_SESSION['tmp_oID']);
		if (isset($_SESSION['cc'])) 			unset ($_SESSION['cc']);
		if (isset($_SESSION['cc_id'])) 			unset ($_SESSION['cc_id']);
		if (isset ($_SESSION['credit_covers']))	unset ($_SESSION['credit_covers']);

		$last_order = $insert_id; //maybe needed in file-includes or function-calls below

		//GV Code Start
		if (isset ($_SESSION['credit_covers']))
			unset ($_SESSION['credit_covers']);
		if (is_object($order_total_modules)) {
			$order_total_modules->clear_posts(); //ICW ADDED FOR CREDIT CLASS SYSTEM
		}
		// GV Code End

		//modified eCommerce Shopsoftware + Gambio Start
		if(@isset($_SESSION['xtb0'])) {
			define('XTB_CHECKOUT_PROCESS', __LINE__);
			require_once (DIR_FS_CATALOG.'callback/xtbooster/xtbcallback.php');
		}
		//modified eCommerce Shopsoftware End

		//Gambio Start
		// BOF GM_MOD GX-Customizer:
		if (file_exists(DIR_FS_CATALOG.'gm/modules/gm_gprint_checkout_process.php')) {
			require(DIR_FS_CATALOG . 'gm/modules/gm_gprint_checkout_process.php');
		}
		//Gambio End

		return true;
	}


	/**
	 * @see xtc_remove_order() in admin/includes/functions/general.php
	 * @param bool $restock - only allowed is: FALSE or 'on'
	 */
	function _cancelOrder($orderId, $restock = false) {
		//only Gambio
		if (HelperFunctions::isGambio()) {
			$this->_gambio_remove_order($orderId, $restock, true, $restock);
		//only xtc3, comSeo
		} else {
			if ($restock == 'on') {
				//following code is (nearly 100%) copy&paste from function xtc_remove_order() - there is no other way for stock-update :-|
				//compatible and checked with xtc3_sp2 and comseo_2.0 and comseo_2.1
				$order_query = xtc_db_query("
					SELECT orders_products_id, products_id, products_quantity
					FROM ".TABLE_ORDERS_PRODUCTS."
					WHERE orders_id = '".xtc_db_input($orderId)."'");

				while ($order = xtc_db_fetch_array($order_query)) {
					xtc_db_query("
						UPDATE ".TABLE_PRODUCTS."
						SET products_quantity = products_quantity + ".(int)$order['products_quantity'].", products_ordered = products_ordered - ".(int)$order['products_quantity']."
						WHERE products_id = '".(int)$order['products_id']."'");

					//only comSeo, not xtc3
					if (function_exists('nc_get_products_attributes_id')) {
						$result = mysqli_query($GLOBALS["___mysqli_ston"], '
							SELECT *
							FROM orders_products_attributes
							WHERE orders_id = "'.(int)$orderId.'"
							AND orders_products_id = "'.HelperFunctions::escapeSql($order['orders_products_id']).'"');

						while(($row = mysqli_fetch_array($result) )) {
							$attributes_id = nc_get_products_attributes_id($order['products_id'], $row['products_options'], $row['products_options_values']);
							mysqli_query($GLOBALS["___mysqli_ston"], '
								UPDATE products_attributes
								SET attributes_stock = attributes_stock + '.(int)$order['products_quantity'].'
								WHERE products_attributes_id = "'.(int)$attributes_id.'"');
							//echo mysql_error(); //buyer ist not allowed to see this
						}
					}
				}
			}
		}

		//update status and customer-history
		$time = date("d.m.Y, G:i:s");
		xtc_db_query('UPDATE orders SET orders_status = "'.HelperFunctions::escapeSql(MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID).'", last_modified = now() WHERE orders_id = '.HelperFunctions::escapeSql($orderId));
		HelperFunctions::insertHistoryEntry((int) $orderId, MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID, MODULE_PAYMENT_SOFORT_MULTIPAY_ORDER_CANCELED, '', $time);
	}


	/**
	 * copy of function xtc_remove_order() in /admin/includes/functions/general.php - Gambioversion: GX2.0.10g - no direct access possible/useful
	 * @param int $orderId
	 * @param $restock
	 * @param bool $canceled - set to FALSE (default) will delete orderinformation completly from the DB!
	 * @param $reshipp
	 * @see xtc_remove_order() in gambio-admin-folder
	 */
	function _gambio_remove_order($order_id, $restock = false, $canceled = false, $reshipp = false) {
		//following code is NOT formatted for better comparison in case of future changes!

		if ($restock == 'on' || $reshipp == 'on')
		{
			// BOF GM_MOD:
			$order_query = xtc_db_query("
										SELECT DISTINCT
											op.orders_products_id,
											op.products_id,
											op.products_quantity,
											opp.products_properties_combis_id,
											o.date_purchased
										FROM ".TABLE_ORDERS_PRODUCTS." op
											LEFT JOIN ".TABLE_ORDERS." o ON op.orders_id = o.orders_id
											LEFT JOIN orders_products_properties opp ON opp.orders_products_id = op.orders_products_id
										WHERE
											op.orders_id = '" . xtc_db_input($order_id) . "'
			");

			while ($order = xtc_db_fetch_array($order_query))
			{
				if($restock == 'on') {
					/* BOF SPECIALS RESTOCK */
					$t_query = xtc_db_query("
											SELECT
												specials_date_added
											AS
												date
											FROM " .
												TABLE_SPECIALS . "
											WHERE
												specials_date_added < '" .	HelperFunctions::escapeSql($order['date_purchased'])	. "'
											AND
												products_id			= '" .	HelperFunctions::escapeSql($order['products_id'])		. "'
					");

					if((int)xtc_db_num_rows($t_query) > 0)
					{
						xtc_db_query("
										UPDATE " .
											TABLE_SPECIALS . "
										SET
											specials_quantity = specials_quantity + " . (int)$order['products_quantity'] . "
										WHERE
											products_id = '" . (int)$order['products_id'] . "'
						");
					}
					/* EOF SPECIALS RESTOCK */

	                // check if combis exists
	                $t_combis_query = xtc_db_query("
									SELECT
	                                    products_properties_combis_id
	                                FROM
										products_properties_combis
									WHERE
										products_id = '" . HelperFunctions::escapeSql($order['products_id']) . "'
					");
	                $t_combis_array_length = xtc_db_num_rows($t_combis_query);

	                if($t_combis_array_length > 0){
	                    $coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
	                    $t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
	                }else{
	                    $t_use_combis_quantity = 0;
	                }

	                if($t_combis_array_length == 0 || ($t_combis_array_length > 0 && $t_use_combis_quantity == 1)){
	                    xtc_db_query("
	                                    UPDATE " .
	                                        TABLE_PRODUCTS . "
	                                    SET
	                                        products_quantity = products_quantity + ".(int)$order['products_quantity']."
	                                    WHERE
	                                        products_id = '".(int)$order['products_id']."'
	                    ");
	                }

	                xtc_db_query("
	                                UPDATE " .
	                                    TABLE_PRODUCTS . "
	                                SET
	                                    products_ordered = products_ordered - ".(int)$order['products_quantity']."
	                                WHERE
	                                    products_id = '".(int)$order['products_id']."'
	                ");

	                if($t_combis_array_length > 0 && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2)){
	                    xtc_db_query("
	                                    UPDATE
	                                        products_properties_combis
	                                    SET
	                                        combi_quantity = combi_quantity + " . $order['products_quantity'] . "
	                                    WHERE
	                                        products_properties_combis_id = '" . $order['products_properties_combis_id'] . "' AND
	                                        products_id = '" . $order['products_id'] . "'
	                    ");
	                }


					// BOF GM_MOD
					if(ATTRIBUTE_STOCK_CHECK == 'true')
					{
						$gm_get_orders_attributes = xtc_db_query("
																SELECT
																	products_options,
																	products_options_values
																FROM
																	orders_products_attributes
																WHERE
																	orders_id = '" . xtc_db_input($order_id) . "'
																AND
																	orders_products_id = '" . HelperFunctions::escapeSql($order['orders_products_id']) . "'
						");

						while($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes))
						{
							$gm_get_attributes_id = xtc_db_query("
																SELECT
																	pa.products_attributes_id
																FROM
																	products_options_values pov,
																	products_options po,
																	products_attributes pa
																WHERE
																	po.products_options_name = '" . HelperFunctions::escapeSql($gm_orders_attributes['products_options']) . "'
																	AND po.products_options_id = pa.options_id
																	AND pov.products_options_values_id = pa.options_values_id
																	AND pov.products_options_values_name = '" . HelperFunctions::escapeSql($gm_orders_attributes['products_options_values']) . "'
																	AND pa.products_id = '" . HelperFunctions::escapeSql($order['products_id']) . "'
																LIMIT 1
							");

							if(xtc_db_num_rows($gm_get_attributes_id) == 1)
							{
								$gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

								xtc_db_query("
												UPDATE
													products_attributes
												SET
													attributes_stock = attributes_stock + ".$order['products_quantity']."
												WHERE
													products_attributes_id = '" . $gm_attributes_id['products_attributes_id'] . "'
								");
							}
						}
					}
					// EOF GM_MOD
				}

				// BOF GM_MOD products_shippingtime:
				if($reshipp == 'on') {
					require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');
					set_shipping_status($order['products_id'], $order['products_properties_combis_id']);
				}
				// BOF GM_MOD products_shippingtime:
			}
		}
	}


	/**
	 * check if order has status "paid" and insert "paid"-status into table_orders and sofort_orders and set comments to history
	 * (sets log-entries in all currious situations)
	 * @return bool TRUE (if errors occured: FALSE)
	 * @todo this function is too long and should be splittet - Notice: this function is the most complicated function of this file!!!
	 */
	function _insertPaidStatus() {
		global $insert_id; //$insert_id equals $orderId!

		//create transactionData-Object
		$SofortLib_TransactionData = new SofortLib_TransactionData(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY);
		if (!is_object($SofortLib_TransactionData) || !($SofortLib_TransactionData instanceof SofortLib_TransactionData)) {
			if (is_object($this->invoice)) {
				$this->invoice->logError("TransactionData-Object corrupt. Maybe status could not be correct set! Order-ID: $insert_id. Check status of order!");
			} else {
				$this->sofort->logError("TransactionData-Object corrupt. Maybe status could not be correct set! Order-ID: $insert_id. Check status of order!");
			}
			return false;
		}

		//get transactionID from table orders
		$transactionId = HelperFunctions::getTransactionIdFromShop($insert_id);

		if (!$transactionId){
			$SofortLib_TransactionData->logError("Transaction-ID not found for order-ID: $insert_id. Maybe status could not be correct set! Check status of order!");
			return false;
		}

		//fetch all transactionData from SOFORT
		$SofortLib_TransactionData->setTransaction($transactionId);
		$SofortLib_TransactionData->sendRequest();

		if ($SofortLib_TransactionData->isError()) {
			$SofortLib_TransactionData->logError("Error in TransactionData: ".print_r($SofortLib_TransactionData->getError(), true)."Transaction-ID: $transactionId.
				Order-ID: $insert_id. Maybe status could not be correct set! Check status of order!");
			return false;
		}

		//check if SOFORT-paymentSecret is equal to shop-paymentSecret
		$paymentSecret = $SofortLib_TransactionData->getUserVariable(3); //paymentSecret was sent to SOFORT in the 4th user variable

		if (!HelperFunctions::checkPaymentSecret($paymentSecret, $transactionId)) {
			$SofortLib_TransactionData->logError("Check of Payment-Secret failed for given Transaction-ID: $transactionId! Checked paymentSecret: $paymentSecret. Order-ID: $insert_id. Check order!");
			return false;
		}

		//check if SOFORT-orderId is equal to shop-orderId
		$orderId = HelperFunctions::getOrderId($transactionId);

		if (!$orderId || $orderId != $insert_id) {
			$SofortLib_TransactionData->logError("Order-ID not found or not equal with shop-order-ID ($insert_id) for Transaction-ID: $transactionId! Check order!");
			return false;
		}

		$time = date('d.m.Y, G:i:s', strtotime($SofortLib_TransactionData->getTime()));
		$statusReason = $SofortLib_TransactionData->getStatusReason();
		$status = $SofortLib_TransactionData->getStatus();
		$paymentMethod = $SofortLib_TransactionData->getPaymentMethod();
		$PnagInvoice = '';

		if ($paymentMethod == 'sr') {
			$PnagInvoice = new PnagInvoice(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY, $transactionId);

			if ($PnagInvoice->isError()) {
				$SofortLib_TransactionData->logError("Errors while creating invoice-object: ".print_r($PnagInvoice->getErrors(), true)." Transaction-ID: $transactionId, Order-ID: $insert_id. Check order!");

				return false;
			}
		}

		$dbEntries = array(
				'amount'		=> $SofortLib_TransactionData->getAmount(),
				'status'		=> $status,
				'status_reason' => $statusReason,
		);

		if ($paymentMethod == 'sr') $dbEntries['status_id'] = $PnagInvoice->getState();

		$statusShort = HelperFunctions::getShortStatus($paymentMethod, $status, $statusReason);
		$newShopStatus = HelperFunctions::getNewShopStatusId($statusShort, $insert_id, $SofortLib_TransactionData);
		$historyComments = HelperFunctions::getHistoryComments($statusShort, $insert_id, $SofortLib_TransactionData, $transactionId, false, $SofortLib_TransactionData);

		//check if notification was faster than call of successURL
		if (HelperFunctions::hasEntriesInTableSofortNotifications($transactionId)) {
			$SofortLib_TransactionData->log("Status of first notification was already set by notification (not by success-URL) for order-ID: $insert_id. There is nothing to do here.");

			return true; //everything was done by notification
		}

		//save all relevant data in table sofort_notification
		//this DB-entry also prevents race-conditions between call of successUrl and notification
		//@see hasEntriesInTableSofortNotifications() and statusAndReasonDidNotChange()
		//must be set (preferably direct) after call of getHistoryComments() (because of a checks there inside)
		HelperFunctions::insertSofortOrdersNotification($transactionId, $dbEntries, $SofortLib_TransactionData);

		//special features for SR
		if ($paymentMethod == 'sr') {
			//if buyer has changed addresses at sofort-wizard -> set new addresses also in shopsystem
			if ($statusShort == 'sr_pen_con_inv') HelperFunctions::updateShopAdresses($SofortLib_TransactionData->getInvoiceAddress(), $SofortLib_TransactionData->getShippingAddress(), $insert_id, $SofortLib_TransactionData);

			//sync SOFORT-articles/orderTotals with the shop-articles/orderTotals
			require_once(DIR_FS_CATALOG.'callback/sofort/ressources/scripts/sofortOrderSynchronisation.php');
			$SofortOrderSynchronisation = new sofortOrderSynchronisation();
			$SofortOrderSynchronisation->editArticlesShop($PnagInvoice, $insert_id);
		}

		//insert all data into orders-table and orders_status_history-table
		if ($historyComments['buyer']) {
			if (!$newShopStatus) {
				$SofortLib_TransactionData->logError("Status for buyer-comment (".$historyComments['buyer'].") unknown. Comment will not be
					inserted. Check order! Order-Id: $insert_id, Transaction-ID: $transactionId!");
				return false;
			}

			HelperFunctions::insertHistoryEntry($insert_id, $newShopStatus, $historyComments['buyer'], '', $time);
		}

		if ($historyComments['seller']) HelperFunctions::insertHistoryEntry($insert_id, -1, $historyComments['seller'], '', $time);

		//if $newShopStatus is set, set it into table orders
		if ($newShopStatus) HelperFunctions::updateTableOrdersStatus($insert_id, $newShopStatus);

		return true;
	}


	/**
	 * checks, if current payment-method is installed (installed != enabled)
	 */
	function _isInstalled() {
		$installedPaymentMethods = explode(';', MODULE_PAYMENT_INSTALLED);

		if (in_array($this->code.'.php', $installedPaymentMethods)) {
			// xtc-Bugfix: if paymentmethod was just deinstalled --> paymentmethod is nevertheless in MODULE_PAYMENT_INSTALLED
			$countResult = xtc_db_query("SELECT count(*) as anzahl FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_".strtoupper($this->code)."_ALLOWED'");
			$count = xtc_db_fetch_array($countResult);
			if ($count['anzahl'] > 0) {
				return true;
			}
		}
		return false;
	}


	/**
	 * checks, if currently installed module-files work together with an older installation of this module
	 * @return bool - TRUE: everything ok - FALSE: module doesnt work with installed version -> deactivate this module until seller reinstalled ALL sofort-gateway-payments
	 */
	function _modulVersionCheck() {
		//all versions without this constant are always to old
		if (!defined('MODULE_PAYMENT_SOFORT_MULTIPAY_MODULE_VERSION')) return false;

		$installedShopVersion = trim(MODULE_PAYMENT_SOFORT_MULTIPAY_MODULE_VERSION);
		//$filesVersion = trim(HelperFunctions::getSofortmodulVersion()); //can be used, if needed

		//all installed versions lower than 5.3.0 are to old and not compatible
		if (version_compare($installedShopVersion, '5.3.0', '<')) return false;

		//currently(!), all higher version are compatible with 5.3.0 an higher
		if (version_compare($installedShopVersion, '5.3.0', '>=')) return true;

		//extend here with later versions of this module...

		//should never be reached
		return false;
	}
}
