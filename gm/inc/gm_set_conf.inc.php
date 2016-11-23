<?php
/* --------------------------------------------------------------
   gm_set_conf.inc.php 2014-09-05 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
	
	/*
		-> function to set configuration values
	*/
	
	function gm_set_conf($gm_conf_key, $gm_conf_value) {

		// -> check if key exist

		$gm_conf_key = xtc_db_prepare_input($gm_conf_key);
		$gm_conf_value = xtc_db_prepare_input($gm_conf_value);

		$gm_query = xtc_db_query("
								SELECT
									gm_key
								FROM
									gm_configuration
								WHERE
									gm_key = '" . xtc_db_input($gm_conf_key) . "'
								"); 

		$gm_row = xtc_db_fetch_array($gm_query);

		if(!empty($gm_row['gm_key'])) {
			
			$result = xtc_db_query("
									UPDATE
										gm_configuration
									SET
										gm_key		= '" . xtc_db_input($gm_conf_key)		. "',
										gm_value	= '" . xtc_db_input($gm_conf_value)	. "'
									WHERE
										gm_key = '" . xtc_db_input($gm_conf_key) . "'
									");		
		} else {
			$result = xtc_db_query("
									INSERT INTO
										gm_configuration
									SET
										gm_key		= '" . xtc_db_input($gm_conf_key)		. "',
										gm_value	= '" . xtc_db_input($gm_conf_value)	. "'
									");		
		}
		
		return $result;
	}