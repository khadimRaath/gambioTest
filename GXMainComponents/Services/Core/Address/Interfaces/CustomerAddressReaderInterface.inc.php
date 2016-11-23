<?php
/* --------------------------------------------------------------
   CustomerAddressReaderInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressReaderInterface
 *
 * @category System
 * @package Customer
 * @subpackage Interfaces
 */
interface CustomerAddressReaderInterface 
{

	/**
	 * Method to find a customer address with a given ID if it exits else it will return null
	 * 
	 * @param IdType $id
	 *
	 * @return CustomerAddress|null
	 * @throws InvalidArgumentException
	 */
	public function findById(IdType $id);


	/**
	 * Method to find customer addresses with a given customer
	 * Returns an array of all customer's addresses
	 * 
	 * @param CustomerInterface $customer
	 *
	 * @return array containing CustomerAddress objects
	 */
	public function findAddressesByCustomer(CustomerInterface $customer);
} 