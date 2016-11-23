<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2012-09-06 13:49:09 +0200 (Thu, 06 Sep 2012) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: helperFunctions.php 5326 2012-09-06 11:49:09Z boehm $
 *
 * This file includes small helper-functions used throughout the module
*/

class HelperFunctions {


	/**
	 * Fill table sofort_orders with a new order
	 * @param int $ordersId
	 * @param string $paymentSecret
	 * @param string $transactionId
	 * @param string $paymentMethod
	 * @return last insert_id ELSE data could not be saved
	 */
	public static function insertSofortOrder($ordersId, $paymentSecret, $transactionId, $paymentMethod) {
		if(!$transactionId || !$paymentMethod) return false;

		switch($paymentMethod){
			case 'SR': $paymentMethod = 'rechnung_by_sofort'; break;
			case 'SU': $paymentMethod = 'sofortueberweisung'; break;
			case 'SV': $paymentMethod = 'vorkasse_by_sofort'; break;
			case 'SL': $paymentMethod = 'sofortlastschrift'; break;
			case 'LS': $paymentMethod = 'lastschrift_by_sofort'; break;
			default: return false;
		}

		$sqlDataArray = array(
				'orders_id' => $ordersId,
				'transaction_id' => $transactionId,
				'payment_method' => $paymentMethod,
				'payment_secret' => $paymentSecret,
		);
		xtc_db_query(HelperFunctions::getEscapedInsertInto('sofort_orders', $sqlDataArray));

		return xtc_db_insert_id(); // fetch and return the last insert id
	}


	/**
	 * Fill table sofort_orders_notification, e.g. in case of status-changes - each notification = one entry
	 * @param string $transactionID
	 * @param array	 $dbEntries - array with key=dbFieldName and value=dbFieldValue (all dbFieldNames are optional) (id and date_time will be ignored)
	 * @param object $SofortLoggingObj (optional)
	 * @return int - last insert_id
	 */
	public static function insertSofortOrdersNotification($transactionId, $dbEntries, $SofortLoggingObj = '') {
		$sofortOrdersId 	= (isset($dbEntries['sofort_orders_id'])) 	? $dbEntries['sofort_orders_id'] 	: '';
		$items 				= (isset($dbEntries['items'])) 				? $dbEntries['items'] 				: '';
		$amount 			= (isset($dbEntries['amount'])) 			? $dbEntries['amount'] 				: '';
		$customerComment	= (isset($dbEntries['customer_comment'])) 	? $dbEntries['customer_comment'] 	: '';
		$sellerComment 		= (isset($dbEntries['seller_comment'])) 	? $dbEntries['seller_comment'] 		: '';
		$statusId 			= (isset($dbEntries['status_id'])) 			? $dbEntries['status_id'] 			: '';
		$status 			= (isset($dbEntries['status'])) 			? $dbEntries['status'] 				: '';
		$statusReason 		= (isset($dbEntries['status_reason'])) 		? $dbEntries['status_reason'] 		: '';
		$invoiceStatus 		= (isset($dbEntries['invoice_status'])) 	? $dbEntries['invoice_status'] 		: '';
		$invoiceObjection 	= (isset($dbEntries['invoice_objection'])) 	? $dbEntries['invoice_objection'] 	: '';

		if (!$sofortOrdersId) {
			$query = xtc_db_query('SELECT id FROM sofort_orders WHERE transaction_id = "'.HelperFunctions::escapeSql($transactionId).'"');
			$result = xtc_db_fetch_array($query);
			$sofortOrdersId = $result['id'];

			if (!$sofortOrdersId) {
				if (is_object($SofortLoggingObj)) $SofortLoggingObj->logWarning("Notification could not be inserted into table sofort_notifications. Transaction-ID: $transactionId, Data:".print_r($dbEntries, true)." Order-ID: $orderId Check order and order-history.");

				return false;
			}
		}

		$sqlDataArray = array(
			'sofort_orders_id' => $sofortOrdersId,
			'items' => $items,
			'amount' => $amount,
			'customer_comment' => $customerComment,
			'seller_comment' => $sellerComment,
			'status_id' => $statusId,
			'status' => $status,
			'status_reason' => $statusReason,
			'invoice_status' => $invoiceStatus,
			'invoice_objection' => $invoiceObjection,
		);
		xtc_db_query(HelperFunctions::getEscapedInsertInto('sofort_orders_notification', $sqlDataArray));

		return xtc_db_insert_id(); // fetch and return the last insert id
	}


	/**
	 * return the last/penultimate value of the given field
	 * @param int $ordersId
	 * @param string $field
	 * @param bool $returnPenultimateEntry (optional) - Default = false
	 * @return mixed field value
	 */
	public static function getLastFieldValueFromSofortTable($ordersId, $field, $returnPenultimateEntry = false){
		$query = xtc_db_query( 'SELECT id FROM sofort_orders WHERE orders_id = '.HelperFunctions::escapeSql($ordersId));
		$result = xtc_db_fetch_array($query);
		$sofortOrdersId = $result['id'];
		$query = 'SELECT '.HelperFunctions::escapeSql($field).' FROM sofort_orders_notification WHERE sofort_orders_id = "'.HelperFunctions::escapeSql($sofortOrdersId).'" ORDER BY date_time DESC LIMIT 1';

		if ($returnPenultimateEntry) $query .= ',1';

		$result = xtc_db_fetch_array(xtc_db_query($query));

		return $result[$field];
	}


	/**
	 * Converts a given String $string to any specified encoding (if supported)
	 *
	 * @param String $string
	 * @param String $to ; 2 = from utf-8 to shopencoding set in sofort.ini ; 3 = from shopencoding set in sofort.ini to utf-8; 1 = use specified $fromEncoding; 4 = html_entity_decode()
	 * @param String $fromEncoding (optional)
	 * @return String $string
	 */
	public static function convertEncoding($string, $to, $fromEncoding = '') {
		$shopEncoding = HelperFunctions::getIniValue('shopEncoding');

		if ($shopEncoding == 'UTF-8'){
			return $string;
		} elseif ($to == 1) {
			return mb_convert_encoding($string, $shopEncoding, $fromEncoding);
		} elseif ($to == 2) {
			return mb_convert_encoding($string, $shopEncoding, 'UTF-8');
		} elseif ($to == 3){
			return mb_convert_encoding($string, 'UTF-8', $shopEncoding);
		} elseif ($to == 4){
			return html_entity_decode($string);
		}
	}


	/**
	 * escapes the given string via mysql_real_esacpe_string (if function exists & a db-connection is available) or mysql_escape_string
	 * @param string $string
	 * @return string $string
	 */
	public static function escapeSql($string) {
		return (function_exists('mysqli_real_escape_string') && mysqli_ping($GLOBALS["___mysqli_ston"])) ? ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) : ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	}


	/**
	 * Combination of functions escapeSql() and convertEncoding()
	 * @param string $string
	 * @param int	 $to -> 2 = from utf-8 to shopencoding set in sofort.ini ; 3 = from shopencoding set in sofort.ini to utf-8
	 * @return string $string escaped and converted
	 */
	public static function escapeConvert($string, $to) {
		return HelperFunctions::escapeSql(HelperFunctions::convertEncoding($string,$to));
	}


	/**
	 * creates an escaped "INSERT INTO" sql-string
	 * @param string $table
	 * @param array	 $data with key=column_name and value=column_value; for sql-commands set value like "sqlcommand:now()"
	 * @return string $returnString
	 */
	public static function getEscapedInsertInto($table, $data) {
		$table = trim($table);

		if (!is_string($table) || !$table || !is_array($data) || !$data) return '';

		$returnString = 'INSERT INTO `'.HelperFunctions::escapeSql($table).'` (`';
		$columns = array_keys($data);
		$returnString .= implode('`, `', $columns);
		$returnString .= '`) VALUES (';

		foreach ($data as $value) {
			$returnString .= ((strpos($value, 'sqlcommand:') === 0)) ? HelperFunctions::escapeSql(substr($value, 11)).", " : "'".HelperFunctions::escapeSql($value)."', ";
		}

		$returnString = substr($returnString, 0, -2); //deletes comma and whitespace
		$returnString .= ')'; //dont add ';'

		return $returnString;
	}


	/**
	 * masks the given variable via strip_tags() and htmlentities()
	 * @param mixed $var
	 * @return mixed $var
	 */
	public static function htmlMask($var){
		return htmlentities(strip_tags($var),ENT_QUOTES,HelperFunctions::getIniValue('shopEncoding'));
	}


	/**
	 * return from the sofort.ini the value of the given key
	 * @param string $key
	 * @return mixed $value - if key is not found: false
	 */
	public static function getIniValue($key){
		$iniArray = parse_ini_file('sofort.ini');

		return (!isset($iniArray[$key])) ? false : $iniArray[$key];
	}


	/**
	 * returns language name if found in shop, english as a fallback
	 * @param string $language
	 * @return string -> language
	 */
	public static function getSofortLanguage($language){
		$lngarr = array();
		$lngdir = DIR_FS_CATALOG.'lang/';

		foreach (new DirectoryIterator($lngdir) as $file) {
			if ($file->isDir()) {
				$lngarr[] = $file->getFilename();
			}
		}

		return (!in_array($language,$lngarr)) ? 'english' : $language;
	}


	/**
	 * returns shortened language code, english as a fallback
	 * @param string $lng
	 * @return string -> language code
	 */
	public static function getShortCode($lng){
		switch ($lng){
			case 'german' : return 'de';
			case 'dutch'  : return 'nl';
			case 'french' : return 'fr';
			case 'italian': return 'it';
			case 'polish' : return 'pl';
			case 'english':
			default		  : return 'en';
		}
	}


	/**
	 * Getter for the sofortmodulVersion, set in sofort.ini
	 * @see HelperFunctions::isGambio();
	 * @return String
	 */
	public static function getSofortmodulVersion() {
		return HelperFunctions::getIniValue('sofortmodulVersion');
	}


	/**
	 * validate given api-key against SOFORT and return result
	 * @param string $apiKey
	 * @return bool
	 */
	public static function apiKeyIsValid($apiKey) {
		preg_match('#([a-zA-Z0-9:]+)#', $apiKey, $matches);
		$configKey = $matches[1];
		$SofortLib_TransactionData = new SofortLib_TransactionData($configKey);
		$SofortLib_TransactionData->setTransaction('00000')->sendRequest();

		return ($SofortLib_TransactionData->isError()) ? false : true;
	}


	/**
	 * get the cancel-URL - add given errors to the url
	 * @param string $paymentMethod - assign $this->code
	 * @param array	 $errors (optional) with error-codes (structure like XML-structure!)
	 * @param string $cancelUrl (optional) - if given use this error-url than standard-error-url
	 * @return string -> error_url
	 */
	public static function getCancelUrl($paymentMethod, $errors = array(), $cancelUrl = '') {
		$params = 'payment_error='.$paymentMethod;

		if ($errors) {
			$errorCodes = array();

			foreach ($errors as $oneError) $errorCodes[] = $oneError['code'];
			if($errorCodes) $params .= '&error_codes='.implode(',', $errorCodes);
		}

		return (!$cancelUrl) ? xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $params, 'SSL', true, false) : $cancelUrl .= $params;
	}


	/**
	 * return the last orderStatus for the given orderId from table orders (NOT from sofort-tables!)
	 */
	public static function getLastOrderStatus($orderId){
		if (!$orderId) return false;

		$sql = 'SELECT orders_status FROM '.HelperFunctions::escapeSql(TABLE_ORDERS).' WHERE orders_id = "'.HelperFunctions::escapeSql($orderId).'"';
		$orderStatus = xtc_db_query($sql);
		$orderStatus = xtc_db_fetch_array($orderStatus);

		return $orderStatus['orders_status'];
	}


	/**
	 * Bug: orderstatus must not be 0 or '' (notice: -1 is a valid given order status)
	 * @param int $orderstatus to check
	 * @return if 0 or '': DEFAULT_ORDERS_STATUS_ID will be returned ELSE given orderstatus will be returned
	 */
	public static function checkStatusId ($orderstatus) {
		return ($orderstatus == 0 || $orderstatus == '') ? DEFAULT_ORDERS_STATUS_ID : $orderstatus;
	}


	/**
	 * check, if given paymentSecret belongs to the given transId
	 * @param string $paymentSecretToCheck
	 * @param string $transactionId
	 * @return bool
	 */
	public static function checkPaymentSecret($paymentSecretToCheck, $transactionId) {
		$query = xtc_db_query('SELECT payment_secret FROM sofort_orders WHERE transaction_id = "'.HelperFunctions::escapeSql($transactionId).'"');
		$result = xtc_db_fetch_array($query);
		$dbPaymentSecret = $result['payment_secret'];

		return ($paymentSecretToCheck == $dbPaymentSecret) ? true : false;
	}


	/**
	 * trim the given params to needed shorter string e.g.: 'sr', 'pending', 'confirm_invoice' --> "sr_pen_con_inv"
	 * @param string $paymentMethod
	 * @param string $status
	 * @param string $statusReason
	 * @return string
	 */
	public static function getShortStatus ($paymentMethod, $status, $statusReason) {
		$longStatus = $paymentMethod.'_'.$status.'_'.$statusReason;
		$parts = explode('_', $longStatus);

		foreach ($parts as $key => $part) {
			$part = trim($part);
			$parts[$key] = substr($part, 0, 3);
		}

		$parts = implode('_', $parts);
		return $parts;
	}


	/**
	 * get for the given notification-status the history-comments for the customer and seller
	 * @param string $notificationStatusShort
	 * @param int	 $orderId
	 * @param object $SofortLib_TransactionData
	 * @param string $transactionId
	 * @param bool	 $checkAgainstPenultimateEntry (optional) - if for current notification the entry into sofort_orders_notification was done, all checks have to be done against the penultimate entry
	 * @param object $SofortLoggingObj (optional)
	 * @return array - Comments for the customer and seller (they can also be empty or only one of them is set!)
	 */
	public static function getHistoryComments($notificationStatusShort, $orderId, $SofortLib_TransactionData, $transactionId, $checkAgainstPenultimateEntry = false, $SofortLoggingObj = ''){
		$historyComments = array();
		$historyComments['seller'] = '';
		$historyComments['buyer'] = '';

		$supportedStatus = array(
				'SV_PEN_WAI_FOR_MON', 'SV_REC_CRE', 'SV_REC_CON_PRO', 'SV_REC_OVE', 'SV_REC_PAR_CRE', 'SV_REF_COM', 'SV_REF_REF', 'SV_LOS_NOT_CRE',
				'SU_PEN_NOT_CRE_YET', 'SU_LOS_NOT_CRE', 'SU_REC_CRE', 'SU_REF_REF', 'SU_REF_COM',
				'LS_REC_CRE', 'LS_LOS_REJ', 'LS_REF_COM', 'LS_REF_REF', 'LS_PEN_NOT_CRE_YET',
				'SL_REC_CRE', 'SL_LOS_REJ', 'SL_REF_COM', 'SL_REF_REF', 'SL_PEN_NOT_CRE_YET',
				'SR_PEN_CON_INV', 'SR_LOS_CAN', 'SR_PEN_NOT_CRE_YET', 'SR_REF_REF', 'SR_LOS_CON_PER_EXP',
		);

		if (!in_array(strtoupper($notificationStatusShort), $supportedStatus)) {
			if (is_object($SofortLoggingObj)) $SofortLoggingObj->log("Paymentstatus ($notificationStatusShort) is unimportant. No comments will be set to history. Order-ID: $orderId");

			//normally we could exit() here but maybe there has been a change of the SR-amount
			return $historyComments;
		}

		//prevent identical comments
		if (HelperFunctions::statusAndReasonDidNotChange($orderId, $SofortLib_TransactionData->getStatus(), $SofortLib_TransactionData->getStatusReason(), $checkAgainstPenultimateEntry)) {
			if (is_object($SofortLoggingObj)) $SofortLoggingObj->log("Paymentstatus ($notificationStatusShort) did not change since last notification. No comments will be set to history. Maybe comments have been inserted by successUrl/notification earlier! This notice is no fault! Order-ID: $orderId");

			return $historyComments;
		}

		if (defined('MODULE_PAYMENT_SOFORT_'.strtoupper($notificationStatusShort).'_BUYER')) $historyComments['buyer'] = constant('MODULE_PAYMENT_SOFORT_'.strtoupper($notificationStatusShort).'_BUYER');
		if (defined('MODULE_PAYMENT_SOFORT_'.strtoupper($notificationStatusShort).'_SELLER')) $historyComments['seller'] = constant('MODULE_PAYMENT_SOFORT_'.strtoupper($notificationStatusShort).'_SELLER');

		//only status where the transId has to be added is sr_pen_con_inv
		if ($notificationStatusShort == 'sr_pen_con_inv' && !empty($historyComments['seller']) && $transactionId) $historyComments['seller'] .= $transactionId;

		//special replacements in history-strings
		$historyComments = HelperFunctions::replaceSpecialTextInHistoryComment($historyComments, $SofortLib_TransactionData, $transactionId);

		return $historyComments;
	}


	/**
	 * get the new shop-status for the given status
	 * @param string $notificationStatus - e.g. "sr_pen_con_inv"
	 * @param int	 $orderId
	 * @param object $SofortLoggingObj (optional) - only for logging of notices/warnings/errors and other messages
	 * @return int newShopStatus for this notification OR false if status-update is unimportant (e.g. there could be an amount-update needed)
	 */
	public static function getNewShopStatusId($notificationStatusShort, $orderId, $SofortLoggingObj = '') {
		$statusConstant = strtoupper('MODULE_PAYMENT_SOFORT_'.$notificationStatusShort.'_STATUS_ID');

		//exceptions
		switch ($statusConstant) {
			case 'MODULE_PAYMENT_SOFORT_SR_LOS_CON_PER_EXP_STATUS_ID': $statusConstant = 'MODULE_PAYMENT_SOFORT_SR_LOS_CAN_STATUS_ID'; break;
			case 'MODULE_PAYMENT_SOFORT_SV_REC_OVE_STATUS_ID':
			case 'MODULE_PAYMENT_SOFORT_SV_REC_PAR_CRE_STATUS_ID':	   $statusConstant = 'MODULE_PAYMENT_SOFORT_SV_WRONG_AMOUNT_STATUS_ID'; break;
		}

		//the needed status-constant must be defined otherwise status is unimportant (or there is an error e.g. if seller has uninstalled the module)
		if (defined($statusConstant)) {
			$newShopStatus = constant($statusConstant);
		} else {
			if (is_object($SofortLoggingObj)) $SofortLoggingObj->log("Required status ($statusConstant) not defined/found. Status and statusupdate is unimportant! Order-ID: $orderId! (This might be an error, if module was not correct installed or is currently uninstalled!)");

			//we should but cannot exit here, there could be a change of the SR-Amount with an unimportant status
			return false;
		}

		//dont set a new status? -> then use the last order status
		return (HelperFunctions::statusIsUnchangedStatus($newShopStatus)) ? HelperFunctions::getLastOrderStatus($orderId) : $newShopStatus;
	}


	/**
	 * check if given status is "dont change - use last order status" ( = MODULE_PAYMENT_SOFORT_MULTIPAY_UNCHANGED_STATUS_ID)
	 * @param int $statusToCheck
	 * @param object $SofortLoggingObj (optional)
	 * @return bool
	 */
	public static function statusIsUnchangedStatus($statusToCheck, $SofortLoggingObj = '') {
		if (!defined('MODULE_PAYMENT_SOFORT_MULTIPAY_UNCHANGED_STATUS_ID')) {
			if (is_object($SofortLoggingObj)) $SofortLoggingObj->logWarning("No 'unchanged'-Status (MODULE_PAYMENT_SOFORT_MULTIPAY_UNCHANGED_STATUS_ID) found. Maybe module was not correct installed. Please read the manual! Unchanged-Status will be ignored. Check order! Order-ID: $orderId");

			return false;
		}

		return ($statusToCheck == MODULE_PAYMENT_SOFORT_MULTIPAY_UNCHANGED_STATUS_ID) ? true : false;
	}


	/**
	 * check if last notification had the same status and statusReason - e.g. SR: pending-not_credited_yet_pending is the same like pending-not_credited_yet_delcredere
	 * @param int	 $orderId
	 * @param string $statusToCheck
	 * @param string $statusReasonToCheck
	 * @param bool	 $checkAgainstPenultimateEntry (optional)
	 * @return bool
	 */
	public static function statusAndReasonDidNotChange($orderId, $statusToCheck, $statusReasonToCheck, $checkAgainstPenultimateEntry = false) {
		$lastStatus = HelperFunctions::getLastFieldValueFromSofortTable($orderId, 'status', $checkAgainstPenultimateEntry);
		$lastStatusReason = HelperFunctions::getLastFieldValueFromSofortTable($orderId, 'status_reason', $checkAgainstPenultimateEntry);

		return ($statusToCheck == $lastStatus && $statusReasonToCheck == $lastStatusReason) ? true : false;
	}


	/**
	 * replace special parts of the history-comment e.g. "{{time}}" -> "01.01.2000 10:10", "{{amount_refunded}}" -> "123" etc.
	 * @param array	 $historyComments within seller- and buyer-comment
	 * @param object $SofortLib_TransactionData
	 * @param int	 $transactionId
	 * @return array $historyComment with all needed replacements
	 */
	public static function replaceSpecialTextInHistoryComment($historyComments, $SofortLib_TransactionData, $transactionId){
		//used in regex below, dont change the naming!
		$refunded_amount = $SofortLib_TransactionData->getAmountRefunded();
		$tId = $transactionId;
		$transaction = $transactionId;
		$paymentMethod = $SofortLib_TransactionData->getPaymentMethod();
		$time = date('d.m.Y, G:i:s');
		$paymentMethodStr = '';
		$ks = ($SofortLib_TransactionData->getConsumerProtection()) ? 'KS_' : '';
		$paymentMethodStr = constant('MODULE_PAYMENT_SOFORT_'.strtoupper($paymentMethod).'_'.$ks.'TEXT_TITLE');

		//some string may have inside {{time}} - its not needed anymore
		$historyComments['seller'] = trim(str_ireplace('{{time}}', '', $historyComments['seller']));
		$historyComments['buyer']  = trim(str_ireplace('{{time}}', '', $historyComments['buyer']));

		//replacement
		$replacements = [
			'{{transactionId}}'    => $transactionId,
			'{{refunded_amount}}'  => $refunded_amount,
			'{{amount_refunded}}'  => $refunded_amount,
			'{{tId}}'              => $tId,
			'{{transaction}}'      => $transaction,
			'{{paymentMethod}}'    => $paymentMethod,
			'{{time}}'             => $time,
			'{{paymentMethodStr}}' => $paymentMethodStr,
		];
		$historyComments['seller'] = strtr($historyComments['seller'], $replacements);
		$historyComments['buyer']  = strtr($historyComments['buyer'], $replacements);

		return $historyComments;
	}


	/**
	 * Fetch order-ID from table orders for given transactionId
	 * @param $transactionId
	 * @param object $SofortLoggingObj (optional)
	 * @return int order-ID ELSE empty string
	 */
	public static function getOrderId($transactionId, $SofortLoggingObj = '') {
		$result = xtc_db_query('SELECT orders_id FROM '.TABLE_ORDERS.' WHERE orders_ident_key = "'.HelperFunctions::escapeSql($transactionId).'"');
		$orderIdArray = xtc_db_fetch_array($result);

		if (!isset($orderIdArray['orders_id']) && !empty($orderIdArray['orders_id'])) {
			if (is_object($SofortLoggingObj)) $SofortLoggingObj->logWarning("Order-ID not found for Transaction-ID: $transactionId. Check order!");

			return '';
		}

		return $orderIdArray['orders_id'];
	}


	/**
	 * Only SR: check given notification-amount against the last shop-amount
	 * @param int	 $notificationAmount
	 * @param int	 $orderId
	 * @param bool	 $usePenultimateEntry (optional) - Standard=false - Check given amount against the penultimate entry in sofort_orders_notification
	 * @param object $SofortLoggingObj (optional)
	 * @return bool true if amount differs ELSE false
	 */
	function checkIfNewTotal($notificationAmount, $orderId, $usePenultimateEntry = false, $SofortLoggingObj = ''){
		$lastShopTotal = HelperFunctions::getLastFieldValueFromSofortTable($orderId, 'amount', $usePenultimateEntry);

		//1st notification? -> amount cannot be changed
		if (!$lastShopTotal) return false;

		//warning, if no notification exists in sofort-table
		if($lastShopTotal === '') {
			if (is_object($SofortLoggingObj)) $SofortLoggingObj->logWarning("Last total (amount) could not be found in shop-DB! Check amount of the order! Order-ID: $orderId");

			return false;
		}

		return ($lastShopTotal != $notificationAmount) ? true : false;
	}


	/**
	 * Only SR: insert a comment into buyer-history with a text like "Invoice changed. New amount: 123 Euro."
	 * @param int	 $orderId
	 * @param int	 $status
	 * @param float	 $oldTotal
	 * @param float	 $newTotal
	 * @param string $time (optional) - will be placed in comment if given
	 * @param object $SofortLoggingObj (optional)
	 * @return bool true (always)
	 */
	public static function insertNewTotalCommentToHistory($orderId, $status, $oldTotal, $newTotal, $time = '', $SofortLoggingObj = ''){
		$comments  = ($newTotal > $oldTotal) ? MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_RESET : MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_EDITED;
		$comments .=' '.MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CURRENT_TOTAL.' '.$newTotal.' Euro';

		if ($time) $comments .= ' '.MODULE_PAYMENT_SOFORT_TRANSLATE_TIME.': '.$time;

		HelperFunctions::insertHistoryEntry((int)$orderId, $status, $comments);

		if (is_object($SofortLoggingObj)) $SofortLoggingObj->log("New total comment set to history. Old total: $oldTotal, new total: $newTotal, Order-ID: ".$orderId);

		return true;
	}


	/**
	 * update delivery- and invoice-address in shopsystem, if buyer changed them at pnag-wizard
	 * @param array	 $invoiceAddress
	 * @param array	 $shippingAddress
	 * @param int	 $orderId
	 * @param object $SofortLoggingObj (optional)
	 * @return bool true (always)
	 */
	public static function updateShopAdresses ($invoiceAddress, $shippingAddress, $orderId, $SofortLoggingObj = '') {
		$sql = "UPDATE	".HelperFunctions::escapeSql(TABLE_ORDERS)."
				SET		billing_name = '".HelperFunctions::escapeConvert($invoiceAddress['firstname'],2)." ".HelperFunctions::escapeConvert($invoiceAddress['lastname'],2)."',
						billing_firstname = '".HelperFunctions::escapeConvert($invoiceAddress['firstname'],2)."',
						billing_lastname = '".HelperFunctions::escapeConvert($invoiceAddress['lastname'],2)."',
						billing_company = '',
						billing_street_address = '".HelperFunctions::escapeConvert($invoiceAddress['street'],2)." ".HelperFunctions::escapeConvert($invoiceAddress['street_number'],2)."',
						billing_suburb = '".HelperFunctions::escapeConvert($invoiceAddress['street_additive'],2)."',
						billing_city = '".HelperFunctions::escapeConvert($invoiceAddress['city'],2)."',
						billing_postcode = '".HelperFunctions::escapeConvert($invoiceAddress['zipcode'],2)."',
						billing_state = '',
						billing_country = 'Germany',
						billing_country_iso_code_2 = '" .HelperFunctions::escapeConvert($invoiceAddress['country_code'],2). "',
						last_modified = now()
				WHERE	orders_id = '".(int)$orderId."'";
		$result = xtc_db_query($sql);

		if (is_object($SofortLoggingObj)) {
			if (!$result) {
				$SofortLoggingObj->logError("Error while updating invoiceaddress in shop. Order-ID: $orderId. Check address! SQL-query: $sql");
			} else if (function_exists('mysqli_affected_rows')) {
				switch (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
					case 1: $SofortLoggingObj->log("Invoiceaddress successfully updated in shop. Order-ID: $orderId."); break;
					case 0: $SofortLoggingObj->log("Invoiceaddress did not change. Update not necessary in Shop. Maybe address was updates earlier by successUrl/Notification. Order-ID: $orderId."); break;
					case -1://currently nothing to do here
				}
			}
		}

		$sql = "UPDATE	".HelperFunctions::escapeSql(TABLE_ORDERS)."
				SET		delivery_name = '".HelperFunctions::escapeConvert($shippingAddress['firstname'],2)." ".HelperFunctions::escapeConvert($shippingAddress['lastname'],2)."',
						delivery_firstname = '".HelperFunctions::escapeConvert($shippingAddress['firstname'],2)."',
						delivery_lastname = '".HelperFunctions::escapeConvert($shippingAddress['lastname'],2)."',
						delivery_company = '',
						delivery_street_address = '".HelperFunctions::escapeConvert($shippingAddress['street'],2)." ".HelperFunctions::escapeConvert($shippingAddress['street_number'],2)."',
						delivery_suburb = '".HelperFunctions::escapeConvert($shippingAddress['street_additive'],2)."',
						delivery_city = '".HelperFunctions::escapeConvert($shippingAddress['city'],2)."',
						delivery_postcode = '".HelperFunctions::escapeConvert($shippingAddress['zipcode'],2)."',
						delivery_state = '',
						delivery_country = 'Germany',
						delivery_country_iso_code_2 = '" .HelperFunctions::escapeConvert($shippingAddress['country_code'],2). "',
						last_modified = now()
				WHERE	orders_id = '".(int)$orderId."'";
		$result = xtc_db_query($sql);

		if (is_object($SofortLoggingObj)) {
			if (!$result) {
				$SofortLoggingObj->logError("Error while updating shippingaddress in shop. Order-ID: $orderId. Check address! SQL-query: $sql");
			} else if (function_exists('mysqli_affected_rows')) {
				switch (mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
					case 1: $SofortLoggingObj->log("Shippingaddress successfully updated in shop. Order-ID: $orderId."); break;
					case 0: $SofortLoggingObj->log("Shippingaddress did not change. Update not necessary in Shop. Maybe address was updates earlier by successUrl/Notification. Order-ID: $orderId."); break;
					case -1://currently nothing to do here
				}
			}
		}

		return true;
	}


	/**
	 * set a new payment-status to an order in table "orders"
	 * @param int $orderId
	 * @param int $newStatus
	 * @return bool true (always)
	 */
	public static function updateTableOrdersStatus($orderId, $newStatus) {
		xtc_db_query('UPDATE '.HelperFunctions::escapeSql(TABLE_ORDERS).'
					  SET	 orders_status = "'.HelperFunctions::escapeSql($newStatus).'",
							 last_modified = now()
					  WHERE	 orders_id = '.(int)$orderId.';');
		return true;
	}


	/**
	 * check, if given text exists in history-comments for given orderId (test is done case-insensitiv!)
	 * @param string $textToCheck - must not be empty
	 * @param int $orderId
	 * @return bool - returns also true, if given string is only a part of the string in the history
	 */
	public function textExistsInOrderHistory($textToCheck, $orderId){
		$textToCheck = trim ($textToCheck);

		if (!$orderId || !$textToCheck) return false;

		$historyQuery = xtc_db_query("SELECT comments FROM ".TABLE_ORDERS_STATUS_HISTORY." WHERE orders_id = '".(int) $orderId."'");

		if (xtc_db_num_rows($historyQuery)) {

			while ($historys = xtc_db_fetch_array($historyQuery)) {
				$oneEntry = $historys['comments'];

				if (stripos($oneEntry, $textToCheck) !== false) {
					return true; //found
				}
			}
		}
		return false; //not found
	}


	/**
	 * set new history-entry into table "orders_status_history"
	 * @param int	 $orderId
	 * @param int	 $status - if '-1' -> comment is only shown to seller (not buyer)
	 * @param string $comment for buyer/seller
	 * @param int	 $customerNotified (optional) - dbField "customer_notified" - if you set empty string, its set to 0 (xtc3, comSeo) or 1 (gam)
	 * @param string $time (optional) - will be placed behind the comment
	 * @return bool true (always)
	 * @todo this function is compatible with modified shop 1.05 but not with 1.06 - differences in fields: customer_notified and comments_sent - use: HelperFunctions::getIniValue('shopsystemVersion');
	 */
	public static function insertHistoryEntry($orderId, $status, $comment, $customerNotified = '', $time = '') {
		if ($time) $comment .= ' '.MODULE_PAYMENT_SOFORT_TRANSLATE_TIME.': '.$time;

		if (!is_numeric($customerNotified)) {
			$customerNotified = 0;

			//if gambio: $customer_notified must be "1" to make information visibible for customer
			if ($status != '-1')
			{
				$customerNotified = '1';
			}
		}

		if ($status < 0)
		{
			$status = DEFAULT_ORDERS_STATUS_ID;
			$last_status_query = sprintf('SELECT orders_status_id FROM orders_status_history WHERE orders_id = %d ORDER BY date_added DESC LIMIT 1', $orderId);
			$result = xtc_db_query($last_status_query);
			while($row = xtc_db_fetch_array($result))
			{
				$status = $row['orders_status_id'];
			}
		}

		$sqlDataArray = array(
			'orders_id'			=> (int)$orderId,
			'orders_status_id'	=> $status,
			'date_added'		=> 'sqlcommand:now()',
			'customer_notified' => (int)$customerNotified,
			'comments'			=> $comment,
		);
		xtc_db_query(HelperFunctions::getEscapedInsertInto(TABLE_ORDERS_STATUS_HISTORY, $sqlDataArray));
		return true;
	}


	/**
	 * check, if there is at least one entry in table sofort_orders_notification for given transactionId
	 * @param string $transactionId
	 * @return bool
	 */
	public static function hasEntriesInTableSofortNotifications($transactionId) {
		$result = xtc_db_query('SELECT	  sofort_orders.id
								FROM	  `sofort_orders_notification`
								LEFT JOIN `sofort_orders`
								ON		  sofort_orders.id = sofort_orders_notification.sofort_orders_id
								WHERE	  transaction_id = "'.HelperFunctions::escapeSql($transactionId).'"');

		return (xtc_db_fetch_array($result)) ? true : false;
	}


	/**
	 * get transaction-ID for given order-ID from table_orders
	 * @param int $orderId
	 * @return string $transactionID (might be empty, if not found)
	 */
	public static function getTransactionIdFromShop($orderId){
		$result = mysqli_query($GLOBALS["___mysqli_ston"], 'SELECT orders_ident_key FROM '.HelperFunctions::escapeSql(TABLE_ORDERS).' WHERE orders_id = "'.HelperFunctions::escapeSql($orderId).'"');
		$row = xtc_db_fetch_array($result);
		$transactionId = $row['orders_ident_key'];
		return $transactionId;
	}


	/**
	 * replace '&amp;' AND '&&' into '&' --- xtc_href_link() doesnt work always correct --- ATTENTION: check, if it is needed for your url-string!
	 * @param string $stringToClean - complete url or only parameters
	 * @return string - cleaned string
	 */
	public static function cleanUrlParameter($stringToClean) {
		while (strstr($stringToClean, '&amp;')) $stringToClean = str_replace('&amp;', '&', $stringToClean);
		while (strstr($stringToClean, '&&')) $stringToClean = str_replace('&&', '&', $stringToClean);
		return $stringToClean;
	}


	/**
	 * checks, if this shopsystem is a Gambio
	 */
	public static function isGambio() {
		if (defined('_GM_VALID_CALL')) return true;
		if (stripos(HelperFunctions::getSofortmodulVersion(), 'gambio') !== false) return true;

		return false;
	}


	/**
	 * checks, if in given order are gx-customizer-articles (Warning: make sure, shopsystem is gambio before function-call)
	 * This function may not work correct, if seller has configured his GX-Customizer incorrect!
	 * @param int $orderId
	 * @return bool
	 */
	public function orderHasGxCustomizerArticles($orderId) {

		if (!orderId) return false;

		$checkQuery = xtc_db_query("SELECT `name` FROM ".TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS." WHERE `orders_products_id` IN (
			SELECT orders_products_id FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = '".(int) $orderId."')");

		return (xtc_db_num_rows($checkQuery)) ? true : false;
	}


	/**
	 * try to find out the GX-Customizer-Data in given productsID and delete it
	 * example given string: "6{4}8{119393}0" -> "{119393}0" is GX-Customizer, delete it and return "6{4}8"
	 * @param string $sofortItemId like "6{4}8{119393}0"
	 * @param int $customerId - ID from buyer
	 * @return string
	 */
	public function deleteGxCustomizerData($productsId, $customerId) {

		if (strpos($productsId, '{') === false) return $productsId; //article has no GX-Customizer-Data

		$query = xtc_db_query("SELECT products_id FROM ".TABLE_GM_GPRINT_CART_ELEMENTS." WHERE customers_id = '".(int) $customerId."'");

		if (!xtc_db_num_rows($query)) return $productsId; //article has no GX-Customizer-Data OR GX-Customizer is wrong configured by seller

		$splitted = explode('{', $productsId);
		$newProductsId = $splitted[0]; //begins with article-ID
		unset ($splitted[0]); //now there are only attributes now inside
		$sortedAttributes = array();

		foreach ($splitted as $optionAndValue) {
			$explodedOptionAndValue = explode('}', $optionAndValue);

			//possible Bug: option-ID can be in Frontend "13_chk47" and in backend "13" --> we always set "13"
			//if there are more "features" like this in the article-attributes -> add/replace here
			preg_match('/^[0-9]*/', $explodedOptionAndValue[0], $explodedOptionAndValue[0]);
			$sortedAttributes[$explodedOptionAndValue[0][0]] = $explodedOptionAndValue[1]; //keys are unique
		}

		foreach ($sortedAttributes as $key => $value) {

			if (strlen($key) == 6) {
				$keyCopy = (int) $key;

				if ($keyCopy > 99999 && $keyCopy < 1000000) {
					unset ($sortedAttributes[$key]);
					break; //only one entry of gx-customizer exists
				}
			}
		}

		foreach ($sortedAttributes as $option => $value) {
			$newProductsId .= '{'.$option.'}'.$value;
		}

		return $newProductsId;
	}


	/**
	 * sort the SOFORT-item-ID by value
	 * example: "49{1}26{6}23" and "49{6}23{1}26" is the same article -> sort by number "...{1}...{6}..." and return it
	 * option-fields like "13_chk47" will be observed
	 * @param string $sofortItemId
	 * @return string
	 */
	public static function sortSofortItemId($sofortItemId) {
		if (strpos($sofortItemId, '{') === false) return $sofortItemId; //article has no attributes, return article-id

		$splitted = explode('{', $sofortItemId);
		$newSofortItemId = $splitted[0]; //begins with article-ID
		unset ($splitted[0]); //now there are only attributes inside
		$sortedAttributes = array();

		foreach ($splitted as $optionAndValue) {
			$explodedOptionAndValue = explode('}', $optionAndValue);

			//possible Bug: option-ID can be in Frontend "13_chk47" and in backend "13" --> we always set "13"
			//if there are more "features" like this in the article-attributes -> add/replace here
			preg_match('/^[0-9]*/', $explodedOptionAndValue[0], $explodedOptionAndValue[0]);
			$sortedAttributes[$explodedOptionAndValue[0][0]] = $explodedOptionAndValue[1]; //keys are unique
		}

		ksort($sortedAttributes);

		foreach ($sortedAttributes as $option => $value) $newSofortItemId .= '{'.$option.'}'.$value;

		return $newSofortItemId; //return something like "49{1}26{6}23"
	}
}