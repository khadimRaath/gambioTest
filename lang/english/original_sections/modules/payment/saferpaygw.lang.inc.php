<?php
/* --------------------------------------------------------------
	saferpaygw.lang.inc.php 2016-10-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_SAFERPAYGW_TEXT_TITLE' => 'Credit Card, Direct Debit<br />payment using Saferpay',
	'MODULE_PAYMENT_SAFERPAYGW_TEXT_DESCRIPTION' => '<b>Saferpay-Payment</b><br />%s<br /><b>Saferpay Test account</b><br />ACCOUNTID: 99867-94913159<br />Test card: 9451123100000004<br />valid to: 12/10, CVC 123<br /><br />Test card for 3D-Secure: 9451123100000111<br />Valid to: 12/10, CVC 123<br /><br /><b>Login to Backoffice<br /><a href="http://www.saferpay.com">www.saferpay.com:</a></b><br />User: e99867001<br />Password: XAjc3Kna',
	'MODULE_PAYMENT_SAFERPAYGW_TEXT_SHOW_TRANSACTION' => 'Show transactions',
	'SAFERPAYGW_ERROR_HEADING' => 'There has been an error connecting to saferpay server.',
	'SAFERPAYGW_ERROR_MESSAGE' => 'Please check your credit card details!',
	'TEXT_SAFERPAYGW_CONFIRMATION_ERROR' => 'There has been an error confirmation your payment',
	'TEXT_SAFERPAYGW_CAPTURING_ERROR' => 'There has been an error capturing your credit card',
	'TEXT_SAFERPAYGW_SETUP_ERROR' => 'There has been an error creating request! Please check your setings!',
	'MODULE_PAYMENT_SAFERPAYGW_STATUS_TITLE' => 'Enable Saferpay Module',
	'MODULE_PAYMENT_SAFERPAYGW_STATUS_DESC' => 'Do you want to accept Saferpay payments?',
	'MODULE_PAYMENT_SAFERPAYGW_ALLOWED_TITLE' => 'Allowed Zones',
	'MODULE_PAYMENT_SAFERPAYGW_ALLOWED_DESC' => 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))',
	'MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER_TITLE' => 'Sort order of display',
	'MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER_DESC' => 'Sort order of display. Lowest is displayed first.',
	'MODULE_PAYMENT_SAFERPAYGW_ZONE_TITLE' => '<hr><br />Payment Zone',
	'MODULE_PAYMENT_SAFERPAYGW_ZONE_DESC' => 'If a zone is selected, only enable this payment method for that zone.',
	'MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID_TITLE' => 'Set Order Status',
	'MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID_DESC' => 'Set the status of orders made with this payment module to this value',
	'MODULE_PAYMENT_SAFERPAYGW_CURRENCY_TITLE' => 'Transaction Currency',
	'MODULE_PAYMENT_SAFERPAYGW_CURRENCY_DESC' => 'The currency to use for credit card transactions',
	'MODULE_PAYMENT_SAFERPAYGW_LOGIN_TITLE' => 'Account Number',
	'MODULE_PAYMENT_SAFERPAYGW_LOGIN_DESC' => 'The account number used for the saferpay service',
	'MODULE_PAYMENT_SAFERPAYGW_PASSWORD_TITLE' => 'User Password',
	'MODULE_PAYMENT_SAFERPAYGW_PASSWORD_DESC' => 'The user password for the saferpay service',
	'MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID_TITLE' => 'User ID',
	'MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID_DESC' => 'The user ID for the saferpay service',
	'MODULE_PAYMENT_SAFERPAYGW_URLREADER_TITLE' => 'Function to read URL',
	'MODULE_PAYMENT_SAFERPAYGW_URLREADER_DESC' => 'Whisch method should be used to read URLs',
	'MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL_TITLE' => 'PayInit URL',
	'MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL_DESC' => 'URL für Initialisierung der Zahlung',
	'MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL_TITLE' => 'PayConfirm URL',
	'MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL_DESC' => 'URL für Bestätigung der Zahlung',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL_TITLE' => 'PayComplete URL',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL_DESC' => 'URL für Abschliesen der Zahlung',
	'MODULE_PAYMENT_SAFERPAYGW_CCCVC_TITLE' => 'CVC Eingabe',
	'MODULE_PAYMENT_SAFERPAYGW_CCCVC_DESC' => 'Ist die CVC Eingabe erforderlich?',
	'MODULE_PAYMENT_SAFERPAYGW_CCNAME_TITLE' => 'Karteninhaber',
	'MODULE_PAYMENT_SAFERPAYGW_CCNAME_DESC' => 'Ist die Eingabe des Karteninhabers erforderlich?',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_TITLE' => 'Complete transaction?',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_DESC' => 'Should Saferpay transaction be completed?',
	'MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR_TITLE' => '<hr>Styling params of Saferpay VT (mandatory) <a href="html/assets/images/legacy/saferpaygw_styling.jpg" target=_blank><img src="images/icons/graphics/unknown.jpg" width="15" border="0" alt="Help"></a><br /><br />MENUCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR_DESC' => 'Specifies the color of the VT menu bar background.',
	'MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR_TITLE' => 'MENUFONTCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR_DESC' => 'Specifies the font color of Saferpay VT menu.',
	'MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR_TITLE' => 'BODYFONTCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR_DESC' => 'Specifies the font color of the VT body area.',
	'MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR_TITLE' => 'BODYCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR_DESC' => 'Specifies the color of the VT body in HTML format.',
	'MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR_TITLE' => 'HEADFONTCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR_DESC' => 'Specifies the font color of the VT head.',
	'MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR_TITLE' => 'HEADCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR_DESC' => 'Specifies the color of the header of the VT header.',
	'MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR_TITLE' => 'HEADLINECOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR_DESC' => 'Specifies the color of the head-line.',
	'MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR_TITLE' => 'LINKCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR_DESC' => 'Specifies the font color of the links of the body area.'
);