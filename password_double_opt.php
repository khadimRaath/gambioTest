<?php
/* --------------------------------------------------------------
  password_double_opt.php 2014-02-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce www.oscommerce.com
  (c) 2003  nextcommerce www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: password_double_opt.php,v 1.0)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$breadcrumb->add(NAVBAR_TITLE_PASSWORD_DOUBLE_OPT, xtc_href_link(FILENAME_PASSWORD_DOUBLE_OPT, '', 'SSL'));

$coo_password_double_opt_control = MainFactory::create_object('PasswordDoubleOptContentControl');
$coo_password_double_opt_control->set_data('GET', $_GET);
$coo_password_double_opt_control->set_data('POST', $_POST);

$coo_password_double_opt_control->proceed($_SESSION['language']);

$t_redirect_url = $coo_password_double_opt_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_password_double_opt_control->get_response();
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
