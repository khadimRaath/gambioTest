<?php
/* --------------------------------------------------------------
   CustomerAddressDeleterInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressDeleterInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerAddressDeleterInterface
{
	/**
	 * Method to delete a customer address
	 * 
	 * @param CustomerAddressInterface $customerAddress
	 */
	public function delete(CustomerAddressInterface $customerAddress);


	/**
	 * Method to delete a customer address with a given customer
	 * 
	 * @param CustomerInterface $customer
	 */
	public function deleteByCustomer(CustomerInterface $customer);
}
 