<?php
/* --------------------------------------------------------------
   CustomerWriteServiceOverload.inc.php 2016-02-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerWriteServiceOverload
 *
 * This example overload class demonstrates the replacement of the CustomerWriteService class. The overload creates
 * debug log entries for the executed overloads.
 *
 * After enabling this sample create a new customer/guest record from the admin customers page. Then delete the new
 * customer records. The debug log entries can be found in the admin logs page.
 *
 * @see CustomerWriteService
 */
class CustomerWriteServiceOverload extends CustomerWriteServiceOverload_parent
{
	/**
	 * Overload the "createNewRegistree" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to create a new customer record.
	 *
	 * @param CustomerEmailInterface      $email
	 * @param CustomerPasswordInterface   $password
	 * @param DateTime                    $dateOfBirth
	 * @param CustomerVatNumberInterface  $vatNumber
	 * @param CustomerCallNumberInterface $telephoneNumber
	 * @param CustomerCallNumberInterface $faxNumber
	 * @param AddressBlockInterface       $addressBlock
	 *
	 * @return Customer
	 */
	public function createNewRegistree(CustomerEmailInterface $email,
	                                   CustomerPasswordInterface $password,
	                                   DateTime $dateOfBirth,
	                                   CustomerVatNumberInterface $vatNumber,
	                                   CustomerCallNumberInterface $telephoneNumber,
	                                   CustomerCallNumberInterface $faxNumber,
	                                   AddressBlockInterface $addressBlock)
	{
		$newCustomer = parent::createNewRegistree($email, $password, $dateOfBirth, $vatNumber, $telephoneNumber,
		                                          $faxNumber, $addressBlock);
		
		$logControl = LogControl::get_instance();
		$logControl->notice('CustomerWriteService::createNewRegistree >> New customer created with ID = '
		                    . $newCustomer->getId());
		
		return $newCustomer;
	}
	
	
	/**
	 * Overload the "createNewGuest" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to create a new guest record.
	 *
	 * @param CustomerEmailInterface      $email
	 * @param DateTime                    $dateOfBirth
	 * @param CustomerVatNumberInterface  $vatNumber
	 * @param CustomerCallNumberInterface $telephoneNumber
	 * @param CustomerCallNumberInterface $faxNumber
	 * @param AddressBlockInterface       $addressBlock
	 *
	 * @return Customer
	 */
	public function createNewGuest(CustomerEmailInterface $email,
	                               DateTime $dateOfBirth,
	                               CustomerVatNumberInterface $vatNumber,
	                               CustomerCallNumberInterface $telephoneNumber,
	                               CustomerCallNumberInterface $faxNumber,
	                               AddressBlockInterface $addressBlock)
	{
		$newGuest = parent::createNewGuest($email, $dateOfBirth, $vatNumber, $telephoneNumber, $faxNumber,
		                                   $addressBlock);
		
		$logControl = LogControl::get_instance();
		$logControl->notice('CustomerWriteService::createNewGuest>> New guest created with ID = '
		                    . $newGuest->getId());
		
		return $newGuest;
	}
	
	
	/**
	 * Overload the "deleteCustomerById" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to delete an existing
	 * customer record.
	 *
	 * @param IdType $customerId
	 */
	public function deleteCustomerById(IdType $customerId)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice('CustomerWriteService::deleteCustomerById >> Existing customer deleted with ID = '
		                    . $customerId->asInt());
		
		parent::deleteCustomerById($customerId);
	}
	
	
	/**
	 * Overload the "updateCustomer" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to update an existing
	 * customer record.
	 *
	 * @param CustomerInterface $customer
	 *
	 * @return Customer
	 */
	public function updateCustomer(CustomerInterface $customer)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice('CustomerWriteService::updateCustomer >> Existing customer updated with ID = '
		                    . $customer->getId());
		
		return parent::updateCustomer($customer);
	}
}
