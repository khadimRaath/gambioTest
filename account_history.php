<?php
/* --------------------------------------------------------------
  account_history.php 2014-10-24 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(account_history_info.php,v 1.97 2003/05/19); www.oscommerce.com
  (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account_history.php 1309 2005-10-17 08:01:11Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_ACCOUNT_HISTORY, xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_ACCOUNT_HISTORY, xtc_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));

$t_page = 0;
if(isset($_GET['page']))
{
	$t_page = $_GET['page'];
}

/* @var AccountHistoryContentView $coo_account_history_view */
$coo_account_history_view = MainFactory::create_object('AccountHistoryContentView');
$coo_account_history_view->setCustomerId($_SESSION['customer_id']);
$coo_account_history_view->setlanguageId($_SESSION['languages_id']);
$coo_account_history_view->setPage($t_page);
$t_main_content = $coo_account_history_view->get_html();

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

