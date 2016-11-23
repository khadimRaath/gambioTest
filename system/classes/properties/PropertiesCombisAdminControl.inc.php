<?php
/* --------------------------------------------------------------
   PropertiesCombisAdminControl.inc.php 2016-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesCombisAdminControl 
{   
	protected $v_coo_xtc_price;
	protected $coo_language_manager;
	
    function PropertiesCombisAdminControl()
	{
		$this->v_coo_xtc_price = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);
		$this->coo_language_manager = MainFactory::create_object('LanguageTextManager', array('combis', $_SESSION['languages_id']));
	}
    
    public function get_all_combis($p_products_id, $p_language_id, $p_combis_values_type = 'full', $p_offset = 0, $p_limit = 300)
    {
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $combis = $coo_product_properties_struct_supplier->get_all_combis($p_products_id, $p_language_id, $p_combis_values_type, $p_offset, $p_limit);
        return $combis;
    }
    
    public function get_combis_count($p_products_id)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis_count: typeof($p_products_id) != integer', E_USER_ERROR); 
        
        $coo_combis_count = new GMDataObject('products_properties_combis', array('products_id' => $c_products_id));
        return $coo_combis_count->get_result_count();
    }
    
    public function get_combis($p_products_id, $p_combis_id, $p_language_id, $p_combis_values_type = 'full')
    {        
        $coo_product_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
        $combis = $coo_product_properties_struct_supplier->get_combis($p_products_id, $p_combis_id, $p_language_id, $p_combis_values_type);
        return $combis;
    }
    
    public function get_combis_id_by_value_ids_array($p_products_id, $p_language_id, $p_values_ids_array)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis_id_by_value_ids_array: typeof($p_products_id) != integer', E_USER_ERROR);   
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) trigger_error('get_combis_id_by_value_ids_array: typeof($p_language_id) != integer', E_USER_ERROR);
        
        if(!is_array($p_values_ids_array)) trigger_error('get_combis_id_by_value_ids_array: typeof($p_values_ids_array) != array', E_USER_ERROR);
        
        $t_accepted_combis_id = 0;
        
        $t_count_values = count($p_values_ids_array);
		
		$t_sql = '
			SELECT
				products_properties_combis_id
			FROM
				products_properties_index
			USE INDEX 
				(products_id_2)
			WHERE 
				products_id = ' . $c_products_id . ' AND
				language_id = ' . $c_language_id . ' AND
				properties_values_id IN ('.implode(',', $p_values_ids_array).')
			GROUP BY 
				products_properties_combis_id
			HAVING 
				count(*) = '.$t_count_values.'
			ORDER BY
				NULL
			LIMIT
				1
		';
        
        $result = xtc_db_query($t_sql);
        if(xtc_db_num_rows($result) == 1){
            $row = xtc_db_fetch_array($result);
            $t_accepted_combis_id = $row['products_properties_combis_id'];
        }
        
        return $t_accepted_combis_id;
    }
        
    public function save_combis($p_data_array, $p_prevent_duplicate_combis = false, $p_remove_taxes = true)
    {
        if((int)$p_data_array['products_id'] == 0) trigger_error('empty products_id'); 
		
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id']; 
        
        $t_return = array();
			
        if($p_prevent_duplicate_combis) # used in autobuild-mode
        {
            # look for existing combi
            $t_products_properties_combis_id = $this->get_combis_id_by_value_ids_array($p_data_array['products_id'], $c_language_id, $p_data_array['properties_values']);            
            if($t_products_properties_combis_id > 0 && (int)$p_data_array['products_properties_combis_id'] != $t_products_properties_combis_id)
            {
                # existing combi found, return found combi_id and stop saving
                $t_return['combis_exists'] = true;
                $t_return['action'] = 'abort';
                $t_return['combis_id'] = $t_products_properties_combis_id;
				$t_return['message'] = $this->coo_language_manager->get_text('combi_already_exists');
                return $t_return;
            }
        }

        $coo_combis = new GMDataObject('products_properties_combis');
        $products_properties_combis_id = (int)$p_data_array['products_properties_combis_id'];

        if(empty($products_properties_combis_id)) 
        {
            $t_return['combis_exists'] = false;
            $t_return['action'] = 'insert_combis';
			$t_return['message'] = $this->coo_language_manager->get_text('combi_saved');
            $coo_combis->set_keys(array('products_properties_combis_id' => false));
        } 
        else
        {
            $t_return['combis_exists'] = true;
            $t_return['action'] = 'update_combis';
			$t_return['message'] = $this->coo_language_manager->get_text('combi_saved');
            $coo_combis->set_keys(array('products_properties_combis_id' => $products_properties_combis_id));
        }

        $coo_combis->set_data_value('products_id', (int)$p_data_array['products_id']);
        $coo_combis->set_data_value('sort_order', (int)$p_data_array['sort_order']);
        $coo_combis->set_data_value('combi_model', xtc_db_input(trim($p_data_array['combi_model'])));
        $coo_combis->set_data_value('combi_ean', xtc_db_input(trim($p_data_array['combi_ean'])));
        $coo_combis->set_data_value('combi_quantity', (double)$p_data_array['combi_quantity']);
        $coo_combis->set_data_value('combi_weight', (double)$p_data_array['combi_weight']);
        $coo_combis->set_data_value('combi_price_type', xtc_db_input(trim($p_data_array['combi_price_type'])));
        $coo_combis->set_data_value('vpe_value', (double)$p_data_array['vpe_value']);
        $coo_combis->set_data_value('products_vpe_id', (int)$p_data_array['products_vpe_id']);
        $coo_combis->set_data_value('combi_shipping_status_id', (int)$p_data_array['combi_shipping_status_id']);
		
		$c_combi_price = clean_numeric_input($p_data_array['combi_price']);
		if($p_data_array['combi_price_type'] == 'calc')
		{            
			// get all properties values prices
			$c_combi_price = $this->get_combis_values_total_price($p_data_array['properties_values']);

		}

	    if(($p_data_array['combi_price_type'] == 'fix' && $p_remove_taxes) || $p_data_array['combi_price_type'] == 'calc')
	    {
			// get products tax class id
			$t_query = 'SELECT
							products_tax_class_id
						FROM
							products
						WHERE
							products_id = "' . (int)$p_data_array['products_id'] . '"';
			$t_result = xtc_db_query($t_query);
			$t_row = xtc_db_fetch_array($t_result);
			$t_products_tax_class_id = $t_row['products_tax_class_id'];
		
			// convert total price in netto
			$c_combi_price = $this->v_coo_xtc_price->xtcRemoveTax($c_combi_price, $this->v_coo_xtc_price->TAX[$t_products_tax_class_id]);
		}
		$coo_combis->set_data_value('combi_price', clean_numeric_input($c_combi_price));
        
        if(empty($p_data_array['delete_image']) == false)
        {
            $t_target_path	= DIR_FS_CATALOG_IMAGES.'product_images/properties_combis_images/';
            $t_filename		= $p_data_array['delete_image'];

            if(file_exists($t_target_path.$t_filename))
            {
                #delete file
                unlink($t_target_path.$t_filename);
            }
            #clear filename
            $coo_combis->set_data_value('combi_image', '');
        }

        if(empty($p_data_array['combi_image']) == false)
        {
            $coo_combis->set_data_value('combi_image', xtc_db_input(trim($p_data_array['combi_image'])));
        }

        $t_insert_id = $coo_combis->save_body_data();
        if($t_insert_id > 0) $products_properties_combis_id = $t_insert_id;

        # remove all maybe existing properties_values in combination before adding new list
        $t_sql = 'DELETE FROM products_properties_combis_values WHERE products_properties_combis_id = "'.$products_properties_combis_id.'"';
        xtc_db_query($t_sql);

        # add selected properties_values to properties combination
        if(count($p_data_array['properties_values']) > 0) $this->add_properties_values_array($products_properties_combis_id, $p_data_array['properties_values']);
        		
        $t_return['combis_id'] = $products_properties_combis_id;
        
        return $t_return;
    }
    
    public function add_properties_values_array($p_properties_combis_id, $p_properties_values_array)
    {
        $c_properties_combis_id = (int)$p_properties_combis_id;
        
        $t_sql = '
            INSERT INTO products_properties_combis_values
            (products_properties_combis_id, properties_values_id) VALUES
        ';
        
        for($i = 0, $total = count($p_properties_values_array); $i < $total; $i++)
        {
            $t_sql .= '('.$c_properties_combis_id.', '.(int)$p_properties_values_array[$i].')';
            if($i < $total-1){
                $t_sql .= ',
                ';
            }
        }
        $t_sql .= ';';
        
        xtc_db_query($t_sql);
        
        return true;
    }
	
	function delete_combis($p_properties_combis_id_array)
	{
		$c_properties_combis_id_array = $p_properties_combis_id_array;
		
		$t_return = array();
		if(count($c_properties_combis_id_array) > 1)
		{
			$t_return['action'] = 'delete multiple combis';
		}
		else
		{
			$t_return['action'] = 'delete one combis';
		}

		foreach($c_properties_combis_id_array AS $t_properties_combis_id){
			$c_properties_combis_id = (int)$t_properties_combis_id;

			# delete products in baskets
			$this->clear_baskets_combis($c_properties_combis_id);

			// delete combi image
			$file_array = glob(DIR_FS_CATALOG_IMAGES.'product_images/properties_combis_images/' . $c_properties_combis_id . '_*');
			if(is_array($file_array) && count($file_array) == 1 && file_exists($file_array[0]))
			{
				#delete image file
				unlink($file_array[0]);
			}
		}

		$t_sql = '
			DELETE 
			FROM
				products_properties_combis
			WHERE
				products_properties_combis_id IN ('.implode(',', $c_properties_combis_id_array).')
        ';
		xtc_db_query($t_sql);
		
		$t_sql = '
			DELETE 
			FROM
				products_properties_combis_values
			WHERE
				products_properties_combis_id IN ('.implode(',', $c_properties_combis_id_array).')
        ';
        xtc_db_query($t_sql);
		
		$t_sql = '
			DELETE 
			FROM
				products_properties_index
			WHERE
				products_properties_combis_id IN ('.implode(',', $c_properties_combis_id_array).')
        ';
        xtc_db_query($t_sql);
		
		return $t_return;
	}
	
	public function delete_all_combis($p_products_id)
	{
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('delete_all_combis: typeof($p_products_id) != integer', E_USER_ERROR);
		
		$t_return = array();
		$t_return['action'] = 'delete all combis from product';
		$t_return['products_id'] = $c_products_id;
		
		$t_limit = 500;
		
		$t_status = true;
		while($t_status)
		{
			$t_sql = '
				SELECT
					products_properties_combis_id 
				FROM
					products_properties_combis
				WHERE
					products_id = ' . $c_products_id . '
				LIMIT ' . $t_limit . '
			';
			$t_result = xtc_db_query($t_sql);
			$t_combis_array = array();
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$t_combis_array[] = $t_row['products_properties_combis_id'];
			}
			if(count($t_combis_array) != 0)
			{
				$this->delete_combis($t_combis_array);
			}
			else
			{
				$t_status = false;
			}
		}
		
		xtc_db_perform('products_properties_admin_select', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		
		return $t_return;
	}
	
	public function clear_baskets_combis($p_combis_id)
	{
		$c_combis_id = (int)$p_combis_id;
		
		$t_sql = 'DELETE FROM customers_basket WHERE products_id LIKE "%x'.$c_combis_id.'"';
		xtc_db_query($t_sql);
	}
    
    public function save_combis_settings($p_data_array)
    {
        $c_data_array = $p_data_array;
        if(empty($c_data_array)) trigger_error('save_combis_settings: typeof($p_data_array) is empty', E_USER_ERROR);
        
        $c_data_array['products_id'] = (int)$p_data_array['products_id'];
        if(empty($c_data_array['products_id'])) trigger_error('save_combis_settings: typeof($p_data_array["products_id"]) != integer', E_USER_ERROR);   
        
        $coo_product = MainFactory::create_object('GMDataObject', array('products'));
        $coo_product->set_keys(array('products_id' => $c_data_array['products_id']));
        
        # save basic data
        $coo_product->set_data_value('properties_dropdown_mode', xtc_db_input(trim($c_data_array['properties_dropdown_mode'])));
        $coo_product->set_data_value('properties_show_price', xtc_db_input(trim($c_data_array['properties_show_price'])));
        $coo_product->set_data_value('use_properties_combis_weight', (int)$c_data_array['use_properties_combis_weight']);
        $coo_product->set_data_value('use_properties_combis_quantity', (int)$c_data_array['use_properties_combis_quantity']);
        $coo_product->set_data_value('use_properties_combis_shipping_time', (int)$c_data_array['use_properties_combis_shipping_time']);

        $coo_product->save_body_data();
        
        return true;
    }
    
    public function get_admin_select($p_products_id)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_admin_select: typeof($p_products_id) != integer', E_USER_ERROR);    
        
        $coo_output_array = array();

        $coo_data_group = new GMDataObjectGroup('products_properties_admin_select', array('products_id' => $c_products_id));
		$coo_data_array = $coo_data_group->get_data_objects_array();
		
		for($i=0; $i<sizeof($coo_data_array); $i++)
		{
			$t_properties_id = $coo_data_array[$i]->get_data_value('properties_id');
			$t_properties_values_id = $coo_data_array[$i]->get_data_value('properties_values_id');

			if(array_key_exists($t_properties_id, $coo_output_array) == false) $coo_output_array[$t_properties_id] = array();
			
            $coo_output_array[$t_properties_id][] = $t_properties_values_id;
		}
		return $coo_output_array;
    }
    
    public function get_admin_select_detailed($p_products_id)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_admin_select_detailed: typeof($p_products_id) != integer', E_USER_ERROR);
        
        $c_language_id = (int)$_SESSION['languages_id']; 
        
        $coo_output_array = array();

		$t_sql = '
			SELECT
				pd.properties_id,
				pd.properties_name,
				pd.properties_admin_name,
				pvd.properties_values_id,
				pvd.values_name
			FROM
				products_properties_admin_select AS ppas
					LEFT JOIN properties AS p USING (properties_id)
					LEFT JOIN properties_description AS pd USING (properties_id)
					LEFT JOIN properties_values AS pv USING (properties_values_id)
					LEFT JOIN properties_values_description AS pvd ON (pv.properties_values_id = pvd.properties_values_id)
			WHERE
				ppas.products_id = "'.$c_products_id.'" AND
				pd.language_id = "'.$c_language_id.'" AND
				pvd.language_id = "'.$c_language_id.'"
			GROUP BY
				pvd.properties_values_id
			ORDER BY
				p.sort_order,
				p.properties_id,
				pv.sort_order,
				pv.properties_values_id
		';
		$t_result = xtc_db_query($t_sql);

		while(($t_row = xtc_db_fetch_array($t_result) ))
		{
			$t_properties_id = $t_row['properties_id'];
			$t_properties_name = $t_row['properties_name'];
			$t_properties_admin_name = $t_row['properties_admin_name'];
			$t_properties_values_id = $t_row['properties_values_id'];
			$t_properties_values_name = $t_row['values_name'];

			if(is_array($coo_output_array[$t_properties_id]) == false) $coo_output_array[$t_properties_id] = array(
                                                                                                                                'properties_name' => $t_properties_name,
                                                                                                                                'properties_admin_name' => $t_properties_admin_name,
                                                                                                                    'properties_values' => array()
                                                                                                                    );
            $coo_output_array[$t_properties_id]['properties_values'] = array_merge(
                                                                                    $coo_output_array[$t_properties_id]['properties_values'],
                                                                                                    array('pv_id-'.$t_properties_values_id => $t_properties_values_name)
                                                                                            );
		}
		return $coo_output_array;
    }
    
    public function save_admin_select($p_products_id, $p_properties_values_ids_array)
    {
        /*
        $p_properties_value_ids_array = array(
                '5' => array(48, 56, 64),
                '6' => array(68, 69, 74),
                '8' => array(78, 76, 84)
                );
        */
        
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('save_admin_select: typeof($p_products_id) != integer', E_USER_ERROR);    
        
        $c_properties_values_ids_array = $p_properties_values_ids_array;
        if(empty($c_properties_values_ids_array)) trigger_error('save_admin_select: typeof($p_properties_values_ids_array) is empty', E_USER_ERROR);

        # remove all maybe existing selects before adding new list
        $coo_select = new GMDataObject('products_properties_admin_select');
        $coo_select->set_keys(array('products_id' => $c_products_id));
        $coo_select->delete();

        foreach($c_properties_values_ids_array as $t_properties_id => $t_values_ids_array)
        {
            foreach($t_values_ids_array as $t_values_id)
            {
                $coo_select = new GMDataObject('products_properties_admin_select');

                $coo_select->set_data_value('products_id', $p_products_id);
                $coo_select->set_data_value('properties_id', $t_properties_id);
                $coo_select->set_data_value('properties_values_id', $t_values_id);

                $coo_select->save_body_data();
                unset($coo_select);
            }
        }
    }
    
    public function autobuild_combis($p_products_id, $p_language_id, $p_properties_values_ids_array, $p_actual_index)
    {	
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('autobuild_combis: typeof($p_products_id) != integer', E_USER_ERROR);   
        
        /* parameter sample
        $p_properties_value_ids_array = array(
                '5' => array(48, 56, 64),
                '6' => array(68, 69, 74),
                '8' => array(78, 76, 84)
                );*/
        # find highest given sort_order
        $coo_data_group = MainFactory::create_object('GMDataObjectGroup', array('products_properties_combis', array('products_id' => $p_products_id), array('sort_order DESC') ));
        $t_data_array = $coo_data_group->get_data_objects_array();
        
        # set start sort_order
        if(sizeof($t_data_array) == 0) {
            $t_sort_order = 1;
        } else {
            $t_sort_order = $t_data_array[0]->get_data_value('sort_order') + 1;
        }
        # get all possible combis
        $t_all_combis_array = $this->get_combined_ids($p_properties_values_ids_array, $p_actual_index);
        
        $time_start = microtime(true);
        $t_last_index = 0;
        
        $combis_defaults = $this->get_combis_defaults($c_products_id, false);
        
        if(trim($combis_defaults['combi_price_type']) == '')
        {
            $combis_defaults['combi_price_type'] = 'calc';
        }
        
        # save combis and collect combi_ids
        foreach($t_all_combis_array as $key => $t_combis_item_array)
        {
            # build combi_model by used properties_models
            $t_combi_model = $this->get_composed_combi_model($t_combis_item_array);
            
            $t_data_array['products_id'] = $c_products_id;
            $t_data_array['sort_order'] = $t_sort_order;
            $t_data_array['combi_model'] = $t_combi_model;
            $t_data_array['properties_values'] = $t_combis_item_array;
            $t_data_array['language_id'] = $p_language_id;
            $t_data_array['combi_ean'] = $combis_defaults['combi_ean'];
            $t_data_array['combi_quantity'] = $combis_defaults['combi_quantity'];
            $t_data_array['combi_shipping_status_id'] = $combis_defaults['combi_shipping_status_id'];
            $t_data_array['combi_weight'] = $combis_defaults['combi_weight'];
            $t_data_array['combi_price_type'] = $combis_defaults['combi_price_type'];
            $t_data_array['combi_price'] = $combis_defaults['combi_price'];
            $t_data_array['products_vpe_id'] = $combis_defaults['products_vpe_id'];
            $t_data_array['vpe_value'] = $combis_defaults['vpe_value'];
            
            $this->save_combis($t_data_array, true, false);
            $t_sort_order++;
            
            if(is_integer($key/100))
            {
                $time_actual = microtime(true);
                if((int)($time_actual - $time_start) > 10)
                {
                    $t_last_index = $key+1;
                    break;
                }
            }
        }
        
        return $t_last_index;
    }
    
    public function get_combined_ids($p_id_array, $p_index = 0)
    {
        $c_index = (int)$p_index;
        
        $t_output_combis_array = array();
        $t_combis_count = 1;
        
        // get combis count
        foreach($p_id_array AS $properties_key => $properties_values)
        {
            $t_combis_count = $t_combis_count * count($properties_values);
        }
        
        //get propertie multiplicators
        $t_modulos = array();
        $t_multi = 1;
        foreach($p_id_array AS $properties_key => $properties_values)
        {
            $t_multi = $t_multi * count($properties_values);
            $t_modulos[$properties_key] = $t_combis_count / $t_multi;
        }
        
        
        for($i = $c_index; $i < $t_combis_count; ++$i)
        {
            foreach($p_id_array AS $properties_key => $properties_values)
            {       
                $tmp_index = 0;
                if($i != 0)
                {
                    $tmp = floor($i / $t_modulos[$properties_key]);
                    if($tmp != 0)
                    {
                        $tmp_index = $tmp % count($properties_values);
                    }
                }
                $t_output_combis_array[$i][] = $p_id_array[$properties_key][$tmp_index];
            }
        }
        
        return $t_output_combis_array;
    }
    
    public function get_composed_combi_model($p_properties_values_ids_array)
    {
	    $model = array();
	    $i = 0;
	    
        # get sort_order and value_model of given value_ids
        foreach($p_properties_values_ids_array as $t_values_key => $t_values_id)
        {
            $t_sql = '
                SELECT
                p.properties_id AS properties_id,
                p.sort_order AS sort_order,
                pv.value_model AS value_model
                FROM
                properties AS p LEFT JOIN properties_values AS pv USING (properties_id)
                WHERE
                pv.properties_values_id = "'.(int)$t_values_id.'"
                ORDER BY
                p.sort_order ASC,
                p.properties_id ASC
            ';
            $t_result = xtc_db_query($t_sql);
            $t_data = xtc_db_fetch_array($t_result);
	
	        if(trim($t_data['value_model']) !== '')
	        {
		        $model[$t_data['sort_order'] . '-' . $i] = $t_data['value_model'];
	        }
	        
	        $i++;
        }
	    
	    ksort($model);
	    $t_model_output = implode('-', $model);

        return $t_model_output;
    }
    
    public function get_combis_defaults($p_products_id, $p_add_tax = true)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis_defaults: typeof($p_products_id) != integer', E_USER_ERROR); 
        
        $t_defaults = array();
        
        $t_sql = '
            SELECT
                *
            FROM
                products_properties_combis_defaults
            WHERE
                products_id ='.$c_products_id.'
            ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_defaults = xtc_db_fetch_array($t_result);
            
            if(PRICE_IS_BRUTTO == 'true' && $p_add_tax)
            {
                // get products_tax_class_id
                $coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id)));
                $t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id');

                $t_defaults['combi_price'] = $this->v_coo_xtc_price->xtcAddTax($t_defaults['combi_price'], $this->v_coo_xtc_price->TAX[$t_products_tax_class_id]);
            }
        }
        else
        {
            $t_defaults['combi_ean'] = '';
            $t_defaults['combi_quantity'] = '';
            $t_defaults['combi_shipping_status_id'] = 0;
            $t_defaults['combi_weight'] = '';
            $t_defaults['combi_price_type'] = 'calc';
            $t_defaults['combi_price'] = '';
            $t_defaults['products_vpe_id'] = 0;
            $t_defaults['vpe_value'] = '';
        }
        return $t_defaults;
    }
    
    public function save_combis_defaults($p_data_array)
    {
        if(is_array(!$p_data_array)) trigger_error('save_combis_defaults: typeof($p_data_array) is not an array', E_USER_ERROR); 
        
        $c_products_id = (int)$p_data_array['products_id'];
		$c_combi_price = clean_numeric_input($p_data_array['combi_price']);
		
        if(empty($c_products_id)) trigger_error('save_combis_defaults: typeof($p_data_array["products_id"]) != integer', E_USER_ERROR); 
        
        $t_return = array();
        $t_return['action'] = 'save_combis_defaults';
        
        $coo_gm_data_object = new GMDataObject('products_properties_combis_defaults');
                                
        $t_sql = '
            SELECT
                *
            FROM
                products_properties_combis_defaults
            WHERE
                products_id ='.$c_products_id.'
            ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_row = xtc_db_fetch_array($t_result);
            $coo_gm_data_object->set_keys(array('products_properties_combis_defaults_id' => $t_row['products_properties_combis_defaults_id']));
        }
        else
        {
            $coo_gm_data_object->set_keys(array('products_properties_combis_defaults_id' => false));
        }
        
        $t_default_price = 0;
        if(PRICE_IS_BRUTTO == 'true')
        {
            // get products tax class id
            $coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id)));
            $t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id');

            // convert total price in netto
            $t_default_price = $this->v_coo_xtc_price->xtcRemoveTax($c_combi_price, $this->v_coo_xtc_price->TAX[$t_products_tax_class_id]);
        }
        else
        {
            $t_default_price = $c_combi_price;
        }
        $coo_gm_data_object->set_data_value('products_id', $c_products_id);
        $coo_gm_data_object->set_data_value('combi_ean', xtc_db_input($p_data_array['combi_ean']));
        $coo_gm_data_object->set_data_value('combi_quantity', (double)$p_data_array['combi_quantity']);
        $coo_gm_data_object->set_data_value('combi_shipping_status_id', (int)$p_data_array['shipping_status_id']);
        $coo_gm_data_object->set_data_value('combi_weight', (double)$p_data_array['combi_weight']);
        $coo_gm_data_object->set_data_value('combi_price_type', xtc_db_input($p_data_array['combi_price_type']));
        $coo_gm_data_object->set_data_value('combi_price', $t_default_price);
        $coo_gm_data_object->set_data_value('products_vpe_id', (int)$p_data_array['products_vpe_id']);
        $coo_gm_data_object->set_data_value('vpe_value', (double)$p_data_array['vpe_value']);
        
        $coo_gm_data_object->save_body_data();
        
        $t_return['products_id'] = $c_products_id;
        
        return $t_return;
    }
    
    public function get_combis_values_total_price($p_combis_values_ids_array)
    {
        if(!is_array($p_combis_values_ids_array)) trigger_error('get_combis_values_total_price: typeof($p_combis_values_ids_array) is not an array', E_USER_ERROR);                
        
        $t_combis_values_total_price = 0;
        
        $t_sql = '
            SELECT
                SUM(value_price) AS combis_values_total_price
            FROM
                properties_values
            WHERE
                properties_values_id IN ('.implode(',', $p_combis_values_ids_array).')
        ';
        $t_result = xtc_db_query($t_sql);
        
        if(xtc_db_num_rows($t_result) == 1)
        {
            $t_row = xtc_db_fetch_array($t_result);
            $t_combis_values_total_price = $t_row['combis_values_total_price'];
        }
        
        return $t_combis_values_total_price;
    }
	
	function get_properties_dropdown_mode($p_products_id){
		
		$t_products_id = (int)$p_products_id;
		
		$t_sql = '
			SELECT
				properties_dropdown_mode
			FROM
				products
			WHERE
				products_id = "'.$t_products_id.'"
		';
		$t_result = xtc_db_query($t_sql);

		$t_row = xtc_db_fetch_array($t_result);
		
		$t_dropdown_mode_value = $t_row['properties_dropdown_mode'];
		
		return $t_dropdown_mode_value;
	}
	
	function get_properties_price_show($p_products_id){
		
		$t_products_id = (int)$p_products_id;
		
		$t_sql = '
			SELECT
				properties_show_price
			FROM
				products
			WHERE
				products_id = "'.$t_products_id.'"
		';
		$t_result = xtc_db_query($t_sql);

		$t_row = xtc_db_fetch_array($t_result);
		
		$t_price_show_value = $t_row['properties_show_price'];
		
		return $t_price_show_value;
	}
	
	function get_use_properties_combis_weight($p_products_id){
		
		$t_products_id = (int)$p_products_id;
		
		$t_sql = '
			SELECT
				use_properties_combis_weight
			FROM
				products
			WHERE
				products_id = "'.$t_products_id.'"
		';
		$t_result = xtc_db_query($t_sql);

		$t_row = xtc_db_fetch_array($t_result);
		
		$t_use_properties_combis_weight = $t_row['use_properties_combis_weight'];
		
		return $t_use_properties_combis_weight;
	}
	
	function get_use_properties_combis_quantity($p_products_id){
		
		$t_products_id = (int)$p_products_id;
		
		$t_sql = '
			SELECT
				use_properties_combis_quantity
			FROM
				products
			WHERE
				products_id = "'.$t_products_id.'"
		';
		$t_result = xtc_db_query($t_sql);

		$t_row = xtc_db_fetch_array($t_result);
		
		$t_use_properties_combis_quantity = $t_row['use_properties_combis_quantity'];
		
		return $t_use_properties_combis_quantity;
	}
	
	function get_use_properties_combis_shipping_time($p_products_id){
		
		$t_products_id = (int)$p_products_id;
		
		$t_sql = '
			SELECT
				use_properties_combis_shipping_time
			FROM
				products
			WHERE
				products_id = "'.$t_products_id.'"
		';
		$t_result = xtc_db_query($t_sql);

		$t_row = xtc_db_fetch_array($t_result);
		
		$t_use_properties_combis_shipping_time = $t_row['use_properties_combis_shipping_time'];
		
		return $t_use_properties_combis_shipping_time;
	}
	
	function reset_combis_sort_order($p_products_id)
	{
		$coo_properties_combis = new GMDataObjectGroup('products_properties_combis', array('products_id' => $p_products_id));

		$t_properties_combis_array = $coo_properties_combis->get_data_objects_array();

		if(sizeof($t_properties_combis_array) > 0)
		{
			foreach($t_properties_combis_array AS $t_properties_combi)
			{
				$t_combi_id = $t_properties_combi->get_data_value('products_properties_combis_id');
				$t_combi_values_array = array();

				$t_sql = "SELECT 
								pv.sort_order
						  FROM 
								products_properties_index AS ppi
						  LEFT JOIN
								properties AS p ON ppi.properties_id = p.properties_id
						  LEFT JOIN
								properties_values AS pv ON ppi.properties_values_id = pv.properties_values_id
						  WHERE
								ppi.products_properties_combis_id = $t_combi_id 
								AND ppi.language_id = 2
						  ORDER BY
								p.sort_order ASC,
								p.properties_id ASC,
								pv.sort_order ASC,
								pv.properties_values_id ASC";

				$t_result = xtc_db_query($t_sql);
				while($t_row = xtc_db_fetch_array($t_result))
				{
					$t_combi_values_array[] = $t_row['sort_order'];                     
				}

				$t_combi_values_string = implode(".", $t_combi_values_array);

				$start_array[] = array("combis_id" => $t_combi_id, "sort_string" => $t_combi_values_string);
			}
		}     

		usort($start_array, array($this, "compare_combis_sort_order"));

		foreach($start_array AS $key => $combi)
		{               
			$coo_properties_do = new GMDataObject('products_properties_combis');

			$coo_properties_do->set_keys(array('products_properties_combis_id' => $combi['combis_id']));

			$coo_properties_do->set_data_value('sort_order', $key);

			$coo_properties_do->save_body_data();
		}
		return true;
	}
        
	function compare_combis_sort_order($a, $b)
	{
		return strnatcmp($a['sort_string'], $b['sort_string']);
	}


    /**
     * Get combi ids by property value id
     *
     * Returns an array of found combi ids, depending
     * on the given property value id.
     *
     * @param int $p_property_value_id
     *
     * @return int array
     */
    public function get_combi_ids_by_property_value_id($p_property_value_id)
    {
        $c_property_value_id = (int)$p_property_value_id;
        if(empty($c_property_value_id))
        {
            trigger_error('get_combi_ids_by_property_value_id: typeof($p_property_value_id) != integer', E_USER_ERROR);
        }

        $t_sql = '
            SELECT
                products_properties_combis_id
            FROM
                products_properties_combis_values
            WHERE
                properties_values_id =' . $c_property_value_id;

        $t_result = xtc_db_query($t_sql);

        $t_combi_ids_array = array();
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $t_combi_ids_array[] = $t_row['products_properties_combis_id'];
        }

        return $t_combi_ids_array;
    }


    /**
     * Get product id by combi id
     *
     * Returns the product id, depending on
     * the given property combi id.
     *
     * @param int $p_combi_id
     *
     * @return int $t_product_id
     */
    public function get_product_id_by_combi_id($p_combi_id)
    {
        $c_combi_id = (int)$p_combi_id;
        if(empty($c_combi_id))
        {
            trigger_error('get_product_id_by_combi_id: typeof($p_combi_id) != integer', E_USER_ERROR);
        }

        $t_sql = '
            SELECT
                products_id
            FROM
                products_properties_combis
            WHERE
                products_properties_combis_id = ' . $c_combi_id;

        $t_result     = xtc_db_query($t_sql);
        if(xtc_db_num_rows($t_result) == 0)
        {
            return 0;
        }
        $t_product_id = xtc_db_fetch_array($t_result);

        return $t_product_id['products_id'];
    }
}