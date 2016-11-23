<?php
/* --------------------------------------------------------------
   GoogleTaxonomyAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleTaxonomyAjaxHandler extends AjaxHandler
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
			case 'get_product_google_category_array':
				$c_product_id = (int)$this->v_data_array['GET']['product_id'];
				$t_output_array = $this->get_product_google_category_array($c_product_id);
				break;
			case 'save_category_google_categories':
				$c_category_id = $this->v_data_array['POST']['categories_id'];
				$c_add_array = $this->v_data_array['POST']['category_list'];
				if($this->v_data_array['POST']['google_recursive_mode'] == 'on') $c_rekursion = true; else $c_rekursion = false;
				if($this->v_data_array['POST']['google_overwrite_categories'] == 'on') $c_overwrite = true; else $c_overwrite = false;

				$t_output_array = $this->save_category_google_categories($c_category_id, $c_add_array, $c_rekursion, $c_overwrite);
				break;
			case 'get_google_categories_array':
				$c_parent = $this->v_data_array['GET']['parent'];
				$t_output_array = $this->get_google_categories_array($c_parent);
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

	function get_product_google_category_array($p_product_id)
	{
		$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
		$coo_taxonomy_control->create_product_google_category();

		$param_array = array('products_id' => $p_product_id);
		$t_result_array = $coo_taxonomy_control->get_product_google_category_array($param_array);

		return $t_result_array;
	}

	function get_google_categories_array($p_parent)
	{
		$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
		$coo_taxonomy_control->create_product_google_category();

		$t_result_array = $coo_taxonomy_control->get_google_categories_array($p_parent);

		return $t_result_array;
	}

	function save_category_google_categories($p_category_id, $p_categories_array, $p_rekursion, $p_overwrite)
	{
		$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
		$coo_taxonomy_control->create_product_google_category();

		// get product IDs of selected category
		$t_product_ids = $coo_taxonomy_control->get_product_ids_by_category_id($p_category_id, $p_rekursion);

		if($p_overwrite == 'on') {
			foreach($t_product_ids as $t_product_id) {
				$t_result_array = $this->get_product_google_category_array($t_product_id);
				foreach($t_result_array as $t_products_google_categories_id) {
					$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
					$coo_taxonomy_control->create_product_google_category();

					$coo_taxonomy_control->coo_product_google_category->set_products_google_categories_id($t_products_google_categories_id->v_products_google_categories_id);
					$coo_taxonomy_control->coo_product_google_category->delete();
				}
			}
		}

		foreach($p_categories_array as $t_value) {
			// save the google category to the product
			foreach($t_product_ids as $t_product_id) {
				$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyControl');
				$coo_taxonomy_control->create_product_google_category();

				$coo_taxonomy_control->coo_product_google_category->set_products_id($t_product_id);
				$coo_taxonomy_control->coo_product_google_category->set_google_category($t_value);
				$coo_taxonomy_control->coo_product_google_category->save();
			}
		}

		return true;
	}

}
?>