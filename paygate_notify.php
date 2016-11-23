<?php
/* --------------------------------------------------------------
   paygate_notify.php 2014-04-02 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once 'includes/application_top.php';
require_once 'includes/modules/payment/paygate/paygate.php';
header('Content-Type: text/plain');

function _log($text) {
	$logger = LogControl::get_instance();
	$logger->notice($text, 'payment', 'payment.paygate');
}

function qs2array($input) {
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

function addOrderStatus($order_id, $status_id, $comment) {
	$order_query = "UPDATE orders SET orders_status = :status_id, last_modified = now() WHERE orders_id = :orders_id";
	$order_query = strtr($order_query, array(':status_id' => $status_id, ':orders_id' => $order_id));
	xtc_db_query($order_query);
	$oh_query = "INSERT INTO orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) values (:orders_id, :orders_status_id, now(), 0, ':comments')";
	$oh_query = strtr($oh_query, array(':orders_id' => $order_id, ':orders_status_id'=> $status_id, ':comments' => $comment));
	xtc_db_query($oh_query);
}


if(isset($_POST['Len']) && isset($_POST['Data'])) {
	$decoded = @mcrypt_decrypt('blowfish', MODULE_PAYMENT_PAYGATE_SSL_PASS, hex2bin($_POST['Data']), 'ecb');
	$decoded = substr($decoded, 0, (int)$_POST['Len']);
	$decoded_data = qs2array($decoded);
	_log('Notify called, POST data:'.PHP_EOL.print_r($decoded_data, true));
	$transid = explode('-', $decoded_data['TransID']);
	if(count($transid) != 2) {
		// TransID in notification does not adhere to format used by Gambio Paygate module
		_log('invalid TransID: '. $decoded_data['TransID']);
		exit;
	}
	$order_id = $transid[0];
	$order = new order($order_id);
	$payment_method = $order->info['payment_method'];
	$status_comment = $decoded_data['Code'].' - '.$decoded_data['Description'];
	$notification_type = $decoded_data['Code'][0];
	switch($notification_type) {
		case '0':
			addOrderStatus($order_id, constant('MODULE_PAYMENT_'.strtoupper($payment_method).'_ORDER_STATUS_ID'), "Paygate Status-Update: ". $status_comment);
			break;
		case '2':
			addOrderStatus($order_id, constant('MODULE_PAYMENT_'.strtoupper($payment_method).'_ORDER_STATUS_ID_FAILED'), "Paygate Status-Update: ". $status_comment);
			break;
		default:
			addOrderStatus($order_id, constant('MODULE_PAYMENT_'.strtoupper($payment_method).'_ORDER_STATUS_ID_ONGOING'), "Paygate Status-Update: ". $status_comment);
	}
}
else {
	echo "NO DATA\n\n";
	_log('Notify called without POST data');
}
