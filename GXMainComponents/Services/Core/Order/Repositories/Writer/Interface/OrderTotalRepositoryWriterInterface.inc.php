<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryWriterInterface.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderTotalRepositoryWriterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderTotalRepositoryWriterInterface
{
	/**
	 * Inserts an order total item to an order by the given order ID.
	 *
	 * @param IdType              $orderId    ID of the order.
	 * @param OrderTotalInterface $orderTotal Order total item to insert.
	 *
	 * @return int ID of stored order total item.
	 */
	public function insertIntoOrder(IdType $orderId, OrderTotalInterface $orderTotal);
	
	
	/**
	 * Updates the passed order total item.
	 *
	 * @param StoredOrderTotalInterface $orderTotal Order total item to update.
	 *
	 * @return OrderTotalRepositoryWriterInterface Same instance for method chaining.
	 */
	public function update(StoredOrderTotalInterface $orderTotal);
}