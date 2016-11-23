<?php

/* --------------------------------------------------------------
   OrderReadServiceInterface.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderReadServiceInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderReadServiceInterface
{
	/**
	 * Returns an order, depending on the provided order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return Order Found order.
	 */
	public function getOrderById(IdType $orderId);
	
	
	/**
	 * Returns a stored order item, depending on the provided order item ID.
	 *
	 * @param IdType $orderItemId Order item ID.
	 *
	 * @return StoredOrderItemInterface Found stored order item interface.
	 */
	public function getOrderItemById(IdType $orderItemId);
	
	
	/**
	 * Returns an OrderListItemCollection depending on the provided arguments.
	 *
	 * @param IntType    $startIndex Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount   Maximum amount of collections.
	 * @param StringType $orderBy    Argument to specify the order.
	 *
	 * @return OrderListItemCollection Order list item collection.
	 */
	public function getOrderList(IntType $startIndex = null, IntType $maxCount = null, StringType $orderBy = null);
	
	
	/**
	 * Filter the order records with specific conditions.
	 *
	 * Provide the filtering values in the conditions array in order to fetch a filtered result set.
	 *
	 * @param array      $filterParameters Contains an array of the GET parameters to be used for filtering the order
	 *                                     records.
	 * @param IntType    $startIndex       Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount         Maximum amount of collections.
	 * @param StringType $orderBy          Argument to specify the order.
	 *
	 * @return OrderListItemCollection
	 */
	public function filterOrderList(array $filterParameters,
	                                IntType $startIndex = null,
	                                IntType $maxCount = null,
	                                StringType $orderBy = null);
	
	
	/**
	 * Get the filtered orders count.
	 *
	 * @param array $filterParameters
	 *
	 * @return int
	 *
	 * @throws BadMethodCallException
	 */
	public function filterOrderListCount(array $filterParameters);
	
	
	/**
	 * Returns an OrderListItemCollection depending on the provided customer ID.
	 *
	 * @param IdType     $customerId Customer ID
	 * @param IntType    $startIndex Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount   Maximum amount of collections.
	 * @param StringType $orderBy    Argument to specify the order.
	 *
	 * @return OrderListItemCollection Order list item collection.
	 */
	public function getOrderListByCustomerId(IdType $customerId,
	                                         IntType $startIndex = null,
	                                         IntType $maxCount = null,
	                                         StringType $orderBy = null);
	
	
	/**
	 * Returns an OrderListItemCollection depending on the provided order status ID.
	 *
	 * @param IntType    $orderStatusId Order status ID
	 * @param IntType    $startIndex    Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount      Maximum amount of collections.
	 * @param StringType $orderBy       Argument to specify the order.
	 *
	 * @return OrderListItemCollection Order list item collection.
	 */
	public function getOrderListByOrderStatusId(IntType $orderStatusId,
	                                            IntType $startIndex = null,
	                                            IntType $maxCount = null,
	                                            StringType $orderBy = null);
	
	
	/**
	 * Filter the order list by a string keyword.
	 *
	 * @param StringType $keyword    Keyword to be used for searching the order list items.
	 * @param IntType    $startIndex Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount   Maximum amount of collections.
	 * @param StringType $orderBy    Argument to specify the order.
	 *
	 * @return OrderListItemCollection Order list item collection.
	 */
	public function getOrderListByKeyword(StringType $keyword,
	                                      IntType $startIndex = null,
	                                      IntType $maxCount = null,
	                                      StringType $orderBy = null);
}