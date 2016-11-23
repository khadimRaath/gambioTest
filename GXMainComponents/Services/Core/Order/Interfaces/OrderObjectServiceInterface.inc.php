<?php

/* --------------------------------------------------------------
   OrderObjectServiceInterface.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderObjectServiceInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderObjectServiceInterface
{
	/**
	 * Creates and returns an order item object.
	 *
	 * @param StringType $name Name of the order item object to be created.
	 *
	 * @return OrderItem New order item object.
	 */
	public function createOrderItemObject(StringType $name);
	
	
	/**
	 * Creates and returns an order item attribute object.
	 *
	 * @param StringType $name  Name of the order item object attribute to be created.
	 * @param StringType $value Value of the order item object attribute to be created.
	 *
	 * @return OrderItemAttribute New order item attribute object.
	 */
	public function createOrderItemAttributeObject(StringType $name, StringType $value);
	
	
	/**
	 * Creates and returns an order item property object.
	 *
	 * @param StringType $name  Name of the order item object property to be created.
	 * @param StringType $value Value of the order item object property to be created.
	 *
	 * @return OrderItemProperty New order item property object.
	 */
	public function createOrderItemPropertyObject(StringType $name, StringType $value);
	
	
	/**
	 * Creates and returns an order total object.
	 *
	 * @param StringType  $title     Title of the order total to be created.
	 * @param DecimalType $value     value of the order total to be created.
	 * @param StringType  $valueText Value text of the order total to be created.
	 * @param  StringType $class     Class of the order total to be created.
	 * @param IntType     $sortOrder Sort order of the order total to be created.
	 *
	 * @return OrderTotal New order total object.
	 */
	public function createOrderTotalObject(StringType $title,
	                                       DecimalType $value,
	                                       StringType $valueText = null,
	                                       StringType $class = null,
	                                       IntType $sortOrder = null);
	
	
	/**
	 * Creates and returns a stored order item object.
	 *
	 * @param IdType $orderItemId Order item ID of the order item to be stored.
	 *
	 * @return StoredOrderItem New stored order item object.
	 */
	public function createStoredOrderItemObject(IdType $orderItemId);
	
	
	/**
	 * Creates and returns a stored order item attribute object.
	 *
	 * @param IdType $orderItemAttributeId Order item attribute ID of the order item attribute to be stored.
	 *
	 * @return StoredOrderItemAttribute New stored order item attribute object.
	 */
	public function createStoredOrderItemAttributeObject(IdType $orderItemAttributeId);
	
	
	/**
	 * Creates and returns a stored order item property object.
	 *
	 * @param IdType $orderItemPropertyId Order property ID of the order item property to be stored.
	 *
	 * @return StoredOrderItemProperty New stored order item property object.
	 */
	public function createStoredOrderItemPropertyObject(IdType $orderItemPropertyId);
	
	
	/**
	 * Creates and returns a stored order total object.
	 *
	 * @param IdType $orderTotalId Order total ID of the order total to be stored.
	 *
	 * @return StoredOrderTotal New stored order total object
	 */
	public function createStoredOrderTotalObject(IdType $orderTotalId);
}