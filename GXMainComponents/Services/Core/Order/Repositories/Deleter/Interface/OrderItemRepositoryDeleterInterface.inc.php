<?php

/* --------------------------------------------------------------
   OrderItemRepositoryDeleterInterface.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemRepositoryDeleterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemRepositoryDeleterInterface
{
	/**
	 * Removes an item from the order by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return OrderItemRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteItemById(IdType $orderItemId);
	
	
	/**
	 * Removes multiple order items by the given order ID.
	 *
	 * @param IdType $orderId ID of the order.
	 *
	 * @return OrderItemRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteItemsByOrderId(IdType $orderId);
}