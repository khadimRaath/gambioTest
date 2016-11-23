<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-02-28 10:34:33 +0100 (Thu, 28 Feb 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: callback.php 6031 2013-02-28 09:34:33Z rotsch $
 */


chdir('../../');
require_once('includes/application_top.php');

require_once(DIR_FS_CATALOG.'callback/sofort/helperFunctions.php');
require_once(DIR_FS_CATALOG.'callback/sofort/library/sofortLib.php');
require_once(DIR_FS_CATALOG.'callback/sofort/library/sofortLib_classic_notification.inc.php');
require_once(DIR_FS_CATALOG.'callback/sofort/library/helper/class.invoice.inc.php');
require_once(DIR_FS_CATALOG.'callback/sofort/ressources/scripts/sofortOrderSynchronisation.php');

$language = HelperFunctions::getSofortLanguage($_SESSION['language']);

$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_ideal.php');
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_lastschrift.php');
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_sofortlastschrift.php');
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_sofortrechnung.php');
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_sofortueberweisung.php');
$coo_lang_file_master->init_from_lang_file('lang/' . $language . '/modules/payment/sofort_sofortvorkasse.php');

if ($_GET['action'] == 'ideal'){ // iDeal
	handleIdeal();
	exit();
} elseif ($_GET['action'] == 'su'){ // SU-classic
	die('Error: SOFORT Ueberweisung (classic) is not implemented here! Exit.');
} elseif ($_GET['action'] == 'multipay' || !$_GET['action']) { // Multipay
	$SofortLib_Notification = new SofortLib_Notification();
	$transactionId = $SofortLib_Notification->getNotification();

	if (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED') && MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED == "True") {
		$SofortLib_Notification->setLogEnabled();
		$loggingEnabled = true;
	} else {
		$loggingEnabled = false;
	}

	if (empty($transactionId)) {
		if ($loggingEnabled) $SofortLib_Notification->logError('Transaction-ID empty! Order-status could not be updated. Check shop for incomplete orders! Notification-Object: '.print_r($SofortLib_Notification, true));

		exit('Error: Request could not be processed.');
	}

	$time = $SofortLib_Notification->getTime();
	$time = date('d.m.Y, G:i:s', strtotime($time));
	$SofortLib_TransactionData = new SofortLib_TransactionData(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY);

	if (!is_object($SofortLib_TransactionData) || !($SofortLib_TransactionData instanceof SofortLib_TransactionData)) {
		if ($loggingEnabled) $SofortLib_Notification->logError("TransactionData-Object corrupt. Transaction-ID: $transactionId. Check order!");

		exit('Error: Request could not be processed.');
	}

	$SofortLib_TransactionData->setTransaction($transactionId);
	$SofortLib_TransactionData->sendRequest();

	if ($SofortLib_TransactionData->isError()) {
		if ($loggingEnabled) $SofortLib_Notification->logError("Error in TransactionData: ".print_r($SofortLib_TransactionData->getError(), true)."Transaction-ID: $transactionId. Check order!");

		exit('Error: Request could not be processed.');
	}

	$paymentMethod = $SofortLib_TransactionData->getPaymentMethod();
	$paymentSecret = $_REQUEST['paymentSecret'];

	if (!HelperFunctions::checkPaymentSecret($paymentSecret, $transactionId)) {
		if ($loggingEnabled) $SofortLib_Notification->logError("Wrong Payment-Secret given for Transaction-ID: $transactionId! Check order!");

		exit('Error: Request could not be processed.');
	}

	$notificationStatusReason = $SofortLib_TransactionData->getStatusReason();
	$notificationStatus = $SofortLib_TransactionData->getStatus();
	$orderId = HelperFunctions::getOrderId($transactionId);

	if (!$orderId) {
		if ($loggingEnabled) $SofortLib_Notification->logError("Order-ID not found for Transaction-ID: $transactionId! Check order!");

		exit('Error: Request could not be processed.');
	}

	if ($orderId != $SofortLib_TransactionData->getUserVariable(0)) {
		if ($loggingEnabled) $SofortLib_Notification->logError("TransactionData-Order-ID and Shop-Order-ID differ. Transaction-ID: $transactionId! Check order!");

		exit('Error: Request could not be processed.');
	}
}

//Notice: Following code ist not compatible with ideal or sofortueberweisung-classic! They have to be implemented above/anywhere!

$dbEntries = array(
		'amount' => $SofortLib_TransactionData->getAmount(),
		'status' => $notificationStatus,
		'status_reason' => $notificationStatusReason,
);

$PnagInvoice = '';

if ($paymentMethod == 'sr') {
	$PnagInvoice = new PnagInvoice(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY, $transactionId);

	if ($PnagInvoice->isError()) {
		$SofortLib_Notification->logError("Errors while creating invoice-object: ".print_r($PnagInvoice->getErrors(), true)." Transaction-ID: $transactionId. Check order!");

		exit('Error: Request could not be processed.');
	}

	$dbEntries['status_id'] = $PnagInvoice->getState();
}

$notificationStatusShort = HelperFunctions::getShortStatus($paymentMethod, $notificationStatus, $notificationStatusReason);
$newShopStatus = HelperFunctions::getNewShopStatusId($notificationStatusShort, $orderId, $SofortLib_Notification);
$historyComments = HelperFunctions::getHistoryComments($notificationStatusShort, $orderId, $SofortLib_TransactionData, $transactionId, false, $SofortLib_Notification);
$tableSofortOrdersNotificationIsEmpty = HelperFunctions::hasEntriesInTableSofortNotifications($transactionId);

//save all relevant data in table sofort_notification
//this DB-entry also prevents race-conditions between call of successUrl and notification
//@see hasEntriesInTableSofortNotifications() and statusAndReasonDidNotChange()
//must be set (preferably direct) after call of getHistoryComments() because of a check inside
HelperFunctions::insertSofortOrdersNotification($transactionId, $dbEntries, $SofortLib_Notification);

//special features for SR
if ($paymentMethod == 'sr') {
	//if buyer has changed addresses at sofort-wizard -> set new addresses also in shopsystem
	if ($notificationStatusShort == 'sr_pen_con_inv' && !$tableSofortOrdersNotificationIsEmpty) HelperFunctions::updateShopAdresses($SofortLib_TransactionData->getInvoiceAddress(), $SofortLib_TransactionData->getShippingAddress(), $orderId, $SofortLib_Notification);

	//insert comment into history, if amount has changed
	$isNewTotal = HelperFunctions::checkIfNewTotal($SofortLib_TransactionData->getAmount(), $orderId, true, $SofortLib_Notification);

	if ($isNewTotal && !invoiceWasReanimated($orderId, $notificationStatus, true)) {
		$oldTotal = HelperFunctions::getLastFieldValueFromSofortTable($orderId, 'amount', true);
		$lastOrderStatus = HelperFunctions::getLastOrderStatus($orderId);
		HelperFunctions::insertNewTotalCommentToHistory($orderId, $lastOrderStatus, $oldTotal, $SofortLib_TransactionData->getAmount(), $time, $SofortLib_Notification);
	}

	//sync SOFORT-articles/orderTotals with the shop-articles/orderTotals
	$SofortOrderSynchronisation = new sofortOrderSynchronisation();
	$SofortOrderSynchronisation->editArticlesShop($PnagInvoice, $orderId);

	//invoice was canceled (after confirmation) and now reanimated again
	if (invoiceWasReanimated($orderId, $notificationStatus, true)) {
		if (!$newShopStatus) $newShopStatus = MODULE_PAYMENT_SOFORT_SR_PEN_NOT_CRE_YET_STATUS_ID; //if $newShopStatus is unknown, set the SR-confirmed-status

		HelperFunctions::insertHistoryEntry($orderId, $newShopStatus, MODULE_PAYMENT_SOFORT_SR_TRANSLATE_INVOICE_REANIMATED, '', $time);
		HelperFunctions::updateTableOrdersStatus($orderId, $newShopStatus);
		//FIXME reset stock and check cart
	}
}

//insert all data into orders-table and orders_status_history-table
if ($historyComments['buyer']) {
	if (!$newShopStatus) {
		$SofortLib_Notification->logError("Status for buyer-comment (".$historyComments['buyer'].") unknown. Comment will not be inserted. Check order! Order-Id: $orderId, Transaction-ID: $transactionId!");
		exit('Error: Request could not be processed.');
	}

	HelperFunctions::insertHistoryEntry($orderId, $newShopStatus, $historyComments['buyer'], '', $time);
}

if ($historyComments['seller']) HelperFunctions::insertHistoryEntry($orderId, -1, $historyComments['seller'], '', $time);

//if $newShopStatus is set, set it into table orders
if ($newShopStatus) HelperFunctions::updateTableOrdersStatus($orderId, $newShopStatus);

//all finished
exit('Callback.php processed.');


//////////////////////////////////////////////////////////
//////////////// ONLY FUNCTIONS FOLLOWING ////////////////
//////////////////////////////////////////////////////////
//////// MORE FUNCTIONS AT CLASS HELPERFUNCTIONS /////////
//////////////////////////////////////////////////////////


/**
 * (only SR) Check if an already refunded invoice was enabled again
 * @param bool $checkAgainstPenultimateEntry (optional) - if current notification is already inserted, we must check against the penultimate entry in table_orders_notification
 * @return boolean
 */
function invoiceWasReanimated($orderId, $notificationStatus, $checkAgainstPenultimateEntry = false) {
	$lastStatus = HelperFunctions::getLastFieldValueFromSofortTable($orderId, 'status', $checkAgainstPenultimateEntry);

	return ($lastStatus == 'refunded' && $notificationStatus != 'refunded') ? true : false;
}


/**
 * handles complete iDEAL-Notification incl. status and history-comments
 * @return nothing - dies at the end
 */
function handleIdeal() {
	list ($userid, $projectid) = explode(':', MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_CONFIGURATION_KEY);
	$SofortLib_ClassicNotification = new SofortLib_ClassicNotification($userid, $projectid, MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_NOTIFICATION_PASSWORD);
	$SofortLib_ClassicNotification->getNotification($_REQUEST);

	//NOTICE: Logging is only available in sofortLib-Multipay (not in sofortLib-Classic)
	//Errors will be placed into exit("...");
	if ($SofortLib_ClassicNotification->isError()) exit($SofortLib_ClassicNotification->getError());

	//dont change the naming of "$tId"! Needed in regex below.
	$tId = $SofortLib_ClassicNotification->getTransaction();

	if (empty($tId)) exit('TransID empty!');

	$time = date('d.m.Y, G:i:s', strtotime($SofortLib_ClassicNotification->getTime())); //Needed in regex below!
	$statusReason = $SofortLib_ClassicNotification->getStatusReason();
	$status = $SofortLib_ClassicNotification->getStatus();
	$orderId = $SofortLib_ClassicNotification->getUserVariable(0);
	$customerId = $SofortLib_ClassicNotification->getUserVariable(1);
	$paymentMethodStr = MODULE_PAYMENT_SOFORT_IDEAL_TEXT_TITLE; //Needed in regex below.

	//first notification will set transId into table orders and table orders_status_history
	xtc_db_query("UPDATE ".HelperFunctions::escapeSql(TABLE_ORDERS)."
				  SET	 orders_ident_key = '".HelperFunctions::escapeSql($tId)."'
				  WHERE	 orders_id = '".HelperFunctions::escapeSql($orderId)."'
				  AND	 orders_ident_key is NULL
				  AND	 customers_id = '".HelperFunctions::escapeSql($customerId)."'");

	if (function_exists('mysqli_affected_rows') && mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == '1') HelperFunctions::insertHistoryEntry($orderId, HelperFunctions::getLastOrderStatus($orderId), MODULE_PAYMENT_SOFORT_MULTIPAY_TRANSACTION_ID.': '.$tId);

	//set new status and history-comment
	if ($status == 'pending' && $statusReason == 'not_credited_yet') {
		$comment = MODULE_PAYMENT_SOFORT_IDEAL_PEN_NOT_CRE_YET_BUYER;
		$newShopStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_TMP_STATUS_ID;
	} elseif ($status == 'received' && $statusReason == 'credited') {
		$comment = MODULE_PAYMENT_SOFORT_IDEAL_REC_CRE_BUYER;
		$newShopStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_ORDER_STATUS_ID;  //only this status equals "paid"! No other status!
	} elseif ($status == 'loss' && $statusReason == 'not_credited') {
		$comment = MODULE_PAYMENT_SOFORT_IDEAL_LOS_NOT_CRE_YET_BUYER;
		$newShopStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LOS_NOT_CRE_STATUS_ID;
	} elseif ($status == 'refunded' && $statusReason == 'compensation') {
		$comment = MODULE_PAYMENT_SOFORT_IDEAL_REF_COM_BUYER;
		$newShopStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_COM_STATUS_ID;
	} elseif ($status == 'refunded' && $statusReason == 'refunded') {
		$comment = MODULE_PAYMENT_SOFORT_IDEAL_REF_REF_BUYER;
		$newShopStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_REF_REF_STATUS_ID;
	} elseif ($status == 'late_succeed' && $statusReason == 'automatic_refund_to_customer') {
		$comment = MODULE_PAYMENT_SOFORT_IDEAL_LAT_SUC_AUT_REF_TO_CUS_BUYER;
		$newShopStatus = MODULE_PAYMENT_SOFORT_IDEAL_CLASSIC_LAT_SUC_AUT_STATUS_ID;
	} else {
		exit("Unknown status ($status) and status-reason ($statusReason). Exit.");
	}

	if (HelperFunctions::statusIsUnchangedStatus($newShopStatus)) $newShopStatus = HelperFunctions::getLastOrderStatus($orderId);

	$newShopStatus = HelperFunctions::checkStatusId($newShopStatus);
	$replacements = [
		'{{tId}}'              => $tId,
		'{{time}}'             => $time,
		'{{paymentMethodStr}}' => $paymentMethodStr,
		'{{status}}'           => $status,
		'{{statusReason}}'     => $statusReason,
		'{{orderId}}'          => $orderId,
		'{{customerId}}'       => $customerId,
	];
	$comment = strtr($comment, $replacements); // Set paymentMethod, tId and time
	$orderQuery = xtc_db_query("SELECT orders_id FROM ".HelperFunctions::escapeSql(TABLE_ORDERS)." WHERE orders_ident_key = '".HelperFunctions::escapeSql($tId)."'");

	if (xtc_db_num_rows($orderQuery) != 1 || empty($tId)) $orderQuery = xtc_db_query("SELECT orders_id FROM ".HelperFunctions::escapeSql(TABLE_ORDERS)." WHERE orders_id = '".(int)$orderId."' AND customers_id = '".(int)$customerId."'");

	if (xtc_db_num_rows($orderQuery) != 1) {
		exit("Order not found! Order-ID: $orderId, transaction-ID: $tId. Exit!");
	} else {
		HelperFunctions::insertHistoryEntry($orderId, $newShopStatus, $comment);
		xtc_db_query("UPDATE ".HelperFunctions::escapeSql(TABLE_ORDERS)." SET orders_status = '".HelperFunctions::escapeSql($newShopStatus)."', last_modified = NOW() WHERE orders_id = '".(int)$orderId."'");
	}

	exit('success'); //iDEAL must exit here!
}
