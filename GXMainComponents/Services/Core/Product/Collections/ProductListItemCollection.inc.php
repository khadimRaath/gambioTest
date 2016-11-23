<?php

/* --------------------------------------------------------------
   ProductListItemCollection.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductListItemCollection
 *
 * @category   System
 * @package    Product
 * @subpackage Collections
 */
class ProductListItemCollection extends EditableCollection
{
	/**
	 * Returns a valid type for the ProductListItemCollection.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'ProductListItem';
	}
}