<?php
/* --------------------------------------------------------------
  account_history_info.php 2015-07-22 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(account_history_info.php,v 1.97 2003/05/19); www.oscommerce.com
  (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_history_info.php 1309 2005-10-17 08:01:11Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY_INFO, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY_INFO, xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
$GLOBALS['breadcrumb']->add(sprintf(NAVBAR_TITLE_3_ACCOUNT_HISTORY_INFO, (int)$_GET['order_id']), xtc_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . (int)$_GET['order_id'], 'SSL'));

//security checks
if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}
if(isset($_GET['order_id']) === false || (isset($_GET['order_id']) && is_numeric($_GET['order_id']) === false))
{
	xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}

$customer_info_query = xtc_db_query("SELECT
											customers_id 
										FROM
											" . TABLE_ORDERS . " o,
											" . TABLE_CUSTOMERS_INFO . " ci
										WHERE 
											o.orders_id = '" . (int)$_GET['order_id'] . "' AND
											o.customers_id = '" . (int)$_SESSION['customer_id'] . "' AND 
											o.customers_id = ci.customers_info_id AND
											o.date_purchased >= ci.customers_info_date_account_created");
$customer_info = xtc_db_fetch_array($customer_info_query);

if($customer_info['customers_id'] != $_SESSION['customer_id'])
{
	xtc_redirect(xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
}

$coo_order = new order($_GET['order_id']);
$coo_account_history_info_view = MainFactory::create_object('AccountHistoryInfoContentView', array($_GET['order_id'], $_SESSION['languages_id'], $_SESSION['language'], $_SESSION['customer_id'], $coo_order));
$t_main_content = $coo_account_history_info_view->get_html();

$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$coo_layout_control->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
$coo_layout_control->set_('coo_product', $GLOBALS['product']);
$coo_layout_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
$coo_layout_control->set_('c_path', $GLOBALS['cPath']);
$coo_layout_control->set_('main_content', $t_main_content);
$coo_layout_control->set_('request_type', $GLOBALS['request_type']);
$coo_layout_control->proceed();

$t_redirect_url = $coo_layout_control->get_redirect_url();
if(empty($t_redirect_url) === false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	echo $coo_layout_control->get_response();
}
