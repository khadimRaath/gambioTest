<?php
/* --------------------------------------------------------------
   print_product_info.php 2014-02-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com 
   (c) 2003	 nextcommerce (print_product_info.php,v 1.16 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: print_product_info.php 1282 2005-10-03 19:39:36Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

$coo_print_product_info = MainFactory::create_object('PrintProductInfoContentView');
$coo_print_product_info->set_("customers_status_show_price", $_SESSION['customers_status']['customers_status_show_price']);
$coo_print_product_info->set_("customers_status_show_price_tax", $_SESSION['customers_status']['customers_status_show_price_tax']);
$coo_print_product_info->set_("customers_status_discount", $_SESSION['customers_status']['customers_status_discount']);
$coo_print_product_info->set_("language", $_SESSION['language']);
$coo_print_product_info->set_("languages_id", $_SESSION['languages_id']);
if(isset($_GET['products_id']))
{
	$coo_print_product_info->set_("product_id", $_GET['products_id']);
}
$coo_print_product_info->set_("coo_xtc_price", $xtPrice);
$coo_print_product_info->set_("coo_main", $main);

$t_view_html = $coo_print_product_info->get_html();

echo $t_view_html;

xtc_db_close();