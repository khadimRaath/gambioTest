<?php
/* --------------------------------------------------------------
   CustomerAddressRepositoryInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressRepositoryInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerAddressRepositoryInterface 
{
	/**
	 * Method to delete all customers addresses with a given customer
	 * 
	 * @param \CustomerInterface $customer
	 */
	public function deleteCustomerAddressesByCustomer(CustomerInterface $customer);


	/**
	 * Method to get all customers addresses
	 * 
	 * @param \CustomerInterface $customer
	 *
	 * @return array of CustomerAddress objects
	 */
	public function getCustomerAddresses(CustomerInterface $customer);


	/**
	 * Method to store the customer address
	 * 
	 * @param \CustomerAddressInterface $address
	 *
	 * @return CustomerAddressInterface
	 */
	public function store(CustomerAddressInterface $address);


	/**
	 * Method to delete a customer address
	 * 
	 * @param \CustomerAddressInterface $address
	 */
	public function deleteCustomerAddress(CustomerAddressInterface $address);


	/**
	 * Method to get a customer address with a given ID
	 * 
	 * @param IdType $addressBookId
	 *
	 * @return CustomerAddress
	 */
	public function getById(IdType $addressBookId);


	/**
	 * Method to find a customer address with a given ID it it exists else it will return null
	 * 
	 * @param IdType $addressBookId
	 *
	 * @return CustomerAddress|null
	 */
	public function findById(IdType $addressBookId);
}