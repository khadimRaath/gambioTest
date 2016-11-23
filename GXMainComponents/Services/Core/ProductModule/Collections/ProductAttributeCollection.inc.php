<?php

/* --------------------------------------------------------------
   ProductAttributeCollection.inc.php 2016-01-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class ProductAttributeCollection
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Collections
 */
class ProductAttributeCollection extends EditableCollection
{
	/**
	 * Returns the valid type for the ProductAttributeCollection.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'ProductAttribute';
	}
}