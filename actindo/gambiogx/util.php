<?php

/**
 * various utilities for xt/os commerce
 *
 * actindo Faktura/WWS connector
 *
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
*/

function act_get_shop_type( )
{
  if( file_exists('includes/gm/classes/GMProductUpload.php') )
    return SHOP_TYPE_GAMBIOGX;
  if( function_exists('xtc_db_query') )
    return SHOP_TYPE_XTCOMMERCE;
  if( function_exists('tep_db_query') )
    return SHOP_TYPE_OSCOMMERCE;
}


function default_lang( )
{
  $res = act_db_query( "SELECT l.`languages_id` AS lang_id FROM `configuration` AS c, `languages` AS l WHERE c.`configuration_key`='DEFAULT_LANGUAGE' AND l.`code`=c.`configuration_value`" );
  $r = act_db_fetch_array( $res );
  act_db_free( $res );
  return (int)$r['lang_id'];
}


function actindo_get_table_fields( $table )
{
  global $export;

  $cols = array();
  $result = act_db_query( "DESCRIBE $table" );
  while( $row = act_db_fetch_array( $result ) )
  {
    $cols[] = current($row);
  }
  act_db_free( $result );
  return $cols;
}



function check_admin_pass( $pass, $login )
{
    $login = trim( $login );
    if( empty($login) )
    {
        $adminPassword = strtolower(get_admin_pass());
        return strtolower($pass) == $adminPassword;
    }
    $res = act_db_query( $q="SELECT IF(`customers_password`='".esc($pass)."', 1, 0) AS okay FROM `customers` LEFT JOIN `admin_access` USING (`customers_id`) WHERE `customers_email_address`='".esc($login)."' AND `admin_access`.`customers_id` IS NOT NULL" );
    $row = act_db_fetch_array( $res );
    act_db_free( $res );
    if( $row['okay'] > 0 )
    {
        return true;
    }
    return FALSE;
}

function get_admin_pass()
{
  $res = act_db_query( "SELECT `customers_password` AS md5 FROM `customers` WHERE `customers_id`=1" );
  $md5 = act_db_fetch_array( $res );
  $md5 = $md5['md5'];
  act_db_free( $res );

  return $md5;
}



function get_language_id_by_code( $code )
{
  global $_language_id_by_code;
  if( !is_array($_language_id_by_code) )
  {
    $_language_id_by_code = array();
    $res = act_db_query( "SELECT languages_id, code FROM ".TABLE_LANGUAGES );
    while( $row = act_db_fetch_array($res) )
      $_language_id_by_code[$row['code']] = (int)$row['languages_id'];
    act_db_free( $res );
  }
  return $_language_id_by_code[$code];
}

function get_language_code_by_id( $languages_id )
{
  global $_language_code_by_id;
  if( !is_array($_language_code_by_id) )
  {
    $_language_code_by_id = array();
    $res = act_db_query( "SELECT languages_id, code FROM ".TABLE_LANGUAGES );
    while( $row = act_db_fetch_array($res) )
      $_language_code_by_id[(int)$row['languages_id']] = $row['code'];
    act_db_free( $res );
  }
  return $_language_code_by_id[(int)$languages_id];
}



function _actindo_get_verf( $payment_modulename )
{
  $payment_modulename = 'MODULE_PAYMENT_'.strtoupper( $payment_modulename ).'_actindo_VERF';
  if( !defined($payment_modulename) )
    return null;
  return constant( $payment_modulename );
}


function act_failsave_db_query( $text )
{
  return mysqli_query($GLOBALS["___mysqli_ston"],  $text );
}

function act_db_query( $text )
{
  if( function_exists('xtc_db_query') )
    return xtc_db_query( $text );
  else if( function_exists('tep_db_query') )
    return tep_db_query( $text );
}

function act_db_free( $res )
{
  if( function_exists('xtc_db_free') )
    return xtc_db_free( $res );
  else if( function_exists('tep_db_free') )
    return tep_db_free( $res );
  else
    return ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
}

function act_db_num_rows( $res )
{
  if( function_exists('xtc_db_num_rows') )
    return xtc_db_num_rows( $res );
  else if( function_exists('tep_db_free') )
    return tep_db_num_rows( $res );
  else
    return mysqli_num_rows( $res );
}

function act_db_fetch_array( $res )
{
  if( function_exists('xtc_db_fetch_array') )
    return xtc_db_fetch_array( $res );
  else if( function_exists('tep_db_fetch_array') )
    return tep_db_fetch_array( $res );
  else
    return mysqli_fetch_array( $res );
}

function act_db_fetch_assoc( $res )
{
  return act_db_fetch_array( $res );
}

function act_db_fetch_row( $res )
{
  $row = act_db_fetch_array( $res );
  if( !is_array($row) || !count($row) )
    return $row;
  $data = array();
  foreach( $row as $_val )
    $data[] = $_val;
  return $data;
}

function act_db_insert_id( $res )
{
  if( function_exists('xtc_db_insert_id') )
    return xtc_db_insert_id( $res );
  else if( function_exists('tep_db_insert_id') )
    return tep_db_insert_id( $res );
  else
    return ((is_null($___mysqli_res = mysqli_insert_id( $res ))) ? false : $___mysqli_res);
}

function act_db_input( )
{
  $arr = func_get_args( );
  array_unshift( $arr, __FUNCTION__ );
  return call_user_func_array( 'act_call_shop_fcn', $arr );
}

function esc( $str )
{
  return ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $str ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
}


function act_have_table( $name )
{
  global $act_have_table_cache;
  is_array($act_have_table_cache) or $act_have_table_cache = array();
  if( isset($act_have_table_cache[$name]) )
    return $act_have_table_cache[$name];

  $have=FALSE;
  $res = act_db_query( "SHOW TABLES LIKE '".esc($name)."'" );
  while( $n=act_db_fetch_row($res) )    // get mixed case here, therefore check again
  {
    if( !strcmp( $n[0], $name ) )
    {
      $have=TRUE;
      break;
    }
  }
  act_db_result( $res );
  $act_have_table_cache[$name] = $have;
}




function act_get_tax_rate( )
{
  $arr = func_get_args( );
  array_unshift( $arr, __FUNCTION__ );
  return call_user_func_array( 'act_call_shop_fcn', $arr );
}


function act_call_shop_fcn( )
{
  $args = func_get_args( );
  $func = array_shift( $args );
  if( preg_match('/^(tep_|xtc_|act_)?(.+)$/', $func, $matches) )
  {
    $func = $matches[2];
  }

  if( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) )
    return call_user_func_array('xtc_'.$func, $args );
  if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
    return call_user_func_array('tep_'.$func, $args );

}






/**
 * Construct SET statement for INSERT,UPDATE,REPLACE with escaping the data
 *
 * This method also takes care of field names which are in the array but not in
 * the table.
 *
 * @param array Array( 'fieldname'=>'data for field'
 * @param string Table name to read field descriptions from
 * @param boolean Do not escape the data to be inserted (USE WITH GREAT CARE)
 * @param boolean Encode null as NULL? (Normally null is encoded as empty string)
 * @returns array Result array( 'ok'=>TRUE/FALSE, 'set'=> string( 'SET `field1`='data1',...), 'warning'=>string() )
*/
function construct_set( $data, $table, $noescape=FALSE, $encode_null=FALSE )
{
  $fields = array();
  $set = "SET ";
  $warning = "";
  $ok = TRUE;

  $fields = actindo_get_table_fields( $table );

  foreach( $data as $field => $data )
  {
    $field = trim( $field );
    if( !in_array( $field, $fields ) )
    {
      $warning .= "Field $field does not exsist in $table!\n";
      continue;
    }

    if( $encode_null && is_null($data) )
    {
      $set .= "`$field`=NULL,";
      continue;
    }

    if( ! $noescape )
      $data = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $data ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $set .= "`$field`='$data',";
  }

  if( substr( $set, strlen($set)-1, 1 ) == ',' )
    $set = substr( $set, 0, strlen($set)-1 );
  return array( "ok" => $ok, "set" => $set, "warning" => $warning );
}



/* ******** admin interface **** */

function actindo_check_config( )
{
  $sort_order=0;
  $arr = array(
    array( 'configuration_key'=>'ACTINDO_ACTIVE', 'configuration_value'=>'false', 'configuration_group_id'=>ACTINDO_CONFIG_ID, 'sort_order'=>++$sort_order, 'use_function'=>null, 'set_function'=>"xtc_cfg_select_option(array('true', 'false')," ),
    array( 'configuration_key'=>'ACTINDO_MAND_ID', 'configuration_value'=>'', 'configuration_group_id'=>ACTINDO_CONFIG_ID, 'sort_order'=>++$sort_order, 'use_function'=>null, 'set_function'=>null ),
    array( 'configuration_key'=>'ACTINDO_USERNAME', 'configuration_value'=>'', 'configuration_group_id'=>ACTINDO_CONFIG_ID, 'sort_order'=>++$sort_order, 'use_function'=>null, 'set_function'=>null ),
    array( 'configuration_key'=>'ACTINDO_TOKEN', 'configuration_value'=>'', 'configuration_group_id'=>ACTINDO_CONFIG_ID, 'sort_order'=>++$sort_order, 'use_function'=>null, 'set_function'=>null ),
    array( 'configuration_key'=>'ACTINDO_SID', 'configuration_value'=>'', 'configuration_group_id'=>ACTINDO_CONFIG_ID, 'sort_order'=>++$sort_order, 'use_function'=>null, 'set_function'=>null ),
  );

  foreach( $arr as $_cfg )
  {
    $retval = @constant( $_cfg['configuration_key'] );
    if( is_null($retval) )
    {
      xtc_db_query( "INSERT INTO ".TABLE_CONFIGURATION." SET `configuration_key`='".$_cfg['configuration_key']."', `configuration_value`='".$_cfg['configuration_value']."', `configuration_group_id`='".$_cfg['configuration_group_id']."', `sort_order`='".$_cfg['sort_order']."', `use_function`=".(is_null($_cfg['use_function']) ? 'NULL' : '"'.$_cfg['use_function'].'"').", `set_function`=".(is_null($_cfg['set_function']) ? 'NULL' : '"'.$_cfg['set_function'].'"')."" );
      define( $_cfg['configuration_key'], $_cfg['configuration_value'] );
    }
  }
}






function actindo_create_temporary_file( $data )
{
  $tmp_name = tempnam( "/tmp", "" );
  if( $tmp_name === FALSE || !is_writable($tmp_name) )
    $tmp_name = tempnam( ini_get('upload_tmp_dir'), "" );
  if( $tmp_name === FALSE || !is_writable($tmp_name) )
    $tmp_name = tempnam( ACTINDO_SHOP_BASEDIR.'/templates_c', "" );   // last resort: try templates_c
  if( $tmp_name === FALSE || !is_writable($tmp_name) )
    return array( 'ok' => FALSE, 'errno' => EIO, 'error' => 'Konnte keine temp�r�re Datei anlegen' );
  $written = file_put_contents( $tmp_name, $data );
  if( $written != strlen($data) )
  {
    $ret = array( 'ok' => FALSE, 'errno' => EIO, 'error' => 'Fehler beim schreiben des Bildes in das Dateisystem (Pfad '.var_dump_string($tmp_name).', written='.var_dump_string($written).', filesize='.var_dump_string(@filesize($tmp_name)).')' );
    unlink( $tmp_name );
    return $ret;
  }

  return array( 'ok'=>TRUE, 'file' => $tmp_name );
}



function actindo_get_gender_map( )
{
  $gender = array(
    'm' => 'Herr',
    'f' => 'Frau',
  );
  return $gender;
}


/**
 * Date conversion from YYYY-MM-DD HH:MM:SS to unix timestamp
 *
 * @param string Date in format 'YYYY-MM-DD HH:MM:SS'
 * @returns int Unix timestamp, or -1 if out of range
 */
function datetime_to_timestamp( $date )
{
  preg_match( '/(\d+)-(\d+)-(\d+)\s+(\d+):(\d+)(:(\d+))/', $date, $date );
  if( (!((int)$date[1]) && !((int)$date[2]) && !((int)$date[0])) )
    return -1;
  return mktime( (int)$date[4], (int)$date[5], (int)$date[7], (int)$date[2], (int)$date[3], (int)$date[1] );
}

if( !function_exists( "xtc_get_shipping_status"  ) )
{
  function xtc_get_shipping_status_name($shipping_status_id)
  {
    $status_query=xtc_db_query("SELECT
     shipping_status_name,
     shipping_status_image
     FROM ".TABLE_SHIPPING_STATUS."
     where shipping_status_id = '".$shipping_status_id."'
     and language_id = '".(int)$_SESSION['languages_id']."'");
    $status_data=xtc_db_fetch_array($status_query);
    $shipping_statuses=array();
    $shipping_status=array('name'=>$status_data['shipping_status_name'],'image'=>$status_data['shipping_status_image']);

    return $shipping_status;
  }
}



function _checksum_dir( $dirname, $pattern, $checksum_type, $recursive )
{
  $dirs = array();
  $files = array();

  $dir = opendir( $dirname );
  if( !is_resource($dir) )
    return FALSE;

  while( $fn = readdir($dir) )
  {
    if( $fn == '.' || $fn == '..' )
      continue;

    if( $fn == 'images' || $fn == 'templates_c' )
      continue;

    $basename = $fn;
    $fn = add_last_slash($dirname).$fn;

    if( is_dir($fn) )
      $dirs[] = $fn;
    else if( is_file($fn) && (!function_exists('fnmatch') || fnmatch($pattern, $basename)) )
    {
      $files[$fn] = _checksum_file( $fn, $checksum_type );
    }
  }
  closedir( $dir );

  if( $recursive && count($dirs) )
  {
    foreach( $dirs as $_dir )
    {
      $files = array_merge( $files, _checksum_dir($_dir, $pattern, $checksum_type, $recursive) );
    }
  }

  return $files;
}


function _checksum_file( $fn, $checksum_type='MD5' )
{
  if( !is_readable($fn) )
  {
    return 'UNREADABLE';
  }

  if( empty($checksum_type) )
    return 'NO-CHECKSUM-TYPE';

  if( $checksum_type == 'FILESIZE' )
    return filesize( $fn );

  $data = file_get_contents( $fn );
  if( $checksum_type == 'MD5' )
  {
    $data = md5( $data );
  }
  else if( $checksum_type == 'SHA1' )
  {
    $data = sha1( $data );
  }
  else if( $checksum_type == 'MD5-TRIM' )
  {
    $data = strtr( $data, array("\r"=>"", "\n"=>"", "\t"=>"", " "=>"") );
    $data = md5( trim($data) );
  }
  else if( $checksum_type == 'SHA1-TRIM' )
  {
    $data = strtr( $data, array("\r"=>"", "\n"=>"", "\t"=>"", " "=>"") );
    $data = sha1( trim($data) );
  }
  else if( $checksum_type == 'SIZE' )
  {
    $data = strlen( $data );
  }
  else if( $checksum_type == 'SIZE-TRIM' )
  {
    $data = strtr( $data, array("\r"=>"", "\n"=>"", "\t"=>"", " "=>"") );
    $data = strlen( trim($data) );
  }
  return $data;
}

function actindo_do_checksums( $subdirectory='', $pattern='*', $checksum_type='MD5', $recursive=TRUE )
{
  $path = add_last_slash( ACTINDO_SHOP_BASEDIR ).$subdirectory;
  if( is_file($path) )
  {
    $files_arr = array( $subdirectory => _checksum_file( $path, $checksum_type ) );
  }
  else
  {
    $files_arr = array();
    $files_arr_2 = _checksum_dir( $path, $pattern, $checksum_type, $recursive );
    foreach( $files_arr_2 as $_fn => $_cs )
    {
      $_fn = substr( $_fn, strlen($path) );
      $files_arr[$_fn] = $_cs;
    }
  }

  return array( 'ok'=>TRUE, 'basedir'=>$path, 'files'=>$files_arr );
}
