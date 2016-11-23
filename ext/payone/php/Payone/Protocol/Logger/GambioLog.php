<?php
/* --------------------------------------------------------------
	GambioLog.php 2013-00-00 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class Payone_Protocol_Logger_GambioLog implements Payone_Protocol_Logger_Interface {
	protected $_logger;
	protected $_event_id;
	protected $_logcount;
	protected $_mode;

	public function __construct($config = null) {
		list($msec, $sec) = explode(' ', microtime());
		$this->_event_id = (int)(($sec + $msec) * 1000);
		$this->_logcount = 0;
		$this->_mode = isset($config) && is_array($config) && array_key_exists('mode', $config) && $config['mode'] == 'transactions' ? 'transactions' : 'api';
	}

	public function getKey() {
		return 'gambiolog';
	}

	public function log($message, $level = 0) {
		$this->_logcount++;
		$table = $this->_mode == 'api' ? 'payone_api_log' : 'payone_transactions_log';
		$query = "INSERT INTO `".$table."` SET `event_id` = :event_id, `date_created` = NOW(), `log_count` = :log_count,
		`log_level` = :log_level, `message` = ':message', `customers_id` = :customers_id";
		$query = strtr($query, array(
			':event_id' => (int)$this->_event_id,
			':log_count' => (int)$this->_logcount,
			':log_level' => (int)$level,
			':message' => xtc_db_input($message),
			':customers_id' => isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0,
		));
		xtc_db_query($query);
	}
}
