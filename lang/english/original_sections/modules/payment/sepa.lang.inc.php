<?php
/* --------------------------------------------------------------
	sepa.lang.inc.php 2015-03-26 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_TYPE_PERMISSION' => 'sepa',
	'MODULE_PAYMENT_SEPA_TEXT_TITLE' => 'SEPA Bank Transfer',
	'MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION' => 'Payments via SEPA',
	'MODULE_PAYMENT_SEPA_TEXT_INFO' => '',
	'MODULE_PAYMENT_SEPA_TEXT_BANK' => 'SEPA',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_INFO' => '',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER' => 'Account holder:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN' => 'IBAN:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_BIC' => 'BIC:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_NAME' => 'Bank:',
	'MODULE_PAYMENT_SEPA_TEXT_NEW_MANDATE_HINT' => 'Hint:<br />You have chosen SEPA-debit as method of payment.<br />You recieve a SEPA direct debit mandate with this order confirmation, which has to be signed and forwarded to us.<br />Only after the mandate has been submitted, a charge can be made and the goods can be shipped.<br />Should you have further questions about SEPA payment, please contact your bank.',
	'MODULE_PAYMENT_SEPA_TEXT_EXISTING_MANDATE_HINT' => 'Hint:<br />You have chosen SEPA-debit as method of payment.<br />Since there is an existing SEPA direct debit mandate, we can debit your account and subsequently ship the goods without additional authentification.<br />Should you have further questions about SEPA payment, please contact your bank.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_FAX' => 'Account information will be sent with the SEPA Direct Debit mandate.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR' => 'ERROR:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_1' => 'Account number and bank code does not match. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_2' => 'IBAN cannot be verified. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_3' => 'IBAN cannot be verified. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4' => 'IBAN cannot be verified. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_5' => 'Bank code not found! Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_10' => 'No account holder entered. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_11' => 'No iban number entered. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_12' => 'There was no check digit specified in the IBAN. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_13' => 'There was no valid iban number entered. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_14' => 'No bic number entered. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_15' => 'There was no valid bic number entered. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_16' => 'No bankname entered. Please check again.',
	'MODULE_PAYMENT_SEPA_TEXT_NOTE' => 'Note:',
	'MODULE_PAYMENT_SEPA_TEXT_NOTE2' => 'If you do not want to send your account details over the Internet,<br />you can send us the account details with the SEPA Direct Debit mandate.',
	'JS_BANK_BLZ' => '* Please enter the BIC of your bank!\n\n',
	'JS_BANK_NAME' => '* Please enter the name of your bank!\n\n',
	'JS_BANK_NUMBER' => '* Please enter your IBAN!\n\n',
	'JS_BANK_OWNER' => '* Please enter the name of the account holder!\n\n',
	'MODULE_PAYMENT_SEPA_DATABASE_BLZ_TITLE' => 'Use database lookup for the bank code?',
	'MODULE_PAYMENT_SEPA_DATABASE_BLZ_DESC' => 'Would you like to use database lookup for the bank code?',
	'MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_TITLE' => 'Allow Fax Confirmation',
	'MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_DESC' => 'Do you want to allow fax confirmation?',
	'MODULE_PAYMENT_SEPA_SORT_ORDER_TITLE' => 'Display Sort Order',
	'MODULE_PAYMENT_SEPA_SORT_ORDER_DESC' => 'Display sort order; the lowest value is displayed first.',
	'MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_TITLE' => 'Set Order Status',
	'MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_DESC' => 'Set the status of orders made with this payment module',
	'MODULE_PAYMENT_SEPA_ZONE_TITLE' => 'Payment Zone',
	'MODULE_PAYMENT_SEPA_ZONE_DESC' => 'When a zone is selected, this payment method will be enabled for that zone only.',
	'MODULE_PAYMENT_SEPA_ALLOWED_TITLE' => 'Allowed Zones',
	'MODULE_PAYMENT_SEPA_ALLOWED_DESC' => 'Please enter the zones <b>individually</b> that should be allowed to use this module (e.g. US, UK (leave blank to allow all zones))',
	'MODULE_PAYMENT_SEPA_STATUS_TITLE' => 'Allow Sepa Payments',
	'MODULE_PAYMENT_SEPA_STATUS_DESC' => 'Do you want to accept Sepa payments?',
	'MODULE_PAYMENT_SEPA_MIN_ORDER_TITLE' => 'Minimum Orders',
	'MODULE_PAYMENT_SEPA_MIN_ORDER_DESC' => 'Minimum orders for a customer to view this option.',
	'MODULE_PAYMENT_SEPA_DATACHECK_TITLE' => 'Check bankdata?',
	'MODULE_PAYMENT_SEPA_DATACHECK_DESC' => 'Shall the entered bank data be checked?',
	'MODULE_PAYMENT_SEPA_CREDITOR_ID_TITLE' => 'Creditor identifier',
	'MODULE_PAYMENT_SEPA_CREDITOR_ID_DESC' => 'Creditor identifier',
	'MODULE_PAYMENT_SEPA_SEND_MANDATE_TITLE' => 'Send mandate form?',
	'MODULE_PAYMENT_SEPA_SEND_MANDATE_DESC' => 'Send mandate form?',
	'MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY_TITLE' => 'Communicate debit mandate reference sparately',
	'MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY_DESC' => 'Would you like to communicate the debit mandate reference separately?'
);