<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_HPBS_TEXT_TITLE' => 'BillSAFE',
	'MODULE_PAYMENT_HPBS_TEXT_DESC' => 'BillSAFE over Heidelberger Payment GmbH',
	'MODULE_PAYMENT_HPBS_TEXT_INFO' => '',
	'MODULE_PAYMENT_HPBS_TEST_ACCOUNT_TITLE' => 'Test Account',
	'MODULE_PAYMENT_HPBS_TEST_ACCOUNT_DESC' => 'In sandbox mode the following e-mail accounts can test the payment. (Comma separated)',
	'MODULE_PAYMENT_HPBS_PROCESSED_STATUS_ID_TITLE' => 'Order status - Success',
	'MODULE_PAYMENT_HPBS_PROCESSED_STATUS_ID_DESC' => 'Order status which will be set in case of successfully payment',
	'MODULE_PAYMENT_HPBS_PENDING_STATUS_ID_TITLE' => 'Order status - Waiting',
	'MODULE_PAYMENT_HPBS_PENDING_STATUS_ID_DESC' => 'Order status which will be set when the customer is on foreign system.',
	'MODULE_PAYMENT_HPBS_CANCELED_STATUS_ID_TITLE' => 'Order status - Cancel',
	'MODULE_PAYMENT_HPBS_CANCELED_STATUS_ID_DESC' => 'Order status which will be set in case of cancel payment.',
	'MODULE_PAYMENT_HPBS_STATUS_TITLE' => 'activate module',
	'MODULE_PAYMENT_HPBS_STATUS_DESC' => 'Do you want to activate the module?',
	'MODULE_PAYMENT_HPBS_SORT_ORDER_TITLE' => 'Sort Order',
	'MODULE_PAYMENT_HPBS_SORT_ORDER_DESC' => 'Sort order for display. Lowest will be shown first.',
	'MODULE_PAYMENT_HPBS_ZONE_TITLE' => 'Paymentzone',
	'MODULE_PAYMENT_HPBS_ZONE_DESC' => 'If a zone is selected, only enable this payment method for that zone.',
	'MODULE_PAYMENT_HPBS_ALLOWED_TITLE' => 'Allowed zones',
	'MODULE_PAYMENT_HPBS_ALLOWED_DESC' => 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))',
	'MODULE_PAYMENT_HPBS_EMAIL_TEXT' => '{LEGALNOTE}<br><br>
	<b>Please transfer the amount of {CURRENCY} {AMOUNT} to the following account:</b><br>
	<br>
	Account holde: {ACC_OWNER} <br>
	Account No.:  {ACC_NUMBER} <br>
	Bank Code: {ACC_BANKCODE} <br>
	Bank Name: {ACC_BANKNAME} <br>
	IBAN: {ACC_IBAN}<br>
	BIC: {ACC_BIC}<br>
	<br>
	Descriptor 1: {ACC_REFEREBCE} <br>
	Descriptor 2: {ACC_SHOPNAME} <br>
	<br>
	<b>Please note that you have to transfer the amount at least {ACC_PERIOD} days after the delivery.</b>'
);