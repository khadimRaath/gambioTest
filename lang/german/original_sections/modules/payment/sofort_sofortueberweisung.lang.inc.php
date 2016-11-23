<?php
/* --------------------------------------------------------------
	sofort_sofortueberweisung.lang.inc.php 2015-01-02 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_INFOLINK_KS' => 'https://www.sofort-bank.com/ger-DE/general/kaeuferschutz/informationen-fuer-kaeufer/',
	'MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT' => '<ul><li>Zahlungssystem mit TÜV-geprüftem Datenschutz</li><li>Keine Registrierung notwendig</li><li>Ware/Dienstleistung wird bei Verfügbarkeit SOFORT versendet</li><li>Bitte halten Sie Ihre Online-Banking-Daten (PIN/TAN) bereit</li></ul>',
	'MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT_KS' => '<ul><li>Bei Bezahlung mit SOFORT Überweisung genießen Sie Käuferschutz! [[link_beginn]]Mehr Informationen[[link_end]]</li><li>Zahlungssystem mit TÜV-geprüftem Datenschutz</li><li>Keine Registrierung notwendig</li><li>Ware/Dienstleistung wird bei Verfügbarkeit SOFORT versendet</li><li>Bitte halten Sie Ihre Online-Banking-Daten (PIN/TAN) bereit</li></ul>',
	'MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_SU' => 'Die gewählte Zahlart ist leider nicht möglich oder wurde auf Kundenwunsch abgebrochen. Bitte wählen Sie eine andere Zahlweise.',
	'MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_TEXT_TITLE' => 'SOFORT Überweisung <br /> <img src="https://images.sofort.com/de/su/logo_90x30.png" alt="Logo SOFORT Überweisung"/>',
	'MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_TEXT_TITLE_ADMIN' => 'SOFORT Überweisung <br /> <img src="https://images.sofort.com/de/su/logo_90x30.png" alt="Logo SOFORT Überweisung"/>',
	'MODULE_PAYMENT_SOFORT_SU_KS_STATUS_DESC' => 'Käuferschutz für SOFORT Überweisung aktivieren',
	'MODULE_PAYMENT_SOFORT_SU_KS_STATUS_TITLE' => 'Käuferschutz aktiviert',
	'MODULE_PAYMENT_SOFORT_SU_KS_TEXT_TITLE' => 'SOFORT Überweisung mit Käuferschutz',
	'MODULE_PAYMENT_SOFORT_SU_LOGO_HTML' => '<img src="https://images.sofort.com/de/su/logo_90x30.png" alt="Logo SOFORT Überweisung"/>',
	'MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_BUYER' => 'Der Zahlungseingang konnte bis dato noch nicht festgestellt werden. {{time}}',
	'MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID_DESC' => 'Status der Bestellung falls kein Geld auf Ihrem Konto eingegangen ist. (Voraussetzung: Konto bei der Sofort Bank).',
	'MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID_TITLE' => 'Bestellstatus, wenn kein Geld angekommen ist',
	'MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_BUYER' => 'Bestellung mit SOFORT Überweisung erfolgreich übermittelt. Ihre Transaktions-ID: {{transaction}}',
	'MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID_DESC' => 'Bestätigter Bestellstatus<br />Bestellstatus nach abgeschlossener Transaktion.',
	'MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID_TITLE' => 'Bestätigter Bestellstatus',
	'MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_DESC' => 'Diese Zahlart als "empfohlene Zahlungsart" markieren. Auf der Bezahlseite erfolgt ein Hinweis direkt hinter der Zahlungsart.',
	'MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_TEXT' => '(Empfohlene Zahlungsweise)',
	'MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_TITLE' => 'Empfohlene Zahlungsweise',
	'MODULE_PAYMENT_SOFORT_SU_REC_CRE_BUYER' => '',
	'MODULE_PAYMENT_SOFORT_SU_REC_CRE_SELLER' => 'Geldeingang auf Konto',
	'MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID_DESC' => 'Status für Bestellungen, wenn das Geld auf dem Konto der SOFORT Bank angekommen ist.',
	'MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID_TITLE' => 'Geldeingang',
	'MODULE_PAYMENT_SOFORT_SU_REF_COM_BUYER' => 'Ein Teil des Betrages wird erstattet.',
	'MODULE_PAYMENT_SOFORT_SU_REF_COM_SELLER' => 'Ein Teil des Rechnungsbetrages wird zurückerstattet. Insgesamt zurückgebuchter Betrag: {{refunded_amount}}. {{time}}',
	'MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID_DESC' => 'Status für Bestellungen, bei denen ein Teilbetrag an den Käufer zurückerstattet wurde.',
	'MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID_TITLE' => 'Teilerstattung',
	'MODULE_PAYMENT_SOFORT_SU_REF_REF_BUYER' => 'Rechnungsbetrag wird zurückerstattet. {{time}}',
	'MODULE_PAYMENT_SOFORT_SU_REF_REF_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID_DESC' => 'Status für Bestellungen, bei denen der vollständige Betrag an den Käufer zurückerstattet wurde.',
	'MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID_TITLE' => 'Vollständige Erstattung',
	'MODULE_PAYMENT_SOFORT_SU_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_SOFORT_SU_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_SOFORT_SU_STATUS_DESC' => 'Aktiviert/deaktiviert das komplette Modul',
	'MODULE_PAYMENT_SOFORT_SU_STATUS_TITLE' => 'sofort.de Modul aktivieren',
	'MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION' => 'SOFORT Überweisung ist der kostenlose, TÜV-zertifizierte Zahlungsdienst der SOFORT AG.',
	'MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE' => '     <table border="0" cellspacing="0" cellpadding="0">      <tr>        <td valign="bottom">
	<a onclick="javascript:window.open(\'https://images.sofort.com/de/su/landing.php\',\'Kundeninformationen\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1020, height=900\');" style="float:left; width:auto; cursor:pointer;">
		{{image}}
	</a>
	</td>      </tr>      <tr> <td class="main">{{text}}</td>      </tr>      </table>',
	'MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT' => 'SOFORT Überweisung',
	'MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_EXTRA' => '
	<script type="text/javascript" src="callback/sofort/ressources/javascript/sofortbox.js"></script>
	<div id="suExtraDesc">
		<div class="content" style="display:none;"></div>
	</div>
	<script type="text/javascript">
		suOverlay = new sofortOverlay(jQuery("#suExtraDesc"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/sue/shopinfo/de");
	</script>
',
	'MODULE_PAYMENT_SOFORT_SU_TEXT_ERROR_MESSAGE' => 'Die gewählte Zahlart ist leider nicht möglich oder wurde auf Kundenwunsch abgebrochen. Bitte wählen Sie eine andere Zahlweise.',
	'MODULE_PAYMENT_SOFORT_SU_TEXT_TITLE' => 'SOFORT Überweisung',
	'MODULE_PAYMENT_SOFORT_SU_TMP_COMMENT' => 'SOFORT Überweisung als Zahlungsart gewählt. Transaktion nicht abgeschlossen.',
	'MODULE_PAYMENT_SOFORT_SU_TMP_COMMENT_SELLER' => 'Weiterleitung zu SOFORT - Bezahlung noch nicht erfolgt.',
	'MODULE_PAYMENT_SOFORT_SU_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_SOFORT_SU_ZONE_TITLE' => 'Zahlungszone'
);