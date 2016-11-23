<?php

/* --------------------------------------------------------------
   ProductAttributeObjectService.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductAttributeObjectService
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interfaces
 */
class ProductAttributeObjectService implements ProductAttributeObjectServiceInterface
{
	/**
	 * @var ProductAttributeFactoryInterface
	 */
	protected $productAttributeFactory;


	/**
	 * Initialize the product attribute object service.
	 *
	 * @param ProductAttributeFactoryInterface $productAttributeFactory
	 */
	public function __construct(ProductAttributeFactoryInterface $productAttributeFactory)
	{
		$this->productAttributeFactory = $productAttributeFactory;
	}


	/**
	 * Creates a new instance of a product attribute object.
	 *
	 * @param IdType $optionId Option id of product attribute.
	 * @param IdType $valueId  Value id of product attribute.
	 *
	 * @return ProductAttributeInterface New instance of product attribute.
	 */
	public function createProductAttributeObject(IdType $optionId, IdType $valueId)
	{
		return $this->productAttributeFactory->createProductAttribute($optionId, $valueId);
	}
}