<?php

/**
 * export orders
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


function export_orders_count( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $counts = array();

    $res = act_db_query( "SELECT COUNT(*) AS cnt FROM `orders`" );
    $tmp = mysqli_fetch_assoc($res);
    $counts['count'] = (int)$tmp['cnt'];
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);


    $res = act_db_query( "SELECT MAX(orders_id) AS cnt FROM `orders`" );
    $tmp = mysqli_fetch_assoc($res);
    $counts['max_order_id'] = (int)$tmp['cnt'];
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

    return resp(array( 'ok'=>TRUE, 'counts' => $counts ));
}


function export_orders_list($params)
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $filters = $params['filters'];
    $from = 0;
    $count = 0x7FFFFFFF;
    isset($filters['start']) or $filters['start'] = (int)$from;
    isset($filters['limit']) or $filters['limit'] = (int)$count;
    !empty($filters['sortColName']) or $filters['sortColName'] = 'order_id';
    !empty($filters['sortOrder']) or $filters['sortOrder'] = 'DESC';

    $gender_map = actindo_get_gender_map( );
    $def_lang = default_lang();

    $mapping = array(
    'order_id' => array('o', 'orders_id'),
    'deb_kred_id' => array('o', 'customers_cid'),
    '_customers_id' => array('o', 'customers_id'),
    'orders_status' => array('o', 'orders_status'),
    );
    $qry = create_query_from_filter( $filters, $mapping );
    if( $qry === FALSE )
    return array( 'ok'=>false, 'errno'=>EINVAL, 'error'=>'Error in filter definition' );


    $orders = array();

    if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
    $res = act_db_query( "SELECT o.*,cc.customers_dob, cc.customers_firstname AS c_firstname, cc.customers_lastname AS c_lastname, cc.customers_fax, cc.customers_gender, cc.customers_cid AS cc_cid, c.countries_iso_code_2 AS cc_iso_code, c1.countries_iso_code_2 AS cd_iso_code, l.code AS `langcode` FROM `orders` AS o LEFT JOIN `countries` AS c ON (o.customers_country=c.countries_name) LEFT JOIN `countries` AS c1 ON (o.delivery_country=c1.countries_name) LEFT JOIN languages AS l ON (l.directory=o.language) LEFT JOIN `customers` AS cc ON (cc.customers_id=o.customers_id) WHERE {$qry['q_search']} ORDER BY {$qry['order']} LIMIT {$qry['limit']}" );
    else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
    $res = act_db_query( "SELECT o.*,cc.customers_dob, cc.customers_firstname AS c_firstname, cc.customers_lastname AS c_lastname, cc.customers_fax, cc.customers_gender, c.countries_iso_code_2 AS cc_iso_code, c1.countries_iso_code_2 AS cd_iso_code FROM `orders` AS o LEFT JOIN `countries` AS c ON (o.customers_country=c.countries_name) LEFT JOIN `countries` AS c1 ON (o.delivery_country=c1.countries_name) LEFT JOIN `customers` AS cc ON (cc.customers_id=o.customers_id) WHERE {$qry['q_search']} ORDER BY {$qry['order']} LIMIT {$qry['limit']}" );
    while( $order = mysqli_fetch_assoc($res) )
    {
        $totals = _export_totals( $order['orders_id'] );

        $mapping = array(
          'orders_id' => 'order_id',
          'customers_id' => '_customers_id',
          'customers_cid' => 'deb_kred_id',
          'customers_vat_id' => 'customer[ustid]',
          'customers_status' => 'customer[preisgruppe]',
          'customers_name' => 'customer[kurzname]',
          'customers_firstname' => 'customer[vorname]',
          'customers_lastname' => 'customer[name]',
          'customers_company' => 'customer[firma]',
          'customers_street_address' => 'customer[adresse]',
          'customers_suburb' => 'customer[adresse2]',
          'customers_city' => 'customer[ort]',
          'customers_postcode' => 'customer[plz]',
          'customers_state' => 'customer[blnd]',
          'countries_iso_code_2' => 'customer[land]',
          'customers_telephone' => 'customer[tel]',
          'customers_fax' => 'customer[fax]',
          'customers_email_address' => 'customer[email]',
          'customers_dob' => 'customer[gebdat]',

          'delivery_name' => 'delivery[kurzname]',
          'delivery_firstname' => 'delivery[vorname]',
          'delivery_lastname' => 'delivery[name]',
          'delivery_company' => 'delivery[firma]',
          'delivery_street_address' => 'delivery[adresse]',
          'delivery_city' => 'delivery[ort]',
          'delivery_postcode' => 'delivery[plz]',
          'delivery_state' => 'delivery[blnd]',
          'delivery_country_iso_code_2' => 'delivery[land]',
          'delivery_telephone' => 'delivery[tel]',
          'delivery_email_address' => 'delivery[email]',

          // 'payment_method' needs special mapping
          'cc_type' => 'cr_type',
          'cc_owner' => 'cr_name',
          'cc_number' => 'cr_nr',
          'cc_expires' => 'cr_valid_to',
          'cc_start' => 'cr_valid_from',
          'cc_issue' => 'cr_issue',
          'cc_cvv' => 'cr_cvv',

          'comments' => 'beleg_status_text',

          'last_modified' => 'tstamp',
          'date_purchased' => 'bill_date',

          'currency' => 'currency',
          'currency_value' => 'currency_value',

          'language' => 'language',
          'langcode' => 'langcode',

          'payment_method' => '_payment_method',

          'orders_status' => 'orders_status',
        );
        $actindoorder = _actindo_generic_mapper( $order, $mapping );

        preg_match( '/^(\d{4}-\d{2}-\d{2})(\s+(\d+:\d+:\d+))?$/', $order['date_purchased'], $matches );
        $actindoorder['webshop_order_date'] = $matches[1];
        $actindoorder['webshop_order_time'] = $matches[3];

        if( isset($order['customers_gender']) && isset($gender_map[$order['customers_gender']]) )
          $actindoorder['customer']['anrede'] = $gender_map[$order['customers_gender']];

        if( empty($actindoorder['customer']['vorname']) || empty($actindoorder['customer']['name']) )
        {
          $n = explode(' ', trim($order['customers_name']) );
          $nn = array_pop( $n );
          if( empty($actindoorder['customer']['vorname']) )
            $actindoorder['customer']['vorname'] = join( " ", $n );
          if( empty($actindoorder['customer']['name']) )
            $actindoorder['customer']['name'] = $nn;
        }

        if( empty($actindoorder['delivery']['vorname']) || empty($actindoorder['delivery']['name']) )
        {
          $n = explode(' ', trim($order['delivery_name']) );
          $nn = array_pop( $n );
          if( empty($actindoorder['delivery']['vorname']) )
            $actindoorder['delivery']['vorname'] = join( " ", $n );
          if( empty($actindoorder['delivery']['name']) )
            $actindoorder['delivery']['name'] = $nn;
        }

        if( empty($actindoorder['customer']['land']) )
          $actindoorder['customer']['land'] = $order['cc_iso_code'];

        if( empty($actindoorder['delivery']['land']) )
          $actindoorder['delivery']['land'] = $order['cd_iso_code'];

        if( !$actindoorder['deb_kred_id'] )
          $actindoorder['deb_kred_id'] = (int)$order['cc_cid'];


        $verfmap = array(
          'banktransfer' => 'L',   // Vorkasse (transfer prepaid)
          'cash' => 'B',
          'cc' => 'KK',
          'cod' => 'NN',
          'eustandardtransfer' => 'U',
          'ipayment' => 'KK',
          'ipaymentelv' => 'VK',
          'luupws' => 'KK',
          'moneybookers' => 'KK',
          'moneyorder' => 'VK',
          'paypal' => 'PP',
          'uos_giropay_modul' => 'VK',
          'uos_gp_modul' => 'VK',
          'uos_kreditkarte_modul' => 'KK',
          'uos_lastschrift_at_modul' => 'VK',
          'uos_lastschrift_de_modul' => 'VK',
          'uos_vorkasse_modul' => 'VK',
          'worldpay' => 'KK',
        );

        $actindoorder['customer']['verf'] = $verfmap[$order['payment_method']];
        if( is_null($actindoorder['customer']['verf']) )
          $actindoorder['customer']['verf'] = 'VK';         // generic prepaid

        _export_payment( $order['orders_id'], $order['payment_method'], $actindoorder );

        $actindoorder['customer']['langcode'] = strtolower( $order['langcode'] );
        $actindoorder['delivery']['langcode'] = strtolower( $order['langcode'] );

        $actindoorder['val_date'] = $actindoorder['bill_date'];
        !empty($actindoorder['tstamp']) or $actindoorder['tstamp'] = $actindoorder['bill_date'];


        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
          $res1 = act_db_query( "SELECT cs.customers_status_show_price_tax, c.customers_status FROM ".TABLE_CUSTOMERS_STATUS." AS cs, ".TABLE_CUSTOMERS." AS c WHERE cs.customers_status_id=c.customers_status AND c.customers_id=".(int)$order['customers_id'] );
          $customer_status = mysqli_fetch_assoc($res1);
          ((mysqli_free_result( $res1 ) || (is_object( $res1 ) && (get_class( $res1 ) == "mysqli_result"))) ? true : false);
          $actindoorder['customer']['print_brutto'] = (int)$customer_status['customers_status_show_price_tax'];
          $actindoorder['customer']['_customers_status'] = (int)$customer_status['customers_status'];
        }
        else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
        {
          $actindoorder['customer']['print_brutto'] = 1;
        }


        $actindoorder['saldo'] = $totals['ot_total'];
        if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
          $actindoorder['netto'] = $totals['ot_total_netto'];
        else
          $actindoorder['netto2'] = round( $totals['ot_total'] - $totals['ot_tax'], 2 );

        if( isset($totals['ot_discount']) )
        {
          if( !isset($customer_status) || $customer_status===FALSE || $customer_status['customers_status_show_price_tax'] > 0 )
          {
        //        $actindoorder['rabatt_betrag'] = round( $p=$totals['ot_discount'] / ($totals['ot_total']/($totals['ot_total'] - $totals['ot_tax'])), 2 )*-1;    // ot_discount is negative, actindo needs it positive
            $actindoorder['rabatt_type'] = 'prozent';
            $actindoorder['rabatt_prozent'] = round( $totals['ot_discount'] / (isset($totals['ot_subtotal']) ? $totals['ot_subtotal'] : $totals['ot_total'])*100, 2 )*-1;
          }
          else
          {
            $actindoorder['rabatt_type'] = 'betrag';
            $actindoorder['rabatt_betrag'] = round( $p=$totals['ot_discount'], 2 )*-1;    // ot_discount is negative, actindo needs it positive
          }
        }
        else
          $actindoorder['rabatt_betrag'] = 0.00;

        if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
          $actindoorder['netto2'] = $actindoorder['netto'] + $actindoorder['rabatt_betrag'];
        else
          $actindoorder['netto'] = $actindoorder['netto2'] - $actindoorder['rabatt_betrag'];

        $actindoorder['_shoporder'] = $order;

        $orders[] = $actindoorder;
    }
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

    return resp($orders);
}

function _export_payment( $orders_id, $payment_method, &$actindoorder )
{
  switch( strtolower($payment_method) )
  {
    case 'banktransfer':
      $res = act_db_query( "SELECT * FROM `banktransfer` WHERE `orders_id`=".(int)$orders_id );
      $payment = mysqli_fetch_assoc($res);
      ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
      $actindoorder['customer']['kto'] = $payment['banktransfer_number'];
      $actindoorder['customer']['blz'] = $payment['banktransfer_blz'];
      $actindoorder['customer']['bankname'] = $payment['banktransfer_bankname'];
      $actindoorder['customer']['kto_inhaber'] = $payment['banktransfer_owner'];
      $actindoorder['_payment'] = $payment;
      return TRUE;
    case 'sepa':
      $res = act_db_query('SELECT * FROM `sepa` WHERE `orders_id` = ' . (int) $orders_id);
      $payment = mysqli_fetch_assoc($res);
      ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);
      
      $actindoorder['customer']['iban']     = (string) $payment['sepa_iban'];
      $actindoorder['customer']['swift']    = (string) $payment['sepa_bic'];
      $actindoorder['customer']['bankname'] = (string) $payment['sepa_bankname'];
      if(!empty($payment['sepa_owner'])) {
          $actindoorder['customer']['kto_inhaber'] = $payment['sepa_owner'];
      }
      $actindoorder['_payment'] = $payment;
      return true;
  }

  $actindoorder['_payment'] = array();
  return FALSE;
}


function _export_totals( $orders_id, $include_title=FALSE )
{
  $totals = array();

  $res1 = act_db_query( "SELECT class,value,title FROM `orders_total` WHERE orders_id=".(int)$orders_id." ORDER BY `sort_order`" );
  while( $t = mysqli_fetch_row($res1) )
  {
    if( $include_title )
      $totals[$t[0]] = array( $t[1], $t[2] );
    else
      $totals[$t[0]] += $t[1];
  }
  ((mysqli_free_result($res1) || (is_object($res1) && (get_class($res1) == "mysqli_result"))) ? true : false);

  return $totals;
}


function export_orders_positions( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $order_id = $params['orderId'];
    global $order;

    require_once( 'compat.php' );

    if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
    {
    require_once (DIR_WS_CLASSES.'order.php');
    }
    else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
    {
    require_once( 'osc/order.php' );
    }

    $products = array();


    $order = new order( $order_id );
    if( is_null($order) || !is_object($order) || is_null($order->info['currency']) )
    {
    return ENOENT;
    }

    if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
    {
    $res1 = act_db_query( "SELECT cs.customers_status_show_price_tax FROM ".TABLE_CUSTOMERS_STATUS." AS cs, ".TABLE_CUSTOMERS." AS c WHERE cs.customers_status_id=c.customers_status AND c.customers_id=".(int)$order->customer['ID'] );
    $customer_status = mysqli_fetch_assoc($res1);
    ((mysqli_free_result( $res1 ) || (is_object( $res1 ) && (get_class( $res1 ) == "mysqli_result"))) ? true : false);
    }
    else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
    {
    $customer_status = FALSE;
    }

    $all_products_price = 0;
    $prod_by_mwst = array();
    foreach( array_keys($order->products) as $i )
    {
        $prod = &$order->products[$i];
        $model = $langtext = '';
        $attributes = array();
        if (sizeof($order->products[$i]['attributes']) > 0)
        {
            for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j ++)
            {
                $model .= ($mod=act_get_attributes_model($order->products[$i]['id'], $order->products[$i]['attributes'][$j]['value'], $order->products[$i]['attributes'][$j]['option']));
                $langtext .= $order->products[$i]['attributes'][$j]['option'].': '.$order->products[$i]['attributes'][$j]['value']."\n";
                $attributes[] = array( $order->products[$i]['attributes'][$j]['option'], $order->products[$i]['attributes'][$j]['value'], $mod );
            }
        }

        if(empty($model) && isset($order->products[$i]['properties_combi_model']) && !empty($order->products[$i]['properties_combi_model']))
        {
            $model = $prod['model'];
            //.$order->products[$i]['properties_combi_model'];
        }
        $product = array(
            'art_nr' => (empty($model)?$prod['model']:$model),
            'art_nr_base' => (empty($model)?$prod['model']:$model),
            'art_name' => decode_entities($prod['name']),
            'preis' => $prod['price'],
            'is_brutto' => $prod['allow_tax'] != 0,
            'type' => 'Lief',
            'mwst' => $prod['tax'],
            'menge' => $prod['qty'],
            'langtext' => $langtext,
            'attributes' => $attributes,
        );
        $product['vk'] = $product['preis'];

        $products[] = $product;

        $preis = round( $prod['final_price'], 2 );
        $prod_by_mwst[(int)($prod['tax']*1000)] += $preis;
        $all_products_price += $preis;
        }

        // can't get this from $order->totals, as this misses type
        $totals = _export_totals( $order_id, TRUE );
        $delivery_country_id = _actindo_get_country_id( $order->delivery['country'] );

        foreach( $totals as $_key => $_val )
        {
        $shipping_type = null;
        $art_nr = $_key;
        switch( strtolower($_key) )
        {
          // don't need those.
          case 'ot_subtotal':
          case 'ot_subtotal_no_tax':
          case 'ot_tax':
          case 'ot_total':
          case 'ot_total_netto':
          case 'ot_gm_tax_free':
            continue;

          // already handled above in export_orders_list
          case 'ot_discount':
            continue;

          case 'ot_redemptions':
            $_i=0;
            $remaining = $_val[0];
            foreach( $prod_by_mwst as $_mwst => $_price )
            {
              $_i++;
              if( $_i == count($prod_by_mwst) )
                $price = $remaining;
              else
                $price = round( $_val[0] / $all_products_price * $_price, 2 );

              $remaining -= $price;
              $products[] = array(
                'art_nr' => strtoupper($art_nr),
                'art_name' => decode_entities($_val[1]),
                'preis' => $price * -1,
                'is_brutto' => 0,
                'type' => 'NLeist',
                'menge' => 1,
                'is_brutto' => 1,
                'mwst' => $_mwst / 1000,
              );
            }
            break;

          case 'ot_shipping':
            if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
            {
              $shipping_type = explode('_', $order->info['shipping_class'] );
              $art_nr .= '_'.$shipping_type[0];
            }
          default:
          case 'ot_cod_fee':
          case 'ot_coupon':
          case 'ot_gv':
          case 'ot_loworderfee':
          case 'ot_ps_fee':
            $tax_class = _actindo_get_ot_tax_class( $_key, $shipping_type );
            $steuer = act_get_tax_rate( $tax_class, $delivery_country_id, $p=act_get_geo_zone_code($delivery_country_id) );
            if( $_key == 'ot_coupon' || $_key == 'ot_gv' )
            {
                //$_val[0] *= -1;
            }
            $products[] = array(
              'art_nr' => strtoupper($art_nr),
              'art_name' => decode_entities($_val[1]),
              'preis' => $_val[0],
              'is_brutto' => !isset($customer_status) || $customer_status===FALSE ? 1 : $customer_status['customers_status_show_price_tax'],
              'type' => 'NLeist',
              'mwst' => $tax_class ? $steuer : 0,
              'menge' => 1,
            );
            break;
        }
    }
    return resp($products);
}


function _actindo_get_ot_tax_class( $class, $shipping_type=null )
{
  switch( $class )
  {
    case 'ot_shipping':
      $class = sprintf( "MODULE_SHIPPING_%s_TAX_CLASS", strtoupper($shipping_type[0]) );
      break;

    case 'ot_cod_fee':
      $class = 'MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS';
      break;
    case 'ot_coupon':
      $class = 'MODULE_ORDER_TOTAL_COUPON_TAX_CLASS';
      break;
    case 'ot_gv':
      $class = 'MODULE_ORDER_TOTAL_GV_TAX_CLASS';
      break;
    case 'ot_loworderfee':
      $class = 'MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS';
      break;
    case 'ot_ps_fee':
      $class = 'MODULE_ORDER_TOTAL_PS_FEE_TAX_CLASS';
      break;

    default:
      $class = 'MODULE_ORDER_TOTAL_'.strtoupper(substr($class,3)).'_TAX_CLASS';
      break;
  }

  $res = act_db_query( "SELECT configuration_value FROM configuration WHERE configuration_key='".$class."'" );
  $s = mysqli_fetch_row( $res );
  if( $s === FALSE || is_null($s) )
    return null;
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

  return $s[0];
}

function _actindo_get_tax_from_class( $tax_class_id, $tax_zone_id=1 )
{
  if( $tax_class_id > 0 )
  {
    $res = act_db_query( "SELECT * FROM tax_rates WHERE tax_class_id=".(int)$tax_class_id." AND tax_zone_id=".(int)$tax_zone_id );
    $tax = mysqli_fetch_row( $res );
    ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
    return (float)$tax[0];
  }
  return 0;
}

function _actindo_get_country_id( $country_name )
{
  $res = act_db_query( "SELECT `countries_id` FROM `countries` WHERE `countries_name`='".$country_name."'" );
  $c = mysqli_fetch_row( $res );
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
  return $c[0];
}
