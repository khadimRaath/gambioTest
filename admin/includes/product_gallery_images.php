<?php
/* --------------------------------------------------------------
   product_gallery_images.php 2015-11-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_thumbnail_images.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

if(!defined('DIR_FS_CATALOG_GALLERY_IMAGES'))
{   
	define('DIR_FS_CATALOG_GALLERY_IMAGES', DIR_FS_CATALOG_IMAGES .'product_images/gallery_images/');   
}   

// BOF GM_IMAGE_LOG
$md5_before = '';
$filetime_before = '';
if(file_exists(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name)) {
	$md5_before			= md5_file(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name);
	$filetime_before	= filemtime(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name);
}
// EOF GM_IMAGE_LOG

$a = new image_manipulation(DIR_FS_CATALOG_ORIGINAL_IMAGES . $products_image_name,86,86,DIR_FS_CATALOG_GALLERY_IMAGES . $products_image_name,IMAGE_QUALITY,'');

$a->create();

// BOF GM_IMAGE_LOG
$md5_after = '';
$filetime_after = '';
if(!empty($md5_before)) {
	$md5_after		= md5_file(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name);
	$filetime_after = filemtime(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name);
}

if($a->image_error) {
	$image_error = true;
} elseif($filetime_before != $filetime_after && $md5_before == $md5_after) {
	$image_error = true;
}
// EOF GM_IMAGE_LOG

// BOF GM_MOD:		
@chmod(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name, 0777);	
?>