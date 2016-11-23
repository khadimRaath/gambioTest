<?php
/* --------------------------------------------------------------
   cart_actions.php 2014-08-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003         nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: cart_actions.php 1298 2005-10-09 13:14:44Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$coo_download_process = MainFactory::create_object('CartActionsProcess');
$coo_download_process->set_data('GET', $_GET);
$coo_download_process->set_data('POST', $_POST);

// Lokale
if(isset($t_turbo_buy_now))
{
	$coo_download_process->reference_set_('turbo_buy_now', $t_turbo_buy_now);
}
if(isset($t_show_cart))
{
	$coo_download_process->reference_set_('show_cart', $t_show_cart);
}
if(isset($t_show_details))
{
	$coo_download_process->reference_set_('show_details', $t_show_details);
}

// Globale
$coo_download_process->set_('php_self', $GLOBALS['PHP_SELF']);
$coo_download_process->set_('coo_seo_boost', $GLOBALS['gmSEOBoost']);
if(isset($GLOBALS['order']) && is_null($GLOBALS['order']) == false)
{
	$coo_download_process->set_('coo_order', $GLOBALS['order']);
}
if($GLOBALS['REMOTE_ADDR'] == false)
{
	$GLOBALS['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
}
$coo_download_process->reference_set_('remote_address', $GLOBALS['REMOTE_ADDR']);
$coo_download_process->set_('coo_price', $GLOBALS['xtPrice']);

// Session
if(isset($_SESSION['customer_id']))
{
	$coo_download_process->set_('customer_id', $_SESSION['customer_id']);
}
$coo_download_process->set_('coo_wish_list', $_SESSION['wishList']);
$coo_download_process->set_('coo_cart', $_SESSION['cart']);
if(isset($_SESSION['coo_gprint_wishlist']) && is_null($_SESSION['coo_gprint_wishlist']) == false)
{
	$coo_download_process->set_('coo_gprint_wish_list', $_SESSION['coo_gprint_wishlist']);
}
if(isset($_SESSION['coo_gprint_cart']) && is_null($_SESSION['coo_gprint_cart']) == false)
{
	$coo_download_process->set_('coo_gprint_cart', $_SESSION['coo_gprint_cart']);
}
if(isset($_SESSION['info_message']))
{
	$coo_download_process->reference_set_('info_message', $_SESSION['info_message']);
}
$coo_download_process->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
$coo_download_process->set_('customers_fsk18', $_SESSION['customers_status']['customers_fsk18']);
$coo_download_process->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);

$coo_download_process->proceed($_GET['action']);

$t_info_message = $coo_download_process->get_('info_message');
if(trim($t_info_message) != '')
{
	$_SESSION['info_message'] = $t_info_message;
}

$t_redirect_url = $coo_download_process->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}