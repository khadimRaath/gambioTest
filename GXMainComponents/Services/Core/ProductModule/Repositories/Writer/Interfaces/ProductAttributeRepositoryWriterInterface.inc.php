<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryWriterInterface.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductAttributeRepositoryWriterInterface
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interface
 */
interface ProductAttributeRepositoryWriterInterface
{
	/**
	 * Adds a product attribute entity to a product by the given product id.
	 *
	 * @param IdType                    $productId        Id of product entity which should belongs to the added
	 *                                                     attributes.
	 * @param ProductAttributeInterface $productAttribute Product attribute entity to add to the product.
	 *
	 * @return int Id of the stored product attribute.
	 */
	public function insertIntoProduct(IdType $productId, ProductAttributeInterface $productAttribute);


	/**
	 * Updates a product attribute entity.
	 *
	 * @param StoredProductAttributeInterface $productAttribute Product attribute entity to update.
	 *
	 * @return ProductAttributeRepositoryWriterInterface|$this Same instance for chained method calls.
	 */
	public function update(StoredProductAttributeInterface $productAttribute);
}