<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryInterface.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderTotalRepositoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderTotalRepositoryInterface
{
	/**
	 * Adds an order total object to the order.
	 *
	 * @param IdType              $orderId    ID of order.
	 * @param OrderTotalInterface $orderTotal Order total object.
	 *
	 * @return int ID of stored order total.
	 */
	public function addToOrder(IdType $orderId, OrderTotalInterface $orderTotal);
	
	
	/**
	 * Updates a stored order total object.
	 *
	 * @param StoredOrderTotalInterface $orderTotal Order total.
	 *
	 * @return OrderTotalRepositoryInterface Same instance for method chaining.
	 */
	public function store(StoredOrderTotalInterface $orderTotal);
	
	
	/**
	 * Returns an order total object by the given ID.
	 *
	 * @param IdType $orderTotalId ID of order total in database table.
	 *
	 * @return StoredOrderTotalInterface Fetched order total.
	 */
	public function getTotalById(IdType $orderTotalId);
	
	
	/**
	 * Returns an collection of order total objects by the given order ID.
	 *
	 * @param IdType $orderId ID of the order in the database table.
	 *
	 * @return StoredOrderTotalCollection Fetched order total collection.
	 */
	public function getTotalsByOrderId(IdType $orderId);
	
	
	/**
	 * Removes an order total by the given order total ID.
	 *
	 * @param IdType $orderTotalId ID of order total in the database table.
	 *
	 * @return OrderTotalRepositoryInterface Same instance for method chaining.
	 */
	public function deleteTotalById(IdType $orderTotalId);
	
	
	/**
	 * Removes multiple order totals by the given order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderTotalRepositoryInterface Same instance for method chaining.
	 */
	public function deleteTotalsByOrderId(IdType $orderId);
}