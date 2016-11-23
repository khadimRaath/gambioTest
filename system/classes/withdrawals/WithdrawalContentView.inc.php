<?php
/* --------------------------------------------------------------
   WithdrawalContentView.inc.php 2014-11-08 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMJSON.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderElements.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderSurfaces.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderSurfacesManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderManager.php');


class WithdrawalContentView extends ContentView
{
	//No flat assigns for gambio template
	public function init_smarty()
	{
		parent::init_smarty();
		$this->set_flat_assigns(false);
	}

    public function prepare_data()
    {
		if(isset($this->content_array['order']))
		{
			$t_date_purchased = $this->content_array['order']->info['date_purchased'];
			$t_date_purchased = date(DATE_FORMAT . ' H:i:s', strtotime($t_date_purchased));
			$this->content_array['order']->info['date_purchased'] = $t_date_purchased;

			$coo_gm_gprint_content_manager = new GMGPrintContentManager();

			for($i = 0; $i < count($this->content_array['order']->products); $i++)
			{

				$coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_orders_products_content($this->content_array['order']->products[$i]['orders_products_id'], true);

				for($m = 0; $m < count($coo_gm_gprint_order_data); $m++)
				{
					$this->content_array['order']->products[$i]['attributes'][] = array('option' => $coo_gm_gprint_order_data[$m]['NAME'],
																							 'value'  => $coo_gm_gprint_order_data[$m]['VALUE']
					);
				}

				$this->content_array['order']->products[$i]['qty'] = (double)$this->content_array['order']->products[$i]['qty'];
			}
		}
	}
}