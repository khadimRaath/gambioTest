<?php
/* --------------------------------------------------------------
	GMKlarna.php 2016-07-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
define('KLARNA_SDK_DIR', DIR_FS_CATALOG.'ext/klarna_php_2.3.0/');
require_once DIR_FS_CATALOG.'inc/xtc_get_countries.inc.php';

class GMKlarna_ORIGIN {
	public $module_version = '2015-01-08';
	protected $_logger;
	protected $_text;
	const LOGLEVEL_INFO = 0;
	const LOGLEVEL_WARN = 10;
	const LOGLEVEL_ERROR = 20;
 	const LOGLEVEL_DEBUG = 99;

	protected $_config;
	const CONFIG_PREFIX = 'KLARNA_';
	protected $configured = false;

	protected $_klarna;

	public function __construct() {
		$this->_logger = new FileLog('payment-klarna', true);
		$this->_text = new LanguageTextManager('klarna', $_SESSION['languages_id']);
		$this->loadConfig();
		$this->_initAPI();
		//$this->_log("initialized with config:\n".print_r($this->_config, true));
	}

	public function get_text($key) {
		return $this->_text->get_text($key);
	}

	protected function _initAPI() {
		$include_paths = explode(PATH_SEPARATOR, get_include_path());
		if(!in_array(KLARNA_SDK_DIR, $include_paths)) {
			set_include_path(get_include_path().PATH_SEPARATOR.KLARNA_SDK_DIR);
		}
		require_once KLARNA_SDK_DIR.'transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc';
		require_once KLARNA_SDK_DIR.'transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc';
		require_once KLARNA_SDK_DIR.'Klarna.php';
		$k_config = array(
			$this->_config['merchant_id'],
			$this->_config['shared_secret'],
			$this->_config['country'],
			$this->_config['language'],
			$this->_config['currency'],
			$this->_config['server'],
			'mysql', // PClass Storage
			array(
				'user' => DB_SERVER_USERNAME,
				'passwd' => DB_SERVER_PASSWORD,
				'dsn' =>  DB_SERVER,
				'db' => DB_DATABASE,
				'table' => 'klarna_pclass',
			),
			true, // SSL
			true, // Remote logging of response times of xmlrpc calls
		);
		$this->_klarna = new Klarna();
		if(empty($this->_config['merchant_id']) === false && empty($this->_config['shared_secret']) === false) {
			$this->_klarna->config($k_config[0], $k_config[1], $k_config[2], $k_config[3], $k_config[4], $k_config[5], $k_config[6], $k_config[7], $k_config[8], $k_config[9]);
			//$this->_log('Klarna configured');
			$this->configured = true;
		}
		/* only for debugging purposes
		else {
			$this->_log('running in unconfigured mode!');
			//$this->_log("k_config:\n".print_r($k_config, true));
		}*/
	}

	public function isConfigured()
	{
		return $this->configured === true;
	}

	public function _log($message, $loglevel = self::LOGLEVEL_INFO) {
		if($this->_logger !== false) {
			$this->_logger->write(date('c'). ' | '. $message ."\n");
		}
	}

	public function getCountries() {
		$countries = array(
			'AT' => KlarnaCountry::AT,
			'DK' => KlarnaCountry::DK,
			'FI' => KlarnaCountry::FI,
			'DE' => KlarnaCountry::DE,
			'NL' => KlarnaCountry::NL,
			'NO' => KlarnaCountry::NO,
			'SE' => KlarnaCountry::SE,
		);
		return $countries;
	}

	public function getCountryIso($klarnacountry = null) {
		if($klarnacountry == null) {
			$klarnacountry = $this->_config['country'];
		}
		$countries = $this->getCountries();
		$countries_flip = array_flip($countries);
		$iso = $countries_flip[$klarnacountry];
		return $iso;
	}

	public function getLanguages() {
		$languages = array(
			'DA' => KlarnaLanguage::DA,
			'DE' => KlarnaLanguage::DE,
			'EN' => KlarnaLanguage::EN,
			'FI' => KlarnaLanguage::FI,
			'NB' => KlarnaLanguage::NB,
			'NL' => KlarnaLanguage::NL,
			'SV' => KlarnaLanguage::SV,
		);
		return $languages;
	}

	public function getCurrencies() {
		$currencies = array(
			'SEK' => KlarnaCurrency::SEK,
			'NOK' => KlarnaCurrency::NOK,
			'EUR' => KlarnaCurrency::EUR,
			'DKK' => KlarnaCurrency::DKK,
		);
		return $currencies;
	}

	public function getCurrencyString() {
		switch($this->_config['currency']) {
			 case KlarnaCurrency::SEK:
			 	$currency = 'SEK';
			 	break;
			 case KlarnaCurrency::NOK:
			 	$currency = 'NOK';
			 	break;
			 case KlarnaCurrency::EUR:
			 	$currency = 'EUR';
			 	break;
			 case KlarnaCurrency::DKK:
			 	$currency = 'DKK';
			 	break;
			 default:
			 	$currency = 'ERROR';
		}
		return $currency;
	}

	public function getCountryConfig($country_iso2) {
		require_once KLARNA_SDK_DIR.'Country.php';
		require_once KLARNA_SDK_DIR.'Language.php';
		require_once KLARNA_SDK_DIR.'Currency.php';
		$country_config = array(
			'DE' => array(
				'country' => KlarnaCountry::DE,
				'language' => KlarnaLanguage::DE,
				'currency' => KlarnaCurrency::EUR,
				'housenumbersplitting' => true,
			),
			'AT' => array(
				'country' => KlarnaCountry::AT,
				'language' => KlarnaLanguage::DE,
				'currency' => KlarnaCurrency::EUR,
				'housenumbersplitting' => false,
			),
			'DK' => array(
				'country' => KlarnaCountry::DK,
				'language' => KlarnaLanguage::DA,
				'currency' => KlarnaCurrency::DKK,
				'housenumbersplitting' => false,
			),
			'FI' => array(
				'country' => KlarnaCountry::FI,
				'language' => KlarnaLanguage::FI,
				'currency' => KlarnaCurrency::EUR,
				'housenumbersplitting' => false,
			),
			'NL' => array(
				'country' => KlarnaCountry::NL,
				'language' => KlarnaLanguage::NL,
				'currency' => KlarnaCurrency::EUR,
				'housenumbersplitting' => true,
			),
			'SE' => array(
				'country' => KlarnaCountry::SE,
				'language' => KlarnaLanguage::SV,
				'currency' => KlarnaCurrency::SEK,
				'housenumbersplitting' => false,
			),
			'NO' => array(
				'country' => KlarnaCountry::NO,
				'language' => KlarnaLanguage::NB,
				'currency' => KlarnaCurrency::NOK,
				'housenumbersplitting' => false,
			),
		);
		if(array_key_exists($country_iso2, $country_config)) {
			return $country_config[$country_iso2];
		}
		return false;
	}

	public function getNumPNODigits() {
		switch($this->_config['country']) {
			case KlarnaCountry::SE:
			case KlarnaCountry::FI:
			case KlarnaCountry::DK:
				$num_digits = 4;
				break;
			case KlarnaCountry::NO:
				$num_digits = 5;
				break;
			default:
				$num_digits = 0;
		}
		return $num_digits;
	}

	public function getCountryByIso2($iso2) {
		$query = "SELECT * FROM countries WHERE countries_iso_code_2 = ':iso2'";
		$query = strtr($query, array(':iso2' => xtc_db_input($iso2)));
		$result = xtc_db_query($query);
		$country = false;
		while($row = xtc_db_fetch_array($result)) {
			$country = $row;
		}
		return $country;
	}

	public function loadConfig() {
		$default_config = array(
			'activate_country_AT' => '',
			'merchant_id_AT' => '',
			'shared_secret_AT' => '',
			'invoice_fee_AT' => '1.95',
			'activate_country_DK' => '',
			'merchant_id_DK' => '',
			'shared_secret_DK' => '',
			'invoice_fee_DK' => '1.95',
			'activate_country_FI' => '',
			'merchant_id_FI' => '',
			'shared_secret_FI' => '',
			'invoice_fee_FI' => '1.95',
			'activate_country_DE' => '',
			'merchant_id_DE' => '',
			'shared_secret_DE' => '',
			'invoice_fee_DE' => '1.95',
			'activate_country_NL' => '',
			'merchant_id_NL' => '',
			'shared_secret_NL' => '',
			'invoice_fee_NL' => '1.95',
			'activate_country_NO' => '',
			'merchant_id_NO' => '',
			'shared_secret_NO' => '',
			'invoice_fee_NO' => '1.95',
			'activate_country_SE' => '',
			'merchant_id_SE' => '',
			'shared_secret_SE' => '',
			'invoice_fee_SE' => '1.95',
			'country' => '',
			'language' => '',
			'currency' => '',
			'server' => '',
			'show_checkout_partpay' => '1',
			'show_product_partpay' => '1',
		);

		$this->_config = array_merge(array(), $default_config);
		foreach(array_keys($this->_config) as $key) {
			$dbkey = self::CONFIG_PREFIX.strtoupper($key);
			$value = gm_get_conf($dbkey);
			if(strpos($dbkey, 'shared_secret_') !== false && empty($value) === true) {
				# shared secrets must not be empty
				$value = 'invalid';
			}
			if(isset($value)) {
				$this->_config[$key] = $value;
			}
		}

		//$order = new order();
		$sendto_country_id = $this->_getSendToCountry();
		if(is_array($GLOBALS['order']->delivery['country']) && !empty($GLOBALS['order']->delivery['country']['id'])) {
			$country_id = $GLOBALS['order']->delivery['country']['id'];
		}
		/*
		else if(is_array($order->delivery['country']) && !empty($order->delivery['country']['id'])) {
			$country_id = $order->delivery['country']['id'];
		}
		*/
		else if($sendto_country_id !== false) {
			$country_id = $sendto_country_id;
		}
		else {
			if(isset($_SESSION['customer_country_id'])) {
				$country_id = $_SESSION['customer_country_id'];
			}
			else {
				$country_id = STORE_COUNTRY;
			}
		}
		//$this->_log("country_id: $country_id");

		$countrydata = xtc_get_countriesList($country_id, true);
		//$this->_log("countrydata:\n".print_r($countrydata, true));
		$country_config = $this->getCountryConfig(strtoupper($countrydata['countries_iso_code_2']));
		//$this->_log("country_config:\n".print_r($country_config, true));
		if($country_config !== false) {
			$this->_config['country'] = $country_config['country'];
			$this->_config['language'] = $country_config['language'];
			$this->_config['currency'] = $country_config['currency'];
			$this->_config['housenumbersplitting'] = $country_config['housenumbersplitting'];
			$this->_config['merchant_id'] = $this->_config['merchant_id_'.strtoupper($countrydata['countries_iso_code_2'])];
			$this->_config['shared_secret'] = $this->_config['shared_secret_'.strtoupper($countrydata['countries_iso_code_2'])];
		}
		//$this->_log("configured:\n".print_r($this->_config, true));
	}

	public function getConfig($reload = false) {
		if($reload == true || empty($this->_config)) {
			$this->loadConfig();
		}
		return $this->_config;
	}

	public function setConfig($key, $value) {
		if(array_key_exists($key, $this->_config)) {
			$dbkey = self::CONFIG_PREFIX.strtoupper($key);
			$this->_config[$key] = $value;
			gm_set_conf($dbkey, $value);
		}
	}

	public function saveConfig(array $new_config) {
		foreach($new_config as $key => $value) {
			$this->setConfig($key, $value);
		}

		# make sure countries can only be activated with a full set of credentials
		foreach($this->getCountries() as $iso2 => $klarnacountry) {
			if(empty($new_config['merchant_id_'.$iso2]) === true || empty($new_config['shared_secret_'.$iso2]) === true) {
				$this->setConfig('activate_country_'.$iso2, '0');
			}
		}
	}

	public function setI18N($country, $language, $currency) {
		if(in_array($this->getCountries(), $country) && in_array($this->getLanguages(), $language) && in_array($this->getCurrencies(), $currency)) {
			$this->_config['country'] = $country;
			$this->_config['language'] = $language;
			$this->_config['currency'] = $currency;
		}
		else {
			throw new GMKlarnaException('invalid i18n data');
		}
	}

	/*
	** INVOICE
	*/

	public function get_properties_combi_data($p_combi_id, $p_language_id = null)
	{
		if($p_language_id === null)
		{
			$t_language_id = $_SESSION['languages_id'];
		}
		else
		{
			$t_language_id = $p_language_id;
		}

		$t_query = "SELECT
				`properties_name`,
				`values_name`
			FROM `products_properties_index`
			WHERE `products_properties_combis_id` = ".(int)$p_combi_id." AND
				`language_id` = ".(int)$t_language_id."
			ORDER BY `properties_sort_order` ASC";
		$t_combi_data = array();
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_combi_data[] = array(
				'name' => $t_row['properties_name'],
				'value' => $t_row['values_name'],
			);
		}
		return $t_combi_data;
	}

	public function reserveInvoiceAmount($order, $orders_id, $pclass_id = null) {
		// make sure API is configured for destination country
		$country_config = $this->getCountryConfig(strtoupper($order->delivery['country']['iso_code_2']));
		if($country_config !== false) {
			$this->_config['country'] = $country_config['country'];
			$this->_config['language'] = $country_config['language'];
			$this->_config['currency'] = $country_config['currency'];
		}
		$this->_initAPI();
		$this->_log("reserveAmount for order $orders_id, country ".$this->_config['country']);
		# $this->_log("order:\n".print_r($order, true));

		if($pclass_id !== null) {
			// partpay mode
			$pclass = $pclass_id;
		}
		else {
			// invoice mode
			$pclass = KlarnaPClass::INVOICE;
		}
		if($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
			$vat_flag = KlarnaFlags::INC_VAT;
			$inc_vat = true;
		}
		else {
			$vat_flag = KlarnaFlags::NO_FLAG;
			$inc_vat = false;
		}
		// add articles
		foreach($order->products as $product) {
			$t_attribute_info = '';
			$t_attributes_data = array();
			if(empty($product['attributes']) !== true)
			{
				foreach($product['attributes'] as $t_pattr)
				{
					$t_attributes_data[] = $t_pattr['option'].': '.$t_pattr['value'];
				}
			}
			if(($t_xpos = strpos($product['id'], 'x')) !== false)
			{
				$t_combi_id = (int)substr($product['id'], $t_xpos + 1);
				$t_combi_data = $this->get_properties_combi_data($t_combi_id);
				foreach($t_combi_data as $t_prop_data)
				{
					$t_attributes_data[] = $t_prop_data['name'].': '.$t_prop_data['value'];
				}
			}
			if(empty($t_attributes_data) !== true)
			{
				$t_attribute_info = ' ('.implode(', ', $t_attributes_data).')';
			}


			# $this->_log("adding product ".$product['name'] . $t_attribute_info);
			//$this->_log("adding product\n".print_r($product, true));
			//$this->_log("Flag: ".(KlarnaFlags::PRINT_1000|$vat_flag));
			if(($product['qty'] - floor($product['qty'])) > 0) {
				// workaround for floating point quantities required
				$product['name'] = $product['qty'] .' '. $product['name'];
				$product['price'] = $product['qty'] * $product['price'];
				$product['qty'] = 1;
			}

			$product['tax'] = round($product['tax'], 2);

			$this->_klarna->addArticle(
				round($product['qty']), // qty
				$this->transcodeOutbound($product['model']), // model
				$this->transcodeOutbound($product['name'] . $t_attribute_info), // name
				$product['price'], // price
				$product['tax'], // vat
				0, // discount
				$vat_flag // flags
			);

			//$this->_log('added product with tax rate '.number_format($product['tax'], 128, '.', ''));
		}

		$global_discount = 0;

		// process order_total modules
		// this assumes that reserveInvoiceAmount was called by the payment module's payment_action() method
		// which was called by checkout_process.php
		$real_ot_modules = array('ot_subtotal', 'ot_subtotal_no_tax', 'ot_tax', 'ot_total_netto', 'ot_total', 'ot_gm_tax_free');
		$known_ot_modules = array('ot_cod_fee', 'ot_coupon', 'ot_discount', 'ot_gambioultra', 'ot_gm_tax_free', 'ot_gv', 'ot_loworderfee',
			'ot_ps_fee', 'ot_shipping', 'ot_klarna2_fee', 'ot_bonus_fee');
		foreach($GLOBALS['order_total_modules']->modules as $ot_module) {
			$class = substr($ot_module, 0, strrpos($ot_module, '.'));
			if(is_object($GLOBALS[$class]) && $GLOBALS[$class]->enabled) {
				if(in_array($GLOBALS[$class]->code, $real_ot_modules) || !in_array($GLOBALS[$class]->code, $known_ot_modules)) {
					continue;
				}
				$output = $GLOBALS[$class]->output;
				if(empty($output)) {
					continue;
				}
				$flags = KlarnaFlags::NO_FLAG;
				$tax_rate = 0;
				$model = '';
				$title = '';
				$value = 0;
				$discount = 0;
				switch($GLOBALS[$class]->code) {
					case 'ot_shipping':
						$output = $output[0];
						//$this->_log("shipping output:\n".print_r($output, true));
						if($inc_vat) {
							$shipping_module = substr($_SESSION['shipping']['id'], 0, strpos($_SESSION['shipping']['id'], '_'));
							if(!isset($GLOBALS[$shipping_module]) && file_exists(DIR_FS_CATALOG . 'includes/modules/shipping/' . basename($shipping_module) . '.php')) {
								include_once(DIR_FS_CATALOG . 'includes/modules/shipping/' . basename($shipping_module) . '.php');
								$GLOBALS[$shipping_module] = new $shipping_module;
							}
							//$shipping_tax = $GLOBALS['xtPrice']->TAX[$GLOBALS[$shipping_module]->tax_class];
							$shipping_tax = xtc_get_tax_rate($GLOBALS[$shipping_module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$shipping_tax_flag |= KlarnaFlags::INC_VAT;
						}
						else {
							$shipping_tax = 0;
							$shipping_tax_flag = KlarnaFlags::NO_FLAG;
						}
						$this->_log("Klarna-Shipping: ${output['title']} ${output['value']} $shipping_tax");
						$value += $output['value'];
						$title .= $output['title'];
						$flags |= KlarnaFlags::IS_SHIPMENT | $shipping_tax_flag;
						$model = 'SHIPPING';
						$tax_rate = $shipping_tax;
						break;

					case 'ot_discount':
						foreach($output as $discount_output) {
							$value = $discount_output['value'];
							$title = $discount_output['title'];
						}
						$model = 'DISCOUNT';
						break;

					case 'ot_bonus_fee':
						foreach($output as $bonus_output) {
							$value -= $bonus_output['value'];
							$title = $bonus_output['title'];
						}
						$model = 'BONUS';
						break;

					case 'ot_coupon':
						foreach($output as $coupon_output) {
							$value = $coupon_output['value'];
							$title = $coupon_output['title'];
						}
						$model = 'COUPON';
						break;

					case 'ot_gambioultra':
						if($inc_vat) {
							$tax_rate = xtc_get_tax_rate(MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$flags |= KlarnaFlags::INC_VAT;
						}
						foreach($output as $gu_output) {
							$value += $gu_output['value'];
							$title .= $gu_output['title'];
						}
						$model = 'SHIPPING';
						$flags |= KlarnaFlags::IS_SHIPMENT;
						break;

					case 'ot_gv':
						foreach($output as $gv_output) {
							$value -= $gv_output['value'];
							$title .= $gv_output['title'];
						}
						$model = 'GIFT';
						break;

					case 'ot_loworderfee':
						if($inc_vat) {
							$tax_rate = xtc_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$flags |= KlarnaFlags::INC_VAT;
						}
						foreach($output as $lo_output) {
							$value += $lo_output['value'];
							$title .= $lo_output['title'];
						}
						$model = 'LOW_ORDER';
						$flags |= KlarnaFlags::IS_HANDLING;
						break;

					case 'ot_klarna2_fee':
						if($inc_vat) {
							$tax_rate = xtc_get_tax_rate(MODULE_ORDER_TOTAL_KLARNA2_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$flags |= KlarnaFlags::INC_VAT;
						}
						foreach($output as $lo_output) {
							$value += $lo_output['value'];
							$title .= $lo_output['title'];
						}
						$model = 'INV_FEE';
						$flags |= KlarnaFlags::IS_HANDLING;
						break;

					case 'ot_ps_fee':
						if($inc_vat) {
							$tax_rate = xtc_get_tax_rate(MODULE_ORDER_TOTAL_PS_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
							$flags |= KlarnaFlags::INC_VAT;
						}
						foreach($output as $ps_output) {
							$value += $ps_output['value'];
							$title .= $ps_output['title'];
						}
						$model = 'PS_FEE';
						$flags |= KlarnaFlags::IS_SHIPMENT;
						break;
				}

				$tax_rate = round($tax_rate, 2);
				$title = $this->transcodeOutbound($title);

				$this->_klarna->addArticle(1, $model, $title, $value, $tax_rate, $discount, $flags);
				//$this->_log('added auxilliary product with tax rate '.number_format($tax_rate, 128, '.', ''));
			}
		}


		// add addresses
		if(empty($order->billing['house_number']))
		{
			$billing_street       = $this->_cutOffHouseNumber($order->billing['street_address']);
			$billing_house_number = $this->_findHouseNumber($order->billing['street_address']);
		}
		else
		{
			$billing_street       = $order->billing['street_address'];
			$billing_house_number = $order->billing['house_number'];
		}

		$billing_house_ext = '';
		if($this->_config['country'] == KlarnaCountry::NL) {
			if(preg_match('/(.*)\s+(.*)/', $billing_house_number, $matches) == 1) {
				$billing_house_number = $matches[1];
				$billing_house_ext = $matches[2];
			}
		}
		$billing_addr = new KlarnaAddr(
			$this->transcodeOutbound($order->customer['email_address']), // email
			$this->transcodeOutbound($order->customer['telephone']), // phone
			null, // cellphone
			$this->transcodeOutbound($order->billing['firstname']), // first name
			$this->transcodeOutbound($order->billing['lastname']), // last name
			$this->transcodeOutbound($order->billing['company']), // add. addr
			//$this->transcodeOutbound($order->billing['street_address'], // street
			$this->transcodeOutbound($billing_street),
			$this->transcodeOutbound($order->billing['postcode']), // postcode
			$this->transcodeOutbound($order->billing['city']), // city
			$this->transcodeOutbound($order->billing['country']['iso_code_2']), // country
			$billing_house_number, // house number (DE/NL)
			$billing_house_ext  // house extension (NL)
		);
		$this->_klarna->setAddress(KlarnaFlags::IS_BILLING, $billing_addr);

		if(empty($order->delivery['house_number']))
		{
			$delivery_street       = $this->_cutOffHouseNumber($order->delivery['street_address']);
			$delivery_house_number = $this->_findHouseNumber($order->delivery['street_address']);
		}
		else
		{
			$delivery_street       = $order->delivery['street_address'];
			$delivery_house_number = $order->delivery['house_number'];
		}
		$delivery_house_ext = '';
		if($this->_config['country'] == KlarnaCountry::NL) {
			if(preg_match('/(.*)\s+(.*)/', $delivery_house_number, $matches) == 1) {
				$delivery_house_number = $matches[1];
				$delivery_house_ext = $matches[2];
			}
		}
		$delivery_addr = new KlarnaAddr(
			$this->transcodeOutbound($order->customer['email_address']), // email
			$this->transcodeOutbound($order->customer['telephone']), // phone
			null, // cellphone
			$this->transcodeOutbound($order->delivery['firstname']), // first name
			$this->transcodeOutbound($order->delivery['lastname']), // last name
			$this->transcodeOutbound($order->delivery['company']), // add. addr
			//$this->transcodeOutbound($order->delivery['street_address'], // street
			$this->transcodeOutbound($delivery_street),
			$this->transcodeOutbound($order->delivery['postcode']), // postcode
			$this->transcodeOutbound($order->delivery['city']), // city
			$this->transcodeOutbound($order->delivery['country']['iso_code_2']), // country
			$delivery_house_number, // house number (DE/NL)
			$delivery_house_ext  // house extension (NL)
		);
		$this->_klarna->setAddress(KlarnaFlags::IS_SHIPPING, $delivery_addr);
		// set store info
		$this->_klarna->setEstoreInfo($orders_id, '', '');
		// set comment
		$this->_klarna->setComment('');
		// set shipment info
		//$this->_klarna->setShipmentInfo('delay_adjust', KlarnaFlags::EXPRESS_SHIPMENT);

		$dob = $this->_getDateOfBirth($_SESSION['customer_id'], $_SESSION['klarna_pno']);
		$gender = $_SESSION['customer_gender'] == 'f' ? 0 : 1;
		$this->_log("Reserving amount with gender $gender and dob $dob");

		$result = false;
		try {
			// reserve amount
			$result = $this->_klarna->reserveAmount(
				$dob,
				$gender, // gender
				-1, // amount
				KlarnaFlags::NO_FLAG,
				$pclass
			);
		}
		catch(Exception $e) {
			$this->_log("Exception in reserveInvoiceAmount: ".$e->getMessage()/*."\n".$e->getTraceAsString()*/);
			throw $e;
		}
		$this->_log("Amount reserved, result:\n".print_r($result, true));
		if ($result[1] == KlarnaFlags::PENDING) {
			$note = $this->get_text('note_invoice_pending');
		}
		else {
			$note = $this->get_text('note_invoice');
		}
		$rno = $result[0];
		$comment = $note.", ".$this->get_text('reservation_number').': '.$rno;
		$this->addOrdersStatusHistoryEntry($orders_id, $comment);
		switch($result[1]) {
			case KlarnaFlags::ACCEPTED:
				$status_text = 'accepted';
				break;
			case KlarnaFlags::PENDING:
				$status_text = 'pending';
				break;
			default:
				$status_text = 'unknown';
		}
		xtc_db_query("REPLACE INTO orders_klarna (`orders_id`, `rno`, `status`) VALUES (".(int)$orders_id.", '".xtc_db_input($rno)."', '".$status_text."')");
		return $result;
	}

	public function checkOrderStatus($orders_id) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->checkOrderStatus($okdata['rno']);
			switch($result) {
				case KlarnaFlags::ACCEPTED:
					$status = 'accepted';
					break;
				case KlarnaFlags::PENDING:
					$status = 'pending';
					break;
				case KlarnaFlags::DENIED:
					$status = 'denied';
					break;
				default:
					$status = 'UNKNOWN';
			}
			$query = "UPDATE orders_klarna SET status = ':status' WHERE orders_id = :orders_id";
			$query = strtr($query, array(':status' => $status, ':orders_id' => (int)$orders_id));
			xtc_db_query($query);
			$this->_log("checkOrderStatus for orders_id $orders_id, RNO ${okdata['rno']}: $status");
		}
		catch(Exception $e) {
			$this->_log("Exception in checkOrderStatus: ".$e->getMessage()."\n".$e->getTraceAsString());
			$status = false;
		}
		return $status;
	}

	public function activateReservation($orders_id) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->activate($okdata['rno']);
			$risk_status = $result[0];
			$reservation_number = $result[1];
			$this->_log("Reservation activated, risk status: $risk_status, reservation number: $reservation_number");
			$query = "UPDATE orders_klarna SET risk_status = ':risk_status', inv_rno = ':inv_rno' WHERE orders_id = :orders_id";
			$query = strtr($query, array(':risk_status' => xtc_db_input($risk_status), ':inv_rno' => xtc_db_input($reservation_number), ':orders_id' => (int)$orders_id));
			xtc_db_query($query);
			$status = true;
		}
		catch(Exception $e) {
			$this->_log("Exception in activateReservation: ".$e->getMessage()."\n".$e->getTraceAsString());
			$status = false;
		}
		return $status;
	}

	public function cancelReservation($orders_id) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->cancelReservation($okdata['rno']);
			if($result == true) {
				$query = "UPDATE orders_klarna SET status = 'cancelled' WHERE orders_id = :orders_id";
				$query = strtr($query, array(':orders_id' => (int)$orders_id));
				xtc_db_query($query);
			}
		}
		catch(Exception $e) {
			$this->_log("Exception in cancelReservation: ".$e->getMessage()."\n".$e->getTraceAsString());
			$result = false;
		}
		return $result;
	}

	public function splitReservation($orders_id, $amount) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->splitReservation($okdata['rno'], $amount);
			$rno = $result[0];
			$status = $result[1];
			$this->_log("Reservation ${okdata['rno']} ($rno) for order $orders_id reduced by $amount, status $status");
		}
		catch(Exception $e) {
			$this->_log("Exception in cancelReservation: ".$e->getMessage()."\n".$e->getTraceAsString());
			$result = false;
		}
		return $result;
	}

	public function changeReservation($orders_id, $amount) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->changeReservation($okdata['rno'], $amount);
			$rno = $result[0];
			$status = $result[1];
			$this->_log("Reservation ${okdata['rno']} ($rno) for order $orders_id changed to $amount, status $status");
		}
		catch(Exception $e) {
			$this->_log("Exception in cancelReservation: ".$e->getMessage()."\n".$e->getTraceAsString());
			$result = false;
		}
		return $result;
	}

	public function creditInvoice($orders_id) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->creditInvoice($okdata['inv_rno']);
			if(is_string($result)) {
				$this->_log("Invoice for order $orders_id, invoice ${okdata['inv_rno']} ($result) has been credited");
				$rc = true;
			}
			else {
				$this->_log("ERROR crediting invoice ${okdata['inv_rno']}:\n".print_r($result, true));
				$rc = false;
			}
		}
		catch(Exception $e) {
			$this->_log("Exception in creditInvoice: ".$e->getMessage()."\n".$e->getTraceAsString());
			$rc = false;
		}
		return $rc;
	}

	public function creditPart($orders_id, $articles) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			foreach($articles as $model => $quantity) {
				$this->_log("returning $quantity of $model");
				$this->_klarna->addArtNo($quantity, $model);
			}
			$result = $this->_klarna->creditPart($okdata['inv_rno']);
			if(is_string($result)) {
				$this->_log("Return for order $orders_id, invoice ${okdata['inv_rno']} ($result) has been credited");
				$this->_logCreditPart($orders_id, $articles);
				$rc = true;
			}
			else {
				$this->_log("ERROR returning articles from invoice ${okdata['inv_rno']}:\n".print_r($result, true));
				$rc = false;
			}
		}
		catch(Exception $e) {
			$this->_log("Exception in creditPart: ".$e->getMessage()."\n".$e->getTraceAsString());
			$rc = false;
		}
		return $rc;
	}

	protected function _logCreditPart($orders_id, $articles) {
		$raw_query = "INSERT INTO orders_klarna_creditpart (orders_id, products_model, quantity, sent_time) VALUES (:orders_id, ':products_model', :quantity, NOW())";
		foreach($articles as $model => $quantity) {
			$query = strtr($raw_query, array(':orders_id' => (int)$orders_id, ':products_model' => xtc_db_input($model), ':quantity' => (int)$quantity));
			$this->_log($query);
			xtc_db_query($query);
		}
	}

	public function getCreditParts($orders_id) {
		$query = "SELECT * FROM orders_klarna_creditpart WHERE orders_id = :orders_id ORDER BY sent_time ASC";
		$query = strtr($query, array(':orders_id' => (int)$orders_id));
		$credit_parts = array();
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			$credit_parts[] = $row;
		}
		return $credit_parts;
	}


	public function returnAmount($orders_id, $amount, $vat, $description = '') {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		$vat_flag = ($vat > 0) ? KlarnaFlags::INC_VAT : KlarnaFlags::NO_FLAG;
		try {
			$result = $this->_klarna->returnAmount($okdata['inv_rno'], $amount, $vat, $vat_flag, $description);
			if(is_string($result)) {
				$this->_log("ReturnAmount for order $orders_id, invoice ${okdata['inv_rno']} ($result) has been sent");
				$this->_logReturnAmount($orders_id, $amount, $vat, $description);
				$rc = true;
			}
			else {
				$this->_log("ERROR returnAmount for invoice ${okdata['inv_rno']} failed:\n".print_r($result, true));
				$rc = false;
			}
		}
		catch(Exception $e) {
			$this->_log("Exception in returnAmount: ".$e->getMessage()."\n".$e->getTraceAsString());
			$rc = false;
		}
		return $rc;
	}

	protected function _logReturnAmount($orders_id, $amount, $vat, $description) {
		$query = "INSERT INTO orders_klarna_returnamount (orders_id, amount, vat, description, sent_time) VALUES (:orders_id, :amount, :vat, ':description', NOW())";
		$query = strtr($query, array(':orders_id' => (int)$orders_id, ':amount' => (double)$amount, ':vat' => (double)$vat, ':description' => xtc_db_input($description)));
		xtc_db_query($query);
	}

	public function getReturnAmounts($orders_id) {
		$query = "SELECT * FROM orders_klarna_returnamount WHERE orders_id = :orders_id ORDER BY sent_time ASC";
		$query = strtr($query, array(':orders_id' => (int)$orders_id));
		$return_amounts = array();
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			$return_amounts[] = $row;
		}
		return $return_amounts;
	}

	public function emailInvoice($orders_id) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->emailInvoice($okdata['inv_rno']);
			if(is_string($result)) {
				$this->_log("Invoice for order $orders_id, invoice ${okdata['inv_rno']} ($result) has been sent via email.");
				$rc = true;
			}
			else {
				$this->_log("ERROR emailing invoice ${okdata['inv_rno']}:\n".print_r($result, true));
				$rc = false;
			}
		}
		catch(Exception $e) {
			$this->_log("Exception in emailInvoice: ".$e->getMessage()."\n".$e->getTraceAsString());
			$rc = false;
		}
		return $rc;
	}

	public function sendInvoice($orders_id) {
		$okdata = $this->getOrdersKlarnaData($orders_id);
		try {
			$result = $this->_klarna->sendInvoice($okdata['inv_rno']);
			if(is_string($result)) {
				$this->_log("Invoice for order $orders_id, invoice ${okdata['inv_rno']} ($result) will be sent via physical mail.");
				$rc = true;
			}
			else {
				$this->_log("ERROR sending invoice ${okdata['inv_rno']}:\n".print_r($result, true));
				$rc = false;
			}
		}
		catch(Exception $e) {
			$this->_log("Exception in sendInvoice: ".$e->getMessage()."\n".$e->getTraceAsString());
			$rc = false;
		}
		return $rc;
	}

	public function getInvoicePDFURL($inv_rno) {
		if($this->_config['server'] == 0) {
			$url = 'https://online.klarna.com/invoices/'.$inv_rno.'.pdf';
		}
		else {
			$url = 'https://beta-test.klarna.com/invoices/'.$inv_rno.'.pdf';
		}
		return $url;
	}

	public function getOrdersKlarnaData($orders_id) {
		$query = "SELECT * FROM orders_klarna WHERE orders_id = :oid";
		$query = strtr($query, array(':oid' => (int)$orders_id));
		$result = xtc_db_query($query);
		$okdata = false;
		while($row = xtc_db_fetch_array($result)) {
			$okdata = $row;
		}
		return $okdata;
	}

	public function getPClasses($cart_value = null) {
		try {
			$pclasses = $this->_klarna->getPClasses();
			/*
			if(empty($pclasses)) {
				$this->_klarna->fetchPClasses();
				$pclasses = $this->_klarna->getPClasses();
			}
			*/
		}
		catch(Exception $e) {
			$this->_log("ERROR fetching PClasses!\n".print_r($e, true));
			$pclasses = array();
		}
		if($cart_value !== null) {
			foreach($pclasses as $idx => $pclass) {
				if($pclass->getMinAmount() > $cart_value) {
					unset($pclasses[$idx]);
				}
			}
		}
		if(empty($pclasses)) {
			$pclasses = false;
		}
		return $pclasses;
	}

	public function clearPClasses() {
		$ok = true;
		try {
			$this->_klarna->clearPClasses();
		}
		catch(Exception $e) {
			$this->_log("ERROR clearing PClasses!\n".print_r($e, true));
			$ok = false;
		}
		foreach($this->getCountries() as $iso => $klarnacountry) {
			$this->_log("fetching pclass for $iso/$klarnacountry");
			try {
				$this->_klarna->fetchPClasses($klarnacountry);
			}
			catch(Exception $e) {
				$this->_log("WARNING: Could not retrieve PClasses for country $iso/$klarnacountry! - " . $e->getMessage());
				//$ok = false;
			}
		}
		return $ok;
	}

	protected function _getDateOfBirth($customers_id = null, $pno = '') {
		if($customers_id === null) {
			$customers_id = $_SESSION['customer_id'];
		}
		$query = "SELECT YEAR(`customers_dob`) AS year, MONTH(`customers_dob`) AS month, DAY(`customers_dob`) AS day FROM customers WHERE customers_id = :cid";
		$query = strtr($query, array(':cid' => (int)$_SESSION['customer_id']));
		$result = xtc_db_query($query, 'db_link', false);
		$dob = '00000000';
		while($row = xtc_db_fetch_array($result)) {
			switch($this->_config['country']) {
				case KlarnaCountry::SE:
					$dob_format = 'YYMMDD-PNO';
					break;
				case KlarnaCountry::FI:
					$dob_format = 'DDMMYY-PNO';
					break;
				case KlarnaCountry::DK:
				case KlarnaCountry::NO:
					$dob_format = 'DDMMYYPNO';
					break;
				default:
					$dob_format = 'DDMMYYYY';
			}
			//$dob = sprintf('%02d%02d%04d', $row['day'], $row['month'], $row['year']);
			$dob = strtr($dob_format, array(
				'YYYY' => $row['year'],
				'YY' => substr($row['year'], 2, 2),
				'MM' => sprintf('%02d', $row['month']),
				'DD' => sprintf('%02d', $row['day']),
				'PNO' => $pno));
		}
		return $dob;
	}

	protected function _findHouseNumber($street_address) {
		if($this->_config['housenumbersplitting'] == true) {
			if(preg_match('/.*\s+(\d+.*)$/i', $street_address, $matches) == 1) {
				$house_number = $matches[1];
			}
			else {
				$house_number = '1';
			}
		}
		else {
			$house_number = '';
		}
		return $house_number;
	}

	protected function _cutOffHouseNumber($street_address) {
		if($this->_config['housenumbersplitting'] == true) {
			if(preg_match('/(.*)\s+(\d+.*)$/i', $street_address, $matches) == 1) {
				$street = $matches[1];
			}
			else {
				$street = $street_address;
			}
		}
		else {
			$street = $street_address;
		}
		return $street;
	}


	public function addOrdersStatusHistoryEntry($orders_id, $comments, $orders_status_id = null) {
		$query = "INSERT INTO orders_status_history (`orders_id`, `orders_status_id`, `date_added`, `customer_notified`, `comments`) ".
			"VALUES (:oid, :os_id, NOW(), 0, ':comments')";
		if($orders_status_id === null) {
			$orders_status_id = "(SELECT orders_status FROM orders WHERE orders_id = ".(int)$orders_id.")";
		}
		else {
			$orders_status_id = (int)$orders_status_id;
		}
		$query = strtr($query, array(':oid' => (int)$orders_id, ':os_id' => $orders_status_id, ':comments' => xtc_db_input($comments)));
		xtc_db_query($query);
	}

	/*
	** helpers for payment module
	*/

	public function consentRequired() {
		$countries = array(KlarnaCountry::DE, KlarnaCountry::AT);
		$reqd = in_array($this->_config['country'], $countries);
		return $reqd;
	}

	public function getConsentboxText() {
		$agb_url = GM_HTTP_SERVER.DIR_WS_CATALOG."popup_content.php?coID=3&lightbox_mode=1";
		switch($this->_config['country']) {
			case KlarnaCountry::DE:
				$text = 'Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode und einer Identitäts- und Bonitätsprüfung '.
							'erforderlichen Daten an Klarna bin ich einverstanden. Meine <a href="https://online.klarna.com/consent_de.yaws" target="_new">Einwilligung</a> '.
							'kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die '.
							'<a class="conditions_info_link lightbox_iframe" target="_new" href="'.$agb_url.'">AGB</a> des Händlers.';
				break;
			case KlarnaCountry::AT:
				$text = 'Mit der Datenverarbeitung der für die Abwicklung des Rechnungskaufes und einer Identitäts-und Bonitätsprüfung '.
					'erforderlichen Daten durch Klarna bin ich einverstanden. Meine <a href="https://online.klarna.com/consent_at.yaws" target="_new">Einwilligung</a> '.
					'kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die '.
					'<a class="conditions_info_link lightbox_iframe" target="_new" href="'.$agb_url.'">AGB</a> des Händlers.';
				break;
			default:
				$text = '<!-- no consentbox required -->';
		}
		return $text;
	}

	public function paymentSelectionCheck($order) {
		$countrydata = xtc_get_countriesList($_SESSION['customer_country_id'], true);
		$country_valid = $this->_isCountryAllowed($countrydata['countries_iso_code_2']);

		if(isset($_SESSION['customer_b2b_status']))
		{
			$is_b2b = $_SESSION['customer_b2b_status'] == true;
		}
		else
		{
			$company = trim($order->billing['company']).trim($order->delivery['company']);
			$is_b2b = !empty($company);
		}
		$b2b_check = !$is_b2b || in_array($countrydata['countries_iso_code_2'], array('DK', 'FI', 'NO', 'SE'));

		$sendto_eq_billto = $_SESSION['sendto'] === $_SESSION['billto'];

		/*
		$check = $country_valid && $b2b_check;
		*/
		$allow_select = $country_valid && $b2b_check && $sendto_eq_billto;
		$check = array(
			'allow_select' => $allow_select,
			'is_b2b' => $is_b2b,
		);
		return $check;
	}

	public function getMaximumPartpayAmount() {
		switch($this->_config['country']) {
			case KlarnaCountry::NL:
				$max_amount = 250;
				break;
			default:
				$max_amount = 999999;
		}
		return $max_amount;
	}

	protected function _isCountryAllowed($iso2) {
		if(!in_array(strtoupper($iso2), array_keys($this->getCountries()))) {
			// country not supported by Klarna
			return false;
		}
		$activated = $this->_config['activate_country_'.strtoupper($iso2)] == true;
		if(!$activated) {
			// country disallowed by configuration
			return false;
		}
		return true;
	}

	public function getInvoiceFee($formatted = false, $klarnacountry = null, $amount = null) {
		//$this->_log('ifee for '.$amount);
		$fee_config = $this->_config['invoice_fee_'.$this->getCountryIso($klarnacountry)];
		$fee = 0;
		if(preg_match('/^\d+(\.\d+)?$/', $fee_config) == 1) {
			$fee = $fee_config;
		}
		else {
			// 100:0.99;200:1.99;1000:2.99;10000:3.99
			if(preg_match('/(\d+:\d+.\d+)(;(\d+:\d+.\d+))+/', $fee_config) == 1) {
				$fee_list = explode(';', $fee_config);
				foreach($fee_list as $fee_entry) {
					list($max_amount, $entry_fee) = explode(':', $fee_entry);
					if($max_amount >= $amount) {
						$fee = $entry_fee;
						break;
					}
				}
			}
		}

		$fee = $GLOBALS['xtPrice']->xtcCalculateCurr($fee);

		if($formatted == true) {
			$fee = $this->_formatAmount($fee, true);
		}
		return $fee;
	}

	public function getMinimumMonthlyCost($amount) {
		$cheapest_pclass = $this->_klarna->getCheapestPClass($amount, KlarnaFlags::CHECKOUT_PAGE);
		$min_cost = KlarnaCalc::calc_monthly_cost($amount, $cheapest_pclass, KlarnaFlags::CHECKOUT_PAGE);
		return $min_cost;
	}

	public function getCDNLogoURL($type, $width = 200, $country = null) {
		if(empty($country)) {
			$country_id = isset($_SESSION['customer_country_id']) ? $_SESSION['customer_country_id'] : STORE_COUNTRY;
			$countrydata = xtc_get_countriesList($country_id, true);
			$country = strtoupper($countrydata['countries_iso_code_2']);
		}
		$locale = $this->getLocale($country);
		$url = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/' . $locale . '/basic/blue-black.png?width=' . $width;
		#$url = 'https://cdn.klarna.com/1.0/shared/image/generic/logo/' . $locale . '/basic/white.png?width=' . $width;
		return $url;
	}

	protected function getLocale($country)
	{
		switch(strtolower($country))
		{
			case 'se':
				$locale = 'sv_se';
				break;
			case 'no':
				$locale = 'nb_no';
				break;
			case 'fi':
				$locale = 'fi_fi';
				break;
			case 'dk':
				$locale = 'da_dk';
				break;
			case 'de':
			case 'at':
				$locale = 'de_de';
				break;
			case 'us':
				$locale = 'en_us';
				break;
			case 'gb':
				$locale = 'en_gb';
				break;
			default:
				$locale = 'en_gb';
		}
		return $locale;
	}

	public function getInvoiceConditionsLink($element_id, $charge = 0, $country = null) {
		$country_id = isset($_SESSION['customer_country_id']) ? $_SESSION['customer_country_id'] : STORE_COUNTRY;
		$countrydata = xtc_get_countriesList($_SESSION['customer_country_id'], true);
		$country = strtoupper($countrydata['countries_iso_code_2']);
		$merchant_id = $this->_config['merchant_id_'.$country];
		$link_code = <<<EOF
         <script type="text/javascript">
             var terms = new Klarna.Terms.Invoice({
                 el: '#element_id',   // The element id of the element you want to use.
                                     // Alternatively you could use an element directly, for example document.getElementsById('#my-link') or jQuery('span.invoice', '#terms').get(0);
                 eid: #merchant_id,             // Your merchant ID
                 country: '#iso2',      // country code (ISO 3166-1 alpha-2
                                     // or ISO 3166-1 alpha-3 code)
                 charge: '#fee'           // the invoice fee charged, defaulted to 0
             })
         </script>
EOF;
		$link_code = strtr($link_code, array(
			'#element_id' => $element_id,
			'#merchant_id' => $merchant_id,
			'#iso2' => strtolower($country),
			'#fee' => number_format($charge, 2, '.', ''),
			));
		$link_code = '<script src="https://cdn.klarna.com/public/kitt/core/v1.0/js/klarna.min.js" ></script>'.
		             '<script src="https://cdn.klarna.com/public/kitt/toc/v1.1/js/klarna.terms.min.js" ></script>'.
		             $link_code;
		return $link_code;
	}

	public function getAccountConditionsLink($element_id, $country = null) {
		$country_id = isset($_SESSION['customer_country_id']) ? $_SESSION['customer_country_id'] : STORE_COUNTRY;
		$countrydata = xtc_get_countriesList($country_id, true);
		$country = strtoupper($countrydata['countries_iso_code_2']);
		$merchant_id = $this->_config['merchant_id_'.$country];
		$link_code = <<<EOF
         <script type="text/javascript">
             new Klarna.Terms.Account({
                 el: '#element_id',   // The element id of the element you want to use.
                                     // Alternatively you could use an element directly, for example document.getElementsById('#my-link') or jQuery('span.invoice', '#terms').get(0);
                 eid: #merchant_id,             // Your merchant ID
                 country: '#iso2'       // country code (ISO 3166-1 alpha-2
                                     // or ISO 3166-1 alpha-3 code)
             })
         </script>
EOF;
		$link_code = strtr($link_code, array(
			'#element_id' => $element_id,
			'#merchant_id' => $merchant_id,
			'#iso2' => $country,
			));
		$link_code = '<script src="https://cdn.klarna.com/public/kitt/core/v1.0/js/klarna.min.js" ></script>'.
		             '<script src="https://cdn.klarna.com/public/kitt/toc/v1.1/js/klarna.terms.min.js" ></script>'.
		             $link_code;
		return $link_code;
	}

	public function getWidgetCode($amount, $is_product = false) {
		if(empty($this->_config['country']) === true) {
			return '';
		}
		if(isset($_SESSION['customer_country_id'])) {
			$country_id = $_SESSION['customer_country_id'];
		}
		else {
			$country_id = STORE_COUNTRY;
		}
		$countrydata = xtc_get_countriesList($country_id, true);
		$country_valid = $this->_isCountryAllowed($countrydata['countries_iso_code_2']);
		if($country_valid === false) {
			return '';
		}
		$country_active = $this->_config['activate_country_'.strtoupper($countrydata['countries_iso_code_2'])] == true;
		$partpay_active = strtoupper(MODULE_PAYMENT_KLARNA2_PARTPAY_STATUS) == 'TRUE';
		$show_in_checkout = $this->_config['show_checkout_partpay'] == true;
		$show_in_product = $this->_config['show_product_partpay'] == true;
		$amount_too_high = $amount > $this->getMaximumPartpayAmount();
		if(!$country_active || !$partpay_active || ($is_product && !$show_in_product) || !$show_in_checkout || $amount_too_high) {
			return '';
		}

		$widget = '<div style="width:210px; height:80px; margin: 5px;" '.
				  '     class="klarna-widget klarna-part-payment col-4 pull-right"' .
				  '     data-eid=":eid" ' .
				  '     data-locale=":locale"' .
				  '     data-price=":price"' .
				  '     data-layout=":layout"' .
				  '     data-invoice-fee=":fee">' .
				  '</div>' .
				  '<script async src="https://cdn.klarna.com/1.0/code/client/all.js"></script>';
		$widget = strtr($widget, array(
				':eid' => $this->_config['merchant_id_'.strtoupper($countrydata['countries_iso_code_2'])],
				':locale' => $this->getLocale($countrydata['countries_iso_code_2']),
				':price' => $amount,
				':layout' => 'pale-v2',
				':fee' => $this->_config['invoice_fee_'.strtoupper($countrydata['countries_iso_code_2'])],
			));

		return $widget;
	}

	public function getCreditWarning() {
		if($this->_config['country'] != KlarnaCountry::NL) {
			return '';
		};
		$warning = '<div class="klarna_credit_warning">';
		$warning .= '<br><img src="http://www.afm.nl/~/media/Images/wetten-regels/kredietwaarschuwing/balk_afm4-jpg.ashx">';
		$warning .= '</div>';
		return $warning;
	}

	public function formatAmount($amount) {
		return $this->_formatAmount($amount);
	}

	protected function _formatAmount($amount, $append_currency = false) {
		$decimal_places = 2;
		$decimal_point = ',';
		$thousands_separator = '.';
		$formatted = number_format($amount, $decimal_places, $decimal_point, $thousands_separator).' '.$this->getCurrencyString();
		return $formatted;
	}

	protected function _getSendToCountry() {
		if(empty($_SESSION['sendto'])) {
			return false;
		}
		$query = "SELECT entry_country_id FROM address_book WHERE address_book_id = :ab_id";
		$query = strtr($query, array(':ab_id' => (int)$_SESSION['sendto']));
		$result = xtc_db_query($query, 'db_link', false);
		$country_id = false;
		while($row = xtc_db_fetch_array($result)) {
			$country_id = $row['entry_country_id'];
		}
		return $country_id;
	}

	/* === auxilliary functions === */
	protected function _localEncodingIsLatin1() {
		$is_latin1 = strpos(strtolower($_SESSION['language_charset']), 'iso-8859-1') !== false;
		return $is_latin1;
	}

	/** transcode incoming string from UTF-8 to whatever the shop system is currenty using */
	public function transcodeInbound($string) {
		if($this->_localEncodingIsLatin1()) {
			$output = $string;
		}
		else {
			$output = utf8_encode($string);
		}
		return $output;
	}

	/** transcode incoming string from whatever the shop system is currenty using to UTF-8 */
	public function transcodeOutbound($string) {
		if($this->_localEncodingIsLatin1()) {
			$output = $string;
		}
		else {
			$output = utf8_decode($string);
		}
		return $output;
	}

	/* ================================================================================================================= */

	function removeOrder($order_id, $restock = true, $canceled = false, $reshipp = true) {
		if ($restock == true || $reshipp == true) {
			// BOF GM_MOD:
			$order_query = xtc_db_query("
										SELECT DISTINCT
											op.orders_products_id,
											op.products_id,
											op.products_quantity,
											opp.products_properties_combis_id,
											o.date_purchased
										FROM ".TABLE_ORDERS_PRODUCTS." op
											LEFT JOIN ".TABLE_ORDERS." o ON op.orders_id = o.orders_id
											LEFT JOIN orders_products_properties opp ON opp.orders_products_id = op.orders_products_id
										WHERE
											op.orders_id = '" . xtc_db_input($order_id) . "'
			");

			while ($order = xtc_db_fetch_array($order_query)) {
				if($restock == true) {
					/* BOF SPECIALS RESTOCK */
					$t_query = xtc_db_query("
											SELECT
												specials_date_added
											AS
												date
											FROM " .
												TABLE_SPECIALS . "
											WHERE
												specials_date_added < '" .	$order['date_purchased']	. "'
											AND
												products_id			= '" .	$order['products_id']		. "'
					");

					if((int)xtc_db_num_rows($t_query) > 0) {
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
	                }
	                else {
	                    $t_use_combis_quantity = 0;
	                }

	                if($t_combis_array_length == 0 || ($t_combis_array_length > 0 && $t_use_combis_quantity == 1)) {
	                    xtc_db_query("
	                                    UPDATE " .
	                                        TABLE_PRODUCTS . "
	                                    SET
	                                        products_quantity = products_quantity + ".$order['products_quantity']."
	                                    WHERE
	                                        products_id = '".$order['products_id']."'
	                    ");
	                }

	                xtc_db_query("
	                                UPDATE " .
	                                    TABLE_PRODUCTS . "
	                                SET
	                                    products_ordered = products_ordered - ".$order['products_quantity']."
	                                WHERE
	                                    products_id = '".$order['products_id']."'
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
					if(ATTRIBUTE_STOCK_CHECK == 'true') {
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

						while($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes)) {
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

							if(xtc_db_num_rows($gm_get_attributes_id) == 1) {
								$gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

								xtc_db_query("
												UPDATE
													products_attributes
												SET
													attributes_stock = attributes_stock + ".$order['products_quantity']."
												WHERE
													products_attributes_id = '" . $gm_attributes_id['products_attributes_id'] . "'
								");
							}
						}
					}
					// EOF GM_MOD
				}

				// BOF GM_MOD products_shippingtime:
				if($reshipp == true) {
					require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');
					set_shipping_status($order['products_id']);
				}
				// BOF GM_MOD products_shippingtime:
			}
		}

		if(!$canceled) {
			xtc_db_query("delete from ".TABLE_ORDERS. " where orders_id = '".xtc_db_input($order_id)."'");

			$t_orders_products_ids_sql = 'SELECT orders_products_id FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . xtc_db_input($order_id) . '"';
			$t_orders_products_ids_result = xtc_db_query($t_orders_products_ids_sql);
			while($t_orders_products_ids_array = xtc_db_fetch_array($t_orders_products_ids_result))	{
				xtc_db_query("DELETE FROM orders_products_quantity_units WHERE orders_products_id = '" . (int)$t_orders_products_ids_array['orders_products_id'] . "'");
			}

			xtc_db_query("delete from ".TABLE_ORDERS_PRODUCTS				. " where orders_id = '".xtc_db_input($order_id)."'");
			xtc_db_query("delete from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES	. " where orders_id = '".xtc_db_input($order_id)."'");
			xtc_db_query("delete from ".TABLE_ORDERS_STATUS_HISTORY			. " where orders_id = '".xtc_db_input($order_id)."'");
			xtc_db_query("delete from ".TABLE_ORDERS_TOTAL					. " where orders_id = '".xtc_db_input($order_id)."'");
			xtc_db_query("DELETE FROM banktransfer WHERE orders_id = '" . (int)$order_id . "'");

			// BOF GM_MOD GX-Customizer:
			require_once DIR_FS_CATALOG.'/gm/modules/gm_gprint_tables.php';
			require_once DIR_FS_CATALOG.'/gm/classes/GMJSON.php';
			require_once DIR_FS_CATALOG.'/gm/classes/GMGPrintOrderElements.php';
			require_once DIR_FS_CATALOG.'/gm/classes/GMGPrintOrderSurfaces.php';
			require_once DIR_FS_CATALOG.'/gm/classes/GMGPrintOrderSurfacesManager.php';
			require_once DIR_FS_CATALOG.'/gm/classes/GMGPrintOrderManager.php';

			$coo_gm_gprint_order_manager = new GMGPrintOrderManager();
			$coo_gm_gprint_order_manager->delete_order($order_id);
		}
	}

}

class GMKlarnaException extends Exception {}

MainFactory::load_origin_class('GMKlarna');