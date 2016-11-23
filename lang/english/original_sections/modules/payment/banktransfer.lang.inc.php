<?php
/* --------------------------------------------------------------
	banktransfer.lang.inc.php 2015-03-26 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_TYPE_PERMISSION' => 'bt',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE' => 'Bank Transfer',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION' => 'Payments via bank transfer',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK' => 'Bank transfer',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER' => 'Note: You can download our fax confirmation form here: ',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO' => 'Please note that bank transfer payments are <b>only</b> available from a <b>German</b> bank account!',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER' => 'Account holder:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER' => 'Account number:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ' => 'Bank code:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME' => 'Bank:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX' => 'Bank transfer payment will be confirmed by fax',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO' => '',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR' => 'ERROR:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1' => 'Account number and bank code do not match! Please check again.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2' => 'No plausibility check method available for this bank code!',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3' => 'Account number cannot be verified!',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4' => 'Account number cannot be verified! Please check again.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5' => 'Bank code not found! Please check again.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8' => 'Incorrect bank code or no bank code entered!',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9' => 'No account number entered!',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10' => 'No account holder entered!',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE' => 'Note:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2' => 'If you do not want to send your<br />account details over the Internet, you can download our ',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3' => 'fax form',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4' => ' and return it to us.',
	'JS_BANK_BLZ' => 'Please enter the bank code of your bank!\n\n',
	'JS_BANK_NAME' => 'Please enter the name of your bank!\n\n',
	'JS_BANK_NUMBER' => 'Please enter your account number!\n\n',
	'JS_BANK_OWNER' => 'Please enter the name of the account holder!\n\n',
	'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_TITLE' => 'Use database lookup for bank code?',
	'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_DESC' => 'Would you like to use database lookup for the bank code?',
	'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_TITLE' => 'Fax Url',
	'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_DESC' => 'The fax confirmation file; this should be located in the catalog dir',
	'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_TITLE' => 'Allow Fax Confirmation',
	'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_DESC' => 'Do you want to allow fax confirmation?',
	'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_TITLE' => 'Display Sort Order',
	'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_DESC' => 'Display sort order; the lowest value is displayed first.',
	'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_TITLE' => 'Set Order Status',
	'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_DESC' => 'Set the status of orders made with this payment module',
	'MODULE_PAYMENT_BANKTRANSFER_ZONE_TITLE' => 'Payment Zone',
	'MODULE_PAYMENT_BANKTRANSFER_ZONE_DESC' => 'When a zone is selected, this payment method will be enabled for that zone only.',
	'MODULE_PAYMENT_BANKTRANSFER_ALLOWED_TITLE' => 'Allowed Zones',
	'MODULE_PAYMENT_BANKTRANSFER_ALLOWED_DESC' => 'Please enter the zones <b>individually</b> that should be allowed to use this module (e.g. US, UK (leave blank to allow all zones))',
	'MODULE_PAYMENT_BANKTRANSFER_STATUS_TITLE' => 'Allow Bank Transfer Payments',
	'MODULE_PAYMENT_BANKTRANSFER_STATUS_DESC' => 'Do you want to accept bank transfer payments?',
	'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_TITLE' => 'Minimum Orders',
	'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_DESC' => 'Minimum orders for a customer to view this option.',
	'MODULE_PAYMENT_BANKTRANSFER_DATACHECK_TITLE' => 'Check bankdata?',
	'MODULE_PAYMENT_BANKTRANSFER_DATACHECK_DESC' => 'Shall the entered bank data be checked?'
);