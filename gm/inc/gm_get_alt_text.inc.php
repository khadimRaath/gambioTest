<?php
/* --------------------------------------------------------------
   gm_get_alt_text.inc.php 2008-05-28 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_alt_text.inc.php 04.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

function gm_get_alt_text($image_id, $no) {

	$lang = xtc_get_languages();
	for($i = 0; $i < count($lang); $i++) {
		if($no == 0) {
			
			$gm_query = xtc_db_query("
									SELECT 
										gm_alt_text
									FROM " . 								
										TABLE_PRODUCTS_DESCRIPTION . "
									WHERE								
										products_id = '" . (int)$_GET['pID']. "'
									AND 
										language_id = '" . $lang[$i]['id'] . "'
								");
					
			$gm = xtc_db_fetch_array($gm_query);		
		
		} else {
			
			$gm_query = xtc_db_query("
									SELECT 
										gm_alt_text,
										img_alt_id
									FROM 
										gm_prd_img_alt
									WHERE								
										image_id	= '" . (int)$image_id . "'
									AND 
										language_id = '" . $lang[$i]['id'] . "'
								");
					
			$gm = xtc_db_fetch_array($gm_query);

			$alt_text .= '' . '<input value="' . $gm['img_alt_id'] . '" type="hidden" name="gm_alt_id[' . $no . '][' . $lang[$i]['id'] . ']" />';
		}

		$alt_text .=  $lang[$i]['name'] .': ' . '<input value="' . $gm['gm_alt_text'] . '" type="text" name="gm_alt_text[' . $no . '][' . $lang[$i]['id'] . ']" />';
	}

	return $alt_text;
}

?>