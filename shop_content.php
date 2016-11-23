<?php
/* --------------------------------------------------------------
   shop_content.php 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(conditions.php,v 1.21 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (shop_content.php,v 1.1 2003/08/19); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shop_content.php 1303 2005-10-12 16:47:31Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once('includes/application_top.php');

$t_subject = '';
if(isset($_POST['subject']) && !empty($_POST['subject']))
{
	$t_subject = stripslashes($_POST['subject']);
}
elseif(isset($_GET['subject']) && !empty($_GET['subject']))
{
	$t_subject = stripslashes($_GET['subject']);
}
else
{
	$t_subject = CONTACT_US_EMAIL_SUBJECT;
}

$t_name = '';
if(isset($_POST['name']) && !empty($_POST['name']))
{
	$t_name = stripslashes($_POST['name']);
}

$t_email_address = '';
if(isset($_POST['email']) && !empty($_POST['email']))
{
	$t_email_address = $_POST['email'];
}

$t_message_body = '';
if(isset($_POST['message_body']) && !empty($_POST['message_body']))
{
	$t_message_body = $_POST['message_body'];
}

$privacyAccepted = '0';
if(isset($_POST['privacy_accepted']))
{
	$privacyAccepted = '1';
}

$coo_shop_content_control = MainFactory::create_object('ShopContentContentControl');
$coo_shop_content_control->set_data('GET', $_GET);
$coo_shop_content_control->set_data('POST', $_POST);
$coo_shop_content_control->set_('coo_seo_boost', $gmSEOBoost);
$coo_shop_content_control->set_('breadcrumb', $breadcrumb);
$coo_shop_content_control->set_('subject', $t_subject);
$coo_shop_content_control->set_('name', $t_name);
$coo_shop_content_control->set_('email_address', $t_email_address);
$coo_shop_content_control->set_('message_body', $t_message_body);
$coo_shop_content_control->set_('privacy_accepted', $privacyAccepted);
$coo_shop_content_control->proceed();

$t_redirect_url = $coo_shop_content_control->get_redirect_url();
if(!empty($t_redirect_url))
{
	xtc_redirect($t_redirect_url);
}
else
{
	$t_main_content = $coo_shop_content_control->get_response();
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
