<?php
/* --------------------------------------------------------------
   OrderWriteServiceInterface.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderWriteServiceInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderWriteServiceInterface
{
	/**
	 * Creates a new customer order and returns the order ID as an integer.
	 *
	 * @param IdType                    $customerId           Customer ID.
	 * @param CustomerStatusInformation $customerStatusInfo   Customer Status Information.
	 * @param StringType                $customerNumber       Customer Number.
	 * @param EmailStringType           $customerEmail        Customer Email.
	 * @param StringType                $customerTelephone    Customer Telephone.
	 * @param StringType                $vatIdNumber          VAT ID Number.
	 * @param AddressBlockInterface     $customerAddress      Address of the customer.
	 * @param AddressBlockInterface     $billingAddress       Billing address of the customer.
	 * @param AddressBlockInterface     $deliveryAddress      Delivery address of the customer.
	 * @param OrderItemCollection       $orderItemCollection  Collection of the order items.
	 * @param OrderTotalCollection      $orderTotalCollection Total collection of the order.
	 * @param OrderShippingType         $shippingType         Shipping type of the order.
	 * @param OrderPaymentType          $paymentType          Payment type of the order.
	 * @param CurrencyCode              $currencyCode         Currency code of the order.
	 * @param LanguageCode              $languageCode         Language code of the order.
	 * @param StringType                $comment              Optional comment of the order (default = null).
	 * @param DecimalType               $totalWeight          Total weight of the order in kg.                                                      
	 * @param IntType                   $orderStatusId        Optional id of the initial order status (default = null).
	 * @param KeyValueCollection        $addonValues          Optional key => value collection of addon values - e.g.
	 *                                                        cookies (default = null).
	 *
	 * @return int Order ID.
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
	                                       KeyValueCollection $addonValues = null);
	
	
	/**
	 * Creates a new standalone order (e.g. for guests) and returns the order ID as an integer.
	 *
	 * @param StringType            $customerNumber       Customer Number.
	 * @param EmailStringType       $customerEmail        Customer Email.
	 * @param StringType            $customerTelephone    Customer Telephone.
	 * @param StringTYpe            $vatIdNumber          VAT ID number of the customer.
	 * @param AddressBlockInterface $customerAddress      Address of the customer.
	 * @param AddressBlockInterface $billingAddress       Billing address of the customer.
	 * @param AddressBlockInterface $deliveryAddress      Delivery address of the customer.
	 * @param OrderItemCollection   $orderItemCollection  Collection of the order items.
	 * @param OrderTotalCollection  $orderTotalCollection Total collection of the order.
	 * @param OrderShippingType     $shippingType         Shipping type of the order.
	 * @param OrderPaymentType      $paymentType          Payment type of the order.
	 * @param CurrencyCode          $currencyCode         Currency code of the order.
	 * @param LanguageCode          $languageCode         Language code of the order.
	 * @param DecimalType           $totalWeight          Total weight of the order in kg.
	 * @param StringType            $comment              Optional comment of the order (default = null).
	 * @param IntType               $orderStatusId        Optional id of the initial order status (default = null).
	 * @param KeyValueCollection    $addonValues          Optional key => value collection of addon values - e.g.
	 *                                                    cookies (default = null).
	 *                                                    
	 * @return int Order ID.
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
	                                         KeyValueCollection $addonValues = null);
	
	
	/**
	 * Updates the customers address.
	 *
	 * @param IdType                $orderId    Order ID which holds the current address.
	 * @param AddressBlockInterface $newAddress New address of the customer.
	 */
	public function updateCustomerAddress(IdType $orderId, AddressBlockInterface $newAddress);
	
	
	/**
	 * Updates the customers billing address.
	 *
	 * @param IdType                $orderId    Order ID which holds the current address.
	 * @param AddressBlockInterface $newAddress New billing address.
	 */
	public function updateBillingAddress(IdType $orderId, AddressBlockInterface $newAddress);
	
	
	/**
	 * Updates the customers delivery address.
	 *
	 * @param IdType                $orderId    Order ID which holds the current address.
	 * @param AddressBlockInterface $newAddress New delivery address.
	 */
	public function updateDeliveryAddress(IdType $orderId, AddressBlockInterface $newAddress);
	
	
	/**
	 * Adds an item to the order.
	 *
	 * @param IdType             $orderId   Order ID of the order to add the item.
	 * @param OrderItemInterface $orderItem The order item to add.
	 *
	 * @return int ID of the StoredOrderItem.
	 */
	public function addOrderItem(IdType $orderId, OrderItemInterface $orderItem);
	
	
	/**
	 * Updates an order item.
	 *
	 * @param StoredOrderItemInterface $orderItem The order item to update.
	 */
	public function updateOrderItem(StoredOrderItemInterface $orderItem);
	
	
	/**
	 * Removes an item from an order.
	 *
	 * @param StoredOrderItemInterface $orderItem The order item to remove.
	 */
	public function removeOrderItem(StoredOrderItemInterface $orderItem);
	
	
	/**
	 * Adds an order item attribute to the order.
	 *
	 * @param IdType                      $orderItemId        Order ID of the order item to add the attribute.
	 * @param OrderItemAttributeInterface $orderItemAttribute The order item attribute to add.
	 *
	 * @return int ID of stored order item attribute.
	 */
	public function addOrderItemAttribute(IdType $orderItemId, OrderItemAttributeInterface $orderItemAttribute);
	
	
	/**
	 * Updates an item attribute of an order.
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemAttribute The order item attribute to update.
	 */
	public function updateOrderItemAttribute(StoredOrderItemAttributeInterface $orderItemAttribute);
	
	
	/**
	 * Removes an item attribute of an order.
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemAttribute The order item attribute to remove.
	 */
	public function removeOrderItemAttribute(StoredOrderItemAttributeInterface $orderItemAttribute);
	
	
	/**
	 * Adds a total price to an order.
	 *
	 * @param IdType              $orderId    Order ID of the order to add the total price.
	 * @param OrderTotalInterface $orderTotal Total price to add to the order.
	 *
	 * @return int ID of stored order total.
	 */
	public function addOrderTotal(IdType $orderId, OrderTotalInterface $orderTotal);
	
	
	/**
	 * Updates a total price of an order.
	 *
	 * @param StoredOrderTotalInterface $orderTotal The total price of an order to update.
	 */
	public function updateOrderTotal(StoredOrderTotalInterface $orderTotal);
	
	
	/**
	 * Removes a total price of an order.
	 *
	 * @param StoredOrderTotalInterface $orderTotal The total price of an order to remove.
	 */
	public function removeOrderTotal(StoredOrderTotalInterface $orderTotal);
	
	
	/**
	 * Updates the shipping type of an order.
	 *
	 * @param IdType            $orderId         Order ID of the order to update.
	 * @param OrderShippingType $newShippingType The new shipping type.
	 */
	public function updateShippingType(IdType $orderId, OrderShippingType $newShippingType);
	
	
	/**
	 * Updates the payment type of an order.
	 *
	 * @param IdType           $orderId        Order ID of the order to update.
	 * @param OrderPaymentType $newPaymentType The new payment type.
	 */
	public function updatePaymentType(IdType $orderId, OrderPaymentType $newPaymentType);
	
	
	/**
	 * Updates the comment of an order.
	 *
	 * @param IdType     $orderId    Order ID of the order to update.
	 * @param StringType $newComment The new comment.
	 */
	public function updateComment(IdType $orderId, StringType $newComment);
	
	
	/**
	 * Updates the order status of an order.
	 *
	 * @param IdType     $orderId          Order ID of the order to update.
	 * @param IntType     $newOrderStatusId The new status ID.
	 * @param StringType $comment          Comment of the order status history item.
	 * @param BoolType   $customerNotified Customer notified flag.
	 */
	public function updateOrderStatus(IdType $orderId,
	                                  IntType $newOrderStatusId,
	                                  StringType $comment,
	                                  BoolType $customerNotified);


	/**
	 * Updates the provided order.
	 *
	 * @param OrderInterface $order Order to update.
	 */
	public function updateOrder(OrderInterface $order);

	
	/**
	 * Removes a specific order, depending on the provided order ID.
	 *
	 * @param IdType $orderId Order ID of the order to remove.
	 */
	public function removeOrderById(IdType $orderId);
}