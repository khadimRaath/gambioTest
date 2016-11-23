<?php

/* --------------------------------------------------------------
   OrderReadService.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderReadServiceInterface');

/**
 * Class OrderReadService
 *
 * @category System
 * @package  Order
 */
class OrderReadService implements OrderReadServiceInterface
{
	/**
	 * @var OrderRepositoryInterface
	 */
	protected $orderRepository;
	
	/**
	 * @var OrderItemRepositoryInterface
	 */
	protected $orderItemRepository;
	
	/**
	 * @var OrderListGenerator
	 */
	protected $orderListGenerator;
	
	
	/**
	 * OrderReadService Constructor
	 *
	 * @param OrderRepositoryInterface     $orderRepository
	 * @param OrderItemRepositoryInterface $orderItemRepository
	 * @param OrderListGeneratorInterface  $orderListGenerator
	 */
	public function __construct(OrderRepositoryInterface $orderRepository,
	                            OrderItemRepositoryInterface $orderItemRepository,
	                            OrderListGeneratorInterface $orderListGenerator)
	{
		$this->orderRepository     = $orderRepository;
		$this->orderItemRepository = $orderItemRepository;
		$this->orderListGenerator  = $orderListGenerator;
	}
	
	
	/**
	 * Get Order by ID
	 *
	 * Returns an order, depending on the provided order ID.
	 *
	 * @param IdType $orderId Order ID of the wanted order
	 *
	 * @return OrderInterface
	 */
	public function getOrderById(IdType $orderId)
	{
		return $this->orderRepository->getById($orderId);
	}
	
	
	/**
	 * Get a stored order item by ID.
	 *
	 * Returns a stored order item, depending on the provided order item ID.
	 *
	 * @param IdType $orderItemId
	 *
	 * @return StoredOrderItemInterface
	 */
	public function getOrderItemById(IdType $orderItemId)
	{
		return $this->orderItemRepository->getItemById($orderItemId);
	}
	
	
	/**
	 * Get Order List
	 *
	 * Returns an OrderListItemCollection depending on the provided arguments.
	 *
	 * @param IntType    $startIndex Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount   Maximum amount of collections.
	 * @param StringType $orderBy    Argument to specify the order.
	 *
	 * @return OrderListItemCollection
	 */
	public function getOrderList(IntType $startIndex = null, IntType $maxCount = null, StringType $orderBy = null)
	{
		return $this->orderListGenerator->getOrderListByConditions(array(), $startIndex, $maxCount, $orderBy);
	}
	
	
	/**
	 * Filter the order records with specific conditions.p
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
	                                StringType $orderBy = null)
	{
		return $this->orderListGenerator->filterOrderList($filterParameters, $startIndex, $maxCount, $orderBy);
	}
	
	
	/**
	 * Get the filtered orders count.
	 *
	 * @param array $filterParameters
	 *
	 * @return int
	 *
	 * @throws BadMethodCallException
	 */
	public function filterOrderListCount(array $filterParameters)
	{
		return $this->orderListGenerator->filterOrderListCount($filterParameters);
	}
	
	
	/**
	 * Get Order List by Customer ID
	 *
	 * Returns an OrderListItemCollection depending on the provided customer ID.
	 *
	 * @param IdType     $customerId Customer ID
	 * @param IntType    $startIndex Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount   Maximum amount of collections.
	 * @param StringType $orderBy    Argument to specify the order.
	 *
	 * @return OrderListItemCollection
	 */
	public function getOrderListByCustomerId(IdType $customerId,
	                                         IntType $startIndex = null,
	                                         IntType $maxCount = null,
	                                         StringType $orderBy = null)
	{
		return $this->orderListGenerator->getOrderListByConditions(array('orders.customers_id' => $customerId->asInt()),
		                                                           $startIndex, $maxCount, $orderBy);
	}
	
	
	/**
	 * Get Order List by Order Status ID
	 *
	 * Returns an OrderListItemCollection depending on the provided order status ID.
	 *
	 * @param IntType    $orderStatusId Order status ID
	 * @param IntType    $startIndex    Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount      Maximum amount of collections.
	 * @param StringType $orderBy       Argument to specify the order.
	 *
	 * @return OrderListItemCollection
	 */
	public function getOrderListByOrderStatusId(IntType $orderStatusId,
	                                            IntType $startIndex = null,
	                                            IntType $maxCount = null,
	                                            StringType $orderBy = null)
	{
		return $this->orderListGenerator->getOrderListByConditions(array('orders.orders_status' => $orderStatusId->asInt()),
		                                                           $startIndex, $maxCount, $orderBy);
	}
	
	
	/**
	 * Filter the order list by a string keyword.
	 *
	 * @param StringType $keyword    Keyword to be used for searching the order list items.
	 * @param IntType    $startIndex Start index of order list item collections which should be returned.
	 * @param IntType    $maxCount   Maximum amount of collections.
	 * @param StringType $orderBy    Argument to specify the order.
	 *
	 * @return OrderListItemCollection
	 */
	public function getOrderListByKeyword(StringType $keyword,
	                                      IntType $startIndex = null,
	                                      IntType $maxCount = null,
	                                      StringType $orderBy = null)
	{
		return $this->orderListGenerator->getOrderListByKeyword($keyword, $startIndex, $maxCount, $orderBy);
	}
}