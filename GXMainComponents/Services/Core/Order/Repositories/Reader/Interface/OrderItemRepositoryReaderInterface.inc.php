<?php

/* --------------------------------------------------------------
   OrderItemRepositoryReaderInterface.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemRepositoryReaderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemRepositoryReaderInterface
{
	/**
	 * Returns an stored order item by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return StoredOrderItemInterface Fetched order item.
	 */
	public function getItemById(IdType $orderItemId);
	
	
	/**
	 * Returns a collection of stored order items by the given order ID.
	 *
	 * @param IdType $orderId ID of the order.
	 *
	 * @return StoredOrderItemCollection Fetched order item collection.
	 */
	public function getItemsByOrderId(IdType $orderId);
}