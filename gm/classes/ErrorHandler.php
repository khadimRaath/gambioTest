<?php
/* --------------------------------------------------------------
   ErrorHandler.php 2015-08-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ErrorHandler 
{
	function __construct()
	{
		// display php errors
		$displayErrors = '1';
		if(file_exists(DIR_FS_CATALOG . 'debug/no_error_output.php') 
			|| file_exists(DIR_FS_CATALOG . 'cache/no_error_output.php'))
		{
			$displayErrors = '0';
		}
		ini_set('display_errors', $displayErrors);
		
		// intercepts E_NOTICE in older php versions (<5.3)
		if(defined('E_DEPRECATED') == false)
		{
			define('E_DEPRECATED', 0);
			define('E_USER_DEPRECATED', 0);
		}

		// intercepts E_NOTICE in older php versions (<5)
		if(defined('E_STRICT') == false)
		{
			define('E_STRICT', 0);
		}
		
		if(defined('STORE_NAME')) // use custom error_reporting level
		{
			if(ERROR_REPORT_HIDE_E_ERROR == 'True')
			{
				$E_ERROR = E_ERROR;
			}
			else
			{
				$E_ERROR = 0;
			}
			if(ERROR_REPORT_HIDE_E_WARNING == 'True')
			{
				$E_WARNING = E_WARNING;
			}
			else
			{
				$E_WARNING = 0;
			}
			if(ERROR_REPORT_HIDE_E_PARSE == 'True')
			{
				$E_PARSE = E_PARSE;
			}
			else
			{
				$E_PARSE = 0;
			}
			if(ERROR_REPORT_HIDE_E_NOTICE == 'True')
			{
				$E_NOTICE = E_NOTICE;
			}
			else
			{
				$E_NOTICE = 0;
			}
			if(ERROR_REPORT_HIDE_E_CORE_ERROR == 'True')
			{
				$E_CORE_ERROR = E_CORE_ERROR;
			}
			else
			{
				$E_CORE_ERROR = 0;
			}
			if(ERROR_REPORT_HIDE_E_CORE_WARNING == 'True')
			{
				$E_CORE_WARNING = E_CORE_WARNING;
			}
			else
			{
				$E_CORE_WARNING = 0;
			}
			if(ERROR_REPORT_HIDE_E_COMPILE_ERROR == 'True')
			{
				$E_COMPILE_ERROR = E_COMPILE_ERROR;
			}
			else
			{
				$E_COMPILE_ERROR = 0;
			}
			if(ERROR_REPORT_HIDE_E_COMPILE_WARNING == 'True')
			{
				$E_COMPILE_WARNING = E_COMPILE_WARNING;
			}
			else
			{
				$E_COMPILE_WARNING = 0;
			}
			if(ERROR_REPORT_HIDE_E_USER_ERROR == 'True')
			{
				$E_USER_ERROR = E_USER_ERROR;
			}
			else
			{
				$E_USER_ERROR = 0;
			}
			if(ERROR_REPORT_HIDE_E_USER_WARNING == 'True')
			{
				$E_USER_WARNING = E_USER_WARNING;
			}
			else
			{
				$E_USER_WARNING = 0;
			}
			if(ERROR_REPORT_HIDE_E_USER_NOTICE == 'True')
			{
				$E_USER_NOTICE = E_USER_NOTICE;
			}
			else
			{
				$E_USER_NOTICE = 0;
			}
			if(ERROR_REPORT_HIDE_E_ALL == 'True')
			{
				$E_ALL = E_ALL;
			}
			else
			{
				$E_ALL = 0;
			}
			if(ERROR_REPORT_HIDE_E_STRICT == 'True')
			{
				$E_STRICT = E_STRICT;
			}
			else
			{
				$E_STRICT = 0;
			}
			if(ERROR_REPORT_HIDE_E_RECOVERABLE_ERROR == 'True')
			{
				$E_RECOVERABLE_ERROR = E_RECOVERABLE_ERROR;
			}
			else
			{
				$E_RECOVERABLE_ERROR = 0;
			}
			if(ERROR_REPORT_HIDE_E_DEPRECATED == 'True')
			{
				$E_DEPRECATED = E_DEPRECATED;
			}
			else
			{
				$E_DEPRECATED = 0;
			}
			if(ERROR_REPORT_HIDE_E_USER_DEPRECATED == 'True')
			{
				$E_USER_DEPRECATED = E_USER_DEPRECATED;
			}
			else
			{
				$E_USER_DEPRECATED = 0;
			}

			error_reporting(
				E_ALL 
				& ~$E_ERROR 
				& ~$E_WARNING 
				& ~$E_PARSE 
				& ~$E_NOTICE 
				& ~$E_CORE_ERROR 
				& ~$E_CORE_WARNING 
				& ~$E_COMPILE_ERROR 
				& ~$E_COMPILE_WARNING 
				& ~$E_USER_ERROR 
				& ~$E_USER_WARNING 
				& ~$E_USER_NOTICE 
				& ~$E_ALL 
				& ~$E_STRICT 
				& ~$E_RECOVERABLE_ERROR 
				& ~$E_DEPRECATED 
				& ~$E_USER_DEPRECATED
			);
		}
		else // use default error_reporting level
		{
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
		}
	}
	
	/*
	* @desc: Error handling function
	*/
	function HandleError($p_errno, $p_errstr, $p_errfile, $p_errline, $p_errcontext) 
	{
		if(defined('UNIT_TEST_RUNNING') && UNIT_TEST_RUNNING === true)
		{
			return false;
		}
			
		// should we show this error code?
		if((error_reporting() & $p_errno) == false)
		{
			return true;
		}
		
		$coo_logger = LogControl::get_instance();
		$t_additional_info = '';
		
		if(function_exists('http_response_code')
		   && ($p_errno === E_ERROR || $p_errno === E_PARSE
		       || $p_errno === E_CORE_ERROR
		       || $p_errno === E_COMPILE_ERROR
		       || $p_errno === E_RECOVERABLE_ERROR
		       || $p_errno === E_USER_ERROR)
		)
		{
			http_response_code(500);
		}
		
		switch($p_errno)
		{
			case E_ERROR: // can't be caught by version 5.2
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'FATAL ERROR';
				}
			case E_PARSE: // can't be caught by version 5.2
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'PARSE ERROR';
				}
			case E_CORE_ERROR: // can't be caught by version 5.2
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'CORE ERROR';
				}
			case E_COMPILE_ERROR: // can't be caught by version 5.2
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'COMPILE ERROR';
				}
			case E_RECOVERABLE_ERROR:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'RECOVERABLE ERROR';
				}
				$t_additional_info = ob_get_clean(); // executed for every error case except E_USER_ERROR
			case E_USER_ERROR:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'USER ERROR';
				}
				
				// executed for every error case
				$t_level = 'error';
				$t_fatal_error_data_array = array();
				$t_fatal_error_data_array['error_number'] = $p_errno;
				$t_fatal_error_data_array['message'] = $p_errstr;
				$t_fatal_error_data_array['file'] = $p_errfile;
				$t_fatal_error_data_array['line'] = $p_errline;
				$coo_logger->set_fatal_error_data_array($t_fatal_error_data_array);
				$coo_logger->write_stack();
				$coo_logger->set_event_stack_enabled(false);
				$this->flush_output_buffer();
				$coo_logger->error($p_errstr, 'error_handler', 'errors', $t_level, $t_level_type, $p_errno, $t_additional_info);
				die();
				break;
			
			case E_COMPILE_WARNING: // can't be caught by version 5.2
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'COMPILE WARNING';
				}
			case E_WARNING:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'WARNING';
				}
			case E_USER_WARNING:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'USER WARNING';
				}
			case E_CORE_WARNING:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'CORE WARNING';
				}
				$t_level = 'warning';
				$coo_logger->warning($p_errstr, 'error_handler', 'errors', $t_level, $t_level_type, $p_errno);
				if($coo_logger->get_event_stack_enabled())
				{
					$coo_logger->write_stack();
				}
				
				$this->flush_output_buffer();
				break;
			
			case E_STRICT: // can't be caught by version 5.2
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'STRICT';
				}
				$t_level = 'warning';
				$coo_logger->warning($p_errstr, 'error_handler', 'errors', $t_level, $t_level_type, $p_errno);
				if($coo_logger->get_event_stack_enabled())
				{
					$coo_logger->write_stack();
				}
				
				$this->flush_output_buffer();
				break;
			
			// Available in php 5.3
			case E_USER_DEPRECATED:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'USER DEPRECATED';
				}
			case E_DEPRECATED:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'DEPRECATED';
				}
				$t_level = 'warning';
				$coo_logger->warning($p_errstr, 'error_handler', 'errors', $t_level, $t_level_type, $p_errno);
				if($coo_logger->get_event_stack_enabled())
				{
					$coo_logger->write_stack();
				}
				
				$this->flush_output_buffer();
				break;
			
			case E_USER_NOTICE:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'USER NOTICE';
				}
			case E_NOTICE:
				if(isset($t_level_type) == false)
				{
					$t_level_type = 'NOTICE';
				}
				$t_level = 'notice';
				$coo_logger->notice($p_errstr, 'error_handler', 'errors', $t_level, $t_level_type, $p_errno);
				if($coo_logger->get_event_stack_enabled())
				{
					$coo_logger->write_stack();
				}
				
				$this->flush_output_buffer();
				break;
			
			default:
				$t_level = 'warning';
				$coo_logger->warning($p_errstr, 'error_handler', 'errors', $t_level, 'UNKNOWN ERROR', $p_errno);
				if($coo_logger->get_event_stack_enabled())
				{
					$coo_logger->write_stack();
				}
				
				$this->flush_output_buffer();
				break;
		}
		return true; // don't execute php internal error handler
	}
	
	function flush_output_buffer()
	{
		if(ob_get_contents() != '')
		{
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression', 'Off');
			}
			ob_end_flush();
		}
	}

	function shutdown()
	{
		$t_error_array = error_get_last();

		if(isset($t_error_array))
		{
			$this->HandleError($t_error_array['type'], $t_error_array['message'], $t_error_array['file'], $t_error_array['line'], null);
		}
	}
}
