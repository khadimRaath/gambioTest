<?php
/* --------------------------------------------------------------
  header.php 2014-02-07 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(header.php,v 1.40 2003/03/14); www.oscommerce.com
  (c) 2003	 nextcommerce (header.php,v 1.13 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: header.php 1140 2005-08-10 10:16:00Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

/* 
 * !!! header.php is DEPRECATED !!!
 * use system/classes/layout/HeaderContentControl.inc.php and system/classes/layout/HeaderContentView.inc.php instead
 * refactored in GX2.1
 */

$coo_header_control = MainFactory::create_object('HeaderContentControl');
$coo_header_control->set_data('GET', $_GET);
$coo_header_control->set_data('POST', $_POST);
$coo_header_control->set_('c_path', $cPath);
$coo_header_control->set_('coo_product', $product);

if(isset($GLOBALS['payment_modules']))
{
	$coo_header_control->set_('coo_payment', $GLOBALS['payment_modules']);
}
		
$coo_header_control->proceed();

$t_redirect_url = $coo_header_control->get_redirect_url();
if(empty($t_redirect_url) == false) 
{
	xtc_redirect($t_redirect_url);
} 
else
{
	echo $coo_header_control->get_response();
}
