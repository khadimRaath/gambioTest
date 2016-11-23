<?php
/* --------------------------------------------------------------
	GMIntraship.php 2016-06-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class GMIntraship_ORIGIN {
	protected $_config;
	protected $_text;
	protected $_logger;

	public $module_version = '2015-07-13';

	const CONFIG_PREFIX = 'INTRASHIP';
	const NUM_ZONES = 4;
	const DEVID = 'capuno';
	const DEVPWD = '';
	const APPID = 'gambio_1';
	const APPToken = '7692819c896fe2ea5fbd861cead0a08c503b69e3d56b77380a42b02aa9';

	public function __construct() {
		$this->_logger = new FileLog('intraship', true);
		$this->_config = array(
			'active' => 0,
			'debug' => 0,
			'send_email' => 0,
			'send_announcement' => 1, // "Paketankündigung" via ShipmentOrder->Shipment->Receiver->Communication->email
			'ekp' => '',
			'user' => '',
			'password' => '',
			'shipper_name' => '',
			'shipper_street' => '',
			'shipper_house' => '',
			'shipper_postcode' => '',
			'shipper_city' => '',
			'shipper_contact' => '',
			'shipper_email' => '',
			'shipper_phone' => '',
			'cod_account_holder' => '',
			'cod_account_number' => '',
			'cod_bank_number' => '',
			'cod_bank_name' => '',
			'cod_iban' => '',
			'cod_bic' => '',
			'status_id_sent' => 0,
			'status_id_storno' => 0,
			'zone_1_countries' => 'DE',
			'zone_1_product' => 'EPN',
			'zone_1_partner_id' => '01',
			'zone_2_countries' => 'AT',
			'zone_2_product' => 'BPI',
			'zone_2_partner_id' => '01',
			'zone_3_countries' => 'BE,CZ,DK,EE,FI,FR,GR,HU,IE,IT,LU,MC,NL,PL,PT,SK,SI,ES,SE,GB',
			'zone_3_product' => 'BPI',
			'zone_3_partner_id' => '01',
			'zone_4_countries' => 'AF,EG,AL,DZ,AD,AO,AI,AQ,AG,GQ,AR,AM,AW,AZ,ET,AU,BS,BH,BD,BB,BY,BZ,BJ,BM,BT,BO,BA,BW,BV,BR,IO,BN,BF,BI,CT,XC,CL,CN,CK,CR,CI,DM,DO,DJ,EC,SV,ER,FO,FK,FJ,TF,GF,PF,FQ,GA,GM,GE,GH,GI,GD,GL,GP,GU,GT,GG,GN,GW,GW,GY,HT,HT,HM,HN,HK,IN,ID,IQ,IR,IS,IL,JM,JP,YE,JE,JT,JO,VG,VI,KY,KH,CM,CA,IC,CV,KZ,QA,KE,KG,KI,CC,CO,CO,KM,CG,CD,KP,KR,HR,CU,KW,LA,LS,LB,LR,LY,LI,MO,MG,MW,MY,MV,ML,IM,MP,MA,MH,MQ,MR,MU,YT,MK,XL,MX,MI,FM,MD,MC,MN,ME,MS,MZ,MM,NA,NR,NP,NC,NZ,NI,AN,NE,NG,NU,NF,NO,OM,PK,PW,PS,PA,PZ,PG,PY,PU,PE,PH,PN,PR,RE,RW,RU,SB,ZM,AS,WS,SM,ST,SA,CH,SN,RS,SC,SL,ZW,SG,SO,SJ,LK,VC,SH,KN,LC,PM,SD,SR,SZ,SY,ZA,GS,TJ,TW,TZ,TH,TL,TG,TK,TO,TT,TD,TN,TM,TC,TV,TR,UM,US,UG',
			'zone_4_product' => 'BPI',
			'zone_4_partner_id' => '01',
			'bpi_use_premium' => 0,
			'use_postfinder' => 0,
			'parcelservice_id' => 0,
		);
		$this->_loadConfig();
		if($this->_config['debug'] == true) {
			$this->log("Configuration:\n".print_r($this->_config, true));
		}
		$this-> _text = new LanguageTextManager('intraship', $_SESSION['languages_id']);
	}

	public function get_text($key) {
		return $this->_text->get_text($key);
	}

	public function log($text) {
		$time = microtime(true);
		$ts = sprintf('%s.%03d | ', date('Y-m-d H:i:s', floor($time)), ($time - floor($time)));
		$this->_logger->write($ts.$text.PHP_EOL);
	}

	protected function _loadConfig() {
		foreach($this->_config as $key => $value) {
			$db_key = self::CONFIG_PREFIX .'_'. strtoupper($key);
			$db_value = gm_get_conf($db_key);
			if(!($db_value === false || $db_value === null)) {
				$this->_config[$key] = $db_value;
			}
		}
	}

	public function saveConfig() {
		foreach($this->_config as $key => $value) {
			$db_key = self::CONFIG_PREFIX .'_'. strtoupper($key);
			$value = xtc_db_input($value);
			gm_set_conf($db_key, $value);
		}
	}

	public function __get($name) {
		if(array_key_exists($name, $this->_config)) {
			return $this->_config[$name];
		}
		return null;
	}

	public function __set($name, $value) {
		if(array_key_exists($name, $this->_config)) {
			$this->_config[$name] = trim($value);
		}
	}

	public function getLabelURL($orders_id) {
		$label_query = "SELECT label_url FROM orders_intraship_labels WHERE orders_id = ".(int)$orders_id;
		$label_result = xtc_db_query($label_query);
		if(xtc_db_num_rows($label_result) > 0) {
			$row = xtc_db_fetch_array($label_result);
			$label_url = $row['label_url'];
		}
		else {
			$label_url  = '';
		}
		return $label_url;
	}

	public function getWSDLLocation() {
		$dhlwsdlurl = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/1.0/geschaeftskundenversand-api-1.0.wsdl';
		return $dhlwsdlurl;
	}

	public function getWebserviceEndpoint() {
		if($this->debug == true) {
			$endpoint = 'https://cig.dhl.de/services/sandbox/soap';
		}
		else {
			$endpoint = 'https://cig.dhl.de/services/production/soap';
		}

		return $endpoint;
	}

	public function getWebserviceCredentials() {
		$credentials = new stdClass();
		if($this->debug == true) {
			$credentials->user = 'gambio_1';
			$credentials->password = '7692819c896fe2ea5fbd861cead0a08c503b69e3d56b77380a42b02aa9';
		}
		else {
			$credentials->user = 'gambio_1';
			$credentials->password = '7692819c896fe2ea5fbd861cead0a08c503b69e3d56b77380a42b02aa9';
		}
		return $credentials;
	}

	public function getIntrashipPortalURL() {
		if($this->debug == true) {
			$dhlintrashipurl='https://test-intraship.dhl.com/intraship.57/jsp/Login_WS.jsp';
		}
		else {
			$dhlintrashipurl = 'https://www.intraship.de/intraship/jsp/Login_WS.jsp';
		}
		return $dhlintrashipurl;
	}

	public function getProductCode($iso2) {
		$iso2 = strtoupper(trim($iso2));
		$product_code = false;
		for($zone = 1; $zone <= self::NUM_ZONES; $zone++) {
			$countries_config = 'zone_'.$zone.'_countries';
			$countries_list = $this->$countries_config;
			$countries = explode(',', $countries_list);
			if(in_array($iso2, $countries)) {
				$product_code_config = 'zone_'.$zone.'_product';
				$product_code = $this->$product_code_config;
				break;
			}
		}
		return $product_code;
	}

	public function getPartnerID($iso2) {
		$iso2 = strtoupper(trim($iso2));
		$partner_id = false;
		for($zone = 1; $zone <= self::NUM_ZONES; $zone++) {
			$countries_config = 'zone_'.$zone.'_countries';
			$countries_list = $this->$countries_config;
			$countries = explode(',', $countries_list);
			if(in_array($iso2, $countries)) {
				$partner_id_config = 'zone_'.$zone.'_partner_id';
				$partner_id = $this->$partner_id_config;
				break;
			}
		}
		return $partner_id;
	}

	public function storeTrackingNumber($orders_id, $trackingNumber)
	{
		if((int)$this->_config['parcelservice_id'] > 0)
		{
			$parcelServiceReader = MainFactory::create('ParcelServiceReader');
			$parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
			$parcelTrackingCodeWriter->insertTrackingCode($orders_id, $trackingNumber, $this->_config['parcelservice_id'], $parcelServiceReader);
		}
	}


	/**
	 * set order status and (optionally) notify customer by email
	 * @param int orders_id
	 * @param int orders_status_id
	 * @param string $order_status_comment
	 * @param boolean $notifyCustomer
	 */
	public function setOrderStatus($orders_id, $order_status_id, $order_status_comment = '', $notifyCustomer = false)
	{
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$this->log(sprintf('changing orders status of order %s to %s', $orders_id, $order_status_id));
		$db->where('orders_id', $orders_id);
		$db->update('orders', array('orders_status' => $order_status_id));

		$orders_status_history_entry = array(
			'orders_id'         => $orders_id,
			'orders_status_id'  => $order_status_id,
			'date_added'        => date('Y-m-d H:i:s'),
			'customer_notified' => $notifyCustomer === true ? '1' : '0',
			'comments'          => $order_status_comment,
		);
		$db->insert('orders_status_history', $orders_status_history_entry);
		if($notifyCustomer === true)
		{
			$this->log(sprintf('sending email notification regarding status change of order %s', $orders_id));
			$this->notifyCustomer($orders_id, $order_status_id, $order_status_comment);
		}
	}

	/**
	 * notify customer of a change in order status
	 *
	 * This is mostly copypasted from orders.php and MUST be refactored ASAP!
	 */
	protected function notifyCustomer($orders_id, $orders_status_id, $order_status_comment)
	{
		require_once DIR_FS_INC . 'xtc_php_mail.inc.php';
		require_once DIR_WS_CLASSES . 'order.php';
		$order       = new order((int)$orders_id);
		$lang_query  = sprintf('select languages_id from %s where directory = \'%s\'', TABLE_LANGUAGES,
		                       $order->info['language']);
		$lang_result = xtc_db_query($lang_query);
		while($lang_row = xtc_db_fetch_array($lang_result))
		{
			$lang = empty($lang_row['languages_id']) ? $_SESSION['languages_id'] : $lang_row['languages_id'];
		}
		$orders_status_array  = array();
		$orders_status_query  = sprintf('select orders_status_id, orders_status_name from %s where language_id = \'%s\'',
		                                TABLE_ORDERS_STATUS, $lang);
		$orders_status_result = xtc_db_query($orders_status_query);
		while($orders_status_row = xtc_db_fetch_array($orders_status_result))
		{
			$orders_status_array[$orders_status_row['orders_status_id']] = $orders_status_row['orders_status_name'];
		}

		$smarty = new Smarty;
		// assign language to template for caching
		$smarty->assign('language', $_SESSION['language']);
		$smarty->caching      = false;
		$smarty->template_dir = DIR_FS_CATALOG . 'templates';
		$smarty->compile_dir  = DIR_FS_CATALOG . 'templates_c';
		$smarty->config_dir   = DIR_FS_CATALOG . 'lang';
		$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
		$smarty->assign('logo_path', HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
		$smarty->assign('NAME', $order->customer['name']);
		$smarty->assign('GENDER', $order->customer['gender']);
		$smarty->assign('ORDER_NR', $orders_id);
		$smarty->assign('ORDER_LINK',
		                xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $orders_id, 'SSL'));
		$smarty->assign('ORDER_DATE', xtc_date_long($order->info['date_purchased']));
		$smarty->assign('ORDER_STATUS', $orders_status_array[$orders_status_id]);
		if(defined('EMAIL_SIGNATURE'))
		{
			$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
			$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
		}

		// START Parcel Tracking Code
		/** @var ParcelTrackingCode $coo_parcel_tracking_code_item */
		$coo_parcel_tracking_code_item   = MainFactory::create_object('ParcelTrackingCode');
		/** @var ParcelTrackingCodeReader $coo_parcel_tracking_code_reader */
		$coo_parcel_tracking_code_reader = MainFactory::create_object('ParcelTrackingCodeReader');
		$t_parcel_tracking_codes_array   = $coo_parcel_tracking_code_reader->getTackingCodeItemsByOrderId($coo_parcel_tracking_code_item,
																								  $orders_id);
		$smarty->assign('PARCEL_TRACKING_CODES_ARRAY', $t_parcel_tracking_codes_array);
		$smarty->assign('PARCEL_TRACKING_CODES', 'true');
		// END Parcel Tracking Code

		$smarty->assign('NOTIFY_COMMENTS', nl2br($order_status_comment));
		$html_mail = fetch_email_template($smarty, 'change_order_mail', 'html');
		$smarty->assign('NOTIFY_COMMENTS', $order_status_comment);
		$txt_mail  = fetch_email_template($smarty, 'change_order_mail', 'txt');

		if($_SESSION['language'] == 'german')
		{
			$subject = 'Ihre Bestellung ' . $orders_id . ', ' . xtc_date_long($order->info['date_purchased']) . ', '
			           . $order->customer['name'];
		}
		else
		{
			$subject = 'Your order ' . $orders_id . ', ' . xtc_date_long($order->info['date_purchased']) . ', '
			           . $order->customer['name'];
		}

		xtc_php_mail(
			EMAIL_BILLING_ADDRESS,
			EMAIL_BILLING_NAME,
			$order->customer['email_address'],
			$order->customer['name'],
			'',
			EMAIL_BILLING_REPLY_ADDRESS,
			EMAIL_BILLING_REPLY_ADDRESS_NAME,
			'',
			'',
			$subject,
			$html_mail,
			$txt_mail
		);
	}
}
MainFactory::load_origin_class('GMIntraship');