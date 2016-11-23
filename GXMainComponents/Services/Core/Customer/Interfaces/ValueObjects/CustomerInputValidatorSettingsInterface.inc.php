<?php
/* --------------------------------------------------------------
   CustomerInputValidatorSettingsInterface.inc.php 2016-08-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object.
 *
 * Interface CustomerInputValidatorSettingsInterface
 *
 * CustomerInputValidatorSettings stores all min length values and error messages for registration form validation.
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerInputValidatorSettingsInterface
{

	/**
	 * Returns a city error message.
	 * @return string City error message.
	 */
	public function getCityErrorMessage();


	/**
	 * Returns the minimum required city character length.
	 * @return int City Minimum required city character length.
	 */
	public function getCityMinLength();


	/**
	 * Returns a company error message.
	 * @return string Company error message.
	 */
	public function getCompanyErrorMessage();


	/**
	 * Returns the minimum required company character length.
	 * @return int Minimum required company character length.
	 */
	public function getCompanyMinLength();


	/**
	 * Returns a country error message.
	 * @return string Country error message.
	 */
	public function getCountryErrorMessage();


	/**
	 * Returns a country zone error message.
	 * @return string Country zone error message.
	 */
	public function getCountryZoneErrorMessage();


	/**
	 * Returns the minimum required country zone character length.
	 * @return int Minimum required country zone character length.
	 */
	public function getCountryZoneMinLength();


	/**
	 * Returns a country zone selection error message.
	 * @return string Country zone selection error message.
	 */
	public function getCountryZoneSelectionErrorMessage();


	/**
	 * Returns a date of birth error message.
	 * @return string Date of birth error message.
	 */
	public function getDateOfBirthErrorMessage();


	/**
	 * Returns the minimum required date of birth character length.
	 * @return int Minimum required date of birth character length.
	 */
	public function getDateOfBirthMinLength();


	/**
	 * Returns an email address check error message.
	 * @return string Email address check error message.
	 */
	public function getEmailAddressCheckErrorMessage();


	/**
	 * Returns an email confirmation error message.
	 * @return string Email confirmation error message.
	 */
	public function getEmailConfirmationErrorMessage();


	/**
	 * Returns an email error message.
	 * @return string Email error message.
	 */
	public function getEmailErrorMessage();


	/**
	 * Returns an email exists error message.
	 * @return string Email exists error message.
	 */
	public function getEmailExistsErrorMessage();


	/**
	 * Returns the minimum required email character length.
	 * @return int Minimum required email character length.
	 */
	public function getEmailMinLength();


	/**
	 * Returns a first name error message.
	 * @return string First name error message.
	 */
	public function getFirstnameErrorMessage();


	/**
	 * Returns the minimum required first name character length.
	 * @return int Minimum required first name character length.
	 */
	public function getFirstnameMinLength();


	/**
	 * Returns a gender error message.
	 * @return string Gender error message.
	 */
	public function getGenderErrorMessage();


	/**
	 * Returns a last name error message.
	 * @return string Last name error message.
	 */
	public function getLastnameErrorMessage();


	/**
	 * Returns the minimum required last name character length.
	 * @return int Minimum required last name character length.
	 */
	public function getLastnameMinLength();


	/**
	 * Returns a password error message.
	 * @return string Password error message.
	 */
	public function getPasswordErrorMessage();


	/**
	 * Returns the minimum required password character length.
	 * @return int Minimum required password character length.
	 */
	public function getPasswordMinLength();


	/**
	 * Returns a password mismatch error message.
	 * @return string Password mismatch error message.
	 */
	public function getPasswordMismatchErrorMessage();


	/**
	 * Returns a post code error message.
	 * @return string Post code error message.
	 */
	public function getPostcodeErrorMessage();


	/**
	 * Returns the minimum required post code character length.
	 * @return int Minimum required post code character length.
	 */
	public function getPostcodeMinLength();


	/**
	 * Returns a street error message.
	 * @return string Street error message.
	 */
	public function getStreetErrorMessage();


	/**
	 * Returns the minimum required street character length.
	 * @return int Minimum required street character length.
	 */
	public function getStreetMinLength();


	/**
	 * Returns a telephone number error message.
	 * @return string Telephone number error message.
	 */
	public function getTelephoneNumberErrorMessage();


	/**
	 * Returns the minimum required telephone number character length.
	 * @return int Minimum required telephone number character length.
	 */
	public function getTelephoneNumberMinLength();


	/**
	 * Returns a VAT number error message.
	 * @return string VAT number error message.
	 */
	public function getVatNumberErrorMessage();


	/**
	 * Retrieves state value of company displaying.
	 * @return bool Display company?
	 */
	public function getDisplayCompany();


	/**
	 * Retrieves state value of country displaying.
	 * @return bool Display country?
	 */
	public function getDisplayCountryZone();


	/**
	 * Retrieves state value of date of birth displaying.
	 * @return bool Display date of birth?
	 */
	public function getDisplayDateOfBirth();


	/**
	 * Retrieves state value of gender displaying.
	 * @return bool Display gender?
	 */
	public function getDisplayGender();


	/**
	 * Retrieves state value of telephone number displaying
	 * @return bool Display telephone number?
	 */
	public function getDisplayTelephone();
	
	
	/**
	 * Retrieves state value of suburb displaying
	 * @return bool Display suburb?
	 */
	public function getDisplaySuburb();


	/**
	 * Retrieves state value of fax displaying
	 * @return bool Display fax?
	 */
	public function getDisplayFax();
	

	/**
	 * Returns an invalid input error message.
	 * @return string Invalid input error message.
	 */
	public function getInvalidInputErrorMessage();


	/**
	 * Returns the maximum required first name character length.
	 * @return int Maximum required first name character length.
	 */
	public function getFirstnameMaxLength();


	/**
	 * Returns the maximum required last name character length.
	 * @return int Maximum required last name character length.
	 */
	public function getLastnameMaxLength();


	/**
	 * Returns the maximum required company character length.
	 * @return int Maximum required company character length.
	 */
	public function getCompanyMaxLength();


	/**
	 * Returns the maximum required VAT number character length.
	 * @return int Maximum required VAT number character length.
	 */
	public function getVatNumberMaxLength();


	/**
	 * Returns the maximum required street character length.
	 * @return int Maximum required street character length.
	 */
	public function getStreetMaxLength();


	/**
	 * Returns the maximum required post code character length.
	 * @return int Maximum required post code character length.
	 */
	public function getPostcodeMaxLength();


	/**
	 * Returns the maximum required city character length.
	 * @return int Maximum required city character length.
	 */
	public function getCityMaxLength();


	/**
	 * Returns the maximum required country zone character length.
	 * @return int Maximum required country zone character length.
	 */
	public function getCountryZoneMaxLength();


	/**
	 * Returns the maximum required suburb character length.
	 * @return int Maximum required suburb character length.
	 */
	public function getSuburbMaxLength();


	/**
	 * Returns the maximum required call number character length.
	 * @return int Maximum required call number character length.
	 */
	public function getCallNumberMaxLength();

	
	/**
	 * @return bool
	 */
	public function isNamesOptional();
	
	
	/**
	 * Retrieves state value of displaying privacy checkbox
	 * @return bool Display privacy checkbox?
	 */
	public function getAcceptPrivacy();
	
	
	/**
	 * Returns a privacy not accepted error message.
	 * @return string Privacy not accepted error message.
	 */
	public function getPrivacyErrorMessage();
} 