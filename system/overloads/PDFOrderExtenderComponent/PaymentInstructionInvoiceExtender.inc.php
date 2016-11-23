<?php
/* --------------------------------------------------------------
	PaymentInstructionInvoiceExtender.inc.php 2016-07-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PaymentInstructionInvoiceExtender extends PaymentInstructionInvoiceExtender_parent
{
	protected $replaceFooterColumn;
	protected $paymentInstructionText;

	public function __construct()
	{
		if(is_callable('parent::__construct'))
		{
			parent::__construct();
		}
		$this->paymentInstructionText = MainFactory::create('LanguageTextManager', 'checkout_payment_instruction');
		$this->replaceFooterColumn = 3;
	}


	public function extendOrderInfo($order_info)
	{
		$order_info = parent::extendOrderInfo($order_info);
		$paymentInstruction = $this->_getPaymentInstruction($this->v_data_array['order_id']);
		if($paymentInstruction !== null)
		{
			$paymentInstructionText = $this->_makePaymentInstructionText($paymentInstruction);
			$order_info['PAYMENT_INSTRUCTION'] = array(
				0 => $this->paymentInstructionText->get_text('payment_instruction'),
				1 => $paymentInstructionText,
			);
		}
		return $order_info;
	}

	public function extendPdfFooter($footer)
	{
		$footer = parent::extendPdfFooter($footer);
		$paymentInstruction = $this->_getPaymentInstruction($this->v_data_array['order_id']);
		if($paymentInstruction !== null)
		{
			$footer[$this->replaceFooterColumn] = '';
		}
		return $footer;
	}

	protected function _getPaymentInstruction($orders_id)
	{
		$paymentInstruction = null;
		$query = 'SELECT * FROM `orders_payment_instruction` WHERE `orders_id` = \''.(int)$orders_id.'\'';
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			$paymentInstruction = $row;
		}
		if($paymentInstruction !== null && $this->_getPaymentMethod($orders_id) === 'paypal3' && $paymentInstruction['due_date'] !== '1000-01-01')
		{
			$paypalText = MainFactory::create('PayPalText');
			$paymentInstruction['additional_note'] = COMPANY_NAME . ' ' . $paypalText->get_text('payment_instruction_additional_note');
		}
		return $paymentInstruction;
	}

	protected function _getPaymentMethod($orders_id)
	{
		$order = new order($orders_id);
		$payment_method = $order->info['payment_method'];
		return $payment_method;
	}

	protected function _makePaymentInstructionText($paymentInstruction)
	{
		$textLines = array(
			$this->paymentInstructionText->get_text('payment_note'),
			$this->paymentInstructionText->get_text('amount'). ': '. sprintf("%.2f %s", $paymentInstruction['value'], $paymentInstruction['currency']),
			$this->paymentInstructionText->get_text('iban'). ': '. $paymentInstruction['iban'],
			$this->paymentInstructionText->get_text('bic'). ': '. $paymentInstruction['bic'],
			$this->paymentInstructionText->get_text('account_holder'). ': '. $paymentInstruction['account_holder'],
			$this->paymentInstructionText->get_text('bank_name'). ': '. $paymentInstruction['bank_name'],
			$this->paymentInstructionText->get_text('reference'). ': '. $paymentInstruction['reference'],
		);

		if($paymentInstruction['due_date'] !== '0000-00-00' && $paymentInstruction['due_date'] !== '1000-01-01')
		{
			$textLines[] = $this->paymentInstructionText->get_text('due_date') . ': '
			               . xtc_date_short($paymentInstruction['due_date'] . ' 00:00:00');
		}

		if(!empty($paymentInstruction['additional_note']))
		{
			$textLines[] = $paymentInstruction['additional_note'];
		}
		$text = implode("\n", $textLines);
		return $text;
	}
}