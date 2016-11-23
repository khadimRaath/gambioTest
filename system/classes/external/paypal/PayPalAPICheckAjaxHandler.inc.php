<?php
/* --------------------------------------------------------------
   PayPalAPICheckAjaxHandler.inc.php 2012-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class PayPalAPICheckAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$this->v_output_buffer = '';

		if(isset($this->v_data_array['POST']['module']) 
			&& ($this->v_data_array['POST']['module'] == 'paypal' || $this->v_data_array['POST']['module'] == 'paypalexpress'))
		{
			require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'paypal_checkout.php');

			$coo_paypal = new paypal_checkout();
			$this->v_output_buffer = $coo_paypal->check_api();
		}

		return true;
	}
}
?>