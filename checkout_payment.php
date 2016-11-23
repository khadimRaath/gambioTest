<?php
/* --------------------------------------------------------------
  checkout_payment.php 2015-06-22 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_payment.php,v 1.110 2003/03/14); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_payment.php,v 1.20 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_payment.php 1325 2005-10-30 10:23:32Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  agree_conditions_1.01        	Autor:	Thomas Plänkers (webmaster@oscommerce.at)

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

// if the customer is not logged on, redirect them to the login page
if(!isset($_SESSION['customer_id']))
{
	if(ACCOUNT_OPTIONS == 'guest')
	{
		xtc_redirect(xtc_href_link('shop.php', 'do=CreateGuest&checkout_started=1', 'SSL'));
	}
	else
	{
		xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
	}
}

$coo_checkout_payment_control = MainFactory::create_object('CheckoutPaymentContentControl');
$coo_checkout_payment_control->set_data('GET', $_GET);
$coo_checkout_payment_control->set_data('POST', $_POST);

$coo_checkout_payment_control->proceed();

$t_redirect_url = $coo_checkout_payment_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$GLOBALS['payment_modules'] = $coo_checkout_payment_control->get_('coo_payment');
	$t_main_content = $coo_checkout_payment_control->get_response();
}

$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$coo_layout_control->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
$coo_layout_control->set_('coo_payment', $coo_checkout_payment_control->get_('coo_payment'));
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
