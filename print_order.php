<?php
/* --------------------------------------------------------------
   print_order.php 2016-02-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (print_order.php,v 1.5 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: print_order.php 1185 2005-08-26 15:16:31Z mz $)
   
   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

$coo_print_order = MainFactory::create_object('PrintOrderContentView');
if(isset($_GET['oID']))
{
	$coo_print_order->set_('order_id', $_GET['oID']);
}
$coo_print_order->set_('customer_id', (int)$_SESSION['customer_id']);
$coo_print_order->set_('language', $_SESSION['language']);
$t_view_html = $coo_print_order->get_html();

echo $t_view_html;

xtc_db_close();