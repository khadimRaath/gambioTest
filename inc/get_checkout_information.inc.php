<?php
/* --------------------------------------------------------------
   get_checkout_information.inc.php 2012-05-31 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function get_checkout_information($p_products_id, $p_languages_id)
{
	$t_checkout_information = '';
	
	$t_sql = "SELECT checkout_information 
				FROM " . TABLE_PRODUCTS_DESCRIPTION . " 
				WHERE 
					products_id = '" . (int)$p_products_id . "' AND
					language_id = '" . (int)$p_languages_id . "'";
	$t_result = xtc_db_query($t_sql);
	
	if(xtc_db_num_rows($t_result) == 1)
	{
		$t_result_array = xtc_db_fetch_array($t_result);
		$t_checkout_information = $t_result_array['checkout_information'];
	}
	
	return $t_checkout_information;
}
?>