<?php
/* --------------------------------------------------------------
   gm_delete_bookmarks.inc.php 2008-08-08 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_delete_bookmarks.inc.php 07.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	function gm_delete_bookmarks($gm_id) {	
		
		$gm_bookmarks_query = xtc_db_query("
											SELECT 
												*
											FROM
												gm_bookmarks
											WHERE
												gm_bookmarks_id = '" . (int)$gm_id . "'
											");				
									
		
		$gm_bookmarks = xtc_db_fetch_array($gm_bookmarks_query);
		
		
		if(file_exists(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $gm_bookmarks['gm_bookmarks_image'])) {
			unlink(DIR_FS_CATALOG . "gm/images/gm_bookmarks/" . $gm_bookmarks['gm_bookmarks_image']);			
		}
		
		$gm_bookmarks_query = xtc_db_query("
											DELETE												
											FROM
												gm_bookmarks
											WHERE
												gm_bookmarks_id = '" . (int)$gm_bookmarks['gm_bookmarks_id'] . "'
											");				


		return $gm_bookmarks_query;
	}


?>