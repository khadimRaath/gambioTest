<?php
/* --------------------------------------------------------------
   gm_set_content.inc.php 2015-07-27 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
	
	/*
		-> function to set content values
	*/
	
	function gm_set_content($gm_key, $gm_value, $languages_id, $group_id=0, $doTrimming = true) {

		$gm_key = xtc_db_prepare_input($gm_key);
		$gm_key = xtc_db_input($gm_key);

		if($doTrimming)
		{
			$gm_value = xtc_db_prepare_input($gm_value);
		}
		else
		{
			$gm_value = stripslashes($gm_value);
		}

		$gm_value = xtc_db_input($gm_value);
		
		// -> check if key exist
		$gm_query = xtc_db_query("
								SELECT
									gm_contents_id
								FROM
									gm_contents
								WHERE
									gm_key = '" . $gm_key . "'
									AND languages_id = '" . (int)$languages_id . "'
								"); 

		if(xtc_db_num_rows($gm_query) != 0) {
			$result = xtc_db_query("
									UPDATE
										gm_contents
									SET
										gm_value	= '" . $gm_value	. "',
										gm_group_id = '" . (int)$group_id	. "'
									WHERE
										gm_key = '" . $gm_key . "'
										AND languages_id = '" . (int)$languages_id . "'
									");		
		} 
		else {
			$result = xtc_db_query("
									INSERT INTO
										gm_contents
									SET
										gm_key		= '" . $gm_key		. "',
										gm_value	= '" . $gm_value	. "',
										languages_id = '" . (int)$languages_id . "',
										gm_group_id = '" . (int)$group_id	. "'
									");		
		}
		
		return $result;
	}