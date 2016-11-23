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
	'MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID_DESC' => 'ACCOUNTID des Saferpay Terminals',
	'MODULE_PAYMENT_SAFERPAYGW_ACCOUNT_ID_TITLE' => 'Saferpay-Konto',
	'MODULE_PAYMENT_SAFERPAYGW_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_SAFERPAYGW_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR_DESC' => 'Hintergrundfarbe des Saferpay VT.',
	'MODULE_PAYMENT_SAFERPAYGW_BODYCOLOR_TITLE' => 'BODYCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR_DESC' => 'Schriftfarbe des Eingabebereichs.',
	'MODULE_PAYMENT_SAFERPAYGW_BODYFONTCOLOR_TITLE' => 'BODYFONTCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_CCCVC_DESC' => 'Abfrage der Kartenprüfnummer',
	'MODULE_PAYMENT_SAFERPAYGW_CCCVC_TITLE' => 'CVC Eingabe',
	'MODULE_PAYMENT_SAFERPAYGW_CCNAME_DESC' => 'Abfrage des Karteninhabernamens',
	'MODULE_PAYMENT_SAFERPAYGW_CCNAME_TITLE' => 'Karteninhaber',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_DESC' => 'Sofortige Verbuchung der Saferpay Transaktion',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_TITLE' => 'Transaktion verbuchen',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL_DESC' => 'URL für das Abschließen der Zahlung',
	'MODULE_PAYMENT_SAFERPAYGW_COMPLETE_URL_TITLE' => 'PayComplete URL',
	'MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL_DESC' => 'URL für die Bestätigung der Zahlung',
	'MODULE_PAYMENT_SAFERPAYGW_CONFIRM_URL_TITLE' => 'PayConfirm URL',
	'MODULE_PAYMENT_SAFERPAYGW_CURRENCY_DESC' => 'Währung für die Zahlungsanfragen',
	'MODULE_PAYMENT_SAFERPAYGW_CURRENCY_TITLE' => 'Transaktionswährung',
	'MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR_DESC' => 'Hintergrundfarbe des oberen Bereichs.',
	'MODULE_PAYMENT_SAFERPAYGW_HEADCOLOR_TITLE' => 'HEADCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR_DESC' => 'Schriftfarbe der Reiter.',
	'MODULE_PAYMENT_SAFERPAYGW_HEADFONTCOLOR_TITLE' => 'HEADFONTCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR_DESC' => 'Farbe der Trennlinie oben links.',
	'MODULE_PAYMENT_SAFERPAYGW_HEADLINECOLOR_TITLE' => 'HEADLINECOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR_DESC' => 'Schriftfarbe der Links.',
	'MODULE_PAYMENT_SAFERPAYGW_LINKCOLOR_TITLE' => 'LINKCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_LOGIN_DESC' => 'Loginname, welches für Saferpay verwendet wird',
	'MODULE_PAYMENT_SAFERPAYGW_LOGIN_TITLE' => 'Saferpay-Loginname',
	'MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR_DESC' => 'Farbe inaktiver Reiter.',
	'MODULE_PAYMENT_SAFERPAYGW_MENUCOLOR_TITLE' => '<hr>Styling-Attribute zur farblichen Anpassung des Saferpay VT (optional) <a href="html/assets/images/legacy/saferpaygw_styling.jpg" target=_blank><img src="images/icons/graphics/unknown.jpg" width="15" border="0" alt="Hilfe"></a><br /><br />MENUCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR_DESC' => 'Schriftfarbe des Menüs.',
	'MODULE_PAYMENT_SAFERPAYGW_MENUFONTCOLOR_TITLE' => 'MENUFONTCOLOR',
	'MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID_DESC' => 'Mit Saferpay bezahlte Bestellungen, auf diesen Status setzen',
	'MODULE_PAYMENT_SAFERPAYGW_ORDER_STATUS_ID_TITLE' => 'Bestellstatus festlegen',
	'MODULE_PAYMENT_SAFERPAYGW_PASSWORD_DESC' => 'Passwort welches für Saferpay verwendet wird',
	'MODULE_PAYMENT_SAFERPAYGW_PASSWORD_TITLE' => 'Saferpay-Passwort',
	'MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL_DESC' => 'URL für die Initialisierung der Zahlung',
	'MODULE_PAYMENT_SAFERPAYGW_PAYINIT_URL_TITLE' => 'PayInit URL',
	'MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_SAFERPAYGW_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_SAFERPAYGW_STATUS_DESC' => 'Möchten Sie Zahlungen per Saferpay akzeptieren?',
	'MODULE_PAYMENT_SAFERPAYGW_STATUS_TITLE' => 'Saferpay Modul aktivieren',
	'MODULE_PAYMENT_SAFERPAYGW_TEXT_DESCRIPTION' => '<b>Saferpay-Zahlung</b><br />%s<br /><b>Saferpay Testkonto</b><br />ACCOUNTID: 99867-94913159<br />Normale Testkarte: 9451123100000004<br />Gültig bis: 12/10, CVC 123<br /><br />Testkarte für 3D-Secure: 9451123100000111<br />Gültig bis: 12/10, CVC 123<br /><br /><b>Login für das Backoffice<br />auf www.saferpay.com:</b><br />Benutzername: e99867001<br />Passwort: XAjc3Kna<br /><hr>',
	'MODULE_PAYMENT_SAFERPAYGW_TEXT_SHOW_TRANSACTION' => 'Zu Transaktionen',
	'MODULE_PAYMENT_SAFERPAYGW_TEXT_TITLE' => 'Kreditkarte, Lastschrift<br />Sicheres Bezahlen mit Saferpay',
	'MODULE_PAYMENT_SAFERPAYGW_URLREADER_DESC' => 'Welche Methode soll benutzt werden um URL zu lesen?',
	'MODULE_PAYMENT_SAFERPAYGW_URLREADER_TITLE' => 'Funktion für URL-Lesen',
	'MODULE_PAYMENT_SAFERPAYGW_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_SAFERPAYGW_ZONE_TITLE' => '<hr><br />Zahlungszone',
	'SAFERPAYGW_ERROR_HEADING' => 'Ein Fehler bei Verbindung zum Saferpay Gateway.',
	'SAFERPAYGW_ERROR_MESSAGE' => 'Bitte kontrollieren Sie die Daten Ihrer Kreditkarte!',
	'TEXT_SAFERPAYGW_CAPTURING_ERROR' => 'There has been an error capturing your credit card',
	'TEXT_SAFERPAYGW_CONFIRMATION_ERROR' => 'There has been an error confirmation your payment',
	'TEXT_SAFERPAYGW_SETUP_ERROR' => 'There has been an error creating request! Please check your setings!'
);