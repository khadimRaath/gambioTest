<?php
/* --------------------------------------------------------------
  LightboxPluginAdminAjaxHandler.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class LightboxPluginAdminAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id = null)
	{
		return true;
	}


	function proceed()
	{
		$t_output_array       = array();
		$t_enable_json_output = true;

		$c_action = preg_replace('/[^\w.]/i', '', $this->v_data_array['GET']['action']);

		$c_template = trim(preg_replace('/[^\w.]/i', '', $this->v_data_array['GET']['template']));

		$c_template_section = preg_replace('/[^\w]/i', '', $this->v_data_array['GET']['section']);

		$t_param_array = $this->v_data_array['GET']['param'];
		$c_param       = array();
		foreach($t_param_array as $t_param_key => $t_param_value)
		{
			$c_param[$t_param_key] = addslashes(stripslashes($t_param_value));
		}

		switch($c_action)
		{
			case 'get_template':
				$t_output_array = $this->get_template($c_template, $c_template_section, $c_param);
				break;

			default:
				trigger_error('Lightbox: could not proceed action [' . htmlentities_wrapper($c_action) . ']',
				              E_USER_ERROR);
		}

		if($t_enable_json_output)
		{
			$coo_json      = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}

		return true;
	}


	function get_template_map()
	{
		$t_template_map_array                                           = array();
		$t_template_map_array['properties_edit.html']                   = 'PropertiesAdminContentView';
		$t_template_map_array['properties_delete.html']                 = 'PropertiesAdminContentView';
		$t_template_map_array['properties_values_edit.html']            = 'PropertiesAdminContentView';
		$t_template_map_array['properties_values_delete.html']          = 'PropertiesAdminContentView';
		$t_template_map_array['properties_combis_edit.html']            = 'PropertiesCombisAdminContentView';
		$t_template_map_array['properties_combis_delete.html']          = 'PropertiesCombisAdminContentView';
		$t_template_map_array['properties_combis_delete_selected.html'] = 'PropertiesCombisAdminContentView';
		$t_template_map_array['combis_settings.html']                   = 'PropertiesCombisAdminContentView';
		$t_template_map_array['combis_defaults.html']                   = 'PropertiesCombisAdminContentView';
		$t_template_map_array['feature_set_container.html']             = 'FeatureSetAdminContentView';
		$t_template_map_array['export_scheme_details.html']             = 'CSVContentView';
		$t_template_map_array['db_backup_restore.html']                 = 'DBBackupContentView';
		$t_template_map_array['shipping_and_payment_matrix.html']       = 'ShippingAndPaymentMatrixAdminContentView';
		$t_template_map_array['parcel_service_edit.html']               = 'ParcelServicesEditContentView';
		$t_template_map_array['shop_topbar_edit_layer.html']            = 'ShopOfflineEditLayerContentView';
		$t_template_map_array['shop_offline_edit_layer.html']           = 'ShopOfflineEditLayerContentView';
		$t_template_map_array['shop_popup_edit_layer.html']             = 'ShopOfflineEditLayerContentView';

		return $t_template_map_array;
	}


	function get_template($p_template_name, $p_template_section, $p_param)
	{
		if(empty($p_template_name))
		{
			trigger_error('LightboxPlugin: empty template', E_USER_ERROR);
		}

		$t_template_map_array = $this->get_template_map();
		if(array_key_exists($p_template_name, $t_template_map_array))
		{
			$coo_view = MainFactory::create_object($t_template_map_array[$p_template_name]);
		}
		else
		{
			$coo_view = MainFactory::create_object('LightboxContentView');

			if($_SESSION['customers_status']['customers_status_id'] !== '0')
			{
				trigger_error('Lightbox: access denied to admin section', E_USER_ERROR);
			}
			else
			{
				$p_template_section = trim(basename($p_template_section));

				if($p_template_section !== ''
				   && (file_exists(DIR_FS_ADMIN . 'html/content/' . $p_template_section))
				)
				{
					$coo_view->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/' . $p_template_section . '/');
				}
				else
				{
					$coo_view->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/');
				}
			}
		}

		$p_param['template'] = $p_template_name;
		$coo_view->set_content_template($p_template_name);
		$coo_view->set_lightbox_mode(true);
		$coo_view->set_lightbox_parameters($p_param);
		$t_javascript_section = str_replace(".html", "", $p_template_name);
		$coo_view->set_javascript_section($t_javascript_section);

		$t_return = $coo_view->get_html_array($p_param);

		return $t_return;
	}
}