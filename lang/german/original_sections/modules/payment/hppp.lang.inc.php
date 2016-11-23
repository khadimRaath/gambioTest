<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_HPPP_TEXT_TITLE' => 'Vorkasse',
	'MODULE_PAYMENT_HPPP_TEXT_DESC' => 'Vorkasse &uuml;ber Heidelberger Payment GmbH',
	'MODULE_PAYMENT_HPPP_TEXT_INFO' => '',
	'MODULE_PAYMENT_HPPP_TEST_ACCOUNT_TITLE' => 'Test Account',
	'MODULE_PAYMENT_HPPP_TEST_ACCOUNT_DESC' => 'Im Sandbox Mode sollen folgende E-Mail-Accounts testen können. (Komma getrennt)',
	'MODULE_PAYMENT_HPPP_PROCESSED_STATUS_ID_TITLE' => 'Bestellstatus - Erfolgreich',
	'MODULE_PAYMENT_HPPP_PROCESSED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung erfolgreich war.',
	'MODULE_PAYMENT_HPPP_PENDING_STATUS_ID_TITLE' => 'Bestellstatus - Wartend',
	'MODULE_PAYMENT_HPPP_PENDING_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die der Kunde auf einem Fremdsystem ist.',
	'MODULE_PAYMENT_HPPP_CANCELED_STATUS_ID_TITLE' => 'Bestellstatus - Abbruch',
	'MODULE_PAYMENT_HPPP_CANCELED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung abgebrochen wurde.',
	'MODULE_PAYMENT_HPPP_STATUS_TITLE' => 'Modul aktivieren',
	'MODULE_PAYMENT_HPPP_STATUS_DESC' => 'Möchten Sie das Modul aktivieren?',
	'MODULE_PAYMENT_HPPP_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_HPPP_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_HPPP_ZONE_TITLE' => 'Zahlungszone',
	'MODULE_PAYMENT_HPPP_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_HPPP_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_HPPP_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_HPPP_EMAIL_TEXT' => '<b>Bitte &uuml;berweisen Sie uns den Betrag von {CURRENCY} {AMOUNT} auf folgendes Konto:</b><br><br>
	Land :         {ACC_COUNTRY}<br>
	Kontoinhaber : {ACC_OWNER}<br>
	Konto-Nr. :    {ACC_NUMBER}<br>
	Bankleitzahl:  {ACC_BANKCODE}<br>
	IBAN:   	   {ACC_IBAN}<br>
	BIC:           {ACC_BIC}<br>
	<br><br><b>Geben sie bitte im Verwendungszweck UNBEDINGT die Identifikationsnummer
	{SHORTID}
	und NICHTS ANDERES an.</b>'
);