<?php
/* --------------------------------------------------------------
   gm_bookmarks_action.php 2015-09-28 gm
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   gm_bookmarks_action.php 04.04.2008 pt
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


	require('includes/application_top.php');

	include(DIR_FS_CATALOG . 'gm/inc/gm_get_bookmarks.inc.php');	
	include(DIR_FS_CATALOG . 'gm/inc/gm_delete_bookmarks.inc.php');

	switch(($_GET['action'])) {
		
		case 'gm_delete_bookmarks':
			
			$_SESSION['coo_page_token']->is_valid($_GET['page_token']);
			
			$gm_action = gm_delete_bookmarks($_GET['gm_bookmarks_id']);

			if($gm_action == 1) {
			
				$_GET['gm_result'] = TITLE_SUCCESS;
			
			} else {

				$_GET['gm_result'] = TITLE_FAILED;

			}
		case 'gm_bookmarks':
			
			$gm_bookmarks = gm_get_bookmarks();

			if(!empty($gm_bookmarks)) {
				include(DIR_FS_ADMIN . 'includes/gm/gm_bookmarks/gm_bookmarks.php');
			} else {
				echo TITLE_BOOKMARKS_EMPTY;
				echo "<br>";
				echo "<br>";
				echo '<span class="gm_strong" style="cursor:pointer" onclick="gm_get_content(\'' . xtc_href_link('gm_bookmarks_action.php', 'action=gm_edit_bookmarks') . '\')">' . TITLE_BOOMARKS_NEW . "</span>";
			}

		break;
		
		case 'gm_edit_bookmarks':
			
			if(!empty($_GET['gm_bookmarks_id'])) {
				$gm_bookmarks = gm_get_bookmarks('id', $_GET['gm_bookmarks_id']);
			}

			include(DIR_FS_ADMIN . 'includes/gm/gm_bookmarks/gm_edit_bookmarks.php');

		break;



		case 'gm_bookmarks_options':

			$boxes = array(
								'GM_BOOKMARKS_START',
								'GM_BOOKMARKS_ARTICLES',
								'GM_BOOKMARKS_CATEGORIES',
								'GM_BOOKMARKS_CONTENT',
								'GM_BOOKMARKS_REST'
								);

			$gm_values = gm_get_conf($boxes);

			include(DIR_FS_ADMIN . 'includes/gm/gm_bookmarks/gm_bookmarks_options.php');

		break;


		case 'gm_bookmarks_update':

			unset($_GET['action']);
			foreach($_GET as $key => $value) {
				$result = gm_set_conf($key , strip_tags(gm_prepare_string($value)));
			}
			echo '<b style="color:#339900">' . PROCEED . '</b><b>' . $error . '</b>';

		break;
	}
?>