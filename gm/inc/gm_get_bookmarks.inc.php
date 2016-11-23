<?php
/* --------------------------------------------------------------
   gm_get_bookmarks.inc.php 2008-04-07 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_bookmarks.inc.php 04.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	/*
		-> function to get bookmarks
	*/
	function gm_get_bookmarks($gm_type = 'all', $gm_id = '') {
		
		// get all 
		if($gm_type == 'all') {
		
			$gm_bookmarks_query = xtc_db_query("
												SELECT 
													*
												FROM
													gm_bookmarks
												ORDER by
													gm_bookmarks_name
												ASC
												");				
		
		} else if ($gm_type == 'active') {
			
			$gm_bookmarks_query = xtc_db_query("
												SELECT 
													*
												FROM
													gm_bookmarks
												WHERE
													gm_bookmarks_use = '1'
												");				
		} else if ($gm_type == 'id') {
			
			$gm_bookmarks_query = xtc_db_query("
												SELECT 
													*
												FROM
													gm_bookmarks
												WHERE
													gm_bookmarks_id = '" . (int)$gm_id . "'
												");				
		}			
		
		while($gm_row = xtc_db_fetch_array($gm_bookmarks_query)) {
			$gm_bookmarks[] = $gm_row;
		}

		return $gm_bookmarks;

	}
?>