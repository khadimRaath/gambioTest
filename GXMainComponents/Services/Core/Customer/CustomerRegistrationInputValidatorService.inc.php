<?php
/* --------------------------------------------------------------
   CustomerRegistrationInputValidatorService.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidator');
MainFactory::load_class('CustomerRegistrationInputValidatorServiceInterface');

/**
 * Class CustomerRegistrationInputValidatorService
 *
 * This class is used for validating customer input while registration
 *
 * @category   System
 * @package    Customer
 * @subpackage Validation
 * @extends    CustomerInputValidator
 * @implements CustomerRegistrationInputValidatorServiceInterface
 */
class CustomerRegistrationInputValidatorService extends CustomerInputValidator
	implements CustomerRegistrationInputValidatorServiceInterface
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
	public function validateCustomerDataByArray(array $inputArray)
	{
		$this->_validateDataByArray($inputArray);
		$this->validateVatNumber($inputArray['vat'], $inputArray['country'], false);
		$this->validatePassword($inputArray['password'], $inputArray['confirmation']);
		$this->validateAddonValues($inputArray['addon_values']);
		
		return !$this->getErrorStatus();
	}


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
	public function validateGuestDataByArray(array $inputArray)
	{
		$this->_validateDataByArray($inputArray);
		$this->validateVatNumber($inputArray['vat'], $inputArray['country'], true);
		$this->validateAddonValues($inputArray['addon_values']);
		
		return !$this->getErrorStatus();
	}

	
	/**
	 * Checks if the entered data is valid.
	 *
	 * Expects an array with the following keys:
	 *  - gender
	 *  - firstname
	 *  - lastname
	 *  - dob (date of birth)
	 *  - company
	 *  - email_address
	 *  - suburb
	 *  - email_address_confirm
	 *  - postcode
	 *  - city
	 *  - country
	 *  - state (ID or name)
	 *  - telephone
	 *  - fax
	 *  - vat
	 *  - privacy_accepted
	 *
	 * @param array $inputArray Customer Input array.
	 *
	 * @return bool Is customer data valid?
	 */
	protected function _validateDataByArray(array $inputArray)
	{
		if(!$this->_namesOptionalAndCompanyNotEmpty($inputArray))
		{
			$this->validateGender($inputArray['gender']);
			$this->validateFirstname($inputArray['firstname']);
			$this->validateLastname($inputArray['lastname']);
		}
		$this->validateDateOfBirth($inputArray['dob']);
		$this->validateCompany($inputArray['company']);
		$this->validateEmailAndConfirmation($inputArray['email_address'], $inputArray['email_address_confirm']);
		$this->validateStreet($inputArray['street_address']);
		$this->validateSuburb($inputArray['suburb']);
		$this->validatePostcode($inputArray['postcode']);
		$this->validateCity($inputArray['city']);
		$this->validateCountry($inputArray['country']);
		$this->validateCountryZone($inputArray['state'], $inputArray['country']);
		$this->validateTelephoneNumber($inputArray['telephone']);
		$this->validateFaxNumber($inputArray['fax']);
		$this->validatePrivacy($inputArray['privacy_accepted']);

		return !$this->getErrorStatus();
	}
	
	
	private function _namesOptionalAndCompanyNotEmpty(array $inputArray)
	{
		return $this->settings->isNamesOptional() && array_key_exists('company', $inputArray)
		       && $inputArray['company'] !== '';
	}
}
