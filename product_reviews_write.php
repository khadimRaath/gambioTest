<?php
/* --------------------------------------------------------------
  product_reviews_write.php 2015-10-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(product_reviews_write.php,v 1.51 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (product_reviews_write.php,v 1.13 2003/08/1); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews_write.php 1101 2005-07-24 14:51:13Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_REVIEWS_WRITE);

if($_SESSION['customers_status']['customers_status_write_reviews'] == 0)
{
	xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
}

$t_customer_id = 0;
if(isset($_SESSION['customer_id']))
{
	$t_customer_id = $_SESSION['customer_id'];
}

$coo_product_reviews_write_control = MainFactory::create_object('ProductReviewsWriteContentControl', array($product, $t_customer_id));
$coo_product_reviews_write_control->set_data('GET', $_GET);
$coo_product_reviews_write_control->set_data('POST', $_POST);

global $gmSEOBoost;
$coo_product_reviews_write_control->set_('gmSEOBoost', $gmSEOBoost);

$coo_product_reviews_write_control->proceed();

$t_redirect_url = $coo_product_reviews_write_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_product_reviews_write_control->get_response();
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
