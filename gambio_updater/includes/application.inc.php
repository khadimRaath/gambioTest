<?php
/* --------------------------------------------------------------
  application.inc.php 2015-09-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

@date_default_timezone_set('Europe/Berlin');

if(file_exists(dirname(__FILE__) . '/../../includes/local/configure.php'))
{
	require_once(dirname(__FILE__) . '/../../includes/local/configure.php');
}
else
{
	require_once(dirname(__FILE__) . '/../../includes/configure.php');
}

require_once(DIR_FS_CATALOG . 'system/core/logging/LogEvent.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/LogControl.inc.php');
require_once(DIR_FS_CATALOG . 'gm/classes/ErrorHandler.php');

register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

function debug_notice($p_message, $p_group = 'updater', $p_log_file_name = 'updater_debug', $p_level = 'notice',
						$p_level_type = 'UPDATER INFO', $p_error_code = 0, $p_additional_info = '')
{
	if($p_additional_info != '')
	{
		$t_additional_info = $p_additional_info;
	}
	else
	{
		$t_additional_info = 'User agent: ' . $_SERVER['HTTP_USER_AGENT'] . "\n";
		$t_censorship_array = array('password', 'FTP_PASSWORD');
			
		if(isset($_GET) && empty($_GET) == false)
		{
			$t_additional_info .= 'GET:';
			foreach($_GET as $t_key => $t_value)
			{
				if(in_array($t_key, $t_censorship_array))
				{
					$t_value = '*****';
				}

				$t_additional_info .= "\t" . $t_key . ': ' . str_replace("\n", "\n\t", var_export($t_value, true)) . "\n";
			}
		}

		if(isset($_POST) && empty($_POST) == false)
		{
			$t_additional_info .= 'POST:';
			foreach($_POST as $t_key => $t_value)
			{
				if(in_array($t_key, $t_censorship_array))
				{
					$t_value = '*****';
				}
				
				$t_additional_info .= "\t" . $t_key . ': ' . str_replace("\n", "\n\t", var_export($t_value, true)) . "\n";
			}
		}
	}
	
	$coo_logger = LogControl::get_instance();
	$coo_logger->notice($p_message, $p_group, $p_log_file_name, $p_level, $p_level_type, $p_error_code, $t_additional_info);
}

if(!defined(SEND_NO_HEADER) && SEND_NO_HEADER !== true)
{
	header('Content-Type: text/html; charset=utf-8');
}