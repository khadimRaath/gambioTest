<?php
/* --------------------------------------------------------------
   GXCoreLoader.inc.php 2016-07-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('GXCoreLoaderInterface');

/**
 * Class GXCoreLoader
 *
 * @category    System
 * @package     Loaders
 * @subpackage  GXCoreLoader
 */
class GXCoreLoader implements GXCoreLoaderInterface
{
	/**
	 * Contains the loader settings.
	 *
	 * @var GXCoreLoaderSettingsInterface
	 */
	protected $gxCoreLoaderSettings;
	
	/**
	 * Database Layer Object
	 *
	 * @var CI_DB_query_builder
	 */
	protected $ciDatabaseQueryBuilder;
	
	/**
	 * Database Utility Helper
	 *
	 * @var CI_DB_utility
	 */
	protected $ciDatabaseUtilityHelper;
	
	/**
	 * Database Forge Helper
	 *
	 * @var CI_DB_forge
	 */
	protected $ciDatabaseForgeHelper;
	
	/**
	 * Factory for OrderService Objects
	 *
	 * @var AbstractOrderServiceFactory
	 */
	protected $orderServiceFactory;
	
	/**
	 * Factory to create objects of the customer service.
	 *
	 * @var CustomerServiceFactory
	 */
	protected $customerServiceFactory;
	
	/**
	 * Factory to create objects of the email service.
	 *
	 * @var EmailFactory
	 */
	protected $emailFactory;
	
	/**
	 * Factory to create objects of the category service.
	 *
	 * @var CategoryServiceFactory
	 */
	protected $categoryServiceFactory;
	
	/**
	 * Factory to create objects of the product service.
	 *
	 * @var ProductServiceFactory
	 */
	protected $productServiceFactory;
	
	/**
	 * Factory to create objects of the addon value service.
	 *
	 * @var AddonValueServiceFactory
	 */
	protected $addonValueServiceFactory;
	
	/**
	 * Factory to create objects of the invoice service.
	 *
	 * @var InvoiceServiceFactory
	 */
	protected $invoiceServiceFactory;
	
	
	/**
	 * Class Constructor
	 *
	 * @param GXCoreLoaderSettingsInterface $gxCoreLoaderSettings
	 */
	public function __construct(GXCoreLoaderSettingsInterface $gxCoreLoaderSettings)
	{
		$this->gxCoreLoaderSettings = $gxCoreLoaderSettings;
	}
	
	
	/**
	 * Get the requested server object.
	 *
	 * @param string $serviceName
	 *
	 * @return AddressBookServiceInterface|CountryServiceInterface|CustomerServiceInterface
	 *
	 * @throws DomainException
	 *
	 * @todo Delegate to GXServiceFactory
	 */
	public function getService($serviceName)
	{
		switch($serviceName)
		{
			case 'Customer': // DEPRECATED!!
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$customerService        = $customerServiceFactory->getCustomerService();
				
				return $customerService;
				break;
			case 'CustomerRead':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$customerReadService    = $customerServiceFactory->createCustomerReadService();
				
				return $customerReadService;
				break;
			case 'CustomerWrite':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$customerWriteService   = $customerServiceFactory->createCustomerWriteService();
				
				return $customerWriteService;
				break;
			case 'AddressBook':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$addressBookService     = $customerServiceFactory->getAddressBookService();
				
				return $addressBookService;
				break;
			case 'Country':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$countryService         = $customerServiceFactory->getCountryService();
				
				return $countryService;
				break;
			case 'RegistrationInputValidator':
				$customerServiceFactory     = $this->_getCustomerServiceFactory();
				$registrationInputValidator = $customerServiceFactory->getCustomerRegistrationInputValidatorService();
				
				return $registrationInputValidator;
				break;
			case 'AccountInputValidator':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$accountInputValidator  = $customerServiceFactory->getCustomerAccountInputValidator();
				
				return $accountInputValidator;
				break;
			case 'AddressInputValidator':
				$customerServiceFactory = $this->_getCustomerServiceFactory();
				$accountInputValidator  = $customerServiceFactory->getCustomerAddressInputValidatorService();
				
				return $accountInputValidator;
				break;
			case 'UserConfiguration':
				$db                       = $this->getDatabaseQueryBuilder();
				$userConfigurationReader  = MainFactory::create('UserConfigurationReader', $db);
				$userConfigurationWriter  = MainFactory::create('UserConfigurationWriter', $db);
				$userConfigurationService = MainFactory::create('UserConfigurationService', $userConfigurationReader,
				                                                $userConfigurationWriter);
				
				return $userConfigurationService;
				break;
			case 'Statistics':
				$db                = $this->getDatabaseQueryBuilder();
				$xtcPrice          = new xtcPrice($_SESSION['currency'],
				                                  $_SESSION['customers_status']['customers_status_id']);
				$statisticsService = MainFactory::create('StatisticsService', $db, $xtcPrice);
				
				return $statisticsService;
				break;
			case 'Email':
				$emailFactory = $this->_getEmailFactory();
				
				return $emailFactory->createService();
				break;
			case 'OrderObject':
				$factory = $this->_getOrderServiceFactory();
				
				return $factory->createOrderObjectService();
				break;
			case 'OrderRead':
				$factory = $this->_getOrderServiceFactory();
				
				return $factory->createOrderReadService();
				break;
			case 'OrderWrite':
				$factory = $this->_getOrderServiceFactory();
				
				return $factory->createOrderWriteService();
				break;
			case 'Http':
				$httpServiceFactory = MainFactory::create('HttpServiceFactory');
				
				return $httpServiceFactory->createService();
				break;
			case 'CategoryRead':
				$factory = $this->_getCategoryServiceFactory();
				
				return $factory->createCategoryReadService();
				break;
			case 'CategoryWrite':
				$factory = $this->_getCategoryServiceFactory();
				
				return $factory->createCategoryWriteService();
				break;
			case 'CategoryObject':
				$factory = $this->_getCategoryServiceFactory();
				
				return $factory->createCategoryObjectService();
				break;
			case 'AddonValue':
				$factory = $this->_getAddonValueServiceFactory();
				
				return $factory->createAddonValueService();
				break;
			case 'ProductRead':
				$factory = $this->_getProductServiceFactory();
				
				return $factory->createProductReadService();
				break;
			case 'ProductWrite':
				$factory = $this->_getProductServiceFactory();
				
				return $factory->createProductWriteService();
				break;
			case 'ProductObject':
				$factory = $this->_getProductServiceFactory();
				
				return $factory->createProductObjectService();
				break;
			case 'SharedShoppingCart':
				$db                           = $this->getDatabaseQueryBuilder();
				$sharedShoppingCartReader     = MainFactory::create('SharedShoppingCartReader', $db);
				$sharedShoppingCartWriter     = MainFactory::create('SharedShoppingCartWriter', $db);
				$sharedShoppingCartDeleter    = MainFactory::create('SharedShoppingCartDeleter', $db);
				$sharedShoppingCartRepository = MainFactory::create('SharedShoppingCartRepository',
				                                                    $sharedShoppingCartReader,
				                                                    $sharedShoppingCartWriter,
				                                                    $sharedShoppingCartDeleter);
				$sharedShoppingCartSettings   = MainFactory::create('SharedShoppingCartSettings');
				
				$sharedShoppingCartService = MainFactory::create('SharedShoppingCartService',
				                                                 $sharedShoppingCartRepository,
				                                                 $sharedShoppingCartSettings);
				
				return $sharedShoppingCartService;
				break;
			case 'InfoBox':
				$db             = $this->getDatabaseQueryBuilder();
				$infoBoxReader  = MainFactory::create('InfoBoxReader', $db);
				$infoBoxWriter  = MainFactory::create('InfoBoxWriter', $db);
				$infoBoxDeleter = MainFactory::create('InfoBoxDeleter', $db);
				$infoBoxService = MainFactory::create('InfoBoxService', $infoBoxReader, $infoBoxWriter,
				                                      $infoBoxDeleter);
				
				return $infoBoxService;
				break;
			case 'InvoiceArchiveRead':
				$invoiceServiceFactory = $this->_getInvoiceServiceFactory();
				
				return $invoiceServiceFactory->createInvoiceArchiveReadService();
				breaK;
			case 'InvoiceArchiveWrite':
				$invoiceServiceFactory = $this->_getInvoiceServiceFactory();
				
				return $invoiceServiceFactory->createInvoiceArchiveWriteService();
				breaK;
			default:
				throw new DomainException('Unknown service: ' . htmlentities($serviceName));
		}
	}
	
	
	/**
	 * Method depends on CodeIgniter database library
	 *
	 * @return CI_DB_query_builder
	 *
	 * @todo check connection errors
	 * @todo escape special characters in mysqli connection string (AT)
	 * @todo use GXDatabaseAccessorInterface
	 */
	public function getDatabaseQueryBuilder()
	{
		if($this->ciDatabaseQueryBuilder !== null)
		{
			return $this->ciDatabaseQueryBuilder;
		}
		
		$connectionString = $this->_getDatabaseConnectionString();
		
		$this->ciDatabaseQueryBuilder = CIDB($connectionString);
		
		// @todo Remove the following block when the shop is totally ready for MySQL strict mode.
		if(is_object($GLOBALS['coo_debugger'])
		   && $GLOBALS['coo_debugger']->is_enabled('enable_mysql_strict_mode')
		)
		{
			$this->ciDatabaseQueryBuilder->query('SET SESSION sql_mode = "ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,'
			                                     . 'NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,'
			                                     . 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"');
		}
		else
		{
			$this->ciDatabaseQueryBuilder->query('SET SESSION sql_mode = ""');
		}
		
		return $this->ciDatabaseQueryBuilder;
	}
	
	
	/**
	 * Method depends on CodeIgniter database library.
	 *
	 * @return CI_DB_utility
	 */
	public function getDatabaseUtilityHelper()
	{
		if($this->ciDatabaseUtilityHelper !== null)
		{
			return $this->ciDatabaseUtilityHelper;
		}
		
		$connectionString = $this->_getDatabaseConnectionString(); 
		
		$this->ciDatabaseUtilityHelper = CIDBUtils($connectionString); 
		
		return $this->ciDatabaseUtilityHelper;
	}
	
	
	/**
	 * Method depends on CodeIgniter database library.
	 *
	 * @return CI_DB_forge
	 */
	public function getDatabaseForgeHelper()
	{
		if($this->ciDatabaseForgeHelper !== null)
		{
			return $this->ciDatabaseForgeHelper;
		}
		
		$connectionString = $this->_getDatabaseConnectionString();
		
		$this->ciDatabaseForgeHelper = CIDBForge($connectionString);
		
		return $this->ciDatabaseForgeHelper;
	}
	
	
	/**
	 * Get connection string for CodeIgniter libraries.
	 * 
	 * @return string
	 */
	protected function _getDatabaseConnectionString()
	{
		$dbUser     = $this->gxCoreLoaderSettings->getDatabaseUser();
		$dbPassword = $this->gxCoreLoaderSettings->getDatabasePassword();
		$dbServer   = $this->gxCoreLoaderSettings->getDatabaseServer();
		$dbName     = $this->gxCoreLoaderSettings->getDatabaseName();
		$dbSocket   = $this->gxCoreLoaderSettings->getDatabaseSocket() ? '?socket='
		                                                                   . $this->gxCoreLoaderSettings->getDatabaseSocket() : '';
		
		$connectionString = 'mysqli://' . $dbUser . ':' . $dbPassword . '@' . $dbServer . '/' . $dbName . $dbSocket;
		
		return $connectionString;
	}
	
	
	/**
	 * Get a customer service factory object.
	 *
	 * @return CustomerServiceFactory
	 */
	protected function _getCustomerServiceFactory()
	{
		if(null === $this->customerServiceFactory)
		{
			$ciDatabaseQueryBuilder       = $this->getDatabaseQueryBuilder();
			$this->customerServiceFactory = MainFactory::create('CustomerServiceFactory', $ciDatabaseQueryBuilder);
		}
		
		return $this->customerServiceFactory;
	}
	
	
	/**
	 * Get an email factory object.
	 *
	 * @return EmailFactory
	 */
	protected function _getEmailFactory()
	{
		if(null === $this->emailFactory)
		{
			$db                 = $this->getDatabaseQueryBuilder();
			$this->emailFactory = MainFactory::create('EmailFactory', $db);
		}
		
		return $this->emailFactory;
	}
	
	
	/**
	 * Get an order service factory object.
	 *
	 * @return AbstractOrderServiceFactory
	 */
	protected function _getOrderServiceFactory()
	{
		if($this->orderServiceFactory === null)
		{
			$db                        = $this->getDatabaseQueryBuilder();
			$this->orderServiceFactory = MainFactory::create('OrderServiceFactory', $db);
		}
		
		return $this->orderServiceFactory;
	}
	
	
	/**
	 * Get a category service factory.
	 *
	 * @return CategoryServiceFactory
	 */
	protected function _getCategoryServiceFactory()
	{
		if(null === $this->categoryServiceFactory)
		{
			$db                           = $this->getDatabaseQueryBuilder();
			$settings                     = MainFactory::create('EnvCategoryServiceSettings');
			$this->categoryServiceFactory = MainFactory::create('CategoryServiceFactory', $db, $settings);
		}
		
		return $this->categoryServiceFactory;
	}
	
	
	/**
	 * Get a product service factory
	 *
	 * @return ProductServiceFactory
	 */
	protected function _getProductServiceFactory()
	{
		if(null === $this->productServiceFactory)
		{
			$db                          = $this->getDatabaseQueryBuilder();
			$this->productServiceFactory = MainFactory::create('ProductServiceFactory', $db);
		}
		
		return $this->productServiceFactory;
	}
	
	
	/**
	 * Get an addon value service factory.
	 *
	 * @return AddonValueServiceFactory
	 */
	protected function _getAddonValueServiceFactory()
	{
		if(null === $this->addonValueServiceFactory)
		{
			$db                             = $this->getDatabaseQueryBuilder();
			$this->addonValueServiceFactory = MainFactory::create('AddonValueServiceFactory', $db);
		}
		
		return $this->addonValueServiceFactory;
	}
	
	
	/**
	 * Returns the invoice service factory to create objects of the invoice service.
	 *
	 * @return \InvoiceServiceFactory
	 */
	protected function _getInvoiceServiceFactory()
	{
		if(null === $this->invoiceServiceFactory)
		{
			$db                          = $this->getDatabaseQueryBuilder();
			$this->invoiceServiceFactory = MainFactory::create('InvoiceServiceFactory', $db);
		}
		
		return $this->invoiceServiceFactory;
	}
}
