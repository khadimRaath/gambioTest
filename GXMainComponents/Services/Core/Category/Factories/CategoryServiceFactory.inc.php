<?php
/* --------------------------------------------------------------
   CategoryServiceFactory.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryServiceFactory
 * 
 * This class provides methods for creating the objects of the public category service api with its dependencies.
 *
 * @category   System
 * @package    Category
 * @subpackage Factories
 */
class CategoryServiceFactory extends AbstractCategoryServiceFactory
{
	/**
	 * Database connector.
	 * 
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Category service settings.
	 * 
	 * @var CategoryServiceSettingsInterface
	 */
	protected $settings;
	
	
	/**
	 * CategoryServiceFactory constructor.
	 *
	 * @param CI_DB_query_builder              $db       Database connector.
	 * @param CategoryServiceSettingsInterface $settings Category service settings.
	 */
	public function __construct(CI_DB_query_builder $db, CategoryServiceSettingsInterface $settings)
	{
		$this->db       = $db;
		$this->settings = $settings;
	}
	
	
	/**
	 * Creates a category object service.
	 * 
	 * @return CategoryObjectServiceInterface
	 */
	public function createCategoryObjectService()
	{
		return MainFactory::create('CategoryObjectService', MainFactory::create('CategoryFactory'));
	}
	
	
	/**
	 * Creates a category read service.
	 * 
	 * @return CategoryReadServiceInterface
	 */
	public function createCategoryReadService()
	{
		// Create CategoryRepository instance.
		$languageProvider = MainFactory::create('LanguageProvider', $this->db);
		
		$urlRewriteStorageContentType = new NonEmptyStringType('category');
		$urlRewriteStorage            = MainFactory::create('UrlRewriteStorage', $urlRewriteStorageContentType,
		                                                    $this->db, $languageProvider);
		
		$addonValueService                = MainFactory::create('AddonValueService',
		                                                        MainFactory::create('AddonValueStorageFactory',
		                                                                            $this->db));
		$customerStatusProvider           = MainFactory::create('CustomerStatusProvider', $this->db);
		$categoryRepositoryReader         = MainFactory::create('CategoryRepositoryReader', $this->db,
		                                                        MainFactory::create('CategoryFactory'));
		$categoryRepositoryWriter         = MainFactory::create('CategoryRepositoryWriter', $this->db,
		                                                        $languageProvider);
		$categoryRepositoryDeleter        = MainFactory::create('CategoryRepositoryDeleter', $this->db);
		$categorySettingsRepositoryReader = MainFactory::create('CategorySettingsRepositoryReader', $this->db,
		                                                        MainFactory::create('CategoryFactory'),
		                                                        $customerStatusProvider);
		$categorySettingsRepositoryWriter = MainFactory::create('CategorySettingsRepositoryWriter', $this->db,
		                                                        $customerStatusProvider);
		$categorySettingsRepository       = MainFactory::create('CategorySettingsRepository',
		                                                        $categorySettingsRepositoryReader,
		                                                        $categorySettingsRepositoryWriter);
		$categoryRepository               = MainFactory::create('CategoryRepository', $categoryRepositoryReader,
		                                                        $categoryRepositoryWriter, $categoryRepositoryDeleter,
		                                                        $categorySettingsRepository, $addonValueService,
		                                                        $customerStatusProvider, $urlRewriteStorage);
		
		// Create CategoryListProviderFactory instance.
		$categoryListProviderFactory = MainFactory::create('CategoryListProviderFactory', $categoryRepository,
		                                                   $this->db);
		
		// Create CategoryReadService instance and return it.
		return MainFactory::create('CategoryReadService', $categoryRepository, $categoryListProviderFactory,
		                           $urlRewriteStorage);
	}
	
	
	/**
	 * Creates a category write service.
	 * 
	 * @return CategoryWriteServiceInterface
	 */
	public function createCategoryWriteService()
	{
		// Create CategoryRepository instance.
		$languageProvider = MainFactory::create('LanguageProvider', $this->db);
		
		$urlRewriteStorageContentType = new NonEmptyStringType('category');
		$urlRewriteStorage            = MainFactory::create('UrlRewriteStorage', $urlRewriteStorageContentType,
		                                                    $this->db, $languageProvider);
		
		$addonValueService                = MainFactory::create('AddonValueService',
		                                                        MainFactory::create('AddonValueStorageFactory',
		                                                                            $this->db));
		$customerStatusProvider           = MainFactory::create('CustomerStatusProvider', $this->db);
		$categoryRepositoryReader         = MainFactory::create('CategoryRepositoryReader', $this->db,
		                                                        MainFactory::create('CategoryFactory'));
		$categoryRepositoryWriter         = MainFactory::create('CategoryRepositoryWriter', $this->db,
		                                                        $languageProvider);
		$categoryRepositoryDeleter        = MainFactory::create('CategoryRepositoryDeleter', $this->db);
		$categorySettingsRepositoryReader = MainFactory::create('CategorySettingsRepositoryReader', $this->db,
		                                                        MainFactory::create('CategoryFactory'),
		                                                        $customerStatusProvider);
		$categorySettingsRepositoryWriter = MainFactory::create('CategorySettingsRepositoryWriter', $this->db,
		                                                        $customerStatusProvider);
		$categorySettingsRepository       = MainFactory::create('CategorySettingsRepository',
		                                                        $categorySettingsRepositoryReader,
		                                                        $categorySettingsRepositoryWriter);
		$categoryRepository               = MainFactory::create('CategoryRepository', $categoryRepositoryReader,
		                                                        $categoryRepositoryWriter, $categoryRepositoryDeleter,
		                                                        $categorySettingsRepository, $addonValueService,
		                                                        $customerStatusProvider, $urlRewriteStorage);
		
		// Create ImageFileStorage instances.
		$imageDirPath = $this->settings->getImagesDirPath() . 'categories';
		$iconDirPath  = $imageDirPath . DIRECTORY_SEPARATOR . 'icons';
		
		$categoryImageStorage = MainFactory::create('ImageFileStorage',
		                                            MainFactory::create('WritableDirectory', $imageDirPath));
		$categoryIconStorage  = MainFactory::create('ImageFileStorage',
		                                            MainFactory::create('WritableDirectory', $iconDirPath));
		
		$productPermissionSetter = MainFactory::create('ProductPermissionSetter', $this->db);
		
		$urlKeywordsRepairer = MainFactory::create_object('GMSEOBoost', array(), true);
		
		return MainFactory::create('CategoryWriteService', $categoryRepository, $categoryImageStorage,
		                           $categoryIconStorage, $productPermissionSetter, $urlKeywordsRepairer);
	}
}