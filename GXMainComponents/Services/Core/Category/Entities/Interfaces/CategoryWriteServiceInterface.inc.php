<?php

/* --------------------------------------------------------------
   CategoryWriteServiceInterface.inc.php 2016-02-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryWriteServiceInterface
 *
 * This interface defines methods for creating, updating and deleting categories data.
 * 
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryWriteServiceInterface
{
	/**
	 * Creates a category and returns the ID of it.
	 *
	 * @param CategoryInterface $category The category to create.
	 *
	 * @return int Returns the ID of the new category record.
	 */
	public function createCategory(CategoryInterface $category);


	/**
	 * Updates the provided category and returns itself.
	 *
	 * @param StoredCategoryInterface $category The category to update.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function updateCategory(StoredCategoryInterface $category);


	/**
	 * Deletes a category depending on the provided category ID.
	 *
	 * @param IdType $categoryId Category ID of the category to delete.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteCategoryById(IdType $categoryId);
	
	
	/**
	 * Moves a category into another category.
	 *
	 * This method moves a category specified by its category ID into another parent category specified by its
	 * category ID.
	 *
	 * @param IdType $categoryId  Category ID of the category to move.
	 * @param IdType $newParentId The new parent ID.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function moveCategory(IdType $categoryId, IdType $newParentId);
	
	
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
	 */
	public function duplicateCategory(IdType $categoryId,
	                                  IdType $targetParentId,
	                                  BoolType $duplicateProducts,
	                                  BoolType $duplicateAttributes,
	                                  BoolType $duplicateSpecials,
	                                  BoolType $duplicateCrossSelling);


	/**
	 * Imports an image file and stores it.
	 *
	 * @param ExistingFile       $sourceFile     The image file to import.
	 * @param FilenameStringType $saveAsFilename The name under which the image should to be stored.
	 *
	 * @return string The new filename.
	 */
	public function importCategoryImageFile(ExistingFile $sourceFile, FilenameStringType $saveAsFilename);


	/**
	 * Imports an icon file and stores it.
	 *
	 * @param ExistingFile       $sourceFile     The icon file to import.
	 * @param FilenameStringType $saveAsFilename The name under which the icon should be stored.
	 *
	 * @return string The new filename.
	 */
	public function importCategoryIconFile(ExistingFile $sourceFile, FilenameStringType $saveAsFilename);


	/**
	 * Renames a category image file.
	 *
	 * @param FilenameStringType $oldName Old filename.
	 * @param FilenameStringType $newName New filename.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function renameCategoryImageFile(FilenameStringType $oldName, FilenameStringType $newName);


	/**
	 * Renames a category icon file.
	 *
	 * @param FilenameStringType $oldName Old filename.
	 * @param FilenameStringType $newName New filename.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function renameCategoryIconFile(FilenameStringType $oldName, FilenameStringType $newName);


	/**
	 * Deletes a category image file.
	 *
	 * @param FilenameStringType $filename Category image filename.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteCategoryImageFile(FilenameStringType $filename);


	/**
	 * Deletes a category icon file.
	 *
	 * @param FilenameStringType $filename Category icon filename.
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteCategoryIconFile(FilenameStringType $filename);
	
	
	/**
	 * Activates a specific category and its subcategories if desired.
	 *
	 * @param IdType   $categoryId           Category ID of the category to activate.
	 * @param BoolType $includeSubcategories Shall the subcategories be activated also?
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function activateCategory(IdType $categoryId, BoolType $includeSubcategories);


	/**
	 * Deactivates a specific category and its subcategories if desired.
	 *
	 * @param \IdType   $categoryId           Category ID of the category to deactivate.
	 * @param \BoolType $includeSubcategories Shall the subcategories be deactivated also?
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function deactivateCategory(IdType $categoryId, BoolType $includeSubcategories);
	
	
	/**
	 * Sets the customer status permissions.
	 *
	 * The customer status permissions decides if the category is visible for a specific customer group. The
	 * permissions can be applied for subcategories also if desired.
	 *
	 * @param \IdType   $categoryId                      Category ID.
	 * @param \IdType   $customerStatusId                Customer status ID.
	 * @param \BoolType $permitted                       Grant permission?
	 * @param \BoolType $includeSubcategoriesAndProducts Grant permission including subcategories?
	 *
	 * @return CategoryWriteServiceInterface Same instance for chained method calls.
	 */
	public function setCustomerStatusPermission(IdType $categoryId,
	                                            IdType $customerStatusId,
	                                            BoolType $permitted,
	                                            BoolType $includeSubcategoriesAndProducts);
}