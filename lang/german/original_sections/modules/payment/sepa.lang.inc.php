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
	'JS_BANK_BIC' => '* Bitte geben Sie die BIC Ihrer Bank ein!\n\n',
	'JS_BANK_IBAN' => '* Bitte geben Sie Ihre IBAN ein!\n\n',
	'JS_BANK_NAME' => '* Bitte geben Sie den Namen Ihrer Bank ein!\n\n',
	'JS_BANK_OWNER' => '* Bitte geben Sie den Namen des Kontobesitzers ein!\n\n',
	'MODULE_PAYMENT_SEPA_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_SEPA_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY_DESC' => 'Möchten Sie die Mandat-Referenz separat mitteilen?',
	'MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY_TITLE' => 'Mandat-Referenz separat mitteilen',
	'MODULE_PAYMENT_SEPA_CREDITOR_ID_DESC' => 'Geben Sie hier Ihre Gläubiger-ID ein.',
	'MODULE_PAYMENT_SEPA_CREDITOR_ID_TITLE' => 'Gläubiger-ID',
	'MODULE_PAYMENT_SEPA_DATABASE_BLZ_DESC' => 'Möchten Sie die Datenbanksuche für die BLZ verwenden?',
	'MODULE_PAYMENT_SEPA_DATABASE_BLZ_TITLE' => 'Datenbanksuche für die BLZ verwenden?',
	'MODULE_PAYMENT_SEPA_DATACHECK_DESC' => 'Sollen die eingegebenen Bankdaten überprüft werden?',
	'MODULE_PAYMENT_SEPA_DATACHECK_TITLE' => 'Bankdaten prüfen?',
	'MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_DESC' => 'Möchten Sie die Fax Bestätigung erlauben?',
	'MODULE_PAYMENT_SEPA_FAX_CONFIRMATION_TITLE' => 'Fax Bestätigung erlauben',
	'MODULE_PAYMENT_SEPA_MIN_ORDER_DESC' => 'Die Mindestanzahl an Bestellungen die ein Kunden haben muss damit die Option zur Verfügung steht.',
	'MODULE_PAYMENT_SEPA_MIN_ORDER_TITLE' => 'Notwendige Bestellungen',
	'MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_DESC' => 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen',
	'MODULE_PAYMENT_SEPA_ORDER_STATUS_ID_TITLE' => 'Bestellstatus festlegen',
	'MODULE_PAYMENT_SEPA_SEND_MANDATE_DESC' => 'Möchten Sie das Mandat-Formular mit der Bestellbestätigung senden?',
	'MODULE_PAYMENT_SEPA_SEND_MANDATE_TITLE' => 'Mandat-Formular senden?',
	'MODULE_PAYMENT_SEPA_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_SEPA_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_SEPA_STATUS_DESC' => 'Möchten Sepa Zahlungen erlauben?',
	'MODULE_PAYMENT_SEPA_STATUS_TITLE' => 'Sepa Zahlungen erlauben',
	'MODULE_PAYMENT_SEPA_TEXT_BANK' => 'SEPA',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_BIC' => 'BIC:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR' => 'FEHLER: ',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_1' => 'IBAN und BLZ stimmen nicht überein, bitte korrigieren Sie Ihre Angabe.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_10' => 'Sie haben keinen Kontoinhaber angegeben.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_11' => 'Sie haben keine IBAN angegeben.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_12' => 'Die angegebene IBAN enthält keine Prüfziffer. Bitte kontrollieren Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_13' => 'Sie haben keine korrekte IBAN eingegeben.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_14' => 'Sie haben keine BIC angegeben.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_15' => 'Sie haben keine korrekte BIC eingegeben.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_16' => 'Sie haben keinen Banknamen angegeben.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_2' => 'Diese IBAN ist nicht prüfbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_3' => 'Diese IBAN ist nicht prüfbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4' => 'Diese IBAN ist nicht prüfbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_5' => 'Die aus der IBAN resultierende BLZ existiert nicht, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_FAX' => 'Kontodaten werden mit dem SEPA-Lastschriftmandat gesendet.',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN' => 'IBAN:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_INFO' => '',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_NAME' => 'Bank:',
	'MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER' => 'Kontoinhaber:',
	'MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION' => 'SEPA',
	'MODULE_PAYMENT_SEPA_TEXT_EXISTING_MANDATE_HINT' => 'Hinweis:<br />Sie haben SEPA-Lastschrift als Zahlungsweise ausgewählt. Da bereits ein SEPA-Lastschriftmandat für Ihr angegebenes Konto vorliegt,<br />können wir die Lastschrift ohne zusätzliche Authentifizierung durchführen und die Ware im Anschluss versenden.<br />Sollten Sie weitere Fragen zu SEPA-Zahlungen haben, wenden Sie sich bitte an Ihr Kreditinstitut.',
	'MODULE_PAYMENT_SEPA_TEXT_INFO' => '',
	'MODULE_PAYMENT_SEPA_TEXT_NEW_MANDATE_HINT' => 'Hinweis:<br />Sie haben SEPA-Lastschrift als Zahlungsweise ausgewählt. Mit dieser Bestellbestätigung erhalten Sie ein SEPA-Lastschriftmandat,<br />welches unterschrieben an uns zurückgeschickt werden muss.<br />Erst nachdem das Mandat eingereicht wurde, kann die Lastschrift unsererseits erfolgen und die Ware versendet werden.<br />Sollten Sie weitere Fragen zu SEPA-Zahlungen haben, wenden Sie sich bitte an Ihr Kreditinstitut.',
	'MODULE_PAYMENT_SEPA_TEXT_NOTE' => 'Hinweis:',
	'MODULE_PAYMENT_SEPA_TEXT_NOTE2' => 'Wenn Sie aus Sicherheitsbedenken keine Bankdaten über das Internet<br />übertragen wollen, können Sie uns die Bankdaten mit dem SEPA-Lastschriftmandat zusenden.',
	'MODULE_PAYMENT_SEPA_TEXT_TITLE' => 'SEPA-Lastschriftverfahren',
	'MODULE_PAYMENT_SEPA_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_SEPA_ZONE_TITLE' => 'Zahlungszone',
	'MODULE_PAYMENT_TYPE_PERMISSION' => 'sepa'
);