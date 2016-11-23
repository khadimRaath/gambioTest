<?php

/* --------------------------------------------------------------
   OrderTotalInterface.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderTotalInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderTotalInterface
{
	/**
	 * Returns the title of the order total.
	 *
	 * @return string Title of the order total.
	 */
	public function getTitle();
	
	
	/**
	 * Returns the value of the order total.
	 *
	 * @return float Value of the order total.
	 */
	public function getValue();
	
	
	/**
	 * Returns the value text of the order total.
	 *
	 * @return string Value text of the order total.
	 */
	public function getValueText();
	
	
	/**
	 * Returns the class of the order total.
	 *
	 * @return string Class of the order total.
	 */
	public function getClass();
	
	
	/**
	 * Returns the sort order of the order total.
	 *
	 * @return int Sort order of the order total.
	 */
	public function getSortOrder();
	
	
	/**
	 * Sets title of the order total.
	 *
	 * @param StringType $title Title of the order total.
	 *
	 * @return OrderTotalInterface Same instance for method chaining.
	 */
	public function setTitle(StringType $title);
	
	
	/**
	 * Sets value of the order total.
	 *
	 * @param DecimalType $value Value of the order total.
	 *
	 * @return OrderTotalInterface Same instance for method chaining.
	 */
	public function setValue(DecimalType $value);
	
	
	/**
	 * Sets value text of the order total.
	 *
	 * @param StringType $valueText Value text of the order total.
	 *
	 * @return OrderTotalInterface Same instance for method chaining.
	 */
	public function setValueText(StringType $valueText);
	
	
	/**
	 * Sets class of the order total.
	 *
	 * @param StringType $class Class of the order total.
	 *
	 * @return OrderTotalInterface Same instance for method chaining.
	 */
	public function setClass(StringType $class);
	
	
	/**
	 * Sets sort order of the order total.
	 *
	 * @param IntType $sortOrder Sort order of the order total.
	 *
	 * @return OrderTotalInterface Same instance for method chaining.
	 */
	public function setSortOrder(IntType $sortOrder);
}