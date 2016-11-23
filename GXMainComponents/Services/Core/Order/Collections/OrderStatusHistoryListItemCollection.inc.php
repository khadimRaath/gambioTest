<?php

/* --------------------------------------------------------------
   OrderStatusHistoryListItemCollection.inc.php 2015-12-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');

/**
 * Class OrderStatusHistoryListItemCollection
 *
 * @category   System
 * @package    Order
 * @subpackage Collections
 */
class OrderStatusHistoryListItemCollection extends AbstractCollection 
{
	/**
	 * Returns the valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * Type must be OrderHistoryListItem
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'OrderStatusHistoryListItem';
	}
}