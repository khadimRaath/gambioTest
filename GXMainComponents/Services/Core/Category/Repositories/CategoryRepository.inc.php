<?php

/* --------------------------------------------------------------
   CategoryRepository.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryRepository
 *
 * This class handles the database operations that concern the category records of the
 * database. It provides a layer for more complicated methods that use the writer, reader and deleter.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategoryRepository implements CategoryRepositoryInterface
{
	/**
	 * Category repository reader.
	 * @var CategoryRepositoryReaderInterface
	 */
	protected $reader;
	
	/**
	 * Category repository writer.
	 * @var CategoryRepositoryWriterInterface
	 */
	protected $writer;
	
	/**
	 * Category repository deleter.
	 * @var CategoryRepositoryDeleterInterface
	 */
	protected $deleter;
	
	/**
	 * Category settings repository.
	 * @var CategorySettingsRepositoryInterface
	 */
	protected $settingsRepo;
	
	/**
	 * Addon value service.
	 * @var AddonValueServiceInterface
	 */
	protected $addonValueService;
	
	/**
	 * Customer Status Provider
	 * @var CustomerStatusProviderInterface
	 */
	protected $customerStatusProvider;
	
	/**
	 * @var UrlRewriteStorage
	 */
	protected $urlRewriteStorage;
	
	
	/**
	 * Initialize the category repository.
	 *
	 * @param CategoryRepositoryReaderInterface   $reader                 Reader instance to fetch category data from
	 *                                                                    the storage.
	 * @param CategoryRepositoryWriterInterface   $writer                 Writer instance to store or add category data
	 *                                                                    in the storage.
	 * @param CategoryRepositoryDeleterInterface  $deleter                Deleter instance to remove category data from
	 *                                                                    the storage.
	 * @param CategorySettingsRepositoryInterface $settingsRepo           Category setting repository to
	 *                                                                    save/add/remove
	 *                                                                    category settings in the storage.
	 * @param AddonValueServiceInterface          $addonValueService      Addon value service instance to handle the
	 *                                                                    category addon values.
	 * @param CustomerStatusProviderInterface     $customerStatusProvider Customer status provider to handle group
	 *                                                                    permissions
	 * @param UrlRewriteStorage                   $urlRewriteStorage      Url rewrite storage.
	 */
	public function __construct(CategoryRepositoryReaderInterface $reader,
	                            CategoryRepositoryWriterInterface $writer,
	                            CategoryRepositoryDeleterInterface $deleter,
	                            CategorySettingsRepositoryInterface $settingsRepo,
	                            AddonValueServiceInterface $addonValueService,
	                            CustomerStatusProviderInterface $customerStatusProvider,
	                            UrlRewriteStorage $urlRewriteStorage)
	{
		$this->reader                 = $reader;
		$this->writer                 = $writer;
		$this->deleter                = $deleter;
		$this->settingsRepo           = $settingsRepo;
		$this->addonValueService      = $addonValueService;
		$this->customerStatusProvider = $customerStatusProvider;
		$this->urlRewriteStorage      = $urlRewriteStorage;
	}
	
	
	/**
	 * Adds a category.
	 *
	 * @param CategoryInterface $category Category.
	 *
	 * @return int Stored ID of the passed category.
	 */
	public function add(CategoryInterface $category)
	{
		$catId       = $this->writer->insert($category);
		$categoryId  = MainFactory::create('IdType', $catId);
		
		$storedCategory = $this->reader->getById($categoryId);
		
		$storedCategory->addAddonValues($category->getAddonValues());
		$this->addonValueService->storeAddonValues($storedCategory);
		
		$storedCategory->setSettings($category->getSettings());
		$this->settingsRepo->store($categoryId, $storedCategory->getSettings());
		$this->urlRewriteStorage->set($categoryId, $category->getUrlRewrites());
		
		return $catId;
	}
	
	
	/**
	 * Stores a category.
	 *
	 * @param StoredCategoryInterface $category Category.
	 *
	 * @return CategoryRepository Same instance for chained method calls.
	 */
	public function store(StoredCategoryInterface $category)
	{
		$category->setLastModifiedDateTime(new DateTime());
		
		$this->writer->update($category);
		$categoryId = MainFactory::create('IdType', $category->getCategoryId());
		
		$this->settingsRepo->store($categoryId, $category->getSettings());
		$this->addonValueService->storeAddonValues($category);
		$this->urlRewriteStorage->set($categoryId, $category->getUrlRewrites());
		
		return $this;
	}
	
	
	/**
	 * Gets a category by the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return StoredCategoryInterface
	 */
	public function getCategoryById(IdType $categoryId)
	{
		$category = $this->reader->getById($categoryId);
		$category->setSettings($this->settingsRepo->getCategorySettingsById($categoryId));
		$this->addonValueService->loadAddonValues($category);
		$category->setUrlRewrites($this->urlRewriteStorage->get($categoryId));
		
		return $category;
	}
	
	
	/**
	 * Returns all Categories with the provided parent ID.
	 *
	 * @param IdType $parentId
	 *
	 * @return IdCollection
	 */
	public function getCategoryIdsByParentId(IdType $parentId)
	{
		return $this->reader->getByParentId($parentId);
	}
	
	
	/**
	 * Deletes a category by the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategoryRepository Same instance for chained method calls.
	 */
	public function deleteCategoryById(IdType $categoryId)
	{
		$category = $this->reader->getById($categoryId);
		$this->addonValueService->deleteAddonValues($category);
		$this->urlRewriteStorage->delete($categoryId);
		
		$this->deleter->deleteById($categoryId);
		
		return $this;
	}
}