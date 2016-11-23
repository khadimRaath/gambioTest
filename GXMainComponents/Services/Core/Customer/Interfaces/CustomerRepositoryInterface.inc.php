<?php
/* --------------------------------------------------------------
   CustomerRepositoryInterface.php 2015-02-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerRepositoryInterface
 *
 * @category   System
 * @package    Customer
 * @subpackage Interfaces
 */
interface CustomerRepositoryInterface
{
	/**
	 * Creates a new customer.
	 *
	 * @return Customer Newly created customer.
	 */
	public function getNewCustomer();


	/**
	 * Stores customer data in the database.
	 *
	 * @param CustomerInterface $customer Customer.
	 */
	public function store(CustomerInterface $customer);


	/**
	 * Finds a registered customer based on the e-mail address.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function getRegistreeByEmail(CustomerEmailInterface $email);


	/**
	 * Deletes a guest account by its email address.
	 *
	 * @param CustomerEmailInterface $email Guest customer's E-Mail address.
	 */
	public function deleteGuestByEmail(CustomerEmailInterface $email);


	/**
	 * Returns a guest account by its email address.
	 *
	 * @param CustomerEmailInterface $email Guest customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function getGuestByEmail(CustomerEmailInterface $email);
}