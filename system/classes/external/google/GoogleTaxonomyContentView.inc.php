<?php
/* --------------------------------------------------------------
   GoogleTaxonomyContentView.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleTaxonomyContentView extends ContentView
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

		$t_action_request = $this->v_data_array['GET']['action'];

		switch($t_action_request)
		{
			case 'get_template':
				$c_template = $this->v_data_array['GET']['template'];
				$t_html_template = $this->get_template($c_template);

				$t_enable_json_output = false;
				$this->v_output_buffer = $t_html_template;
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

	function get_template($p_template_name = 'image_mapper_standard.html')
	{
		$coo_view = MainFactory::create_object('ContentView');
		$coo_view->set_template_dir(DIR_FS_CATALOG.'admin/html/content/');
		$coo_view->set_content_template($p_template_name);

		$t_html = $coo_view->get_html();
		return $t_html;
	}

}
?>