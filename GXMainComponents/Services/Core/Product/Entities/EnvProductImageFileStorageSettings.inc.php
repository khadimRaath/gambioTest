<?php
/* --------------------------------------------------------------
   EnvProductImageFileStorageSettings.inc.php 2016-02-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EnvProductImageFileStorageSettings
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 *
 * @codeCoverageIgnore
 */
class EnvProductImageFileStorageSettings implements ProductImagePathsSettingsInterface
{
	
	/**
	 * Get Product Original Images Dir Path
	 *
	 * Returns a string to the original_images folder.
	 *
	 * @throws UnknownEnvironmentException if the original_images folder was not found.
	 *
	 * @return string The path to the original_images folder.
	 */
	public function getProductOriginalImagesDirPath()
	{
		if(defined('DIR_FS_CATALOG_ORIGINAL_IMAGES'))
		{
			return DIR_FS_CATALOG_ORIGINAL_IMAGES;
		}
		elseif(defined('DIR_WS_ORIGINAL_IMAGES'))
		{
			return DIR_FS_CATALOG . DIR_WS_ORIGINAL_IMAGES;
		}
		throw new UnknownEnvironmentException();
	}
	
	
	/**
	 * Get Product Gallery Images Dir Path
	 *
	 * Returns a string to the gallery_images folder.
	 *
	 * @throws UnknownEnvironmentException if the gallery_images folder was not found.
	 *
	 * @return string The path to the gallery_images folder.
	 */
	public function getProductGalleryImagesDirPath()
	{
		if(defined('DIR_FS_CATALOG') && defined('DIR_WS_IMAGES'))
		{
			return DIR_FS_CATALOG . DIR_WS_IMAGES . 'product_images/gallery_images/';
		}
		throw new UnknownEnvironmentException();
	}
	
	
	/**
	 * Get Product Info Images Dir Path
	 *
	 * Returns a string to the info_images folder.
	 *
	 * @throws UnknownEnvironmentException if the info_images folder was not found.
	 *
	 * @return string The path to the info_images folder.
	 */
	public function getProductInfoImagesDirPath()
	{
		if(defined('DIR_FS_CATALOG_INFO_IMAGES'))
		{
			return DIR_FS_CATALOG_INFO_IMAGES;
		}
		elseif(defined('DIR_WS_INFO_IMAGES'))
		{
			return DIR_FS_CATALOG . DIR_WS_INFO_IMAGES;
		}
		throw new UnknownEnvironmentException();
	}
	
	
	/**
	 * Get Product Popup Images Dir Path
	 *
	 * Returns a string to the popup_images folder.
	 *
	 * @throws UnknownEnvironmentException if the popup_images folder was not found.
	 *
	 * @return string The path to the popup_images folder.
	 */
	public function getProductPopupImagesDirPath()
	{
		if(defined('DIR_FS_CATALOG_POPUP_IMAGES'))
		{
			return DIR_FS_CATALOG_POPUP_IMAGES;
		}
		elseif(defined('DIR_WS_POPUP_IMAGES'))
		{
			return DIR_FS_CATALOG . DIR_WS_POPUP_IMAGES;
		}
		throw new UnknownEnvironmentException();
	}
	
	
	/**
	 * Get Product Thumbnail Images Dir Path
	 *
	 * Returns a string to the thumbnail_images folder.
	 *
	 * @throws UnknownEnvironmentException if the thumbnail_images folder was not found.
	 *
	 * @return string The path to the thumbnail_images folder.
	 */
	public function getProductThumbnailImagesDirPath()
	{
		if(defined('DIR_FS_CATALOG_THUMBNAIL_IMAGES'))
		{
			return DIR_FS_CATALOG_THUMBNAIL_IMAGES;
		}
		elseif(defined('DIR_WS_THUMBNAIL_IMAGES'))
		{
			return DIR_FS_CATALOG . DIR_WS_THUMBNAIL_IMAGES;
		}
		throw new UnknownEnvironmentException();
	}
}