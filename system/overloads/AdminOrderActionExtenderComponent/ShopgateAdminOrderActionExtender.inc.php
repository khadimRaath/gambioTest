<?php
/* --------------------------------------------------------------
  ShopgateAdminOrderActionExtender.inc.php 2014-07-18 tt
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class ShopgateAdminOrderActionExtender extends ShopgateAdminOrderActionExtender_parent
{
	function proceed()
	{
		parent::proceed();

		/******** SHOPGATE **********/
		if(defined('MODULE_PAYMENT_INSTALLED') && strpos(MODULE_PAYMENT_INSTALLED, 'shopgate.php') !== false)
		{
			$t_oID = xtc_db_prepare_input($this->v_data_array['GET']['oID']);
			$t_status = xtc_db_prepare_input($this->v_data_array['POST']['status']);
			include_once(DIR_FS_CATALOG . '/shopgate/gambiogx/admin/orders.php');

			switch($this->v_data_array['GET']['action'])
			{
				case 'gm_multi_status':
					if($this->v_data_array['order_updated'])
					{
						setShopgateOrderlistStatus($this->v_data_array['POST']['gm_multi_status'], $t_status);
					}

					break;

				case 'update_order':
					if($this->v_data_array['order_updated'])
					{
						setShopgateOrderStatus($t_oID, $t_status);
					}
					break;

				case 'deleteconfirm':

					xtc_db_query("DELETE FROM " . TABLE_SHOPGATE_ORDERS . " WHERE orders_id = '" . (int)$t_oID . "'");

					break;
			}
		}
		/******** SHOPGATE **********/
	}
}