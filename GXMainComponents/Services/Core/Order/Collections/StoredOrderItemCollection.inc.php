<?php

/* --------------------------------------------------------------
   StoredOrderItemCollection.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemCollection');

/**
 * Class StoredOrderItemCollection
 *
 * @category   System
 * @package    Order
 * @subpackage Collections
 */
class StoredOrderItemCollection extends OrderItemCollection
{
	/**
	 * Returns the valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'StoredOrderItemInterface';
	}
}