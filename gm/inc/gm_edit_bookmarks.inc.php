<?php
/* --------------------------------------------------------------
   gm_edit_bookmarks.inc.php 2008-05-05 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_edit_bookmarks.inc.php 07.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	function gm_edit_bookmarks() {				

		if(empty($_POST['gm_bookmarks_id'])) {

			if(check_upload($_FILES['gm_bookmarks_image']['type']) && !file_exists(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $_FILES['gm_bookmarks_image']['name'])) {			
				
				copy($_FILES['gm_bookmarks_image']['tmp_name'], DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $_FILES['gm_bookmarks_image']['name']);
				
				chmod(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $_FILES['gm_bookmarks_image']['name'], 0777);

				xtc_db_query("
							INSERT INTO
								gm_bookmarks
							SET
								gm_bookmarks_name	= '" . gm_prepare_string($_POST['gm_bookmarks_name'])	. "',
								gm_bookmarks_link	= '" . gm_prepare_string($_POST['gm_bookmarks_link'])	. "',
								gm_bookmarks_image	= '" . $_FILES['gm_bookmarks_image']['name']			. "'
							");	
				
				$gm_result = TITLE_SUCCESS;

			} else {

				$gm_result = TITLE_FAILED;

			}

		} else {

			if(!file_exists(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $_FILES['gm_bookmarks_image']['name'])  && check_upload($_FILES['gm_bookmarks_image']['type']) || empty($_FILES['gm_bookmarks_image']['tmp_name'])) {					
					
				$gm_bookmark = gm_get_bookmarks('id', $_POST['gm_bookmarks_id']);

				if(!empty($_FILES['gm_bookmarks_image']['tmp_name'])) {
					
					unlink(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $gm_bookmark[0]['gm_bookmarks_image']);

					copy($_FILES['gm_bookmarks_image']['tmp_name'], DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $_FILES['gm_bookmarks_image']['name']);

					chmod(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $_FILES['gm_bookmarks_image']['name'], 0777);

				} else {
					$_FILES['gm_bookmarks_image']['name'] = $gm_bookmark[0]['gm_bookmarks_image'];

				}
				
				xtc_db_query("
							UPDATE
								gm_bookmarks
							SET
								gm_bookmarks_name	= '" . gm_prepare_string($_POST['gm_bookmarks_name'])	. "',
								gm_bookmarks_link	= '" . gm_prepare_string($_POST['gm_bookmarks_link'])	. "',
								gm_bookmarks_image	= '" . $_FILES['gm_bookmarks_image']['name']			. "'
							WHERE
								gm_bookmarks_id		= '" . $gm_bookmark[0]['gm_bookmarks_id']				. "'
							");		

				$gm_result = TITLE_SUCCESS;

			} else {

				$gm_result = TITLE_FAILED;

			}
		}		
		
		return $gm_result;
	}