<?php
/* --------------------------------------------------------------
   PropertiesView.inc.php 2015-04-27 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

#TODO: create language files
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

  function steves_json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }

	$coo_json = new GMJSON(false);

    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = $coo_json->encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = $coo_json->encode($k).':'.$coo_json->encode($v);
      return '{' . join(',', $result) . '}';
    }
  }


class PropertiesView 
{
	var $v_coo_properties_control = false;
	
	var $m_env_get_array = array();
	var $m_env_post_array = array();
	
	var $m_content_array = array();
	var $m_content_template = '';
	
	protected $coo_lang_manager;
	
	function PropertiesView($p_get_array=false, $p_post_array=false) 
	{
		$this->v_coo_properties_control = MainFactory::create_object('PropertiesControl');
		$this->coo_lang_manager = MainFactory::create_object('LanguageTextManager', array('properties_dropdown', $_SESSION['languages_id']));
		
		if($p_get_array) $this->m_env_get_array = $p_get_array;
		if($p_post_array) $this->m_env_post_array = $p_post_array;
	}
	
	
	function proceed()
	{
		$t_output = '';
		return $t_output;
	}
	
	function get_order_details_by_combis_id($p_properties_combis_id, $p_order_details_type)
	{
		$t_language_id = $_SESSION['languages_id'];
		$t_properties_data_array = $this->v_coo_properties_control->get_properties_combis_details($p_properties_combis_id, $t_language_id);
								
		$t_html_output = $this->build_order_details($t_properties_data_array, $p_order_details_type);
		return $t_html_output;
	}
	
	function build_order_details($p_properties_data_array, $p_order_details_type)
	{
		$t_content_data_array = array(
									'PROPERTIES_DATA' => $p_properties_data_array
								);
		
		switch($p_order_details_type)
		{
			case 'mail_html':
				$t_content_template = 'order_details_mail.html';
				break;
			
			case 'mail_text':
				$t_content_template = 'order_details_mail.txt';
				break;
			
			case 'cart':
			default:
				$t_content_template = 'order_details_cart.html';
				break;
		}
		
		$t_html_output = $this->build_html($t_content_data_array, $t_content_template);
		return $t_html_output;
	}
	
	# -1 not available
	#  0 out of stock
	#  1 available
	function get_combis_status_code($p_products_id, $p_value_ids_array, $p_need_qty=1)
	{
		$t_return_code = 0; # return value
		$c_need_qty= gm_prepare_number($p_need_qty);
		
		$t_products_properties_combis_id = $this->v_coo_properties_control->get_available_combis_ids_by_values($p_products_id, $p_value_ids_array, false);
		if(is_array($t_products_properties_combis_id) == false || count($t_products_properties_combis_id) != 1) 
		{
			# not found
			$t_return_code = -1; 
		}
		else
		{
			# found -> check quantity
			$t_return_code = $this->get_combis_status_code_by_combis_id($p_products_id, $t_products_properties_combis_id[0], $c_need_qty);
		}
		return $t_return_code;
	}

	#  0 out of stock
	#  1 available
	function get_combis_status_code_by_combis_id($p_products_id, $p_combis_id, $p_need_qty=1)
	{
		$t_return_code = 0; # return value
        
        // get quantity check
        $coo_products = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $p_products_id) ));
        $use_properties_combis_quantity = $coo_products->get_data_value('use_properties_combis_quantity');
        
		if(($use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $use_properties_combis_quantity == 2)
		{
            $t_quantity = $this->v_coo_properties_control->get_properties_combis_quantity($p_combis_id);
        }
		else if(($use_properties_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'false') || $use_properties_combis_quantity == 1)
		{
            $t_quantity = $coo_products->get_data_value('products_quantity');
        }
		else
		{
			$t_quantity = 99999;
		}
		
		if($t_quantity < $p_need_qty && STOCK_ALLOW_CHECKOUT == 'true')
		{
			# out of stock but allowed
			$t_return_code = 2;
		}
		else if($t_quantity < $p_need_qty)
		{
			# out of stock
			$t_return_code = 0;
		}
		else
		{
			# combi found and available
			$t_return_code = 1;
		}
		return $t_return_code;
	}

	function get_combis_status_code_text($p_status_code)
	{	
		switch($p_status_code)
		{
			case -1: 	 	 
				$t_output_text = $this->coo_lang_manager->get_text('COMBI_NOT_EXIST');
				break; 
			case 0:
				$t_output_text = $this->coo_lang_manager->get_text('COMBI_NOT_AVAILABLE');
				break;
			case 1:
				$t_output_text = $this->coo_lang_manager->get_text('available');
				break;
			case 2:
				$t_output_text = $this->coo_lang_manager->get_text('COMBI_NOT_AVAILABLE_BUT_ALLOWED');
				break;
		}
		return $t_output_text;
	}

	function get_combis_status_json($p_products_id, $p_value_ids_array, $p_need_qty=1)
	{
		$t_output_json = false;

		# get CODE and TEXT
		$t_status_code = $this->get_combis_status_code($p_products_id, $p_value_ids_array, $p_need_qty);
		$t_status_text = $this->get_combis_status_code_text($t_status_code);
                
		$t_status_array = array(
							'STATUS_CODE' => $t_status_code,
							'STATUS_TEXT' => $t_status_text
							);
							
		$t_output_json = steves_json_encode($t_status_array);
		return $t_output_json;
	}
	
	function get_combis_status_by_combis_id_json($p_combis_id, $p_need_qty=1)
	{
		$t_output_json = false;

        $coo_products_properties_combis = MainFactory::create_object('GMDataObject', array('products_properties_combis', array('products_properties_combis_id' => $p_combis_id) ));
        
		# get CODE and TEXT
		$t_status_code = $this->get_combis_status_code_by_combis_id($coo_products_properties_combis->get_data_value('products_id'), $p_combis_id, $p_need_qty);
		$t_status_text = $this->get_combis_status_code_text($t_status_code);
      
		$t_status_array = array(
							'STATUS_CODE' => $t_status_code,
							'STATUS_TEXT' => $t_status_text
							);

		$t_output_json = steves_json_encode($t_status_array);
		return $t_output_json;
	}
        
        function get_combis_exists($p_products_id, $p_value_ids_array, $p_need_qty=1)
	{
		$t_return_code = 0; # return value
		
		$t_products_properties_combis_id = $this->v_coo_properties_control->get_combis_id_by_value_ids_array($p_products_id, $p_value_ids_array);
		if($t_products_properties_combis_id == 0) 
		{
			# not found
			$t_return_code = 0; 
		}
		else {
			# found -> return $t_products_properties_combis_id
			$t_return_code = $t_products_properties_combis_id;
		}
		return $t_return_code;
	}

	function get_selection_form($p_products_id, $p_language_id, $p_selected_ids=false, $p_selected_combi=false, $p_quantity=false)
	{
		$c_products_id = (int)$p_products_id;
		$c_language_id = (int)$p_language_id;
		$c_quantity = (int)$p_quantity;
		if($c_quantity == 0)
		{
			$c_quantity = 1;
		}

		$t_html_output = '';
		
		if(gm_get_env_info('TEMPLATE_VERSION') < FIRST_GX2_TEMPLATE_VERSION)
		{
			return $t_html_output;
		}
		
		$t_selection_form_type = 'dropdowns';

		switch($t_selection_form_type)
		{
			case 'dropdowns':
				// GET PRODUCT
				$coo_product_object = MainFactory::create_object("GMDataObject", array("products", array("products_id" => $c_products_id)));
				$t_properties_dropdown_mode = $coo_product_object->get_data_value('properties_dropdown_mode');
				$t_properties_price_show = $coo_product_object->get_data_value('properties_show_price');
				
				// GET ALL PROPERTIES DATA
				$t_properties_array = $this->v_coo_properties_control->get_products_properties_data($c_products_id, $c_language_id);

				if(is_string($p_selected_ids) && trim($p_selected_ids) != '')
				{
					$p_selected_ids = $this->v_coo_properties_control->split_properties_values_string($p_selected_ids);
				}
				$t_error = '';
				$t_image = '';
				$t_selected_combi = false;
				$t_selected_values = array();
				if($p_selected_combi != false)
				{
					// GET SELECTED COMBI
					$t_selected_combi = $p_selected_combi;
					if(trim($t_selected_combi['combi_image']) != '')
					{
						$t_image = '<img class="img-responsive" src="images/product_images/properties_combis_images/' . $t_selected_combi['combi_image'] . '" alt="" />';
					}					
				}
				
				if($t_selected_combi != false)
				{
					$t_valid_quantity = $this->v_coo_properties_control->quantity_check($coo_product_object, $t_selected_combi, $c_quantity);
					$t_error = $t_valid_quantity['message'];
					
					// GET VALUES FROM SELECTED COMBI
					foreach($t_selected_combi['COMBIS_VALUES'] as $t_value)
					{
						$t_selected_values[$t_value['properties_id']] = $t_value['properties_values_id'];
					}
				}
				else if(is_array($p_selected_ids) && count($p_selected_ids) > 0)
				{
					$t_selected_combi = $this->v_coo_properties_control->get_selected_combi($c_products_id, $c_language_id, $p_selected_ids);
					$t_selected_values = $p_selected_ids;
				}
				
				if($t_properties_dropdown_mode == '' && $t_selected_combi == false && is_array($p_selected_ids) && count($p_selected_ids) == count($t_properties_array))
				{
					$t_error = $this->coo_lang_manager->get_text('COMBI_NOT_EXIST');
				}
				
				$t_single_propertie = 0;
				if(count($t_selected_values) == 1)
				{
					$t_single_propertie = key($t_selected_values);
				}
				
				$t_available_properties_values = $this->v_coo_properties_control->get_available_properties_values_by_values($c_products_id, $t_selected_values);
				
				$t_visible_properties = array();
				if($t_selected_combi != false || $t_properties_dropdown_mode != 'dropdown_mode_2')
				{
					foreach($t_properties_array as $t_propertie)
					{
						$t_visible_properties[] = $t_propertie['properties_id'];
					}
				}
				else
				{
					if(count($t_selected_values) > 0)
					{
						$t_append_next = false;
						foreach($t_properties_array as $t_propertie)
						{
							if(count(array_intersect($t_selected_values, array_keys($t_propertie['values_array']))) > 0)
							{
								$t_visible_properties[] = $t_propertie['properties_id'];
							}
							else
							{
								$t_append_next = true;
							}
							if($t_append_next == true)
							{
								$t_visible_properties[] = $t_propertie['properties_id'];
								break;
							}
						}
					}
				}
				
				$t_index = 0;
				foreach($t_properties_array as $t_propertie)
				{
					$t_properties_id = $t_propertie['properties_id'];
					$t_visible = false;
					if(in_array($t_properties_id, $t_visible_properties) == true || $t_index == 0)
					{
						$t_visible = true;
					}
					foreach($t_properties_array[$t_properties_id]['values_array'] as $t_value)
					{
						$t_value_id = $t_value['properties_values_id'];
						$t_disabled = true;
						$t_selected = false;
						if(in_array($t_value_id, $t_available_properties_values) == true)
						{
							$t_disabled = false;
						}
						if(in_array($t_value_id, $t_selected_values) == true && $t_visible == true)
						{
							$t_selected = true;
						}
						$t_properties_array[$t_properties_id]['values_array'][$t_value_id]['selected'] = $t_selected;
						$t_properties_array[$t_properties_id]['values_array'][$t_value_id]['disabled'] = $t_disabled;
					}
					if (APPLICATION_RUN_MODE === 'backend')
					{
						$t_properties_array[$t_properties_id]['visible'] = true;
					}
					else
					{
						$t_properties_array[$t_properties_id]['visible'] = $t_visible;
					}
					$t_index++;
				}			
				
				$t_content_data_array = array(
											'products_id'				=> $c_products_id,
											'properties_dropdown_mode'	=> $t_properties_dropdown_mode,
											'properties_price_show'		=> $t_properties_price_show,
											'properties_currency'		=> $_SESSION['currency'],
											'PROPERTIES_DATA'			=> $t_properties_array,
											'PROPERTIES_ERROR'			=> $t_error,
											'PROPERTIES_IMAGE'			=> $t_image
										);
				$t_content_template = 'selection_forms/dropdowns.html';
				break;
			
			default:
				break;
		}

		$t_html_output = $this->build_html($t_content_data_array, $t_content_template);
		return $t_html_output;
	}
		
	
	
	
	# standard function
	function build_html($p_content_data_array, $p_template_file, $p_add_languages=true)
	{
		# language array for assigning in smarty template
		if($p_add_languages)
		{
			$t_languages_array = $this->v_coo_properties_control->get_shop_languages_data();
			$p_content_data_array = array_merge(
										array('LANGUAGES' => $t_languages_array), 
										$p_content_data_array);
		}
		$coo_smarty = new Smarty();
		
		# assign content
		$coo_smarty->assign('content_data', $p_content_data_array);
		
		# compile settings
		$coo_smarty->compile_dir = DIR_FS_CATALOG.'templates_c/';
		$coo_smarty->caching = false;
		
		# get html content
		$t_full_template_path = DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/properties/'.$p_template_file;
		//$coo_smarty->clear_compiled_tpl($t_full_template_path);
		//$coo_smarty->clear_compiled_tpl();
		
		$t_html_output = $coo_smarty->fetch($t_full_template_path);
		return $t_html_output;
	}
	
	# ???
	function get_output() 
	{
		$t_content_array = $this->m_content_array;
		$t_template_file = $this->m_content_template;
		
		$t_html_output = $this->build_html($t_content_array, $t_template_file); 
		return $t_html_output;
	}
}