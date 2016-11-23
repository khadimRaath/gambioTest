<?php
/* --------------------------------------------------------------
	PayPalLogger.inc.php 2015-02-23
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * central logging class for PayPal3.
 * Uses LogControl where available, falls back to FileLog otherwise
 */
class PayPalLogger
{
	/**
	 * LogControl group
	 */
	const LOG_GROUP = 'payment';

	/**
	 * Log file name
	 */
	const LOG_FILE = 'payment.paypal3';

	/**
	 * Debug log file name
	 */
	const LOG_FILE_DEBUG = 'payment.paypal3-debug';

	/**
	 * LogControl instance
	 */
	protected $logControl;

	/**
	 * FileLog instance
	 */
	protected $fileLog;

	/**
	 * PayPalConfigurationStorage instance
	 */
	protected $configuration;

	/**
	 * constructor; initializes logging mechanism (LogControl/FileLog) and configuration
	 */
	public function __construct()
	{
		if(class_exists('LogControl'))
		{
			$this->logControl = LogControl::get_instance();
			$this->fileLog = false;
		}
		else
		{
			$this->logControl = false;
			$this->fileLog = MainFactory::create('FileLog', self::LOG_FILE);
		}
		$this->configuration = MainFactory::create('PayPalConfigurationStorage');
	}

	/**
	 * logs an error message
	 * @param string $message message to be logged
	 */
	public function error($message)
	{
		if($this->logControl !== false)
		{
			$this->logControl->error($message, self::LOG_GROUP, self::LOG_FILE);
		}
		else
		{
			$this->fileLog->write(sprintf("%s | %s\n", date('c'), $message));
		}
	}

	/**
	 * logs a warning message
	 * @param string $message message to be logged
	 */
	public function warning($message)
	{
		if($this->logControl !== false)
		{
			$this->logControl->warning($message, self::LOG_GROUP, self::LOG_FILE);
		}
		else
		{
			$this->fileLog->write(sprintf("%s | %s\n", date('c'), $message));
		}
	}

	/**
	 * logs a notice message
	 * @param string $message message to be logged
	 */
	public function notice($message)
	{
		if($this->logControl !== false)
		{
			$this->logControl->notice($message, self::LOG_GROUP, self::LOG_FILE);
		}
		else
		{
			$this->fileLog->write(sprintf("%s | %s\n", date('c'), $message));
		}
	}

	/**
	 * logs a debug message.
	 * Debug messages are used for extended logging; this will log all API traffic
	 * @param string $message message to be logged
	 */
	public function debug_notice($message)
	{
		if($this->configuration->get('debug_logging') == true)
		{
			if($this->logControl !== false)
			{
				$this->logControl->notice($message, self::LOG_GROUP, self::LOG_FILE_DEBUG);
			}
			else
			{
				$debugFileLog = MainFactory::create('FileLog', self::LOG_FILE_DEBUG);
				$debugFileLog->write(sprintf("%s | %s\n", date('c'), $message));
			}
		}
	}
}
