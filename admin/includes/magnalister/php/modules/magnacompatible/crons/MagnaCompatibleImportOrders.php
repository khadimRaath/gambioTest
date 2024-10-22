<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$ osC
 *
 * (c) 2010 - 2011 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleCronBase.php');

abstract class MagnaCompatibleImportOrders extends MagnaCompatibleCronBase {
	protected $hasNext = true;
	protected $offset = array();
	protected $beginImportDate = false;
	
	protected $db = null;
	protected $dbCharSet = '';
	
	protected $simplePrice = null;
	
	/* specific to one order only */
	protected $cur = array();
	protected $o = array(); /* the current order */
	protected $p = array(); /* the current product */
	protected $taxValues = array(); /* tax values for the current order */
	protected $mailOrderSummary = array();
	protected $comment = '';
	
	/* specific to all orders */
	protected $syncBatch = array(); /* sync batch for other marketplaces */
	protected $allCurrencies = array(); /* list of different currencies */
	
	/* For acknowledging */
	protected $processedOrders = array ();
	protected $lastOrderDate = false;

	/* multivariations, set to true for modules which support it */
	protected $multivariationsEnabled = false;
	
	protected $verbose = false;
	
	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);

		$this->initImport();
	}
	
	protected function initImport() {
		#$_GET['MLDEBUG'] = 'true'; #hack
		
		if (isset($_GET['MLDEBUG']) && ($_GET['MLDEBUG'] == 'true')) {
			require_once(DIR_MAGNALISTER_INCLUDES . 'lib/MagnaTestDB.php');
			$this->db = MagnaTestDB::gi();
		} else {
			$this->db = MagnaDB::gi();
		}

		$this->dbCharSet = MagnaDB::gi()->mysqlVariableValue('character_set_client');
		if (('utf8mb3' == $this->dbCharSet) || ('utf8mb4' == $this->dbCharSet)) {
			# means the same for us
			$this->dbCharSet = 'utf8';
		}
		$this->verbose = (
				(MAGNA_CALLBACK_MODE == 'STANDALONE') 
				|| (defined('MAGNALISTER_PLUGIN') && (MAGNALISTER_PLUGIN == true))
			) && (get_class($this->db) == 'MagnaTestDB');
		
		require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
		$this->simplePrice = new SimplePrice();
		
		if (   ( file_exists(DIR_WS_FUNCTIONS.'password_funcs.php'))
			&& (!function_exists('tep_encrypt_password'))
		) {
			require_once(DIR_WS_FUNCTIONS.'password_funcs.php');
		}
	}
	
	protected function getConfigKeys() {
		return array (
			'UpdateExchangeRate' => array (
				'key' => array('exchangerate', 'update'),
				'default' => false,
			),
			'LastImport' => array (
				'key' => 'orderimport.lastrun',
				'default' => 0,
			),
			'FirstImportDate' => array (
				'key' => 'preimport.start',
				'default' => '1970-01-01',
			),
			'CustomerGroup' => array (
				'key' => 'CustomerGroup',
				'default' => 1
			),
			'MwStFallback' => array (
				'key' => 'mwst.fallback',
				'default' => 0
			),
			'MwStShipping' => array (
				'key' => 'mwst.shipping',
				'default' => 0
			),
			'StockSync.FromMarketplace' => array (
				'key' => 'stocksync.frommarketplace',
				'default' => 'no'
			),
			'MailSend' => array (
				'key' => 'mail.send',
				'default' => 'false',
			),
			'ShippingMethod' => array (
				'key' => 'orderimport.shippingmethod',
				'default' => 'textfield',
			),
			'ShippingMethodName' => array (
				'key' => 'orderimport.shippingmethod.name',
				'default' => 'marketplace',
			),
			'PaymentMethod' => array (
				'key' => 'orderimport.paymentmethod',
				'default' => 'textfield',
			),
			'PaymentMethodName' => array (
				'key' => 'orderimport.paymentmethod.name',
				'default' => 'marketplace',
			),
		);
	}
	
	protected function initConfig() {
		$this->config['CIDAssignment'] = getDBConfigValue('customers_cid.assignment', '0', 'none');

		parent::initConfig();

		if ($this->config['ShippingMethod'] == 'textfield') {
			$this->config['ShippingMethod'] = trim($this->config['ShippingMethodName']);
		}
		if (empty($this->config['ShippingMethod'])) {
			$k = $this->getConfigKeys();
			$this->config['ShippingMethod'] = $k['ShippingMethodName']['default'];
		}
		if ($this->config['PaymentMethod'] == 'textfield') {
			$this->config['PaymentMethod'] = trim($this->config['PaymentMethodName']);
		}
		if (empty($this->config['PaymentMethod'])) {
			$k = $this->getConfigKeys();
			$this->config['PaymentMethod'] = $k['PaymentMethodName']['default'];
		}
		$this->config['DBColumnExists'] = array (
			'orders.gm_send_order_status' => MagnaDB::gi()->columnExistsInTable('gm_send_order_status', TABLE_ORDERS),
			'orders.customers_status_discount' => MagnaDB::gi()->columnExistsInTable('customers_status_discount', TABLE_ORDERS),
		);

		//Bugfix for floats as array keys
		$this->config['MwStShipping'] = (string)round($this->config['MwStShipping'], 2);

		#echo var_dump_pre($this->config['PaymentMethod'], 'PaymentMethod');
		#echo var_dump_pre($this->config['ShippingMethod'], 'ShippingMethod');
		#echo print_m($this->config);
	}

	/**
	 * How many hours, days, weeks or whatever we go back in time to request older orders?
	 * @return time in seconds
	 */ 
	protected function getPastTimeOffset() {
		return 60 * 60 * 24 * 7;
	}

	protected function getBeginDate() {
		global $_modules;
		if ($this->beginImportDate !== false) {
			return $this->beginImportDate;
		}
		$begin = strtotime($this->config['FirstImportDate']);
		if ($begin <= '1970-01-01 00:00:00') {
			# not configured. Check if this is a required key for the platform.
			# If so, return false, which stops the import.
			if (in_array($this->marketplace.'.preimport.start', $_modules[$this->marketplace]['requiredConfigKeys'])) {
				return false;
			}
		}
		if ($begin > time()) {
			if ($this->verbose) echo "Date in the future --> no import\n";
			return false;
		}
		
		$dateRegexp = '/^([1-2][0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])'.
			'(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))?$/';
		
		$lastImport = $this->config['LastImport'];
		if (preg_match($dateRegexp, $lastImport)) {
			# Since we only request non acknowledged orders, we go back in time by 7 days.
			$lastImport = strtotime($lastImport.' +0000') - $this->getPastTimeOffset();
		} else {
			$lastImport = 0;
		}
	
		if ( ($lastImport > 0) && ($begin < $lastImport) ) {
			$begin = $lastImport;
		}
		
		if (isset($_GET['ForceBeginImportDate']) && preg_match($dateRegexp, $_GET['ForceBeginImportDate'])) {
			$begin = strtotime($_GET['ForceBeginImportDate']);
		}
		
		return $this->beginImportDate = gmdate('Y-m-d H:i:s', $begin);
	}

	protected function buildRequest() {
		if (empty($this->offset)) {
			$this->offset = array (
				'COUNT' => 200,
				'START' => 0,
			);
		}
		return array (
			'ACTION' => 'GetOrdersForDateRange',
			'SUBSYSTEM' => $this->marketplace,
			'MARKETPLACEID' => $this->mpID,
			'BEGIN' => $this->getBeginDate(),
			'OFFSET' => $this->offset,
		);
	}

	protected function getOrders() {
		if ($this->hasNext != true) {
			return false;
		}
		$request = $this->buildRequest();
		if ($this->verbose) {
			echo print_m($request, '$request');
		}
		if ($request['BEGIN'] === false) {
			echo "No BEGIN Date has been set, so no import yet.\n";
			return false;
		}
		try {
			$res = MagnaConnector::gi()->submitRequest($request);
		} catch (MagnaException $e) {
			if ((MAGNA_CALLBACK_MODE == 'STANDALONE') || $this->verbose) {
				echo print_m($e->getErrorArray(), 'Error: '.$e->getMessage());
			}
			if (MAGNA_DEBUG && ($e->getMessage() == ML_INTERNAL_API_TIMEOUT)) {
				$e->setCriticalStatus(false);
			}
			return false;
		}
		if (!array_key_exists('DATA', $res) || empty($res['DATA'])) {
			if ($this->verbose) echo "No Data.\n";
			return false;
		}
		$this->hasNext = $res['HASNEXT'];
		$this->offset['START'] += $this->offset['COUNT'];
		
		$orders = $res['DATA'];
		$res['DATA'] = 'Cleaned';
		
		if ($this->verbose) echo print_m($res, '$res');

		if (!is_array($orders)) return false;

		# ggf. Zeichensatz korrigieren
		if ($this->dbCharSet != 'utf8') {
			arrayEntitiesToLatin1($orders);
		}

		return $orders;
	}
	
	protected function updateOrderCurrency($currency) {
		// Does the currency exist in the shop?
		if (!$this->simplePrice->currencyExists($currency)) {
			if ($this->verbose) echo "Currency does not exist.\n";
			return false;
		}
		#if ($this->verbose) echo 'Set Currency to: ['.$currency."]\n";
		$this->simplePrice->setCurrency($currency);

		if (array_key_exists($currency, $this->allCurrencies)) {
			return true;
		}

		if ($this->config['UpdateExchangeRate']) {
			$this->simplePrice->updateCurrencyByService();
		}

		$currencyValue = $this->simplePrice->getCurrencyValue();
		if ((float)$currencyValue <= 0.0) {
			if ($this->verbose) echo "CurrencyValue <= 0.\n";
			return false;
		}
		$this->allCurrencies[$currency] = $currencyValue;
		return true;
	}
	
	protected function getCountryByISOCode($code, $fallbackName = '') {
		$c = MagnaDB::gi()->fetchRow('
			SELECT countries_id as ID, countries_name AS Name
			  FROM '.TABLE_COUNTRIES.'
			 WHERE countries_iso_code_2="'.$code.'" 
			 LIMIT 1
		');
		if (!is_array($c)) {
			$c = array (
				'ID' => 0,
				'Name' => empty($fallbackName) ? $code : $fallbackName,
			);
		}
		return $c;
	}

	protected function getAddressFormatID($country) {
		$ret = (int)MagnaDB::gi()->fetchOne(eecho('
			SELECT address_format_id 
			  FROM '.TABLE_COUNTRIES.'
			 WHERE countries_id="'.$country['ID'].'"
		', $this->verbose));
		if ($ret < 1) {
			return 1;
		}
		return $ret;
	}

	protected function getCustomer($email) {
		$fields = array('customers_id as ID');
		if (($this->config['CIDAssignment'] != 'none') &&
			MagnaDB::gi()->columnExistsInTable('customers_cid', TABLE_CUSTOMERS)	
		) {
			$fields[] = 'customers_cid as CID';
		}
		$c = MagnaDB::gi()->fetchRow('
		    SELECT '.implode(',', $fields).'
		      FROM '.TABLE_CUSTOMERS.' 
		     WHERE customers_email_address="'.$email.'" 
		     LIMIT 1
		');
		if (!is_array($c)) {
			return false;
		}
		return $c;
	}

	protected function insertCustomer() {
		$customer = array();
		$customer['Password'] = randomString(10);
		$this->o['customer']['customers_password'] = md5($customer['Password']);
		
		if (SHOPSYSTEM != 'oscommerce') {
			$this->o['customer']['customers_status'] = $this->config['CustomerGroup'];
			$this->o['customer']['account_type'] = '0';
		} else if (function_exists('tep_encrypt_password')) {
			$this->o['customer']['customers_password'] = tep_encrypt_password($customer['Password']);
		}
		$this->db->insert(TABLE_CUSTOMERS, $this->o['customer']);
		$cupdate = array();
		
		# Kunden-ID herausfinden
		$customer['ID'] = $this->db->getLastInsertID();
		# customers_cid bestimmen
		if (MagnaDB::gi()->columnExistsInTable('customers_cid', TABLE_CUSTOMERS)) {
			switch ($this->config['CIDAssignment']) {
				case 'sequential': {
					$customer['CID'] = MagnaDB::gi()->fetchOne('
					    SELECT MAX(CAST(IFNULL(customers_cid,0) AS SIGNED))+1
					      FROM '.TABLE_CUSTOMERS
					);
					break;
				}
				case 'customers_id': {
					$customer['CID'] = $customer['ID'];
					break;
				}
			}
			if (isset($customer['CID'])) {
				$cupdate['customers_cid'] = $customer['CID'];
			}
		}
		
		# Infodatensatz erzeugen
		$this->db->insert(TABLE_CUSTOMERS_INFO, array(
			'customers_info_id' => $customer['ID'],
			'customers_info_number_of_logons' => 0,
			'customers_info_date_account_created' => date('Y-m-d H:i:s', strtotime($this->o['order']['date_purchased']) - 1),
			'customers_info_date_account_last_modified' => date('Y-m-d H:i:s'),
		));
		// echo 'DELETE FROM '.TABLE_CUSTOMERS_INFO.' WHERE customers_info_id="'.$customersId.'";'."\n\n";

		# Adressbuchdatensatz ergaenzen.
		$this->o['adress']['customers_id'] = $customer['ID'];
		$this->o['adress']['entry_country_id'] = $this->cur['BuyerCountry']['ID'];

		$this->db->insert(TABLE_ADDRESS_BOOK, $this->o['adress']);

		# Adressbuchdatensatz-Id herausfinden.
		$abId = $this->db->getLastInsertID();
		// echo 'DELETE FROM '.TABLE_ADDRESS_BOOK.' WHERE customers_id="'.$customersId.'";'."\n\n";

		# Kundendatensatz updaten.
		$cupdate['customers_default_address_id'] = $abId;
		$this->db->update(TABLE_CUSTOMERS, $cupdate, array (
			'customers_id' => $customer['ID']
		));

		return $customer;
	}

	protected function updateCustomer() {
		$customer = $this->o['customer'];
		unset($customer['customers_date_added']);
		unset($customer['account_type']);
		$this->db->update(TABLE_CUSTOMERS, $customer, array (
			'customers_id' => $this->cur['customer']['ID'],
		));
		
		# Adressbuchdatensatz aktualisieren.
		if (isset($this->o['adress']['address_date_added'])) {
			unset($this->o['adress']['address_date_added']);
		}
		$this->o['adress']['entry_country_id'] = $this->cur['BuyerCountry']['ID'];
		$this->db->update(TABLE_ADDRESS_BOOK, $this->o['adress'], array (
			'customers_id' => $this->cur['customer']['ID'],
		));
	}

	protected function processCustomer() {
		$customer = $this->getCustomer($this->o['customer']['customers_email_address']);
		if (!is_array($customer)) {
			$this->cur['customer'] = $this->insertCustomer($this->o);
			return;
		}
		$this->cur['customer'] = $customer;
		$this->updateCustomer();
		switch ($this->o['orderInfo']['BuyerCountryISO']) {
			case 'AT':
			case 'DE': {
				$this->cur['customer']['Password'] = '(wie bekannt)';
				break;
			}
			default: {
				$this->cur['customer']['Password'] = '(as known)';
				break;
			}
		}
	}
	
	/**
	 * Load some basic info, e.g. country etc from DB
	 */
	protected function prepareOrderInfo() {
		$this->cur['BuyerCountry'] = $this->getCountryByISOCode(
			$this->o['orderInfo']['BuyerCountryISO'],
			isset($this->o['order']['customers_country'])
				? $this->o['order']['customers_country']
				: false
		);
		$this->cur['ShippingCountry'] = $this->getCountryByISOCode(
			$this->o['orderInfo']['ShippingCountryISO'],
			isset($this->o['order']['delivery_country'])
				? $this->o['order']['delivery_country']
				: false
		);
	}
	
	/**
	 * Returns the marketplace specific order ID from $this->o.
	 *
	 * @return string
	 *    OrderID of the marketplace used in magnalister_orders.special (Database)
	 */
	protected function getMarketplaceOrderID() {
		return $this->o['orderInfo']['MOrderID'];
	}

	protected function addCurrentOrderToProcessed() {
		$this->processedOrders[] = array (
			'MOrderID' => $this->getMarketplaceOrderID(),
			'ShopOrderID' => $this->cur['OrderID'],
		);
	}

	protected function orderExists() {
		$mOID = $this->getMarketplaceOrderID();
		$oID = MagnaDB::gi()->fetchOne(eecho('
			SELECT orders_id
			  FROM '.TABLE_MAGNA_ORDERS.'
			 WHERE special="'.MagnaDB::gi()->escape($mOID).'"
			 LIMIT 1
		', false));
		if ($oID === false) {
			return false;
		}
		if ($this->verbose) echo 'orderExists(MOrderID: '.$mOID.', OrderID: '.$oID.')'."\n";
		$this->cur['OrderID'] = $oID;
		
		/* Ack again */
		$this->addCurrentOrderToProcessed();
		return true;
	}
	
	/**
	 * Returns the status that the order should have as string.
	 * Use $this->o['order'].
	 *
	 * @return String	The order status for the currently processed order.
	 */
	protected abstract function getOrdersStatus();
	
	/**
	 * Returns the comment for orders.comment (Database). 
	 * E.g. the comment from the customer or magnalister related information.
	 * Use $this->o['order'].
	 *
	 * @return String
	 *    The comment for the order.
	 */
	protected function generateOrderComment() {
		return trim(
			sprintf(ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT, $this->marketplaceTitle)."\n".
			ML_LABEL_MARKETPLACE_ORDER_ID.': '.$this->getMarketplaceOrderID()."\n\n".
			$this->comment
		);
	}
	
	/**
	 * Returns the comment for orders_status.comment (Database). 
	 * E.g. the comment from the customer or magnalister related information.
	 * May differ from self::generateOrderComment()
	 * Use $this->o['order'].
	 *
	 * @return String
	 *    The comment for the order.
	 */
	protected function generateOrdersStatusComment() {
		return $this->generateOrderComment();
	}
	
	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * orders before it is inserted.
	 * Use $this->o['order'].
	 */
	protected function doBeforeInsertOrder() {
		/* Do nothing here. */
	}

	protected function insertBankData() {
		# Bankdaten hinterlegen
		if (empty($this->o['bank'])) {
			return;
		}
		if (MagnaDB::gi()->tableExists('banktransfer')) {
			$this->o['bank']['orders_id'] = $this->cur['OrderID'];
			$this->db->insert('banktransfer', $this->o['bank']);
			//echo 'DELETE FROM '.'banktransfer'.' WHERE orders_id="'.$ordersId.'";'."\n\n";
			
		} else {
			$this->o['magnaOrders']['ML_LABEL_ACCOUNTING_OWNER']  = $this->o['bank']['banktransfer_owner'];
			$this->o['magnaOrders']['ML_LABEL_ACCOUNTING_NUMBER'] = $this->o['bank']['banktransfer_number'];
			$this->o['magnaOrders']['ML_LABEL_ACCOUNTING_BLZ']    = $this->o['bank']['banktransfer_blz'];
			$this->o['magnaOrders']['ML_LABEL_ACCOUNTING_NAME']   = $this->o['bank']['banktransfer_bankname'];
		}
		
	}

	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * magnalister_orders before it is inserted.
	 *
	 * @return array
	 *     Associative array that will be stored serialized
	 *     in magnalister_orders.internaldata (Database)
	 */
	protected function doBeforeInsertMagnaOrder() {
		/* Do nothing here. */
		return array();
	}

	/**
	 * In child classes this method can be used to extend the data for the DB-table
	 * orders_history before it is inserted.
	 * Use $this->o['orderStatus']
	 */
	protected function doBeforeInsertOrderHistory() {
		/* Do nothing here. */
	}
	
	/**
	 * Returs the payment method for the current order.
	 * @return string
	 */
	protected function getPaymentMethod() {
		return $this->config['PaymentMethod'];
	}
	
	/**
	 * Returs the shipping method for the current order.
	 * @return string
	 */
	protected function getShippingMethod() {
		return $this->config['ShippingMethod'];
	}
	
	protected function insertOrder() {
		$this->comment = $this->o['order']['comments'];
		$this->o['order']['customers_id'] = $this->cur['customer']['ID'];

		$this->o['order']['customers_address_format_id'] = 
				$this->o['order']['billing_address_format_id'] = 
				$this->getAddressFormatID($this->cur['BuyerCountry']);
		$this->o['order']['delivery_address_format_id'] = 
				$this->getAddressFormatID($this->cur['ShippingCountry']);

		$this->o['order']['orders_status'] = $this->getOrdersStatus();

		$this->o['order']['customers_country'] = $this->cur['BuyerCountry']['Name'];
		$this->o['order']['delivery_country'] = $this->cur['ShippingCountry']['Name'];
		$this->o['order']['billing_country'] = $this->cur['BuyerCountry']['Name'];

		if (SHOPSYSTEM != 'oscommerce') {
			if (isset($this->cur['customer']['CID'])) {
				$this->o['order']['customers_cid'] = $this->cur['customer']['CID'];
			}
			$this->o['order']['customers_status'] = $this->config['CustomerGroup'];
			$this->o['order']['language'] = $this->language;
			$this->o['order']['comments'] = $this->generateOrderComment();
		}
		
		if ($this->config['DBColumnExists']['orders.gm_send_order_status']) {
			$this->o['order']['gm_send_order_status'] = 1;
		}
		if ($this->config['DBColumnExists']['orders.customers_status_discount']) {
			$this->o['order']['customers_status_discount'] = '0.0';
		}
		
		/* Change Shipping and Payment Methods */
		$this->o['order']['payment_method'] = $this->getPaymentMethod();
		if (SHOPSYSTEM != 'oscommerce') {
			$this->o['order']['payment_class'] = $this->o['order']['payment_method'];
			$this->o['order']['shipping_class'] = $this->o['order']['shipping_method'] = $this->getShippingMethod();
		}
		// set currency_value
		$this->o['order']['currency_value'] = $this->allCurrencies[$this->o['order']['currency']];
		
		$this->doBeforeInsertOrder();
		//echo var_dump_pre($this->o['order'], 'InsertOrder');
		$this->db->insert(TABLE_ORDERS, $this->o['order']);

		# OrderId merken
		$this->cur['OrderID'] = $this->db->getLastInsertID();
		// echo 'DELETE FROM '.TABLE_ORDERS.' WHERE orders_id="'.$this->cur['OrderID'].'";'."\n\n";

		$this->insertBankData();

		/* Bestellung in unserer Tabelle registrieren */
		$internalData = $this->doBeforeInsertMagnaOrder();
		$this->db->insert(TABLE_MAGNA_ORDERS, array(
			'mpID' => $this->mpID,
			'orders_id' => $this->cur['OrderID'],
			'orders_status' => $this->o['order']['orders_status'],
			'data' => serialize($this->o['magnaOrders']),
			'internaldata' => is_array($internalData) ? serialize($internalData) : '',
			'special' => $this->getMarketplaceOrderID(),
			'platform' => $this->marketplace
		));
		// echo 'DELETE FROM '.TABLE_MAGNA_ORDERS.' WHERE orders_id="'.$this->cur['OrderID'].'";'."\n\n";

		# Statuseintrag fuer Historie vornehmen.
		$this->o['orderStatus']['orders_id'] = $this->cur['OrderID'];
		$this->o['orderStatus']['orders_status_id'] = $this->o['order']['orders_status'];
		
		$this->o['orderStatus']['comments'] = $this->generateOrdersStatusComment();

		$this->doBeforeInsertOrderHistory();
		$this->db->insert(TABLE_ORDERS_STATUS_HISTORY, $this->o['orderStatus']);
		// echo 'DELETE FROM '.TABLE_ORDERS_STATUS_HISTORY.' WHERE orders_id="'.$this->cur['OrderID'].'";'."\n\n";

		/* {Hook} "MagnaCompatibleImportOrders_PostInsertOrder": Is called after the order in <code>$this->o['order']</code> is imported.
				Usefull to manipulate some of the data in the database
				Variables that can be used:
				<ul><li>$this->o['order']: The order that is going to be imported. The order is an 
				        associative array representing the structures of the order and customer related shop tables.</li>
				    <li>$this->mpID: The ID of the marketplace.</li>
					<li>$this->marketplace: The name of the marketplace.</li>
				    <li>$this->cur['OrderID']: The Order ID of the shop (<code>orders_id</code>).</li>
				    <li>$this->o['order']['customers_id']: The Customers ID of the shop (<code>customers_id</code>).</li>
				    <li>$this->db: Instance of the magnalister database class. USE THIS for accessing the database during the
				        order import. DO NOT USE the shop functions for database access or MagnaDB::gi()!</li>
				</ul>
		*/
		if (function_exists('magnaContribVerify') && (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PostInsertOrder', 1)) !== false)) {
			require($hp);
		}
	}
	
	/**
	 * May be overwritten to allow additional identification of the product based on EAN or title.
	 * @todo: Replace products_ean in the query with the constant.
	 */
	protected function additionalProductsIdentification() {

	}
	
	/**
	 * Converts whatever the API has submitted in $this->p['products_tax'] to a
	 * real tax value.
	 * Here it just returns the parameter. Child Clases however may override this
	 * mehtod and convert the parameter.
	 *
	 * @parameter mixed $tax
	 *    Something that represents a tax value
	 * @return float
	 *    The actual tax value
	 */
	protected function getTaxValue($tax) {
		return $tax;
	}
	
	protected function doBeforeInsertOrdersProducts() {}
	
	/**
	 * Returns true if the stock of the imported and identified item has to be reduced.
	 * @return bool
	 */
	protected function hasReduceStock() {
		return $this->config['StockSync.FromMarketplace'] != 'no';
	}
	
	/**
	 *
	 */
	protected function insertProduct() {
		$this->p['orders_id'] = $this->cur['OrderID'];
		
		/* Attribute Values ermitteln aus der SKU, nicht aus dem Hauptprodukt.
		   Daher bevor products_id ermittelt wird. */
		/* sku needed later */
		$sku = $this->p['products_id']; 
		$attrValues = magnaSKU2pOpt($sku, $this->o['orderInfo']['BuyerCountryISO'], $this->multivariationsEnabled);
		$this->p['products_id'] = magnaSKU2pID($sku);
		
		$this->additionalProductsIdentification();

		$this->mailOrderSummary[] = array(
			'quantity' => $this->p['products_quantity'],
			'name' => $this->p['products_name'],
			'price' => $this->simplePrice->setPrice($this->p['products_price'])->format(),
			'finalprice' => $this->simplePrice->setPrice($this->p['final_price'])->format(),
		);
		if (array_key_exists($this->p['products_id'], $this->syncBatch)) {
			$this->syncBatch[$this->p['products_id']]['NewQuantity']['Value'] += (int)$this->p['products_quantity'];
		} else {
			$this->syncBatch[$this->p['products_id']] = array (
				'SKU' => ('artNr' == getDBConfigValue('general.keytype', '0'))
						? $sku
						: magnaPID2SKU($this->p['products_id']), /* add ML */
				'NewQuantity' => array (
					'Mode' => 'SUB',
					'Value' => (int)$this->p['products_quantity']
				),
			);
		}

		$tax = false;
		if (isset($this->p['products_tax'])) {
			$tax = $this->getTaxValue($this->p['products_tax']);
		}

		if (!MagnaDB::gi()->recordExists(TABLE_PRODUCTS, array('products_id' => (int)$this->p['products_id']))) {
			$this->p['products_id'] = 0;
		} else {
			/* Lagerbestand reduzieren */
			if ($this->hasReduceStock()) {
				$this->db->query('
					UPDATE '.TABLE_PRODUCTS.'
					   SET products_quantity = products_quantity - '.(int)$this->p['products_quantity'].'
					 WHERE products_id='.(int)$this->p['products_id'].'
				');
				/* Varianten-Bestand reduzieren, falls Produkt mit Varianten (gibt es bei osCommerce nicht) */
				if (MagnaDB::gi()->columnExistsInTable('attributes_stock',TABLE_PRODUCTS_ATTRIBUTES)) {
					if ($this->multivariationsEnabled) {
						$this->db->query('UPDATE '.TABLE_MAGNA_VARIATIONS.'
										  SET variation_quantity = variation_quantity - '.(int)$this->p['products_quantity'].'
										  WHERE products_id='.(int)$this->p['products_id'].'
											AND '.mlGetVariationSkuField().'="'.$sku.'"');
					} else {
						/* use 'foreach' in both cases, to omit code duplication */
						$attrValues = array($attrValues);
					}
					foreach ($attrValues as $attrV) {
						if (!empty($attrV['options_name'])) {
							$this->db->query('
								UPDATE '.TABLE_PRODUCTS_ATTRIBUTES.'
						   		SET attributes_stock = attributes_stock - '.(int)$this->p['products_quantity'].'
						 		WHERE products_id='.(int)$this->p['products_id'].' 
						       		AND options_id='.(int)$attrV['options_id'].' 
						       		AND options_values_id='.(int)$attrV['options_values_id'].'
							');
						}
					}
				}
			}
			/* Steuersatz und Model holen */
			$row = MagnaDB::gi()->fetchRow('
				SELECT products_tax_class_id, products_model 
				  FROM '.TABLE_PRODUCTS.' 
				 WHERE products_id="'.(int)$this->p['products_id'].'"
			');
			if ($row !== false) {
				$tax = SimplePrice::getTaxByClassID((int)$row['products_tax_class_id'], (int)$this->cur['ShippingCountry']['ID']);
				$this->p['products_model'] = $row['products_model'];
			}
		}
		if ($tax === false) {
			$tax = (float)$this->config['MwStFallback'];
		} else {
			$tax = (float)$tax;
		}
		//Bugfix for floats as array keys
		$tax = (string)round($tax, 2);

		$this->p['products_tax'] = $tax;

		$priceWOTax = $this->simplePrice->setPrice($this->p['products_price'])->removeTax($tax)->getPrice();

		if (!isset($this->taxValues[$tax])) {
			$this->taxValues[$tax] = 0.0;
		}
		$this->taxValues[$tax] += $priceWOTax * (int)$this->p['products_quantity'];

		if (SHOPSYSTEM != 'oscommerce') {
			$this->p['allow_tax'] = 1;
		} else {
			$this->p['products_price'] = $priceWOTax;
			$this->p['final_price'] = $this->p['products_price'];
		}

		$this->doBeforeInsertOrdersProducts();
		# Produktdatensatz in Tabelle "orders_products".	
		$this->db->insert(TABLE_ORDERS_PRODUCTS, $this->p);
		$ordersProductsId = $this->db->getLastInsertID();

		// orders_products_attributes:
		if (array_key_exists('options_name', $attrValues)) $attrValues = array($attrValues);
		foreach ($attrValues as $attrV) {
			$prodOrderAttrData = array(
				'orders_id' => $this->p['orders_id'],
				'orders_products_id' => $ordersProductsId,
				'products_options' => $attrV['options_name'],
				'products_options_values' => $attrV['options_values_name'],
				'options_values_price' => 0.0,
				'price_prefix' => ''
			);
		
			if (!empty($attrV['options_name'])) {
				if (MagnaDB::gi()->columnExistsInTable('products_options_id', TABLE_ORDERS_PRODUCTS_ATTRIBUTES)) {
					$prodOrderAttrData['products_options_id'] = $attrV['options_id'];
					$prodOrderAttrData['products_options_values_id'] = $attrV['options_values_id'];
				}
				$this->db->insert(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $prodOrderAttrData);
			}
		}
	}

	protected function proccessShippingTax() {
		if (($this->config['MwStShipping'] <= 0) 
			|| !array_key_exists('Shipping', $this->o['orderTotal'])
		) {
			return;
		}

		if (!isset($this->taxValues[$this->config['MwStShipping']])) {
			$this->taxValues[$this->config['MwStShipping']] = 0.0;
		}
		$this->taxValues[$this->config['MwStShipping']] += $this->simplePrice->setPrice(
			$this->o['orderTotal']['Shipping']['value']
		)->removeTax($this->config['MwStShipping'])->getPrice();
		
	}
	
	protected function addTaxValuesToOrdersTotal() {
		if (empty($this->taxValues)) return;
		if (!defined('MODULE_ORDER_TOTAL_TAX_STATUS') || (MODULE_ORDER_TOTAL_TAX_STATUS != 'true')) {
			return;
		}
		ksort($this->taxValues);
		
		/* fuer summe netto erst brutto-wert nehmen */
		$netto = $this->o['orderTotal']['Total']['value'];
		
		$otc = 60;
		foreach ($this->taxValues as $tax => $value) {
			$this->o['orderTotal']['Tax'.$tax] = array (
				'title' => ML_LABEL_INCL.' '.round($tax, 2).'% '.MAGNA_LABEL_ORDERS_TAX,
				'value' => $this->simplePrice->setPrice($value)->getTaxValue($tax),
				'class' => 'ot_tax',
				'sort_order' => $otc++,
			);
			/* steuerbetrag von netto abziehen */
			$netto -= $this->o['orderTotal']['Tax'.$tax]['value'];
		}
		/* Netto Betrag-Eintrag einfuegen. */
		if (SHOPSYSTEM == 'gambio') {
			$this->o['orderTotal']['Netto'] = array (
				'title' => (defined('MODULE_ORDER_TOTAL_TOTAL_NETTO_TITLE') ? MODULE_ORDER_TOTAL_TOTAL_NETTO_TITLE : 'Summe netto') . ':',
				'value' => $netto,
				'class' => 'ot_total_netto',
				'sort_order' => $otc++,
			);
		}
	}
	
	/**
	 * This method prepares and inserts data for orders_total.
	 * Child-Classes may extend this method. However this method should be called
	 * at the end to do the actual inertion of data in the database.
	 * parent::insertOrdersTotal(); as last statement.
	 */
	protected function insertOrdersTotal() {
		//echo print_m($this->o['orderTotal']);
		foreach ($this->o['orderTotal'] as $key => &$entry) {
			$entry['orders_id'] = $this->cur['OrderID'];
			if (defined($entry['title'])) {
				$entry['title'] = constant($entry['title']);
			}
			$entry['text'] = $this->simplePrice->setPrice($entry['value'])->format();
			$this->db->insert(TABLE_ORDERS_TOTAL, $entry);
		}

		// Gambio specific "Kleinunternehmer Regelung"
		if (defined('MAGNA_GAMBIO_PLUGIN_GM_TAX_FREE_STATUS') && MAGNA_GAMBIO_PLUGIN_GM_TAX_FREE_STATUS) {
			$this->db->insert(TABLE_ORDERS_TOTAL, array(
				'orders_id' => $this->cur['OrderID'],
				'title' => MODULE_ORDER_TOTAL_GM_TAX_FREE_TEXT,
				'class' => 'ot_gm_tax_free',
				'sort_order' => MODULE_ORDER_TOTAL_GM_TAX_FREE_SORT_ORDER
			));
		}
		// echo 'DELETE FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id="'.$this->cur['OrderID'].'";'."\n\n";	
	}
	
	/**
	 * Returns an array with the replacement keys and the content for the promotion mail.
	 * @return array
	 */
	protected function generatePromoMailContent() {
		return array (
			'#FIRSTNAME#' => $this->o['customer']['customers_firstname'],
			'#LASTNAME#' => $this->o['customer']['customers_lastname'],
			'#EMAIL#' => $this->o['customer']['customers_email_address'],
			'#PASSWORD#'  => $this->cur['customer']['Password'],
			'#ORDERSUMMARY#' => $this->mailOrderSummary,
			'#MARKETPLACE#' => $this->marketplaceTitle,
			'#SHOPURL#' => HTTP_SERVER.DIR_WS_CATALOG,
		);
	}
	
	protected function sendPromoMail() {
		if (($this->config['MailSend'] != 'true') || (get_class($this->db) == 'MagnaTestDB')) return;
		sendSaleConfirmationMail(
			$this->mpID,
			$this->o['customer']['customers_email_address'],
			$this->generatePromoMailContent()
		);
	}
	
	protected function processSingleOrder() {
		if ($this->verbose) echo print_m($this->o, 'order');
		if (!$this->updateOrderCurrency($this->o['order']['currency'])) {
			/* Currency is not available in this shop or 
			   the currency value can't be determined. */
			if ($this->verbose) echo '!updateOrderCurrency'."\n";
			return;
		}
		/* Reset order specific class atributes */
		$this->cur = array();
		$this->taxValues = array();
		$this->mailOrderSummary = array();
		
		/* Prepare order specific informations */
		$this->prepareOrderInfo();
		
		$this->processCustomer();
		#echo print_m($this->cur['customer'], '$customer');
		
		if ($this->orderExists()) {
			return;
		}
		$this->insertOrder();
	
		foreach ($this->o['products'] as $p) {
			$this->p = $p;
			$this->insertProduct();
		}
		//echo 'DELETE FROM '.TABLE_ORDERS_PRODUCTS.' WHERE orders_id="'.$this->cur['OrderID'].'";'."\n\n";
		
		$this->proccessShippingTax();
		
		$this->addTaxValuesToOrdersTotal();
		
		$this->insertOrdersTotal();
		
		$this->sendPromoMail();
		
		$this->addCurrentOrderToProcessed();
		
		$this->lastOrderDate = $this->o['order']['date_purchased'];
	}
	
	protected function acknowledgeImportedOrders() {
		if (empty($this->processedOrders)) return;
		/* Acknowledge imported orders */
		$request = array(
			'ACTION' => 'AcknowledgeImportedOrders',
			'SUBSYSTEM' => $this->marketplace,
			'MARKETPLACEID' => $this->mpID,
			'DATA' => $this->processedOrders,
		);
		if (get_class($this->db) == 'MagnaTestDB') {
			if ($this->verbose) echo print_m($request);
			$this->processedOrders = array();
			return;
		}
		try {
			$res = MagnaConnector::gi()->submitRequest($request);
			$this->processedOrders = array();
		} catch (MagnaException $e) {
			if ((MAGNA_CALLBACK_MODE == 'STANDALONE') || $this->verbose) {
				echo print_m($e->getErrorArray(), 'Error: '.$e->getMessage(), true);
			}
			if ($e->getCode() == MagnaException::TIMEOUT) {
				$e->saveRequest();
				$e->setCriticalStatus(false);
			}
		}
	}
	
	protected function submitSyncBatch() {
		if (get_class($this->db) != 'MagnaTestDB') {
			require_once(DIR_MAGNALISTER_CALLBACK.'inventoryUpdate.php');
			magnaInventoryUpdateByOrderImport(array_values($this->syncBatch), $this->mpID);
		}
	}
	
	final public function process() {
		while (($orders = $this->getOrders()) !== false) {
			#if ($this->verbose) echo print_m($orders, 'orders');
			foreach ($orders as $order) {
				$this->cur = array();
				$this->o = $order;
				
				$continue = false;
				/* {Hook} "MagnaCompatibleImportOrders_PreOrderImport": Is called before the order in <code>$this->o</code> is imported.
					Variables that can be used:
					<ul><li>$this->o: The order that is going to be imported. The order is an 
					        associative array representing the structures of the order and customer related shop tables.</li>
					    <li>$this->mpID: The ID of the marketplace.</li>
					    <li>$this->marketplace: The name of the marketplace.</li>
					    <li>$this->db: Instance of the magnalister database class. USE THIS for writing or changing data in the database during the
					        order import. DO NOT USE the shop functions or MagnaDB::gi() for this purpose!</li>
						<li>$continue (bool): Set this to true to skip the processing of current order.</li>
					</ul>
				*/
				if (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PreOrderImport', 1)) !== false) {
					require($hp);
				}
				if ($continue) {
					continue;
				}
				
				
				$this->processSingleOrder();
				
				/* {Hook} "MagnaCompatibleImportOrders_PostOrderImport": Is called after the order in <code>$this->o</code> is imported.
					Usefull to manipulate some of the data in the database
					Variables that can be used:
					<ul><li>$this->o: The order that has just been imported. The order is an associative array representing the
					        structures of the order and customer related shop tables.</li>
					    <li>$this->mpID: The ID of the marketplace.</li>
					    <li>$this->marketplace: The name of the marketplace.</li>
					    <li>$this->cur['OrderID']: The Order ID of the shop (<code>orders_id</code>).</li>
					    <li>$this->cur['customer']['ID']: The Customers ID of the shop (<code>customers_id</code>).</li>
					    <li>$this->db: Instance of the magnalister database class. USE THIS for writing or changing data in the database during the
					        order import. DO NOT USE the shop functions or MagnaDB::gi() for this purpose!</li>
					</ul>
				*/
				if (($hp = magnaContribVerify('MagnaCompatibleImportOrders_PostOrderImport', 1)) !== false) {
					require($hp);
				}
			}
			$this->acknowledgeImportedOrders();
		}
		
		$this->submitSyncBatch();
		
		if (get_class($this->db) != 'MagnaTestDB') {
			if (!empty($this->lastOrderDate)) {
				setDBConfigValue($this->marketplace.'.orderimport.lastrun', $this->mpID, $this->lastOrderDate, true);
			}
		}
		
	}

}
