<?php

/* --------------------------------------------------------------
   OrderItemRepositoryWriterInterface.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemRepositoryWriterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemRepositoryWriterInterface
{
	/**
	 * Inserts an order item to an order by the given order ID.
	 *
	 * @param IdType             $orderId   ID of the order.
	 * @param OrderItemInterface $orderItem Order item to insert.
	 *
	 * @return int ID of inserted item.
	 */
	public function insertIntoOrder(IdType $orderId, OrderItemInterface $orderItem);
	
	
	/**
	 * Update the passed order item.
	 *
	 * @param StoredOrderItemInterface $orderItem Order item to update.
	 *
	 * @return OrderItemRepositoryWriterInterface Same instance for method chaining.
	 */
	public function update(StoredOrderItemInterface $orderItem);
}