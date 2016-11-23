<?php
/* --------------------------------------------------------------
   AddressBlockInterface.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object.
 *
 * Interface AddressBlockInterface
 *
 * Stores all customer address data.
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface AddressBlockInterface
{

	/**
	 * Returns the customer's gender.
	 *
	 * @return CustomerGenderInterface Customer's gender.
	 */
	public function getGender();


	/**
	 * Returns the customer's first name.
	 *
	 * @return CustomerFirstnameInterface Customer's first name.
	 */
	public function getFirstname();


	/**
	 * Returns the customer's last name.
	 *
	 * @return CustomerLastnameInterface Customer's last name.
	 */
	public function getLastname();


	/**
	 * Returns the customer's company.
	 *
	 * @return CustomerCompanyInterface Customer's company.
	 */
	public function getCompany();


	/**
	 * Returns the customer's B2B status.
	 *
	 * @return CustomerB2BStatusInterface Customer's B2B status.
	 */
	public function getB2BStatus();


	/**
	 * Returns the customer's street.
	 *
	 * @return CustomerStreetInterface Customer's street.
	 */
	public function getStreet();
	
	
	/**
	 * Returns the customer's house number.
	 *
	 * @return CustomerHouseNumberInterface Customer's house number.
	 */
	public function getHouseNumber();
	
	
	/**
	 * Returns the customer's additional address information.
	 *
	 * @return CustomerAdditionalAddressInfoInterface Customer's additional address information.
	 */
	public function getAdditionalAddressInfo();


	/**
	 * Returns the customer's suburb.
	 *
	 * @return CustomerSuburbInterface Customer's suburb.
	 */
	public function getSuburb();


	/**
	 * Returns the customer's postcode.
	 *
	 * @return CustomerPostcodeInterface Customer's postcode.
	 */
	public function getPostcode();


	/**
	 * Returns the customer's city.
	 *
	 * @return CustomerCityInterface Customer's city.
	 */
	public function getCity();


	/**
	 * Returns the customer's country.
	 *
	 * @return CustomerCountryInterface Customer's country.
	 */
	public function getCountry();


	/**
	 * Returns the customer's country zone.
	 *
	 * @return CustomerCountryZoneInterface Customer's country zone.
	 */
	public function getCountryZone();
}
