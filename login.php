<?php
/* --------------------------------------------------------------
  login.php 2015-03-24 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(login.php,v 1.79 2003/05/19); www.oscommerce.com
  (c) 2003      nextcommerce (login.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: login.php 1143 2005-08-11 11:58:59Z gwinger $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  guest account idea by Ingo T. <xIngox@web.de>
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

if(isset($_SESSION['customer_id']))
{
	xtc_redirect(xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
}

if($GLOBALS['navigation']->snapshot['page'] == FILENAME_CHECKOUT_SHIPPING)
{
	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_LOGIN_CHECKOUT, xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}
else
{
	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_LOGIN, xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$coo_login_control = MainFactory::create_object('LoginContentControl');
$coo_login_control->set_data('GET', $_GET);
$coo_login_control->set_data('POST', $_POST);

$coo_login_control->proceed();

$t_redirect_url = $coo_login_control->get_redirect_url();
if(empty($t_redirect_url) === false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_login_control->get_response();
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
