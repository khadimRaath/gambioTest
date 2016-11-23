<?php
/* --------------------------------------------------------------
  gv_send.php 2013-12-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_vpe_name.inc.php)

  Released under the GNU General Public License
  ----------------------------------------------------------------------------------------- */

function xtc_get_vpe_name($p_products_vpe_id)
{
	static $t_vpe_name_array;
	
	$c_languages_id = (int)$_SESSION['languages_id'];
	$c_products_vpe_id = (int)$p_products_vpe_id;
	$t_key = $c_languages_id . '_' . $c_products_vpe_id;
	
	if($t_vpe_name_array !== null && isset($t_vpe_name_array[$t_key]))
	{
		return $t_vpe_name_array[$t_key];
	}
	else
	{
		$t_sql = 'SELECT products_vpe_name 
					FROM ' . TABLE_PRODUCTS_VPE . ' 
					WHERE 
						language_id = "' . $c_languages_id . '" AND 
						products_vpe_id = "' . $c_products_vpe_id . '"';
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			
			if(is_array($t_vpe_name_array) === false)
			{
				$t_vpe_name_array = array();
			}
			
			$t_vpe_name_array[$t_key] = $t_result_array['products_vpe_name'];
			
			return $t_vpe_name_array[$t_key];
		}
		
		return '';
	}
}
