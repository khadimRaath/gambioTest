<?php

/* --------------------------------------------------------------
   OrderItemInterface.inc.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemInterface
{
	/**
	 * Returns the product model of the order item.
	 *
	 * @return string Product model of the order item.
	 */
	public function getProductModel();
	
	
	/**
	 * Returns the name of the order item.
	 *
	 * @return string Name of the order item.
	 */
	public function getName();
	
	
	/**
	 * Returns the price of the order item.
	 *
	 * @return double Price of the order item.
	 */
	public function getPrice();
	
	
	/**
	 * Returns the quantity of the order item.
	 *
	 * @return double Quantity of the order item.
	 */
	public function getQuantity();
	
	
	/**
	 * Returns the final price of the order item.
	 *
	 * @return float Final price of the order item.
	 */
	public function getFinalPrice();
	
	
	/**
	 * Returns the tax of the order item.
	 *
	 * @return float Tax of the order item.
	 */
	public function getTax();
	
	
	/**
	 * Is tax of the order item allowed?
	 *
	 * @return bool Is tax of the order item allowed?
	 */
	public function isTaxAllowed();
	
	
	/**
	 * Returns the amount of discount of the order item.
	 *
	 * @return float Amount of discount of the order item.
	 */
	public function getDiscountMade();
	
	
	/**
	 * Returns the shipping time of the order item.
	 *
	 * @return string Shipping time of the order item.
	 */
	public function getShippingTimeInfo();
	
	
	/**
	 * Returns the attributes of the order item.
	 *
	 * @return OrderItemAttributeCollection Attributes of the order item.
	 */
	public function getAttributes();
	
	
	/**
	 * Returns the name of quantity unit of the order item.
	 *
	 * @return string Name of quantity unit of the order item.
	 */
	public function getQuantityUnitName();
	
	
	/**
	 * Returns the checkout information of the order item.
	 *
	 * @return string Checkout information of the order item.
	 */
	public function getCheckoutInformation();
	
	
	/**
	 * Returns the download information collection of the order item.
	 *
	 * @return OrderItemDownloadInformationCollection Download information collection of the order item.
	 */
	public function getDownloadInformation();
	
	
	/**
	 * Sets the product model of the order item.
	 *
	 * @param StringType $model Model of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setProductModel(StringType $model);
	
	
	/**
	 * Sets the name of the order item.
	 *
	 * @param StringType $name Name of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setName(StringType $name);
	
	
	/**
	 * Sets the price of the order item.
	 *
	 * @param DecimalType $price Price of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setPrice(DecimalType $price);
	
	
	/**
	 * Sets the quantity of the order item.
	 *
	 * @param DecimalType $quantity Quantity of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setQuantity(DecimalType $quantity);
	
	
	/**
	 * Sets the tax of the order item.
	 *
	 * @param DecimalType $tax Tax of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setTax(DecimalType $tax);
	
	
	/**
	 * Sets whether tax of the OrderItem is allowed or not.
	 *
	 * @param BoolType $allow Tax allowed or not?
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setTaxAllowed(BoolType $allow);
	
	
	/**
	 * Sets the discount of the order item.
	 *
	 * @param DecimalType $discount Discount of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setDiscountMade(DecimalType $discount);
	
	
	/**
	 * Sets the shipping time of the order item.
	 *
	 * @param StringType $time Shipping time of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setShippingTimeInfo(StringType $time);
	
	
	/**
	 * Sets the attributes of the order item.
	 *
	 * @param OrderItemAttributeCollection $attributeCollection Attributes of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setAttributes(OrderItemAttributeCollection $attributeCollection);
	
	
	/**
	 * Sets the name of quantity unit of the order item.
	 *
	 * @param StringType $name Name of quantity unit of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setQuantityUnitName(StringType $name);
	
	
	/**
	 * Sets the checkout information.
	 *
	 * @param StringType $checkoutInformation Contains the checkout info of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setCheckoutInformation(StringType $checkoutInformation);
	
	
	/**
	 * Sets the download information of the order item.
	 *
	 * @param OrderItemDownloadInformationCollection $downloads Download information collection of the order item.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setDownloadInformation(OrderItemDownloadInformationCollection $downloads);
	
	
	/**
	 * Returns the addon collection of the order item.
	 *
	 * @return EditableKeyValueCollection The addon collection of the order item.
	 */
	public function getAddonValues();
	
	
	/**
	 * Returns the order addon key value from collection.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @throws InvalidArgumentException On invalid arguments.
	 * @return string Addon value.
	 */
	public function getAddonValue(StringType $key);
	
	
	/**
	 * Adds/updates a key value in the addon value collection.
	 *
	 * @param StringType $key   Addon key.
	 * @param StringType $value Addon value.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function setAddonValue(StringType $key, StringType $value);
	
	
	/**
	 * Merges the existing addon values with new ones.
	 *
	 * @param KeyValueCollection $addonValues Contains the new addon values to be merged with the existing ones.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function addAddonValues(KeyValueCollection $addonValues);
	
	
	/**
	 * Deletes a specific addon value entry by key.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @return OrderItemInterface Same instance for method chaining.
	 */
	public function deleteAddonValue(StringType $key);
}