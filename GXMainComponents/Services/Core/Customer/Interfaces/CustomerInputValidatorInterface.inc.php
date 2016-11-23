<?php
/* --------------------------------------------------------------
   CustomerInputValidatorInterface.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerInputValidatorInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerInputValidatorInterface
{
	/**
	 * Checks if the entered customer's gender is valid.
	 *
	 * @param string $p_gender Customer's gender.
	 *
	 * @return bool Is valid?
	 */
	public function validateGender($p_gender);


	/**
	 * Checks if the entered customer's first name is valid.
	 *
	 * @param string $p_firstname Customer's first name.
	 *
	 * @return bool Is valid?
	 */
	public function validateFirstname($p_firstname);


	/**
	 * Checks if the entered customer's last name is valid.
	 *
	 * @param string $p_lastname Customer's last name.
	 *
	 * @return bool Is valid?
	 */
	public function validateLastname($p_lastname);


	/**
	 * Checks if the entered customer's date of birth is valid.
	 * Valid format is: dd.mm.yyyy
	 *
	 * @param string $p_dateOfBirth Customer's date of birth.
	 *
	 * @return bool Is valid?
	 */
	public function validateDateOfBirth($p_dateOfBirth);


	/**
	 * Checks if the entered customer's company is valid.
	 *
	 * @param string $p_company Customer's company.
	 *
	 * @return bool Is valid?
	 */
	public function validateCompany($p_company);


	/**
	 * Checks if the entered email and email confirmation are valid.
	 * It will check the minimum length, address syntax, confirmation matching and existence of e-mail address.
	 *
	 * @param string $p_email             Customer's E-Mail address.
	 * @param string $p_emailConfirmation Confirmation E-Mail address.
	 *
	 * @return bool Is valid?
	 */
	public function validateEmailAndConfirmation($p_email, $p_emailConfirmation);


	/**
	 * Checks if the entered email is valid.
	 *
	 * @param string $p_email Customer's E-Mail address.
	 *
	 * @return bool Is valid?
	 */
	public function validateEmail($p_email);


	/**
	 * Checks if the entered email already exists.
	 *
	 * @param string            $p_email  Customer's E-Mail address-
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return bool Is valid?
	 */
	public function validateEmailExists($p_email, CustomerInterface $customer = null);
	

	/**
	 * Checks if the entered street is valid.
	 *
	 * @param string $p_street Customer's street.
	 *
	 * @return bool Is valid?
	 */
	public function validateStreet($p_street);


	/**
	 * Checks if the entered postcode is valid.
	 *
	 * @param string $p_postcode Customer's post code.
	 *
	 * @return bool Is valid?
	 */
	public function validatePostcode($p_postcode);


	/**
	 * Checks if the entered city is valid.
	 *
	 * @param string $p_city Customer's city.
	 *
	 * @return bool Is valid?
	 */
	public function validateCity($p_city);


	/**
	 * Checks if the entered country exists.
	 *
	 * @param int $p_countryId Customer's country ID.
	 *
	 * @return bool Is valid?
	 */
	public function validateCountry($p_countryId);


	/**
	 * Checks if the entered suburb is valid.
	 *
	 * @param $p_suburb Customer's suburb.
	 *
	 * @return bool Is valid?
	 */
	public function validateSuburb($p_suburb);
	

	/**
	 * Checks if the entered country zone is valid.
	 *
	 * Cases:
	 * - If country has zones: It checks if zone belongs to country.
	 * - If country does not contain zones: It checks the minimum length of zone name.
	 *
	 * @param $p_countryZoneName Customer's country zone name.
	 * @param $p_countryId       Customer's country ID.
	 *
	 * @return bool Is valid?
	 */
	public function validateCountryZone($p_countryZoneName, $p_countryId);


	/**
	 * Checks if the entered telephone number is valid.
	 *
	 * @param string $p_telephoneNumber Customer's telephone number.
	 *
	 * @return bool Is valid?
	 */
	public function validateTelephoneNumber($p_telephoneNumber);


	/**
	 * Checks if the entered password is valid.
	 *
	 * @param string $p_password             Customer's password.
	 * @param string $p_passwordConfirmation Customer's password confirmation.
	 *
	 * @return bool Is valid?
	 */
	public function validatePassword($p_password, $p_passwordConfirmation);


	/**
	 * Returns error messages.
	 *
	 * @return array Error messages.
	 */
	public function getErrorMessages();


	/**
	 * Returns a collection of error messages.
	 *
	 * @return EditableKeyValueCollection Collection of error messages.
	 */
	public function getErrorMessageCollection();
	

	/**
	 * Returns the error status.
	 *
	 * @return bool Error status.
	 */
	public function getErrorStatus();
	
	
	/**
	 * Checks if the user has accepted the privacy policy.
	 *
	 * @param string $p_privacyAccepted
	 *
	 * @return bool Is valid?
	 */
	public function validatePrivacy($p_privacyAccepted);
} 