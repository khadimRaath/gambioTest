<?php

/* --------------------------------------------------------------
   OrderItemPropertyRepositoryDeleterInterface.php 2015-10-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemPropertyRepositoryDeleterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemPropertyRepositoryDeleterInterface
{
	/**
	 * Removes a property by the given order item property ID.
	 *
	 * @param IdType $orderItemPropertyId ID of the order item property.
	 *
	 * @return OrderItemPropertyRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deletePropertyById(IdType $orderItemPropertyId);
	
	
	/**
	 * Removes all properties from the order item by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return OrderItemPropertyRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deletePropertiesByOrderItemId(IdType $orderItemId);
}