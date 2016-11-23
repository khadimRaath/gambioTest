<?php
/* --------------------------------------------------------------
   CustomerServiceInterface.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerServiceInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerServiceInterface
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
	public function createNewCustomer(CustomerEmailInterface $email,
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
	 * Checks if the email address of the registree already exists.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return bool Does the provided E-Mail address already exist?
	 */
	public function registreeEmailExists(CustomerEmailInterface $email);


	/**
	 * Updates customer data.
	 *
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return CustomerInterface Updated customer.
	 */
	public function updateCustomer(CustomerInterface $customer);


	/**
	 * Checks if address is the default address of the customer.
	 *
	 * @param CustomerAddressInterface $customerAddress Customer's address.
	 *
	 * @return bool Is the provided address the customer's default address?
	 */
	public function addressIsDefaultCustomerAddress(CustomerAddressInterface $customerAddress);
}