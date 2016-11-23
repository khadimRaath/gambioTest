<?php
/* --------------------------------------------------------------
   GMGPrintSurfacesManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMGPrintSurfacesManager_ORIGIN
{
	var $v_surfaces = array();
	var $v_current_surfaces_id = 0;
	var $v_name = '';
	
	var $v_surfaces_groups_id;
	
	function __construct($p_surfaces_groups_id)
	{
		$this->v_surfaces_groups_id = (int)$p_surfaces_groups_id;
	}
	
	function create_surface($p_names, $p_width, $p_height)
	{
		$c_surfaces_groups_id = (int)$this->v_surfaces_groups_id;
		
		$t_create_surface = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_SURFACES . " 
											SET gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
		$t_surfaces_id = xtc_db_insert_id();
		
		if(!empty($t_surfaces_id))
		{
			$t_delete_trash = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . " 
											WHERE gm_gprint_surfaces_id = '" . $t_surfaces_id . "'");
			
			foreach($p_names AS $t_language_id => $t_name)
			{
				$c_languages_id = (int)$t_language_id;
				$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($t_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
				
				$t_create_surface_description = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . " 
												SET gm_gprint_surfaces_id = '" . $t_surfaces_id . "',
													name = '" . $c_name . "',
													languages_id = '" . $c_languages_id . "'");
			}
			
			$coo_surface = new GMGPrintSurfaces($t_surfaces_id);
			$coo_surface->set_size($p_width, $p_height);
			$coo_surface->set_names($p_names);
			
			$this->v_surfaces[$t_surfaces_id] =& $coo_surface;
			$this->set_current_surfaces_id($t_surfaces_id);
		}
		
		return $t_surfaces_id;
	}
	
	function load_surfaces_group($p_surfaces_groups_id, &$p_coo_gprint_configuration, $p_product = '')
	{
		$c_surfaces_groups_id = (int)$p_surfaces_groups_id;
		
		$t_get_name = xtc_db_query("SELECT name 
									FROM " . TABLE_GM_GPRINT_SURFACES_GROUPS . " 
									WHERE gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
		if(xtc_db_num_rows($t_get_name) == 1)
		{
			$t_gm_name = xtc_db_fetch_array($t_get_name);
			$this->set_name($t_gm_name['name']);
		}
		
		$t_get_surfaces = xtc_db_query("SELECT 
											gm_gprint_surfaces_id, 
											width, 
											height 
										FROM " . TABLE_GM_GPRINT_SURFACES . "
										WHERE gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "'
										ORDER BY gm_gprint_surfaces_id");
		while($t_surfaces = xtc_db_fetch_array($t_get_surfaces))
		{
			$coo_surface = new GMGPrintSurfaces($t_surfaces['gm_gprint_surfaces_id']);
			$coo_surface->set_width($t_surfaces['width']);
			$coo_surface->set_height($t_surfaces['height']);
			
			$t_names = array();
			
			$t_get_surfaces_names = xtc_db_query("SELECT 
														languages_id,
														name
													FROM " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . "
													WHERE gm_gprint_surfaces_id = '" . $t_surfaces['gm_gprint_surfaces_id'] . "'
													ORDER BY languages_id"); 
			while($t_surfaces_names = xtc_db_fetch_array($t_get_surfaces_names))
			{
				$t_names[$t_surfaces_names['languages_id']] = $t_surfaces_names['name'];
			}
			
			$coo_surface->set_names($t_names);
			
			$coo_surface->load_elements($t_surfaces['gm_gprint_surfaces_id'], $p_coo_gprint_configuration);
			
			$this->v_surfaces[$t_surfaces['gm_gprint_surfaces_id']] = $coo_surface;
			$this->set_current_surfaces_id($t_surfaces['gm_gprint_surfaces_id']);
		}
		
		if(!empty($p_product))
		{
			if(strpos($p_product, 'cart_') === 0)
			{
				$t_product = str_replace('cart_' , '', $p_product);
				
				foreach($this->v_surfaces AS $t_surfaces_id => $t_surface)
				{
					foreach($this->v_surfaces[$t_surfaces_id]->v_elements AS $t_elements_id => $t_element)
					{
						foreach($_SESSION['coo_gprint_cart']->v_elements[$t_product] AS $t_cart_elements_id => $t_cart_elements_value)
						{
							if($t_elements_id == $t_cart_elements_id)
							{
								$t_customers_values = array();
								$t_languages_ids = $p_coo_gprint_configuration->get_languages_ids();
								
								for($i = 0; $i < count($t_languages_ids); $i++)
								{
									$t_customers_values[$t_languages_ids[$i]][] = $t_cart_elements_value;
								}								
								
								if($this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_type() != 'dropdown')
								{
									$this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->set_values($t_customers_values);
								}
								else
								{
									$this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->set_selected_dropdown_value($t_cart_elements_value);
								}
								
								if($_SESSION['coo_gprint_cart']->v_files[$t_product][$t_cart_elements_id] > 0)
								{
									$c_uploads_id = (int)$_SESSION['coo_gprint_cart']->v_files[$t_product][$t_cart_elements_id];
									$t_get_download_key = xtc_db_query("SELECT download_key
																		FROM " . TABLE_GM_GPRINT_UPLOADS . "
																		WHERE gm_gprint_uploads_id = '" . $c_uploads_id . "'");
									if(xtc_db_num_rows($t_get_download_key) == 1)
									{
										$t_download_data = xtc_db_fetch_array($t_get_download_key);
										$this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->set_download_key($t_download_data['download_key']);
									}
								}
							}
						}
					}
				}				
			}
			elseif(strpos($p_product, 'wishlist_') === 0)
			{
				$t_product = str_replace('wishlist_' , '', $p_product);
				
				foreach($this->v_surfaces AS $t_surfaces_id => $t_surface)
				{
					foreach($this->v_surfaces[$t_surfaces_id]->v_elements AS $t_elements_id => $t_element)
					{
						foreach($_SESSION['coo_gprint_wishlist']->v_elements[$t_product] AS $t_wishlist_elements_id => $t_wishlist_elements_value)
						{
							if($t_elements_id == $t_wishlist_elements_id)
							{
								$t_customers_values = array();
								$t_languages_ids = $p_coo_gprint_configuration->get_languages_ids();
								
								for($i = 0; $i < count($t_languages_ids); $i++)
								{
									$t_customers_values[$t_languages_ids[$i]][] = $t_wishlist_elements_value;
								}								
								
								if($this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->get_type() != 'dropdown')
								{
									$this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->set_values($t_customers_values);
								}
								else
								{
									$this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->set_selected_dropdown_value($t_wishlist_elements_value);
								}
								
								if($_SESSION['coo_gprint_wishlist']->v_files[$t_product][$t_wishlist_elements_id] > 0)
								{
									$c_uploads_id = (int)$_SESSION['coo_gprint_wishlist']->v_files[$t_product][$t_wishlist_elements_id];
									$t_get_download_key = xtc_db_query("SELECT download_key
																		FROM " . TABLE_GM_GPRINT_UPLOADS . "
																		WHERE gm_gprint_uploads_id = '" . $c_uploads_id . "'");
									if(xtc_db_num_rows($t_get_download_key) == 1)
									{
										$t_download_data = xtc_db_fetch_array($t_get_download_key);
										$this->v_surfaces[$t_surfaces_id]->v_elements[$t_elements_id]->set_download_key($t_download_data['download_key']);
									}
								}
							}
						}
					}
				}				
			}
		}
		
		$coo_json = new GMJSON(false, true);
		$t_json = $coo_json->encode($this);
		return $t_json;
	}
	
	function delete_surface($p_surfaces_id)
	{
		$t_success = false;
		
		if(!empty($p_surfaces_id))
		{
			$c_surfaces_id = (int)$p_surfaces_id;
			
			$t_get_elements = xtc_db_query("SELECT gm_gprint_elements_id 
											FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
											WHERE gm_gprint_surfaces_id = '" . $c_surfaces_id. "'");
			while($t_elements = xtc_db_fetch_array($t_get_elements))
			{
				$t_success = $this->v_surfaces[$p_surfaces_id]->delete_element($t_elements['gm_gprint_elements_id']);
			}
			
			if($t_success == 'true' || xtc_db_num_rows($t_get_elements) == 0)
			{
				$t_success = false;
				
				$t_delete_surface_description = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . "
																WHERE gm_gprint_surfaces_id = '" . $c_surfaces_id. "'");
				$t_delete_surface = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES . "
																WHERE gm_gprint_surfaces_id = '" . $c_surfaces_id. "'");
				
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
		
		$t_update_name = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_SURFACES_GROUPS . " 
										SET name = '" . $c_name . "' 
										WHERE gm_gprint_surfaces_groups_id = '" . $c_surfaces_groups_id . "'");
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
MainFactory::load_origin_class('GMGPrintSurfacesManager');