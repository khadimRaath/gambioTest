<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

class heidelpayGW{
	var $version		= 'HGW 16.08.12';
	var $live_url 		= 'https://heidelpay.hpcgw.net/ngw/post';
	var $test_url		= 'https://test-heidelpay.hpcgw.net/ngw/post';
	var $response		= '';
	var $error			= '';
	var $pageURL 	= '';
	var $requestUrl	= '';
	var $gx_version 	= '';
	var $modulConfButton = '';
	var $js_setter;
	var $formUrl;
	var $defConf;
	var $getConf;
	var $coo_ot_total;
	var $adminAccess = 'admin_access';
	var $hasRow = false;
	const DB_PREFIX = 'HGW_';

	function heidelpayGW(){
		ob_start();

		include(DIR_FS_CATALOG.'release_info.php');
		require_once(DIR_FS_CATALOG . 'includes/modules/order_total/ot_total.php');
		
		$this->defConf = array(
			'senderId' 			=>	'31HA07BC8142C5A171745D00AD63D182',
			'login' 			=>	'31ha07bc8142c5a171744e5aef11ffd3',
			'pw' 				=>	'93167DE7',
			'transactionMode' 	=>	1,
			'cc_chan' 			=>	'31HA07BC8142C5A171744F3D6D155865',
			'dc_chan' 			=>	'31HA07BC8142C5A171744F3D6D155865',
			'dd_chan' 			=>	'31HA07BC8142C5A171749A60D979B6E4',
			'pp_chan' 			=>	'31HA07BC8142C5A171749A60D979B6E4',
			'iv_chan' 			=>	'31HA07BC8142C5A171749A60D979B6E4',
			'su_chan' 			=>	'31HA07BC8142C5A171749A60D979B6E4',
			'pay_chan' 			=>	'31HA07BC8142C5A171749A60D979B6E4',
			'bs_chan' 			=>	'31HA07BC8142EE6D02715F4CA97DDD8B',
			'eps_chan' 			=>	'',
			'idl_chan' 			=>	'31HA07BC8142C5A171744B56E61281E5',
			'mk_chan' 			=>	'31HA07BC8142EE6D0271011E4508C3F2',
			'gp_chan' 			=>	'31HA07BC8142C5A171740166AF277E03',
			'pf_chan' 			=>	'31HA07BC811E8AEF9AB2733D80C21DA8',
			'bp_chan' 			=>	'',
			'cc_bookingMode' 	=>	1,
			'dc_bookingMode' 	=>	1,
			'dd_bookingMode' 	=>	1,
			'pay_bookingMode' 	=>	1,
			'debug' 			=>	0,
			'iban' 				=>	2,
			'secret' 			=>	strtoupper(sha1(mt_rand(10000, mt_getrandmax()))),
			'shippinghash' 		=>	0,
		);

		$this->pageURL = GM_HTTP_SERVER.'';
		$this->getConf = $this->getConf();

		$this->checkAdminAccess();
		if($this->hasRow){
			$this->modulConfButton = '<div class="add-margin-top-20"><a class="btn" style="margin: 0 0 10px 0;" href="' . GM_HTTP_SERVER . DIR_WS_ADMIN . 'admin.php?do=HeidelpayModuleCenterModule">' . HGW_TXT_CONFIG . '</a></div>';
		}
		$this->gx_version = $gx_version; // $gx_version is set in release_info.php		
		$this->coo_ot_total = new ot_total();

		/* create log file */
		$this->_logger = MainFactory::create_object('FileLog', array('heidelpaygw', true));
	}
	
	/*
	 * function to log errors
	 */
	public function log($file, $msg){
		$timestamp = date('Y-m-d - H:i:s');
		$callers = debug_backtrace();		
		$log = $timestamp.' || '.$file.' => '.$msg."\n";

		if(isset($callers)){
			$log .= "\tBacktrace:\n";
			foreach($callers as $key => $value){
				$log .= "\t\t[".$key.'] = '. $value['function'].'() || '.$value['file'].':'.$value['line']."\n";
			}
		}
		$log .= "\n";
		$this->_logger->write($log);
	}
	
	/*
	 * function to set configuration
	 */	
	public function setConf($vals = array()){
		$return = true;

		foreach($vals as $key => $val){		
			if(($key == 'secret') && ($val == '')){
				$_SESSION[$messages_ns][] = "'Secret' darf nicht leer sein";
				$return = false;
			}else{
				$dbKey = self::DB_PREFIX.strtoupper($key);
				$dbVal = xtc_db_input($val);
				gm_set_conf($dbKey, $dbVal);
			}
		}
		
		return $return;
	}

	/*
	 * function to get configuration
	 * return array
	 */	
	public function getConf(){
		$sql = "SELECT gm_key, gm_value FROM `gm_configuration` WHERE gm_key LIKE 'HGW_%'";
		$result = xtc_db_query($sql);
	
		foreach($this->defConf as $key => $val){
			$dbKey = self::DB_PREFIX.strtoupper($key);
			$dbVal = gm_get_conf($dbKey);			
			
			if(xtc_db_num_rows($result) > 0){
				$this->getConf[$key] = $dbVal;
			}
		}
		return $this->getConf;
	}

	public function checkAdminAccess(){	
		$sql = "SELECT * FROM ".$this->adminAccess." LIMIT 1";		
		$result = xtc_db_fetch_array(xtc_db_query($sql));
		
		foreach($result as $key => $value){
			if($key == 'heidelpaygw'){ $this->hasRow = true; }
		}
		
		if(!$this->hasRow){
			xtc_db_query('ALTER TABLE `'.$this->adminAccess.'` ADD `heidelpaygw` INT( 1 ) NOT NULL DEFAULT \'0\';');
			xtc_db_query('UPDATE `'.$this->adminAccess.'` SET `heidelpaygw` = \'1\' WHERE `customers_id` = \''.$_SESSION['customer_id'].'\' LIMIT 1;');
			$this->hasRow = true;
		}
	}
	
	/**
	 * Create database table for registration data
	 */	
	public function checkRegTable(){
		$sql = "CREATE TABLE IF NOT EXISTS `heidelpayGW_regdata` (
			`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`userID` bigint(20) UNSIGNED NOT NULL,
			`payType` enum('cc','dc','dd','va') NOT NULL,
			`uid` varchar(32) NOT NULL,
			`cardnr` varchar(25) NOT NULL,
			`expMonth` tinyint(2) UNSIGNED NOT NULL,
			`expYear` int(4) UNSIGNED NOT NULL,
			`brand` varchar(25) NOT NULL,
			`owner` varchar(100) NOT NULL,
			`kto` varchar(25) NOT NULL,
			`blz` varchar(25) NOT NULL,
			`chan` varchar(32) NOT NULL,
			`shippingHash` varchar(128) NOT NULL,
			`email` varchar(70) NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		xtc_db_query($sql);
		
		$res = xtc_db_query("SHOW INDEX FROM `heidelpayGW_regdata` WHERE Key_name = 'userPm'");
		if(xtc_db_num_rows($res) == 0){
			$sql = "CREATE UNIQUE INDEX userPm ON `heidelpayGW_regdata` (userID, payType);";
			xtc_db_query($sql);
		}
	}
	
	/**
	 * Method to check order states and add them if they're missing
	 */	
	function checkOrderStatus(){
		$status[330][1] = 'Canceled';
		$status[330][2] = 'Abgebrochen';
		$status[331][1] = 'Check payment receipt';				
		$status[331][2] = 'Zahlungseingang prüfen';
		$status[332][1] = 'Reserved';
		$status[332][2] = 'Reserviert';
		$status[333][1] = 'Paid';
		$status[333][2] = 'Bezahlt';
		$status[334][1] = 'Partial amount paid';
		$status[334][2] = 'Teilbetrag bezahlt';
		
		foreach($status as $s_id => $s_lang){
			foreach($s_lang as $lang_id => $name){			
				$sql = "
				INSERT INTO `orders_status` (orders_status_id, language_id, orders_status_name)
				VALUES ('".$s_id."', '". $lang_id."', '". $name."')
				ON DUPLICATE KEY UPDATE orders_status_id = orders_status_id";
				
				xtc_db_query($sql);
			}
		}
	}

	/**
	 * Method to load registerd payment data
	 * @param int $userId - user id
	 * @param string $pm - payment method
	 * @return array $regData
	 */
	function getRegData($userId, $pm){
		$sql = 'SELECT * FROM `heidelpayGW_regdata` WHERE userID = '.intval($userId).' AND payType = "'.htmlspecialchars($pm).'"';
		$result = xtc_db_query($sql);
	
		while ($regDataTMP = xtc_db_fetch_array($result)){
			$regData = $regDataTMP;
		}
		
		if(!empty($regData)){
			$regData = $this->checkRegData($regData);
		}
		
		return $regData;
	}
	
	/**
	 * Method to check if Registration Channel has changed.
	 * If it has changed, remove registration for this payment method
	 * @params array $regData - registered payment data
	 * @return false or array $regData
	 */
	function checkRegData($regData){
		$pm = $regData['payType'];
		if($regData['payType'] == 'va'){ $pm = 'pay'; }
		$channel = $this->getConf[$pm.'_chan'];

		if($regData['chan'] != $channel){
			$this->removeRegData($regData['id']);
			return false;
		}
		return $regData;
	}
	
	/**
	 * Method to remove registration from Database
	 * @param string $id - id of registration
	 */	
	function removeRegData($id){
// 		$sql = 'DELETE FROM `heidelpayGW_regdata` WHERE id = '.intval($id);
// 		xtc_db_query($sql);
		
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$sql = "DELETE FROM `heidelpayGW_regdata` WHERE id = ?";
		$query = $db->query($sql, array(intval($id)));
	}
	
	/**
	 * Create database if table for transaction data exists
	 */
	public function checkTransactTable(){
		$sql = 'CREATE TABLE IF NOT EXISTS `heidelpayGW_transactions` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`meth` char(2) NOT NULL,
			`type` char(2) NOT NULL,
			`IDENTIFICATION_UNIQUEID` varchar(32) NOT NULL,
			`IDENTIFICATION_SHORTID` varchar(14) NOT NULL,
			`IDENTIFICATION_TRANSACTIONID` varchar(255) NOT NULL,
			`IDENTIFICATION_REFERENCEID` varchar(32) NOT NULL,
			`PROCESSING_RESULT` varchar(20) NOT NULL,
			`PROCESSING_RETURN_CODE` varchar(11) NOT NULL,
			`PROCESSING_STATUS_CODE` varchar(2) NOT NULL,
			`TRANSACTION_SOURCE` varchar(10) NOT NULL,
			`TRANSACTION_CHANNEL` varchar(32) NOT NULL,
			`jsonresponse` blob NOT NULL,
			`created` datetime NOT NULL,
			PRIMARY KEY (`id`),
			KEY `meth` (`meth`),
			KEY `type` (`type`),
			KEY `IDENTIFICATION_UNIQUEID` (`IDENTIFICATION_UNIQUEID`),
			KEY `IDENTIFICATION_SHORTID` (`IDENTIFICATION_SHORTID`),
			KEY `IDENTIFICATION_TRANSACTIONID` (`IDENTIFICATION_TRANSACTIONID`),
			KEY `IDENTIFICATION_REFERENCEID` (`IDENTIFICATION_REFERENCEID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

		xtc_db_query($sql);
	}	
	
	/*
	 * save transaction response to db
	 */
public function saveRes($paramsRaw){
		foreach($paramsRaw as $key => $value){ $params[str_replace('_','.',$key)] = $value; }		
	
		if(!empty($params['IDENTIFICATION.UNIQUEID'])){

			foreach ($params as $key => $value){ $params[$key] = $value; }
			// to-do: TRANSACTION_SOURCE = shop oder xml
			$serial		= json_encode($params);
			$payType	= substr($params['PAYMENT.CODE'], 0, 2);
			$transType	= substr($params['PAYMENT.CODE'], 3, 2);
		
			$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
			//search if an entry in hp-transaction-table exists
			$sql = 'SELECT `id` FROM `heidelpayGW_transactions` WHERE `IDENTIFICATION_UNIQUEID`= ?;';
			$query = $db->query($sql, array($params['IDENTIFICATION.UNIQUEID']));
			$result = $query->row_array();
		
			if (!empty($result)) {
				$id = $result['id'];
			}

			// check if DB-Entry already exists
			if($id > 0){	
				
				if($params['TRANSACTION.SOURCE'] == 'PUSH') {
					$sql = "UPDATE `heidelpayGW_transactions` SET 
							PROCESSING_RESULT		= ?, 
							PROCESSING_RETURN_CODE	= ?,
							PROCESSING_STATUS_CODE	= ?, 
							TRANSACTION_SOURCE		= ?, 
							IDENTIFICATION_REFERENCEID=?, 
							jsonresponse			= ?,
							created					= NOW()
							WHERE `id`				= ?";
					
					$query = $db->query($sql, array(
							$params['PROCESSING.RESULT'],
							$params['PROCESSING.RETURN.CODE'],
							$params['PROCESSING.STATUS.CODE'],
							$params['TRANSACTION.SOURCE'],
							$params['IDENTIFICATION.REFERENCEID'],
							$serial,
							$id,
					));
					
					$affRows = $db->affected_rows();
					if ($affRows <= 0) {
						/* Schreibe Logeintrag */;
						$this->log(__FILE__, "
							\n\tSQL-Error while saving in heidelpay_transactions:
							\n\tError: " . $query->error()
							);
					}
				}else{
					$sql = "UPDATE `heidelpayGW_transactions` SET 
							meth						= ?, 
							type						= ?, 
							IDENTIFICATION_UNIQUEID		= ?, 
							IDENTIFICATION_SHORTID		= ?, 
							IDENTIFICATION_TRANSACTIONID= ?, 
							IDENTIFICATION_REFERENCEID	= ?, 
							PROCESSING_RESULT			= ?, 
							PROCESSING_RETURN_CODE		= ?, 
							PROCESSING_STATUS_CODE		= ?, 
							TRANSACTION_SOURCE			= ?, 
							TRANSACTION_CHANNEL			= ?, 
							jsonresponse				= ?, 
							created						= NOW()
							WHERE `id`					= ?";
				
					$query = $db->query($sql, array(
							$payType, 
							$transType, 
							$params['IDENTIFICATION.UNIQUEID'], 
							$params['IDENTIFICATION.SHORTID'],
							$params['IDENTIFICATION.TRANSACTIONID'], 
							$params['IDENTIFICATION.REFERENCEID'],
							$params['PROCESSING.RESULT'],
							$params['PROCESSING.RETURN.CODE'],
							$params['PROCESSING.STATUS.CODE'], 
							$params['TRANSACTION.SOURCE'],
							$params['TRANSACTION.CHANNEL'],
							$serial,
							$id
					));
					
					$affRows = $db->affected_rows();
					if ($affRows <= 0) {
						/* Schreibe Logeintrag */;
						$this->log(__FILE__, "
							\n\tSQL-Error while saving in heidelpay_transactions:
							\n\tError: " . $query->error()
						);
					}
					
				}
			}else{
				$sql = "
				INSERT INTO `heidelpayGW_transactions` (
						meth, type,	IDENTIFICATION_UNIQUEID, IDENTIFICATION_SHORTID, IDENTIFICATION_TRANSACTIONID, IDENTIFICATION_REFERENCEID,
						PROCESSING_RESULT, PROCESSING_RETURN_CODE, PROCESSING_STATUS_CODE, TRANSACTION_SOURCE, TRANSACTION_CHANNEL,	jsonresponse,
						created)
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
				
				$params['IDENTIFICATION.REFERENCEID'] = empty($params['IDENTIFICATION.REFERENCEID']) ? '' : $params['IDENTIFICATION.REFERENCEID'];
				$params['TRANSACTION.SOURCE'] = empty($params['TRANSACTION.SOURCE']) ? 'RESPONSE' : $params['TRANSACTION.SOURCE'];

				$query = $db->query($sql, array(
						$payType, $transType, $params['IDENTIFICATION.UNIQUEID'], $params['IDENTIFICATION.SHORTID'], $params['IDENTIFICATION.TRANSACTIONID'], $params['IDENTIFICATION.REFERENCEID'],
						$params['PROCESSING.RESULT'], $params['PROCESSING.RETURN.CODE'], $params['PROCESSING.STATUS.CODE'],	$params['TRANSACTION.SOURCE'],$params['TRANSACTION.CHANNEL'], $serial,
					));
						
				$affRows = $db->affected_rows();
				
				if ($affRows <= 0) {
					/* Schreibe Logeintrag */;
					$this->log(__FILE__, "
							\n\tSQL-Error while saving in heidelpay_transactions:
							\n\tError: " . $query->error()
					);
				}
			}
		}		
	}
	
	/**
	 * function to serialize the post data and save them in the database
	 * @param int $id
	 * @return array
	 */
	private function saveSERIAL($id, $data){
		foreach ($data AS $key => $value){
			$data[$key] = urlencode($value);
		}
		$serial = serialize($data);
		$sql = 'UPDATE `'.$this->dbtable.'` 
			SET `SERIAL` = "'.addslashes($serial).'" 
			WHERE `id` = '.(int)$id;
		return Shopware()->Db()->query($sql);
	}
	
	/**
	 * Method to get Form-URL for Heidelpay whitelabel solution
	 * @param string $pm - payment code
	 * @param string $bookingMode - booking mode
	 * @param string $userId - user id
	 * @param string $uid - unique id
	 * @param array $basket - basket information
	 * @param array $ppd_crit - criterions
	 * @return $response - if transaction is ACK
	 */
	function getFormUrl($pm, $bookingMode, $userId, $uid=NULL, $basket=NULL, $ppd_crit=NULL){	
		if((isset($_SESSION['tmp_oID'])) && ($_SESSION['tmp_oID'] != '')){ 
			$orderId = $_SESSION['tmp_oID'];
		}else{
			$orderId = md5($pm.date('YmdHis'));
		}

		$basket['customer']['id'] = $userId;
		$basket['billing']['id'] = $userId;
		$ppd_config = $this->ppd_config($bookingMode, $pm, $uid);
		
		if($pm == 'pay'){
			$ppd_user = $this->ppd_user($basket['delivery']);
		}else{		
			$ppd_user = $this->ppd_user($basket['billing']);
		}
		
		$ppd_bskt['PRESENTATION.AMOUNT'] = $this->formatNumber($this->coo_ot_total->output['0']['value']);
		$ppd_bskt['PRESENTATION.CURRENCY'] = $basket['info']['currency'];
		$ppd_crit['CRITERION.USER_ID'] = $userId;

		$ppd_crit['CRITERION.SECRET'] = $this->createSecretHash($orderId);
		$ppd_crit['CRITERION.SESSIONID'] = session_id();
		
		$ppd_crit['IDENTIFICATION.TRANSACTIONID'] = $orderId;		
		$response = $this->doRequest($this->preparePostData($ppd_config, array(), $ppd_user, $ppd_bskt, $ppd_crit));

		if($response['PROCESSING.RESULT'] == 'ACK'){
			return $response;
		}else{
			$this->log(__FILE__, "
				\n\t".$pm.": " . $response['PROCESSING.RETURN']);
			if($uid == NULL){
				return $response;
			}else{
				// if there is an issue with the RG in the DB, call the function again to preform a RG instead of a RR
				$return = $this->getFormUrl($pm, $bookingMode, $userId, NULL, $basket, $ppd_crit);
				$return['delReg'] = true;
				return $return;
			}
		}
	}

	/**
	 * Method to get config data
	 * @param string $bookingMode - booking mode
	 * @param string $pm - payment code
	 * @param string $uid - unique id
	 * @return array $ppd_config
	 */
	function ppd_config($bookingMode, $pm, $uid=NULL, $gateway=NULL, $isabo=NULL){
		$getConf = $this->getConf;

		if($bookingMode == 1){ $ppd_config['PAYMENT.TYPE'] = "DB"; }
		if($bookingMode == 2){ $ppd_config['PAYMENT.TYPE'] = "PA"; }
		if(($bookingMode == 3) || ($bookingMode == 4)){
			if($uid != NULL){
				if($gateway && $bookingMode == 3){
					$ppd_config['PAYMENT.TYPE'] = "DB";
				}elseif($gateway && $bookingMode == 4){
					$ppd_config['PAYMENT.TYPE'] = "PA";
				}else{
					$ppd_config['PAYMENT.TYPE'] = "RR";
				}
				$ppd_config['IDENTIFICATION.REFERENCEID'] = $uid;
			}else{
				$ppd_config['PAYMENT.TYPE'] = "RG";
			}
		}
		$ppd_config['SECURITY.SENDER']	= trim($getConf['senderId']);
		$ppd_config['USER.LOGIN'] 		= trim($getConf['login']);
		$ppd_config['USER.PWD'] 		= trim($getConf['pw']);

		$ta_mode = $getConf['transactionMode'];
		
		if(is_numeric($ta_mode) && $ta_mode == 0){
			$ppd_config['TRANSACTION.MODE'] = 'LIVE';
			$this->requestUrl = $this->live_url;
		}else{
			$ppd_config['TRANSACTION.MODE'] = 'CONNECTOR_TEST';
			$this->requestUrl = $this->test_url;
		}

		$ppd_config['TRANSACTION.CHANNEL'] = trim($getConf[$pm.'_chan']);
		$ppd_config['PAYMENT.METHOD'] = $pm;		
		$ppd_config['SHOP.TYPE'] = 'Gambio GX '.$this->gx_version;
		$ppd_config['SHOPMODULE.VERSION'] = $this->version;

		return $ppd_config;
	}

	/**
	 * Method to get user data
	 * @return array $ppd_user
	 */	
	function ppd_user($user=NULL){
		if($user == NULL){			
			$user = $GLOBALS['order']->billing;		
			$ppd_user['IDENTIFICATION.SHOPPERID']	= $_SESSION['customer_id'];
		}else{
			$ppd_user['IDENTIFICATION.SHOPPERID']	= $user['id'];
		}
		
		$ppd_user['ADDRESS.COUNTRY'] 					= $user['country']['iso_code_2'];
		$ppd_user['NAME.GIVEN'] 								= $user['firstname'];
		$ppd_user['NAME.FAMILY'] 								= $user['lastname'];
		$ppd_user['ADDRESS.STREET'] 						= $user['street_address'];
		$ppd_user['ADDRESS.ZIP'] 								= $user['postcode'];
		$ppd_user['ADDRESS.CITY'] 							= $user['city'];
		$ppd_user['CONTACT.EMAIL'] 							= $GLOBALS['order']->customer[email_address];
		$ppd_user['ACCOUNT.HOLDER'] 						= $ppd_user['NAME.GIVEN'].' '.$ppd_user['NAME.FAMILY'];		
		if($user['company'] != ''){ $ppd_user['NAME.COMPANY'] = $user['company'];}

		return $ppd_user;
	}

	/**
	 * Method to prepare post data
	 * @param array $config - config params
	 * @param array $frontend - frontend params
	 * @param array $userData - userData params
	 * @param array $basketData - basket params
	 * @param array $criterion - criterions
	 * @return array $params
	 */		
	function preparePostData($config = array(), $frontend = array(), $userData = array(), $basketData = array(), $criterion = array()){
		
		$params = array();
		/*
		 * configurtation part of this function
		 */
		$params['SECURITY.SENDER']		= $config['SECURITY.SENDER'];
		$params['USER.LOGIN'] 			= $config['USER.LOGIN'];
		$params['USER.PWD'] 			= $config['USER.PWD'];
		$params['TRANSACTION.MODE']		= $config['TRANSACTION.MODE'];
		$params['TRANSACTION.CHANNEL']	= $config['TRANSACTION.CHANNEL'];
		$params['CONTACT.IP'] 			= $_SERVER['REMOTE_ADDR'];
		$params['FRONTEND.LANGUAGE'] 	= strtoupper($_SESSION['language_code']);
		$params['FRONTEND.MODE'] 		= "WHITELABEL";

		/* set payment method */
		switch($config['PAYMENT.METHOD']){
			/* sofort banking */
			case 'su':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "OT.".$type;
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
			/* griopay */
			case 'gp':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "OT.".$type;
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
			/* ideal */
			case 'idl':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "OT.".$type;
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
			/* eps */
			case 'eps':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "OT.".$type;
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
			/* postfinance */
			case 'pf':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "OT.".$type;
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
			/* paypal */
			case 'pay':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'DB' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "VA.".$type;
				$params['ACCOUNT.BRAND'] 		= "PAYPAL";
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
			/* prepayment */
			case 'pp':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "PP.".$type;
				$params['FRONTEND.ENABLED'] 	= "false";				
				break;
			/* invoce */
			case 'iv':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "IV.".$type;
				$params['FRONTEND.ENABLED'] 	= "false";
				break;
			/* BillSafe */
			case 'bs':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "IV.".$type;
				$params['ACCOUNT.BRAND']		= "BILLSAFE";
				$params['FRONTEND.ENABLED']	= "false";
				break;
			/* BarPay */
			case 'bp':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "PP.".$type;
				$params['ACCOUNT.BRAND'] 		= "BARPAY";
				$params['FRONTEND.ENABLED']	= "false";
				break;				
			/* MangirKart */
			case 'mk':
				$type = (!array_key_exists('PAYMENT.TYPE',$config)) ? 'PA' : $config['PAYMENT.TYPE'];
				$params['PAYMENT.CODE'] 			= "PC.".$type;
				$params['ACCOUNT.BRAND'] 		= "MANGIRKART";
				$params['FRONTEND.ENABLED']	= "false";
				$basketData['PRESENTATION.CURRENCY'] = 'TRY';
				break;
			/* default */		 			 		
			default:
				$params['PAYMENT.CODE'] 			= strtoupper($config['PAYMENT.METHOD']).'.'.$config['PAYMENT.TYPE'];
				$params['FRONTEND.RETURN_ACCOUNT'] = "true";
				$params['FRONTEND.ENABLED'] 	= "true";
				break;
		}
		/* Debit on registration */
		if(array_key_exists('ACCOUNT.REGISTRATION',$config)){
			$params['ACCOUNT.REGISTRATION']	= $config['ACCOUNT.REGISTRATION'];
			$params['FRONTEND.ENABLED']		= "false";
		}
		
		if (array_key_exists('SHOP.TYPE',$config)) $params['SHOP.TYPE'] = $config['SHOP.TYPE'];
		if (array_key_exists('SHOPMODULE.VERSION',$config)) $params['SHOPMODULE.VERSION'] = $config['SHOPMODULE.VERSION'];

		/* frontend configuration  |  override FRONTEND.ENABLED if nessessary */
		if (array_key_exists('FRONTEND.ENABLED',$frontend)){
			$params['FRONTEND.ENABLED'] = $frontend['FRONTEND.ENABLED'];
			unset($frontend['FRONTEND.ENABLED']);
		}
		$params = array_merge($params, $frontend);
		
		/* costumer data configuration */
		$params = array_merge($params, $userData);
		
		/* basket data configuration */
		$params = array_merge($params, $basketData);
		
		/* criterion data configuration */
		$params = array_merge($params, $criterion);

		$params['REQUEST.VERSION'] = "1.0";
		$params['FRONTEND.RESPONSE_URL'] =  $this->pageURL.DIR_WS_CATALOG."ext/heidelpay/heidelpayGW_response.php".'?'.session_name().'='.session_id();

		if(!empty($config['IDENTIFICATION.REFERENCEID'])){
			$params['IDENTIFICATION.REFERENCEID'] = $config['IDENTIFICATION.REFERENCEID'];
		}
		
		return $params;
	}
	
	/**
	 * Method to send request and get answer via CURL
	 * @param array $data
	 * @return array $res
	 */
	function doRequest($data=array()){
		// get ShopbaseUrl for param[FRONTEND.PAYMENT_FRAME_ORIGIN]
	$baseUrl = '';
		if(isset($_SERVER['REQUEST_SCHEME']) && !empty($_SERVER['REQUEST_SCHEME']))
		{
			$baseUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
		} else {
			if($_SERVER['SERVER_PORT']== '443') {
			$baseUrl = 'https';
			} else {
				$baseUrl = 'http';
			}
			$baseUrl .= '://'.$_SERVER['SERVER_NAME'];
		}
		
		//nessessary for PaymentFrame iFrame
		if ( strpos($data['PAYMENT.CODE'], 'DC') !== false || strpos($data['PAYMENT.CODE'], 'CC') !== false ) {
			$data['FRONTEND.PAYMENT_FRAME_ORIGIN'] = $baseUrl;
			$data['FRONTEND.PREVENT_ASYNC_REDIRECT'] = 'FALSE';
		}
	
		foreach(array_keys($data) AS $key){
			$$key .= $data[$key];
			$$key = urlencode($$key);
			$$key .= "&";
			$var = strtoupper($key);
			$value = $$key;
			$result .= "$var=$value";
		}
		$strPOST = stripslashes($result);
		

	
		// check for CURL existence
		if(function_exists('curl_init')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->requestUrl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $strPOST);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$curlResultString 	= curl_exec($ch);
			$curlerror 				= curl_error($ch);
			$curlinfo 				= curl_getinfo($ch);
			curl_close($ch);
			
			$curlResponseArrayTmp  	= explode("&",$curlResultString);
			$curlResponseArray 			= array();

			foreach($curlResponseArrayTmp AS $responseKeyValPair){
				$temp = urldecode($responseKeyValPair);
				$temp = preg_split("#=#",$temp,2);

				if(array_key_exists(0, $temp) && $temp[0]){
					$responseKey = $temp[0];
					$responseValue = 'NO VALUE FOUND';
					if(array_key_exists('1', $temp)){
						$responseValue = $temp[1];
					}
					$curlResponseArray[$responseKey]=$responseValue;
				}
			}
			$this->saveRes($curlResponseArray);
			return $curlResponseArray;
		}else{
			$msg = urlencode('Curl Fehler');
			$res = 'PROCESSING_RESULT=NOK&PROCESSING_RETURN='.$msg;
			return $res;
		}		
	}

	/**
	 * Method to get error snippets if set
	 * @param string $prc - PROCESSING_RETURN_CODE
	 * @return string $error
	 */		
	function getHPErrorMsg($prc = NULL){
		$error = HGW_HPERROR_DEFAULT;

		if($prc != NULL){
			$prc = str_replace('.','_', $prc);
			$error = defined('HGW_HPERROR_'.$prc) != '' ? constant('HGW_HPERROR_'.$prc) : $error;
		}
		return $error;
	}
	
	/**
	 * function to generate a hash
	 * @param string $orderId
	 * @return string $hash
	 */
	function createSecretHash($orderId){
	
		$getConf = $this->getConf;
		$hash = hash('sha512', $orderId.$getConf['secret']);
		
		return $hash;
	}

	/**
	 * function to cross check if delivery address 
	 * and billing address are the same
	 * @param objekt $order
	 * @return bool
	 */
	function checkAddressMatch($order){
		$diff1 = $this->multiArrDiff($order->delivery, $order->billing);
		$diff2 = $this->multiArrDiff($order->billing, $order->delivery);
		
		if($diff1 && $diff2){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * function to check if keys and values 
	 * of two arrays are the same
	 * @param array $arr1
	 * @param array $arr2
	 * @return bool
	 */		
	function multiArrDiff($arr1,$arr2){
		if((!empty($arr1)) && (!empty($arr2))){
			foreach($arr1 AS $key1 => $val1){
				if(is_array($val1)){
					foreach($val1 AS $key2 => $val2){
						if($arr2[$key1][$key2] != $val2){ return false; }
					}
				}else{
					if($arr2[$key1] != $val1){ return false; }
				}
			}
			return true;
		}
	}

	/** 
	 * generate BillSafe informations from basket
	 * @param object $order
	 * @return array $params - criterions for BillSafe
	 */	
	function getBasketDetails($order){
		GLOBAL$xtPrice;
		$items = $order->products;
		$iKey = '';
		if(isset($items)){
			foreach($items as $iKey => $item){
				$iKey++;
				$prefix = 'CRITERION.POS_'.sprintf('%02d', $iKey);
				$params[$prefix.'.POSITION']		= $iKey;
				$params[$prefix.'.QUANTITY'] 	= (int)$item['qty'];
				if(empty($item['unit_name'])){ $item['unit_name'] = "Stk."; }
				$params[$prefix.'.UNIT'] 			= $item['unit_name'];
				
				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == '0'){
					//amount w/o tax
					$params[$prefix.'.AMOUNT_UNIT']	= round($item['price']*100);
					$params[$prefix.'.AMOUNT']			= round($item['final_price']*100);
				} else {
					//amount with tax
					$params[$prefix.'.AMOUNT_UNIT_GROSS']	= round($item['price']*100);
					$params[$prefix.'.AMOUNT_GROSS']			= round($item['final_price']*100);
				}
				
				$item['name'] = preg_replace('/%/','Proz.', $item['name']);
				$item['name'] = preg_replace('/("|\'|!|$|=)/',' ', $item['name']);
				$params[$prefix.'.TEXT'] = strlen($item['name']) > 100 ? substr($item['name'], 0, 90) . '...' : $item['name'];				
				$params[$prefix.'.ARTICLE_NUMBER'] = $item['id'];
				if($_SESSION['customers_status']['customers_status_add_tax_ot'] == '0'){
					$params[$prefix.'.PERCENT_VAT']	= $this->formatNumber('0');
				}else{
					$params[$prefix.'.PERCENT_VAT']	= $this->formatNumber($item['tax']);
				}
				$params[$prefix.'.ARTICLE_TYPE']		= 'goods';
			}			
		}
		
		if($order->info['shipping_cost'] > 0){
			$shipping_id = explode('_', $order->info['shipping_class']);
			$shipping_id = $shipping_id[0];
			$shipping_tax_rate = $this->get_shipping_tax_rate($shipping_id);
			
			$iKey++;
			$prefix = 'CRITERION.POS_'.sprintf('%02d', $iKey);
			$params[$prefix.'.POSITION']			= $iKey;
			$params[$prefix.'.QUANTITY']			= '1';
			$params[$prefix.'.UNIT']					= 'Stk.';
			if($_SESSION['customers_status']['customers_status_show_price_tax'] == '0'){
				//amount w/o tax
				$params[$prefix.'.AMOUNT_UNIT']	= round($order->info['shipping_cost']*100);
				$params[$prefix.'.AMOUNT']			= round($order->info['shipping_cost']*100);
			}else{
				//amount with tax
				$params[$prefix.'.AMOUNT_UNIT_GROSS']	= round($order->info['shipping_cost']*100);
				$params[$prefix.'.AMOUNT_GROSS']			= round($order->info['shipping_cost']*100);
			}
			$params[$prefix.'.TEXT'] = $order->info['shipping_method'];
			$params[$prefix.'.ARTICLE_NUMBER'] = '0';
			if($_SESSION['customers_status']['customers_status_add_tax_ot'] == '0'){
				$params[$prefix.'.PERCENT_VAT']	= $this->formatNumber('0');
			}else{
				$params[$prefix.'.PERCENT_VAT']	= $this->formatNumber($shipping_tax_rate);
			}
			$params[$prefix.'.ARTICLE_TYPE']		= 'shipment';
		}

		if(isset($GLOBALS['ot_coupon']->deduction)){
			$iKey++;
			$prefix = 'CRITERION.POS_'.sprintf('%02d', $iKey);
			$params[$prefix.'.POSITION']	= $iKey;
			$params[$prefix.'.QUANTITY']	= '1';
			$params[$prefix.'.UNIT']			= 'Stk.';
			if($_SESSION['customers_status']['customers_status_add_tax_ot'] == '0'){
				$params[$prefix.'.AMOUNT_UNIT_GROSS']	= round($xtPrice->xtcFormat($GLOBALS['ot_coupon']->output['0']['value'],false)*100);
				$params[$prefix.'.AMOUNT_GROSS']			= round($xtPrice->xtcFormat($GLOBALS['ot_coupon']->output['0']['value'],false)*100);
			}else{
				$params[$prefix.'.AMOUNT_UNIT_GROSS']	= round(-$GLOBALS['ot_coupon']->deduction*100);
				$params[$prefix.'.AMOUNT_GROSS']			= round(-$GLOBALS['ot_coupon']->deduction*100);				
			}
			$params[$prefix.'.TEXT'] = HGW_TXT_COUP;
			$params[$prefix.'.ARTICLE_NUMBER'] = $GLOBALS['ot_coupon']->coupon_code;
			$params[$prefix.'.PERCENT_VAT']	= $this->formatNumber('0');
			$params[$prefix.'.ARTICLE_TYPE']	= 'voucher';
		}

		if(isset($GLOBALS['ot_gv']->deduction)){
			$iKey++;
			$prefix = 'CRITERION.POS_'.sprintf('%02d', $iKey);
			$params[$prefix.'.POSITION']	= $iKey;
			$params[$prefix.'.QUANTITY']	= '1';
			$params[$prefix.'.UNIT']			= 'Stk.';
			$params[$prefix.'.AMOUNT_UNIT_GROSS']	= round(-$GLOBALS['ot_gv']->deduction*100);
			$params[$prefix.'.AMOUNT_GROSS']			= round(-$GLOBALS['ot_gv']->deduction*100);
			$params[$prefix.'.TEXT'] = HGW_TXT_GIFT;
			$params[$prefix.'.ARTICLE_NUMBER'] = $GLOBALS['ot_gv']->title;
			$params[$prefix.'.PERCENT_VAT']	= $this->formatNumber('0');
			$params[$prefix.'.ARTICLE_TYPE']	= 'voucher';
		}
		return $params;
	}

	/**
	 * function to format a number to 0.00
	 * @param string $value
	 * @return string $value - formatted 
	 */	
	public function formatNumber($value){
		return sprintf('%1.2f', $value);
	}		
	
	/**
	 * function to include JS to checkout
	 * @param string $pm
	 * @return string $checkoutJs - HTML
	 */		
	public function includeCheckoutJs($pm = NULL){

		GLOBAL $js_setter, $formUrl;
		
		// getting template name to set paths to css, js and img folders
		$tplName         = CURRENT_TEMPLATE;
		$templateVersion = gm_get_env_info('TEMPLATE_VERSION');
		
		if ($templateVersion === 3.0) {
			// for Honeygrid
			$tplPathJs     = $tplName . '/assets/javascript/checkout';
			$tplPathCss    = $tplName . '/assets/styles';
			$tplPathImages = $tplName . '/assets/images';
		}
		else
		{
			// for EyeCandy
			$tplPathJs     = $tplName . '/javascript/checkout';
			$tplPathCss    = $tplName . '/css';
			$tplPathImages = $tplName . '/img';
		}
 		


		if((!isset($js_setter)) || ($js_setter == $pm)){
			$js_setter = $pm;
				
			$checkoutJs = '<link type="text/css" rel="stylesheet" href="'.GM_HTTP_SERVER . DIR_WS_CATALOG . 'templates/'.$tplPathCss.'/heidelpay.css" />';
			$checkoutJs .= '
				<script type="text/javascript">var formUrl = '.json_encode($formUrl).';</script>
				<script src="'.GM_HTTP_SERVER . DIR_WS_CATALOG . 'templates/'.$tplPathJs.'/HeidelpayValPayment.js" type="text/javascript"></script>
				<div class="hp_error">
					<div class="msg_checkPymnt">'.HGW_MSG_CHECKPYMNT.'</div>
					<div class="msg_fill">'.HGW_MSG_FILL.'</div>
					<div class="msg_crdnr">'.HGW_MSG_CRDNR.'</div>
					<div class="msg_cvv">'.HGW_MSG_CVV.'</div>
					<div class="msg_iban">'.HGW_MSG_IBAN.'</div>
					<div class="msg_bic">'.HGW_MSG_BIC.'</div>
					<div class="msg_account">'.HGW_MSG_ACCOUNT.'</div>
					<div class="msg_bank">'.HGW_MSG_BANK.'</div>
					<div class="msg_holder">'.HGW_MSG_HOLDER.'</div>
					
					<div class="msg_missnumber">'.HGW_MSG_CRDMISSNUMBER.'</div>
					<div class="msg_missmonth">'.HGW_MSG_CRDMISSEXPMONTH.'</div>
					<div class="msg_missyear">'.HGW_MSG_CRDMISSEXPYEAR.'</div>
					<div class="msg_misscvv">'.HGW_MSG_CRDMISSCVV.'</div>
					<div class="msg_missholder">'.HGW_MSG_CRDMISSHOLDER.'</div>
					<div class="msg_wrongnumber">'.HGW_MSG_CRDWRONGNUMBER.'</div>
					<div class="msg_wrongmonth">'.HGW_MSG_CRDWRONGMONTH.'</div>
					<div class="msg_wrongyear">'.HGW_MSG_CRDWRONGYEAR.'</div>
					<div class="msg_wrongverif">'.HGW_MSG_CRDWRONGVERIFI.'</div>
							
				</div>
			';			
			
				if($_SESSION['MOBILE_ACTIVE'] === 'false'){
					$checkoutJs .= '<script src="'.GM_HTTP_SERVER . DIR_WS_CATALOG . 'gm/javascript/jquery/jquery.js" type="text/javascript"></script>';
				}
				
			return $checkoutJs;
		}else{
			return '<script type="text/javascript">var formUrl = '.json_encode($formUrl).';</script>'.
					'<div class="hp_error">
						<div class="msg_checkPymnt">'.HGW_MSG_CHECKPYMNT.'</div>
						<div class="msg_fill">'.HGW_MSG_FILL.'</div>
						<div class="msg_crdnr">'.HGW_MSG_CRDNR.'</div>
						<div class="msg_cvv">'.HGW_MSG_CVV.'</div>
						<div class="msg_iban">'.HGW_MSG_IBAN.'</div>
						<div class="msg_bic">'.HGW_MSG_BIC.'</div>
						<div class="msg_account">'.HGW_MSG_ACCOUNT.'</div>
						<div class="msg_bank">'.HGW_MSG_BANK.'</div>
						<div class="msg_holder">'.HGW_MSG_HOLDER.'</div>
							
						<div class="msg_missnumber">'.HGW_MSG_CRDMISSNUMBER.'</div>
						<div class="msg_missmonth">'.HGW_MSG_CRDMISSEXPMONTH.'</div>
						<div class="msg_missyear">'.HGW_MSG_CRDMISSEXPYEAR.'</div>
						<div class="msg_misscvv">'.HGW_MSG_CRDMISSCVV.'</div>
						<div class="msg_missholder">'.HGW_MSG_CRDMISSHOLDER.'</div>
						<div class="msg_wrongnumber">'.HGW_MSG_CRDWRONGNUMBER.'</div>
						<div class="msg_wrongmonth">'.HGW_MSG_CRDWRONGMONTH.'</div>
						<div class="msg_wrongyear">'.HGW_MSG_CRDWRONGYEAR.'</div>
						<div class="msg_wrongverif">'.HGW_MSG_CRDWRONGVERIFI.'</div>
					</div>';
		}
	}

	/**
	 * function to update the DB order status 
	 * @param string $orderId
	 * @param string $status
	 */	
	function setOrderStatus($orderId, $status){
// 		xtc_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . (int)$status . "' WHERE orders_id='" . $orderId . "'");
	
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$sql = "UPDATE " . TABLE_ORDERS . " SET orders_status=? WHERE orders_id=?";
		$query = $db->query($sql, array((int)$status,$orderId));
	
	}
	
	/**
	 * function to add a comment to order history
	 * @param string $orderId
	 * @param string $comment
	 * @param string $status
	 * @param string $customer_notified
	 * @return DB-Query
	 */	
	function addHistoryComment($orderId, $comment, $status = '', $customer_notified = '0'){
		if(empty($orderId) || empty($comment)){ return false; }
		
		$orderHistory['orders_id'] = $orderId;
		$orderHistory['orders_status_id'] = $status;
		$orderHistory['date_added'] = date('Y-m-d H:i:s');
		$orderHistory['customer_notified'] = $customer_notified;
		$orderHistory['comments'] = $comment;

		return xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $orderHistory);
	}

	// xtc_remove_order() from Gambio:
	// find it in: admin/includes/functions/general.php
	// needed to restock canceled orders
	function removeOrder($order_id, $restock = false, $canceled = false, $reshipp = false, $reactivateArticle = false){
		if($restock == 'on' || $reshipp == 'on'){
			// BOF GM_MOD:
			$order_query = xtc_db_query("
				SELECT DISTINCT
					op.orders_products_id, 
					op.products_id, 
					op.products_quantity,
					opp.products_properties_combis_id,
					o.date_purchased
				FROM " . TABLE_ORDERS_PRODUCTS . " op
					LEFT JOIN " . TABLE_ORDERS . " o ON op.orders_id = o.orders_id
					LEFT JOIN orders_products_properties opp ON opp.orders_products_id = op.orders_products_id
				WHERE 
					op.orders_id = '" . xtc_db_input($order_id) . "'
			");

			while($order = xtc_db_fetch_array($order_query)){
				if($restock == 'on'){
					/* BOF SPECIALS RESTOCK */
					$t_query = xtc_db_query("
						SELECT
							specials_date_added
						AS
							date
						FROM " .
						TABLE_SPECIALS . "
						WHERE
							specials_date_added < '" . $order['date_purchased'] . "'
						AND
							products_id = '" . $order['products_id'] . "'
					");

					if((int)xtc_db_num_rows($t_query) > 0){
						xtc_db_query("
							UPDATE " .
							TABLE_SPECIALS . "
							SET
								specials_quantity = specials_quantity + " . $order['products_quantity'] . "
							WHERE
								products_id = '" . $order['products_id'] . "'
						");
					}
					/* EOF SPECIALS RESTOCK */

					// check if combis exists
					$t_combis_query = xtc_db_query("
						SELECT
							products_properties_combis_id
						FROM
							products_properties_combis
						WHERE
							products_id = '" . $order['products_id'] . "'
					");
					$t_combis_array_length = xtc_db_num_rows($t_combis_query);

					if($t_combis_array_length > 0){
						$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
						$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
					}else{
						$t_use_combis_quantity = 0;
					}

					if($t_combis_array_length == 0 || $t_use_combis_quantity == 1 || ($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK != 'true')){
						xtc_db_query("
							UPDATE " .
							TABLE_PRODUCTS . "
							SET
								products_quantity = products_quantity + " . $order['products_quantity'] . "
							WHERE
								products_id = '" . $order['products_id'] . "'
						");
					}

					xtc_db_query("
						UPDATE " .
						TABLE_PRODUCTS . "
						SET
							products_ordered = products_ordered - " . $order['products_quantity'] . "
						WHERE
							products_id = '" . $order['products_id'] . "'
					");

					if($t_combis_array_length > 0 && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2)){
						xtc_db_query("
							UPDATE
								products_properties_combis
							SET
								combi_quantity = combi_quantity + " . $order['products_quantity'] . "
							WHERE
								products_properties_combis_id = '" . $order['products_properties_combis_id'] . "' AND
								products_id = '" . $order['products_id'] . "'
						");
					}

					// BOF GM_MOD
					if(ATTRIBUTE_STOCK_CHECK == 'true'){
						$gm_get_orders_attributes = xtc_db_query("
							SELECT
								products_options,
								products_options_values
							FROM
								orders_products_attributes
							WHERE
								orders_id = '" . xtc_db_input($order_id) . "'
							AND
								orders_products_id = '" . $order['orders_products_id'] . "'
						");

						while($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes))
						{
							$gm_get_attributes_id = xtc_db_query("
								SELECT
									pa.products_attributes_id
								FROM
									products_options_values pov,
									products_options po,
									products_attributes pa
								WHERE
									po.products_options_name = '" . $gm_orders_attributes['products_options'] . "'
									AND po.products_options_id = pa.options_id
									AND pov.products_options_values_id = pa.options_values_id
									AND pov.products_options_values_name = '" . $gm_orders_attributes['products_options_values'] . "'
									AND pa.products_id = '" . $order['products_id'] . "'
								LIMIT 1
							");

							if(xtc_db_num_rows($gm_get_attributes_id) == 1){
								$gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

								xtc_db_query("
									UPDATE
										products_attributes
									SET
										attributes_stock = attributes_stock + " . $order['products_quantity'] . "
									WHERE
										products_attributes_id = '" . $gm_attributes_id['products_attributes_id'] . "'
								");
							}
						}
					}
					if($reactivateArticle == 'on'){
						$t_reactivate_product = false;
						
						// check if combis exists
						$t_combis_query = xtc_db_query("
							SELECT
								products_properties_combis_id
							FROM
								products_properties_combis
							WHERE
								products_id = '" . $order['products_id'] . "'
						");
						$t_combis_array_length = xtc_db_num_rows($t_combis_query);
						
						if($t_combis_array_length > 0){
							$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
							$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
						}else{
							$t_use_combis_quantity = 0;
						}
						
						// CHECK PRODUCT QUANTITY
						if($t_combis_array_length == 0 || $t_use_combis_quantity == 1 || ($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK != 'true')){
							$coo_get_product = new GMDataObject('products', array('products_id' => $order['products_id']));
							if($coo_get_product->get_data_value('products_quantity') > 0 && $coo_get_product->get_data_value('products_status') == 0){
								$t_reactivate_product = true;
							}
						}

						// CHECK COMBI QUANTITY
						if($t_combis_array_length > 0 && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2)){
							$coo_properties_control = MainFactory::create_object('PropertiesControl');
							$t_reactivate_product = $coo_properties_control->available_combi_exists($order['products_id']);
						}
						
						if($t_reactivate_product){
							$coo_set_product = new GMDataObject('products');
							$coo_set_product->set_keys(array('products_id' => $order['products_id']));
							$coo_set_product->set_data_value('products_status', 1);
							$coo_set_product->save_body_data();
						}
					}
					// EOF GM_MOD
				}

				// BOF GM_MOD products_shippingtime:
				if($reshipp == 'on'){
					require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');
					set_shipping_status($order['products_id'], $order['products_properties_combis_id']);
				}
				// BOF GM_MOD products_shippingtime:
			}
		}

		if(!$canceled){
			xtc_db_query("DELETE from " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($order_id) . "'");

			$t_orders_products_ids_sql = 'SELECT orders_products_id FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . xtc_db_input($order_id) . '"';
			$t_orders_products_ids_result = xtc_db_query($t_orders_products_ids_sql);
			while($t_orders_products_ids_array = xtc_db_fetch_array($t_orders_products_ids_result)){
				xtc_db_query("DELETE FROM orders_products_quantity_units WHERE orders_products_id = '" . (int)$t_orders_products_ids_array['orders_products_id'] . "'");
				xtc_db_query('DELETE FROM orders_products_properties WHERE orders_products_id = "' . (int)$t_orders_products_ids_array['orders_products_id'] . '"');
			}

			// DELETE from gm_gprint_orders_*, and gm_gprint_uploads
			$coo_gm_gprint_order_manager = MainFactory::create_object('GMGPrintOrderManager');
			$coo_gm_gprint_order_manager->delete_order((int)$order_id);

			xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM banktransfer WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM sepa WHERE orders_id = '" . (int)$order_id . "'");
		}
	}

    function get_shipping_tax_rate($shipping_id){
  		$check_query = xtc_db_query('SELECT configuration_value FROM '.TABLE_CONFIGURATION.' WHERE configuration_key = "MODULE_SHIPPING_'.$shipping_id.'_TAX_CLASS"');
		$configuration = xtc_db_fetch_array($check_query);
		$tax_class_id = $configuration['configuration_value'];
		$shipping_tax_rate = xtc_get_tax_rate($tax_class_id);
		return $shipping_tax_rate;
	}
	
	/**
	 * function to get order status
	 * @param string $orderId
	 * @return $orders_status
	 */	
	public function getOrderStatus($orderId){
// 		$sql = "SELECT `orders_status` FROM `orders` WHERE orders_id = '" . $orderId . "'";
// 		$query = xtc_db_query($sql);
// 		$array = xtc_db_fetch_array($query);
// 		$orders_status = $array['orders_status'];

		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$sql = "SELECT `orders_status` FROM `orders` WHERE orders_id = ?";
		$query = $db->query($sql, array($orderId));
		$result = $query->row_array();
		$orders_status = $result['orders_status'];

		return $orders_status;
	} 
	
	/**
	 * function to get payment method
	 * @param string $orderId
	 * @return $payment_method
	 */	
	public function getPaymentMethod($orderId){
// 		$sql = "SELECT `payment_method` FROM `orders` WHERE orders_id = '" . $orderId . "'";
// 		$query = xtc_db_query($sql);
// 		$array = xtc_db_fetch_array($query);
// 		$payment_method = $array['payment_method'];
	
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$sql = "SELECT `payment_method` FROM `orders` WHERE orders_id = ?";
		$query = $db->query($sql, array($orderId));
		$result = $query->row_array();
		$payment_method = $result['payment_method'];

		return $payment_method;
	}
	
	/**
	 * function to get processing result from database
	 * @param string $uniqueID
	 * @return $orders_status
	 */	
	public function getProcessingResult($uniqueID){
// 		$sql = "SELECT `PROCESSING_RESULT` FROM `heidelpayGW_transactions` WHERE IDENTIFICATION_UNIQUEID = '" . $uniqueID . "'";
// 		$query = xtc_db_query($sql);
// 		$array = xtc_db_fetch_array($query);
// 		$processingResultDB = $array['PROCESSING_RESULT'];
		
		
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		$sql = "SELECT `PROCESSING_RESULT` FROM `heidelpayGW_transactions` WHERE IDENTIFICATION_UNIQUEID = ?";
		$query = $db->query($sql, array($uniqueID));
		$result = $query->row_array();
		$processingResultDB = $result['PROCESSING_RESULT'];

		return $processingResultDB;
	}
	
	/**
	 * function to get status id from configuration
	 * @param string $pm - payment method
	 * @param string $state - state of status
	 * possible values for $state:
	 * - pending
	 * - canceled
	 * - processed
	 * @return $status
	 */	 
	public function getStatus($pm, $state){		
		$state = '_'.strtoupper($state);
		
		$stateName = 'MODULE_PAYMENT_HP'. $pm.$state .'_STATUS_ID';		
		$sql = "SELECT `configuration_value` FROM `configuration` WHERE configuration_key = '" . $stateName . "'";
		
		$query = xtc_db_query($sql);
		$array = xtc_db_fetch_array($query);
		$status = $array['configuration_value'];
		return $status;
	}
	
	/**
	 * function to empty shopping cart and send confirmation mail
	 * @param string $sessionId
	 * @param string $orderId
	 */	
	public function emptyCartAndSendMail($sessionId, $orderId){
		GLOBAL $hgw;
		
		$base = GM_HTTP_SERVER.DIR_WS_CATALOG;
		$url = $base.'checkout_process.php?'. session_name() . '=' . $sessionId;
		$hgw->requestUrl = $url;
		$hgw->doRequest($hgw->requestUrl);

		$hgw->sendMail($orderId);

		xtc_db_query("UPDATE `orders` SET gm_send_order_status=1 WHERE orders_id='" . $orderId . "'");
		$url = $base.'checkout_process.php';
		xtc_redirect($url);
	}
	
	/**
	 * function to send confirmation mail
	 * @param string $orderId
	 */	
	public function sendMail($orderId){
		$coo_send_order_process = MainFactory::create_object('SendOrderProcess');
		$coo_send_order_process->set_('order_id', $orderId);
		$coo_send_order_process->proceed();
	}
	
}
?>