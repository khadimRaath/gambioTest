<?php
/* --------------------------------------------------------------
   OrderReadOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderReadOverload
 *
 * This sample overload demonstrates the overloading of the OrderReadService class.
 *
 * Notice: This service is not currently used within the shop (the integration will come gradually). You can try
 * this overload with the sample files that reside in the docs/PHP/samples/order-service directory.
 *
 * @see OrderReadService
 */
class OrderReadOverload extends OrderReadOverload_parent
{
	/**
	 * Overloaded "getOrderById" method.
	 *
	 * This method will log the use of "getOrderById" method in the debug logs of the shop.
	 *
	 * @param IdType $orderId
	 *
	 * @return OrderInterface
	 */
	public function getOrderById(IdType $orderId)
	{
		$this->_createDebugLog('OrderReadService::getOrderById >> Fetch order with ID = ' . $orderId->asInt());
		
		return parent::getOrderById($orderId);
	}
	
	
	/**
	 * Overloaded "getOrderItemById" method.
	 *
	 * This method will log the use of "getOrderItemById" method in the debug logs of the shop.
	 *
	 * @param IdType $orderItemId
	 *
	 * @return OrderItemInterface
	 */
	public function getOrderItemById(IdType $orderItemId)
	{
		$this->_createDebugLog('OrderReadSevice::getOrderItemById >> Fetch order item with ID =  '
		                       . $orderItemId->asInt());
		
		return parent::getOrderItemById($orderItemId);
	}
	
	
	/**
	 * Overloaded the "getOrderList" method.
	 *
	 * This method will log the use of "getOrderList" method in the debug logs of the shop.
	 *
	 * @param IntType    $startIndex
	 * @param IntType    $maxCount
	 * @param StringType $orderBy
	 *
	 * @return OrderListItemCollection
	 */
	public function getOrderList(IntType $startIndex = null, IntType $maxCount = null, StringType $orderBy = null)
	{
		$index     = $startIndex ? : 'null';
		$count     = $maxCount ? : 'null';
		$sortOrder = $orderBy ? : 'null';
		
		$this->_createDebugLog('OrderReadService::getOrderList >> Fetch order list with start index ' . $index
		                       . ' and max count ' . $count . ' ordered by ' . $sortOrder);
		
		return parent::getOrderList($startIndex, $maxCount, $orderBy);
	}
	
	
	/**
	 * Create new debug log entry.
	 *
	 * @param string $message
	 */
	protected function _createDebugLog($message)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice($message);
	}
}
