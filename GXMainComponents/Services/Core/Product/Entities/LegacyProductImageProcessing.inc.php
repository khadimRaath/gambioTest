<?php

/* --------------------------------------------------------------
   LegacyProductImageProcessing.inc.php 2016-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LegacyProductImageProcessing
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class LegacyProductImageProcessing implements ProductImageProcessingInterface
{
	/**
	 * Proceed Image
	 *
	 * Processes an image for the front end.
	 *
	 * @param FilenameStringType $image Image to proceed.
	 *
	 * @throws InvalidArgumentException if the provided image name is not valid.
	 * @throws FileNotFoundException if the provided image was not found.
	 * @throws RuntimeException if the PHP GD library is not installed.
	 *
	 * @return LegacyProductImageProcessing Same instance for chained method calls.
	 */
	public function proceedImage(FilenameStringType $image)
	{
		if(!$this->_isGdLibraryInstalled())
		{
			// @codeCoverageIgnoreStart
			throw new \RuntimeException('PHP GD library is not installed on your server.');
			// @codeCoverageIgnoreEnd
		}

		// Variable name cannot be changed, because it is needed by the include files.
		$products_image_name = $image->asString();

		$this->_defineNeededConstantsForIncludes();

		$this->_throwExceptionIfImageDoesNotExist($products_image_name);

		require_once(DIR_FS_CATALOG . 'admin/includes/classes/' . IMAGE_MANIPULATOR);

		// If an error emerges, the value will be set to true in one of the include files.
		// This variable name also has to be named exactly like this.
		$image_error = false;

		// Images will be processed in these includes.
		// product_popup_images.php has to be included first, because a function will be defined here
		// if it does not exist yet.
		include(DIR_FS_CATALOG . 'admin/includes/' . 'product_popup_images.php');
		include(DIR_FS_CATALOG . 'admin/includes/' . 'product_info_images.php');
		include(DIR_FS_CATALOG . 'admin/includes/' . 'product_thumbnail_images.php');
		include(DIR_FS_CATALOG . 'admin/includes/' . 'product_gallery_images.php');

		if($image_error)
		{
			// @codeCoverageIgnoreStart
			throw new \InvalidArgumentException('Image: ' . $products_image_name
			                                    . ' could not be processed. Please check, if the provided name is valid.');
			// @codeCoverageIgnoreEnd
		}

		return $this;
	}


	/**
	 * Define Needed Constants for Includes
	 *
	 * These constants are needed for the include files. The global constants defined in admin/includes/configure.php
	 * are not available, because the current file is within in the src/includes folder and thus, is using the
	 * src/includes/configure.php, in which those constants are either not defined or not correct for this
	 * class.
	 */
	protected function _defineNeededConstantsForIncludes()
	{
		// Has to be defined as true, in order to include IMAGE_MANIPULATOR (image_manipulator_GD2.php),
		// else an error of 'No direct access allowed' will be thrown.
		if(!defined('_VALID_XTC'))
		{
			define(_VALID_XTC, true);
		}

		// Image folders.
		if(!defined('DIR_FS_CATALOG_IMAGES'))
		{
			define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
		}
		if(!defined('DIR_FS_CATALOG_ORIGINAL_IMAGES'))
		{
			define('DIR_FS_CATALOG_ORIGINAL_IMAGES', DIR_FS_CATALOG . 'images/product_images/original_images/');
		}
		if(!defined('DIR_FS_CATALOG_POPUP_IMAGES'))
		{
			define('DIR_FS_CATALOG_POPUP_IMAGES', DIR_FS_CATALOG . 'images/product_images/popup_images/');
		}
		if(!defined('DIR_FS_CATALOG_INFO_IMAGES'))
		{
			define('DIR_FS_CATALOG_INFO_IMAGES', DIR_FS_CATALOG . 'images/product_images/info_images/');
		}
		if(!defined('DIR_FS_CATALOG_THUMBNAIL_IMAGES'))
		{
			define('DIR_FS_CATALOG_THUMBNAIL_IMAGES', DIR_FS_CATALOG . 'images/product_images/thumbnail_images/');
		}
		if(!defined('DIR_FS_CATALOG_GALLERY_IMAGES'))
		{
			define('DIR_FS_CATALOG_GALLERY_IMAGES', DIR_FS_CATALOG . 'images/product_images/gallery_images/');
		}
	}


	/**
	 * Is GD Library Installed
	 *
	 * Checks if the GD library is installed, which is necessary for the image processing.
	 * Returns true if the library is installed.
	 *
	 * @return bool Is the PHP GD library installed?
	 */
	protected function _isGdLibraryInstalled()
	{
		return (extension_loaded('gd') && function_exists('gd_info'));
	}


	/**
	 * Throw Exception If Image Does Not Exist
	 *
	 * Checks if the image exist in the folder original_images.
	 * If the image does not exist, an exception will be thrown.
	 *
	 * @throws \FileNotFoundException if the image was not found.
	 *
	 * @param $image Image to check.
	 */
	protected function _throwExceptionIfImageDoesNotExist($image)
	{
		if(!file_exists(DIR_FS_CATALOG_ORIGINAL_IMAGES . $image))
		{
			throw new \FileNotFoundException($image . ' does not exist in ' . DIR_FS_CATALOG_ORIGINAL_IMAGES);
		}
	}
}
