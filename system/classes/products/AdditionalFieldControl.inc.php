<?php
/* --------------------------------------------------------------
   AdditionalFieldControl.inc.php 2013-07-10 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AdditionalFieldControl
{
	public function __construct()
	{
		
	}
	
	public function get_field_names_by_item_type($p_item_type)
	{
		$c_item_type = xtc_db_input($p_item_type);
		$t_fields_array = array();
		
		$t_sql = "
				SELECT
					additional_field_id
				FROM
					additional_fields
				WHERE
					item_type = '" . $c_item_type . "'";
		
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_fields_array[] = MainFactory::create_object('AdditionalField', array($t_row['additional_field_id']));
		}
		
		return $t_fields_array;
	}
	
	public function get_fields_by_item_id_and_item_type($p_item_id, $p_item_type)
	{
		$t_fields_array = array();
		
		$c_item_type = xtc_db_input($p_item_type);
		$c_item_id = (int)$p_item_id;
		
		$t_sql = "
				SELECT
					additional_field_id
				FROM
					additional_fields
				WHERE
					item_type = '$c_item_type'";
		
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_fields_array[$t_row['additional_field_id']] = MainFactory::create_object('AdditionalField', array($t_row['additional_field_id'], $c_item_id));
		}
		
		return $t_fields_array;
	}
	
	public function import_field_value_by_language_id($p_additional_field_id, $p_item_id, $p_language_id, $p_value)
	{
		$c_additional_field_id = (int)$p_additional_field_id;
		$c_item_id = (int)$p_item_id;
		$c_language_id = (int)$p_language_id;
		
		$t_coo_additional_field = MainFactory::create_object('AdditionalField', array($c_additional_field_id, $c_item_id));
		$t_coo_field_value = $t_coo_additional_field->get_field_value($c_item_id);
		if ($t_coo_field_value == false && !empty($p_value))
		{
			$t_coo_field_value = MainFactory::create_object('AdditionalFieldValue');
			$t_coo_field_value->set_additional_field_id($c_additional_field_id);
			$t_coo_field_value->set_item_id($c_item_id);
		}
		if($t_coo_field_value !== false && get_class($t_coo_field_value) == 'AdditionalFieldValue')
		{
			$t_coo_field_value->set_value($p_value, $c_language_id);
			$t_coo_field_value->save();
		}
	}
	
	public function copy_field_values($p_source_item_id, $p_target_item_id, $p_additional_field_id = 0)
	{
		$t_additional_fields_array = array();
		
		if (empty($p_additional_field_id))
		{
			$t_additional_fields_array = $this->get_fields_by_item_id_and_item_type($p_source_item_id, 'product');
		}
		else
		{
			$t_additional_fields_array[] = MainFactory::create_object('AdditionalField', array($p_additional_field_id, $p_source_item_id));
		}
		
		foreach ($t_additional_fields_array as $coo_additional_field)
		{
			$t_additional_field_values_array = $coo_additional_field->get_field_value_array();
			foreach ($t_additional_field_values_array as $coo_additional_field_value)
			{
				$coo_additional_field_value->set_additional_field_value_id('');
				$coo_additional_field_value->set_item_id($p_target_item_id);
				$coo_additional_field_value->save(false);
			}
		}
	}
}