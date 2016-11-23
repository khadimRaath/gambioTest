<?php
/* --------------------------------------------------------------
  ShopgateApplicationTopExtender.inc.php 2014-07-10 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class ShopgateApplicationTopExtender extends ShopgateApplicationTopExtender_parent
{
	function proceed()
	{
		parent::proceed();

		/******** SHOPGATE **********/
		define("TABLE_SHOPGATE_ORDERS", "orders_shopgate_order");
		define('FILENAME_SHOPGATE', 'shopgate.php');
		include_once(DIR_FS_CATALOG . '/shopgate/gambiogx/includes/application_top.php');
		/******** SHOPGATE **********/
	}
}