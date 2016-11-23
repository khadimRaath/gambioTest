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

class SliderAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['POST']['action'];

		switch($t_action_request)
		{
			case 'get_flyover_content':
        $t_enable_json_output = false;
				$f_image_area_id = $this->v_data_array['POST']['slider_image_area_id'];
				$this->v_output_buffer = $this->get_flyover_content($f_image_area_id);
				break;
				
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = MainFactory::create_object('GMJSON', array(false));
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		return true;
	}


	function get_flyover_content($p_slider_image_area_id)
	{
		if(!check_data_type($p_slider_image_area_id, 'int')) die();
		$c_slider_image_area_id = (int)$p_slider_image_area_id;
		
		$coo_slider_control = MainFactory::create_object('SliderControl');

		# get associated image_areas
		$coo_slider_image_area_array = $coo_slider_control->get_slider_image_area_array(array('slider_image_area_id' => $c_slider_image_area_id));

    $t_output_string = $coo_slider_image_area_array[0]->get_flyover_content();
    
		return $t_output_string;
	}

}
?>