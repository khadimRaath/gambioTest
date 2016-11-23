<?php

/* --------------------------------------------------------------
   AbstractCategoryServiceFactory.inc.php 2015-11-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractCategoryServiceFactory
 *
 * This abstract class defines defines the api contract for the CategoryServiceFactory.
 *
 * @category   System
 * @package    Category
 * @subpackage Factories
 */
abstract class AbstractCategoryServiceFactory
{
	/**
	 * Creates a category object service.
	 * 
	 * @return CategoryObjectServiceInterface
	 */
	abstract public function createCategoryObjectService();
	
	
	/**
	 * Creates a category read service.
	 * 
	 * @return CategoryReadServiceInterface
	 */
	abstract public function createCategoryReadService();
	
	
	/**
	 * Creates a category write service.
	 * 
	 * @return CategoryWriteServiceInterface
	 */
	abstract public function createCategoryWriteService();
}