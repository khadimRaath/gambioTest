<?php
/* --------------------------------------------------------------
   CustomerWriteServiceInterface.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerWriteServiceInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerWriteServiceInterface
{
	/**
	 * Creates a new customer with the given parameters.
	 *
	 * @param CustomerEmailInterface      $email           Customer's E-Mail address.
	 * @param CustomerPasswordInterface   $password        Customer's password.
	 * @param DateTime                    $dateOfBirth     Customer's date of birth.
	 * @param CustomerVatNumberInterface  $vatNumber       Customer's VAT number.
	 * @param CustomerCallNumberInterface $telephoneNumber Customer's telephone number.
	 * @param CustomerCallNumberInterface $faxNumber       Customer's fax number.
	 * @param AddressBlockInterface       $addressBlock    Customer's address.
	 * @param KeyValueCollection          $addonValues     Customer's additional values.
	 *
	 * @return Customer Created customer.
	 * @throws UnexpectedValueException On invalid arguments.
	 */
	public function createNewRegistree(CustomerEmailInterface $email,
	                                   CustomerPasswordInterface $password,
	                                   DateTime $dateOfBirth,
	                                   CustomerVatNumberInterface $vatNumber,
	                                   CustomerCallNumberInterface $telephoneNumber,
	                                   CustomerCallNumberInterface $faxNumber,
	                                   AddressBlockInterface $addressBlock,
	                                   KeyValueCollection $addonValues);


	/**
	 * Creates a new guest account with the given parameters.
	 *
	 * @param CustomerEmailInterface      $email           Customer's E-Mail address.
	 * @param DateTime                    $dateOfBirth     Customer's date of birth.
	 * @param CustomerVatNumberInterface  $vatNumber       Customer's VAT number.
	 * @param CustomerCallNumberInterface $telephoneNumber Customer's telephone number.
	 * @param CustomerCallNumberInterface $faxNumber       Customer's fax number.
	 * @param AddressBlockInterface       $addressBlock    Customer's address.
	 * @param KeyValueCollection          $addonValues     Customer's additional values.
	 *
	 * @return Customer Created guest customer.
	 * @throws UnexpectedValueException On invalid arguments.
	 */
	public function createNewGuest(CustomerEmailInterface $email,
	                               DateTime $dateOfBirth,
	                               CustomerVatNumberInterface $vatNumber,
	                               CustomerCallNumberInterface $telephoneNumber,
	                               CustomerCallNumberInterface $faxNumber,
	                               AddressBlockInterface $addressBlock,
	                               KeyValueCollection $addonValues);


	/**
	 * Updates customer data.
	 *
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return CustomerInterface Updated customer.
	 */
	public function updateCustomer(CustomerInterface $customer);


	/**
	 * Deletes the customer with the provided ID.
	 *
	 * @param IdType $customerId Customer's ID.
	 */
	public function deleteCustomerById(IdType $customerId);
}