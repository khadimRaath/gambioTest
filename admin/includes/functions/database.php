<?php
/* --------------------------------------------------------------
  database.php 2016-07-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(database.php,v 1.22 2003/03/22); www.oscommerce.com
  (c) 2003	 nextcommerce (database.php,v 1.6 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: database.php 950 2005-05-14 16:45:21Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_FS_INC . 'xtc_db_perform.inc.php');

function xtc_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link')
{
	global $$link;
	
	$port   = isset(explode(':', $server)[1]) && is_numeric(explode(':', $server)[1]) ? (int)explode(':', $server)[1] : null;
	$socket = isset(explode(':', $server)[1]) && !is_numeric(explode(':', $server)[1]) ? explode(':', $server)[1] : null;
	$server = explode(':', $server)[0];
	
	if(USE_PCONNECT == 'true')
	{
		$$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect('p:' . $server, $username, $password, $database, $port, $socket));
	}
	else
	{
		$$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect($server, $username, $password, $database, $port, $socket));
	}

	if($$link)
	{
		$t_mysql_version = @((is_null($___mysqli_res = mysqli_get_server_info($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		if(!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>='))
		{
			@mysqli_query( $$link, "SET SESSION sql_mode=''");
		}

		@mysqli_query( $$link, "SET SQL_BIG_SELECTS=1");

		((bool)mysqli_query( $$link, "USE " . $database));

		if(version_compare(PHP_VERSION, '5.2.3', '>='))
		{
			mysqli_set_charset($$link, 'utf8');
		}
		else
		{
			mysqli_query( $$link, "SET NAMES utf8");
		}
	}

	return $$link;
}

// db connection for Servicedatabase  
function service_xtc_db_connect($server_service = SERVICE_DB_SERVER, $username_service = SERVICE_DB_SERVER_USERNAME, $password_service = SERVICE_DB_SERVER_PASSWORD, $database_service = SERVICE_DB_DATABASE, $link_service = 'db_link_service')
{
	global $$link_service;
	
	$port           = isset(explode(':', $server_service)[1]) && is_numeric(explode(':', $server_service)[1]) ? (int)explode(':', $server_service)[1] : null;
	$socket         = isset(explode(':', $server_service)[1]) && !is_numeric(explode(':', $server_service)[1]) ? explode(':', $server_service)[1] : null;
	$server_service = explode(':', $server_service)[0];
	
	if(SERVICE_USE_PCONNECT == 'true')
	{
		$$link_service = ($GLOBALS["___mysqli_ston"] = mysqli_connect('p:' . $server_service,  $username_service,  $password_service, $database_service, $port, $socket));
	}
	else
	{
		$$link_service = ($GLOBALS["___mysqli_ston"] = mysqli_connect($server_service,  $username_service,  $password_service, $database_service, $port, $socket));
	}

	if($$link_service)
	{
		$t_mysql_version = @((is_null($___mysqli_res = mysqli_get_server_info($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		if(!empty($t_mysql_version) && version_compare($t_mysql_version, '5', '>='))
		{
			@mysqli_query( $$link_service, "SET SESSION sql_mode=''");
		}

		@mysqli_query( $$link_service, "SET SQL_BIG_SELECTS=1");

		((bool)mysqli_query( $$link_service, "USE " . $database_service));

		if(version_compare(PHP_VERSION, '5.2.3', '>='))
		{
			mysqli_set_charset($$link_service, 'utf8');
		}
		else
		{
			mysqli_query( $$link_service, "SET NAMES utf8");
		}
	}

	return $$link_service;
}

function xtc_db_close($p_link = 'db_link')
{
	$t_link = $GLOBALS[$p_link];
	
	$t_close_result = ((is_null($___mysqli_res = mysqli_close($t_link))) ? false : $___mysqli_res);
	
	return $t_close_result;
}

// db connection for Servicedatabase  
function service_xtc_db_close($link_service = 'db_link_service')
{
	global $$link_service;

	return ((is_null($___mysqli_res = mysqli_close($$link_service))) ? false : $___mysqli_res);
}

function xtc_db_error($p_query, $p_errno, $p_error)
{
	$coo_logger = LogControl::get_instance();
	$coo_logger->notice($p_error, 'error_handler', 'errors', 'notice', 'SQL ERROR', $p_errno, 'Query:' . "\r\n" . trim($p_query));
	trigger_error('SQL Error', E_USER_ERROR);
}

function xtc_db_query($query, $link = 'db_link', $p_enable_data_cache = false)
{
	global $$link;
	
	$coo_logger = LogControl::get_instance();
	$coo_logger->fetch_configuration('sql_queries');
	
	$result = mysqli_query( $$link, $query) or xtc_db_error($query, ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$coo_logger->write_sql_log($query);

	return $result;
}

// db connection for Servicedatabase 
function service_xtc_db_query($query, $link_service = 'db_link_service')
{
	global $$link_service;
	
	$coo_logger = LogControl::get_instance();
	$coo_logger->fetch_configuration('sql_queries');
	
	$result = mysqli_query( $$link_service, $query) or xtc_db_error($query, ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$coo_logger->write_sql_log($query);

	return $result;
}

function xtc_db_fetch_array($db_query)
{
	return mysqli_fetch_array($db_query,  MYSQLI_ASSOC);
}

function xtc_db_result($result, $row, $field = '')
{
	return mysql_result($result, $row, $field);
}

function xtc_db_num_rows($db_query)
{
	return mysqli_num_rows($db_query);
}

function xtc_db_data_seek($db_query, $row_number)
{
	return mysqli_data_seek($db_query,  $row_number);
}

function xtc_db_insert_id()
{
	return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

function xtc_db_free_result($db_query)
{
	return ((mysqli_free_result($db_query) || (is_object($db_query) && (get_class($db_query) == "mysqli_result"))) ? true : false);
}

function xtc_db_fetch_fields($db_query)
{
	return (((($___mysqli_tmp = mysqli_fetch_field_direct($db_query, mysqli_field_tell($db_query))) && is_object($___mysqli_tmp)) ? ( (!is_null($___mysqli_tmp->primary_key = ($___mysqli_tmp->flags & MYSQLI_PRI_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->multiple_key = ($___mysqli_tmp->flags & MYSQLI_MULTIPLE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->unique_key = ($___mysqli_tmp->flags & MYSQLI_UNIQUE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->numeric = (int)(($___mysqli_tmp->type <= MYSQLI_TYPE_INT24) || ($___mysqli_tmp->type == MYSQLI_TYPE_YEAR) || ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? ($___mysqli_tmp->type == MYSQLI_TYPE_NEWDECIMAL) : 0)))) && (!is_null($___mysqli_tmp->blob = (int)in_array($___mysqli_tmp->type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) && (!is_null($___mysqli_tmp->unsigned = ($___mysqli_tmp->flags & MYSQLI_UNSIGNED_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->zerofill = ($___mysqli_tmp->flags & MYSQLI_ZEROFILL_FLAG) ? 1 : 0)) && (!is_null($___mysqli_type = $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = (($___mysqli_type == MYSQLI_TYPE_STRING) || ($___mysqli_type == MYSQLI_TYPE_VAR_STRING)) ? "type" : "")) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG, MYSQLI_TYPE_INT24))) ? "int" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_DECIMAL, ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? constant("MYSQLI_TYPE_NEWDECIMAL") : -1)))) ? "real" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIMESTAMP) ? "timestamp" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_YEAR) ? "year" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (($___mysqli_type == MYSQLI_TYPE_DATE) || ($___mysqli_type == MYSQLI_TYPE_NEWDATE))) ? "date " : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIME) ? "time" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_SET) ? "set" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_ENUM) ? "enum" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_GEOMETRY) ? "geometry" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_DATETIME) ? "datetime" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (in_array($___mysqli_type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) ? "blob" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_NULL) ? "null" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type) ? "unknown" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->not_null = ($___mysqli_tmp->flags & MYSQLI_NOT_NULL_FLAG) ? 1 : 0)) ) : false ) ? $___mysqli_tmp : false);
}

function xtc_db_output($string)
{
	return htmlspecialchars_wrapper($string);
}

function xtc_db_input($string)
{
	return addslashes($string);
}

function xtc_db_prepare_input($string)
{
	if(is_string($string))
	{
		return trim(stripslashes($string));
	}
	elseif(is_array($string))
	{
		reset($string);
		
		while(list($key, $value) = each($string))
		{
			$string[$key] = xtc_db_prepare_input($value);
		}
		
		return $string;
	}
	else
	{
		return $string;
	}
}
