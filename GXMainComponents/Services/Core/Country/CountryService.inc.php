<?php
/* --------------------------------------------------------------
   CountryService.inc.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CountryServiceInterface');

/**
 * Class CountryService
 *
 * This class is used for finding country data
 *
 * @category   System
 * @package    Customer
 * @subpackage Country
 * @implements CountryServiceInterface
 */
class CountryService implements CountryServiceInterface
{
	/**
	 * @var CustomerCountryRepositoryInterface
	 */
	protected $customerCountryRepo;
	/**
	 * @var CustomerCountryZoneRepositoryInterface
	 */
	protected $customerCountryZoneRepo;
	
	
	/**
	 * Constructor of the class CountryService
	 *
	 * @param CustomerCountryRepositoryInterface     $customerCountryRepo
	 * @param CustomerCountryZoneRepositoryInterface $customerCountryZoneRepo
	 */
	public function __construct(CustomerCountryRepositoryInterface $customerCountryRepo,
	                            CustomerCountryZoneRepositoryInterface $customerCountryZoneRepo)
	{
		$this->customerCountryRepo     = $customerCountryRepo;
		$this->customerCountryZoneRepo = $customerCountryZoneRepo;
	}
	
	
	/**
	 * Getter method for the country ID
	 *
	 * @param IdType $id
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountryById(IdType $id)
	{
		return $this->customerCountryRepo->getById($id);
	}
	
	
	/**
	 * Getter method for the country zone
	 *
	 * @param CustomerCountryZoneNameInterface|string $p_zoneName
	 * @param CustomerCountryInterface                $customerCountry
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZoneByNameAndCountry($p_zoneName, CustomerCountryInterface $customerCountry)
	{
		if(is_a($p_zoneName, 'CustomerCountryZoneNameInterface'))
		{
			$zoneName = $p_zoneName;
		}
		else
		{
			$zoneName = MainFactory::create('CustomerCountryZoneName', $p_zoneName);
		}
		
		return $this->customerCountryZoneRepo->getByNameAndCountry($zoneName, $customerCountry);
	}
	
	
	/**
	 * This method returns a CustomerCountryZone object if found. Otherwise null will be returned.
	 *
	 * @param CustomerCountryZoneNameInterface|string $p_zoneName
	 * @param CustomerCountryInterface                $customerCountry
	 *
	 * @return CustomerCountryZoneInterface|null
	 */
	public function findCountryZoneByNameAndCountry($p_zoneName, CustomerCountryInterface $customerCountry)
	{
		if(is_a($p_zoneName, 'CustomerCountryZoneNameInterface'))
		{
			$zoneName = $p_zoneName;
		}
		else
		{
			$zoneName = MainFactory::create('CustomerCountryZoneName', $p_zoneName);
		}
		
		return $this->customerCountryZoneRepo->findByNameAndCountry($zoneName, $customerCountry);
	}
	
	
	/**
	 * @param IdType $id
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZoneById(IdType $id)
	{
		return $this->customerCountryZoneRepo->getById($id);
	}
	
	
	/**
	 * This method will return a new CustomerCountryZone object representing an unknown country zone.
	 *
	 * @param string $p_zoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function getUnknownCountryZoneByName($p_zoneName)
	{
		$countryZoneName = MainFactory::create('CustomerCountryZoneName', $p_zoneName);
		
		return $this->customerCountryZoneRepo->getUnknownCountryZoneByName($countryZoneName);
	}
	
	
	/**
	 * This method will return an array of CustomerCountryZone objects found by the country ID. If the country has
	 * no zones, an empty array will be returned
	 *
	 * @param IdType $countryId
	 *
	 * @return array of CustomerCountryZone objects
	 */
	public function findCountryZonesByCountryId(IdType $countryId)
	{
		$countryZones = $this->customerCountryZoneRepo->findCountryZonesByCountryId($countryId);
		
		return $countryZones;
	}
	
	
	/**
	 * Checks if there is a country zone in a country
	 *
	 * @param CustomerCountryZoneInterface $customerCountryZone
	 * @param CustomerCountryInterface     $customerCountry
	 *
	 * @return bool true|false if country zone exists|not exists
	 */
	public function countryZoneExistsInCountry(CustomerCountryZoneInterface $customerCountryZone,
	                                           CustomerCountryInterface $customerCountry)
	{
		$countryZones               = $this->customerCountryZoneRepo->findCountryZonesByCountryId(new IdType((int)$customerCountry->getId()));
		$countryZoneExistsInCountry = in_array($customerCountryZone, $countryZones);
		
		return $countryZoneExistsInCountry;
	}
	
	
	/**
	 * Checks if country has country zones
	 *
	 * @param CustomerCountryInterface $customerCountry
	 *
	 * @return bool true|false if there are|are not country zones
	 */
	public function countryHasCountryZones(CustomerCountryInterface $customerCountry)
	{
		$countryZones           = $this->customerCountryZoneRepo->findCountryZonesByCountryId(new IdType($customerCountry->getId()));
		$countryHasCountryZones = !empty($countryZones);
		
		return $countryHasCountryZones;
	}
	
	
	/**
	 * Get country by name.
	 *
	 * @param string $p_countryName
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountryByName($p_countryName)
	{
		$countryName = MainFactory::create('CustomerCountryName', $p_countryName);
		
		return $this->customerCountryRepo->getByName($countryName);
	}
	
	
	/**
	 * Find country by name. 
	 * 
	 * @param string $p_countryName
	 *
	 * @return CustomerCountryInterface
	 */
	public function findCountryByName($p_countryName)
	{
		$countryName = MainFactory::create('CustomerCountryName', $p_countryName);
		
		return $this->customerCountryRepo->findByName($countryName); 
	}
}
 