<?php
/* --------------------------------------------------------------
   VatNumberValidator.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('VatNumberValidatorInterface');

/**
 * Class VatNumberValidator
 *
 * This class provides methods for validating VAT numbers
 *
 * @category   System
 * @package    Customer
 * @subpackage Validation
 * @implements VatNumberValidatorInterface
 */
class VatNumberValidator implements VatNumberValidatorInterface
{
	/**
	 * VAT validation.
	 * @var vat_validation_ORIGIN
	 */
	protected $vatValidation;


	/**
	 * Initialize the VAT number validator.
	 *
	 * @param vat_validation_ORIGIN $vatValidation VAT validation.
	 */
	public function __construct(vat_validation_ORIGIN $vatValidation = null)
	{
		$this->vatValidation = ($vatValidation) ? : new vat_validation();
	}


	/**
	 * Returns the VAT number status code ID.
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return int VAT number status code ID.
	 */
	public function getVatNumberStatusCodeId($p_vatNumber, $p_countryId, $p_isGuest)
	{
		$this->vatValidation->reset($p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$vatInfo = $this->vatValidation->getVatInfo();

		return (int)$vatInfo['vat_id_status'];
	}


	/**
	 * Returns the customer status ID.
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return int Customer status ID.
	 */
	public function getCustomerStatusId($p_vatNumber, $p_countryId, $p_isGuest)
	{
		$this->vatValidation->reset($p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$vatInfo = $this->vatValidation->getVatInfo();

		return (int)$vatInfo['status'];
	}


	/**
	 * Returns the error status
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return bool Error status.
	 */
	public function getErrorStatus($p_vatNumber, $p_countryId, $p_isGuest)
	{
		$this->vatValidation->reset($p_vatNumber, '', '', $p_countryId, (int)$p_isGuest);
		$vatInfo = $this->vatValidation->getVatInfo();

		return $vatInfo['error'];
	}


	/**
	 * Writes the validation results to cache.
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * TODO Write validation results to cache.
	 */
	protected function _putValidationCache($p_vatNumber, $p_countryId, $p_isGuest)
	{
		/*
		$coo_vat_validation = new vat_validation($vatNumber, '', '', $country->getId(), $p_guest);

		$customerStatus = $coo_vat_validation->vat_info['status'];
		$numberStatus = $coo_vat_validation->vat_info['vat_id_status'];
		$infoError = $coo_vat_validation->vat_info['error'];
		*/
	}
}
 