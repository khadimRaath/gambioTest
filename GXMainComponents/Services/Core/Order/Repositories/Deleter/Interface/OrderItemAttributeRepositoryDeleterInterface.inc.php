<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryDeleterInterface.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeRepositoryDeleterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemAttributeRepositoryDeleterInterface
{
	/**
	 * Removes an attribute by the given order item attribute ID.
	 *
	 * @param IdType $orderItemAttributeId ID of the order item attribute.
	 *
	 * @return OrderItemAttributeRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteAttributeById(IdType $orderItemAttributeId);
	
	
	/**
	 * Removes all attributes from the order item by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return OrderItemAttributeRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteAttributesByOrderItemId(IdType $orderItemId);
}