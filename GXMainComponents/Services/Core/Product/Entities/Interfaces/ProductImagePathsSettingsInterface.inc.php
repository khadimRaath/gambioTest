<?php

/* --------------------------------------------------------------
   ProductImagePathsSettingsInterface.inc.php 2016-02-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductImagePathsSettingsInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductImagePathsSettingsInterface
{
	/**
	 * Get Product Original Images Dir Path
	 *
	 * Returns a string to the original_images folder.
	 *
	 * @return string The path to the original_images folder.
	 */
	public function getProductOriginalImagesDirPath();
	
	
	/**
	 * Get Product Gallery Images Dir Path
	 *
	 * Returns a string to the gallery_images folder.
	 *
	 * @throws UnknownEnvironmentException
	 *
	 * @return string The path to the gallery_images folder.
	 */
	public function getProductGalleryImagesDirPath();


	/**
	 * Get Product Info Images Dir Path
	 *
	 * Returns a string to the info_images folder.
	 *
	 * @return string The path to the info_images folder.
	 */
	public function getProductInfoImagesDirPath();


	/**
	 * Get Product Popup Images Dir Path
	 *
	 * Returns a string to the popup_images folder.
	 *
	 * @return string The path to the popup_images folder.
	 */
	public function getProductPopupImagesDirPath();


	/**
	 * Get Product Thumbnail Images Dir Path
	 *
	 * Returns a string to the thumbnail_images folder.
	 *
	 * @return string The path to the thumbnail_images folder.
	 */
	public function getProductThumbnailImagesDirPath();
}