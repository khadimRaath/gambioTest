<?php
/* --------------------------------------------------------------
   CustomerCountryReaderInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryZoneReaderInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerCountryZoneReaderInterface
{

	/**
	 * Method to find a country zone with a given name if it exists else it will return null
	 * 
	 * @param CustomerCountryZoneNameInterface $countryZoneName
	 *
	 * @return CustomerCountryZone|null
	 * @throws InvalidArgumentException if $p_countryZoneName is not a string
	 */
	public function findByName(CustomerCountryZoneNameInterface $countryZoneName);


	/**
	 * Method to find a country zone with a given ID if it exists else it will return null
	 * 
	 * @param IdType $countryZoneId
	 *
	 * @return CustomerCountryZone|null
	 * @throws InvalidArgumentException if $p_countryZoneName is not a string
	 */
	public function findById(IdType $countryZoneId);


	/**
	 * Method to find a country zone with a given country ID if it exists else it will return an empty array
	 * 
	 * @param IdType $countryId
	 *
	 * @return array of CustomerCountryZone objects|empty array
	 * @throws InvalidArgumentException
	 */
	public function findCountryZonesByCountryId(IdType $countryId);
}