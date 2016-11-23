<?php
/**
 * Instance of this class handles the callback of Payment Network to notify about a status change
 *
 * In rare cases notifications might be doubled or even wrong alltogether (if
 * send by a malicious user). So don't use this to change your status but instead
 * use the transaction id to query the webservice for detailed data (SofortLib_TransactionData)
 *
 * eg: $notificationObj = new SofortLib_Notification();
 *
 * $transactionId = $notificationObj->getNotification();
 *
 * Copyright (c) 2012 SOFORT AG
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Date: 2012-11-22 11:51:26 +0100 (Thu, 22 Nov 2012) $
 * @version SofortLib 1.5.0  $Id: sofortLib_notification.inc.php 5735 2012-11-22 10:51:26Z rotsch $
 * @author SOFORT AG http://www.sofort.com (integration@sofort.com)
 *
 */
class SofortLib_Notification extends SofortLib_Abstract {
	
	protected $_parameters = array();
	
	protected $_response = array();
	
	private $_transactionId = '';
	
	private $_time;
	
	
	/**
	 * creates a new notification object for receiving notifications
	 */
	public function __construct() {
		parent::__construct('', '', '');
	}
	
	
	/**
	 * reads the input and tries to read the transaction id
	 *
	 * @return array transactionid=>status
	 */
	public function getNotification($source = 'php://input') {
		$data = file_get_contents($source);
		
		//we don't really need a huge parser, simply extract the transaction-id
		if (!preg_match('#<transaction>([0-9a-z-]+)</transaction>#i', $data, $matches)) {
			$this->log(__CLASS__.' <- '.$data);
			$this->errors['error']['message'] = 'could not parse message';
			return false;
		}
		
		$this->_transactionId = $matches[1];
		$this->log(__CLASS__.' <- '.$data);
		preg_match('#<time>(.+)</time>#i', $data, $matches);
		
		if (isset($matches[1])) {
			$this->_time = $matches[1];
		}
		
		return $this->_transactionId;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see SofortLib_Abstract::sendRequest()
	 */
	public function sendRequest() {
		trigger_error('sendRequest() not possible in this case', E_USER_NOTICE);
	}
	
	
	/**
	 * 
	 * Getter for time
	 */
	public function getTime() {
		return $this->_time;
	}
	
	
	/**
	 * 
	 * Getter for transaction
	 */
	public function getTransactionId() {
		return $this->_transactionId;
	}
}
?>