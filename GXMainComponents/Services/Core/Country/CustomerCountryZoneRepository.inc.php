<?php
/* --------------------------------------------------------------
   CustomerCountryZoneRepository.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCountryZoneRepositoryInterface');

/**
 * Class CustomerCountryZoneRepository
 * 
 * This class provides basic methods for finding customer country zone data
 *
 * @category System
 * @package Customer
 * @subpackage CountryZone
 * @implements CustomerCountryZoneRepositoryInterface
 */
class CustomerCountryZoneRepository implements CustomerCountryZoneRepositoryInterface
{
	/**
	 * @var CustomerCountryZoneReaderInterface
	 */
	protected $customerCountryZoneReader;

	/**
	 * @var AbstractCustomerFactory
	 */
	protected $customerFactory;


	/**
	 * Constructor of the class CustomerCountryZoneRepository
	 * 
	 * @param CustomerCountryZoneReaderInterface $customerCountryZoneReader
	 * @param AbstractCustomerFactory $customerFactory
	 */
	public function __construct(CustomerCountryZoneReaderInterface $customerCountryZoneReader,
	                            AbstractCustomerFactory $customerFactory)
	{
		$this->customerCountryZoneReader = $customerCountryZoneReader;
		$this->customerFactory = $customerFactory;
	}
	

	/**
	 * @param IdType $countryZoneId
	 * 
	 * @throws Exception if country zone not found
	 * 
	 * @return CustomerCountryZoneInterface
	 */
	public function getById(IdType $countryZoneId)
	{
		$countryZone = $this->customerCountryZoneReader->findById($countryZoneId);

		if($countryZone === null)
		{
			throw new Exception('country zone not found');
		}
		
		return $countryZone;
	}

	
	/**
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 * @param CustomerCountryInterface         $country
	 *
	 * @throws Exception if country zone not found
	 *                   
	 * @return CustomerCountryZoneInterface
	 */
	public function getByNameAndCountry(CustomerCountryZoneNameInterface $countryZoneName,
										CustomerCountryInterface $country)
	{
		$countryZone = $this->customerCountryZoneReader->findByNameAndCountry($countryZoneName, $country);
		
		if($countryZone === null)
		{
			throw new Exception('country zone not found');
		}
		
		return $countryZone;
	}


	/**
	 * This method will return a new CustomerCountryZone object representing an unknown country zone.
	 * ID is 0 and ISO code is empty.
	 * 
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 *
	 * @return CustomerCountryZone
	 */
	public function getUnknownCountryZoneByName(CustomerCountryZoneNameInterface $countryZoneName)
	{
		return $this->customerFactory->createCustomerCountryZone(new IdType(0), 
																 $countryZoneName, 
																 MainFactory::create('CustomerCountryZoneIsoCode', ''));
	}


	/**
	 * This method will get the country zone by its name and country if it exists, if not it will return null.
	 *
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 * @param CustomerCountryInterface         $country
	 *
	 * @return CustomerCountryZone|null
	 */
	public function findByNameAndCountry(CustomerCountryZoneNameInterface $countryZoneName, 
										 CustomerCountryInterface $country)
	{
		$countryZone = $this->customerCountryZoneReader->findByNameAndCountry($countryZoneName, $country);

		return $countryZone;
	}


	/**
	 * This method will get the country zone by its ID if it exists, if not it will return null.
	 * 
	 * @param IdType $countryZoneId
	 *
	 * @return CustomerCountryZone|null
	 */
	public function findById(IdType $countryZoneId)
	{
		$countryZone = $this->customerCountryZoneReader->findById($countryZoneId);

		return $countryZone;
	}
	
	
	/**
	 * This method will return an array of country zones found by the country ID.
	 * 
	 * @param IdType $countryId
	 *
	 * @return array
	 */
	public function findCountryZonesByCountryId(IdType $countryId)
	{
		$countryZones = $this->customerCountryZoneReader->findCountryZonesByCountryId($countryId);
		return $countryZones;
	}
}