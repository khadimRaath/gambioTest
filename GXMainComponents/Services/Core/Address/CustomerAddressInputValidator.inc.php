<?php
/* --------------------------------------------------------------
   CustomerAddressInputValidator.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidator');
MainFactory::load_class('CustomerAddressInputValidatorInterface');

/**
 * Class CustomerAccountInputValidator
 * 
 * This class is used for validating entered customer address data
 * 
 * @category System
 * @package Customer
 * @subpackage Address
 *
 * @extends CustomerInputValidator
 * @implements CustomerAddressInputValidatorInterface
 */
class CustomerAddressInputValidator extends CustomerInputValidator
	implements CustomerAddressInputValidatorInterface
{
	/**
	 * Validates the entered customer address data based on a given array
	 * 
	 * expects array with following keys:
	 * gender, company, firstname, lastname, street_address, suburb, postcode, city, country, state
	 * 
	 * @param array             $inputArray
	 *
	 * @return bool Returns the validation result (false indicates no validation error).
	 */
	public function validateByArray(array $inputArray)
	{
		$this->validateGender($inputArray['gender']);
		$this->validateCompany($inputArray['company']);
		$this->validateFirstname($inputArray['firstname']);
		$this->validateLastname($inputArray['lastname']);
		$this->validateStreet($inputArray['street_address']);
		$this->validateCountryZone($inputArray['state'], $inputArray['country']);
		$this->validatePostcode($inputArray['postcode']);
		$this->validateCity($inputArray['city']);
		$this->validateCountry($inputArray['country']);
		$this->validateSuburb($inputArray['suburb']);
		
		return $this->getErrorStatus();
	}
}
