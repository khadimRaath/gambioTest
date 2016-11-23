<?php

/* --------------------------------------------------------------
   ProductImageContainer.php 2015-12-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductImageContainer
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class ProductImageContainer implements ProductImageContainerInterface
{
	/**
	 * Primary image.
	 *
	 * @var ProductImage
	 */
	protected $primaryImage;

	/**
	 * Additional image associative array.
	 *
	 * @var array
	 */
	protected $additionalImages;


	/**
	 * ProductImageContainer constructor.
	 */
	public function __construct()
	{
		$primaryImageFile   = MainFactory::create('FilenameStringType', '');
		$this->primaryImage = MainFactory::create('ProductImage', $primaryImageFile);

		$this->additionalImages = array();
	}


	/**
	 * Sets the primary image of the image container.
	 *
	 * @param ProductImageInterface $image Primary product image to set.
	 *
	 * @return ProductImageContainer Same instance for chained method calls.
	 */
	public function setPrimary(ProductImageInterface $image)
	{
		$this->primaryImage = $image;

		return $this;
	}


	/**
	 * Returns the product primary image from container.
	 *
	 * @return ProductImageInterface The requested primary product image from the image container.
	 */
	public function getPrimary()
	{
		return $this->primaryImage;
	}


	/**
	 * Adds an additional image to the additional images array.
	 *
	 * @param ProductImageInterface $image Additional product image to set in the container.
	 *
	 * @throws InvalidArgumentException if the provided image type is not valid.
	 *
	 * @return ProductImageContainer Same instance for chained method calls.
	 */
	public function addAdditional(ProductImageInterface $image)
	{
		$key = $image->getFilename();

		$this->additionalImages[$key] = $image;

		return $this;
	}


	/**
	 * Returns the array of additional images as a collection.
	 *
	 * @return ProductImageCollection The requested additional images from container.
	 */
	public function getAdditionals()
	{
		$collection = MainFactory::create('ProductImageCollection', $this->additionalImages);

		return $collection;
	}


	/**
	 * Replaces an additional product image in the container.
	 *
	 * @param FilenameStringType    $imageFile Image file name.
	 * @param ProductImageInterface $image     Image to place.
	 *
	 * @return ProductImageContainer Same instance for chained method calls.
	 */
	public function replaceAdditional(FilenameStringType $imageFile, ProductImageInterface $image)
	{
		$key                          = $imageFile->asString();
		$this->additionalImages[$key] = $image;

		return $this;
	}


	/**
	 * Deletes an image from the additional images array.
	 *
	 * @param FilenameStringType $imageFile Image filename.
	 *
	 * @return ProductImageContainer Same instance for chained method calls.
	 */
	public function delete(FilenameStringType $imageFile)
	{
		$key = $imageFile->asString();
		if($this->primaryImage->getFilename() === $imageFile->asString())
		{
			$this->primaryImage = MainFactory::create('EmptyProductImage');
		}
		else
		{
			unset($this->additionalImages[$key]);
		}

		return $this;
	}
}