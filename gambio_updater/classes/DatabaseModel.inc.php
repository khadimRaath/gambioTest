<?php
/* --------------------------------------------------------------
   DatabaseModel.inc.php 2016-09-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DatabaseModel
{
	protected static $mysqli;
	
	protected $coo_mysqli;
	protected $sql_errors;

	/**
	 * Creates a new DatabaseModel and establishes a DB connection
	 *
	 * @param string p_db_host The host for the DB connection
	 * @param string p_db_user The user for the DB connection
	 * @param string p_db_password The password for the DB connection
	 * @param string p_db_name The selected DB name
	 * @param bool p_db_persistent Persistent DB connection?
	 */
	public function __construct($p_db_host = '', $p_db_user = '', $p_db_password = '', $p_db_name = '', $p_db_persistent = null)
	{
		if(self::$mysqli === null)
		{
			$t_db_host = empty($p_db_host) ? DB_SERVER : $p_db_host;
			$t_db_user = empty($p_db_user) ? DB_SERVER_USERNAME : $p_db_user;
			$t_db_password = empty($p_db_password) ? DB_SERVER_PASSWORD : $p_db_password;
			$t_db_name = empty($p_db_name) ? DB_DATABASE : $p_db_name;
			//$t_db_persistent = $p_db_persistent == null ? USE_PCONNECT : $p_db_persistent;
			$t_db_port = ini_get("mysqli.default_port");
			$t_db_socket = ini_get("mysqli.default_socket");

			if(strstr($t_db_host,':'))
			{
				$t_db_host = explode(':', $t_db_host);
				if(is_numeric($t_db_host[1]))
				{
					$t_db_port = $t_db_host[1];
				}
				else
				{
					$t_db_socket = $t_db_host[1];
				}
				$t_db_host = $t_db_host[0];
			}

			// Port and Socket variables must not be an empty string (refs #41773). 
			if($t_db_port == '')
			{
				$t_db_port = null;
			}

			if($t_db_socket == '')
			{
				$t_db_socket = null;
			}
			
			$this->coo_mysqli = new mysqli($t_db_host, $t_db_user, $t_db_password, $t_db_name,$t_db_port,$t_db_socket);
			$this->sql_errors = array();

			if (version_compare($this->coo_mysqli->server_info, '5', '>=')) $this->query("SET SESSION sql_mode=''");

			$this->query("SET SQL_BIG_SELECTS=1");
			$this->set_charset('utf8');

			self::$mysqli = $this->coo_mysqli;
		}
		else
		{
			$this->coo_mysqli = self::$mysqli;
			$this->sql_errors = array();
		}
	}


	/**
	 * Returns all logged SQL errors
	 *
	 * @return array An Array of all logged SQL errors
	 */
	public function get_sql_errors( )
	{
		return $this->sql_errors;
	}


	/**
	 * Executes query, returning a result depending on the type of query
	 *
	 * @param string p_sql
	 * @param bool p_force_result_object
	 * @return mixed
	 */
	public function query($p_sql, $p_force_result_object = false)
	{
		if ($coo_result = $this->coo_mysqli->query($p_sql))
		{
			if ($p_force_result_object)
			{
				return $coo_result;
			}
			if (strpos(strtolower(trim($p_sql)), 'select') === 0 || strpos(strtolower(trim($p_sql)), 'show') === 0)
			{
				$t_result_array = array();
				while ($t_row = $coo_result->fetch_assoc())
				{
					$t_result_array[] = $t_row;
				}
				return $t_result_array;
			}
			else if (strpos(strtolower(trim($p_sql)), 'insert') === 0)
			{
				return $this->coo_mysqli->insert_id;
			}
			else if (strpos(strtolower(trim($p_sql)), 'update') === 0 || strpos(strtolower(trim($p_sql)), 'delete') === 0)
			{
				return $this->coo_mysqli->affected_rows;
			}
			return true;
		}
		else
		{
			$this->sql_errors[] = array('query' => $p_sql, 'error' => $this->coo_mysqli->error);

			debug_notice("MySQL-Error: " . $this->coo_mysqli->error . "\nQuery: " . $p_sql);

			return false;
		}
	}


	/**
	 * Gets the last insert_id
	 *
	 * @return int
	 */
	public function get_insert_id()
	{
		return $this->coo_mysqli->insert_id;
	}


	public function set_charset($p_charset = 'utf8')
	{
		switch ($p_charset)
		{
			case 'big5':
			case 'dec8':
			case 'cp850':
			case 'hp8':
			case 'koi8r':
			case 'latin1':
			case 'latin2':
			case 'swe7':
			case 'ascii':
			case 'ujis':
			case 'sjis':
			case 'hebrew':
			case 'tis620':
			case 'euckr':
			case 'koi8u':
			case 'gb2312':
			case 'greek':
			case 'cp1250':
			case 'gbk':
			case 'latin5':
			case 'armscii8':
			case 'utf8':
			case 'ucs2':
			case 'cp866':
			case 'keybcs2':
			case 'macce':
			case 'macroman':
			case 'cp852':
			case 'latin7':
			case 'utf8mb4':
			case 'cp1251':
			case 'utf16':
			case 'utf16le':
			case 'cp1256':
			case 'cp1257':
			case 'utf32':
			case 'binary':
			case 'geostd8':
			case 'cp932':
			case 'eucjpms':
				if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
					$this->coo_mysqli->set_charset($p_charset);
				} else {
					$this->query("SET NAMES " . $p_charset);
				}
				return true;
			default:
				return false;
		}
	}

	/* Clean duplicate entries in given table
	 *
	 * @param string $p_table
	 * @param array $p_columns_array
	 * @return boolean Returns true, if re-inserting of unique values was successful
	 */
	public function clean_table($p_table, $p_columns_array)
	{
		$t_success = true;

		$t_check = 'SELECT
						*
					FROM
						`' . $p_table . '`
					GROUP BY
						`' . implode('`,`', $p_columns_array) . '`
					HAVING
						COUNT(*) > 1
		';
		$t_result = $this->query($t_check);

		if(is_array($t_result) && count($t_result))
		{
			foreach($t_result as $t_line)
			{
				$t_delete = 'DELETE FROM `' . $p_table . '` WHERE ';
				$t_insert = 'INSERT INTO `' . $p_table . '` VALUES (';
				$t_where_part_array = array();
				$t_insert_values_array = array();

				foreach($t_line as $t_key => $t_value)
				{
					if(in_array($t_key, $p_columns_array))
					{
						$t_where_part_array[] = '`' . $t_key . '`=' . '\'' . $this->coo_mysqli->real_escape_string($t_value) . '\'';
					}
					$t_insert_values_array[] =  '\'' . $this->coo_mysqli->real_escape_string($t_value) . '\'';
				}

				$t_delete .= implode(' AND ', $t_where_part_array);
				$t_insert .= implode(', ', $t_insert_values_array) . ')';

				$t_success &= $this->query($t_delete, true);
				$t_success &= $this->query($t_insert, true);
			}
		}

		return $t_success;
	}


	/**
	 * Set index for table, checking existence if index name is known
	 *
	 * @param type $p_table
	 * @param type $p_index_type = PRIMARY KEY, INDEX, UNIQUE or FULLTEXT
	 * @param type $p_columns_array
	 * @param type $p_index_name
	 * @return boolean Returns true, if index is successfully created or does already exist
	 */
	public function set_index($p_table, $p_index_type, $p_columns_array, $p_index_name = null)
	{
		$c_table = $this->coo_mysqli->real_escape_string($p_table);
		
		$t_non_unique = ($p_index_type == 'INDEX' || $p_index_type == 'FULLTEXT') ? 1 : 0;
		$t_get_indexes_query = $this->query('SHOW INDEX FROM `' . $c_table . '` WHERE `Non_unique` = ' . $t_non_unique);

		foreach($t_get_indexes_query as $value)
		{
			$t_column_array = array();
			$t_get_key_name_columns = $this->query('SHOW INDEX FROM `' . $c_table . '` WHERE `Key_name` = "' . $value['Key_name'] . '"');
			foreach($t_get_key_name_columns as $t_field)
			{
				$t_column_array[] = $t_field['Column_name'];
			}
			if($t_column_array === $p_columns_array)
			{
				return true;
			}
		}
		
		if($p_index_type === 'UNIQUE' || $p_index_type === 'PRIMARY KEY')
		{
			$this->clean_table($c_table, $p_columns_array);
		}
		$t_index_type = ($p_index_type == 'FULLTEXT') ? 'FULLTEXT' : 'BTREE';
		
		$t_index_names_array = $this->getIndicesNames($c_table, $t_non_unique, $t_index_type);
		$t_timestamp = time();
		
		if(sizeof($p_columns_array) <= 0)
		{
			return false;
		}
		
		// drop other indices with same name 
		$t_check = $this->query('SHOW INDEX FROM `' . $c_table . '` WHERE `Key_name` = "' . $this->coo_mysqli->real_escape_string($p_index_name) . '"', true);
		if($t_check->num_rows)
		{
			$this->query('DROP INDEX `' . $this->coo_mysqli->real_escape_string($p_index_name) . '` ON ' . $c_table);
		}
		
		// set name of index
		if(!empty($p_index_name)){
			// check 1
			if(in_array($p_index_name, $t_index_names_array))
			{
				$p_index_name = $p_index_name . '_' . $t_timestamp;
			}
			// rename with timestamp and check 2
			if(in_array($p_index_name, $t_index_names_array))
			{
				// if index exists, cancel process to add index
				return false;
			}
		}
		else
		{
			// no index to check
			return false;
		}
		
		$t_index_name_sql_part = ' `' . $this->coo_mysqli->real_escape_string($p_index_name) . '` ';
		
		// check, if column exists
		$t_columns_exists = $this->check_col_exists($c_table, $p_columns_array);
		
		// 1 column or more dosn't exist
		if($t_columns_exists !== $p_columns_array)
		{
			return false;
		}

		// add index
		if($p_index_type == 'PRIMARY KEY')
		{
			$t_sql = 'SHOW INDEX FROM ' . $c_table . ' WHERE Key_name = "PRIMARY"';
			$coo_result = $this->query($t_sql, true);
			if($coo_result->num_rows > 0)
			{
				$this->query('DROP INDEX `PRIMARY` ON ' . $c_table);
			}
			$t_index_name_sql_part = '';
		}
		
		if(!$this->indexExists($c_table, $p_index_type, $p_columns_array))
		{
			$t_sql = "ALTER TABLE `" . $c_table . "`
				ADD " . $this->coo_mysqli->real_escape_string($p_index_type) . " " . $t_index_name_sql_part . "(`" . implode('`,`', $p_columns_array) . "`)";

			return $this->query($t_sql, true);
		}
		
		return true;
	}

	/**
	 * selects all indices which exists in table
	 *
	 * @param type $p_table
	 * @param type $t_non_unique
	 * @param type $t_index_type
	 * @return array Returns indices which exists in table
	 */
	protected function getIndicesNames($p_table, $t_non_unique, $t_index_type)
	{
		$t_indices_names = array();
		$t_sql = "SHOW INDEX
						FROM `" . $this->coo_mysqli->real_escape_string($p_table) . "`
						WHERE
							Non_unique = '" . $t_non_unique . "' AND
							Index_type = '" . $t_index_type . "'";
		$t_result_array = $this->query($t_sql);
		foreach($t_result_array AS $t_key => $t_data_array)
		{
			$t_indices_names[$t_data_array['Key_name']] = $t_data_array['Key_name'];
		}
		return $t_indices_names;
	}


	/**
	 * Returns an array of all indices of a table
	 * 
	 * @param string $p_table
	 *
	 * @return array
	 */
	protected function getIndices($p_table)
	{
		$indicesGroupedByColums = array();
		
		$indexRows = $this->query('SHOW INDEX FROM `' . $this->coo_mysqli->real_escape_string($p_table) . '`');

		$indices = array();

		foreach($indexRows as $key => $row)
		{
			if(!isset($indices[$row['Key_name']]))
			{
				$indices[$row['Key_name']] = array('type' => $this->getIndexType($row), 'cols' => $row['Column_name']);
			}
			else
			{
				$indices[$row['Key_name']]['cols'] .= ',' . $row['Column_name'];
			}
		}

		foreach($indices as $keyName => $indexInfoArray)
		{
			if(!isset($indicesGroupedByColums[$indexInfoArray['cols']]))
			{
				$indicesGroupedByColums[$indexInfoArray['cols']] = array($indexInfoArray['type'] => array($keyName));
			}
			elseif(!isset($indicesGroupedByColums[$indexInfoArray['cols']][$indexInfoArray['type']]))
			{
				$indicesGroupedByColums[$indexInfoArray['cols']][$indexInfoArray['type']] = array($keyName);
			}
			else
			{
				$indicesGroupedByColums[$indexInfoArray['cols']][$indexInfoArray['type']][] = $keyName;
			}
		}
		
		return $indicesGroupedByColums;
	}


	/**
	 * get index type by SHOW INDEX record
	 * 
	 * @param array $row
	 *
	 * @return string
	 */
	protected function getIndexType(array $row)
	{
		if($row['Key_name'] === 'PRIMARY')
		{
			return 'PRIMARY KEY';
		}

		if($row['Index_type'] === 'FULLTEXT')
		{
			return 'FULLTEXT';
		}

		if($row['Non_unique'] === '0')
		{
			return 'UNIQUE';
		}

		return 'INDEX';
	}


	/**
	 * check if index already exists
	 *
	 * @param string $p_table
	 * @param string $p_indexType
	 * @param array  $columns
	 *
	 * @return bool
	 */
	protected function indexExists($p_table, $p_indexType, array $columns)
	{
		$indicesGroupedByColums = $this->getIndices($p_table);
		
		$columnsString = implode(',', $columns);
		
		switch($p_indexType)
		{
			case 'FULLTEXT':
				if($indicesGroupedByColums[$columnsString]['FULLTEXT'])
				{
					return true;
				}
			case 'INDEX':
				if($indicesGroupedByColums[$columnsString]['INDEX'])
				{
					return true;
				}
			case 'UNIQUE':
				if($indicesGroupedByColums[$columnsString]['UNIQUE'])
				{
					return true;
				}
			case 'PRIMARY KEY':
				if($indicesGroupedByColums[$columnsString]['PRIMARY KEY'])
				{
					return true;
				}
		}

		return false;
	}
	

	/**
	 * check if col of index-params exists in table
	 *
	 * @param type $p_table
	 * @param type $p_columns_array
	 * @return array Returns columns which exists
	 */
	protected function check_col_exists($p_table, $p_columns_array)
	{
		$t_columns_exists = $t_columns_index = array();
		
		$t_sql = "SHOW COLUMNS FROM `" . $this->coo_mysqli->real_escape_string($p_table) . "`;";
		$t_result_array = $this->query($t_sql);
		foreach($t_result_array AS $t_key => $t_data_array)
		{
			$t_columns_exists[] = $t_data_array['Field'];
		}
		
		foreach($p_columns_array AS $k_col => $v_col)
		{
			if(in_array($v_col, $t_columns_exists ))
			{
				$t_columns_index[] = $v_col;
			}
		}
		return $t_columns_index;
	}

	/**
	 * handle errors of function set_index
	 *
	 * @param type $p_error_number
	 * @param type $p_table
	 * @param type $p_index_type
	 * @param type $p_columns_array
	 * @param type $p_index_name
	 * @return String Returns error-text
	 */
	protected function set_index_error($p_error_number, $p_table, $p_index_type, $p_columns_array, $p_index_name)
	{
		$t_error_text = 'Unbekannter Fehler';
		switch($p_error_number)
		{
			case 1 :
				$t_error_text = 'Indexname nicht bekannt<br/>' . 'Tabelle: ' . $p_table;
				break;
			case 2 :
				$t_error_text = 'Indexname existiert bereits<br/>' . 'Tabelle: ' . $p_table . '<br/>Index: ' . $p_index_name;
				break;
			case 3 :
				$t_error_text = 'Mindestens eine Spalte existiert nicht<br/>' . 'Tabelle: ' . $p_table . '<br/>Spalten: ' . implode($p_columns_array, ', ');
				break;
			case 4 :
				$t_error_text = 'Keine Spalten für den Index angegeben<br/>' . 'Tabelle: ' . $p_table . '<br/>Index: ' . $p_index_name;
				break;
		}
		$t_error_text = 'Error in set_index_error:<br/>' . $t_error_text . '<br/><br/>';
		return $t_error_text;
	}

	/**
	 * Drop index if exists in table
	 *
	 * @param type $p_table
	 * @param type $p_index_name
	 * @return boolean Returns true, if index is successfully deleted
	 */
	protected function drop_index($p_table, $p_index_name)
	{
		$t_success = true;

		$t_sql = 'SHOW INDEX FROM `' . $this->coo_mysqli->real_escape_string($p_table) . '` WHERE Key_name = "' .  $this->coo_mysqli->real_escape_string($p_index_name) . '"';

		$t_get_columns = $this->query($t_sql, true);
		if($t_get_columns->num_rows > 0)
		{
			if($p_index_name !== 'PRIMARY')
			{
				$t_query = 'ALTER TABLE `' . $this->coo_mysqli->real_escape_string($p_table) . '` DROP INDEX `' . $this->coo_mysqli->real_escape_string($p_index_name) . '`';
			}
			else
			{
				$t_query = 'ALTER TABLE `' . $this->coo_mysqli->real_escape_string($p_table) . '` DROP PRIMARY KEY';
			}

			$t_success = $this->query($t_query);
		}

		return $t_success;
	}

	/**
	 * Check if column exists in table
	 *
	 * @param type $p_table
	 * @param type $p_column
	 * @return Returns true, if column already exist
	 */
	protected function table_column_exists($p_table, $p_column)
	{
		$t_return = false;
		$t_check = $this->query("DESCRIBE `" . $p_table . "` '" . $p_column . "'", true);
		if($t_check->num_rows != 0)
		{
			$t_return = true;
		}
		return $t_return;
	}


	/**
	 * mysqli::real_escape_string
	 * @param string $p_string
	 * @return string Returns an escaped string.
	 */
	public function real_escape_string($p_string)
	{
		return $this->coo_mysqli->real_escape_string($p_string);
	}


	/**
	 * Method to delete duplicate entries for a unique column that is missing a UNIQUE KEY constraint.
	 *
	 * @param string $p_table The table featuring the unique column
	 * @param string $p_unique_key The unique column (to be)
	 * @param string $p_primary_key The primary key to differ the rows that bare the same unique key
	 * @return boolean Success
	 */
	public function delete_duplicate_entries($p_table, $p_unique_key, $p_primary_key)
	{
		$t_sql = '	DELETE FROM
						' . $p_table . '
					USING
						' . $p_table . ',
						' . $p_table . ' AS tmp_table
					WHERE
						' . $p_table . '.' . $p_unique_key . ' = tmp_table.' . $p_unique_key . ' AND
						' . $p_table . '.' . $p_primary_key . ' < tmp_table.' . $p_primary_key;

		$t_success = $this->query($t_sql, true);

		return $t_success;
	}
	
	public function get_coo_mysqli()
	{
		return $this->coo_mysqli;
	}
}