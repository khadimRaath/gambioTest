<?php
/* --------------------------------------------------------------
	sofort_lastschrift.lang.inc.php 2015-01-02 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_TEXT_TITLE' => 'Bankeinzug (Lastschrift) <br /><img src="https://images.sofort.com/de/ls/logo_90x30.png" alt="Logo Lastschrift"/>',
	'MODULE_PAYMENT_SOFORT_LASTSCHRIFT_TEXT_TITLE_ADMIN' => 'Lastschrift by SOFORT <br /><img src="https://images.sofort.com/de/ls/logo_90x30.png" alt="Logo Lastschrift"/>',
	'MODULE_PAYMENT_SOFORT_LS_BANNER' => 'banner_300x100.png',
	'MODULE_PAYMENT_SOFORT_LS_CHECKOUT_CONDITIONS' => '
	<script type="text/javascript">
		function showLsConditions() {
			lsOverlay = new sofortOverlay(jQuery(".lsOverlay"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/de/ls/privacy_de");
			lsOverlay.trigger();
		}
	</script>
	<noscript>
		<div>
			<a href="https://documents.sofort.com/de/ls/privacy_de" target="_blank">Ich habe die Datenschutzhinweise gelesen.</a>
		</div>
	</noscript>
	<!-- comSeo-Ajax-Checkout-Bugfix: show also div, when buyer doesnt use JS -->
	<div>
		<a id="lsNotice" href="javascript:void(0)" onclick="showLsConditions()">Ich habe die Datenschutzhinweise gelesen.</a>
	</div>
	<div style="display:none; z-index: 1001;filter: alpha(opacity=92);filter: progid :DXImageTransform.Microsoft.Alpha(opacity=92);-moz-opacity: .92;-khtml-opacity: 0.92;opacity: 0.92;background-color: black;position: fixed;top: 0px;left: 0px;width: 100%;height: 100%;text-align: center;vertical-align: middle;" class="lsOverlay">
		<div class="loader" style="z-index: 1002; position: relative;background-color: #fff;border: 5px solid #C0C0C0;top: 40px;overflow: scroll;padding: 4px;border-radius: 7px;-moz-border-radius: 7px;-webkit-border-radius: 7px;margin: auto;width: 620px;height: 400px;overflow: scroll; overflow-x: hidden;">
			<div class="closeButton" style="position: fixed; top: 54px; background: url(callback/sofort/ressources/images/close.png) right top no-repeat;cursor:pointer;height: 30px;width: 30px;"></div>
			<div class="content"></div>
		</div>
	</div>
',
	'MODULE_PAYMENT_SOFORT_LS_CHECKOUT_CONDITIONS_WITH_LIGHTBOX' => '<a href="https://documents.sofort.com/de/ls/privacy_de" target="_blank">Ich habe die Datenschutzhinweise gelesen.</a>',
	'MODULE_PAYMENT_SOFORT_LS_CHECKOUT_TEXT' => '',
	'MODULE_PAYMENT_SOFORT_LS_LOGO' => 'logo_155x50.png',
	'MODULE_PAYMENT_SOFORT_LS_LOGO_HTML' => '<img src="https://images.sofort.com/de/ls/logo_90x30.png" alt="Logo Lastschrift"/>',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_BUYER' => 'Zu dieser Transaktion liegt eine Rücklastschrift vor. {{time}}',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_STATUS_ID_DESC' => 'Status für Bestellungen, bei denen eine Rücklastschrift vorliegt.',
	'MODULE_PAYMENT_SOFORT_LS_LOS_REJ_STATUS_ID_TITLE' => 'Rücklastschrift',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_BUYER' => 'Bestellung erfolgreich.',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_SELLER' => 'Bestellung erfolgreich abgeschlossen - Lastschrifteinzug wird durchgeführt. Ihre Transaktions-ID: {{tId}}',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_STATUS_ID_DESC' => 'Bestätigter Bestellstatus<br />Bestellstatus nach abgeschlossener Transaktion.',
	'MODULE_PAYMENT_SOFORT_LS_PEN_NOT_CRE_YET_STATUS_ID_TITLE' => 'Bestätigter Bestellstatus',
	'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_DESC' => 'Diese Zahlart als "empfohlene Zahlungsart" markieren. Auf der Bezahlseite erfolgt ein Hinweis direkt hinter der Zahlungsart.',
	'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_TEXT' => '(Empfohlene Zahlungsweise)',
	'MODULE_PAYMENT_SOFORT_LS_RECOMMENDED_PAYMENT_TITLE' => 'Empfohlene Zahlungsweise',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_BUYER' => '',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_SELLER' => 'Geldeingang auf Konto',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_STATUS_ID_DESC' => 'Status für Bestellungen, wenn das Geld auf dem Konto der SOFORT Bank angekommen ist.',
	'MODULE_PAYMENT_SOFORT_LS_REC_CRE_STATUS_ID_TITLE' => 'Geldeingang',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_BUYER' => 'Ein Teil des Betrages wird erstattet.',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_SELLER' => 'Ein Teil des Rechnungsbetrages wird zurückerstattet. Insgesamt zurückgebuchter Betrag: {{refunded_amount}}. {{time}}',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_STATUS_ID_DESC' => 'Status für Bestellungen, bei denen ein Teilbetrag an den Käufer zurückerstattet wurde.',
	'MODULE_PAYMENT_SOFORT_LS_REF_COM_STATUS_ID_TITLE' => 'Teilerstattung',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_BUYER' => 'Rechnungsbetrag wird zurückerstattet. {{time}}',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_SELLER' => '',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_STATUS_ID_DESC' => 'Status für Bestellungen, bei denen der vollständige Betrag an den Käufer zurückerstattet wurde.',
	'MODULE_PAYMENT_SOFORT_LS_REF_REF_STATUS_ID_TITLE' => 'Vollständige Erstattung',
	'MODULE_PAYMENT_SOFORT_LS_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt.',
	'MODULE_PAYMENT_SOFORT_LS_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_SOFORT_LS_STATUS_DESC' => 'Aktiviert/deaktiviert das komplette Modul',
	'MODULE_PAYMENT_SOFORT_LS_STATUS_TITLE' => 'sofort.de Modul aktivieren',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_ACCOUNT_NUMBER' => 'Kontonummer: ',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_BANK_CODE' => 'Bankleitzahl: ',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_DESCRIPTION' => 'Zahlungsmodul für Lastschrift by SOFORT',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_DESCRIPTION_EXTRA' => '
	<script type="text/javascript" src="callback/sofort/ressources/javascript/sofortbox.js"></script>
	<div id="lsExtraDesc">
		<div class="content" style="display:none;"></div>
	</div>
	<script type="text/javascript">
		lsOverlay = new sofortOverlay(jQuery("#lsExtraDesc"), "callback/sofort/ressources/scripts/getContent.php", "https://documents.sofort.com/ls/shopinfo/de");
	</script>
',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_ERROR_HEADING' => 'Fehler bei der Bestellung aufgetreten.',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_ERROR_MESSAGE' => 'Die gewählte Zahlart ist leider nicht möglich oder wurde auf Kundenwunsch abgebrochen. Bitte wählen Sie eine andere Zahlweise.',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_HOLDER' => 'Kontoinhaber: ',
	'MODULE_PAYMENT_SOFORT_LS_TEXT_TITLE' => 'Bankeinzug (Lastschrift)',
	'MODULE_PAYMENT_SOFORT_LS_TMP_COMMENT' => 'Lastschrift by SOFORT als Zahlungsart gewählt. Transaktion nicht abgeschlossen.',
	'MODULE_PAYMENT_SOFORT_LS_TMP_COMMENT_SELLER' => 'Weiterleitung zu SOFORT - Bezahlung noch nicht erfolgt.',
	'MODULE_PAYMENT_SOFORT_LS_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_SOFORT_LS_ZONE_TITLE' => 'Zahlungszone',
	'MODULE_PAYMENT_SOFORT_MULTIPAY_XML_FAULT_LS' => 'Die gewählte Zahlart ist leider nicht möglich oder wurde auf Kundenwunsch abgebrochen. Bitte wählen Sie eine andere Zahlweise.'
);