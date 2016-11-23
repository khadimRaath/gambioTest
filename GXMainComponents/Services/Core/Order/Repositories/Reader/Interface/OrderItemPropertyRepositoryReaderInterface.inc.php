<?php

/* --------------------------------------------------------------
   OrderItemRepositoryReaderInterface.inc.php 2015-11-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemPropertyRepositoryReaderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemPropertyRepositoryReaderInterface
{
	/**
	 * Returns an order item property by the given ID.
	 *
	 * @param IdType $orderItemPropertyId ID of order item property.
	 *
	 * @return StoredOrderItemProperty Fetched order item property.
	 */
	public function getPropertyById(IdType $orderItemPropertyId);
	
	
	/**
	 * Returns a collection of order item properties by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return StoredOrderItemAttributeCollection Fetched order item attribute collection.
	 */
	public function getPropertiesByOrderItemId(IdType $orderItemId);
}