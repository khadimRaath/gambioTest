<?php
/* --------------------------------------------------------------
   application_top_callback.php 2015-06-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003	 nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_top_callback.php 149 2007-01-24 09:46:13Z mzanier $) 

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon
    
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

# info for shared functions
if(defined('APPLICATION_RUN_MODE') == false)
{
	define('APPLICATION_RUN_MODE', 'frontend');
}

// start the timer for the page parse time log
define('PAGE_PARSE_START_TIME', microtime(true));

// Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if(file_exists('../../includes/local/configure.php'))
{
	require_once('../../includes/local/configure.php');
}
else
{
	require_once('../../includes/configure.php');
}

require_once(DIR_FS_INC . 'set_memory_limit.inc.php');
set_memory_limit(128);

require_once(DIR_FS_INC . 'htmlentities_wrapper.inc.php');
require_once(DIR_FS_INC . 'htmlspecialchars_wrapper.inc.php');
require_once(DIR_FS_INC . 'html_entity_decode_wrapper.inc.php');
require_once(DIR_FS_INC . 'strlen_wrapper.inc.php');
require_once(DIR_FS_INC . 'substr_wrapper.inc.php');
require_once(DIR_FS_INC . 'strpos_wrapper.inc.php');
require_once(DIR_FS_INC . 'strrpos_wrapper.inc.php');
require_once(DIR_FS_INC . 'strtolower_wrapper.inc.php');
require_once(DIR_FS_INC . 'strtoupper_wrapper.inc.php');
require_once(DIR_FS_INC . 'substr_count_wrapper.inc.php');
require_once(DIR_FS_INC . 'utf8_encode_wrapper.inc.php');

require_once(DIR_FS_CATALOG . 'system/gngp_layer_init.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/LogEvent.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/LogControl.inc.php');
require_once(DIR_FS_CATALOG . 'gm/classes/ErrorHandler.php');
require_once(DIR_FS_CATALOG . 'gm/inc/check_data_type.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_env_info.inc.php');

# custom error handler with DEFAULT SETTINGS
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

$coo_timezone_setter = MainFactory::create_object('TimezoneSetter');
$coo_timezone_setter->set_date_default_timezone();

StopWatch::get_instance()->add_specific_time_stamp('start', PAGE_PARSE_START_TIME);

# custom class autoloader
spl_autoload_register(array(new MainAutoloader('frontend'), 'load'));

# global debugger object
$coo_debugger = new Debugger();


// define the project version
define('PROJECT_VERSION', 'xt:Commerce v3.0.4 SP2');

// set the type of request (secure or not)
$request_type = (getenv('HTTPS') == '1' || getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

// set php_self in the local scope
$PHP_SELF = $_SERVER['PHP_SELF'];

// include the list of project filenames
require(DIR_WS_INCLUDES . 'filenames.php');

// include the list of project database tables
require(DIR_WS_INCLUDES . 'database_tables.php');


// Store DB-Querys in a Log File
define('STORE_DB_TRANSACTIONS', 'false');

// include used functions
require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
require_once(DIR_FS_INC . 'xtc_db_error.inc.php');
require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');
require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
require_once(DIR_FS_INC . 'xtc_db_data_seek.inc.php');
require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_fields.inc.php');
require_once(DIR_FS_INC . 'xtc_db_output.inc.php');
require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');

require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

require_once(DIR_FS_INC . 'xtc_product_link.inc.php');
require_once(DIR_FS_INC . 'xtc_category_link.inc.php');

// html functions
require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_hidden_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_password_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_radio_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_selection_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_textarea_field.inc.php');
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');

require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_update_whos_online.inc.php');
require_once(DIR_FS_INC . 'xtc_activate_banners.inc.php');
require_once(DIR_FS_INC . 'xtc_expire_banners.inc.php');
require_once(DIR_FS_INC . 'xtc_expire_specials.inc.php');
require_once(DIR_FS_INC . 'xtc_parse_category_path.inc.php');
require_once(DIR_FS_INC . 'xtc_get_product_path.inc.php');

//require_once (DIR_FS_INC.'xtc_get_category_path.inc.php');

require_once(DIR_FS_INC . 'xtc_get_parent_categories.inc.php');
require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_get_uprid.inc.php');
require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
require_once(DIR_FS_INC . 'xtc_has_product_attributes.inc.php');
require_once(DIR_FS_INC . 'xtc_image.inc.php');
require_once(DIR_FS_INC . 'xtc_check_stock_attributes.inc.php');
require_once(DIR_FS_INC . 'xtc_currency_exists.inc.php');
require_once(DIR_FS_INC . 'xtc_remove_non_numeric.inc.php');
require_once(DIR_FS_INC . 'xtc_get_ip_address.inc.php');
require_once(DIR_FS_INC . 'xtc_setcookie.inc.php');
require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
require_once(DIR_FS_INC . 'xtc_count_cart.inc.php');
require_once(DIR_FS_INC . 'xtc_get_qty.inc.php');
require_once(DIR_FS_INC . 'create_coupon_code.inc.php');
require_once(DIR_FS_INC . 'xtc_gv_account_update.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_rate_from_desc.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
require_once(DIR_FS_INC . 'xtc_cleanName.inc.php');
require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');
require_once(DIR_FS_INC . 'xtc_input_validation.inc.php');
require_once(DIR_FS_INC . 'fetch_email_template.inc.php');
require_once(DIR_FS_INC . 'clean_numeric_input.inc.php');
require_once(DIR_FS_INC . 'country_eu_status_by_country_id.inc.php');
require_once(DIR_FS_INC . 'update_customer_b2b_status.inc.php');
require_once(DIR_FS_INC . 'xtc_write_user_info.inc.php');
require_once(DIR_FS_INC . 'xtc_validate_email.inc.php');

// make a connection to the database... now
xtc_db_connect() or die('Unable to connect to database server!');

// set the application parameters
$configuration_query = xtc_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' .
									TABLE_CONFIGURATION);
while($configuration = xtc_db_fetch_array($configuration_query))
{
	define($configuration['cfgKey'], $configuration['cfgValue']);
}

$coo_timezone_setter->set_date_default_timezone(DATE_TIMEZONE);

# custom error handler with DEFAULT SETTINGS
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

require(DIR_WS_FUNCTIONS . 'sessions.php');

if(!isset($_SESSION['coo_page_token']))
{
	$_SESSION['coo_page_token'] = MainFactory::create_object('PageToken');
}