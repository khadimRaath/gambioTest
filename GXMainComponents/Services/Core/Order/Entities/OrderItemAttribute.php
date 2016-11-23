<?php

/* --------------------------------------------------------------
   OrderItemAttribute.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeInterface');

/**
 * Class OrderItemAttribute
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderItemAttribute implements OrderItemAttributeInterface
{
	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $name = '';
	
	/**
	 * Value.
	 *
	 * @var string
	 */
	protected $value = '';
	
	/**
	 * Price.
	 *
	 * @var float
	 */
	protected $price = 0.0;
	
	/**
	 * Price type.
	 *
	 * @var string
	 */
	protected $priceType = '';
	
	/**
	 * Option ID.
	 *
	 * @var IdType
	 */
	protected $optionId = 0;
	
	/**
	 * Option value ID.
	 *
	 * @var IdType
	 */
	protected $optionValueId = 0;
	
	
	/**
	 * OrderItemAttribute constructor.
	 *
	 * @param StringType $name  Order item attribute name.
	 * @param StringType $value Order item attribute value.
	 */
	public function __construct(StringType $name, StringType $value)
	{
		$this->setName($name);
		$this->setValue($value);
	}
	
	
	/**
	 * Returns the name of the order item attribute.
	 *
	 * @return string Name of the order item attribute.
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/**
	 * Returns the value of the order item attribute.
	 *
	 * @return string Value of the order item attribute.
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	
	/**
	 * Returns the price of the order item attribute.
	 *
	 * @return float Price of the order item attribute.
	 */
	public function getPrice()
	{
		return $this->price;
	}
	
	
	/**
	 * Returns the price type of the order item attribute.
	 *
	 * @return string Price type of the order item attribute.
	 */
	public function getPriceType()
	{
		return $this->priceType;
	}
	
	
	/**
	 * Returns the option ID.
	 *
	 * @return IdType Option ID.
	 */
	public function getOptionId()
	{
		return $this->optionId;
	}
	
	
	/**
	 * Returns the option value ID.
	 *
	 * @return IdType Option value ID.
	 */
	public function getOptionValueId()
	{
		return $this->optionValueId;
	}
	
	
	/**
	 * Sets the name of the order item attribute.
	 *
	 * @param StringType $name Name of the order item attribute.
	 *
	 * @return OrderItemAttribute Same instance for method chaining.
	 */
	public function setName(StringType $name)
	{
		$this->name = $name->asString();

		return $this;
	}
	
	
	/**
	 * Sets the value of the order item attribute.
	 *
	 * @param StringType $value Value of the order item attribute.
	 *
	 * @return OrderItemAttribute Same instance for method chaining.
	 */
	public function setValue(StringType $value)
	{
		$this->value = $value->asString();

		return $this;
	}
	
	
	/**
	 * Sets the price of the order item attribute.
	 *
	 * @param DecimalType $price Price of the order item attribute.
	 *
	 * @return OrderItemAttribute Same instance for method chaining.
	 */
	public function setPrice(DecimalType $price)
	{
		$this->price = $price->asDecimal();

		return $this;
	}
	
	
	/**
	 * Sets the price type of the order item attribute.
	 *
	 * @param StringType $priceType Price type of the order item attribute.
	 *
	 * @return OrderItemAttribute Same instance for method chaining.
	 */
	public function setPriceType(StringType $priceType)
	{
		$this->priceType = $priceType->asString();

		return $this;
	}
	
	
	/**
	 * Sets the option ID.
	 *
	 * @param IdType $optionId Option ID.
	 *
	 * @return OrderItemAttribute Same instance for method chaining.
	 */
	public function setOptionId(IdType $optionId)
	{
		$this->optionId = $optionId->asInt();

		return $this;
	}
	
	
	/**
	 * Set option value ID.
	 *
	 * @param IdType $optionValueId Option value ID.
	 *
	 * @return OrderItemAttribute Same instance for method chaining.
	 */
	public function setOptionValueId(IdType $optionValueId)
	{
		$this->optionValueId = $optionValueId->asInt();

		return $this;
	}
}