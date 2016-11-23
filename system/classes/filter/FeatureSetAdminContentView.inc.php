<?php
/* --------------------------------------------------------------
   FeatureSetAdminContentView.inc.php 2015-08-31 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeatureSetAdminContentView extends LightboxContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/filter/');
	}
	
	public function get_html_array( $p_data_array = array(), $p_dummy_data_array = array() )
	{
		if (!is_array($p_data_array))
		{
			trigger_error('p_data_array is not an array: '. htmlentities($p_data_array));
		}
		switch($p_data_array['action'])
		{
			case "get_set":
				$t_html_output['html'] = $this->get_feature_set($p_data_array);
				break;
			case "edit":
				$t_html_output['html'] = $this->get_feature_set_edit($p_data_array);
				break;
			case "delete":
				$t_html_output['html'] = $this->get_feature_set_delete($p_data_array);
				break;
			case "get_feature_boxes":
				$t_html_output['html'] = $this->get_feature_boxes($p_data_array);
				break;
			case "get_feature_box":
				$t_html_output['html'] = $this->get_feature_box($p_data_array);
				break;
			default:
				$t_html_output['html'] = $this->get_feature_set_main($p_data_array);
				break;
		}
		
		return $t_html_output;
	}
	
	protected function get_feature_set_main( $p_data_array )
	{
		$this->set_content_template('feature_set_container.html');
		$coo_feature_set_control = MainFactory::create_object('FeatureSetControl');
		$t_feature_sets = $coo_feature_set_control->get_all_sets($p_data_array["products_id"]);
		
		$t_feature_sets_1 = array_slice($t_feature_sets, 0, 2, true);
		$t_feature_sets_2_length = count($t_feature_sets) - 2;
		$t_feature_sets_2 = array_slice($t_feature_sets, 2, $t_feature_sets_2_length, true);
		$t_feature_sets_2_length = count($t_feature_sets_2);
		
		$coo_feature_set_content_view = MainFactory::create_object('FeatureSetAdminContentView');
		$t_feature_boxes_content = $coo_feature_set_content_view->get_html(array('action' => 'get_feature_boxes',
																					'features_array' => $t_feature_sets_1,
																					'individual_features' => array(),
																					'categories_path' => $p_data_array["categories_path"],
																					'products_id' => $p_data_array["products_id"],
																					'show_set_container' => true,
																					'edit_set_button' => true,
																					'delete_set_button' => true,
																					'delete_box_button' => false));
		
		$t_feature_boxes_content_2 = $coo_feature_set_content_view->get_html(array('action' => 'get_feature_boxes',
																					'features_array' => $t_feature_sets_2,
																					'individual_features' => array(),
																					'categories_path' => $p_data_array["categories_path"],
																					'products_id' => $p_data_array["products_id"],
																					'show_set_container' => true,
																					'edit_set_button' => true,
																					'delete_set_button' => true,
																					'delete_box_button' => false));

		$this->set_content_data('feature_sets', $t_feature_boxes_content);
		$this->set_content_data('feature_sets_2', $t_feature_boxes_content_2);
		$this->set_content_data('feature_sets_2_length', $t_feature_sets_2_length);
		$this->set_content_data('show_all_button', true);
		$this->set_content_data('add_set_button', true);
		$this->set_content_data('categories_path', $p_data_array["categories_path"]);
		$this->set_content_data('products_id', $p_data_array["products_id"]);
		
		$t_html_output = $this->build_html();
		return $t_html_output;
	}
	
	protected function get_feature_set( $p_data_array )
	{
		$this->set_content_template('feature_set_container.html');
		$coo_feature_set_control = MainFactory::create_object('FeatureSetControl');
		$t_feature_sets = array($p_data_array["feature_set_id"] => $coo_feature_set_control->get_feature_set($p_data_array["feature_set_id"]));

		$coo_feature_set_content_view = MainFactory::create_object('FeatureSetAdminContentView');
		$t_feature_boxes_content = $coo_feature_set_content_view->get_html(array('action' => 'get_feature_boxes',
																					'features_array' => $t_feature_sets,
																					'individual_features' => array(),
																					'categories_path' => $p_data_array["categories_path"],
																					'products_id' => $p_data_array["products_id"],
																					'show_set_container' => true,
																					'edit_set_button' => true,
																					'delete_set_button' => true,
																					'delete_box_button' => false));

		return $t_feature_boxes_content;
	}
	
	protected function get_feature_set_edit( $p_data_array )
	{
		$c_feature_set_id = (int)$p_data_array["feature_set_id"];
		
		$this->set_content_template('feature_set_container.html');
		$coo_feature_set_control = MainFactory::create_object('FeatureSetControl');
		
		$t_feature_categories_features = $coo_feature_set_control->get_categories_features($p_data_array["categories_path"]);
		$t_feature_selected_features = $coo_feature_set_control->get_selected_features($c_feature_set_id);

		$t_individual_features = array_diff_key($t_feature_selected_features, $t_feature_categories_features);
		
		$t_feature_sets = $t_feature_categories_features + $t_feature_selected_features;
		
		if(!empty($t_feature_sets))
		{
			ksort($t_feature_sets, SORT_NUMERIC);
			$t_feature_sets = $coo_feature_set_control->get_values_by_features($t_feature_sets);
		}

		$t_unselected_features = $coo_feature_set_control->get_unselected_features(array_keys($t_feature_sets));
		
		$t_set_values = array();
		if(!empty($c_feature_set_id))
		{
			$t_set_values = $coo_feature_set_control->get_feature_set_values($c_feature_set_id);
		}
		
		$coo_feature_set_content_view = MainFactory::create_object('FeatureSetAdminContentView');
		$t_feature_boxes_content = $coo_feature_set_content_view->get_html(array('action' => 'get_feature_boxes',
																					'features_array' => array($t_feature_sets),
																					'unselected_features' => $t_unselected_features,
																					'individual_features' => $t_individual_features,
																					'selected_values' => $t_set_values,
																					'show_set_container' => true,
																					'edit_set_button' => false,
																					'delete_set_button' => false,
																					'delete_box_button' => false));

		$this->set_content_data('feature_sets', $t_feature_boxes_content);
		$this->set_content_data('unselected_features', $t_unselected_features);
		$this->set_content_data('add_feature_select', true);
		$this->set_content_data('editable_values', true);
		
		$this->set_lightbox_button('left', 'cancel', array('lightbox_close', 'cancel'));
		$this->set_lightbox_button('right', 'save', array('save', 'green'));
		$this->set_lightbox_button('right', 'save_close', array('save', 'close', 'green'));
		$this->set_javascript_section('filter_set_edit');
		
		$t_html_output = $this->build_html();
		return $t_html_output;
	}
	
	protected function get_feature_set_delete( $p_data_array )
	{
		$c_feature_set_id = (int)$p_data_array["feature_set_id"];
		
		$this->set_content_template('feature_set_container.html');
		$coo_feature_set_control = MainFactory::create_object('FeatureSetControl');
		
		$t_feature_sets = array($coo_feature_set_control->get_feature_set($c_feature_set_id));
		
		$coo_feature_set_content_view = MainFactory::create_object('FeatureSetAdminContentView');
		$t_feature_boxes_content = $coo_feature_set_content_view->get_html(array('action' => 'get_feature_boxes',
																					'features_array' => $t_feature_sets,
																					'individual_features' => array(),
																					'show_set_container' => true,
																					'edit_set_button' => false,
																					'delete_set_button' => false,
																					'delete_box_button' => false));

		$this->set_content_data('feature_sets', $t_feature_boxes_content);
		$this->set_content_data('lightbox_text', 'delete_info');
		
		$this->set_lightbox_button('left', 'cancel', array('lightbox_close', 'cancel'));
		$this->set_lightbox_button('right', 'delete', array('delete', 'green'));
		$this->set_javascript_section('filter_set_delete');
		
		$t_html_output = $this->build_html();
		return $t_html_output;
	}
	
	protected function get_feature_boxes( $p_data_array )
	{
		$this->set_content_template('feature_set_boxes.html');
		
		$this->set_content_data('feature_sets', $p_data_array["features_array"]);
		$this->set_content_data('selected_values', $p_data_array["selected_values"]);
		$this->set_content_data('individual_features', $p_data_array["individual_features"]);
		$this->set_content_data('unselected_features', $p_data_array["unselected_features"]);
		$this->set_content_data('show_set_container', (boolean)$p_data_array["show_set_container"]);
		$this->set_content_data('categories_path', $p_data_array["categories_path"]);
		$this->set_content_data('products_id', $p_data_array["products_id"]);
		$this->set_content_data('edit_set_button', (boolean)$p_data_array["edit_set_button"]);
		$this->set_content_data('delete_set_button', (boolean)$p_data_array["delete_set_button"]);
		$this->set_content_data('delete_box_button', (boolean)$p_data_array["delete_box_button"]);
		
		$t_html_output = $this->build_html();
		return $t_html_output;
	}
	
	protected function get_feature_box( $p_data_array )
	{
		$coo_feature_set_control = MainFactory::create_object('FeatureSetControl');
		$t_feature_values = $coo_feature_set_control->get_feature_by_feature_id($p_data_array['feature_id']);
		//$t_feature_values = $t_feature_values[$p_data_array['feature_id']]['feature_values'];
		
		$p_data_array['features_array'] = array($t_feature_values);
		$p_data_array['selected_values'] = array();
		$p_data_array['individual_features'] = array();
		$p_data_array['show_set_container'] = false;
		$p_data_array['categories_path'] = 0;
		$p_data_array['products_id'] = 0;
		$p_data_array['edit_set_button'] = false;
		$p_data_array['delete_set_button'] = false;
		$p_data_array['delete_box_button'] = true;
		
		return $this->get_feature_boxes($p_data_array);
	}
	
//	edit_set_button: "false",
//				delete_set_button: "false",
//				delete_box_button: "true",
//				show_all_button: "false",
//				add_set_button: "false"
//	show_set_container: "false",
}
