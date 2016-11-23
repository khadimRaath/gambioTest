<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryReaderInterface.inc.php 2015-11-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeRepositoryReaderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemAttributeRepositoryReaderInterface
{
	/**
	 * Returns an order item attribute by the given ID.
	 *
	 * @param IdType $orderItemAttributeId ID of order item attribute.
	 *
	 * @throws \UnexpectedValueException If no order item attribute recors matches the provided $orderItemAttributeId
	 * @return StoredOrderItemAttributeInterface Fetched order item attribute.
	 */
	public function getAttributeById(IdType $orderItemAttributeId);
	
	
	/**
	 * Returns a collection of order item attributes by the given order item ID.
	 *
	 * @param \IdType $orderItemId ID of the order item.
	 *
	 * @return StoredOrderItemAttributeCollection Fetched order item attribute collection.
	 */
	public function getAttributesByOrderItemId(IdType $orderItemId);
}