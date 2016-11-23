<?php
/* --------------------------------------------------------------
   CustomerAddress.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAddressInterface');

/**
 * Class CustomerAddress
 *
 * This class is used for managing customer address data
 *
 * @category   System
 * @package    Customer
 * @subpackage Address
 * @implements CustomerAddressInterface
 */
class CustomerAddress implements CustomerAddressInterface
{
	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $customerId;

	/**
	 * @var CustomerGenderInterface
	 */
	protected $gender;

	/**
	 * @var CustomerFirstnameInterface
	 */
	protected $firstname;

	/**
	 * @var CustomerLastnameInterface
	 */
	protected $lastname;

	/**
	 * @var CustomerCompanyInterface
	 */
	protected $company;

	/**
	 * @var CustomerStreetInterface
	 */
	protected $street;
	
	/**
	 * @var CustomerHouseNumberInterface
	 */
	protected $houseNumber;
	
	/**
	 * @var CustomerAdditionalAddressInfoInterface
	 */
	protected $additionalAddressInfo;

	/**
	 * @var CustomerSuburbInterface
	 */
	protected $suburb;

	/**
	 * @var CustomerPostcodeInterface
	 */
	protected $postcode;

	/**
	 * @var CustomerCityInterface
	 */
	protected $city;

	/**
	 * @var CustomerCountryInterface
	 */
	protected $country;

	/**
	 * @var CustomerCountryZoneInterface
	 */
	protected $countryZone;

	/**
	 * @var AddressClassInterface
	 */
	protected $addressClass;

	/**
	 * @var CustomerB2BStatusInterface
	 */
	protected $b2bStatus;


	/**
	 * Import Address Block
	 *
	 * This method will import all address parts from the address block
	 *
	 * @param AddressBlockInterface $addressBlock
	 */
	public function importAddressBlock(AddressBlockInterface $addressBlock)
	{
		$this->setGender($addressBlock->getGender());
		$this->setFirstname($addressBlock->getFirstname());
		$this->setLastname($addressBlock->getLastname());
		$this->setCompany($addressBlock->getCompany());
		$this->setB2BStatus($addressBlock->getB2BStatus());
		$this->setStreet($addressBlock->getStreet());
		$this->setHouseNumber($addressBlock->getHouseNumber());
		$this->setAdditionalAddressInfo($addressBlock->getAdditionalAddressInfo());
		$this->setSuburb($addressBlock->getSuburb());
		$this->setPostcode($addressBlock->getPostcode());
		$this->setCity($addressBlock->getCity());
		$this->setCountry($addressBlock->getCountry());
		$this->setCountryZone($addressBlock->getCountryZone());
	}


	/**
	 * Setter method for the ID
	 *
	 * @param IdType $id addressBookId
	 */
	public function setId(IdType $id)
	{
		$this->id = (int)(string)$id;
	}


	/**
	 * Setter method for the customer ID
	 *
	 * @param IdType $customerId
	 */
	public function setCustomerId(IdType $customerId)
	{
		$this->customerId = (int)(string)$customerId;
	}


	/**
	 * Getter method for the customer ID
	 *
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}


	/**
	 * Getter method for the ID
	 *
	 * @return int | null
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Setter method for the city
	 *
	 * @param CustomerCityInterface $city
	 */
	public function setCity(CustomerCityInterface $city)
	{
		$this->city = $city;
	}


	/**
	 * Getter method for the city
	 *
	 * @return CustomerCityInterface
	 */
	public function getCity()
	{
		return $this->city;
	}


	/**
	 * Setter method for the country
	 *
	 * @param CustomerCountryInterface $country
	 */
	public function setCountry(CustomerCountryInterface $country)
	{
		$this->country = $country;
	}


	/**
	 * Getter method for the country
	 *
	 * @return CustomerCountryInterface
	 */
	public function getCountry()
	{
		return $this->country;
	}


	/**
	 * Setter method for the country zone
	 *
	 * @param CustomerCountryZoneInterface $countryZone
	 */
	public function setCountryZone(CustomerCountryZoneInterface $countryZone)
	{
		$this->countryZone = $countryZone;
	}


	/**
	 * Getter method for the country zone
	 *
	 * @return CustomerCountryZoneInterface
	 */
	public function getCountryZone()
	{
		return $this->countryZone;
	}


	/**
	 * Setter method for the first name of the customer
	 *
	 * @param CustomerFirstnameInterface $firstname
	 */
	public function setFirstname(CustomerFirstnameInterface $firstname)
	{
		$this->firstname = $firstname;
	}


	/**
	 * Getter method for the first name of the customer
	 *
	 * @return CustomerFirstnameInterface
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}


	/**
	 * Setter method for the gender of the customer
	 *
	 * @param CustomerGenderInterface
	 */
	public function setGender(CustomerGenderInterface $gender)
	{
		$this->gender = $gender;
	}


	/**
	 * Getter method for the gender of the customer
	 *
	 * @return CustomerGenderInterface
	 */
	public function getGender()
	{
		return $this->gender;
	}


	/**
	 * Setter method for the last name of the customer
	 *
	 * @param CustomerLastnameInterface $lastname
	 */
	public function setLastname(CustomerLastnameInterface $lastname)
	{
		$this->lastname = $lastname;
	}


	/**
	 * Getter method for the last name of the customer
	 *
	 * @return CustomerLastnameInterface
	 */
	public function getLastname()
	{
		return $this->lastname;
	}


	/**
	 * Setter method for the postcode
	 *
	 * @param CustomerPostcodeInterface $postcode
	 */
	public function setPostcode(CustomerPostcodeInterface $postcode)
	{
		$this->postcode = $postcode;
	}


	/**
	 * Getter method for the postcode
	 *
	 * @return CustomerPostcodeInterface
	 */
	public function getPostcode()
	{
		return $this->postcode;
	}


	/**
	 * Setter method for the street name
	 *
	 * @param CustomerStreetInterface $street
	 */
	public function setStreet(CustomerStreetInterface $street)
	{
		$this->street = $street;
	}


	/**
	 * Getter method for the street name
	 *
	 * @return CustomerStreetInterface
	 */
	public function getStreet()
	{
		return $this->street;
	}
	
	
	/**
	 * Setter method for the house number
	 *
	 * @param CustomerHouseNumberInterface $houseNumber
	 */
	public function setHouseNumber(CustomerHouseNumberInterface $houseNumber)
	{
		$this->houseNumber = $houseNumber;
	}
	
	
	/**
	 * Getter method for the house number
	 *
	 * @return CustomerHouseNumberInterface
	 */
	public function getHouseNumber()
	{
		return $this->houseNumber;
	}
	
	
	/**
	 * Setter method for the additional address information
	 *
	 * @param CustomerAdditionalAddressInfoInterface $additionalAddressInfo
	 */
	public function setAdditionalAddressInfo(CustomerAdditionalAddressInfoInterface $additionalAddressInfo)
	{
		$this->additionalAddressInfo = $additionalAddressInfo;
	}
	
	
	/**
	 * Getter method for the additional address information
	 *
	 * @return CustomerAdditionalAddressInfoInterface
	 */
	public function getAdditionalAddressInfo()
	{
		return $this->additionalAddressInfo;
	}


	/**
	 * Setter method for the company name
	 *
	 * @param CustomerCompanyInterface $company
	 */
	public function setCompany(CustomerCompanyInterface $company)
	{
		$this->company = $company;
	}


	/**
	 * Getter method for the company name
	 *
	 * @return CustomerCompanyInterface
	 */
	public function getCompany()
	{
		return $this->company;
	}


	/**
	 * Setter method for the suburb
	 *
	 * @param CustomerSuburbInterface $suburb
	 */
	public function setSuburb(CustomerSuburbInterface $suburb)
	{
		$this->suburb = $suburb;
	}


	/**
	 * Getter method of the suburb
	 *
	 * @return CustomerSuburbInterface
	 */
	public function getSuburb()
	{
		return $this->suburb;
	}


	/**
	 * Setter method for the address class
	 *
	 * @param AddressClassInterface $addressClass
	 */
	public function setAddressClass(AddressClassInterface $addressClass)
	{
		$this->addressClass = $addressClass;
	}


	/**
	 * Getter method of the address class
	 *
	 * @return AddressClassInterface
	 */
	public function getAddressClass()
	{
		return $this->addressClass;
	}


	/**
	 * Setter method for the address class
	 *
	 * @param CustomerB2BStatusInterface $b2bStatus
	 */
	public function setB2BStatus(CustomerB2BStatusInterface $b2bStatus)
	{
		$this->b2bStatus = $b2bStatus;
	}


	/**
	 * Getter method of the address class
	 *
	 * @return CustomerB2BStatusInterface
	 */
	public function getB2BStatus()
	{
		return $this->b2bStatus;
	}
} 