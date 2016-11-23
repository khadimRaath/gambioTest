<?php
/* --------------------------------------------------------------
   CustomerService.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerServiceInterface');

/**
 * Class CustomerService
 *
 * This class provides methods for creating and deleting customer data
 *
 * @category   System
 * @package    Customer
 * @implements CustomerServiceInterface
 */
class CustomerService implements CustomerServiceInterface
{
	/**
	 * Customer read service.
	 * @var CustomerReadServiceInterface
	 */
	protected $customerReadService;

	/**
	 * Customer write service.
	 * @var CustomerWriteServiceInterface
	 */
	protected $customerWriteService;


	/**
	 * Constructor of the class CustomerService.
	 *
	 * @param CustomerReadServiceInterface  $customerReadService  Customer read service.
	 * @param CustomerWriteServiceInterface $customerWriteService Customer write service.
	 */
	public function __construct(CustomerReadServiceInterface $customerReadService,
	                            CustomerWriteServiceInterface $customerWriteService)
	{
		$this->customerReadService  = $customerReadService;
		$this->customerWriteService = $customerWriteService;
	}


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
	 *
	 * TODO Replaced by VAT Check
	 * TODO Rename to createNewRegistree
	 */
	public function createNewCustomer(CustomerEmailInterface $email,
	                                  CustomerPasswordInterface $password,
	                                  DateTime $dateOfBirth,
	                                  CustomerVatNumberInterface $vatNumber,
	                                  CustomerCallNumberInterface $telephoneNumber,
	                                  CustomerCallNumberInterface $faxNumber,
	                                  AddressBlockInterface $addressBlock,
	                                  KeyValueCollection $addonValues)
	{
		return $this->customerWriteService->createNewRegistree($email, $password, $dateOfBirth, $vatNumber,
		                                                       $telephoneNumber, $faxNumber, $addressBlock,
		                                                       $addonValues);
	}


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
	                               KeyValueCollection $addonValues)
	{
		return $this->customerWriteService->createNewGuest($email, $dateOfBirth, $vatNumber, $telephoneNumber,
		                                                   $faxNumber, $addressBlock, $addonValues);
	}


	/**
	 * Finds a customer by its ID.
	 *
	 * @param IdType $customerId Customer's ID.
	 *
	 * @return Customer Customer.
	 */
	public function getCustomerById(IdType $customerId)
	{
		return $this->customerReadService->getCustomerById($customerId);
	}


	/**
	 * Deletes the customer by its ID.
	 *
	 * @param IdType $customerId Customer's ID.
	 */
	public function deleteCustomerById(IdType $customerId)
	{
		return $this->customerWriteService->deleteCustomerById($customerId);
	}


	/**
	 * Checks if the email address of the registree already exists.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return bool Does the provided E-Mail address already exist?
	 */
	public function registreeEmailExists(CustomerEmailInterface $email)
	{
		return $this->customerReadService->registreeEmailExists($email);
	}


	/**
	 * Updates customer data.
	 *
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return CustomerInterface Updated customer.
	 */
	public function updateCustomer(CustomerInterface $customer)
	{
		return $this->customerWriteService->updateCustomer($customer);
	}


	/**
	 * Checks if address is the default address of the customer.
	 *
	 * @param CustomerAddressInterface $customerAddress Customer's address.
	 *
	 * @return bool Is the provided address the customer's default address?
	 */
	public function addressIsDefaultCustomerAddress(CustomerAddressInterface $customerAddress)
	{
		return $this->customerReadService->addressIsDefaultCustomerAddress($customerAddress);
	}


	/**
	 * Filters customer records and returns an array with results.
	 *
	 * Example:
	 *        $service->filterCustomers('customers_id' => 1);
	 *
	 * @param array $conditions Associative array containing the desired field and value.
	 * @param int   $limit      MySQL limit applied to the records.
	 * @param int   $offset     MySQL offset applied to the records.
	 *
	 * @return array Returns an array that contains customer objects.
	 */
	public function filterCustomers(array $conditions = array(), $limit = null, $offset = null)
	{
		return $this->customerReadService->filterCustomers($conditions, $limit, $offset);
	}
}