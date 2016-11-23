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
	'JS_BANK_BLZ' => '* Bitte geben Sie die BLZ Ihrer Bank ein!\n\n',
	'JS_BANK_NAME' => '* Bitte geben Sie den Namen Ihrer Bank ein!\n\n',
	'JS_BANK_NUMBER' => '* Bitte geben Sie Ihre Kontonummer ein!\n\n',
	'JS_BANK_OWNER' => '* Bitte geben Sie den Namen des Kontobesitzers ein!\n\n',
	'MODULE_PAYMENT_BANKTRANSFER_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_BANKTRANSFER_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_DESC' => 'Möchten Sie die Datenbanksuche für die BLZ verwenden?',
	'MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ_TITLE' => 'Datenbanksuche für die BLZ verwenden?',
	'MODULE_PAYMENT_BANKTRANSFER_DATACHECK_DESC' => 'Sollen die eingegebenen Bankdaten überprüft werden?',
	'MODULE_PAYMENT_BANKTRANSFER_DATACHECK_TITLE' => 'Bankdaten prüfen?',
	'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_DESC' => 'Möchten Sie die Fax Bestätigung erlauben?',
	'MODULE_PAYMENT_BANKTRANSFER_FAX_CONFIRMATION_TITLE' => 'Fax Bestätigung erlauben',
	'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_DESC' => 'Die Mindestanzahl an Bestellungen die ein Kunden haben muss damit die Option zur Verfügung steht.',
	'MODULE_PAYMENT_BANKTRANSFER_MIN_ORDER_TITLE' => 'Notwendige Bestellungen',
	'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_DESC' => 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen',
	'MODULE_PAYMENT_BANKTRANSFER_ORDER_STATUS_ID_TITLE' => 'Bestellstatus festlegen',
	'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_BANKTRANSFER_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_BANKTRANSFER_STATUS_DESC' => 'Möchten Sie Banktranfer Zahlungen erlauben?',
	'MODULE_PAYMENT_BANKTRANSFER_STATUS_TITLE' => 'Banktranfer Zahlungen erlauben',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK' => 'Einzugsermächtigung',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_BLZ' => 'BLZ:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR' => 'FEHLER: ',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_1' => 'Kontonummer und Bankleitzahl stimmen nicht überein, bitte korrigieren Sie Ihre Angaben.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_10' => 'Sie haben keinen Kontoinhaber angegeben.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_2' => 'Diese Kontonummer ist nicht pruefbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_3' => 'Diese Kontonummer ist nicht pruefbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_4' => 'Diese Kontonummer ist nicht pruefbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_5' => 'Diese Kontonummer ist nicht pruefbar, bitte kontrollieren zur Sicherheit Sie Ihre Eingabe nochmals.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_8' => 'Sie haben keine korrekte Bankleitzahl eingegeben.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_ERROR_9' => 'Sie haben keine korrekte Kontonummer eingegeben.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_FAX' => 'Einzugsermächtigung wird per Fax bestätigt',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_INFO' => 'Bitte beachten Sie, dass das Lastschriftverfahren <b>nur</b> von einem <b>deutschen Girokonto</b> aus möglich ist',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NAME' => 'Bank:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_NUMBER' => 'Kontonummer:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_BANK_OWNER' => 'Kontoinhaber:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_DESCRIPTION' => 'Lastschriftverfahren (Beachten Sie, dass die Erhebung von sensiblen Zahlungsdaten nur bei gleichzeitig aktivierter Verschlüsselung erfolgen darf.)',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_EMAIL_FOOTER' => 'Hinweis: Sie können unsere Fax-Bestätigung hier herunterladen: ',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_INFO' => '',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE' => 'Hinweis:',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE2' => 'Wenn Sie aus Sicherheitsbedenken keine Bankdaten über das Internet<br />übertragen wollen, können Sie sich unser ',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE3' => 'Faxformular',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_NOTE4' => ' herunterladen und uns ausgefüllt zusenden.',
	'MODULE_PAYMENT_BANKTRANSFER_TEXT_TITLE' => 'Lastschriftverfahren',
	'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_DESC' => 'Die Fax-Bestätigungsdatei. Diese muss im Catalog-Verzeichnis liegen',
	'MODULE_PAYMENT_BANKTRANSFER_URL_NOTE_TITLE' => 'Fax-URL',
	'MODULE_PAYMENT_BANKTRANSFER_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_BANKTRANSFER_ZONE_TITLE' => 'Zahlungszone',
	'MODULE_PAYMENT_TYPE_PERMISSION' => 'bt'
);