<?php
/* --------------------------------------------------------------
   CustomerInputValidator.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidatorInterface');


/**
 * Class CustomerInputValidator
 *
 * Validator class that checks the entered user data.
 *
 * @category   System
 * @package    Customer
 * @subpackage Validation
 * @implements CustomerInputValidatorInterface
 */
class CustomerInputValidator implements CustomerInputValidatorInterface
{
	/**
	 * Customer country repository.
	 * @var CustomerCountryRepositoryInterface
	 */
	protected $customerCountryRepository;

	/**
	 * Customer country zone repository.
	 * @var CustomerCountryZoneRepositoryInterface
	 */
	protected $customerCountryZoneRepository;

	/**
	 * Customer service.
	 * @var CustomerServiceInterface
	 */
	protected $customerService;

	/**
	 * Country service.
	 * @var CountryServiceInterface
	 */
	protected $countryService;

	/**
	 * Customer input validator settings.
	 * @var CustomerInputValidatorSettingsInterface
	 */
	protected $settings;

	/**
	 * VAT number validator.
	 * @var VatNumberValidatorInterface
	 */
	protected $vatNumberValidator;

	/**
	 * Error message collection.
	 * @var EditableKeyValueCollection
	 */
	protected $errorMessageCollection;

	/**
	 * Error status.
	 * @var bool
	 */
	protected $errorStatus = false;


	/**
	 * Constructor of the class CustomerInputValidator.
	 *
	 * @param CustomerServiceInterface                $customerService                Customer service.
	 * @param CountryServiceInterface                 $countryService                 Country service.
	 * @param CustomerInputValidatorSettingsInterface $customerInputValidatorSettings Customer input validator settings.
	 * @param CustomerCountryRepositoryInterface      $customerCountryRepository      Customer country repository.
	 * @param CustomerCountryZoneRepositoryInterface  $customerCountryZoneRepository  Customer country zone repository.
	 * @param VatNumberValidatorInterface             $vatNumberValidator             VAT number validator.
	 */
	public function __construct(CustomerServiceInterface $customerService,
	                            CountryServiceInterface $countryService,
	                            CustomerInputValidatorSettingsInterface $customerInputValidatorSettings,
	                            CustomerCountryRepositoryInterface $customerCountryRepository,
	                            CustomerCountryZoneRepositoryInterface $customerCountryZoneRepository,
	                            VatNumberValidatorInterface $vatNumberValidator)
	{
		$this->customerService               = $customerService;
		$this->countryService                = $countryService;
		$this->settings                      = $customerInputValidatorSettings;
		$this->customerCountryRepository     = $customerCountryRepository;
		$this->customerCountryZoneRepository = $customerCountryZoneRepository;
		$this->vatNumberValidator            = $vatNumberValidator;
		
		$this->errorMessageCollection = MainFactory::create('EditableKeyValueCollection', array());
	}
	

	/**
	 * Checks if the entered customer's gender is valid.
	 *
	 * @param string $p_gender Customer's gender.
	 *
	 * @return bool Is valid?
	 */
	public function validateGender($p_gender)
	{
		if(!$this->settings->getDisplayGender())
		{
			return true;
		}
		if((string)$p_gender != 'm' && (string)$p_gender != 'f')
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_gender', $this->settings->getGenderErrorMessage());

			return false;
		}

		return true;
	}
	

	/**
	 * Checks if the entered customer's first name is valid.
	 *
	 * @param string $p_firstname Customer's first name.
	 *
	 * @return bool Is valid?
	 */
	public function validateFirstname($p_firstname)
	{
		if(strlen_wrapper(trim((string)$p_firstname)) > $this->settings->getFirstnameMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_first_name', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper((string)$p_firstname) < $this->settings->getFirstnameMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_first_name', $this->settings->getFirstnameErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered customer's last name is valid.
	 *
	 * @param string $p_lastname Customer's last name.
	 *
	 * @return bool Is valid?
	 */
	public function validateLastname($p_lastname)
	{
		if(strlen_wrapper(trim((string)$p_lastname)) > $this->settings->getLastnameMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_last_name', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper((string)$p_lastname) < $this->settings->getLastnameMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_last_name', $this->settings->getLastnameErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered customer's date of birth is valid.
	 * Valid format is: dd.mm.yyyy
	 *
	 * @param string $p_dateOfBirth Customer's date of birth.
	 *
	 * @return bool Is valid?
	 */
	public function validateDateOfBirth($p_dateOfBirth)
	{
		// @todo DisplayDateOfBirth setting is blocking the unit tests of the class.
		if(!$this->settings->getDisplayDateOfBirth())
		{
			return true;
		}
		$dateOfBirth = (string)$p_dateOfBirth;
		$minLength   = $this->settings->getDateOfBirthMinLength();
		
		if($minLength > 0 || ($dateOfBirth != '' && $minLength === 0))
		{
			if(xtc_date_raw($dateOfBirth) === '' || !preg_match('/^[0-9]{2}[\.\/]{1}[0-9]{2}[\.\/]{1}[0-9]{4}$/', $dateOfBirth)
			   || checkdate(substr(xtc_date_raw($dateOfBirth), 4, 2), substr(xtc_date_raw($dateOfBirth), 6, 2),
			                substr(xtc_date_raw($dateOfBirth), 0, 4)) == false
			)
			{
				$this->errorStatus = true;
				$this->errorMessageCollection->setValue('error_birth_day',
				                                        $this->settings->getDateOfBirthErrorMessage());

				return false;
			}
		}

		return true;
	}


	/**
	 * Checks if the entered customer's company is valid.
	 *
	 * @param string $p_company Customer's company.
	 *
	 * @return bool Is valid?
	 */
	public function validateCompany($p_company)
	{
		if(!$this->settings->getDisplayCompany())
		{
			return true;
		}
		$company = (string)$p_company;

		if(strlen_wrapper(trim($company)) > $this->settings->getCompanyMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_company', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper($company) > 0 && strlen_wrapper($company) < $this->settings->getCompanyMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_company', $this->settings->getCompanyErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered parameters are in a valid format.
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return bool Is valid?
	 */
	public function validateVatNumber($p_vatNumber, $p_countryId, $p_isGuest)
	{
		if(strlen_wrapper(trim((string)$p_vatNumber)) > $this->settings->getVatNumberMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_vat', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if($this->vatNumberValidator->getErrorStatus($p_vatNumber, $p_countryId, $p_isGuest))
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_vat', $this->settings->getVatNumberErrorMessage());

			return false;
		}

		return true;
	}
	

	/**
	 * Checks if the entered email and email confirmation are valid.
	 * It will check the minimum length, address syntax, confirmation matching and existence of e-mail address.
	 *
	 * @param string $p_email             Customer's E-Mail address.
	 * @param string $p_emailConfirmation Confirmation E-Mail address.
	 *
	 * @return bool Is valid?
	 */
	public function validateEmailAndConfirmation($p_email, $p_emailConfirmation)
	{
		if($p_email != $p_emailConfirmation)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailConfirmationErrorMessage());

			return false;
		}

		if(!$this->validateEmail($p_email))
		{
			return false;
		}

		if(!$this->validateEmailExists($p_email))
		{
			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered email is valid.
	 *
	 * @param string $p_email Customer's E-Mail address.
	 *
	 * @return bool Is valid?
	 */
	public function validateEmail($p_email)
	{
		if(strlen_wrapper($p_email) < $this->settings->getEmailMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailErrorMessage());

			return false;
		}
		elseif(xtc_validate_email($p_email) == false)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailAddressCheckErrorMessage());

			return false;
		}
		elseif(!filter_var($p_email, FILTER_VALIDATE_EMAIL))
		{
			// @codeCoverageIgnoreStart
			# code coverage ignore this edge case, xtc_validate_email usually recognize invalid email addresses.
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailAddressCheckErrorMessage());

			return false;
			// @codeCoverageIgnoreEnd
		}

		return true;
	}
	
	
	/**
	 * Checks if the entered email already exists.
	 *
	 * @param string            $p_email  Customer's E-Mail address-
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return bool Is valid?
	 */
	public function validateEmailExists($p_email, CustomerInterface $customer = null)
	{
		if($this->customerService->registreeEmailExists(MainFactory::create('CustomerEmail', $p_email))
		   && ($customer === null
		       || $customer->getEmail() != $p_email)
		)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_mail', $this->settings->getEmailExistsErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered street is valid.
	 *
	 * @param string $p_street Customer's street.
	 *
	 * @return bool Is valid?
	 */
	public function validateStreet($p_street)
	{
		if(strlen_wrapper(trim((string)$p_street)) > $this->settings->getStreetMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_street', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper((string)$p_street) < $this->settings->getStreetMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_street', $this->settings->getStreetErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered postcode is valid.
	 *
	 * @param string $p_postcode Customer's post code.
	 *
	 * @return bool Is valid?
	 */
	public function validatePostcode($p_postcode)
	{
		if(strlen_wrapper(trim((string)$p_postcode)) > $this->settings->getPostcodeMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_post_code', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper((string)$p_postcode) < $this->settings->getPostcodeMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_post_code', $this->settings->getPostcodeErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered city is valid.
	 *
	 * @param string $p_city Customer's city.
	 *
	 * @return bool Is valid?
	 */
	public function validateCity($p_city)
	{
		if(strlen_wrapper(trim((string)$p_city)) > $this->settings->getCityMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_city', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper((string)$p_city) < $this->settings->getCityMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_city', $this->settings->getCityErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered country exists.
	 *
	 * @param int $p_countryId Customer's country ID.
	 *
	 * @return bool Is valid?
	 */
	public function validateCountry($p_countryId)
	{
		$country = $this->customerCountryRepository->findById(new IdType($p_countryId));
		
		if($country === null || $country->getStatus() === false)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_country', $this->settings->getCountryErrorMessage());

			return false;
		}

		return true;
	}


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
	public function validateCountryZone($p_countryZone, $p_countryId)
	{
		if(!$this->settings->getDisplayCountryZone())
		{
			return true;
		}

		$country = $this->customerCountryRepository->findById(new IdType($p_countryId));
		
		if($country !== null && $this->countryService->countryHasCountryZones($country))
		{
			if(is_numeric($p_countryZone))
			{
				$countryZone = $this->customerCountryZoneRepository->findById(new IdType($p_countryZone));
			}
			else
			{
				if(strlen_wrapper(trim((string)$p_countryZone)) > $this->settings->getCountryZoneMaxLength())
				{
					$this->errorStatus = true;
					$this->errorMessageCollection->setValue('error_state',
					                                        $this->settings->getInvalidInputErrorMessage());

					return false;
				}
				
				$countryZone = $this->customerCountryZoneRepository->findByNameAndCountry(MainFactory::create('CustomerCountryZoneName',
				                                                                                              $p_countryZone),
				                                                                          $country);
			}
			
			if($countryZone === null
			   || $country->getStatus() === false
			   || !$this->countryService->countryZoneExistsInCountry($countryZone, $country)
			)
			{
				$this->errorStatus = true;
				$this->errorMessageCollection->setValue('error_state',
				                                        $this->settings->getCountryZoneSelectionErrorMessage());

				return false;
			}
		}
		elseif(is_numeric($p_countryZone)
		       || strlen_wrapper((string)$p_countryZone) < $this->settings->getCountryZoneMinLength()
		)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_state', $this->settings->getCountryZoneErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered suburb is valid.
	 *
	 * @param $p_suburb Customer's suburb.
	 *
	 * @return bool Is valid?
	 */
	public function validateSuburb($p_suburb)
	{
		if(!$this->settings->getDisplaySuburb())
		{
			return true;
		}

		if(strlen_wrapper(trim((string)$p_suburb)) > $this->settings->getSuburbMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_suburb', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		return true;
	}


	/**
	 * Checks if the entered telephone number is valid.
	 *
	 * @param string $p_telephoneNumber Customer's telephone number.
	 *
	 * @return bool Is valid?
	 */
	public function validateTelephoneNumber($p_telephoneNumber)
	{
		if(!$this->settings->getDisplayTelephone())
		{
			return true;
		}

		if(strlen_wrapper(trim((string)$p_telephoneNumber)) > $this->settings->getCallNumberMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_tel', $this->settings->getInvalidInputErrorMessage());

			return false;
		}
		
		if(strlen_wrapper((string)$p_telephoneNumber) < $this->settings->getTelephoneNumberMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_tel', $this->settings->getTelephoneNumberErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered fax number is valid.
	 *
	 * @param string $p_faxNumber Customer's fax number.
	 *
	 * @return bool Is valid?
	 */
	public function validateFaxNumber($p_faxNumber)
	{
		if(!$this->settings->getDisplayFax())
		{
			return true;
		}

		if(strlen_wrapper(trim((string)$p_faxNumber)) > $this->settings->getCallNumberMaxLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_fax', $this->settings->getInvalidInputErrorMessage());

			return false;
		}

		return true;
	}


	/**
	 * Checks if the entered password is valid.
	 *
	 * @param string $p_password             Customer's password.
	 * @param string $p_passwordConfirmation Customer's password confirmation.
	 *
	 * @return bool Is valid?
	 */
	public function validatePassword($p_password, $p_passwordConfirmation)
	{
		if(strlen_wrapper($p_password) < $this->settings->getPasswordMinLength())
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_password', $this->settings->getPasswordErrorMessage());

			return false;
		}
		elseif($p_password !== $p_passwordConfirmation)
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_password2',
			                                        $this->settings->getPasswordMismatchErrorMessage());

			return false;
		}

		return true;
	}
	
	
	/**
	 * Checks if the user has accepted the privacy policy.
	 * 
	 * @param string $p_privacyAccepted
	 *
	 * @return bool Is valid?
	 */
	public function validatePrivacy($p_privacyAccepted)
	{
		if(!$this->settings->getAcceptPrivacy())
		{
			return true;
		}
		
		
		if($p_privacyAccepted !== '1')
		{
			$this->errorStatus = true;
			$this->errorMessageCollection->setValue('error_privacy', $this->settings->getPrivacyErrorMessage());
			
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Checks if the entered additional values are valid.
	 *
	 * Overload this method to implement needed validation logic.
	 *
	 * @param KeyValueCollection $addonValues Customer's additional values.
	 *
	 * @return bool Is valid?
	 */
	public function validateAddonValues(KeyValueCollection $addonValues)
	{
		return true;
	}
	

	/**
	 * Returns error messages.
	 * @deprecated Use getErrorMessageCollection() instead
	 * @return array Error messages.
	 */
	public function getErrorMessages()
	{
		return $this->errorMessageCollection->getArray();
	}


	/**
	 * Returns a collection of error messages.
	 *
	 * @return EditableKeyValueCollection Collection of error messages.
	 */
	public function getErrorMessageCollection()
	{
		return $this->errorMessageCollection;
	}
	
	
	/**
	 * Returns the error status.
	 *
	 * @return bool Error status.
	 */
	public function getErrorStatus()
	{
		return $this->errorStatus;
	}
} 