<?php
/* --------------------------------------------------------------
   OrderTotal.php 2015-11-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class OrderTotal
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderTotal implements OrderTotalInterface
{
	/**
	 * Title.
	 * @var string
	 */
	protected $title = '';
	
	/**
	 * Value.
	 * @var float
	 */
	protected $value = 0.00;
	
	/**
	 * Value text.
	 * @var string
	 */
	protected $valueText = '';
	
	/**
	 * Class name.
	 * @var string
	 */
	protected $class = '';
	
	/**
	 * Sort order.
	 * @var int
	 */
	protected $sortOrder = 0;
	
	
	/**
	 * OrderTotal constructor.
	 *
	 * @param StringType  $title     Order total title.
	 * @param DecimalType $value     Order total value
	 * @param StringType  $valueText Order total value text.
	 * @param StringType  $class     Class name.
	 * @param IntType     $sortOrder Sort order.
	 */
	public function __construct(StringType $title,
	                            DecimalType $value,
	                            StringType $valueText = null,
	                            StringType $class = null,
	                            IntType $sortOrder = null)
	{
		$this->title     = $title->asString();
		$this->value     = $value->asDecimal();
		$this->valueText = (null !== $valueText) ? $valueText->asString() : '';
		$this->class     = (null !== $class) ? $class->asString() : '';
		$this->sortOrder = (null !== $sortOrder) ? $sortOrder->asInt() : 0;
	}
	
	
	/**
	 * Returns the title of the order total.
	 *
	 * @return string Title of the order total.
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	
	/**
	 * Returns the value of the order total.
	 *
	 * @return float Value of the order total.
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	
	/**
	 * Returns the value text of the order total.
	 *
	 * @return string Value text of the order total.
	 */
	public function getValueText()
	{
		return $this->valueText;
	}
	
	
	/**
	 * Returns the class of the order total.
	 *
	 * @return string Class of the order total.
	 */
	public function getClass()
	{
		return $this->class;
	}
	
	
	/**
	 * Returns the sort order of the order total.
	 *
	 * @return int Sort order of the order total.
	 */
	public function getSortOrder()
	{
		return $this->sortOrder;
	}
	
	
	/**
	 * Sets title of the order total.
	 *
	 * @param StringType $title Title of the order total.
	 *
	 * @return OrderTotal Same instance for method chaining.
	 */
	public function setTitle(StringType $title)
	{
		$this->title = $title->asString();
	}
	
	
	/**
	 * Sets value of the order total.
	 *
	 * @param DecimalType $value Value of the order total.
	 *
	 * @return OrderTotal Same instance for method chaining.
	 */
	public function setValue(DecimalType $value)
	{
		$this->value = $value->asDecimal();
	}
	
	
	/**
	 * Sets value text of the order total.
	 *
	 * @param StringType $valueText Value text of the order total.
	 *
	 * @return OrderTotal Same instance for method chaining.
	 */
	public function setValueText(StringType $valueText)
	{
		$this->valueText = $valueText->asString();
	}
	
	
	/**
	 * Sets class of the order total.
	 *
	 * @param StringType $class Class of the order total.
	 *
	 * @return OrderTotal Same instance for method chaining.
	 */
	public function setClass(StringType $class)
	{
		$this->class = $class->asString();
	}
	
	
	/**
	 * Sets sort order of the order total.
	 *
	 * @param IntType $sortOrder Sort order of the order total.
	 *
	 * @return OrderTotal Same instance for method chaining.
	 */
	public function setSortOrder(IntType $sortOrder)
	{
		$this->sortOrder = $sortOrder->asInt();
	}
}