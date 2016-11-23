<?php
/* --------------------------------------------------------------
   CustomerAddressWriter.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAddressWriterInterface');

/**
 * Class CustomerAddressWriter
 *
 * This class is used for writing customer address data into the database
 *
 * @category   System
 * @package    Customer
 * @subpackage Address
 * @implements CustomerAddressWriterInterface
 */
class CustomerAddressWriter implements CustomerAddressWriterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * Constructor of the class CustomerAddressWriter
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Calls the protected insert method to store the customer address into the database
	 *
	 * @param CustomerAddressInterface $address
	 */
	public function write(CustomerAddressInterface $address)
	{
		$id = $address->getId();
		if(empty($id))
		{
			$this->_insert($address);
		}
		else
		{
			$this->_update($address);
		}
	}


	/**
	 * Inserts the customer address into the database
	 *
	 * @param CustomerAddressInterface $address
	 *
	 * @return CustomerAddressInterface
	 */
	protected function _insert(CustomerAddressInterface $address)
	{
		// Check for Country and Country Zone values.
		$countryId = ($address->getCountry() !== null) ? $address->getCountry()->getId() : null;
		$zoneName  = ($address->getCountryZone() !== null) ? $address->getCountryZone()->getName() : null;
		$zoneId    = ($address->getCountryZone() !== null) ? $address->getCountryZone()->getId() : null;

		$addressDataArray = array(
			'customers_id'          => (int)(string)$address->getCustomerId(),
			'entry_gender'          => (string)$address->getGender(),
			'entry_company'         => (string)$address->getCompany(),
			'entry_firstname'       => (string)$address->getFirstname(),
			'entry_lastname'        => (string)$address->getLastname(),
			'entry_street_address'  => (string)$address->getStreet(),
			'entry_house_number'    => (string)$address->getHouseNumber(),
			'entry_additional_info' => (string)$address->getAdditionalAddressInfo(),
			'entry_suburb'          => (string)$address->getSuburb(),
			'entry_postcode'        => (string)$address->getPostcode(),
			'entry_city'            => (string)$address->getCity(),
			'entry_state'           => (string)$zoneName,
			'entry_country_id'      => (int)$countryId,
			'entry_zone_id'         => (int)$zoneId,
			'customer_b2b_status'   => (int)(string)$address->getB2BStatus(),
			'address_date_added'    => date('Y-m-d'),
			'address_last_modified' => date('Y-m-d')
		);

		$this->db->insert('address_book', $addressDataArray);
		$address->setId(new IdType($this->db->insert_id()));

		return $address;
	}


	/**
	 * Updates an existing customer address in the database
	 *
	 * @param CustomerAddressInterface $address
	 *
	 * @return CustomerAddressInterface
	 */
	protected function _update(CustomerAddressInterface $address)
	{
		// Check for Country and Country Zone values.
		$countryId = ($address->getCountry() !== null) ? $address->getCountry()->getId() : null;
		$zoneName  = ($address->getCountryZone() !== null) ? $address->getCountryZone()->getName() : null;
		$zoneId    = ($address->getCountryZone() !== null) ? $address->getCountryZone()->getId() : null;

		$addressDataArray = array(
			'customers_id'          => (int)(string)$address->getCustomerId(),
			'entry_gender'          => (string)$address->getGender(),
			'entry_company'         => (string)$address->getCompany(),
			'entry_firstname'       => (string)$address->getFirstname(),
			'entry_lastname'        => (string)$address->getLastname(),
			'entry_street_address'  => (string)$address->getStreet(),
			'entry_house_number'    => (string)$address->getHouseNumber(),
			'entry_additional_info' => (string)$address->getAdditionalAddressInfo(),
			'entry_suburb'          => (string)$address->getSuburb(),
			'entry_postcode'        => (string)$address->getPostcode(),
			'entry_city'            => (string)$address->getCity(),
			'entry_state'           => (string)$zoneName,
			'entry_country_id'      => (int)$countryId,
			'entry_zone_id'         => (int)$zoneId,
			'customer_b2b_status'   => (int)(string)$address->getB2BStatus(),
			'address_last_modified' => date('Y-m-d')
		);

		$this->db->update('address_book', $addressDataArray,
		                  array('address_book_id' => (int)(string)$address->getId()));

		return $address;
	}
}