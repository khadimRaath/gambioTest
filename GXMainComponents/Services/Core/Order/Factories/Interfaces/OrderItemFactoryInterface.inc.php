<?php

/* --------------------------------------------------------------
   OrderItemFactoryInterface.inc.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemFactoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemFactoryInterface
{
	/**
	 * Creates an OrderItem object.
	 *
	 * @param StringType $name Order item name.
	 *
	 * @return OrderItemInterface New OrderItem instance.
	 */
	public function createOrderItem(StringType $name);
	
	
	/**
	 * Creates a StoredOrderItem object.
	 *
	 * @param IdType             $orderItemId ID of order item.
	 * @param OrderItemInterface $orderItem   (optional) When provided the object values will be used for the
	 *                                        generation of the StoredOrderItemInstance.
	 *
	 * @return StoredOrderItemInterface New StoredOrderItem instance
	 */
	public function createStoredOrderItem(IdType $orderItemId, OrderItemInterface $orderItem = null);
}