<?php
/* --------------------------------------------------------------
   gm_get_language_link.inc.php 2008-05-29 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_language_link.inc.php 01.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

	function gm_get_language_link($file, $action, $submenu) {

		$gm_lang = gm_get_language();
	
		$lang = '<div id="gm_language">';
		
		foreach($gm_lang as $value) {		
			
			$lang .= '<span onclick="gm_get_content(\'' . 
			xtc_href_link($file, 'action=' . $action . '&subpage='. htmlentities_wrapper($_GET['subpage']) . '&lang_id=' . $value['languages_id']) . '\',' .
			'\'' . $action . '\',' .			
			'\'' . xtc_href_link($file, 'action='. $submenu)	. '\')">' . 
			xtc_image(DIR_WS_LANGUAGES.$value['directory'].'/admin/images/'.$value['image'])
			. '</span> ';
		}	
		


		$lang .= '</div>';

		return $lang; 
	
	}
	
	function gm_get_lang_link($file, $action) {

		$gm_lang = gm_get_language();
	
		$lang = '<div id="gm_language">';
		

		foreach($gm_lang as $value) {		
			
			$lang .= '<a href="' . 
			xtc_href_link($file, 'action=' . $action . '&lang_id=' . $value['languages_id']) . '">' . 
			xtc_image(DIR_WS_LANGUAGES.$value['directory'].'/admin/images/'.$value['image'])		
			. '</a> ';
		}	
		


		$lang .= '</div>';

		return $lang; 
	
	}
?>