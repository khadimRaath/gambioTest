<?php

/* --------------------------------------------------------------
   AddressFormatProviderInterface.inc.php 2016-01-04
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
 * @category   System
 * @package    Shared
 * @subpackage Interfaces
 */
interface AddressFormatProviderInterface
{
	/**
	 * Returns the address format IDs.
	 * @return IdCollection Collection of address format IDs.
	 */
	public function getIds();
	
	
	/**
	 * Returns address format IDs based on the country ID provided.
	 *
	 * @param IdType $countryId Country ID.
	 *
	 * @return int Address format ID.
	 */
	public function getByCountryId(IdType $countryId);
	

	/**
	 * Returns the address format IDs based on the ISO-2 country code provided.
	 *
	 * @param StringType $iso2 ISO-2 country code.
	 *
	 * @return int Address format ID.
	 */
	public function getByIsoCode2(StringType $iso2);


	/**
	 * Returns the address format IDs based on the ISO-3 country code provided.
	 *
	 * @param StringType $iso3 ISO-3 country code.
	 *
	 * @return int Address format ID.
	 */
	public function getByIsoCode3(StringType $iso3);
}