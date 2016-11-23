<?php
/* --------------------------------------------------------------
   CustomerAddressInputValidatorInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressInputValidatorInterface
 * 
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerAddressInputValidatorInterface
{
	/**
	 * Method to validate the customer address input with a given array
	 * 
	 * expects array with following keys:
	 * gender, company, firstname, lastname, street_address, suburb, postcode, city, country, primary, state
	 * 
	 * @param array             $inputArray
	 *
	 * @return int				The error status of the validation
	 */
	public function validateByArray(array $inputArray);
}
