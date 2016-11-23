<?php
/* --------------------------------------------------------------
	GMPayOne.php 2016-07-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once DIR_FS_CATALOG.'ext/payone/php/Payone/Bootstrap.php';

class GMPayOne_ORIGIN {
	const CONFIG_PREFIX = 'PAYONE_';
	const CONFIG_STORAGE_NAMESPACE = 'modules/payment/payone/p1config';
	protected $_logger;
	protected $_client_api_url;
	protected $_frontend_url;
	protected $_server_api_url;

	public function __construct() {
		$this->_moveOldLogfiles();
		$this->_client_api_url = 'https://secure.pay1.de/client-api/';
		$this->_frontend_url = 'https://secure.pay1.de/frontend/';
		$this->_server_api_url = 'https://api.pay1.de/post-gateway/';
		$this->_logger = new FileLog('payment-payone', true);
		$this->_txt = new LanguageTextManager('payone', $_SESSION['languages_id']);
		$bootstrap = new Payone_Bootstrap();
		$bootstrap->init();
	}

	protected function _moveOldLogfiles()
	{
		if(file_exists(DIR_FS_CATALOG.'logfiles/payone_sdk_api.log'))
		{
			rename(DIR_FS_CATALOG.'logfiles/payone_sdk_api.log', DIR_FS_CATALOG.'logfiles/payone_sdk_api-'.LogControl::get_secure_token().'.log');
		}
		if(file_exists(DIR_FS_CATALOG.'logfiles/payone_sdk_transaction.log'))
		{
			rename(DIR_FS_CATALOG.'logfiles/payone_sdk_transaction.log', DIR_FS_CATALOG.'logfiles/payone_sdk_transaction-'.LogControl::get_secure_token().'.log');
		}
	}

	public function log($message) {
		$microtime = microtime(true);
		$timestamp = date('Ymd_His', floor($microtime));
		$timestamp .= '.' . sprintf('%03d', (int)(($microtime - floor($microtime)) * 1000));
		$this->_logger->write($timestamp.' | '.$message."\n");
	}

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

	public function getPayoneConfig() {
		$p1config = new Payone_Config();
		$p1config->setValue('api/default/protocol/loggers/Payone_Protocol_Logger_GambioLog/mode', 'api');
		$p1config->setValue('api/default/protocol/loggers/Payone_Protocol_Logger_Log4php/filename', DIR_FS_CATALOG.'logfiles/payone_sdk_api-'.LogControl::get_secure_token().'.log');
		$p1config->setValue('api/default/protocol/loggers/Payone_Protocol_Logger_Log4php/max_file_size', '5MB');
		$p1config->setValue('transaction_status/default/protocol/loggers/Payone_Protocol_Logger_Log4php/filename', DIR_FS_CATALOG.'logfiles/payone_sdk_transaction-'.LogControl::get_secure_token().'.log');
		$p1config->setValue('transaction_status/default/protocol/loggers/Payone_Protocol_Logger_GambioLog/mode', 'transactions');
		return $p1config;
	}

	public function getStatusNames() {
		$names = array('approved', 'appointed', 'capture', 'paid', 'underpaid', 'cancelation', 'refund', 'debit', 'transfer', 'reminder', 'vauthorization', 'vsettlement', 'invoice');
		return $names;
	}

	public function getPaymentTypes() {
		// genre => types
		$payment_types = array(
			'creditcard' => array('visa', 'mastercard', 'amex', 'cartebleue', 'dinersclub', 'discover', 'jcb', 'maestro'),
			'onlinetransfer' => array('sofortueberweisung', 'giropay', 'eps', 'pfefinance', 'pfcard', 'ideal'),
			'ewallet' => array('paypal'),
			'accountbased' => array('lastschrift', 'openinvoice', 'prepay', 'cod'),
			'installment' => array('commerzfinanz', /* 'klarnainstallment' */),
			'safeinv' => array('billsafe', 'payolutioninvoicing', /*'klarnainvoice'*/),
		);
		return $payment_types;
	}

	public function getBankGroups() {
		$bankgroups = array(
			'eps' => array(
				'ARZ_OVB' => 'Volksbanken',
				'ARZ_BAF' => 'Bank für Ärzte und Freie Berufe',
				'ARZ_NLH' => 'Niederösterreichische Landes-Hypo',
				'ARZ_VLH' => 'Vorarlberger Landes-Hypo',
				'ARZ_BCS' => 'Bankhaus Carl Spängler & Co. AG',
				'ARZ_HTB' => 'Hypo Tirol',
				'ARZ_HAA' => 'Hypo Alpe Adria',
				'ARZ_IKB' => 'Investkreditbank',
				'ARZ_OAB' => 'Österreichische Apothekerbank',
				'ARZ_IMB' => 'Immobank',
				'ARZ_GRB' => 'Gärtnerbank',
				'ARZ_HIB' => 'HYPO Investment',
				'BA_AUS' => 'Bank Austria',
				'BAWAG_BWG' => 'BAWAG',
				'BAWAG_PSK' => 'PSK Bank',
				'BAWAG_ESY' => 'easybank',
				'BAWAG_SPD' => 'Sparda Bank',
				'SPARDAT_EBS' => 'Erste Bank',
				'SPARDAT_BBL' => 'Bank Burgenland',
				'RAC_RAC' => 'Raiffeisen',
				'HRAC_OOS' => 'Hypo Oberösterreich',
				'HRAC_SLB' => 'Hypo Salzburg',
				'HRAC_STM' => 'Hypo Steiermark',
			),
			'ideal' => array(
				'ABN_AMRO_BANK' => 'ABN Amro',
				'RABOBANK' => 'Rabobank',
				'FRIESLAND_BANK' => 'Friesland Bank',
				'ASN_BANK' => 'ASN Bank',
				'SNS_BANK' => 'SNS Bank',
				'TRIODOS_BANK' => 'Triodos',
				'SNS_REGIO_BANK' => 'SNS Regio Bank',
				'ING_BANK' => 'ING',
			),
		);
		return $bankgroups;
	}

	public function getSepaCountries() {
		# countries_iso_code_2 == BIC code
		$sepa_countries = array(
			array('countries_name' => 'Austria', 'countries_iso_code_2' => 'AT', 'countries_iban_code' => 'AT', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Belgium', 'countries_iso_code_2' => 'BE', 'countries_iban_code' => 'BE', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Bulgaria', 'countries_iso_code_2' => 'BG', 'countries_iban_code' => 'BG', 'countries_currency_code' => 'BGN'),
			array('countries_name' => 'Saint Barthelemy', 'countries_iso_code_2' => 'BL', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Switzerland', 'countries_iso_code_2' => 'CH', 'countries_iban_code' => 'CH', 'countries_currency_code' => 'CHF'),
			array('countries_name' => 'Cyprus', 'countries_iso_code_2' => 'CY', 'countries_iban_code' => 'CY', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Czech Republic', 'countries_iso_code_2' => 'CZ', 'countries_iban_code' => 'CZ', 'countries_currency_code' => 'CZK'),
			array('countries_name' => 'Germany', 'countries_iso_code_2' => 'DE', 'countries_iban_code' => 'DE', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Denmark', 'countries_iso_code_2' => 'DK', 'countries_iban_code' => 'DK', 'countries_currency_code' => 'DKK'),
			array('countries_name' => 'Estonia', 'countries_iso_code_2' => 'EE', 'countries_iban_code' => 'EE', 'countries_currency_code' => 'EUR'),
			# array('countries_name' => 'Canary Islands', 'countries_iso_code_2' => 'ES', 'countries_iban_code' => 'ES', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Spain', 'countries_iso_code_2' => 'ES', 'countries_iban_code' => 'ES', 'countries_currency_code' => 'EUR'),
			# array('countries_name' => 'Aland Islands', 'countries_iso_code_2' => 'FI', 'countries_iban_code' => 'FI', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Finland', 'countries_iso_code_2' => 'FI', 'countries_iban_code' => 'FI', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'France', 'countries_iso_code_2' => 'FR', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'United Kingdom', 'countries_iso_code_2' => 'GB', 'countries_iban_code' => 'GB', 'countries_currency_code' => 'GBP'),
			array('countries_name' => 'French Guiana', 'countries_iso_code_2' => 'GF', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Gibraltar', 'countries_iso_code_2' => 'GI', 'countries_iban_code' => 'GI', 'countries_currency_code' => 'GIP'),
			array('countries_name' => 'Guadeloupe', 'countries_iso_code_2' => 'GP', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Greece', 'countries_iso_code_2' => 'GR', 'countries_iban_code' => 'GR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Croatia2', 'countries_iso_code_2' => 'HR', 'countries_iban_code' => 'HR', 'countries_currency_code' => 'HRK'),
			array('countries_name' => 'Hungary', 'countries_iso_code_2' => 'HU', 'countries_iban_code' => 'HU', 'countries_currency_code' => 'HUF'),
			array('countries_name' => 'Ireland', 'countries_iso_code_2' => 'IE', 'countries_iban_code' => 'IE', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Iceland', 'countries_iso_code_2' => 'IS', 'countries_iban_code' => 'IS', 'countries_currency_code' => 'ISK'),
			array('countries_name' => 'Italy', 'countries_iso_code_2' => 'IT', 'countries_iban_code' => 'IT', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Liechtenstein', 'countries_iso_code_2' => 'LI', 'countries_iban_code' => 'LI', 'countries_currency_code' => 'CHF'),
			array('countries_name' => 'Lithuania', 'countries_iso_code_2' => 'LT', 'countries_iban_code' => 'LT', 'countries_currency_code' => 'LTL'),
			array('countries_name' => 'Luxembourg', 'countries_iso_code_2' => 'LU', 'countries_iban_code' => 'LU', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Latvia', 'countries_iso_code_2' => 'LV', 'countries_iban_code' => 'LV', 'countries_currency_code' => 'LVL'),
			array('countries_name' => 'Monaco', 'countries_iso_code_2' => 'MC', 'countries_iban_code' => 'MC', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Saint Martin (French part)', 'countries_iso_code_2' => 'MF', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Martinique', 'countries_iso_code_2' => 'MQ', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Malta', 'countries_iso_code_2' => 'MT', 'countries_iban_code' => 'MT', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Netherlands', 'countries_iso_code_2' => 'NL', 'countries_iban_code' => 'NL', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Norway', 'countries_iso_code_2' => 'NO', 'countries_iban_code' => 'NO', 'countries_currency_code' => 'NOK'),
			array('countries_name' => 'Poland', 'countries_iso_code_2' => 'PL', 'countries_iban_code' => 'PL', 'countries_currency_code' => 'PLN'),
			array('countries_name' => 'Saint Pierre and Miquelon', 'countries_iso_code_2' => 'PM', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			# array('countries_name' => 'Azores', 'countries_iso_code_2' => 'PT', 'countries_iban_code' => 'PT', 'countries_currency_code' => 'EUR'),
			# array('countries_name' => 'Madeira', 'countries_iso_code_2' => 'PT', 'countries_iban_code' => 'PT', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Portugal', 'countries_iso_code_2' => 'PT', 'countries_iban_code' => 'PT', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Reunion', 'countries_iso_code_2' => 'RE', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Romania', 'countries_iso_code_2' => 'RO', 'countries_iban_code' => 'RO', 'countries_currency_code' => 'RON'),
			array('countries_name' => 'Sweden', 'countries_iso_code_2' => 'SE', 'countries_iban_code' => 'SE', 'countries_currency_code' => 'SEK'),
			array('countries_name' => 'Slovenia', 'countries_iso_code_2' => 'SI', 'countries_iban_code' => 'SI', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Slovakia', 'countries_iso_code_2' => 'SK', 'countries_iban_code' => 'SK', 'countries_currency_code' => 'EUR'),
			array('countries_name' => 'Mayotte', 'countries_iso_code_2' => 'YT', 'countries_iban_code' => 'FR', 'countries_currency_code' => 'EUR'),
		);
		return $sepa_countries;
	}

	protected function _getDefaultConfig() {
		$config = array(
			'orders_status' => array(
				'tmp' => '1',
				/*
				'pending' => '1',
				'paid' => '1',
				'denied' => '1',
				*/
			),

			'global' => array(
				'merchant_id' => 'no_id',
				'portal_id' => 'no_id',
				'subaccount_id' => 'no_id',
				'key' => 'no_key',
				'operating_mode' => 'test',
				'authorization_method' => 'auth',
				'send_cart' => 'false',
			),

			'address_check' => array(
				'active' => 'false',
				'operating_mode' => 'test', // test | live
				'billing_address' => 'none', // none | basic | person
				'delivery_address' => 'none', // none | basic | person
				'automatic_correction' => 'no', // no | yes | user
				'error_mode' => 'abort', // abort | reenter | check | continue
				'min_cart_value' => '0',
				'max_cart_value' => '10000',
				'validity' => '3',
				'error_message' => 'Ihre Eingabe war nicht korrekt - {payone_error}',
				'pstatus' => array(
					'nopcheck' => 'green',
					'fullnameknown' => 'green',
					'lastnameknown' => 'green',
					'nameunknown' => 'green',
					'nameaddrambiguity' => 'green',
					'undeliverable' => 'green',
					'dead' => 'green',
					'postalerror' => 'green',
				),
			),

			'credit_risk' => array(
				'active' => 'false',
				'operating_mode' => 'test',
				'timeofcheck' => 'before',
				'typeofcheck' => 'iscorehard',
				'newclientdefault' => 'green',
				'validity' => '3',
				'min_cart_value' => '100',
				'max_cart_value' => '5000',
				'checkforgenre' => array(),
				'error_mode' => 'continue',
				'notice' => array(
					'active' => 'true',
					'text' => 'Es wird eine Bonitätsabfrage durchgeführt.',
				),
				'confirmation' => array(
					'active' => 'true',
					'text' => 'Möchten Sie dem zustimmen?',
				),
				'abtest' => array(
					'active' => 'false',
					'value' => '3',
				),
			),
		);

		foreach($this->getStatusNames() as $sname) {
			$config['orders_status'][$sname] = '1';
		}

		return $config;
	}

	protected function _getGenreModuleMapping() {
		$mapping = array(
			'creditcard' => 'cc',
			'onlinetransfer' => 'otrans',
			'ewallet' => 'wlt',
			'accountbased' => 'account',
			'installment' => 'installment',
		);
		return $mapping;
	}

	protected function _getPaymentGenreDefaultConfig($genre) {
		$payment_types = $this->getPaymentTypes();
		$valid_genres = array_keys($payment_types);
		if(!in_array($genre, $valid_genres)) {
			throw new Exception('invalid payment genre '.$genre);
		}
		$default_config = $this->_getDefaultConfig();
		$configuration = array(
			'genre' => $genre,
			'global_override' => 'false',
			'global' => $default_config['global'],
			'name' => $this->get_text('paymentgenre_'.$genre) .' '.uniqid(),
			'active' => 'false',
			'order' => 0,
			'min_cart_value' => 0,
			'max_cart_value' => 5000,
			'operating_mode' => 'test',
			'countries' => array(),
			'allow_red' => 'false',
			'allow_yellow' => 'false',
			'allow_green' => 'true',
			'genre_specific' => array(),
		);

		foreach($payment_types[$genre] as $pt) {
			$configuration['types'][$pt]['active'] = 'false';
			$configuration['types'][$pt]['name'] = $this->get_text('paymenttype_'.$pt);
		}

		switch($genre) {
			case 'creditcard':
				$configuration['genre_specific']['check_cav'] = 'false';
				$configuration['genre_specific']['inputstyle']['cardpan']['type'] = 'text';
				$configuration['genre_specific']['inputstyle']['cardpan']['size_min'] = '20';
				$configuration['genre_specific']['inputstyle']['cardpan']['size_max'] = '20';
				$configuration['genre_specific']['inputstyle']['cardpan']['iframe'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardpan']['iframe_width'] = '100';
				$configuration['genre_specific']['inputstyle']['cardpan']['iframe_height'] = '20';
				$configuration['genre_specific']['inputstyle']['cardpan']['style'] = 'user';
				$configuration['genre_specific']['inputstyle']['cardpan']['css'] = 'font-size: 1em; border: 1px solid #000;';

				$configuration['genre_specific']['inputstyle']['cardcvc2']['type'] = 'password';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['size_min'] = '4';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['size_max'] = '4';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['iframe'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['iframe_width'] = '100';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['iframe_height'] = '20';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['style'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardcvc2']['css'] = '';

				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['type'] = 'select';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['size_min'] = '2';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['size_max'] = '2';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['iframe'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['iframe_width'] = '50';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['iframe_height'] = '20';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['style'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardexpiremonth']['css'] = '';

				$configuration['genre_specific']['inputstyle']['cardexpireyear']['type'] = 'select';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['size_min'] = '20';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['size_max'] = '20';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['iframe'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['iframe_width'] = '80';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['iframe_height'] = '20';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['style'] = 'standard';
				$configuration['genre_specific']['inputstyle']['cardexpireyear']['css'] = '';

				$configuration['genre_specific']['inputstyle']['default-input-css'] = 'font-size: 14px; border: 1px solid #000; width: 175px;';
				$configuration['genre_specific']['inputstyle']['default-select-css'] = 'font-size: 14px; border: 1px solid #000;';
				$configuration['genre_specific']['inputstyle']['default-iframe_width'] = '450';
				$configuration['genre_specific']['inputstyle']['default-iframe_height'] = '26';

				break;
			case 'accountbased':
				$configuration['genre_specific']['check_bankdata'] = 'none';
				$configuration['genre_specific']['sepa_account_countries'] = array();
				$configuration['genre_specific']['sepa_display_ktoblz'] = 'false';
				$configuration['genre_specific']['sepa_use_managemandate'] = 'false';
				$configuration['genre_specific']['sepa_download_pdf'] = 'false';
				break;
			case 'onlinetransfer':
			case 'ewallet':
			case 'installment':
				break;
			case 'safeinv':
				$configuration['genre_specific']['payolution_b2b_enabled'] = 'false';
				$configuration['genre_specific']['payolution_company_name'] = @constant('COMPANY_NAME');
				$configuration['genre_specific']['payolution_account_holder'] = '--';
				$configuration['genre_specific']['payolution_bank_name'] = '--';
				$configuration['genre_specific']['payolution_iban'] = '--';
				$configuration['genre_specific']['payolution_bic'] = '--';
				$configuration['genre_specific']['payolution_due_days'] = '14';
				break;
		}

		return $configuration;
	}

	public function getConfig($identifier = null) {
		$coo_confstore = MainFactory::create_object('ConfigurationStorage', array(self::CONFIG_STORAGE_NAMESPACE));
		$configuration = $coo_confstore->get_all_tree();
		$default_config = $this->_getDefaultConfig();
		$configuration = $this->mergeConfigs($default_config, $configuration);
		foreach($configuration as $topkey => $data)
		{
			if(strpos($topkey, 'paymentgenre') === false) {
				continue;
			}
			$genre_default_config = $this->_getPaymentGenreDefaultConfig($data['genre']);
			$configuration[$topkey] = array_replace_recursive($genre_default_config, $data);
		}
		if(!empty($identifier) && array_key_exists($identifier, $configuration)) {
			return $configuration[$identifier];
		}
		else {
			return $configuration;
		}
	}

	public function getGenresConfig() {
		$config = $this->getConfig();
		$genre_configs = array();
		$order_array = array();
		foreach($config as $topkey => $data) {
			if(strpos($topkey, 'paymentgenre') === false) {
				continue;
			}
			$order_key = sprintf('%05d_%s', $data['order'], $topkey);
			$order_array[$order_key] = $topkey;
		}
		ksort($order_array);
		foreach($order_array as $sort_key => $top_key) {
			$genre_configs[$top_key] = $config[$top_key];
		}
		return $genre_configs;
	}

	public function setConfig($configuration) {
		$coo_confstore = MainFactory::create_object('ConfigurationStorage', array(self::CONFIG_STORAGE_NAMESPACE));
		$coo_confstore->delete_all();
		$coo_confstore->set_all($configuration);
		$this->adjustSortOrders();
	}

	/**
	* adjust sort order of payment modules to reflect PayOne configuration
	*/
	public function adjustSortOrders() {
		$gconfig = $this->getGenresConfig();
		$module_mapping = $this->_getGenreModuleMapping();
		$payone_modules_sort_order = array();
		foreach($gconfig as $gconfig_entry) {
			$module = $module_mapping[$gconfig_entry['genre']];
			$query = "UPDATE `configuration` SET `configuration_value` = ".(int)$gconfig_entry['order']." WHERE `configuration_key` = 'MODULE_PAYMENT_PAYONE_".strtoupper($module)."_SORT_ORDER'";
			xtc_db_query($query);
			$payone_modules_sort_order[$module] = $gconfig_entry['order'];
		}

		$modules = explode(';', @constant('MODULE_PAYMENT_INSTALLED'));
		$modules_sorttmp = array();
		foreach($modules as $pmodule)
		{
			$pmodule_fullpath = DIR_FS_CATALOG.'includes/modules/payment/'.basename($pmodule);
			if(file_exists($pmodule_fullpath))
			{
				$pmodule_class = basename($pmodule, '.php');
				if(strpos($pmodule, 'payone') !== false)
				{
					$pmodule_sort_order = $payone_modules_sort_order[$pmodule_class];
				}
				else
				{
					require_once $pmodule_fullpath;
					$module_object = new $pmodule_class;
					$pmodule_sort_order = $module_object->sort_order;
				}
				$modules_sorttmp[] = sprintf('%+06d##%s', $pmodule_sort_order, basename($pmodule));
			}
			else
			{
				continue;
			}
		}
		sort($modules_sorttmp, SORT_NUMERIC);
		$modules_payment_installed_new_array = array();
		foreach($modules_sorttmp as $smodule)
		{
			$modules_payment_installed_new_array[] = substr($smodule, 8);
		}
		$modules_payment_installed_new = implode(';', $modules_payment_installed_new_array);
		xtc_db_query("UPDATE `configuration` SET `configuration_value` = '".xtc_db_input($modules_payment_installed_new)."' WHERE `configuration_key` = 'MODULE_PAYMENT_INSTALLED'");
	}

	public function mergeConfigs($old_config, $new_config) {
		$old_keys = array_keys($old_config);
		if($old_keys[0] === 0)
		{
			# special case: numerically indexed array, e.g. list of countries
			$merged = array_values(array_unique($new_config));
		}
		else
		{
			$merged = array();
			foreach($old_config as $key => $value) {
				if(empty($new_config[$key]) && !is_numeric($new_config[$key])) {
					if(array_key_exists($key, $new_config)) {
						if(is_array($value)) {
							$merged[$key] = array();
						}
						else if($value == 'true' || $value == 'false') {
							$merged[$key] = 'false';
						}
						else {
							$merged[$key] = '';
						}
					}
					else {
						if($value == 'true' || $value == 'false') {
							$merged[$key] = 'false';
						}
						else {
							$merged[$key] = $value;
						}
					}
				}
				else {
					if(is_array($value)) {
						$merged[$key] = $this->mergeConfigs($value, $new_config[$key]);
					}
					else if($value == 'true' || $value == 'false') {
						$merged[$key] = $new_config[$key] == 'true' ? 'true' : 'false';
					}
					else {
						$merged[$key] = $new_config[$key];
					}
				}

				if($value == 'true' || $value == 'false') {
					$merged[$key] = $new_config[$key] == 'true' ? 'true' : 'false';
				}
			}
			foreach($new_config as $nkey => $nvalue) {
				if(!array_key_exists($nkey, $merged)) {
					$merged[$nkey] = $nvalue;
				}
			}
		}
		return $merged;
	}

	protected function _flattenArray($input, $prefix = '') {
		$divider = '/';
		if(!empty($prefix)) {
			$prefix .= $divider;
		}
		$output = array();
		foreach($input as $key => $value) {
			if(is_array($value)) {
				if(empty($value)) {
					$output[$prefix.$key] = '';
				}
				else {
					$flattened = $this->_flattenArray($value, $key);
					foreach($flattened as $fkey => $fvalue) {
						$output[$prefix.$fkey] = $fvalue;
					}
				}
			}
			else {
				$output[$prefix.$key] = $value;
			}
		}
		return $output;
	}

	protected function _inflateArray($input) {
		$divider = '/';
		$output = array();
		foreach($input as $key => $value) {
			$keys = explode($divider, $key);
			$subarray =& $output;
			while(count($keys) > 1) {
				$subkey = array_shift($keys);
				if(!is_array($subarray[$subkey])) {
					$subarray[$subkey] = array();
				}
				$subarray =& $subarray[$subkey];
			}
			$final_key = array_shift($keys);
			$subarray[$final_key] = $value;
		}
		return $output;
	}

	public function dumpConfig() {
		$t_filename = DIR_FS_CATALOG.'cache/payone-config-'.FileLog::get_secure_token().'.cfg';
		$t_fh = @fopen($t_filename, 'w');
		if($t_fh == false)
		{
			return false;
		}
		$config_array = $this->getConfig();
		$config_flat_array = $this->_flattenArray($config_array);
		foreach($config_flat_array as $cfg_key => $cfg_value)
		{
			fwrite($t_fh, $cfg_key. "\t". $cfg_value ."\n");
		}
		fclose($t_fh);
		return $t_filename;
	}

	public function addPaymentGenreConfig($genre) {
		$genre_config = $this->_getPaymentGenreDefaultConfig($genre);
		$identifier = 'paymentgenre_'.uniqid();
		$configuration = $this->getConfig();
		$configuration[$identifier] = $genre_config;
		$this->setConfig($configuration);
	}

	public function getPaymentGenreIdentifiers() {
		$configuration = $this->getConfig();
		$config_identifiers = array_keys($configuration);
		$paymentgenre_identifiers = array();
		foreach($config_identifiers as $ci) {
			if(strpos($ci, 'paymentgenre_') === 0) {
				$paymentgenre_identifiers[] = $ci;
			}
		}
		return $paymentgenre_identifiers;
	}

	public function getTypesForGenre($genre_identifier) {
		$pgenre = $this->getConfig($genre_identifier);
		$types = array();
		if($pgenre['genre'] == 'creditcard') {
			$cctypes = array('visa' => 'V', 'mastercard' => 'M', 'amex' => 'A', 'cartebleue' => 'B', 'dinersclub' => 'D', 'discover' => 'C', 'jcb' => 'J', 'maestro' => 'O');
			foreach($cctypes as $cctype => $shorttype) {
				if($pgenre['types'][$cctype]['active'] != 'true') {
					continue;
				}
				$types[] = array(
					'typekey' => $cctype,
					'shorttype' => $shorttype,
					'typename' => $pgenre['types'][$cctype]['name'],
				);
			}
		}

		// todo: other genres

		return $types;
	}


	/* ============================================================================================================ */

	public function getStandardParameters($request = null, $config_override = null) {
		$config = $this->getConfig('global');
		if($config_override != null) {
			$config = array_merge($config, $config_override);
		}
		$params = array(
			'mid' => $config['merchant_id'],
			'portalid' => $config['portal_id'],
			'aid' => $config['subaccount_id'],
			'mode' => $config['operating_mode'],
			//'request' => $request,
			'responsetype' => 'REDIRECT',
			//'hash' => '',
			//'successurl' => GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PROCESS,
			//'errorurl' => GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PROCESS,
			'encoding' => 'UTF-8',
			'language' => strtolower($_SESSION['language_code']),
			'integrator_name' => 'Gambio',
		);
		if($request !== null) {
			$params['request'] = $request;
		}
		return $params;
	}

	public function computeHash($params, $key) {
		$hash_keys = array('access_aboperiod', 'access_aboprice', 'access_canceltime', 'access_expiretime', 'access_period', 'access_price', 'access_starttime',
			'access_vat', 'accesscode', 'accessname', 'addresschecktype', 'aid', 'amount', 'backurl', 'booking_date', 'checktype', 'clearingtype', 'consumerscoretype',
			'currency', 'customerid', 'document_date', 'due_time', 'eci', 'encoding', 'errorurl', 'exiturl', 'invoice_deliverymode', 'invoiceappendix',
			'invoiceid', 'mid', 'mode', 'narrative_text', 'param', 'portalid', 'productid', 'reference', 'request', 'responsetype', 'settleaccount',
			'settleperiod', 'settletime', 'storecarddata', 'successurl', 'userid', 'vaccountname', 'vreference');
		$varnum_hash_keys = array('de[\d+]', 'id[\d+]', 'no[\d+]', 'pr[\d+]', 'ti[\d+]', 'va[\d+]');
		$hash_data = array();
		foreach($params as $pkey => $pvalue) {
			if(in_array($pkey, $hash_keys) || preg_match('/^(de|id|no|pr|ti|va)\[\d+\]$/', $pkey) == 1) {
				$hash_data[$pkey] = $pvalue;
			}
		}
		ksort($hash_data);
		$hash_string = implode('', $hash_data);
		$hash_string .= $key;
		//$this->log("computing hash for $hash_string");
		$hash = md5($hash_string);
		return $hash;
	}

	public function getFormActionURL() {
		return $this->_client_api_url;
	}

	/* ============================================================================================================ */

	public function retrieveSepaMandate($file_reference)
	{
		$global_config = $this->getConfig('global');
		$standard_parameters = $this->getStandardParameters();
		$builder = new Payone_Builder($this->getPayoneConfig());
		$service = $builder->buildServiceManagementGetFile();
		$request_data = array
			(
				'key' => $global_config['key'],
				'file_reference' => $file_reference,
				'file_type' => 'SEPA_MANDATE',
				'file_format' => 'PDF',
			);
		$params = array_merge($standard_parameters, $request_data);
		$request = new Payone_Api_Request_GetFile($params);
		try
		{
			$result = $service->getFile($request);
		}
		catch(Payone_Api_Exception_InvalidResponse $e)
		{
			$this->log('Exception in getFile: '.$e->getMessage());
			return false;
		}
		# $this->log('getFile result:'.PHP_EOL.print_r($result, true));
		if($result instanceof Payone_Api_Response_Management_GetFile)
		{
			$t_pdf_data = $result->getRawResponse();
			$mandate_filename = 'sepa_mandate_'.$_SESSION['customer_id'].'_'.md5($file_reference).'.pdf';
			$bytes_written = file_put_contents(DIR_FS_DOWNLOAD_PUBLIC.$mandate_filename, $t_pdf_data);
			if($bytes_written === false) {
				$this->log('ERROR writing mandate file '.DIR_FS_DOWNLOAD_PUBLIC.$mandate_filename);
				return false;
			}
			else
			{
				$this->log('SEPA mandate written to '.$mandate_filename.' ('.$bytes_written.' bytes)');
				return $mandate_filename;
			}

		}
		else
		{
			return false;
		}
	}

	/* ============================================================================================================ */

	/**
	* returns payment genres which are suitable for the current checkout.
	* NOTE: This method assumes to be called from within Gambio's checkout, it uses $_SESSION data!
	*/
	public function getAvailablePaymentGenres() {
		$config = $this->getGenresConfig();
		$available = array();

		$cart_value = $_SESSION['cart']->show_total();
		if(!empty($_SESSION['billto']))
		{
			$billto_address = $this->_getAddressBookEntry($_SESSION['billto'], $_SESSION['customer_id']);

			foreach($config as $topkey => $pgconfig) {
				if($pgconfig['active'] != 'true') {
					$this->log("$topkey not active");
					continue;
				}
				if($pgconfig['min_cart_value'] > $cart_value || $pgconfig['max_cart_value'] < $cart_value) {
					continue;
				}
				if(!is_array($pgconfig['countries']) || !in_array($billto_address['countries_iso_code_2'], $pgconfig['countries'])) {
					$this->log("$topkey country ".$billto_address['countries_iso_code_2']." not activated");
					continue;
				}
				$available[$topkey] = $pgconfig;
			}
		}

		return $available;
	}

	protected function _getAddressBookEntry($ab_id, $customers_id = null) {
		$query = "SELECT ab.*, c.customers_telephone, DATE(c.customers_dob) AS dob_date, cy.* FROM `address_book` ab
			left join customers c on c.customers_id = ab.customers_id
			left join countries cy on cy.countries_id = ab.entry_country_id
			WHERE ab.address_book_id = :ab_id";
		if($customers_id !== null) {
			$query .= " AND c.customers_id = :customers_id";
		}

		$query = strtr($query, array(':ab_id' => (int)$ab_id, ':customers_id' => (int)$customers_id));
		/* N.B.: we need uncached data here because the database entry may have changed within the current request */
		$result = xtc_db_query($query, 'db_link', false);
		$entry = false;
		while($row = xtc_db_fetch_array($result)) {
			$entry = $row;
		}
		return $entry;
	}

	public function getAddressBookEntry($ab_id, $customer_id = null) {
		return $this->_getAddressBookEntry($ab_id, $customer_id);
	}

	public function getAddressHash($ab_id) {
		$hash_fields = array('entry_gender', 'entry_company', 'entry_firstname', 'entry_lastname', 'entry_street_address', 'entry_suburb',
			'entry_postcode', 'entry_city', 'entry_state', 'entry_country_id', 'entry_zone_id', 'entry_house_number');
		$ab_entry = $this->_getAddressBookEntry($ab_id);
		$hash_input = '';
		foreach($hash_fields as $key) {
			$value = $ab_entry[$key];
			$hash_input .= $value;
		}
		$hash = md5($hash_input);
		return $hash;
	}


	/* ============================================================================================================ */

	public function saveTransaction($orders_id, $status, $txid, $userid) {
		$query = "INSERT INTO `payone_transactions` SET `orders_id` = :orders_id, `status` = ':status', `txid` = ':txid', `userid` = ':userid',
			`created` = NOW(), `last_modified` = NOW()";
		$query = strtr($query, array(':orders_id' => (int)$orders_id, ':status' => xtc_db_input($status),
			':txid' => xtc_db_input($txid), ':userid' => xtc_db_input($userid)));
		xtc_db_query($query);
		$this->log("transaction saved: orders_id $orders_id, status $status, txid $txid, userid $userid");
	}

	public function getOrdersData($orders_id) {
		$data = array();
		// transaction data
		$tx_query = "SELECT * FROM `payone_transactions` WHERE `orders_id` = :orders_id";
		$tx_query = strtr($tx_query, array(':orders_id' => (int)$orders_id));
		$tx_result = xtc_db_query($tx_query);
		$data['transactions'] = array();
		while($tx_row = xtc_db_fetch_array($tx_result)) {
			$data['transactions'][] = $tx_row;
		}

		$data['transaction_status'] = $this->getTransactionStatus($orders_id);

		return $data;
	}

	protected function _tableExists($tableName)
	{
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$result = $db->query('SHOW TABLES LIKE \''.$tableName.'\'');
		$exists = $result->num_rows() > 0;
		return $exists;
	}

	public function getAddPaydata($orders_id)
	{
		$paydata = [];
		if($this->_tableExists('payone_add_paydata'))
		{
			$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
			$paydata = $db->get_where('payone_add_paydata', array('orders_id' => (int)$orders_id))->result_array();
		}
		return $paydata;
	}

	/**
	* stores data from a TransactionStatus request in local database
	* @param $txstatus essentially the $_POST from PayOne
	*/
	public function saveTransactionStatus($txstatus) {
		if(empty($txstatus['reference'])) {
			$this->log("received TxStatus w/o reference!");
			return;
		}
		$config = $this->getConfig();
		$key_valid = false;
		if(md5($config['global']['key']) == $txstatus['key']) {
			$key_valid = true;
		}
		else {
			$paymentgenre_identifiers = $this->getPaymentGenreIdentifiers();
			foreach($paymentgenre_identifiers as $pg_id) {
				if(md5($config[$pg_id]['global']['key']) == $txstatus['key']) {
					$key_valid = true;
				}
			}
		}

		$t_array_keys = array('de', 'id', 'no', 'va', 'ti', 'pr', 'ed', 'sd');
		if($key_valid == true) {
			$txstatus_query = "INSERT INTO `payone_txstatus` SET `orders_id` = :orders_id, `received` = NOW()";
			$txstatus_query = strtr($txstatus_query, array(':orders_id' => (int)$txstatus['reference']));
			xtc_db_query($txstatus_query);
			$txstatus_id = xtc_db_insert_id();
			$txstatus_data_query = "INSERT INTO `payone_txstatus_data` SET `payone_txstatus_id` = :txstatus_id, `key` = ':key', `value` = ':value'";
			$t_products_data = array();
			foreach($txstatus as $key => $value) {
				if(in_array($key, $t_array_keys) && is_array($value))
				{
					foreach($value as $item_no => $data)
					{
						if(isset($t_products_data[$item_no]) !== true)
						{
							$t_products_data[$item_no] = array();
						}
						$t_products_data[$item_no][$key] = $data;
					}
				}
				else if(is_string($value))
				{
					$txd_query = strtr($txstatus_data_query, array(':txstatus_id' => $txstatus_id, ':key' => xtc_db_input($key), ':value' => xtc_db_input($value)));
					xtc_db_query($txd_query);
				}
			}
			foreach($t_products_data as $item_no => $item_data)
			{
				foreach($item_data as $key => $data)
				{
					$item_key = 'item_'.$item_no.'_'.$key;
					$item_value = $data;
					$txd_query = strtr($txstatus_data_query, array(':txstatus_id' => $txstatus_id, ':key' => xtc_db_input($item_key), ':value' => xtc_db_input($item_value)));
					xtc_db_query($txd_query);
				}
			}
			$update_tx_query = "UPDATE `payone_transactions` SET `status` = '".xtc_db_input(strtoupper($txstatus['txaction']))."', `last_modified` = NOW() WHERE `txid` = '".xtc_db_input($txstatus['txid'])."'";
			xtc_db_query($update_tx_query);
			if(in_array($txstatus['txaction'], $this->getStatusNames())) {
				$orders_status_id = $config['orders_status'][$txstatus['txaction']];
				$orders_status_query = "UPDATE `orders` SET `orders_status` = :orders_status, `last_modified` = NOW() WHERE orders_id = :orders_id";
				$orders_status_query = strtr($orders_status_query, array(':orders_status' => (int)$orders_status_id, ':orders_id' => (int)$txstatus['reference']));
				xtc_db_query($orders_status_query);
				$oshistory_query = "INSERT INTO `orders_status_history` SET `orders_id` = :orders_id, `orders_status_id` = :orders_status, `date_added` = NOW(), `customer_notified` = 0, `comments` = ':comments'";
				$oshistory_query = strtr($oshistory_query, array(':orders_status' => (int)$orders_status_id, ':orders_id' => (int)$txstatus['reference'], ':comments' => $this->get_text('status_update_by_payone')));
				xtc_db_query($oshistory_query);
			}
		}
		else {
			$this->log("received TxStatus with an invalid key! TxStatus will not be processed.");
		}

		// logging
		$message_parts = array();
		foreach($txstatus as $name => $value) {
			if(in_array($name, $t_array_keys))
			{
				continue;
			}
			$message_parts[] = "$name=$value";
		}
		foreach($t_products_data as $item_no => $item_data)
		{
			foreach($item_data as $key => $value)
			{
				$message_parts[] = 'item_'.$item_no.'_'.$key.'='.$value;
			}
		}
		$message = implode('|', $message_parts);
		$log_query = "INSERT INTO `payone_transactions_log` SET `event_id` = :event_id, `date_created` = NOW(), `log_count` = 0, `log_level` = 0, `message` = ':message', `customers_id` = 0";
		list($msec, $sec) = explode(' ', microtime());
		$log_query = strtr($log_query, array(
			':event_id' => (int)(($sec + $msec) * 1000),
			':message' => $message,
		));
		$this->log($log_query);
		xtc_db_query($log_query);
	}

	public function getTransactionStatus($orders_id) {
		// get metadata first
		$txstatus = array();
		$txstatus_query = "SELECT * FROM `payone_txstatus` WHERE orders_id = :orders_id";
		$txstatus_query = strtr($txstatus_query, array(':orders_id' => (int)$orders_id));
		$txstatus_result = xtc_db_query($txstatus_query);
		while($txstatus_row = xtc_db_fetch_array($txstatus_result)) {
			$txstatus_row['data'] = array();
			$txstatus[] = $txstatus_row;
		}

		// get details
		$txstatusdata_query = "SELECT `key`, `value` FROM `payone_txstatus_data` WHERE `payone_txstatus_id` = :txstatus_id";
		foreach($txstatus as $idx => $txs) {
			$txsd_query = strtr($txstatusdata_query, array(':txstatus_id' => (int)$txs['payone_txstatus_id']));
			$txsd_result = xtc_db_query($txsd_query);
			while($txsd_row = xtc_db_fetch_array($txsd_result)) {
				$txstatus[$idx]['data'][$txsd_row['key']] = $txsd_row['value'];
			}
		}

		return $txstatus;
	}

	/* ============================================================================================================ */

	public function getCaptureData($orders_id) {
		// a transaction can be captured if it is "appointed"
		$capture_data = false; // i.e. cannot be captured
		$orders_data = $this->getOrdersData($orders_id);
		//ob_clean(); header('Content-Type: text/plain'); die(print_r($orders_data, true));
		foreach($orders_data['transaction_status'] as $tstatus) {
			if(strtoupper($tstatus['data']['txaction']) == 'APPOINTED') {
				$capture_data = array(
					'txid' => $tstatus['data']['txid'],
					'price' => $tstatus['data']['price'],
					'portalid' => $tstatus['data']['portalid'],
					'aid' => $tstatus['data']['aid'],
					'currency' => $tstatus['data']['currency'],
					'sequencenumber' => $tstatus['data']['sequencenumber'],
				);
			}
		}

		return $capture_data;
	}

	protected function _getNextSequencenumber($txid) {
		$query = "SELECT MAX(`d`.`value`) AS max_sequence FROM `payone_transactions` t
			left join `payone_txstatus` s on s.orders_id = t.orders_id
			left join payone_txstatus_data d on d.payone_txstatus_id = s.payone_txstatus_id AND d.key = 'sequencenumber'
			where t.txid = ".(int)$txid;
		$result = xtc_db_query($query);
		$next_seqnum = 0;
		while($row = xtc_db_fetch_array($result)) {
			$next_seqnum = $row['max_sequence'] + 1;
		}
		return $next_seqnum;
	}

	public function captureAmount($txid, $portalid, $p1_capture_amount, $p1_capture_currency) {
		$this->log("capturing $p1_capture_amount $p1_capture_currency for transaction $txid (portal $portalid)");
		$config = $this->getConfig();
		$global_config = $config['global'];
		$standard_parameters = $this->getStandardParameters('capture', $global_config);
		unset($standard_parameters['responsetype']);
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		unset($standard_parameters['hash']);
		$request_parameters = array(
			'aid' => $global_config['subaccount_id'],
			'key' => $global_config['key'],
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$amount = round($p1_capture_amount, 2);
		$builder = new Payone_Builder($this->getPayoneConfig());
		$service = $builder->buildServicePaymentCapture();
		$request = new Payone_Api_Request_Capture($params);
		$request->setTxid($txid);
		$request->setPortalid($portalid);
		$request->setSequencenumber($this->_getNextSequencenumber($txid));
		$request->setAmount($amount);
		$request->setCurrency($p1_capture_currency);
		$this->log("capture request:\n".print_r($request, true));
		$response = $service->capture($request);
		$this->log("capture response:\n".print_r($response, true));
		if($response->getStatus() == 'ERROR') {
			$this->log("ERROR capturing amount: ".$response->getErrorcode().' '.$response->getErrormessage());
		}
		return $response;
	}

	public function refundAmount($parameters) {
		$this->log("refunding amount\n".print_r($parameters, true));
		$config = $this->getConfig();
		$global_config = $config['global'];
		$standard_parameters = $this->getStandardParameters('debit', $global_config);
		unset($standard_parameters['responsetype']);
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		unset($standard_parameters['hash']);
		$request_parameters = array(
			'aid' => $global_config['subaccount_id'],
			'key' => $global_config['key'],
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->getPayoneConfig());
		$service = $builder->buildServicePaymentDebit();
		$request = new Payone_Api_Request_Debit($params);
		$request->setAmount(-1 * (double)$parameters['amount']);
		$request->setCurrency($parameters['currency']);
		$request->setSequencenumber($parameters['sequencenumber']);
		$request->setTxid($parameters['txid']);
		if(false && !empty($parameters['bankaccount'])) {
			$payment = new Payone_Api_Request_Parameter_Refund_PaymentMethod_BankAccount();
			$payment->setBankaccount($parameters['bankaccount']);
			$payment->setBankbranchcode($parameters['bankbranchcode']);
			$payment->setBankcheckdigit($parameters['bankcheckdigit']);
			$payment->setBankcode($parameters['bankcode']);
			$payment->setBankcountry($parameters['bankcountry']);
			$request->setPayment($payment);
		}
		$this->log("debit request:\n".print_r($request, true));
		$response = $service->debit($request);
		$this->log("debit response:\n".print_r($response, true));
		if($response->getStatus() == 'ERROR') {
			$this->log("ERROR refunding amount: ".$response->getErrorcode().' '.$response->getErrormessage());
		}
		return $response;
	}

	/* ============================================================================================================ */

	public function getBillToCountry() {
		if(!(isset($_SESSION['billto']) && is_numeric($_SESSION['billto']))) {
			return '';
		}
		$ab_id = $_SESSION['billto'];
		$customer_id = $_SESSION['customer_id'];
		$query = "SELECT `ab`.*, `c`.* FROM `address_book` ab left join `countries` c on `c`.`countries_id` = `ab`.`entry_country_id`
			WHERE ab.address_book_id = :ab_id AND ab.customers_id = :customer_id";
		$query = strtr($query, array(':ab_id' => (int)$ab_id, ':customer_id' => (int)$customer_id));
		$result = xtc_db_query($query);
		$country = '';
		while($row = xtc_db_fetch_array($result)) {
			$country = $row['countries_iso_code_2'];
		}
		return $country;
	}

	public function getClearingData($orders_id) {
		$result = xtc_db_query("SELECT * FROM `payone_clearingdata` WHERE `orders_id` = ".(int)$orders_id);
		$cd = false;
		while($row = xtc_db_fetch_array($result)) {
			$cd = $row;
		}
		return $cd;
	}

	/* ============================================================================================================ */

	public function addressCheck($ab_id, $checktype = 'BA') {
		$global_config = $this->getConfig('global');
		$config = $this->getConfig('address_check');
		$cdata = $this->_getAddressBookEntry($ab_id);

		if($cdata === false) {
			throw new Exception('invalid address book entry');
		}

		$standard_parameters = $this->getStandardParameters();
		$builder = new Payone_Builder($this->getPayoneConfig());
		$service = $builder->buildServiceVerificationAddressCheck();
		$requestData = array(
			'key' => $global_config['key'],
			'addresschecktype' => $checktype, // BA|PE|NO (basic | person | no)
		);
		$addressData = array(
			'firstname'       => $cdata['entry_firstname'],
			'lastname'        => $cdata['entry_lastname'],
			'company'         => $cdata['entry_company'],
			//'street'          => $cdata['entry_street_address'],
			'zip'             => $cdata['entry_postcode'],
			'city'            => $cdata['entry_city'],
			'country'         => $cdata['countries_iso_code_2'],
			'birthday'        => date('Ymd', strtotime($cdata['dob_date'])),
			'telephonenumber' => $cdata['customers_telephone'],
		);
		if(empty($cdata['entry_house_number']))
		{
			$addressData['street']       = $cdata['entry_street_address'];
		}
		else
		{
			$addressData['streetname']   = $cdata['entry_street_address'];
			$addressData['streetnumber'] = $cdata['entry_house_number'];
		}
		$address_hash = md5(implode('', $addressData));
		$response = $this->_retrieveCachedAddressCheckResponse($address_hash);
		if($response == false) {
			$this->log("addressCheck cache miss");
			$requestData = array_merge($standard_parameters, $requestData, $addressData);
			$request = new Payone_Api_Request_AddressCheck($requestData);
			$this->log("addressCheck hash: ".$address_hash."\n");
			$this->log("addressCheck request:\n".print_r($request, true));
			$response = $service->check($request);
			$this->log("addressCheck response:\n".print_r($response, true));
		}
		else {
			$this->log("addressCheck cache hit");
		}
		if($response instanceof Payone_Api_Response_AddressCheck_Valid || $response instanceof Payone_Api_Response_AddressCheck_Invalid) {
			$this->_storeAddressCheckResponse($response, $ab_id, $address_hash);
			return $response;
		}
		else if($response instanceof Payone_Api_Response_Error)
		{
			$error_message = "ERROR checking address: ".(string)$response;
			$this->log($error_message);
			throw new Exception($error_message);
		}
		else {
			$this->log("unhandled response of type ".gettype($response).":\n".print_r($response, true));
			return false;
		}
	}

	protected function _retrieveCachedAddressCheckResponse($address_hash) {
		$config = $this->getConfig('address_check');
		$cache_days = $config['validity'];
		$query = "SELECT * FROM `payone_ac_cache` WHERE address_hash = ':address_hash' AND `received` >= DATE_SUB(NOW(), INTERVAL :cache_days DAY)";
		$query = strtr($query, array(':address_hash' => xtc_db_input($address_hash), ':cache_days' => (int)$cache_days));
		$cached_response = false;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			if(empty($row['errorcode'])) {
				$cached_response = new Payone_Api_Response_AddressCheck_Valid($row);
			}
			else {
				$cached_response = new Payone_Api_Response_AddressCheck_Invalid($row);
			}
		}
		return $cached_response;
	}

	protected function _storeAddressCheckResponse($response, $ab_id, $address_hash) {
		$query = "REPLACE INTO `payone_ac_cache` SET
			`address_hash` = ':address_hash',
			`address_book_id` = :address_book_id,
			`received` = NOW(),
			`secstatus` = :secstatus,
			`status` = ':status',
			`personstatus` = ':personstatus',
			`street` = ':street',
			`streetname` = ':streetname',
			`streetnumber` = ':streetnumber',
			`zip` = ':zip',
			`city` = ':city',
			`errorcode` = ':errorcode',
			`errormessage` = ':errormessage',
			`customermessage` = ':customermessage'
			";
		if($response instanceof Payone_Api_Response_AddressCheck_Valid) {
			$data = array(
				':address_hash' => xtc_db_input($address_hash),
				':address_book_id' => (int)$ab_id,
				':secstatus' => (int)$response->getSecstatus(),
				':status' => xtc_db_input($response->getStatus()),
				':personstatus' => xtc_db_input($response->getPersonstatus()),
				':street' => xtc_db_input($response->getStreet()),
				':streetname' => xtc_db_input($response->getStreetname()),
				':streetnumber' => xtc_db_input($response->getStreetnumber()),
				':zip' => xtc_db_input($response->getZip()),
				':city' => xtc_db_input($response->getCity()),
				':errorcode' => '',
				':errormessage' => '',
				':customermessage' => '',
			);
		}
		else if($response instanceof Payone_Api_Response_AddressCheck_Invalid) {
			$data = array(
				':address_hash' => xtc_db_input($address_hash),
				':address_book_id' => (int)$ab_id,
				':secstatus' => (int)$response->getSecstatus(),
				':status' => xtc_db_input($response->getStatus()),
				':personstatus' => '',
				':street' => '',
				':streetname' => '',
				':streetnumber' => '',
				':zip' => '',
				':city' => '',
				':errorcode' => xtc_db_input($response->getErrorcode()),
				':errormessage' => xtc_db_input($response->getErrormessage()),
				':customermessage' => xtc_db_input($response->getCustomerMessage()),
			);
		}
		$query = strtr($query, $data);
		xtc_db_query($query);
	}

	public function scoreCustomer($ab_id) {
		$global_config = $this->getConfig('global');
		$config = $this->getConfig('credit_risk');
		$cdata = $this->_getAddressBookEntry($ab_id);

		if($cdata === false) {
			throw new Exception('invalid address book entry');
		}

		switch($config['typeofcheck']) {
			case 'iscorehard':
				$scoretype = 'IH';
				break;
			case 'iscoreall':
				$scoretype = 'IA';
				break;
			case 'iscorebscore';
				$scoretype = 'IB';
				break;
			default:
				$scoretype = 'IH';
		}

		$standard_parameters = $this->getStandardParameters();
		$builder = new Payone_Builder($this->getPayoneConfig());
		$service = $builder->buildServiceVerificationConsumerscore();
		$requestData = array(
			'key' => $global_config['key'],
			'addresschecktype' => 'NO', // BA|PE|NO (basic | person | no)
			'consumerscoretype' => $scoretype, // IH|IA|IB (hart | alle | alle+boni)
		);
		$addressData = array(
			'firstname'       => $cdata['entry_firstname'],
			'lastname'        => $cdata['entry_lastname'],
			'company'         => $cdata['entry_company'],
			// 'street'          => $cdata['entry_street_address'],
			'zip'             => $cdata['entry_postcode'],
			'city'            => $cdata['entry_city'],
			'country'         => $cdata['countries_iso_code_2'],
			'birthday'        => date('Ymd', strtotime($cdata['dob_date'])),
			'telephonenumber' => $cdata['customers_telephone'],
		);
		if(empty($cdata['entry_house_number']))
		{
			$addressData['street']       = $cdata['entry_street_address'];
		}
		else
		{
			$addressData['streetname']   = $cdata['entry_street_address'];
			$addressData['streetnumber'] = $cdata['entry_house_number'];
		}
		$address_hash = md5(implode('', $addressData));
		$response = $this->_retrieveCachedCreditRiskResponse($address_hash, $scoretype);
		if($response == false) {
			$this->log("creditRisk cache miss");
			$requestData = array_merge($standard_parameters, $requestData, $addressData);
			$request = new Payone_Api_Request_Consumerscore($requestData);
			$this->log("scoreCustomer request:\n".print_r($request, true));
			$response = $service->score($request);
			$this->log("scoreCustomer response:\n".print_r($response, true));
		}
		else {
			$this->log("creditRisk cache hit");
		}
		if($response instanceof Payone_Api_Response_Consumerscore_Valid || $response instanceof Payone_Api_Response_Consumerscore_Invalid) {
			$this->_storeCreditRiskResponse($response, $ab_id, $address_hash, $scoretype);
			return $response;
		}
		else {
			return false;
		}
	}

	protected function _retrieveCachedCreditRiskResponse($address_hash, $scoretype) {
		$config = $this->getConfig('credit_risk');
		$cache_days = $config['validity'];
		$query = "SELECT * FROM `payone_cr_cache` WHERE address_hash = ':address_hash' AND `scoretype` = ':scoretype' AND `received` >= DATE_SUB(NOW(), INTERVAL :cache_days DAY)";
		$query = strtr($query, array(':address_hash' => xtc_db_input($address_hash), ':scoretype' => xtc_db_input($scoretype), ':cache_days' => (int)$cache_days));
		$this->log("credit_risk checking cache:\n".$query);
		$cached_response = false;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result)) {
			if(empty($row['errorcode'])) {
				$cached_response = new Payone_Api_Response_Consumerscore_Valid($row);
			}
			else {
				$cached_response = new Payone_Api_Response_Consumerscore_Invalid($row);
			}
		}
		return $cached_response;
	}

	protected function _storeCreditRiskResponse($response, $ab_id, $address_hash, $scoretype) {
		$query = "REPLACE INTO `payone_cr_cache` SET
			`address_hash` = ':address_hash',
			`address_book_id` = :address_book_id,
			`scoretype` = ':scoretype',
			`received` = NOW(),
			`secstatus` = :secstatus,
			`status` = ':status',
			`score` = ':score',
			`scorevalue` = ':scorevalue',
			`secscore` = ':secscore',
			`personstatus` = ':personstatus',
			`firstname` = ':firstname',
			`lastname` = ':lastname',
			`street` = ':street',
			`streetname` = ':streetname',
			`streetnumber` = ':streetnumber',
			`zip` = ':zip',
			`city` = ':city',
			`errorcode` = ':errorcode',
			`errormessage` = ':errormessage',
			`customermessage` = ':customermessage'
			";
		if($response instanceof Payone_Api_Response_Consumerscore_Valid) {
			$data = array(
				':address_hash' => xtc_db_input($address_hash),
				':address_book_id' => (int)$ab_id,
				':scoretype' => xtc_db_input($scoretype),
				':secstatus' => (int)$response->getSecstatus(),
				':status' => xtc_db_input($response->getStatus()),
				':score' => xtc_db_input($response->getScore()),
				':scorevalue' => xtc_db_input($response->getScorevalue()),
				':secscore' => xtc_db_input($response->getSecscore()),
				':personstatus' => xtc_db_input($response->getPersonstatus()),
				':firstname' => xtc_db_input($response->getFirstname()),
				':lastname' => xtc_db_input($response->getLastname()),
				':street' => xtc_db_input($response->getStreet()),
				':streetname' => xtc_db_input($response->getStreetname()),
				':streetnumber' => xtc_db_input($response->getStreetnumber()),
				':zip' => xtc_db_input($response->getZip()),
				':city' => xtc_db_input($response->getCity()),
				':errorcode' => '',
				':errormessage' => '',
				':customermessage' => '',
			);
		}
		else if($response instanceof Payone_Api_Response_Consumerscore_Invalid) {
			$data = array(
				':address_hash' => xtc_db_input($address_hash),
				':address_book_id' => (int)$ab_id,
				':scoretype' => xtc_db_input($scoretype),
				':secstatus' => (int)$response->getSecstatus(),
				':status' => xtc_db_input($response->getStatus()),
				':score' => '',
				':scorevalue' => '',
				':secscore' => '',
				':personstatus' => '',
				':firstname' => '',
				':lastname' => '',
				':street' => '',
				':streetname' => '',
				':streetnumber' => '',
				':zip' => '',
				':city' => '',
				':errorcode' => xtc_db_input($response->getErrorcode()),
				':errormessage' => xtc_db_input($response->getErrormessage()),
				':customermessage' => xtc_db_input($response->getCustomerMessage()),
			);
		}
		$query = strtr($query, $data);
		xtc_db_query($query);
	}

	/* -------------------------------------------------------------------------------------------- */

	public function getLogsCount($mode, $date_start = null, $date_end = null) {
		$table = $mode == 'api' ? 'payone_api_log' : 'payone_transactions_log';
		$query = "SELECT COUNT(*) AS logs_count FROM $table";
		if($date_start !== null && $date_end !== null) {
			$query .= " WHERE date_created BETWEEN '".date('Y-m-d 00:00:00', strtotime($date_start))."' AND '".date('Y-m-d 23:59:59', strtotime($date_end))."'";
		}
		$result = xtc_db_query($query);
		$count = 0;
		while($row = xtc_db_fetch_array($result)) {
			$count = $row['logs_count'];
		}
		return $count;
	}

	public function getLogs($mode, $limit, $offset, $date_start = null, $date_end = null) {
		$table = $mode == 'api' ? 'payone_api_log' : 'payone_transactions_log';
		$query = "SELECT l.event_id, l.date_created, l.customers_id, c.customers_firstname, c.customers_lastname FROM $table l
			LEFT OUTER JOIN customers c ON c.customers_id = l.customers_id ";
		if($date_start !== null && $date_end !== null) {
			$query .= " WHERE date_created BETWEEN '".date('Y-m-d 00:00:00', strtotime($date_start))."' AND '".date('Y-m-d 23:59:59', strtotime($date_end))."' ";
		}
		$query .= "GROUP BY l.event_id ORDER BY l.date_created ASC LIMIT $limit OFFSET $offset";
		$result = xtc_db_query($query);
		$logs = array();
		while($row = xtc_db_fetch_array($result)) {
			$logs[] = $row;
		}
		return $logs;
	}

	public function getLogData($mode, $event_id) {
		$table = $mode == 'api' ? 'payone_api_log' : 'payone_transactions_log';
		$query = "SELECT * FROM $table WHERE event_id = ".(int)$event_id." ORDER BY log_count";
		$result = xtc_db_query($query);
		$data = array();
		while($row = xtc_db_fetch_array($result)) {
			$row['message'] = $this->_splitLogMessage($row['message']);
			$data[] = $row;
		}
		return $data;
	}

	protected function _splitLogMessage($message) {
		$parts = explode('|', $message);
		$message = array();
		foreach($parts as $part) {
			list($name, $value) = explode('=', $part);
			$message[$name] = $value;
		}
		return $message;
	}
}
MainFactory::load_origin_class('GMPayOne');