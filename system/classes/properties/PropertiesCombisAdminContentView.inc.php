<?php
/* --------------------------------------------------------------
   PropertiesCombisAdminContentView.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesCombisAdminContentView extends LightboxContentView
{
    protected $v_properties_admin_control;
    protected $v_properties_combis_admin_control;
    
    function __construct()
    {
		parent::__construct();
        $this->v_properties_admin_control = MainFactory::create_object('PropertiesAdminControl');
        $this->v_properties_combis_admin_control = MainFactory::create_object('PropertiesCombisAdminControl');
        
        $this->set_template_dir(DIR_FS_CATALOG.'admin/html/content/properties/');
        
        $this->v_caching_enabled = false;
    }
    
    public function get_html_array( $p_data_array = array(), $p_post_array = array() )
    {
        if(empty($p_data_array)) trigger_error('PropertiesCombisAdminContentView: $p_data_array is empty', E_USER_ERROR);
        if(empty($p_data_array['template'])) trigger_error('PropertiesCombisAdminContentView: $p_data_array["template"] is empty', E_USER_ERROR);
        
        switch($p_data_array['template'])
        {
            case 'combis_main':
                $t_html_output['html'] = $this->get_combis_main($p_data_array['products_id'], $p_data_array['page'], $p_data_array['language_id'], $p_data_array['cPath']);
                break;
            case 'combis_table':
                $t_html_output['html'] = $this->get_combis_table($p_data_array['products_id'], $_SESSION['languages_id'], $p_data_array['combis_id']);
                break;
            case 'combis_settings.html':
                $t_html_output['html'] = $this->get_combis_settings($p_data_array['products_id']);
                break;
            case 'combis_defaults.html':
                $t_html_output['html'] = $this->get_combis_defaults($p_data_array['products_id']);
                break;
            case 'properties_combis_edit.html': 
				$t_html_output['html'] = $this->get_properties_combis_edit($p_data_array);
				break;
            case 'properties_combis_delete.html':              
            case 'properties_combis_delete_selected.html':
                $t_html_output['html'] = $this->get_properties_combis_delete($p_data_array);
                break;
            default:
                break;
        }
        
        return $t_html_output;
    }
    
    public function get_combis_main($p_products_id, $p_page, $p_language_id, $p_cPath)
    {       
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis_main: typeof($p_products_id) != integer', E_USER_ERROR);
        
        $c_page = (int)$p_page;
        if(empty($c_page)) trigger_error('get_combis_main: typeof($p_page) != integer', E_USER_ERROR);
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id']; 
        
        $c_cPath = trim($p_cPath);
        if($c_cPath === '' || !isset($c_cPath)) trigger_error('get_combis_main: typeof($p_cPath) is empty', E_USER_ERROR);
        
        // get product
        $t_coo_product = new GMDataObject('products_description', array('products_id' => $c_products_id, 'language_id' => $_SESSION['languages_id']));
        $t_products_name = $t_coo_product->get_data_value('products_name');
        
        // get combi_count
        $t_combis_count = $this->v_properties_combis_admin_control->get_combis_count($c_products_id);     
        
        if($t_combis_count != 0)
        {
            $t_max_page_number = ceil($t_combis_count/300);
        }
        else
        {
            $t_max_page_number = 1;
        }
        
        // get page_number
        if($c_page > $t_max_page_number)
        {
            $c_page = $t_max_page_number;
        }
        else if($c_page <= 1)
        {
            $c_page = 1;
        }
        $t_offset = 0;
        if($c_page > 1)
        {
            $t_offset = ($c_page-1) * 300;
        }
        
        // get combis
        $t_combis_tables_array = $this->v_properties_combis_admin_control->get_all_combis($c_products_id, $c_language_id, "full", $t_offset, 300);      
		
        // get_properties
        $t_properties_data_array = $this->v_properties_admin_control->get_all_properties();
        
        if(count($t_combis_tables_array) > 0)
        {
            $available_properties = $this->v_properties_combis_admin_control->get_admin_select($c_products_id);
            foreach($t_properties_data_array AS $key => $value)
            {
                $t_properties_data_array[$key]['class'] = "";					
                if(!array_key_exists($value['properties_id'], $available_properties))
                {
                    $t_properties_data_array[$key]['class'] = " disable";
                }
            }
        }
        
        // get products quantity type
        $coo_quantity_unit = MainFactory::create_object('QuantityUnit');
        $t_quantity_unit_name = $coo_quantity_unit->get_quantity_unit_name_by_products_id($c_products_id, $_SESSION['languages_id']);
        
        $coo_shipping_status_source = MainFactory::create_object('ProductsShippingStatusSource');
        $t_shipping_status = $coo_shipping_status_source->get_all_shipping_status();
        
        $coo_products_vpe_source = MainFactory::create_object('ProductsVPESource');
        $t_products_vpe = $coo_products_vpe_source->get_all_products_vpe();
        
        $t_content_data_array = array(
                                        'combis'            => $t_combis_tables_array,
                                        'combis_count'       => $t_combis_count,
                                        'max_page_number'   => $t_max_page_number,
                                        'current_page'      => $c_page,
                                        'properties'        => $t_properties_data_array,
                                        'products_id'       => $c_products_id,
                                        'products_name'     => $t_products_name,
                                        'cPath'             => $c_cPath,
                                        'shipping_status'   => $t_shipping_status,
                                        'products_vpe'     => $t_products_vpe,
                                        'products_path'     => xtc_href_link(FILENAME_CATEGORIES, "cPath=".$c_cPath."&pID=".$c_products_id."&action=new_product"),
                                        'products_quantity_unit_name' => $t_quantity_unit_name
								);
        
        $this->set_content_template('combis_main.html');
        $this->set_content_data('combis_main', $t_content_data_array);
        
        $t_html_output = $this->build_html();
            	
        return $t_html_output;
    }
    
    public function get_combis_table($p_products_id, $p_language_id, $p_combis_id)
    {       
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis_table: typeof($p_products_id) != integer', E_USER_ERROR);
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) trigger_error('get_combis_table: typeof($p_language_id) != integer', E_USER_ERROR); 
        
        $c_combis_id = (int)$p_combis_id;
        if(empty($c_combis_id)) trigger_error('get_combis_table: typeof($p_combis_id) != integer', E_USER_ERROR);
        
        #get data array for assigning in smarty template
        $t_content_data_array = $this->v_properties_combis_admin_control->get_combis($c_products_id, $c_combis_id, $c_language_id);	
		        
        $coo_shipping_status_source = MainFactory::create_object('ProductsShippingStatusSource');
        $t_content_data_array['shipping_status'] = $coo_shipping_status_source->get_all_shipping_status();
        
        $coo_products_vpe_source = MainFactory::create_object('ProductsVPESource');
        $t_content_data_array['products_vpe'] = $coo_products_vpe_source->get_all_products_vpe();

		$t_content_data_array['combi_quantity'] = (double)$t_content_data_array['combi_quantity'];
		
        // get products quantity type
        $coo_quantity_unit = MainFactory::create_object('QuantityUnit');
        $t_content_data_array['products_quantity_unit_name'] = $coo_quantity_unit->get_quantity_unit_name_by_products_id($c_products_id, $_SESSION['languages_id']);
        
        $this->set_content_template('combis_table.html');
        $this->set_content_data('combis_table', $t_content_data_array);
		$this->set_content_data('products_id', $c_products_id);
		        
        $t_html_output = $this->build_html();	
        return $t_html_output;
    }
    
    public function get_combis_settings($p_products_id)
    {  
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis_settings: typeof($p_products_id) != integer', E_USER_ERROR);
        
        $t_coo_product = new GMDataObject('products', array('products_id' => $c_products_id));
        $t_settings = array();
        $t_settings['properties_dropdown_mode'] = $t_coo_product->get_data_value('properties_dropdown_mode');
        $t_settings['properties_show_price'] = $t_coo_product->get_data_value('properties_show_price');
        $t_settings['use_properties_combis_weight'] = $t_coo_product->get_data_value('use_properties_combis_weight');
        $t_settings['use_properties_combis_quantity'] = $t_coo_product->get_data_value('use_properties_combis_quantity');
        $t_settings['use_properties_combis_shipping_time'] = $t_coo_product->get_data_value('use_properties_combis_shipping_time');
        
        $this->set_content_data("combis_setting", $t_settings);
		
		$this->set_lightbox_button('left', 'cancel', array('close', 'lightbox_close'));
		$this->set_lightbox_button('right', 'save', array('save', 'green'));

        $t_html_output = $this->build_html();
        
        return $t_html_output;
    }
    
    public function get_properties_combis_edit($p_param)
    {
        $c_products_id = (int)$p_param['products_id'];
        if(empty($c_products_id)) trigger_error('get_properties_combis_edit: typeof($p_param["products_id"]) != integer', E_USER_ERROR);
        
        $t_html_output = '';
        
        $c_combis_id = (int)$p_param['products_properties_combis_id'];
        $t_combis = array();
        if($c_combis_id > 0)
        {
			# load properties data by optional given properties_id
            $t_combis = $this->v_properties_combis_admin_control->get_combis($c_products_id, $c_combis_id, $_SESSION['languages_id']);
            $t_combis['combis_values'] = $t_combis['combis_values'];                        
            $t_combis['combis_values_ids'] = array_keys($t_combis['combis_values']);                        
        }
        else
        {
            # no properties_id given. use defaults and empty array for empty fields
            $t_combis['products_properties_combis_id'] = '';
            
            $combis_defaults = $this->v_properties_combis_admin_control->get_combis_defaults($c_products_id, false);          
        
            if(trim($combis_defaults['combi_price_type']) == ''){
                $combis_defaults['combi_price_type'] = 'calc';
            }
            
            $t_combis['combi_ean'] = $combis_defaults['combi_ean'];
            $t_combis['combi_quantity'] = $combis_defaults['combi_quantity'];
            $t_combis['combi_shipping_status_id'] = $combis_defaults['combi_shipping_status_id'];
            $t_combis['combi_weight'] = $combis_defaults['combi_weight'];
            $t_combis['combi_price_type'] = $combis_defaults['combi_price_type'];
            $t_combis['combi_price'] = $combis_defaults['combi_price'];
            $t_combis['products_vpe_id'] = $combis_defaults['products_vpe_id'];
            $t_combis['vpe_value'] = $combis_defaults['vpe_value'];    
            
            $coo_data_group = MainFactory::create_object('GMDataObjectGroup', array('products_properties_combis', array('products_id' => $c_products_id), array('sort_order DESC') ));
            $t_data_array = $coo_data_group->get_data_objects_array();

            # set start sort_order
            if(sizeof($t_data_array) == 0) {
                $t_combis['sort_order'] = 1;
            } else {
                $t_combis['sort_order'] = $t_data_array[0]->get_data_value('sort_order') + 1;
            }

			$t_combis['combis_values'] = array();
			$t_combis['combis_values_ids'] = array();
		}
		
		$t_combis['combi_quantity'] = (double)$t_combis['combi_quantity'];
						
		$coo_xtc_price = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']); 

		$coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id )));
		
		$t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id'); 
		
		if(PRICE_IS_BRUTTO == 'true')
		{
			// convert total price in netto
			$t_combis['combi_price'] = $coo_xtc_price->xtcAddTax($t_combis['combi_price'], $coo_xtc_price->TAX[$t_products_tax_class_id]);
		}
		
        $t_combis['admin_select'] = $this->v_properties_combis_admin_control->get_admin_select_detailed($c_products_id);
        
        $coo_shipping_status_source = MainFactory::create_object('ProductsShippingStatusSource');
        $t_combis['shipping_status'] = $coo_shipping_status_source->get_all_shipping_status();
        
        $coo_products_vpe_source = MainFactory::create_object('ProductsVPESource');
        $t_combis['products_vpe'] = $coo_products_vpe_source->get_all_products_vpe();
        
        $coo_quantity_unit = MainFactory::create_object('QuantityUnit');
        $t_combis['products_quantity_unit_name'] = $coo_quantity_unit->get_quantity_unit_name_by_products_id($c_products_id, $_SESSION['languages_id']);

        $this->set_content_data("combis", $t_combis);
		
		$this->set_lightbox_button('left', 'cancel', array('close', 'lightbox_close'));
		$this->set_lightbox_button('right', 'save', array('save', 'green'));
		$this->set_lightbox_button('right', 'save_close', array('save_close', 'green'));

        $t_html_output = $this->build_html();
        
        return $t_html_output;
    }
	
	public function get_properties_combis_delete($p_param)
    {
        $c_products_id = (int)$p_param['products_id'];
        if(empty($c_products_id)) trigger_error('get_properties_combis_edit: typeof($p_param["products_id"]) != integer', E_USER_ERROR);
        
        $c_combis_id = (int)$p_param['products_properties_combis_id'];
        $t_combis = array();
        if($c_combis_id > 0)
        {
			# load properties data by optional given properties_id
            $t_combis = $this->v_properties_combis_admin_control->get_combis($c_products_id, $c_combis_id, $_SESSION['languages_id']);
            $t_combis['combis_values'] = $t_combis['combis_values'];                        
            $t_combis['combis_values_ids'] = array_keys($t_combis['combis_values']);

			$coo_xtc_price = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);
			
			$coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id )));

			$t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id');

			if(PRICE_IS_BRUTTO == 'true')
			{
				// convert total price in netto
				$t_combis['combi_price'] = $coo_xtc_price->xtcAddTax($t_combis['combi_price'], $coo_xtc_price->TAX[$t_products_tax_class_id]);
			}
		}
        
        $coo_shipping_status_source = MainFactory::create_object('ProductsShippingStatusSource');
        $t_combis['shipping_status'] = $coo_shipping_status_source->get_all_shipping_status();
        
        $coo_products_vpe_source = MainFactory::create_object('ProductsVPESource');
        $t_combis['products_vpe'] = $coo_products_vpe_source->get_all_products_vpe();
        
        $this->set_content_data("combis", $t_combis);
		
		$this->set_lightbox_button('left', 'cancel', array('close', 'lightbox_close'));
		$this->set_lightbox_button('right', 'delete', array('delete', 'red'));

        $t_html_output = $this->build_html();
        
        return $t_html_output;
    }
    
    public function get_combis_defaults($p_products_id)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_propertie_combi_edit: typeof($p_products_id) != integer', E_USER_ERROR);
        
        $t_html_output = '';
        
        $this->set_content_data("combis", $this->v_properties_combis_admin_control->get_combis_defaults($c_products_id, $_SESSION['languages_id']));   
        
        $coo_shipping_status_source = MainFactory::create_object('ProductsShippingStatusSource');
        $this->set_content_data("shipping_status", $coo_shipping_status_source->get_all_shipping_status());
        
        $coo_products_vpe_source = MainFactory::create_object('ProductsVPESource');
        $this->set_content_data("products_vpe", $coo_products_vpe_source->get_all_products_vpe());
		
		$this->set_lightbox_button('left', 'cancel', array('close', 'lightbox_close'));
		$this->set_lightbox_button('right', 'save', array('save', 'green'));

        $t_html_output = $this->build_html();
        
        return $t_html_output;
    }
}