<?php
/* --------------------------------------------------------------
	PayPalConfigurationStorage.inc.php 2015-05-06
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * subclass of ConfigurationStorage for parameters concerning PayPal3
 */
class PayPalConfigurationStorage extends ConfigurationStorage
{
	/**
	 * namespace inside the configuration storage
	 */
	const CONFIG_STORAGE_NAMESPACE = 'modules/payment/paypal3';

	/**
	 * array holding default values to be used in absence of configured values
	 */
	protected $default_configuration;

	/**
	 * constructor; initializes default configuration
	 */
	public function __construct()
	{
		parent::__construct(self::CONFIG_STORAGE_NAMESPACE);
		$this->setDefaultConfiguration();
	}

	/**
	 * fills $default_configuration with initial values
	 */
	protected function setDefaultConfiguration()
	{
		$this->default_configuration = array(
				'mode' => 'live',
				'service_base_url/sandbox' => 'https://api.sandbox.paypal.com',
				'service_base_url/live' => 'https://api.paypal.com',
				'restapi-credentials/sandbox/client_id' => '',
				'restapi-credentials/sandbox/secret' => '',
				'restapi-credentials/live/client_id' => '',
				'restapi-credentials/live/secret' => '',
				'payment_experience_profile_id' => '',
				'use_paypal_plus' => '0',
				'use_ecs_cart' => '0',
				'use_ecs_products' => '0',
				'allow_ecs_login' => '0',
				'webhook_id' => '',
				'intent' => 'sale',
				'ecs_button_style' => 'Silver',  // Sunrise|Silver
				'thirdparty_payments/invoice/mode' => 'paywall', // paywall|external
				'thirdparty_payments/cod/mode' => 'paywall',
				'thirdparty_payments/moneyorder/mode' => 'paywall',
				'thirdparty_payments/eustandardtransfer/mode' => 'paywall',
				'thirdparty_payments/cash/mode' => 'paywall',
				'orderstatus/completed' => @constant('DEFAULT_ORDERS_STATUS_ID'),
				'orderstatus/pending' => @constant('DEFAULT_ORDERS_STATUS_ID'),
				'orderstatus/error' => @constant('DEFAULT_ORDERS_STATUS_ID'),
				'logo/image' => 'images/icons/paypal/de-pp-logo-100px.png',
				'logo/position' => 'right',
				'debug_logging' => '1',
				'allow_selfpickup' => '0',
			);

		$Language = new language();
		foreach($Language->catalog_languages as $iso2 => $langData)
		{
			$this->default_configuration['payment_experience_profile/'.$langData['code']] = '';
		}
	}

	/**
	 * returns a single configuration value by its key
	 * @param string $key a configuration key (relative to the namespace prefix)
	 * @return string configuration value
	 */
	public function get($key)
	{
		$value = parent::get($key);
		if($value === false && array_key_exists($key, $this->default_configuration))
		{
			$value = $this->default_configuration[$key];
		}
		return $value;
	}

	/**
	 * stores a configuration value by name/key
	 * @param string $name name/key of configuration entry
	 * @param string $value value to be stored
	 * @throws Exception if data validation fails
	 */
	public function set($name, $value)
	{
		if($value === null)
		{
			return;
		}

		switch($name)
		{
			case 'mode':
				if(!in_array($value, array('sandbox', 'live')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'intent':
				if(!in_array($value, array('sale', 'authorize', 'order')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'ecs_button_style':
				if(!in_array($value, array('Silver', 'Sunrise')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'use_paypal_plus':
			case 'use_ecs_cart':
			case 'use_ecs_products':
			case 'allow_ecs_login':
				$value = ($value == true ? '1' : '0');
				break;
			case 'thirdparty_payments/invoice/mode':
			case 'thirdparty_payments/cod/mode':
			case 'thirdparty_payments/moneyorder/mode':
			case 'thirdparty_payments/eustandardtransfer/mode':
			case 'thirdparty_payments/cash/mode':
				if(!in_array($value, array('paywall', 'external')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'orderstatus/completed':
			case 'orderstatus/pending':
			case 'orderstatus/error':
				$value = (string)(int)$value;
				break;
			case 'logo/position':
				if(!in_array($value, array('left', 'right')))
				{
					throw new Exception(__CLASS__.': invalid value '.$value.' for '.$name);
				}
				break;
			case 'debug_logging':
				$value = ($value == true ? '1' : '0');
				break;
			default:
		}
		parent::set($name, $value);
	}

}