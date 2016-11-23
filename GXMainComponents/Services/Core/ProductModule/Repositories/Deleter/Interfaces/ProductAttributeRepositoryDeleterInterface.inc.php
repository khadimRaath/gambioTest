<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryDeleterInterface.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductAttributeRepositoryDeleterInterface
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interface
 */
interface ProductAttributeRepositoryDeleterInterface
{
	/**
	 * Removes a product attribute entity by the given product attribute id.
	 *
	 * @param IdType $productAttributeId Id of attribute entity to delete.
	 *
	 * @return ProductAttributeRepositoryDeleterInterface|$this Same instance for chained method calls.
	 */
	public function deleteAttributeById(IdType $productAttributeId);


	/**
	 * Removes all product attributes entities that belongs to the given product entity id.
	 *
	 * @param IdType $productId Id of product entity which belongs to the product attribute entities to delete.
	 *
	 * @return ProductAttributeRepositoryDeleterInterface|$this Same instance for chained method calls.
	 */
	public function deleteAttributesByProductId(IdType $productId);
}