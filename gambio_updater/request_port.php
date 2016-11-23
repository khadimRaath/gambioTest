<?php
/* --------------------------------------------------------------
   request_port.php 2016-09-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application.inc.php');

debug_notice('request_port.php called');

$t_output_array                 = array();
$t_output_array['login_succes'] = false;

$t_language = isset($_GET['language'])
              && file_exists('lang/' . basename($_GET['language'])
                             . '.inc.php') ? basename($_GET['language']) : 'german';

require_once('lang/' . $t_language . '.inc.php');
require_once('classes/GambioUpdateControl.inc.php');
$coo_update_control = new GambioUpdateControl(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

if($coo_update_control->login($_POST['email'], $_POST['password']))
{
	debug_notice('request_port.php: login success');
	
	$t_output_array['login_succes'] = true;
	
	$t_action  = isset($_GET['action']) ? $_GET['action'] : '';
	$t_content = isset($_GET['content']) ? $_GET['content'] : '';
	
	if($t_action == 'ftp' && $t_content == 'move' || $t_content == 'delete' || $t_content == 'chmod')
	{
		debug_notice('request_port.php: action \'' . $t_action . '\', content \'' . $t_content . '\'');
		
		if(isset($_POST['FTP_HOST']) && isset($_POST['FTP_USER']) && isset($_POST['FTP_PASSWORD'])
		   && isset($_POST['FTP_PASV'])
		)
		{
			$coo_ftp_manager = FTPManager::get_instance(true, $_POST['FTP_HOST'], $_POST['FTP_USER'],
			                                            $_POST['FTP_PASSWORD'], $_POST['FTP_PASV']);
			
			if(isset($_POST['dir']) && $_POST['dir'] !== '/')
			{
				$t_dir = $_POST['dir'];
			}
			else
			{
				$t_dir = $coo_ftp_manager->find_shop_dir('/');
			}
			
			$t_output_array['html'] .= $coo_update_control->get_ftp_html($coo_ftp_manager, $t_dir, $t_content);
		}
	}
	else
	{
		$t_step                                           = isset($_GET['step']) ? $_GET['step'] : 'dependent';
		$t_refusion_array                                 = isset($_POST['section_phrase']) ? $_POST['section_phrase'] : array();
		$t_output_array['url']                            = '';
		$t_output_array['current_update']                 = '';
		$t_output_array['next_content']                   = '';
		$t_output_array['section_file_delete_info_array'] = array();
		$t_output_array['sql_errors']                     = '';
		
		$t_execute_independent_queries = false;
		$t_execute_dependent_queries   = false;
		$t_update_css                  = false;
		$t_update_sections             = false;
		$t_update_version_history      = false;
		
		switch($t_step)
		{
			case 'dependent':
				$t_execute_dependent_queries = true;
				$t_next_step                 = 'independent';
				break;
			case 'independent':
				$t_execute_independent_queries = true;
				$t_next_step                   = 'css';
				break;
			case 'css':
				$t_update_css = true;
				$t_next_step  = 'sections';
				break;
			case 'sections':
				$t_update_sections = true;
				$t_next_step       = 'history';
				break;
			case 'history':
				$t_update_version_history = true;
				$t_next_step              = 'dependent';
				break;
		}
		
		$t_current_update = !empty($coo_update_control->gambio_update_array) ? $coo_update_control->gambio_update_array[0]->get_update_name() : '';
		if(isset($_GET['current_update']))
		{
			$t_current_update = $_GET['current_update'];
		}
		
		debug_notice('request_port.php: action \'' . $t_action . '\', step \'' . $t_step . '\', current_update \''
		             . $t_current_update . '\'');
		
		switch($t_action)
		{
			case 'get_first_update':
				$t_output_array['current_update'] = $t_current_update;
				$t_output_array['url']            = 'request_port.php?action=install&language=' . $t_language;
				break;
			case 'error_log':
				$coo_logger = LogControl::get_instance();
				$coo_logger->write_stack();
				
				die();
				break;
			case 'set_installed_version':
				$coo_update_control->reset_current_shop_version();
				$coo_update_control->set_installed_version();
				break;
			case 'clear_cache':
				chdir(DIR_FS_CATALOG);
				
				$coo_update_control->clear_cache();
				unlink(DIR_FS_CATALOG . 'cache/update_dir_array.pdc');
				
				if(file_exists(DIR_FS_CATALOG . 'cache/additional_delete_list.pdc'))
				{
					unlink(DIR_FS_CATALOG . 'cache/additional_delete_list.pdc');
				}
				
				include_once(DIR_FS_CATALOG . 'includes/application_top_main.php');
				$t_output_array = $coo_update_control->rebuild_cache();
				
				break;
			case 'install':
			default:
				
				$t_additional_delete_list_array = array();
				
				foreach($coo_update_control->gambio_update_array as $coo_update)
				{
					$t_additional_delete_list_array = array_merge($t_additional_delete_list_array,
					                                              $coo_update->get_section_files_array(true));
				}
				
				if(isset($_POST['keep_list']) && !empty($_POST['keep_list']))
				{
					foreach($_POST['keep_list'] as $t_value)
					{
						if($t_value !== '')
						{
							$t_key = array_search($t_value, $t_additional_delete_list_array);
							if($t_key !== false)
							{
								unset($t_additional_delete_list_array[$t_key]);
							}
						}
					}
				}
				
				if(count($t_additional_delete_list_array) > 0)
				{
					file_put_contents(DIR_FS_CATALOG . 'cache/additional_delete_list.pdc',
					                  serialize($t_additional_delete_list_array));
				}
				
				$t_success = $coo_update_control->update($t_current_update, $t_refusion_array,
				                                         $t_execute_dependent_queries, $t_execute_independent_queries,
				                                         $t_update_css, $t_update_sections, $t_update_version_history);
				if($t_success)
				{
					if($t_update_sections)
					{
						$t_output_array['section_file_delete_info_array'] = $coo_update_control->section_file_delete_info_array;
					}
					
					if($coo_update_control->get_rerun_step())
					{
						$t_next_step = $t_step;
					}
					
					$t_next_update = $t_current_update;
					if($t_update_version_history == true && $t_next_step != $t_step)
					{
						$t_next_update = '';
						
						foreach($coo_update_control->gambio_update_array AS $t_key => $coo_update_model)
						{
							if($coo_update_model->get_update_name() == $t_current_update)
							{
								if(isset($coo_update_control->gambio_update_array[$t_key + 1]))
								{
									$t_next_update = $coo_update_control->gambio_update_array[$t_key
									                                                          + 1]->get_update_name();
								}
								
								break;
							}
						}
					}
					
					if($t_next_update != '')
					{
						$t_output_array['url']            = 'request_port.php?action=install&language=' . $t_language
						                                    . '&current_update=' . rawurlencode($t_next_update)
						                                    . '&step=' . $t_next_step;
						$t_output_array['current_update'] = $t_next_update;
					}
					else
					{
						$t_update_dir_array = unserialize(file_get_contents(DIR_FS_CATALOG
						                                                    . 'cache/update_dir_array.pdc'));
						$coo_update_control->rebuild_gambio_update_array($t_update_dir_array);
						
						$t_delete_array = $coo_update_control->get_delete_list();
						$t_move_array   = $coo_update_control->get_move_array();
						$t_chmod_array  = $coo_update_control->get_chmod_array();
						
						if(empty($t_chmod_array) === false)
						{
							$t_output_array['next_content'] = 'chmod';
						}
						
						if(empty($t_delete_array) === false)
						{
							$t_output_array['next_content'] = 'delete_files';
						}
						
						if(empty($t_move_array) === false)
						{
							$t_output_array['next_content'] = 'move';
						}
					}
				}
				
				$t_sql_errors_array = $coo_update_control->get_sql_errors_array();
				if(count($t_sql_errors_array) > 0)
				{
					foreach($t_sql_errors_array as $t_data_array)
					{
						$t_output_array['sql_errors'] .= 'Query: ' . $t_data_array['query'] . "\n" . 'Error message: '
						                                 . $t_data_array['error'] . "\n\n";
					}
				}
				else
				{
					if(!$t_success)
					{
						/** @var GambioUpdateModel $coo_update */
						$error_msg = str_replace('x.x.x.x', $t_current_update, ERROR_SQL_UNKNOWN);
						$t_output_array['sql_errors'] .= $error_msg . "\n\n";
					}
				}
				
				break;
		}
	}
}

if(function_exists('json_encode'))
{
	$t_output_json = json_encode($t_output_array);
}
else
{
	require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');
	$coo_json      = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
	$t_output_json = $coo_json->encode($t_output_array);
}

debug_notice('request_port.php: JSON-output: ' . $t_output_json);

$coo_logger = LogControl::get_instance();
$coo_logger->write_stack();

echo $t_output_json;
