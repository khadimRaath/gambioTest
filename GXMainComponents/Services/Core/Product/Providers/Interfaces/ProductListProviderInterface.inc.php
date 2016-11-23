<?php

/* --------------------------------------------------------------
   ProductListProviderInterface.inc.php 2015-12-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductListProviderInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductListProviderInterface
{
	/**
	 * Returns a product list item collection by the provided category ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return ProductListItemCollection
	 */
	public function getByCategoryId(IdType $categoryId);


	/**
	 * Returns all product list items.
	 * 
	 * @return ProductListItemCollection
	 */
	public function getAll();
}