<?php
/* --------------------------------------------------------------
   xtc_get_products_mo_images.inc.php 2015-11-04 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?PHP
/* ----------------------------------------------------------------------------------------- 
   -----------------------------------------------------------------------------------------
   (c) 2004 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_products_mo_images.inc.php 1009 2005-07-11 16:19:29Z mz $)
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   // BOF GM_MOD
   function xtc_get_products_mo_images($products_id = '', $p_ignore_gm_show_image = false){
	if($p_ignore_gm_show_image)
	{
		$mo_query = "select image_id, image_nr, image_name, gm_show_image from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$products_id ."' ORDER BY image_nr";
	}
   	else
   	{
   		$mo_query = "select image_id, image_nr, image_name from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . (int)$products_id ."' AND gm_show_image = '1' ORDER BY image_nr";
   	}
	// EOF GM_MOD

   $products_mo_images_query = xtDBquery($mo_query);
   
  
   while ($row = xtc_db_fetch_array($products_mo_images_query,true)) $results[($row['image_nr']-1)] = $row;
   if (isset($results) && is_array($results))
   {
       return $results;
   } else {
       return false;
   }
   }
   
   ?>