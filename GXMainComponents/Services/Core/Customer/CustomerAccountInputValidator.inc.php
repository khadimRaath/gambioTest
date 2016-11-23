<?php
/* --------------------------------------------------------------
   CustomerAccountInputValidator.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidator');
MainFactory::load_class('CustomerAccountInputValidatorInterface');

/**
 * Class CustomerAccountInputValidator
 *
 * This class is used for validating input data from the customer account
 *
 * @category   System
 * @package    Customer
 * @subpackage Validation
 * @extends    CustomerInputValidator
 * @implements CustomerAccountInputValidatorInterface
 */
class CustomerAccountInputValidator extends CustomerInputValidator implements CustomerAccountInputValidatorInterface
{
	/**
	 * Checks if the entered customer data is valid based on an array.
	 *
	 * Expects an array with following keys:
	 *  - gender
	 *  - firstname
	 *  - lastname
	 *  - dob (date of birth)
	 *  - email_address
	 *  - telephone
	 *  - vat
	 *  - country
	 *
	 * @param array             $inputArray Input data.
	 * @param CustomerInterface $customer   Customer data.
	 *
	 * @return bool Returns the validation result (true indicates no validation error).
	 */
	public function validateByArray(array $inputArray, CustomerInterface $customer)
	{
		$this->validateGender($inputArray['gender']);
		$this->validateFirstname($inputArray['firstname']);
		$this->validateLastname($inputArray['lastname']);
		$this->validateDateOfBirth($inputArray['dob']);
		$this->validateEmail($inputArray['email_address']);
		$this->validateEmailExists($inputArray['email_address'], $customer);
		$this->validateTelephoneNumber($inputArray['telephone']);
		$this->validateVatNumber($inputArray['vat'], $inputArray['country'], $customer->isGuest());
		
		return !$this->getErrorStatus();
	}
}
