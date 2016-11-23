<?php

/**
 * xmlrpc server
 * 
 * actindo Faktura/WWS connector
 *
 *
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
*/

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__));

define( 'ACTINDO_TRANSPORT_CHARSET', 'UTF-8' );
define( 'ACTINDO_CONVERT_TO_UTF8',false);
define( 'ACTINDO_DEBUG', false);


if(ACTINDO_DEBUG===true)
{
    ini_set("error_log", dirname(__FILE__).'/error.log');
}

/**
 * Changes the supported Attributes Model
 * Possible Values:
 * New Properties Model:    properties
 * Old Model:               attributes (or any other Value!)
 */
define( 'ACTINDO_ATTRIBUTES_MODE','properties');

/* initialize error handling */
$GLOBALS['actindo_occured_errors'] = array();
ini_set( 'display_errors', "1" );
//error_reporting( E_ALL & ~E_NOTICE );
set_error_handler( 'actindo_error_handler' );

require_once( 'error.php' );
require_once( 'util.php' );
require_once( 'interface.php' );

ini_set( 'display_errors', "1" );
//error_reporting( E_ALL & ~E_NOTICE );
set_error_handler( 'actindo_error_handler' );

if( !defined('ACTINDO_SHOP_CHARSET') )
  define( 'ACTINDO_SHOP_CHARSET', ACTINDO_TRANSPORT_CHARSET );

require_once('gambiogx/actindo.php');
require_once('gambiogx/attributeHandler.php');
require_once('gambiogx/compat.php');
require_once('gambiogx/export.php');
require_once('gambiogx/import.php');
require_once('gambiogx/util.php');
require_once('Zend/XmlRpc/Server.php');
require_once('Zend/XmlRpc/Value/Base64.php');
require_once('classes/Service/Actindo.php');
require_once('classes/Service/Category.php');
require_once('classes/Service/Orders.php');
require_once('classes/Service/Customers.php');
require_once('classes/Service/Settings.php');
require_once('classes/Service/Product.php');
require_once('classes/Components/Request.php');
require_once('classes/Components/Response.php');
require_once('classes/Components/Server.php');
/**
 * check crypt mode
 */
if(isset($_GET['get_cryptmode']) || isset($_POST['get_cryptmode']))
{
    die('cryptmode=MD5&connector_type=XMLRPCUTF8');
}
if(ACTINDO_DEBUG===true)
{
    ob_start();
    $server = new Actindo_Connector_Components_Server();
    $request = new Actindo_Connector_Components_Request(null,null,$_SERVER);
    try
    {
        $response = $server->handle($request);
    }
    catch (Exception $ex)
    {
        $response = $server->fault($ex);
    }
    echo $response->__toString();
    $output = ob_get_flush();

    file_put_contents(dirname(__FILE__).'/out.log', var_dump_string($output),FILE_APPEND);
    print($output);
}
else
{
    $server = new Actindo_Connector_Components_Server();
    $request = new Actindo_Connector_Components_Request(null,null,$_SERVER);
    try
    {
        $response = $server->handle($request);
    }
    catch (Exception $ex)
    {
        $response = $server->fault($ex);
    }
    echo $response->__toString();
}
/**
 * Error handler
 */
function actindo_error_handler( $errno, $errstr, $errfile=null, $errline=null, $errcontext=null )
{
  global $actindo_occured_errors;
  if( ($errno & error_reporting()) == 0 )
    return;
  $actindo_occured_errors[] = array( $errno, $errstr, $errfile, $errline );
}

function actindo_ping( $params )
{
  if( !parse_args($params,$ret) )
    return $ret;

  $res = array( 'ok'=>TRUE, 'pong'=>'pong' );
  return resp( $res );
}

function parse_args( &$params, &$ret )
{
	if(is_array($params))
	{
    	$log = $params['params'];
    	unset($params['params']);
    	$ret = $params;
    }
    else
    {
    	$log = $params;
    	$ret = array();
    }
    list( $pass, $login ) = explode('|||', $log );
    if( check_admin_pass($pass, $login) )
    {
      return 1;
    }
    else {
        throw new Exception( ELOGINFAILED );
    }
}

function actindo_get_connector_version( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $arr0 = array();
    shop_get_connector_version( $arr0, $params );
    $revision = '$Revision: 511 $';
    $arr = array(
        'xmlrpc_server_revision' => $revision,
        // 'protocol_version' => set in shop_get_connector_version,
        'interface_type' => ACTINDO_CONNECTOR_TYPE,
        // 'shop_type' => set in shop_get_connector_version,
        // 'shop_version' => set in shop_get_connector_version
        // 'capabilities' => set in shop_get_connector_version,
        'php_version' => is_callable('phpversion') ? phpversion() : '0.0.0',
        'zend_version' => is_callable('zend_version') ? zend_version() : '0.0.0',
        'cpuinfo' => @file_get_contents( '/proc/cpuinfo' ),
        'meminfo' => @file_get_contents( '/proc/meminfo' ),
        'extensions' => array(),
    );
    foreach( get_loaded_extensions() as $_name )
    {
        $arr['extensions'][$_name] = phpversion($_name);
    }

    if( is_callable('phpinfo') )
    {
        ob_start(); phpinfo();
        $c = ob_get_contents();
        ob_end_clean();
        $arr['phpinfo'] = $c;
    }

    $arr = array_merge( $arr0, $arr );

    $default_capabilities = array(
        'artikel_vpe' => 1,
        'artikel_shippingtime' => 1,
        'artikel_properties' => 0,
        'artikel_contents' => 1,
        'wg_sync' => 0,
    );
    $arr['capabilities'] = array_merge( $default_capabilities, $arr['capabilities'] );
    return resp( $arr );
}

function resp($array)
{
    //check if output is an array or not
    if(is_array($array) && count($array) > 0)
    {
        //run through array
        foreach($array as $valueId=>$valueData)
        {
            //if the output is array, run rekursive
            if(is_array($valueData))
            {
                $array[$valueId] = resp($valueData);
            }
            else
            {
                $tmpData = $valueData;
                //should null values  be removed?
                if(empty($tmpData) && !is_bool($tmpData) && !is_object($tmpData) && !is_array($tmpData))
                {
                    $tmpData = (string)'';
                }
                if(!is_object($tmpData) && !is_array($tmpData))
                {
                    if(ACTINDO_CONVERT_TO_UTF8)
                    {
                        $tmpData = utf8_encode($tmpData);
                    }
                    $tmpData = (string)$tmpData;
                }
                $array[$valueId] = $tmpData;
            }
        }
        return $array;
    }
    else
    {
        if(empty($array) && !is_bool($array) && !is_object($array) && !is_array($array))
        {
            $array = (string)'';
        }
        if(!is_object($array) && !is_array($array))
        {
            if(ACTINDO_CONVERT_TO_UTF8)
            {
                $array = utf8_encode($array);
            }
            $array = (string)$array;
        }
        return $array;
    }
}

function actindo_checksums( $params )
{
  if( !parse_args($params,$ret) )
    return $ret;

  if( !function_exists('actindo_do_checksums') )
    return resp( array('ok'=>FALSE, 'errno'=>ENOSYS, 'error'=>'Function actindo_do_checksums does not exist') );

  $res = call_user_func_array( 'actindo_do_checksums', $params );
  return resp( $res );
}

/**
 * Check Version
 */
function actindo_check_version($versionCheck)
{
    $application_top = realpath(dirname(__FILE__).'/../includes/application_top.php');
    if(!class_exists('Services_JSON'))
    {
        include(DIR_FS_CATALOG . 'gm/classes/JSON.php');
    }
    $coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $coo_versioninfo = MainFactory::create_object('VersionInfo');
    $t_shop_versioninfo = $coo_versioninfo->get_shop_versioninfo();
    reset($t_shop_versioninfo);
    $version = key($t_shop_versioninfo);
    $version = str_replace('_','.',$version);
    if(version_compare($version,$versionCheck)>=0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function xmlrpc_error($error)
{
    throw new Exception($error);
}

