<?php
/* --------------------------------------------------------------
   CatWriteServiceOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CatWriteServiceOverload
 *
 * This example overload class demonstrates the replacement of the CategoryRead class.
 *
 * @see CategoryWriteService
 */
class CatWriteServiceOverload extends CatWriteServiceOverload_parent
{
	/**
	 * Overloaded constructor of the category write service.
	 *
	 * @param CategoryRepositoryInterface      $categoryRepo
	 * @param AbstractFileStorage              $categoryImageStorage
	 * @param AbstractFileStorage              $categoryIconStorage
	 * @param ProductPermissionSetterInterface $productPermissionSetter
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepo,
	                            AbstractFileStorage $categoryImageStorage,
	                            AbstractFileStorage $categoryIconStorage,
	                            ProductPermissionSetterInterface $productPermissionSetter)
	{
		$GLOBALS['messageStack']->add('
			<h3>CategoryWriteService overload is used!</h3>
			<p>
				This overload will create a new debug log entry whenever you create or update a category record
				(actually whenever the "createCategory" and "updateCategory" methods are executed). Check the logs  
				admin page for the new entries.
			</p>
		', 'info');
		
		parent::__construct($categoryRepo, $categoryImageStorage, $categoryIconStorage, $productPermissionSetter);
	}
	
	
	/**
	 * Overload the "createCategory" method.
	 *
	 * This method will create a new debug entry whenever a new category is created.
	 *
	 * @param CategoryInterface $category
	 *
	 * @return int
	 */
	public function createCategory(CategoryInterface $category)
	{
		$newCategoryId = parent::createCategory($category);
		
		$logControl = LogControl::get_instance();
		$logControl->notice('CategoryWrite::createCategory >> New category created with ID = ' . $newCategoryId);
		
		return $newCategoryId;
	}
	
	
	/**
	 * Overload the "updateCategory" method.
	 *
	 * This method will create a new debug entry whenever an existing category is updated.
	 *
	 * @param StoredCategoryInterface $category
	 *
	 * @return CategoryWriteService
	 */
	public function updateCategory(StoredCategoryInterface $category)
	{
		parent::updateCategory($category);
		
		$logControl = LogControl::get_instance();
		$logControl->notice('CategoryWrite::updateCategory >> Existing category updated with ID = '
		                    . $category->getCategoryId());
		
		return $this;
	}
}
