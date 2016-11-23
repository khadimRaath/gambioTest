<?php
/* --------------------------------------------------------------
   GMGPrintSurfacesGroupsManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintSurfacesGroupsManager_ORIGIN
{
	var $v_surfaces_groups_id;
	
	function __construct($p_surfaces_groups_id = false)
	{
		$this->set_surfaces_groups_id($p_surfaces_groups_id);
	}
	
	function create($p_name)
	{
		$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		
		$t_create_set = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_SURFACES_GROUPS . " 
											SET name = '" . $c_name . "'");
		$t_surfaces_groups_id = xtc_db_insert_id();
		
		
		return $t_surfaces_groups_id;
	}
	
	function delete($p_surfaces_groups_id, &$p_coo_gprint, &$p_coo_gprint_configuration)
	{
		$c_surfaces_groups_id = (int)$p_surfaces_groups_id;
		
		if(is_object($p_coo_gprint)) 
		{
			$p_coo_gprint->load_surfaces_group($p_surfaces_groups_id, $p_coo_gprint_configuration);
		
			$t_success = "true";
			
			foreach($p_coo_gprint->v_surfaces AS $t_surfaces_id => $t_surface)
			{
				$t_success = $p_coo_gprint->delete_surface($t_surfaces_id);
				if($t_success != "true")
				{
					return "cannot delete surfaces";
				}
			}
			
			$t_delete_surfaces_group = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS . " 
														WHERE gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
			$t_delete_surfaces_group = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . " 
														WHERE gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
		}
		
		return true;
	}
	
	function set_surfaces_groups_id($p_surfaces_groups_id)
	{
		$this->v_surfaces_groups_id = $p_surfaces_groups_id;
	}
}
MainFactory::load_origin_class('GMGPrintSurfacesGroupsManager');