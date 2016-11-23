<?php
/* --------------------------------------------------------------
   request_port.php 2015-02-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

if(defined('E_DEPRECATED'))
{
	error_reporting(
			E_ALL
			& ~E_NOTICE
			& ~E_DEPRECATED
			& ~E_STRICT
			& ~E_CORE_ERROR
			& ~E_CORE_WARNING
	);
}
else
{
	error_reporting(
			E_ALL
			& ~E_NOTICE
			& ~E_STRICT
			& ~E_CORE_ERROR
			& ~E_CORE_WARNING
	);
}	

if($_POST['action'] == 'setup_shop' || $_POST['action'] == 'create_account')
{
	require('../includes/configure.php');
}

require_once('includes/application.php');
require_once('includes/FTPManager.inc.php');
require_once('../gm/classes/JSON.php');

$t_output = '';

switch($_POST['action'])
{
	case 'test_db_connection':

		$t_db_array = array();
		$t_db_array['DB_SERVER'] = trim(stripslashes($_POST['DB_SERVER']));
		$t_db_array['DB_SERVER_USERNAME'] = trim(stripslashes($_POST['DB_SERVER_USERNAME']));
		$t_db_array['DB_SERVER_PASSWORD'] = trim(stripslashes($_POST['DB_SERVER_PASSWORD']));
		$t_db_array['DB_DATABASE'] = trim(stripslashes($_POST['DB_DATABASE']));

		xtc_db_connect_installer($t_db_array['DB_SERVER'], $t_db_array['DB_SERVER_USERNAME'], $t_db_array['DB_SERVER_PASSWORD']);

		if(!$db_error)
		{
			$t_select_db = xtc_db_select_db($t_db_array['DB_DATABASE']);
			if(!$t_select_db)
			{
				$db_error = 'No database selected';
			}
			else
			{
				@mysqli_query($GLOBALS["___mysqli_ston"], "DROP TABLE `gambio_test_db`");
				
				$t_create = @mysqli_query($GLOBALS["___mysqli_ston"], "CREATE TABLE `gambio_test_db` (
							`id` INT NOT NULL ,
							PRIMARY KEY ( `id` )
							) ENGINE = MYISAM");
				if($t_create === false)
				{
					$t_output = 'CREATE';
					break;
				}
				else
				{
					$t_insert = @mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO `gambio_test_db` SET `id` = 1");
					if($t_insert === false)
					{
						$t_output = 'INSERT';
						break;
					}
					else
					{
						$t_update = @mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE `gambio_test_db` SET `id` = 2");
						if($t_update === false)
						{
							$t_output = 'UPDATE';
							break;
						}
						else
						{
							$t_select = @mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `gambio_test_db`");
							if($t_select === false)
							{
								$t_output = 'SELECT';
								break;
							}
							else
							{
								$t_alter = @mysqli_query($GLOBALS["___mysqli_ston"], "ALTER TABLE `gambio_test_db` CHANGE `id` `id_new` INT( 11 ) NOT NULL");
								if($t_alter === false)
								{
									$t_output = 'ALTER';
									break;
								}
								else
								{
									$t_delete = @mysqli_query($GLOBALS["___mysqli_ston"], "DELETE FROM `gambio_test_db` WHERE `id_new` = 2");
									if($t_delete === false)
									{
										$t_output = 'DELETE';
										break;
									}
									else
									{
										$t_drop = @mysqli_query($GLOBALS["___mysqli_ston"], "DROP TABLE `gambio_test_db`");
										if($t_drop === false)
										{
											$t_output = 'DROP';
											break;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$t_output = 'no connection';
			break;
		}

		if(!$db_error)
		{
			$gm_show_tables = mysqli_query($GLOBALS["___mysqli_ston"], "SHOW TABLES");
			$gm_tables = array();
			while($row = mysqli_fetch_array($gm_show_tables))
			{
				// The array $inSetupCreatedTables contain the table names which have been created in the setup_shop.php
				// file. The array need to become maintain and each time when the setup_shop.php create new tables, this
				// array also must be extended.
				$inSetupCreatedTables       = array(
					'personal_offers_by_customers_status_0',
					'personal_offers_by_customers_status_1',
					'personal_offers_by_customers_status_2',
					'personal_offers_by_customers_status_3'
				);
				$tablesArray = gm_get_tables($inSetupCreatedTables);
				
				$found = false;
				foreach($tablesArray as $tableName)
				{
					if((string)trim($tableName) === (string)$row[0])
					{
						$found = true;
					}
				}
				
				if($found)
				{
					$gm_tables[] = '<strong><font color="red">' . $row[0] . '</font></strong>';
				}
				else
				{
					$gm_tables[] = $row[0];
				}
			}

			if(!empty($gm_tables))
			{
				$t_output = implode('<br />', $gm_tables);
			}
			else
			{
				$t_output = 'success';
			}
		}
		elseif($db_error)
		{
			$t_output = 'no database';
		}
		
		break;
	case 'import_sql':

		include_once('includes/import_sql.php');

		break;
	case 'write_config':

		include_once('includes/write_config.php');

		break;
	case 'create_account':

		include_once('includes/create_account.php');
		$t_output = 'success';

		break;
	case 'setup_shop':

		include_once('includes/setup_shop.php');
		$t_output = 'success';

		break;
	case 'get_countries':
		require_once('../includes/configure.php');
		require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
		require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
		require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
		require_once(DIR_FS_INC .'xtc_db_num_rows.inc.php');
		require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
		require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');
		require_once(DIR_FS_CATALOG.'gm/inc/gm_get_env_info.inc.php');
		require_once(DIR_FS_CATALOG.'system/core/logging/Debugger.inc.php');
		require_once(DIR_FS_CATALOG.'system/core/caching/DataCache.inc.php');

		// connect do database
		xtc_db_connect() or die('Unable to connect to database server!');
		
		$t_output = xtc_get_country_list('COUNTRY', (int)$_POST['COUNTRY']);

		break;
	case 'get_states':
		require_once('../includes/configure.php');
		require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
		require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
		require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
		require_once(DIR_FS_INC .'xtc_db_num_rows.inc.php');
		require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
		require_once(DIR_FS_INC . 'xtc_draw_pull_down_menu.inc.php');
		require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');
		require_once(DIR_FS_CATALOG.'gm/inc/gm_get_env_info.inc.php');
		require_once(DIR_FS_CATALOG.'system/core/logging/Debugger.inc.php');
		require_once(DIR_FS_CATALOG.'system/core/caching/DataCache.inc.php');

		// connect do database
		xtc_db_connect() or die('Unable to connect to database server!');

		$check_query = xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$_POST['COUNTRY'] . "'");
		$check = xtc_db_fetch_array($check_query);
		$entry_state_has_zones = ($check['total'] > 0);

		if ($check['total'] > 0)
		{
			$zones_array = array();
			$zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$_POST['COUNTRY'] . "' order by zone_name");
			while ($zones_values = xtc_db_fetch_array($zones_query)) {
				$zones_array[] = array('id' => htmlentities_wrapper($zones_values['zone_name']), 'text' => htmlentities_wrapper($zones_values['zone_name']));
			}
			$t_output =  xtc_draw_pull_down_menu('STATE', $zones_array);
		}
		else
		{
			$t_output =  xtc_draw_input_field('STATE');
		}

		break;
	case 'write_robots_file':
		$t_output = 'failed';
		
		if(!empty($_SESSION['FTP_HOST']) && !empty($_SESSION['FTP_USER']) && !empty($_SESSION['FTP_PASSWORD']) && !empty($_SESSION['FTP_PATH']))
		{
			$t_host = $_SESSION['FTP_HOST'];
			$t_user = $_SESSION['FTP_USER'];
			$t_password = $_SESSION['FTP_PASSWORD'];
			$t_pasv = false;
			if(!empty($_SESSION['FTP_PASV'])) $t_pasv = true;

			$coo_ftp_manager = new FTPManager(true, $t_host, $t_user, $t_password, $t_pasv);

			if($coo_ftp_manager->v_error == '')
			{
				if (!$script_filename = str_replace("\\", '/', getenv('PATH_TRANSLATED'))) {
				$script_filename = getenv('SCRIPT_FILENAME');
				}
				$script_filename = str_replace('//', '/', $script_filename);

				if (!$request_uri = getenv('REQUEST_URI')) {
					if (!$request_uri = getenv('PATH_INFO')) {
						$request_uri = getenv('SCRIPT_NAME');
					}

					if (getenv('QUERY_STRING')) $request_uri .=  '?' . getenv('QUERY_STRING');
				}

				$dir_fs_www_root_array = explode('/', dirname($script_filename));
				$dir_fs_www_root = array();
				for ($i=0; $i<sizeof($dir_fs_www_root_array)-2; $i++) {
					$dir_fs_www_root[] = $dir_fs_www_root_array[$i];
				}
				$dir_fs_www_root = implode('/', $dir_fs_www_root);

				$dir_ws_www_root_array = explode('/', dirname($request_uri));
				$dir_ws_www_root = array();
				for ($i=0; $i<sizeof($dir_ws_www_root_array)-1; $i++) {
					$dir_ws_www_root[] = $dir_ws_www_root_array[$i];
				}
				$dir_ws_www_root = implode('/', $dir_ws_www_root);
				$dir_ws_www_root .= '/';
				
				$t_https_server = false;
				if($_POST['ENABLE_SSL'] == 'true')
				{
					$t_https_server = rawurldecode($_POST['HTTPS_SERVER']);
				}

				$t_success = $coo_ftp_manager->write_robots_file($_SESSION['FTP_PATH'], $dir_ws_www_root, $t_https_server);

				if($t_success)
				{
					$t_output = 'success';
				}
			}
		}
		
		// clear cache of previous installation
		
		include_once(DIR_FS_CATALOG.'system/core/caching/CacheControl.inc.php');
		
		$coo_cache_control = new CacheControl();
		$coo_cache_control->clear_data_cache();
		$coo_cache_control->clear_content_view_cache();
		$coo_cache_control->clear_templates_c();
		$coo_cache_control->clear_css_cache();

		break;
	case 'chmod_444':
		$t_output = 'failed';

		if(!empty($_SESSION['FTP_HOST']) && !empty($_SESSION['FTP_USER']) && !empty($_SESSION['FTP_PASSWORD']) && !empty($_SESSION['FTP_PATH']))
		{
			$t_host = $_SESSION['FTP_HOST'];
			$t_user = $_SESSION['FTP_USER'];
			$t_password = $_SESSION['FTP_PASSWORD'];
			$t_pasv = false;
			if(!empty($_SESSION['FTP_PASV'])) $t_pasv = true;

			$coo_ftp_manager = new FTPManager(true, $t_host, $t_user, $t_password, $t_pasv);

			if($coo_ftp_manager->v_error == '')
			{
				$t_success = $coo_ftp_manager->chmod_444($_SESSION['FTP_PATH']);

				if($t_success)
				{
					$t_output = 'success';
				}
			}
		}

		break;
}

if(is_object($coo_ftp_manager))
{
	$coo_ftp_manager->quit();
}

$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
$t_output_json = $coo_json->encode($t_output);

echo $t_output_json;

@((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);