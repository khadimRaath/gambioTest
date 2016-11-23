<?php
/* --------------------------------------------------------------
   MegaFlyoverAjaxHandler.inc.php 2014-10-29 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MegaFlyoverAjaxHandler
 */
class MegaFlyoverAjaxHandler extends AjaxHandler
{
	/**
	 * @param null|int $p_customers_id
	 *
	 * @return bool
	 */
	public function get_permission_status($p_customers_id = null)
	{
		return true;
	}


	/**
	 * @return bool
	 */
	public function proceed()
	{
		if(isset($this->v_data_array['GET']['mf_products_id']))
		{
			/* @var MegaFlyoverContentView $megaFlyoverView */
			$megaFlyoverView = MainFactory::create_object('MegaFlyoverContentView');
			$megaFlyoverView->setProductId($this->v_data_array['GET']['mf_products_id']);
			$megaFlyoverView->setMain(new main());
			$megaFlyoverView->setShowPrice(($_SESSION['customers_status']['customers_status_show_price']) ? true : false);
			$megaFlyoverView->setXtcPrice(new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']));
			$this->v_output_buffer = $megaFlyoverView->get_html();
		}

		return true;
	}
}