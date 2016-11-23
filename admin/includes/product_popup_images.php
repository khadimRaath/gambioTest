<?php
/* --------------------------------------------------------------
   product_popup_images.php 2015-11-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_popup_images.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

if(!function_exists('clear_string'))
{
	function clear_string($value)
	{
		$html = str_replace("'", '', $value);
		$html = str_replace(')', '', $html);
		$html = str_replace('(', '', $html);
		$array = explode(',', $html);
		foreach($array as $key => $value)
		{
			$array[$key] = trim($value);
		}
		return $array;
	}
}

// BOF GM_IMAGE_LOG
$md5_before = '';
$filetime_before = '';
if(file_exists(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name)) {
	$md5_before			= md5_file(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name);
	$filetime_before	= filemtime(DIR_FS_CATALOG_GALLERY_IMAGES.$products_image_name);
}
// EOF GM_IMAGE_LOG

$a = new image_manipulation(DIR_FS_CATALOG_ORIGINAL_IMAGES . $products_image_name,PRODUCT_IMAGE_POPUP_WIDTH,PRODUCT_IMAGE_POPUP_HEIGHT,DIR_FS_CATALOG_POPUP_IMAGES . $products_image_name,IMAGE_QUALITY,'');
$array=clear_string(PRODUCT_IMAGE_POPUP_BEVEL);
if (PRODUCT_IMAGE_POPUP_BEVEL != ''){
$a->bevel($array[0],$array[1],$array[2]);}

$array=clear_string(PRODUCT_IMAGE_POPUP_GREYSCALE);
if (PRODUCT_IMAGE_POPUP_GREYSCALE != ''){
$a->greyscale($array[0],$array[1],$array[2]);}

$array=clear_string(PRODUCT_IMAGE_POPUP_ELLIPSE);
if (PRODUCT_IMAGE_POPUP_ELLIPSE != ''){
$a->ellipse($array[0]);}

$array=clear_string(PRODUCT_IMAGE_POPUP_ROUND_EDGES);
if (PRODUCT_IMAGE_POPUP_ROUND_EDGES != ''){
$a->round_edges($array[0],$array[1],$array[2]);}

$string=str_replace("'",'',PRODUCT_IMAGE_POPUP_MERGE);
$string=str_replace(')','',$string);
$string=str_replace('(',DIR_FS_CATALOG_IMAGES . 'logos/',$string);
$array=explode(',',$string);
foreach($array as $key => $value)
{
	$array[$key] = trim($value);
}
//$array=clear_string();
if (PRODUCT_IMAGE_POPUP_MERGE != ''){
$a->merge($array[0],$array[1],$array[2],$array[3],$array[4]);}

$array=clear_string(PRODUCT_IMAGE_POPUP_FRAME);
if (PRODUCT_IMAGE_POPUP_FRAME != ''){
$a->frame($array[0],$array[1],$array[2],$array[3]);}

$array=clear_string(PRODUCT_IMAGE_POPUP_DROP_SHADDOW);
if (PRODUCT_IMAGE_POPUP_DROP_SHADDOW != ''){
$a->drop_shadow($array[0],$array[1],$array[2]);}

$array=clear_string(PRODUCT_IMAGE_POPUP_MOTION_BLUR);
if (PRODUCT_IMAGE_POPUP_MOTION_BLUR != ''){
$a->motion_blur($array[0],$array[1]);}

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
@chmod(DIR_FS_CATALOG_POPUP_IMAGES.$products_image_name, 0777);