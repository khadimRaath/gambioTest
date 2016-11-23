<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */


define('MODULE_PAYMENT_SHOPGATE_TEXT_TITLE', 'Shopgate');
define('MODULE_PAYMENT_SHOPGATE_TEXT_DESCRIPTION', 'Shopgate - Mobile Shopping.');
define('MODULE_PAYMENT_SHOPGATE_TEXT_INFO', 'Bestellungen sind bereits bei Shopgate bezahlt.');

define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_SHIPPING', 'Versand');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_SUBTOTAL', 'Zwischensumme');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_PAYMENTFEE', 'Zahlungsartkosten');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_TOTAL', 'Summe');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_TOTAL_WITHOUT_TAX', 'Summe (netto)');

define('MODULE_PAYMENT_SHOPGATE_TEXT_EMAIL_FOOTER', '');
define('MODULE_PAYMENT_SHOPGATE_STATUS_TITLE', 'Shopgate-Zahlungsmodul aktiviert:');

define('MODULE_PAYMENT_SHOPGATE_STATUS_DESC', '');
define('MODULE_PAYMENT_SHOPGATE_ALLOWED_TITLE', '');
define('MODULE_PAYMENT_SHOPGATE_ALLOWED_DESC', '');
define('MODULE_PAYMENT_SHOPGATE_PAYTO_TITLE', '');
define('MODULE_PAYMENT_SHOPGATE_PAYTO_DESC', '');
define('MODULE_PAYMENT_SHOPGATE_SORT_ORDER_TITLE', '');
define('MODULE_PAYMENT_SHOPGATE_SORT_ORDER_DESC', '');
define('MODULE_PAYMENT_SHOPGATE_ZONE_TITLE', '');
define('MODULE_PAYMENT_SHOPGATE_ZONE_DESC', '');
define('MODULE_PAYMENT_SHOPGATE_ORDER_STATUS_ID_TITLE', 'Status');
define('MODULE_PAYMENT_SHOPGATE_ORDER_STATUS_ID_DESC', 'Bestellungen, die mit diesem Modul importiert werden, auf diesen Status setzen:');
define('MODULE_PAYMENT_SHOPGATE_ERROR_READING_LANGUAGES', 'Fehler beim Konfigurieren der Spracheinstellungen.');
define('MODULE_PAYMENT_SHOPGATE_ERROR_LOADING_CONFIG', 'Fehler beim Laden der Konfiguration.');
define(
'MODULE_PAYMENT_SHOPGATE_ERROR_SAVING_CONFIG',
    'Fehler beim Speichern der Konfiguration. ' .
    'Bitte &uuml;berpr&uuml;fen Sie die Schreibrechte (777) f&uuml;r ' .
    'den Ordner &quot;/shopgate_library/config/&quot; des Shopgate-Plugins.'
);
define('MODULE_PAYMENT_SHOPGATE_TITLE_BLANKET ', 'Pauschal');

define("SHOPGATE_COUPON_ERROR_NEED_ACCOUNT", "Um diesen Gutschein verwenden zu können, müssen Sie angemeldet sein.");
define("SHOPGATE_COUPON_ERROR_RESTRICTED_PRODUCTS", "Dieser Gutschein ist auf bestimmte Produkte beschränkt");
define("SHOPGATE_COUPON_ERROR_RESTRICTED_CATEGORIES", "Dieser Gutschein ist auf bestimmte Kategorien beschränkt");
define("SHOPGATE_COUPON_ERROR_MINIMUM_ORDER_AMOUNT_NOT_REACHED", "Der Mindestbestellwert, um diesen Gutschein nutzen zu können, wurde nicht erreicht");
