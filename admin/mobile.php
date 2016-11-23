<?php
/* --------------------------------------------------------------
   mobile.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

define("V_1_0","1.0");
define("CURRENT_VERSION","1.0");
define("GET_ORDERS","getOrders");
define("GET_DETAILS","getOrderDetails");  
define("GET_STATS","getStats");
define("GET_ONLINE_DATA","getOnlineData");
define("DATA_TYPE_GUESTS","Guests");
define("DATA_TYPE_CUSTOMERS","Customers");
define("GET_CART_DATA","getCartData"); 
define("GET_NEWS",'getNews');
define("GET_SCRIPT_VERSION",'getScriptVersion');

require('includes/application_top.php'); 
require(DIR_FS_INC. 'xtc_get_products.inc.php');
require(DIR_FS_ADMIN . 'includes/gm/classes/GMStart.php');


if(isset($_POST)){
  
  $action     = $_POST['action'];
  $lastUpdate = $_POST['lastUpdate']; 
  $order_id   = $_POST['order_id'];
  $version    = $_POST['use_script_version'];
  $data_typ   = $_POST['data_type'];
  $cust_id    = $_POST['customer_id'];
  $session_id = $_POST['session_id'];
  
  if($action == GET_SCRIPT_VERSION){
  	getScriptVersion();
  }

  if($version == V_1_0){
    if($action == GET_ORDERS){
      getOrders($lastUpdate);
    }else if($action == GET_DETAILS){
      getOrderDetails($order_id);
    }else if($action == GET_STATS){
      getStats();
    }else if($action == GET_ONLINE_DATA){
      getOnlineData();
    }else if($action == GET_CART_DATA){
      getUserCartData($session_id);
    }else if($action == GET_NEWS){
      getNewsUrl();
    }
  }
}  


function getScriptVersion(){
    $response = array('VERSION'=>CURRENT_VERSION);
    echo json_encode($response);
}
  
  
//===== functions for V_1_0 ============
function getOrderDetails($order_id){
   			
 $sql = "select o.customers_id, o.customers_name, o.customers_company, o.orders_id, o.customers_address_format_id, o.currency,
 o.customers_street_address, o.customers_city, o.customers_postcode, o.customers_state, o.customers_email_address, o.customers_telephone,
 o.delivery_name, o.delivery_company, o.delivery_address_format_id, o.delivery_street_address, o.delivery_city, o.delivery_postcode, o.delivery_state, 
 o.delivery_country, o.delivery_country_iso_code_2, o.billing_name, billing_company, o.billing_address_format_id, o.billing_street_address, 
 o.billing_city, o.billing_postcode, o.billing_country, o.billing_country_iso_code_2, date_format( o.date_purchased, '%d.%m.%Y %H:%i:%s' ) as order_date,
 date_format( o.last_modified, '%d.%m.%Y %H:%i:%s' ) as modified, st.orders_status_name as order_state,
 (select count(customers_name) from ". TABLE_ORDERS ." as o, ". TABLE_ORDERS_STATUS ." as st where st.orders_status_id = o.orders_status and customers_id = o.customers_id) as amount_orders
 from ". TABLE_ORDERS ." as o, ". TABLE_ORDERS_STATUS . " as st
 where o.orders_id = $order_id and o.orders_status = st.orders_status_id and st.language_id =".$_SESSION['languages_id'];
    
 $order_data  = xtc_db_query($sql);

 $sql2 = "select op.products_model, op.products_name, op.products_price, op.products_tax, op.final_price, op.products_quantity,
 (select value from ". TABLE_ORDERS_TOTAL ." where class = 'ot_shipping' and orders_id = op.orders_id) as order_shipping,
 (select value from ". TABLE_ORDERS_TOTAL ." where class = 'ot_total' and orders_id = op.orders_id) as order_total,
 (select value from ". TABLE_ORDERS_TOTAL ." where class = 'ot_gv' and orders_id = op.orders_id) as order_discount
 from ". TABLE_ORDERS_PRODUCTS ." as op 
 where op.orders_id = $order_id;";

 $order_items = xtc_db_query($sql2);
 
  $details = array();
  while($orderDataRow = xtc_db_fetch_array($order_data)){
      
    performUTF8Decoding($orderDataRow);
  
    $items = array();
    $shipping; $total;
    while($itemData = xtc_db_fetch_array($order_items)){
      
      performUTF8Decoding($itemData);
      
      $item  = array(
      'M'   => $itemData['products_model'],
      'N'   => $itemData['products_name'],
      'P'   => $itemData['products_price'],
      'Q'   => $itemData['products_quantity'],
      'T'   => $itemData['products_tax'],
      'F'   => $itemData['final_price']);
      
      $shipping = $itemData['order_shipping'];
      $discount = $itemData['order_discount'];
      $total    = $itemData['order_total'];
      array_push($items,$item);
    }
     
    
    $details = array(
    'CID'       => $orderDataRow['customers_id'],
    'N'     	=> $orderDataRow['customers_name'],
    'CO'    	=> $orderDataRow['customers_company'],
    'OID'   	=> $orderDataRow['orders_id'],
    'CAF'   	=> $orderDataRow['customers_address_format_id'],
    'CS'    	=> $orderDataRow['customers_street_address'],
    'CC'    	=> $orderDataRow['customers_city'],
    'CP'    	=> $orderDataRow['customers_postcode'],
    'CST'   	=> $orderDataRow['customers_state'],
    'CEA'   	=> $orderDataRow['customers_email_address'], 
    'CTEL'  	=> $orderDataRow['customers_telephone'],
    'DELAF' 	=> $orderDataRow['delivery_address_format_id'],
    'DELN'  	=> $orderDataRow['delivery_name'],
    'DELCO' 	=> $orderDataRow['delivery_company'],
    'DELS'  	=> $orderDataRow['delivery_street_address'],
    'DELC'  	=> $orderDataRow['delivery_city'],
    'DELP'  	=> $orderDataRow['delivery_postcode'],
    'DELCON'	=> $orderDataRow['delivery_country'],
    'DELCC' 	=> $orderDataRow['delivery_country_iso_code_2'],
    'BILLAF'	=> $orderDataRow['billing_address_format_id'],
    'BILLN' 	=> $orderDataRow['billing_name'],
    'BILLCO'	=> $orderDataRow['billing_company'],
    'BILLA' 	=> $orderDataRow['billing_street_address'],
    'BILLC' 	=> $orderDataRow['billing_city'],
    'BILLP' 	=> $orderDataRow['billing_postcode'],
    'BILLCON' 	=> $orderDataRow['billing_country'],
    'BILLCC'	=> $orderDataRow['billing_country_iso_code_2'],
    'AMOUNT'	=> $orderDataRow['amount_orders'],
    'STATE' 	=> $orderDataRow['order_state'],
    'DATE'  	=> $orderDataRow['order_date'],
    'MODIFIED'	=> $orderDataRow['modified'],
    'ITEMS' 	=> $items,
    'SHIP'  	=> $shipping,
    'DISCOUNT'  => $discount,
    'CURRENCY' => $orderDataRow['currency'],
    'TOTAL' 	=> $total);

    $response = array('DETAILS'=>$details);
    echo json_encode($response);
  }
}
  
  
function getOrders($last_update){
 
    $filter = "";
    if(isset($last_update) && preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/",$last_update)){
        $filter .= " and (o.date_purchased > '$last_update' 
        or o.last_modified > '$last_update')";
    }
  
    $sql = "select distinct o.orders_id, o.customers_id, o.customers_name, o.currency, st.orders_status_id as order_state_id,
    o.customers_email_address as customers_email, date_format(o.date_purchased,'%d.%m.%Y %H:%i:%s') as order_date,
    st.orders_status_name as order_state,
    (select value from ". TABLE_ORDERS_TOTAL ." as ot where o.orders_id = ot.orders_id and class='ot_total') as order_total,
    o.last_modified from ". TABLE_ORDERS ." as o, orders_status as st where st.orders_status_id = o.orders_status
    and st.language_id = ".$_SESSION['languages_id']." $filter order by o.date_purchased desc;";

    $order_data = xtc_db_query($sql);

    $orders = array();
    while($orderDataRow =  xtc_db_fetch_array($order_data)){

        performUTF8Decoding($orderDataRow); 

        $order = array(
        'OID' => $orderDataRow['orders_id'],
        'CID' => $orderDataRow['customers_id'],
        'N'   => $orderDataRow['customers_name'],
        'E'   => $orderDataRow['customers_email'],
        'D'   => $orderDataRow['order_date'],
        'SID' => $orderDataRow['order_state_id'],
        'S'   => $orderDataRow['order_state'],
        'C'   => $orderDataRow['currency'],
        'T'   => $orderDataRow['order_total'],
        'M'   => $orderDataRow['last_modified']);
        
        array_push($orders,$order);
    }
    
    $response = array('ORDERS' => $orders);
    echo json_encode($response);
}
    
// Mehtod to get statistic information  
function getStats(){
  
  $gmStart       = new GMStart();
  $visits_today  = $gmStart->gm_rates['VISITORS']['TODAY'];
  $visits_yest   = $gmStart->gm_rates['VISITORS']['YESTERDAY'];
  $visits_diff   = $gmStart->gm_rates['VISITORS']['DIFFERENCE'];
    
  $orders_today  = $gmStart->gm_rates['ORDERS']['TODAY'];
  $orders_yest   = $gmStart->gm_rates['ORDERS']['YESTERDAY'];
  $orders_diff   = $gmStart->gm_rates['ORDERS']['DIFFERENCE'];
    
  $sales_today   = $gmStart->gm_rates['SALES']['TODAY'];
  $sales_yest    = $gmStart->gm_rates['SALES']['YESTERDAY'];
  $sales_diff    = $gmStart->gm_rates['SALES']['DIFFERENCE'];
  
  $hits_today    = $gmStart->gm_rates['HITS']['TODAY'];
  $hits_yest     = $gmStart->gm_rates['HITS']['YESTERDAY'];
  $hits_diff     = $gmStart->gm_rates['HITS']['DIFFERENCE'];
    
  $average_today = 0;
  if($orders_today > 0){
    $average_today = $orders_today/$sales_today;
  }
    
  $average_yest  = 0;
  if($average_yest > 0){
    $average_yest  = $orders_yest/$sales_today;
  }
    
  $result = array('VT' => $visits_today,
  'VY' => $visits_yest,
  'VD' => $visits_diff,
  'OT' => $orders_today,
  'OY' => $orders_yest, 
  'OD' => $orders_diff, 
  'ST' => $sales_today,
  'SY' => $sales_yest,
  'SD' => $sales_diff,
  'AT' => $average_today,
  'AY' => $average_yest,
  'HT' => $hits_today,
  'HY' => $hits_yest,
  'HD' => $hits_diff);
    
  $response = array('STATS' => $result);
  echo json_encode($response);
}


function getNewsUrl(){
    $gmStart   = new GMStart();
    $ping_host = $gmStart->ping_host;
    $ping_path = $gmStart->ping_path;
    $response  = array('NEWS_URL' => array('PING'=>$ping_host,'PATH'=>$ping_path));
    echo json_encode($response);
}
  
  
function getOnlineData(){
    $whos_online_query = xtc_db_query("select customer_id, full_name, ip_address, time_entry, time_last_click, last_page_url, session_id from ". TABLE_WHOS_ONLINE ." order by time_last_click desc");
    $entries = array();

    while($row = xtc_db_fetch_array($whos_online_query)){
     
        performUTF8Decoding($row);
        
        $user_session = getUserSessionById($row['session_id']);

        $data = array(
                'CID' => $row['customer_id'],
                'CN'  => $row['full_name'],
                'IP'  => $row['ip_address'],
                'TE'  => $row['time_entry'],
                'LA'  => $row['time_last_click'],
                'LP'  => $row['last_page_url'],
                'S'   => $row['session_id'],
                'V'   => $user_session['cart']->total,
                'C'   => $user_session['currency']
                );  
        array_push($entries,$data);
    }

    $cart_data 	= array('ONLINE_DATA'=>$entries);
    echo json_encode($cart_data);
}
  
  
function getUserCartData($session_id){
    $user_session = getUserSessionById($session_id);
    $products     = xtc_get_products($user_session);

    $items = array();
    foreach($products as $prod){
        
        performUTF8Decoding($prod);

        $tx  = xtc_db_query("select tax_rate from ". TABLE_TAX_RATES ." where tax_rates_id = ".$prod['tax_class_id']);
        $tax = xtc_db_fetch_array($tx);

        $item  = array(
        'M'   => $prod['model'],
        'N'   => $prod['name'],
        'P'   => $prod['price'],
        'Q'   => $prod['quantity']*1,
        'T'   => (!empty($tax)) ? $tax['tax_rate']*1 : 0,
        'F'   => $prod['final_price']);
        
        array_push($items,$item);
    }

    $response = array('DETAILS'=>array('ITEMS'=>$items,'SHIP'=>0,'TOTAL' => $user_session['cart']->total, 'CURRENCY'=>$user_session['currency']));
    echo json_encode($response);
}


function getUserSessionById($session_id){
		
    $info = $session_id;
    $session_data = null;
    if ( (file_exists(xtc_session_save_path() . '/sess_' . $info)) && (filesize(xtc_session_save_path() . '/sess_' . $info) > 0) ) {
        $session_data = file(xtc_session_save_path() . '/sess_' . $info);
        $session_data = trim(implode('', $session_data));
    }

    $user_session = unserialize_session_data($session_data);
    return $user_session;
}


function performUTF8Decoding(&$orderDataRow) {
    foreach ($orderDataRow as $key => $value) {
        $orderDataRow[$key] = $value;
    }
}

require('includes/application_bottom.php');