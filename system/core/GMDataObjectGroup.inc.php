<?php
/* --------------------------------------------------------------
   GMDataObjectGroup.inc.php 2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

* needed class for EXTEND
 */
require_once(DIR_FS_CATALOG . 'system/core/GMDataObject.inc.php');

class GMDataObjectGroup extends GMDataObject
{
	var $coo_data_objects_array = array();
	var $v_enable_data_cache = true;
	
	
	function GMDataObjectGroup($p_db_table, $p_key_values_array=false, $p_orderby_keys_array=false, $p_enable_data_cache=true)
	{
		$this->v_enable_data_cache = $p_enable_data_cache;
		parent::GMDataObject($p_db_table, $p_key_values_array, $p_orderby_keys_array);
	}
	
	# overwrite parent-function
	function init_data_body()
	{
		#GET where and orderby strings
		$t_where_array = $this->get_sql_where_part();
		$t_where_string 	= $t_where_array[0];
		$t_orderby_string	= $this->get_sql_orderby_part();
		
		#BUILD and execute sql query
		$t_sql = '
			SELECT *
			FROM  '. $this->v_db_table .
			$t_where_string.
			$t_orderby_string
		;
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('init_data_body: ' . $t_sql, 'GMDataObjectGroup');
		$t_result = xtc_db_query($t_sql, 'db_link', $this->v_enable_data_cache);
		

		$this->v_last_sql = $t_sql;
		$this->v_result_count = xtc_db_num_rows($t_result);
		
		while($t_data = xtc_db_fetch_array($t_result))
		{
			#REMOVE keys from data body
			for($i=0; $i<count($this->v_key_values); $i++)
			{
				if (!is_array($this->v_key_values))
				{
					$t_key = key($this->v_key_values);
					unset($t_data[$t_key]);
				}
				next($this->v_key_values);  
			}
			reset($this->v_key_values);
			
			$coo_data_object = new GMDataObject($this->v_db_table);
			$coo_data_object->set_keys($this->v_key_values);
			$coo_data_object->v_table_content = $t_data;
			$coo_data_object->v_table_fields = $this->fetch_result_fields($t_result);
			
			$this->coo_data_objects_array[] = $coo_data_object;
		}
		
		#SET data body
		//$this->v_table_content = $t_data;
	}
	
	# overwrite parent-function
	function set_data_value($p_key_name, $p_value, $p_unquoted_value = false)
	{
		foreach($this->coo_data_objects_array as $coo_data_object)
		{
			$coo_data_object->set_data_value($p_key_name, $p_value, $p_unquoted_value);
			$coo_data_object->save_body_data();
		}
	}
	
	
	function get_data_objects_array()
	{
		return $this->coo_data_objects_array;
	}

	function delete()
	{
		$t_data_objects_array = $this->get_data_objects_array();

		for($i=0; $i<sizeof($t_data_objects_array); $i++)
		{
			$t_data_objects_array[$i]->delete();
		}
	}
}
