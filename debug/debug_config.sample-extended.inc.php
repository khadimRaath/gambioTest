<?php
/* --------------------------------------------------------------
   debug_config.inc.php 2014-03-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

# sample debug code:
# $GLOBALS['coo_debugger']->log('LOG TEXT', 'SOURCE');


$t_debug_config = array
(
	'ENABLED_SOURCES' => array()
);

$t_debug_config['ENABLED_SOURCES'][] = 'notice';
$t_debug_config['ENABLED_SOURCES'][] = 'warning';
$t_debug_config['ENABLED_SOURCES'][] = 'error';
$t_debug_config['ENABLED_SOURCES'][] = 'security';

$t_debug_config['ENABLED_SOURCES'][] = 'smarty_compile_check';
$t_debug_config['ENABLED_SOURCES'][] = 'print_sql_on_error';
$t_debug_config['ENABLED_SOURCES'][] = 'uncompressed_js';
$t_debug_config['ENABLED_SOURCES'][] = 'class_overloading';
$t_debug_config['ENABLED_SOURCES'][] = 'include_usermod_requested';
$t_debug_config['ENABLED_SOURCES'][] = 'include_usermod_found';

$t_debug_config['ENABLED_SOURCES'][] = 'js';
//$t_debug_config['ENABLED_SOURCES'][] = 'StopWatch';
//$t_debug_config['ENABLED_SOURCES'][] = 'PageParseTime';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_sql_queries';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_js_errors';

//$t_debug_config['ENABLED_SOURCES'][] = 'Properties';
//$t_debug_config['ENABLED_SOURCES'][] = 'FilterManager';
//$t_debug_config['ENABLED_SOURCES'][] = 'IndexFeatureProductFinder';
//$t_debug_config['ENABLED_SOURCES'][] = 'TemplateControl';

//$t_debug_config['ENABLED_SOURCES'][] = 'ClassRegistry';
//$t_debug_config['ENABLED_SOURCES'][] = 'DataCache';
//$t_debug_config['ENABLED_SOURCES'][] = 'DataCache_disable_cache';
//$t_debug_config['ENABLED_SOURCES'][] = 'DataCache_disable_persistent';
//$t_debug_config['ENABLED_SOURCES'][] = 'SmartyCache';
//$t_debug_config['ENABLED_SOURCES'][] = 'GMDataObjectGroup';
//$t_debug_config['ENABLED_SOURCES'][] = 'hide_styleedit';
//$t_debug_config['ENABLED_SOURCES'][] = 'error_code_snippet';

//$t_debug_config['ENABLED_SOURCES'][] = 'execute_deprecated';

$t_debug_config['ENABLED_SOURCES'][] = 'log_error';
$t_debug_config['ENABLED_SOURCES'][] = 'log_warning';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_notice';

$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_output';
$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_filepath';
$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_backtrace';
$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_code_snippet';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_request_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_function_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_class_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_screen_session_data';

$t_debug_config['ENABLED_SOURCES'][] = 'log_file_output';
$t_debug_config['ENABLED_SOURCES'][] = 'log_file_filepath';
$t_debug_config['ENABLED_SOURCES'][] = 'log_file_request_data';
$t_debug_config['ENABLED_SOURCES'][] = 'log_file_backtrace';
$t_debug_config['ENABLED_SOURCES'][] = 'log_file_code_snippet';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_file_function_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_file_class_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_file_session_data';

$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_output';
$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_filepath';
$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_request_data';
$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_backtrace';
$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_code_snippet';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_function_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_class_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_html_file_session_data';

$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_output';
$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_filepath';
$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_request_data';
$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_backtrace';
$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_code_snippet';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_function_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_class_data';
//$t_debug_config['ENABLED_SOURCES'][] = 'log_mail_session_data';