<?php

/* --------------------------------------------------------------
   OrderItemProperty.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeInterface');

/**
 * Class OrderItemProperty
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderItemProperty implements OrderItemAttributeInterface
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
	 * Combinations ID.
	 *
	 * @var int
	 */
	protected $combisId = 0;
	
	
	/**
	 * OrderItemProperty constructor.
	 *
	 * @param StringType $name  Order item property name.
	 * @param StringType $value Order item property value.
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
	 * Returns the combis ID of the order item property.
	 *
	 * @return int Combis ID of the order item property
	 */
	public function getCombisId()
	{
		return $this->combisId;
	}
	
	
	/**
	 * Sets the name of the order item attribute.
	 *
	 * @param StringType $name Name of the order item attribute.
	 *
	 * @return OrderItemProperty Same instance for method chaining.
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
	 * @return OrderItemProperty Same instance for method chaining.
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
	 * @return OrderItemProperty Same instance for method chaining.
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
	 * @return OrderItemProperty Same instance for method chaining.
	 */
	public function setPriceType(StringType $priceType)
	{
		$this->priceType = $priceType->asString();
		
		return $this;
	}
	
	
	/**
	 * Sets the combis ID of the order item property.
	 *
	 * @param IdType $combisId Combis ID.
	 *
	 * @return OrderItemProperty Same instance for method chaining.
	 */
	public function setCombisId(IdType $combisId)
	{
		$this->combisId = $combisId->asInt();

		return $this;
	}
}