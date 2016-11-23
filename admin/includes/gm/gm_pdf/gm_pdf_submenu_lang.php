<?php
/* --------------------------------------------------------------
   gm_pdf_submenu_lang.php 2008-04-01 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>
<div id="gm_language">
<?php 
	$lang .= '[';
	foreach($gm_lang as $value) {		
		
		$lang .= '<span onclick="gm_get_content(\'' . 
																xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_content&subpage='. $_GET['subpage'] . '&lang_id=' . $value['languages_id']) . '\',' .
																'\'' . 'gm_pdf_content' . '\',' .			
																'\'' . xtc_href_link('gm_pdf_action.php', 'action=gm_box_submenu_content')	. '\')">' . $value['name'] . '</span>|';
	}	
	
	$lang = substr($lang, 0, strlen($lang)-1);	

	echo $lang. ']';
?>
</div>