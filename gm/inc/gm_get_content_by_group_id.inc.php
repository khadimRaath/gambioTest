<?php
/* --------------------------------------------------------------
   gm_get_content_by_group_id.inc.php 2008-03-17 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_content_by_group_id.inc.php 2008-01-30 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	/*
	*	-> function to get content values by group id
	*/	
	function gm_get_content_by_group_id($gm_group_id, $languages_id, $result_type = 'ASSOC') {

		$gm_values = false;
		
		if($result_type == 'ASSOC' || $result_type == 'NUMERIC'){			
				
			$gm_query = xtc_db_query("
									SELECT
										gm_value,
										gm_key
									FROM
										gm_contents
									WHERE
										languages_id = '" . (int)$languages_id . "'
									AND
										 gm_group_id = '" . (int)$gm_group_id . "'
									ORDER by
										gm_group_id ASC
									"); 

			$gm_values = array();
					
			while($gm_row = xtc_db_fetch_array($gm_query)) {
			
				if($result_type == 'ASSOC') {

					$gm_values[$gm_row['gm_key']] = $gm_row['gm_value'];

				} else {

					$gm_values[] = $gm_row['gm_value'];				
				}		
			}
		}
		
		return $gm_values;
	}
?>