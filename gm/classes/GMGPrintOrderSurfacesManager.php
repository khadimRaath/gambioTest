<?php
/* --------------------------------------------------------------
   GMGPrintOrderSurfacesManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMGPrintOrderSurfacesManager_ORIGIN
{
	var $v_surfaces = array();
	var $v_current_surfaces_id = 0;
	var $v_name = '';
	
	var $v_surfaces_groups_id;
	
	function __construct($p_surfaces_groups_id)
	{
		$this->v_surfaces_groups_id = (int)$p_surfaces_groups_id;
	}
	
	function create_surface($p_name, $p_width, $p_height)
	{
		$c_width = (int)$p_width;
		$c_height = (int)$p_height;
		$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$c_surfaces_groups_id = (int)$this->v_surfaces_groups_id;
		
		$t_create_surface = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ORDERS_SURFACES . " 
											SET gm_gprint_orders_surfaces_groups_id = '" . $c_surfaces_groups_id . "', 
												name = '" . $c_name . "',
												width = '" . $c_width . "',
												height = '" . $c_height . "'");
		$t_surfaces_id = xtc_db_insert_id();
		
		if(!empty($t_surfaces_id))
		{
			$coo_surface = new GMGPrintOrderSurfaces($t_surfaces_id);
			$coo_surface->set_width($c_width);
			$coo_surface->set_height($c_height);
			$coo_surface->set_name($c_name);
			$this->v_surfaces[$t_surfaces_id] =& $coo_surface;
			$this->set_current_surfaces_id($t_surfaces_id);
		}
		
		return $t_surfaces_id;
	}
	
	function load_surfaces_group($p_surfaces_groups_id)
	{
		$c_surfaces_groups_id = (int)$p_surfaces_groups_id;
		
		$t_get_name = xtc_db_query("SELECT name 
									FROM " . TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS . " 
									WHERE gm_gprint_orders_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
		if(xtc_db_num_rows($t_get_name) == 1)
		{
			$t_gm_name = xtc_db_fetch_array($t_get_name);
			$this->set_name($t_gm_name['name']);
		}
		
		$t_get_surfaces = xtc_db_query("SELECT 
											gm_gprint_orders_surfaces_id, 
											name,
											width, 
											height 
										FROM " . TABLE_GM_GPRINT_ORDERS_SURFACES . "
										WHERE gm_gprint_orders_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
		while($t_surfaces = xtc_db_fetch_array($t_get_surfaces))
		{
			$coo_surface = new GMGPrintOrderSurfaces($t_surfaces['gm_gprint_orders_surfaces_id']);
			$coo_surface->set_width($t_surfaces['width']);
			$coo_surface->set_height($t_surfaces['height']);
			$coo_surface->set_name($t_surfaces['name']);
			
			$coo_surface->load_elements($t_surfaces['gm_gprint_orders_surfaces_id']);
			
			$this->v_surfaces[$t_surfaces['gm_gprint_orders_surfaces_id']] = $coo_surface;
			$this->set_current_surfaces_id($t_surfaces['gm_gprint_orders_surfaces_id']);
		}
		
		$coo_json = new GMJSON(false, true);
		$t_json = $coo_json->encode($this);
		return $t_json;
	}
	
	function delete_surface($p_surfaces_id)
	{
		$t_success = false;
		
		$c_surfaces_id = (int)$p_surfaces_id;
		
		if(!empty($c_surfaces_id))
		{
			$t_get_elements = xtc_db_query("SELECT gm_gprint_orders_elements_id 
											FROM " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . " 
											WHERE gm_gprint_orders_surfaces_id = '" . $c_surfaces_id. "'");
			while($t_elements = xtc_db_fetch_array($t_get_elements))
			{
				$t_success = $this->v_surfaces[$p_surfaces_id]->delete_element($t_elements['gm_gprint_orders_elements_id']);
			}
			
			if($t_success == 'true' || xtc_db_num_rows($t_get_elements) == 0)
			{
				$t_success = false;
				
				$t_delete_surface = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ORDERS_SURFACES . "
																WHERE gm_gprint_orders_surfaces_id = '" . $c_surfaces_id. "'");
				
				unset($this->v_surfaces[$p_surfaces_id]);
				
				$t_success = 'true';
			}
		}
		
		return $t_success;
	}
	
	function update_name($p_name)
	{
		$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$c_surfaces_groups_id = (int)$this->get_surfaces_groups_id();
		
		$t_success = false;
		$t_update_name = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS . " 
										SET name = '" . $c_name . "' 
										WHERE gm_gprint_orders_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
		if($t_update_name !== false)
		{
			$this->set_name($p_name);
			$t_success = true;
		}
		
		return $t_success;
	}
	
	function get_name()
	{
		return $this->v_name;
	}
	
	function set_name($p_name)
	{
		$this->v_name = $p_name;
	}
	
	function get_current_surfaces_id()
	{
		return $this->v_current_surfaces_id;
	}
	
	function set_current_surfaces_id($p_current_surfaces_id)
	{
		$this->v_current_surfaces_id = $p_current_surfaces_id;
	}
	
	function get_surfaces_groups_id()
	{
		return $this->v_surfaces_groups_id;
	}
}
MainFactory::load_origin_class('GMGPrintOrderSurfacesManager');