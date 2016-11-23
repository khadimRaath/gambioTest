<?php
/* --------------------------------------------------------------
   AddressBlock.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AddressBlockInterface');

/**
 * Value Object
 *
 * Class AddressBlock
 *
 * Stores all customer address data
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 *
 * @implements AddressBlockInterface
 */
class AddressBlock implements AddressBlockInterface
{
	/**
	 * Customer's gender.
	 * 
	 * @var CustomerGenderInterface
	 */
	protected $gender;

	/**
	 * Customer first name.
	 * 
	 * @var CustomerFirstnameInterface
	 */
	protected $firstname;

	/**
	 * Customer last name.
	 * 
	 * @var CustomerLastnameInterface
	 */
	protected $lastname;

	/**
	 * Customer's company.
	 * 
	 * @var CustomerCompanyInterface
	 */
	protected $company;

	/**
	 * Customer's street.
	 * 
	 * @var CustomerStreetInterface
	 */
	protected $street;
	
	/**
	 * Customer's house number.
	 * 
	 * @var CustomerHouseNumberInterface
	 */
	protected $houseNumber;
	
	/**
	 * Customer's additional address information.
	 * 
	 * @var CustomerAdditionalAddressInfoInterface
	 */
	protected $additionalAddressInfo;

	/**
	 * Customer's suburb.
	 * 
	 * @var CustomerSuburbInterface
	 */
	protected $suburb;

	/**
	 * Customer's post code.
	 * 
	 * @var CustomerPostcodeInterface
	 */
	protected $postcode;

	/**
	 * Customer's city.
	 * 
	 * @var CustomerCityInterface
	 */
	protected $city;

	/**
	 * Customer's country.
	 * 
	 * @var CustomerCountryInterface
	 */
	protected $country;

	/**
	 * Customer's country zone.
	 * 
	 * @var CustomerCountryZoneInterface
	 */
	protected $countryZone;

	/**
	 * Customer's B2B status.
	 * 
	 * @var CustomerB2BStatusInterface
	 */
	protected $b2bStatus;


	/**
	 * Constructor of the class AddressBlock.
	 *
	 * @param CustomerGenderInterface                $gender                Customer's gender.
	 * @param CustomerFirstnameInterface             $firstname             Customer's first name.
	 * @param CustomerLastnameInterface              $lastname              Customer's last name.
	 * @param CustomerCompanyInterface               $company               Customer's company.
	 * @param CustomerB2BStatusInterface             $b2bStatus             Customer's B2B status.
	 * @param CustomerStreetInterface                $street                Customer's street.
	 * @param CustomerHouseNumberInterface           $houseNumber           Customer's house number.
	 * @param CustomerAdditionalAddressInfoInterface $additionalAddressInfo Customer's additional address information.
	 * @param CustomerSuburbInterface                $suburb                Customer's suburb.
	 * @param CustomerPostcodeInterface              $postcode              Customer's post code.
	 * @param CustomerCityInterface                  $city                  Customer's city.
	 * @param CustomerCountryInterface               $country               Customer's country.
	 * @param CustomerCountryZoneInterface           $countryZone           Customer's country zone.
	 * @param CustomerB2BStatusInterface             $b2bStatus             Customer's B2B status.
	 */
	public function __construct(CustomerGenderInterface $gender,
	                            CustomerFirstnameInterface $firstname,
	                            CustomerLastnameInterface $lastname,
	                            CustomerCompanyInterface $company,
	                            CustomerB2BStatusInterface $b2bStatus,
	                            CustomerStreetInterface $street,
	                            CustomerHouseNumberInterface $houseNumber,
	                            CustomerAdditionalAddressInfoInterface $additionalAddressInfo,
	                            CustomerSuburbInterface $suburb,
	                            CustomerPostcodeInterface $postcode,
	                            CustomerCityInterface $city,
	                            CustomerCountryInterface $country,
	                            CustomerCountryZoneInterface $countryZone = null)
	{
		$this->gender                = $gender;
		$this->firstname             = $firstname;
		$this->lastname              = $lastname;
		$this->company               = $company;
		$this->b2bStatus             = $b2bStatus;
		$this->street                = $street;
		$this->houseNumber           = $houseNumber;
		$this->additionalAddressInfo = $additionalAddressInfo;
		$this->suburb                = $suburb;
		$this->postcode              = $postcode;
		$this->city                  = $city;
		$this->country               = $country;
		$this->countryZone           = $countryZone;
	}


	/**
	 * Returns the customer's gender.
	 *
	 * @return CustomerGender Customer's gender.
	 */
	public function getGender()
	{
		return $this->gender;
	}


	/**
	 * Returns the customer's first name.
	 *
	 * @return CustomerFirstname Customer's first name.
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}


	/**
	 * Returns the customer's last name.
	 *
	 * @return CustomerLastname Customer's last name.
	 */
	public function getLastname()
	{
		return $this->lastname;
	}


	/**
	 * Returns the customer's company.
	 *
	 * @return CustomerCompany Customer's company.
	 */
	public function getCompany()
	{
		return $this->company;
	}


	/**
	 * Returns the customer's B2B status.
	 *
	 * @return CustomerB2BStatus Customer's B2B status.
	 */
	public function getB2BStatus()
	{
		return $this->b2bStatus;
	}


	/**
	 * Returns the customer's street.
	 *
	 * @return CustomerStreet Customer's street.
	 */
	public function getStreet()
	{
		return $this->street;
	}
	
	
	/**
	 * Returns the customer's house number.
	 *
	 * @return CustomerHouseNumber Customer's house number.
	 */
	public function getHouseNumber()
	{
		return $this->houseNumber;
	}
	
	
	/**
	 * Returns the customer's additional address information.
	 *
	 * @return CustomerAdditionalAddressInfo Customer's additional address information.
	 */
	public function getAdditionalAddressInfo()
	{
		return $this->additionalAddressInfo;
	}


	/**
	 * Returns the customer's suburb.
	 *
	 * @return CustomerSuburb Customer's suburb.
	 */
	public function getSuburb()
	{
		return $this->suburb;
	}


	/**
	 * Returns the customer's postcode.
	 *
	 * @return CustomerPostcode Customer's postcode.
	 */
	public function getPostcode()
	{
		return $this->postcode;
	}


	/**
	 * Returns the customer's city.
	 *
	 * @return CustomerCity Customer's city.
	 */
	public function getCity()
	{
		return $this->city;
	}


	/**
	 * Returns the customer's country.
	 *
	 * @return CustomerCountry Customer's country.
	 */
	public function getCountry()
	{
		return $this->country;
	}


	/**
	 * Returns the customer's country zone.
	 *
	 * @return CustomerCountryZone Customer's country zone.
	 */
	public function getCountryZone()
	{
		return $this->countryZone;
	}
} 