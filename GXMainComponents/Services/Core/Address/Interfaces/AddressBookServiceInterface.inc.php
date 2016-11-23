<?php
/* --------------------------------------------------------------
   AddressBookServiceInterface.inc.php 2015-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AddressBookServiceInterface
 * 
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface AddressBookServiceInterface 
{
	/**
	 * Method to add a new address in the address book 
	 * 
	 * @param AddressBlockInterface $addressBlock
	 * @param CustomerInterface $customer
	 *
	 * @return CustomerAddressInterface
	 */
	public function createNewAddress(AddressBlockInterface $addressBlock, CustomerInterface $customer);


	/**
	 * Method to update an address in the address book
	 * 
	 * @param AddressBlockInterface    $addressBlock
	 * @param CustomerAddressInterface $address
	 *
	 * @return CustomerAddressInterface
	 */
	public function updateAddress(AddressBlockInterface $addressBlock, CustomerAddressInterface $address);


	/**
	 * Method to delete an address from the address book
	 *
	 * @param CustomerAddressInterface $address
	 */
	public function deleteAddress(CustomerAddressInterface $address);
}