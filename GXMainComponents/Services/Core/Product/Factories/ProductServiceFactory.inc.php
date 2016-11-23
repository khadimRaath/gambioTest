<?php
/* --------------------------------------------------------------
   ProductServiceFactory.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductServiceFactory
 *
 * @category   System
 * @package    Product
 * @subpackage Factories
 */
class ProductServiceFactory extends AbstractProductServiceFactory
{
	/**
	 * Database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * ProductServiceFactory constructor.
	 *
	 * @param CI_DB_query_builder $db Database connection.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Creates a product object service.
	 *
	 * @return ProductObjectServiceInterface
	 */
	public function createProductObjectService()
	{
		$productFactory = MainFactory::create('ProductFactory');

		return MainFactory::create('ProductObjectService', $productFactory);
	}


	/**
	 * Creates a product read service.
	 *
	 * @return ProductReadServiceInterface
	 */
	public function createProductReadService()
	{
		$productRepository = $this->_getProductRepository();

		$productListProviderFactory = MainFactory::create('ProductListProviderFactory', $productRepository, $this->db);
		
		$productCategoryLinker = MainFactory::create('ProductCategoryLinker', $this->db);
		
		$languageProvider             = MainFactory::create('LanguageProvider', $this->db);
		$urlRewriteStorageContentType = new NonEmptyStringType('product');
		$urlRewriteStorage            = MainFactory::create('UrlRewriteStorage', $urlRewriteStorageContentType,
		                                                    $this->db, $languageProvider);
		
		return MainFactory::create('ProductReadService', $productRepository, $productListProviderFactory,
		                           $productCategoryLinker, $urlRewriteStorage);
	}


	/**
	 * Creates a product write service.
	 *
	 * @return ProductWriteServiceInterface
	 */
	public function createProductWriteService()
	{
		$productRepository = $this->_getProductRepository();
		
		$productImageFileStorage = $this->_getProductImageFileStorage();
		
		$productCategoryLinker = MainFactory::create('ProductCategoryLinker', $this->db);
		
		$envProductImagePathsSettings = MainFactory::create('EnvProductImageFileStorageSettings');
		
		$languageProvider = MainFactory::create('LanguageProvider', $this->db);
		
		$urlKeywordsRepairer = MainFactory::create_object('GMSEOBoost', array(), true);
		
		return MainFactory::create('ProductWriteService', $productRepository, $productImageFileStorage,
		                           $productCategoryLinker, $envProductImagePathsSettings, $languageProvider, 
		                           $urlKeywordsRepairer);
	}


	/**
	 * Creates a product repository
	 *
	 * @return ProductRepository
	 */
	protected function _getProductRepository()
	{
		$productFactory = MainFactory::create('ProductFactory');

		$customerStatusProvider = MainFactory::create('CustomerStatusProvider', $this->db);

		$languageProvider   = MainFactory::create('LanguageProvider', $this->db);
		$productRepoReader  = MainFactory::create('ProductRepositoryReader', $this->db, $productFactory,
		                                          $customerStatusProvider);
		$productRepoWriter  = MainFactory::create('ProductRepositoryWriter', $this->db, $languageProvider);
		$productRepoDeleter = MainFactory::create('ProductRepositoryDeleter', $this->db);

		$productSettingsRepoReader = MainFactory::create('ProductSettingsRepositoryReader', $this->db, $productFactory,
		                                                 $customerStatusProvider);
		$productSettingsRepoWriter = MainFactory::create('ProductSettingsRepositoryWriter', $this->db,
		                                                 $customerStatusProvider);

		$productSettingsRepo = MainFactory::create('ProductSettingsRepository', $productSettingsRepoReader,
		                                           $productSettingsRepoWriter);

		$addonValueStorageFactory = MainFactory::create('AddonValueStorageFactory', $this->db);

		$addonValueService = MainFactory::create('AddonValueService', $addonValueStorageFactory);

		$imageContainerRepo = MainFactory::create('ProductImageContainerRepository', $this->db, $languageProvider);
		
		$urlRewriteStorageContentType = new NonEmptyStringType('product');
		$urlRewriteStorage            = MainFactory::create('UrlRewriteStorage', $urlRewriteStorageContentType,
		                                                    $this->db, $languageProvider);

		$productRepo = MainFactory::create('ProductRepository', $productRepoReader, $productRepoWriter,
		                                   $productRepoDeleter, $productSettingsRepo, $addonValueService,
		                                   $imageContainerRepo, $urlRewriteStorage);

		return $productRepo;
	}
	
	
	/**
	 * Creates a ProductImageFileStorage
	 *
	 * @return ProductImageFileStorage
	 */
	protected function _getProductImageFileStorage()
	{
		$productImageFileSettings = MainFactory::create('EnvProductImageFileStorageSettings');
		$productImageProcessing   = MainFactory::create('LegacyProductImageProcessing');
		$productImageFileStorage  = MainFactory::create('ProductImageFileStorage', $productImageFileSettings,
		                                                $productImageProcessing);
		
		return $productImageFileStorage;
	}
}