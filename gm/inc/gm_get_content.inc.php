<?php
/* --------------------------------------------------------------
   gm_get_content.inc.php 2008-03-17 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_get_content.inc.php 2008-01-30 mb
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
	
	function gm_get_content($gm_key, $languages_id, $result_type = 'ASSOC') {
		$gm_values = false;
		if($result_type == 'ASSOC' || $result_type == 'NUMERIC'){
			if(is_array($gm_key)){
				foreach($gm_key as $key){
					$gm_query = xtc_db_query("
											SELECT
												gm_value
											FROM
												gm_contents
											WHERE
												gm_key = '" . xtc_db_input($key) . "'
												AND languages_id = '" . (int)$languages_id . "'
												LIMIT 1
											"); 

					if(xtc_db_num_rows($gm_query) == 1){
						if($gm_values == false) $gm_values = array();
						$gm_row = xtc_db_fetch_array($gm_query);
						if($result_type == 'ASSOC') {
							$gm_values[$key] = $gm_row['gm_value'];
						} else {
							$gm_values[] = $gm_row['gm_value'];				
						}					
					}
				}
			} 
			else {
				$gm_query = xtc_db_query("
										SELECT
											gm_value
										FROM
											gm_contents
										WHERE
											gm_key = '" . xtc_db_input($gm_key) . "'
											AND languages_id = '" . (int)$languages_id . "'
											LIMIT 1
										"); 
				if(xtc_db_num_rows($gm_query) == 1){
					if($gm_values == false) $gm_values = '';
					$gm_row = xtc_db_fetch_array($gm_query);
					$gm_values = $gm_row['gm_value'];
				}
			}
		}
		
		return $gm_values;
	}
?>