<?php

/* --------------------------------------------------------------
   CategoryRepositoryDeleterInterface.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryRepositoryDeleterInterface
 *
 * This interface defines methods for deleting category records from the database and is used in the category
 * repository among the interfaces for writing and reading category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryRepositoryDeleterInterface
{
	/**
	 * Deletes a category based on the ID provided.
	 *
	 * @param IdType $categoryId Category ID.
	 */
	public function deleteById(IdType $categoryId);
}