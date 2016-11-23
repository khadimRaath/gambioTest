<?php
/* --------------------------------------------------------------
   CustomerReader.inc.php 2015-06-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerReaderInterface');

/**
 * Class CustomerReader
 *
 * This class is used for reading customer data from the database
 *
 * @category   System
 * @package    Customer
 * @implements CustomerReaderInterface
 */
class CustomerReader implements CustomerReaderInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Customer factory.
	 * @var AbstractCustomerFactory
	 */
	protected $customerFactory;

	/**
	 * Customer address repository.
	 * @var CustomerAddressRepositoryInterface
	 */
	protected $customerAddressRepository;

	/**
	 * String helper.
	 * @var StringHelperInterface
	 */
	protected $stringHelper;

	/**
	 * Is customer a guest?
	 * @var bool
	 */
	protected $isGuest;


	/**
	 * Constructor of the class CustomerReader.
	 *
	 * CrossCuttingLoader dependencies:
	 * - StringHelper
	 *
	 * @param AbstractCustomerFactory            $customerFactory           Customer factory.
	 * @param CustomerAddressRepositoryInterface $customerAddressRepository Customer address repository.
	 * @param CI_DB_query_builder                $dbQueryBuilder            Query builder.
	 */
	public function __construct(AbstractCustomerFactory $customerFactory,
	                            CustomerAddressRepositoryInterface $customerAddressRepository,
	                            CI_DB_query_builder $dbQueryBuilder)
	{
		$this->customerFactory           = $customerFactory;
		$this->customerAddressRepository = $customerAddressRepository;
		$this->db                        = $dbQueryBuilder;

		$this->stringHelperService = StaticCrossCuttingLoader::getObject('StringHelper');
	}


	/**
	 * Finds a customer by the given ID.
	 *
	 * @param IdType $id Customer's ID.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function findById(IdType $id)
	{
		$filterArray = array('customers_id' => (string)$id);

		return $this->_findByFilter($filterArray);
	}


	/**
	 * Finds a registree by email address.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function findRegistreeByEmail(CustomerEmailInterface $email)
	{
		$isGuest     = false;
		$filterArray = array(
			'customers_email_address' => (string)$email,
			'account_type'            => (string)(int)$isGuest
		);

		return $this->_findByFilter($filterArray);
	}


	/**
	 * Finds a guest by email address.
	 *
	 * @param CustomerEmailInterface $email Customer's E-Mail address.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	public function findGuestByEmail(CustomerEmailInterface $email)
	{
		$isGuest     = true;
		$filterArray = array(
			'customers_email_address' => (string)$email,
			'account_type'            => (string)(int)$isGuest
		);

		return $this->_findByFilter($filterArray);
	}


	/**
	 * Helper method which searches for user data based on an applied filter.
	 *
	 * @param array $filterArray Filters.
	 *
	 * @return Customer|null Customer or null if not found.
	 */
	protected function _findByFilter(array $filterArray)
	{
		$customerDataArray = $this->db->get_where('customers', $filterArray)->row_array();
		if(empty($customerDataArray))
		{
			return null;
		}

		return $this->_createCustomerByArray($customerDataArray);
	}


	/**
	 * Creates a customer based on the provided data.
	 *
	 * @param array $customerDataArray Customer data.
	 *
	 * @return Customer $customer Created customer.
	 *
	 * @todo If date of birth is null in the database then: $customerDataArray['customers_dob'] = '0000-00-00 00:00:00'
	 *       and then the getDateOfBirth() will return wrong results ($customer->getDateOfBirth() >> -0001-11-30
	 *       00:00:00).
	 */
	protected function _createCustomerByArray(array $customerDataArray)
	{
		$customerDataArray = $this->stringHelperService->convertNullValuesToStringInArray($customerDataArray);

		$customer = $this->customerFactory->createCustomer();
		$customer->setId(new IdType($customerDataArray['customers_id']));
		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', $customerDataArray['customers_cid']));
		$customer->setVatNumber(MainFactory::create('CustomerVatNumber', $customerDataArray['customers_vat_id']));
		$customer->setVatNumberStatus($customerDataArray['customers_vat_id_status']);
		$customer->setStatusId($customerDataArray['customers_status']);
		$customer->setGender(MainFactory::create('CustomerGender', $customerDataArray['customers_gender']));
		$customer->setFirstname(MainFactory::create('CustomerFirstname', $customerDataArray['customers_firstname']));
		$customer->setLastname(MainFactory::create('CustomerLastname', $customerDataArray['customers_lastname']));
		$customer->setDateOfBirth(MainFactory::create('CustomerDateOfBirth', $customerDataArray['customers_dob']));
		$customer->setEmail(MainFactory::create('CustomerEmail', $customerDataArray['customers_email_address']));
		$customer->setPassword(MainFactory::create('CustomerPassword', $customerDataArray['customers_password'], true));
		$customer->setTelephoneNumber(MainFactory::create('CustomerCallNumber',
		                                                  $customerDataArray['customers_telephone']));
		$customer->setFaxNumber(MainFactory::create('CustomerCallNumber', $customerDataArray['customers_fax']));
		$customer->setGuest((boolean)(int)$customerDataArray['account_type']);

		$customerAddress = $this->customerAddressRepository->getById(new IdType((int)$customerDataArray['customers_default_address_id']));
		$customer->setDefaultAddress($customerAddress);

		return $customer;
	}


	/**
	 * Filters customer records and returns an array with results.
	 *
	 * Example:
	 *        $reader->filterCustomers( array('customers_id' => 1) );
	 *
	 * @param array $conditions Associative array containing the desired field and value.
	 * @param int   $limit      MySQL limit index.
	 * @param int   $offset     Number of records to be returned.
	 *
	 * @return array Returns an array that contains customer objects.
	 */
	public function filterCustomers(array $conditions = array(), $limit = null, $offset = null)
	{
		if($limit !== null)
		{
			$this->db->limit((int)$limit, (int)$offset);
		}
		
		if(count($conditions) > 1) // connect multiple conditions with the "OR" operator
		{
			foreach($conditions as $field => $value)
			{
				$this->db->or_where($field, $value);
			}
			$results = $this->db->get('customers')->result_array();
		}
		else
		{
			$results = $this->db->get_where('customers', $conditions)->result_array();
		}
		
		$customers = array();
		
		foreach($results as $item)
		{
			$customers[] = $this->_createCustomerByArray($item);
		}

		return $customers;
	}
} 