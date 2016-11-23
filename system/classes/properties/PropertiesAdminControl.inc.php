<?php
/* --------------------------------------------------------------
   PropertiesAdminControl.inc.php 2015-01-08 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesAdminControl
{        
    public function __construct() {}
    
    public function get_all_properties()
    {
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $all_properties = $coo_product_properties_struct_supplier->get_all_properties();
        return $all_properties;
    }
    
    public function get_properties($p_properties_id)
    {
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $properties = $coo_product_properties_struct_supplier->get_properties($p_properties_id);
        return $properties;
    }
    
    public function get_properties_values_by_properties_values_id($p_properties_values_id)
    { 
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $properties_values = $coo_product_properties_struct_supplier->get_properties_values_by_properties_values_id($p_properties_values_id);
        return $properties_values;
    }
    
    public function save_properties($p_properties_data)
    {
        $c_properties_data = $p_properties_data;
        if(!is_array($c_properties_data)) trigger_error('save_properties: typeof($p_properties_data) != array', E_USER_ERROR); 
        
        $t_return = array();
        
        $t_insert_mode = true;
        $t_properties_id = (int)$c_properties_data['properties_id'];
        $t_sort_order = (int)$c_properties_data['sort_order'];
        
        $coo_properties = new GMDataObject('properties');

        if(empty($t_properties_id))
        {
            $t_return['action'] = 'insert_properties';
            $coo_properties->set_keys(array('properties_id' => false));
        } 
        else 
        { 
            $t_insert_mode = false;
            $t_return['action'] = 'update_properties';
            $coo_properties->set_keys(array('properties_id' => $t_properties_id));
        }
        $coo_properties->set_data_value('sort_order', $t_sort_order);
        
        $t_insert_id = $coo_properties->save_body_data();
        if($t_insert_id > 0) $t_properties_id = $t_insert_id;

        // save properties description
        $t_languages_array = xtc_get_languages();

        for($i=0; $i<sizeof($t_languages_array); $i++) 
        {
            $coo_properties_description = new GMDataObject('properties_description');

            $t_language_id 	 = $t_languages_array[$i]['id'];
            $t_language_code = $t_languages_array[$i]['code'];

            if($t_insert_mode)
            {
                $coo_properties_description->set_keys(array('properties_description_id' => false));
                $coo_properties_description->set_data_value('properties_id', $t_properties_id);
                $coo_properties_description->set_data_value('language_id', $t_language_id);
            } 
            else
            {
                $coo_properties_description->set_keys(array('properties_id' => $t_properties_id, 'language_id'	=> $t_language_id));
            }

            $coo_properties_description->set_data_value('properties_name', $c_properties_data['properties_name'][$t_language_code]);
            $coo_properties_description->set_data_value('properties_admin_name', $c_properties_data['properties_admin_name'][$t_language_code]);
            $coo_properties_description->save_body_data();
        }
        
        return $t_properties_id;
    }
    
    public function delete_properties($p_properties_id)
    {
        $c_properties_id = (int)$p_properties_id;
        if(empty($c_properties_id)) trigger_error('delete_properties: typeof($p_properties_id) != integer', E_USER_ERROR); 
        
        $t_return = array();
        
        # delete properties
        $coo_data_object = new GMDataObject('properties');
        $coo_data_object->set_keys(array('properties_id' => $c_properties_id));
        $coo_data_object->delete();

        # delete properties_description
        $coo_data_object = new GMDataObject('properties_description');
        $coo_data_object->set_keys(array('properties_id' => $c_properties_id));
        $coo_data_object->delete();
        
        # get related properties_values 
        $coo_data = new GMDataObjectGroup('properties_values', array('properties_id' => $c_properties_id));
        $coo_data_array = $coo_data->get_data_objects_array();

        # delete related properties_values
        for($i=0; $i<sizeof($coo_data_array); $i++)
        {
            $this->delete_properties_values($coo_data_array[$i]->get_data_value('properties_values_id') );
        }
        
        $t_return['action'] = 'delete_properties';
        $t_return['properties_id'] = $c_properties_id;
        
        return $t_return;
    }
    
    public function save_properties_values($p_properties_values_data)
    {
        $c_properties_values_data = $p_properties_values_data;
        if(!is_array($c_properties_values_data)) trigger_error('save_properties_values: typeof($p_properties_values_data) != array', E_USER_ERROR); 
        
        $t_return = array();
        
        $t_insert_mode = true;
        
        $t_properties_values_id = (int)$c_properties_values_data['properties_values_id'];
        
        $coo_properties_values = new GMDataObject('properties_values');

        if(empty($t_properties_values_id))
        {
            $t_return['action'] = 'insert_properties_values';
            $coo_properties_values->set_keys(array('properties_values_id' => false));
        } 
        else
        {
            $t_insert_mode = false;
            $t_return['action'] = 'update_properties_values';
            $coo_properties_values->set_keys(array('properties_values_id' => $t_properties_values_id));
            $t_property_value = $this->get_properties_values_by_properties_values_id($t_properties_values_id);
        }
        $coo_properties_values->set_data_value('properties_id', (int)$c_properties_values_data['properties_id']);
        $coo_properties_values->set_data_value('sort_order',    (int)$c_properties_values_data['sort_order']);
        $coo_properties_values->set_data_value('value_model', htmlspecialchars_wrapper($c_properties_values_data['value_model']));
        $coo_properties_values->set_data_value('value_price', clean_numeric_input($c_properties_values_data['value_price']));

        $t_insert_id = $coo_properties_values->save_body_data();
        if($t_insert_id > 0) $t_properties_values_id = $t_insert_id;

        // save properties values description
        $t_languages_array = xtc_get_languages();

        for($i=0; $i<sizeof($t_languages_array); $i++) 
        {
            $coo_properties_values_description = new GMDataObject('properties_values_description');

            $t_language_id 	 = $t_languages_array[$i]['id'];
            $t_language_code = $t_languages_array[$i]['code'];

            if($t_insert_mode)
            {
                $coo_properties_values_description->set_keys(array('properties_values_description_id' => false));
                $coo_properties_values_description->set_data_value('properties_values_id', $t_properties_values_id);
                $coo_properties_values_description->set_data_value('language_id', $t_language_id);
            } 
            else
            {
                $coo_properties_values_description->set_keys(array('properties_values_id' => $t_properties_values_id, 'language_id'	=> $t_language_id));
            }

            $coo_properties_values_description->set_data_value('values_name', $c_properties_values_data['values_name'][$t_language_code]);
            $coo_properties_values_description->save_body_data();
        }

        if(!$t_insert_mode)
        {
            if((double)$t_property_value['value_price'] !== (double)$c_properties_values_data['value_price'])
            {
                $t_property_combis_admin_contol = new PropertiesCombisAdminControl();
                $t_combi_ids_array              = $t_property_combis_admin_contol->get_combi_ids_by_property_value_id($t_properties_values_id);

                $t_product_properties = new ProductPropertiesStructSupplier();
                foreach($t_combi_ids_array as $t_combi_id)
                {
                    $t_product_id = (int)$t_property_combis_admin_contol->get_product_id_by_combi_id($t_combi_id);
                    if($t_product_id === 0)
                    {
                        continue;
                    }
                    $t_properties_combis_array                      = $t_product_properties->get_combis($t_product_id,
                        $t_combi_id,
                        $_SESSION['languages_id']);
                    $t_properties_combis_array['properties_values'] = array_keys($t_properties_combis_array['combis_values']);

                    $t_properties_combis_array['products_id'] = $t_product_id;
                    $t_property_combis_admin_contol->save_combis($t_properties_combis_array);
                }
            }
        }
        
        $t_return['properties_id'] = (int)$c_properties_values_data['properties_id'];        
        $t_return['properties_values_id'] = $t_properties_values_id;             
        
        
        return $t_return;
    }
    
    public function delete_properties_values($p_properties_values_id)
    {
        $c_properties_values_id = (int)$p_properties_values_id;
        if(empty($c_properties_values_id)) trigger_error('delete_properties_values: typeof($p_properties_values_id) != integer', E_USER_ERROR);  
        
        $t_return = array();
        
        # delete properties_values
        $coo_data_object = new GMDataObject('properties_values');
        $coo_data_object->set_keys(array('properties_values_id' => $c_properties_values_id));
        $coo_data_object->delete();

        # delete properties_values_description
        $coo_data = new GMDataObject('properties_values_description');
        $coo_data->set_keys(array('properties_values_id' => $c_properties_values_id));
        $coo_data->delete();

        # delete products_properties_combis_values
        $coo_properties_combis_admin_control = MainFactory::create_object('PropertiesCombisAdminControl');
        
        $coo_data = new GMDataObjectGroup('products_properties_index', array('properties_values_id' => $c_properties_values_id));
        $coo_data_array = $coo_data->get_data_objects_array();
        
        # delete related properties_combis
        for($i=0; $i<sizeof($coo_data_array); $i++)
        {
            $coo_properties_combis_admin_control->delete_combis(array($coo_data_array[$i]->get_data_value('products_properties_combis_id')));
            $coo_data_array[$i]->delete(); #delete index entry
        }
        
        $t_return['properties_values_id'] = $c_properties_values_id;
        $t_return['action'] = 'delete_properties_values';
        
        return $t_return;
    }
    
    function get_properties_in_combis_count($p_properties_id)
    {
        $c_properties_id = (int)$p_properties_id;
        if(empty($c_properties_id)) trigger_error('get_properties_in_combis_count: typeof($p_properties_id) != integer', E_USER_ERROR);  
        
        $t_return = array();

        $t_count_combis = 0;
        
        $t_sql = '
            SELECT 
                count(*) AS countCombis
            FROM
                products_properties_index
            WHERE
                language_id = '.$_SESSION['languages_id'].' AND
                properties_id = '.$c_properties_id.'
        ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_row = xtc_db_fetch_array($t_result);
            $t_count_combis = $t_row['countCombis'];
        }
        
        $t_return['combis_count'] = $t_count_combis;
        $t_return['properties_id'] = $c_properties_id;
        
        return $t_return;
    }
	
    function get_properties_values_in_combis_count($p_properties_values_id)
    {
        $c_properties_values_id = (int)$p_properties_values_id;
        if(empty($c_properties_values_id)) trigger_error('get_properties_values_in_combis_count: typeof($p_properties_values_id) != integer', E_USER_ERROR);
        
        $t_return = array();
        
        $t_count_combis = 0;
        
        $t_sql = '
            SELECT 
                count(*) AS countCombis
            FROM
                products_properties_combis_values
            WHERE
                properties_values_id = '.$c_properties_values_id.'
        ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_row = xtc_db_fetch_array($t_result);
            $t_count_combis = $t_row['countCombis'];
        }

        $t_return['combis_count'] = $t_count_combis;
        $t_return['properties_values_id'] = $c_properties_values_id;
        
        return $t_return;
    }
}