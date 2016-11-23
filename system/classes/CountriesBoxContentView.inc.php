<?php
/* --------------------------------------------------------------
   CountriesBoxContentView.inc.php 2015-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CountriesBoxContentView
 */
class CountriesBoxContentView extends ContentView
{
	/**
	 * @var int $languageId
	 */
	protected $languageId;
	
	/**
	 * @var CustomerCountryIso2Interface
	 */
	protected $customerCountryIsoCode;
	
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_countries_dropdown.html');
	}


	public function prepare_data()
	{
		$this->set_content_data('country_data', $this->buildCountryArray());
		$this->set_content_data('URL', $this->buildUrl());
		
		if($this->customerCountryIsoCode !== null)
		{
			$this->set_content_data('SELECTED_COUNTRY', (string)$this->customerCountryIsoCode);
		}
		else
		{
			$this->set_content_data('SELECTED_COUNTRY', '');
		}
	}


	/**
	 * @param int $p_languageId
	 */
	public function setLanguageId($p_languageId)
	{
		$this->languageId = (int)$p_languageId;
	}
	
	
	/**
	 * @param CustomerCountryIso2Interface $isoCode
	 */
	public function setCustomerCountryIsoCode(CustomerCountryIso2Interface $isoCode)
	{
		$this->customerCountryIsoCode = $isoCode;
	}


	/**
	 * @return array
	 */
	protected function buildCountryArray()
	{
		/* @var Countries $countries */
		$countries = MainFactory::create_object('Countries', array($this->languageId, true, true));
		$countryArray = $countries->get_countries_array();
		
		return $countryArray;
	}


	/**
	 * @return string
	 */
	protected function buildUrl()
	{
		$url = htmlspecialchars_wrapper(gm_get_env_info('REQUEST_URI'));
		$url = preg_replace('/(\?|&amp;)switch_country=[A-Z]{2}/', '', $url);

		return $url;
	}
} 