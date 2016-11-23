<?php
/* --------------------------------------------------------------
   CountrySessionWriter.inc.php 2014-12-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CountrySessionWriter
 */
class CountrySessionWriter
{
	protected $countries;


	/**
	 * @param Countries $countries
	 */
	public function __construct(Countries $countries)
	{
		$this->countries = $countries;
	}
	
	/**
	 * @param string $p_countryIsoCode
	 */
	public function setSessionCountryIdByIsoCode($p_countryIsoCode)
	{
		if(is_string($p_countryIsoCode) && strlen($p_countryIsoCode) > 0)
		{
			$countriesArray = $this->countries->get_countries_array();
			if(isset($countriesArray[$p_countryIsoCode]))
			{
				$countryId = $this->countries->get_country_id_by_iso_code($p_countryIsoCode);
				$_SESSION['customer_country_id'] = $countryId;
			}
		}
	}

	/**
	 * @param string $p_countryIsoCode
	 */
	public function setSessionIsoCode($p_countryIsoCode)
	{
		if(is_string($p_countryIsoCode) && strlen($p_countryIsoCode) > 0)
		{
			$_SESSION['customer_country_iso'] = strtoupper(trim($p_countryIsoCode));
		}
	}
} 