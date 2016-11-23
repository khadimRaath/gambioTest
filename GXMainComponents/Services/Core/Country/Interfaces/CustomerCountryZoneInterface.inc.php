<?php
/* --------------------------------------------------------------
   CustomerCountryZoneInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerCountryZoneInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerCountryZoneInterface
{


	/**
	 * Getter method for the ID
	 * 
	 * @return int countryId
	 */
	public function getId();


	/**
	 * Getter method for the name
	 * 
	 * @return CustomerCountryZoneNameInterface country zone name
	 */
	public function getName();


	/**
	 * Getter method for the customer country zone ISO 2 code
	 * 
	 * @return CustomerCountryZoneIsoCodeInterface iso2 code
	 */
	public function getCode();
} 