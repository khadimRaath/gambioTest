<?php

/* --------------------------------------------------------------
   ProductImageCollection.php 2015-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductImageCollection
 *
 * @category   System
 * @package    Product
 * @subpackage Collections
 */
class ProductImageCollection extends EditableCollection
{
	/**
	 * Returns a valid type for the ProductImageCollection.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'ProductImage';
	}
}