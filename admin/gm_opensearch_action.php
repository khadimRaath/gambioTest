<?php
/* 
	--------------------------------------------------------------
	gm_opensearch_action.php 2015-09-28 gm
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
	$lang_id = (int)$_GET['lang_id'];
} else {
	$lang_id = $_SESSION['languages_id'];
}


if(isset($_POST['go_opensearch'])) 	{
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']); 

	if((int)$_POST['GM_OPENSEARCH_BOX']	== 1) {
		gm_set_conf('GM_OPENSEARCH_BOX', 1);
	} else {
		gm_set_conf('GM_OPENSEARCH_BOX', 0);
	}

	if((int)$_POST['GM_OPENSEARCH_SEARCH']	== 1) {
		gm_set_conf('GM_OPENSEARCH_SEARCH', 1);
	} else {
		gm_set_conf('GM_OPENSEARCH_SEARCH', 0);
	}
}

if(isset($_POST['go_save'])) {
	$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
	gm_set_conf('GM_OPENSEARCH_CHANGED', '1');
	gm_set_content('GM_OPENSEARCH_TEXT',		htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_TEXT'])),			trim($_POST['gm_lang']));
	gm_set_content('GM_OPENSEARCH_LINK',		htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_LINK'])),			trim($_POST['gm_lang']));
	gm_set_content('GM_OPENSEARCH_SHORTNAME',	htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_SHORTNAME'])),		trim($_POST['gm_lang']));
	gm_set_content('GM_OPENSEARCH_LONGNAME',	htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_LONGNAME'])),		trim($_POST['gm_lang']));
	gm_set_content('GM_OPENSEARCH_DESCRIPTION', htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_DESCRIPTION'])),	trim($_POST['gm_lang']));
	gm_set_content('GM_OPENSEARCH_TAGS',		htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_TAGS'])),			trim($_POST['gm_lang']));
	gm_set_content('GM_OPENSEARCH_CONTACT',		htmlentities_wrapper(trim($_POST['GM_OPENSEARCH_CONTACT'])),		trim($_POST['gm_lang']));
}

// Display view HTML to user ... 

switch(($_GET['action'])) {

	case 'gm_opensearch_conf':
		include(DIR_FS_ADMIN . 'includes/gm/gm_opensearch/gm_opensearch_conf.php');
		break;

	default:
		$gm_values = gm_get_meta($lang_id);
		include(DIR_FS_ADMIN . 'includes/gm/gm_opensearch/gm_opensearch.php');
		break;
}

?>