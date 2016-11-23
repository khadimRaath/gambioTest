<?php

/* --------------------------------------------------------------
   ProductImageContainerRepositoryInterface.inc.php 2016-01-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface ProductImageContainerRepositoryInterface
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductImageContainerRepositoryInterface
{
	/**
	 * Stores the product image container.
	 *
	 * @param IdType                         $productId      Product ID.
	 * @param ProductImageContainerInterface $imageContainer Product image container.
	 *
	 * @return ProductImageContainerRepositoryInterface Same instance for method chaining.
	 */
	public function store(IdType $productId, ProductImageContainerInterface $imageContainer);
	

	/**
	 * Returns a product image container based on the product ID given.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return ProductImageContainerInterface Product image container.
	 */
	public function getByProductId(IdType $productId);


	/**
	 * Deletes a product image container based on the product ID given.
	 *
	 * @param IdType $productId Product ID.
	 *
	 * @return ProductImageContainerRepositoryInterface Same instance for method chaining.
	 */
	public function deleteByProductId(IdType $productId);
}