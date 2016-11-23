<?php
/*
	--------------------------------------------------------------
	gm_popup_header.php 2011-01-13 gambio
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2011 Gambio GmbH
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
   ---------------------------------------------------------------------------------------*/

if(defined('_GM_VALID_CALL') === false) die('x0');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
<head>
<?php
/*
  The following copyright announcement is in compliance
  to section 2c of the GNU General Public License, and
  thus can not be removed, or can only be modified
  appropriately.

  Please leave this comment intact together with the
  following copyright announcement.

*/
?>
<!--
	This OnlineStore is brought to you by XT-Commerce, Community made shopping
	XTC is a free open source e-Commerce System
	created by Mario Zanier & Guido Winger and licensed under GNU/GPL.
	Information and contribution at http://www.xt-commerce.com
-->
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE.'/stylesheet.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?php echo 'templates/'.CURRENT_TEMPLATE.'/gm_dynamic.css.php'; ?>" />
<?php
if(is_dir(DIR_FS_CATALOG.'StyleEdit/')) {
	echo '<link type="text/css" rel="stylesheet" href="StyleEdit/stylesheet.css" />';
}
echo '<body class="popup-coupon-help" style="background-color: transparent">';

?>