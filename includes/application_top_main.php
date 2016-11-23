<?php
/* --------------------------------------------------------------
   application_top_main.php 2016-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_top.php 1323 2005-10-27 17:58:08Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

if(file_exists(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/system/core/logging'))
{
	require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/system/core/logging/LogControl.inc.php');
}

if(file_exists(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/GProtector'))
{
	require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/GProtector/start.inc.php');
}

@ini_set('session.use_only_cookies', 0);

if(empty($_SERVER['PATH_INFO'])) {
	$_SERVER['PATH_INFO'] = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
	$_SERVER['PATH_INFO'] = strtok($_SERVER['PATH_INFO'], '?');
}

# info for shared functions
if(defined('APPLICATION_RUN_MODE') == false) define('APPLICATION_RUN_MODE', 'frontend');

$php4_3_10 = (0 == version_compare(phpversion(), "4.3.10"));
define('PHP4_3_10', $php4_3_10);
define('PROJECT_VERSION', 'xt:Commerce v3.0.4 SP2.1');
define('FIRST_GX2_TEMPLATE_VERSION', 2.0);
define('PAGE_PARSE_START_TIME', microtime(true));
define('_GM_VALID_CALL', 1);

# Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if (file_exists('includes/local/configure.php')) {
	require_once('includes/local/configure.php');
} else {
	require_once('includes/configure.php');
}

require_once(DIR_FS_INC . 'set_memory_limit.inc.php');
set_memory_limit(128);

require_once(DIR_FS_INC.'htmlentities_wrapper.inc.php');
require_once(DIR_FS_INC.'htmlspecialchars_wrapper.inc.php');
require_once(DIR_FS_INC.'html_entity_decode_wrapper.inc.php');
require_once(DIR_FS_INC.'strlen_wrapper.inc.php');
require_once(DIR_FS_INC.'substr_wrapper.inc.php');
require_once(DIR_FS_INC.'strpos_wrapper.inc.php');
require_once(DIR_FS_INC.'strrpos_wrapper.inc.php');
require_once(DIR_FS_INC.'strtolower_wrapper.inc.php');
require_once(DIR_FS_INC.'strtoupper_wrapper.inc.php');
require_once(DIR_FS_INC.'substr_count_wrapper.inc.php');
require_once(DIR_FS_INC.'utf8_encode_wrapper.inc.php');

require_once(DIR_FS_CATALOG . 'system/core/logging/LogEvent.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/LogControl.inc.php');
require_once(DIR_FS_CATALOG . 'gm/classes/ErrorHandler.php');
require_once(DIR_FS_CATALOG . 'gm/inc/check_data_type.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_env_info.inc.php');
require_once(DIR_FS_CATALOG . 'system/gngp_layer_init.inc.php');

# custom error handler with DEFAULT SETTINGS
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

$coo_timezone_setter = MainFactory::create_object('TimezoneSetter');
$coo_timezone_setter->set_date_default_timezone();

StopWatch::get_instance()->add_specific_time_stamp('start', PAGE_PARSE_START_TIME);

# custom class autoloader
spl_autoload_register(array(new MainAutoloader('frontend'), 'load'));

// Composer class autoloader.
if(!file_exists(DIR_FS_CATALOG . 'vendor/autoload.php'))
{
	throw new RuntimeException('Vendor directory is missing from the filesystem. Please install the PHP dependencies by '
	                           . 'executing the "composer install && gulp general:composer" command.');
}
require_once(DIR_FS_CATALOG . 'vendor/autoload.php');

# global debugger object
$coo_debugger = new Debugger();

# set the type of request (secure or not)
$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

if($request_type == 'SSL' || !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
	define('GM_HTTP_SERVER', HTTPS_SERVER);
} else {
	define('GM_HTTP_SERVER', HTTP_SERVER);
}

# set php_self in the local scope
$PHP_SELF = gm_get_env_info('PHP_SELF');

// include the list of project filenames
require (DIR_WS_INCLUDES.'filenames.php');

// include the list of project database tables
require (DIR_WS_INCLUDES.'database_tables.php');

// SQL caching dir
define('SQL_CACHEDIR', DIR_FS_CATALOG.'cache/');

// graduated prices model or products assigned ?
define('GRADUATED_ASSIGN', 'true');

// Database
require_once (DIR_FS_INC.'xtc_db_connect.inc.php');
require_once (DIR_FS_INC.'xtc_db_close.inc.php');
require_once (DIR_FS_INC.'xtc_db_error.inc.php');
require_once (DIR_FS_INC.'xtc_db_perform.inc.php');
require_once (DIR_FS_INC.'xtc_db_query.inc.php');
require_once (DIR_FS_INC.'xtc_db_queryCached.inc.php');
require_once (DIR_FS_INC.'xtc_db_fetch_array.inc.php');
require_once (DIR_FS_INC.'xtc_db_num_rows.inc.php');
require_once (DIR_FS_INC.'xtc_db_data_seek.inc.php');
require_once (DIR_FS_INC.'xtc_db_insert_id.inc.php');
require_once (DIR_FS_INC.'xtc_db_free_result.inc.php');
require_once (DIR_FS_INC.'xtc_db_fetch_fields.inc.php');
require_once (DIR_FS_INC.'xtc_db_output.inc.php');
require_once (DIR_FS_INC.'xtc_db_input.inc.php');
require_once (DIR_FS_INC.'xtc_db_prepare_input.inc.php');
require_once (DIR_FS_INC.'xtc_get_top_level_domain.inc.php');
require_once (DIR_FS_INC.'xtc_hide_session_id.inc.php');

// include needed functions
require_once(DIR_FS_INC . 'get_usermod.inc.php');
require_once(DIR_FS_INC . 'xtc_create_random_value.inc.php');
require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');

// html basics
require_once (DIR_FS_INC.'xtc_href_link.inc.php');
require_once (DIR_FS_INC.'xtc_draw_separator.inc.php');

require_once (DIR_FS_INC.'xtc_product_link.inc.php');
require_once (DIR_FS_INC.'xtc_category_link.inc.php');
require_once (DIR_FS_INC.'xtc_manufacturer_link.inc.php');

// html functions
require_once (DIR_FS_INC.'xtc_draw_checkbox_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_form.inc.php');
require_once (DIR_FS_INC.'xtc_draw_hidden_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_input_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_password_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_pull_down_menu.inc.php');
require_once (DIR_FS_INC.'xtc_draw_radio_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_selection_field.inc.php');
require_once (DIR_FS_INC.'xtc_draw_separator.inc.php');
require_once (DIR_FS_INC.'xtc_draw_textarea_field.inc.php');
require_once (DIR_FS_INC.'xtc_image_button.inc.php');

require_once (DIR_FS_INC.'is_mobile_template_installed.inc.php');
require_once (DIR_FS_INC.'xtc_not_null.inc.php');
require_once (DIR_FS_INC.'xtc_parse_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_product_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');
require_once (DIR_FS_INC.'xtc_get_parent_categories.inc.php');
require_once (DIR_FS_INC.'xtc_redirect.inc.php');
require_once (DIR_FS_INC.'xtc_get_uprid.inc.php');
require_once (DIR_FS_INC.'xtc_get_all_get_params.inc.php');
require_once (DIR_FS_INC.'xtc_has_product_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_image.inc.php');
require_once (DIR_FS_INC.'xtc_check_stock_attributes.inc.php');
require_once (DIR_FS_INC.'xtc_currency_exists.inc.php');
require_once (DIR_FS_INC.'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC.'xtc_get_ip_address.inc.php');
require_once (DIR_FS_INC.'xtc_setcookie.inc.php');
require_once (DIR_FS_INC.'xtc_check_agent.inc.php');
require_once (DIR_FS_INC.'xtc_count_cart.inc.php');
require_once (DIR_FS_INC.'xtc_get_qty.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate_from_desc.inc.php');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
require_once (DIR_FS_INC.'xtc_add_tax.inc.php');
require_once (DIR_FS_INC.'xtc_cleanName.inc.php');
require_once (DIR_FS_INC.'xtc_calculate_tax.inc.php');
require_once (DIR_FS_INC.'xtc_input_validation.inc.php');
require_once (DIR_FS_INC.'xtc_js_lang.php');
require_once (DIR_FS_INC.'xtc_get_products_name.inc.php');
require_once (DIR_FS_INC.'fetch_email_template.inc.php');
require_once (DIR_FS_INC.'clean_numeric_input.inc.php');
require_once (DIR_FS_INC.'country_eu_status_by_country_id.inc.php');
require_once (DIR_FS_INC.'update_customer_b2b_status.inc.php');
require_once (DIR_FS_INC.'xtc_date_raw.inc.php');
require_once (DIR_FS_INC.'xtc_create_password.inc.php');
require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');
require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');
require_once (DIR_FS_INC.'create_coupon_code.inc.php');
require_once (DIR_WS_CLASSES.'boxes.php');

require_once (DIR_FS_CATALOG . 'gm/modules/gm_gprint_application_top.php');
require_once (DIR_FS_CATALOG . 'gm/classes/GMCounter.php');
require_once (DIR_FS_CATALOG . 'gm/classes/GMLightboxControl.php');
require_once (DIR_FS_CATALOG . 'admin/includes/gm/classes/GMOpenSearch.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_clear_string.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_prepare_string.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_set_conf.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_set_content.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_content.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_convert_qty.inc.php');

# make a connection to the database... now
xtc_db_connect() or die('Unable to connect to database server!');

$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION.' WHERE configuration_key != "CURRENT_TEMPLATE"');
while ($configuration = xtc_db_fetch_array($configuration_query)) {
	define($configuration['cfgKey'], $configuration['cfgValue']);
}

$coo_timezone_setter->set_date_default_timezone(DATE_TIMEZONE);

# check GET/POST/COOKIE VARS
require (DIR_WS_CLASSES.'class.inputfilter.php');
$InputFilter = new InputFilter();
$_GET = $InputFilter->process($_GET, true);
$_POST = $InputFilter->process($_POST);

# set the top level domains
$http_domain = xtc_get_top_level_domain(HTTP_SERVER);
$https_domain = xtc_get_top_level_domain(HTTPS_SERVER);
$current_domain = (($request_type == 'NONSSL') ? $http_domain : $https_domain);

// some code to solve compatibility issues
require (DIR_WS_FUNCTIONS.'compatibility.php');

// define how the session functions will be used
require (DIR_WS_FUNCTIONS.'sessions.php');

// set the session name and save path
session_name('XTCsid');
session_save_path(DIR_FS_CATALOG . 'cache/');

session_set_cookie_params(0, '/', (xtc_not_null($current_domain) ? '.'.$current_domain : ''), false, true);

// set the session ID if it exists
if (isset ($_POST[session_name()]) && !empty($_POST[session_name()]) && preg_replace('/[^a-zA-Z0-9,-]/', "", $_POST[session_name()]) === $_POST[session_name()]) {
	session_id($_POST[session_name()]);
}
elseif (($request_type == 'SSL') && isset ($_GET[session_name()]) && !empty($_GET[session_name()]) && preg_replace('/[^a-zA-Z0-9,-]/', "", $_GET[session_name()]) === $_GET[session_name()]) {
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
	xtc_setcookie('cookie_test', 'please_accept_for_session', time() + 60 * 60 * 24 * 30, '/', $current_domain);

	if (isset ($_COOKIE['cookie_test'])) {
		session_start();
		include (DIR_WS_INCLUDES.'tracking.php');
		$session_started = true;
	}
} else {
	session_start();
	include (DIR_WS_INCLUDES.'tracking.php');
	$session_started = true;
}

$coo_application_top_lead_extender_component = MainFactory::create_object('ApplicationTopPrimalExtenderComponent');
$coo_application_top_lead_extender_component->set_data('GET', $_GET);
$coo_application_top_lead_extender_component->set_data('POST', $_POST);
$coo_application_top_lead_extender_component->proceed();

// INITIALIZE
$c_template = '';
$c_mobile_template_active = 'false';
$c_mobile_template = 'false';
$c_template_switcher = '';

$coo_mobile_control = false;
if(file_exists(DIR_FS_CATALOG . 'system/classes/mobile/MobileControl.inc.php') && is_mobile_template_installed())
{
	require_once( DIR_FS_CATALOG . 'system/classes/mobile/MobileControl.inc.php' );
	$coo_mobile_control = new MobileControl();
	
	// GET MOBILE TEMPLATE STATUS
	$c_mobile_template_active =  $coo_mobile_control->get_mobile_template_active();

	// GET MOBILE TEMPLATE
	$c_mobile_template = $coo_mobile_control->get_mobile_template();
}

// GET DEFAULT TEMPLATE
$c_default_template = '';
$t_configuration_result = xtc_db_query( 'select configuration_key as cfgKey, configuration_value as cfgValue from '.TABLE_CONFIGURATION.' WHERE configuration_key = "CURRENT_TEMPLATE"' );
if(xtc_db_num_rows( $t_configuration_result ) == 1 )
{
	$t_configuration_row = xtc_db_fetch_array( $t_configuration_result );
	if( $t_configuration_row['cfgValue'] != '' && file_exists( DIR_FS_CATALOG . 'templates/' . $t_configuration_row['cfgValue'] ) )
	{
		$c_default_template = $t_configuration_row['cfgValue'];
	}
}

if( $c_default_template == '' )
{
	die( 'No default template available' );
}

if( $c_mobile_template_active == 'true' && $c_mobile_template != 'false' )
{
	if($c_mobile_template == $c_default_template)
	{
		$c_template = $c_mobile_template;
		$c_mobile_template_active = 'true';
	}
	else
	{
		$c_template = $_GET['tpl'];
		if( $c_template == '' )
		{
			$c_template = $_SESSION[ 'tpl' ];
		}

		// check if given template is default or mobile template
		if( $c_template != $c_default_template && $c_template != $c_mobile_template )
		{
			// reset unallowed template
			$c_template = '';
		}

		if( $c_template == '' )
		{
			// check if user agent is mobile_device
			if( $coo_mobile_control->is_mobile_device() == 'true' )
			{
				$c_template = $c_mobile_template;
				$c_mobile_template_active = 'true';
			}
			else
			{
				$c_template = $c_default_template;
				$c_mobile_template_active = 'false';
			}
		} 
		else if( $c_template == $c_mobile_template )
		{
			$c_mobile_template_active = 'true';
		}
		else
		{
			$c_mobile_template_active = 'false';
		}
	}
}
else
{
	$c_template = $c_default_template;
	$c_mobile_template_active = 'false';
}

if( $coo_mobile_control != false && $c_mobile_template_active != 'false')
{
	// TEMPLATE SWITCHER
	$c_is_mobile_device = $coo_mobile_control->is_mobile_device();
	if( $c_mobile_template_active == 'false' && $c_is_mobile_device == 'true' && $c_mobile_template != 'false' )
	{
		$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('mobile_template', $_SESSION['languages_id']));
		$t_current_url = xtc_href_link( basename( $_SERVER["SCRIPT_NAME"] ), 'tpl=' . $c_mobile_template . '&' . xtc_get_all_get_params( array( 'tpl' ) ) );
		$c_template_switcher = '<a href="' . str_replace( "&amp;=", '', $t_current_url ) . '" rel="nofollow" class="button_blue button_set"><span class="button-outer"><span class="button-inner">' . $coo_text_mgr->get_text('mobile_view') . '</span></span></a><br /><br />';
	}
}

define( 'TEMPLATE_SWITCHER', $c_template_switcher );
define( 'CURRENT_TEMPLATE', $c_template );
define( 'MOBILE_ACTIVE', $c_mobile_template_active );

$_SESSION["tpl"] = CURRENT_TEMPLATE;
$_SESSION["MOBILE_ACTIVE"] = $c_mobile_template_active;

# custom error handler with USER DEFINED settings
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

clearstatcache();

# build template control instance
$coo_template_control =& MainFactory::create_object('TemplateControl', array(CURRENT_TEMPLATE), true);

# include external StyleEdit, if available
if($coo_template_control->get_template_presentation_version() >= 3
   && file_exists(DIR_FS_CATALOG . 'StyleEdit3/bootstrap.inc.php')
   && file_exists(DIR_FS_CATALOG . 'StyleEdit3/templates/' . CURRENT_TEMPLATE))
{
	try
	{
		require_once(DIR_FS_CATALOG . 'StyleEdit3/bootstrap.inc.php');
		
		$styleName = null;
		
		if(\StyleEdit\Authentication::isAuthenticated())
		{
			if(isset($_SESSION['style_edit_style_name']))
			{
				$styleName = $_SESSION['style_edit_style_name'];
			}
			
			if(isset($_GET['style_edit_style_name']) && $_GET['style_edit_style_name'] !== '')
			{
				$styleName = (string)$_GET['style_edit_style_name'];
				
				$_SESSION['style_edit_style_name'] = $styleName;
			}
		}
		elseif(isset($_SESSION['style_edit_style_name']))
		{
			unset($_SESSION['style_edit_style_name']);
		}
		
		$gmBoxesMaster = \StyleEdit\StyleConfigReader::getInstance(CURRENT_TEMPLATE, $styleName);
	}
	catch(\Exception $e)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice($e->getMessage(), 'error_handler', 'style_edit_errors');
	}
}

if($coo_template_control->get_template_presentation_version() < 3 && is_dir(DIR_FS_CATALOG.'StyleEdit/'))
{
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMCSSManager.php');
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMSESecurity.php');
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMSEDatabase.php');
	require_once(DIR_FS_CATALOG.'StyleEdit/classes/GMBoxesMaster.php');
	if(isset($_GET['style_edit_mode']))
	{
		$cooMySQLi = new GMSEDatabase(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
	}
	else
	{
		$cooMySQLi = new GMSEDatabase(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, $db_link);
	}
	$gmBoxesMaster = new GMBoxesMaster(CURRENT_TEMPLATE, $cooMySQLi);
}

$coo_template_control->reset_boxes_master();

$gmSEOBoost = MainFactory::create_object('GMSEOBoost');

require_once (DIR_FS_INC.'xtc_Security.inc.php');

function xtDBquery($query) {
	if (DB_CACHE == 'true') {
		$result = xtc_db_queryCached($query);
	} else {
		$result = xtc_db_query($query);
	}
	return $result;
}

function CacheCheck() {
	if (USE_CACHE == 'false') return false;
	if (!isset($_COOKIE['XTCsid'])) return false;
	return true;
}

// check the Agent
$truncate_session_id = false;
if (CHECK_CLIENT_AGENT) {
	if (xtc_check_agent() == 1) {
		$truncate_session_id = true;
	}
}

// verify the ssl_session_id if the feature is enabled
if (($request_type == 'SSL') && (SESSION_CHECK_SSL_SESSION_ID == 'True') && (ENABLE_SSL == true) && ($session_started == true)) {
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
	if (!isset ($_SESSION['SESSION_USER_AGENT'])) {
		$_SESSION['SESSION_USER_AGENT'] = $http_user_agent;
	}

	if ($_SESSION['SESSION_USER_AGENT'] != $http_user_agent) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// verify the IP address if the feature is enabled
if (SESSION_CHECK_IP_ADDRESS == 'True') {
	$ip_address = xtc_get_ip_address();
	if (!isset ($_SESSION['SESSION_IP_ADDRESS'])) {
		$_SESSION['SESSION_IP_ADDRESS'] = $ip_address;
	}

	if ($_SESSION['SESSION_IP_ADDRESS'] != $ip_address) {
		session_destroy();
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

// set the language
$t_language = '';
if (isset ($_GET['language']))
{
	$t_language = $_GET['language'];
}
elseif (isset($_SESSION['language']))
{
	$t_language = $_SESSION['language_code'];
}
include (DIR_WS_CLASSES.'language.php');
$lng = new language(xtc_input_validation($t_language, 'char', ''));

if(!isset($_SESSION['language']) && !isset($_GET['language']) && gm_get_conf('GM_CHECK_BROWSER_LANGUAGE') === '1')
{
	$lng->get_browser_language();
}

$_SESSION['language'] = $lng->language['directory'];
$_SESSION['languages_id'] = $lng->language['id'];
$_SESSION['language_charset'] = $lng->language['language_charset'];
$_SESSION['language_code'] = $lng->language['code'];

// needs to be initialized after $_SESSION['languages_id'] is set
$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

// include the language translations
require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/init.inc.php');

// currency
if(isset($_GET['currency']) && xtc_currency_exists($_GET['currency']))
{
	$_SESSION['currency'] = xtc_currency_exists($_GET['currency']);
}

if(!isset($_SESSION['currency']))
{
	$_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true'
	                         && xtc_currency_exists(LANGUAGE_CURRENCY)) ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
}

// write customers status in session
require (DIR_WS_INCLUDES.'write_customers_status.php');

//require (DIR_WS_CLASSES.'main.php');
$main = new main();

require (DIR_WS_CLASSES.'xtcPrice.php');
$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

// create the shopping cart & fix the cart if necesary
if (!is_object($_SESSION['cart'])) {
	$_SESSION['cart'] = new shoppingCart();
}
// create the wish list & fix the list if necesary
if (!is_object($_SESSION['wishList'])) {
  $_SESSION['wishList'] = new wishList();
}

if (!is_object($_SESSION['lightbox'])) {
  $_SESSION['lightbox'] = new GMLightboxControl();
}

require (DIR_WS_CLASSES.'product.php');

// new p URLS
if (isset ($_GET['info'])) {
	$site = explode('_', $_GET['info']);
	$pID = $site[0];
	$actual_products_id = (int) str_replace('p', '', $pID);
	$product = new product($actual_products_id);
} // also check for old 3.0.3 URLS
elseif (isset($_GET['products_id'])) {
	$actual_products_id = (int) $_GET['products_id'];
	$product = new product($actual_products_id);

}
/* BOF GM SEO MOD */
if (!is_object($product)) {
	$product = new product();
}
// new c URLS
if (isset ($_GET['cat'])) {
	$site = explode('_', $_GET['cat']);
	$cID = $site[0];
	$cID = str_replace('c', '', $cID);
	$_GET['cPath'] = xtc_get_category_path($cID);
}

/* EOF GM SEO MOD */
// new m URLS
if (isset ($_GET['manu'])) {
	$site = explode('_', $_GET['manu']);
	$mID = $site[0];
	$mID = (int)str_replace('m', '', $mID);
	$_GET['manufacturers_id'] = $mID;
}

// calculate category path
if (isset ($_GET['cPath'])) {
	$cPath = xtc_input_validation($_GET['cPath'], 'cPath', '');
}
elseif (is_object($product) && !isset ($_GET['manufacturers_id'])) {
	if ($product->isProduct()) {
		$cPath = xtc_get_product_path($actual_products_id);
	} else {
		$cPath = '';
	}
} else {
	$cPath = '';
}

if (xtc_not_null($cPath)) {
	$cPath_array = xtc_parse_category_path($cPath);
	$cPath = implode('_', $cPath_array);
	$current_category_id = end($cPath_array);
} else {
	$current_category_id = 0;
}

if (isset ($_SESSION['customer_id'])) {
	$account_type_query = xtc_db_query("SELECT
		                                    account_type,
		                                    customers_default_address_id
		                                    FROM
		                                    ".TABLE_CUSTOMERS."
		                                    WHERE customers_id = '".(int) $_SESSION['customer_id']."'");
	$account_type = xtc_db_fetch_array($account_type_query);

	// check if zone id is unset bug #0000169
	if (!isset ($_SESSION['customer_country_id'])) {
		$zone_query = xtc_db_query("SELECT  entry_country_id
				                                     FROM ".TABLE_ADDRESS_BOOK."
				                                     WHERE customers_id='".(int) $_SESSION['customer_id']."'
				                                     and address_book_id='".$account_type['customers_default_address_id']."'");

		$zone = xtc_db_fetch_array($zone_query);
		$_SESSION['customer_country_id'] = $zone['entry_country_id'];
	}
	$_SESSION['account_type'] = $account_type['account_type'];
} else {
	$_SESSION['account_type'] = '0';
}

// modification for nre graduated system
unset ($_SESSION['actual_content']);

xtc_count_cart();

require(DIR_WS_CLASSES . 'Smarty/Smarty.class.php');

$coo_application_top_extender_component = MainFactory::create_object('ApplicationTopExtenderComponent');
$coo_application_top_extender_component->set_data('GET', $_GET);
$coo_application_top_extender_component->set_data('POST', $_POST);
$coo_application_top_extender_component->proceed();

header('Content-Type: text/html; charset=' . $_SESSION['language_charset'] . '');