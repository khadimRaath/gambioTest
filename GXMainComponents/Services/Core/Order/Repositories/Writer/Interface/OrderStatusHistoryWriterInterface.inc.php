<?php

/* --------------------------------------------------------------
   OrderStatusHistoryWriterInterface.inc.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusHistoryWriterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderStatusHistoryWriterInterface
{
	/**
	 * Adds an order status history item.
	 *
	 * @param IdType     $orderId          Order ID.
	 * @param IntType    $newOrderStatusId New order status ID.
	 * @param StringType $comment          Comment.
	 * @param BoolType   $customerNotified Is customer notified?
	 */
	public function addStatusUpdate(IdType $orderId,
	                                IntType $newOrderStatusId,
	                                StringType $comment,
	                                BoolType $customerNotified);
	
	
	/**
	 * Deletes all order status history items which are associated with the given order item ID.
	 *
	 * @param IdType $orderId Order ID.
	 */
	public function deleteHistory(IdType $orderId);
}