<?php
/* --------------------------------------------------------------
	TrustedShopsExcellenceAjaxHandler.inc.php 2015-02-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class TrustedShopsExcellenceAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	public function proceed()
	{
		if(isset($this->v_data_array['POST']['remove_tsbp'])) {
			unset($_SESSION['ts_excellence']);
			$this->v_output_buffer .= 'excellence removed';
		}
		elseif(isset($this->v_data_array['POST']['add_tsbp'])) {
			$this->v_output_buffer .= 'adding excellence';
			$order = new order();
			$service = MainFactory::create_object('GMTSService');
			$tsid = $service->findExcellenceID($_SESSION['language_code']);
			if($tsid !== false)
			{
				// gather data
				$trusted_amount = round($order->info['total'], 2);
				$product = $service->findProtectionProduct($tsid, $trusted_amount, $order->info['currency']);
				$amount = (double)$this->v_data_array['POST']['amount'];
				$_SESSION['ts_excellence'] = array(
					'application_number' => $application_number,
					'protection_grossfee' => $product['grossfee'],
					'cart_total' => $amount,
					'protectedamount' => $product['protectedamount'],
					'tsproductid' => $product['tsproductid'],
					'from_protection' => true,
				);
			}
			else
			{
				$this->v_output_buffer .= ' - tsid not found';
			}
		}
		return true;
	}
}
