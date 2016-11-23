<?php
/* --------------------------------------------------------------
  product_reviews_info.php 2014-02-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(product_reviews_info.php,v 1.47 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (product_reviews_info.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews_info.php 1238 2005-09-24 10:51:19Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

// lets retrieve all $HTTP_GET_VARS keys and values..
$get_params = xtc_get_all_get_params(array('reviews_id'));
$get_params = substr_wrapper($get_params, 0, -1); //remove trailing &

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_PRODUCT_REVIEWS, xtc_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

$coo_product_reviews_info_control = MainFactory::create_object('ProductReviewsInfoContentControl');
$coo_product_reviews_info_control->set_data('GET', $_GET);
$coo_product_reviews_info_control->set_data('POST', $_POST);
if(isset($_GET['reviews_id']))
{
	$coo_product_reviews_info_control->set_('review_id', $_GET['reviews_id']);
}
$coo_product_reviews_info_control->set_('language_id', $_SESSION['languages_id']);
$coo_product_reviews_info_control->proceed();

$t_redirect_url = $coo_product_reviews_info_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_product_reviews_info_control->get_response();
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