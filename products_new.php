<?php
/* --------------------------------------------------------------
  products_new.php 2014-02-27 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(products_new.php,v 1.25 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (products_new.php,v 1.16 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_new.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_PRODUCTS_NEW, xtc_href_link(FILENAME_PRODUCTS_NEW));
$coo_products_new_content_view = MainFactory::create_object('ProductsNewContentView');
if(isset($_GET['page']))
{
	$coo_products_new_content_view->set_('page', $_GET['page']);
}
$coo_products_new_content_view->set_('customer_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
$coo_products_new_content_view->set_('customer_status_id', $_SESSION['customers_status']['customers_status_id']);
$coo_products_new_content_view->set_('language_id', $_SESSION['languages_id']);
$coo_products_new_content_view->set_('currency', $_SESSION['currency']);
$t_main_content = $coo_products_new_content_view->get_html();

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