<?php

/* --------------------------------------------------------------
   ProductAttribute.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttribute
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Entities
 */
class ProductAttribute implements ProductAttributeInterface
{
	/**
	 * @var int
	 */
	protected $optionId = 0;

	/**
	 * @var int
	 */
	protected $optionValueId = 0;

	/**
	 * @var float
	 */
	protected $price = 0.0;

	/**
	 * @var string
	 */
	protected $priceType = '';

	/**
	 * @var float
	 */
	protected $weight = 0.0;

	/**
	 * @var string
	 */
	protected $weightType = '';

	/**
	 * @var string
	 */
	protected $attributeModel = '';

	/**
	 * @var string
	 */
	protected $attributeEan = '';

	/**
	 * @var float
	 */
	protected $stock = 0.0;

	/**
	 * @var int
	 */
	protected $sortOrder = 0;

	/**
	 * @var int
	 */
	protected $vpeId = 0;

	/**
	 * @var float
	 */
	protected $vpeValue = 0.0;


	/**
	 * Initialize the product attribute.
	 *
	 * @param IdType $optionId Option id of product attribute.
	 * @param IdType $valueId  Option value id of product attribute.
	 */
	public function __construct(IdType $optionId, IdType $valueId)
	{
		$this->optionId      = $optionId->asInt();
		$this->optionValueId = $valueId->asInt();
	}


	/**
	 * Returns the option id of the product attribute.
	 *
	 * @return int Option id of product attribute.
	 */
	public function getOptionId()
	{
		return $this->optionId;
	}


	/**
	 * Sets the option id of the product attribute.
	 *
	 * @param IdType $optionId Option id of product attribute.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setOptionId(IdType $optionId)
	{
		$this->optionId = $optionId->asInt();

		return $this;
	}


	/**
	 * Returns option value id of the product attribute.
	 *
	 * @return int Option value id of product attribute.
	 */
	public function getOptionValueId()
	{
		return $this->optionValueId;
	}


	/**
	 * Sets the option value id.
	 *
	 * @param IdType $optionValueId Option value id of product attribute.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setOptionValueId(IdType $optionValueId)
	{
		$this->optionValueId = $optionValueId->asInt();

		return $this;
	}


	/**
	 * Returns the price of the product attribute.
	 *
	 * @return float Price of product attribute.
	 */
	public function getPrice()
	{
		return $this->price;
	}


	/**
	 * Sets the price of the product attribute.
	 *
	 * @param DecimalType $price New price of product attribute.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setPrice(DecimalType $price)
	{
		$this->price = $price->asDecimal();

		return $this;
	}


	/**
	 * Returns the price type of the product attribute.
	 *
	 * @return string Price type of product attribute.
	 */
	public function getPriceType()
	{
		return $this->priceType;
	}


	/**
	 * Sets the price type of the product attribute.
	 *
	 * @param StringType $priceType New price type.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setPriceType(StringType $priceType)
	{
		$this->priceType = $priceType->asString();

		return $this;
	}


	/**
	 * Returns the weight of the product attribute.
	 *
	 * @return float Weight of product attribute.
	 */
	public function getWeight()
	{
		return $this->weight;
	}


	/**
	 * Sets the weight of the product attribute.
	 *
	 * @param DecimalType $weight New weight.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setWeight(DecimalType $weight)
	{
		$this->weight = $weight->asDecimal();

		return $this;
	}


	/**
	 * Returns the weight type of the product attribute.
	 *
	 * @return string Weight type of product attribute.
	 */
	public function getWeightType()
	{
		return $this->weightType;
	}


	/**
	 * Sets the weight type of the product attribute.
	 *
	 * @param StringType $weightType New weight type.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setWeightType(StringType $weightType)
	{
		$this->weightType = $weightType->asString();

		return $this;
	}


	/**
	 * Returns the attribute model of the product attribute.
	 *
	 * @return string Model of product attribute.
	 */
	public function getAttributeModel()
	{
		return $this->attributeModel;
	}


	/**
	 * Sets the attribute model of the product attribute.
	 *
	 * @param StringType $attributeModel New attribute model.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setAttributeModel(StringType $attributeModel)
	{
		$this->attributeModel = $attributeModel->asString();

		return $this;
	}


	/**
	 * Returns the ean of the product attribute.
	 *
	 * @return string Ean of product attribute.
	 */
	public function getAttributeEan()
	{
		return $this->attributeEan;
	}


	/**
	 * Sets the ean of the product attribute.
	 *
	 * @param StringType $attributeEan New ean.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setAttributeEan(StringType $attributeEan)
	{
		$this->attributeEan = $attributeEan->asString();

		return $this;
	}


	/**
	 * Returns the stock of the product attribute.
	 *
	 * @return float Stock of product attribute.
	 */
	public function getStock()
	{
		return $this->stock;
	}


	/**
	 * Sets the stock of the product attribute.
	 *
	 * @param DecimalType $stock New stock
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setStock(DecimalType $stock)
	{
		$this->stock = $stock->asDecimal();

		return $this;
	}


	/**
	 * Returns the sort order of the product attribute.
	 *
	 * @return int Sort order of product attribute.
	 */
	public function getSortOrder()
	{
		return $this->sortOrder;
	}


	/**
	 * Sets the sort order of the product attribute.
	 *
	 * @param IntType $sortOrder New sort order position.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setSortOrder(IntType $sortOrder)
	{
		$this->sortOrder = $sortOrder->asInt();

		return $this;
	}


	/**
	 * Returns the vpe id of the product attribute.
	 *
	 * @return int Vpe id of product attribute.
	 */
	public function getVpeId()
	{
		return $this->vpeId;
	}


	/**
	 * Sets the vpe id of the product attribute.
	 *
	 * @param IdType $vpeId New vpe id.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setVpeId(IdType $vpeId)
	{
		$this->vpeId = $vpeId->asInt();

		return $this;
	}


	/**
	 * Returns the product vpe value of the product attribute.
	 *
	 * @return float Vpe value of product attribute.
	 */
	public function getVpeValue()
	{
		return $this->vpeValue;
	}


	/**
	 * Sets the vpe value of the product attribute.
	 *
	 * @param DecimalType $vpeValue New vpe value.
	 *
	 * @return ProductAttribute|$this Same instance for chained method calls.
	 */
	public function setVpeValue(DecimalType $vpeValue)
	{
		$this->vpeValue = $vpeValue->asDecimal();

		return $this;
	}
}