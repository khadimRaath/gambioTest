<?php

/* --------------------------------------------------------------
   ProductRepositoryDeleterInterface.inc.php 2015-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductRepositoryDeleterInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductRepositoryDeleterInterface
{
	/**
	 * Removes a product by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @return ProductRepositoryDeleterInterface|$this Same instance for chained method calls.
	 */
	public function deleteById(IdType $productId);
}