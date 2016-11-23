<?php

/* --------------------------------------------------------------
   ProductFactory.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class ProductFactory
 *
 * @category   System
 * @package    Product
 * @subpackage Factories
 */
class ProductFactory implements ProductFactoryInterface
{
	/**
	 * Creates a product.
	 *
	 * @return GXEngineProduct The created product.
	 */
	public function createProduct()
	{
		$settings = $this->createProductSettings();
		
		return MainFactory::create('GXEngineProduct', $settings);
	}
	
	
	/**
	 * Creates a stored product.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return StoredProduct
	 */
	public function createStoredProduct(IdType $productId)
	{
		return MainFactory::create('StoredProduct', $productId, MainFactory::create('ProductSettings'));
	}
	
	
	/**
	 * Creates a product settings container.
	 *
	 * @return ProductSettings
	 */
	public function createProductSettings()
	{
		return MainFactory::create('ProductSettings');
	}
}