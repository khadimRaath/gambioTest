<?php
/* --------------------------------------------------------------
   gm_get_bookmarks_link.inc.php 2008-11-27 pt
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

> function to get bookmarks
	*/
	function gm_get_bookmarks_link($PHP_SELF) {
		global $product;
		global $cPath;
		global $gmSEOBoost;
		$gm_get_params = xtc_get_all_get_params();

		// -> handle cats
		if(!empty($cPath) && empty($product->pID)) {
			if($gmSEOBoost->boost_categories && !strstr(basename($PHP_SELF), "reviews")) {	
				$gm_back_link = xtc_href_link($gmSEOBoost->get_boosted_category_url($cPath, $_SESSION['languages_id']));				
			} else {
				if(empty($gm_get_params)) {
					$gm_back_link = xtc_href_link(basename($PHP_SELF));
				} else {
					$gm_back_link = xtc_href_link(basename($PHP_SELF), $gm_get_params);
				}
			}			
			
		// -> handle prds
		} elseif($product->pID !=0 && !strstr(basename($PHP_SELF), "reviews")) {
			if($gmSEOBoost->boost_products) {
				$gm_back_link = xtc_href_link($gmSEOBoost->get_boosted_product_url($product->pID, $product->products_name, $_SESSION['languages_id']));
			} else {
				$gm_back_link = xtc_href_link('product_info.php', xtc_product_link($product->pID, $product->products_name));
			}				

		// -> handle content
		} else if(basename($PHP_SELF) == "shop_content.php") {
			
			if($gmSEOBoost->boost_content) {
				$result = xtc_db_query('
					SELECT
						content_id
					FROM
						content_manager
					WHERE
						content_group = "' . xtc_db_input($_GET['coID']) .'"
					AND
						languages_id = ' .  $_SESSION['languages_id'] . '
				');

				$row = xtc_db_fetch_array($result);

				$gm_back_link = xtc_href_link($gmSEOBoost->get_boosted_content_url($row['content_id'], $_SESSION['languages_id']));
				

			} else {
				
				if(empty($gm_get_params)) {
					$gm_back_link = xtc_href_link(basename($PHP_SELF));
				} else {
					$gm_back_link = xtc_href_link(basename($PHP_SELF), $gm_get_params);
				}
			}

		// -> handle other
		} else {	

			if(empty($gm_get_params)) {
				$gm_back_link = xtc_href_link(basename($PHP_SELF));
			} else {
				$gm_back_link = xtc_href_link(basename($PHP_SELF), $gm_get_params);
			}
		}


		
		// get bookmarks
		$gm_bookmarks_query = xtc_db_query("
											SELECT 
												*
											FROM
												gm_bookmarks
											");				
	
		// create link
		while($gm_row = xtc_db_fetch_array($gm_bookmarks_query)) {			
				
			//@$imgsize = getimagesize(HTTP_SERVER . DIR_WS_CATALOG . "gm/images/gm_bookmarks/" . $gm_row['gm_bookmarks_image']);
			
			$gm_image = '<img alt="' . $gm_row['gm_bookmarks_name'] . '" title="' . $gm_row['gm_bookmarks_name'] . '" src="' . GM_HTTP_SERVER . DIR_WS_CATALOG . 'gm/images/gm_bookmarks/' . $gm_row['gm_bookmarks_image'] . '" />';				

			$gm_bookmarks .= '<a href="' . htmlentities_wrapper($gm_row['gm_bookmarks_link']) . $gm_back_link . '" rel="nofollow">' . $gm_image . '</a>';

		}
		return $gm_bookmarks;
	}