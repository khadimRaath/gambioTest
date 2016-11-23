<?php
/* --------------------------------------------------------------
	hpppal.lang.inc.php 2015-01-02 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_HPPPAL_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_HPPPAL_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_HPPPAL_CANCELED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung abgebrochen wurde.',
	'MODULE_PAYMENT_HPPPAL_CANCELED_STATUS_ID_TITLE' => 'Bestellstatus - Abbruch',
	'MODULE_PAYMENT_HPPPAL_DEBUGTEXT' => 'Das Zahlverfahren wird gerade gewartet. Bitte wählen Sie ein anderes Zahlverfahren oder versuchen Sie es zu einem späteren Zeitpunkt.',
	'MODULE_PAYMENT_HPPPAL_DEBUG_DESC' => 'Schalten Sie diesen nur auf Anweisung von Heidelpay an, da sonst eine Bezahlung im Shop nicht mehr funktioniert.',
	'MODULE_PAYMENT_HPPPAL_DEBUG_TITLE' => 'Debug Modus',
	'MODULE_PAYMENT_HPPPAL_DIRECT_MODE_DESC' => 'Wenn Modul Mode auf DIRECT dann wählen Sie hier ob die Zahldaten auf einer Extraseite oder in einer Lightbox eingegeben werden sollen.',
	'MODULE_PAYMENT_HPPPAL_DIRECT_MODE_TITLE' => 'Direct Mode',
	'MODULE_PAYMENT_HPPPAL_MAX_AMOUNT_DESC' => 'Wählen Sie hier den Maximalbetrag <br>(Bitte in EURO-CENT d.h. 5 EUR = 500 Cent).',
	'MODULE_PAYMENT_HPPPAL_MAX_AMOUNT_TITLE' => 'Maximum Betrag',
	'MODULE_PAYMENT_HPPPAL_MIN_AMOUNT_DESC' => 'Wählen Sie hier den Mindestbetrag <br>(Bitte in EURO-CENT d.h. 5 EUR = 500 Cent).',
	'MODULE_PAYMENT_HPPPAL_MIN_AMOUNT_TITLE' => 'Minimum Betrag',
	'MODULE_PAYMENT_HPPPAL_MODULE_MODE_DESC' => 'DIRECT: Die Zahldaten werden auf der Zahlverfahrenauswahl mit REGISTER Funktion erfasst (zzgl. Registrierungsgebuehr). <br>AFTER: Die Zahldaten werden nachgelagert mit DEBIT Funktion erfasst.',
	'MODULE_PAYMENT_HPPPAL_MODULE_MODE_TITLE' => 'Modul Mode',
	'MODULE_PAYMENT_HPPPAL_NEWORDER_STATUS_ID_DESC' => 'Dieser Status wird zu Beginn der Bezahlung gesetzt.',
	'MODULE_PAYMENT_HPPPAL_NEWORDER_STATUS_ID_TITLE' => 'Bestellstatus - Neue Bestellung',
	'MODULE_PAYMENT_HPPPAL_PAY_MODE_DESC' => 'Wählen Sie zwischen Debit (DB) und Preauthorisation (PA).',
	'MODULE_PAYMENT_HPPPAL_PAY_MODE_TITLE' => 'Payment Mode',
	'MODULE_PAYMENT_HPPPAL_PENDING_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die der Kunde auf einem Fremdsystem ist.',
	'MODULE_PAYMENT_HPPPAL_PENDING_STATUS_ID_TITLE' => 'Bestellstatus - Wartend',
	'MODULE_PAYMENT_HPPPAL_PROCESSED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung erfolgreich war.',
	'MODULE_PAYMENT_HPPPAL_PROCESSED_STATUS_ID_TITLE' => 'Bestellstatus - Erfolgreich',
	'MODULE_PAYMENT_HPPPAL_SECURITY_SENDER_DESC' => 'Ihre Heidelpay Sender ID',
	'MODULE_PAYMENT_HPPPAL_SECURITY_SENDER_TITLE' => 'Sender ID',
	'MODULE_PAYMENT_HPPPAL_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_HPPPAL_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_HPPPAL_STATUS_DESC' => 'Möchten Sie das Modul aktivieren?',
	'MODULE_PAYMENT_HPPPAL_STATUS_TITLE' => 'Modul aktivieren',
	'MODULE_PAYMENT_HPPPAL_SYSTEM_DESC' => 'Wählen Sie hier das Heidelpay System.',
	'MODULE_PAYMENT_HPPPAL_SYSTEM_TITLE' => 'System',
	'MODULE_PAYMENT_HPPPAL_TEST_ACCOUNT_DESC' => 'Wenn Transaction Mode nicht LIVE, sollen folgende Accounts (EMail) testen können. (Komma getrennt)',
	'MODULE_PAYMENT_HPPPAL_TEST_ACCOUNT_TITLE' => 'Test Account',
	'MODULE_PAYMENT_HPPPAL_TEXT_DESC' => 'Pay Pal über Heidelberger Payment GmbH',
	'MODULE_PAYMENT_HPPPAL_TEXT_INFO' => '',
	'MODULE_PAYMENT_HPPPAL_TEXT_TITLE' => 'Pay Pal',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_CHANNEL_DESC' => 'Ihre Heidelpay Channel ID',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_CHANNEL_TITLE' => 'Channel ID',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_MODE_DESC' => 'Wählen Sie hier den Transaktionsmodus.',
	'MODULE_PAYMENT_HPPPAL_TRANSACTION_MODE_TITLE' => 'Transaction Mode',
	'MODULE_PAYMENT_HPPPAL_USER_LOGIN_DESC' => 'Ihr Heidelpay User Login',
	'MODULE_PAYMENT_HPPPAL_USER_LOGIN_TITLE' => 'User Login',
	'MODULE_PAYMENT_HPPPAL_USER_PWD_DESC' => 'Ihr Heidelpay User Passwort',
	'MODULE_PAYMENT_HPPPAL_USER_PWD_TITLE' => 'User Passwort',
	'MODULE_PAYMENT_HPPPAL_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_HPPPAL_ZONE_TITLE' => 'Zahlungszone'
);