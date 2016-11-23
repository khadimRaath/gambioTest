<?php

/* --------------------------------------------------------------
   CategoryListProviderInterface.inc.php 2015-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CategoryListProviderInterface
 * 
 * This interface defines methods for creating a list of flattened categories with just its essential data.
 * The list of categories is filtered by its parent category ID and customer status permissions and for simplicity
 * it contains language specific data only in one language.
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryListProviderInterface
{
	/**
	 * Returns a category list based the parent ID provided.
	 *
	 * @param IdType $parentId Category parent ID.
	 *
	 * @return CategoryListItemCollection
	 */
	public function getByParentId(IdType $parentId);
}