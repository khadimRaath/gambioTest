<?php
/* --------------------------------------------------------------
   GMGPrintProductManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintProductManager_ORIGIN
{
	
	function __construct()
	{
		//
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
			$t_gprint_data = xtc_db_fetch_array($t_get_surfaces_groups_id);
			$t_surfaces_groups_id = $t_gprint_data['gm_gprint_surfaces_groups_id'];
		}
		
		return $t_surfaces_groups_id;
	}
	
	function add($p_surfaces_groups_id, $p_products_id)
	{
		$c_surfaces_groups_id = (int)$p_surfaces_groups_id;
		$c_products_id = (int)$p_products_id;
		
		$t_delete = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . "
									WHERE products_id = '" . $c_products_id . "'");
		
		$t_insert = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . "
									SET	gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "',
										products_id = '" . $c_products_id . "'");
	}
	
	function remove($p_products_id)
	{
		$c_products_id = (int)$p_products_id;
		
		$t_delete = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . "
									WHERE products_id = '" . $c_products_id . "'");
	}
	
	function get_surfaces_groups()
	{
		$t_surfaces_groups = array();
		
		$t_get_surfaces_groups = xtc_db_query("SELECT 
													gm_gprint_surfaces_groups_id,
													name
												FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS . "
												ORDER BY name");
		while($t_surfaces_groups_data = xtc_db_fetch_array($t_get_surfaces_groups))
		{
			$t_surfaces_groups[] = array('ID' => $t_surfaces_groups_data['gm_gprint_surfaces_groups_id'],
											'NAME' => $t_surfaces_groups_data['name']);
		}
		
		return $t_surfaces_groups;
	}
	
	function save_category($p_categories_id, $p_surfaces_groups_id, $p_include_subcategories = false, $p_remove = false)
	{
		$c_categories_id = (int)$p_categories_id;
		$c_surfaces_groups_id = (int)$p_surfaces_groups_id;
		
		$t_get_products = xtc_db_query("SELECT products_id 
										FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
										WHERE categories_id = '" . $c_categories_id . "'");
		while($t_products = xtc_db_fetch_array($t_get_products))
		{
			if(!$p_remove && $c_surfaces_groups_id > 0)
			{
				$this->add($c_surfaces_groups_id, $t_products['products_id']);
			}
			elseif($p_remove)
			{
				$this->remove($t_products['products_id']);
			}
		}
		
		if($p_include_subcategories)
		{
			$t_gm_gprint_categories = $this->get_categories($c_categories_id);
			
			for($i = 0; $i < count($t_gm_gprint_categories); $i++)
			{
				$t_get_products = xtc_db_query("SELECT products_id 
												FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
												WHERE categories_id = '" . (int)$t_gm_gprint_categories[$i] . "'");
				while($t_products = xtc_db_fetch_array($t_get_products))
				{
					if(!$p_remove && $c_surfaces_groups_id > 0)
					{
						$this->add($c_surfaces_groups_id, $t_products['products_id']);
					}
					elseif($p_remove)
					{
						$this->remove($t_products['products_id']);
					}
				}
			}
		}
	}
	
	function get_categories($p_parent_id, $p_categories_array = false)
	{
		$c_parent_id = (int)$p_parent_id;

	    if (!is_array($p_categories_array))
	    {
	    	$t_categories_array = array();
	    }
	    else
	    {
	    	$t_categories_array = $p_categories_array;
	    }
		
	    $t_get_categories = xtc_db_query("SELECT 
	    										categories_id
	    									FROM " . TABLE_CATEGORIES . " 
	    									WHERE parent_id = '" . $c_parent_id . "'");
	    
	    while($t_categories = xtc_db_fetch_array($t_get_categories))
	    {
	    	$t_categories_array[] = $t_categories['categories_id'];
	    	
	    	if($t_categories['categories_id'] != $c_parent_id)
	    	{
	    		$t_categories_array = $this->get_categories($t_categories['categories_id'], $t_categories_array);
	    	}
	    }
	    
	    return $t_categories_array;
	}
}
MainFactory::load_origin_class('GMGPrintProductManager');