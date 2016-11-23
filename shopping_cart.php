<?php
/* --------------------------------------------------------------
  shopping_cart.php 2014-02-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(shopping_cart.php,v 1.71 2003/02/14); www.oscommerce.com
  (c) 2003	 nextcommerce (shopping_cart.php,v 1.24 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shopping_cart.php 1299 2005-10-09 18:54:29Z gwinger $)

  Released under the GNU General Public License
  --------------------------------------------------------------
  Third Party contributions:
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$cart_empty = false;

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_SHOPPING_CART, xtc_href_link(FILENAME_SHOPPING_CART));

$coo_shopping_cart_view = MainFactory::create_object('ShoppingCartContentView');

$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
$coo_shopping_cart_view->setXtcPrice($xtPrice);

$coo_shopping_cart_view->setLanguagesId((int)$_SESSION['languages_id']);
$coo_shopping_cart_view->setLanguageCode($_SESSION['language_code']);
$coo_shopping_cart_view->setCart($_SESSION['cart']);
$coo_shopping_cart_view->setCartCountContents($_SESSION['cart']->count_contents());
$coo_shopping_cart_view->setCustomerStatusMinOrder($_SESSION['customers_status']['customers_status_min_order']);
$coo_shopping_cart_view->setCustomerStatusMaxOrder($_SESSION['customers_status']['customers_status_max_order']);

$t_main_content = $coo_shopping_cart_view->get_html();

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

unset($_SESSION['nvpReqArray']);
unset($_SESSION['reshash']['FORMATED_ERRORS']);
unset($_SESSION['reshash']);
unset($_SESSION['tmp_oID']);
