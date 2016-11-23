<?php

/* --------------------------------------------------------------
   ProductWriteServiceInterface.inc.php 2016-04-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductWriteServiceInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
Interface ProductWriteServiceInterface
{
	/**
	 * Create Product
	 *
	 * Creates a new product and returns the ID of it.
	 *
	 * @param ProductInterface $product The product to create.
	 *
	 * @return int The ID of the created product.
	 */
	public function createProduct(ProductInterface $product);


	/**
	 * Update Product
	 *
	 * Updates a stored product.
	 *
	 * @param StoredProductInterface $product The product to update.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function updateProduct(StoredProductInterface $product);


	/**
	 * Delete Product
	 *
	 * Deletes a specific product, depending on the provided product ID.
	 *
	 * @param IdType $productId The product ID of the product to delete.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteProductById(IdType $productId);


	/**
	 * Duplicate Product
	 *
	 * Duplicates a product to a category.
	 *
	 * @param IdType   $productId             The product ID of the product to duplicate.
	 * @param IdType   $targetCategoryId      The target category ID of the product to be duplicated to.s
	 * @param BoolType $duplicateAttributes   Should the attributes be duplicated also?
	 * @param BoolType $duplicateSpecials     Should the specials be duplicated also?
	 * @param BoolType $duplicateCrossSelling Should cross selling be duplicated also?
	 *
	 * @return int Returns the ID of the new product.
	 */
	public function duplicateProduct(IdType $productId,
	                                 IdType $targetCategoryId,
	                                 BoolType $duplicateAttributes,
	                                 BoolType $duplicateSpecials,
	                                 BoolType $duplicateCrossSelling);


	/**
	 * Link Product
	 *
	 * Links a product to a category.
	 *
	 * @param IdType $productId        The product ID of the product to link.
	 * @param IdType $targetCategoryId The target category ID, of the category to be linked to.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function linkProduct(IdType $productId, IdType $targetCategoryId);


	/**
	 * Changes the category link of a product.
	 *
	 * @param IdType $productId         The product ID of the product to move.
	 * @param IdType $currentCategoryId Old category ID of the product.
	 * @param IdType $newCategoryId     New category ID of the product.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function changeProductLink(IdType $productId, IdType $currentCategoryId, IdType $newCategoryId);


	/**
	 * Removes a category link from a product by the given product id.
	 *
	 * @param IdType $productId  Id of the product.
	 * @param IdType $categoryId Id of category from where the product is link is to delete.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteProductLink(IdType $productId, IdType $categoryId);


	/**
	 * Removes all category links from a product by given product ID.
	 *
	 * @param IdType $productId ID of product.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteProductLinks(IdType $productId);


	/**
	 * Import Product Image File
	 *
	 * Imports an image for the product.
	 *
	 * @param ExistingFile       $sourceFile        The existing file to import.
	 * @param FilenameStringType $preferredFilename The preferred filename.
	 *
	 * @return string The new filename.
	 */
	public function importProductImageFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename);


	/**
	 * Rename Product Image File
	 *
	 * Renames a product image file.
	 *
	 * @param FilenameStringType $oldName The old name of the product image file.
	 * @param FilenameStringType $newName The new name of the product image file.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function renameProductImage(FilenameStringType $oldName, FilenameStringType $newName);


	/**
	 * Delete Product Image
	 *
	 * Deletes a product image.
	 *
	 * @param FilenameStringType $filename The filename of the product image to delete.
	 *
	 * @return ProductWriteServiceInterface Same instance for chained method calls.
	 */
	public function deleteProductImage(FilenameStringType $filename);
	
	
	/**
	 * Processes an image for the front end.
	 *
	 * @param FilenameStringType $productImage
	 *
	 * @return ProductWriteService Same instance for chained method calls.
	 */
	public function processProductImage(FilenameStringType $productImage);
}