<?php
/* --------------------------------------------------------------
   CustomerInputValidatorSettings.inc.php 2016-09-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidatorSettingsInterface');

/**
 * Value Object
 *
 * Class CustomerInputValidatorSettings
 *
 * CustomerInputValidatorSettings stores all min length values and error messages for registration form validation
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerInputValidatorSettingsInterface
 */
class CustomerInputValidatorSettings implements CustomerInputValidatorSettingsInterface
{
	/**
	 * Customer's first name minimum length.
	 * @var int
	 */
	protected $firstnameMinLength;

	/**
	 * Customer's last name minimum length.
	 * @var int
	 */
	protected $lastnameMinLength;

	/**
	 * Customer's date of birth minimum length.
	 * @var int
	 */
	protected $dateOfBirthMinLength;

	/**
	 * Customer's E-Mail address minimum length.
	 * @var int
	 */
	protected $emailMinLength;

	/**
	 * Customer's street minimum length.
	 * @var int
	 */
	protected $streetMinLength;

	/**
	 * Customer's company minimum length.
	 * @var int
	 */
	protected $companyMinLength;

	/**
	 * Customer's post code minimum length.
	 * @var int
	 */
	protected $postcodeMinLength;

	/**
	 * Customer's city minimum length.
	 * @var int
	 */
	protected $cityMinLength;

	/**
	 * Customer's country zone minimum length.
	 * @var int
	 */
	protected $countryZoneMinLength;

	/**
	 * Customer's telephone number minimum length.
	 * @var int
	 */
	protected $telephoneNumberMinLength;

	/**
	 * Customer's password minimum length.
	 * @var int
	 */
	protected $passwordMinLength;

	/**
	 * Customer's gender error message.
	 * @var string
	 */
	protected $genderErrorMessage;

	/**
	 * Customer's first name error message.
	 * @var string
	 */
	protected $firstnameErrorMessage;

	/**
	 * Customer's last name error message.
	 * @var string
	 */
	protected $lastnameErrorMessage;

	/**
	 * Customer's date of birth error message.
	 * @var string
	 */
	protected $dateOfBirthErrorMessage;

	/**
	 * Customer's company error message.
	 * @var string
	 */
	protected $companyErrorMessage;

	/**
	 * Customer's VAT number error message.
	 * @var string
	 */
	protected $vatNumberErrorMessage;

	/**
	 * Customer's E-Mail address error message.
	 * @var string
	 */
	protected $emailErrorMessage;

	/**
	 * Customer's E-Mail address check error message.
	 * @var string
	 */
	protected $emailAddressCheckErrorMessage;

	/**
	 * Customer's E-Mail address confirmation error message.
	 * @var string
	 */
	protected $emailConfirmationErrorMessage;

	/**
	 * Customer's E-Mail address already exists error message.
	 * @var string
	 */
	protected $emailExistsErrorMessage;

	/**
	 * Customer's street error message.
	 * @var string
	 */
	protected $streetErrorMessage;

	/**
	 * Customer's post code error message.
	 * @var string
	 */
	protected $postcodeErrorMessage;

	/**
	 * Customer's city error message.
	 * @var string
	 */
	protected $cityErrorMessage;

	/**
	 * Customer's country error message.
	 * @var string
	 */
	protected $countryErrorMessage;

	/**
	 * Customer's country zone selection error message.
	 * @var string
	 */
	protected $countryZoneSelectionErrorMessage;

	/**
	 * Customer's country tone error message.
	 * @var string
	 */
	protected $countryZoneErrorMessage;

	/**
	 * Customer's telephone number error message.
	 * @var string
	 */
	protected $telephoneNumberErrorMessage;

	/**
	 * Customer's password error message.
	 * @var string
	 */
	protected $passwordErrorMessage;

	/**
	 * Customer's password mismatch error message.
	 * @var string
	 */
	protected $passwordMismatchErrorMessage;

	/**
	 * Customer's input error error message.
	 * @var string
	 */
	protected $invalidInputErrorMessage;
	
	/**
	 * Customer's privacy policy error message.
	 * 
	 * @var string
	 */
	protected $privacyErrorMessage;
	
	/**
	 * Display customer's gender?
	 * @var bool
	 */
	protected $displayGender;

	/**
	 * Display customer's date of birth?
	 * @var bool
	 */
	protected $displayDateOfBirth;

	/**
	 * Display customer's company?
	 * @var bool
	 */
	protected $displayCompany;

	/**
	 * Display customer's country zone?
	 * @var bool
	 */
	protected $displayCountryZone;

	/**
	 * Display customer's telephone number?
	 * @var bool
	 */
	protected $displayTelephone;

	/**
	 * Display customer's fax number?
	 * @var bool
	 */
	protected $displayFax;

	/**
	 * Display customer's suburb?
	 * @var bool
	 */
	protected $displaySuburb;

	/**
	 * Customer's first name maximum length.
	 * @var int
	 */
	protected $firstnameMaxLength;

	/**
	 * Customer's last name maximum length.
	 * @var int
	 */
	protected $lastnameMaxLength;

	/**
	 * Customer's company maximum length.
	 * @var int
	 */
	protected $companyMaxLength;

	/**
	 * Customer's VAT number maximum length.
	 * @var int
	 */
	protected $vatNumberMaxLength;

	/**
	 * Customer's street maximum length.
	 * @var int
	 */
	protected $streetMaxLength;

	/**
	 * Customer's post code maximum length.
	 * @var int
	 */
	protected $postcodeMaxLength;

	/**
	 * Customer's city maximum length.
	 * @var int
	 */
	protected $cityMaxLength;

	/**
	 * Customer's country zone maximum length.
	 * @var int
	 */
	protected $countryZoneMaxLength;

	/**
	 * Customer's suburb maximum length.
	 * @var int
	 */
	protected $suburbMaxLength;

	/**
	 * Customer's call number maximum length.
	 * @var int
	 */
	protected $callNumberMaxLength;
	
	/**
	 * Status if customer has to accept privacy policy.
	 * @var bool
	 */
	protected $acceptPrivacy;

	/**
	 * @var bool
	 */
	protected $namesOptional;

	/**
	 * Constructor of the class CustomerInputValidatorSettings.
	 *
	 * Set minimum length values and error messages texts from constants.
	 * Set maximum length values from DB field length.
	 */
	public function __construct()
	{
		$this->firstnameMinLength       = (int)ENTRY_FIRST_NAME_MIN_LENGTH;
		$this->lastnameMinLength        = (int)ENTRY_LAST_NAME_MIN_LENGTH;
		$this->dateOfBirthMinLength     = (int)ENTRY_DOB_MIN_LENGTH;
		$this->emailMinLength           = (int)ENTRY_EMAIL_ADDRESS_MIN_LENGTH;
		$this->streetMinLength          = (int)ENTRY_STREET_ADDRESS_MIN_LENGTH;
		$this->companyMinLength         = (int)ENTRY_COMPANY_MIN_LENGTH;
		$this->postcodeMinLength        = (int)ENTRY_POSTCODE_MIN_LENGTH;
		$this->cityMinLength            = (int)ENTRY_CITY_MIN_LENGTH;
		$this->countryZoneMinLength     = (int)ENTRY_STATE_MIN_LENGTH;
		$this->telephoneNumberMinLength = (int)ENTRY_TELEPHONE_MIN_LENGTH;
		$this->passwordMinLength        = (int)ENTRY_PASSWORD_MIN_LENGTH;

		$this->genderErrorMessage               = ENTRY_GENDER_ERROR;
		$this->firstnameErrorMessage            = sprintf(ENTRY_FIRST_NAME_ERROR, ENTRY_FIRST_NAME_MIN_LENGTH);
		$this->lastnameErrorMessage             = sprintf(ENTRY_LAST_NAME_ERROR, ENTRY_LAST_NAME_MIN_LENGTH);
		$this->dateOfBirthErrorMessage          = ENTRY_DATE_OF_BIRTH_ERROR;
		$this->companyErrorMessage              = sprintf(ENTRY_COMPANY_ERROR, ENTRY_COMPANY_MIN_LENGTH);
		$this->vatNumberErrorMessage            = ENTRY_VAT_ERROR;
		$this->emailErrorMessage                = sprintf(ENTRY_EMAIL_ADDRESS_ERROR, ENTRY_EMAIL_ADDRESS_MIN_LENGTH);
		$this->emailAddressCheckErrorMessage    = ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
		$this->emailConfirmationErrorMessage    = ENTRY_EMAIL_ADDRESS_CONFIRM_DIFFERENT_ERROR;
		$this->emailExistsErrorMessage          = ENTRY_EMAIL_ADDRESS_ERROR_EXISTS;
		$this->streetErrorMessage               = sprintf(ENTRY_STREET_ADDRESS_ERROR, ENTRY_STREET_ADDRESS_MIN_LENGTH);
		$this->postcodeErrorMessage             = sprintf(ENTRY_POST_CODE_ERROR, ENTRY_POSTCODE_MIN_LENGTH);
		$this->cityErrorMessage                 = sprintf(ENTRY_CITY_ERROR, ENTRY_CITY_MIN_LENGTH);
		$this->countryErrorMessage              = ENTRY_COUNTRY_ERROR;
		$this->countryZoneErrorMessage          = sprintf(ENTRY_STATE_ERROR, ENTRY_STATE_MIN_LENGTH);
		$this->countryZoneSelectionErrorMessage = ENTRY_STATE_ERROR_SELECT;
		$this->telephoneNumberErrorMessage      = sprintf(ENTRY_TELEPHONE_NUMBER_ERROR, ENTRY_TELEPHONE_MIN_LENGTH);
		$this->passwordErrorMessage             = sprintf(ENTRY_PASSWORD_ERROR, ENTRY_PASSWORD_MIN_LENGTH);
		$this->passwordMismatchErrorMessage     = ENTRY_PASSWORD_ERROR_NOT_MATCHING;
		$this->privacyErrorMessage              = ENTRY_PRIVACY_ERROR;

		$this->invalidInputErrorMessage = ENTRY_MAX_LENGTH_ERROR;

		$this->displayGender      = (ACCOUNT_GENDER === 'true') ? true : false;
		$this->displayDateOfBirth = (ACCOUNT_DOB === 'true') ? true : false;
		$this->displayCompany     = (ACCOUNT_COMPANY === 'true') ? true : false;
		$this->displayCountryZone = (ACCOUNT_STATE === 'true') ? true : false;
		$this->displayTelephone   = (ACCOUNT_TELEPHONE === 'true') ? true : false;
		$this->displayFax         = (ACCOUNT_FAX === 'true') ? true : false;
		$this->displaySuburb      = (ACCOUNT_SUBURB === 'true') ? true : false;

		$this->namesOptional = (ACCOUNT_NAMES_OPTIONAL === 'true') ?: false;
		
		$this->acceptPrivacy = false;
		
		if(gm_get_conf('GM_SHOW_PRIVACY_REGISTRATION') === '1'
		   && gm_get_conf('PRIVACY_CHECKBOX_REGISTRATION') === '1')
		{
			$this->acceptPrivacy = true;
		}
		
		$this->firstnameMaxLength   = 64;
		$this->lastnameMaxLength    = 64;
		$this->companyMaxLength     = 255;
		$this->vatNumberMaxLength   = 20;
		$this->streetMaxLength      = 64;
		$this->postcodeMaxLength    = 10;
		$this->cityMaxLength        = 32;
		$this->countryZoneMaxLength = 32;
		$this->suburbMaxLength      = 32;
		$this->callNumberMaxLength  = 32;
	}


	/**
	 * Returns a city error message.
	 * @return string City error message.
	 */
	public function getCityErrorMessage()
	{
		return $this->cityErrorMessage;
	}


	/**
	 * Returns the minimum required city character length.
	 * @return int City Minimum required city character length.
	 */
	public function getCityMinLength()
	{
		return $this->cityMinLength;
	}


	/**
	 * Returns a company error message.
	 * @return string Company error message.
	 */
	public function getCompanyErrorMessage()
	{
		return $this->companyErrorMessage;
	}


	/**
	 * Returns the minimum required company character length.
	 * @return int Minimum required company character length.
	 */
	public function getCompanyMinLength()
	{
		return $this->companyMinLength;
	}


	/**
	 * Returns a country error message.
	 * @return string Country error message.
	 */
	public function getCountryErrorMessage()
	{
		return $this->countryErrorMessage;
	}


	/**
	 * Returns a country zone error message.
	 * @return string Country zone error message.
	 */
	public function getCountryZoneErrorMessage()
	{
		return $this->countryZoneErrorMessage;
	}


	/**
	 * Returns the minimum required country zone character length.
	 * @return int Minimum required country zone character length.
	 */
	public function getCountryZoneMinLength()
	{
		return $this->countryZoneMinLength;
	}


	/**
	 * Returns a country zone selection error message.
	 * @return string Country zone selection error message.
	 */
	public function getCountryZoneSelectionErrorMessage()
	{
		return $this->countryZoneSelectionErrorMessage;
	}


	/**
	 * Returns a date of birth error message.
	 * @return string Date of birth error message.
	 */
	public function getDateOfBirthErrorMessage()
	{
		return $this->dateOfBirthErrorMessage;
	}


	/**
	 * Returns the minimum required date of birth character length.
	 * @return int Minimum required date of birth character length.
	 */
	public function getDateOfBirthMinLength()
	{
		return $this->dateOfBirthMinLength;
	}


	/**
	 * Returns an email address check error message.
	 * @return string Email address check error message.
	 */
	public function getEmailAddressCheckErrorMessage()
	{
		return $this->emailAddressCheckErrorMessage;
	}


	/**
	 * Returns an email confirmation error message.
	 * @return string Email confirmation error message.
	 */
	public function getEmailConfirmationErrorMessage()
	{
		return $this->emailConfirmationErrorMessage;
	}


	/**
	 * Returns an email error message.
	 * @return string Email error message.
	 */
	public function getEmailErrorMessage()
	{
		return $this->emailErrorMessage;
	}


	/**
	 * Returns an email exists error message.
	 * @return string Email exists error message.
	 */
	public function getEmailExistsErrorMessage()
	{
		return $this->emailExistsErrorMessage;
	}


	/**
	 * Returns the minimum required email character length.
	 * @return int Minimum required email character length.
	 */
	public function getEmailMinLength()
	{
		return $this->emailMinLength;
	}


	/**
	 * Returns a first name error message.
	 * @return string First name error message.
	 */
	public function getFirstnameErrorMessage()
	{
		return $this->firstnameErrorMessage;
	}


	/**
	 * Returns the minimum required first name character length.
	 * @return int Minimum required first name character length.
	 */
	public function getFirstnameMinLength()
	{
		return $this->firstnameMinLength;
	}


	/**
	 * Returns a gender error message.
	 * @return string Gender error message.
	 */
	public function getGenderErrorMessage()
	{
		return $this->genderErrorMessage;
	}


	/**
	 * Returns a last name error message.
	 * @return string Last name error message.
	 */
	public function getLastnameErrorMessage()
	{
		return $this->lastnameErrorMessage;
	}


	/**
	 * Returns the minimum required last name character length.
	 * @return int Minimum required last name character length.
	 */
	public function getLastnameMinLength()
	{
		return $this->lastnameMinLength;
	}


	/**
	 * Returns a password error message.
	 * @return string Password error message.
	 */
	public function getPasswordErrorMessage()
	{
		return $this->passwordErrorMessage;
	}


	/**
	 * Returns the minimum required password character length.
	 * @return int Minimum required password character length.
	 */
	public function getPasswordMinLength()
	{
		return $this->passwordMinLength;
	}


	/**
	 * Returns a password mismatch error message.
	 * @return string Password mismatch error message.
	 */
	public function getPasswordMismatchErrorMessage()
	{
		return $this->passwordMismatchErrorMessage;
	}


	/**
	 * Returns a post code error message.
	 * @return string Post code error message.
	 */
	public function getPostcodeErrorMessage()
	{
		return $this->postcodeErrorMessage;
	}


	/**
	 * Returns the minimum required post code character length.
	 * @return int Minimum required post code character length.
	 */
	public function getPostcodeMinLength()
	{
		return $this->postcodeMinLength;
	}


	/**
	 * Returns a street error message.
	 * @return string Street error message.
	 */
	public function getStreetErrorMessage()
	{
		return $this->streetErrorMessage;
	}


	/**
	 * Returns the minimum required street character length.
	 * @return int Minimum required street character length.
	 */
	public function getStreetMinLength()
	{
		return $this->streetMinLength;
	}


	/**
	 * Returns a telephone number error message.
	 * @return string Telephone number error message.
	 */
	public function getTelephoneNumberErrorMessage()
	{
		return $this->telephoneNumberErrorMessage;
	}


	/**
	 * Returns the minimum required telephone number character length.
	 * @return int Minimum required telephone number character length.
	 */
	public function getTelephoneNumberMinLength()
	{
		return $this->telephoneNumberMinLength;
	}


	/**
	 * Returns a VAT number error message.
	 * @return string VAT number error message.
	 */
	public function getVatNumberErrorMessage()
	{
		return $this->vatNumberErrorMessage;
	}


	/**
	 * Retrieves state value of company displaying.
	 * @return bool Display company?
	 */
	public function getDisplayCompany()
	{
		return $this->displayCompany;
	}


	/**
	 * Retrieves state value of country displaying.
	 * @return bool Display country?
	 */
	public function getDisplayCountryZone()
	{
		return $this->displayCountryZone;
	}


	/**
	 * Retrieves state value of date of birth displaying.
	 * @return bool Display date of birth?
	 */
	public function getDisplayDateOfBirth()
	{
		return $this->displayDateOfBirth;
	}


	/**
	 * Retrieves state value of gender displaying.
	 * @return bool Display gender?
	 */
	public function getDisplayGender()
	{
		return $this->displayGender;
	}


	/**
	 * Retrieves state value of telephone number displaying
	 * @return bool Display telephone number?
	 */
	public function getDisplayTelephone()
	{
		return $this->displayTelephone;
	}


	/**
	 * Retrieves state value of fax displaying
	 * @return bool Display fax?
	 */
	public function getDisplayFax()
	{
		return $this->displayFax;
	}


	/**
	 * Retrieves state value of suburb displaying
	 * @return bool Display suburb?
	 */
	public function getDisplaySuburb()
	{
		return $this->displaySuburb;
	}


	/**
	 * Returns an invalid input error message.
	 * @return string Invalid input error message.
	 */
	public function getInvalidInputErrorMessage()
	{
		return $this->invalidInputErrorMessage;
	}


	/**
	 * Returns the maximum required first name character length.
	 * @return int Maximum required first name character length.
	 */
	public function getFirstnameMaxLength()
	{
		return $this->firstnameMaxLength;
	}


	/**
	 * Returns the maximum required last name character length.
	 * @return int Maximum required last name character length.
	 */
	public function getLastnameMaxLength()
	{
		return $this->lastnameMaxLength;
	}


	/**
	 * Returns the maximum required company character length.
	 * @return int Maximum required company character length.
	 */
	public function getCompanyMaxLength()
	{
		return $this->companyMaxLength;
	}


	/**
	 * Returns the maximum required VAT number character length.
	 * @return int Maximum required VAT number character length.
	 */
	public function getVatNumberMaxLength()
	{
		return $this->vatNumberMaxLength;
	}


	/**
	 * Returns the maximum required street character length.
	 * @return int Maximum required street character length.
	 */
	public function getStreetMaxLength()
	{
		return $this->streetMaxLength;
	}


	/**
	 * Returns the maximum required post code character length.
	 * @return int Maximum required post code character length.
	 */
	public function getPostcodeMaxLength()
	{
		return $this->postcodeMaxLength;
	}


	/**
	 * Returns the maximum required city character length.
	 * @return int Maximum required city character length.
	 */
	public function getCityMaxLength()
	{
		return $this->cityMaxLength;
	}


	/**
	 * Returns the maximum required country zone character length.
	 * @return int Maximum required country zone character length.
	 */
	public function getCountryZoneMaxLength()
	{
		return $this->countryZoneMaxLength;
	}


	/**
	 * Returns the maximum required suburb character length.
	 * @return int Maximum required suburb character length.
	 */
	public function getSuburbMaxLength()
	{
		return $this->suburbMaxLength;
	}


	/**
	 * Returns the maximum required call number character length.
	 * @return int Maximum required call number character length.
	 */
	public function getCallNumberMaxLength()
	{
		return $this->callNumberMaxLength;
	}
	
	
	/**
	 * @return bool
	 */
	public function isNamesOptional()
	{
		return $this->namesOptional;
	}
	
	
	/**
	 * Retrieves state value of displaying privacy checkbox
	 * @return bool Display privacy checkbox?
	 */
	public function getAcceptPrivacy()
	{
		return $this->acceptPrivacy;
	}
	
	
	/**
	 * Returns a privacy not accepted error message.
	 * @return string Privacy not accepted error message.
	 */
	public function getPrivacyErrorMessage()
	{
		return $this->privacyErrorMessage;
	}
} 