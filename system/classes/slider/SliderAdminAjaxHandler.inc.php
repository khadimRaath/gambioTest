<?php
/* --------------------------------------------------------------
   SliderAdminAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class SliderAdminAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['POST']['action'];

		switch($t_action_request)
		{
			case 'get_template':
				$c_template = $this->v_data_array['POST']['template'];
				$t_html_template = $this->get_template($c_template);

				$t_enable_json_output = false;
				$this->v_output_buffer = $t_html_template;
				break;

			case 'get_language_text':
				$t_language_text_array = $this->get_language_text();
				$t_output_array = $t_language_text_array;
				break;

			case 'create_area':
				$c_slider_image_id = (int)$this->v_data_array['POST']['slider_image_id'];

				$t_slider_image_area_id = $this->create_area($c_slider_image_id);
				$t_output_array['slider_image_area_id'] = $t_slider_image_area_id;
				break;
			
			case 'save_area':
				$f_slider_image_area_id = $this->v_data_array['POST']['slider_image_area_id'];
				$f_shape = $this->v_data_array['POST']['shape'];
				$f_coords = $this->v_data_array['POST']['coords'];
				$f_title = $this->v_data_array['POST']['title'];
				$f_link_url = $this->v_data_array['POST']['link_url'];
				$f_link_target = $this->v_data_array['POST']['link_target'];
				$f_flyover_content = $this->v_data_array['POST']['flyover_content'];
				$this->save_area($f_slider_image_area_id, $f_shape, $f_coords, $f_title, $f_link_url, $f_link_target, $f_flyover_content);
				break;

			case 'delete_area':
				$f_slider_image_area_id = $this->v_data_array['POST']['slider_image_area_id'];
				$this->delete_area($f_slider_image_area_id);
				break;

			case 'get_image_area_data':
				$f_image_id = $this->v_data_array['POST']['slider_image_id'];
				$t_output_array = $this->get_image_area_data($f_image_id);
				break;
				
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);
			
			$this->v_output_buffer = $t_output_json;
		}
		return true;
	}

	function get_template($p_template_name='image_mapper_standard.html')
	{
		$coo_view = MainFactory::create_object('ContentView');
		$coo_view->set_template_dir(DIR_FS_CATALOG.'admin/html/content/');
    $coo_view->set_content_template($p_template_name);
    
		$t_html = $coo_view->get_html();
		return $t_html;
	}

	function get_language_text()
	{
		$t_section = 'imagemap_editor';
		$t_language_id = $_SESSION['languages_id'];

		$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array($t_section, $t_language_id), false);
		$t_section_array = $coo_text_mgr->get_section_array();

		return $t_section_array;
	}
	
	function create_area($p_slider_image_id)
	{
		if(!check_data_type($p_slider_image_id, 'int')) die();
		$c_slider_image_id = (int)$p_slider_image_id;

		$coo_slider_control = MainFactory::create_object('SliderControl');
		$coo_slider_image_area = $coo_slider_control->create_slider_image_area($c_slider_image_id);
		$t_slider_image_area_id = $coo_slider_image_area->save();

		return $t_slider_image_area_id;
	}

	function save_area($p_slider_image_area_id, $p_shape, $p_coords, $p_title, $p_link_url, $p_link_target, $p_flyover_content)
	{
		if(!check_data_type($p_slider_image_area_id, 'int')) die();
		$c_slider_image_area_id = (int)$p_slider_image_area_id;
		
		if(!check_data_type($p_shape, 'string')) die();
		$c_shape = $p_shape;

		if(!check_data_type($p_coords, 'string')) die();
		$c_coords = $p_coords;
		
		if(!check_data_type($p_title, 'string')) die();
		$c_title = stripslashes( $p_title );
		
		if(!check_data_type($p_link_url, 'string')) die();
		$c_link_url = $p_link_url;

		if(!check_data_type($p_link_target, 'string')) die();
		$c_link_target = $p_link_target;
    
    if(!check_data_type($p_flyover_content, 'string')) die();
		$c_flyover_content = stripslashes( $p_flyover_content );
		
		$coo_slider_control = MainFactory::create_object('SliderControl');

		# load existing image_area
		$coo_slider_image_area_array = $coo_slider_control->get_slider_image_area_array(array('slider_image_area_id' => $c_slider_image_area_id));

		# set given data for saving
		$coo_slider_image_area_array[0]->set_shape($c_shape);
		$coo_slider_image_area_array[0]->set_coords($c_coords);
		$coo_slider_image_area_array[0]->set_title($c_title);
		$coo_slider_image_area_array[0]->set_link_url($c_link_url);
		$coo_slider_image_area_array[0]->set_link_target($c_link_target);
    $coo_slider_image_area_array[0]->set_flyover_content($c_flyover_content);

		$coo_slider_image_area_array[0]->save();
		
		return true;
	}

	function delete_area($p_slider_image_area_id)
	{
		if(!check_data_type($p_slider_image_area_id, 'int')) die();
		$c_slider_image_area_id = (int)$p_slider_image_area_id;
		
		$coo_slider_control = MainFactory::create_object('SliderControl');

		# load existing image_area
		$coo_slider_image_area = $coo_slider_control->get_slider_image_area_array(array('slider_image_area_id' => $c_slider_image_area_id));

		# do delete
		$coo_slider_image_area[0]->delete();

		return true;
	}

	function get_image_area_data($p_slider_image_id)
	{
		$t_output_array = array();

		if(!check_data_type($p_slider_image_id, 'int')) die();
		$c_slider_image_id = (int)$p_slider_image_id;
		
		$coo_slider_control = MainFactory::create_object('SliderControl');

		# get associated image_areas
		$coo_slider_image_area_array = $coo_slider_control->get_slider_image_area_array(array('slider_image_id' => $c_slider_image_id));

		for($i = 0; $i < count($coo_slider_image_area_array); $i++)
		{
			$t_item_array = array(
									'slider_image_area_id' => $coo_slider_image_area_array[$i]->get_slider_image_area_id(),
									'slider_image_id' => $coo_slider_image_area_array[$i]->get_slider_image_id(),
									'shape' => $coo_slider_image_area_array[$i]->get_shape(),
									'coords' => $coo_slider_image_area_array[$i]->get_coords(),
									'title' => $coo_slider_image_area_array[$i]->get_title(),
									'link_url' => $coo_slider_image_area_array[$i]->get_link_url(),
									'link_target' => $coo_slider_image_area_array[$i]->get_link_target(),
									'flyover_content' => $coo_slider_image_area_array[$i]->get_flyover_content()
								);
			$t_output_array[] = $t_item_array;
		}

		return $t_output_array;
	}
}