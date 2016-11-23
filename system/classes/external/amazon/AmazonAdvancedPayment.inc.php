<?php
/* --------------------------------------------------------------
	AmazonAdvancedPayment.inc.php 2016-02-10
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class AmazonAdvancedPayment
{
	const EP_OAP_DE_SANDBOX = 'https://mws-eu.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/';
	const EP_OAP_DE_PRODUCTION = 'https://mws-eu.amazonservices.com/OffAmazonPayments/2013-01-01/';
	const EP_OAP_UK_SANDBOX = 'https://mws-eu.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/';
	const EP_OAP_UK_PRODUCTION = 'https://mws-eu.amazonservices.com/OffAmazonPayments/2013-01-01/';
	const EP_OAP_US_SANDBOX = 'https://mws.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01/';
	const EP_OAP_US_PRODUCTION = 'https://mws.amazonservices.com/OffAmazonPayments/2013-01-01/';

	const URL_WIDGET_DE_SANDBOX = 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/js/Widgets.js';
	const URL_WIDGET_DE_PRODUCTION = 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/js/Widgets.js';
	const URL_WIDGET_UK_SANDBOX = 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/js/Widgets.js';
	const URL_WIDGET_UK_PRODUCTION = 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/js/Widgets.js';
	const URL_WIDGET_US_SANDBOX = 'https://static.payments-amazon.com/OffAmazonPayments/sandbox/js/Widgets.js';
	const URL_WIDGET_US_PRODUCTION = 'https://static.payments-amazon.com/OffAmazonPayments/js/Widgets.js';

	const URL_BUTTON_DE_SANDBOX = 'https://payments-sandbox.amazon.de/gp/widgets/button';
	const URL_BUTTON_DE_PRODUCTION = 'https://payments.amazon.de/gp/widgets/button';
	const URL_BUTTON_UK_SANDBOX = 'https://payments-sandbox.amazon.co.uk/gp/widgets/button';
	const URL_BUTTON_UK_PRODUCTION = 'https://payments.amazon.co.uk/gp/widgets/button';
	const URL_BUTTON_US_SANDBOX = 'https://payments-sandbox.amazon.com/gp/widgets/button';
	const URL_BUTTON_US_PRODUCTION = 'https://payments.amazon.com/gp/widgets/button';

	const MARKETPLACE_DE = 'A1PA6795UKMFR9';
	const MARKETPLACE_UK = 'A1F83G8C2ARO7P';
	const MARKETPLACE_US = 'ATVPDKIKX0DER';

	const CONFIG_PREFIX = 'AMAZONADVPAY_';

	const LOGLEVEL_INFO = 0x01;
	const LOGLEVEL_WARN = 0x02;
	const LOGLEVEL_ERROR = 0x04;
 	const LOGLEVEL_DEBUG = 0x10;

	protected $_configuration;
	protected $_txt;
	protected $_logger;
	protected $_throttle_max_retries = 5;
	protected $_retry_delay = 1000; // milliseconds

	public function __construct()
	{
		/*
		if(class_exists('LogControl'))
		{
			$this->_logger = LogControl::get_instance();
		}
		else
		{
			$this->_logger = false;
		}
		*/
		$this->_logger = new FileLog('payment-amzadvpay', true);
		$this->_txt = MainFactory::create_object('LanguageTextManager', array('amazonadvancedpayment', $_SESSION['languages_id']));
		$this->_configuration = $this->_load_configuration();
	}

	protected function _load_configuration()
	{
		$t_cfg = array(
				'location' => 'de', // de|uk|us
				'mode' => 'sandbox', // sandbox|production
				'seller_id' => '__seller_id',
				'aws_access_key' => '__aws_access_key',
				'secret_key' => '__secret_key',
				'button_color' => 'orange', // orange|tan
				'button_size' => 'medium', // medium|large|x-large
				'hidden_button' => '0',
				'authorization_mode' => 'auto-sync', // auto-sync|auto-async|manual
				'capture_mode' => 'manual', // immediate|manual
				'erp_mode' => '0', // 0|1
				'ipn_enabled' => '1', // 0|1
				'orders_status_auth_open' => '0',
				'orders_status_auth_declined' => '0',
				'orders_status_auth_declined_hard' => '0',
				'orders_status_captured' => '0',
				'orders_status_capture_failed' => '0',
			);

		foreach($t_cfg as $cfg_key => $cfg_value)
		{
			$t_db_cfg_key = self::CONFIG_PREFIX.strtoupper($cfg_key);
			$t_db_cfg_value = gm_get_conf($t_db_cfg_key);
			if($t_db_cfg_value !== null)
			{
				$t_cfg[$cfg_key] = $t_db_cfg_value;
			}
		}

		return $t_cfg;
	}

	protected function _save_configuration($p_key = null)
	{
		foreach($this->_configuration as $cfg_key => $cfg_value)
		{
			if($p_key !== null && $cfg_key != $p_key)
			{
				continue;
			}
			$t_db_cfg_key = self::CONFIG_PREFIX.strtoupper($cfg_key);
			gm_set_conf($t_db_cfg_key, xtc_db_input($cfg_value));
		}
	}

	public function __get($p_varname)
	{
		$t_value = null;
		if(array_key_exists($p_varname, $this->_configuration))
		{
			$t_value = $this->_configuration[$p_varname];
		}

		return $t_value;
	}

	public function __set($p_varname, $p_value)
	{
		if(array_key_exists($p_varname, $this->_configuration))
		{
			$this->_configuration[$p_varname] = $p_value;
			$this->_save_configuration($p_varname);
		}
	}

	public function __isset($p_varname)
	{
		$t_isset = false;
		if(in_array($p_varname, $this->_configuration))
		{
			$t_isset = true;
		}
		return $t_isset;
	}

	/* ---------------------------------------------------------------------------------------------- */

	public function get_authorization_timeout()
	{
		if($this->authorization_mode == 'auto-async')
		{
			$t_timeout = 1440;
		}
		else
		{
			$t_timeout = 0;
		}
		return $t_timeout;
	}

	/* ---------------------------------------------------------------------------------------------- */

	public function get_text($placeholder)
	{
		return $this->_txt->get_text($placeholder);
	}

	public function replaceLanguagePlaceholders($content)
	{
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1)
		{
			$replacement = $this->get_text($matches[1]);
			if(empty($replacement))
			{
				$replacement = $matches[1];
			}
			$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}

	/* ---------------------------------------------------------------------------------------------- */

	public function log($message, $loglevel = self::LOGLEVEL_INFO)
	{
		if($this->_logger !== false)
		{
			if($this->_logger instanceof LogControl)
			{
				switch($loglevel)
				{
					case self::LOGLEVEL_ERROR:
						$this->_logger->error($message, 'payment', 'payment.amazonadvpay');
						break;
					case self::LOGLEVEL_WARN:
						$this->_logger->warning($message, 'payment', 'payment.amazonadvpay');
						break;
					case self::LOGLEVEL_INFO:
					case self::LOGLEVEL_DEBUG:
					default:
						$this->_logger->notice($message, 'payment', 'payment.amazonadvpay');
						break;
				}
			}
			if($this->_logger instanceof FileLog)
			{
				list($t_millis, $t_secs) = explode(' ', microtime());
				$t_timestamp = sprintf('%s.%03d', date('Y-m-d H:i:s', $t_secs), round($t_millis * 1000));
				$t_message = $t_timestamp.' | '.$message.PHP_EOL;
				$this->_logger->write($t_message);
			}
		}
	}

	public function is_enabled()
	{
		$t_is_enabled = (defined('MODULE_PAYMENT_AMAZONADVPAY_STATUS') && MODULE_PAYMENT_AMAZONADVPAY_STATUS == 'True');
		$t_is_enabled = $t_is_enabled && strpos(MODULE_PAYMENT_INSTALLED, 'amazonadvpay.php') !== false;
		return $t_is_enabled;
	}

	public function useDefaultOrdersStatusConfiguration()
	{
		$LanguageTextManagers = array();
		$Language = new language();
		foreach($Language->catalog_languages as $lang_iso => $lang_data)
		{
			$LanguageTextManagers[$lang_iso] = MainFactory::create_object('LanguageTextManager', array('amazonadvancedpayment', $lang_data['id']));
		}

		$OrdersStatusNames = array(
				'orders_status_auth_open' => 'orders_status_name_payment_authorized',
				'orders_status_auth_declined' => 'orders_status_name_authorization_declined',
				'orders_status_auth_declined_hard' => 'orders_status_name_authorization_declined_hard',
				'orders_status_captured' => 'orders_status_name_payment_captured',
				'orders_status_capture_failed' => 'orders_status_name_capture_failed',
		);

		foreach($OrdersStatusNames as $ConfigName => $PhraseLabel)
		{
			$osm = MainFactory::create_object('OrdersStatusModel');
			try {
				$osm->loadByName($LanguageTextManagers[DEFAULT_LANGUAGE]->get_text($PhraseLabel));
			}
			catch(OrdersStatusModelStatusNotFoundException $e)
			{
				foreach($LanguageTextManagers as $LangISO2 => $LanguageTextManager)
				{
					$osm->setStatusName($LanguageTextManager->get_text($PhraseLabel), $Language->catalog_languages[$LangISO2]['id']);
					$osm->save();
				}
			}
			$this->$ConfigName = $osm->get_('orders_status_id');
		}
	}

	/* ---------------------------------------------------------------------------------------------- */

	public function get_amazon_address_book_entry($p_customers_id)
	{
		$t_address_book_id = false;

		// find Amazon address book entry
		$t_ab_query =
			'SELECT
				`address_book_id`
			FROM
				`address_book`
			WHERE
				`customers_id` = \':customers_id\' AND
				`address_class` = \'amzadvpay_temp\'';
		$t_ab_query = strtr($t_ab_query, array(':customers_id' => (int)$p_customers_id));
		$t_ab_result = xtc_db_query($t_ab_query, 'db_link', false);
		while($t_ab_row = xtc_db_fetch_array($t_ab_result))
		{
			$t_address_book_id = $t_ab_row['address_book_id'];
		}

		// create address book entry if necessary
		if($t_address_book_id === false)
		{
			$t_country_and_zone_id = $this->_get_customers_default_country_and_zone_id($p_customers_id);
			$t_text_tbd = $this->get_text('ab_entry_to_be_determined');
			$t_ab_data = array(
					'customers_id' => (int)$p_customers_id,
					'entry_gender' => '', // N.B.: Amazon does not record customer's gender!
					'entry_company' => '',
					'entry_firstname' => ' ',
					'entry_lastname' => $t_text_tbd,
					'entry_street_address' => '',
					'entry_postcode' => '',
					'entry_city' => '',
					'entry_state' => '',
					'entry_country_id' => $t_country_and_zone_id['country_id'],
					'entry_zone_id' => $t_country_and_zone_id['zone_id'],
					'address_date_added' => 'now()',
					'address_last_modified' => 'now()',
					'address_class' => 'amzadvpay_temp',
				);
			xtc_db_perform('address_book', $t_ab_data);
			$t_address_book_id = xtc_db_insert_id();
		}

		return $t_address_book_id;
	}

	public function update_amazon_address_book_entry($p_customers_id, $p_data)
	{
		$t_address_book_id = $this->get_amazon_address_book_entry($p_customers_id);
		if(isset($p_data['country_iso2']))
		{
			$t_country_and_zone_id = $this->_get_country_and_zone_id_by_iso2($p_data['country_iso2']);
			$p_data['entry_country_id'] = $t_country_and_zone_id['country_id'];
			$p_data['entry_zone_id'] = $t_country_and_zone_id['zone_id'];
			unset($p_data['country_iso2']);
		}
		$this->log("updating ab entry $t_address_book_id for customer $p_customers_id with data:\n".print_r($p_data, true));
		xtc_db_perform('address_book', $p_data, 'update', 'address_book_id = \''.(int)$t_address_book_id.'\'');
	}

	public function delete_amazon_address_book_entries($p_customers_id)
	{
		$t_query =
			'DELETE
				FROM `address_book`
				WHERE
					`customers_id` = \':customers_id\' AND
					`address_class` = \'amzadvpay_temp\'';
		$t_query = strtr($t_query, array(':customers_id' => (int)$p_customers_id));
		xtc_db_query($t_query);
	}

	protected function _get_customers_default_country_and_zone_id($p_customers_id)
	{
		$t_query =
			'SELECT
				`entry_country_id`, `entry_zone_id`
			FROM
				`address_book` ab
				LEFT JOIN `customers` c
				ON
					`c`.`customers_id` = `ab`.`customers_id` AND
					`ab`.`address_book_id` = `c`.`customers_default_address_id`
			WHERE
				`ab`.`customers_id` = \':customers_id\'';
		$t_query = strtr($t_query, array(':customers_id' => (int)$p_customers_id));
		$t_result = xtc_db_query($t_query);
		$t_country_id = false;
		$t_zone_id = false;
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_country_id = $t_row['entry_country_id'];
			$t_zone_id = $t_row['entry_zone_id'];
		}
		if($t_country_id === false)
		{
			$t_result = $this->_get_country_and_zone_id_by_iso2(STORE_COUNTRY);
		}
		else
		{
			$t_result = array('country_id' => $t_country_id, 'zone_id' => $t_zone_id);
		}
		return $t_result;
	}

	public function get_country_and_zone_id_by_iso2($p_iso2)
	{
		return $this->_get_country_and_zone_id_by_iso2($p_iso2);
	}

	protected function _get_country_and_zone_id_by_iso2($p_iso2, $p_zone = '')
	{
		$this->log("finding country for $p_iso2");
		$t_query =
			'SELECT
				`countries_id`, `zone_id`
			FROM
				`countries` c
				LEFT JOIN `zones` z ON `z`.`zone_country_id` = `c`.`countries_id`
			WHERE
				`countries_iso_code_2` LIKE \':iso2\'';
		if(empty($p_zone) !== true)
		{
			$t_query .= 'AND (`zone_code` LIKE \':zone\' OR `zone_name` LIKE \':zone\')';
		}
		$t_query = strtr($t_query, array(':iso2' => xtc_db_input($p_iso2), ':zone' => xtc_db_input($p_zone)));
		$t_result = xtc_db_query($t_query);
		$t_country_id = false;
		$t_zone_id = false;
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_country_id = $t_row['countries_id'];
			$t_zone_id = $t_row['zone_id'];
		}
		$t_result = array('country_id' => $t_country_id, 'zone_id' => $t_zone_id);
		return $t_result;
	}

	public function country_is_allowed($p_iso2)
	{
		$t_query =
			'SELECT
				`status`
			FROM
				`countries`
			WHERE
				`countries_iso_code_2` = \':iso2\'';
		$t_query = strtr($t_query, array(':iso2' => xtc_db_input($p_iso2)));
		$t_result = xtc_db_query($t_query);
		$t_is_allowed = false;
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_is_allowed = $t_row['status'] == 1;
		}
		return $t_is_allowed;
	}

	public function get_country_name_and_address_format($p_country_iso2)
	{
		return $this->_get_country_name_and_address_format($p_country_iso2);
	}

	protected function _get_country_name_and_address_format($p_country_iso2)
	{
		$t_query =
			'SELECT
				`countries_name`, `address_format_id`
			FROM
				`countries`
			WHERE
				`countries_iso_code_2` LIKE \':iso2\'';
		$t_query = strtr($t_query, array(':iso2' => xtc_db_input($p_country_iso2)));
		$t_data = array(
			'country_name' => '',
			'address_format_id' => '0',
		);
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_data['country_name'] = $t_row['countries_name'];
			$t_data['address_format_id'] = $t_row['address_format_id'];
		}
		return $t_data;
	}

	/* ---------------------------------------------------------------------------------------------- */

	public function get_oap_endpoint()
	{
		$t_endpoint_identifier = 'self::EP_OAP_'.strtoupper($this->location).'_'.strtoupper($this->mode);
		if(defined($t_endpoint_identifier) == false)
		{
			throw new AmazonAdvancedPaymentException('invalid location or operating mode');
		}
		$t_endpoint_url = constant($t_endpoint_identifier);
		return $t_endpoint_url;
	}

	public function get_widgets_url()
	{
		$t_widgets_url_identfier = 'self::URL_WIDGET_'.strtoupper($this->location).'_'.strtoupper($this->mode);
		if(defined($t_widgets_url_identfier) == false)
		{
			throw new AmazonAdvancedPaymentException('invalid location or operating mode');
		}
		$t_widgets_url = constant($t_widgets_url_identfier);
		$t_widgets_url .= '?sellerId='.$this->seller_id;
		return $t_widgets_url;
	}

	public function get_button_url()
	{
		$t_button_url_identfier = 'self::URL_BUTTON_'.strtoupper($this->location).'_'.strtoupper($this->mode);
		if(defined($t_button_url_identfier) == false)
		{
			throw new AmazonAdvancedPaymentException('invalid location or operating mode');
		}
		$t_button_url = constant($t_button_url_identfier);
		$t_button_url .= '?sellerId='.$this->seller_id;
		return $t_button_url;
	}

	public function get_button_element()
	{
		$t_button_number = uniqid();
		$t_button_url = $this->get_button_url() .'&size='.$this->button_size.'&color='.$this->button_color;
		$t_button_url = str_replace('&', '&amp;', $t_button_url);
		$t_style = '';
		if($this->hidden_button == true)
		{
			$t_style .= 'visibility:hidden;';
		}
		$useAmazonAddressBook = ($_SESSION['cart']->get_content_type() === 'virtual') ? 'false' : 'true';
		$t_element = '<div data-gambio-widget="amazon_payment" data-amazon_payment-seller-id="' . $this->seller_id . '" data-amazon_payment-address-book="' . $useAmazonAddressBook . '" class="paywithamazonbtn" id="paywithamazonbtn_'.$t_button_number.'" style="'.$t_style.'">';
		$t_element .= '<img src="'.$t_button_url.'">';
		$t_element .= '</div>';

		return $t_element;
	}

	public function get_service_status()
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);
		$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
		$t_service_status_xml = $t_mws_request->proceed('GetServiceStatus');
		$t_service_status = simplexml_load_string($t_service_status_xml);
		$t_status = (string)$t_service_status->GetServiceStatusResult->Status;
		return $t_status;
	}

	public function check_credentials($seller_id, $aws_access_key, $secret_key)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$aws_access_key,
			$secret_key,
			$seller_id,
		);
		$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
		$t_order_reference_details_xml = $t_mws_request->proceed('GetOrderReferenceDetails', array('AmazonOrderReferenceId' => 'S00-0000000-0000000'));
		$t_order_reference_details = simplexml_load_string((string)$t_order_reference_details_xml);
		return $t_order_reference_details;
	}

	public function get_order_reference_details($p_amz_order_reference_id, $p_force_update = false)
	{
		$t_order_reference_details_xml = false;
		$t_from_db = false;
		$t_old_state = false;

		$t_get_query =
			'SELECT
				`last_details`, `state`
			FROM
				`amzadvpay_orders`
			WHERE
				`order_reference_id` = \':order_reference_id\'';
		$t_get_query = strtr($t_get_query, array(':order_reference_id' => $p_amz_order_reference_id));
		$t_get_result = xtc_db_query($t_get_query);
		while($t_get_row = xtc_db_fetch_array($t_get_result))
		{
			$t_old_state = $t_get_row['state'];
			if($p_force_update === false)
			{
				$t_order_reference_details_xml = $t_get_row['last_details'];
			}
		}
		if($t_order_reference_details_xml != false)
		{
			$t_from_db = true;
			$this->log('Retrieved OrderReferenceDetails from database for '.$p_amz_order_reference_id);
		}

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			if($p_force_update || $t_order_reference_details_xml == false)
			{
				$t_endpoint_url = $this->get_oap_endpoint();
				$t_request_params = array(
					$t_endpoint_url,
					$this->_configuration['aws_access_key'],
					$this->_configuration['secret_key'],
					$this->_configuration['seller_id'],
				);
				$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
				$t_order_reference_details_xml = $t_mws_request->proceed('GetOrderReferenceDetails', array('AmazonOrderReferenceId' => $p_amz_order_reference_id));
				$this->log('Received OrderReferenceDetails for '.$p_amz_order_reference_id.":\n".$t_order_reference_details_xml."\n");
			}

			$t_order_reference_details = simplexml_load_string((string)$t_order_reference_details_xml);
			if(isset($t_order_reference_details->Error))
			{
				$t_error_code = (string)$t_order_reference_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_order_reference_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_order_reference_details->Error->Type.' / '.
						(string)$t_order_reference_details->Error->Code.' / '.
						(string)$t_order_reference_details->Error->Message;
					$this->log('GetOrderReferenceDetails failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}


		if($t_from_db !== true)
		{
			// update db with newly acquired data
			$t_amzadvpay_orders_id = $this->_find_row_id('amzadvpay_orders', 'order_reference_id', $p_amz_order_reference_id);
			$t_order_reference_state = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderReferenceStatus->State;
			$t_last_update = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderReferenceStatus->LastUpdateTimestamp;
			$t_last_update_datetime = date('Y-m-d H:i:s', strtotime($t_last_update));
			$t_query =
				'UPDATE
					`amzadvpay_orders`
				SET
					`state` = \':state\',
					`last_update` = \':last_update\',
					`last_details` = \':last_details\'
				WHERE
					`amzadvpay_orders_id` = \':amzadvpay_orders_id\'';
			$t_query = strtr($t_query, array(
				':state' => xtc_db_input($t_order_reference_state),
				':last_update' => xtc_db_input($t_last_update_datetime),
				':last_details' => xtc_db_input($t_order_reference_details_xml),
				':amzadvpay_orders_id' => (int)$t_amzadvpay_orders_id,
			));
			xtc_db_query($t_query);
			// handle state changes
			if($t_old_state == 'Suspended' && $t_order_reference_state == 'Open')
			{
				$this->log('State of order '.$p_amz_order_reference_id.' changed from Suspended to Open');
				if($this->authorization_mode != 'manual')
				{
					$t_amount = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderTotal->Amount;
					$t_currency = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderTotal->CurrencyCode;
					$this->log('requesting new authorization of '.$t_amount.' '.$t_currency.' against '.$p_amz_order_reference_id);
					$t_capturenow = $this->capture_mode == 'immediate';
					$t_authorization_response = $this->authorize_payment($p_amz_order_reference_id, $t_amount, $t_currency, 0, $t_capturenow);
					$t_authorization_details = $t_authorization_response->AuthorizeResult->AuthorizationDetails;
					$t_state = (string)$t_authorization_details->AuthorizationStatus->State;
					$t_orders_id = $this->get_orders_id_for_orders_reference_id($p_amz_order_reference_id);
					if($t_state == 'Open')
					{
						$t_osh_comment = $this->get_text('orderref_state_change_suspended_open_new_auth_open');
						$this->_update_orders_status($t_orders_id, $this->orders_status_auth_open, $t_osh_comment);
					}
					elseif($t_state == 'Closed')
					{
						// with immediate capture the authorization will be closed immediately with MaxCapturesProcessed
						$t_reason_code = (string)$t_authorization_details->AuthorizationStatus->ReasonCode;
						$t_osh_comment = $this->get_text('payment_authorized');
						$this->_update_orders_status($t_orders_id, $this->orders_status_auth_open, $t_osh_comment);
					}
					else
					{
						$t_reason_code = (string)$t_authorization_details->AuthorizationStatus->ReasonCode;
						$t_osh_comment = $this->get_text('orderref_state_change_suspended_open_new_auth_fail');
						$t_osh_comment .= "\n".$this->get_text('reason').': '.$t_reason_code;
						$this->_update_orders_status($t_orders_id, $this->orders_status_auth_declined, $t_osh_comment);
					}
				}
			}
		}
		return $t_order_reference_details;
	}

	public function set_order_amount($p_amz_order_reference_id, $p_amount, $p_currency)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_order_reference_details_xml = $t_mws_request->proceed(
				'SetOrderReferenceDetails',
				array(
					'AmazonOrderReferenceId' => $p_amz_order_reference_id,
					'OrderReferenceAttributes.OrderTotal.Amount' => sprintf('%.2f', $p_amount),
					'OrderReferenceAttributes.OrderTotal.CurrencyCode' => $p_currency,
					'OrderReferenceAttributes.SellerOrderAttributes.StoreName' => STORE_NAME,
					'OrderReferenceAttributes.PlatformId' => 'AX37G17YUUI1C',
				)
			);
			$this->log('Received OrderReferenceDetails for '.$p_amz_order_reference_id.":\n".$t_order_reference_details_xml."\n");
			$t_order_reference_details = simplexml_load_string((string)$t_order_reference_details_xml);
			if(isset($t_order_reference_details->Error))
			{
				$t_error_code = (string)$t_order_reference_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_order_reference_details->Error->Type.' / '.
						(string)$t_order_reference_details->Error->Code.' / '.
						(string)$t_order_reference_details->Error->Message;
					$this->log('SetOrderReferenceDetails/set_order_amount failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}
		return $t_order_reference_details;
	}

	public function set_orders_id($p_amz_order_reference_id, $p_orders_id, $p_amount, $p_currency)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_order_reference_details_xml = $t_mws_request->proceed(
				'SetOrderReferenceDetails',
				array(
					'AmazonOrderReferenceId' => $p_amz_order_reference_id,
					'OrderReferenceAttributes.OrderTotal.Amount' => sprintf('%.2f', $p_amount),
					'OrderReferenceAttributes.OrderTotal.CurrencyCode' => $p_currency,
					'OrderReferenceAttributes.SellerOrderAttributes.SellerOrderId' => $p_orders_id,
				)
			);
			$this->log('set orders_id '.$p_orders_id.' for order with reference id '.$p_amz_order_reference_id);
			$t_order_reference_details = simplexml_load_string((string)$t_order_reference_details_xml);
			if(isset($t_order_reference_details->Error))
			{
				$t_error_code = (string)$t_order_reference_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_order_reference_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_order_reference_details->Error->Type.' / '.
						(string)$t_order_reference_details->Error->Code.' / '.
						(string)$t_order_reference_details->Error->Message;
					$this->log('SetOrderReferenceDetails/set_orders_id failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}
		return $t_order_reference_details;
	}

	public function confirm_order($p_amz_order_reference_id, $p_orders_id = null, $p_amount = null, $p_currency = null)
	{
		$t_orders_id = $p_orders_id;
		$t_amount = $p_amount;
		$t_currency = $p_currency;

		$this->log(sprintf("confirming order %d for %s %s", (int)$t_orders_id, (string)$t_amount, (string)$t_currency));

		if($p_orders_id !== null)
		{
			try
			{
				$this->set_orders_id($p_amz_order_reference_id, $p_orders_id, $p_amount, $p_currency);
			}
			catch(Exception $e)
			{
				throw $e;
			}
		}
		else
		{
			// re-confirm order, e.g. after payment method is changed b/c of InvalidPaymentMethod in sync-auth
			$t_orders_id = $this->get_orders_id_for_orders_reference_id($p_amz_order_reference_id);
			$t_order = new order($t_orders_id);
			$t_amount = $t_order->info['pp_total'];
			$t_currency = $t_order->info['currency'];
		}
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_confirm_order_response_xml = $t_mws_request->proceed(
				'ConfirmOrderReference',
				array(
					'AmazonOrderReferenceId' => $p_amz_order_reference_id,
				)
			);
			$this->log('confirmed order '.$t_orders_id.' / '.$p_amz_order_reference_id);
			$t_confirm_order_response = simplexml_load_string((string)$t_confirm_order_response_xml);
			if(isset($t_confirm_order_response->Error))
			{
				$t_error_code = (string)$t_confirm_order_response->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_confirm_order_response->Error->Type.' / '.
						(string)$t_confirm_order_response->Error->Code.' / '.
						(string)$t_confirm_order_response->Error->Message;
					$this->log('ConfirmOrderReference/confirm_order failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}

		if($p_orders_id !== null)
		{
			$t_orders_query =
				'INSERT INTO
					`amzadvpay_orders`
				SET
					`orders_id` = \':orders_id\',
					`order_reference_id` = \':order_ref_id\',
					`state` = \'Confirmed\'';
			$t_orders_query = strtr($t_orders_query, array(
				':orders_id' => (int)$t_orders_id,
				':order_ref_id' => xtc_db_input($p_amz_order_reference_id)
			));
			xtc_db_query($t_orders_query);
		}

		return $t_confirm_order_response;
	}

	public function close_order_reference($p_order_reference_id)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_close_order_details_xml = $t_mws_request->proceed('CloseOrderReference',
				array(
					'AmazonOrderReferenceId' => $p_order_reference_id,
				)
			);
			$this->log('Closed Order for '.$p_order_reference_id.":\n".$t_close_order_details_xml."\n");
			$t_close_order_details = simplexml_load_string((string)$t_close_order_details_xml);
			if(isset($t_close_order_details->Error))
			{
				$t_error_code = (string)$t_close_order_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_close_order_details->Error->Type.' / '.
						(string)$t_close_order_details->Error->Code.' / '.
						(string)$t_close_order_details->Error->Message;
					$this->log('CloseAuthorization failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}
		return $t_close_order_details;
	}

	public function update_delivery_address($p_amz_order_reference_id, $p_orders_id, $p_update_customer_data = false)
	{
		$t_order_reference_details = $this->get_order_reference_details($p_amz_order_reference_id);
		$t_name = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->Name;
		list($t_firstname, $t_lastname) = explode(' ', $t_name, 2);
		$t_street1 = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->AddressLine1;
		$t_street2 = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->AddressLine2;
		$t_street3 = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->AddressLine3;
		$t_street = '';
		if(!empty($t_street3))
		{
			$t_street = $t_street3;
			$t_company = $t_street2;
		}
		elseif(!empty($t_street2))
		{
			$t_street = $t_street2;
			$t_company = $t_street1;
		}
		else
		{
			$t_street = $t_street1;
			$t_company = '';
		}
		$t_postcode = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->PostalCode;
		$t_city = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->City;
		$t_country_iso2 = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->CountryCode;
		$t_country_name_and_address_format = $this->_get_country_name_and_address_format($t_country_iso2);
		//$t_company = '';

		$t_order_data = array(
			'delivery_name' => $t_name,
			'delivery_firstname' => $t_firstname,
			'delivery_lastname' => $t_lastname,
			// 'delivery_gender' =>
			'delivery_company' => $t_company,
			'delivery_street_address' => $t_street,
			'delivery_city' => $t_city,
			'delivery_postcode' => $t_postcode,
			'delivery_country' => $t_country_name_and_address_format['country_name'],
			'delivery_country_iso_code_2' => $t_country_iso2,
			'delivery_address_format_id' => $t_country_name_and_address_format['address_format_id'],
		);

		if($p_update_customer_data == true)
		{
			$t_customer_data = array();

			if(!empty($t_name))
			{
				$t_customer_data = array(
					'customers_name'              => $t_name,
					'customers_firstname'         => $t_firstname,
					'customers_lastname'          => $t_lastname,
					'customers_company'           => $t_company,
					'customers_street_address'    => $t_street,
					'customers_city'              => $t_city,
					'customers_postcode'          => $t_postcode,
					'customers_country'           => $t_country_name_and_address_format['country_name'],
					'customers_address_format_id' => $t_country_name_and_address_format['address_format_id'],
				);
			}
			$t_email = (string)$t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->Buyer->Email;
			if(empty($t_email) !== true)
			{
				$t_customer_data['customers_email_address'] = $t_email;
			}

			$t_order_data = array_merge($t_order_data, $t_customer_data);
		}

		xtc_db_perform('orders', $t_order_data, 'update', 'orders_id = \''.(int)$p_orders_id.'\'');
	}

	public function update_billing_address($p_orders_id, $p_billing_address)
	{
		list($t_firstname, $t_lastname) = explode(' ', $p_billing_address['name'], 2);
		$t_country_name_and_address_format = $this->_get_country_name_and_address_format($p_billing_address['country_iso2']);

		$t_order_data = array(
			'billing_name'               => $p_billing_address['name'],
			'billing_firstname'          => $t_firstname,
			'billing_lastname'           => $t_lastname,
			'billing_company'            => $p_billing_address['company'],
			'billing_street_address'     => $p_billing_address['street'],
			'billing_city'               => $p_billing_address['city'],
			'billing_postcode'           => $p_billing_address['postcode'],
			'billing_country'            => $t_country_name_and_address_format['country_name'],
			'billing_country_iso_code_2' => $p_billing_address['country_iso2'],
			'billing_address_format_id'  => $t_country_name_and_address_format['address_format_id'],
		);

		$checkCustomerQuery = 'SELECT customers_name, customers_firstname, customers_lastname, customers_street_address FROM orders WHERE orders_id = "' . (int)$p_orders_id . '"';
		$checkCustomerResult = xtc_db_query($checkCustomerQuery);
		while($checkCustomerRow = xtc_db_fetch_array($checkCustomerResult))
		{
			if($checkCustomerRow['customers_name']           === 'Amazon Amazon' &&
			   $checkCustomerRow['customers_firstname']      === 'Amazon' &&
			   $checkCustomerRow['customers_lastname']       === 'Amazon' &&
			   $checkCustomerRow['customers_street_address'] === 'Amazon')
			{
				// replace dummy customer data with billing address; necessary for guest orders of non-physical goods
				$t_order_data['customers_name']              = $p_billing_address['name'];
				$t_order_data['customers_firstname']         = $t_firstname;
				$t_order_data['customers_lastname']          = $t_lastname;
				$t_order_data['customers_company']           = $p_billing_address['company'];
				$t_order_data['customers_street_address']    = $p_billing_address['street'];
				$t_order_data['customers_city']              = $p_billing_address['city'];
				$t_order_data['customers_postcode']          = $p_billing_address['postcode'];
				$t_order_data['customers_country']           = $t_country_name_and_address_format['country_name'];
				$t_order_data['customers_address_format_id'] = $t_country_name_and_address_format['address_format_id'];
			}
		}

		xtc_db_perform('orders', $t_order_data, 'update', 'orders_id = \''.(int)$p_orders_id.'\'');
	}

	/** copies the delivery address into the billing address
	@param int $orders_id
	*/
	public function copy_delivery_address_to_billing_address($orders_id)
	{
		$orders_id = (int)$orders_id;
		$copy_query =
			'UPDATE
				`orders` o
			SET
				`billing_name` = `delivery_name`,
				`billing_firstname` = `delivery_firstname`,
				`billing_lastname` = `delivery_lastname`,
				`billing_gender` = `delivery_gender`,
				`billing_company` = `delivery_company`,
				`billing_street_address` = `delivery_street_address`,
				`billing_suburb` = `delivery_suburb`,
				`billing_city`  = `delivery_city`,
				`billing_postcode` = `delivery_postcode`,
				`billing_state` = `delivery_state`,
				`billing_country` = `delivery_country`,
				`billing_country_iso_code_2` = `delivery_country_iso_code_2`,
				`billing_address_format_id` = `delivery_address_format_id`
			WHERE
			 	`orders_id` = \':orders_id\'
				';
		$copy_query = strtr($copy_query, array(':orders_id' => $orders_id));
		xtc_db_query($copy_query);
	}

	public function authorize_payment($p_amz_order_reference_id, $p_amount, $p_currency, $p_timeout = 1440, $p_capture_now = false, $p_seller_authorization_note = '')
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);
		$t_auth_ref_id = uniqid();

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_authorize_params = array(
					'AmazonOrderReferenceId' => $p_amz_order_reference_id,
					'AuthorizationReferenceId' => $t_auth_ref_id,
					'AuthorizationAmount.Amount' => $p_amount,
					'AuthorizationAmount.CurrencyCode' => $p_currency,
					'TransactionTimeout' => (int)$p_timeout,
					'CaptureNow' => $p_capture_now == true ? 'true' : 'false',
				);
			if(empty($p_seller_authorization_note) !== true)
			{
				$t_authorize_params['SellerAuthorizationNote'] = $p_seller_authorization_note;
			}
			$t_authorization_details_xml = $t_mws_request->proceed('Authorize', $t_authorize_params);
			$this->log('authorization of '.$p_amount.' '.$p_currency.' against '.$p_amz_order_reference_id.' with AuthRefId '.$t_auth_ref_id);
			$this->log("authorization details:\n".$t_authorization_details_xml);
			$t_authorization_details = simplexml_load_string((string)$t_authorization_details_xml);
			if(isset($t_authorization_details->Error))
			{
				$t_error_code = (string)$t_authorization_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_authorization_details->Error->Type.' / '.
						(string)$t_authorization_details->Error->Code.' / '.
						(string)$t_authorization_details->Error->Message;
					$this->log('authorize failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}
		$t_amazon_authorization_id = (string)$t_authorization_details->AuthorizeResult->AuthorizationDetails->AmazonAuthorizationId;
		// keep track of authorizations
		$t_orders_id = $this->get_orders_id_for_orders_reference_id($p_amz_order_reference_id);
		$this->_insert_authorization($t_orders_id, $p_amz_order_reference_id, $t_amazon_authorization_id);
		$t_authorization_state = (string)$t_authorization_details->AuthorizeResult->AuthorizationDetails->AuthorizationStatus->State;
		if($p_capture_now == true && $t_authorization_state != 'Declined')
		{
			foreach($t_authorization_details->AuthorizeResult->AuthorizationDetails->IdList->member as $t_capture_node)
			{
				$t_capture_id = (string)$t_capture_node;
				$this->_insert_capture($t_orders_id, $p_amz_order_reference_id, $t_amazon_authorization_id, $t_capture_id);
			}
		}
		return $t_authorization_details;
	}

	protected function _insert_authorization($p_orders_id, $p_order_reference_id, $p_authorization_reference_id)
	{
		$t_auth_data = array(
			'orders_id' => (int)$p_orders_id,
			'order_reference_id' => $p_order_reference_id,
			'authorization_reference_id' => $p_authorization_reference_id,
		);
		xtc_db_perform('amzadvpay_authorizations', $t_auth_data, 'insert');
	}

	public function get_authorization_details($p_authorization_reference_id, $p_order_reference_id, $p_force_update = false)
	{
		$t_authorization_details_xml = false;
		$t_from_db = false;
		$t_old_state = false;

		$t_get_query =
			'SELECT
				`last_details`, `state`
			FROM
				`amzadvpay_authorizations`
			WHERE
				`authorization_reference_id` = \':authorization_reference_id\'';
		$t_get_query = strtr($t_get_query, array(':authorization_reference_id' => $p_authorization_reference_id));
		$t_get_result = xtc_db_query($t_get_query);
		while($t_get_row = xtc_db_fetch_array($t_get_result))
		{
			$t_old_state = $t_get_row['state'];
			if($p_force_update === false)
			{
				$t_authorization_details_xml = $t_get_row['last_details'];
			}
		}

		if($t_authorization_details_xml != false)
		{
			$t_from_db = true;
			$this->log('Retrieved AuthorizationReferenceDetails from database for '.$p_authorization_reference_id);
		}

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			if($p_force_update || $t_authorization_details_xml == false)
			{
				$t_endpoint_url = $this->get_oap_endpoint();
				$t_request_params = array(
					$t_endpoint_url,
					$this->_configuration['aws_access_key'],
					$this->_configuration['secret_key'],
					$this->_configuration['seller_id'],
				);
				$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
				$t_authorization_details_xml = $t_mws_request->proceed('GetAuthorizationDetails',
					array(
						'AmazonAuthorizationId' => $p_authorization_reference_id,
					)
				);
				$this->log('Received AuthorizationDetails for '.$p_authorization_reference_id.":\n".$t_authorization_details_xml."\n");
			}

			$t_authorization_details = simplexml_load_string((string)$t_authorization_details_xml);
			if(isset($t_authorization_details->Error))
			{
				$t_error_code = (string)$t_authorization_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_authorization_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_authorization_details->Error->Type.' / '.
						(string)$t_authorization_details->Error->Code.' / '.
						(string)$t_authorization_details->Error->Message;
					$this->log('GetAuthorizationDetails failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}

		if($t_from_db !== true)
		{
			$t_amzadvpay_authorizations_id = $this->_find_row_id('amzadvpay_authorizations', 'authorization_reference_id', $p_authorization_reference_id);
			$t_orders_id = $this->get_orders_id_for_orders_reference_id($p_order_reference_id);
			if($t_orders_id === false)
			{
				$this->log("failed to retrieve orders_id for $p_order_reference_id");
			}
			if($t_amzadvpay_authorizations_id === false)
			{
				$this->_insert_authorization($t_orders_id, $p_order_reference_id, $p_authorization_reference_id);
				$t_old_state = '';
			}
			$t_authorization_state = (string)$t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->State;
			$t_authorization_last_update = (string)$t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->LastUpdateTimestamp;
			$t_authorization_last_update_datetime = $this->_convert_xml_timestamp_to_datetime($t_authorization_last_update);
			$t_query =
				'UPDATE
					`amzadvpay_authorizations`
				SET
					`state` = \':state\',
					`last_update` = \':last_update\',
					`last_details` = \':last_details\'
				WHERE
					`authorization_reference_id` = \':authorization_reference_id\'';
			$t_query = strtr($t_query, array(
				':state' => xtc_db_input($t_authorization_state),
				':last_update' => xtc_db_input($t_authorization_last_update_datetime),
				':last_details' => xtc_db_input($t_authorization_details_xml),
				':authorization_reference_id' => xtc_db_input($p_authorization_reference_id),
			));
			xtc_db_query($t_query);

			$this->log("authorization state: $t_old_state => $t_authorization_state");
			//if(($t_old_state == 'Pending' || $t_old_state == 'Declined' || $t_old_state == '') && $t_authorization_state == 'Open')
			$t_update_billing_address = false;
			if($t_old_state != 'Open' && $t_authorization_state == 'Open')
			{
				$this->log("transition to Open state detected, updating order's status of $t_orders_id to ".$this->orders_status_auth_open);
				$this->_update_orders_status($t_orders_id, $this->orders_status_auth_open);
				$t_update_billing_address = true;
			}

			if($t_old_state != 'Closed' && $t_authorization_state == 'Closed')
			{
				$this->log("transition to Closed state detected");
				if($this->capture_mode == 'immediate')
				{
					$t_update_billing_address = true;
				}
			}

			if($t_update_billing_address == true)
			{
				if(empty($t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationBillingAddress) !== true)
				{
					$this->log("updating billing address");
					$t_billing_address = $t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationBillingAddress;
					$street1 = (string)$t_billing_address->AddressLine1;
					$street2 = (string)$t_billing_address->AddressLine2;
					$street3 = (string)$t_billing_address->AddressLine3;
					$this->log("street1: $street1, street2: $street2, street3: $street3");
					if(!empty($street3))
					{
						$street = $street3;
						$company = $street2;
					}
					elseif(!empty($street2))
					{
						$street = $street2;
						$company = $street1;
					}
					else
					{
						$street = $street1;
						$company = '';
					}
					$t_billing_address_data = array(
						'name' => (string)$t_billing_address->Name,
						'street' => $street,
						'company' => $company,
						'city' => (string)$t_billing_address->City,
						'postcode' => (string)$t_billing_address->PostalCode,
						'country_iso2' => (string)$t_billing_address->CountryCode,
					);
					$this->update_billing_address($t_orders_id, $t_billing_address_data);
				}
				else
				{
					// no billing address in authorization details - this happens if Amazon does not have a valid VAT ID on file for the merchant
					// As a workaround the delivery address gets copied over.
					$this->copy_delivery_address_to_billing_address($t_orders_id);
				}
			}

			if(($t_old_state == 'Pending' || $t_old_state == '') && $t_authorization_state == 'Declined')
			{
				$t_reason_code = (string)$t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->ReasonCode;
				$t_osh_comment = $this->get_text('reason').': '.$t_reason_code;
				if($this->authorization_mode != 'auto-sync')
				{
					if($t_reason_code == 'InvalidPaymentMethod')
					{
						$t_osh_comment .= "\n".$this->get_text('invalid_payment_mail_sent');
						$this->log('authorization is reported as Declined/InvalidPaymentMethod, contacting buyer');
						$this->_send_invalid_payment_method_notification($t_orders_id);
					}
					else
					{
						$t_osh_comment .= "\n".$this->get_text('invalid_payment_mail_sent');
						$this->log('authorization is reported as Declined/'.$t_reason_code.', contacting buyer');
						$this->_send_invalid_payment_method_notification($t_orders_id, true);
					}
				}
				$this->_update_orders_status($t_orders_id, $this->orders_status_auth_declined, $t_osh_comment);
			}
			if($t_old_state != 'Declined' && $t_authorization_state == 'Declined')
			{
				$t_reason_code = (string)$t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->ReasonCode;
				if($t_reason_code == 'AmazonRejected')
				{
					$this->log('Authorization declined by Amazon, closing Order Reference');
					try
					{
						$this->close_order_reference($p_order_reference_id);
						$t_osh_comment = $this->get_text('order_closed_due_to_rejected_authorization');
						$this->_update_orders_status($t_orders_id, $this->orders_status_auth_declined_hard, $t_osh_comment);
					}
					catch(Exception $e)
					{
						$this->log('ERROR closing Order Reference: '.$e->getMessage());
					}
				}
			}
		}

		return $t_authorization_details;
	}

	protected function _send_invalid_payment_method_notification($p_orders_id, $p_hardfail = false)
	{
		require_once DIR_FS_CATALOG.'inc/xtc_php_mail.inc.php';
		$t_order = new order((int)$p_orders_id);

		$t_body_text = $this->get_text('invalid_payment_notification');

		$coo_amazonmailcontentview = MainFactory::create_object('AmazonMailContentView');

		// ASSIGN VARIABLES
		$coo_amazonmailcontentview->set_('language', $_SESSION['language']);
		$coo_amazonmailcontentview->set_('language_id', $_SESSION['languages_id']);
		$coo_amazonmailcontentview->set_('name', $t_order->customer['name']);
		$coo_amazonmailcontentview->set_('orders_id', $p_orders_id);
		$coo_amazonmailcontentview->set_('orderdate', $t_order->info['date_purchased']);

		// GET E-MAIL LOGO
		$t_mail_logo = '';
		$t_logo_mail = MainFactory::create_object('GMLogoManager', array("gm_logo_mail"));
		if($t_logo_mail->logo_use == '1')
		{
			$t_mail_logo = $t_logo_mail->get_logo();
		}
		$coo_amazonmailcontentview->set_('mail_logo', $t_mail_logo);

		// GET MAIL CONTENTS ARRAY
		$t_mail_content_array = $coo_amazonmailcontentview->get_mail_content_array($p_hardfail);

		// GET HTML MAIL CONTENT
		$t_html_mail = $t_mail_content_array['html'];

		// GET TXT MAIL CONTENT
		$t_txt_mail = $t_mail_content_array['txt'];

		$t_from_address = EMAIL_BILLING_ADDRESS;
		$t_from_name = EMAIL_BILLING_NAME;
		$t_to_address = $t_order->customer['email_address'];
		$t_to_name = $t_order->customer['name'];
		$t_subject = $this->get_text('invalid_payment_notification_subject');
		$t_forwarding = '';
		$t_reply_address = EMAIL_BILLING_REPLY_ADDRESS;
		$t_reply_name = EMAIL_BILLING_REPLY_ADDRESS_NAME;
		$t_attachment = '';
		$t_attachments = '';

		$t_success = xtc_php_mail(
			$t_from_address,
			$t_from_name,
			$t_to_address,
			$t_to_name,
			$t_forwarding,
			$t_reply_address,
			$t_reply_name,
			$t_attachment,
			$t_attachments,
			$t_subject,
			$t_html_mail,
			$t_txt_mail
		);
		return $t_success;
	}

	public function close_authorization($p_authorization_reference_id)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_close_authorization_details_xml = $t_mws_request->proceed('CloseAuthorization',
				array(
					'AmazonAuthorizationId' => $p_authorization_reference_id,
				)
			);
			$this->log('Closed Authorization for '.$p_authorization_reference_id.":\n".$t_close_authorization_details_xml."\n");
			$t_close_authorization_details = simplexml_load_string((string)$t_close_authorization_details_xml);
			if(isset($t_close_authorization_details->Error))
			{
				$t_error_code = (string)$t_close_authorization_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_order_reference_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_close_authorization_details->Error->Type.' / '.
						(string)$t_close_authorization_details->Error->Code.' / '.
						(string)$t_close_authorization_details->Error->Message;
					$this->log('CloseAuthorization failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}
		return $t_close_authorization_details;
	}

	public function capture_payment($p_auth_ref_id, $p_amount, $p_currency, $p_simulation_string = null)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);
		$t_capture_ref_id = uniqid();
		$t_seller_capture_note = '';
		if($this->mode == 'sandbox' && $p_simulation_string !== null)
		{
			$t_seller_capture_note = (string)$p_simulation_string;
		}

		$t_orders_id = $this->get_orders_id_for_authorization_reference_id($p_auth_ref_id);
		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_capture_details_xml = $t_mws_request->proceed(
				'Capture',
				array(
					'AmazonAuthorizationId' => $p_auth_ref_id,
					'CaptureReferenceId' => $t_capture_ref_id,
					'CaptureAmount.Amount' => $p_amount,
					'CaptureAmount.CurrencyCode' => $p_currency,
					'SellerCaptureNote' => $t_seller_capture_note,
				)
			);
			$this->log('capture of '.$p_amount.' '.$p_currency.' against '.$p_auth_ref_id);
			$this->log("capture details:\n".$t_capture_details_xml);
			$t_capture_details = simplexml_load_string((string)$t_capture_details_xml);
			if(isset($t_capture_details->Error))
			{
				$t_error_code = (string)$t_capture_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_capture_details->Error->Type.' / '.
						(string)$t_capture_details->Error->Code.' / '.
						(string)$t_capture_details->Error->Message;
					$this->log('capture failed, '.$t_error_message);
					$this->_update_orders_status($t_orders_id, $this->orders_status_capture_failed, $this->get_text('capture failed')."\n".$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}

		$t_order_reference_id = $this->get_order_reference_for_orders_id($t_orders_id);
		$t_capture_reference_id = (string)$t_capture_details->CaptureResult->CaptureDetails->AmazonCaptureId;
		$t_capture_state = (string)$t_capture_details->CaptureResult->CaptureDetails->CaptureState->State;
		$this->_insert_capture($t_orders_id, $t_order_reference_id, $p_auth_ref_id, $t_capture_reference_id, $t_capture_state);
		return $t_capture_details;
	}

	protected function _insert_capture($p_orders_id, $p_order_reference_id, $p_authorization_reference_id, $p_capture_reference_id, $p_capture_state = 'Pending')
	{
		$t_query =
			'INSERT INTO
				`amzadvpay_captures`
			SET
				`state` = \':state\',
				`orders_id` = \':orders_id\',
				`order_reference_id` = \':order_reference_id\',
				`authorization_reference_id` = \':authorization_reference_id\',
				`capture_reference_id` = \':capture_reference_id\'';
		$t_query = strtr($t_query, array(
			':state' => xtc_db_input($p_capture_state),
			':orders_id' => (int)$p_orders_id,
			':order_reference_id' => xtc_db_input($p_order_reference_id),
			':authorization_reference_id' => xtc_db_input($p_authorization_reference_id),
			':capture_reference_id' => xtc_db_input($p_capture_reference_id),
		));
		xtc_db_query($t_query);
	}

	public function get_capture_details($p_capture_id, $p_authorization_reference_id = '', $p_order_reference_id = '', $p_force_update = false)
	{
		$t_capture_details_xml = false;
		$t_from_db = false;
		$t_old_state = false;

		$t_get_query =
			'SELECT
				`last_details`, `state`
			FROM
				`amzadvpay_captures`
			WHERE
				`capture_reference_id` = \':capture_reference_id\'';
		$t_get_query = strtr($t_get_query, array(':capture_reference_id' => $p_capture_id));
		$t_get_result = xtc_db_query($t_get_query);
		while($t_get_row = xtc_db_fetch_array($t_get_result))
		{
			$t_old_state = $t_get_row['state'];
			if($p_force_update === false)
			{
				$t_capture_details_xml = $t_get_row['last_details'];
			}
		}
		if($t_capture_details_xml !== false)
		{
			$t_from_db = true;
			$this->log('Retrieved CaptureDetails from database');
		}

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			if($p_force_update || $t_capture_details_xml == false)
			{
				$t_endpoint_url = $this->get_oap_endpoint();
				$t_request_params = array(
					$t_endpoint_url,
					$this->_configuration['aws_access_key'],
					$this->_configuration['secret_key'],
					$this->_configuration['seller_id'],
				);
				$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
				$t_capture_details_xml = $t_mws_request->proceed('GetCaptureDetails',
					array(
						'AmazonCaptureId' => $p_capture_id,
					)
				);
				$this->log('Received CaptureDetails for '.$p_capture_id.":\n".$t_capture_details_xml."\n");
			}

			$t_capture_details = simplexml_load_string((string)$t_capture_details_xml);
			if(isset($t_capture_details->Error))
			{
				$t_error_code = (string)$t_capture_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_order_reference_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_capture_details->Error->Type.' / '.
						(string)$t_capture_details->Error->Code.' / '.
						(string)$t_capture_details->Error->Message;
					$this->log('GetcaptureDetails failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}

		if($t_from_db === false)
		{
			$t_orders_id = $this->get_orders_id_for_authorization_reference_id($p_authorization_reference_id);
			$t_row_id = $this->_find_row_id('amzadvpay_captures', 'capture_reference_id', $p_capture_id);
			if($t_row_id === false)
			{
				// capture has not been recorded yet
				$t_order_reference_id = $p_order_reference_id;
				if(empty($t_order_reference_id) == true)
				{
					$t_order_reference_id = $this->get_order_reference_for_orders_id($t_orders_id);
				}
				$this->_insert_capture($t_orders_id, $t_order_reference_id, $p_authorization_reference_id, $p_capture_id);
			}
			$t_state = (string)$t_capture_details->GetCaptureDetailsResult->CaptureDetails->CaptureStatus->State;
			$t_last_update = (string)$t_capture_details->GetCaptureDetailsResult->CaptureDetails->CaptureStatus->LastUpdateTimestamp;
			$t_last_update_datetime = $this->_convert_xml_timestamp_to_datetime($t_last_update);
			$t_query =
				'UPDATE
					`amzadvpay_captures`
				SET
					`state` = \':state\',
					`last_update` = \':last_update\',
					`last_details` = \':last_details\'
				WHERE
					`capture_reference_id` = \':capture_reference_id\'';
			$t_query = strtr($t_query, array(
				':state' => xtc_db_input($t_state),
				':last_update' => $t_last_update_datetime,
				':last_details' => $t_capture_details_xml,
				':capture_reference_id' => xtc_db_input($p_capture_id),
			));
			xtc_db_query($t_query);

			if($t_old_state != $t_state)
			{
				$this->log('state change for capture '.$p_capture_id.' '.$t_old_state.' -> '.$t_state);
			}

			if($t_old_state != 'Completed' && $t_state == 'Completed')
			{
				$this->log('capture '.$p_capture_id.' completed');
				$this->_update_orders_status($t_orders_id, $this->orders_status_captured, $this->get_text('capture_completed'));
			}

			if($t_old_state != 'Closed' && $t_state == 'Closed')
			{
				$this->log('capture '.$p_capture_id.' closed');
			}

			if($t_old_state != 'Declined' && $t_state == 'Declined')
			{
				$this->log('capture '.$p_capture_id.' declined');
				$this->_update_orders_status($t_orders_id, $this->orders_status_capture_failed, $this->get_text('capture_failed'));
			}
		}

		return $t_capture_details;
	}


	public function refund_payment($p_capture_id, $p_amount, $p_currency)
	{
		$t_endpoint_url = $this->get_oap_endpoint();
		$t_request_params = array(
			$t_endpoint_url,
			$this->_configuration['aws_access_key'],
			$this->_configuration['secret_key'],
			$this->_configuration['seller_id'],
		);
		$t_refund_ref_id = uniqid();

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
			$t_refund_details_xml = $t_mws_request->proceed(
				'Refund',
				array(
					'AmazonCaptureId' => $p_capture_id,
					'RefundReferenceId' => $t_refund_ref_id,
					'RefundAmount.Amount' => $p_amount,
					'RefundAmount.CurrencyCode' => $p_currency,
				)
			);
			$this->log('refund of '.$p_amount.' '.$p_currency.' against '.$p_capture_id);
			$this->log("refund details:\n".$t_refund_details_xml);
			$t_refund_details = simplexml_load_string((string)$t_refund_details_xml);
			if(isset($t_refund_details->Error))
			{
				$t_error_code = (string)$t_order_reference_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_order_reference_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_refund_details->Error->Type.' / '.
						(string)$t_refund_details->Error->Code.' / '.
						(string)$t_refund_details->Error->Message;
					$this->log('refund failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}

		$t_authorization_reference_id = $this->get_authorization_reference_id_for_capture_reference_id($p_capture_id);
		$t_orders_id = $this->get_orders_id_for_authorization_reference_id($t_authorization_reference_id);
		$t_order_reference_id = $this->get_order_reference_for_orders_id($t_orders_id);
		$t_refund_reference_id = (string)$t_refund_details->RefundResult->RefundDetails->AmazonRefundId;
		$this->_insert_refund($t_orders_id, $t_order_reference_id, $t_authorization_reference_id, $p_capture_id, $t_refund_reference_id);

		return $t_refund_details;
	}

	protected function _insert_refund($p_orders_id, $p_order_reference_id, $p_authorization_reference_id, $p_capture_reference_id, $p_refund_reference_id)
	{
		$t_query =
			'INSERT INTO
				`amzadvpay_refunds`
			SET
				`orders_id` = \':orders_id\',
				`order_reference_id` = \':order_reference_id\',
				`authorization_reference_id` = \':authorization_reference_id\',
				`capture_reference_id` = \':capture_reference_id\',
				`refund_reference_id` = \':refund_reference_id\'';
		$t_query = strtr($t_query, array(
			':orders_id' => (int)$p_orders_id,
			':order_reference_id' => xtc_db_input($p_order_reference_id),
			':authorization_reference_id' => xtc_db_input($p_authorization_reference_id),
			':capture_reference_id' => xtc_db_input($p_capture_reference_id),
			':refund_reference_id' => xtc_db_input($p_refund_reference_id),
		));
		xtc_db_query($t_query);
	}

	public function get_refund_details($p_refund_id, $p_capture_reference_id = '', $p_authorization_reference_id = '', $p_order_reference_id = '', $p_force_update = false)
	{
		$t_refund_details_xml = false;
		$t_from_db = false;

		if($p_force_update === false)
		{
			$t_get_query =
				'SELECT
					`last_details`
				FROM
					`amzadvpay_refunds`
				WHERE
					`refund_reference_id` = \':refund_id\'';
			$t_get_query = strtr($t_get_query, array(':refund_id' => $p_refund_id));
			$t_get_result = xtc_db_query($t_get_query);
			while($t_get_row = xtc_db_fetch_array($t_get_result))
			{
				$t_refund_details_xml = $t_get_row['last_details'];
			}
			if($t_refund_details_xml !== false)
			{
				$t_from_db = true;
				$this->log('Retrieved RefundDetails from database');
			}
		}

		$t_throttle_retries = $this->_throttle_max_retries;
		$t_error = true;
		while($t_error === true)
		{
			if($p_force_update || $t_refund_details_xml == false)
			{
				$t_endpoint_url = $this->get_oap_endpoint();
				$t_request_params = array(
					$t_endpoint_url,
					$this->_configuration['aws_access_key'],
					$this->_configuration['secret_key'],
					$this->_configuration['seller_id'],
				);
				$t_mws_request = MainFactory::create_object('AmazonMWSRequest', $t_request_params);
				$t_refund_details_xml = $t_mws_request->proceed('GetRefundDetails',
					array(
						'AmazonRefundId' => $p_refund_id,
					)
				);
				$this->log('Received RefundDetails for '.$p_refund_id.":\n".$t_refund_details_xml."\n");
			}

			$t_refund_details = simplexml_load_string((string)$t_refund_details_xml);
			if(isset($t_refund_details->Error))
			{
				$t_error_code = (string)$t_refund_details->Error->Code;
				if($t_throttle_retries > 0 && $t_error_code == 'RequestThrottled')
				{
					$t_refund_details_xml = false;
					$t_throttle_retries--;
					$t_sleep_time = ($this->_throttle_max_retries - $t_throttle_retries) * $this->_retry_delay * 1000;
					$this->log('Request throttled, delaying '.($t_sleep_time/1000).'ms before retrying');
					usleep($t_sleep_time);
				}
				else
				{
					$t_error_message =
						'ERROR: '.
						(string)$t_refund_details->Error->Type.' / '.
						(string)$t_refund_details->Error->Code.' / '.
						(string)$t_refund_details->Error->Message;
					$this->log('GetRefundDetails failed, '.$t_error_message);
					throw new AmazonAdvancedPaymentException($t_error_message);
				}
			}
			else
			{
				$t_error = false;
			}
		}

		if($t_from_db !== true)
		{
			$t_row_id = $this->_find_row_id('amzadvpay_refunds', 'refund_reference_id', $p_refund_id);
			if($t_row_id == false)
			{
				$t_capture_reference_id = $p_capture_reference_id;
				if(empty($t_capture_reference_id) == true)
				{
					$t_capture_reference_id = $this->get_capture_reference_id_for_refund_reference_id($p_refund_id);
				}
				$t_authorization_reference_id = $p_authorization_reference_id;
				if(empty($t_authorization_reference_id) == true)
				{
					$t_authorization_reference_id = $this->get_authorization_reference_id_for_capture_reference_id($t_capture_reference_id);
				}
				$t_orders_id = $this->get_orders_id_for_authorization_reference_id($t_authorization_reference_id);
				$t_order_reference_id = $p_order_reference_id;
				if(empty($t_order_reference_id))
				{
					$t_order_reference_id = $this->get_order_reference_for_orders_id($t_orders_id);
				}
				$this->_insert_refund($t_orders_id, $t_order_reference_id, $t_authorization_reference_id, $t_capture_reference_id, $p_refund_id);
			}

			$t_state = (string)$t_refund_details->GetRefundDetailsResult->RefundDetails->RefundStatus->State;
			$t_last_update = (string)$t_refund_details->GetRefundDetailsResult->RefundDetails->RefundStatus->LastUpdateTimestamp;
			$t_last_update_datetime = $this->_convert_xml_timestamp_to_datetime($t_last_update);

			$t_query =
				'UPDATE
					`amzadvpay_refunds`
				SET
					`state` = \':state\',
					`last_update` = \':last_update\',
					`last_details` = \':last_details\'
				WHERE
					`refund_reference_id` = \':refund_reference_id\'';
			$t_query = strtr($t_query, array(
				':state' => xtc_db_input($t_state),
				':last_update' => xtc_db_input($t_last_update_datetime),
				':last_details' => xtc_db_input($t_refund_details_xml),
				':refund_reference_id' => xtc_db_input($p_refund_id),
			));
			xtc_db_query($t_query);
		}

		return $t_refund_details;
	}

	/**
	polls all OrderReference data and referenced data
	*/
	public function poll_data($p_order_reference_id)
	{
		$t_force_update = true;
		$t_order_reference_details = $this->get_order_reference_details($p_order_reference_id, $t_force_update);
		if(isset($t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->IdList))
		{
			foreach($t_order_reference_details->GetOrderReferenceDetailsResult->OrderReferenceDetails->IdList->member as $auth_member)
			{
				$t_amz_auth_id = (string)$auth_member;
				$t_authorization_details = $this->get_authorization_details($t_amz_auth_id, $p_order_reference_id, $t_force_update);
				if(isset($t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->IdList))
				{
					foreach($t_authorization_details->GetAuthorizationDetailsResult->AuthorizationDetails->IdList->member as $capture_member)
					{
						$t_capture_ref_id = (string)$capture_member;
						$t_capture_details = $this->get_capture_details($t_capture_ref_id, $t_amz_auth_id, $t_order_reference_id, $t_force_update);
						if(isset($t_capture_details->GetCaptureDetailsResult->CaptureDetails->IdList))
						{
							foreach($t_capture_details->GetCaptureDetailsResult->CaptureDetails->IdList->member as $refund_member)
							{
								$t_refund_id = (string)$refund_member;
								$t_refund_details = $this->get_refund_details($t_refund_id, $t_capture_ref_id, $t_amz_auth_id, $t_order_reference_id, $t_force_update);
							}
						}
					}
				}
			}
		}
	}


	/* ---------------------------------------------------------------------------------------------- */

	public function is_known_entity($p_type, $p_reference_id)
	{
		$t_is_known = false;
		switch(strtolower($p_type))
		{
			case 'order':
				$t_is_known = $this->_find_row_id('amzadvpay_orders', 'order_reference_id', $p_reference_id) !== false;
				break;
			case 'authorization':
				$t_is_known = $this->_find_row_id('amzadvpay_authorizations', 'authorization_reference_id', $p_reference_id) !== false;
				break;
			case 'capture':
				$t_is_known = $this->_find_row_id('amzadvpay_captures', 'capture_reference_id', $p_reference_id) !== false;
				break;
			case 'refund':
				$t_is_known = $this->_find_row_id('amzadvpay_refunds', 'refund_reference_id', $p_reference_id) !== false;
				break;
			default:
				throw new AmazonAdvancedPaymentException('unknown entity type '.$p_type);
		}
		return $t_is_known;
	}


	protected function _find_row_id($p_table, $p_column, $p_value)
	{
		$t_id = false;
		$t_valid_tables = array('amzadvpay_orders', 'amzadvpay_authorizations', 'amzadvpay_captures', 'amzadvpay_refunds');
		if(in_array($p_table, $t_valid_tables) !== true)
		{
			throw new AmazonAdvancedPaymentException('invalid table name');
		}

		$t_query =
			'SELECT
				:tablename_id
			FROM
				:tablename
			WHERE
				`:column` = \':value\'';
		$t_query = strtr($t_query, array(
			':tablename' => xtc_db_input($p_table),
			':column' => xtc_db_input($p_column),
			':value' => xtc_db_input($p_value),
		));

		$t_ids = array();
		$t_result = xtc_db_query($t_query, 'db_link', false);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_ids[] = $t_row[$p_table.'_id'];
			$t_id = $t_row[$p_table.'_id'];
		}

		return $t_id;
	}

	public function get_orders_id_for_orders_reference_id($p_amz_order_reference_id)
	{
		$t_query =
			'SELECT
				`orders_id`
			FROM
				`amzadvpay_orders`
			WHERE
				`order_reference_id` = \':order_ref_id\'';
		$t_query = strtr($t_query, array(':order_ref_id' => xtc_db_input($p_amz_order_reference_id)));
		$t_orders_id = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_orders_id = $t_row['orders_id'];
		}
		return $t_orders_id;
	}

	public function get_order_reference_for_orders_id($p_orders_id)
	{
		$t_query =
			'SELECT
				`order_reference_id`
			FROM
				`amzadvpay_orders`
			WHERE
				`orders_id` = \':orders_id\'';
		$t_query = strtr($t_query, array(':orders_id' => (int)$p_orders_id));
		$t_order_reference_id = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_order_reference_id = $t_row['order_reference_id'];
		}
		return $t_order_reference_id;
	}

	public function get_orders_id_for_authorization_reference_id($p_amz_authorization_id)
	{
		$t_query =
			'SELECT
				`orders_id`
			FROM
				`amzadvpay_authorizations`
			WHERE
				`authorization_reference_id` = \':auth_id\'';
		$t_query = strtr($t_query, array(':auth_id' => xtc_db_input($p_amz_authorization_id)));
		$t_orders_id = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_orders_id = $t_row['orders_id'];
		}
		return $t_orders_id;
	}

	public function get_authorization_reference_id_for_capture_reference_id($p_capture_id)
	{
		$t_query =
			'SELECT
				`authorization_reference_id`
			FROM
				`amzadvpay_captures`
			WHERE
				`capture_reference_id` = \':capture_id\'';
		$t_query = strtr($t_query, array(':capture_id' => xtc_db_input($p_capture_id)));
		$t_authorization_id = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_authorization_id = $t_row['authorization_reference_id'];
		}
		return $t_authorization_id;
	}

	public function get_capture_reference_id_for_refund_reference_id($p_refund_reference_id)
	{
		$t_query =
			'SELECT
				`capture_reference_id`
			FROM
				`amzadvpay_refunds`
			WHERE
				`refund_reference_id` = \':refund_reference_id\'';
		$t_query = strtr($t_query, array(
			':refund_reference_id' => xtc_db_input($p_refund_reference_id),
		));
		$t_capture_reference_id = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_capture_reference_id = $t_row['capture_reference_id'];
		}
		return $t_capture_reference_id;
	}

	protected function _convert_xml_timestamp_to_datetime($p_timestamp)
	{
		$t_real_timestamp = strtotime($p_timestamp);
		$t_datetime = date('Y-m-d H:i:s', $t_real_timestamp);
		return $t_datetime;
	}

	protected function _update_orders_status($p_orders_id, $p_orders_status_id, $p_comments = '')
	{
		xtc_db_perform(
			'orders',
			array(
				'orders_status' => (int)$p_orders_status_id,
				'last_modified' => 'now()'
			),
			'update',
			'orders_id = '.(int)$p_orders_id);
		xtc_db_perform(
			'orders_status_history',
			array(
				'orders_id' => (int)$p_orders_id,
				'orders_status_id' => (int)$p_orders_status_id,
				'date_added' => 'now()',
				'customer_notified' => '0',
				'comments' => $p_comments,
			),
			'insert');
	}
}

class AmazonAdvancedPaymentException extends Exception {}
