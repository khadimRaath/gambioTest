<?php
/* --------------------------------------------------------------
  wish_list.php 2014-10-29 gm
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
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shopping_cart.php,v 1.15 2004/04/25 13:58:08 fanta2k Exp $)

  Released under the GNU General Public License
  --------------------------------------------------------------
  Third Party contributions:
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$cart_empty = false;

require_once('includes/application_top.php');

unset($_SESSION['any_out_of_stock']);

/** @noinspection PhpUndefinedMethodInspection */
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_WISHLIST, xtc_href_link('wish_list.php'));

/** @var WishListContentView $coo_wish_list_view */
$coo_wish_list_view = MainFactory::create_object('WishListContentView');

if(isset($_SESSION['wishList']) === false)
{
	trigger_error('Session has no Object wishList', E_USER_ERROR);
}
$coo_wish_list_view->setCooWhishlist($_SESSION['wishList']);

if(isset($_GET['info_message']))
{
	$coo_wish_list_view->setInfoMessage($_GET['info_message']);
}
if(isset($_SESSION['gm_history']))
{
	$coo_wish_list_view->setGmHistory($_SESSION['gm_history']);
}
if(isset($_SESSION['any_out_of_stock']) === false)
{
	$_SESSION['any_out_of_stock'] = null;
}
$coo_wish_list_view->setAnyOutOfStock($_SESSION['any_out_of_stock']);


if(isset($_SESSION['allow_checkout']) === false)
{
	$_SESSION['allow_checkout'] = null;
}
$coo_wish_list_view->setAnyOutOfStock($_SESSION['allow_checkout']);



$t_main_content = $coo_wish_list_view->get_html();


/** @var LayoutContentControl $coo_layout_control */
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