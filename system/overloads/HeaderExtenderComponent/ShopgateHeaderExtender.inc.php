<?php
/* --------------------------------------------------------------
  ShopgateHeaderExtender.inc.php 2014-07-10 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class ShopgateHeaderExtender extends ShopgateHeaderExtender_parent
{
	function proceed()
	{
		if($this->v_data_array['GET']['page'] == 'ProductInfo' && defined('MODULE_PAYMENT_INSTALLED') && strpos(MODULE_PAYMENT_INSTALLED, 'shopgate.php') !== false)
		{
			/******** SHOPGATE **********/
			include_once(DIR_FS_CATALOG . '/shopgate/gambiogx/includes/header.php');
			/******** SHOPGATE **********/
		}

		parent::proceed();
	}
}