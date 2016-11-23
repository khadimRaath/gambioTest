<?php
/* --------------------------------------------------------------
   PropertiesStructSupplier.inc.php 2014-01-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesStructSupplier
{
	function PropertiesStructSupplier()
	{
	}
	
	function get_properties_struct($p_properties_id, $p_language_id=false)
	{
		$t_properties_struct_array = array();
		
		$c_properties_id = (int)$p_properties_id;
		$c_language_id = (int)$p_language_id;
		
		# where-key for main-tables data-object
		$t_main_key_array = array('properties_id' => $c_properties_id);
		
		# where-key for description-tables data-object
		$t_descr_key_array = array('properties_id' => $c_properties_id);
		
		# add language_id to key-list, if not empty
		if($c_language_id > 0)
		{
			$t_descr_key_array = array_merge(
									$t_descr_key_array, 
									array('language_id' => $c_language_id)
								);
		}
		
		# get sort_order
		$coo_data = new GMDataObject('properties', $t_main_key_array);
		$t_properties_struct_array['properties_id'] = $c_properties_id;
		$t_properties_struct_array['sort_order'] = $coo_data->get_data_value('sort_order');
		
		# get properties names
		$coo_data = new GMDataObjectGroup('properties_description', $t_descr_key_array, array('language_id'));
		$coo_data_array = $coo_data->get_data_objects_array();
		
		$t_properties_names_array = array();
		$t_language_ids_array = array();
		
		for($i=0; $i<sizeof($coo_data_array); $i++)
		{
			$t_properties_names_array[] = $coo_data_array[$i]->get_data_value('properties_name');
			$t_properties_language_ids_array[] = array("properties_name" => $coo_data_array[$i]->get_data_value('properties_name'),
                                                                    "properties_admin_name" => $coo_data_array[$i]->get_data_value('properties_admin_name'),
                                                                    "language_id" => $coo_data_array[$i]->get_data_value('language_id'));
		}
		$t_properties_struct_array['PROPERTIES_NAMES'] = $t_properties_names_array;
		$t_properties_struct_array['PROPERTIES_LANGUAGE_ARRAY'] = $t_properties_language_ids_array;
		
		return $t_properties_struct_array;
	}
	
	
	function get_properties_values_struct($p_properties_id, $p_language_id=false)
	{
		$t_properties_struct_array = array();
		
		$c_properties_id = (int)$p_properties_id;
		$c_language_id = (int)$p_language_id;
		
		# where-key for main-tables data-object
		$t_main_key_array = array('properties_id' => $c_properties_id);
		
		# get properties_values (sort_order, value_model, etc.)
		$coo_data = new GMDataObjectGroup('properties_values', $t_main_key_array, array('sort_order'));
		$coo_data_array = $coo_data->get_data_objects_array();
		
		$t_properties_values_array = array();
		
		for($i=0; $i<sizeof($coo_data_array); $i++)
		{
			$t_properties_values_array[] = array(
												'properties_values_id' 	=> $coo_data_array[$i]->get_data_value('properties_values_id'),
												'sort_order' 			=> $coo_data_array[$i]->get_data_value('sort_order'),
												'value_model' 			=> $coo_data_array[$i]->get_data_value('value_model'),
												'value_price_type' 		=> $coo_data_array[$i]->get_data_value('value_price_type'),
												'value_price' 			=> $coo_data_array[$i]->get_data_value('value_price'),
												'VALUES_DESCRIPTIONS'	=> array()
											);
		}
		
		# get properties_values_description (name, image, ...)
		for($i=0; $i<sizeof($t_properties_values_array); $i++)
		{
			$t_prop_descr_key_array = array('properties_values_id' => $t_properties_values_array[$i]['properties_values_id']);
			if($c_language_id > 0)
			{
				$t_prop_descr_key_array = array_merge(
												$t_prop_descr_key_array, 
												array('language_id' => $c_language_id)
											);
			}
			$coo_data = new GMDataObjectGroup('properties_values_description', $t_prop_descr_key_array, array('language_id'));
			$coo_data_array = $coo_data->get_data_objects_array();
			
			for($h=0; $h<sizeof($coo_data_array); $h++)
			{
				$t_properties_values_array[$i]['VALUES_DESCRIPTIONS'][] = array(
																		'name' 	=> $coo_data_array[$h]->get_data_value('values_name'),
																		'image' => $coo_data_array[$h]->get_data_value('values_image'),
																		'language_id' => $coo_data_array[$h]->get_data_value('language_id')
																	);
			}
		}
		$t_properties_struct_array['PROPERTIES_VALUES'] = $t_properties_values_array;
		
		return $t_properties_struct_array;
	}
}