<?php

/* --------------------------------------------------------------
   ProductAttributeInterface.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductAttributeInterface
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interfaces
 */
interface ProductAttributeInterface
{
	/**
	 * Returns the option id of the product attribute.
	 *
	 * @return int Option id of product attribute.
	 */
	public function getOptionId();


	/**
	 * Sets the option id of the product attribute.
	 *
	 * @param IdType $optionId Option id of product attribute.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setOptionId(IdType $optionId);


	/**
	 * Returns option value id of the product attribute.
	 *
	 * @return int Option value id of product attribute.
	 */
	public function getOptionValueId();


	/**
	 * Sets the option value id.
	 *
	 * @param IdType $optionValueId Option value id of product attribute.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setOptionValueId(IdType $optionValueId);


	/**
	 * Returns the price of the product attribute.
	 *
	 * @return float Price of product attribute.
	 */
	public function getPrice();


	/**
	 * Sets the price of the product attribute.
	 *
	 * @param DecimalType $price New price of product attribute.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setPrice(DecimalType $price);


	/**
	 * Returns the price type of the product attribute.
	 *
	 * @return string Price type of product attribute.
	 */
	public function getPriceType();


	/**
	 * Sets the price type of the product attribute.
	 *
	 * @param StringType $priceType New price type.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setPriceType(StringType $priceType);


	/**
	 * Returns the weight of the product attribute.
	 *
	 * @return float Weight of product attribute.
	 */
	public function getWeight();


	/**
	 * Sets the weight of the product attribute.
	 *
	 * @param DecimalType $weight New weight.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setWeight(DecimalType $weight);


	/**
	 * Returns the weight type of the product attribute.
	 *
	 * @return string Weight type of product attribute.
	 */
	public function getWeightType();


	/**
	 * Sets the weight type of the product attribute.
	 *
	 * @param StringType $weightType New weight type.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setWeightType(StringType $weightType);


	/**
	 * Returns the attribute model of the product attribute.
	 *
	 * @return string Model of product attribute.
	 */
	public function getAttributeModel();


	/**
	 * Sets the attribute model of the product attribute.
	 *
	 * @param StringType $attributeModel New attribute model.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setAttributeModel(StringType $attributeModel);


	/**
	 * Returns the ean of the product attribute.
	 *
	 * @return string Ean of product attribute.
	 */
	public function getAttributeEan();


	/**
	 * Sets the ean of the product attribute.
	 *
	 * @param StringType $attributeEan New ean.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setAttributeEan(StringType $attributeEan);


	/**
	 * Returns the stock of the product attribute.
	 *
	 * @return float Stock of product attribute.
	 */
	public function getStock();


	/**
	 * Sets the stock of the product attribute.
	 *
	 * @param DecimalType $stock New stock
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setStock(DecimalType $stock);


	/**
	 * Returns the sort order of the product attribute.
	 *
	 * @return int Sort order of product attribute.
	 */
	public function getSortOrder();


	/**
	 * Sets the sort order of the product attribute.
	 *
	 * @param IntType $sortOrder New sort order position.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setSortOrder(IntType $sortOrder);


	/**
	 * Returns the vpe id of the product attribute.
	 *
	 * @return int Vpe id of product attribute.
	 */
	public function getVpeId();


	/**
	 * Sets the vpe id of the product attribute.
	 *
	 * @param IdType $vpeId New vpe id.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setVpeId(IdType $vpeId);


	/**
	 * Returns the product vpe value of the product attribute.
	 *
	 * @return float Vpe value of product attribute.
	 */
	public function getVpeValue();


	/**
	 * Sets the vpe value of the product attribute.
	 *
	 * @param DecimalType $vpeValue New vpe value.
	 *
	 * @return ProductAttributeInterface|$this Same instance for chained method calls.
	 */
	public function setVpeValue(DecimalType $vpeValue);
}