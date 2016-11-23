<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryInterface.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeRepositoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemAttributeRepositoryInterface
{
	/**
	 * Adds an attribute to an order item.
	 *
	 * @param IdType                      $orderItemId        ID of the order item.
	 * @param OrderItemAttributeInterface $orderItemAttribute Order item attribute to add.
	 *
	 * @return int ID of stored order item attribute.
	 */
	public function addToOrderItem(IdType $orderItemId, OrderItemAttributeInterface $orderItemAttribute);
	
	
	/**
	 * Saves the attribute to the repository.
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemAttribute Attribute to save.
	 *
	 * @return OrderItemAttributeRepositoryInterface Same instance for method chaining.
	 */
	public function store(StoredOrderItemAttributeInterface $orderItemAttribute);
	
	
	/**
	 * Returns a stored attribute by the given ID.
	 *
	 * @param IdType $orderItemAttributeId ID of item attribute.
	 *
	 * @return StoredOrderItemAttributeInterface Stored attribute.
	 */
	public function getItemAttributeById(IdType $orderItemAttributeId);
	
	
	/**
	 * Returns a stored attribute collection by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return StoredOrderItemAttributeCollection Stored item attribute collection.
	 */
	public function getItemAttributesByOrderItemId(IdType $orderItemId);
	
	
	/**
	 * Deletes an item attribute by the given item attribute ID.
	 *
	 * @param IdType $orderItemAttributeId ID of order item attribute.
	 *
	 * @return OrderItemAttributeRepositoryInterface Same instance for method chaining.
	 */
	public function deleteItemAttributeById(IdType $orderItemAttributeId);
	
	
	/**
	 * Deletes an item attribute by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return OrderItemAttributeRepositoryInterface Same instance for method chaining.
	 */
	public function deleteItemAttributesByOrderItemId(IdType $orderItemId);
}