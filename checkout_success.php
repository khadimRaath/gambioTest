<?php
/* --------------------------------------------------------------
  checkout_success.php 2014-02-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_success.php,v 1.48 2003/02/17); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_success.php,v 1.14 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_success.php 896 2005-04-27 19:22:59Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

// if the customer is not logged on, redirect them to the shopping cart page
if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

$coo_checkout_success_control = MainFactory::create_object('CheckoutSuccessContentControl');
$coo_checkout_success_control->set_data('GET', $_GET);
$coo_checkout_success_control->proceed();

$t_redirect_url = $coo_checkout_success_control->get_redirect_url();
if(!empty($t_redirect_url))
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_checkout_success_control->get_response();
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
