<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryInterface.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductAttributeRepositoryInterface
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interface
 */
interface ProductAttributeRepositoryInterface
{
	/**
	 * Adds an product attribute to a product by the given id.
	 *
	 * @param IdType                    $productId        Id of product entity that adds the attribute.
	 * @param ProductAttributeInterface $productAttribute Product attribute entity to add.
	 *
	 * @return int Id of added product attribute entity.
	 */
	public function addToProduct(IdType $productId, ProductAttributeInterface $productAttribute);


	/**
	 * Stores/Updates the passed product attribute entity.
	 *
	 * @param StoredProductAttributeInterface $productAttribute Product attribute entity to store.
	 *
	 * @return ProductAttributeRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function store(StoredProductAttributeInterface $productAttribute);


	/**
	 * Returns a stored product attribute entity by the given product attribute id.
	 *
	 * @param IdType $productAttributeId Id of expected product attribute entity.
	 *
	 * @return StoredProductAttributeInterface Expected stored product attribute entity.
	 */
	public function getAttributeById(IdType $productAttributeId);


	/**
	 * Returns a collection with all attribute entities that belongs to a product entity by the given product id.
	 *
	 * @param IdType $productId Id of product entity that contain the expected attributes.
	 *
	 * @return StoredProductAttributeCollection Collection with all attributes that belongs to the product.
	 */
	public function getAttributesByProductId(IdType $productId);


	/**
	 * Removes a product attribute entity by the given product attribute id.
	 *
	 * @param IdType $productAttributeId Id of product attribute entity that should be deleted.
	 *
	 * @return ProductAttributeRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function deleteAttributeById(IdType $productAttributeId);


	/**
	 * Removes product attributes by the given product id.
	 *
	 * @param IdType $productId Id of product entity of the attributes that should be deleted.
	 *
	 * @return ProductAttributeRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function deleteAttributesByProductId(IdType $productId);
}