<?php
/* --------------------------------------------------------------
   CustomerAddressReader.inc.php 2016-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAddressReaderInterface');

/**
 * Class CustomerAddressReader
 *
 * This class is used for reading customer address data from the database
 * 
 * @category   System
 * @package    Customer
 * @subpackage Address
 * @implements CustomerAddressReaderInterface
 */
class CustomerAddressReader implements CustomerAddressReaderInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	/**
	 * @var AbstractCustomerFactory
	 */
	protected $customerFactory;
	/**
	 * @var CountryServiceInterface
	 */
	protected $countryService;
	/**
	 * @var StringHelperInterface
	 */
	protected $stringHelper;


	/**
	 * Constructor for the class CustomerAddressReader
	 *
	 * CrossCuttingLoader dependencies:
	 * - StringHelper
	 *
	 * @param AbstractCustomerFactory $customerFactory
	 * @param CountryServiceInterface  $countryService
	 * @param CI_DB_query_builder      $dbQueryBuilder
	 */
	public function __construct(AbstractCustomerFactory $customerFactory, CountryServiceInterface $countryService,
								CI_DB_query_builder $dbQueryBuilder)
	{
		$this->customerFactory = $customerFactory;
		$this->countryService  = $countryService;
		$this->db              = $dbQueryBuilder;

		$this->stringHelper = StaticCrossCuttingLoader::getObject('StringHelper');
	}


	/**
	 * @param IdType $id
	 *
	 * @return CustomerAddress
	 * @throws InvalidArgumentException
	 */
	public function getById(IdType $id)
	{
		$address = $this->findById($id);
		if($address === null)
		{
			throw new InvalidArgumentException('No Address found for the given ID.');
		}
		
		return $address;
	}


	/**
	 * @param IdType $id
	 * @return CustomerAddress
	 */
	public function findById(IdType $id)
	{
		$addressDataResult = $this->db->get_where('address_book', array('address_book_id' => (int)(string)$id));
		$addressDataArray = $addressDataResult->row_array();
		if($addressDataResult->num_rows() == 0)
		{
			return null;
		}
		
		return $this->_createCustomerAddressByArray($addressDataArray);
	}


	/**
	 * This method will return an array of all customer's addresses
	 *
	 * @param CustomerInterface $customer
	 *
	 * @return array containing CustomerAddress objects
	 */
	public function findAddressesByCustomer(CustomerInterface $customer)
	{
		$addressesArray = $this->db->get_where('address_book', array('customers_id' => $customer->getId()))
								   ->result_array();

		foreach($addressesArray as &$address)
		{
			$address = $this->_createCustomerAddressByArray($address);
		}

		return $addressesArray;
	}


	/**
	 * Get all system addresses. 
	 */
	public function getAllAddresses()
	{
		$addressesArray = $this->db->get('address_book')->result_array();
		
		foreach($addressesArray as &$address)
		{
			$address = $this->_createCustomerAddressByArray($address); 
		}
		
		return $addressesArray; 
	}

	
	/**
	 * Filter existing addresses by keyword. 
	 * 
	 * This method is useful when creating a search mechanism for the registered addresses.
	 * 
	 * @param string $p_keyword The keyword to be used for filtering the records.
	 *                          
	 * @return array Returns an array of CustomerAddress objects.
	 */
	public function filterAddresses($p_keyword)
	{		
		// CodeIgniter DB library will automatically escape the keyword. 
		
		$this->db->like('entry_gender', $p_keyword);
		$this->db->or_like('entry_company', $p_keyword);
		$this->db->or_like('entry_firstname', $p_keyword);
		$this->db->or_like('entry_lastname', $p_keyword);
		$this->db->or_like('entry_street_address', $p_keyword);
		$this->db->or_like('entry_house_number', $p_keyword);
		$this->db->or_like('entry_additional_info', $p_keyword);
		$this->db->or_like('entry_suburb', $p_keyword);
		$this->db->or_like('entry_postcode', $p_keyword);
		$this->db->or_like('entry_city', $p_keyword);
		$this->db->or_like('entry_state', $p_keyword);
		
		$addressesArray = $this->db->get('address_book')->result_array(); 
		
		foreach($addressesArray as &$address)
		{
			$address = $this->_createCustomerAddressByArray($address); 
		}
		
		return $addressesArray;
	}

	/**
	 * @param array $addressDataArray
	 *
	 * @return CustomerAddress
	 */
	protected function _createCustomerAddressByArray(array $addressDataArray)
	{
		$addressDataArray = $this->stringHelper->convertNullValuesToStringInArray($addressDataArray);

		$customerAddress = $this->customerFactory->createCustomerAddress();
		$customerAddress->setId(new IdType((int)$addressDataArray['address_book_id']));
		$customerAddress->setCustomerId(new IdType($addressDataArray['customers_id']));
		$customerAddress->setGender(MainFactory::create('CustomerGender', $addressDataArray['entry_gender']));
		$customerAddress->setCompany(MainFactory::create('CustomerCompany', $addressDataArray['entry_company']));
		$customerAddress->setB2BStatus(MainFactory::create('CustomerB2BStatus', (bool)(int)$addressDataArray['customer_b2b_status']));
		$customerAddress->setFirstname(MainFactory::create('CustomerFirstname', $addressDataArray['entry_firstname']));
		$customerAddress->setLastname(MainFactory::create('CustomerLastname', $addressDataArray['entry_lastname']));
		$customerAddress->setStreet(MainFactory::create('CustomerStreet', $addressDataArray['entry_street_address']));
		$customerAddress->setHouseNumber(MainFactory::create('CustomerHouseNumber', $addressDataArray['entry_house_number']));
		$customerAddress->setAdditionalAddressInfo(MainFactory::create('CustomerAdditionalAddressInfo', $addressDataArray['entry_additional_info']));
		$customerAddress->setSuburb(MainFactory::create('CustomerSuburb', $addressDataArray['entry_suburb']));
		$customerAddress->setPostcode(MainFactory::create('CustomerPostcode', $addressDataArray['entry_postcode']));
		$customerAddress->setCity(MainFactory::create('CustomerCity', $addressDataArray['entry_city']));

		$country = $this->countryService->getCountryById(new IdType(
																			 $addressDataArray['entry_country_id']));
		$customerAddress->setCountry($country);

		$state = MainFactory::create('CustomerCountryZoneName', $addressDataArray['entry_state']);

		if($this->countryService->countryHasCountryZones($country) && (string)$state !== '')
		{
			$countryZone = $this->countryService->findCountryZoneByNameAndCountry($state, $country);
		}
		
		if(!isset($countryZone))
		{
			$countryZone = $this->customerFactory->createCustomerCountryZone(new IdType(0), 
																			 $state,
																			 MainFactory::create('CustomerCountryZoneIsoCode', ''));
		}

		$customerAddress->setCountryZone($countryZone);

		return $customerAddress;
	}
}