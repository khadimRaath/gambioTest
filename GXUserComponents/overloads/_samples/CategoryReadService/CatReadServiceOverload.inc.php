<?php
/* --------------------------------------------------------------
   CatReadServiceOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CatReadServiceOverload
 *
 * This example overload class demonstrates the replacement of the CategoryReadService class.
 * 
 * @see CategoryReadService
 */
class CatReadServiceOverload extends CatReadServiceOverload_parent
{
	/**
	 * Overloaded constructor of the category read service.
	 *
	 * @param CategoryRepositoryInterface          $categoryRepo
	 * @param CategoryListProviderFactoryInterface $categoryListProviderFactory
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepo,
	                            CategoryListProviderFactoryInterface $categoryListProviderFactory)
	{
		$GLOBALS['messageStack']->add('
			<h3>CategoryReadService overload is used!</h3>
			<p>
				This overload will create a new debug log entry whenever you perform a category-related operation (and  
				the "getCategoryById" method of CategoryRead class is executed). Check the logs admin page for the new
				entries.
			</p>
		', 'info');
		
		parent::__construct($categoryRepo, $categoryListProviderFactory);
	}
	
	
	/**
	 * Overload the "getCategoryById" method.
	 *
	 * This method will now create a new debug log entry every time the method is used to fetch a category record from
	 * the database.
	 *
	 * @param IdType $categoryId
	 *
	 * @return CategoryInterface
	 */
	public function getCategoryById(IdType $categoryId)
	{
		$log = LogControl::get_instance();
		$log->notice('CategoryRead::getCategoryById >> Fetched category with ID = ' . $categoryId->asInt());
		
		return parent::getCategoryById($categoryId);
	}
}
