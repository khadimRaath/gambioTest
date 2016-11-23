<?php
/* --------------------------------------------------------------
   CustomerRegistrationInputValidatorServiceInterface.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerRegistrationInputValidatorServiceInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerRegistrationInputValidatorServiceInterface
{
	/**
	 * Validates the entered customer data with an array of parameters.
	 *
	 * Expects array with following keys:
	 *  - gender
	 *  - firstname
	 *  - lastname
	 *  - dob (date of birth)
	 *  - company
	 *  - email_address
	 *  - email_address_confirm
	 *  - postcode
	 *  - city
	 *  - country
	 *  - state (ID or name)
	 *  - telephone
	 *  - vat
	 *  - password
	 *  - confirmation
	 *  - privacy_accepted
	 *
	 * @param array $inputArray Customer data input.
	 *
	 * @return bool Is customer data valid?
	 */
	public function validateCustomerDataByArray(array $inputArray);


	/**
	 * Validate the entered guest data with an array of parameters.
	 *
	 * expects array with following keys:
	 *  - gender
	 *  - firstname
	 *  - lastname
	 *  - dob (date of birth)
	 *  - company
	 *  - email_address
	 *  - email_address_confirm
	 *  - postcode
	 *  - city
	 *  - country
	 *  - state (ID or name)
	 *  - telephone
	 *  - vat
	 *  - privacy_accepted
	 *
	 * @param array $inputArray Guest customer data input.
	 *
	 * @return bool Is guest customer data valid?
	 */
	public function validateGuestDataByArray(array $inputArray);
}
