<?php
/* --------------------------------------------------------------
   application.php 2016-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application.php,v 1.4 2002/11/29); www.oscommerce.com
   (c) 2003	 nextcommerce (application.php,v 1.16 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application.php 1119 2005-07-25 22:19:50Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// Set the level of error reporting
if(defined('E_DEPRECATED'))
{
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}
else
{
	error_reporting(E_ALL & ~E_NOTICE);
}

$t_memory_limit = 128;

@date_default_timezone_set('Europe/Berlin');

function gm_delete_get_parameters($string)
{
	if(strpos($string, '?') !== false)
	{
		$string = substr($string, 0, strpos($string, '?'));
	}

	return $string;
}


function gm_magic_check($string)
{
	if(preg_match('/(^"|[^\\\]{1}")/', $string) == 1)
	{
		return false;
	}
	if(preg_match('/(^\'|[^\\\]{1}\')/', $string) == 1)
	{
		return false;
	}
	else
	{
		return true;
	}
}


function gm_prepare_string($string, $strip = false)
{
	if(!$strip)
	{
		if(ini_get('magic_quotes_gpc') == 0 || ini_get('magic_quotes_gpc') == 'Off' ||
		   ini_get('magic_quotes_gpc') == 'off'
		)
		{
			if(!gm_magic_check($string))
			{
				$string = addslashes($string);
			}
		}
	}
	else
	{
		if(ini_get('magic_quotes_gpc') == 1 || ini_get('magic_quotes_gpc') == 'On' ||
		   ini_get('magic_quotes_gpc') == 'on'
		)
		{
			$string = stripslashes($string);
		}
		else
		{
			if(gm_magic_check($string))
			{
				$string = stripslashes($string);
			}
		}
	}

	return $string;
}


function gm_document_root()
{
	if(file_exists(getcwd() . '/index.php'))
	{
		$gm_relative = $_SERVER['PHP_SELF'];
		
		if(empty($gm_relative))
		{
			$gm_relative = $_SERVER['SCRIPT_NAME'];
		}
		
		if(empty($gm_relative))
		{
			$gm_relative = $_SERVER['REQUEST_URI'];
		}
		
		if(empty($gm_relative))
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}

		$gm_relative = gm_delete_get_parameters($gm_relative);

		$gm_pos           = strrpos(getcwd(), dirname($gm_relative));
		$gm_document_root = substr(getcwd(), 0, $gm_pos);
	}
	elseif(file_exists(__FILE__))
	{
		$gm_relative = $_SERVER['PHP_SELF'];
		if(empty($gm_relative))
		{
			$gm_relative = $_SERVER['SCRIPT_NAME'];
		}
		if(empty($gm_relative))
		{
			$gm_relative = $_SERVER['REQUEST_URI'];
		}
		if(empty($gm_relative))
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}

		$gm_relative = gm_delete_get_parameters($gm_relative);

		$gm_pos           = strrpos(dirname(__FILE__), dirname($gm_relative));
		$gm_document_root = substr(dirname(__FILE__), 0, $gm_pos);
	}
	else
	{
		$gm_document_root = $_SERVER['DOCUMENT_ROOT'];
	}

	$gm_document_root = str_replace("\\", '/', $gm_document_root);
	$gm_document_root = str_replace('//', '/', $gm_document_root);

	return $gm_document_root;
}

function gm_local_install_path()
{
	if(file_exists(getcwd() . '/index.php'))
	{
		$gm_pos                = strrpos(getcwd(), 'gambio_installer');
		$gm_local_install_path = substr(getcwd(), 0, $gm_pos);
	}
	elseif(file_exists(__FILE__))
	{
		$gm_pos                = strrpos(dirname(__FILE__), 'gambio_installer');
		$gm_local_install_path = substr(dirname(__FILE__), 0, $gm_pos);
	}
	else
	{
		$gm_relative = $_SERVER['PHP_SELF'];
		
		if(empty($gm_relative))
		{
			$gm_relative = $_SERVER['SCRIPT_NAME'];
		}
		
		if(empty($gm_relative))
		{
			$gm_relative = $_SERVER['REQUEST_URI'];
		}
		
		$gm_relative           = gm_delete_get_parameters($gm_relative);
		$local_install_path    = str_replace('/gambio_installer', '', $gm_relative);
		$local_install_path    = str_replace('index.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step1.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step2.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step3.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step4.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step5.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step6.php', '', $local_install_path);
		$local_install_path    = str_replace('install_step7.php', '', $local_install_path);
		$local_install_path    = str_replace('install_finished.php', '', $local_install_path);
		$gm_local_install_path = $_SERVER['DOCUMENT_ROOT'] . $local_install_path;
	}

	$gm_local_install_path = str_replace("\\", '/', $gm_local_install_path);
	$gm_local_install_path = str_replace('//', '/', $gm_local_install_path);

	return $gm_local_install_path;
}

function gm_get_tables(array $inSetupShopCreatedTables)
{
	$sqlDirectoryTableFileNames = array();
	$dir = dir('sql/');
	while($filename = $dir->read())
	{
		if($filename !== '.' && $filename !== '..')
		{
			$sqlDirectoryTableFileNames[] = str_replace('.sql', '', $filename);
		}
	}
	$dir->close();

	$tablesArray = array_merge($sqlDirectoryTableFileNames, $inSetupShopCreatedTables);
	sort($tablesArray);

	return $tablesArray;
}


if(!defined('DIR_FS_DOCUMENT_ROOT'))
{
	define('DIR_FS_DOCUMENT_ROOT', gm_document_root());
	define('DIR_FS_CATALOG', gm_local_install_path());
	$gm_relative = $_SERVER['PHP_SELF'];
	
	if(empty($gm_relative))
	{
		$gm_relative = $_SERVER['SCRIPT_NAME'];
	}
	
	if(empty($gm_relative))
	{
		$gm_relative = $_SERVER['REQUEST_URI'];
	}
	
	$gm_relative        = gm_delete_get_parameters($gm_relative);
	$local_install_path = str_replace('/gambio_installer', '', $gm_relative);
	$local_install_path = str_replace('index.php', '', $local_install_path);
	$local_install_path = str_replace('install_step1.php', '', $local_install_path);
	$local_install_path = str_replace('install_step2.php', '', $local_install_path);
	$local_install_path = str_replace('install_step3.php', '', $local_install_path);
	$local_install_path = str_replace('install_step4.php', '', $local_install_path);
	$local_install_path = str_replace('install_step5.php', '', $local_install_path);
	$local_install_path = str_replace('install_step6.php', '', $local_install_path);
	$local_install_path = str_replace('install_step7.php', '', $local_install_path);
	$local_install_path = str_replace('install_finished.php', '', $local_install_path);
}

if(!defined('DIR_FS_INC'))
{
	define('DIR_FS_INC', DIR_FS_CATALOG . 'inc/');
}

require_once(DIR_FS_INC . 'set_memory_limit.inc.php');
$t_memory_limit_ok = set_memory_limit($t_memory_limit);

$gm_test = DIR_FS_CATALOG . 'includes/classes/boxes.php';

if(!file_exists($gm_test))
{
	die('Befindet sich der Installer im Ordner &quot;gambio_installer&quot;? Dies ist f&uuml;r die Installation zwingend erforderlich. Erscheint diese Meldung, obwohl sich der Installer im richtigen Verzeichnis befindet, kann der Installer aufgrund der Serverkonfiguration die Verzeichnispfade nicht auslesen. Daher ist die  Installation mittels des Installers leider nicht m&ouml;glich. Wenden Sie sich bitte an den Gambio-Support, der Ihnen bei der Installation behilflich sein wird.');
}

require_once(DIR_FS_INC . 'htmlentities_wrapper.inc.php');
require_once(DIR_FS_INC . 'htmlspecialchars_wrapper.inc.php');
require_once(DIR_FS_INC . 'html_entity_decode_wrapper.inc.php');

require_once(DIR_FS_CATALOG . 'system/core/logging/LogEvent.inc.php');
require_once(DIR_FS_CATALOG . 'system/core/logging/LogControl.inc.php');
require_once(DIR_FS_CATALOG . 'gm/classes/ErrorHandler.php');
require_once(DIR_FS_CATALOG . 'gm/inc/check_data_type.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_env_info.inc.php');
require_once(DIR_FS_CATALOG . 'system/gngp_layer_init.inc.php');

# custom error handler with DEFAULT SETTINGS
register_shutdown_function(array(new ErrorHandler(), 'shutdown'));
set_error_handler(array(new ErrorHandler(), 'HandleError'));

# custom class autoloader
spl_autoload_register(array(new MainAutoloader('frontend'), 'load'));

// include
require_once(DIR_FS_CATALOG . 'includes/classes/boxes.php');
require_once(DIR_FS_CATALOG . 'includes/classes/message_stack.php');
require_once(DIR_FS_CATALOG . 'includes/filenames.php');
require_once(DIR_FS_CATALOG . 'includes/database_tables.php');
require_once(DIR_FS_CATALOG . 'inc/xtc_image.inc.php');

# Session Handling

$t_session_started = true;

@session_start();
$_SESSION['session_test'] = true;
@session_write_close();

@session_start();

if(!isset($_SESSION['session_test']))
{
	@session_write_close();

	$t_session_save_path = (string)ini_get('upload_tmp_dir');
	@session_save_path($t_session_save_path);
	@session_start();
	$_SESSION['session_test'] = true;
	@session_write_close();

	@session_save_path($t_session_save_path);
	@session_start();

	if(!isset($_SESSION['session_test']))
	{
		@session_write_close();

		$t_session_save_path = gm_local_install_path() . 'cache';
		@session_save_path($t_session_save_path);
		@session_start();
		$_SESSION['session_test'] = true;
		@session_write_close();

		@session_save_path($t_session_save_path);
		@session_start();

		if(!isset($_SESSION['session_test']))
		{
			$t_session_started = false;
		}
	}
}

unset($_SESSION['session_test']);


# set installer language
if(isset($_GET['language']))
{
	switch($_GET['language'])
	{
		case 'english':
			$_SESSION['language'] = 'english';
			break;
		default:
			$_SESSION['language'] = 'german';
	}
}

if(empty($_SESSION['language']))
{
	$_SESSION['language'] = 'german';
}

// Set the level of error reporting
if(defined('E_DEPRECATED'))
{
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}
else
{
	error_reporting(E_ALL & ~E_NOTICE);
}

// include General functions
require_once(DIR_FS_INC . 'xtc_set_time_limit.inc.php');
require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
require_once(DIR_FS_INC . 'xtc_in_array.inc.php');

// Include Database functions for installer
require_once(DIR_FS_INC . 'xtc_db_prepare_input.inc.php');
require_once(DIR_FS_INC . 'xtc_db_connect_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_db_select_db.inc.php');
require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
require_once(DIR_FS_INC . 'xtc_db_query_installer.inc.php');
require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
require_once(DIR_FS_INC . 'xtc_db_num_rows.inc.php');
require_once(DIR_FS_INC . 'xtc_db_data_seek.inc.php');
require_once(DIR_FS_INC . 'xtc_db_insert_id.inc.php');
require_once(DIR_FS_INC . 'xtc_db_free_result.inc.php');
require_once(DIR_FS_INC . 'xtc_db_test_create_db_permission.inc.php');
require_once(DIR_FS_INC . 'xtc_db_test_connection.inc.php');
require_once(DIR_FS_INC . 'xtc_db_install.inc.php');

// include Html output functions
require_once(DIR_FS_INC . 'xtc_draw_hidden_field_installer.inc.php');

if(!defined('DIR_WS_ICONS'))
{
	define('DIR_WS_ICONS', 'images/');
}