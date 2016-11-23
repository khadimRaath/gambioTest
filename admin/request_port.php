<?php
/* --------------------------------------------------------------
   request_port.inc.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
require('includes/application_top.php');

$t_output_content = '';

switch($_GET['module'])
{	
	case 'megadropdown':
		$c_categories_id = (int)$_GET['categories_id'];

		$coo_categories_dropdown = MainFactory::create_object('CategoriesMenuBoxContentView');
		$coo_categories_dropdown->set_content_template('module/megadropdown.html');
		$coo_categories_dropdown->set_tree_depth(1);
		$t_categories_html = $coo_categories_dropdown->get_html($c_categories_id);

		$t_output_content = $t_categories_html;
		break;

	case 'reset_combis_sort_order':
		$coo_properties_combis_admin_control = MainFactory::create_object('PropertiesCombisAdminControl');
		$coo_properties_combis_admin_control->reset_combis_sort_order((int)$_GET['products_id']);
		$t_output_content = 'success';
		break;

	case 'properties_combis_status':
		$coo_properties_view = MainFactory::create_object('PropertiesView');
		$t_output_content = $coo_properties_view->get_combis_status_json($_GET['products_id'], $_GET['properties_values_ids']);
		break;

	case 'properties_combis_image_upload':
		$c_combis_id	= (int)$_GET['combis_id'];
		$t_target_path	= DIR_FS_CATALOG_IMAGES.'product_images/properties_combis_images/';
		$t_filename		= '';

		#copy upload file to target dir
		$t_upload_file =& xtc_try_upload('combi_image', $t_target_path);

		if($t_upload_file)
		{
			#rename uploaded file
			$t_old_file = $t_upload_file->filename;
			$t_new_file = $c_combis_id .'_'. $t_old_file;

			rename(
				$t_target_path . $t_old_file,
				$t_target_path . $t_new_file
			);

			#get combi data object
			$coo_combis = new GMDataObject('products_properties_combis', array('products_properties_combis_id' => $c_combis_id));

			#delete old combi_image if ixists
			$t_old_image = $coo_combis->get_data_value('combi_image');
			if(empty($t_old_image) == false) unlink($t_target_path.$t_old_image);

			#save new filename to combi
			$coo_combis->set_data_value('combi_image', $t_new_file);
			$coo_combis->save_body_data();
			
			#return value
			$t_output_content = 'success';
		}
		else
		{
			$t_output_content = 'upload_error';
		}
		break;
	
	case 'load_content':
		$coo_load_url = MainFactory::create_object('LoadUrl');
		
		$t_header_data_array = array();
		if(isset($_GET['header_data_array']) && is_array($_GET['header_data_array']))
		{
			$t_header_data_array = $_GET['header_data_array'];
		}
		$t_iframe_style = '';
		if(isset($_GET['iframe_style']))
		{
			$t_iframe_style = (string)$_GET['iframe_style'];
		}
		
		$result = $coo_load_url->load_url($_GET['link'], $t_header_data_array, $t_iframe_style);
		
		$t_output_content = TEXT_NO_CONTENT;
		if($result) {
			$t_output_content = $result;
		}
		break;
		
	default:
		$f_module_name = $_GET['module'];
		
		if(trim($f_module_name) != '')
		{
			$t_class_name_suffix = 'AjaxHandler';
			$coo_request_router = MainFactory::create_object('RequestRouter', array($t_class_name_suffix));

			$coo_request_router->set_data('GET', $_GET);
			$coo_request_router->set_data('POST', $_POST);

			$t_proceed_status = $coo_request_router->proceed($f_module_name);
			if($t_proceed_status == true) {
				$t_output_content = $coo_request_router->get_response();
			} else {
				trigger_error('could not proceed module ['.htmlentities_wrapper($f_module_name).']', E_USER_ERROR);
			}
		}
}	

echo $t_output_content;
((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);