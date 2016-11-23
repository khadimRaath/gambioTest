<?php
/* --------------------------------------------------------------
   LogControl.php 2015-08-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('SHOP_ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require_once(SHOP_ROOT . 'system/core/logging/LogConfiguration.inc.php');
require_once(SHOP_ROOT . 'system/core/logging/LogEvent.inc.php');
require_once(SHOP_ROOT . 'system/core/logging/StopWatch.inc.php');

class LogControl
{
	const FILE_EXTENSION				= '.log';
	const GZIP_FILE_EXTENSION			= '.gz';
	const LOGFILES_DIR					= 'logfiles/';
	const DEFAULT_TEXT_LOG_FILE_NAME	= 'debug-general';

	static protected $instance = null;

	protected $configuration;
	protected $group_configuration_array;

	protected $admin_mail;
	protected $enabled;
	protected $event_stack;
	protected $event_stack_enabled;
	protected $fatal_error_data_array;
	protected $file_suffix;
	protected $max_file_size;

	protected $error_count;
	protected $warning_count;
	protected $notice_count;

	protected $stop_watch;


	protected function __construct($p_enabled)
	{
		if(defined('LOGGING_ENABLED'))
		{
			$this->set_enabled((boolean)LOGGING_ENABLED == 'true');
		}
		else
		{
			$this->set_enabled($p_enabled);
		}

		if(defined('SQL_LOG_MAX_FILESIZE') && (double)SQL_LOG_MAX_FILESIZE > 0)
		{
			$this->max_filesize = (double)SQL_LOG_MAX_FILESIZE * 1024 * 1024;
		}
		else
		{
			$this->max_filesize = 1 * 1024 * 1024;
		}

		$this->event_stack = array();
		$this->event_stack_enabled = false;
		$this->file_suffix = $this->get_secure_token();
		$this->configuration = new LogConfiguration();
		$this->group_configuration_array = array();
		$this->error_count = 0;
		$this->warning_count = 0;
		$this->notice_count = 0;
		$this->stop_watch = StopWatch::get_instance();

		if(defined('ERROR_REPORT_EMAIL') && ERROR_REPORT_EMAIL != '')
		{
			$this->set_admin_mail(ERROR_REPORT_EMAIL);
		}
	}


	static public function get_instance($p_enabled = true)
	{
		if(self::$instance === null)
		{
			self::$instance = new self($p_enabled);
		}

		if(defined('LOGGING_ENABLED'))
		{
			self::$instance->set_enabled(LOGGING_ENABLED == 'true');
		}
		else
		{
			self::$instance->set_enabled($p_enabled);
		}

		return self::$instance;
	}


	public static function get_secure_token()
	{
		static $t_secure_token;

		if($t_secure_token === null && is_dir(SHOP_ROOT . 'media'))
		{
			$t_dh = opendir(SHOP_ROOT . 'media');
			if($t_dh !== false)
			{
				while(($t_file = readdir($t_dh)) !== false)
				{
					// search for secure token file
					if(strpos($t_file, 'secure_token_') !== false)
					{
						$t_secure_token = str_replace('secure_token_', '', $t_file);
						break;
					}
				}

				if($t_secure_token === null)
				{
					$t_secure_token = md5(mt_rand());

					$fp = fopen(SHOP_ROOT . 'media/secure_token_' . $t_secure_token, 'w');
					fwrite($fp, '.');
					fclose($fp);
				}
			}
		}

		return $t_secure_token;
	}


	public function error($p_message, $p_group = '', $p_log_file_name = 'debug', $p_level = 'error', $p_level_type = 'USER ERROR', $p_error_code = E_USER_ERROR, $p_additional_info = '', $p_coo_configuration = null)
	{
		$this->error_count++;
		$this->event_stack_enabled = false;
		$this->create_event($p_message, $p_group, $p_log_file_name, $p_level, $p_level_type, $p_error_code, $p_additional_info, $p_coo_configuration);
	}

	public function warning($p_message, $p_group = '', $p_log_file_name = 'debug', $p_level = 'warning', $p_level_type = 'USER WARNING', $p_error_code = E_USER_WARNING, $p_additional_info = '', $p_coo_configuration = null)
	{
		$this->warning_count++;
		$this->create_event($p_message, $p_group, $p_log_file_name, $p_level, $p_level_type, $p_error_code, $p_additional_info, $p_coo_configuration);
	}

	public function notice($p_message, $p_group = '', $p_log_file_name = 'debug', $p_level = 'notice', $p_level_type = 'USER NOTICE', $p_error_code = 0, $p_additional_info = '', $p_coo_configuration = null)
	{
		$this->notice_count++;
		$this->create_event($p_message, $p_group, $p_log_file_name, $p_level, $p_level_type, $p_error_code, $p_additional_info, $p_coo_configuration);
	}

	protected function create_event($p_message, $p_group, $p_log_file_name, $p_level, $p_level_type, $p_error_code, $p_additional_info, $p_coo_configuration = null)
	{
		$coo_configuration = $this->fetch_configuration($p_group);
		if(is_null($p_coo_configuration) == false)
		{
			$coo_configuration = new LogConfiguration($p_group);
		}

		$coo_log_event = new LogEvent($p_message, $p_group, $p_log_file_name, $p_level, $p_level_type, $p_error_code, $p_additional_info, $coo_configuration);

		$t_filepath = $coo_log_event->get_file();
		if(isset($this->fatal_error_data_array['file']) && empty($t_filepath))
		{
			$coo_log_event->set_file($this->fatal_error_data_array['file']);
		}

		$t_line = $coo_log_event->get_line();
		if(isset($this->fatal_error_data_array['line']) && empty($t_line))
		{
			$coo_log_event->set_line($this->fatal_error_data_array['line']);
		}

		$this->event_stack[] = $coo_log_event;

		if($this->event_stack_enabled == false)
		{
			$this->write_stack(array($p_group));
		}
	}

	public function fetch_configuration($p_group = '', $p_load_debug_configuration = true)
	{
		if(empty($p_group) == false)
		{
			if(isset($this->group_configuration_array[$p_group]))
			{
				return $this->group_configuration_array[$p_group];
			}

			$this->group_configuration_array[$p_group] = new LogConfiguration($p_group, $p_load_debug_configuration);
			return $this->group_configuration_array[$p_group];
		}

		return $this->configuration;
	}

	public function clear_stack(array $p_group_array = array())
	{
		if(empty($p_group_array))
		{
			$this->event_stack = array();
			return;
		}

		foreach($this->event_stack as $t_key => $coo_log_event)
		{
			if(in_array($coo_log_event->get_group(), $p_group_array))
			{
				unset($this->event_stack[$t_key]);
			}
		}
	}


	public function write_stack(array $p_group_array = array(), array $p_output_type_array = array(), $p_clear_stack = true)
	{
		if($this->enabled == false)
		{
			// log deactivated. cancel write.
			return true;
		}

		$t_mail_messages_array = array();

		foreach($this->event_stack as $coo_log_event)
		{
			$t_group = $coo_log_event->get_group();
			$t_level = $coo_log_event->get_level();
			if(empty($p_group_array) == false && in_array($t_group, $p_group_array) == false)
			{
				continue;
			}

			$coo_configuration = $this->fetch_configuration($t_group);
			if(empty($p_output_type_array) == false)
			{
				$t_output_type_array = $p_output_type_array;
			}
			else
			{
				$t_output_type_array = $coo_configuration->get_output_type_array();
			}

			foreach($t_output_type_array as $t_output_type)
			{
				if($coo_configuration->is_active_output_type($t_level, $t_output_type) == false || empty($p_output_type_array) == false && in_array($t_output_type, $p_output_type_array) == false)
				{
					continue;
				}

				$t_output = $coo_log_event->get_output($t_output_type);

				switch($t_output_type)
				{
					case 'screen':
						if(!file_exists(DIR_FS_CATALOG . 'debug/no_error_output.php') 
							&& !file_exists(DIR_FS_CATALOG . 'cache/no_error_output.php'))
						{
							echo $t_output;
						}
						break;
					case 'html_file':
					case 'file':
						$t_file_extension = LogControl::FILE_EXTENSION;
						if($t_output_type == 'html_file')
						{
							$t_file_extension = '.html';
						}
						$this->write_to_file($t_output, $coo_log_event->get_log_file_name(), $t_file_extension);
						break;
					case 'mail':
						$t_mail_messages_array[] = $t_output;
						break;
					case 'database':
						// not supported yet
						break;
				}
			}
		}

		if(empty($t_mail_messages_array) == false)
		{
			$t_message = implode('', $t_mail_messages_array);

			$this->mail($t_message);
		}

		if($p_clear_stack)
		{
			$this->clear_stack($p_group_array);
		}
	}


	public function write_sql_log($p_query = '', $p_type = 'QUERY', $p_level = 'notice', $p_error_code = 0, $p_error_description = '', $p_output_type_array = array())
	{
		if(($this->enabled == false || defined('APPLICATION_RUN_MODE') == false
			|| defined('LOG_SQL_BACKEND') == false || defined('LOG_SQL_FRONTEND') == false
			|| APPLICATION_RUN_MODE == 'frontend' && LOG_SQL_FRONTEND == 'false'
			|| APPLICATION_RUN_MODE == 'backend' && LOG_SQL_BACKEND == 'false')
			&& (is_object($GLOBALS['coo_debugger']) == false || $GLOBALS['coo_debugger']->is_enabled('log_sql_queries') == false)
			&& $p_level != 'error')
		{
			// log deactivated. cancel write.
			return true;
		}

		$coo_configuration = $this->fetch_configuration('sql_queries');
		if(empty($p_output_type_array) == false)
		{
			$t_output_type_array = $p_output_type_array;
		}
		else
		{
			$t_output_type_array = $coo_configuration->get_output_type_array();
		}

		if(empty($t_output_type_array))
		{
			return true;
		}

		$t_group = 'sql_queries';
		$t_log_file_name = 'sql_queries';

		if(empty($p_error_description))
		{
			$t_duration = $this->stop_watch->get_group_duration('sql_queries');
			$t_output = 'Execution time: ~' . $t_duration . 's';
		}
		else
		{
			$t_output = $p_error_description;
		}

		switch($p_level)
		{
			case 'error':
				$this->error($t_output, $t_group, $t_log_file_name, $p_level, $p_type, $p_error_code, 'Query:' . "\r\n" . trim($p_query));
				break;
			case 'warning':
				$this->warning($t_output, $t_group, $t_log_file_name, $p_level, $p_type, $p_error_code, 'Query:' . "\r\n" . trim($p_query));
				break;
			case 'notice':
				$this->notice($t_output, $t_group, $t_log_file_name, $p_level, $p_type, $p_error_code, 'Query:' . "\r\n" . trim($p_query));
				break;
		}

		$this->write_stack(array('sql_queries'));
	}


	public function write_time_log($p_message = '', $p_output_type_array = array())
	{
		if($this->enabled == false)
		{
			// log deactivated. cancel write.
			return true;
		}

		$coo_configuration = $this->fetch_configuration('time_log');

		if(defined('STORE_PAGE_PARSE_TIME') && STORE_PAGE_PARSE_TIME == 'true'
			|| isset($GLOBALS['coo_debugger']) && $GLOBALS['coo_debugger']->is_enabled('StopWatch'))
		{
			$coo_configuration->set('notice', 'file', 'output');
		}
		if(defined('DISPLAY_PAGE_PARSE_TIME') && DISPLAY_PAGE_PARSE_TIME == 'true'
			|| isset($GLOBALS['coo_debugger']) && $GLOBALS['coo_debugger']->is_enabled('PageParseTime'))
		{
			$coo_configuration->set('notice', 'screen', 'output');
		}

		if(empty($p_output_type_array) == false)
		{
			$t_output_type_array = $p_output_type_array;
		}
		else
		{
			$t_output_type_array = $coo_configuration->get_output_type_array();
		}

		if(empty($t_output_type_array))
		{
			return true;
		}

		$t_duration_array = $this->stop_watch->get_duration_array();

		$t_date = date("Y-m-d H:i:s");
		$t_remote_address = $_SERVER['REMOTE_ADDR'];
		$t_message = '';
		if(empty($p_message) == false)
		{
			$t_message = $p_message . ' ';
		}
		$t_execution_time_title = 'Execution time (seconds):';
		$t_time_stamps = '';


		$t_total_time = '';
		if(isset($t_duration_array['main']))
		{
			$t_total_time = ' ~' . $t_duration_array['main'];
		}
		$t_calculated_total = 0;

		foreach($t_duration_array as $t_time_stamp_name => $t_duration)
		{
			if($t_time_stamp_name == 'main')
			{
				continue;
			}

			$t_time_stamps .= "\t" . $t_time_stamp_name . ': ~' . $t_duration . "\r\n";

			if(empty($t_total_time))
			{
				$t_calculated_total += $t_duration;
			}
		}
		if(empty($t_total_time))
		{
			$t_total_time = ' ~' . $t_calculated_total;
		}

//		$this->notice($t_message, 'time_log', 'debug-general', 'notice', 'StopWatch');
//		$this->write_stack(array('time_log'));

		$t_output = $t_date . ' (' . md5($t_remote_address) . ') <StopWatch> ' . $t_message . 'in ' . $_SERVER['REQUEST_URI'] . "\r\n" . $t_execution_time_title . $t_total_time . "\r\n" . $t_time_stamps;

		$t_screen_output = '';

		foreach($t_output_type_array as $t_output_type)
		{
			if($coo_configuration->is_active_output_type('notice', $t_output_type) == false || empty($p_output_type_array) == false && in_array($t_output_type, $p_output_type_array) == false)
			{
				continue;
			}

			switch($t_output_type)
			{
				case 'screen':
					$t_screen_output = $t_execution_time_title . $t_total_time;
					break;
				case 'html_file':
					$t_file_name = self::DEFAULT_TEXT_LOG_FILE_NAME;
					$t_file_extension = '.html';
					$this->write_to_file('<pre>' . htmlentities_wrapper($t_output) . '</pre>', $t_file_name, $t_file_extension);
				case 'file':
					$this->write_text_log($t_output);
					break;
				case 'mail':
					$this->mail($t_output);
					break;
				case 'database':
					// not supported yet
					break;
			}
		}

		return $t_screen_output;
	}


	public function write_text_log($p_message, $p_file_name = '')
	{
		if(empty($p_file_name))
		{
			$t_file_name = self::DEFAULT_TEXT_LOG_FILE_NAME;
		}
		else
		{
			$t_file_name = $p_file_name;
		}

		$this->write_to_file($p_message, $t_file_name);
	}


	protected function write_to_file($p_message, $p_file_name, $p_file_extension = '')
	{
		if((file_exists($this->get_file_path($p_file_name, $p_file_extension)) == false &&
			is_writeable(SHOP_ROOT . self::LOGFILES_DIR) == false) ||
		   (file_exists($this->get_file_path($p_file_name, $p_file_extension)) == true &&
			is_writeable($this->get_file_path($p_file_name, $p_file_extension)) == false))
		{
			return false;
		}

		$t_file_extension = self::FILE_EXTENSION;
		if(empty($p_file_extension) == false)
		{
			$t_file_extension = $p_file_extension;
		}

		if(file_exists($this->get_file_path($p_file_name, $p_file_extension))
		   && @filesize($this->get_file_path($p_file_name, $p_file_extension)) >= $this->max_filesize
		   && is_writeable(SHOP_ROOT . self::LOGFILES_DIR) == true
		)
		{
			$t_file_content    = file_get_contents($this->get_file_path($p_file_name, $p_file_extension));
			$t_gz_file_content = gzencode($t_file_content, 9);
			$t_gz_file_path    = $this->get_file_path($p_file_name, $p_file_extension) . '_' . date('Ymd_His') . $t_file_extension . self::GZIP_FILE_EXTENSION;
			$fp                = fopen($t_gz_file_path, 'w+');
			fwrite($fp, $t_gz_file_content);
			fclose($fp);
			unlink($this->get_file_path($p_file_name, $p_file_extension));
		}

		$fp = fopen($this->get_file_path($p_file_name, $p_file_extension), 'a+');
		fwrite($fp, $p_message);
		fclose($fp);

		return true;
	}


	protected function get_file_path($p_log_file_name, $p_file_extension = '')
	{
		$t_file_extension = self::FILE_EXTENSION;
		if(empty($p_file_extension) == false)
		{
			$t_file_extension = $p_file_extension;
		}

		$t_path = SHOP_ROOT . self::LOGFILES_DIR . basename($p_log_file_name) . '-' . $this->file_suffix . $t_file_extension;

		return $t_path;
	}


	protected function mail($p_message)
	{
		if(empty($this->admin_mail) == false)
		{
			$t_to = $this->admin_mail;
			$t_from = $this->admin_mail;
			$t_subject = 'Debug Report ' . HTTP_SERVER . DIR_WS_CATALOG . ' ' . date('Y-m-d H:i:s');
			$t_body    = $p_message;

			$this->send_mail($t_to, $t_from, $t_subject, $t_body);
		}
	}


	protected function send_mail($p_email_to, $p_email_from, $p_subject, $p_body)
	{
		$t_params = sprintf("-oi -f %s", $p_email_from);

		$t_success = mail($p_email_to, $p_subject, $p_body, 'From: ' . $p_email_from . "\r\n" . 'Content-Type: text/plain', $t_params);

		return $t_success;
	}

	public function is_shop_environment()
	{
		return defined('STORE_NAME') && function_exists('xtc_db_query');
	}

	public function get_event_count()
	{
		return $this->error_count + $this->warning_count + $this->notice_count;
	}

	public function get_group_configuration($p_group)
	{
		return $this->fetch_configuration($p_group, false);
	}

	/**
	 * @return LogConfiguration
	 */
	public function get_configuration()
	{
		return $this->configuration;
	}

	/**
	 * @param LogConfiguration $p_configuration
	 */
	public function set_configuration($p_configuration)
	{
		$this->configuration = $p_configuration;
	}

	/**
	 * @return array
	 */
	public function get_group_configuration_array()
	{
		return $this->group_configuration_array;
	}

	/**
	 * @param array $p_group_configuration_array
	 */
	public function set_group_configuration_array($p_group_configuration_array)
	{
		$this->group_configuration_array = $p_group_configuration_array;
	}

	/**
	 * @return string
	 */
	public function get_admin_mail()
	{
		return $this->admin_mail;
	}


	/**
	 * @param string $admin_mail
	 */
	public function set_admin_mail($p_admin_mail)
	{
		if(is_string($p_admin_mail))
		{
			$this->admin_mail = $p_admin_mail;
		}
	}


	/**
	 * @return bool
	 */
	public function get_enabled()
	{
		return $this->enabled;
	}


	/**
	 * @param bool $enabled
	 */
	public function set_enabled($p_enabled)
	{
		if(is_bool($p_enabled))
		{
			$this->enabled = (bool)$p_enabled;
		}
	}


	/**
	 * @return array
	 */
	public function get_event_stack()
	{
		return $this->event_stack;
	}


	/**
	 * @param array $event_stack
	 */
	public function set_event_stack(array $p_event_stack)
	{
		$this->event_stack = $p_event_stack;
	}


	/**
	 * @return bool
	 */
	public function get_event_stack_enabled()
	{
		return $this->event_stack_enabled;
	}


	/**
	 * @param bool $event_stack_enabled
	 */
	public function set_event_stack_enabled($p_event_stack_enabled)
	{
		if(is_bool($p_event_stack_enabled))
		{
			$this->event_stack_enabled = $p_event_stack_enabled;
		}
	}


	/**
	 * @return array
	 */
	public function get_fatal_error_data_array()
	{
		return $this->fatal_error_data_array;
	}


	/**
	 * @param array $fatal_error_data_array
	 */
	public function set_fatal_error_data_array(array $p_fatal_error_data_array)
	{
		$this->fatal_error_data_array = $p_fatal_error_data_array;
	}


	/**
	 * @return string
	 */
	public function get_file_suffix()
	{
		return $this->file_suffix;
	}


	/**
	 * @param string $file_suffix
	 */
	public function set_file_suffix($p_file_suffix)
	{
		if(is_string($p_file_suffix))
		{
			$this->file_suffix = basename($p_file_suffix);
		}
	}


	/**
	 * @return int
	 */
	public function get_max_file_size()
	{
		return $this->max_file_size;
	}


	/**
	 * @param int $max_file_size in bytes
	 */
	public function set_max_file_size($p_max_file_size)
	{
		if(is_int($p_max_file_size))
		{
			$this->max_file_size = $p_max_file_size;
		}
	}

	public function get_error_count()
	{
		return $this->error_count;
	}

	public function get_warning_count()
	{
		return $this->warning_count;
	}

	public function get_notice_count()
	{
		return $this->notice_count;
	}

	public function get_stop_watch()
	{
		return $this->stop_watch;
	}

	public function set_error_count($p_error_count)
	{
		$this->error_count = $p_error_count;
	}

	public function set_warning_count($p_warning_count)
	{
		$this->warning_count = $p_warning_count;
	}

	public function set_notice_count($p_notice_count)
	{
		$this->notice_count = $p_notice_count;
	}

	public function set_stop_watch($p_stop_watch)
	{
		$this->stop_watch = $p_stop_watch;
	}
}