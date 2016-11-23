<?php

/* --------------------------------------------------------------
   CategoryListItemCollection.inc.php 2016-02-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryListItemCollection
 * 
 * This class is a container (collection) for CategoryListItem objects.
 *
 * @category   System
 * @package    Category
 * @subpackage Collections
 */
class CategoryListItemCollection extends AbstractCollection
{

	/**
	 * Get valid type.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'CategoryListItem';
	}
}