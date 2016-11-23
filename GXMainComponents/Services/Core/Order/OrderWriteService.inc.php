<?php
/* --------------------------------------------------------------
   OrderWriteService.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderWriteServiceInterface');

/**
 * Class OrderWriteService
 *
 * @category System
 * @package  Order
 */
class OrderWriteService implements OrderWriteServiceInterface
{
	/**
	 * @var OrderRepository
	 */
	protected $orderRepository;
	
	/**
	 * @var OrderItemRepository
	 */
	protected $orderItemRepository;
	
	/**
	 * @var OrderItemAttributeRepositoryFactory
	 */
	protected $orderItemAttributeRepositoryFactory;
	
	/**
	 * @var OrderTotalRepository
	 */
	protected $orderTotalRepository;
	
	/**
	 * @var OrderStatusHistoryStorage
	 */
	protected $orderStatusHistoryStorage;
	
	/**
	 * @var OrderServiceSettingsInterface
	 */
	protected $orderServiceSettings;
	
	
	/**
	 * Class Constructor
	 *
	 * @param OrderRepositoryInterface                     $orderRepository
	 * @param OrderItemRepositoryInterface                 $orderItemRepository
	 * @param OrderItemAttributeRepositoryFactoryInterface $orderItemAttributeRepositoryFactory
	 * @param OrderTotalRepository                         $orderTotalRepository
	 * @param OrderStatusHistoryStorage                    $orderStatusHistoryWriter
	 * @param OrderServiceSettingsInterface                $orderServiceSetting
	 */
	public function __construct(OrderRepositoryInterface $orderRepository,
	                            OrderItemRepositoryInterface $orderItemRepository,
	                            OrderItemAttributeRepositoryFactoryInterface $orderItemAttributeRepositoryFactory,
	                            OrderTotalRepository $orderTotalRepository,
	                            OrderStatusHistoryStorage $orderStatusHistoryWriter,
	                            OrderServiceSettingsInterface $orderServiceSetting)
	{
		$this->orderRepository                     = $orderRepository;
		$this->orderItemRepository                 = $orderItemRepository;
		$this->orderItemAttributeRepositoryFactory = $orderItemAttributeRepositoryFactory;
		$this->orderTotalRepository                = $orderTotalRepository;
		$this->orderStatusHistoryStorage           = $orderStatusHistoryWriter;
		$this->orderServiceSettings                = $orderServiceSetting;
	}
	
	
	/**
	 * Create New Customer Order
	 *
	 * Creates a new customer order and returns
	 * the order ID as an integer.
	 *
	 * @param IdType                    $customerId           Customer ID
	 * @param CustomerStatusInformation $customerStatusInfo   Customer Status Information
	 * @param StringType                $customerNumber       Customer Number
	 * @param EmailStringType           $customerEmail        Customer Email
	 * @param StringType                $customerTelephone    Customer Telephone
	 * @param StringType                $vatIdNumber          VAT ID Number
	 * @param AddressBlockInterface     $customerAddress      Address of the customer
	 * @param AddressBlockInterface     $billingAddress       Billing address of the customer
	 * @param AddressBlockInterface     $deliveryAddress      Delivery address of the customer
	 * @param OrderItemCollection       $orderItemCollection  Collection of the order items
	 * @param OrderTotalCollection      $orderTotalCollection Total collection of the order
	 * @param OrderShippingType         $shippingType         Shipping type of the order
	 * @param OrderPaymentType          $paymentType          Payment type of the order
	 * @param CurrencyCode              $currencyCode         Currency code of the order
	 * @param LanguageCode              $languageCode         Language code of the order
	 * @param DecimalType               $totalWeight          Total weight of the order in kilo gram
	 * @param StringType                $comment              Optional comment of the order (default = null)
	 * @param IntType                    $orderStatusId        Optional id of the initial order status (default = null)
	 * @param KeyValueCollection        $addonValues          Optional key => value collection of addon values - e.g.
	 *                                                        cookies (default = null)
	 *
	 * @return int
	 */
	public function createNewCustomerOrder(IdType $customerId,
	                                       CustomerStatusInformation $customerStatusInfo,
	                                       StringType $customerNumber,
	                                       EmailStringType $customerEmail,
	                                       StringType $customerTelephone,
	                                       StringType $vatIdNumber,
	                                       AddressBlockInterface $customerAddress,
	                                       AddressBlockInterface $billingAddress,
	                                       AddressBlockInterface $deliveryAddress,
	                                       OrderItemCollection $orderItemCollection,
	                                       OrderTotalCollection $orderTotalCollection,
	                                       OrderShippingType $shippingType,
	                                       OrderPaymentType $paymentType,
	                                       CurrencyCode $currencyCode,
	                                       LanguageCode $languageCode,
	                                       DecimalType $totalWeight,
	                                       StringType $comment = null,
	                                       IntType $orderStatusId = null,
	                                       KeyValueCollection $addonValues = null)
	{
		$order = $this->orderRepository->createNew();
		
		$newOrderStatusId = ($orderStatusId
		                     !== null) ? $orderStatusId : new IntType($this->orderServiceSettings->getDefaultOrderStatusId());
		
		$order->setStatusId($newOrderStatusId);
		$order->setCustomerId($customerId);
		$order->setCustomerStatusInformation($customerStatusInfo);
		$order->setCustomerNumber($customerNumber);
		$order->setCustomerEmail($customerEmail);
		$order->setCustomerTelephone($customerTelephone);
		$order->setVatIdNumber($vatIdNumber);
		$order->setCustomerAddress($customerAddress);
		$order->setBillingAddress($billingAddress);
		$order->setDeliveryAddress($deliveryAddress);
		
		foreach($orderItemCollection->getArray() as $orderItem)
		{
			$this->orderItemRepository->addToOrder(new IdType($order->getOrderId()), $orderItem);
		}
		
		foreach($orderTotalCollection->getArray() as $orderTotal)
		{
			$this->orderTotalRepository->addToOrder(new IdType($order->getOrderId()), $orderTotal);
		}
		
		$order->setShippingType($shippingType);
		$order->setPaymentType($paymentType);
		$order->setCurrencyCode($currencyCode);
		$order->setLanguageCode($languageCode);
		$order->setTotalWeight($totalWeight);
		
		if($comment !== null)
		{
			$order->setComment($comment);
		}
		
		if($addonValues !== null)
		{
			$order->addAddonValues($addonValues);
		}
		
		$this->orderRepository->store($order);
		
		$statusComment    = ($comment !== null) ? $comment : new StringType('');
		$this->orderStatusHistoryStorage->addStatusUpdate(new IdType($order->getOrderId()), $newOrderStatusId,
		                                                  $statusComment, new BoolType(true));
		
		return $order->getOrderId();
	}
	
	
	/**
	 * Create New Standalone Order
	 *
	 * Creates a new standalone order (e.g. for guests) and returns
	 * the order ID as an integer.
	 *
	 * @param StringType            $customerNumber       Customer Number
	 * @param EmailStringType       $customerEmail        Customer Email
	 * @param StringType            $customerTelephone    Customer Telephone
	 * @param StringTYpe            $vatIdNumber          VAT ID number of the customer.
	 * @param AddressBlockInterface $customerAddress      Address of the customer
	 * @param AddressBlockInterface $billingAddress       Billing address of the customer
	 * @param AddressBlockInterface $deliveryAddress      Delivery address of the customer
	 * @param OrderItemCollection   $orderItemCollection  Collection of the order items
	 * @param OrderTotalCollection  $orderTotalCollection Total collection of the order
	 * @param OrderShippingType     $shippingType         Shipping type of the order
	 * @param OrderPaymentType      $paymentType          Payment type of the order
	 * @param CurrencyCode          $currencyCode         Currency code of the order
	 * @param LanguageCode          $languageCode         Language code of the order
	 * @param DecimalType           $totalWeight          Total weight of the order in kg.
	 * @param StringType            $comment              Optional comment of the order (default = null)
	 * @param IntType               $orderStatusId        Optional id of the initial order status (default = null)
	 * @param KeyValueCollection    $addonValues          Optional key => value collection of addon values -  e.g.
	 *                                                    cookies (default = null)
	 *
	 * @return int
	 */
	public function createNewStandaloneOrder(StringType $customerNumber,
	                                         EmailStringType $customerEmail,
	                                         StringType $customerTelephone,
	                                         StringType $vatIdNumber,
	                                         AddressBlockInterface $customerAddress,
	                                         AddressBlockInterface $billingAddress,
	                                         AddressBlockInterface $deliveryAddress,
	                                         OrderItemCollection $orderItemCollection,
	                                         OrderTotalCollection $orderTotalCollection,
	                                         OrderShippingType $shippingType,
	                                         OrderPaymentType $paymentType,
	                                         CurrencyCode $currencyCode,
	                                         LanguageCode $languageCode,
	                                         DecimalType $totalWeight,
	                                         StringType $comment = null,
	                                         IntType $orderStatusId = null,
	                                         KeyValueCollection $addonValues = null)
	{
		$order = $this->orderRepository->createNew();
		
		$order->setStatusId(new IntType($this->orderServiceSettings->getDefaultOrderStatusId()));
		
		$customerStatusInformation = MainFactory::create('CustomerStatusInformation',
		                                                 new IdType($this->orderServiceSettings->getDefaultGuestStatusId()),
		                                                 new StringType(''), new StringType(''), new DecimalType(0.0),
		                                                 new BoolType(true));
		$order->setCustomerStatusInformation($customerStatusInformation);
		
		$order->setCustomerNumber($customerNumber);
		$order->setCustomerEmail($customerEmail);
		$order->setCustomerTelephone($customerTelephone);
		$order->setVatIdNumber($vatIdNumber);
		$order->setCustomerAddress($customerAddress);
		$order->setBillingAddress($billingAddress);
		$order->setDeliveryAddress($deliveryAddress);
		
		foreach($orderItemCollection->getArray() as $orderItem)
		{
			$this->orderItemRepository->addToOrder(new IdType($order->getOrderId()), $orderItem);
		}
		
		foreach($orderTotalCollection->getArray() as $orderTotal)
		{
			$this->orderTotalRepository->addToOrder(new IdType($order->getOrderId()), $orderTotal);
		}
		
		$order->setShippingType($shippingType);
		$order->setPaymentType($paymentType);
		$order->setCurrencyCode($currencyCode);
		$order->setLanguageCode($languageCode);
		$order->setTotalWeight($totalWeight);
		
		if($comment !== null)
		{
			$order->setComment($comment);
		}
		
		if($addonValues !== null)
		{
			$order->addAddonValues($addonValues);
		}
		
		$this->orderRepository->store($order);
		
		$newOrderStatusId = ($orderStatusId
		                     !== null) ? $orderStatusId : new IntType($this->orderServiceSettings->getDefaultOrderStatusId());
		$statusComment    = ($comment !== null) ? $comment : new StringType('');
		$this->orderStatusHistoryStorage->addStatusUpdate(new IdType($order->getOrderId()), $newOrderStatusId,
		                                                  $statusComment, new BoolType(true));
		
		return $order->getOrderId();
	}
	
	
	/**
	 * Update Customer Address
	 *
	 * Updates the customers address.
	 *
	 * @param IdType                $orderId    Order ID which holds the current address
	 * @param AddressBlockInterface $newAddress New address of the customer
	 */
	public function updateCustomerAddress(IdType $orderId, AddressBlockInterface $newAddress)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setCustomerAddress($newAddress);
		$this->orderRepository->store($order);
	}
	
	
	/**
	 * Update Billing Address
	 *
	 * Updates the customers billing address.
	 *
	 * @param IdType                $orderId    Order ID which holds the current address
	 * @param AddressBlockInterface $newAddress New billing address
	 */
	public function updateBillingAddress(IdType $orderId, AddressBlockInterface $newAddress)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setBillingAddress($newAddress);
		$this->orderRepository->store($order);
	}
	
	
	/**
	 * Update Delivery Address
	 *
	 * Updates the customers delivery address.
	 *
	 * @param IdType                $orderId    Order ID which holds the current address
	 * @param AddressBlockInterface $newAddress New delivery address
	 */
	public function updateDeliveryAddress(IdType $orderId, AddressBlockInterface $newAddress)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setDeliveryAddress($newAddress);
		$this->orderRepository->store($order);
	}
	
	
	/**
	 * Add Order Item
	 *
	 * Adds an item to the order.
	 *
	 * @param IdType             $orderId   Order ID of the order to add the item
	 * @param OrderItemInterface $orderItem The order item to add
	 *
	 * @return int Id of the StoredOrderItem
	 */
	public function addOrderItem(IdType $orderId, OrderItemInterface $orderItem)
	{
		return $this->orderItemRepository->addToOrder($orderId, $orderItem);
	}
	
	
	/**
	 * Update Order Item
	 *
	 * Updates an order item.
	 *
	 * @param StoredOrderItemInterface $orderItem The order item to update
	 */
	public function updateOrderItem(StoredOrderItemInterface $orderItem)
	{
		$this->orderItemRepository->store($orderItem);
	}
	
	
	/**
	 * Remove Order Item
	 *
	 * Removes an item from an order.
	 *
	 * @param StoredOrderItemInterface $orderItem The order item to remove
	 */
	public function removeOrderItem(StoredOrderItemInterface $orderItem)
	{
		$this->orderItemRepository->deleteItemById(new IdType($orderItem->getOrderItemId()));
	}
	
	
	/**
	 * Add Order Item Attribute
	 *
	 * Adds an order item attribute to the order
	 *
	 * @param IdType                      $orderItemId        Order ID of the order item to add the attribute.
	 * @param OrderItemAttributeInterface $orderItemAttribute The order item attribute to add
	 *
	 * @return int Id of stored order item attribute.
	 */
	public function addOrderItemAttribute(IdType $orderItemId, OrderItemAttributeInterface $orderItemAttribute)
	{
		$orderItemAttributeRepository = $this->orderItemAttributeRepositoryFactory->createRepositoryByAttributeObject($orderItemAttribute);
		return $orderItemAttributeRepository->addToOrderItem($orderItemId, $orderItemAttribute);
	}
	
	
	/**
	 * Update Order Item Attribute
	 *
	 * Updates an item attribute of an order.
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemAttribute The order item attribute to update
	 */
	public function updateOrderItemAttribute(StoredOrderItemAttributeInterface $orderItemAttribute)
	{
		$orderItemAttributeRepository = $this->orderItemAttributeRepositoryFactory->createRepositoryByAttributeObject($orderItemAttribute);
		$orderItemAttributeRepository->store($orderItemAttribute);
	}
	
	
	/**
	 * Remove Order Item Attribute
	 *
	 * Removes an item attribute of an order
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemAttribute The order item attribute to remove
	 */
	public function removeOrderItemAttribute(StoredOrderItemAttributeInterface $orderItemAttribute)
	{
		$orderItemAttributeRepository = $this->orderItemAttributeRepositoryFactory->createRepositoryByAttributeObject($orderItemAttribute);
		$orderItemAttributeRepository->deleteItemAttributeById(new IdType($orderItemAttribute->getOrderItemAttributeId()));
	}
	
	
	/**
	 * Add Order Total
	 *
	 * Adds a total price to an order.
	 *
	 * @param IdType              $orderId    Order Id of the order to add the total price
	 * @param OrderTotalInterface $orderTotal Total price to add to the order
	 * 
	 * @return int Id of stored order total.
	 */
	public function addOrderTotal(IdType $orderId, OrderTotalInterface $orderTotal)
	{
		return $this->orderTotalRepository->addToOrder($orderId, $orderTotal);
	}
	
	
	/**
	 * Update Order Total
	 *
	 * Updates a total price of an order
	 *
	 * @param StoredOrderTotalInterface $orderTotal The total price of an order to update
	 */
	public function updateOrderTotal(StoredOrderTotalInterface $orderTotal)
	{
		$this->orderTotalRepository->store($orderTotal);
	}
	
	
	/**
	 * Remove Order Total
	 *
	 * Removes a total price of an order
	 *
	 * @param StoredOrderTotalInterface $orderTotal The total price of an order to remove
	 */
	public function removeOrderTotal(StoredOrderTotalInterface $orderTotal)
	{
		$this->orderTotalRepository->deleteTotalById(new IdType($orderTotal->getOrderTotalId()));
	}
	
	
	/**
	 * Update Shipping Type
	 *
	 * Updates the shipping type of an order.
	 *
	 * @param IdType            $orderId         Order ID of the order to update
	 * @param OrderShippingType $newShippingType The new shipping type
	 */
	public function updateShippingType(IdType $orderId, OrderShippingType $newShippingType)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setShippingType($newShippingType);
		$this->orderRepository->store($order);
	}
	
	
	/**
	 * Update Payment Type
	 *
	 * Updates the payment type of an order.
	 *
	 * @param IdType           $orderId        Order ID of the order to update
	 * @param OrderPaymentType $newPaymentType The new payment type
	 */
	public function updatePaymentType(IdType $orderId, OrderPaymentType $newPaymentType)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setPaymentType($newPaymentType);
		$this->orderRepository->store($order);
	}
	
	
	/**
	 * Update Comment
	 *
	 * Updates the comment of an order.
	 *
	 * @param IdType     $orderId    Order ID of the order to update
	 * @param StringType $newComment The new comment
	 */
	public function updateComment(IdType $orderId, StringType $newComment)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setComment($newComment);
		$this->orderRepository->store($order);
	}
	
	
	/**
	 * Update Order Status
	 *
	 * Updates the order status of an order.
	 *
	 * @param IdType     $orderId          Order ID of the order to update
	 * @param IntType    $newOrderStatusId The new status Id
	 * @param StringType $comment          Comment of the order status history item
	 * @param BoolType   $customerNotified Customer notified flag
	 */
	public function updateOrderStatus(IdType $orderId,
	                                  IntType $newOrderStatusId,
	                                  StringType $comment,
	                                  BoolType $customerNotified)
	{
		$order = $this->orderRepository->getById($orderId);
		$order->setStatusId($newOrderStatusId);
		$this->orderRepository->store($order);
		
		$this->orderStatusHistoryStorage->addStatusUpdate($orderId, $newOrderStatusId, $comment, $customerNotified);
	}


	/**
	 * Update Order
	 *
	 * Updates the provided order.
	 *
	 * @param \OrderInterface $order Order to update
	 *
	 * @return \OrderWriteService Same instance for chained method calls.
	 */
	public function updateOrder(OrderInterface $order)
	{
		$this->orderRepository->store($order);

		return $this;
	}

	
	/**
	 * Remove Order by ID
	 *
	 * Removes a specific order, depending on the provided order ID.
	 *
	 * @param IdType $orderId Order ID of the order to remove
	 */
	public function removeOrderById(IdType $orderId)
	{
		$this->orderRepository->deleteById($orderId);
		$this->orderStatusHistoryStorage->deleteHistory($orderId);
	}
}
