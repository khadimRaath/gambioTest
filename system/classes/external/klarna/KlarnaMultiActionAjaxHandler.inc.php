<?php
/* --------------------------------------------------------------
	KlarnaMultiActionAjaxHandler.inc.php 2015-01-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class KlarnaMultiActionAjaxHandler extends AjaxHandler
{
	public function get_permission_status($p_customers_id = NULL)
	{
		$access_allowed = false;
		if($_SESSION['customers_status']['customers_status_id'] == 0)
		{
			$access_allowed = true;
		}
		return $access_allowed;
	}

	public function proceed()
	{
		require_once DIR_FS_ADMIN.'includes/classes/order.php';
		$klarna = MainFactory::create_object('GMKlarna');

		$orders_params = base64_decode($this->v_data_array['POST']['orders_params']);
		$redirect_url = xtc_href_link('orders.php?'.$orders_params);

		$orders_ids = $this->v_data_array['POST']['checked_orders_ids'];
		if(isset($this->v_data_array['POST']['klarna_activate_reservation']))
		{
			foreach($orders_ids as $orders_id)
			{
				$order = new order($orders_id);
				if(strpos($order->info['payment_method'], 'klarna2') !== false)
				{
					$orders_klarna_data = $klarna->getOrdersKlarnaData($orders_id);
					if($orders_klarna_data === false || empty($orders_klarna_data['inv_rno']))
					{
						$result = $klarna->activateReservation($orders_id);
						if($result == true)
						{
							$_SESSION['messages']['orders.php'][] = $orders_id.' - '.$klarna->get_text('activate_reservation_result');
						}
						else
						{
							$_SESSION['messages']['orders.php'][] = $orders_id.' - '.$klarna->get_text('activate_reservation_fail');
						}
					}
					else
					{
						$_SESSION['messages']['orders.php'][] = $orders_id.' - '.$klarna->get_text('already_activated');
					}
				}
				else
				{
					$_SESSION['messages']['orders.php'][] = $orders_id.' - '.$klarna->get_text('is_not_a_klarna_order');
				}
			}
		}

		xtc_redirect($redirect_url);
	}
}