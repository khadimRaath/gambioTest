<?php

/* --------------------------------------------------------------
   OrderItemRepositoryInterface.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemRepositoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemRepositoryInterface
{
	/**
	 * Adds an order item to the order item repository.
	 *
	 * @param IdType             $orderId   Order ID.
	 * @param OrderItemInterface $orderItem Order item to add.
	 *
	 * @return int ID of the StoredOrderItem
	 */
	public function addToOrder(IdType $orderId, OrderItemInterface $orderItem);
	
	
	/**
	 * Saves the order item in the repository.
	 *
	 * @param StoredOrderItemInterface $storedOrderItem Order item to save.
	 *
	 * @return OrderItemRepositoryInterface Same instance for method chaining.
	 */
	public function store(StoredOrderItemInterface $storedOrderItem);
	
	
	/**
	 * Returns a stored order ID by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the stored order item.
	 *
	 * @return StoredOrderItemInterface Stored order item.
	 */
	public function getItemById(IdType $orderItemId);
	
	
	/**
	 * Returns a stored order item collection by the given order ID.
	 *
	 * @param IdType $orderId ID of the order.
	 *
	 * @return StoredOrderItemCollection Stored order item collection.
	 */
	public function getItemsByOrderId(IdType $orderId);
	
	
	/**
	 * Deletes an order item from the repository by the given order item ID.
	 *
	 * @param IdType $orderItemId Order item ID.
	 *
	 * @return OrderItemRepositoryInterface Same instance for method chaining.
	 */
	public function deleteItemById(IdType $orderItemId);
	
	
	/**
	 * Deletes order items from the repository by the given order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderItemRepositoryInterface Same instance for method chaining.
	 */
	public function deleteItemsByOrderId(IdType $orderId);
}