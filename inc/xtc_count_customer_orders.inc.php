<?php
/* --------------------------------------------------------------
   xtc_count_customer_orders.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_count_customer_orders.inc.php,v 1.3 2003/08/13); www.nextcommerce.org 
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_count_customer_orders.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  function xtc_count_customer_orders($id = '', $check_session = true) {

    if (is_numeric($id) == false) {
      if (isset($_SESSION['customer_id'])) {
        $id = $_SESSION['customer_id'];
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( (isset($_SESSION['customer_id']) == false) || ($id != $_SESSION['customer_id']) ) {
        return 0;
      }
    }

    $orders_check_query = xtc_db_query("SELECT COUNT(*) AS total 
										FROM
											" . TABLE_ORDERS . " o,												
											" . TABLE_CUSTOMERS_INFO . " ci
										WHERE
											o.customers_id = '" . (int)$id . "'
											AND o.customers_id = ci.customers_info_id 
											AND o.date_purchased >= ci.customers_info_date_account_created");
    $orders_check = xtc_db_fetch_array($orders_check_query);
    return $orders_check['total'];
  }
 ?>