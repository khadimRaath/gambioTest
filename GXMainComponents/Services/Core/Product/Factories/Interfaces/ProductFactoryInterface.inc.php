<?php

/* --------------------------------------------------------------
   ProductFactoryInterface.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductFactoryInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductFactoryInterface
{
	/**
	 * Creates a product.
	 *
	 * @return ProductInterface
	 */
	public function createProduct();
	
	
	/**
	 * Creates a stored product.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return StoredProductInterface
	 */
	public function createStoredProduct(IdType $productId);
	
	
	/**
	 * Creates a product settings container.
	 *
	 * @return ProductSettingsInterface
	 */
	public function createProductSettings();
}