<?php
/* --------------------------------------------------------------
	PaymentInstructionAccountHistoryInfoContentView.inc.php 2015-11-03
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PaymentInstructionAccountHistoryInfoContentView extends PaymentInstructionAccountHistoryInfoContentView_parent
{
	protected function _assignPaymentData()
	{
		parent::_assignPaymentData();
		if(!empty($this->content_array['PAYMENT_METHOD']))
		{
			$paymentInstructionContentView = MainFactory::create('PaymentInstructionContentView');
			$paymentInstructionContentView->set_('order_id', $this->orderId);
			$html = $paymentInstructionContentView->get_html();
			$this->content_array['PAYMENT_METHOD'] .= $html;
		}
	}
}