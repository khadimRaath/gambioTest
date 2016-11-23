<?php
/* --------------------------------------------------------------
   CustomerAccountInputValidatorInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAccountInputValidatorInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerAccountInputValidatorInterface
{
	/**
	 * Validates the customer account input with a given array.
	 *
	 * @param array             $inputArray Input data.
	 * @param CustomerInterface $customer   Customer data.
	 *
	 * @return int              The error status of the validation.
	 */
	public function validateByArray(array $inputArray, CustomerInterface $customer);
}
