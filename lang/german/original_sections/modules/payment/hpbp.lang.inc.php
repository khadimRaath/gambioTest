<?php
/* --------------------------------------------------------------
	hpbp.lang.inc.php 2015-04-29 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_HPBP_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_HPBP_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_HPBP_CANCELED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung abgebrochen wurde.',
	'MODULE_PAYMENT_HPBP_CANCELED_STATUS_ID_TITLE' => 'Bestellstatus - Abbruch',
	'MODULE_PAYMENT_HPBP_DEBUG_DESC' => 'Schalten Sie diesen nur auf Anweisung von Heidelpay an, da sonst eine Bezahlung im Shop nicht mehr funktioniert.',
	'MODULE_PAYMENT_HPBP_DEBUG_TITLE' => 'Debug Modus',
	'MODULE_PAYMENT_HPBP_DEBUGTEXT' => 'Das Zahlverfahren wird gerade gewartet. Bitte wählen Sie ein anderes Zahlverfahren oder versuchen Sie es zu einem späteren Zeitpunkt.',
	'MODULE_PAYMENT_HPBP_DIRECT_MODE_DESC' => 'Wenn Modul Mode auf DIRECT dann wählen Sie hier ob die Zahldaten auf einer Extraseite oder in einer Lightbox eingegeben werden sollen.',
	'MODULE_PAYMENT_HPBP_DIRECT_MODE_TITLE' => 'Direct Mode',
	'MODULE_PAYMENT_HPBP_EMAIL_TEXT' => '<a href="{BARPAY_PAYCODE_URL}" target="_blank" class="button-right large">{BARPAY_PAYCODE_URL}</a> <br /><br />Drucken Sie den Barcode aus oder speichern Sie diesen auf Ihrem mobilen Endgerät. Gehen Sie nun zu einer Kasse der <b>18.000 Akzeptanzstellen in Deutschland</b> und bezahlen Sie ganz einfach in bar. <br/><br/>In dem Augenblick, wenn der Rechnungsbetrag beglichen wird, erhält der Online-Händler die Information über den Zahlungseingang. Die bestellte Ware oder Dienstleistung geht umgehend in den Versand.',
	'MODULE_PAYMENT_HPBP_MAX_AMOUNT_DESC' => 'Wählen Sie hier den Maximalbetrag <br>(Bitte in EURO-CENT d.h. 5 EUR = 500 Cent).',
	'MODULE_PAYMENT_HPBP_MAX_AMOUNT_TITLE' => 'Maximum Betrag',
	'MODULE_PAYMENT_HPBP_MIN_AMOUNT_DESC' => 'Wählen Sie hier den Mindestbetrag <br>(Bitte in EURO-CENT d.h. 5 EUR = 500 Cent).',
	'MODULE_PAYMENT_HPBP_MIN_AMOUNT_TITLE' => 'Minimum Betrag',
	'MODULE_PAYMENT_HPBP_MODULE_MODE_DESC' => 'DIRECT: Die Zahldaten werden auf der Zahlverfahrenauswahl mit REGISTER Funktion erfasst (zzgl. Registrierungsgebuehr). <br>AFTER: Die Zahldaten werden nachgelagert mit DEBIT Funktion erfasst.',
	'MODULE_PAYMENT_HPBP_MODULE_MODE_TITLE' => 'Modul Mode',
	'MODULE_PAYMENT_HPBP_NEWORDER_STATUS_ID_DESC' => 'Dieser Status wird zu Beginn der Bezahlung gesetzt.',
	'MODULE_PAYMENT_HPBP_NEWORDER_STATUS_ID_TITLE' => 'Bestellstatus - Neue Bestellung',
	'MODULE_PAYMENT_HPBP_PAY_MODE_DESC' => 'Wählen Sie zwischen Debit (DB) und Preauthorisation (PA).',
	'MODULE_PAYMENT_HPBP_PAY_MODE_TITLE' => 'Payment Mode',
	'MODULE_PAYMENT_HPBP_PENDING_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die der Kunde auf einem Fremdsystem ist.',
	'MODULE_PAYMENT_HPBP_PENDING_STATUS_ID_TITLE' => 'Bestellstatus - Wartend',
	'MODULE_PAYMENT_HPBP_PROCESSED_STATUS_ID_DESC' => 'Dieser Status wird gesetzt wenn die Bezahlung erfolgreich war.',
	'MODULE_PAYMENT_HPBP_PROCESSED_STATUS_ID_TITLE' => 'Bestellstatus - Erfolgreich',
	'MODULE_PAYMENT_HPBP_SECURITY_SENDER_DESC' => 'Ihre Heidelpay Sender ID',
	'MODULE_PAYMENT_HPBP_SECURITY_SENDER_TITLE' => 'Sender ID',
	'MODULE_PAYMENT_HPBP_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_HPBP_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_HPBP_STATUS_DESC' => 'Möchten Sie das Modul aktivieren?',
	'MODULE_PAYMENT_HPBP_STATUS_TITLE' => 'Modul aktivieren',
	'MODULE_PAYMENT_HPBP_SYSTEM_DESC' => 'Wählen Sie hier das Heidelpay System.',
	'MODULE_PAYMENT_HPBP_SYSTEM_TITLE' => 'System',
	'MODULE_PAYMENT_HPBP_TEST_ACCOUNT_DESC' => 'Im Sandbox Mode sollen folgende E-Mail-Accounts testen können. (Komma getrennt)',
	'MODULE_PAYMENT_HPBP_TEST_ACCOUNT_TITLE' => 'Test Account',
	'MODULE_PAYMENT_HPBP_TEXT_DESC' => 'BarPay über Heidelberger Payment GmbH',
	'MODULE_PAYMENT_HPBP_TEXT_INFO' => 'Sicher, schnell und ohne Gebühren: mit BarPay zahlen Sie Internet-Einkäufe mit Bargeld. Ohne Anmeldung. Ohne Kreditkarte. Ohne Kontodetails. 		<br /><br />Nach Auswahl von BarPay übermittelt Ihnen Ihr Online-Händler einen individuellen Barcode per E-Mail oder zum Download auf Ihren Computer. Diesen können Sie ausdrucken und in über 18.000 BarPay-Akzeptanzstellen bezahlen. Der Zahlungseingang wird dem Online-Händler in Echtzeit übermittelt, und die bestellte Ware geht umgehend in den Versand. <br /><br /> 		<a href="http://www.barpay.de/info/" onclick="window.open(this.href,\'Popup\',\'width=580,height=550,scrollbars=no\');return false;"><img src="./images/BarPay.jpg" style="border:0"/></a>',
	'MODULE_PAYMENT_HPBP_TEXT_TITLE' => 'BarPay',
	'MODULE_PAYMENT_HPBP_TRANSACTION_CHANNEL_DESC' => 'Ihre Heidelpay Channel ID',
	'MODULE_PAYMENT_HPBP_TRANSACTION_CHANNEL_TITLE' => 'Channel ID',
	'MODULE_PAYMENT_HPBP_TRANSACTION_MODE_DESC' => 'Wählen Sie hier den Transaktionsmodus.',
	'MODULE_PAYMENT_HPBP_TRANSACTION_MODE_TITLE' => 'Transaction Mode',
	'MODULE_PAYMENT_HPBP_USER_LOGIN_DESC' => 'Ihr Heidelpay User Login',
	'MODULE_PAYMENT_HPBP_USER_LOGIN_TITLE' => 'User Login',
	'MODULE_PAYMENT_HPBP_USER_PWD_DESC' => 'Ihr Heidelpay User Passwort',
	'MODULE_PAYMENT_HPBP_USER_PWD_TITLE' => 'User Passwort',
	'MODULE_PAYMENT_HPBP_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_HPBP_ZONE_TITLE' => 'Zahlungszone'
);