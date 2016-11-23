<?php

/* --------------------------------------------------------------
   OrderItemPropertyRepositoryWriterInterface.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemPropertyRepositoryWriterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemPropertyRepositoryWriterInterface
{
	/**
	 * Adds a new property to the order item.
	 *
	 * @param IdType            $orderItemId       ID of the order item.
	 * @param OrderItemProperty $orderItemProperty Property to add.
	 *
	 * @return int ID of stored order item property.
	 */
	public function insertIntoOrderItem(IdType $orderItemId, OrderItemProperty $orderItemProperty);
	
	
	/**
	 * Updates the stored order item property.
	 *
	 * @param StoredOrderItemProperty $orderItemProperty Order item property.
	 *
	 * @return OrderItemPropertyRepositoryWriterInterface Same instance for method chaining.
	 */
	public function update(StoredOrderItemProperty $orderItemProperty);
}