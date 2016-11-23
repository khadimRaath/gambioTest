<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryWriterInterface.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeRepositoryWriterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemAttributeRepositoryWriterInterface
{
	/**
	 * Adds a new attribute to the order item.
	 *
	 * @param IdType             $orderItemId        ID of the order item.
	 * @param OrderItemAttribute $orderItemAttribute Attribute to add.
	 *
	 * @return int ID of stored order item attribute.
	 */
	public function insertIntoOrderItem(IdType $orderItemId, OrderItemAttribute $orderItemAttribute);


	/**
	 * Updates the stored order item attribute.
	 *
	 * @param StoredOrderItemAttribute $orderItemAttribute Order item attribute.
	 *
	 * @return OrderItemAttributeRepositoryWriterInterface Same instance for method chaining.
	 */
	public function update(StoredOrderItemAttribute $orderItemAttribute);
}