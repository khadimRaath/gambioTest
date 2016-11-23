<?php
/* --------------------------------------------------------------
  order_history.php 2014-08-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(order_history.php,v 1.4 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (order_history.php,v 1.9 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: order_history.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_order_history = MainFactory::create_object('OrderHistoryBoxContentView');
if(isset($_SESSION['customer_id']))
{
	$coo_order_history->set_('customer_id', $_SESSION['customer_id']);
}
$coo_order_history->set_('language_id', $_SESSION['languages_id']);
$t_box_html = $coo_order_history->get_html();

$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('order_history');
$this->set_content_data($gm_box_pos, $t_box_html);