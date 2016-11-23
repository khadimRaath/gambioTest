<?php
/* --------------------------------------------------------------
  application_bottom.php 2014-02-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(application_bottom.php,v 1.14 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (application_bottom.php,v 1.6 2003/08/13); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: application_bottom.php 1239 2005-09-24 20:09:56Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

$coo_bottom_control = MainFactory::create_object('BottomContentControl');
$coo_bottom_control->set_data('GET', $_GET);
$coo_bottom_control->set_data('POST', $_POST);
$coo_bottom_control->set_('c_path', $GLOBALS['cPath']);
$coo_bottom_control->set_('coo_product', $GLOBALS['product']);

$coo_bottom_control->proceed();

$t_redirect_url = $coo_bottom_control->get_redirect_url();
if(empty($t_redirect_url) == false)
{
	xtc_redirect($t_redirect_url);
}

echo $coo_bottom_control->get_response();

xtc_db_close();
