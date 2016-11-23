<?php
/* --------------------------------------------------------------
   application_top.php 2016-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.158 2003/03/22); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.46 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_top.php 1323 2005-10-27 17:58:08Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

if(file_exists(__DIR__ . '/../../system/core/logging'))
{
	require_once(__DIR__ . '/../../system/core/logging/LogControl.inc.php');
}

if(file_exists(__DIR__ . '/../../GProtector'))
{
	require_once(__DIR__ . '/../../GProtector/start.inc.php');
}

@ini_set('session.use_only_cookies', 0);

// Start the clock for the page parse time log
define('PAGE_PARSE_START_TIME', microtime(true));

// security
define('_VALID_XTC',true);
define('_GM_VALID_CALL', 1);
define('APPLICATION_RUN_MODE', 'backend');

// Set the level of error reporting
if(defined('E_DEPRECATED'))
{
	error_reporting(
			E_ALL
			& ~E_NOTICE
			& ~E_DEPRECATED
			& ~E_STRICT
			& ~E_CORE_ERROR
			& ~E_CORE_WARNING
	);
}
else
{
	error_reporting(
			E_ALL
			& ~E_NOTICE
			& ~E_STRICT
			& ~E_CORE_ERROR
			& ~E_CORE_WARNING
	);
}

// Disable use_trans_sid as xtc_href_link() does this manually
if (function_exists('ini_set')) {
	ini_set('session.use_trans_sid', 0);
}

// Set the local configuration parameters - mainly for developers or the main-configure
if (file_exists('includes/local/configure.php')) {
	require_once('includes/local/configure.php');
} else {
	require_once('includes/configure.php');
}

# block crawlers
require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
if(xtc_check_agent() == 1)
{
	header("HTTP/1.0 404 Not Found");
	die('Spiders are not allowed to access this page.');
}

require_once(DIR_FS_INC . 'set_memory_limit.inc.php');
$t_memory_limit = 128;
$t_memory_limit_ok = set_memory_limit($t_memory_limit);

require_once(DIR_FS_INC.'htmlentities_wrapper.inc.php');
require_once(DIR_FS_INC.'htmlspecialchars_wrapper.inc.php');
require_once(DIR_FS_INC.'html_entity_decode_wrapper.inc.php');
require_once(DIR_FS_INC.'parse_str_wrapper.inc.php');
require_once(DIR_FS_INC.'strlen_wrapper.inc.php');
require_once(DIR_FS_INC.'substr_wrapper.inc.php');
require_once(DIR_FS_INC.'strpos_wrapper.inc.php');
require_once(DIR_FS_INC.'strrpos_wrapper.inc.php');
require_once(DIR_FS_INC.'strtolower_wrapper.inc.php');
require_once(DIR_FS_INC.'strtoupper_wrapper.inc.php');
require_once(DIR_FS_INC.'substr_count_wrapper.inc.php');
require_once(DIR_FS_INC.'utf8_encode_wrapper.inc.php');
require_once(DIR_FS_INC.'xtc_format_filesize.inc.php');

require_once(DIR_WS_MODULES . 'new_attributes_functions.php');

require_once(DIR_FS_CATALOG.'system/core/logging/LogEvent.inc.php');
require_once(DIR_FS_CATALOG.'gm/classes/ErrorHandler.php');
require_once(DIR_FS_CATALOG.'gm/classes/FileLog.php');
require_once(DIR_FS_CATALOG.'gm/inc/check_data_type.inc.php');
require_once(DIR_FS_CATALOG.'gm/inc/gm_get_env_info.inc.php');
require_once(DIR_FS_CATALOG.'system/gngp_layer_init.inc.php');
require_once(DIR_FS_CATALOG.'inc/generate_withdrawal_link.inc.php');

# custom error handler with DEFAULT SETTINGS
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

$coo_timezone_setter = MainFactory::create_object('TimezoneSetter');
$coo_timezone_setter->set_date_default_timezone();

StopWatch::get_instance()->add_specific_time_stamp('start', PAGE_PARSE_START_TIME);

# custom class autoloader
spl_autoload_register(array(new MainAutoloader('backend'), 'load'));

// Composer class autoloader.
if(!file_exists(DIR_FS_CATALOG . 'vendor/autoload.php'))
{
	throw new RuntimeException('Vendor directory is missing from the filesystem. Please install the PHP dependencies by '
	                           . 'executing the "composer install && gulp general:composer" command.');
}
require_once(DIR_FS_CATALOG . 'vendor/autoload.php');

# global debugger object
$coo_debugger = new Debugger();

define('SQL_CACHEDIR',DIR_FS_CATALOG.'cache/');

// Define the project version
define('PROJECT_VERSION', 'xt:Commerce v3.0.4 SP2.1');
define('FIRST_GX2_TEMPLATE_VERSION', 2.0);

// Set the length of the redeem code, the longer the more secure
// BOF GM_MOD:
// define('SECURITY_CODE_LENGTH', '6');

// Used in the "Backup Manager" to compress backups
define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

// define the filenames used in the project

// BOF Gambio Box
define('FILENAME_GM_COUNTER', 'gm_counter.php');
define('FILENAME_GM_PDF', 'gm_pdf.php');
define('FILENAME_GM_LOGO', 'gm_logo.php');
define('FILENAME_GM_SECURITY', 'gm_security.php');
define('FILENAME_GM_META', 'gm_meta.php');
define('FILENAME_GM_ANALYTICS', 'gm_analytics.php');
define('FILENAME_GM_SITEMAP', 'gm_sitemap.php');
define('FILENAME_GM_BOOKMARKS', 'gm_bookmarks.php');
define('FILENAME_GM_SEO_OPTIONS', 'gm_seo_options.php');
define('FILENAME_GM_SEO_BOOST', 'gm_seo_boost.php');
define('FILENAME_GM_SCROLLER', 'gm_scroller.php');
define('FILENAME_GM_ID_STARTS', 'gm_id_starts.php');
define('FILENAME_GM_EMAILS', 'gm_emails.php');
define('FILENAME_GM_GUESTBOOK', 'gm_guestbook.php');
define('FILENAME_GM_STYLE_EDIT', 'gm_style_edit.php');
define('FILENAME_GM_LANG_EDIT', 'gm_lang_edit.php');
define('FILENAME_GM_MISCELLANEOUS', 'gm_miscellaneous.php');
define('FILENAME_GM_LAWS', 'gm_laws.php');
define('FILENAME_GM_SQL', 'gm_sql.php');
define('FILENAME_GM_OFFLINE', 'gm_offline.php');
define('FILENAME_GM_LIGHTBOX', 'gm_lightbox.php');
define('FILENAME_GM_TRUSTED_SHOP_ID', 'gm_trusted_shop_id.php');
define('FILENAME_GM_OPENSEARCH', 'gm_opensearch.php');
define('FILENAME_GM_MODULE_EXPORT', 'gm_module_export.php');
define('FILENAME_GM_BACKUP_FILES_ZIP', 'gm_backup_files_zip.php');
define('FILENAME_GM_PRODUCT_EXPORT','gm_product_export.php');
define('FILENAME_GM_GMOTION', 'gm_gmotion.php');
define('FILENAME_GM_FEATURE_CONTROL', 'gm_feature_control.php');
define('FILENAME_GM_SLIDER', 'gm_slider.php');
define('FILENAME_QUANTITYUNITS', 'quantity_units.php');
define('FILENAME_ROBOTS_DOWNLOAD', 'robots_download.php');
// EOF Gambio Box
define('FILENAME_ACCOUNTING', 'accounting.php');
define('FILENAME_BACKUP', 'backup.php');
define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
define('FILENAME_CACHE', 'cache.php');
define('FILENAME_CAMPAIGNS', 'campaigns.php');
define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
define('FILENAME_CATALOG_NEWSLETTER', 'newsletter.php');
define('FILENAME_CATEGORIES', 'categories.php');
define('FILENAME_CONFIGURATION', 'configuration.php');
define('FILENAME_COUNTRIES', 'countries.php');
define('FILENAME_CURRENCIES', 'currencies.php');
define('FILENAME_CUSTOMERS', 'customers.php');
define('FILENAME_CUSTOMERS_STATUS', 'customers_status.php');
define('FILENAME_DEFAULT', 'start.php');
define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
define('FILENAME_FORMS', 'forms.php');
define('FILENAME_FORM_VALUES', 'form_values.php');
define('FILENAME_GEO_ZONES', 'geo_zones.php');
define('FILENAME_LANGUAGES', 'languages.php');
define('FILENAME_MAGNALISTER','magnalister.php');  /* magnalister v1.0.0 */
define('FILENAME_MAIL', 'mail.php');
define('FILENAME_MANUFACTURERS', 'manufacturers.php');
define('FILENAME_MODULES', 'modules.php');
define('FILENAME_ORDERS', 'orders.php');
define('FILENAME_ORDERS_INVOICE', 'invoice.php');
define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
define('FILENAME_ORDERS_STATUS', 'orders_status.php');
define('FILENAME_ORDERS_EDIT', 'orders_edit.php');
define('FILENAME_POPUP_IMAGE', 'popup_image.php');
define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
define('FILENAME_REVIEWS', 'reviews.php');
define('FILENAME_SERVER_INFO', 'server_info.php');
define('FILENAME_CLEAR_CACHE', 'clear_cache.php');
define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
define('FILENAME_SPECIALS', 'specials.php');
define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
define('FILENAME_TAX_CLASSES', 'tax_classes.php');
define('FILENAME_TAX_RATES', 'tax_rates.php');
define('FILENAME_WHOS_ONLINE', 'whos_online.php');
define('FILENAME_SHOW_LOGS', 'show_logs.php');
define('FILENAME_ZONES', 'zones.php');
define('FILENAME_START', 'start.php');
define('FILENAME_STATS_STOCK_WARNING', 'stats_stock_warning.php');
define('FILENAME_TPL_BOXES','templates_boxes.php');
define('FILENAME_TPL_MODULES','templates_modules.php');
define('FILENAME_NEW_ATTRIBUTES','new_attributes.php');
define('FILENAME_LOGOUT','../logoff.php');
define('FILENAME_LOGIN','../login.php');
define('FILENAME_CREATE_ACCOUNT','create_account.php');
define('FILENAME_CREATE_ACCOUNT_SUCCESS','create_account_success.php');
define('FILENAME_CUSTOMER_MEMO','customer_memo.php');
define('FILENAME_CONTENT_MANAGER','content_manager.php');
define('FILENAME_CONTENT_PREVIEW','content_preview.php');
define('FILENAME_SECURITY_CHECK','security_check.php');
define('FILENAME_CREDITS','credits.php');
define('FILENAME_MODULE_NEWSLETTER','module_newsletter.php');
define('FILENAME_GV_QUEUE', 'gv_queue.php');
define('FILENAME_GV_MAIL', 'gv_mail.php');
define('FILENAME_GV_SENT', 'gv_sent.php');
define('FILENAME_COUPON_ADMIN', 'coupon_admin.php');
define('FILENAME_POPUP_MEMO', 'popup_memo.php');
define('FILENAME_SHIPPING_STATUS', 'shipping_status.php');
define('FILENAME_SALES_REPORT','stats_sales_report.php');
define('FILENAME_MODULE_EXPORT','module_export.php');
define('FILENAME_EASY_POPULATE','easypopulate.php');
define('FILENAME_PRODUCTS_VPE','products_vpe.php');
define('FILENAME_CAMPAIGNS_REPORT','stats_campaigns.php');
define('FILENAME_XSELL_GROUPS','cross_sell_groups.php');
define('FILENAME_GM_JANOLAW','gm_janolaw.php');
define('FILENAME_PROPERTIES','properties.php');
define('FILENAME_GM_INVOICING','gm_invoicing.php');
define('FILENAME_EKOMI','ekomi.php');

// define the database table names used in the project
define('TABLE_ADDRESS_BOOK', 'address_book');
define('TABLE_ADDRESS_FORMAT', 'address_format');
define('TABLE_ADMIN_ACCESS', 'admin_access');
define('TABLE_BANNERS', 'banners');
define('TABLE_BANNERS_HISTORY', 'banners_history');
define('TABLE_BLACKLIST', 'card_blacklist');
define('TABLE_BOX_ALIGN','box_align');
define('TABLE_CAMPAIGNS', 'campaigns');
define('TABLE_CAMPAIGNS_IP','campaigns_ip');
define('TABLE_CATEGORIES', 'categories');
define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
define('TABLE_CM_FILE_FLAGS', 'cm_file_flags');
define('TABLE_CONFIGURATION', 'configuration');
define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
define('TABLE_CONTENT_MANAGER', 'content_manager');
define('TABLE_CONTENT_SLIDER_SET', 'content_slider_set');
define('TABLE_COUNTER', 'counter');
define('TABLE_COUNTER_HISTORY', 'counter_history');
define('TABLE_COUNTRIES', 'countries');
define('TABLE_COUPONS', 'coupons');
define('TABLE_COUPONS_DESCRIPTION', 'coupons_description');
define('TABLE_COUPON_EMAIL_TRACK', 'coupon_email_track');
define('TABLE_COUPON_GV_CUSTOMER', 'coupon_gv_customer');
define('TABLE_COUPON_GV_QUEUE', 'coupon_gv_queue');
define('TABLE_COUPON_REDEEM_TRACK', 'coupon_redeem_track');
define('TABLE_CURRENCIES', 'currencies');
define('TABLE_CUSTOMERS', 'customers');
define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
define('TABLE_CUSTOMERS_INFO', 'customers_info');
define('TABLE_CUSTOMERS_IP', 'customers_ip');
define('TABLE_CUSTOMERS_MEMO','customers_memo');
define('TABLE_CUSTOMERS_STATUS', 'customers_status');
define('TABLE_CUSTOMERS_STATUS_HISTORY', 'customers_status_history');
define('TABLE_CUSTOMERS_WISHLIST', 'customers_wishlist');
define('TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES', 'customers_wishlist_attributes');
define('TABLE_FORMS', 'forms');
define('TABLE_GEO_ZONES', 'geo_zones');
define('TABLE_LANGUAGES', 'languages');
define('TABLE_MANUFACTURERS', 'manufacturers');
define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
define('TABLE_MEDIA_CONTENT','media_content');
define('TABLE_MODULE_NEWSLETTER','module_newsletter');
define('TABLE_NEWSLETTERS', 'newsletters');
define('TABLE_NEWSLETTERS_HISTORY', 'newsletters_history');
define('TABLE_NEWSLETTER_RECIPIENTS', 'newsletter_recipients');
define('TABLE_ORDERS', 'orders');
define('TABLE_ORDERS_PRODUCTS', 'orders_products');
define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
define('TABLE_ORDERS_RECALCULATE', 'orders_recalculate');
define('TABLE_ORDERS_STATUS', 'orders_status');
define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
define('TABLE_ORDERS_TOTAL', 'orders_total');
define('TABLE_PERSONAL_OFFERS_BY','personal_offers_by_customers_status_');
define('TABLE_PRODUCTS', 'products');
define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
define('TABLE_PRODUCTS_CONTENT','products_content');
define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
define('TABLE_PRODUCTS_IMAGES', 'products_images');
define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
define('TABLE_PRODUCTS_OPTIONS', 'products_options');
define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
define('TABLE_PRODUCTS_SLIDER_SET', 'products_slider_set');
define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
define('TABLE_PRODUCTS_VPE','products_vpe');
define('TABLE_PRODUCTS_XSELL','products_xsell');
define('TABLE_PRODUCTS_XSELL_GROUPS','products_xsell_grp_name');
define('TABLE_REVIEWS', 'reviews');
define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
define('TABLE_SERVER_TRACKING', 'server_tracking');
define('TABLE_SESSIONS', 'sessions');
define('TABLE_SHIPPING_STATUS', 'shipping_status');
define('TABLE_SPECIALS', 'specials');
define('TABLE_TAX_CLASS', 'tax_class');
define('TABLE_TAX_RATES', 'tax_rates');
define('TABLE_TPL_MODULES_CONFIGURATION', 'tpl_modules_configuration');
define('TABLE_WHOS_ONLINE', 'whos_online');
define('TABLE_ZONES', 'zones');
define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');

define('GM_TABLE_GM_GMOTION', 'gm_gmotion');
define('GM_TABLE_GM_GMOTION_PRODUCTS', 'gm_gmotion_products');
define('TABLE_CUSTOMERS_LOGS_HISTORY', 'customers_logs_history');
define('TABLE_INFOBOX_MESSAGES', 'infobox_messages');
define('TABLE_INFOBOX_MESSAGES_DESCRIPTION', 'infobox_messages_description');

// include needed functions
require_once(DIR_FS_INC . 'get_usermod.inc.php');
require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
require_once(DIR_FS_INC . 'xtc_db_error.inc.php');
require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
require_once(DIR_FS_INC . 'xtc_db_queryCached.inc.php');
require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
require_once(DIR_FS_INC . 'xtc_db_data_seek.inc.php');
require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_fields.inc.php');
require_once(DIR_FS_INC . 'xtc_db_output.inc.php');
require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');
require_once(DIR_FS_INC . 'xtc_get_ip_address.inc.php');
require_once(DIR_FS_INC . 'xtc_setcookie.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');
require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC . 'xtc_get_qty.inc.php');
require_once(DIR_FS_INC . 'xtc_product_link.inc.php');
require_once(DIR_FS_INC . 'xtc_cleanName.inc.php');
require_once(DIR_FS_INC . 'xtc_category_link.inc.php');
require_once(DIR_FS_INC . 'fetch_email_template.inc.php');
require_once(DIR_FS_INC . 'clean_numeric_input.inc.php');
require_once(DIR_FS_INC . 'xtc_wysiwyg.inc.php');
require_once(DIR_FS_INC . 'country_eu_status_by_country_id.inc.php');
require_once(DIR_FS_INC . 'update_customer_b2b_status.inc.php');
require_once(DIR_FS_INC . 'xtc_date_raw.inc.php');

//GM_MOD BOF
require_once (DIR_FS_CATALOG . 'gm/inc/gm_clear_string.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_prepare_string.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_set_conf.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_set_content.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_content.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_pdf_is_installed.inc.php');

require_once (DIR_FS_CATALOG . 'system/core/GMDataObject.inc.php');
require_once (DIR_FS_CATALOG . 'system/core/GMDataObjectGroup.inc.php');
require_once (DIR_FS_CATALOG . 'system/core/Registry.inc.php');
require_once (DIR_FS_CATALOG . 'system/core/ClassRegistry.inc.php');
require_once (DIR_FS_CATALOG . 'system/core/MainFactory.inc.php');

//GM_MOD EOF

// customization for the design layout
// BOF GM_MOD GX-Customizer:
define('BOX_WIDTH', 160); // how wide the boxes should be in pixels (default: 125)

// Define how do we update currency exchange rates
// Possible values are 'oanda' 'xe' or ''
define('CURRENCY_SERVER_PRIMARY', 'oanda');
define('CURRENCY_SERVER_BACKUP', 'xe');

// Use the DB-Logger
define('STORE_DB_TRANSACTIONS', 'false');

// include the database functions
//  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
xtc_db_connect() or die('Unable to connect to database server!');

// general functions
require(DIR_WS_FUNCTIONS . 'general.php');

// set application wide parameters
$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . '');
while ($configuration = xtc_db_fetch_array($configuration_query)) {
	define($configuration['cfgKey'], $configuration['cfgValue']);
}

$coo_timezone_setter->set_date_default_timezone(DATE_TIMEZONE);

# custom error handler with DEFAULT SETTINGS
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

# build template control instance
$coo_template_control =& MainFactory::create_object('TemplateControl', array(CURRENT_TEMPLATE), true);

define('FILENAME_IMAGEMANIPULATOR',IMAGE_MANIPULATOR);

function xtDBquery($query) {
   if (DB_CACHE=='true') {
		 $result=xtc_db_queryCached($query);
		 //echo 'cached query: '.$query.'<br>';
	  } else {
		 $result=xtc_db_query($query);
	}
return $result;
}


// include shopping cart class
require(DIR_WS_CLASSES . 'shopping_cart.php');

// some code to solve compatibility issues
require(DIR_WS_FUNCTIONS . 'compatibility.php');

// define how the session functions will be used
require(DIR_WS_FUNCTIONS . 'sessions.php');

// define our general functions used application-wide
require(DIR_WS_FUNCTIONS . 'html_output.php');

// set the session name and save path
session_name('XTCsid');
session_save_path(DIR_FS_CATALOG . 'cache/');

session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.' . $current_domain : ''), false, true);

// set the session ID if it exists
if(isset($_POST[session_name()]) && !empty($_POST[session_name()]) && preg_replace('/[^a-zA-Z0-9,-]/', "", $_POST[session_name()]) === $_POST[session_name()])
{
	session_id($_POST[session_name()]);
}
elseif(($request_type == 'SSL') && isset($_GET[session_name()]) && !empty($_GET[session_name()]) && preg_replace('/[^a-zA-Z0-9,-]/', "", $_GET[session_name()]) === $_GET[session_name()])
{
	session_id($_GET[session_name()]);
}

if(isset($_POST[session_name()]) && (empty($_POST[session_name()]) || preg_replace('/[^a-zA-Z0-9,-]/', "", $_POST[session_name()]) !== $_POST[session_name()]))
{
	unset($_POST[session_name()]);
}

if(isset($_GET[session_name()]) && (empty($_GET[session_name()]) || preg_replace('/[^a-zA-Z0-9,-]/', "", $_GET[session_name()]) !== $_GET[session_name()]))
{
	unset($_GET[session_name()]);
}

// start the session
$session_started = false;
if (SESSION_FORCE_COOKIE_USE == 'True') {
	xtc_setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, '/', $current_domain);

	if (isset($_COOKIE['cookie_test'])) {
	  session_start();
	  $session_started = true;
	}
}
elseif (CHECK_CLIENT_AGENT == 'True') {
	$user_agent = strtolower(getenv('HTTP_USER_AGENT'));
	$spider_flag = false;

	if ($spider_flag == false) {
		session_start();
		$session_started = true;
	}
}
else {
	session_start();
	$session_started = true;
}

// verify the ssl_session_id if the feature is enabled
if(($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true) ) {
	$ssl_session_id = getenv('SSL_SESSION_ID');
	if (!isset($_SESSION['SESSION_SSL_ID'])) {
		$_SESSION['SESSION_SSL_ID'] = $ssl_session_id;
	}

	if ($_SESSION['SESSION_SSL_ID'] != $ssl_session_id) {
		  session_destroy();
		  xtc_redirect(xtc_href_link(FILENAME_SSL_CHECK));
	}
}

// verify the browser user agent if the feature is enabled
if (SESSION_CHECK_USER_AGENT == 'True') {
	$http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$http_user_agent2 = strtolower(getenv("HTTP_USER_AGENT"));
	$http_user_agent = ($http_user_agent == $http_user_agent2) ? $http_user_agent : $http_user_agent.';'.$http_user_agent2;
	if (!isset($_SESSION['SESSION_USER_AGENT'])) {
		$_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
	}

	if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
		$t_message = 'User agent changed from "' . $_SESSION['SESSION_USER_AGENT'] . '" to "' . $http_user_agent . '"';
		LogControl::get_instance()->notice($t_message, 'security', 'security');
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// verify the IP address if the feature is enabled
if (SESSION_CHECK_IP_ADDRESS == 'True') {
	$ip_address = xtc_get_ip_address();
	if (!xtc_session_is_registered('SESSION_IP_ADDRESS')) {
		$_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
	}

	if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
		$t_message = 'IP address changed from "' . $_SESSION['SESSION_IP_ADDRESS'] . '" to "' . $ip_address . '"';
		LogControl::get_instance()->notice($t_message, 'security', 'security');
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

$coo_application_top_lead_extender_component = MainFactory::create_object('AdminApplicationTopPrimalExtenderComponent');
$coo_application_top_lead_extender_component->set_data('GET', $_GET);
$coo_application_top_lead_extender_component->set_data('POST', $_POST);
$coo_application_top_lead_extender_component->proceed();

// set the language
if (!isset($_SESSION['language']) || $_SESSION['language'] === false || isset($_GET['language']))
{
	$lng = new language($_GET['language']);

	if(!isset($_GET['language']) && gm_get_conf('GM_CHECK_BROWSER_LANGUAGE') === '1')
	{
		$lng->get_browser_language();
	}

	$_SESSION['language'] = $lng->language['directory'];
	$_SESSION['languages_id'] = $lng->language['id'];
	$_SESSION['language_charset'] = $lng->language['language_charset'];
	$_SESSION['language_code'] = $lng->language['code'];
}

// needs to be initialized after $_SESSION['languages_id'] is set
$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

// include the language translations
require(DIR_FS_LANGUAGES . $_SESSION['language'] . '/admin/init.inc.php');
$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/admin/buttons.php');
$current_page = explode('?', basename($_SERVER['PHP_SELF'])); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility

$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/admin/' . $current_page);

// write customers status in session
require('../' . DIR_WS_INCLUDES . 'write_customers_status.php');

if(defined('SUPPRESS_REDIRECT') == false && (file_exists($current_page) == false || $_SESSION['customers_status']['customers_status_id'] !== '0')) {
	xtc_redirect(xtc_href_link(FILENAME_LOGIN));
}

// for tracking of customers
$_SESSION['user_info'] = array();
if (!$_SESSION['user_info']['user_ip']) {
	$_SESSION['user_info']['user_ip'] = $_SERVER['REMOTE_ADDR'];
	//$user_info['user_ip_date'] =  value will be in fact added when login ;
	$_SESSION['user_info']['user_host'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['user_info']['advertiser'] = $_GET['ad'];
	$_SESSION['user_info']['referer_url'] = $_SERVER['HTTP_REFERER'];
}

// define our localization functions
require(DIR_WS_FUNCTIONS . 'localization.php');

// Include validation functions (right now only email address)
//require(DIR_WS_FUNCTIONS . 'validations.php');

// setup our boxes
require(DIR_WS_CLASSES . 'table_block.php');
require(DIR_WS_CLASSES . 'box.php');

// initialize the message stack for output messages
require(DIR_WS_CLASSES . 'message_stack.php');
$messageStack = new messageStack;

// split-page-results
require(DIR_WS_CLASSES . 'split_page_results.php');

// entry/item info classes
require(DIR_WS_CLASSES . 'object_info.php');

// file uploading class
require(DIR_WS_CLASSES . 'upload.php');

// calculate category path
if (isset($_GET['cPath'])) {
	$cPath = $_GET['cPath'];
} else {
	$cPath = '';
}
if (strlen_wrapper($cPath) > 0) {
	$cPath_array = explode('_', $cPath);
	$current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
} else {
	$current_category_id = 0;
}

// default open navigation box
if (!isset($_SESSION['selected_box'])) {
	$_SESSION['selected_box'] = 'configuration';
}
if (isset($_GET['selected_box'])) {
	$_SESSION['selected_box'] = $_GET['selected_box'];
}

// the following cache blocks are used in the Tools->Cache section
// ('language' in the filename is automatically replaced by available languages)
$cache_blocks = array(
	array('title' => TEXT_CACHE_CATEGORIES, 'code' => 'categories', 'file' => 'categories_box-language.cache', 'multiple' => true),
	array('title' => TEXT_CACHE_MANUFACTURERS, 'code' => 'manufacturers', 'file' => 'manufacturers_box-language.cache', 'multiple' => true),
	array('title' => TEXT_CACHE_ALSO_PURCHASED, 'code' => 'also_purchased', 'file' => 'also_purchased-language.cache', 'multiple' => true)
);

// check if a default currency is set
if (!defined('DEFAULT_CURRENCY')) {
	$messageStack->add(ERROR_NO_DEFAULT_CURRENCY_DEFINED, 'error');
}

// check if a default language is set
if (!defined('DEFAULT_LANGUAGE')) {
	$messageStack->add(ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
}

// for Customers Status
xtc_get_customers_statuses();

if(!defined('SUPPRESS_REDIRECT') )
{
	$pagename = strtok($current_page, '.');
	if (!isset($_SESSION['customer_id'])) {
		$t_message = 'Cumstomer not found in session.';
		LogControl::get_instance()->notice($t_message, 'security', 'security');
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
	if (xtc_check_permission($pagename) == '0') {
		$t_message = 'Access denied. User (ID: ' . (int)$_SESSION['customer_id'] . ') has no permission to access page "' . $current_page . '".';
		LogControl::get_instance()->notice($t_message, 'security', 'security');
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// BOF GM_MOD GX-Customizer:
require_once('../gm/modules/gm_gprint_admin_application_top.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');

// Include Template Engine
require_once(DIR_FS_CATALOG.DIR_WS_CLASSES . 'Smarty/Smarty.class.php');

if(!isset($_SESSION['coo_page_token']))
	$_SESSION['coo_page_token'] = MainFactory::create_object('PageToken');

$coo_application_top_extender_component = MainFactory::create_object('AdminApplicationTopExtenderComponent');
$coo_application_top_extender_component->set_data('GET', $_GET);
$coo_application_top_extender_component->set_data('POST', $_POST);
$coo_application_top_extender_component->proceed();

$gmLangFileMaster = MainFactory::create_object('GMLangFileMaster');

header('Content-Type: text/html; charset=' . $_SESSION['language_charset'] . '');
