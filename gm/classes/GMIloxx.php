<?php
/* --------------------------------------------------------------
   GMIloxx.php 2016-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class GMIloxx_ORIGIN {
	protected $_soapclient;
	protected $_wsdl;
	protected $_debug = false;
	protected $_logging;
	protected $_logger;
	protected $_txt;
	public $_service_errors = array();

	public $module_version = '2016-07-21';

	const CONFIG_PREFIX = 'ILOXX';

	public function __construct() {
		if($this->_debug == true) {
			$this->_wsdl = 'http://qa.www.iloxx.de/iloxxapi/ppvapi.asmx?WSDL';
		}
		else {
			$this->_wsdl = 'https://www.iloxx.de/iloxxapi/ppvapi.asmx?WSDL';
		}
		$this->_logging = gm_get_conf('ILOXX_LOGGING') == true;
		$this->_logger = LogControl::get_instance();
		$this->_txt = new LanguageTextManager('iloxx', $_SESSION['languages_id']);
	}

	/*
	** utility functions
	*/

	public function log($message) {
		if($this->_logging)
		{
			$this->_logger->notice($message, 'shipping', 'shipping.iloxx');
		}
	}


	protected function prettyXML($xml)
	{
		if(empty($xml))
		{
			return '-- NO CONTENT --';
		}
		$doc                     = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput       = true;
		$doc->loadXML($xml);

		return $doc->saveXML();
	}


	public function _logLastTransaction()
	{
		$soap_responseheaders = $this->_soapclient->__getLastResponseHeaders();
		$soap_response        = $this->_soapclient->__getLastResponse();
		$soap_requestheaders  = $this->_soapclient->__getLastRequestHeaders();
		$soap_request         = $this->_soapclient->__getLastRequest();
		$log                  = "ResponseHeaders:\n\n";
		$log .= $soap_responseheaders;
		$log .= "\n\nResponse:\n\n";
		$log .= $this->prettyXML($soap_response);
		$log .= "\n\nRequestHeaders:\n\n";
		$log .= $soap_requestheaders;
		$log .= "\n\nRequest:\n\n";
		$log .= $this->prettyXML($soap_request);

		return $log;
	}

	protected function _handleSoapFault(SoapFault $sf) {
		if($this->_debug) {
			if($this->_soapclient instanceof SoapClient) {
				$log = $this->_logLastTransaction();
			}
			else {
				$log = 'SoapClient konnte nicht instanziert werden!';
			}
			$log .= "\n\nSoapFault:\n\n";
			$log .= print_r($sf, true) ."\n";
			$this->log($log);
		}
		$this->log("FEHLER: ". (string)$sf->faultstring .' '. (string)$sf->detail);;
	}

	/*
	** I18N
	*/

	public function get_text($name) {
		$replacement = $this->_txt->get_text($name);
		return $replacement;
	}

	public function replaceTextPlaceholders($content) {
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
			$replacement = $this->get_text($matches[1]);
			if(empty($replacement)) {
				$replacement = $matches[1];
			}
			$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}

	/*
	** getters and setters
	*/

	public function __isset($name) {
		$getter = '_get'.ucfirst($name);
		if(method_exists($this, $getter)) {
			$value = $this->$getter();
			$isset = $value !== null;
			return $isset;
		}
		return false;
	}

	public function __get($name) {
		$getter = '_get'.ucfirst($name);
		if(method_exists($this, $getter)) {
			return $this->$getter();
		}
		else {
			return null;
		}
	}

	public function __set($name, $value) {
		$setter = '_set'.ucfirst($name);
		if(method_exists($this, $setter)) {
			$this->$setter($value);
		}
	}

	public function _setUserid($user_id) {
		gm_set_conf(self::CONFIG_PREFIX .'_USER_ID', $user_id);
	}

	public function _getUserid() {
		$user_id = gm_get_conf(self::CONFIG_PREFIX .'_USER_ID');
		$user_id = empty($user_id) ? '' : $user_id;
		return $user_id;
	}

	public function _setUsertoken($user_token) {
		gm_set_conf(self::CONFIG_PREFIX .'_USER_TOKEN', $user_token);
	}

	public function _getUsertoken() {
		$user_token = gm_get_conf(self::CONFIG_PREFIX .'_USER_TOKEN');
		$user_token = empty($user_token) ? '' : $user_token;
		return $user_token;
	}

	protected function _setOslabelacquired($orders_status_id) {
		gm_set_conf(self::CONFIG_PREFIX .'_OS_LABEL_ACQUIRED', $orders_status_id);
	}

	protected function _getOslabelacquired() {
		$orders_status_id = gm_get_conf(self::CONFIG_PREFIX .'_OS_LABEL_ACQUIRED');
		$orders_status_id = empty($orders_status_id) ? '' : $orders_status_id;
		return $orders_status_id;
	}

	protected function _setOstracking($orders_status_id) {
		gm_set_conf(self::CONFIG_PREFIX .'_OS_TRACKING', $orders_status_id);
	}

	protected function _getOstracking() {
		$orders_status_id = gm_get_conf(self::CONFIG_PREFIX .'_OS_TRACKING');
		$orders_status_id = empty($orders_status_id) ? '' : $orders_status_id;
		return $orders_status_id;
	}

	protected function _setUse_weight_options($value) {
		$value = ($value == true) ? 1 : 0;
		gm_set_conf(self::CONFIG_PREFIX.'_USE_WEIGHT_OPTIONS', $value);
	}

	protected function _getUse_weight_options() {
		$weight_options = gm_get_conf(self::CONFIG_PREFIX.'_USE_WEIGHT_OPTIONS');
		$weight_options = $weight_options == true;
		return $weight_options;
	}

	protected function _setDefault_ship_service($value) {
		$services = $this->getShipServices();
		if(array_key_exists($value, $services) === false) {
			$value = 'dpdNormalpaket';
		}
		gm_set_conf(self::CONFIG_PREFIX.'_DEFAULT_SHIP_SERVICE', $value);
	}

	protected function _getDefault_ship_service() {
		$default_ship_service = gm_get_conf(self::CONFIG_PREFIX.'_DEFAULT_SHIP_SERVICE');
		return $default_ship_service;
	}

	protected function _setDefault_ship_service_cod($value) {
		$services = $this->getShipServices();
		if(array_key_exists($value, $services) === false) {
			$value = 'dpdNormalpaketCOD';
		}
		gm_set_conf(self::CONFIG_PREFIX.'_DEFAULT_SHIP_SERVICE_COD', $value);
	}

	protected function _getDefault_ship_service_cod() {
		$default_ship_service_cod = gm_get_conf(self::CONFIG_PREFIX.'_DEFAULT_SHIP_SERVICE_COD');
		return $default_ship_service_cod;
	}

	protected function _setParcelservice_id($parcelServiceId)
	{
		gm_set_conf(self::CONFIG_PREFIX.'_PARCELSERVICE_ID', (int)$parcelServiceId);
	}

	protected function _getParcelservice_id()
	{
		return gm_get_conf(self::CONFIG_PREFIX.'_PARCELSERVICE_ID');
	}

	/*
	** SOAP basics
	*/

	protected function _getSoapClient() {
		if(!$this->_soapclient instanceof SoapClient) {
			$soap_params = array(
					'trace' => true,
					'exceptions' => true,
					'cache_wsdl' => WSDL_CACHE_NONE,
					'user_agent' => 'Gambio GX2',
					'encoding' => 'utf-8',
					'soap_version' => SOAP_1_2,
					'features' => SOAP_USE_XSI_ARRAY_TYPE, //|SOAP_SINGLE_ELEMENT_ARRAYS,
			);

			try {
				$this->_soapclient = new SoapClient($this->_wsdl, $soap_params);
			}
			catch(SoapFault $sf) {
				$this->_handleSoapFault($sf);
				return false;
			}
		}
		return $this->_soapclient;
	}

	public function makeServiceCall($call, $params) {
		$sc = $this->_getSoapClient() or die('Schnittstellenfehler!');
		$call_params = array(
			'ppvGetDailyTransactionList' => array(
				'params_body' => 'ppvDailyTransactionRequest',
				'result_body' => 'ppvGetDailyTransactionListResult',
			),
			'ppvAddOrder' => array(
				'params_body' => 'ppvOrderRequest',
				'result_body' => 'ppvAddOrderResult',
			),
		);

		$params_auth = array(
			'Version' => 102,
			'PartnerCredentials' => array(
				'PartnerName' => 'gambio.15',
				'PartnerKey' => '344E68523769755735426A57486C62654A56766F70513D3D',
			),
			'UserCredentials' => array(
				'UserID' => $this->userid,
				'UserToken' => $this->usertoken,
			)
		);
		$params = array_merge($params_auth, $params);
		//die(print_r($params, true));
		$params_body = $call_params[$call]['params_body'];
		$result_body = $call_params[$call]['result_body'];
		try {
			$result = $sc->$call(array($params_body => $params));
			if(isset($result->$result_body->Ack) && $result->$result_body->Ack == 'Success') {
				return $result->$result_body;
			}
			else {
				$this->_service_errors = array();
				$errors = $result->$result_body->ErrorDataArray->ErrorData;
				if(!is_array($errors)) {
					$errors = array($errors);
				}
				foreach($errors as $ed) {
					$error_txt = $ed->ErrorID ." ". $ed->ErrorMsg;
					$this->log("Fehler bei Aufruf von $call: ".$error_txt);
					$this->_service_errors[] = $error_txt;
				}
				if($this->_debug) {
					$this->log("Result:\n".print_r($result, true));
				}
				return false;
			}
		}
		catch(SoapFault $sf) {
			$this->_handleSoapFault($sf);
			return false;
		}
	}


	/*
	 * Service Calls
	 */

	public function addOrder($orders, $shipdate, $labelsize = "A4", $labelpos = "ul", $mode = "check") {
		if(empty($shipdate)) {
			$shipdate = date('Y-m-d');
		}
		else {
			$shipdate = date('Y-m-d', strtotime($shipdate));
		}
		$labelsizes = array(
			'A4' => 'MultiLabel_A4',
			'A6' => 'SingleLabel_A6',
		);
		$labelpositions = array(
			'ul' => 'UpperLeft',
			'ur' => 'UpperRight',
			'll' => 'LowerLeft',
			'lr' => 'LowerRight',
		);
		$params = array(
			'OrderAction' => $mode == 'check' ? 'check' : 'addOrder',
			'ServiceSettings' => array(
				'ErrorLanguage' => 'German',
				'CountrySettings' => 'Alpha3', # ISO3166|Alpha3
				'ZipCodeSetting' => 'ZipCodeAsSingleValue',
			),
			'OrderLabel' => array(
				'LabelSize' => $labelsizes[strtoupper($labelsize)],
				'LabelStartPosition' => $labelpositions[$labelpos],
			),
			'ShipDate' => $shipdate,
			'ppvOrderDataArray' => array(),
		);
		$track_url = HTTP_SERVER.DIR_WS_CATALOG.'iloxx_track.php?key='.$this->_logger->get_secure_token().'&order_id=';
		foreach($orders as $o) {
			$odata = array(
				'PartnerOrderReference' => $o['orders_id'],
				'Customer' => $o['customers_name'],
				'Reference' => 'ORDER_'.$o['orders_id'],
				'Content' => 'content',
				'Weight' => $o['orders_weight'],
				'ShipService' => $o['iloxx_service'],
				'ShipAddress' => array(
					'SexCode' => $this->_findSexCode($o['customers_id'], $o['delivery_firstname'], $o['delivery_lastname']),
					'Name' => $o['delivery_name'],
					'Street' => $o['delivery_street_address'],
					'ZipCode' => $o['delivery_postcode'],
					'City' => $o['delivery_city'],
					'State' => $o['delivery_state'],
					'Country' => $this->getISO3fromISO2($o['delivery_country_iso_code_2']),
					'Phone' => $o['customers_telephone'],
					'Mail' => $o['customers_email_address'],
				),
				'TrackURL' => $track_url.$o['orders_id'],
			);
			if(!empty($o['delivery_house_number']))
			{
				$odata['ShipAddress']['Street'] .= ' ' . $o['delivery_house_number'];
			}
			if(!empty($o['delivery_company'])) {
				$odata['ShipAddress']['Company'] = $o['delivery_company'];
			}
			if(in_array($o['iloxx_service'], array_keys($this->getCODShipServices()))) {
				$odata['CODAmount'] = $o['value'];
			}
			else {
				$odata['CODAmount'] = 0;
			}

			$params['ppvOrderDataArray'][] = $odata;
		}
		if($this->_debug) {
			$this->log("addOrder $shipdate $labelsize $labelpos $mode with params:\n".print_r($params, true));
		}
		$result = $this->makeServiceCall('ppvAddOrder', $params);
		if($this->_debug) {
			$this->log("ppvAddOrder Result:\n".print_r($result, true));
		}
		if($result !== false && $mode !== 'check' && $result->Ack == "Success") {
			$pdfdata = base64_decode((string)$result->LabelPDFStream);
			file_put_contents($this->getLabelsFileName(), $pdfdata);
			$parcel_query = "UPDATE orders_iloxxdata SET parcelnumber = ':pnumber' WHERE orders_id = :orders_id";
			$responsedata = $result->ResponseDataArray->ResponseData;
			if(!is_array($responsedata)) {
				$responsedata = array($responsedata);
			}
			foreach($responsedata as $rd) {
				$query = strtr($parcel_query, array(':orders_id' => (string)$rd->PartnerOrderReference, ':pnumber' => (string)$rd->ParcelNumber));
				xtc_db_query($query);
				$this->storeTrackingNumber((int)$rd->PartnerOrderReference, (string)$rd->ParcelNumber);
				$this->_setOrdersStatus((string)$rd->PartnerOrderReference, $this->oslabelacquired, "Paketschein ".(string)$rd->ParcelNumber." erzeugt. ");
			}
		}
		return $result;
	}

	public function storeTrackingNumber($orders_id, $trackingNumber)
	{
		if((int)$this->parcelservice_id > 0)
		{
			$parcelServiceReader = MainFactory::create('ParcelServiceReader');
			$parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
			$parcelTrackingCodeWriter->insertTrackingCode($orders_id, $trackingNumber, $this->parcelservice_id, $parcelServiceReader);
		}
	}

	public function getISO3fromISO2($iso2) {
		$query = "SELECT countries_iso_code_3 FROM countries WHERE countries_iso_code_2 = '".xtc_db_input($iso2)."'";
		$result = xtc_db_query($query);
		$iso3 = '';
		while($row = xtc_db_fetch_array($result)) {
			$iso3 = $row['countries_iso_code_3'];
		}
		return $iso3;
	}

	public function getTrackPopUrl($orders_id) {
		$data = $this->getOrderIloxxData($orders_id);
		if(empty($data['parcelnumber'])) {
			return false;
		}
		if($this->_debug == true) {
			$tracking_url = 'http://qa.www.iloxx.de/net/popup/trackpop.aspx?id='.$data['parcelnumber'];
		}
		else {
			$tracking_url = 'https://www.iloxx.de/net/popup/trackpop.aspx?id='.$data['parcelnumber'];
		}
		return $tracking_url;
	}

	public function getLabelsFileName() {
		return DIR_FS_CATALOG.'cache/iloxx-labels-'.$this->_logger->get_secure_token().'.pdf';
	}

	protected function _findSexCode($customers_id, $firstname, $lastname) {
		$query = "SELECT entry_gender FROM address_book WHERE customers_id = :customers_id AND entry_firstname LIKE ':firstname' AND entry_lastname LIKE ':lastname' LIMIT 1";
		$query = strtr($query, array(':customers_id' => (int)$customers_id, ':firstname' => xtc_db_input($firstname), ':lastname' => xtc_db_input($lastname)));
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) == 0) {
			// cannot determine gender, return default value
			return 'NoSexCode';
		}
		$row = xtc_db_fetch_array($result);
		if($row['entry_gender'] == 'm') {
			return 'Male';
		}
		else if($row['entry_gender'] == 'f') {
			return 'Female';
		}
		else {
			return 'NoSexCode';
		}
	}

	public function getDailyTransactionList($date, $type) {
		$date = date('Y-m-d', strtotime($date));
		$params = array(
			'TransactionListDate' => $date,
			'TransactionListType' => $type,
		);
		$result = $this->makeServiceCall('ppvGetDailyTransactionList', $params);
		$pdfstream = $result->TransactionListPDFStream;
		$pdfdata = base64_decode($pdfstream);
		return $pdfdata;
	}

	/*
	 * misc
	 */

	public function setOrderIloxxData($orders_id, $parcelnumber, $service, $weight, $shipdate) {
		if(empty($shipdate)) {
			$shipdate = 'NULL';
		}
		else {
			$shipdate = "'".date('Y-m-d', strtotime($shipdate)) ."'";
		}
		$values = (int)$orders_id .", ";
		$values .= (empty($parcelnumber) ? 'NULL' : $parcelnumber) .', ';
		$values .= "'".$service ."', ";
		$values .= (double)$weight .", ";
		$values .= $shipdate;
		$query = "REPLACE INTO orders_iloxxdata (orders_id, parcelnumber, service, weight, shipdate) VALUES (".$values.")";
		xtc_db_query($query);
	}

	public function getOrderIloxxData($orders_id, $is_cod = false) {
		$query = "SELECT * FROM orders_iloxxdata WHERE orders_id = :orders_id";
		$query = strtr($query, array(':orders_id' => (int)$orders_id));
		$result = xtc_db_query($query);
		$data = array(
			'orders_id' => $orders_id,
			'parcelnumber' => '',
			'service' => $is_cod == true ? $this->default_ship_service_cod : $this->default_ship_service,
			'weight' => 0,
			'shipdate' => ''
		);
		while($row = xtc_db_fetch_array($result)) {
			$data = $row;
		}
		return $data;
	}

	public static function getShipServices() {
		return array(
			'dpdNormalpaketFlex' => 'Normalpaket Flex',
			'dpdNormalpaket' => 'Normalpaket',
			'dpdNormalpaketCOD' => 'Normalpaket Nachnahme',
			'dpdExpress10' => 'Express 10',
			'dpdExpress10COD' => 'Express 10 Nachnahme',
			'dpdExpress12' => 'Express 12',
			'dpdExpress12COD' => 'Express 12 Nachnahme',
			'dpdExpress18' => 'Express 18',
			'dpdExpress18COD' => 'Express 18 Nachnahme',
			'dpdExpressSamstag12' => 'Express Samstag 12',
			'dpdExpressSamstag12COD' => 'Express Samstag 12 Nachnahme',
			'dpdShopRetoure' => 'ShopRetoure',
			'dpdRetourePickup' => 'Retoure Abholung',
		);
	}

	public static function getCODShipServices() {
		return array(
			'dpdNormalpaketCOD' => 'Normalpaket Nachnahme',
			'dpdExpress10COD' => 'Express 10 Nachnahme',
			'dpdExpress12COD' => 'Express 12 Nachnahme',
			'dpdExpress18COD' => 'Express 18 Nachnahme',
			'dpdExpressSamstag12COD' => 'Express Samstag 12 Nachnahme',
		);
	}

	public function verifyTrackingKey($key) {
		$valid = $key == $this->_logger->get_secure_token();
		return $valid;
	}

	public function recordTrackingEvent($orders_id, $tracking_data) {
		$status_comment = "Tracking:\nTrack-ID: ".$tracking_data['iloxxtrackid']."\n".
			"Status: ".$tracking_data['iloxxstatusid']."\n".
			"Datum: ".$tracking_data['iloxxstatusdate']."\n".
			"Preis: ".$tracking_data['iloxxorderprice'];
		$this->_setOrdersStatus($orders_id, $this->ostracking, $status_comment);
	}

	protected function _setOrdersStatus($orders_id, $orders_status_id, $comment) {
		xtc_db_query("UPDATE orders SET orders_status = ".(int)$orders_status_id." WHERE orders_id = ".(int)$orders_id);
		xtc_db_query("INSERT INTO orders_status_history (orders_id, orders_status_id, date_added, customer_notified, comments) ".
			"VALUES (".(int)$orders_id.", ".(int)$orders_status_id.", NOW(), 0, '".xtc_db_input($comment)."')");
	}

}
MainFactory::load_origin_class('GMIloxx');