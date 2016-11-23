<?php
/* --------------------------------------------------------------
   CustomerAddressInterface.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerAddressInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 *
 * @extends    AddressBlockInterface
 */
interface CustomerAddressInterface extends AddressBlockInterface
{

	/**
	 * Getter method for the ID
	 *
	 * @return int
	 */
	public function getId();


	/**
	 * Setter method for the ID
	 *
	 * @param IdType $id addressBookId
	 */
	public function setId(IdType $id);


	/**
	 * Getter method for the customer ID
	 *
	 * @return int
	 */
	public function getCustomerId();


	/**
	 * Setter method for the customer ID
	 *
	 * @param IdType $customerId
	 */
	public function setCustomerId(IdType $customerId);


	/**
	 * Getter method for the address class
	 *
	 * @return AddressClassInterface
	 */
	public function getAddressClass();


	/**
	 * Setter method for the address class
	 *
	 * @param AddressClassInterface $addressClass
	 */
	public function setAddressClass(AddressClassInterface $addressClass);


	/**
	 * Setter method for the address class
	 *
	 * @param CustomerB2BStatusInterface $b2bStatus
	 */
	public function setB2BStatus(CustomerB2BStatusInterface $b2bStatus);
} 