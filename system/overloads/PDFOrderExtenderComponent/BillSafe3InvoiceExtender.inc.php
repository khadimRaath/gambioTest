<?php
/* --------------------------------------------------------------
   BillSafe3InvoiceExtender.php 2015-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class BillSafe3InvoiceExtender extends BillSafe3InvoiceExtender_parent {
	function extendOrderInfo($order_info) {
		$order_info = parent::extendOrderInfo($order_info);
		if(strpos($this->v_data_array['order']->info['payment_method'], 'billsafe_3') === false) {
			return $order_info;
		}
		$coo_bs = MainFactory::create_object('GMBillSafe', array());
		$bspi = $coo_bs->getPaymentInfo((int)$this->v_data_array['order_id'], true);
		try {
			$coo_bs->setInvoiceNumber((int)$this->v_data_array['order_id'], $this->v_data_array['order_check']['gm_orders_code']);
		}
		catch(BillSafeException $bse) {
			// this generally happens if the invoice number has been set before; we can safely ignore this
		}
		$order_info['BILLSAFE'] = array(
			0 => 'BillSAFE',
			1 => html_entity_decode($bspi, ENT_COMPAT, 'utf-8'),
		);
		return $order_info;
	}

	function extendPdfFooter($footer)
	{
		$footer = parent::extendPdfFooter($footer);
		if(strpos($this->v_data_array['order']->info['payment_method'], 'billsafe_3') === false) {
			return $footer;
		}
		if(defined('MODULE_PAYMENT_BILLSAFE_3_INVOICE_HIDEPDFCOLUMN') && is_numeric(constant('MODULE_PAYMENT_BILLSAFE_3_INVOICE_HIDEPDFCOLUMN')))
		{
			$footer[MODULE_PAYMENT_BILLSAFE_3_INVOICE_HIDEPDFCOLUMN - 1] = '';
		}
		return $footer;
	}
}
