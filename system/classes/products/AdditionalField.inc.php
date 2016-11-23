<?php
/* --------------------------------------------------------------
   AdditionalField.inc.php 2015-03-24 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AdditionalField
{
	protected $v_additional_field_id;
	protected $v_field_key;
	protected $v_item_type;
	protected $v_name_array;
	protected $v_multilingual;

	protected $v_field_value_array;

	public function __construct($p_additional_field_id = 0, $p_item_id = 0)
	{
		if (!empty($p_additional_field_id))
		{
			$this->load_field_data($p_additional_field_id);
		}
		
		if (!empty($p_additional_field_id) && !empty($p_item_id))
		{
			$this->load_field_value_data($p_additional_field_id, $p_item_id);
		}
	}
	
	protected function load_field_data($p_additional_field_id)
	{
		$t_data_array = array();
		$this->v_name_array = array();
		$c_additional_field_id = (int) $p_additional_field_id;
		
		$t_sql = "
			SELECT
				af.field_key,
				af.item_type,
				af.multilingual,
				afd.language_id,
				afd.name
			FROM
				additional_fields af,
				additional_field_descriptions afd
			WHERE
				af.additional_field_id = afd.additional_field_id AND
				af.additional_field_id = " . $c_additional_field_id;
		$t_result = xtc_db_query($t_sql);
		
		if ($t_row = xtc_db_fetch_array($t_result))
		{
			$t_data_array['additional_field_id'] = $c_additional_field_id;
			$t_data_array['field_key'] = $t_row['field_key'];
			$t_data_array['item_type'] = $t_row['item_type'];
			$t_data_array['multilingual'] = $t_row['multilingual'];
			$t_data_array['name'] = array();
			$t_data_array['name'][$t_row['language_id']] = $t_row['name'];
		}
		
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$t_data_array['name'][$t_row['language_id']] = $t_row['name'];
		}
		
		$this->set_data($t_data_array);
	}
	
	public function load_field_value_data($p_additional_field_id, $p_item_id = 0)
	{
		$this->v_field_value_array = array();
		$c_additional_field_id = (int) $p_additional_field_id;
		$c_item_id = (int) $p_item_id;
		$t_sql = "
			SELECT
				afv.additional_field_value_id
			FROM
				additional_field_values afv
			WHERE
				" . (!empty($c_item_id) ? "afv.item_id = " . $c_item_id . " AND" : "") . "
				afv.additional_field_id = " . $c_additional_field_id;
		$t_result = xtc_db_query($t_sql);
		
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$this->v_field_value_array[] = MainFactory::create_object('AdditionalFieldValue', array($t_row['additional_field_value_id']));
		}
	}
	
	public function set_data($p_data_array)
	{
		if (empty($p_data_array) || !is_array($p_data_array))
		{
			return;
		}
		
		$t_prefix = 'v_';
		foreach ($p_data_array as $t_key => $t_value)
		{
			$t_appendix = '';
			if (is_array($t_value))
			{
				$t_appendix = '_array';
			}
			$this->{$t_prefix . $t_key . $t_appendix} = $t_value;
		}
	}
	
	public function save($p_replace = true)
	{
		$t_success = true;
		
		$t_replace = $p_replace;
		if (empty($this->v_additional_field_id))
		{
			$t_replace = false;
		}
		
		$coo_additional_field_object = MainFactory::create_object('GMDataObject', array('additional_fields'));
		$coo_additional_field_description_object = MainFactory::create_object('GMDataObject', array('additional_field_descriptions'));
		
		$t_additional_field_array = array(
			'field_key' => $this->v_field_key,
			'item_type' => $this->v_item_type,
			'multilingual' => $this->v_multilingual ? 1 : 0
		);
		
		if ($t_replace)
		{
			$t_additional_field_array['additional_field_id'] = $this->v_additional_field_id;
			$coo_additional_field_object->set_keys(array('additional_field_id' => $this->v_additional_field_id));
		}
		
		$coo_additional_field_object->v_table_content = $t_additional_field_array;
		$t_insert_id = $coo_additional_field_object->save_body_data($t_replace);
		if (!empty($t_insert_id) && is_numeric($t_insert_id))
		{
			$this->v_additional_field_id = $t_insert_id;
		}
		$t_success &= (boolean) $t_insert_id;
		
		foreach ($this->v_name_array as $t_language_id => $t_name)
		{
			$t_additional_field_description_array = array(
				'additional_field_id' => $this->v_additional_field_id,
				'language_id' => $t_language_id,
				'name' => $t_name
			);
			
			$coo_additional_field_description_object->set_keys(array('additional_field_id' => $this->v_additional_field_id, 'language_id' => $t_language_id));
			$coo_additional_field_description_object->v_table_content = $t_additional_field_description_array;
			$t_success &= (boolean) $coo_additional_field_description_object->save_body_data(true);

			$t_sql = 'REPLACE INTO `language_phrases_edited` 
						SET 
							`language_id` = ' . $t_language_id . ', 
							`section_name` = "export_schemes_variables",
							`phrase_name` = "p_additional_field#' . $this->v_additional_field_id . '",
							`phrase_text` = "' . addslashes($t_name) . '"';
			xtc_db_query($t_sql);

			$t_sql = 'REPLACE INTO `language_phrases_cache` 
						SET 
							`language_id` = ' . $t_language_id . ', 
							`section_name` = "export_schemes_variables",
							`phrase_name` = "p_additional_field#' . $this->v_additional_field_id . '",
							`phrase_text` = "' . addslashes($t_name) . '",
							`source` = "language_phrases_edited"';
			xtc_db_query($t_sql);
		}
		
		if (is_array($this->v_field_value_array))
		{
			foreach ($this->v_field_value_array as $coo_field_value)
			{
				$coo_field_value->set_additional_field_id($this->v_additional_field_id);
				$t_success &= $coo_field_value->save($t_replace);
			}
		}
		
		return $t_success;
	}
	
	public function delete()
	{
		$t_success = true;
		
		$coo_additional_field_object = MainFactory::create_object('GMDataObject', array('additional_fields'));
		$coo_additional_field_description_object = MainFactory::create_object('GMDataObject', array('additional_field_descriptions'));
		
		$coo_additional_field_object->set_keys(array('additional_field_id' => $this->v_additional_field_id) );
		$coo_additional_field_description_object->set_keys(array('additional_field_id' => $this->v_additional_field_id));
		
		$t_sql = 'DELETE FROM `language_phrases_cache` WHERE `phrase_name` LIKE "p_additional_field#' . $this->v_additional_field_id . '"';
		xtc_db_query($t_sql);
		
		$t_sql = 'DELETE FROM `language_phrases_edited` WHERE `phrase_name` LIKE "p_additional_field#' . $this->v_additional_field_id . '"';
		xtc_db_query($t_sql);
		
		$t_success &= $this->delete_all_field_values();
		$t_success &= $coo_additional_field_object->delete();
		$t_success &= $coo_additional_field_description_object->delete();
		
		return $t_success;
	}
	
	public function delete_all_field_values()
	{
		$t_success = true;
		
		$this->load_field_value_data($this->v_additional_field_id);
		foreach ($this->v_field_value_array as $coo_field_value)
		{
			$t_success &= $coo_field_value->delete();
		}
		
		return $t_success;
	}
	
	public function delete_all_field_values_by_item_id($p_item_id)
	{
		$t_success = true;
		
		$this->load_field_value_data($this->v_additional_field_id, $p_item_id);
		foreach ($this->v_field_value_array as $coo_field_value)
		{
			$t_success &= $coo_field_value->delete();
		}
		
		return $t_success;
	}
	
	
	public function get_additional_field_id()
	{
		return $this->v_additional_field_id;
	}
	
	public function get_field_key()
	{
		return $this->v_field_key;
	}
	
	public function get_item_type()
	{
		return $this->v_item_type;
	}
	
	public function get_name_array()
	{
		return $this->v_name_array;
	}
	
	public function get_field_value_array()
	{
		return $this->v_field_value_array;
	}
	
	public function get_field_value($p_item_id)
	{
		foreach($this->v_field_value_array AS $coo_field_value)
		{
			if($coo_field_value->get_item_id() == $p_item_id)
			{
				return $coo_field_value;
			}
		}
		
		return false;
	}
	
	public function get_multilingual()
	{
		return $this->v_multilingual;
	}

	public function set_additional_field_id($p_additional_field_id)
	{
		$this->v_additional_field_id = $p_additional_field_id;
	}
	
	public function set_field_key($p_field_key)
	{
		$this->v_field_key = $p_field_key;
	}
	
	public function set_item_type($p_item_type)
	{
		$this->v_item_type = $p_item_type;
	}
	
	public function set_name_array($p_name_array)
	{
		$this->v_name_array = $p_name_array;
	}
	
	public function set_field_value_array($p_field_value_array = array())
	{
		$this->v_field_value_array = $p_field_value_array;
	}
	
	public function set_multilingual($p_multilingual)
	{
		$this->v_multilingual = $p_multilingual;
	}
	
	public function is_multilingual()
	{
		if($this->get_multilingual() == '1') return true;
		
		return false;
	}
	
	public function add_field_value($p_field_value)
	{
		if (!is_array($this->v_field_value_array))
		{
			$this->v_field_value_array = array();
		}
		$this->v_field_value_array[] = $p_field_value;
	}
}