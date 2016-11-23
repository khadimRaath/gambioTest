<?php
/* --------------------------------------------------------------
  checkout_payment_address.php 2014-02-14 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_payment_address.php,v 1.13 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_payment_address.php,v 1.14 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_payment_address.php 993 2005-07-06 11:34:27Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_PAYMENT_ADDRESS, xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_PAYMENT_ADDRESS, xtc_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL'));

$coo_checkout_address_control = MainFactory::create_object('CheckoutAddressContentControl');
$coo_checkout_address_control->set_data('GET', $_GET);
$coo_checkout_address_control->set_data('POST', $_POST);
$coo_checkout_address_control->set_('page_type', 'payment');
$coo_checkout_address_control->set_('customer_id', $_SESSION['customer_id']);

if(isset($GLOBALS['order']) === false)
{
	 $GLOBALS['order'] = new order();
}

$coo_checkout_address_control->set_('coo_order', $GLOBALS['order']);

$coo_checkout_address_control->proceed();

$t_redirect_url = $coo_checkout_address_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_checkout_address_control->get_response();
}

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
