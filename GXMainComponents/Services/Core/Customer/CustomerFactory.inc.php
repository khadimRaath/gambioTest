<?php
/* --------------------------------------------------------------
   CustomerFactory.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCustomerFactory');

/**
 * Class CustomerFactory
 *
 * Factory class for all needed customer data.
 *
 * @category System
 * @package  Customer
 * @extends  AbstractCustomerFactory
 */
class CustomerFactory extends AbstractCustomerFactory
{
	/**
	 * Creates a new customer object.
	 *
	 * @return Customer Created customer.
	 */
	public function createCustomer()
	{
		$customer = MainFactory::create('Customer');

		return $customer;
	}


	/**
	 * Creates a new customer address object.
	 *
	 * @return CustomerAddress Created customer address.
	 */
	public function createCustomerAddress()
	{
		$address = MainFactory::create('CustomerAddress');

		return $address;
	}


	/**
	 * Creates a new customer country object with the given parameters.
	 *
	 * @param IdType                       $id              Country ID.
	 * @param CustomerCountryNameInterface $name            Country name.
	 * @param CustomerCountryIso2Interface $iso2            Country ISO-2 code.
	 * @param CustomerCountryIso3Interface $iso3            Country ISO-3 code.
	 * @param IdType                       $addressFormatId Country address format ID.
	 * @param bool                         $status          Country status.
	 *
	 * @return CustomerCountry Created customer country.
	 */
	public function createCustomerCountry(IdType $id,
	                                      CustomerCountryNameInterface $name,
	                                      CustomerCountryIso2Interface $iso2,
	                                      CustomerCountryIso3Interface $iso3,
	                                      IdType $addressFormatId,
	                                      $status)
	{
		$country = MainFactory::create('CustomerCountry', $id, $name, $iso2, $iso3, $addressFormatId, $status);

		return $country;
	}


	/**
	 * Creates a new customer country zone object with the given parameters.
	 *
	 * @param IdType                              $id      Country zone ID.
	 * @param CustomerCountryZoneNameInterface    $name    Country zone name.
	 * @param CustomerCountryZoneIsoCodeInterface $isoCode Country ISO code.
	 *
	 * @return CustomerCountryZone Created customer country zone.
	 */
	public function createCustomerCountryZone(IdType $id,
	                                          CustomerCountryZoneNameInterface $name,
	                                          CustomerCountryZoneIsoCodeInterface $isoCode)
	{
		$countryZone = MainFactory::create('CustomerCountryZone', $id, $name, $isoCode);

		return $countryZone;
	}

} 