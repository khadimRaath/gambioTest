<?php

/* --------------------------------------------------------------
   CategoryListProviderFactoryInterface.inc.php 2015-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryListProviderFactoryInterface
 *
 * This interface defines methods for creating CategoryListProvider objects for a specific language and filter of
 * customer status permissions with its dependencies.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryListProviderFactoryInterface
{

	/**
	 * Creates a CategoryListProvider for retrieving lists.
	 *
	 * @param LanguageCode $languageCode Two letter language code.
	 * @param array        $conditions   Data request conditions.
	 *
	 * @return CategoryListProviderInterface
	 */
	public function createCategoryListProvider(LanguageCode $languageCode, array $conditions = array());
}