<?php
/* --------------------------------------------------------------
   set_additional_field_data.inc.php 2016-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

$t_sql = 'SELECT scheme_id FROM export_schemes WHERE type_id = 1 AND created_by LIKE "gambio"';
$t_result = xtc_db_query($t_sql);
$t_export_scheme_data = xtc_db_fetch_array($t_result);
$t_export_scheme_id = (int)$t_export_scheme_data['scheme_id'];

if(isset($_POST['additional_field_delete_array']))
{
	foreach($_POST['additional_field_delete_array'] as $t_delete_field_id)
	{
		$coo_delete_additional_field = MainFactory::create_object('AdditionalField', array($t_delete_field_id));
		$coo_delete_additional_field->delete();
		
		//Delete CSV scheme fields
		$t_sql = 'DELETE FROM export_scheme_fields WHERE scheme_id = ' . $t_export_scheme_id . ' AND created_by LIKE "gambio" AND field_name LIKE "Zusatzfeld:%[' . (int)$t_delete_field_id . ']"';
		xtc_db_query($t_sql);
	}
}

$t_sql = 'SELECT languages_id, code FROM ' . TABLE_LANGUAGES;
$t_language_result = xtc_db_query($t_sql);
$t_language_code_array = array();
while($t_row = xtc_db_fetch_array($t_language_result))
{
	$t_language_code_array[$t_row['languages_id']] = $t_row['code'];
}

if(isset($_POST['additional_field_names_array']))
{
	foreach($_POST['additional_field_names_array'] as $t_key => $t_field_name_array)
	{
		if(strpos($t_key, 'new') !== false)
		{
			$coo_additional_field = MainFactory::create_object('AdditionalField');
			$coo_additional_field->set_field_key('product-' . md5(time() . rand()));
			$coo_additional_field->set_item_type('product');
			
			$coo_additional_field->set_multilingual($_POST['additional_field_multilingual'][$t_key]);
		}
		else
		{
			$coo_additional_field = MainFactory::create_object('AdditionalField', array($t_key));
		}
		$coo_additional_field->set_name_array($t_field_name_array);
		$coo_additional_field->set_field_value_array();
		
		$coo_additional_field_value = MainFactory::create_object('AdditionalFieldValue');
		$coo_additional_field_value->set_item_id($productId);
		$coo_additional_field_value->set_value_array($_POST['additional_field_values_array'][$t_key]);
		
		$coo_additional_field->add_field_value($coo_additional_field_value);
		
		if(strpos($t_key, 'new') !== false)
		{
			$coo_additional_field->save(false);
			
			//New CSV scheme fields
			$t_csv_scheme_field_data_array = array();
			$t_csv_scheme_field_data_array['scheme_id'] = $t_export_scheme_id;
			$t_csv_scheme_field_data_array['field_content_default'] = '';
			$t_csv_scheme_field_data_array['created_by'] = 'gambio';
			
			if($coo_additional_field->get_multilingual())
			{
				foreach($t_field_name_array as $t_language_id => $t_additional_field_name)
				{
					$coo_csv_field_model = MainFactory::create_object('CSVFieldModel');
					$t_csv_scheme_field_data_array['field_name'] = 'Zusatzfeld: ' . $t_field_name_array[$_SESSION['languages_id']] . '.' . $t_language_code_array[$t_language_id] . ' [' . $coo_additional_field->get_additional_field_id() . ']';
					$t_csv_scheme_field_data_array['field_content'] = '{p_additional_field#' . $coo_additional_field->get_additional_field_id() . '.' . $t_language_code_array[$t_language_id] . '}';
					$coo_csv_field_model->save($t_csv_scheme_field_data_array);
				}
			}
			else
			{
				$coo_csv_field_model = MainFactory::create_object('CSVFieldModel');
				$t_csv_scheme_field_data_array['field_name'] = 'Zusatzfeld: ' . $t_field_name_array[$_SESSION['languages_id']] . ' [' . $coo_additional_field->get_additional_field_id() . ']';
				$t_csv_scheme_field_data_array['field_content'] = '{p_additional_field#' . $coo_additional_field->get_additional_field_id() . '}';
				$coo_csv_field_model->save($t_csv_scheme_field_data_array);
			}
		}
		else
		{
			$coo_additional_field->save();
		}
	}
}