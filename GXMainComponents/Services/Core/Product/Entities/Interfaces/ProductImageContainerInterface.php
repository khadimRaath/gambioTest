<?php

/* --------------------------------------------------------------
   ProductImageContainerInterface.php 2015-12-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductImageContainerInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductImageContainerInterface
{
	/**
	 * Sets the primary image of the image container.
	 *
	 * @param ProductImageInterface $image Primary product image to set.
	 *
	 * @return ProductImageContainerInterface Same instance for chained method calls.
	 */
	public function setPrimary(ProductImageInterface $image);
	
	
	/**
	 * Returns the primary product image from the image container.
	 *
	 * @return ProductImageInterface The requested primary product image from container.
	 */
	public function getPrimary();
	

	/**
	 * Adds an additional image to the container.
	 *
	 * @param ProductImageInterface $image Additional product image to add to the container.
	 *
	 * @return ProductImageContainerInterface Same instance for chained method calls.
	 */
	public function addAdditional(ProductImageInterface $image);


	/**
	 * Returns the collection of additional images from the image container.
	 *
	 * @return ProductImageCollection The requested additional images from the image container.
	 */
	public function getAdditionals();


	/**
	 * Replaces an additional product image in the container.
	 *
	 * @param FilenameStringType    $imageFile Image filename.
	 * @param ProductImageInterface $image     Image to place.
	 *
	 * @return ProductImageContainerInterface Same instance for chained method calls.
	 */
	public function replaceAdditional(FilenameStringType $imageFile, ProductImageInterface $image);


	/**
	 * Deletes an image from the image container.
	 *
	 * @param FilenameStringType $imageFile Image filename.
	 *
	 * @return ProductImageContainerInterface Same instance for chained method calls.
	 */
	public function delete(FilenameStringType $imageFile);
}