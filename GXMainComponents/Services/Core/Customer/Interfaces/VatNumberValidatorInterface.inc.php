<?php
/* --------------------------------------------------------------
   VatNumberValidatorInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface VatNumberValidatorInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface VatNumberValidatorInterface
{

	/**
	 * Returns the VAT number status code ID.
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return int VAT number status code ID.
	 */
	public function getVatNumberStatusCodeId($p_vatNumber, $p_countryId, $p_isGuest);


	/**
	 * Returns the customer status ID.
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return int Customer status ID.
	 */
	public function getCustomerStatusId($p_vatNumber, $p_countryId, $p_isGuest);


	/**
	 * Returns the error status
	 *
	 * @param string $p_vatNumber VAT number.
	 * @param int    $p_countryId Country ID.
	 * @param bool   $p_isGuest   Is customer a guest?
	 *
	 * @return bool Error status.
	 */
	public function getErrorStatus($p_vatNumber, $p_countryId, $p_isGuest);
}
 