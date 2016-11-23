<?php
/* --------------------------------------------------------------
  specials.php 2014-02-26 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(specials.php,v 1.47 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (specials.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: specials.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_SPECIALS, xtc_href_link(FILENAME_SPECIALS));

$t_page = 0;
if(isset($_GET['page']))
{
	$t_page = $_GET['page'];
}

$coo_specials_view = MainFactory::create_object('SpecialsPageContentView');
$coo_specials_view->set_('coo_product', $GLOBALS['product']);
$coo_specials_view->set_('language_id', $_SESSION['languages_id']);
$coo_specials_view->set_('currency', $_SESSION['currency']);
$coo_specials_view->set_('customer_status_id', $_SESSION['customers_status']['customers_status_id']);
$coo_specials_view->set_('page', $t_page);
$t_main_content = $coo_specials_view->get_html();

if($coo_specials_view->get_('redirect') === true)
{
	xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
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
