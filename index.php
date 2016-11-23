<?php
/* --------------------------------------------------------------
  index.php 2016-05-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
  (c) 2003	 nextcommerce (default.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: index.php 1321 2005-10-26 20:55:07Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

include ('includes/application_top.php');

$coo_listing_control = MainFactory::create_object('ProductListingContentControl');

$coo_listing_control->set_data('GET', $_GET);
$coo_listing_control->set_data('POST', $_POST);

$coo_listing_control->set_('c_path', $GLOBALS['cPath']);

if(isset($_GET['cat']))
{
	$coo_listing_control->set_('cat', $_GET['cat']);
}
 
if(isset($GLOBALS['cID']))
{
	$coo_listing_control->set_('categories_id', $GLOBALS['cID']);
}

$coo_listing_control->set_('coo_filter_manager', $_SESSION['coo_filter_manager']);
$coo_listing_control->set_('coo_product', $GLOBALS['product']);
$coo_listing_control->set_('currency_code', $_SESSION['currency']);
$coo_listing_control->set_('current_category_id', $GLOBALS['current_category_id']);
$coo_listing_control->set_('current_page', basename($GLOBALS['PHP_SELF']));

if(isset($_GET['customer_country_id']))
{
	$coo_listing_control->set_('customer_country_id', $_SESSION['customer_country_id']);
}
else
{
	$coo_listing_control->set_('customer_country_id', STORE_COUNTRY);
}

if(isset($_GET['customer_zone_id']))
{
	$coo_listing_control->set_('customer_zone_id', $_SESSION['customer_zone_id']);
}
else
{
	$coo_listing_control->set_('customer_zone_id', STORE_ZONE);
}

$coo_listing_control->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);
$coo_listing_control->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);

if(isset($_GET['filter_fv_id']))
{
	$coo_listing_control->set_('filter_fv_id', $_GET['filter_fv_id']);
}

if(isset($_GET['filter_id']))
{
	$coo_listing_control->set_('filter_id', $_GET['filter_id']);
}


if(isset($_GET['filter_price_min']))
{
	$coo_listing_control->set_('filter_price_min', $_GET['filter_price_min']);
}

if(isset($_GET['filter_price_max']))
{
	$coo_listing_control->set_('filter_price_max', $_GET['filter_price_max']);
}

if(isset($_GET['feature_categories_id']))
{
	$coo_listing_control->set_('feature_categories_id', $_GET['feature_categories_id']);
}

if(empty($_SESSION['customers_status']['customers_status_graduated_prices']))
{
	$coo_listing_control->set_('show_graduated_prices', false);
}
else
{
	$coo_listing_control->set_('show_graduated_prices', true);
}

$coo_listing_control->set_('languages_id', $_SESSION['languages_id']);

if(isset($_SESSION['last_listing_sql']) == false)
{
	$_SESSION['last_listing_sql'] = '';
}
$coo_listing_control->reference_set_('last_listing_sql', $_SESSION['last_listing_sql']);

if(isset($_GET['listing_count']))
{
	$coo_listing_control->set_('listing_count', $_GET['listing_count']);
}

if(isset($_GET['listing_sort']))
{
	$coo_listing_control->set_('listing_sort', $_GET['listing_sort']);
}

if(isset($_GET['manufacturers_id']) && empty($_GET['manufacturers_id']) == false)
{
	$coo_listing_control->set_('manufacturers_id', $_GET['manufacturers_id']);
}

if(isset($_GET['page']) && !empty($_GET['page']))
{
	$coo_listing_control->set_('page_number', (int)$_GET['page']);
}

if(isset($_GET['sort']))
{
	$coo_listing_control->set_('sort', $_GET['sort']);
}

if(isset($_GET['value_conjunction']))
{
	$coo_listing_control->set_('value_conjunction', $_GET['value_conjunction']);
}

if(isset($_GET['view_mode']))
{
	$coo_listing_control->set_('view_mode', $_GET['view_mode']);
}

$coo_listing_control->set_('show_price_tax', $_SESSION['customers_status']['customers_status_show_price_tax']);

$coo_listing_control->proceed();

$t_redirect_url = $coo_listing_control->get_redirect_url();
if(empty($t_redirect_url) === false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_listing_control->get_response();
}

/** @var LayoutContentControl $coo_layout_control */
$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$t_category_id = 0;
if(isset($GLOBALS['cID']))
{
	$t_category_id = $GLOBALS['cID'];
}
$coo_layout_control->set_('category_id', $t_category_id);
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