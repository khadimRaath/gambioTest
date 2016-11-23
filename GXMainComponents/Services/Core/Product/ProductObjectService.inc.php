<?php

/* --------------------------------------------------------------
   ProductObjectService.inc.php 2015-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductObjectService
 *
 * @category   System
 * @package    Product
 */
class ProductObjectService implements ProductObjectServiceInterface
{
	/**
	 * Product factory.
	 *
	 * @var ProductFactoryInterface
	 */
	protected $productFactory;


	/**
	 * ProductObjectService constructor.
	 *
	 * @param ProductFactoryInterface $productFactory Product factory.
	 */
	public function __construct(ProductFactoryInterface $productFactory)
	{
		$this->productFactory = $productFactory;
	}


	/**
	 * Creates a product object.
	 *
	 * @return ProductInterface The created product.
	 */
	public function createProductObject()
	{
		return $this->productFactory->createProduct();
	}
}