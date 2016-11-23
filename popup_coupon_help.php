<?php
/* --------------------------------------------------------------
   popup_coupon_help.php 2014-09-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(popup_coupon_help.php,v 1.1.2.5 2003/05/02); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: popup_coupon_help.php 1313 2005-10-18 15:49:15Z mz $)


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require ('includes/application_top.php');

require_once (DIR_FS_CATALOG . 'gm/modules/gm_popup_header.php');

$coo_popup_coupon_help = MainFactory::create_object('PopupCouponHelpContentView');
$coo_popup_coupon_help->set_('coupon_id', (int)$_GET['cID']);
$coo_popup_coupon_help->set_('language_id', (int)$_SESSION['languages_id']);
$coo_popup_coupon_help->set_('coo_xtc_price', $xtPrice);
$t_view_html = $coo_popup_coupon_help->get_html();

echo $t_view_html;

xtc_db_close();
