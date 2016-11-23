<?php
/* --------------------------------------------------------------
   GMDataObject.inc.php 2016-03-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMDataObject
{
	var $v_db_table		= '';
	var $v_key_values 	= array();
	var $v_orderby_keys = array();
	
	var $v_table_content 	= array();
	var $v_related_objects	= array();

	var $v_table_content_unquoted = array();
	var $v_table_fields = array();

	var $v_last_sql = '';
	var $v_result_count = 0;
	
	var $v_enable_data_cache = true;
	
	function GMDataObject($p_db_table, $p_key_values_array=false, $p_orderby_keys_array=false, $p_enable_data_cache=true)
	{
		$this->v_db_table = $p_db_table;
		$this->v_enable_data_cache = $p_enable_data_cache;
		
		if($p_orderby_keys_array !== false)
		{
			$this->set_orderby_keys($p_orderby_keys_array);
		}
		
		if($p_key_values_array !== false)
		{
			$this->set_keys($p_key_values_array);
			$this->init();
		}
	}
	
	function set_keys($p_key_values_array)
	{
		$this->v_key_values = $p_key_values_array;
	}
	
	function delete()
	{
		$t_where_array = $this->get_sql_where_part();
		
		foreach($t_where_array AS $t_where_string)
		{
			#BUILD and execute sql query
			$t_sql = '
				DELETE FROM '. $this->v_db_table .
				$t_where_string
			;
			$t_result = xtc_db_query($t_sql);
		}		
	}
	
	function set_orderby_keys($p_orderby_array)
	{
		$this->v_orderby_keys = $p_orderby_array;
	}
	
	function init()
	{
		#LOAD data body from db
		$this->init_data_body();
		$this->init_related_objects();
	}
	
	function get_sql_where_part()
	{
		$t_first_iteration = true;
		$t_where_parts_ready = true;
		$t_where_string_array = array();
		$t_value_array_index = 0;
		
		$t_where_parts 	= array();
		$t_where_string = '';
		
		while(!$t_where_parts_ready || $t_first_iteration)
		{
			#COLLECT key/value pairs for sql-where 
			for($i=0; $i<count($this->v_key_values); $i++)
			{
				$t_key = key($this->v_key_values);
				$t_value = current($this->v_key_values);  

				if($t_value !== false && !is_array($t_value)) #do nothing if key-value is not set
				{
					$c_key = xtc_db_input($t_key);
					$c_value = xtc_db_input($t_value);

					$t_where_parts[] = ' '.$c_key.' = "'.$c_value.'" ';
				}
				else if( is_array( $t_value ) )
				{
					$t_in_array = array();
					$t_count_values = count( $t_value );
					if($t_count_values > 500)
					{
						$t_in_array = array_slice($t_value, $t_value_array_index*500, 500);
					}
					else
					{
						$t_in_array = $t_value;
					}
					$t_value_array_index++;
					if($t_count_values <= $t_value_array_index*500)
					{
						$t_where_parts_ready = true;
					}
					else
					{
						$t_where_parts_ready = false;
					}
					
					$c_key = xtc_db_input($t_key);
					
					foreach($t_in_array as $c_value_key => $t_value)
					{
						$t_in_array[$c_value_key] = xtc_db_input($t_value);
					}
					
					$c_value = implode('","', $t_in_array);

					$t_where_parts[] = ' '.$c_key.' IN ( "'.$c_value.'" ) ';
				}
				next($this->v_key_values); 
				
			}
			reset($this->v_key_values);
			
			#BUILD where part
			if(sizeof($t_where_parts) > 0) 
			{
				$t_where_string  = ' WHERE ';
				$t_where_string .= implode(' AND ', $t_where_parts);
			}
			$t_where_parts = array();
			$t_where_string_array[] = $t_where_string;
			
			$t_first_iteration = false;
		}
		
		return $t_where_string_array;
	}
	
	function get_sql_orderby_part()
	{
		$c_orderby_parts = array();
		$t_orderby_string = '';
		
		if(sizeof($this->v_orderby_keys) > 0) 
		{
			# use explicit orderby-keys
			foreach($this->v_orderby_keys as $t_value) {
				$c_orderby_parts[] = xtc_db_input($t_value);
			}
		}
		elseif(sizeof($this->v_key_values) > 0) 
		{
			# use where-key-names as fallback
			for($i=0; $i<count($this->v_key_values); $i++) {
				$t_key = key($this->v_key_values);
				$c_orderby_parts[] = xtc_db_input($t_key);
			}
		}
		
		#build string, if array not empty
		if(sizeof($c_orderby_parts) > 0) 
		{
			$t_orderby_string  = ' ORDER BY ';
			$t_orderby_string .= implode(', ', $c_orderby_parts);
		}
		return $t_orderby_string;
	}

	function init_data_body()
	{
		#GET sql string parts
		$t_where_array 	= $this->get_sql_where_part();
		$t_where_string 	= $t_where_array[0];
		$t_orderby_string	= $this->get_sql_orderby_part();
		
		if($t_where_string != '')
		{
			#BUILD and execute sql query
			$t_sql = '
				SELECT *
				FROM  '. $this->v_db_table 	.
				$t_where_string 			.
				$t_orderby_string
			;
			$t_result = xtc_db_query($t_sql, 'db_link', $this->v_enable_data_cache);
			$t_data   = xtc_db_fetch_array($t_result);
			
			$this->v_last_sql = $t_sql;
			$this->v_result_count = xtc_db_num_rows($t_result);
			$this->v_table_fields = $this->fetch_result_fields($t_result);
			
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
			
			#SET data body
			$this->v_table_content = $t_data;
		}
	}
	
	function get_related_objects($p_key_name)
	{
		$t_output = $this->v_related_objects[$p_key_name];
		return $t_output;
	}

	function get_data_value($p_key_name)
	{
		if(isset($this->v_table_content[$p_key_name])) {
			#ELEMENT IN DATA BODY?
			$t_output = $this->v_table_content[$p_key_name];
		}
		elseif(isset($this->v_key_values[$p_key_name])) {
			#ELEMENT IN PRIMARY KEY LIST?
			$t_output = $this->v_key_values[$p_key_name];
		}
		elseif(in_array($p_key_name, $this->v_table_fields) == true)
		{
			#FOUND, BUT VALUE IS NULL
			$t_output = '';
		}
		else {
			#ELEMENT NOT FOUND !
			trigger_error('last query: '. $this->v_last_sql, E_USER_WARNING);
			trigger_error('key_name not found in data body: "'. $p_key_name .'" Dump: '. print_r($this->v_table_fields, true), E_USER_WARNING);
			$t_output = '';
		}
		return $t_output;
	}
	
	function set_data_value($p_key_name, $p_value, $p_unquoted_value=false)
	{
		$this->v_table_content[$p_key_name] = $p_value;

		if($p_unquoted_value) {
			# add field to unquoted list
			$this->v_table_content_unquoted[] = $p_key_name;
		}
	}
	
	function save_body_data($p_replace=false)
	{
		$t_content_array = array();
		$t_return_id = false;
		
		#COLLECT content pairs for insert/update 
		for($i=0; $i<count($this->v_table_content); $i++)
		{
			$t_key = key($this->v_table_content);
			$t_value = current($this->v_table_content);  
			
			$c_key = xtc_db_input($t_key);
			$c_value = xtc_db_input($t_value);

			if(in_array($c_key, $this->v_table_content_unquoted) == true) {
				# no quotes
				$t_content_array[] = ' '.$c_key.' = '.$c_value.' ';
			} else {
				# use quotes
				$t_content_array[] = ' '.$c_key.' = "'.$c_value.'" ';
			}
			next($this->v_table_content);  
		}
		reset($this->v_table_content);
		
		$t_content_string = implode(', ', $t_content_array);
		$t_where_array = $this->get_sql_where_part();
		$t_where_string 	= $t_where_array[0];
		
		if ($p_replace)
		{
			#REPLACE, if explicity given
			$t_sql = '
				REPLACE INTO '. $this->v_db_table .'
				SET '. $t_content_string
			;
			$this->v_last_sql = $t_sql;
			$t_result = xtc_db_query($t_sql);
			$t_return_id = xtc_db_insert_id();
		}
		elseif ($t_where_string != '')
		{
			#UPDATE, if where string available
			$t_sql = '
				UPDATE 	'. $this->v_db_table .'
				SET 	'. $t_content_string .
				$t_where_string
			;
			$this->v_last_sql = $t_sql;
			$t_result = xtc_db_query($t_sql);
			$t_return_id = 0;
			
			if(mysqli_affected_rows($GLOBALS["___mysqli_ston"]) == 0)
			{
				$t_sql = 'SELECT COUNT(*) AS cnt FROM '. $this->v_db_table . $t_where_string;
				$t_result = xtc_db_query($t_sql, 'db_link', $this->v_enable_data_cache);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_data = xtc_db_fetch_array($t_result);
					if((int)$t_data['cnt'] == 0)
					{
						$t_return_id = false;
					}
				}
			}
		}
		else 
		{
			#INSERT, if there's no where string
			$t_sql = '
				INSERT INTO '. $this->v_db_table .'
				SET 		'. $t_content_string
			;
			$this->v_last_sql = $t_sql;
			$t_result = xtc_db_query($t_sql);
			$t_return_id = xtc_db_insert_id();
			
			if($t_return_id !== 0)
			{
				#SET key-value
				reset($this->v_key_values);
				$t_primary_key_name = key($this->v_key_values);
				$this->v_key_values[$t_primary_key_name] = $t_return_id;
			}
			else
			{
				#key is no auto-increment value / combined primary key
				$t_return_id = true;

				$t_key_values_array = array();

				reset($this->v_key_values);
				foreach($this->v_key_values AS $t_name => $t_value)
				{
					$t_key_values_array[$t_name] = $this->v_table_content[$t_name];
					unset($this->v_table_content[$t_name]);
				}
				$this->set_keys($t_key_values_array);
			}
		}

		if(sizeof($this->v_table_content_unquoted) > 0)
		{
			# re-init data body for fields with functions as value
			$this->init_data_body();
			$this->v_table_content_unquoted = array();
		}
		return $t_return_id;
	}

	function fetch_result_fields($p_result)
	{
		$t_found_fields = array();
		
		while($t_meta = mysqli_fetch_field($p_result))
		{
			$t_found_fields[] = $t_meta->name;
		}
		
		return $t_found_fields;
	}

	function get_result_count()
	{
		$t_output = $this->v_result_count;
		return $t_output;
	}
	
	#ABSTRACT empty method
	function init_related_objects()
	{
	}
}