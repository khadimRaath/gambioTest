<?php
/* --------------------------------------------------------------
   OrderWriteOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderWriteOverload
 *
 * This sample overload demonstrates the overloading of the OrderWriteService class. After enabling this sample
 * the included OrderWriteService methods will log a new debug entry every time they are executed.
 *
 * Notice: This service is not currently used within the shop (the integration will come gradually). You can try
 * this overload with the sample files that reside in the docs/PHP/samples/order-service directory.
 *
 * @see OrderWriteService
 */
class OrderWriteOverload extends OrderWriteOverload_parent
{
	/**
	 * Overloaded "createNewCustomerOrder" method.
	 *
	 * @param IdType                    $customerId
	 * @param CustomerStatusInformation $customerStatusInfo
	 * @param StringType                $customerNumber
	 * @param EmailStringType           $customerEmail
	 * @param StringType                $customerTelephone
	 * @param StringType                $vatIdNumber
	 * @param AddressBlockInterface     $customerAddress
	 * @param AddressBlockInterface     $billingAddress
	 * @param AddressBlockInterface     $deliveryAddress
	 * @param OrderItemCollection       $orderItemCollection
	 * @param OrderTotalCollection      $orderTotalCollection
	 * @param OrderShippingType         $shippingType
	 * @param OrderPaymentType          $paymentType
	 * @param CurrencyCode              $currencyCode
	 * @param LanguageCode              $languageCode
	 * @param StringType|null           $comment
	 * @param IdType|null               $orderStatusId
	 * @param KeyValueCollection|null   $addonValues
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
	                                       StringType $comment = null,
	                                       IdType $orderStatusId = null,
	                                       KeyValueCollection $addonValues = null)
	{
		$newOrderId = parent::createNewCustomerOrder($customerId, $customerStatusInfo, $customerNumber, $customerEmail,
		                                             $customerTelephone, $vatIdNumber, $customerAddress,
		                                             $billingAddress, $deliveryAddress, $orderItemCollection,
		                                             $orderTotalCollection, $shippingType, $paymentType, $currencyCode,
		                                             $languageCode, $comment, $orderStatusId, $addonValues);
		
		$this->_createDebugLog('OrderWriteService::createNewCustomerOrder >> Created new customer order with ID ='
		                       . $newOrderId);
		
		return $newOrderId;
	}
	
	
	/**
	 * Overloaded "createNewStandaloneOrder" method.
	 *
	 * @param StringType              $customerNumber
	 * @param EmailStringType         $customerEmail
	 * @param StringType              $customerTelephone
	 * @param StringType              $vatIdNumber
	 * @param AddressBlockInterface   $customerAddress
	 * @param AddressBlockInterface   $billingAddress
	 * @param AddressBlockInterface   $deliveryAddress
	 * @param OrderItemCollection     $orderItemCollection
	 * @param OrderTotalCollection    $orderTotalCollection
	 * @param OrderShippingType       $shippingType
	 * @param OrderPaymentType        $paymentType
	 * @param CurrencyCode            $currencyCode
	 * @param LanguageCode            $languageCode
	 * @param StringType|null         $comment
	 * @param IdType|null             $orderStatusId
	 * @param KeyValueCollection|null $addonValues
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
	                                         StringType $comment = null,
	                                         IdType $orderStatusId = null,
	                                         KeyValueCollection $addonValues = null)
	{
		$newOrderId = parent::createNewStandaloneOrder($customerNumber, $customerEmail, $customerTelephone,
		                                               $vatIdNumber, $customerAddress, $billingAddress,
		                                               $deliveryAddress, $orderItemCollection, $orderTotalCollection,
		                                               $shippingType, $paymentType, $currencyCode, $languageCode,
		                                               $comment, $orderStatusId, $addonValues);
		
		$this->_createDebugLog('OrderWriteService::createNewStandaloneOrder >> Created new stand alone order with ID ='
		                       . $newOrderId);
		
		return $newOrderId;
	}
	
	
	/**
	 * Overloaded "updateCustomerAddress" method.
	 *
	 * @param IdType                $orderId
	 * @param AddressBlockInterface $newAddress
	 */
	public function updateCustomerAddress(IdType $orderId, AddressBlockInterface $newAddress)
	{
		$this->_createDebugLog('OrderWriteService::updateCustomerAddress >> Updated customer address for order with ID = '
		                       . $orderId->asInt());
		
		parent::updateCustomerAddress($orderId, $newAddress);
	}
	
	
	/**
	 * Overloaded "updateBillingAddress" method.
	 *
	 * @param IdType                $orderId
	 * @param AddressBlockInterface $newAddress
	 */
	public function updateBillingAddress(IdType $orderId, AddressBlockInterface $newAddress)
	{
		$this->_createDebugLog('OrderWriteService::updateBillingAddress >> Updated billing address for order with ID = '
		                       . $orderId->asInt());
		
		parent::updateBillingAddress($orderId, $newAddress);
	}
	
	
	/**
	 * Overloaded "updateDeliveryAddress" method.
	 *
	 * @param IdType                $orderId
	 * @param AddressBlockInterface $newAddress
	 */
	public function updateDeliveryAddress(IdType $orderId, AddressBlockInterface $newAddress)
	{
		$this->_createDebugLog('OrderWriteService::updateDeliveryAddress >> Updated delivery address for order with ID = '
		                       . $orderId->asInt());
		
		parent::updateDeliveryAddress($orderId, $newAddress);
	}
	
	
	/**
	 * Overloaded "addOrderItem" method.
	 *
	 * @param IdType             $orderId
	 * @param OrderItemInterface $orderItem
	 *
	 * @return int
	 */
	public function addOrderItem(IdType $orderId, OrderItemInterface $orderItem)
	{
		$this->_createDebugLog('OrderWriteService::addOrderItem >> Added order item to order with ID = '
		                       . $orderId->asInt());
		
		return parent::addOrderItem($orderId, $orderItem);
	}
	
	
	/**
	 * Overloaded "updateOrderItem" method.
	 *
	 * @param StoredOrderItemInterface $orderItem
	 */
	public function updateOrderItem(StoredOrderItemInterface $orderItem)
	{
		$this->_createDebugLog('OrderWriteService::updateOrderItem >> Updated order item with ID = '
		                       . $orderItem->getOrderItemId());
		
		parent::updateOrderItem($orderItem);
	}
	
	
	/**
	 * Create new debug log entry.
	 *
	 * @param string $message
	 */
	private function _createDebugLog($message)
	{
		$logControl = LogControl::get_instance();
		$logControl->notice($message);
	}
}
