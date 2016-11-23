<?php

/* --------------------------------------------------------------
   ProductAttributeRepositoryReaderInterface.inc.php 2016-01-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductAttributeRepositoryReaderInterface
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Interface
 */
interface ProductAttributeRepositoryReaderInterface
{
	/**
	 * Returns a product attribute entity by the given product attribute id.
	 *
	 * @param IdType $productAttributeId Id of expected product attribute entity.
	 *
	 * @return StoredProductAttributeInterface Expected product attribute entity.
	 */
	public function getAttributeById(IdType $productAttributeId);


	/**
	 * Returns a collection with all product attribute entities which belongs to the product entity by the given
	 * product id.
	 *
	 * @param IdType $productId Id of product entity which belongs to the expected product attribute entities.
	 *
	 * @return StoredProductAttributeCollection Collection which contains all expected product attribute entities.
	 */
	public function getAttributesByProductId(IdType $productId);
}