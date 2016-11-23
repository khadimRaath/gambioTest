<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_HPBS_TEXT_TITLE' => 'Kauf auf Rechnung',
	'MODULE_PAYMENT_HPBS_TEXT_DESC' => 'BillSAFE &uuml;ber Heidelberger Payment GmbH',
	'MODULE_PAYMENT_HPBS_TEXT_INFO' => 'Kaufen Sie jetzt auf Rechnung und begutachten Sie Ihre Eink&auml;ufe in Ruhe bevor Sie bezahlen. 
		<br/><br/><a title="Ihre Vorteile" href="http://www.billsafe.de/special/payment-info" target="_blank"><img src="https://images.billsafe.de/image/image/id/191997712fbe" style="border:0"/></a>',
	'MODULE_PAYMENT_HPBS_TEST_ACCOUNT_TITLE' => 'Test Account',
	'MODULE_PAYMENT_HPBS_TEST_ACCOUNT_DESC' => 'Im Sandbox Mode sollen folgende E-Mail-Accounts testen können. (Komma getrennt)',
	'MODULE_PAYMENT_HPBS_PROCESSED_STATUS_ID_TITLE' => 'Bestellstatus - Erfolgreich',
	'MODULE_PAYMENT_HPBS_PROCESSED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung erfolgreich war.',
	'MODULE_PAYMENT_HPBS_PENDING_STATUS_ID_TITLE' => 'Bestellstatus - Wartend',
	'MODULE_PAYMENT_HPBS_PENDING_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die der Kunde auf einem Fremdsystem ist.',
	'MODULE_PAYMENT_HPBS_CANCELED_STATUS_ID_TITLE' => 'Bestellstatus - Abbruch',
	'MODULE_PAYMENT_HPBS_CANCELED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung abgebrochen wurde.',
	'MODULE_PAYMENT_HPBS_STATUS_TITLE' => 'Modul aktivieren',
	'MODULE_PAYMENT_HPBS_STATUS_DESC' => 'Möchten Sie das Modul aktivieren?',
	'MODULE_PAYMENT_HPBS_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_HPBS_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_HPBS_ZONE_TITLE' => 'Zahlungszone',
	'MODULE_PAYMENT_HPBS_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_HPBS_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_HPBS_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_HPBS_EMAIL_TEXT' => '{LEGALNOTE}<br><br>
	<b>Bitte &uuml;berweisen Sie uns den Betrag von {CURRENCY} {AMOUNT} auf folgendes Konto:</b><br>
	<br>
	Empf&auml;nger: {ACC_OWNER} <br>
	Kontonr.:  {ACC_NUMBER} <br>
	BLZ: {ACC_BANKCODE} <br>
	Bank: {ACC_BANKNAME} <br>
	IBAN: {ACC_IBAN}<br>
	BIC: {ACC_BIC}<br>
	<br>
	Verwendungszweck 1: {ACC_REFEREBCE} <br>
	Verwendungszweck 2: {ACC_SHOPNAME} <br>
	<br>
	<b>Bitte beachten Sie, dass der Betrag sp&auml;testens {ACC_PERIOD} Tagen nach dem Versand angewiesen werden muss.</b>'
);