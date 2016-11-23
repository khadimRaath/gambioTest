<?php

/**
 * export settings
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

require_once( 'export_customers.php' );
require_once( 'export_orders.php' );
require_once( 'export_products.php' );


function export_shop_languages( )
{
  $lang = array();
  $res = act_db_query( "SELECT * FROM ".TABLE_LANGUAGES );
  while( $val = act_db_fetch_array( $res ) )
  {
    $lang[(int)$val['languages_id']] = array(
      "language_id" => (int)$val['languages_id'],
      "language_name" => $val['name'],
      'language_code' => $val['code'],
      'is_default' => $val['code'] == DEFAULT_LANGUAGE,
    );
  }
  act_db_free( $res );
  return $lang;
}


function export_customers_status( )
{
  $status = array();
  $res = act_db_query( "SELECT `customers_status_id`, `language_id`, `customers_status_name`, `customers_status_min_order`, `customers_status_max_order`, `customers_status_discount`, `customers_status_ot_discount_flag`, `customers_status_ot_discount`, `customers_status_show_price`, `customers_status_show_price_tax`, `customers_status_add_tax_ot`, `customers_status_discount_attributes` FROM ".TABLE_CUSTOMERS_STATUS );
  while( $val = act_db_fetch_array( $res ) )
  {
    if( !isset($status[(int)$val['customers_status_id']]) )
    {
      $val1 = $val;
      unset( $val1['customers_status_name'] );
      $status[(int)$val['customers_status_id']] = $val1;
    }
    $status[(int)$val['customers_status_id']]['customers_status_name'][(int)$val['language_id']] = $val['customers_status_name'];
  }
  act_db_free( $res );
  return $status;
}

function export_xsell_groups( )
{
  $grps = array();
  $res = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_XSELL_GROUPS );
  while( $val = act_db_fetch_array( $res ) )
  {
    if( !isset($grps[(int)$val['products_xsell_grp_name_id']]) )
    {
      $val1 = $val;
      unset( $val1['groupname'] );
      unset( $val1['language_id'] );
      $grps[(int)$val['products_xsell_grp_name_id']] = $val1;
    }
    $grps[(int)$val['products_xsell_grp_name_id']]['groupname'][(int)$val['language_id']] = $val['groupname'];
  }
  act_db_free( $res );
  return $grps;
}



function _get_modules( $subdir )
{
  $modules = array();

  ob_start();
  $dh = opendir( $dir=DIR_FS_CATALOG_MODULES.$subdir.'/' );
  while( $fn = readdir($dh) )
  {
    $filename = $fn;
    $fn = $dir.$fn;

    if( !preg_match('/^(.+)\.php$/', $filename, $matches) )
      continue;

    $modulename = $matches[1];

    if( !is_readable($fn) )
      continue;
    include_once( $fn );
    if( !class_exists($modulename) )
      continue;

    $lang_fn = DIR_FS_LANGUAGES.'german/modules/'.$subdir.'/'.$filename;
    if( is_readable($lang_fn) )
      include_once( $lang_fn );

    $status_constant = 'MODULE_'.strtoupper($subdir).'_'.strtoupper($modulename).'_STATUS';
    $name_constant = 'MODULE_'.strtoupper($subdir).'_'.strtoupper($modulename).'_TEXT_TITLE';

    if(defined($status_constant))
    {
        $modules[$modulename] = array(
          'id' => $modulename,
          'code' => $modulename,
          'active' => constant($status_constant) == 'True' ? 1 : 0,
          'name' => trim(htmlspecialchars(strip_tags( defined($name_constant) ? constant($name_constant) : $modulename ))),
        );
    }
  }
  closedir( $dh );
  ob_end_clean();

  return $modules;
}

function export_shop_settings()
{
  $ret = array();
  $ret['languages'] = export_shop_languages();

  if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
  {
    $res = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_VPE );
    while( $val = act_db_fetch_array( $res ) )
    {
      $ret['vpe'][$val['products_vpe_id']][$val['language_id']] = array(
        "products_vpe" => $val['products_vpe_id'],
        "vpe_name" => $val['products_vpe_name']
      );
    }
    act_db_free( $res );
  }


  $res = act_db_query( "select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." order by manufacturers_name" );
  while( $val = act_db_fetch_array( $res ) )
  {
    $ret['manufacturers'][] = array(
      "manufacturers_id" => $val['manufacturers_id'],
      "manufacturers_name" => $val['manufacturers_name']
    );
  }
  act_db_free( $res );

  if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
  {
    $ret['shipping'] = xtc_get_shipping_status();

    $ret['info_template'] = array ();
    if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'))
    {
      while (($file = readdir($dir)) !== false)
      {
        if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and ($file != "index.html"))
        {
          $ret['info_template'][] = array ('id' => $file, 'text' => $file);
        }
      }
      closedir($dir);
    }

    $ret['options_template'] = array ();
    if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'))
    {
      while (($file = readdir($dir)) !== false)
      {
        if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and ($file != "index.html"))
        {
          $ret['options_template'][] = array ('id' => $file, 'text' => $file);
        }
      }
      closedir($dir);
    }
  }


  $ret['orders_status'] = array();
  $res = act_db_query( "SELECT * FROM ".TABLE_ORDERS_STATUS );
  while( $val = act_db_fetch_array( $res ) )
  {
    $ret['orders_status'][$val['orders_status_id']][$val['language_id']] = $val['orders_status_name'];
  }
  act_db_free( $res );

  if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
  {
    $ret['customers_status'] = export_customers_status( );
    $ret['xsell_groups'] = export_xsell_groups( );
  }

  $ret['installed_payment_modules'] = _get_modules( 'payment' );
  $ret['installed_shipping_modules'] = _get_modules( 'shipping' );

  return $ret;
}

?>
