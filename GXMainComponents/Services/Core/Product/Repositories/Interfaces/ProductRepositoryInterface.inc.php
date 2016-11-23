<?php

/* --------------------------------------------------------------
   ProductRepositoryInterface.inc.php 2015-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductRepositoryInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductRepositoryInterface
{
	/**
	 * Adds a new product in the database.
	 *
	 * @param ProductInterface $product Product entity which holds the values for the database columns.
	 *
	 * @return int Id of inserted product.
	 */
	public function add(ProductInterface $product);


	/**
	 * Updates an existing product in the database.
	 *
	 * @param StoredProductInterface $product Product entity to update.
	 *
	 * @return ProductRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function store(StoredProductInterface $product);


	/**
	 * Returns a stored product by the given id.
	 *
	 * @param IdType $productId Id of expected product entity.
	 *
	 * @return StoredProductInterface Product entity with the expected product id.
	 */
	public function getProductById(IdType $productId);


	/**
	 * Removes a product from the database by the given id.
	 *
	 * @param IdType $productId Id of expected product entity.
	 *
	 * @return ProductRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function deleteProductById(IdType $productId);
}