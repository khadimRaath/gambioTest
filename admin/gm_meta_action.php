<?php
/* --------------------------------------------------------------
   gm_meta_action.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

include(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');
include(DIR_FS_CATALOG . 'gm/inc/gm_get_language_link.inc.php');
include(DIR_FS_CATALOG . 'gm/inc/gm_get_meta.inc.php');

if(!empty($_GET['lang_id'])) {
	$lang_id = $_GET['lang_id'];
} else {
	$lang_id = $_SESSION['languages_id'];
}

if($_GET['gm_new'] == '1') {
	if(!empty($_POST['gm_lang']) && !empty($_POST['gm_new_value']) && (!empty($_POST['gm_meta']) || !empty($_POST['gm_new_key']))) {
		for($i=0; $i < count($_POST['gm_lang']); $i++) {
			$gm_value_1 = gm_get_content($_POST['gm_new_key'], $_POST['gm_lang'][$i]);
			$gm_value_2 = gm_get_content($_POST['gm_meta'], $_POST['gm_lang'][$i]);

			if(empty($gm_value_1) && empty($gm_value_2)) {
				if($_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
					if(!empty($_POST['gm_new_key'])) {
						gm_set_content($_POST['gm_new_key'], $_POST['gm_new_value'], $_POST['gm_lang'][$i], 1);
					} elseif($_POST['gm_meta'] != "1") {
						gm_set_content($_POST['gm_meta'], $_POST['gm_new_value'], $_POST['gm_lang'][$i], 1);
					}
				}
			} else {
				// meta exists
				$gm_status = GM_META_EXISTS;
			}
		}
		// language empty
	} else {
		$gm_status = GM_META_LANG_EMPTY;
	}

} else if($_GET['gm_options'] == '1') {

	if(isset($_POST) && $_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
		gm_set_conf('GM_TITLE_USE_STANDARD_META_TITLE',				$_POST['GM_TITLE_USE_STANDARD_META_TITLE']);
		gm_set_conf('GM_TITLE_SHOW_STANDARD_META_TITLE',			$_POST['GM_TITLE_SHOW_STANDARD_META_TITLE']);
		gm_set_content('GM_TITLE_STANDARD_META_TITLE_SEPARATOR',	$_POST['GM_TITLE_STANDARD_META_TITLE_SEPARATOR'],	$_POST['gm_lang'], 0, false);
		gm_set_content('GM_TITLE_STANDARD_META_TITLE',				$_POST['GM_TITLE_STANDARD_META_TITLE'],				$_POST['gm_lang']);
	}
	
} else {

	if(!empty($_POST['gm_submit']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token'])) {
		foreach($_POST as $gm_key => $gm_value) {
			if($gm_key != 'gm_delete' && $gm_key != 'gm_lang' && $gm_key != 'gm_submit' && $gm_key != 'page_token') {
				gm_set_content($gm_key, $gm_value, $lang_id, 1);
			}
		}

		if(!empty($_POST['gm_delete'])) {
			foreach($_POST['gm_delete'] as $gm_id) {
				xtc_db_query("
								DELETE
								FROM
									gm_contents								
								WHERE
									gm_contents_id = '" . xtc_db_input($gm_id) . "'
								");
			}
		}
	}
}


switch($_GET['action']) {

	case 'gm_meta_new':
		include(DIR_FS_ADMIN . 'includes/gm/gm_meta/gm_meta_new.php');
		break;

	case 'gm_meta_options':
		include(DIR_FS_ADMIN . 'includes/gm/gm_meta/gm_meta_options.php');
		break;

	default:
		$gm_values = gm_get_meta($lang_id);
		include(DIR_FS_ADMIN . 'includes/gm/gm_meta/gm_meta.php');
		break;
}

?>
