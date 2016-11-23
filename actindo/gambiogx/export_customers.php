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


function export_customers_count( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $counts = array();

    $res = act_db_query( "SELECT MAX(customers_id) AS cnt FROM ".TABLE_CUSTOMERS );
    $tmp = act_db_fetch_assoc($res);
    $counts['max_customers_id'] = (int)$tmp['cnt'];
    act_db_free($res);

    $res = act_db_query( "SELECT MAX(customers_cid) AS deb_kred_id FROM ".TABLE_CUSTOMERS );
    $tmp = act_db_fetch_assoc($res);
    $counts['max_deb_kred_id'] = (int)$tmp['deb_kred_id'];
    act_db_free($res);

    $res = act_db_query( "SELECT COUNT(customers_id) AS cnt FROM ".TABLE_CUSTOMERS );
    $tmp = act_db_fetch_assoc($res);
    $counts['count'] = (int)$tmp['cnt'];
    act_db_free($res);

    return resp(array( 'ok'=>TRUE, 'counts' => $counts ));
}


function export_customers_list( $params )
{
  if( !parse_args($params,$ret) )
        return $ret;
  $just_list = $params['list'];
  $filters = $params['filters'];
  $gender_map = actindo_get_gender_map( );
  $def_lang = default_lang();
//  $paymentmeans = actindo_get_paymentmeans( );

  $mapping = array(
    '_customers_id' => array('cc', 'customers_id'),
    'deb_kred_id' =>   array('cc', 'customers_cid'),
    'vorname' =>       array('ab', 'entry_firstname'),
    'name' =>          array('ab', 'entry_lastname'),
    'firma' =>         array('ab', 'entry_company'),
    'land' =>          array('bc', 'countryiso'),
    'email' =>         array('cc', 'customers_email_address'),
  );
  $qry = create_query_from_filter( $filters, $mapping );
  if( $qry === FALSE )
    return array( 'ok'=>false, 'errno'=>EINVAL, 'error'=>'Error in filter definition' );
/*
  $orders = array();

  $q = "1";
  if( is_array($filters) && count($filters) )
  {
    switch( $filters[0] )
    {
      case 'customers_id':
        $q = '`customers_id` BETWEEN '.(int)$filters[1].' AND '.(int)$filters[2];
        break;
      case 'customers_cid':
        $q = '`customers_cid` BETWEEN '.(int)$filters[1].' AND '.(int)$filters[2];
        break;
      case 'customers_date_added':
      case 'customers_last_modified':
        $q = '`'.$filters[0].'` BETWEEN '.esc($filters[1]).' AND '.esc($filters[2]);
        break;
      // else unfiltered
    }
  }
*/

  if( $just_list )
  {
    $sql = "SELECT SQL_CALC_FOUND_ROWS cc.customers_id, cc.customers_email_address, '' AS language, cc.customers_cid,
      ab.entry_gender, ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_postcode, ab.entry_city, c.countries_iso_code_2 AS countryiso FROM (`customers` AS cc, `address_book` AS ab) LEFT JOIN `countries` AS c ON (ab.entry_country_id=c.countries_id) WHERE ab.address_book_id=cc.customers_default_address_id AND {$qry['q_search']} ORDER BY {$qry['order']}, cc.`customers_id` DESC LIMIT {$qry['limit']}";
  }
  else
  {
    $sql = "SELECT SQL_CALC_FOUND_ROWS cc.customers_id, cc.customers_email_address, '' AS language, cc.customers_cid,
      cc.*, ab.*, c.countries_iso_code_2 AS countryiso, cs.customers_status_show_price_tax FROM (`customers` AS cc, `address_book` AS ab) LEFT JOIN `countries` AS c ON (ab.entry_country_id=c.countries_id) LEFT JOIN ".TABLE_CUSTOMERS_STATUS." AS cs ON (cs.customers_status_id=cc.customers_status AND cs.language_id=".(int)$def_lang.") WHERE ab.address_book_id=cc.customers_default_address_id AND {$qry['q_search']} ORDER BY {$qry['order']}, cc.`customers_id` DESC LIMIT {$qry['limit']}";
  }

  $res = act_db_query( $sql );

  $res1 = act_db_query( "SELECT FOUND_ROWS()" );
  $count = act_db_fetch_row( $res1 );
  act_db_free( $res1 );

  while( $customer = act_db_fetch_assoc($res) )
  {
    $id = (int)$customer['customers_id'];
    $delivery_id = (int)$customer['address_book_id'];

    if( $just_list )
    {
      $actindocustomer = array(
        'deb_kred_id' => (int)($customer['customers_cid'] > 0 ? $customer['customers_cid'] : 0),
        'anrede' => !empty($customer['entry_company']) ? 'Firma' : $gender_map[$customer['entry_gender']],
        'kurzname' => !empty($customer['entry_company']) ? $customer['entry_company'] : $customer['entry_lastname'],
        'firma' => $customer['entry_company'],
        'name' => $customer['entry_lastname'],
        'vorname' => $customer['entry_firstname'],
        'adresse' => $customer['entry_street_address'],
        'plz' => $customer['entry_postcode'],
        'ort' => $customer['entry_city'],
        'land' => $customer['countryiso'],
        'email' => $customer['customers_email_address'],
        '_customers_id' => (int)$customer['customers_id'],
      );
    }
    else
    {
      $actindocustomer = array(
        'deb_kred_id' => (int)($customer['customers_cid'] > 0 ? $customer['customers_cid'] : 0),
        'anrede' => !empty($customer['entry_company']) ? 'Firma' : $gender_map[$customer['entry_gender']],
        'kurzname' => !empty($customer['entry_company']) ? $customer['entry_company'] : $customer['entry_lastname'],
        'firma' => $customer['entry_company'],
        'name' => $customer['entry_lastname'],
        'vorname' => $customer['entry_firstname'],
        'adresse' => $customer['entry_street_address'],
        'adresse2' => $customer['entry_suburb'],
        'plz' => $customer['entry_postcode'],
        'ort' => $customer['entry_city'],
        'land' => $customer['countryiso'],
        'tel' => $customer['customers_telephone'],
        'fax' => $customer['customers_fax'],
        'ustid' => $customer['customers_vat_id'],
        'email' => $customer['customers_email_address'],
        'print_brutto' => $customer['customers_status_show_price_tax'] ? 1 : 0,
        '_customers_id' => (int)$customer['customers_id'],
        'currency' => 'EUR',

        'delivery_addresses' => array(),
      );


      $sql = "SELECT ab.*, c.countries_iso_code_2 AS countryiso FROM `address_book` AS ab LEFT JOIN `countries` AS c ON (ab.entry_country_id=c.countries_id) WHERE ab.customers_id=".(int)$customer['customers_id'];
      $res2 = act_db_query( $sql );
      while( $delivery = act_db_fetch_array($res2) )
      {
        $actindodelivery = array(
          'delivery_id' => (int)$delivery['address_book_id'],
          'delivery_anrede' => !empty($delivery['entry_company']) ? 'Firma' : $gender_map[$delivery['entry_gender']],
          'delivery_kurzname' => !empty($delivery['entry_company']) ? $delivery['entry_company'] : $delivery['entry_lastname'],
          'delivery_firma' => $delivery['entry_company'],
          'delivery_name' => $delivery['entry_lastname'],
          'delivery_vorname' => $delivery['entry_firstname'],
          'delivery_adresse' => $delivery['entry_street_address'],
          'delivery_adresse2' => $delivery['entry_suburb'],
          'delivery_plz' => $delivery['entry_postcode'],
          'delivery_ort' => $delivery['entry_city'],
          'delivery_land' => $delivery['countryiso'],
        );
        if( $delivery['address_book_id'] == $customer['customers_default_address_id'] )
        {
          $actindodelivery['delivery_as_customer'] = 1;
          $actindocustomer = array_merge( $actindocustomer, $actindodelivery );
        }
        else
          $actindocustomer['delivery_addresses'][] = $actindodelivery;
      }
    }

    $customers[] = $actindocustomer;
  }
  act_db_free( $res );


  return resp(array( 'ok'=>TRUE, 'customers'=>$customers, 'count'=>$count[0] ));
}
