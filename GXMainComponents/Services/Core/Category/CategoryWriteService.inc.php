<?php
/* --------------------------------------------------------------
   CategoryWriteService.inc.php 2016-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryWriteService
 * 
 * This class provides methods for creating, updating and deleting categories data.
 *
 * @category   System
 * @package    Category
 * @implements CategoryWriteServiceInterface
 */
class CategoryWriteService implements CategoryWriteServiceInterface
{
	/**
	 * Category repository.
	 * 
	 * @var CategoryRepositoryInterface
	 */
	protected $categoryRepo;

	/**
	 * Category image.
	 * 
	 * @var AbstractFileStorage
	 */
	protected $categoryImageStorage;

	/**
	 * Category icon.
	 * 
	 * @var AbstractFileStorage
	 */
	protected $categoryIconStorage;
	
	/**
	 * ProductPermissionSetter
	 * 
	 * @var ProductPermissionSetterInterface
	 */
	protected $productPermissionSetter;
	
	/**
	 * Used for writing and repairing category's url keywords
	 *
	 * @var UrlKeywordsRepairerInterface
	 */
	protected $urlKeywordsRepairer;


	/**
	 * CategoryWriteService constructor.
	 *
	 * @param CategoryRepositoryInterface      $categoryRepo            Category repository.
	 * @param AbstractFileStorage              $categoryImageStorage    Category image.
	 * @param AbstractFileStorage              $categoryIconStorage     Category icon.
	 * @param ProductPermissionSetterInterface $productPermissionSetter ProductPermissionSetter.
	 * @param UrlKeywordsRepairerInterface    $urlKeywordsRepairer
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepo,
	                            AbstractFileStorage $categoryImageStorage,
	                            AbstractFileStorage $categoryIconStorage,
	                            ProductPermissionSetterInterface $productPermissionSetter,
	                            UrlKeywordsRepairerInterface $urlKeywordsRepairer)
	{
		$this->categoryRepo            = $categoryRepo;
		$this->categoryImageStorage    = $categoryImageStorage;
		$this->categoryIconStorage     = $categoryIconStorage;
		$this->productPermissionSetter = $productPermissionSetter;
		$this->urlKeywordsRepairer     = $urlKeywordsRepairer;
	}


	/**
	 * Stores a category in the database and returns the newly created ID of it.
	 *
	 * @param CategoryInterface $category The category to store.
	 *
	 * @return int Returns the ID of the new category record.
	 */
	public function createCategory(CategoryInterface $category)
	{
		$categoryId = $this->categoryRepo->add($category);
		
		// set url keywords
		$this->urlKeywordsRepairer->repair('categories');
		
		return $categoryId;
	}


	/**
	 * Updates the provided category and returns itself.
	 *
	 * @param StoredCategoryInterface $category The category to update.
	 *
	 * @return CategoryWriteService Same instance for chained method calls.
	 */
	public function updateCategory(StoredCategoryInterface $category)
	{
		$this->categoryRepo->store($category);
		
		// set url keywords
		$this->urlKeywordsRepairer->repair('categories');

		return $this;
	}


	/**
	 * Deletes a category depending on the provided category ID.
	 *
	 * @param IdType $categoryId Category ID of the category to delete.
	 *
	 * @return CategoryWriteService Same instance for chained method calls.
	 */
	public function deleteCategoryById(IdType $categoryId)
	{
		$category = $this->categoryRepo->getCategoryById($categoryId);
		
		$this->categoryIconStorage->deleteFile(new FilenameStringType($category->getIcon()));
		$this->categoryImageStorage->deleteFile(new FilenameStringType($category->getImage()));
		
		$this->categoryRepo->deleteCategoryById($categoryId);

		return $this;
	}
	
	
	/**
	 * Moves a category into another category.
	 *
	 * This method moves a category specified by its category ID into another parent category specified by its
	 * category ID.
	 *
	 * @param IdType $categoryId  Category ID of the category to move.
	 * @param IdType $newParentId The new parent ID.
	 *
	 * @return CategoryWriteService Same instance for chained method calls.
	 */
	public function moveCategory(IdType $categoryId, IdType $newParentId)
	{
		$storedCategory = $this->categoryRepo->getCategoryById($categoryId);
		$storedCategory->setParentId($newParentId);

		$this->categoryRepo->store($storedCategory);

		return $this;
	}
	
	
	/**
	 * Duplicates a category specified by its category ID.
	 *
	 * This method duplicates the category which are identified by the provided category ID and links the duplicated
	 * category with the provided parent category ID. Containing subcategories and products will also be recursively
	 * duplicated with their attributes, specials and cross selling data depending on the last four arguments.
	 *
	 * @param IdType   $categoryId            The category ID of the category to duplicate.
	 * @param IdType   $targetParentId        The target parent ID of the duplicated category.
	 * @param BoolType $duplicateProducts     Should the products be duplicated?
	 * @param BoolType $duplicateAttributes   Should the attributes be duplicated?
	 * @param BoolType $duplicateSpecials     Should the specials be duplicated?
	 * @param BoolType $duplicateCrossSelling Should cross selling be duplicated?
	 *
	 * @return int Returns the ID of the new category record.
	 *
	 * @todo Implement the last four arguments when finished in UML.
	 */
	public function duplicateCategory(IdType $categoryId,
	                                  IdType $targetParentId,
	                                  BoolType $duplicateProducts = null,
	                                  BoolType $duplicateAttributes = null,
	                                  BoolType $duplicateSpecials = null,
	                                  BoolType $duplicateCrossSelling = null)
	{
		$storedCategory = $this->categoryRepo->getCategoryById($categoryId);
		$storedCategory->setParentId($targetParentId);
		$newCategoryId = $this->categoryRepo->add($storedCategory);
		
		// set url keywords
		$this->urlKeywordsRepairer->repair('categories');

		return $newCategoryId;
	}


	/**
	 * Imports an image file and stores it.
	 *
	 * @param ExistingFile       $sourceFile     The image file to import.
	 * @param FilenameStringType $saveAsFilename The name under which the image should to be stored.
	 *
	 * @return string The new filename.
	 */
	public function importCategoryImageFile(ExistingFile $sourceFile, FilenameStringType $saveAsFilename)
	{
		return $this->categoryImageStorage->importFile($sourceFile, $saveAsFilename);
	}


	/**
	 * Imports an icon file and stores it.
	 *
	 * @param ExistingFile       $sourceFile     The icon file to import.
	 * @param FilenameStringType $saveAsFilename The name under which the icon should be stored.
	 *
	 * @return string The new filename.
	 */
	public function importCategoryIconFile(ExistingFile $sourceFile, FilenameStringType $saveAsFilename)
	{
		return $this->categoryIconStorage->importFile($sourceFile, $saveAsFilename);
	}


	/**
	 * Renames a category image file.
	 *
	 * @param FilenameStringType $oldName Old file name.
	 * @param FilenameStringType $newName New file name.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function renameCategoryImageFile(FilenameStringType $oldName, FilenameStringType $newName)
	{
		$this->categoryImageStorage->renameFile($oldName, $newName);

		return $this;
	}


	/**
	 * Renames a category icon file.
	 *
	 * @param FilenameStringType $oldName Old file name.
	 * @param FilenameStringType $newName New file name.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function renameCategoryIconFile(FilenameStringType $oldName, FilenameStringType $newName)
	{
		$this->categoryIconStorage->renameFile($oldName, $newName);

		return $this;
	}


	/**
	 * Deletes a category image file.
	 *
	 * @param FilenameStringType $filename Category image file name.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteCategoryImageFile(FilenameStringType $filename)
	{
		$this->categoryImageStorage->deleteFile($filename);

		return $this;
	}


	/**
	 * Deletes a category icon file.
	 *
	 * @param FilenameStringType $filename Category icon file name.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteCategoryIconFile(FilenameStringType $filename)
	{
		$this->categoryIconStorage->deleteFile($filename);

		return $this;
	}


	/**
	 * Activates a specific category and its subcategories if desired.
	 *
	 * @param IdType   $categoryId           Category ID of the category to activate.
	 * @param BoolType $includeSubcategories Shall the subcategories be activated also?
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function activateCategory(IdType $categoryId, BoolType $includeSubcategories)
	{
		$storedCategory = $this->categoryRepo->getCategoryById($categoryId);
		$storedCategory->setActive(new BoolType(true));
		$this->categoryRepo->store($storedCategory);
		
		if($includeSubcategories->asBool())
		{
			foreach($this->categoryRepo->getCategoryIdsByParentId($categoryId)->getArray() as $subCategoryId)
			{
				$this->activateCategory($subCategoryId, $includeSubcategories);
			}
		}
		
		return $this;
	}


	/**
	 * Deactivates a specific category and its subcategories if desired.
	 *
	 * @param IdType   $categoryId           Category ID of the category to deactivate.
	 * @param BoolType $includeSubcategories Shall the subcategories be deactivated also?
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deactivateCategory(IdType $categoryId, BoolType $includeSubcategories)
	{
		$storedCategory = $this->categoryRepo->getCategoryById($categoryId);
		$storedCategory->setActive(new BoolType(false));
		$this->categoryRepo->store($storedCategory);
		
		if($includeSubcategories->asBool())
		{
			foreach($this->categoryRepo->getCategoryIdsByParentId($categoryId)->getArray() as $subCategoryId)
			{
				$this->deactivateCategory($subCategoryId, $includeSubcategories);
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Sets the customer status permissions.
	 *
	 * The customer status permissions decides if the category is visible for a specific customer group. The
	 * permissions can be applied for subcategories also if desired.
	 *
	 * @param IdType   $categoryId                      Category ID.
	 * @param IdType   $customerStatusId                Customer status ID.
	 * @param BoolType $permitted                       Grant permission?
	 * @param BoolType $includeSubcategoriesAndProducts Grant permission including subcategories?
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function setCustomerStatusPermission(IdType $categoryId,
	                                            IdType $customerStatusId,
	                                            BoolType $permitted,
	                                            BoolType $includeSubcategoriesAndProducts)
	{
		$storedCategory         = $this->categoryRepo->getCategoryById($categoryId);
		$storedCategorySettings = $storedCategory->getSettings();
		$storedCategorySettings->setPermittedCustomerStatus($customerStatusId, $permitted);
		$this->categoryRepo->store($storedCategory);
		
		if($includeSubcategoriesAndProducts->asBool())
		{
			$this->productPermissionSetter->setProductsPermissionByCategoryId($categoryId, $customerStatusId,
			                                                                  $permitted);
			
			foreach($this->categoryRepo->getCategoryIdsByParentId($categoryId)->getArray() as $subCategoryId)
			{
				$this->setCustomerStatusPermission($subCategoryId, $customerStatusId, $permitted,
				                                   $includeSubcategoriesAndProducts);
			}
		}
		
		return $this;
	}
}
