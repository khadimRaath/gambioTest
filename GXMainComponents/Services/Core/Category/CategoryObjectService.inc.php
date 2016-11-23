<?php

/* --------------------------------------------------------------
   CategoryObjectService.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryObjectService
 * 
 * Use this class for creating new categories objects with its default values.
 *
 * @category   System
 * @package    Category
 */
class CategoryObjectService implements CategoryObjectServiceInterface
{
	/**
	 * CategoryFactory interface.
	 * @var CategoryFactoryInterface
	 */
	protected $categoryFactory;

	/**
	 * CategoryObjectService constructor.
	 *
	 * @param CategoryFactoryInterface $categoryFactory The category factory.
	 */
	public function __construct(CategoryFactoryInterface $categoryFactory)
	{
		$this->categoryFactory = $categoryFactory;
	}


	/**
	 * Creates a new category object with its default values.
	 *
	 * @return CategoryInterface
	 */
	public function createCategoryObject()
	{
		return $this->categoryFactory->createCategory();
	}
}