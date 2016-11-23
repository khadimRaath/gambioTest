<?php
/* --------------------------------------------------------------
   PayPalNotificationAjaxHandler.inc.php 2012-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayPalNotificationAjaxHandler extends AjaxHandler
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
		define('TABLE_PAYPAL','paypal');
		define('FILENAME_PAYPAL','paypal.php');

		require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'paypal_checkout.php');
		require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.paypal.php');

		$coo_paypal = new paypal_admin();
		$this->v_output_buffer = $coo_paypal->admin_notification($this->v_data_array['GET']['oID']);

		return true;
	}
}
?>