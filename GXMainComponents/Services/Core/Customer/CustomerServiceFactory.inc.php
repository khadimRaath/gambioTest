<?php
/* --------------------------------------------------------------
   CustomerServiceFactory.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCustomerServiceFactory');

/**
 * Class CustomerServiceFactory
 *
 * Factory class for all needed customer data.
 *
 * @category System
 * @package  Customer
 * @extends  AbstractCustomerServiceFactory
 */
class CustomerServiceFactory extends AbstractCustomerServiceFactory
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $ciDatabaseQueryBuilder;


	/**
	 * CustomerServiceFactory constructor.
	 *
	 * @param CI_DB_query_builder $ciDatabaseQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $ciDatabaseQueryBuilder)
	{
		$this->ciDatabaseQueryBuilder = $ciDatabaseQueryBuilder;
	}


	/**
	 * Returns the country service.
	 *
	 * @return CountryService Country service.
	 */
	public function getCountryService()
	{
		$customerCountryRepo     = $this->_getCustomerCountryRepository();
		$customerCountryZoneRepo = $this->_getCustomerCountryZoneRepository();

		$countryService = MainFactory::create('CountryService', $customerCountryRepo, $customerCountryZoneRepo);

		return $countryService;
	}


	/**
	 * Returns the customer service.
	 *
	 * @return CustomerService Customer service.
	 */
	public function getCustomerService()
	{
		$customerReadService  = $this->createCustomerReadService();
		$customerWriteService = $this->createCustomerWriteService();

		$customerService = MainFactory::create('CustomerService', $customerReadService, $customerWriteService);

		return $customerService;
	}


	/**
	 * Creates a customer read service object.
	 *
	 * @return CustomerReadService Customer read service.
	 */
	public function createCustomerReadService()
	{
		$customerRepository = $this->_getCustomerRepository();

		$customerReadService = MainFactory::create('CustomerReadService', $customerRepository);

		return $customerReadService;
	}


	/**
	 * Creates a customer service object.
	 *
	 * @return CustomerService Customer service.
	 */
	public function createCustomerWriteService()
	{
		$addressBookService      = $this->getAddressBookService();
		$customerRepository      = $this->_getCustomerRepository();
		$customerServiceSettings = $this->_getCustomerServiceSettings();
		$vatValidator            = MainFactory::create('VatNumberValidator');

		$customerWriteService = MainFactory::create('CustomerWriteService', $addressBookService, $customerRepository,
		                                            $customerServiceSettings, $vatValidator);

		return $customerWriteService;
	}
	
	
	/**
	 * Returns the address book service.
	 *
	 * @return AddressBookService Address book service.
	 */
	public function getAddressBookService()
	{
		$addressRepository  = $this->_getCustomerAddressRepository();
		$addressBookService = MainFactory::create('AddressBookService', $addressRepository);

		return $addressBookService;
	}


	/**
	 * Returns the customer registration input validator service.
	 *
	 * @return CustomerRegistrationInputValidatorService Customer registration input validator service.
	 */
	public function getCustomerRegistrationInputValidatorService()
	{
		return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerRegistrationInputValidatorService');
	}
	

	/**
	 * Returns the customer account input validator.
	 *
	 * @return CustomerAccountInputValidator Customer account input validator.
	 */
	public function getCustomerAccountInputValidator()
	{
		return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerAccountInputValidator');
	}


	/**
	 * Returns the database query builder.
	 *
	 * @return CI_DB_query_builder Query builder.
	 */
	public function getDatabaseQueryBuilder()
	{
		return $this->ciDatabaseQueryBuilder;
	}


	/**
	 * Returns the customer factory.
	 *
	 * @return CustomerFactory Customer factory.
	 *
	 * TODO Inject CustomerFactory
	 */
	protected function _getCustomerFactory()
	{
		$customerFactory = MainFactory::create('CustomerFactory');

		return $customerFactory;
	}
	

	/**
	 * Creates a customer repository object.
	 *
	 * @return CustomerRepository Customer repository.
	 */
	protected function _getCustomerRepository()
	{
		$customerWriter    = $this->_getCustomerWriter();
		$customerReader    = $this->_getCustomerReader();
		$customerDeleter   = $this->_getCustomerDeleter();
		$addressRepository = $this->_getCustomerAddressRepository();
		$customerFactory   = $this->_getCustomerFactory();
		$addonValueService = $this->_getAddonValueService();
		
		$repository = MainFactory::create('CustomerRepository', $customerWriter, $customerReader, $customerDeleter,
		                                  $addressRepository, $customerFactory, $addonValueService);

		return $repository;
	}


	/**
	 * Returns the customer input validator.
	 *
	 * @return CustomerAddressInputValidator Customer input validator.
	 */
	public function getCustomerAddressInputValidatorService()
	{
		return $this->_getCustomerInputValidatorServiceByValidatorName('CustomerAddressInputValidator');
	}
	

	/**
	 * Creates a customer country repository object.
	 *
	 * @return CustomerCountryRepository Customer country repository.
	 */
	protected function _getCustomerCountryRepository()
	{
		$reader = $this->_getCustomerCountryReader();
		$repo   = MainFactory::create('CustomerCountryRepository', $reader);

		return $repo;
	}


	/**
	 * Creates a customer country zone repository object.
	 *
	 * @return CustomerCountryZoneRepository Customer country zone repository.
	 */
	protected function _getCustomerCountryZoneRepository()
	{
		$reader          = $this->_getCustomerCountryZoneReader();
		$customerFactory = $this->_getCustomerFactory();
		$repo            = MainFactory::create('CustomerCountryZoneRepository', $reader, $customerFactory);

		return $repo;
	}


	/**
	 * Creates a customer address repository object.
	 *
	 * @return CustomerAddressRepository Customer address repository.
	 */
	protected function _getCustomerAddressRepository()
	{
		$writer     = $this->_getCustomerAddressWriter();
		$reader     = $this->_getCustomerAddressReader();
		$deleter    = $this->_getCustomerAddressDeleter();
		$factory    = $this->_getCustomerFactory();
		$repository = MainFactory::create('CustomerAddressRepository', $writer, $deleter, $reader, $factory);

		return $repository;
	}


	/**
	 * Returns customer input validator service by validator name.
	 *
	 * @param string $inputValidatorName Name of input validator service.
	 *
	 * @return object Found customer input validator service.
	 */
	protected function _getCustomerInputValidatorServiceByValidatorName($inputValidatorName)
	{
		$customerService    = $this->getCustomerService();
		$countryService     = $this->getCountryService();
		$settings           = MainFactory::create('CustomerInputValidatorSettings');
		$countryRepo        = $this->_getCustomerCountryRepository();
		$countryZoneRepo    = $this->_getCustomerCountryZoneRepository();
		$vatNumberValidator = MainFactory::create('VatNumberValidator');

		$validator = MainFactory::create($inputValidatorName, $customerService, $countryService, $settings,
		                                 $countryRepo, $countryZoneRepo, $vatNumberValidator);

		return $validator;
	}


	/**
	 * Creates a customer address deleter object.
	 *
	 * @return CustomerAddressDeleter Customer address deleter.
	 */
	protected function _getCustomerAddressDeleter()
	{
		$deleter = MainFactory::create('CustomerAddressDeleter', $this->getDatabaseQueryBuilder());

		return $deleter;
	}
	

	/**
	 * Creates a customer address reader object.
	 *
	 * @return CustomerAddressReader Customer address reader.
	 */
	protected function _getCustomerAddressReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$countryService  = $this->getCountryService();

		$reader = MainFactory::create('CustomerAddressReader', $customerFactory, $countryService,
		                              $this->getDatabaseQueryBuilder());

		return $reader;
	}


	/**
	 * Creates a customer country zone reader object.
	 *
	 * @return CustomerCountryZoneReader Customer country zone reader.
	 */
	protected function _getCustomerCountryZoneReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$reader          = MainFactory::create('CustomerCountryZoneReader', $customerFactory,
		                                       $this->getDatabaseQueryBuilder());

		return $reader;
	}


	/**
	 * Creates a customer country reader object
	 *
	 * @return CustomerCountryReader Customer country reader.
	 */
	protected function _getCustomerCountryReader()
	{
		$customerFactory = $this->_getCustomerFactory();
		$reader          = MainFactory::create('CustomerCountryReader', $customerFactory,
		                                       $this->getDatabaseQueryBuilder());

		return $reader;
	}


	/**
	 * Creates a customer writer object.
	 *
	 * @return CustomerWriter Customer writer.
	 */
	protected function _getCustomerWriter()
	{
		$customerWriter = MainFactory::create('CustomerWriter', $this->getDatabaseQueryBuilder());

		return $customerWriter;
	}


	/**
	 * Creates a customer reader object.
	 *
	 * @return CustomerReader Customer reader.
	 */
	protected function _getCustomerReader()
	{
		$customerFactory           = $this->_getCustomerFactory();
		$customerAddressRepository = $this->_getCustomerAddressRepository();
		$dbQueryBuilder            = $this->getDatabaseQueryBuilder();

		$customerReader = MainFactory::create('CustomerReader', $customerFactory, $customerAddressRepository,
		                                      $dbQueryBuilder);

		return $customerReader;
	}
	

	/**
	 * Creates a customer deleter object.
	 *
	 * @return CustomerDeleter Customer deleter.
	 */
	protected function _getCustomerDeleter()
	{
		$customerDeleter = MainFactory::create('CustomerDeleter', $this->getDatabaseQueryBuilder());

		return $customerDeleter;
	}
	

	/**
	 * Creates a customer service settings object.
	 *
	 * @return CustomerServiceSettings Customer service settings.
	 */
	protected function _getCustomerServiceSettings()
	{
		$settings = MainFactory::create('CustomerServiceSettings');

		return $settings;
	}


	/**
	 * Creates a customer address writer object.
	 *
	 * @return CustomerAddressWriter Customer address writer.
	 */
	protected function _getCustomerAddressWriter()
	{
		$writer = MainFactory::create('CustomerAddressWriter', $this->getDatabaseQueryBuilder());

		return $writer;
	}
	
	
	protected function _getAddonValueService()
	{
		$addonValueStorageFactory = MainFactory::create('AddonValueStorageFactory', $this->getDatabaseQueryBuilder());
		$addonValueService        = MainFactory::create('AddonValueService', $addonValueStorageFactory);
		
		return $addonValueService;
	}
} 