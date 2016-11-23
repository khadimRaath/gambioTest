<?php
/* --------------------------------------------------------------
  skrill_iframe.php 2014-05-06 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_process.php,v 1.128 2003/05/28); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_process.php,v 1.30 2003/08/24); www.nextcommerce.org
  (c) 2009 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_payment_iframe.php 44 2009-01-27 15:38:52Z mzanier $)

  Released under the GNU General Public License
--------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

// include needed functions
require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');
require_once(DIR_FS_INC . 'xtc_address_label.inc.php');
require_once(DIR_FS_INC . 'changedatain.inc.php');

// if the customer is not logged on, redirect them to the login page
if(!isset($_SESSION['customer_id']))
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

if($_SESSION['customers_status']['customers_status_show_price'] != '1')
{
	xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', ''));
}

if((xtc_not_null(MODULE_PAYMENT_INSTALLED)) && (!isset($_SESSION['payment'])))
{
	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if(isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID']))
{
	if($_SESSION['cart']->cartID != $_SESSION['cartID'])
	{
		xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
	}
}

// load selected payment module
require_once(DIR_WS_CLASSES . 'payment.php');

$payment_modules = new payment($_SESSION['payment']);

// load the selected shipping module
require_once(DIR_WS_CLASSES . 'shipping.php');
$shipping_modules = new shipping($_SESSION['shipping']);

require_once(DIR_WS_CLASSES . 'order_total.php');
require_once(DIR_WS_CLASSES . 'order.php');
$GLOBALS['order'] = new order($_SESSION['tmp_oID']);

$coo_cart_shipping_costs_control = MainFactory::create_object('CartShippingCostsControl', array(), true);
$t_country_array = $coo_cart_shipping_costs_control->get_selected_country();		 
$t_country = xtc_get_countriesList( key($t_country_array), true, true );

$GLOBALS['order']->delivery['country'] = array();
$GLOBALS['order']->delivery['country']['id'] = key($t_country_array);
$GLOBALS['order']->delivery['country']['iso_code_2'] = $t_country['countries_iso_code_2'];

$order_total_modules = new order_total();
$order_total_modules->process();

$t_iframe_url = $payment_modules->iframeAction();

if($t_iframe_url == '')
{
	xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

$t_main_content = '<iframe src="' . $t_iframe_url . '" width="100%" height="780" name="_top" frameborder="0"></iframe>';

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
