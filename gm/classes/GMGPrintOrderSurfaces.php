<?php
/* --------------------------------------------------------------
   GMGPrintOrderSurfaces.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintOrderSurfaces_ORIGIN
{
	var $v_elements = array();
	var $v_width = 0;
	var $v_height = 0;
	var $v_name = '';
	var $v_current_elements_id = 0;
	
	var $v_surfaces_id;
	
	function __construct($p_surfaces_id)
	{
		$this->v_surfaces_id = (int)$p_surfaces_id;
	}
	
	function create_element($p_type, $p_name, $p_value, $p_width, $p_height, $p_position_x, $p_position_y, $p_z_index, $p_show_name)
	{
		$t_elements_id = 0;
		
		if($this->v_surfaces_id > 0)
		{
			$c_surfaces_id = (int)$this->get_surfaces_id();
			
			$t_create_element = xtc_db_query("INSERT INTO " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . " 
												SET gm_gprint_orders_surfaces_id = '" . $c_surfaces_id . "'");
			$t_elements_id = xtc_db_insert_id();
			$coo_element = new GMGPrintOrderElements($t_elements_id);
			$coo_element->set_size($p_width, $p_height);
			$coo_element->set_position($p_position_x, $p_position_y);
			$coo_element->set_element_z_index($p_z_index);
			$coo_element->set_element_show_name($p_show_name);
			$coo_element->set_element_type($p_type);
			$coo_element->set_element_name($p_name);
			$coo_element->set_element_value($p_value);
		
			$this->v_elements[$t_elements_id] =& $coo_element;
		
			$this->set_current_elements_id($t_elements_id);
		}
		
		return $t_elements_id;
	}
	
	function load_elements($p_surfaces_id)
	{
		$c_surfaces_id = (int)$p_surfaces_id;
		
		$t_get_elements = xtc_db_query("SELECT
											e.gm_gprint_orders_elements_id,
											e.position_x,
											e.position_y,
											e.height,
											e.width,
											e.z_index,
											e.show_name,
											e.group_type,
											e.name,
											e.elements_value,
											e.gm_gprint_uploads_id,
											u.download_key
										FROM " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . " e
										LEFT JOIN " . TABLE_GM_GPRINT_UPLOADS . " AS u USING (gm_gprint_uploads_id)
										WHERE e.gm_gprint_orders_surfaces_id = '" . $c_surfaces_id . "'");
		while($t_elements = xtc_db_fetch_array($t_get_elements))
		{
			$coo_element = new GMGPrintOrderElements($t_elements['gm_gprint_orders_elements_id']);
			$coo_element->set_width($t_elements['width']);
			$coo_element->set_height($t_elements['height']);
			$coo_element->set_position_x($t_elements['position_x']);
			$coo_element->set_position_y($t_elements['position_y']);
			$coo_element->set_z_index($t_elements['z_index']);
			$coo_element->set_show_name($t_elements['show_name']);
			$coo_element->set_type($t_elements['group_type']);
			$coo_element->set_name($t_elements['name']);
			$coo_element->set_value($t_elements['elements_value']);
			$coo_element->set_uploads_id($t_elements['gm_gprint_uploads_id']);
			if(!empty($t_elements['download_key']))
			{
				$coo_element->set_download_key($t_elements['download_key']);
			}
			else
			{
				$coo_element->set_download_key('');
			}
			
			if($t_elements['group_type'] == 'image')
			{
				$t_image_data = array();
				$t_image_data = $coo_element->get_image_data($t_elements['elements_value']);
				$coo_element->set_width($t_image_data['WIDTH']);
				$coo_element->set_height($t_image_data['HEIGHT']);
			}
		
			$this->v_elements[$t_elements['gm_gprint_orders_elements_id']] = $coo_element;
		
			$this->set_current_elements_id($t_elements['gm_gprint_orders_elements_id']);
		}
	}
	
	function delete_element($p_elements_id)
	{
		$c_elements_id = (int)$p_elements_id;
		$t_success = false;
		
		$c_gm_gprint_uploads_id = (int)$this->v_elements[$c_elements_id]->get_uploads_id();
		
		$t_get_file_data = xtc_db_query("SELECT encrypted_filename
											FROM " . TABLE_GM_GPRINT_UPLOADS . "
											WHERE
												gm_gprint_uploads_id = '" . $c_gm_gprint_uploads_id . "'");	
		if(xtc_db_num_rows($t_get_file_data) == 1)
		{
			$t_file_data = xtc_db_fetch_array($t_get_file_data);
			@unlink(DIR_FS_CATALOG . 'gm/customers_uploads/gprint/' . basename($t_file_data['encrypted_filename']));
			
			$t_delete_file = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_UPLOADS . " WHERE gm_gprint_uploads_id = '" . $c_gm_gprint_uploads_id . "'");
		}
		
		$t_delete_elements_values = xtc_db_query("DELETE FROM " . TABLE_GM_GPRINT_ORDERS_ELEMENTS . "
																WHERE gm_gprint_orders_elements_id = '" . $c_elements_id. "'");
					
		unset($this->v_elements[$c_elements_id]);
		
		$t_success = 'true';
		
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
		
		$t_success = false;
		
		if($p_surfaces_id > 0 && $c_width >= 0 && $c_height >= 0)
		{
			$c_surfaces_id = (int)$p_surfaces_id;
			
			$t_update_surface = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_ORDERS_SURFACES . "
												SET width = '" . $c_width . "',
													height = '" . $c_height . "'
												WHERE gm_gprint_orders_surfaces_id = '" . $c_surfaces_id . "'");
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
	
	function set_name($p_name)
	{
		$this->v_name = $p_name;
	}
	
	function update_name($p_surfaces_id, $p_name)
	{
		$c_name = gm_prepare_atring($p_name);
		$c_surfaces_id = (int)$p_surfaces_id;
		
		$t_update_name = xtc_db_query("UPDATE " . TABLE_GM_GPRINT_SURFACES_DESCRIPTION . " 
										SET name = '" . $c_name . "'
										WHERE gm_gprint_orders_surfaces_id = '" . $c_surfaces_id . "'");
		
		$this->set_name($c_name);
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
	
	function get_surfaces_id()
	{
		return $this->v_surfaces_id;
	}
}
MainFactory::load_origin_class('GMGPrintOrderSurfaces');