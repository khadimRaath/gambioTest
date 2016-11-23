<?php
/* --------------------------------------------------------------
   CustomerInterface.inc.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerInterface extends AddonValueContainerInterface
{
	/**
	 * Returns the customer's ID.
	 *
	 * @return int Customer's ID.
	 */
	public function getId();


	/**
	 * Returns the customer's gender.
	 *
	 * @return CustomerGenderInterface Customer's gender.
	 */
	public function getGender();


	/**
	 * Returns the customer's first name.
	 *
	 * @return CustomerFirstnameInterface Customer's first name.
	 */
	public function getFirstname();


	/**
	 * Returns the customer's last name.
	 *
	 * @return CustomerLastnameInterface Customer's last name.
	 */
	public function getLastname();


	/**
	 * Returns the customer's date of birth.
	 *
	 * @return DateTime date of birth Customer's date of birth.
	 */
	public function getDateOfBirth();


	/**
	 * Returns the customer's VAT number.
	 *
	 * @return CustomerVatNumberInterface Customer's VAT number.
	 */
	public function getVatNumber();


	/**
	 * Returns the customer's telephone number.
	 *
	 * @return CustomerCallNumberInterface Customer's telephone number.
	 */
	public function getTelephoneNumber();


	/**
	 * Returns the customer's fax number.
	 *
	 * @return CustomerCallNumberInterface Customer's fax number.
	 */
	public function getFaxNumber();


	/**
	 * Returns the customer's email.
	 *
	 * @return CustomerEmailInterface Customer's email.
	 */
	public function getEmail();


	/**
	 * Returns the customer's default address.
	 *
	 * @return CustomerAddressInterface Customer's default address.
	 */
	public function getDefaultAddress();


	/**
	 * Sets the customer's ID.
	 *
	 * @param IdType $id customerId Customer ID.
	 *
	 * @throws InvalidArgumentException On invalid argument.
	 */
	public function setId(IdType $id);


	/**
	 * Sets the customer's gender.
	 *
	 * @param CustomerGenderInterface $gender Customer's gender.
	 */
	public function setGender(CustomerGenderInterface $gender);


	/**
	 * Sets the customer's first name.
	 *
	 * @param CustomerFirstnameInterface $firstname Customer's first name.
	 */
	public function setFirstname(CustomerFirstnameInterface $firstname);


	/**
	 * Sets the customer's last name.
	 *
	 * @param CustomerLastnameInterface $lastname Customer's last name.
	 */
	public function setLastname(CustomerLastnameInterface $lastname);


	/**
	 * Sets the customer's date of birth.
	 *
	 * @param DateTime $dateOfBirth date of birth Customer's date of birth.
	 */
	public function setDateOfBirth(DateTime $dateOfBirth);


	/**
	 * Sets the customer's VAT number.
	 *
	 * @param CustomerVatNumberInterface $vatNumber Customer's VAT number.
	 */
	public function setVatNumber(CustomerVatNumberInterface $vatNumber);


	/**
	 * Sets the customer's telephone number.
	 *
	 * @param CustomerCallNumberInterface $telephoneNumber Customer's telephone number.
	 */
	public function setTelephoneNumber(CustomerCallNumberInterface $telephoneNumber);


	/**
	 * Sets the customer's fax number.
	 *
	 * @param CustomerCallNumberInterface $faxNumber Customer's fax number.
	 */
	public function setFaxNumber(CustomerCallNumberInterface $faxNumber);


	/**
	 * Sets the customer's email.
	 *
	 * @param CustomerEmailInterface $email Customer's email.
	 */
	public function setEmail(CustomerEmailInterface $email);


	/**
	 * Sets the customer's password.
	 *
	 * @param CustomerPasswordInterface $password Customer's password.
	 */
	public function setPassword(CustomerPasswordInterface $password);


	/**
	 * Sets the customer's default address.
	 *
	 * @param CustomerAddressInterface $address Customer's default address.
	 */
	public function setDefaultAddress(CustomerAddressInterface $address);


	/**
	 * Returns the customer's password.
	 *
	 * @return CustomerPasswordInterface Customer's password.
	 */
	public function getPassword();


	/**
	 * Sets the customer's guest status.
	 *
	 * @param boolean $p_guest Customer's guest status.
	 */
	public function setGuest($p_guest);


	/**
	 * Checks if customer is a guest.
	 *
	 * @return bool Is customer a guest?
	 */
	public function isGuest();


	/**
	 * Returns the customer's status ID.
	 *
	 * @return int customerStatusId Customer's status ID.
	 */
	public function getStatusId();


	/**
	 * Sets the customer's status ID.
	 *
	 * @param int $p_statusId Customer's status ID.
	 */
	public function setStatusId($p_statusId);


	/**
	 * Returns the customer's number.
	 *
	 * @return string customerNumber Customer's number.
	 */
	public function getCustomerNumber();


	/**
	 * Sets the customer's number.
	 *
	 * @param CustomerNumberInterface $customerNumber Customer's number.
	 */
	public function setCustomerNumber(CustomerNumberInterface $customerNumber);


	/**
	 * Returns the customer's VAT number status.
	 *
	 * @return int Customer's VAT number status.
	 */
	public function getVatNumberStatus();


	/**
	 * Sets the customer's VAT number status.
	 *
	 * @param int $p_vatNumberStatus Customer's VAT number status.
	 */
	public function setVatNumberStatus($p_vatNumberStatus);
}
