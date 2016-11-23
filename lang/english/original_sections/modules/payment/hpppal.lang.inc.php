<?php
/* --------------------------------------------------------------
	hpppal.lang.inc.php 2015-01-05 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_HPPPAL_TEXT_TITLE' => 'Pay Pal',
	'MODULE_PAYMENT_HPPPAL_TEXT_DESC' => 'Pay Pal over Heidelberger Payment GmbH',
	'MODULE_PAYMENT_HPPPAL_SECURITY_SENDER_TITLE' => 'Sender ID',
	'MODULE_PAYMENT_HPPPAL_SECURITY_SENDER_DESC' => 'Your Heidelpay Sender ID',
	'MODULE_PAYMENT_HPPPAL_USER_LOGIN_TITLE' => 'User Login',
	'MODULE_PAYMENT_HPPPAL_USER_LOGIN_DESC' => 'Your Heidelpay User Login',
	'MODULE_PAYMENT_HPPPAL_USER_PWD_TITLE' => 'User Password',
	'MODULE_PAYMENT_HPPPAL_USER_PWD_DESC' => 'Your Heidelpay User Password',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_CHANNEL_TITLE' => 'Channel ID',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_CHANNEL_DESC' => 'Your Heidelpay Channel ID',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_MODE_TITLE' => 'Transaction Mode',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_MODE_DESC' => 'Please choose your transaction mode.',
	'MODULE_PAYMENT_HPPPAL_SYSTEM_TITLE' => 'System',
	'MODULE_PAYMENT_HPPPAL_SYSTEM_DESC' => 'Please choose the Heidelpay System.',
	'MODULE_PAYMENT_HPPPAL_MIN_AMOUNT_TITLE' => 'Minimum Amount',
	'MODULE_PAYMENT_HPPPAL_MIN_AMOUNT_DESC' => 'Please choose the minimum amount <br>(in EURO-CENT e.g. 5 EUR = 500 Cent).',
	'MODULE_PAYMENT_HPPPAL_MAX_AMOUNT_TITLE' => 'Maximum Amount',
	'MODULE_PAYMENT_HPPPAL_MAX_AMOUNT_DESC' => 'Please choose the maximum amount <br>(in EURO-CENT e.g. 5 EUR = 500 Cent).',
	'MODULE_PAYMENT_HPPPAL_MODULE_MODE_TITLE' => 'Module Mode',
	'MODULE_PAYMENT_HPPPAL_MODULE_MODE_DESC' => 'DIRECT: Paymentinformations will be entered on payment selection with REGISTER function (plus Registerfee). <br>AFTER: Paymentinformations will be entered after process with DEBIT function.',
	'MODULE_PAYMENT_HPPPAL_DIRECT_MODE_TITLE' => 'Direct Mode',
	'MODULE_PAYMENT_HPPPAL_DIRECT_MODE_DESC' => 'If Modul Mode is on DIRECT you can decide if the paymentdata should be entered on an extra site or a lightbox.',
	'MODULE_PAYMENT_HPPPAL_PAY_MODE_TITLE' => 'Payment Mode',
	'MODULE_PAYMENT_HPPPAL_PAY_MODE_DESC' => 'Select between Debit (DB) and Preauthorisation (PA).',
	'MODULE_PAYMENT_HPPPAL_TEST_ACCOUNT_TITLE' => 'Test Account',
	'MODULE_PAYMENT_HPPPAL_TEST_ACCOUNT_DESC' => 'If Transaction Mode is not LIVE, the following Accounts (EMail) can test the payment. (Comma separated)',
	'MODULE_PAYMENT_HPPPAL_PROCESSED_STATUS_ID_TITLE' => 'Orderstatus - Success',
	'MODULE_PAYMENT_HPPPAL_PROCESSED_STATUS_ID_DESC' => 'Order Status which will be set in case of successfully payment',
	'MODULE_PAYMENT_HPPPAL_PENDING_STATUS_ID_TITLE' => 'Bestellstatus - Waiting',
	'MODULE_PAYMENT_HPPPAL_PENDING_STATUS_ID_DESC' => 'Order Status which will be set when the customer is on foreign system',
	'MODULE_PAYMENT_HPPPAL_CANCELED_STATUS_ID_TITLE' => 'Orderstatus - Cancel',
	'MODULE_PAYMENT_HPPPAL_CANCELED_STATUS_ID_DESC' => 'Order Status which will be set in case of cancel payment',
	'MODULE_PAYMENT_HPPPAL_NEWORDER_STATUS_ID_TITLE' => 'Orderstatus - New Order',
	'MODULE_PAYMENT_HPPPAL_NEWORDER_STATUS_ID_DESC' => 'Order Status which will be set in case of beginning payment',
	'MODULE_PAYMENT_HPPPAL_STATUS_TITLE' => 'Activate Module',
	'MODULE_PAYMENT_HPPPAL_STATUS_DESC' => 'Do you want to activate the module?',
	'MODULE_PAYMENT_HPPPAL_SORT_ORDER_TITLE' => 'Sort Order',
	'MODULE_PAYMENT_HPPPAL_SORT_ORDER_DESC' => 'Sort order for display. Lowest will be shown first.',
	'MODULE_PAYMENT_HPPPAL_ZONE_TITLE' => 'Paymentzone',
	'MODULE_PAYMENT_HPPPAL_ZONE_DESC' => 'If a zone is selected, only enable this payment method for that zone.',
	'MODULE_PAYMENT_HPPPAL_ALLOWED_TITLE' => 'Allowed Zones',
	'MODULE_PAYMENT_HPPPAL_ALLOWED_DESC' => 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))',
	'MODULE_PAYMENT_HPPPAL_DEBUG_TITLE' => 'Debug Mode',
	'MODULE_PAYMENT_HPPPAL_DEBUG_DESC' => 'Please activate only if heidelpay told this to you. Otherwise the checkout will not work in your shop correctly.',
	'MODULE_PAYMENT_HPPPAL_TEXT_INFO' => '',
	'MODULE_PAYMENT_HPPPAL_DEBUGTEXT' => 'The payment is temporary not available. Please use another one or try again later.'
);