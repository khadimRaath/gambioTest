<?php

/* --------------------------------------------------------------
   ProductAttributeFactory.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttributeFactory
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Factories
 */
class ProductAttributeFactory implements ProductAttributeFactoryInterface
{
	/**
	 * Creates a new product attribute instance.
	 *
	 * @param IdType $optionId Option id of the created product attribute instance.
	 * @param IdType $valueId  Value id of the created product attribute instance.
	 *
	 * @return ProductAttribute A new product attribute instance.
	 */
	public function createProductAttribute(IdType $optionId, IdType $valueId)
	{
		return MainFactory::create('ProductAttribute', $optionId, $valueId);
	}


	/**
	 * Creates a new stored product attribute instance.
	 *
	 * @param IdType $productAttributeId Id of the created stored product attribute instance.
	 *
	 * @return StoredProductAttribute A new stored product attribute instance.
	 */
	public function createStoredProductAttribute(IdType $productAttributeId)
	{
		return MainFactory::create('StoredProductAttribute', $productAttributeId);
	}
}