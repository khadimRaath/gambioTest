<?php

/* --------------------------------------------------------------
   CategoryRepositoryReaderInterface.php 2016-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryRepositoryReaderInterface
 * 
 * This interface defines methods for fetching category records from the database and is used in the category
 * repository among the classes for writing and deleting category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryRepositoryReaderInterface
{
	/**
	 * Returns a category by the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return StoredCategoryInterface
	 */
	public function getById(IdType $categoryId);
	
	
	/**
	 * Returns all Categories with the provided parent ID.
	 *
	 * @param IdType $parentId
	 *
	 * @return IdCollection
	 */
	public function getByParentId(IdType $parentId);
}