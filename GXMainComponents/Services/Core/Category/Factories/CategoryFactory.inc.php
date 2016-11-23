<?php

/* --------------------------------------------------------------
   CategoryFactory.inc.php 2015-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

MainFactory::load_class('CategoryFactoryInterface');

/**
 * Class CategoryFactory
 * 
 * This class creates Category and StoredCategory objects with its CategorySettings dependency.
 *
 * @category   System
 * @package    Category
 * @subpackage Factories
 */
class CategoryFactory implements CategoryFactoryInterface
{
	/**
	 * Creates and returns a new instance of a category object.
	 *
	 * @return Category
	 */
	public function createCategory()
	{
		return MainFactory::create('Category', MainFactory::create('CategorySettings'));
	}


	/**
	 * Creates and returns a new instance of a stored category object.
	 *
	 * @param IdType $categoryId CategoryID.
	 *
	 * @return StoredCategory
	 */
	public function createStoredCategory(IdType $categoryId)
	{
		return MainFactory::create('StoredCategory', $categoryId, MainFactory::create('CategorySettings'));
	}


	/**
	 * Creates and returns a new instance of a category settings object.
	 *
	 * @return CategorySettings
	 */
	public function createCategorySettings()
	{
		return MainFactory::create('CategorySettings');
	}
}