<?php
/* --------------------------------------------------------------
	PaymentInstructionCheckoutSuccessExtender.inc.php 2015-11-03
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PaymentInstructionCheckoutSuccessExtender extends PaymentInstructionCheckoutSuccessExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$paymentInstructionContentView = MainFactory::create('PaymentInstructionContentView');
		$paymentInstructionContentView->set_('order_id', $this->v_data_array['orders_id']);
		$html = $paymentInstructionContentView->get_html();
		$this->html_output_array['PAYMENT_INSTRUCTION'] = $html;
	}
}