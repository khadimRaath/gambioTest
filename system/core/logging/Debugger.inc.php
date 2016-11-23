<?php
/* --------------------------------------------------------------
   Debugger.inc.php 2016-07-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Debugger
{
	var $v_config_array = false;
	
	protected $debugFiles;

	/*
	* constructor
	*/
	function Debugger()
	{
		$t_config = $this->get_config();
		$this->v_config_array = $t_config;
		
		if(!$this->debugFiles)
		{
			$this->debugFiles = glob(DIR_FS_CATALOG . 'debug/*.php');	
		}
	}

	function log($p_message, $p_source = 'notice', $p_type = 'general')
	{
		$t_do_log = $GLOBALS['coo_debugger']->is_enabled($p_source);
		
		if($t_do_log)
		{
			$coo_logger = LogControl::get_instance(true);
			
			$t_log_file_name = 'debug-' . $p_type;
			$t_source = $p_source;
			$t_message = $p_message;
			
			switch($p_source)
			{
				case 'error':
					$coo_logger->error($t_message, 'debugger', $t_log_file_name, $t_source, $p_level_type = 'DEBUG ERROR', E_USER_ERROR);
					break;
				case 'warning':
					$coo_logger->warning($t_message, 'debugger', $t_log_file_name, 'warning', $p_level_type = 'DEBUG WARNING', E_USER_WARNING);
					break;
				case 'notice':
				default:
					$coo_logger->notice($t_message, 'debugger', $t_log_file_name, 'notice', $p_level_type = 'DEBUG NOTICE', E_USER_NOTICE);
					break;
			}
			
			$coo_logger->write_stack(array('debugger'));
		}
	}

	function is_enabled($p_source)
	{
		$t_flag_file = DIR_FS_CATALOG.'debug/'.$p_source.'.php';

		if(in_array($t_flag_file, $this->debugFiles))
		{
			return true;
		}
		
		$t_output = false;
		if($this->v_config_array !== false)
		{
			# debug config found
			if(in_array($p_source, $this->v_config_array['ENABLED_SOURCES']))
			{
				# source output enabled in config file
				$t_output = true;
			}
		}
		return $t_output;
	}

	function get_config()
	{
		$t_output = false;
		$t_config_file = DIR_FS_CATALOG.'debug/debug_config.inc.php';

		if(file_exists($t_config_file) == true)
		{
			$t_output = true;
			
			# load found config file
			include($t_config_file);

			# check config array and load
			if(isset($t_debug_config) && is_array($t_debug_config))
			{
				$t_output = $t_debug_config;
			}
		}
		return $t_output;
	}

}
?>