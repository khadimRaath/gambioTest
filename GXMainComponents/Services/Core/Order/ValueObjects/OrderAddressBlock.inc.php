<?php

/* --------------------------------------------------------------
   OrderAddressBlock.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderAddressBlock
 *
 * This class is used by the OrderListItem and InvoiceListItem
 * and includes all required address values for the listings.
 *
 * @category   System
 * @package    Order
 * @subpackage ValueObjects
 */
class OrderAddressBlock
{
	/**
	 * @var string
	 */
	protected $firstName;
	
	/**
	 * @var string
	 */
	protected $lastName;
	
	/**
	 * @var string
	 */
	protected $company;
	
	/**
	 * @var string
	 */
	protected $street;
	
	/**
	 * @var string
	 */
	protected $houseNumber;
	
	/**
	 * @var string
	 */
	protected $additionalAddressInfo;
	
	/**
	 * @var String
	 */
	protected $postcode;
	
	/**
	 * @var string
	 */
	protected $city;
	
	/**
	 * @var string
	 */
	protected $state;
	
	/**
	 * @var string
	 */
	protected $country;
	
	/**
	 * @var string
	 */
	protected $countryIsoCode;
	
	
	/**
	 * OrderAddressBlock constructor.
	 *
	 * @param StringType $firstName
	 * @param StringType $lastName
	 * @param StringType $company
	 * @param StringType $street
	 * @param StringType $houseNumber
	 * @param StringType $additionalAddressInfo
	 * @param StringType $postcode
	 * @param StringType $city
	 * @param StringType $state
	 * @param StringType $country
	 * @param StringType $countryIsoCode
	 */
	public function __construct(StringType $firstName,
	                            StringType $lastName,
	                            StringType $company,
	                            StringType $street,
	                            StringType $houseNumber,
	                            StringType $additionalAddressInfo,
	                            StringType $postcode,
	                            StringType $city,
	                            StringType $state,
	                            StringType $country,
	                            StringType $countryIsoCode)
	{
		$this->firstName = $firstName->asString();
		$this->lastName = $lastName->asString();
		$this->company = $company->asString();
		$this->street = $street->asString();
		$this->houseNumber = $houseNumber->asString();
		$this->additionalAddressInfo = $additionalAddressInfo->asString();
		$this->postcode = $postcode->asString();
		$this->city = $city->asString();
		$this->state = $state->asString();
		$this->country = $country->asString();
		$this->countryIsoCode = $countryIsoCode->asString();
	}


	/**
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}


	/**
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}


	/**
	 * @return string
	 */
	public function getCompany()
	{
		return $this->company;
	}

	
	/**
	 * @return string
	 */
	public function getStreet()
	{
		return $this->street;
	}
	
	
	/**
	 * @return string
	 */
	public function getHouseNumber()
	{
		return $this->houseNumber;
	}
	
	
	/**
	 * @return string
	 */
	public function getAdditionalAddressInfo()
	{
		return $this->additionalAddressInfo;
	}
	
	
	/**
	 * @return String
	 */
	public function getPostcode()
	{
		return $this->postcode;
	}
	
	
	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	
	/**
	 * @return string
	 */
	public function getState()
	{
		return $this->state;
	}
	
	
	/**
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
	
	/**
	 * @return string
	 */
	public function getCountryIsoCode()
	{
		return $this->countryIsoCode;
	}
}