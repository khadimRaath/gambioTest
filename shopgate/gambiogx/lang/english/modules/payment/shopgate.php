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
define('MODULE_PAYMENT_SHOPGATE_TEXT_INFO', 'Orders are already paid at Shopgate.');

define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_SHIPPING', 'Shipping');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_SUBTOTAL', 'Subtotal');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_PAYMENTFEE', 'Payment Fees');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_TOTAL', 'Total');
define('MODULE_PAYMENT_SHOPGATE_ORDER_LINE_TEXT_TOTAL_WITHOUT_TAX', 'Total (net)');

define('MODULE_PAYMENT_SHOPGATE_TEXT_EMAIL_FOOTER', "");
define('MODULE_PAYMENT_SHOPGATE_STATUS_TITLE', 'Shopgate payment module activated:');

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
define('MODULE_PAYMENT_SHOPGATE_ORDER_STATUS_ID_DESC', 'Set status of orders imported by this module to:');
define('MODULE_PAYMENT_SHOPGATE_ERROR_READING_LANGUAGES', 'Error configuring language settings.');
define('MODULE_PAYMENT_SHOPGATE_ERROR_LOADING_CONFIG', 'Error loading configuration.');
define(
'MODULE_PAYMENT_SHOPGATE_ERROR_SAVING_CONFIG',
    'Error saving configuration. ' .
    'Please check the permissions (777) for the folder ' .
    '&quot;/shopgate_library/config&quot; of the Shopgate plugin.'
);
define('MODULE_PAYMENT_SHOPGATE_TITLE_BLANKET ', 'Blanket');

define("SHOPGATE_COUPON_ERROR_NEED_ACCOUNT", "You need to be logged in to use this coupon");
define("SHOPGATE_COUPON_ERROR_RESTRICTED_PRODUCTS", "This coupon is restricted to specific products");
define("SHOPGATE_COUPON_ERROR_RESTRICTED_CATEGORIES", "This coupon is restricted to specific categories");
define("SHOPGATE_COUPON_ERROR_MINIMUM_ORDER_AMOUNT_NOT_REACHED", "This coupon has a minimum order amount which has not been reached");
