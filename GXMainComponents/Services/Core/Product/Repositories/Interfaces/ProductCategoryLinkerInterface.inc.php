<?php

/* --------------------------------------------------------------
   ProductCategoryLinkerInterface.inc.php 2016-01-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductCategoryLinkerInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductCategoryLinkerInterface
{
	/**
	 * Links a product to a category.
	 *
	 * @param IdType $productId        Product ID.
	 *
	 * @param IdType $targetCategoryId Target category ID.
	 *
	 * @return $this|ProductCategoryLinkerInterface Same instance for chained method calls.
	 */
	public function linkProduct(IdType $productId, IdType $targetCategoryId);


	/**
	 * Changes a link to a new category.
	 *
	 * @param IdType $productId         Product ID.
	 * @param IdType $currentCategoryId Category ID which the product is linked to.
	 * @param IdType $newCategoryId     New category to be linked to.
	 *
	 * @return $this|ProductCategoryLinkerInterface Same instance for chained method calls.
	 */
	public function changeProductLink(IdType $productId, IdType $currentCategoryId, IdType $newCategoryId);


	/**
	 * Removes a link to a category.
	 *
	 * @param IdType $productId  Product ID.
	 * @param IdType $categoryId Category ID which the link should be removed to.
	 *
	 * @return $this|ProductCategoryLinkerInterface Same instance for chained method calls.
	 */
	public function deleteProductLink(IdType $productId, IdType $categoryId);


	/**
	 * Removes all links from a product.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return $this|ProductCategoryLinkerInterface Same instance for chained method calls.
	 */
	public function deleteProductLinks(IdType $productId);
	
	
	/**
	 * Returns the category Ids which are linked with given product id.
	 * 
	 * @param IdType $productId
	 *
	 * @return IdCollection
	 */
	public function getProductLinks(IdType $productId);
}