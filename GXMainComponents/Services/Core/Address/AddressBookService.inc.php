<?php
/* --------------------------------------------------------------
   AddressBookService.inc.php 2016-06-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AddressBookServiceInterface');

/**
 * Class AddressBookService
 *
 * This class is used to manage addresses
 *
 * @category   System
 * @package    Customer
 * @subpackage Address
 * @implements AddressBookServiceInterface
 */
class AddressBookService implements AddressBookServiceInterface
{
	/**
	 * @var CustomerAddressRepositoryInterface
	 */
	protected $customerAddressRepository;


	/**
	 * Constructor of the class AddressBookService
	 *
	 * @param CustomerAddressRepositoryInterface $addressRepository
	 */
	public function __construct(CustomerAddressRepositoryInterface $addressRepository)
	{
		$this->customerAddressRepository = $addressRepository;
	}


	/**
	 * Method to add a new address in the address book
	 *
	 * @param AddressBlockInterface $addressBlock
	 * @param CustomerInterface     $customer
	 *
	 * @return CustomerAddressInterface
	 */
	public function createNewAddress(AddressBlockInterface $addressBlock, CustomerInterface $customer)
	{
		/* @var CustomerAddress $address */
		$address = $this->customerAddressRepository->getNewAddress();
		$address->importAddressBlock($addressBlock);
		$address->setCustomerId(new IdType($customer->getId()));
		$this->customerAddressRepository->store($address);

		return $address;
	}


	/**
	 * Method to update an address in the address book
	 *
	 * @param AddressBlockInterface    $addressBlock
	 * @param CustomerAddressInterface $address
	 *
	 * @return CustomerAddressInterface
	 */
	public function updateAddress(AddressBlockInterface $addressBlock, CustomerAddressInterface $address)
	{
		$address->importAddressBlock($addressBlock);
		$this->customerAddressRepository->store($address);

		return $address;
	}


	/**
	 * @param CustomerAddressInterface $address
	 */
	public function deleteAddress(CustomerAddressInterface $address)
	{
		$this->customerAddressRepository->deleteCustomerAddress($address);
	}


	/**
	 * @param IdType $addressId
	 *
	 * @return CustomerAddress|null
	 */
	public function findAddressById(IdType $addressId)
	{
		return $this->customerAddressRepository->findById($addressId);
	}


	/**
	 * @param CustomerAddressInterface $customerAddress
	 */
	public function updateCustomerAddress(CustomerAddressInterface $customerAddress)
	{
		$this->customerAddressRepository->store($customerAddress);
	}


	/**
	 * Get customer addresses.
	 *
	 * @param CustomerInterface $customer Contains the customer data.
	 *
	 * @return array Returns an array of CustomerAddress objects.
	 */
	public function getCustomerAddresses(CustomerInterface $customer)
	{
		return $this->customerAddressRepository->getCustomerAddresses($customer);
	}


	/**
	 * Get all registered addresses.
	 *
	 * @return array Returns an array of CustomerAddress objects.
	 */
	public function getAllAddresses()
	{
		return $this->customerAddressRepository->getAllAddresses();
	}


	/**
	 * Filter registered addresses by string.
	 *
	 * @param string $p_keyword Used to filter the address records.
	 *
	 * @return array Returns an array of CustomerAddress objects.
	 */
	public function filterAddresses($p_keyword)
	{
		return $this->customerAddressRepository->filterAddresses($p_keyword);
	}
} 