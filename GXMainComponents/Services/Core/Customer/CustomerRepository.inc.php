<?php
/* --------------------------------------------------------------
   CustomerRepository.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerRepositoryInterface');

/**
 * Class CustomerRepository
 *
 * This class contains basic methods for finding, creating and deleting customer data
 *
 * @category   System
 * @package    Customer
 * @implements CustomerRepositoryInterface
 */
class CustomerRepository implements CustomerRepositoryInterface
{
	/**
	 * Customer writer.
	 * @var CustomerWriterInterface $customerWriter
	 */
	protected $customerWriter;

	/**
	 * Customer reader.
	 * @var CustomerReaderInterface $customerReader
	 */
	protected $customerReader;

	/**
	 * Customer deleter.
	 * @var CustomerDeleterInterface $customerDeleter
	 */
	protected $customerDeleter;

	/**
	 * Customer address repository.
	 * @var CustomerAddressRepositoryInterface $customerAddressRepository
	 */
	protected $customerAddressRepository;

	/**
	 * Customer factory.
	 * @var AbstractCustomerFactory $customerFactory
	 */
	protected $customerFactory;
	
	/**
	 * @var AddonValueServiceInterface
	 */
	protected $addonValueService;
	
	
	/**
	 * Constructor of the class CustomerRepository.
	 *
	 * @param CustomerWriterInterface            $customerWriter            Customer writer.
	 * @param CustomerReaderInterface            $customerReader            Customer reader.
	 * @param CustomerDeleterInterface           $customerDeleter           Customer deleter.
	 * @param CustomerAddressRepositoryInterface $customerAddressRepository Customer address repository.
	 * @param AbstractCustomerFactory            $customerFactory           Customer factory.
	 * @param AddonValueServiceInterface         $addonValueService         Service to handle customer addon values.
	 */
	public function __construct(CustomerWriterInterface $customerWriter,
	                            CustomerReaderInterface $customerReader,
	                            CustomerDeleterInterface $customerDeleter,
	                            CustomerAddressRepositoryInterface $customerAddressRepository,
	                            AbstractCustomerFactory $customerFactory,
	                            AddonValueServiceInterface $addonValueService)
	{
		$this->customerWriter            = $customerWriter;
		$this->customerReader            = $customerReader;
		$this->customerDeleter           = $customerDeleter;
		$this->customerAddressRepository = $customerAddressRepository;
		$this->customerFactory           = $customerFactory;
		$this->addonValueService         = $addonValueService;
	}


	/**
	 * Creates a new customer.
	 *
	 * @return Customer Newly created customer.
	 */
	public function getNewCustomer()
	{
		/* @var Customer $customer */
		$customer = $this->customerFactory->createCustomer();

		$emptyAddress = $this->customerFactory->createCustomerAddress();
		$this->customerAddressRepository->store($emptyAddress);

		$customer->setDefaultAddress($emptyAddress);
		$this->store($customer);

		$emptyAddress->setCustomerId(new IdType($customer->getId()));
		$this->customerAddressRepository->store($emptyAddress);

		return $customer;
	}


	/**
	 * Stores customer data in the database.
	 *
	 * @param CustomerInterface $customer Customer.
	 */
	public function store(CustomerInterface $customer)
	{
		$this->customerWriter->write($customer);
		$this->addonValueService->storeAddonValues($customer);
	}


	/**
	 * Finds customer data by an ID.
	 *
	 * @param IdType $customerId Customer's ID.
	 *
	 * @throws InvalidArgumentException If customer has been not found.
	 * @return CustomerInterface
	 */
	public function getCustomerById(IdType $customerId)
	{
		$customer = $this->customerReader->findById($customerId);
		if($customer == null)
		{
			throw new InvalidArgumentException('No customer found by given id');
		}
		
		$this->addonValueService->loadAddonValues($customer);

		return $customer;
	}


	/**
	 * Finds a registered customer based on the e-mail address.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function getRegistreeByEmail(CustomerEmailInterface $email)
	{
		$customer = $this->customerReader->findRegistreeByEmail($email);
		if($customer !== null)
		{
			$this->addonValueService->loadAddonValues($customer);
		}

		return $customer;
	}


	/**
	 * Deletes the customer by the ID.
	 *
	 * @param IdType $customerId Customer's ID.
	 */
	public function deleteCustomerById(IdType $customerId)
	{
		$customer = MainFactory::create('Customer');
		$customer->setId($customerId);
		$this->addonValueService->deleteAddonValues($customer);
		$this->customerDeleter->delete($customer);
	}
	
	
	/**
	 * Deletes a guest account by its email address.
	 *
	 * @param CustomerEmailInterface $email Guest customer's E-Mail address.
	 */
	public function deleteGuestByEmail(CustomerEmailInterface $email)
	{
		$customer = $this->customerReader->findGuestByEmail($email);
		if($customer != null)
		{
			$this->addonValueService->deleteAddonValues($customer);
			$this->customerAddressRepository->deleteCustomerAddressesByCustomer($customer);
			$this->customerDeleter->delete($customer);
		}
	}

	
	/**
	 * Returns a guest account by its email address.
	 *
	 * @param CustomerEmailInterface $email Guest customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function getGuestByEmail(CustomerEmailInterface $email)
	{
		$customer = $this->customerReader->findGuestByEmail($email);
		$this->addonValueService->loadAddonValues($customer);

		return $customer;
	}

	
	/**
	 * Filters customer records and returns an array with results.
	 *
	 * Example:
	 *        $repository->filterCustomers('customers_id' => 1);
	 *
	 * @param array $conditions Associative array containing the desired field and value.
	 * @param int   $limit      Result limit
	 * @param int   $offset     Result offset
	 *
	 * @return array Returns an array that contains customer objects.
	 */
	public function filterCustomers(array $conditions = array(), $limit = null, $offset = null)
	{
		$customers = $this->customerReader->filterCustomers($conditions, $limit, $offset);
		/** @var CustomerInterface $customer */
		foreach($customers as $customer)
		{
			$this->addonValueService->loadAddonValues($customer);
		}
		
		return $customers;
	}
}