<?php

/* --------------------------------------------------------------
   CategoryRepositoryInterface.inc.php 2016-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryRepositoryInterface
 * 
 * This interface defines methods for handling the database operations that concern the category records of the
 * database. It provides a layer for more complicated methods that use the writer, reader and deleter.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryRepositoryInterface
{
	/**
	 * Adds a category.
	 *
	 * @param CategoryInterface $category Category to add.
	 *
	 * @return int Stored id of the passed category.
	 */
	public function add(CategoryInterface $category);
	
	
	/**
	 * Stores a category.
	 *
	 * @param StoredCategoryInterface $category Stored category.
	 *
	 * @return CategoryRepositoryInterface Same instance for chained method calls.
	 */
	public function store(StoredCategoryInterface $category);
	
	
	/**
	 * Gets a category by the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return StoredCategoryInterface
	 */
	public function getCategoryById(IdType $categoryId);
	
	
	/**
	 * Deletes a category by the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategoryRepositoryInterface Same instance for chained method calls.
	 */
	public function deleteCategoryById(IdType $categoryId);
	
	
	/**
	 * Returns all Categories with the provided parent ID.
	 *
	 * @param IdType $parentId
	 *
	 * @return IdCollection
	 */
	public function getCategoryIdsByParentId(IdType $parentId);
}