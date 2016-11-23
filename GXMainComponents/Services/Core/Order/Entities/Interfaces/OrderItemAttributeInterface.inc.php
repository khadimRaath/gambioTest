<?php

/* --------------------------------------------------------------
   OrderItemAttributeInterface.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemAttributeInterface
{
	/**
	 * Returns the name of the order item attribute.
	 *
	 * @return string Name of the order item attribute.
	 */
	public function getName();
	
	
	/**
	 * Returns the value of the order item attribute.
	 *
	 * @return string Value of the order item attribute.
	 */
	public function getValue();
	
	
	/**
	 * Returns the price of the order item attribute.
	 *
	 * @return float Price of the order item attribute.
	 */
	public function getPrice();
	
	
	/**
	 * Returns the price type of the order item attribute.
	 *
	 * @return string Price type of the order item attribute.
	 */
	public function getPriceType();
	
	
	/**
	 * Sets the name of the order item attribute.
	 *
	 * @param StringType $name Name of the order item attribute.
	 *
	 * @return OrderItemAttributeInterface Same instance for method chaining.
	 */
	public function setName(StringType $name);
	
	
	/**
	 * Sets the value of the order item attribute.
	 *
	 * @param StringType $value Value of the order item attribute.
	 *
	 * @return OrderItemAttributeInterface Same instance for method chaining.
	 */
	public function setValue(StringType $value);
	
	
	/**
	 * Sets the price of the order item attribute.
	 *
	 * @param DecimalType $price Price of the order item attribute.
	 *
	 * @return OrderItemAttributeInterface Same instance for method chaining.
	 */
	public function setPrice(DecimalType $price);
	
	
	/**
	 * Sets the price type of the order item attribute.
	 *
	 * @param StringType $priceType Price type of the order item attribute.
	 *
	 * @return OrderItemAttributeInterface Same instance for method chaining.
	 */
	public function setPriceType(StringType $priceType);
}