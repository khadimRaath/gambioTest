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
 * Interface CustomerCountryReaderInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerCountryReaderInterface 
{
	/**
	 * Method to find a country with a given ID if it exists else it will return null
	 * 
	 * @param IdType $countryId
	 *
	 * @throws InvalidArgumentException if $p_countryId is not a valid ID
	 * @throws Exception if country not found
	 * @return CustomerCountry
	 */
	public function findById(IdType $countryId);
	
	
	
	/**
	 * Method to find a country with a given name if it exists else it will return null
	 * 
	 * @param $countryName
	 *
	 * @return CustomerCountry|null
	 */
	public function findByName(CustomerCountryNameInterface $countryName);
}