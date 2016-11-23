<?php
/* --------------------------------------------------------------
  LogConfigurationPresets.inc.php 2015-07-06 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class LogConfigurationPresets
{
	public static function load_default_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_output('output');
		$p_coo_configuration->set('notice', 'screen', 'output', false, false);
		$p_coo_configuration->set_for_output_type_and_output('file', 'filepath');
		$p_coo_configuration->set_for_output_type_and_output('file', 'backtrace');
		$p_coo_configuration->set_for_output_type_and_output('file', 'request_data');
		$p_coo_configuration->set_for_output_type_and_output('html_file', 'filepath');
		$p_coo_configuration->set_for_output_type_and_output('html_file', 'backtrace');
		$p_coo_configuration->set_for_output_type_and_output('html_file', 'request_data');
		$p_coo_configuration->set_for_output_type_and_output('mail', 'filepath');
		$p_coo_configuration->set_for_output_type_and_output('mail', 'backtrace');
		$p_coo_configuration->set_for_output_type_and_output('mail', 'request_data');
		
		return true;
	}
	
	public static function load_group_error_handler_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_level('error');
		$p_coo_configuration->set_for_level('warning');
		$p_coo_configuration->set_for_level('notice', false, false);
		$p_coo_configuration->set_for_output_type('screen', false, false);
		$p_coo_configuration->set_for_output_type_and_output('screen', 'output', true);
		$p_coo_configuration->set_for_output('class_data', false, false);
		$p_coo_configuration->set_for_output('function_data', false, false);
		$p_coo_configuration->set_for_output('session_data', false, false);

		return true;
	}
	
	public static function load_group_time_log_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_all(false, false);
		
		return true;
	}
	
	public static function load_group_sql_queries_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_all(false, false);
		$p_coo_configuration->set('error', 'file', 'output');
		$p_coo_configuration->set('error', 'html_file', 'output');
		$p_coo_configuration->set('warning', 'file', 'output');
		$p_coo_configuration->set('warning', 'html_file', 'output');
		$p_coo_configuration->set('notice', 'file', 'output');
		$p_coo_configuration->set('notice', 'html_file', 'output');
		
		return true;
	}
	
	public static function load_group_debugger_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_all(false, false);
		
		return true;
	}
	
	public static function load_group_security_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_all(false, false);
		$p_coo_configuration->set_for_level_and_output_type('error', 'file');
		$p_coo_configuration->set_for_level_and_output_type('error', 'html_file');
		$p_coo_configuration->set_for_level_and_output_type('error', 'mail');
		$p_coo_configuration->set_for_level_and_output_type('warning', 'file');
		$p_coo_configuration->set_for_level_and_output_type('warning', 'html_file');
		$p_coo_configuration->set_for_level_and_output_type('warning', 'mail');
		$p_coo_configuration->set_for_level_and_output('notice', 'output');
		$p_coo_configuration->set_for_level_and_output('notice', 'filepath');
		$p_coo_configuration->set_for_level_and_output('notice', 'backtrace');
		$p_coo_configuration->set_for_level_and_output('notice', 'request_data');
		$p_coo_configuration->set_for_level_and_output_type('notice', 'screen', false, false);
		
		return true;
	}
	
	public static function load_group_full_debug_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_all();
		
		return true;
	}
	
	public static function load_group_updater_preset(LogConfiguration $p_coo_configuration)
	{
		$p_coo_configuration->set_for_output_type('file');
		$p_coo_configuration->set_for_output_type('html_file');
		$p_coo_configuration->set_for_output_type('mail');

		$p_coo_configuration->set_for_level('notice', false, false);
		$p_coo_configuration->set_for_level_and_output('notice', 'output');
		$p_coo_configuration->set('notice', 'screen', 'output', false, false);
		
		return true;
	}
}