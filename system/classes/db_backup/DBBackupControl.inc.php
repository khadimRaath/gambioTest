<?php
/* --------------------------------------------------------------
   DBBackupControl.inc.php 2016-03-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DBBackupControl
{
	protected $v_backup_file_path = '';
	protected $v_backup_temp_file_path = '';
	protected $v_execution_time = 15;
	protected $v_export_values_per_call = 2000;
	protected $v_session_export_file = '';
	protected $v_sql_commands_list = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'INSERT', 'REPLACE', 'SELECT', 'SET', 'TRUNCATE', 'UPDATE', 'USE', 'START', 'COMMIT');
	
	public function DBBackupControl()
	{		
		$this->v_backup_file_path = DIR_FS_BACKUP;
		$this->v_backup_temp_file_path = $this->v_backup_file_path . 'temp/';
		$this->v_session_export_file = DIR_FS_CATALOG . 'cache/db_export.txt';
	}
	
	public function get_db_backup_path()
	{
		return $this->v_backup_file_path;
	}
	
	protected function get_db_backup_files_array($p_loopup_path)
	{
		$tmp_files_array = array();
		
		$t_db_backup_extensions = array('*.sql', '*.gz', '*.gzip', '*.zip');
		foreach($t_db_backup_extensions as $t_extension)
		{
			$t_glob_array = glob($p_loopup_path . $t_extension);
			
			if(is_array($t_glob_array) && count($t_glob_array) > 0)
			{
				$tmp_files_array = array_merge($tmp_files_array, $t_glob_array);
			}
		}
		
		return $tmp_files_array;
	}

	public function get_db_backup_files( $p_sort = 'DESC' )
	{
		$c_sort = 'DESC';
		if( trim( $p_sort ) == 'ASC' )
		{
			$c_sort = 'ASC';
		}
		
		$t_files_array = array();
		$tmp_files_array = $this->get_db_backup_files_array($this->v_backup_file_path);
		if( is_array( $tmp_files_array ) && count( $tmp_files_array ) > 0 )
		{
			foreach( $tmp_files_array AS $tmp_file )
			{
				$t_file_data = array();
				$t_filename = str_replace( $this->v_backup_file_path, '', $tmp_file );
				$t_file_data[ 'filename' ] = $t_filename;
				$t_file_data[ 'filesize' ] = number_format( (double) filesize( $tmp_file ) ) . ' bytes';
				$t_file_data[ 'filedate' ] = date( 'Y-m-d H:i:s', filemtime( $tmp_file ) );
				$t_file_data[ 'filedate_formatted' ] = date( PHP_DATE_TIME_FORMAT, filemtime( $tmp_file ) );
				$t_files_array[] = $t_file_data;
			}
		}
		
		usort( $t_files_array, array( $this, 'cmp_filedate_' . $c_sort ) );
		
		return $t_files_array;
	}
	
	public function get_new_db_backup_filename()
	{
		return 'db_' . DB_DATABASE . '_' . date('YmdHis');
	}
	
	public function get_tmp_table_filename($p_table_name)
	{
		$t_return = $this->v_backup_temp_file_path . $p_table_name . '_' . DB_DATABASE . '-' . date('YmdHis') . '.sql';
		
		$t_files_array = glob($this->v_backup_temp_file_path . $p_table_name . '_' . DB_DATABASE . '-*.sql');
		if(is_array($t_files_array) && count($t_files_array) == 1)
		{
			$t_return = $t_files_array[0];
		}
		
		return $t_return;
	}
	
	public function get_db_backup_temp_path()
	{
		return $this->v_backup_temp_file_path;
	}
	
	public function get_temp_sql_files()
	{
		$t_files_array = glob($this->v_backup_temp_file_path . '*.sql');
		
		return $t_files_array;
	}
	
	protected function cmp_filedate_asc($a, $b)
	{
		return $a['filedate'] > $b['filedate'];
	} 
	
	protected function cmp_filedate_desc($a, $b)
	{
		return $a['filedate'] < $b['filedate'];
	} 
	
	public function create_backup()
	{
		$t_start_call = microtime(true);
		$t_table_key = 0;
		$t_offset = 0;
		
		// check if script already has been executed 
		if( file_exists( $this->v_session_export_file ) != false )
		{
			$handle_export_session = fopen( $this->v_session_export_file, "r" );
			// get table_key & offset from last execution
			$t_db_export_session_text = fgets( $handle_export_session );
			$t_db_export_session = unserialize( $t_db_export_session_text );
			$t_table_key = $t_db_export_session['table_key'];
			$t_offset = $t_db_export_session['offset'];
			fclose( $handle_export_session );
		}
		else
		{
			$this->reset();
		}
		
		$handle_export_session = fopen( $this->v_session_export_file, "w" );
		
		// get all tables from database
		$t_tables = $this->get_db_tables();	
		$t_tables_count = count( $t_tables );
		for( $i = $t_table_key; $i < $t_tables_count; $i++ )
		{			 
			$t_status = true;
			// while values still existing
			while( $t_status )
			{
				// check the execution time (server timeout))
				$t_actual_time = microtime( true );
				if( (int)( $t_actual_time - $t_start_call ) > $this->v_execution_time ){
					// save present table_key & offset
					$t_db_export_session['table_key'] = $i;
					$t_db_export_session['offset'] = $t_offset;
					
					fwrite( $handle_export_session, serialize( $t_db_export_session ) );
					$t_progress = floor( $i / 100 );
					$t_progress = 10;
					return array( 'status' => 'continue_backup', 'progress' => $t_progress );
				}
				// export table
				$t_status = $this->export_table( $t_tables[$i], $t_offset );							
			
				if( $t_status )
				{
					// increase offset for next iteration
					$t_offset += $this->v_export_values_per_call;
				}
				else
				{
					// reset offset for next table					
					$t_offset = 0;					
					break;
				}
			}
		}
		
		fclose( $handle_export_session );
		
		// after all tables saved - delete temporary session file
		if( file_exists( $this->v_session_export_file ) )
		{
			unlink( $this->v_session_export_file );
		}
		return array( 'status' => 'backup_done', 'progress' => '100' );
	}
	
	protected function get_db_tables()
	{
		$t_tables = Array();
		$t_result = mysqli_query($GLOBALS["___mysqli_ston"],  "SHOW TABLES" );
		while( $t_row = mysqli_fetch_array( $t_result ) )
		{
			array_push( $t_tables, $t_row[0] );
		}
		return $t_tables;
	}
	
	protected function export_table( $p_table_name, $p_offset )
	{	
		// get parameter
		$c_table_name = (string)$p_table_name;
        $c_table_name = '`' . $p_table_name . '`';
		$c_offset = (int)$p_offset;
		$c_limit = (int)$this->v_export_values_per_call;

		// set backup file name
		$backup_file = $this->get_tmp_table_filename($p_table_name);
		$handle_export = fopen( $backup_file, "a" );

		// if offset = 0: write create table command
		if( $p_offset == 0 )
		{
			fwrite( $handle_export, "DROP TABLE IF EXISTS " . $c_table_name . ";\n\n" );
			
			$t_sql = 'SHOW CREATE TABLE '.$c_table_name;
			$result = mysqli_query($GLOBALS["___mysqli_ston"],  $t_sql );
			while( $t_row = mysqli_fetch_array( $result ) )
			{
				fwrite( $handle_export, $t_row[1].";\n" );
			}		
		}
		
		// get all values from table
		$t_sql= 'SELECT * FROM ' . $c_table_name . ' LIMIT ' . $c_offset . ', ' . $c_limit;
		$t_result = mysqli_query($GLOBALS["___mysqli_ston"],  $t_sql );
		$t_count_values = mysqli_num_rows( $t_result );	
		while( $t_row = mysqli_fetch_array( $t_result ) )
		{
			// build insert into command
			$t_value = "\nINSERT INTO " . $c_table_name . " VALUES(";
			$t_value_count = count( $t_row ) / 2;
			for( $i = 0; $i < $t_value_count; $i++ ) 
			{ 
				if(is_null($t_row[$i]))
				{
					$t_value .= "NULL";
				}
				else
				{
					$t_value .= "'".$this->prepare_value( $t_row[$i] )."'";
				}
				
				if($i < $t_value_count - 1)
				{
					$t_value .= ","; 
				}
			}
			$t_value .= ");";
			fwrite( $handle_export, $t_value );
		}
		
		fclose( $handle_export );

		if( $t_count_values < $c_limit )
		{
			// all values saved
			return false;
		}

		// values still existing
		return true;
	}
	
	protected function prepare_value( $p_value )
	{
		// prepare value
		$c_value = str_replace( "'", "''", $p_value );
		$c_value = str_replace( '\\', '\\\\', $c_value );
		return $c_value;
	}
	
	public function import( $p_file_pattern, $p_file_index, $p_file_position )
	{
		// DUMMY
		$c_import_runnning = false;
		$c_file_pattern = (string)$p_file_pattern;
		$c_file_index = (int)$p_file_index;
		$c_file_start_position = (int)$p_file_position;
		$c_actual_file_position = $c_file_start_position;
		$c_execution_time_start = microtime(true);
		$t_file = false;
		
		$t_files_array = glob( $c_file_pattern );
		
		if( is_array( $t_files_array ) && count( $t_files_array ) > 0 )
		{
			$t_count_files = count( $t_files_array );
			
			while( $c_file_index < $t_count_files )
			{
				if( !$t_file = @fopen( $t_files_array[ $c_file_index ], "r" ) )
				{ 
					$c_response = array( 'status' => 'error', 'error_message' => 'Datei "' . $t_files_array[ $c_file_index ] . '" kann nicht ge&ouml;ffnet werden!' );
					break;
				}
				else
				{
					if( $c_file_start_position != $c_actual_file_position )
					{
						$c_actual_file_position = 0;
					}
					fseek( $t_file, $c_actual_file_position );
					$t_query = array();
					$t_canceled = false;
					
					while( feof( $t_file ) === false )
					{
						// get actual line
						$t_line = fgets( $t_file );
						
						// check if configuration table
						if( $c_import_runnning && ( trim( $t_line ) == 'DROP TABLE IF EXISTS configuration;' || trim( $t_line ) == 'DROP TABLE IF EXISTS gm_configuration;') )
						{
							// build last sql command
							$t_query = trim( implode( '', $t_query ) );
							// execute last sql command
							if( trim( $t_query ) != '' )
							{
								xtc_db_query( $t_query );
							}
							
							// reset ajax request before configuration tables
							$t_canceled = true;
							break;
						}
						
						// check if this line has a new command
						if( in_array( strtok( $t_line, " " ), $this->v_sql_commands_list ) && $c_file_start_position != $c_actual_file_position )
						{
							// build sql command
							$t_query = trim( implode( "", $t_query ) );
							// execute sql command
							if(trim($t_query) != '')
							{
								xtc_db_query( $t_query );
							}

							// check the execution time (server timeout)
							$c_execution_time_actual = microtime( true );
							if( (int)( $c_execution_time_actual - $c_execution_time_start ) > 20 )
							{
								// cancel work
								$t_canceled = true;
								break;
							}
							// reset query array
							$t_query = array();
							$c_import_runnning = true;
						}
						$c_actual_file_position = ftell( $t_file );
						$t_query[] = $t_line;
					}
					fclose($t_file);
					if( $t_canceled )
					{
						break;
					}
					
					// build last sql command
					$t_query = trim( implode( '', $t_query ) );
					// execute last sql command
					if( trim( $t_query ) != '' )
					{
						xtc_db_query( $t_query );
					}
				}
				$c_file_index++;
			}
			
			if( $t_canceled )
			{
				if( count( $t_files_array ) > 1 )
				{
					$t_total_filesize = 0;
					$t_completed_filesize = 0;
					for( $i = 0; $i < $t_count_files; $i++ )
					{
						$t_filesize = filesize( $t_files_array[ $i ] );
						$t_total_filesize += $t_filesize;
						if( $i < $c_file_index )
						{
							$t_completed_filesize += $t_filesize;
						}
					}
					$t_completed_filesize += $c_actual_file_position;
				}
				else
				{
					$t_total_filesize = filesize( $t_files_array[0] );
					$t_completed_filesize = $c_actual_file_position;
				}
				$t_progress = ($t_completed_filesize / $t_total_filesize ) * 100;
				$c_response = array('status' => 'continue_restore', 
									'file_index' => $c_file_index, 
									'file_position' => $c_actual_file_position, 
									'progress' => $t_progress
									);
			}
			else
			{
				$c_response = array( 'status' => 'success', 'progress' => '100' );
			}
			
		}
		else
		{
			$c_response = array( 'status' => 'error', 'error_message' => 'Keine Dateien f&uuml;r Import gefunden!' );
		}
		
		return $c_response;
	}
	
	public function get_file_list( $p_serialize )
	{
		$t_response = array();
		
		if( file_exists( $this->v_backup_file_path . 'db_export.zip' ) )
		{
			$t_response[] = array('filename' => 'db_export.zip', 'size' => filesize($this->v_backup_file_path . 'db_export-' . $this->v_hash . '.zip'), 'filepath' => $this->v_backup_file_path . 'db_export-' . $this->v_hash . '.zip');
		}
		else
		{
			// get all sql files (with hash)
			$t_files = glob($this->v_backup_file_path . 'db_backup_*.sql');

			foreach($t_files AS $t_file)
			{
				$t_response[] = array('filename' => str_replace($this->v_backup_file_path, '', $t_file), 'size' => filesize($t_file), 'filepath' => $t_file);
			}
		}
		
		if($p_serialize)
		{
			$t_response = serialize($t_response);
		}
		
		return $t_response;
	}
	
	public function reset()
	{
		$t_status = true;
		
		// get all sql files (with hash)
		$t_files = $this->get_db_backup_files_array($this->v_backup_temp_file_path);
		if( is_array( $t_files ) && count( $t_files ) > 0 )
		{
			foreach( $t_files AS $t_file )
			{
				@unlink( $t_file );
				if( file_exists( $t_file ) )
				{
					$t_status = false;
				}
			}
		}
		// delete temporary session file
		@unlink( $this->v_session_export_file );
		if( file_exists( $this->v_session_export_file ) )
		{
			$t_status = false;
		}
		return $t_status;
	}
}