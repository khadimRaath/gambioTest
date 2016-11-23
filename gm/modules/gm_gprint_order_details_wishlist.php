<?php
/* --------------------------------------------------------------
   gm_gprint_order_details_wishlist.php 2010-01-06 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

if(isset($_SESSION['coo_gprint_wishlist']))
{
	$coo_gprint_content_manager = new GMGPrintContentManager();
	
	// delete empty attributes (random id)
	foreach($module_content[$i]['ATTRIBUTES'] AS $t_key => $t_value)
	{
		if(empty($module_content[$i]['ATTRIBUTES'][$t_key]['NAME']))
		{
			unset($module_content[$i]['ATTRIBUTES'][$t_key]);
		}
	}
	
	if(isset($_SESSION['coo_gprint_wishlist']->v_elements[$p_products_array[$i]['id']]) && $value == 0)
	{
		$t_gm_gprint_data = $coo_gprint_content_manager->get_content($p_products_array[$i]['id'], 'wishlist');
		for($j = 0; $j < count($t_gm_gprint_data); $j++)
		{
			$module_content[$i]['ATTRIBUTES'][] = array('ID' => 0,
														'MODEL' => '', 
														'NAME' => $t_gm_gprint_data[$j]['NAME'], 
														'VALUE_NAME' => $t_gm_gprint_data[$j]['VALUE']);
		}
	}
	elseif($_SESSION['coo_gprint_wishlist']->check_wishlist($p_products_array[$i]['id'], 'coo_gprint_wishlist') !== false && $value == 0)
	{
		$t_gm_gprint_data = $coo_gprint_content_manager->get_content($p_products_array[$i]['id'], 'wishlist');
		for($j = 0; $j < count($t_gm_gprint_data); $j++)
		{
			$module_content[$i]['ATTRIBUTES'][] = array('ID' => 0,
														'MODEL' => '', 
														'NAME' => $t_gm_gprint_data[$j]['NAME'], 
														'VALUE_NAME' => $t_gm_gprint_data[$j]['VALUE']);
		}
	}
}
?>