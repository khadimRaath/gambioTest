<?php
/* --------------------------------------------------------------
	PaymentInstructionSendOrderContentView.inc.php 2016-07-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

if(!function_exists('xtc_date_short'))
{
	require_once DIR_FS_INC . 'xtc_date_short.inc.php';
}

class PaymentInstructionSendOrderContentView extends PaymentInstructionSendOrderContentView_parent
{
	public function set_payment_info_html($html)
	{
		$html = (string)$html;
		$paymentInstruction = $this->_getPaymentInstruction($this->order_id);
		if($paymentInstruction !== null)
		{
			$html .= nl2br($this->_makePaymentInstructionText($paymentInstruction));
		}
		$this->payment_info_html = $html;
	}

	public function set_payment_info_text($text)
	{
		$text = (string)$text;
		$paymentInstruction = $this->_getPaymentInstruction($this->order_id);
		if($paymentInstruction !== null)
		{
			$text .= $this->_makePaymentInstructionText($paymentInstruction);
		}
		$this->payment_info_text = $text;
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
		$paymentInstructionText = MainFactory::create('LanguageTextManager', 'checkout_payment_instruction');
		$textLines = array(
			$paymentInstructionText->get_text('payment_note'),
			$paymentInstructionText->get_text('amount'). ': '. sprintf("%.2f %s", $paymentInstruction['value'], $paymentInstruction['currency']),
			$paymentInstructionText->get_text('iban'). ': '. $paymentInstruction['iban'],
			$paymentInstructionText->get_text('bic'). ': '. $paymentInstruction['bic'],
			$paymentInstructionText->get_text('account_holder'). ': '. $paymentInstruction['account_holder'],
			$paymentInstructionText->get_text('bank_name'). ': '. $paymentInstruction['bank_name'],
			$paymentInstructionText->get_text('reference'). ': '. $paymentInstruction['reference'],
		);

		if($paymentInstruction['due_date'] !== '0000-00-00' && $paymentInstruction['due_date'] !== '1000-01-01')
		{
			$textLines[] = $paymentInstructionText->get_text('due_date') . ': '
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