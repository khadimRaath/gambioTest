<?php
/* --------------------------------------------------------------
   GMGPrintOrderManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

class GMGPrintOrderManager_ORIGIN
{
	function __construct()
	{
		//
	}	
	
	function save($p_product, $p_orders_products_id)
	{
		// avoid saving invalid data
		$_SESSION['coo_gprint_cart']->restore();
		
		$coo_gprint_configuration = new GMGPrintConfiguration($_SESSION['languages_id']);
		
		if(is_array($_SESSION['coo_gprint_cart']->v_elements[$p_product]))
		{
			$t_product = explode('{', $p_product);
			$t_products_id = $t_product[0];
						
			$t_surfaces_groups_id = $this->get_surfaces_groups_id($t_products_id);
			
			$coo_gprint = new GMGPrintSurfacesManager($t_surfaces_groups_id);
			
			$coo_gprint->load_surfaces_group($t_surfaces_groups_id, $coo_gprint_configuration, 'cart_' . $p_product);
			
			$t_orders_surfaces_groups_id = $this->create($coo_gprint->get_name(), $p_orders_products_id);
			
			$coo_orders_gprint = new GMGPrintOrderSurfacesManager($t_orders_surfaces_groups_id);
			
			foreach($coo_gprint->v_surfaces AS $t_surfaces_id => $t_surface)
			{
				$t_orders_surfaces_id = $coo_orders_gprint->create_surface($coo_gprint->v_surfaces[$t_surfaces_id]->get_name($_SESSION['languages_id']), 
																			$coo_gprint->v_surfaces[$t_surfaces_id]->get_width(), 
																			$coo_gprint->v_surfaces[$t_surfaces_id]->get_height());
				
				foreach($coo_gprint->v_surfaces[$t_surfaces_id]->v_elements AS $t_elements_id => $t_element)
				{										
					if($coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_type() != 'dropdown')
					{
						$t_value = $coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_value($_SESSION['languages_id']);
					}
					else
					{
						$t_value = $coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_selected_dropdown_value();
					}
					
					$t_orders_elements_id = $coo_orders_gprint->v_surfaces[$t_orders_surfaces_id]->create_element(
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_type(), 
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_name($_SESSION['languages_id']), 
						$t_value, 
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_width(), 
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_height(), 
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_position_x(), 
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_position_y(), 
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_z_index(),
						$coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_show_name()
					);
					
					if($coo_gprint->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_type() == 'file'
						&& isset($_SESSION['coo_gprint_cart']->v_files[$p_product][$t_elements_id])
					)
					{
						$coo_orders_gprint->v_surfaces[$t_orders_surfaces_id]->v_elements[$t_orders_elements_id]->set_elements_uploads_id($_SESSION['coo_gprint_cart']->v_files[$p_product][$t_elements_id]);
					}
				}
			}
		}
	}
	
	function create($p_name, $p_orders_products_id)
	{
		$c_orders_products_id = $p_orders_products_id;
		
		$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		
		$t_create_set = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS . " 
											SET name = '" . $c_name . "',
												orders_products_id = '" . $c_orders_products_id . "'");
		$t_surfaces_groups_id = xtc_db_insert_id();		
		
		return $t_surfaces_groups_id;
	}
	
	function get_surfaces_groups_id($p_products_id)
	{
		$c_products_id = (int)$p_products_id;
		$t_surfaces_groups_id = false;
				
		$t_get_surfaces_groups_id = xtc_db_query("SELECT gm_gprint_surfaces_groups_id
													FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . "
													WHERE products_id = '" . $c_products_id . "'");
		if(xtc_db_num_rows($t_get_surfaces_groups_id) == 1)
		{
			$t_surfaces_group_data = xtc_db_fetch_array($t_get_surfaces_groups_id);
			$t_surfaces_groups_id = $t_surfaces_group_data['gm_gprint_surfaces_groups_id'];
		}
		
		return $t_surfaces_groups_id;
	}
	
	function get_order_surfaces_groups_id($p_orders_products_id)
	{
		$c_orders_products_id = (int)$p_orders_products_id;
		$t_surfaces_groups_id = false;
		
		$t_get_surfaces_groups_id = xtc_db_query("SELECT gm_gprint_orders_surfaces_groups_id 
											FROM " . TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS . "
											WHERE orders_products_id = '" . $c_orders_products_id . "'");
		if(xtc_db_num_rows($t_get_surfaces_groups_id) == 1)
		{
			$t_result = xtc_db_fetch_array($t_get_surfaces_groups_id);
			$t_surfaces_groups_id = $t_result['gm_gprint_orders_surfaces_groups_id'];
		}
		
		return $t_surfaces_groups_id;
	}
	
	function delete_order($p_orders_id)
	{
		$c_orders_id = (int)$p_orders_id;
		
		$t_get_orders_products_id = xtc_db_query("SELECT orders_products_id 
													FROM " . TABLE_ORDERS_PRODUCTS . "
													WHERE orders_id = '" . $c_orders_id . "'");
		while($t_order_data = xtc_db_fetch_array($t_get_orders_products_id))
		{
			$this->delete($t_order_data['orders_products_id']);
		}
	}
	
	function delete($p_orders_products_id)
	{
		$t_order_surfaces_groups_id = $this->get_order_surfaces_groups_id($p_orders_products_id);
		
		if($t_order_surfaces_groups_id !== false)
		{
			$coo_order_surfaces_manager = new GMGPrintOrderSurfacesManager($t_order_surfaces_groups_id);
			$coo_order_surfaces_manager->load_surfaces_group($t_order_surfaces_groups_id);
			
			foreach($coo_order_surfaces_manager->v_surfaces AS $t_surfaces_id => $t_surface)
			{
				$coo_order_surfaces_manager->delete_surface($t_surfaces_id);
			}
			
			$t_delete_orders_surfaces_group = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS . "
															WHERE gm_gprint_orders_surfaces_groups_id = '" . $t_order_surfaces_groups_id . "'");
		}
	}
}
MainFactory::load_origin_class('GMGPrintOrderManager');