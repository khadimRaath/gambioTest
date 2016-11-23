<?php
/* --------------------------------------------------------------
   OrderInterface.inc.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderInterface
{
	/**
	 * Returns the order ID.
	 *
	 * @return int Order ID.
	 */
	public function getOrderId();
	
	
	/**
	 * Returns the unique order hash.
	 *
	 * @return string Unique order hash.
	 */
	public function getOrderHash();
	
	
	/**
	 * Returns the associated customer ID.
	 *
	 * @return int Associated customer ID.
	 */
	public function getCustomerId();
	
	
	/**
	 * Returns the associated customer email address.
	 *
	 * @return string Associated customer email address.
	 */
	public function getCustomerEmail();
	
	
	/**
	 * Returns the associated customer telephone number.
	 *
	 * @return string Associated customer telephone number.
	 */
	public function getCustomerTelephone();
	
	
	/**
	 * Returns the order status ID.
	 *
	 * @return int Order status ID.
	 */
	public function getStatusId();
	
	
	/**
	 * Sets customer number of Order
	 *
	 * @param StringType $customerNumber Customer number.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setCustomerNumber(StringType $customerNumber);
	
	
	/**
	 * Returns the customer number.
	 *
	 * @return string Customer number.
	 */
	public function getCustomerNumber();
	
	
	/**
	 * Sets the VAT ID Number.
	 *
	 * @param StringType $vatIdNumber VAT ID number.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setVatIdNumber(StringType $vatIdNumber);
	
	
	/**
	 * Returns the VAT ID number.
	 *
	 * @return string VAT ID number.
	 */
	public function getVatIdNumber();
	
	
	/**
	 * Sets the customer status information.
	 *
	 * @param CustomerStatusInformation $customerStatusInformation Customer status information.
	 */
	public function setCustomerStatusInformation(CustomerStatusInformation $customerStatusInformation);
	
	
	/**
	 * Returns the Customer status information.
	 *
	 * @return CustomerStatusInformation Customer status information.
	 */
	public function getCustomerStatusInformation();
	
	
	/**
	 * Returns the customer address.
	 *
	 * @return AddressBlockInterface Customer address.
	 */
	public function getCustomerAddress();
	
	
	/**
	 * Returns the billing address.
	 *
	 * @return AddressBlockInterface Billing address.
	 */
	public function getBillingAddress();
	
	
	/**
	 * Returns the delivery address.
	 *
	 * @return AddressBlockInterface Delivery address.
	 */
	public function getDeliveryAddress();
	
	
	/**
	 * Returns the order items collection.
	 *
	 * @return OrderItemCollection Order items collection.
	 */
	public function getOrderItems();
	
	
	/**
	 * Returns the order totals collection.
	 *
	 * @return OrderTotalCollection Order totals collection.
	 */
	public function getOrderTotals();
	
	
	/**
	 * Returns the order shipping type.
	 *
	 * @return OrderShippingType Order shipping type.
	 */
	public function getShippingType();
	
	
	/**
	 * Returns the order payment type.
	 *
	 * @return OrderPaymentType Order payment type.
	 */
	public function getPaymentType();
	
	
	/**
	 * Returns the order currency code.
	 *
	 * @return CurrencyCode Order currency code.
	 */
	public function getCurrencyCode();
	
	
	/**
	 * Returns the order language code.
	 *
	 * @return LanguageCode Order language code.
	 */
	public function getLanguageCode();
	
	
	/**
	 * Returns the order purchase datetime.
	 *
	 * @return DateTime Order purchase datetime.
	 */
	public function getPurchaseDateTime();
	
	
	/**
	 * Returns the datetime of last modification.
	 *
	 * @return DateTime Datetime of last modification.
	 */
	public function getLastModifiedDateTime();
	
	
	/**
	 * Returns the order status history.
	 *
	 * @return OrderStatusHistoryListItemCollection Order status history.
	 */
	public function getStatusHistory();
	
	
	/**
	 * Returns the order comment.
	 *
	 * @return string Order comment.
	 */
	public function getComment();
	
	
	/**
	 * Returns the order addon key value from collection.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @return string Addon value.
	 */
	public function getAddonValue(StringType $key);
	
	
	/**
	 * Returns the order addon value collection.
	 *
	 * @return EditableKeyValueCollection Order addon value collection.
	 */
	public function getAddonValues();
	
	
	/**
	 * Sets the order ID.
	 *
	 * @param IdType $id Order ID.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setOrderId(IdType $id);
	
	/**
	 * Sets the unique order hash.
	 *
	 * @param NonEmptyStringType $orderHash Unique order hash.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setOrderHash(NonEmptyStringType $orderHash);
	
	
	/**
	 * Sets the associated customer ID.
	 *
	 * @param IdType $id Customer ID.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setCustomerId(IdType $id);
	
	
	/**
	 * Sets the customer email address.
	 *
	 * @param EmailStringType $email Customer email address.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setCustomerEmail(EmailStringType $email);
	
	
	/**
	 * Sets the customer telephone number.
	 *
	 * @param StringType $telephone Customer telephone number.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setCustomerTelephone(StringType $telephone);
	
	
	/**
	 * Sets the order status ID.
	 *
	 * @param IntType $id Status ID.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setStatusId(IntType $id);
	
	
	/**
	 * Sets the customer address.
	 *
	 * @param AddressBlockInterface $address Customer address.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setCustomerAddress(AddressBlockInterface $address);
	
	
	/**
	 * Sets the billing address.
	 *
	 * @param AddressBlockInterface $address Billing address.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setBillingAddress(AddressBlockInterface $address);
	
	
	/**
	 * Sets the delivery address.
	 *
	 * @param AddressBlockInterface $address Delivery address.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setDeliveryAddress(AddressBlockInterface $address);
	
	
	/**
	 * Sets the order items collection.
	 *
	 * @param OrderItemCollection $collection Items collection.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setOrderItems(OrderItemCollection $collection);
	
	
	/**
	 * Sets the order total collection.
	 *
	 * @param OrderTotalCollection $collection Total collection.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setOrderTotals(OrderTotalCollection $collection);
	
	
	/**
	 * Sets the order shipping type.
	 *
	 * @param OrderShippingType $shippingType Shipping type.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setShippingType(OrderShippingType $shippingType);
	
	
	/**
	 * Sets the order payment type.
	 *
	 * @param OrderPaymentType $paymentType Payment type.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setPaymentType(OrderPaymentType $paymentType);
	
	
	/**
	 * Sets the order currency code.
	 *
	 * @param CurrencyCode $currencyCode Currency code.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setCurrencyCode(CurrencyCode $currencyCode);
	
	
	/**
	 * Sets the order language code.
	 *
	 * @param LanguageCode $languageCode Language code.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setLanguageCode(LanguageCode $languageCode);
	
	
	/**
	 * Sets the order purchase date time.
	 *
	 * @param DateTime $dateTime Purchase date time.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setPurchaseDateTime(DateTime $dateTime);
	
	
	/**
	 * Sets the date time of last modification.
	 *
	 * @param DateTime $lastModifiedDateTime Last modification date time
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setLastModifiedDateTime(DateTime $lastModifiedDateTime);
	
	
	/**
	 * Sets the order comment.
	 *
	 * @param StringType $comment Comment.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setComment(StringType $comment);


	/**
	 * Returns the total weight of the ordered products.
	 *
	 * @return double
	 */
	public function getTotalWeight();


	/**
	 * Sets the total weight of the ordered products.
	 *
	 * @param \DecimalType $totalWeight
	 *
	 * @return $this|OrderInterface Same instance for method chaining.
	 */
	public function setTotalWeight(DecimalType $totalWeight);


	/**
	 * Adds/updates a key value in the addon value collection.
	 *
	 * @param StringType $key   Addon key.
	 * @param StringType $value Addon value.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function setAddonValue(StringType $key, StringType $value);
	
	
	/**
	 * Adds an addon collection to the existing one.
	 *
	 * @param KeyValueCollection $addonCollection Addon collection.
	 *
	 * @return OrderInterface Same instance for method chaining.
	 */
	public function addAddonValues(KeyValueCollection $addonCollection);
}