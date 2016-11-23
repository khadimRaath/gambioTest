<?php
/* --------------------------------------------------------------
   gm_dynamic.css.php 2016-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
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

@date_default_timezone_set('Europe/Berlin');

define('PAGE_PARSE_START_TIME', microtime());

header('Content-Type: text/css; charset=utf-8');

if (file_exists('../../includes/local/configure.php')) {
	include ('../../includes/local/configure.php');
} else {
	include ('../../includes/configure.php');
}

$t_current_template = basename(dirname(__FILE__));
if(isset($_GET['current_template']) && empty($_GET['current_template']) == false && is_dir(DIR_FS_CATALOG . 'templates/' . basename($_GET['current_template']) . '/usermod'))
{
	$t_current_template = basename($_GET['current_template']);
}

$t_usermod_files = array();

$t_path_pattern = DIR_FS_CATALOG . 'templates/' . $t_current_template . '/usermod/css/*.css';

$t_glob_data_array = glob($t_path_pattern);
if(is_array($t_glob_data_array))
{
	foreach($t_glob_data_array AS $t_result)
	{
		$t_entry = basename($t_result);

		$t_usermod_files[] = DIR_FS_CATALOG . 'templates/' . $t_current_template . '/usermod/css/' . $t_entry;
	}
}

if((int)$_GET['gm_css_debug'] == 1)
{
	$t_debug = true;
	@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
}

$t_renew = false;
if((int)$_GET['renew'] == 1)
{
	$t_renew = true;
}

$t_static_css_file = DIR_FS_CATALOG . 'templates/' . $t_current_template . '/' . $t_current_template . '.css';
$cache_file 	= DIR_FS_CATALOG . 'cache/__dynamics.css';
$create_cache = false;

if($_GET['renew_cache'] == '1')
{
	$create_cache = true;
}
elseif(file_exists($cache_file) == false)
{
	$create_cache = true;
}
elseif(filesize($cache_file) < 10)
{
	$create_cache = true;
}

function getFilemtime($p_file)
{
	$lastModified = filemtime($p_file);

	// Windows time fix
	if(date('I', $lastModified) != 1 && date('I') == 1)
	{
		$lastModified += 3600;
	}
	elseif(date('I', $lastModified) == 1 && date('I') != 1)
	{
		$lastModified -= 3600;
	}
	
	return $lastModified;
}

if($create_cache === $t_renew && $_GET['stop_style_edit'] != '1')
{
	if($_GET['http_caching'] === 'true')
	{
		$lastModified = getFilemtime($cache_file);

		$additionalCssFiles = $t_usermod_files;
		if(file_exists(DIR_FS_CATALOG . 'templates/' . $t_current_template . '/stylesheet.css'))
		{
			$additionalCssFiles[] = DIR_FS_CATALOG . 'templates/' . $t_current_template . '/stylesheet.css';
		}

		foreach($additionalCssFiles as $file)
		{
			if(getFilemtime($file) > $lastModified)
			{
				$lastModified = getFilemtime($file);
			}
		}

		header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastModified) . ' GMT');
		header('Cache-Control: public');

		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
			@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified)
		{
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
	}

	// GZip compression
	if($_GET['gzip'] === 'true' && extension_loaded('zlib') && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6.') === false)
	{
		if($_GET['ob_gzhandler'] === 'false')
		{
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression', 'On');
			}
		}

		if(strtolower((string)ini_get('zlib.output_compression') == 'off') || (string)ini_get('zlib.output_compression') == '0' || $_GET['ob_gzhandler'] === 'true')
		{
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression', 'Off');
			}

			$t_buffer = ob_start("ob_gzhandler");

			if($t_buffer === false)
			{
				ob_start();
			}
		}
		else
		{
			$t_output_compression_level = (int)$_GET['gzip_level'];
			if($t_output_compression_level < 1 || $t_output_compression_level > 9)
			{
				$t_output_compression_level = 9;
			}
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression_level', $t_output_compression_level);
			}
		}
	}

	if(file_exists(DIR_FS_CATALOG . 'templates/' . $t_current_template . '/stylesheet.css'))
	{
		include(DIR_FS_CATALOG . 'templates/' . $t_current_template . '/stylesheet.css');

		// print comment to close unclosed comment in included file
		echo "\n/**/\n";
	}

	include($cache_file);

	foreach($t_usermod_files AS $t_file)
	{
		include($t_file);

		// print comment to close unclosed comment in included file
		echo "\n/**/\n";
	}
}
else
{
	$port   = isset(explode(':', DB_SERVER)[1]) && is_numeric(explode(':', DB_SERVER)[1]) ? (int)explode(':', DB_SERVER)[1] : null;
	$socket = isset(explode(':', DB_SERVER)[1]) && !is_numeric(explode(':', DB_SERVER)[1]) ? explode(':', DB_SERVER)[1] : null;
	$host   = explode(':', DB_SERVER)[0];
	
	if(USE_PCONNECT == 'true')
	{
		$conn_id = ($GLOBALS["___mysqli_ston"] = mysqli_connect(
											'p:' . $host, 
											DB_SERVER_USERNAME, 
											DB_SERVER_PASSWORD,
											DB_DATABASE,
											$port,
											$socket
		));
	}
	else
	{
		$conn_id = ($GLOBALS["___mysqli_ston"] = mysqli_connect(
											$host, 
											DB_SERVER_USERNAME, 
											DB_SERVER_PASSWORD,
											DB_DATABASE,
											$port,
											$socket
		));
	}

	$t_mysql_version = @((is_null($___mysqli_res = mysqli_get_server_info($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	if(!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>='))
	{
		@mysqli_query( $conn_id, "SET SESSION sql_mode=''");
	}
	
	@mysqli_query( $conn_id, "SET SQL_BIG_SELECTS=1");
	
	((bool)mysqli_query( $conn_id, "USE " . constant('DB_DATABASE')));
	
	if(version_compare(PHP_VERSION, '5.2.3', '>='))
	{
		//mysql_set_charset('utf8', $conn_id);
	}
	else
	{
		mysqli_query( $conn_id, "SET NAMES utf8");
	}

	// GZip compression
	if($_GET['gzip'] === 'true' && extension_loaded('zlib') && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6.') === false)
	{
		if($_GET['ob_gzhandler'] === 'false')
		{
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression', 'On');
			}
		}

		if(strtolower((string)ini_get('zlib.output_compression') == 'off') || (string)ini_get('zlib.output_compression') == '0' || $_GET['ob_gzhandler'] === 'true')
		{
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression', 'Off');
			}

			$t_buffer = ob_start("ob_gzhandler");

			if($t_buffer === false)
			{
				ob_start();
			}
		}
		else
		{
			$t_output_compression_level = (int)$_GET['gzip_level'];
			if($t_output_compression_level < 1 || $t_output_compression_level > 9)
			{
				$t_output_compression_level = 9;
			}
			if(headers_sent() === false)
			{
				@ini_set('zlib.output_compression_level', $t_output_compression_level);
			}
		}
	}

	if(file_exists(DIR_FS_CATALOG . 'templates/' . $t_current_template . '/stylesheet.css'))
	{
		include(DIR_FS_CATALOG . 'templates/' . $t_current_template . '/stylesheet.css');

		// print comment to close unclosed comment in included file
		echo "\n/**/\n";
	}

	$t_sql = 'SHOW TABLES LIKE "gm_css_style"';
	$t_result = mysqli_query($GLOBALS["___mysqli_ston"], $t_sql);
	if(mysqli_num_rows($t_result) == 1)
	{
		require(DIR_FS_CATALOG . 'gm/classes/csstidy/class.csstidy.php');
		require(DIR_FS_CATALOG . 'gm/classes/GMCSSOptimizer.php');

		$t_style_edit = false;

		if($_GET['style_edit'] == '1')
		{
			$t_style_edit = true;
			$t_debug = true;
		}

		$coo_css = new GMCSSOptimizer($t_debug, $t_style_edit);

		$coo_css->create_css();

		$coo_css->save_css();
	
		echo $coo_css->get_css();
	}
	elseif(file_exists($t_static_css_file) && is_readable($t_static_css_file))
	{
		$t_static_css = file_get_contents($t_static_css_file);
		file_put_contents($cache_file, $t_static_css);
		include($t_static_css_file);
	}

	foreach($t_usermod_files AS $t_file)
	{
		include($t_file);

		// print comment to close unclosed comment in included file
		echo "\n/**/\n";
	}

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}
