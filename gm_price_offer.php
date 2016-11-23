<?php
/* --------------------------------------------------------------
  gm_price_offer.php 2015-09-19
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once('includes/application_top.php');

$GLOBALS['breadcrumb']->add(GM_PRICE_OFFER_NAVBAR_TITLE, xtc_href_link('gm_price_offer.php'));

$coo_price_offer_view = MainFactory::create_object('PriceOfferContentView', array($_GET, $_POST));
$coo_price_offer_view->set_('v_env_get_array', $_GET);
$coo_price_offer_view->set_('v_env_post_array', $_POST);
if(isset($_GET['products_id']))
{
	$coo_price_offer_view->set_('product_id', $_GET['products_id']);
}
else
{
	xtc_redirect(xtc_href_link(FILENAME_DEFAULT));
}
$coo_price_offer_view->set_('language_id', $_SESSION['languages_id']);
if(isset($_GET['properties_values_ids']))
{
	$coo_price_offer_view->set_('propertie_value_ids_array', $_GET['properties_values_ids']);
}
if(isset($_GET['id']))
{
	$coo_price_offer_view->set_('attributes_ids_array', $_GET['id']);
}

$t_main_content = $coo_price_offer_view->get_html();

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
