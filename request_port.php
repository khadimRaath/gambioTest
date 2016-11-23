<?php
/* --------------------------------------------------------------
   request_port.inc.php 2014-07-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require('includes/application_top_main.php');

#error_reporting(E_ALL);
$t_output_content = '';

switch($_GET['module'])
{
	case 'buy_now': #TODO: move to coo_handler
		# fake environment
		$_GET['action'] = 'buy_now';
		$_GET['BUYproducts_id'] = (int)$_POST['products_id'];

		$t_turbo_buy_now = true;	# flag used in cart_actions
		$t_show_cart = false;		# will be changed in cart_actions
		$t_show_details = false;	# will be changed in cart_actions

		# run cart_actions
		require(DIR_WS_INCLUDES.FILENAME_CART_ACTIONS);
		
		$t_output_array = array
		(
			'show_cart' => $t_show_cart,
			'show_details' => $t_show_details,
			'products_details_url' => xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$_GET['BUYproducts_id'])
		);
		$coo_json = new GMJSON(false);
		$t_output_json = $coo_json->encode($t_output_array);

		$t_output_content = $t_output_json;
		break;

	case 'properties_combis_status':
		$coo_properties_view = MainFactory::create_object('PropertiesView');
		$t_output_content = $coo_properties_view->get_combis_status_json($_GET['products_id'], $_GET['properties_values_ids'], $_GET['need_qty']);
		break;

	case 'properties_combis_status_by_combis_id':
		$coo_properties_view = MainFactory::create_object('PropertiesView');
		$t_output_content = $coo_properties_view->get_combis_status_by_combis_id_json($_GET['combis_id'], $_GET['need_qty']);
		break;

	// Deactivates the PayPal deprecation notice alert permanently.
	case 'deactivate_pp_deprecated_alert':
		PayPalDeprecatedCheck::deactivateOutputPermanently();
		$t_output_content = '{"success": "true"}';
		break;
	
	default:
		# plugin requests
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