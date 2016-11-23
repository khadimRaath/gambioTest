<?php

/* --------------------------------------------------------------
   ProductRepositoryWriterInterface.inc.php 2016-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductRepositoryWriterInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductRepositoryWriterInterface
{
	/**
	 * Inserts a new product in the database.
	 *
	 * @param ProductInterface $product Product entity which holds the values for the database columns.
	 *
	 * @return int Id of inserted product.
	 */
	public function insert(ProductInterface $product);
	
	
	/**
	 * Updates a product in the database.
	 *
	 * @param StoredProductInterface $product Product entity to update.
	 *
	 * @return ProductRepositoryWriterInterface|$this Same instance for chained method calls.
	 */
	public function update(StoredProductInterface $product);
}