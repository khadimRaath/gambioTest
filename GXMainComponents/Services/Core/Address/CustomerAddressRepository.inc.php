<?php
/* --------------------------------------------------------------
   CustomerAddressRepository.inc.php 2015-07-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerAddressRepositoryInterface');

/**
 * Class CustomerAddressRepository
 * 
 * This class contains basic methods for finding, creating and deleting customer addresses
 *
 * @category System
 * @package Customer
 * @subpackage Address
 * @implements CustomerAddressRepositoryInterface
 */
class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{
	/**
	 * @var CustomerAddressWriterInterface
	 */
	protected $customerAddressWriter;
	/**
	 * @var CustomerAddressDeleterInterface
	 */
	protected $customerAddressDeleter;
	/**
	 * @var CustomerAddressReaderInterface
	 */
	protected $customerAddressReader;
	/**
	 * @var AbstractCustomerFactory
	 */
	protected $customerFactory;

	
	/**
	 * Constructor of the class CustomerAddressRepository
	 * 
	 * @param CustomerAddressWriterInterface $customerAddressWriter
	 * @param CustomerAddressDeleterInterface $customerAddressDeleter
	 * @param CustomerAddressReaderInterface  $customerAddressReader
	 * @param AbstractCustomerFactory $customerFactory
	 */
	public function __construct(CustomerAddressWriterInterface $customerAddressWriter, 
								CustomerAddressDeleterInterface $customerAddressDeleter, 
								CustomerAddressReaderInterface $customerAddressReader,
								AbstractCustomerFactory $customerFactory)
	{
		$this->customerAddressWriter = $customerAddressWriter;
		$this->customerAddressDeleter = $customerAddressDeleter;
		$this->customerAddressReader = $customerAddressReader;
		$this->customerFactory = $customerFactory;
	}


	/**
	 * @return CustomerAddress
	 */
	public function getNewAddress()
	{
		$address = $this->customerFactory->createCustomerAddress();
		$this->customerAddressWriter->write($address);
		return $address;
	}

	/**
	 * Stores the customer address
	 * 
	 * @param CustomerAddressInterface $address
	 */
	public function store(CustomerAddressInterface $address)
	{
		$this->customerAddressWriter->write($address);
	}

	/**
	 * Deletes the customer address
	 * 
	 * @param CustomerInterface $customer
	 */
	public function deleteCustomerAddressesByCustomer(CustomerInterface $customer)
	{
		$this->customerAddressDeleter->deleteByCustomer($customer);
	}


	public function deleteCustomerAddress(CustomerAddressInterface $address)
	{
		$this->customerAddressDeleter->delete($address);
	}


	/**
	 * Get all registered address records. 
	 * 
	 * @return array Returns an array of CustomerAddress objects.
	 */
	public function getAllAddresses()
	{
		return $this->customerAddressReader->getAllAddresses(); 
	}


	/**
	 * Filter existing address records by provided string. 
	 * 
	 * @param string $p_keyword
	 *
	 * @return array Returns an array of CustomerAddress objects. 
	 */
	public function filterAddresses($p_keyword)
	{
		return $this->customerAddressReader->filterAddresses($p_keyword); 
	}

	/**
	 * Gets all customer addresses
	 * 
	 * @param CustomerInterface $customer
	 *
	 * @return array of CustomerAddress objects
	 */
	public function getCustomerAddresses(CustomerInterface $customer)
	{
		$customerAddressArray = $this->customerAddressReader->findAddressesByCustomer($customer);
		return $customerAddressArray;
	}


	/**
	 * @param IdType $addressBookId
	 *
	 * @return CustomerAddress
	 */
	public function getById(IdType $addressBookId)
	{
		$customerAddress = $this->customerAddressReader->findById($addressBookId);
		return $customerAddress;
	}


	/**
	 * @param IdType $addressBookId
	 *
	 * @return CustomerAddress
	 */
	public function findById(IdType $addressBookId)
	{
		$customerAddress = $this->customerAddressReader->findById($addressBookId);
		return $customerAddress;
	}
}
