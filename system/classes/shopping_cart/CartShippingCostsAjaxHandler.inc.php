<?php
/* --------------------------------------------------------------
   CardShippingCostsAjaxHandler.inc.php 2016-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require_once(DIR_FS_CATALOG . 'inc/xtc_get_countries.inc.php');

class CartShippingCostsAjaxHandler extends AjaxHandler
{
    public function get_permission_status($p_customers_id = NULL)
    {
		return true;
    }

    public function proceed()
    {
		global $total_weight, $total_count;
		$total_weight = $_SESSION['cart']->weight;
		$total_count = $_SESSION['cart']->count_contents();
		$t_output_array = array();
		$t_output_json = '';		
		$t_cart_shipping_country = $this->v_data_array['POST']['cart_shipping_country'];
		$t_cart_shipping_module = str_replace('___', '_', $this->v_data_array['POST']['cart_shipping_module']);
		$t_cart_shipping_method = '';		
		$_SESSION['cart_shipping_country'] = $t_cart_shipping_country;
		$t_country = xtc_get_countriesList( $t_cart_shipping_country, true, true );
		$_SESSION['delivery_zone'] = $t_country['countries_iso_code_2'];
		
		$coo_cart_shipping_costs_controller = MainFactory::create_object('CartShippingCostsControl', array(), true);
		
		if(empty($t_cart_shipping_country))
		{
			trigger_error('t_cart_shipping_country not found in CartShippingCostsAjaxHandler');
			return false;
		}
		
		$t_cart_shipping_method = substr_wrapper($t_cart_shipping_module, strpos($t_cart_shipping_module, $coo_cart_shipping_costs_controller->v_module_method_separator) + strlen($coo_cart_shipping_costs_controller->v_module_method_separator));
		$t_cart_shipping_module = substr_wrapper($t_cart_shipping_module, 0, strpos($t_cart_shipping_module, $coo_cart_shipping_costs_controller->v_module_method_separator));
		
		#var_dump($coo_cart_shipping_costs_controller->v_shipping_class->module_is_allowed($t_cart_shipping_country, $t_cart_shipping_module));
				
		if (!$coo_cart_shipping_costs_controller->v_shipping_class->module_is_allowed($t_cart_shipping_country, $t_cart_shipping_module))
		{
			$t_module = $coo_cart_shipping_costs_controller->v_shipping_class->shopping_cart_cheapest();
			if(!empty($t_module) && is_array($t_module))
			{
				$t_cart_shipping_module = $t_module['id'];
				$t_cart_shipping_method = $t_module['method_id'];
			}
		}

	    $_SESSION['shipping']['id'] = $t_cart_shipping_module . '_' . $t_cart_shipping_method;
	    
		$_SESSION['shipping'] = array('id' => $t_cart_shipping_module . '_' . $t_cart_shipping_method,
									  'title' => current($coo_cart_shipping_costs_controller->get_selected_shipping_module()),
									  'cost' => $coo_cart_shipping_costs_controller->get_shipping_costs($t_cart_shipping_country, $t_cart_shipping_module, $t_cart_shipping_method, true));
		
		if(isset($this->v_data_array['GET']['action']))
		{
			$t_action_request = $this->v_data_array['GET']['action'];
			switch($t_action_request)
			{
				case 'get_shipping_costs':
					$t_cart_shipping_costs = $coo_cart_shipping_costs_controller->get_shipping_costs($t_cart_shipping_country, $t_cart_shipping_module, $t_cart_shipping_method);
					$t_cart_ot_gambioultra_costs = $coo_cart_shipping_costs_controller->get_ot_gambioultra_costs($t_cart_shipping_country);
					if( $t_cart_shipping_costs === false )
					{
						$t_output_array['status'] = 'error';
						$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array( 'cart_shipping_costs', $_SESSION['languages_id']), false);
						$t_output_array['error_message'] = $coo_text_mgr->get_text( 'combi_not_allowed' );
					}
					else
					{
						$t_output_array['status'] = 'success';
						$t_output_array['cart_shipping_costs'] = htmlentities_wrapper($t_cart_shipping_costs);
						if( $t_cart_ot_gambioultra_costs != '' )
						{
							$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array( 'gambioultra', $_SESSION['languages_id']), false);
							$t_output_array['cart_ot_gambioultra_costs'] = '<br />' . SHIPPING_EXCL . ' ' . $coo_text_mgr->get_text('name') . ': ' . $t_cart_ot_gambioultra_costs;
						}
						
					}
					$coo_cart_shipping_costs_content_view = MainFactory::create_object( 'CartShippingCostsContentView' );
					$t_output_array['html'] = $coo_cart_shipping_costs_content_view->get_html();					
					break;
				
				case 'get_shipping_modules':
					$coo_cart_shipping_costs_content_view = MainFactory::create_object('CartShippingCostsContentView');
					$coo_cart_shipping_costs_content_view->set_content_template('module/cart_shipping_costs_shipping_module_selection.html');
					$t_output_array['html'] = $coo_cart_shipping_costs_content_view->get_html();
					break;
				
				case 'get_shipping_weight':
					$coo_cart_shipping_costs_content_view = MainFactory::create_object('CartShippingCostsContentView');
					$coo_cart_shipping_costs_content_view->set_content_template('module/cart_shipping_costs_shipping_weight_information.html');
					$t_output_array['html'] = $coo_cart_shipping_costs_content_view->get_html();
					break;
				
				default:
					trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
					return false;
			}
			
			if(function_exists('json_encode'))
			{
				$t_output_json = json_encode($t_output_array);
			}
			else
			{
				// Services_JSON muss mit ausgeliefert werden!!!
				require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');
				$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
				$t_output_json = $coo_json->encode($t_output_array);
			}
		}
		
		$this->v_output_buffer = $t_output_json;
		
		return true;
    }
}
