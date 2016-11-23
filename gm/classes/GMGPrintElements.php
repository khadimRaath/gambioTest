<?php
/* --------------------------------------------------------------
   GMGPrintElements.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintElements_ORIGIN
{
	var $v_width = 0;
	var $v_height = 0;
	var $v_position_x = 0;
	var $v_position_y = 0;
	var $v_z_index = 0;
	var $v_max_characters = 0;
	var $v_show_name = false;
	var $v_allowed_extensions = '';
	var $v_minimum_filesize = 0;
	var $v_maximum_filesize = 0;
	var $v_type = '';
	var $v_names = array();
	var $v_values = array();
	var $v_selected_dropdown_value = '';
	var $v_download_key = '';
	
	var $v_elements_id;
	
	function __construct($p_elements_id)
	{
		$this->v_elements_id = $p_elements_id;
	}
	
	function set_size($p_width, $p_height)
	{
		$c_width = (int)$p_width;
		$c_height = (int)$p_height;
		
		$t_success = false;
		
		if($this->get_elements_id() > 0 && $c_width > 0 && $c_height > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET width = '" . $c_width . "',
													height = '" . $c_height . "'
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
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
	
	function set_position($p_position_x, $p_position_y)
	{
		$c_position_x = (int)$p_position_x;
		$c_position_y = (int)$p_position_y;
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET position_x = '" . $c_position_x . "',
													position_y = '" . $c_position_y . "'
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			$this->set_position_x($c_position_x);
			$this->set_position_y($c_position_y);
			
			$t_success = 'true';
		}
		
		return $t_success;
	}
	
	function set_position_x($p_position_x)
	{
		$this->v_position_x = $p_position_x;
	}
	
	function set_position_y($p_position_y)
	{
		$this->v_position_y = $p_position_y;
	}
	
	function set_element_z_index($p_z_index)
	{
		$c_z_index = (int)$p_z_index;
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET z_index = '" . $c_z_index . "' 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			
			$this->set_z_index($c_z_index);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_z_index($p_z_index)
	{
		$this->v_z_index = $p_z_index;
	}
	
	function set_element_max_characters($p_max_characters)
	{
		$c_max_characters = (int)$p_max_characters;
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET max_characters = '" . $c_max_characters . "' 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			
			$this->set_max_characters($c_max_characters);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_max_characters($p_max_characters)
	{
		$this->v_max_characters = $p_max_characters;
	}
	
	function set_element_show_name($p_show_name)
	{
		$c_show_name = (int)$p_show_name;
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET show_name = '" . $c_show_name . "' 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			
			$this->set_show_name($c_show_name);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_show_name($p_show_name)
	{
		if($p_show_name === '1' || $p_show_name === 'true' || $p_show_name === 1 || $p_show_name === true)
		{
			$this->v_show_name = true;
		}
		else
		{
			$this->v_show_name = false;
		}	
	}
	
	function set_element_allowed_extensions($p_allowed_extensions)
	{
		$c_allowed_extensions = gm_string_filter($p_allowed_extensions, 'a-z0-9,');
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET allowed_extensions = '" . $c_allowed_extensions . "' 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			
			$this->set_allowed_extensions($c_allowed_extensions);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_allowed_extensions($p_allowed_extensions)
	{
		$this->v_allowed_extensions = $p_allowed_extensions;
	}
	
	function set_element_minimum_filesize($p_minimum_filesize)
	{
		$c_minimum_filesize = (double)str_replace(',', '.', $p_minimum_filesize);
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET minimum_filesize = '" . $c_minimum_filesize . "' 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			
			$this->set_minimum_filesize($c_minimum_filesize);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_minimum_filesize($p_minimum_filesize)
	{
		$this->v_minimum_filesize = (double)$p_minimum_filesize;
	}
	
	function set_element_maximum_filesize($p_maximum_filesize)
	{
		$c_maximum_filesize = (double)str_replace(',', '.', $p_maximum_filesize);
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . "
												SET maximum_filesize = '" . $c_maximum_filesize . "' 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			
			$this->set_maximum_filesize($c_maximum_filesize);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_maximum_filesize($p_maximum_filesize)
	{
		$this->v_maximum_filesize = (double)$p_maximum_filesize;
	}
	
	/*
	function set_element_name($p_elements_id, $p_name)
	{
		// Variablen sauebern??? addslashes??
		
		$t_success = false;
		
		$t_get_groups_id = xtc_db_query("SELECT gm_gprint_elements_groups_id 
												FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
												WHERE gm_gprint_elements_id = '" . $p_elements_id. "'");
		
		if(xtc_db_num_rows($t_get_groups_id) == 1)
		{
			$t_groups_id = xtc_db_fetch_array($t_get_groups_id);
			
			if($t_groups_id['gm_gprint_elements_groups_id'] > 0)
			{
				$t_update_name = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS_GROUPS . " 
												SET group_name = '" . $p_name . "'
												WHERE gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'");
				$t_success = 'true';
				
				$this->set_name($p_name);
			}
		}

		return $t_success;
	}
	*/
	
	function set_element_values($p_type, $p_names, $p_values)
	{
		$c_type = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_type)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		
		$t_groups_id = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = $this->get_elements_id();
			
			$t_get_groups_id = xtc_db_query("SELECT gm_gprint_elements_groups_id 
												FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
												WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
			if(xtc_db_num_rows($t_get_groups_id) == 1)
			{
				$t_groups_id = xtc_db_fetch_array($t_get_groups_id);
				
				if($t_groups_id['gm_gprint_elements_groups_id'] > 0)
				{
					$t_delete_group_trash = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS_GROUPS . " 
															WHERE gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'");
					$t_delete_values_trash = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . " 
															WHERE gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'");
				}
				
				$t_set_type = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ELEMENTS_GROUPS . " 
																SET group_type = '" . $c_type . "'");
				$t_groups_id = xtc_db_insert_id();
				
				$t_update_groups_id = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS . " 
																SET gm_gprint_elements_groups_id = '" . $t_groups_id . "'
																WHERE gm_gprint_elements_id = '" . $c_elements_id . "'");
				
				$this->set_type($p_type);
				
				foreach($p_values AS $t_language_id => $t_values)
				{
					$c_languages_id = (int)$t_language_id;
					
					$t_delete_values = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . "
														WHERE
															gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'
															AND languages_id = '" . $c_languages_id . "'");
						
					foreach($t_values AS $t_key => $t_value)
					{
						$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_names[$c_languages_id])) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
						$c_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($t_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
						
						$t_set_values = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ELEMENTS_VALUES . " 
																	SET languages_id = '" . $c_languages_id . "',
																		gm_gprint_elements_groups_id = '" . $t_groups_id . "',
																		name = '" . $c_name . "',
																		elements_value = '" . $c_value . "'");
					}
				}				
				$this->set_names($p_names);
				$this->set_values($p_values);				
			}
		}
		
		return $t_groups_id;
	}
	
	function update_names($p_elements_id, $p_names)
	{
		$c_elements_id = (int)$p_elements_id;
		
		$t_success = false;
		
		if(!empty($c_elements_id))
		{
			$t_get_groups_id = xtc_db_query("SELECT gm_gprint_elements_groups_id 
												FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
												WHERE gm_gprint_elements_id = '" . $c_elements_id. "'");
		
			if(xtc_db_num_rows($t_get_groups_id) == 1)
			{
				$t_groups_id = xtc_db_fetch_array($t_get_groups_id);
				
				if($t_groups_id['gm_gprint_elements_groups_id'] > 0)
				{
					foreach($p_names AS $t_language_id => $t_name)
					{
						$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($t_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
						$c_languages_id = (int)$t_language_id;
						$t_update_name = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ELEMENTS_VALUES . " 
														SET name = '" . $c_name . "'
														WHERE 
															gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'
															AND languages_id = '" . $c_languages_id . "'");
					}
					
					$t_success = 'true';
					
					$this->set_names($p_names);
				}			
			}
		}
		
		return $t_success;
	}
	
	
	function update_values($p_elements_id, $p_values)
	{
		$t_success = false;
		
		if(!empty($p_elements_id))
		{
			$t_get_groups_id = xtc_db_query("SELECT gm_gprint_elements_groups_id 
												FROM " . TABLE_GM_GPRINT_ELEMENTS . " 
												WHERE gm_gprint_elements_id = '" . $p_elements_id. "'");
		
			if(xtc_db_num_rows($t_get_groups_id) == 1)
			{
				$t_groups_id = xtc_db_fetch_array($t_get_groups_id);
				
				if($t_groups_id['gm_gprint_elements_groups_id'] > 0)
				{
					foreach($p_values AS $t_language_id => $t_elements_values)
					{
						$c_languages_id = $t_language_id;
						
						$t_get_name = xtc_db_query("SELECT name 
													FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . "
													WHERE
														gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'
														AND languages_id = '" . $c_languages_id . "'
													GROUP BY languages_id");
						if(xtc_db_num_rows($t_get_name) == 1)
						{
							$t_name = xtc_db_fetch_array($t_get_name);
							$t_name = $t_name['name'];
						}
						else
						{
							$t_name = '';
						}
						
						$t_delete_values = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ELEMENTS_VALUES . "
															WHERE
																gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "'
																AND languages_id = '" . $c_languages_id . "'");
						foreach($t_elements_values AS $t_key => $t_elements_value)
						{
							$c_elements_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($t_elements_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
							$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
							
							$t_insert_value = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ELEMENTS_VALUES . " 
															SET 
																name = '" . $c_name . "',
																elements_value = '" . $c_elements_value . "',
																gm_gprint_elements_groups_id = '" . $t_groups_id['gm_gprint_elements_groups_id'] . "',
																languages_id = '" . $c_languages_id . "'");
					
						}
					}
					
					$t_success = 'true';
					
					$this->set_values($p_values);
				}			
			}
		}
		
		return $t_success;
	}
	
	function get_elements_id()
	{
		return $this->v_elements_id;
	}
	
	function set_type($p_type)
	{
		$this->v_type = $p_type;
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
	
	function set_values($p_values)
	{
		$t_success = false;
		
		foreach($p_values AS $t_language_id => $t_value)
		{
			$this->v_values[$t_language_id] = $t_value;
			$t_success = true;
		}
		
		return $t_success;
	}
	
	function get_image_data($p_filename)
	{
		$t_image_size = array();		
		$t_image_data = array();
		$c_filename = basename($p_filename);
		
		$t_image_size = getimagesize(DIR_FS_CATALOG . DIR_WS_IMAGES . 'gm/gprint/' . $c_filename);
		$t_image_data['WIDTH'] = $t_image_size[0];
		$t_image_data['HEIGHT'] = $t_image_size[1];
		$t_image_data['FILENAME'] = $c_filename;
		
		return $t_image_data;
	}
	
	function get_width()
	{
		return $this->v_width;
	}
	
	function get_height()
	{
		return $this->v_height;
	}
	
	function get_position_x()
	{
		return $this->v_position_x;
	}

	function get_position_y()
	{
		return $this->v_position_y;
	}

	function get_z_index()
	{
		return $this->v_z_index;
	}
	
	function get_max_characters()
	{
		return $this->v_max_characters;
	}
	
	function get_show_name()
	{
		return $this->v_show_name;
	}

	function get_allowed_extensions()
	{
		return $this->v_allowed_extensions;
	}
	
	function get_minimum_filesize()
	{
		return $this->v_minimum_filesize;
	}
	
	function get_maximum_filesize()
	{
		return $this->v_maximum_filesize;
	}
	
	function get_type()
	{
		return $this->v_type;
	}

	function get_names()
	{
		return $this->v_names;
	}
	
	function get_name($p_languages_id)
	{
		return $this->v_names[$p_languages_id];
	}

	function get_values()
	{
		return $this->v_values;
	}
	
	function get_value($p_languages_id)
	{
		return $this->v_values[$p_languages_id][0];
	}
	
	function set_selected_dropdown_value($p_value)
	{
		$this->v_selected_dropdown_value = $p_value;
	}
	
	function get_selected_dropdown_value()
	{
		return $this->v_selected_dropdown_value;
	}
	
	function set_download_key($p_download_key)
	{
		$this->v_download_key = $p_download_key;
	}
	
	function get_download_key()
	{
		return $this->v_download_key;
	}
}
MainFactory::load_origin_class('GMGPrintElements');