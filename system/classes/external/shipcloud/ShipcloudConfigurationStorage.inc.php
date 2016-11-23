<?php
/* --------------------------------------------------------------
	ShipcloudConfigurationStorage.inc.php 2016-03-24
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Configuration storage for Shipcloud
 */
class ShipcloudConfigurationStorage extends ConfigurationStorage
{
	/**
	 * namespace inside the configuration storage
	 */
	const CONFIG_STORAGE_NAMESPACE = 'modules/shipping/shipcloud';

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
		$countryService   = StaticGXCoreLoader::getService('Country');
		$countryId        = MainFactory::create('Id', STORE_COUNTRY);
		$storeCountry     = $countryService->getCountryById($countryId);
		$storeCountryISO2 = $storeCountry->getIso2();

		$this->default_configuration = array(
			'mode'                            => 'live',
			'service_base_url'                => 'https://api.shipcloud.io',
			'boarding_url'                    => 'https://www.shipcloud.io/de/lp/gambio/?utm_source=gambio&utm_campaign=partnerseite',
			'api-key/sandbox'                 => '',
			'api-key/live'                    => '',
			'debug_logging'                   => '1',
			'cod-account/bank_account_holder' => '',
			'cod-account/bank_name'           => '',
			'cod-account/bank_account_number' => '',
			'cod-account/bank_code'           => '',
			'declared_value/minimum'          => '500',
			'from/company'                    => COMPANY_NAME,
			'from/first_name'                 => TRADER_FIRSTNAME,
			'from/last_name'                  => TRADER_NAME,
			'from/street'                     => TRADER_STREET,
			'from/street_no'                  => TRADER_STREET_NUMBER,
			'from/city'                       => TRADER_LOCATION,
			'from/zip_code'                   => TRADER_ZIPCODE,
			'from/country'                    => (string)$storeCountryISO2,
			'from/phone'                      => TRADER_TEL,
			'default_package'                 => '1',
			'parcel_service_id'               => '6',
			'order_status_after_label'        => '-1',
			'preselected_carriers/dhl'        => '1',
			'preselected_carriers/ups'        => '1',
			'preselected_carriers/dpd'        => '1',
			'preselected_carriers/hermes'     => '1',
			'preselected_carriers/gls'        => '1',
			'preselected_carriers/fedex'      => '1',
			'preselected_carriers/liefery'    => '0',
			'checked_carriers/dhl'            => '1',
			'checked_carriers/ups'            => '1',
			'checked_carriers/dpd'            => '1',
			'checked_carriers/hermes'         => '1',
			'checked_carriers/gls'            => '1',
			'checked_carriers/fedex'          => '1',
			'checked_carriers/liefery'        => '0',
			'additional_services/dpd-predict' => '1',
			'notify_customer'                 => '1',
		);
	}

	/**
	 * returns a single configuration value by its key
	 *
	 * @param string $key a configuration key (relative to the namespace prefix)
	 *
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
	 * Retrieves all keys/values from a given prefix namespace
	 *
	 * @param  string $p_prefix
	 * @return array
	 */
	public function get_all($p_prefix = '')
	{
		$values = parent::get_all($p_prefix);
		if(empty($values))
		{
			foreach($this->default_configuration as $key => $default_value)
			{
				$key_prefix = substr($key, 0, strlen($p_prefix));
				if($key_prefix == $p_prefix)
				{
					$values[$key] = $default_value;
				}
			}
		}
		return $values;
	}

	/**
	 * stores a configuration value by name/key
	 *
	 * @param string $name  name/key of configuration entry
	 * @param string $value value to be stored
	 *
	 * @throws Exception if data validation fails
	 */
	public function set($name, $value)
	{
		$nameParts = explode('/', $name);
		if($nameParts[0] === 'packages')
		{
			if(is_numeric($nameParts[1]) === false)
			{
				$value = null;
			}
			else
			{
				switch($nameParts[2])
				{
					case 'name':
						$value = strip_tags($value);
						break;
					case 'weight':
					case 'width':
					case 'height':
					case 'length':
						$value = (double)$value;
						break;
					default:
						$value = null;
				}
			}
		}
		else if($nameParts[0] === 'preselected_carriers' || $nameParts[0] === 'checked_carriers')
		{
			$value = $value == true ? '1' : '0';
		}
		else
		{
			switch($name)
			{
				case 'mode':
					$value = $value == 'sandbox' ? 'sandbox' : 'live';
					break;
				case 'debug_logging':
				case 'additional_services/dpd-predict':
				case 'notify_customer':
					$value = $value == true ? '1' : '0';
					break;
				case 'service_base_url':
				case 'api-key/sandbox':
				case 'api-key/live':
				case 'cod-account/bank_account_holder':
				case 'cod-account/bank_name':
				case 'cod-account/bank_account_number':
				case 'cod-account/bank_code':
				case 'from/company':
				case 'from/first_name':
				case 'from/last_name':
				case 'from/street':
				case 'from/street_no':
				case 'from/city':
				case 'from/zip_code':
				case 'from/country':
				case 'from/phone':
					break;
				case 'default_package':
				case 'parcel_service_id':
				case 'order_status_after_label':
					$value = (int)$value;
					break;
				default:
					//throw new Exception(sprintf('tried to set invalid key %s in %s', $key, __CLASS__));
					$value = null;
			}
		}

		if($value === null)
		{
			return;
		}
		parent::set($name, $value);
	}

	/**
	 * @return int maximum id value for package templates
	 */
	public function getMaximumPackageTemplateId()
	{
		$packages = $this->get_all_tree('packages');
		$max_id   = 0;
		if(!empty($packages))
		{
			foreach($packages['packages'] as $id => $package)
			{
				$max_id = max($max_id, (int)$id);
			}
		}
		return $max_id;
	}

	/**
	 * Returns a list of supported carriers
	 *
	 * @return array
	 */
	public function getCarriers()
	{
		//$carriers = array('dhl', 'ups', 'dpd', 'hermes', 'gls', 'fedex', 'liefery');
		$carriersCache = MainFactory::create('ShipcloudCarriersCache');
		$carrierList = $carriersCache->getCarriers();
		$carriers = array();
		foreach($carrierList as $shipcloudCarrier)
		{
			$carriers[] = $shipcloudCarrier->name;
		}

		return $carriers;
	}
}

