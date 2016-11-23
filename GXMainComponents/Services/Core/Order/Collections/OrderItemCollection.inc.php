<?php

/* --------------------------------------------------------------
   OrderItemCollection.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');

/**
 * Class OrderItemCollection
 *
 * @category   System
 * @package    Order
 * @subpackage Collections
 */
class OrderItemCollection extends AbstractCollection
{
	/**
	 * Returns the valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * Type must be OrderItemInterface
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'OrderItemInterface';
	}
}