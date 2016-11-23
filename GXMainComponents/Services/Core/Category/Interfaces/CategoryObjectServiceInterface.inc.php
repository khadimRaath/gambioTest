<?php

/* --------------------------------------------------------------
   CategoryObjectServiceInterface.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryObjectServiceInterface
 * 
 * This interface defines methods for creating new categories objects with its default values.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryObjectServiceInterface
{

	/**
	 * Creates a new category object with its default values.
	 * 
	 * @return CategoryInterface
	 */
	public function createCategoryObject();
}