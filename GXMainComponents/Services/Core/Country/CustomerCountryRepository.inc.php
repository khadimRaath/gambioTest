<?php
/* --------------------------------------------------------------
   CustomerCountryRepository.inc.php 2016-07-04 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryRepositoryInterface');

/**
 * Class CustomerCountryRepository
 *
 * This class provides basic methods for finding customer country data
 *
 * @category   System
 * @package    Customer
 * @subpackage Country
 * @implements CustomerCountryRepositoryInterface
 */
class CustomerCountryRepository implements CustomerCountryRepositoryInterface
{
	/**
	 * @var CustomerCountryReaderInterface
	 */
	protected $customerCountryReader;
	
	
	/**
	 * Constructor of the class CustomerCountryRepository
	 *
	 * @param CustomerCountryReader $customerCountryReader
	 */
	public function __construct(CustomerCountryReader $customerCountryReader)
	{
		$this->customerCountryReader = $customerCountryReader;
	}
	
	
	/**
	 * @param IdType $countryId
	 *
	 * @return CustomerCountry
	 * @throws Exception if country not found
	 */
	public function getById(IdType $countryId)
	{
		$country = $this->customerCountryReader->findById($countryId);
		
		if($country === null)
		{
			throw new Exception('Country with the following ID could not be found: ' . (string)$countryId);
		}
		
		return $country;
	}
	
	
	/**
	 * This method will get a country if it exists else it will return null.
	 *
	 * @param IdType $countryId
	 *
	 * @return CustomerCountry|null
	 */
	public function findById(IdType $countryId)
	{
		$country = $this->customerCountryReader->findById($countryId);
		
		return $country;
	}
	
	
	/**
	 * Get country by name.
	 *
	 * @param \CustomerCountryNameInterface $countryName
	 *
	 * @return CustomerCountry
	 *
	 * @throws Exception If the country could not be found.
	 */
	public function getByName(CustomerCountryNameInterface $countryName)
	{
		$country = $this->customerCountryReader->findByName($countryName);
		
		if(empty($country))
		{
			throw new Exception('Country with the following name could not be found: ' . (string)$countryName);
		}
		
		return $country;
	}
	
	
	/**
	 * Find country by name. 
	 * 
	 * @param CustomerCountryNameInterface $countryName
	 *
	 * @return CustomerCountryInterface
	 */
	public function findByName(CustomerCountryNameInterface $countryName)
	{
		$country = $this->customerCountryReader->findByName($countryName);
		
		if($country === null)
		{
			$country = MainFactory::create('UnknownCustomerCountry', $countryName); 
		}
		
		return $country; 
	}
}