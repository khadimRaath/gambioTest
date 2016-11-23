<?php
/* --------------------------------------------------------------
   CreateAccountProcess.inc.php 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCreateAccountProcess');


/**
 * Class CreateAccountProcess
 * 
 * @category System
 * @package Extensions
 * @subpackage Customers
 */
class CreateAccountProcess extends AbstractCreateAccountProcess
{
	/**
	 * @var string $giftCode
	 */
	protected $giftCode = '';


	protected function _saveRegistree()
	{
		$this->_prepareCustomerArray();
		$addressBlock = $this->_createAddressBlock();
		$customer     = $this->customerWriteService->createNewRegistree(MainFactory::create('CustomerEmail',
		                                                                                    $this->customerCollection->getValue('email_address')),
		                                                                MainFactory::create('CustomerPassword',
		                                                                                    $this->customerCollection->getValue('password')),
		                                                                MainFactory::create('CustomerDateOfBirth',
		                                                                                    $this->_formatDateOfBirth($this->customerCollection->getValue('dob'))),
		                                                                MainFactory::create('CustomerVatNumber',
		                                                                                    $this->customerCollection->getValue('vat')),
		                                                                MainFactory::create('CustomerCallNumber',
		                                                                                    $this->customerCollection->getValue('telephone')),
		                                                                MainFactory::create('CustomerCallNumber',
		                                                                                    $this->customerCollection->getValue('fax')),
		                                                                $addressBlock,
		                                                                $this->customerCollection->getValue('addon_values'));

		$this->customerCollection->setValue('customer_id', $customer->getId());
		$this->customerCollection->setValue('default_address_id', $customer->getDefaultAddress()->getId());
		$this->customerCollection->setValue('zone_id', $customer->getDefaultAddress()->getCountryZone()->getId());
		$this->customerCollection->setValue('account_type', '0');
	}


	protected function _saveGuest()
	{
		$this->_prepareCustomerArray();
		$addressBlock = $this->_createAddressBlock();
		$customer     = $this->customerWriteService->createNewGuest(MainFactory::create('CustomerEmail',
		                                                                                $this->customerCollection->getValue('email_address')),
		                                                            MainFactory::create('CustomerDateOfBirth',
		                                                                                $this->_formatDateOfBirth($this->customerCollection->getValue('dob'))),
		                                                            MainFactory::create('CustomerVatNumber',
		                                                                                $this->customerCollection->getValue('vat')),
		                                                            MainFactory::create('CustomerCallNumber',
		                                                                                $this->customerCollection->getValue('telephone')),
		                                                            MainFactory::create('CustomerCallNumber',
		                                                                                $this->customerCollection->getValue('fax')),
		                                                            $addressBlock,
		                                                            $this->customerCollection->getValue('addon_values'));

		$this->customerCollection->setValue('customer_id', $customer->getId());
		$this->customerCollection->setValue('default_address_id', $customer->getDefaultAddress()->getId());
		$this->customerCollection->setValue('zone_id', $customer->getDefaultAddress()->getCountryZone()->getId());
		$this->customerCollection->setValue('account_type', '1');
	}


	protected function _prepareCustomerArray()
	{
		$countryZones = $this->countryService->findCountryZonesByCountryId(new IdType(
		                                                                                       $this->customerCollection->getValue('country')));

		$this->customerCollection->setValue('entry_state_has_zones', false);

		if(!empty($countryZones))
		{
			$this->customerCollection->setValue('entry_state_has_zones', true);
		}

		$zonesArray = array();

		/* @var CustomerCountryZone $countryZone */
		foreach($countryZones as $countryZone)
		{
			if($countryZone->getName() === $this->customerCollection->getValue('state'))
			{
				$this->customerCollection->setValue('state', $countryZone->getId());
			}

			$zonesArray[] = array(
				'id'   => $countryZone->getId(),
				'text' => $countryZone->getName()
			);
		}

		if(!empty($zonesArray))
		{
			$this->customerCollection->setValue('zones_array', $zonesArray);
		}
	}


	/**
	 * @throws InvalidCustomerDataException
	 */
	protected function _validateRegistree()
	{
		/** @var CustomerRegistrationInputValidatorService $inputValidatorService */
		$inputValidatorService = StaticGXCoreLoader::getService('RegistrationInputValidator');

		$inputValidatorService->validateCustomerDataByArray($this->customerCollection->getArray());

		if($inputValidatorService->getErrorStatus())
		{
			$exception = MainFactory::create('InvalidCustomerDataException', 'customer data is not valid');
			$exception->setErrorMessageCollection($inputValidatorService->getErrorMessageCollection());

			throw $exception;
		}
	}


	/**
	 * @throws InvalidCustomerDataException
	 */
	protected function _validateGuest()
	{
		/** @var CustomerRegistrationInputValidatorService $inputValidatorService */
		$inputValidatorService = StaticGXCoreLoader::getService('RegistrationInputValidator');

		$inputValidatorService->validateGuestDataByArray($this->customerCollection->getArray());

		if($inputValidatorService->getErrorStatus())
		{
			$exception = MainFactory::create('InvalidCustomerDataException', 'customer data is not valid');
			$exception->setErrorMessageCollection($inputValidatorService->getErrorMessageCollection());

			throw $exception;
		}
	}


	/**
	 * @return AddressBlock
	 */
	protected function _createAddressBlock()
	{
		if(ACCOUNT_STATE == 'true')
		{
			if(is_numeric($this->customerCollection->getValue('state')))
			{
				$countryZone = $this->countryService->getCountryZoneById(new IdType(
				                                                                             $this->customerCollection->getValue('state')));
			}
			else
			{
				$country = $this->countryService->getCountryById(new IdType(
				                                                                     $this->customerCollection->getValue('country')));

				if($this->countryService->countryHasCountryZones($country))
				{
					$countryZone = $this->countryService->getCountryZoneByNameAndCountry($this->customerCollection->getValue('state'),
					                                                                     $country);
				}
				else
				{
					$countryZone = $this->countryService->getUnknownCountryZoneByName($this->customerCollection->getValue('state'));
				}
			}
		}
		else
		{
			$countryZone = MainFactory::create('CustomerCountryZone', new IdType(0),
			                                   MainFactory::create('CustomerCountryZoneName', ''),
			                                   MainFactory::create('CustomerCountryZoneIsoCode', ''));
		}

		$addressBlock = MainFactory::create('AddressBlock', MainFactory::create('CustomerGender',
		                                                                        $this->customerCollection->getValue('gender') ?: ''),
		                                    MainFactory::create('CustomerFirstname',
		                                                        $this->customerCollection->getValue('firstname')),
		                                    MainFactory::create('CustomerLastname',
		                                                        $this->customerCollection->getValue('lastname')),
		                                    MainFactory::create('CustomerCompany',
		                                                        $this->customerCollection->getValue('company')),
		                                    MainFactory::create('CustomerB2BStatus',
		                                                        (boolean)(int)$this->customerCollection->getValue('b2b_status')),
		                                    MainFactory::create('CustomerStreet',
		                                                        $this->customerCollection->getValue('street_address')),
		                                    MainFactory::create('CustomerHouseNumber',
		                                                        $this->customerCollection->getValue('house_number')),
		                                    MainFactory::create('CustomerAdditionalAddressInfo',
		                                                        $this->customerCollection->getValue('additional_address_info')),
		                                    MainFactory::create('CustomerSuburb',
		                                                        $this->customerCollection->getValue('suburb')),
		                                    MainFactory::create('CustomerPostcode',
		                                                        $this->customerCollection->getValue('postcode')),
		                                    MainFactory::create('CustomerCity',
		                                                        $this->customerCollection->getValue('city')),
		                                    $this->countryService->getCountryById(new IdType(
		                                                                                              $this->customerCollection->getValue('country'))),
		                                    $countryZone);

		return $addressBlock;
	}


	protected function _proceedTracking()
	{
		xtc_write_user_info($this->customerCollection->getValue('customer_id'));

		if(isset($_SESSION['tracking']['refID']))
		{
			$query = "SELECT * FROM " . TABLE_CAMPAIGNS . "
						WHERE campaigns_refID = '" . xtc_db_input($_SESSION['tracking']['refID']) . "'";

			$result = xtc_db_query($query);
			if(xtc_db_num_rows($result) > 0)
			{
				$campaign = xtc_db_fetch_array($result);
				$refID    = $campaign['campaigns_id'];

				xtc_db_perform(TABLE_CUSTOMERS, array('refferers_id' => $refID), 'update',
				               'customers_id = ' . (int)$this->customerCollection->getValue('customer_id'));

				$leads = $campaign['campaigns_leads'] + 1;
				xtc_db_perform(TABLE_CAMPAIGNS, array('campaigns_leads' => $leads), 'update',
				               'campaigns_id = ' . $refID);
			}
		}
	}


	protected function _proceedVoucher()
	{
		if(ACTIVATE_GIFT_SYSTEM == 'true')
		{
			if(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0)
			{
				$this->giftCode = create_coupon_code();

				$couponId = $this->_saveGift($this->_buildGiftSqlDataArray());
				$this->_saveGiftEmailTrack($this->_buildGiftEmailTrackSqlDataArray($couponId));
			}

			if(NEW_SIGNUP_DISCOUNT_COUPON !== '')
			{
				$this->_saveCouponEmailTrack($this->_buildCouponEmailTrackSqlDataArray());
			}
		}
	}


	/**
	 * @return array
	 */
	protected function _buildGiftSqlDataArray()
	{
		$sqlDataArray                  = array();
		$sqlDataArray['coupon_code']   = $this->giftCode;
		$sqlDataArray['coupon_type']   = 'G';
		$sqlDataArray['coupon_amount'] = NEW_SIGNUP_GIFT_VOUCHER_AMOUNT;
		$sqlDataArray['date_created']  = 'now()';

		return $sqlDataArray;
	}


	/**
	 * @param array $sqlDataArray
	 *
	 * @return int
	 */
	protected function _saveGift(array $sqlDataArray)
	{
		xtc_db_perform(TABLE_COUPONS, $sqlDataArray);

		return xtc_db_insert_id();
	}


	/**
	 * @param int $p_couponId
	 *
	 * @return array
	 */
	protected function _buildGiftEmailTrackSqlDataArray($p_couponId)
	{
		$sqlDataArray = array();

		$sqlDataArray['coupon_id']        = (int)$p_couponId;
		$sqlDataArray['customer_id_sent'] = 0;
		$sqlDataArray['sent_firstname']   = 'Admin';
		$sqlDataArray['emailed_to']       = $this->customerCollection->getValue('email_address');
		$sqlDataArray['date_sent']        = 'now()';

		return $sqlDataArray;
	}


	/**
	 * @param array $sqlDataArray
	 */
	protected function _saveGiftEmailTrack(array $sqlDataArray)
	{
		xtc_db_perform(TABLE_COUPON_EMAIL_TRACK, $sqlDataArray);
	}


	/**
	 * @return array
	 */
	protected function _buildCouponEmailTrackSqlDataArray()
	{
		$sqlDataArray = array();

		$couponData = $this->_getSignUpCouponCollection();

		if(!$couponData->isEmpty())
		{
			$sqlDataArray['coupon_id']        = $couponData->getValue('coupon_id');
			$sqlDataArray['customer_id_sent'] = 0;
			$sqlDataArray['sent_firstname']   = 'Admin';
			$sqlDataArray['emailed_to']       = $this->customerCollection->getValue('email_address');
			$sqlDataArray['date_sent']        = 'now()';
		}

		return $sqlDataArray;
	}


	/**
	 * @param array $sqlDataArray
	 */
	protected function _saveCouponEmailTrack(array $sqlDataArray)
	{
		if(!empty($sqlDataArray))
		{
			xtc_db_perform(TABLE_COUPON_EMAIL_TRACK, $sqlDataArray);
		}
	}


	protected function _login()
	{
		if(SESSION_RECREATE == 'True')
		{
			xtc_session_recreate();
		}

		$_SESSION['customer_id']                 = $this->customerCollection->getValue('customer_id');
		$_SESSION['customer_first_name']         = $this->customerCollection->getValue('firstname');
		$_SESSION['customer_last_name']          = $this->customerCollection->getValue('lastname');
		$_SESSION['customer_default_address_id'] = $this->customerCollection->getValue('default_address_id');
		$_SESSION['customer_country_id']         = $this->customerCollection->getValue('country');
		$_SESSION['customer_zone_id']            = $this->customerCollection->getValue('zone_id');
		$_SESSION['customer_vat_id']             = $this->customerCollection->getValue('vat');
		$_SESSION['account_type']                = $this->customerCollection->getValue('account_type');
		
		// write customers status in session
		require DIR_WS_INCLUDES . 'write_customers_status.php';
		
		// restore cart contents
		$_SESSION['cart']->restore_contents();
	}


	/**
	 * @param GMLogoManager $logoManager
	 */
	protected function _proceedMail(GMLogoManager $logoManager)
	{
		$this->_sendMail($this->_buildMailDataArray($logoManager));
	}


	/**
	 * @param GMLogoManager $logoManager
	 *
	 * @return array
	 */
	protected function _buildMailDataArray(GMLogoManager $logoManager)
	{
		$mailDataArray = array();

		// build the message content
		$name = $this->customerCollection->getValue('firstname') . ' '
		        . $this->customerCollection->getValue('lastname');

		// load data into array
		$content = array(
			'MAIL_NAME'          => htmlspecialchars_wrapper($name),
			'MAIL_REPLY_ADDRESS' => EMAIL_SUPPORT_REPLY_ADDRESS,
			'MAIL_GENDER'        => htmlspecialchars_wrapper($this->customerCollection->getValue('gender'))
		);

		// assign data to smarty
		$mailDataArray['language']     = $_SESSION['language'];
		$mailDataArray['logo_path']    = HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/';
		$mailDataArray['content']      = $content;
		$mailDataArray['GENDER']       = $this->customerCollection->getValue('gender');
		$mailDataArray['NAME']         = $name;
		$mailDataArray['mail_address'] = $this->customerCollection->getValue('email_address');

		if($logoManager->logo_use == '1')
		{
			$mailDataArray['gm_logo_mail'] = $logoManager->get_logo();
		}

		if($this->giftCode !== '')
		{
			$xtcPrice                      = new xtcPrice($_SESSION['currency'],
			                                              $_SESSION['customers_status']['customers_status_id']);
			$mailDataArray['SEND_GIFT']    = 'true';
			$mailDataArray['GIFT_AMMOUNT'] = $xtcPrice->xtcFormat(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT, true);
			$mailDataArray['GIFT_CODE']    = $this->giftCode;
			$mailDataArray['GIFT_LINK']    = xtc_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $this->giftCode, 'NONSSL',
			                                               false);
		}

		$couponCollection = $this->_getSignUpCouponCollection();

		if(!$couponCollection->isEmpty())
		{
			$mailDataArray['SEND_COUPON'] = 'true';
			$mailDataArray['COUPON_DESC'] = $couponCollection->getValue('coupon_description');
			$mailDataArray['COUPON_CODE'] = $couponCollection->getValue('coupon_code');
		}

		if(defined('EMAIL_SIGNATURE'))
		{
			$mailDataArray['EMAIL_SIGNATURE_HTML'] = nl2br(EMAIL_SIGNATURE);
			$mailDataArray['EMAIL_SIGNATURE_TEXT'] = EMAIL_SIGNATURE;
		}

		return $mailDataArray;
	}


	/**
	 * @param array $mailDataArray
	 */
	protected function _sendMail(array $mailDataArray)
	{
		$smarty = new Smarty;

		if(is_array($mailDataArray) && count($mailDataArray) > 0)
		{
			foreach($mailDataArray as $key => $content)
			{
				$smarty->assign($key, $content);
			}
		}

		$smarty->caching = 0;
		$htmlMail        = fetch_email_template($smarty, 'create_account_mail');

		if(ACTIVATE_GIFT_SYSTEM == 'true' && NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0)
		{
			$smarty->assign('GIFT_LINK', str_replace('&amp;', '&', $mailDataArray['GIFT_LINK']));
		}

		$smarty->caching = 0;
		$txtMail         = fetch_email_template($smarty, 'create_account_mail', 'txt');

		if(SEND_EMAILS == 'true')
		{
			xtc_php_mail(EMAIL_SUPPORT_ADDRESS, EMAIL_SUPPORT_NAME, $mailDataArray['mail_address'],
			             $mailDataArray['NAME'], EMAIL_SUPPORT_FORWARDING_STRING, EMAIL_SUPPORT_REPLY_ADDRESS,
			             EMAIL_SUPPORT_REPLY_ADDRESS_NAME, '', '', EMAIL_SUPPORT_SUBJECT, $htmlMail, $txtMail);
		}
	}


	/**
	 * @return KeyValueCollection
	 */
	protected function _getSignUpCouponCollection()
	{
		$couponArray = array();

		$query = 'SELECT
						c.coupon_id,
						c.coupon_code,
						d.coupon_description 
					FROM 
						' . TABLE_COUPONS . ' c,
						' . TABLE_COUPONS_DESCRIPTION . ' d
					WHERE 
						c.coupon_code = "' . xtc_db_input(NEW_SIGNUP_DISCOUNT_COUPON) . '" AND
						c.coupon_id = d.coupon_id AND
						d.language_id = ' . (int)$_SESSION['languages_id'] . ' 
					LIMIT 1';

		$result = xtc_db_query($query);

		if(xtc_db_num_rows($result))
		{
			$couponArray = xtc_db_fetch_array($result);
		}

		return MainFactory::create('KeyValueCollection', $couponArray);
	}


	/**
	 * @param string $p_dateOfBirth
	 *
	 * @return string YYYY-MM-DD or ''
	 */
	protected function _formatDateOfBirth($p_dateOfBirth)
	{
		$dateOfBirth = xtc_date_raw($p_dateOfBirth);
		
		if(strlen($dateOfBirth) === 8)
		{
			$dateOfBirth = substr($dateOfBirth, 0, 4) . '-' . substr($dateOfBirth, 4, 2) . '-' . substr($dateOfBirth, 6, 2);
		}
		
		return $dateOfBirth;
	}
}