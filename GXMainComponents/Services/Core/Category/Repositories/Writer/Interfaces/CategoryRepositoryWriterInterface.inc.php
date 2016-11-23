<?php

/* --------------------------------------------------------------
   CategoryRepositoryWriterInterface.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryRepositoryWriterInterface
 * 
 * This interface defines methods for creating and updating specific category records in the database and is used in the category
 * repository among the interfaces for reading and deleting category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryRepositoryWriterInterface
{
	/**
	 * Inserts a category record into the database accordingly to the provided category object and returns the ID from
	 * the saved entity.
	 *
	 * @param CategoryInterface $category The category to insert.
	 *
	 * @return int Returns the ID of the new category.
	 */
	public function insert(CategoryInterface $category);


	/**
	 * Updates an existing category record accordingly to the provided category object.
	 *
	 * @param StoredCategoryInterface $category The category to update.
	 *
	 * @return CategoryRepositoryWriterInterface Same instance for chained method calls.
	 */
	public function update(StoredCategoryInterface $category);
}