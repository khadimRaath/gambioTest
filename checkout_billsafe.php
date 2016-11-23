<?php
/* --------------------------------------------------------------
   checkout_billsafe.php 2012-12 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas PlÃ¤nkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/*
* this file get called in these cases:
* 1. as return_url in normal mode (with $_GET['token'] set) => check transaction result, forward to checkout_process or checkout_payment
* 2. as trigger page in layer mode (without $_GET['token']) => output pseudo-form to open payment layer
* 3. as return_url in layer mode (with $_GET['token'] and without $_GET['process']) => close layer, redirect onto itself for further processing as in 1.
*/

require_once 'includes/application_top.php';

if(strpos($_SESSION['payment'], 'billsafe_3') === false) {
	xtc_db_close();
	die('invalid payment module, aborting.');
}

$GLOBALS['breadcrumb']->add('BillSAFE', xtc_href_link(basename(__FILE__), '', 'SSL'));

$coo_billsafe_view = MainFactory::create_object('BillSafeContentView');
$coo_billsafe_view->set_('customer_id', $_SESSION['customer_id']);
$coo_billsafe_view->set_('languages_id', $_SESSION['languages_id']);
$coo_billsafe_view->set_('tracking_data_array', $_SESSION['tracking']);
$coo_billsafe_view->set_('coo_product', $GLOBALS['product']);
$coo_billsafe_view->set_('coo_message_stack', $GLOBALS['messageStack']);

$coo_billsafe_view->set_('layerform_action', GM_HTTP_SERVER.DIR_WS_CATALOG.basename(__FILE__));
$coo_billsafe_view->set_('lpg_close_url', GM_HTTP_SERVER.DIR_WS_CATALOG.basename(__FILE__));
$coo_billsafe_view->set_('sandbox_mode', strtolower(constant('MODULE_PAYMENT_'.strtoupper($_SESSION['payment']).'_SANDBOX')) == 'true' ? 'true' : 'false');
$coo_billsafe_view->set_('main_content', $main_content);
$coo_billsafe_view->set_('request_method', $_SERVER['REQUEST_METHOD']);
$coo_billsafe_view->set_('current_payment', $_SESSION['payment']);

if(isset($_SESSION['billsafe_token'])) {
	$coo_billsafe_view->set_('billsafe_token', $_SESSION['billsafe_token']);
}
if(isset($_REQUEST['layeredPaymentGateway']))
{
	$coo_billsafe_view->set_('layered_payment_gateway', $_REQUEST['layeredPaymentGateway']);
}
if(isset($_SESSION['billsafe_token']))
{
	$coo_billsafe_view->set_('billsafe_token', $_SESSION['billsafe_token']);
}
if(isset($_GET['mode']))
{
	$coo_billsafe_view->set_('mode', $_GET['mode']);
}
if(isset($_GET['token']))
{
	$coo_billsafe_view->set_('token', $_GET['token']);
}
if(isset($_GET['process']))
{
	$coo_billsafe_view->set_('process', $_GET['process']);
}

$t_main_content = $coo_billsafe_view->get_html();

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
