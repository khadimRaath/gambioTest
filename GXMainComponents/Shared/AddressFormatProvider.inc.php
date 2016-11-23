<?php

/* --------------------------------------------------------------
   AddressFormatProvider.inc.php 2016-01-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCollection
 *
 * @category System
 * @package  Shared
 */
class AddressFormatProvider implements AddressFormatProviderInterface
{
	/**
	 * Database connection.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * AddressFormatProvider constructor.
	 *
	 * @param CI_DB_query_builder $db Database connection.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	

	/**
	 * Returns the address format IDs.
	 * @throws UnexpectedValueException If no ID has been found.
	 * @return IdCollection Collection of address format IDs.
	 */
	public function getIds()
	{
		// Database query.
		$query = $this->db->select('address_format_id')->from('address_format');

		// Array in which the fetched address format IDs will be pushed as IdType to.
		$fetchedIds = array();

		// Iterate over each found row and push ID as IdType to array.
		foreach($query->get()->result_array() as $row)
		{
			$id           = (integer)$row['address_format_id'];
			$fetchedIds[] = new IdType($id);
		}

		// Throw exception if not ID has been found.
		if($fetchedIds === null)
		{
			throw new UnexpectedValueException('No address format IDs were found in the database');
		}

		// Create a new IdCollection class with the array of IdTypes.
		$collection = MainFactory::create('IdCollection', $fetchedIds);

		// Return created IdCollection
		return $collection;
	}


	/**
	 * Returns address format ID based on the country ID provided.
	 *
	 * @param IdType $countryId Country ID.
	 *
	 * @throws UnexpectedValueException If no results have been found.
	 * @return int Address format ID.
	 */
	public function getByCountryId(IdType $countryId)
	{
		// Database query.
		$this->db->select('address_format.address_format_id')
		         ->from('address_format')
		         ->join('countries', 'address_format.address_format_id = countries.address_format_id')
		         ->where('countries.countries_id', $countryId->asInt());

		$data = $this->db->get()->row_array();

		if($data === null)
		{
			throw new UnexpectedValueException('No address format ID has been found');
		}

		$id = (integer)$data['address_format_id'];

		return $id;
	}


	/**
	 * Returns the address format ID based on the ISO-2 country code provided.
	 *
	 * @param StringType $iso2 ISO-2 country code.
	 *
	 * @throws UnexpectedValueException If no results have been found.
	 * @throws InvalidArgumentException If code does not have two letters.
	 *
	 * @return int Address format ID.
	 */
	public function getByIsoCode2(StringType $iso2)
	{
		if(strlen($iso2->asString()) !== 2) // Argument must have 2 letters.
		{
			throw new InvalidArgumentException('Provided ISO code must have exactly 2 letters');
		}

		// Database query.
		$this->db->select('address_format.address_format_id')
		         ->from('address_format')
		         ->join('countries', 'address_format.address_format_id = countries.address_format_id')
		         ->where('countries.countries_iso_code_2', $iso2->asString());

		$data = $this->db->get()->row_array();

		if($data === null)
		{
			throw new UnexpectedValueException('No address format ID has been found');
		}

		$id = (integer)$data['address_format_id'];

		return $id;
	}


	/**
	 * Returns the address format ID based on the ISO-3 country code provided.
	 *
	 * @param StringType $iso3 ISO-3 country code.
	 *
	 * @throws UnexpectedValueException If no results have been found.
	 * @throws InvalidArgumentException If code does not have three letters.
	 *
	 * @return int Address format ID.
	 */
	public function getByIsoCode3(StringType $iso3)
	{
		if(strlen($iso3->asString()) !== 3) // Argument must have 3 letters.
		{
			throw new InvalidArgumentException('Provided ISO code must have exactly 3 letters');
		}

		// Database query.
		$this->db->select('address_format.address_format_id')
		         ->from('address_format')
		         ->join('countries', 'address_format.address_format_id = countries.address_format_id')
		         ->where('countries.countries_iso_code_3', $iso3->asString());

		$data = $this->db->get()->row_array();

		if($data === null)
		{
			throw new UnexpectedValueException('No address format ID has been found');
		}

		$id = (integer)$data['address_format_id'];

		return $id;
	}

}