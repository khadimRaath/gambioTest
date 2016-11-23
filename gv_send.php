<?php
/* --------------------------------------------------------------
  gv_send.php 2014-02-21 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
  (c) 2002-2003 osCommerce (gv_send.php,v 1.1.2.3 2003/05/12); www.oscommerce.com
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: gv_send.php 1034 2005-07-15 15:21:43Z mz $)

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

if(ACTIVATE_GIFT_SYSTEM != 'true')
{
	xtc_redirect(FILENAME_DEFAULT);
}

// if the customer is not logged on, redirect them to the login page
if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$GLOBALS['breadcrumb']->add(NAVBAR_GV_SEND);

$coo_gv_send_control = MainFactory::create_object('GVSendContentControl');
$coo_gv_send_control->set_data('GET', $_GET);
$coo_gv_send_control->set_data('POST', $_POST);
$coo_gv_send_control->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
$coo_gv_send_control->set_('currency', $_SESSION['currency']);
$coo_gv_send_control->set_('customer_id', $_SESSION['customer_id']);
$coo_gv_send_control->set_('language', basename($_SESSION['language']));

$coo_gv_send_control->proceed();

$t_redirect_url = $coo_gv_send_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_gv_send_control->get_response();
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
