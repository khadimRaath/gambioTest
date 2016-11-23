<?php
/* --------------------------------------------------------------
   CustomerCountryRepositoryInterface.inc.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryRepositoryInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerCountryRepositoryInterface 
{

	/**
	 * Method to get a customer country with a given country ID
	 * 
	 * @param IdType $countryId
     * 
     * @return CustomerCountryInterface
	 */
	public function getById(IdType $countryId);


	/**
	 * Method to find a country if exists else return null
	 * 
     * @param IdType $countryId
     * 
     * @return CustomerCountry|null
	 */
	public function findById(IdType $countryId);
	
	
	/**
	 * Get country by name.
	 *
	 * @param \CustomerCountryNameInterface $countryName
	 *
	 * @return CustomerCountry
	 *
	 * @throws Exception If the country could not be found.
	 */
	public function getByName(CustomerCountryNameInterface $countryName);
	
	
	/**
	 * Find country by name.
	 *
	 * @param CustomerCountryNameInterface $countryName
	 *
	 * @return CustomerCountry
	 */
	public function findByName(CustomerCountryNameInterface $countryName); 
}