<?php
/* --------------------------------------------------------------
   CustomerInputToCollectionTransformer.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerInputToCollectionTransformer
 * 
 * @category System
 * @package Extensions
 * @subpackage Customers
 */
class CustomerInputToCollectionTransformer
{
	/**
	 * @param array                   $inputArray
	 * @param CountryServiceInterface $countryService
	 *
	 * @return EditableKeyValueCollection
	 */
	public function getGuestCollectionFromInputArray(array $inputArray, CountryServiceInterface $countryService)
	{
		return $this->_getCustomerCollectionFromInputArray($inputArray, $countryService);
	}


	/**
	 * @param array                   $inputArray
	 * @param CountryServiceInterface $countryService
	 *
	 * @return EditableKeyValueCollection
	 */
	public function getRegistreeCollectionFromInputArray(array $inputArray, CountryServiceInterface $countryService)
	{
		$customerCollection = $this->_getCustomerCollectionFromInputArray($inputArray, $countryService);

		$customerCollection->setValue('password', xtc_db_prepare_input($inputArray['password']));
		$customerCollection->setValue('confirmation', xtc_db_prepare_input($inputArray['confirmation']));

		return $customerCollection;
	}


	/**
	 * @param array                   $inputArray
	 * @param CountryServiceInterface $countryService
	 *
	 * @return EditableKeyValueCollection
	 */
	protected function _getCustomerCollectionFromInputArray(array $inputArray, CountryServiceInterface $countryService)
	{
		$customerCollection = MainFactory::create('EditableKeyValueCollection', array());

		$customerCollection->setValue('firstname', xtc_db_prepare_input($inputArray['firstname']));
		$customerCollection->setValue('lastname', xtc_db_prepare_input($inputArray['lastname']));
		$customerCollection->setValue('email_address', xtc_db_prepare_input($inputArray['email_address']));
		$customerCollection->setValue('street_address', xtc_db_prepare_input($inputArray['street_address']));
		
		if(isset($inputArray['house_number']))
		{
			$customerCollection->setValue('house_number',
			                              xtc_db_prepare_input($inputArray['house_number']));
		}
		else
		{
			$customerCollection->setValue('house_number', '');
		}
		
		if(isset($inputArray['additional_address_info']))
		{
			$customerCollection->setValue('additional_address_info',
			                              xtc_db_prepare_input($inputArray['additional_address_info']));
		}
		else
		{
			$customerCollection->setValue('additional_address_info', '');
		}
		
		$customerCollection->setValue('postcode', xtc_db_prepare_input($inputArray['postcode']));
		$customerCollection->setValue('city', xtc_db_prepare_input($inputArray['city']));
		$customerCollection->setValue('country', xtc_db_prepare_input($inputArray['country']));

		if(isset($inputArray['email_address_confirm']))
		{
			$customerCollection->setValue('email_address_confirm',
			                              xtc_db_prepare_input($inputArray['email_address_confirm']));
		}
		else
		{
			$customerCollection->setValue('email_address_confirm', xtc_db_prepare_input($inputArray['email_address']));
		}

		$customerCollection->setValue('gender', '');
		if(ACCOUNT_GENDER === 'true')
		{
			$customerCollection->setValue('gender', xtc_db_prepare_input($inputArray['gender']));
		}

		$customerCollection->setValue('dob', '');
		if(ACCOUNT_DOB === 'true')
		{
			$customerCollection->setValue('dob', xtc_db_prepare_input($inputArray['dob']));
		}

		$customerCollection->setValue('company', '');
		$customerCollection->setValue('vat', '');
		if(ACCOUNT_COMPANY === 'true')
		{
			$customerCollection->setValue('company', xtc_db_prepare_input($inputArray['company']));

			if(ACCOUNT_COMPANY_VAT_CHECK === 'true')
			{
				$customerCollection->setValue('vat', xtc_db_prepare_input($inputArray['vat']));
			}
		}

		$customerCollection->setValue('suburb', '');
		if(ACCOUNT_SUBURB === 'true')
		{
			$customerCollection->setValue('suburb', xtc_db_prepare_input($inputArray['suburb']));
		}

		$customerCollection->setValue('state', '');
		if(ACCOUNT_STATE === 'true')
		{
			$countryZones = $countryService->findCountryZonesByCountryId(new IdType(
			                                                                                 (int)$inputArray['country']));

			if(count($countryZones))
			{
				$customerCollection->setValue('entry_state_has_zones', true);

				$zonesArray = array();

				/**
				 * @var CustomerCountryZone $countryZone
				 */
				foreach($countryZones as $countryZone)
				{
					$zonesArray[] = array('id' => $countryZone->getId(), 'text' => $countryZone->getName());

					if($inputArray['state'] === (string)$countryZone->getName() || (int)$inputArray['state'] === $countryZone->getId())
					{
						$customerCollection->setValue('state', $countryZone->getId());
					}
				}

				$customerCollection->setValue('zones_array', $zonesArray);
			}
			else
			{
				$customerCollection->setValue('state', xtc_db_prepare_input($inputArray['state']));
				$customerCollection->setValue('entry_state_has_zones', false);
			}
		}

		$customerCollection->setValue('telephone', '');
		if(ACCOUNT_TELEPHONE === 'true')
		{
			$customerCollection->setValue('telephone', xtc_db_prepare_input($inputArray['telephone']));
		}

		$customerCollection->setValue('fax', '');
		if(ACCOUNT_FAX === 'true')
		{
			$customerCollection->setValue('fax', xtc_db_prepare_input($inputArray['fax']));
		}

		if(isset($inputArray['newsletter']))
		{
			$customerCollection->setValue('newsletter', (int)$inputArray['newsletter']);
		}
		else
		{
			$customerCollection->setValue('newsletter', 0);
		}

		$customerCollection->setValue('b2b_status', 0);
		if(isset($inputArray['b2b_status']))
		{
			$customerCollection->setValue('b2b_status', (int)$inputArray['b2b_status']);
		}
		elseif(ACCOUNT_DEFAULT_B2B_STATUS === 'true')
		{
			$customerCollection->setValue('b2b_status', 1);
		}
		
		$customerCollection->setValue('addon_values', $this->_getCustomerAddonValuesFromInputArray($inputArray));

		$customerCollection->setValue('privacy_accepted', 
		                              isset($inputArray['privacy_accepted']) ? $inputArray['privacy_accepted'] : '0');
		
		return $customerCollection;
	}
	
	
	/**
	 * Add the addon values to the customer collection.
	 * 
	 * Overload this method to extract your additional customer's data from the given input array ($_POST) to consider
	 * them while validation and registration process.
	 *
	 * @param array $inputArray
	 *
	 * @return EditableKeyValueCollection
	 */
	protected function _getCustomerAddonValuesFromInputArray(array $inputArray)
	{
		return MainFactory::create('EditableKeyValueCollection', array());
	}
}