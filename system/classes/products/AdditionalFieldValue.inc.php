<?php
/* --------------------------------------------------------------
   AdditionalFieldValue.inc.php 2014-07-15 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AdditionalFieldValue
{
	protected $v_additional_field_value_id;
	protected $v_additional_field_id;
	protected $v_item_id;
	protected $v_value_array;

	public function __construct($p_additional_field_value_id = 0)
	{
		if (!empty($p_additional_field_value_id))
		{
			$this->load_field_value_data($p_additional_field_value_id);
		}
	}
	
	protected function load_field_value_data($p_additional_field_value_id)
	{
		$t_data_array = array();
		$this->v_value_array = array();
		$c_additional_field_value_id = (int) $p_additional_field_value_id;
		
		$t_sql = "
			SELECT
				afv.additional_field_id,
				afv.item_id,
				afvd.language_id,
				afvd.value
			FROM
				additional_field_values afv,
				additional_field_value_descriptions afvd
			WHERE
				afv.additional_field_value_id = afvd.additional_field_value_id AND
				afv.additional_field_value_id = " . $c_additional_field_value_id;
		$t_result = xtc_db_query($t_sql);
		
		if ($t_row = xtc_db_fetch_array($t_result))
		{
			$t_data_array['additional_field_value_id'] = $c_additional_field_value_id;
			$t_data_array['additional_field_id'] = $t_row['additional_field_id'];
			$t_data_array['item_id'] = $t_row['item_id'];
			$t_data_array['value'] = array();
			$t_data_array['value'][$t_row['language_id']] = $t_row['value'];
		}
		
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$t_data_array['value'][$t_row['language_id']] = $t_row['value'];
		}
		
		$this->set_data($t_data_array);
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
		if (empty($this->v_additional_field_value_id))
		{
			if (!$this->find_existing_value_id() && (empty($this->v_additional_field_id) || empty($this->v_item_id)))
			{
				$t_replace = false;
			}
		}
		
		if ($this->has_only_empty_values())
		{
			if (!empty($this->v_additional_field_value_id))
			{
				$this->delete();
			}
			return $t_success;
		}
		
		$coo_additional_field_value_object = MainFactory::create_object('GMDataObject', array('additional_field_values'));
		$coo_additional_field_value_description_object = MainFactory::create_object('GMDataObject', array('additional_field_value_descriptions'));
		
		$t_additional_field_value_array = array(
			'additional_field_id' => $this->v_additional_field_id,
			'item_id' => $this->v_item_id
		);
		
		if ($t_replace)
		{
			$t_additional_field_value_array['additional_field_value_id'] = $this->v_additional_field_value_id;
			$coo_additional_field_value_object->set_keys(array('additional_field_value_id' => $this->v_additional_field_value_id));
		}
		
		$coo_additional_field_value_object->v_table_content = $t_additional_field_value_array;
		$t_insert_id = $coo_additional_field_value_object->save_body_data($t_replace);
		if (!empty($t_insert_id) && is_numeric($t_insert_id))
		{
			$this->v_additional_field_value_id = $t_insert_id;
		}
		$t_success &= (boolean) $t_insert_id;
		
		foreach ($this->v_value_array as $t_language_id => $t_value)
		{
			if (empty($t_value))
			{
				$this->delete_value($t_language_id);
				continue;
			}
			$t_additional_field_value_description_array = array(
				'additional_field_value_id' => $this->v_additional_field_value_id,
				'language_id' => $t_language_id,
				'value' => $t_value
			);
			
			$coo_additional_field_value_description_object->set_keys(array('additional_field_value_id' => $this->v_additional_field_value_id, 'language_id' => $t_language_id));
			$coo_additional_field_value_description_object->v_table_content = $t_additional_field_value_description_array;
			$t_success &= (boolean) $coo_additional_field_value_description_object->save_body_data(true);
		}
		
		return $t_success;
	}
	
	public function delete()
	{
		$t_success = true;
		
		$coo_additional_field_value_object = MainFactory::create_object('GMDataObject', array('additional_field_values'));
		$coo_additional_field_value_description_object = MainFactory::create_object('GMDataObject', array('additional_field_value_descriptions'));
		
		$coo_additional_field_value_object->set_keys(array('additional_field_value_id' => $this->v_additional_field_value_id));
		$coo_additional_field_value_description_object->set_keys(array('additional_field_value_id' => $this->v_additional_field_value_id));
		
		$t_success &= $coo_additional_field_value_object->delete();
		$t_success &= $coo_additional_field_value_description_object->delete();
		
		return $t_success;
	}
	
	public function delete_value($p_language_id)
	{
		$t_success = true;
		
		$coo_additional_field_value_description_object = MainFactory::create_object('GMDataObject', array('additional_field_value_descriptions'));
		$coo_additional_field_value_description_object->set_keys(array('additional_field_value_id' => $this->v_additional_field_value_id, 'language_id' => (int) $p_language_id));
		$t_success &= $coo_additional_field_value_description_object->delete();
		
		return $t_success;
	}
	
	public function find_existing_value_id()
	{
		if (empty($this->v_additional_field_id) || empty($this->v_item_id))
		{
			return false;
		}
		
		$t_sql = "
			SELECT
				additional_field_value_id
			FROM
				additional_field_values
			WHERE
				additional_field_id = " . $this->v_additional_field_id . " AND
				item_id = " . $this->v_item_id;
		$t_result = xtc_db_query($t_sql);
		
		$t_value_id = xtc_db_fetch_array($t_result);
		
		if (!empty($t_value_id) && isset($t_value_id['additional_field_value_id']))
		{
			$this->v_additional_field_value_id = $t_value_id['additional_field_value_id'];
			return true;
		}
		return false;
	}
	
	public function has_only_empty_values()
	{
		$t_is_empty = true;
		
		foreach ($this->v_value_array as $t_value)
		{
			$t_is_empty &= empty($t_value);
		}
		
		return $t_is_empty;
	}
	
	
	
	
	public function get_additional_field_value_id()
	{
		return $this->v_additional_field_value_id;
	}
	
	public function get_additional_field_id()
	{
		return $this->v_additional_field_id;
	}
	
	public function get_item_id()
	{
		return $this->v_item_id;
	}
	
	public function get_value_array()
	{
		return $this->v_value_array;
	}
	
	public function set_additional_field_value_id($p_additional_field_value_id)
	{
		$this->v_additional_field_value_id = $p_additional_field_value_id;
	}
	
	public function set_additional_field_id($p_additional_field_id)
	{
		$this->v_additional_field_id = $p_additional_field_id;
	}
	
	public function set_item_id($p_item_id)
	{
		$this->v_item_id = $p_item_id;
	}
	
	public function set_value_array($p_value_array)
	{
		$this->v_value_array = $p_value_array;
	}
	
	public function set_value($p_value, $p_language_id = 0)
	{
		if (!is_array($this->v_value_array))
		{
			$this->v_value_array = array();
		}
		$this->v_value_array[$p_language_id] = $p_value;
	}
}