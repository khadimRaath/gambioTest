<?php

/* --------------------------------------------------------------
   CategoryFactoryInterface.inc.php 2015-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/


/**
 * Interface CategoryFactoryInterface
 * 
 * This interface defines methods for creating Category and StoredCategory objects with its CategorySettings dependency.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryFactoryInterface
{
	/**
	 * Creates and returns a new instance of a category object.
	 *
	 * @return Category
	 */
	public function createCategory();


	/**
	 * Creates and returns a new instance of a stored category object.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return StoredCategory
	 */
	public function createStoredCategory(IdType $categoryId);


	/**
	 * Creates and returns a new instance of a category settings object.
	 *
	 * @return CategorySettings
	 */
	public function createCategorySettings();
}