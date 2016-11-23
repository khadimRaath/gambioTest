<?php
/* --------------------------------------------------------------
	luupws.lang.inc.php 2015-01-02 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = array
(
	'MODULE_BOXES_LUUP_TITLE' => 'Bezahlt mit',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_PENDING' => '<input type="submit" name="luup_request" value="Collect">&nbsp;<input type="submit" name="luup_request" value="Cancel">',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_BUTTON_REFUND' => '<input type="submit" name="luup_request" value="Refund">',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_ACTION' => 'Zahlung aktualisieren:',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_CANCELLED' => '<span class="messageStackSuccess">Zahlung wurde storniert</span>',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_COMPLETED' => '<span class="messageStackSuccess">Zahlung wurde durchgeführt</span>',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_FAILED' => '<span class="messageStackError">Webservice Error</span>',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_REFUNDED' => '<span class="messageStackSuccess">Zahlung wurde erstattet</span>',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_STATUS' => 'Zahlungsstatus:',
	'MODULE_PAYMENT_LUUPWS_ADMIN_ORDERS_TEXT_TRANSACTION_ID' => 'Transaktions ID:',
	'MODULE_PAYMENT_LUUPWS_ALLOWED_DESC' => 'Geben Sie <b>einzeln</b> die Zonen an, welche für dieses Modul erlaubt sein sollen. (z.B. AT,DE (wenn leer, werden alle Zonen erlaubt))',
	'MODULE_PAYMENT_LUUPWS_ALLOWED_TITLE' => 'Erlaubte Zonen',
	'MODULE_PAYMENT_LUUPWS_MERCHANT_ID_DESC' => 'Ihre LUUPAY Shop ID',
	'MODULE_PAYMENT_LUUPWS_MERCHANT_ID_TITLE' => 'Händler ID',
	'MODULE_PAYMENT_LUUPWS_MERCHANT_KEY_DESC' => 'Ihr LUUPAY Händler Passwort',
	'MODULE_PAYMENT_LUUPWS_MERCHANT_KEY_TITLE' => 'Händler Passwort',
	'MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID_DESC' => 'Bestellungen, welche mit diesem Modul gemacht werden, auf diesen Status setzen',
	'MODULE_PAYMENT_LUUPWS_ORDER_STATUS_ID_TITLE' => 'Bestellstatus festlegen',
	'MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION_DESC' => 'Select payment collection type',
	'MODULE_PAYMENT_LUUPWS_PAYMENT_COLLECTION_TITLE' => 'Payment type',
	'MODULE_PAYMENT_LUUPWS_SORT_ORDER_DESC' => 'Reihenfolge der Anzeige. Kleinste Ziffer wird zuerst angezeigt',
	'MODULE_PAYMENT_LUUPWS_SORT_ORDER_TITLE' => 'Anzeigereihenfolge',
	'MODULE_PAYMENT_LUUPWS_STATUS_DESC' => 'Möchten Sie Zahlungen per LUUPAY akzeptieren?',
	'MODULE_PAYMENT_LUUPWS_STATUS_TITLE' => 'LUUPAY Modul aktivieren',
	'MODULE_PAYMENT_LUUPWS_TESTMODE_DESC' => 'Testmodus mit Testwährung',
	'MODULE_PAYMENT_LUUPWS_TESTMODE_TITLE' => 'Testmodus',
	'MODULE_PAYMENT_LUUPWS_TEXT_CONTINUE' => 'Weiter',
	'MODULE_PAYMENT_LUUPWS_TEXT_COUNTRIES' => 'DEU|Deutschland',
	'MODULE_PAYMENT_LUUPWS_TEXT_DESCRIPTION' => 'LUUPAY Konto<br />',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR' => 'Fehler im Bezahlvorgang!',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_101' => 'LUUPAY kann die Anfrage nicht bearbeiten. Fehlende Daten.',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_201' => 'Der Haendler konnte nicht identifiziert werden.',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_202' => 'Du hast einen falschen Benutzernamen oder LUUPAY-PIN eingegeben. Bitte versuche es erneut. Falls Du noch nicht bei LUUPAY registriert bist, gehe auf https://www.luupay.de/Signup.aspx?c=de .',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_203' => 'Ungueltiger Verifizierungscode',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_206' => 'Ein Fehler ist aufgetreten beim Haendler. Bitte den Haendler benachrichtigen.',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_301' => 'Die Transaktion konnte nicht beendet werden. Vielleicht langt Dein Guthaben nicht aus. Gehe zu www.luupay.de und Ueberpruefe Deinen Kontostand',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_401' => 'LUUPAY interner Fehler',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_MESSAGE' => 'Versuch fehlgeschlagen: ',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_NO_EURO_CONVERSION_VALUE' => 'Falsche Waehrung - keine Umrechnung in Euro moeglich',
	'MODULE_PAYMENT_LUUPWS_TEXT_ERROR_UNKNOWN' => 'Unbekannter Fehler',
	'MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_CODE' => ' * Du musst den LUUPAY-Verifizierungscode eingeben (8 Ziffern)\\n',
	'MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_PIN' => ' * Du musst Deine LUUPAY-PIN eingeben (4 Ziffern)\\n',
	'MODULE_PAYMENT_LUUPWS_TEXT_JS_FILL_USER' => ' * Du musst Deine Handynummer oder Deinen Benutzernamen eingeben\\n',
	'MODULE_PAYMENT_LUUPWS_TEXT_LINK_REGISTER' => 'LUUPAY hat eine E-Geld-Lizenz und ist unabhängig ob Vertrags-, Prepaid- oder Dienst-Handy. Die Abwicklung der Zahlungen erfolgt über das kostenfreie LUUPAY-Konto. Keine laufenden Kosten, keine Kontoführungskosten, keine Verpflichtungen und kein Abonnement. Noch kein Kunde? Einfach <a href="https://www.luupay.de/Signup.aspx?c=de" target="_blank"><span style="font-weight: normal;"><u>hier anmelden</u></span></a>.',
	'MODULE_PAYMENT_LUUPWS_TEXT_PIN' => 'LUUPAY-PIN:',
	'MODULE_PAYMENT_LUUPWS_TEXT_REGISTERED_IN' => 'Registriert in:',
	'MODULE_PAYMENT_LUUPWS_TEXT_STEP1' => 'Schritt 1 von 2:',
	'MODULE_PAYMENT_LUUPWS_TEXT_STEP1_DESCRIPTION' => 'Deine Handynummer oder Deinen LUUPAY-Benutzernamen',
	'MODULE_PAYMENT_LUUPWS_TEXT_STEP2' => 'Schritt 2 von 2:',
	'MODULE_PAYMENT_LUUPWS_TEXT_STEP2_DESCRIPTION' => 'LUUPAY hat Dir soeben per SMS Deinen LUUPAY-Verifizierungscode für diesen Einkauf auf Dein Handy gesandt. Bitte einfach hier eingeben.',
	'MODULE_PAYMENT_LUUPWS_TEXT_TITLE' => 'LUUPAY',
	'MODULE_PAYMENT_LUUPWS_TEXT_TITLE_SHOP' => 'LUUPAY : Dein Geld wird mobil. Einfach, schnell und sicher!',
	'MODULE_PAYMENT_LUUPWS_TEXT_USERID' => 'Handy-Nr/Benutzername:',
	'MODULE_PAYMENT_LUUPWS_TEXT_VERIFICATION_CODE' => 'LUUPAY-Verifizierungscode:',
	'MODULE_PAYMENT_LUUPWS_USE_DB_DESC' => 'Is the LUUPAY admin extension installed?',
	'MODULE_PAYMENT_LUUPWS_USE_DB_TITLE' => 'Uses admin extension',
	'MODULE_PAYMENT_LUUPWS_ZONE_DESC' => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'MODULE_PAYMENT_LUUPWS_ZONE_TITLE' => 'Zahlungszone'
);