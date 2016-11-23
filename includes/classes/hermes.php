<?php
/* --------------------------------------------------------------
   hermes.php 2016-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Hermes_ORIGIN {
	const WSDLFILE = 'ProPS.wsdl';
	const WSDLFILE_SANDBOX = 'ProPS-sandbox.wsdl';
	const PRIPS_WSDLFILE = 'PriPS.wsdl';
	const PRIPS_WSDLFILE_SANDBOX = 'PriPS-sandbox.wsdl';
	const API_NAMESPACE = 'http://hermes_api.service.hlg.de';
	const PARTNERTOKEN_LIFETIME = 7200; // 120 minutes
	const USERTOKEN_LIFETIME = 15552000; // 180 days
	const API_VERSION = '1.5';
	const SERVICE_PRIPS = 'PriPS';
	const SERVICE_PROPS = 'ProPS';

	/**
	 * directory containing local copies of shipping labels
	 * cannot be 'const' b/c PHP doesn't allow use of define()'d consts in values for class consts
	 */
	protected static $_DIR_LABELS;

	protected $partnerID;
	protected $partnerPwd;
	protected $partnerToken;
	protected $username;
	protected $password;
	protected $userToken;

	protected $labelpos;
	protected $service; // PriPS/ProPS

	protected $soapClient;
	protected $soap_params;
	protected $wsdlfile;

	protected $_prips_lop; // cache for PriPS List Of Products

	protected $_debug = true;
	protected $_sandboxmode = false;
	protected $_logger;
	protected $_txt;

	protected $parcelServiceId;

	public function __construct()
	{
		$this->_txt = new LanguageTextManager('hermes', $_SESSION['languages_id']);
		$this->_logger = LogControl::get_instance();
		self::$_DIR_LABELS = DIR_FS_CATALOG .'/cache/hermes_labels';
		$this->prepareCacheDir();
		$this->getConfig();
	}

	protected function prepareCacheDir()
	{
		if (!is_dir(self::$_DIR_LABELS))
		{
			mkdir(self::$_DIR_LABELS, 0777) or die('failed to create cache directory for Hermes labels');
		}
		if (!is_file(self::$_DIR_LABELS .'/index.html'))
		{
			file_put_contents(self::$_DIR_LABELS .'/index.html', '<html></html>');
		}
	}


	protected function _log($message, $additional_info = '', $level = 'notice', $level_type = 'USER NOTICE', $error_code = 0)
	{
		$this->_logger->notice($message, 'shipping', 'shipping.hermes', $level, $level_type, $error_code, $additional_info);
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

	protected function getConfig() {
		$cfgquery = xtc_db_query("SELECT configuration_key, configuration_value FROM configuration WHERE configuration_key LIKE 'MODULE_SHIPPING_HERMESPROPS_%'");
		$cfg = array();
		while($row = xtc_db_fetch_array($cfgquery)) {
			$key = $row['configuration_key'];
			$value = $row['configuration_value'];
			$cfg[$key] = $value;
		}
		$this->username = gm_get_conf('HERMES_PROPS_USERNAME');
		$this->password = gm_get_conf('HERMES_PROPS_PASSWORD');
		$this->_sandboxmode = gm_get_conf('HERMES_PROPS_SANDBOXMODE') == true;
		$this->parcelServiceId = (int)gm_get_conf('HERMES_PARCELSERVICE_ID');
		$service = gm_get_conf('HERMES_PROPS_SERVICE');
		if(empty($service) || $service == 'ProPS') {
			$this->service = self::SERVICE_PROPS;
			$this->partnerID = 'EXT000147';
			$this->partnerPwd = '90213787c3365489134b17f911332563';
		}
		else {
			$this->service = self::SERVICE_PRIPS;
			$this->partnerID = 'EXT000310';
			$this->partnerPwd = '251684185fce9fdd134b17f911332563';
		}
		if($this->_sandboxmode) {
			if($this->service == 'ProPS') {
				$this->wsdlfile = dirname(__FILE__).'/'.self::WSDLFILE_SANDBOX;
			}
			else {
				# $this->wsdlfile = dirname(__FILE__).'/'.self::PRIPS_WSDLFILE_SANDBOX;
				$this->wsdlfile = 'https://hermesapisbx.hlg.de/hermes-api-prips-web/services/v15/PriPS?wsdl';
			}
			$this->soap_params = array(
				'trace' => true,
				'exceptions' => true,
				'cache_wsdl' => WSDL_CACHE_NONE,
				'user_agent' => 'Gambio GX2',
			);
		}
		else {
			if($this->service == 'ProPS') {
				$this->wsdlfile = dirname(__FILE__).'/'.self::WSDLFILE;
			}
			else {
				# $this->wsdlfile = dirname(__FILE__).'/'.self::PRIPS_WSDLFILE;
				$this->wsdlfile = 'https://hermesapi.hlg.de/hermes-api-prips-web/services/v15/PriPS?wsdl';
			}
			$this->soap_params = array(
				'trace' => true,
				'exceptions' => true,
				'cache_wsdl' => WSDL_CACHE_MEMORY,
				'user_agent' => 'Gambio GX2',
				'connection_timeout' => 5,
			);
		}
		#$this->soap_params['encoding'] = 'utf8';
		$this->soap_params['soap_version'] = SOAP_1_1;
		$this->soap_params['features'] = SOAP_USE_XSI_ARRAY_TYPE|SOAP_SINGLE_ELEMENT_ARRAYS;
	}

	public function setService($service) {
		gm_set_conf('HERMES_PROPS_SERVICE', $service);
		$this->service = $service;
	}

	public function getService() {
		return $this->service;
	}

	public function setUsername($username) {
		gm_set_conf('HERMES_PROPS_USERNAME', $username);
		$this->username = $username;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setPassword($password) {
		gm_set_conf('HERMES_PROPS_PASSWORD', $password);
		$this->password = $password;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setSandboxmode($sbmode) {
		if($sbmode == true) {
			gm_set_conf('HERMES_PROPS_SANDBOXMODE', 1);
		}
		else {
			gm_set_conf('HERMES_PROPS_SANDBOXMODE', 0);
		}
		$this->_sandboxmode = $sbmode == true;
	}

	public function getSandboxmode() {
		return $this->_sandboxmode;
	}

	public function setOrdersStatusAfterSave($os_id) {
		gm_set_conf('HERMES_PROPS_OSAFTERSAVE', $os_id);
	}

	public function getOrdersStatusAfterSave() {
		return gm_get_conf('HERMES_PROPS_OSAFTERSAVE');
	}

	public function setOrdersStatusAfterLabel($os_id) {
		gm_set_conf('HERMES_PROPS_OSAFTERLABEL', $os_id);
	}

	public function getOrdersStatusAfterLabel() {
		return gm_get_conf('HERMES_PROPS_OSAFTERLABEL');
	}

	public function getParcelServiceId()
	{
		return (int)gm_get_conf('HERMES_PARCELSERVICE_ID');
	}

	public function setParcelServiceId($parcelServiceId)
	{
		gm_set_conf('HERMES_PARCELSERVICE_ID', (int)$parcelServiceId);
	}

	public function getPripsShipper() {
		$shipper = array(
			'shipperType' => 'COMMERCIAL',
			'firstname' => 'Vorname',
			'lastname' => 'Nachname',
			'addressAdd' => 'Adresszusatz',
			'street' => 'Straße',
			'houseNumber' => '42',
			'postcode' => '12345',
			'city' => 'Stadt',
			'district' => 'Stadtteil',
			'countryCode' => 'DEU',
			'telephonePrefix' => '+49123',
			'telephoneNumber' => '123123123',
			'email' => 'max@example.com',
			'referenceAuctionNumber' => '',
		);

		$cfg_shipper_serialized = gm_get_conf('HERMES_PRIPS_SHIPPER', 'ASSOC', true);
		if(empty($cfg_shipper_serialized) === false) {
			$cfg_shipper = unserialize($cfg_shipper_serialized);
			$shipper = array_merge($shipper, $cfg_shipper);
		}

		return $shipper;
	}

	public function setPripsShipper(array $newshipper) {
		$shipper = $this->getPripsShipper();
		foreach($shipper as $key => $value)
		{
			if(array_key_exists($key, $newshipper))
			{
				$shipper[$key] = $newshipper[$key];
			}
		}
		gm_set_conf('HERMES_PRIPS_SHIPPER', serialize($shipper));
	}

	public function getSoapClient($addUserToken = true) {
		if(!($this->soapClient instanceof SoapClient)) {
			try {
				$this->soapClient = new SoapClient($this->wsdlfile, $this->soap_params);
				$headers = array();
				$ns_wsse = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
				$security_header = '<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
					<wsse:UsernameToken wsu:Id="UsernameToken-102">
					<wsse:Username>'.$this->partnerID.'</wsse:Username>
					<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->partnerPwd.'</wsse:Password>
					</wsse:UsernameToken></wsse:Security>';
				$header_security = new SoapVar($security_header, XSD_ANYXML);
				$headers[] = new SoapHeader($ns_wsse, 'Security', $header_security);
				$this->soapClient->__setSoapHeaders($headers);
				if($this->service == 'ProPS' && $addUserToken == true) {
					$loginarguments = array(
						'login' => array(
							'benutzername' => $this->username,
							'kennwort' => $this->password,
						)
					);
					$user_login_response = $this->soapClient->propsUserLogin($loginarguments);

					$this->userToken = trim((string)$user_login_response->propsUserLoginReturn);
					$headers[] = new SoapHeader(self::API_NAMESPACE, 'UserToken', $this->userToken);
					$this->soapClient = new SoapClient($this->wsdlfile, $this->soap_params);
					$this->soapClient->__setSoapHeaders($headers);
				}
			}
			catch(SoapFault $sf) {
				if($this->_debug) {
					$soap_responseheaders = $this->soapClient->__getLastResponseHeaders();
					$soap_response = $this->soapClient->__getLastResponse();
					$soap_requestheaders = $this->soapClient->__getLastRequestHeaders();
					$soap_request = $this->soapClient->__getLastRequest();
					$log_message = "SoapFault in getSoapClient";
					$log = "ResponseHeaders:\r\n\r\n";
					$log .= $soap_responseheaders;
					$log .= "\r\n\r\nResponse:\r\n\r\n";
					$log .= formatXmlString($soap_response);
					$log .= "\r\n\r\nRequestHeaders:\r\n\r\n";
					$log .= $soap_requestheaders;
					$log .= "\r\n\r\nRequest:\r\n\r\n";
					$log .= formatXmlString($soap_request);
					$log .= "\r\n\r\nSoapFault:\r\n\r\n";
					$log .= print_r($sf, true);
					$this->_log($log_message, $log, 'warning', 'USER WARNING');
				}
				$errorCode = $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorCode;
				$errorMessage = $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage;
				$this->_log($errorMessage, '', 'error', 'USER ERROR', $errorCode);
				return false;
			}
		}
		return $this->soapClient;
	}

	public function getPackageClasses() {
		$classes = array(
			'XS' => array(
				'size' => 0,
				'name' => 'XS',
				'desc' => 'a+b = max. 30 cm',
				'bulkoption' => true,
			),
			'S' => array(
				'size' => 1,
				'name' => 'S',
				'desc' => 'a+b = max. 50 cm',
				'bulkoption' => true,
			),
			'M' => array(
				'size' => 2,
				'name' => 'M',
				'desc' => 'a+b = max. 80 cm',
				'bulkoption' => true,
			),
			'L' => array(
				'size' => 3,
				'name' => 'L',
				'desc' => 'a+b = max. 120 cm',
				'bulkoption' => true,
			),
			'XL' => array(
				'size' => 4,
				'name' => 'XL',
				'desc' => 'a+b = max. 150 cm',
				'bulkoption' => false,
			),
		);
		if($this->service == self::SERVICE_PRIPS)
		{
			$prips_classes = array(
					'XS' => array(
							'name' => 'Päckchen',
							'desc' => 'a+b = max. 37 cm',
						),
				);
			unset($classes['XS']);
			$classes = array_merge($prips_classes, $classes);
		}
		return $classes;
	}

	public function getMinimumPackageClass() {
		return 'XS';
	}

	public static function getCountries() {
		return array('BEL', 'DNK', 'DEU', 'EST', 'FIN', 'FRA', 'IRL', 'ITA', 'LVA', 'LIE', 'LTU', 'LUX', 'MCO',
			'NLD', 'POL', 'AUT', 'PRT', 'SWE', 'CHE', 'SVK', 'SVN', 'ESP', 'CZE', 'HUN', 'GBR');
	}

	public function getProductOptions($products_id) {
		$hermes_query = xtc_db_query("SELECT * FROM products_hermesoptions WHERE products_id = ". (int)$products_id);
		if(xtc_db_num_rows($hermes_query) == 1) {
			$hermes_options = xtc_db_fetch_array($hermes_query);
		}
		else {
			$hermes_options = false;
		}
		return $hermes_options;
	}

	public function setProductsOptions($data) {
		if(!isset($data['products_id'])) {
			die('poot.');
		}
		$hermes_data = $this->getProductOptions($data['products_id']);
		if($hermes_data === false) {
			xtc_db_perform('products_hermesoptions', $data);
		}
		else {
			xtc_db_query("UPDATE products_hermesoptions SET min_pclass = '".$data['min_pclass']."' WHERE products_id = ". (int)$hermes_data['products_id']);
		}
	}

	/* ----------------------------------------------------------- */

	public function checkAvailability() {
		try {
			$time_start = microtime(true);
			$sc = $this->getSoapClient();
			if($sc === false) {
				return false;
			}
			$t_socket_timout = ini_get('default_socket_timeout');
			ini_set('default_socket_timeout', 5);
			if($this->service == 'ProPS') {
				$result = $sc->propsCheckAvailability();
				$result_empty = empty($result->propsCheckAvailabilityReturn) == true;
			}
			else {
				$result = $sc->pripsCheckAvailability();
				$result_empty = empty($result->pripsCheckAvailabilityReturn) == true;
			}
			ini_set('default_socket_timeout', $t_socket_timout);
			if($result_empty !== true) {
				return true;
			}
			else {
				return false;
			}
		}
		catch(SoapFault $sf) {
			if($this->_debug) {
				$this->_log("CheckAvailability: SOAP Fault", print_r($sf, true), 'warning', 'USER WARNING');
			}
			else {
				$this->_log("CheckAvailability: SOAP Fault", $sf->getMessage(), 'warning', 'USER WARNING');
			}
			return false;
		}
	}

	public function getInfo() {
		try {
			$sc = $this->getSoapClient();
			$result = $sc->propsListOfProductsATG();
			if(isset($result->propsListOfProductsATGReturn)) {
				return $result->propsListOfProductsATGReturn;
			}
			else {
				return false;
			}
		}
		catch(SoapFault $sf) {
			return false;
		}
	}

	public function orderSave(HermesOrder $order) {
		if($order->isTemporary()) {
			$order->saveToDb();
		}
		$propsorder = $order->getPropsOrder();
		$sc = $this->getSoapClient();
		try {
			$response = $sc->propsOrderSave(array('propsOrder' => $propsorder));
			$new_orderno = $response->propsOrderSaveReturn;
			if($order->isTemporary()) {
				$order->deleteFromDb();
			}
			$order->orderno = $new_orderno;
			$order->state = 'sent';
			$order->saveToDb();
			if($this->_debug) { print_r($response); }
		}
		catch(Exception $e) {
			$saveresult = array(
				'code' => $e->detail->ServiceException->exceptionItems->errorCode,
				'message' => $e->detail->ServiceException->exceptionItems->errorMessage,
			);
			return $saveresult;
		}
		return true;
	}

	public function storeTrackingNumber($orders_id, $trackingNumber)
	{
		if((int)$this->parcelServiceId > 0)
		{
			$parcelServiceReader = MainFactory::create('ParcelServiceReader');
			$parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
			$parcelTrackingCodeWriter->insertTrackingCode($orders_id, $trackingNumber, $this->parcelServiceId, $parcelServiceReader);
		}
	}

	public function orderCancel(HermesOrder $order) {
		if($order->state != 'not_sent') {
			$sc = $this->getSoapClient();
			try {
				$response = $sc->propsOrderDelete(array('orderNo' => $order->orderno));
				$deleted = $response->propsOrderDeleteReturn;
			}
			catch(SoapFault $e) {
				$cancelresult = array(
					'code' => $e->detail->ServiceException->exceptionItems->ExceptionItem->errorCode,
					'message' => $e->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage,
				);
				return $cancelresult;
			}
		}
		else {
			// locally saved order, OK to delete
			$deleted = true;
		}
		if($deleted) {
			$order->deleteFromDb();
		}
		return true;
	}

	public function makeLabelFileName($order, $printpos) {
		if(!($order instanceof HermesOrder) && is_numeric($order)) {
			$order = new HermesOrder($order);
		}
		$labelfile = self::$_DIR_LABELS .'/'. $order->orderno .'_'. $printpos .'.pdf';
		return $labelfile;
	}

	public function makeLabelsFileName() {
		$labelfile = self::$_DIR_LABELS .'/batchlabels.pdf';
		return $labelfile;
	}


	public function orderPrintLabel(HermesOrder $order, $printpos = 1, $forcefetch = false) {
		//$labelfile = self::$_DIR_LABELS .'/'. $order->orderno .'_'. $printpos .'.pdf';
		$labelfile = $this->makeLabelFileName($order, $printpos);
		if(!is_file($labelfile) || $forcefetch == true) {
			$sc = $this->getSoapClient();
			try {
				/*
				$response = $sc->propsOrderPrintLabelPdf(array('orderNo' => $order->orderno, 'printPosition' => $this->labelpos));
				$pdfreturn = $response->propsOrderPrintLabelPdfResponse->propsOrderPrintLabelPdfReturn;
				$pdfb64 = $pdfreturn->pdfData;
				*/
				$pdfdata = $this->getLabelPdf($order->orderno, $printpos);
				if(!empty($pdfdata)) {
					file_put_contents($labelfile, $pdfdata);
					$orderdata = $this->getOrder($order->orderno);
					if($orderdata !== false) {
						$order->shipping_id = $orderdata->shippingId;
						$this->storeTrackingNumber($order->orders_id, $orderdata->shippingId);
					}
					$order->state = 'printed';
					$order->saveToDb();
				}
			}
			catch(Exception $e) {
				die($e);
			}
		}
	}

	public function getLabelPdf($orderno, $printpos = 1) {
		$sc = $this->getSoapClient();
		try {
			$oplp = $sc->propsOrderPrintLabelPdf(array('orderNo' => $orderno, 'printPosition' => $printpos));
		}
		catch(SoapFault $sf) {
			echo "soapFault!\n";
			var_dump($sf);
			echo "\n\n";
		}

		return $oplp->propsOrderPrintLabelPdfReturn->pdfData;
	}

	public function getLabelsPdf($ordernumbers) {
		$sc = $this->getSoapClient();
		try {
			$oplp = $sc->propsOrdersPrintLabelsPdf(array('requestedOrderNumbers' => array('orderNumbers' => $ordernumbers)));
			$pdfdata = $oplp->propsOrdersPrintLabelsPdfReturn->pdfData;
			$orderRes = $oplp->propsOrdersPrintLabelsPdfReturn->orderRes;
			foreach($ordernumbers as $order_no)
			{
				$orderdata = $this->getOrder($order_no);
				if($orderdata !== false)
				{
					$order = new HermesOrder($order_no);
					$this->storeTrackingNumber($order->orders_id, $orderdata->shippingId);
					$order->shipping_id = $orderdata->shippingId;
					$order->state = 'printed';
					$order->saveToDb();
				}
			}
			return array(
				'pdfdata' => $pdfdata,
				'orderres' => $orderRes,
			);
		}
		catch(SoapFault $sf) {
			echo "soapFault!\n";
			var_dump($sf);
			echo "\n\n";
		}
		return false;
	}

	public function getLabelUrl($orderno) {
		if(!defined('DIR_WS_ADMIN')) {
			return false;
		}
		if($orderno instanceof HermesOrder) {
			$orderno = $orderno->orderno;
		}
		$url = HTTP_SERVER . DIR_WS_ADMIN .'/images/hermes_labels';
		$filename = $orderno.'.pdf';
		if(is_file(self::$_DIR_LABELS .'/'.$filename)) {
			return $url .'/'. $filename;
		}
		else {
			return false;
		}
	}

	public function getLabelsUrl() {
		$url = HTTP_SERVER . DIR_WS_ADMIN .'/images/hermes_labels/batchlabels.pdf';
		return $url;
	}

	public function getPropsOrders($search_criteria = array()) {
		$search_criteria_default = array(
			'orderNo' => null,
			'identNo' => null,
			'from' => null,
			'to' => null,
			'lastname' => null,
			'city' => null,
			'postcode' => null,
			'countryCode' => null,
			'clientReferenceNumber' => null,
			'ebayNumber' => null,
			'status' => null,
		);
		$search_criteria = array_merge($search_criteria_default, $search_criteria);
		$sc = $this->getSoapClient();
		try {
			$gpo = $sc->propsGetPropsOrders(array('searchCriteria' => $search_criteria));
			$orders = $gpo->propsGetPropsOrdersReturn->orders->PropsOrderShort; // array of stdClass
			if($orders === null) {
				$orders = array();
			}
		}
		catch(SoapFault $sf) {
			//var_dump($sf);
			//die('SOAP FAULT');
			return array(
				'code' => $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorCode,
				'message' => $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage
			);
		}
		return $orders;
	}

	public function getShipmentStatus($shipping_id) {
		$sc = $this->getSoapClient();
		try {
			// get ProPS status
			$shipstatus = $sc->propsReadShipmentStatus(array('shippingId' => $shipping_id));
			$status = array(
				'text' => $shipstatus->propsReadShipmentStatusReturn->statusText,
				//'text' => '<pre>'.print_r($shipstatus, true).'</pre>',
				'datetime' => $shipstatus->propsReadShipmentStatusReturn->statusDateTime,
			);
		}
		catch(Exception $e) {
			$status = array(
				'text' => 'Status kann nicht ermittelt werden',
				'datetime' => date('Y-m-d H:i:s'),
			);
		}
		return $status;
	}

	public function getOrder($orderno = false, $shipping_id = false) {
		if($orderno === false && $shipping_id === false) {
			die('invalid call of getOrder');
		}
		$sc = $this->getSoapClient();
		try {
			$orderreturn = $sc->propsGetPropsOrder(array('orderNo' => $orderno, 'shippingId' => $shipping_id));
		}
		catch(SoapFault $sf) {
			return false;
		}
		return $orderreturn->propsGetPropsOrderReturn;
	}

	public function addPropsCollectionRequest($datetime, $packets) {
		$collection_order = array(
			'collectionDate' => $datetime,
		);
		foreach($packets as $pclass => $number) {
			$collection_order['numberOfParcelsClass_'.$pclass] = $number;
		}
		$collection_order['numberOfParcelsClass_XLwithBulkGoods'] = 0;
		$sc = $this->getSoapClient();
		try {
			$collreqreturn = $sc->propsCollectionRequest(array('collectionOrder' => $collection_order));
		}
		catch(SoapFault $sf) {
			/*
			header('Content-Type: text/plain');
			var_dump($sf);
			die('SOAP FAULT');
			*/
			$this->_log('SoapFault in addPropsCollectionRequest:'.PHP_EOL.print_r($sf, true));
			return array(
				'code' => $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorCode,
				'message' => $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage
			);
		}
		return $collreqreturn->propsCollectionRequestReturn;
	}

	public function collectionCancel($datetime) {
		$sc = $this->getSoapClient();
		try {
			$cancelreturn = $sc->propsCollectionCancel(array('collectionDate' => $datetime));
		}
		catch(SoapFault $sf) {
			return array(
				'code' => $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorCode,
				'message' => $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage
			);
		}
		return true;
	}

	public function getCollectionOrders() {
		$sc = $this->getSoapClient();
		try {
			$t_date_from = date('c');
			$t_date_to = date('c', strtotime('+60 days'));
			$cordersreturn = $sc->propsGetCollectionOrders(array('collectionDateFrom' => $t_date_from, 'collectionDateTo' => $t_date_to, 'onlyMoreThan2ccm' => false));
			$corders = $cordersreturn->propsGetCollectionOrdersReturn->orders->PropsCollectionOrderLong;
			return $corders;
		}
		catch(SoapFault $sf) {
			$this->_log('SoapFault in getCollectionOrders:'.PHP_EOL.print_r($sf, true));
			if(isset($sf->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage)) {
				return $sf->detail->ServiceException->exceptionItems->ExceptionItem->errorMessage;
			}
			else {
				return false;
			}
		}
	}

	/*****
	 * PriPS
	 *****/

	public function getLabelAcceptanceLiabilityLimit() {
		// PriPS List of Products contains required data
		$prips_lop = $this->getPripsListOfProductsExDeu();
		$label = (string)$prips_lop->labelAcceptanceLiabilityLimit;
		return $label;
	}

	public function getLabelAcceptanceTermsAndConditions() {
		// PriPS List of Products contains required data
		$prips_lop = $this->getPripsListOfProductsExDeu();
		$label = (string)$prips_lop->labelAcceptanceTermsAndConditions;
		return $label;
	}

	public function getUrlTermsAndConditions() {
		$prips_lop = $this->getPripsListOfProductsExDeu();
		$label = (string)$prips_lop->urlTermsAndConditions;
		return $label;
	}

	public function getPripsListOfProductsExDeu()
	{
		$t_cachedata_name = 'pripslistofproductsexdeu';
		$t_max_age = 43200; # 43200s = 24h * 60min/h * 60s/min
		if($this->_prips_lop == null)
		{
			$t_cached_data = $this->_getCachedData($t_cachedata_name, $t_max_age);
			if($t_cached_data === false)
			{
				$sc = $this->getSoapClient();
				$response = $sc->pripsListOfProductsExDeu();
				if($response instanceof stdClass) {
					$this->_prips_lop = $response->pripsListOfProductsExDeuReturn;
					$this->_setCachedData($t_cachedata_name, $response->pripsListOfProductsExDeuReturn);
				}
			}
			else {
				$this->_prips_lop = $t_cached_data;
			}
		}
		return $this->_prips_lop;
	}

	public function pripsMakeLabel($labeldata)
	{
		$t_orderno = false;
		$sc = $this->getSoapClient();
		try
		{
			$order_request = array('orderRequest' => $labeldata);
			$response = $sc->pripsOrderPrintLabelPdf($order_request);
			# $this->_log(print_r($response, true));
			$t_orderno = (string)$response->pripsOrderPrintLabelPdfReturn->orderNo;
			$t_pdf_data = (string)$response->pripsOrderPrintLabelPdfReturn->pdfData;
			$t_shipping_id = (string)$response->pripsOrderPrintLabelPdfReturn->shippingId->string[0];
			$labelfile = self::$_DIR_LABELS .'/'. $t_orderno .'.pdf';
			$this->_log('PriPS label created, orderNo '.$t_orderno.', shippingId '.$t_shipping_id.', saving label to '.$labelfile);
			file_put_contents($labelfile, $t_pdf_data);
		}
		catch(SoapFault $sf)
		{
			$msg = "SOAP fault: ".$sf->getMessage();
			$msg .= "\nRequest:\n".formatXmlString($sc->__getLastRequest());
			$msg .= "\nResponse:\n".formatXmlString($sc->__getLastResponse());
			$this->_log('PriPS ERROR creating label:'.PHP_EOL.$msg, 'SF detail:'.PHP_EOL.print_r($sf->detail, true), 'warning', 'USER WARNING');
			$error_msg = '';

			$t_exception_items = $sf->detail->ServiceException->exceptionItems;
			if(count($t_exception_items) == 0)
			{
				$error_msg = $sf->getMessage();
			}
			else
			{
				if(count($t_exception_items) == 1)
				{
					$t_exception_items = array($t_exception_items);
				}
				foreach($t_exception_items as $ex_item)
				{
					if(empty($error_msg) !== true)
					{
						$error_msg .= '<br>';
					}
					$error_msg .= (string)$ex_item->errorCode .' - '. (string)$ex_item->errorMessage;
				}
			}
			throw new Exception($error_msg);
		}

		return $t_orderno;
	}

	/* ===========================================================================================================================
	=========================================================================================================================== */

	protected function _localEncodingIsLatin1() {
		$is_latin1 = strpos(strtolower($_SESSION['language_charset']), 'iso-8859-1') !== false;
		return $is_latin1;
	}

	/** transcode incoming string from UTF-8 to whatever the shop system is currenty using */
	public function transcodeInbound($string) {
		if($this->_localEncodingIsLatin1()) {
			$output = utf8_decode($string);
		}
		else {
			$output = $string;
		}
		return $output;
	}

	/** transcode incoming string from whatever the shop system is currenty using to UTF-8 */
	public function transcodeOutbound($string) {
		if($this->_localEncodingIsLatin1()) {
			$output = utf8_encode($string);
		}
		else {
			$output = $string;
		}
		return $output;
	}

	/* ===========================================================================================================================
	=========================================================================================================================== */

	protected function _getCachedDataFile($namebase) {
		$secure_token = LogControl::get_secure_token();
		$namebase = basename($namebase);
		$cachefile = DIR_FS_CATALOG.'cache/'.strtolower(__CLASS__).'-'.$namebase.'-'.$secure_token.'.pdc';
		return $cachefile;
	}

	protected function _getCachedData($title, $max_age = null) {
		$file = $this->_getCachedDataFile($title);
		if(file_exists($file) === false) {
			return false;
		}
		$too_old = ($max_age !== null) && ((time() - filemtime($file)) > $max_age);
		if($too_old) {
			return false;
		}
		$data = unserialize(file_get_contents($file));
		return $data;
	}

	protected function _setCachedData($title, $data) {
		$file = $this->_getCachedDataFile($title);
		$file_exists = file_exists($file);
		if(($file_exists && is_writable($file)) || is_writable(dirname($file))) {
			file_put_contents($file, serialize($data));
			$success = true;
		}
		else {
			$success = false;
		}
		return $success;
	}
}


/*********************************/

class HermesOrder {
	public $orderno;
	public $order_type;
	public $orders_id;
	public $receiver_firstname;
	public $receiver_lastname;
	public $receiver_street;
	public $receiver_housenumber;
	public $receiver_addressadd;
	public $receiver_postcode;
	public $receiver_city;
	public $receiver_district;
	public $receiver_countrycode;
	public $receiver_email;
	public $receiver_telephonenumber;
	public $receiver_telephoneprefix;
	public $clientreferencenumber;
	public $parcelclass;
	protected $_parcelclasses;
	public $amountcashondeliveryeurocent;
	protected $state;
	public $shipping_id;
	public $paket_shop_id;
	public $hand_over_mode;
	public $collection_desired_date;

	protected $_txt;
	const TEMP_PREFIX = 'tmpid_';

	public function __construct($orderno = false) {
		$this->_txt = new LanguageTextManager('hermes', $_SESSION['languages_id']);
		$this->orderno = uniqid(self::TEMP_PREFIX);
		$this->order_type = 'props';
		$this->receiver_firstname = 'test';
		$this->receiver_housenumber = '';
		$this->receiver_countrycode = 'DEU';
		$this->parcelclass = '';
		$this->setState('not_sent');
		if($orderno !== false) {
			$this->getFromDb($orderno);
		}
	}

	public function get_text($name) {
		$replacement = $this->_txt->get_text($name);
		return $replacement;
	}

	public function __set($name, $value) {
		$methodname = 'set'.ucfirst($name);
		if(method_exists($this, $methodname)) {
			$this->$methodname($value);
		}
		else {
			if(property_exists(get_class(), $name)) {
				$this->$name = $value;
			}
		}
	}

	public function __get($name) {
		$methodname = 'get'.ucfirst($name);
		if(method_exists($this, $methodname)) {
			return $this->$methodname();
		}
		else {
			return null;
		}
	}

	public static function getKeys() {
		return array('orderno', 'order_type', 'orders_id', 'receiver_firstname', 'receiver_lastname', 'receiver_street', 'receiver_housenumber',
			'receiver_addressadd', 'receiver_postcode', 'receiver_city', 'receiver_district', 'receiver_countrycode', 'receiver_email',
			'receiver_telephonenumber', 'receiver_telephoneprefix', 'clientreferencenumber', 'parcelclass', 'amountcashondeliveryeurocent',
			'state', 'shipping_id', 'paket_shop_id', 'hand_over_mode', 'collection_desired_date');
	}

	public static function getValidStates() {
		$valid_states = array('not_sent', 'sent', 'printed');
		return $valid_states;
	}

	public function isTemporary() {
		return (strpos($this->orderno, self::TEMP_PREFIX) !== false);
	}

	public function getState() {
		return $this->state;
	}

	public function setState($new_state) {
		$valid_states = self::getValidStates();
		if(in_array($new_state, $valid_states)) {
			$this->state = $new_state;
		}
		else {
			throw new Exception('Invalid state for HermesOrder');
		}
	}

	public function setAmountcashondeliveryeurocent($amount) {
		// $amount is in Euros, convert to cents
		$this->amountcashondeliveryeurocent = round($amount * 100);
	}

	public function getParcelclasses($class) {
		if(isset($this->_parcelclasses[$class])) {
			return $this->_parcelclasses[$class];
		}
		else {
			return ''; // false;
		}
	}

	public function setParcelclasses($class, $number) {
		$this->_parcelclasses[$class] = $number;
	}

	public static function getStateName($state, $lang = 'de') {
		$statenames = array(
			'de' => array(
				'not_sent' => 'nicht übertragen',
				'sent' => 'übertragen',
				'printed' => 'Paketschein erzeugt',
			),
			'en' => array(
				'not_sent' => 'not transmitted',
				'sent' => 'transmitted',
				'printed' => 'label created',
			)
		);
		return $statenames[$lang][$state];
	}

	public function isMutable() {
		$t_is_mutable = $this->state != 'printed';
		return $t_is_mutable;
	}

	public function fillFromOrder($orders_id) {
		$query = "SELECT
		            `customers_email_address`,
		            `customers_telephone`,
		            `delivery_name`,
		            `delivery_firstname`,
		            `delivery_lastname`,
		            `delivery_additional_info`,
		            `delivery_company`,
		            `delivery_street_address`,
		            `delivery_house_number`,
		            `delivery_suburb`,
		            `delivery_city`,
		            `delivery_postcode`,
		            `delivery_state`,
		            `delivery_country`,
		            `delivery_country_iso_code_2`, c.countries_iso_code_3,
			        payment_method, ot.value
			      FROM
			        `orders`, countries c, orders_total ot
			      WHERE
			        orders.delivery_country_iso_code_2 = c.countries_iso_code_2 AND
			        orders.orders_id = ". (int)$orders_id ." AND
			        ot.orders_id = orders.orders_id AND
			        ot.class = 'ot_total'";
		$result = xtc_db_query($query);
		if(xtc_db_num_rows($result) > 0) {
			$row = xtc_db_fetch_array($result);
			$this->orders_id                = (int)$orders_id;
			$this->clientreferencenumber    = $orders_id;
			$this->receiver_firstname       = $row['delivery_firstname'];
			$this->receiver_lastname        = $row['delivery_lastname'];
			$this->receiver_addressadd      = $row['delivery_company'] . (!empty($row['delivery_additional_info']) ? ', ' . $row['delivery_additional_info'] : '');
			$this->receiver_street          = $row['delivery_street_address'];
			$this->receiver_housenumber     = $row['delivery_house_number'];
			$this->receiver_district        = $row['delivery_suburb'];
			$this->receiver_postcode        = $row['delivery_postcode'];
			$this->receiver_city            = $row['delivery_city'];
			$this->receiver_countrycode     = $row['countries_iso_code_3'];
			$this->receiver_email           = $row['customers_email_address'];
			$this->receiver_telephonenumber = $row['customers_telephone'];
			if($row['payment_method'] == 'cod') {
				$this->amountcashondeliveryeurocent = floor(round($row['value'] * 100));
			}

			$pclasses = array('XS', 'S', 'M', 'L', 'XL'); // , 'XXL'
			$fpclasses = array_flip($pclasses);
			$min_pclass = 'XS';
			$pclass_query = xtc_db_query("SELECT min_pclass FROM products_hermesoptions ph, orders_products op
				WHERE op.products_id = ph.products_id AND op.orders_id = ".$orders_id);
			while($pcrow = xtc_db_fetch_array($pclass_query)) {
				if($fpclasses[$pcrow['min_pclass']] > $fpclasses[$min_pclass]) {
					$min_pclass = $pcrow['min_pclass'];
				}
			}
			$this->parcelclass = $min_pclass;
			return true;
		}
		else {
			return false;
		}
	}

	public function fillFromArray(array $input) {
		foreach(self::getKeys() as $key) {
			if(isset($input[$key])) {
				$methodname = 'set'.ucfirst($key);
				if(method_exists($this, $methodname)) {
					$this->$methodname($input[$key]);
				}
				else {
					$this->$key = $input[$key];
				}
			}
		}
		if(isset($input['parcelclasses']) && is_array($input['parcelclasses'])) {
			$this->_parcelclasses = $input['parcelclasses'];
		}
	}

	public function getPropsOrder() {
		$propsorder = array(
			'orderNo' => $this->orderno,
			/*
			'receiver' => array(
				'firstname' => $this->receiver_firstname,
				'lastname' => $this->receiver_lastname,
				'street' => $this->receiver_street,
				'houseNumber' => $this->receiver_housenumber,
				'addressAdd' => $this->receiver_addressadd,
				'postcode' => $this->receiver_postcode,
				'city' => $this->receiver_city,
				'district' => $this->receiver_district,
				'countryCode' => $this->receiver_countrycode,
				'email' => $this->receiver_email,
				'telephoneNumber' => $this->receiver_telephonenumber,
			),*/
			'receiver' => $this->getReceiver(),
			'clientReferenceNumber' => $this->clientreferencenumber,
			'parcelClass' => $this->parcelclass,
			'withBulkGoods' => false,
		);

		if($this->amountcashondeliveryeurocent > 0) {
			$propsorder['amountCashOnDeliveryEurocent'] = $this->amountcashondeliveryeurocent;
			$propsorder['includeCashOnDelivery'] = true;
		}
		else {
			$propsorder['includeCashOnDelivery'] = false;
		}

		if($this->state == 'not_sent') {
			// orderNo in db is only temporary, not an official Hermes orderNo
			$propsorder['orderNo'] = '';
		}

		return $propsorder;
	}

	public function getReceiver() {
		$receiver = array(
				'firstname' => $this->receiver_firstname,
				'lastname' => $this->receiver_lastname,
				'street' => $this->receiver_street,
				'houseNumber' => $this->receiver_housenumber,
				'addressAdd' => $this->receiver_addressadd,
				'postcode' => $this->receiver_postcode,
				'city' => $this->receiver_city,
				'district' => $this->receiver_district,
				'countryCode' => $this->receiver_countrycode,
				'email' => $this->receiver_email,
				'telephoneNumber' => $this->receiver_telephonenumber,
				'paketShopId' => $this->paket_shop_id,
			);
		return $receiver;
	}

	protected function getFromDb($orderno) {
		$hermes = MainFactory::create('hermes');
		$query = xtc_db_query("SELECT * FROM orders_hermes WHERE orderno = '". xtc_db_input($orderno) ."'");
		if(xtc_db_num_rows($query) == 1) {
			$row = xtc_db_fetch_array($query);
			foreach($row as $key => $value) {
				$this->$key = $value;
			}
			if(!in_array($this->parcelclass, array_keys($hermes->getPackageClasses()))) {
				// PriPS data
				$this->_parcelclasses = unserialize($this->parcelclass);
				$this->parcelclass = '';
			}
		}
		else {
			throw new Exception("Order not found: ". $orderno);
		}
	}

	public function saveToDb() {
		$dbdata = array();
		foreach(self::getKeys() as $key) {
			$dbdata[$key] = $this->$key;
		}
		if(!empty($this->_parcelclasses)) {
			$dbdata['parcelclass'] = serialize($this->_parcelclasses);
		}
		$query = "REPLACE INTO orders_hermes SET ";
		$queryparts = array();
		foreach($dbdata as $col => $value) {
			$queryparts[] = "`".$col."` = '". ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $value) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) ."'";
		}
		$query .= implode(',', $queryparts);
		//die($query);
		return xtc_db_query($query);
	}

	public function deleteFromDb() {
		xtc_db_query("DELETE FROM orders_hermes WHERE orderno = '". xtc_db_input($this->orderno) ."'");
		$this->orderno = uniqid();
	}

	public static function getOrders($orders_id) {
		$query = xtc_db_query("SELECT orderno FROM orders_hermes WHERE orders_id = ". (int)$orders_id ." ORDER BY orderno DESC");
		$orders = array();
		while($row = xtc_db_fetch_array($query)) {
			$orders[] = new HermesOrder($row['orderno']);
		}
		return $orders;
	}
}



/* ======================================================== */
/* ================ FOR DEBUGGING ONLY ==================== */
/* ======================================================== */

if(function_exists('formatXmlString') === false) {
	function formatXmlString($input) {
		$dom = new DOMDocument();
		$dom->recover = true;
		$dom->loadXML($input);
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$output = $dom->saveXML();
		return $output;
	}
}
MainFactory::load_origin_class('Hermes');