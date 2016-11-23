<?php
/* --------------------------------------------------------------
   gm_get_categories_icon.inc.php 2008-07-18 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

	function gm_get_categories_icon($cid, $cname) {

		$icon		= HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'logos/' . gm_get_conf('GM_LOGO_CAT');
		$imagesize	= getimagesize(DIR_FS_CATALOG . 'images/logos/' . gm_get_conf('GM_LOGO_CAT'));	
		
		$gm_query = xtc_db_query("
								SELECT
									categories_icon 
								AS
									icon
								FROM
									categories
								WHERE
									categories_id = '" . (int)$cid . "'
								");

		if(xtc_db_num_rows($gm_query) > 0) {
			$gm_icon = xtc_db_fetch_array($gm_query);
			
			if(!empty($gm_icon['icon'])) {
				$icon = $gm_icon['icon'];
							$imagesize	= getimagesize(DIR_FS_CATALOG.'images/categories/icons/' . $icon);
			$icon		=  DIR_WS_IMAGES . 'categories/icons/' . $icon;

			}	


		}
		
		
		return('<div class="cat_icon"><img src="' . $icon . '" ' . $imagesize[3] . ' alt="' . htmlspecialchars_wrapper($cname) . '" title="' . htmlspecialchars_wrapper($cname) . '" /></div><div class="cat_link" style="padding-left:' . ($imagesize[0]+3) . 'px;">');
	 }
	
	function gm_count_products_in_category($cid) {
		@require_once (DIR_FS_INC.'xtc_count_products_in_category.inc.php');
		if (SHOW_COUNTS == 'true') {
			$products_in_category = xtc_count_products_in_category($cid);
			if ($products_in_category > 0) {
				return $categories_string .= ' <span>(' . $products_in_category . ')</span>';
			}
		}
	}

?>