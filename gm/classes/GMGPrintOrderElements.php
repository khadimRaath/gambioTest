<?php
/* --------------------------------------------------------------
   GMGPrintOrderElements.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintOrderElements_ORIGIN
{
	var $v_width = 0;
	var $v_height = 0;
	var $v_position_x = 0;
	var $v_position_y = 0;
	var $v_z_index = 0;
	var $v_show_name = false;
	var $v_type = '';
	var $v_name = '';
	var $v_value = '';
	var $v_uploads_id = 0;
	var $v_download_key = 0;
	
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
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET width = '" . $c_width . "',
													height = '" . $c_height . "'
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
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
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET position_x = '" . $c_position_x . "',
													position_y = '" . $c_position_y . "'
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
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
		$c_elements_id = (int)$this->get_elements_id();
		
		$t_success = false;
		
		if($c_elements_id > 0)
		{
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET z_index = '" . $c_z_index. "' 
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
			
			$this->set_z_index($c_z_index);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_element_show_name($p_show_name)
	{
		$c_show_name = (int)$p_show_name;
		$c_elements_id = (int)$this->get_elements_id();
		
		$t_success = false;
		
		if($c_elements_id > 0)
		{
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET show_name = '" . $c_show_name. "' 
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
			
			$this->set_show_name($c_show_name);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_element_type($p_type)
	{
		$c_type = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_type)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET group_type = '" . $c_type. "' 
												WHERE gm_gprint_orders_elements_id = '" . $this->get_elements_id() . "'");
			
			$this->set_type($c_type);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_z_index($p_z_index)
	{
		$this->v_z_index = $p_z_index;
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
		
	function set_element_name($p_name)
	{
		$c_name = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_name)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET name = '" . $c_name. "' 
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
			
			$this->set_name($c_name);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_element_value($p_value)
	{
		$c_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET elements_value = '" . $c_value. "' 
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
			
			$this->set_value($c_value);
			
			$t_success = 'true';
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
	
	function set_name($p_name)
	{
		$this->v_name = $p_name;
	}
	
	function set_value($p_value)
	{
		$this->v_value = $p_value;
	}
	
	function set_elements_uploads_id($p_uploads_id)
	{
		$c_uploads_id = (int)$p_uploads_id;
		
		$t_success = false;
		
		if($this->get_elements_id() > 0)
		{
			$c_elements_id = (int)$this->get_elements_id();
			
			$t_update_element = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
												SET gm_gprint_uploads_id = '" . $c_uploads_id. "' 
												WHERE gm_gprint_orders_elements_id = '" . $c_elements_id . "'");
			
			$this->set_uploads_id($c_uploads_id);
			
			$t_success = 'true';
		}
	
		return $t_success;
	}
	
	function set_uploads_id($p_uploads_id)
	{
		$this->v_uploads_id = $p_uploads_id;
	}
	
	function get_uploads_id()
	{
		return $this->v_uploads_id;
	}
	
	function set_download_key($p_download_key)
	{
		$this->v_download_key = $p_download_key;
	}
	
	function get_download_key()
	{
		return $this->v_download_key;
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
	
	function get_show_name()
	{
		return $this->v_show_name;
	}

	function get_type()
	{
		return $this->v_type;
	}

	function get_name()
	{
		return $this->v_name;
	}

	function get_value()
	{
		return $this->v_value;
	}
}
MainFactory::load_origin_class('GMGPrintOrderElements');