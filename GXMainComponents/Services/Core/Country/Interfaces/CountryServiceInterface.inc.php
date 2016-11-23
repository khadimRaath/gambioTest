<?php
/* --------------------------------------------------------------
   CountryServiceInterface.inc.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CountryServiceInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CountryServiceInterface
{
	
	/**
	 * Method to get a country with a given id
	 *
	 * @param IdType $id
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountryById(IdType $id);
	
	
	/**
	 * Method to get a country with a given name and country
	 *
	 * @param CustomerCountryZoneNameInterface|string $p_zoneName
	 * @param CustomerCountryInterface                $customerCountry
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZoneByNameAndCountry($p_zoneName, CustomerCountryInterface $customerCountry);
	
	
	/**
	 * This method returns a CustomerCountryZone object if found. Otherwise null will be returned.
	 *
	 * @param CustomerCountryZoneNameInterface|string $p_zoneName
	 * @param CustomerCountryInterface                $customerCountry
	 *
	 * @return CustomerCountryZoneInterface|null
	 */
	public function findCountryZoneByNameAndCountry($p_zoneName, CustomerCountryInterface $customerCountry);
	
	
	/**
	 * Method to check if a country zone exists in a country
	 *
	 * @param CustomerCountryZoneInterface $customerCountryZone
	 * @param CustomerCountryInterface     $customerCountry
	 *
	 * @return bool
	 */
	public function countryZoneExistsInCountry(CustomerCountryZoneInterface $customerCountryZone,
	                                           CustomerCountryInterface $customerCountry);
	
	
	/**
	 * Method to check if a country has country zones
	 *
	 * @param CustomerCountryInterface $customerCountry
	 *
	 * @return bool
	 */
	public function countryHasCountryZones(CustomerCountryInterface $customerCountry);
	
	
	/**
	 * This method will return a new CustomerCountryZone object representing an unknown country zone.
	 *
	 * @param string $p_zoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function getUnknownCountryZoneByName($p_zoneName);
	
	
	/**
	 * This method will return an array of CustomerCountryZone objects found by the country ID. If the country has
	 * no zones, an empty array will be returned
	 *
	 * @param IdType $countryId
	 *
	 * @return array of CustomerCountryZone objects
	 */
	public function findCountryZonesByCountryId(IdType $countryId);
	
	
	/**
	 * Get country by name.
	 *
	 * @param string $p_countryName
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountryByName($p_countryName);
	
	
	/**
	 * Find country by name.
	 *
	 * @param string $p_countryName
	 *
	 * @return CustomerCountryInterface
	 */
	public function findCountryByName($p_countryName); 
	
}