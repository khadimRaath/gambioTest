<?php
/* --------------------------------------------------------------
   gm_get_meta.inc.php 2008-04-14 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_meta.inc.php 2008-01-30 mb
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	/*
		-> function to get content values
	*/
	
	function gm_get_meta($languages_id) {
		
		$gm_query = xtc_db_query("
								SELECT
									gm_contents_id,
									gm_value,
									gm_key
								FROM
									gm_contents
								WHERE
									languages_id = '" . (int)$languages_id . "'
								AND
									 gm_group_id = '1'
								ORDER by
									gm_group_id ASC
								"); 
		
		while($gm_row = xtc_db_fetch_array($gm_query)) {
			$gm_values[] = $gm_row;		
		}

		return $gm_values;
	}	
?>