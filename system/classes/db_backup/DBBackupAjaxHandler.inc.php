<?php
/* --------------------------------------------------------------
   DBBackupAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class DBBackupAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{		
		$t_action = $this->v_data_array['GET']['action'];
		
		$coo_db_backup_control = MainFactory::create_object( 'DBBackupControl' );
		$coo_lang_manager = MainFactory::create_object('LanguageTextManager', array('db_backup', $_SESSION['languages_id']));
		
		// Check page token before proceeding with the response (see db_backup_create.js).
		$_SESSION['coo_page_token']->is_valid($_GET['page_token']);
		
		switch( $t_action )
		{
			case 'create_db_backup':
				$c_response = $coo_db_backup_control->create_backup();
				$c_response['progress'] = floor(  $c_response['progress'] * 0.97 );
				switch( $c_response['status'] )
				{
					case 'backup_done':
						$c_response['job'] = $coo_lang_manager->get_text('compressing_backup');
						break;
					case 'continue_backup':
						$c_response['job'] = $coo_lang_manager->get_text('backup_in_process');
						break;
					default:
						$c_response['job'] = 'error';
						break;
				}
				break;
			case 'bundle_db_backup':
				$c_bundle_type = trim( (string)$this->v_data_array['GET']['bundle_type'] );
				switch( $c_bundle_type )
				{
					case 'zip':
						if( extension_loaded( 'zip' ) )
						{
							$t_files = $coo_db_backup_control->get_temp_sql_files();
							
							if( is_array( $t_files ) && count( $t_files ) > 0 )
							{
								$t_new_zip_file = $coo_db_backup_control->get_new_db_backup_filename() . '.zip';
								$t_zip_archive = new ZipArchive();
								if( !$t_zip_archive->open( $coo_db_backup_control->get_db_backup_temp_path() . $t_new_zip_file , ZIPARCHIVE::CREATE ) )
								{
									$c_response = array( 'status' => 'error', 'error_message' => 'Zip-Archiv konnte nicht erstellt werden!' );
								}
								else
								{
									foreach( $t_files AS $t_file )
									{
										$t_zip_archive->addFile( $t_file, str_replace( $coo_db_backup_control->get_db_backup_temp_path(), '', $t_file ) );
									}									
									$t_zip_archive->close();
									if( @copy( $coo_db_backup_control->get_db_backup_temp_path() . $t_new_zip_file, $coo_db_backup_control->get_db_backup_path() . $t_new_zip_file ) ) {
										@unlink( $coo_db_backup_control->get_db_backup_temp_path() . $t_new_zip_file );
										if( file_exists( $coo_db_backup_control->get_db_backup_temp_path() . $t_new_zip_file ) )
										{
											$c_response =  array( 'status' => 'error', 'error_message' => 'cannot_delete_temp_zip_file' );
										}
										else
										{
											$c_response = array( 'status' => 'success', 'filename' => $coo_db_backup_control->get_db_backup_path() . $t_new_zip_file );
										}
									}
									else
									{
										$c_response =  array( 'status' => 'error', 'error_message' => 'cannot_copy_temp_zip_file' );
									}
								}
							}
							else
							{
								$c_response =  array( 'status' => 'error', 'error_message' => 'no_files_found' );
							}
						}
						else
						{
							 $c_response =  array( 'status' => 'error', 'error_message' => 'no_zip' );
						}
						break;
					case 'gzip':	
						$coo_gzip_control = MainFactory::create_object( 'GZipControl' );
						$t_temp_filepath = $coo_db_backup_control->get_db_backup_temp_path();
						$t_filepath = $coo_db_backup_control->get_db_backup_path();
						$t_filename = $coo_db_backup_control->get_new_db_backup_filename();
						$t_source_pattern = $t_temp_filepath . '*.sql';
						
						$c_response = $coo_gzip_control->gzip( $t_temp_filepath, $t_filename, $t_source_pattern );
						
						if( !is_array( $c_response ) )
						{
							$c_response = array( 'status' => 'error' );
						}
						else if(is_array( $c_response ) && $c_response['status'] == 'success')
						{
							if( @copy( $t_temp_filepath . $c_response['filename'], $t_filepath . $c_response['filename'] ) ) {
								@unlink( $t_temp_filepath . $c_response['filename'] );
								if( file_exists( $t_temp_filepath . $c_response['filename'] ) )
								{
									$c_response =  array( 'status' => 'error', 'error_message' => 'cannot_delete_temp_gz_file' );
								}
								else
								{
									$c_response = array( 'status' => 'success', 'filename' => $t_filepath . $c_response['filename'] );
								}
							}
							else
							{
								$c_response =  array( 'status' => 'error', 'error_message' => 'cannot_copy_temp_gz_file' );
							}
						}
						break;
					case 'sql':	
						$t_files = $coo_db_backup_control->get_temp_sql_files();
						
						if( is_array( $t_files ) && count( $t_files ) > 0 )
						{
							$t_new_sql_file = $coo_db_backup_control->get_db_backup_path() . $coo_db_backup_control->get_new_db_backup_filename() . '.sql';
							$t_new_sql_handle = fopen( $t_new_sql_file, 'a' );
							if( $t_new_sql_handle == false )
							{
								return array( 'status' => 'error', 'error_message' => 'backup_dir_not_writeable' );
							}
							foreach( $t_files AS $t_file )
							{
								$t_old_sql_handle = fopen( $t_file, 'r' );
								while( !feof( $t_old_sql_handle ) )
								{
									fwrite( $t_new_sql_handle, fread( $t_old_sql_handle, 8192 ) );
								}
								fwrite( $t_new_sql_handle, "\n\n" );
							}
							$c_response = array( 'status' => 'success', 'filename' => $t_new_sql_file );
						}
						else
						{
							$c_response =  array( 'status' => 'error', 'error_message' => 'no_files_found' );
						}
						break;
					default:
						trigger_error( 'bundle_type not found: '. htmlentities_wrapper( $c_bundle_type ), E_USER_WARNING);
						return false;
				}
				if( $c_response['status'] == 'success' )
				{
					$c_response['job'] = $coo_lang_manager->get_text('delete_tmp_files');
				}
				break;
			case 'reset_db_backup':
				$coo_db_backup_control->reset();
				$c_response = array();
				$c_response['status'] = 'success';
				$c_response['job'] = '<span style="color: green;">' . $coo_lang_manager->get_text('process_completed') . '</span>';
				$coo_db_backup_content_view = MainFactory::create_object( 'DBBackupContentView' );
				$coo_db_backup_content_view->setPageToken($_SESSION['coo_page_token']->generate_token());
				$c_response['html'] = $coo_db_backup_content_view->get_html( array( 'template' => 'db_backup.html' ) );
				break;
			case 'restore_db_backup':
				$c_filename = trim( (string)$this->v_data_array['GET']['filename'] );
				if( $c_filename == '' )
				{
					trigger_error( 'DBBackup: filename is empty' );
				}
				
				$t_file_extension = substr_wrapper( $c_filename, strrpos( $c_filename, '.' ) + 1 );
				switch( $t_file_extension )
				{
					case 'zip':
						if( extension_loaded( 'zip' ) )
						{
							$t_zip_archive = new ZipArchive();
							if( !$t_zip_archive->open( $coo_db_backup_control->get_db_backup_path() . $c_filename , ZIPARCHIVE::CREATE ) )
							{
								$c_response = array( 'status' => 'error', 'error_message' => 'Zip-Archiv konnte nicht entpackt werden!' );
							}
							$t_zip_archive->extractTo( $coo_db_backup_control->get_db_backup_temp_path() );
							$t_zip_archive->close();
							
							$c_response = $coo_db_backup_control->import( $coo_db_backup_control->get_db_backup_temp_path() . '*.sql', $this->v_data_array['GET']['file_index'], $this->v_data_array['GET']['file_position'] );
							$c_response['progress'] = floor(  $c_response['progress'] * 0.99 );
							$c_response['job'] = $coo_lang_manager->get_text('restoration_in_process');
						}
						else
						{
							 $c_response =  array( 'status' => 'error', 'error_message' => 'no_zip' );
						}
						break;
					case 'gz':					
						$coo_gzip_control = MainFactory::create_object( 'GZipControl' );
						$t_temp_filepath = $coo_db_backup_control->get_db_backup_temp_path();
						$t_filepath = $coo_db_backup_control->get_db_backup_path();
						
						$c_response = $coo_gzip_control->extract_gzip( $t_filepath . $c_filename, $t_temp_filepath );
						if(is_array($c_response) && $c_response['status'] == 'success')
						{
							$c_response = $coo_db_backup_control->import( $t_temp_filepath . '*.sql', $this->v_data_array['GET']['file_index'], $this->v_data_array['GET']['file_position'] );
							$c_response['progress'] = floor(  $c_response['progress'] * 0.97 );
						}
						else
						{
							$c_response = array( 'status' => 'error', 'error_message' => 'no_gzip' );
						}
						break;
					case 'sql':
						$c_response = $coo_db_backup_control->import( $coo_db_backup_control->get_db_backup_path() . $c_filename, $this->v_data_array['GET']['file_index'], $this->v_data_array['GET']['file_position'] );
						$c_response['progress'] = floor(  $c_response['progress'] * 0.97 );
						break;
					default:
						trigger_error( 'bundle_type not found: '. htmlentities_wrapper( $c_bundle_type ), E_USER_WARNING);
						return false;
				}
				break;
			case 'download_db_backup':
				$c_filename = trim( (string)$this->v_data_array['GET']['filename'] );
				if( $c_filename == '' )
				{
					trigger_error( 'DBBackup: filename is empty' );
				}
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="' . basename( $c_filename ) . '"');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize( $coo_db_backup_control->get_db_backup_path() . basename( $c_filename ) ) );
				echo file_get_contents( $coo_db_backup_control->get_db_backup_path() . basename( $c_filename ) );
				exit(0);
				break;
			case 'delete_db_backup':
				$c_filename = trim( (string)$this->v_data_array['GET']['filename'] );
				if( $c_filename == '' )
				{
					trigger_error( 'DBBackup: filename is empty' );
				}
				if( file_exists( $coo_db_backup_control->get_db_backup_path() . $c_filename ) )
				{
					@unlink( $coo_db_backup_control->get_db_backup_path() . $c_filename );
				}
				$c_response = array();
				$c_response['status'] = 'error';
				$c_response['error_code'] = 'file_not_deleted';
				if( !file_exists( $coo_db_backup_control->get_db_backup_path() . $c_filename ) )
				{
					$c_response['status'] = 'success';
					$coo_db_backup_content_view = MainFactory::create_object( 'DBBackupContentView' );
					$coo_db_backup_content_view->setPageToken($_SESSION['coo_page_token']->generate_token()); 
					$c_response['html'] = $coo_db_backup_content_view->get_html( array( 'template' => 'db_backup.html' ) );
				}
				break;
			default:
				trigger_error( 't_action_request not found: '. htmlentities_wrapper( $t_action ), E_USER_WARNING);
				return false;
		}
		
		$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$t_output_json = $coo_json->encode($c_response);
		
		$this->v_output_buffer = $t_output_json;
	}
}
