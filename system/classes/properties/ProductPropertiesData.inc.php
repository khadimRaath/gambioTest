<?php
/* --------------------------------------------------------------
   ProductPropertiesData.inc.php 2014-10-21 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class ProductPropertiesData 
{
	var $v_products_id = 0;
	var $v_language_id = 0;
	
	var $v_properties_struct 		= array();
	
	function ProductPropertiesData($p_products_id, $p_language_id) 
	{
		$this->v_products_id = (int)$p_products_id;
		$this->v_language_id = (int)$p_language_id;
		
		$this->init_properties_struct($this->v_products_id, $this->v_language_id);
	}
	
	function init_properties_struct($p_products_id, $p_language_id)
	{
		$c_products_id = (int)$p_products_id;
		$c_language_id = (int)$p_language_id;
		
		$t_available_properties_array = array();
		
		$t_available_values = array();
		$t_query = '
			SELECT DISTINCT
				properties_values_id 
			FROM 
				products_properties_combis_values as ppcv
			LEFT JOIN 
				products_properties_combis USING (products_properties_combis_id)
			WHERE 
				products_id = "' . $c_products_id . '"
		';
		$t_result = xtc_db_query($t_query);
		
		# init properties_names
		while(($t_row = xtc_db_fetch_array($t_result) ))
		{
			$t_available_values[] = $t_row['properties_values_id'];
		}
		
		$coo_properties_struct_supplier = MainFactory::create_object('ProductPropertiesStructSupplier');
		$t_properties_array = $coo_properties_struct_supplier->get_all_properties_by_products_id($c_products_id);
		
		if(is_array($t_properties_array) && count($t_properties_array) > 0)
		{
			foreach($t_properties_array as $t_propertie)
			{
				if(is_array($t_propertie['properties_values']) && count($t_propertie['properties_values']) > 0)
				{
					foreach($t_propertie['properties_values'] as $t_propertie_value)
					{
						//print_r($t_available_values);
						if(in_array($t_propertie_value['properties_values_id'], $t_available_values))
						{
							// add propertie
							if(array_key_exists($t_propertie['properties_id'], $t_available_properties_array) == false)
							{
								$t_available_properties_array[$t_propertie['properties_id']] = array(
																'properties_id'		=> $t_propertie['properties_id'],
																'properties_name' 	=> $t_propertie['properties_names'][$c_language_id]['properties_name'],
																'values_array'		=> array()
															);
							}
							$t_available_properties_array[$t_propertie['properties_id']]['values_array'][$t_propertie_value['properties_values_id']] = array(
                                                                                                                                    'properties_values_id' => $t_propertie_value['properties_values_id'],
                                                                                                                                    'values_name' => $t_propertie_value['values_names'][$c_language_id]['values_name'],
                                                                                                                                    'values_price' => $t_propertie_value['value_price_formatted']
                                                                                                                            );
						}
					}
				}
			}
		}
		
		$this->v_properties_struct = $t_available_properties_array;
	}
	
	function get_properties_struct()
	{
		return $this->v_properties_struct;
	}
}