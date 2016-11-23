<?php
/* --------------------------------------------------------------
   FeatureSetAdminAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 *	@author Daniel Wu 
 */
class FeatureSetAdminAjaxHandler extends AjaxHandler
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
		$coo_text_manager = MainFactory::create_object('LanguageTextManager', array('feature_set', $_SESSION['languages_id']));
		
		$t_output_array = array();
		$t_enable_json_output = true;

		$t_action_request = $this->v_data_array['GET']['action'];
		$coo_feature_set_control = MainFactory::create_object('FeatureSetControl');
		$coo_feature_set_view = MainFactory::create_object('FeatureSetAdminContentView');

		switch($t_action_request)
		{
			case 'load':
				$t_enable_json_output = false;
                $this->v_output_buffer = $coo_feature_set_view->get_html($this->v_data_array['GET']);
				break;
			case 'save':
				$t_feature_set_id = $coo_feature_set_control->save_feature_set($this->v_data_array['POST']['feature_set_id'], $this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['feature_value']);
				if($t_feature_set_id == -1)
				{
					$t_output_array['status'] = 'set_already_exists';
					$t_output_array['message'] = $coo_text_manager->get_text('TEXT_FEATURE_SET_REFERENCE_EXISTS');
				}
				else if($t_feature_set_id > 0)
				{
					$t_output_array['status'] = 'success';
					$t_output_array['html'] = $coo_feature_set_view->get_html(array('action' => 'get_set', 'feature_set_id' => $t_feature_set_id, 'products_id' => $this->v_data_array['POST']['products_id'], 'categories_path' => $this->v_data_array['POST']['categories_path']));
				}
				else
				{
					$t_output_array['status'] = 'error';
				}
				break;
			case 'copy_by_product':
				$t_output_array = $coo_feature_set_control->copy_feature_sets_by_products_id($this->v_data_array['POST']['source_products_id'], $this->v_data_array['POST']['target_products_id']);
				break;
			case 'delete_by_product':
				$coo_feature_set_control->delete_feature_sets_by_products_id($this->v_data_array['POST']['products_id']);
				$this->v_output_buffer = $coo_feature_set_view->get_html($this->v_data_array['GET']);
				break;
			case 'delete':
				$coo_feature_set_control->delete_feature_sets((int)$this->v_data_array['POST']['feature_set_id'], $this->v_data_array['POST']['products_id']);
				$t_output_array['action'] = 'delete_set';
				$t_output_array['status'] = 'success';
				//$this->v_output_buffer = $coo_feature_set_view->get_html($this->v_data_array['GET']);
				break;
			case 'get_feature_box':
				$t_enable_json_output = false;
				$this->v_output_buffer = $coo_feature_set_view->get_html(array('action' => 'get_feature_box', 'feature_id' => $this->v_data_array['POST']['feature_id']));
				//$this->v_data_array['POST']['features_array'] = $coo_feature_set_control->get_values_by_features(array($this->v_data_array['POST']['feature_id']));
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

}
?>