<?php

/* --------------------------------------------------------------
   ProductRepositoryReaderInterface.inc.php 2015-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductRepositoryReaderInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductRepositoryReaderInterface
{
	/**
	 * Returns a product entity instance by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @return StoredProductInterface Product entity with the expected product id.
	 */
	public function getById(IdType $productId);
}