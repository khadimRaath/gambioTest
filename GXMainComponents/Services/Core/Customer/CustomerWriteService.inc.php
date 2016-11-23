<?php
/* --------------------------------------------------------------
   CustomerWriteService.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerWriteServiceInterface');

/**
 * Class CustomerWriteService
 *
 * This class provides methods for creating and deleting customer data
 *
 * @category   System
 * @package    Customer
 * @implements CustomerWriteServiceInterface
 */
class CustomerWriteService implements CustomerWriteServiceInterface
{
	/**
	 * Address book service.
	 * @var AddressBookServiceInterface
	 */
	protected $addressBookService;

	/**
	 * Customer repository.
	 * @var CustomerRepositoryInterface
	 */
	protected $customerRepository;

	/**
	 * Customer service settings.
	 * @var CustomerServiceSettingsInterface
	 */
	protected $customerServiceSettings;

	/**
	 * VAT number validator.
	 * @var VatNumberValidatorInterface
	 */
	protected $vatNumberValidator;


	/**
	 * Constructor of the class CustomerService.
	 *
	 * @param AddressBookServiceInterface      $addressBookService      Address book service.
	 * @param CustomerRepositoryInterface      $customerRepository      Customer repository.
	 * @param CustomerServiceSettingsInterface $customerServiceSettings Customer service settings.
	 * @param VatNumberValidatorInterface      $vatNumberValidator      VAT number validator.
	 */
	public function __construct(AddressBookServiceInterface $addressBookService,
	                            CustomerRepositoryInterface $customerRepository,
	                            CustomerServiceSettingsInterface $customerServiceSettings,
	                            VatNumberValidatorInterface $vatNumberValidator)
	{
		$this->addressBookService      = $addressBookService;
		$this->customerRepository      = $customerRepository;
		$this->customerServiceSettings = $customerServiceSettings;
		$this->vatNumberValidator      = $vatNumberValidator;
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
	 * TODO Replaced by Vat Check
	 * TODO Rename to createNewRegistree
	 */
	public function createNewRegistree(CustomerEmailInterface $email,
	                                   CustomerPasswordInterface $password,
	                                   DateTime $dateOfBirth,
	                                   CustomerVatNumberInterface $vatNumber,
	                                   CustomerCallNumberInterface $telephoneNumber,
	                                   CustomerCallNumberInterface $faxNumber,
	                                   AddressBlockInterface $addressBlock,
	                                   KeyValueCollection $addonValues)
	{
		if($this->customerRepository->getRegistreeByEmail($email) != null)
		{
			throw new UnexpectedValueException('E-Mail already used in existing customer.');
		}

		/* @var Customer $customer */
		$customer = $this->customerRepository->getNewCustomer();
		$customer->setStatusId($this->customerServiceSettings->getDefaultCustomerStatusId()); // TODO: replaced by vat check?

		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', (string)$customer->getId()));
		$customer->setGender($addressBlock->getGender());
		$customer->setFirstname($addressBlock->getFirstname());
		$customer->setLastname($addressBlock->getLastname());
		$customer->setEmail($email);
		$customer->setPassword($password);
		$customer->setDateOfBirth($dateOfBirth);
		$customer->setTelephoneNumber($telephoneNumber);
		$customer->setFaxNumber($faxNumber);

		// import addressBlock data into empty default address
		$this->addressBookService->updateAddress($addressBlock, $customer->getDefaultAddress());

		$vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($vatNumber,
		                                                                       $addressBlock->getCountry()->getId(),
		                                                                       false);
		$customer->setVatNumber($vatNumber);
		$customer->setVatNumberStatus($vatNumberStatus);

		$vatCustomerStatus = $this->vatNumberValidator->getCustomerStatusId($vatNumber,
		                                                                    $addressBlock->getCountry()->getId(),
		                                                                    false);
		$customer->setStatusId($vatCustomerStatus);
		
		$customer->addAddonValues($addonValues);

		$this->customerRepository->store($customer);

		return $customer;
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
		$this->customerRepository->deleteGuestByEmail($email);

		if($this->customerRepository->getRegistreeByEmail($email) != null)
		{
			throw new UnexpectedValueException('E-Mail already used in existing customer.');
		}

		/* @var Customer $customer */
		$customer = $this->customerRepository->getNewCustomer();
		$customer->setGuest(true);
		$customer->setStatusId($this->customerServiceSettings->getDefaultGuestStatusId());

		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', (string)$customer->getId()));
		$customer->setGender($addressBlock->getGender());
		$customer->setFirstname($addressBlock->getFirstname());
		$customer->setLastname($addressBlock->getLastname());
		$customer->setEmail($email);
		$customer->setDateOfBirth($dateOfBirth);
		$customer->setTelephoneNumber($telephoneNumber);
		$customer->setFaxNumber($faxNumber);

		// import addressBlock data into empty default address
		$this->addressBookService->updateAddress($addressBlock, $customer->getDefaultAddress());

		$vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($vatNumber,
		                                                                       $addressBlock->getCountry()->getId(),
		                                                                       true);
		$customer->setVatNumber($vatNumber);
		$customer->setVatNumberStatus($vatNumberStatus);

		$vatCustomerStatus = $this->vatNumberValidator->getCustomerStatusId($vatNumber,
		                                                                    $addressBlock->getCountry()->getId(), true);
		$customer->setStatusId($vatCustomerStatus);
		
		$customer->addAddonValues($addonValues);

		$this->customerRepository->store($customer);

		return $customer;
	}


	/**
	 * Deletes the customer with the provided ID.
	 *
	 * @param IdType $customerId Customer's ID.
	 */
	public function deleteCustomerById(IdType $customerId)
	{
		$this->customerRepository->deleteCustomerById($customerId);
	}


	/**
	 * Updates customer data.
	 *
	 * @param CustomerInterface $customer Customer.
	 *
	 * @return CustomerInterface Updated customer.
	 *
	 * TODO check if the new email address is used by another record
	 */
	public function updateCustomer(CustomerInterface $customer)
	{
		$vatNumberStatus = $this->vatNumberValidator->getVatNumberStatusCodeId($customer->getVatNumber(),
		                                                                       $customer->getDefaultAddress()
		                                                                                ->getCountry()
		                                                                                ->getId(), false);
		$customer->setVatNumberStatus($vatNumberStatus);
		$this->customerRepository->store($customer);

		return $customer;
	}

}