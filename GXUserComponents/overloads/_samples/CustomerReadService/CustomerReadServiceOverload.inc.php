<?php
/* --------------------------------------------------------------
   CustomerReadServiceOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerReadServiceOverload
 *
 * This sample demonstrates the overloading of the CustomerReadService class. The overload sets the product & category
 * listing templates to a fixed value.
 *
 * After enabling this sample head to the admin customers overview page and click on the "New Order" row option of a
 * customer record. The "getCustomerById" override method will be executed and a new debug log entry will be created.
 *
 * In extend you can create a new customer from the frontend template. After completing the registration process
 * the "registreeEmailExists" override method will be executed, creating a new debug log entry.
 *
 * @see CustomerReadService
 */
class CustomerReadServiceOverload extends CustomerReadServiceOverload_parent
{
	/**
	 * Overloaded constructor of the customer read service.
	 *
	 * Pass the parent arguments in the parent call and inject optionally an instance of mysqli
	 * for some extra logging.
	 *
	 * @param CustomerRepositoryInterface $customerRepository
	 */
	public function __construct(CustomerRepositoryInterface $customerRepository)
	{
		parent::__construct($customerRepository);
	}
	
	
	/**
	 * Overloaded the "getCustomerById" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to fetch a customer record from
	 * the database.
	 *
	 * @param IdType $customerId
	 */
	public function getCustomerById(IdType $customerId)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice('CustomerReadService::getCustomerById >> Fetched customer with ID = '
		                    . $customerId->asInt());
		
		return parent::getCustomerById($customerId);
	}
	
	
	/**
	 * Overload the "registreeEmailExists" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to check whether a customer
	 * email already exists.
	 *
	 * @param CustomerEmailInterface $email
	 *
	 * @return bool
	 */
	public function registreeEmailExists(CustomerEmailInterface $email)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice('CustomerReadService::registreeEmailExists >> Checked if there is an existing customer with '
		                    . 'email = ' . (string)$email);
		
		return parent::registreeEmailExists($email);
	}
}
