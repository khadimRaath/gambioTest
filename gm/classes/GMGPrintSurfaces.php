<?php
/* --------------------------------------------------------------
   GMGPrintSurfaces.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintSurfaces_ORIGIN
{
	var $v_elements = array();
	var $v_width = 0;
	var $v_height = 0;
	var $v_names = array();
	var $v_current_elements_id = 0;
	
	var $v_surfaces_id;
	
	function __construct($p_surfaces_id)
	{
		$this->v_surfaces_id = (int)$p_surfaces_id;
	}
	
	function create_element($p_type, $p_names, $p_values, $p_width, $p_height, $p_position_x, $p_position_y, $p_z_index, $p_max_characters, $p_show_name, $p_allowed_extensions, $p_minimum_filesize, $p_maximum_filesize)
	{
		$t_elements_id = 0;
		
		if($this->v_surfaces_id > 0 && is_array($p_values))
		{
			$c_surfaces_id = (int)$this->get_surfaces_id();
			
			$t_create_element = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ELEMENTS . " 
												SET gm_gprint_surfaces_id = '" . $c_surfaces_id . "'");
			$t_elements_id = xtc_db_insert_id();
			$coo_element = new GMGPrintElements($t_elements_id);
			$coo_element->set_size($p_width, $p_height);
			$coo_element->set_position($p_position_x, $p_position_y);
			$coo_element->set_element_z_index($p_z_index);
			if($p_type == 'text_input' || $p_type == 'textarea' || $p_type == 'dropdown')
			{
				$coo_element->set_element_max_characters($p_max_characters);
				$coo_element->set_element_show_name($p_show_name);
			}
			if($p_type == 'file')
			{
				$coo_element->set_element_allowed_extensions($p_allowed_extensions);
				$coo_element->set_element_minimum_filesize($p_minimum_filesize);
				$coo_element->set_element_minimum_filesize($p_maximum_filesize);
			}
			$coo_element->set_element_values($p_type, $p_names, $p_values);
		
			$this->v_elements[$t_elements_id] =& $coo_element;
		
			$this->set_current_elements_id($t_elements_id);
		}
		
		return $t_elements_id;
	}
	
	function load_elements($p_surfaces_id, $p_coo_gprint_configuration)
	{
		$c_surfaces_id = (int)$p_surfaces_id;
		
		$t_get_elements = xtc_db_query("SELECT
											gm_gprint_elements_id,
											gm_gprint_elements_groups_id,
											position_x,
											position_y,
											height,
											width,
											z_index,
											max_characters,
											allowed_extensions,
											minimum_filesize,
											maximum_filesize,
											show_name
										FROM " . TABLE_GM_GPRINT_ELEMENTS . "
										WHERE gm_gprint_surfaces_id = '" . $c_surfaces_id . "'");
		while($t_elements = xtc_db_fetch_array($t_get_elements))
		{
			$coo_element = new GMGPrintElements($t_elements['gm_gprint_elements_id']);
			$coo_element->set_width($t_elements['width']);
			$coo_element->set_height($t_elements['height']);
			$coo_element->set_position_x($t_elements['position_x']);
			$coo_element->set_position_y($t_elements['position_y']);
			$coo_element->set_z_index($t_elements['z_index']);
			
			$t_get_type = xtc_db_query("SELECT
											group_type
										FROM " . TABLE_GM_GPRINT_ELEMENTS_GROUPS . "
										WHERE gm_gprint_elements_groups_id = '" . $t_elements['gm_gprint_elements_groups_id'] . "'");
			while($t_type = xtc_db_fetch_array($t_get_type))
			{
				$coo_element->set_type($t_type['group_type']);
								
				$t_values = array();
				$t_names = array();
				
				$t_get_elements_values = xtc_db_query("SELECT
															languages_id,
															name,
															elements_value
														FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . "
														WHERE gm_gprint_elements_groups_id = '" . $t_elements['gm_gprint_elements_groups_id'] . "'
														ORDER BY languages_id, gm_gprint_elements_values_id");
				while($t_elements_values = xtc_db_fetch_array($t_get_elements_values))
				{
					$t_values[$t_elements_values['languages_id']][] = $t_elements_values['elements_value'];
					$t_names[$t_elements_values['languages_id']] = $t_elements_values['name'];
					
					if($t_type['group_type'] == 'image' && $p_coo_gprint_configuration->get_languages_id() == $t_elements_values['languages_id'])
					{
						$t_image_data = array();
						$t_image_data = $coo_element->get_image_data($t_elements_values['elements_value']);
						$coo_element->set_width($t_image_data['WIDTH']);
						$coo_element->set_height($t_image_data['HEIGHT']);
					}			
				}
				
				$coo_element->set_values($t_values);			
				$coo_element->set_names($t_names);				
			}
			
			$coo_element->set_max_characters($t_elements['max_characters']);
			$coo_element->set_show_name($t_elements['show_name']);
			$coo_element->set_allowed_extensions($t_elements['allowed_extensions']);
			$coo_element->set_minimum_filesize($t_elements['minimum_filesize']);
			$coo_element->set_maximum_filesize($t_elements['maximum_filesize']);
		
			$this->v_elements[$t_elements['gm_gprint_elements_id']] = $coo_element;
		
			$this->set_current_elements_id($t_elements['gm_gprint_elements_id']);
		}
	}
	
	function delete_element($p_elements_id)
	{
		$t_success = false;
		
		if(!empty($p_elements_id))
		{
			$c_elements_id = (int)$p_elements_id;
			
			$t_get_groups_id = xtc_db_query("SELECT gm_gprint_elements_groups_id 
												FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
												WHERE gm_gprint_elements_id = '" . $c_elements_id. "'");
		
			if(xtc_db_num_rows($t_get_groups_id) == 1)
			{
				$t_groups_id = xtc_db_fetch_array($t_get_groups_id);
				
				if($t_groups_id['gm_gprint_elements_groups_id'] > 0)
				{
					$t_get_linked_elements = xtc_db_query("SELECT COUNT(*) AS count
															FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
															WHERE gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'");
					$t_linked_elements = xtc_db_fetch_array($t_get_linked_elements);
								
					if($t_linked_elements['count'] == 1)
					{			
						if($this->v_elements[$p_elements_id]->v_type == 'image')
						{
							$coo_file_manager = new GMGPrintFileManager();
							
							foreach($this->v_elements[$p_elements_id]->v_values AS $t_languages_id => $t_values)
							{
								if(!empty($t_values[0]))
								{
									$c_filename = gm_prepare_string($t_values[0]);
									
									$t_get_filename_values = xtc_db_query("SELECT COUNT(*) AS count
																			FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . " 
																			WHERE elements_value = '" . $c_filename . "'
																			GROUP BY gm_gprint_elements_groups_id");
									$t_filename_values = xtc_db_fetch_array($t_get_filename_values);
										
									if($t_filename_values['count'] == 1)
									{	
										$t_filename = basename($t_values[0]);
										$t_delete_file = $coo_file_manager->delete_file(DIR_FS_CATALOG . DIR_WS_IMAGES . 'gm/gprint/' . $t_filename);
									}
								}						
							}	
						}
						
						$t_delete_elements_values = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . "
																	WHERE gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'");
						$t_delete_elements_values = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS_GROUPS . "
																	WHERE gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id']. "'");
					}

					$t_delete_elements_values = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS . "
																WHERE gm_gprint_elements_id = '" . $c_elements_id. "'");
					
					unset($this->v_elements[$p_elements_id]);
					
					$t_success = 'true';
				}
			}
		}
		
		return $t_success;
	}
	
	function set_size($p_width, $p_height, $p_surfaces_id = 0)
	{
		if(empty($p_surfaces_id))
		{
			$p_surfaces_id = $this->get_surfaces_id();
		}
		
		$c_width = (int)$p_width;
		$c_height = (int)$p_height;
		$c_surfaces_id = (int)$p_surfaces_id;
		
		$t_success = false;
		
		if($c_surfaces_id > 0 && $c_width >= 0 && $c_height >= 0)
		{
			$t_update_surface = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_SURFACES . "
												SET width = '" . $c_width . "',
													height = '" . $c_height . "'
												WHERE gm_gprint_surfaces_id = '" . $c_surfaces_id . "'");
			$t_success = 'true';
			
			$this->set_width($c_width);
			$this->set_height($c_height);
		}
		
		return $t_success;
	}
	
	function set_width($p_width)
	{
		$this->v_width = $p_width;
	}
	
	function set_height($p_height)
	{
		$this->v_height = $p_height;
	}
	
	function set_names($p_names)
	{
		$t_success = false;
		
		foreach($p_names AS $t_language_id => $t_name)
		{
			$this->v_names[$t_language_id] = $t_name;
			$t_success = true;
		}
		
		return $t_success;
	}
	
	function update_names($p_surfaces_id, $p_names)
	{
		$c_surfaces_id = (int)$p_surfaces_id;
		
		if(!empty($c_surfaces_id))
		{
			$t_delete_trash = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . " 
											WHERE gm_gprint_surfaces_id = '" . $c_surfaces_id . "'");
			
			foreach($p_names AS $t_language_id => $t_name)
			{
				$c_languages_id = (int)$t_language_id;
				$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($t_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
				
				$t_create_surface_description = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . " 
												SET gm_gprint_surfaces_id = '" . $c_surfaces_id . "',
													name = '" . $c_name . "',
													languages_id = '" . $c_languages_id . "'");
			}
		}
			
		$this->set_names($p_names);
	}
	
	function set_current_elements_id($p_element_id)
	{
		$this->v_current_elements_id = $p_element_id;
	}
	
	function get_width()
	{
		return $this->v_width;
	}
	
	function get_height()
	{
		return $this->v_height;
	}
	
	function get_names()
	{
		return $this->v_names;
	}
	
	function get_name($p_languages_id)
	{
		return $this->v_names[$p_languages_id];
	}
	
	function get_surfaces_id()
	{
		return $this->v_surfaces_id;
	}
}
MainFactory::load_origin_class('GMGPrintSurfaces');