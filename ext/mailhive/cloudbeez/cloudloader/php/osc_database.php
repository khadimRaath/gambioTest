<?php

/*
  $Id: database.php 1739 2007-12-20 00:52:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License


  mailbeez.com: modified for compatibility in zencart - just in case someone already did include these functions
 */

$db_link = $db->link;

// detect type of db connection
if (is_resource($db_link) && get_resource_type($db_link) == 'mysql link') {
    define('MH_DBTYPE', 'MYSQL');
} else {
    if (is_object($db_link) && get_class($db_link) == 'mysqli') {
        define('MH_DBTYPE', 'MYSQLI');
    }
}


// issue: on zencart the dblink is not of type mysqli


if (!function_exists('tep_db_connect')) {

    function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link')
    {
        global $$link;

	    $port   = isset(explode(':', $server)[1]) && is_numeric(explode(':', $server)[1]) ? (int)explode(':', $server)[1] : null;
	    $socket = isset(explode(':', $server)[1]) && !is_numeric(explode(':', $server)[1]) ? explode(':', $server)[1] : null;
        $server = explode(':', $server)[0];
	
	    switch (MH_DBTYPE) {
            case 'MYSQL':
                if (USE_PCONNECT == 'true') {
                    $$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect('p:' . $server,  $username,  $password, $database, $port, $socket));
                } else {
                    $$link = ($GLOBALS["___mysqli_ston"] = mysqli_connect($server,  $username,  $password, $database, $port, $socket));
                }

                if ($$link)
                    ((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $database));

                return $$link;

                break;
            case 'MYSQLI':
                if (USE_PCONNECT == 'true') {
                    $server = 'p:' . $server;
                }

                $$link = mysqli_connect($server, $username, $password, $database, $port, $socket);
                return $$link;
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}


if (!function_exists('tep_db_close')) {

    function tep_db_close($link = 'db_link')
    {
        global $$link;

        switch (MH_DBTYPE) {
            case 'MYSQL':
                return ((is_null($___mysqli_res = mysqli_close($$link))) ? false : $___mysqli_res);
                break;
            case 'MYSQLI':
                return mysqli_close($$link);
                break;
            default:
                echo 'DB Type not supported';
        }
    }
}

if (!function_exists('tep_db_error')) {

    function tep_db_error($query, $errno, $error)
    {
        die('<font color="#000000"><strong>' . $errno . ' - ' . $error . '<br /><br />' . $query . '<br /><br /><small><font color="#ff0000">[TEP STOP]</font></small><br /><br /></strong></font>');
    }

}

if (!function_exists('tep_db_query')) {

    function tep_db_query($query, $link = 'db_link')
    {
        global $$link;

        if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
            error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
        }


        switch (MH_DBTYPE) {
            case 'MYSQL':
                $result = mysqli_query( $$link, $query) or tep_db_error($query, ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)), ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

                if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
                    $result_error = ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
                    error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
                }
                return $result;
                break;
            case 'MYSQLI':
                $result = mysqli_query($$link, $query) or tep_db_error($query, mysqli_errno($$link), mysqli_error($$link));

                if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
                    $result_error = mysqli_error($$link);
                    error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
                }
                return $result;
                break;
            default:
                echo 'DB Type not supported';
        }

    }

}


if (!function_exists('tep_db_perform')) {

    function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link')
    {
        reset($data);
        if ($action == 'insert') {
            $query = 'insert into ' . $table . ' (';
            while (list($columns,) = each($data)) {
                $query .= $columns . ', ';
            }
            $query = substr($query, 0, -2) . ') values (';
            reset($data);
            while (list(, $value) = each($data)) {
                switch ((string)$value) {
                    case 'now()':
                        $query .= 'now(), ';
                        break;
                    case 'null':
                        $query .= 'null, ';
                        break;
                    default:
                        $query .= '\'' . tep_db_input($value) . '\', ';
                        break;
                }
            }
            $query = substr($query, 0, -2) . ')';
        } elseif ($action == 'update') {
            $query = 'update ' . $table . ' set ';
            while (list($columns, $value) = each($data)) {
                switch ((string)$value) {
                    case 'now()':
                        $query .= $columns . ' = now(), ';
                        break;
                    case 'null':
                        $query .= $columns .= ' = null, ';
                        break;
                    default:
                        $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
                        break;
                }
            }
            $query = substr($query, 0, -2) . ' where ' . $parameters;
        }

        return tep_db_query($query, $link);
    }

}

if (!function_exists('tep_db_fetch_array')) {

    function tep_db_fetch_array($db_query)
    {
        switch (MH_DBTYPE) {
            case 'MYSQL':
                return mysqli_fetch_array($db_query,  MYSQLI_ASSOC);
                break;
            case 'MYSQLI':
                return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}

if (!function_exists('tep_db_num_rows')) {

    function tep_db_num_rows($db_query)
    {
        switch (MH_DBTYPE) {
            case 'MYSQL':
                return mysqli_num_rows($db_query);
                break;
            case 'MYSQLI':
                return mysqli_num_rows($db_query);
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}


if (!function_exists('tep_db_data_seek')) {

    function tep_db_data_seek($db_query, $row_number)
    {
        switch (MH_DBTYPE) {
            case 'MYSQL':
                return mysqli_data_seek($db_query,  $row_number);
                break;
            case 'MYSQLI':
                return mysqli_data_seek($db_query, $row_number);
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}

if (!function_exists('tep_db_insert_id')) {

    function tep_db_insert_id($link = 'db_link')
    {
        global $$link;

        switch (MH_DBTYPE) {
            case 'MYSQL':
                return ((is_null($___mysqli_res = mysqli_insert_id($$link))) ? false : $___mysqli_res);
                break;
            case 'MYSQLI':
                return mysqli_insert_id($$link);
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}

if (!function_exists('tep_db_free_result')) {

    function tep_db_free_result($db_query)
    {
        switch (MH_DBTYPE) {
            case 'MYSQL':
                return ((mysqli_free_result($db_query) || (is_object($db_query) && (get_class($db_query) == "mysqli_result"))) ? true : false);
                break;
            case 'MYSQLI':
                return mysqli_free_result($db_query);
                break;
            default:
                echo 'DB Type not supported';
        }

    }

}


if (!function_exists('tep_db_fetch_fields')) {

    function tep_db_fetch_fields($db_query)
    {
        switch (MH_DBTYPE) {
            case 'MYSQL':
                return (((($___mysqli_tmp = mysqli_fetch_field_direct($db_query, mysqli_field_tell($db_query))) && is_object($___mysqli_tmp)) ? ( (!is_null($___mysqli_tmp->primary_key = ($___mysqli_tmp->flags & MYSQLI_PRI_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->multiple_key = ($___mysqli_tmp->flags & MYSQLI_MULTIPLE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->unique_key = ($___mysqli_tmp->flags & MYSQLI_UNIQUE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->numeric = (int)(($___mysqli_tmp->type <= MYSQLI_TYPE_INT24) || ($___mysqli_tmp->type == MYSQLI_TYPE_YEAR) || ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? ($___mysqli_tmp->type == MYSQLI_TYPE_NEWDECIMAL) : 0)))) && (!is_null($___mysqli_tmp->blob = (int)in_array($___mysqli_tmp->type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) && (!is_null($___mysqli_tmp->unsigned = ($___mysqli_tmp->flags & MYSQLI_UNSIGNED_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->zerofill = ($___mysqli_tmp->flags & MYSQLI_ZEROFILL_FLAG) ? 1 : 0)) && (!is_null($___mysqli_type = $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = (($___mysqli_type == MYSQLI_TYPE_STRING) || ($___mysqli_type == MYSQLI_TYPE_VAR_STRING)) ? "type" : "")) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG, MYSQLI_TYPE_INT24))) ? "int" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_DECIMAL, ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? constant("MYSQLI_TYPE_NEWDECIMAL") : -1)))) ? "real" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIMESTAMP) ? "timestamp" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_YEAR) ? "year" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (($___mysqli_type == MYSQLI_TYPE_DATE) || ($___mysqli_type == MYSQLI_TYPE_NEWDATE))) ? "date " : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIME) ? "time" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_SET) ? "set" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_ENUM) ? "enum" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_GEOMETRY) ? "geometry" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_DATETIME) ? "datetime" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (in_array($___mysqli_type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) ? "blob" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_NULL) ? "null" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type) ? "unknown" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->not_null = ($___mysqli_tmp->flags & MYSQLI_NOT_NULL_FLAG) ? 1 : 0)) ) : false ) ? $___mysqli_tmp : false);
                break;
            case 'MYSQLI':
                return mysqli_fetch_field($db_query);
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}

if (!function_exists('tep_db_output')) {

    function tep_db_output($string)
    {
        return htmlspecialchars($string);
    }

}

if (!function_exists('tep_db_input')) {

    function tep_db_input($string, $link = 'db_link')
    {
        global $$link;

        switch (MH_DBTYPE) {
            case 'MYSQL':
                if (function_exists('mysqli_real_escape_string')) {
                    return mysqli_real_escape_string( $$link, $string);
                } elseif (function_exists('mysqli_real_escape_string')) {
                    return ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
                }
                break;
            case 'MYSQLI':
                return mysqli_real_escape_string($$link, $string);
                break;
            default:
                echo 'DB Type not supported';
        }
    }

}


if (!function_exists('tep_db_prepare_input')) {

    function tep_db_prepare_input($string)
    {
        if (is_string($string)) {
            return trim(stripslashes($string));
        } elseif (is_array($string)) {
            reset($string);
            while (list($key, $value) = each($string)) {
                $string[$key] = tep_db_prepare_input($value);
            }
            return $string;
        } else {
            return $string;
        }
    }

}


if (!function_exists('mysqli_connect')) {

    define('MYSQLI_ASSOC', MYSQLI_ASSOC);

    function mysqli_connect($server, $username, $password, $database)
    {
        if (substr($server, 0, 2) == 'p:') {
            $link = ($GLOBALS["___mysqli_ston"] = mysqli_connect(substr($server, 2),  $username,  $password));
        } else {
            $link = ($GLOBALS["___mysqli_ston"] = mysqli_connect($server,  $username,  $password));
        }

        if ($link) {
            ((bool)mysqli_query( $link, "USE " . $database));
        }

        return $link;
    }

    function mysqli_close($link)
    {
        return ((is_null($___mysqli_res = mysqli_close($link))) ? false : $___mysqli_res);
    }

    function mysqli_query($link, $query)
    {
        return mysqli_query( $link, $query);
    }

    function mysqli_errno($link = null)
    {
        return ((is_object($link)) ? mysqli_errno($link) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false));
    }

    function mysqli_error($link = null)
    {
        return ((is_object($link)) ? mysqli_error($link) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    }

    function mysqli_fetch_array($query, $type)
    {
        return mysqli_fetch_array($query,  $type);
    }

    function mysqli_num_rows($query)
    {
        return mysqli_num_rows($query);
    }

    function mysqli_data_seek($query, $offset)
    {
        return mysqli_data_seek($query,  $offset);
    }

    function mysqli_insert_id($link)
    {
        return ((is_null($___mysqli_res = mysqli_insert_id($link))) ? false : $___mysqli_res);
    }

    function mysqli_free_result($query)
    {
        return ((mysqli_free_result($query) || (is_object($query) && (get_class($query) == "mysqli_result"))) ? true : false);
    }

    function mysqli_fetch_field($query)
    {
        return (((($___mysqli_tmp = mysqli_fetch_field_direct($query, mysqli_field_tell($query))) && is_object($___mysqli_tmp)) ? ( (!is_null($___mysqli_tmp->primary_key = ($___mysqli_tmp->flags & MYSQLI_PRI_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->multiple_key = ($___mysqli_tmp->flags & MYSQLI_MULTIPLE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->unique_key = ($___mysqli_tmp->flags & MYSQLI_UNIQUE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->numeric = (int)(($___mysqli_tmp->type <= MYSQLI_TYPE_INT24) || ($___mysqli_tmp->type == MYSQLI_TYPE_YEAR) || ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? ($___mysqli_tmp->type == MYSQLI_TYPE_NEWDECIMAL) : 0)))) && (!is_null($___mysqli_tmp->blob = (int)in_array($___mysqli_tmp->type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) && (!is_null($___mysqli_tmp->unsigned = ($___mysqli_tmp->flags & MYSQLI_UNSIGNED_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->zerofill = ($___mysqli_tmp->flags & MYSQLI_ZEROFILL_FLAG) ? 1 : 0)) && (!is_null($___mysqli_type = $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = (($___mysqli_type == MYSQLI_TYPE_STRING) || ($___mysqli_type == MYSQLI_TYPE_VAR_STRING)) ? "type" : "")) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG, MYSQLI_TYPE_INT24))) ? "int" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_DECIMAL, ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? constant("MYSQLI_TYPE_NEWDECIMAL") : -1)))) ? "real" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIMESTAMP) ? "timestamp" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_YEAR) ? "year" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (($___mysqli_type == MYSQLI_TYPE_DATE) || ($___mysqli_type == MYSQLI_TYPE_NEWDATE))) ? "date " : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIME) ? "time" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_SET) ? "set" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_ENUM) ? "enum" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_GEOMETRY) ? "geometry" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_DATETIME) ? "datetime" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (in_array($___mysqli_type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) ? "blob" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_NULL) ? "null" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type) ? "unknown" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->not_null = ($___mysqli_tmp->flags & MYSQLI_NOT_NULL_FLAG) ? 1 : 0)) ) : false ) ? $___mysqli_tmp : false);
    }

    function mysqli_real_escape_string($link, $string)
    {
        if (function_exists('mysqli_real_escape_string')) {
            return mysqli_real_escape_string( $link, $string);
        } elseif (function_exists('mysqli_real_escape_string')) {
            return ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
        }

        return addslashes($string);
    }

    function mysqli_affected_rows($link)
    {
        return mysqli_affected_rows($link);
    }

    function mysqli_get_server_info($link)
    {
        return ((is_null($___mysqli_res = mysqli_get_server_info($link))) ? false : $___mysqli_res);
    }
}
