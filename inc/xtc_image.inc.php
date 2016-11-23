<?php
/* --------------------------------------------------------------
   xtc_image.inc.php 2008-06-17 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_image.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_image.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
 // include needed functions
 require_once(DIR_FS_INC . 'xtc_parse_input_field_data.inc.php');
 require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
// The HTML image wrapper function
  function xtc_image($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if ( (empty($src) || ($src == DIR_WS_IMAGES) || ( $src == DIR_WS_THUMBNAIL_IMAGES))) {
      return false;
    }

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . xtc_parse_input_field_data($src, array('"' => '&quot;')) . '" alt="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '" title="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';
/*
    if (xtc_not_null($alt)) {
      $image .= ' title=" ' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . ' "';
    }
*/
    if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height)) ) {
      if ($image_size = @getimagesize($src)) {
        if (empty($width) && xtc_not_null($height)) {
          $ratio = $height / $image_size[1];
          $width = $image_size[0] * $ratio;
        } elseif (xtc_not_null($width) && empty($height)) {
          $ratio = $width / $image_size[0];
          $height = $image_size[1] * $ratio;
        } elseif (empty($width) && empty($height)) {
          $width = $image_size[0];
          $height = $image_size[1];
        }
      } elseif (IMAGE_REQUIRED == 'false') {
        return false;
      }
    }

    if (xtc_not_null($width) && xtc_not_null($height)) {
      $image .= ' width="' . xtc_parse_input_field_data($width, array('"' => '&quot;')) . '" height="' . xtc_parse_input_field_data($height, array('"' => '&quot;')) . '"';
    }

    if (xtc_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= ' />';
    return $image;
  }
 ?>