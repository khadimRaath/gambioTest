<?php

/* --------------------------------------------------------------
   StoredProductAttributeCollection.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StoredProductAttributeCollection
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Collections
 */
class StoredProductAttributeCollection extends ProductAttributeCollection
{
	/**
	 * Returns the valid type for the StoredProductAttributeCollection
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'StoredProductAttribute';
	}
}