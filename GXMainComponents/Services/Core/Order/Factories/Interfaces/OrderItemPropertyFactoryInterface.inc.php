<?php

/* --------------------------------------------------------------
   OrderItemPropertyFactoryInterface.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemPropertyFactoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemPropertyFactoryInterface
{
	/**
	 * Creates an OrderItemProperty object.
	 *
	 * @return OrderItemProperty New OrderItemProperty instance.
	 */
	public function createOrderItemProperty();
	
	
	/**
	 * Creates a StoredOrderItemProperty object.
	 *
	 * @param IdType $orderItemPropertyId ID of order item property.
	 *
	 * @return StoredOrderItemProperty New StoredOrderItemProperty instance.
	 */
	public function createStoredOrderItemProperty(IdType $orderItemPropertyId);
}