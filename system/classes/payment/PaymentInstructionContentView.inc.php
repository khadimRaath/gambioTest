<?php
/* --------------------------------------------------------------
	PaymentInstructionContentView.inc.php 2016-07-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once DIR_FS_INC . 'xtc_date_short.inc.php';

class PaymentInstructionContentView extends ContentView
{
	protected $order_id;
	protected $payment_instruction;

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/payment_instruction.html');
		$this->set_flat_assigns(true);
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{
		$this->payment_instruction = $this->_getPaymentInstruction($this->order_id);
		if(is_array($this->payment_instruction))
		{
			foreach($this->payment_instruction as $pi_key => $pi_value)
			{
				if($pi_key === 'due_date')
				{
					if($pi_value === '0000-00-00' || $pi_value === '1000-01-01')
					{
						continue;
					}
					
					$pi_value = xtc_date_short($pi_value . ' 00:00:00');
				}
				
				$this->set_content_data($pi_key, $pi_value);
			}
		}
		else
		{
			$this->build_html = false;
		}
	}


	/**
	 * @param int $p_orderId
	 */
	public function set_order_id($p_orderId)
	{
		$this->order_id = (int)$p_orderId;
	}

	/**
	 * @return int
	 */
	public function get_order_id()
	{
		return $this->order_id;
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
		return $paymentInstruction;
	}

	protected function _getPaymentMethod($orders_id)
	{
		$order = new order($orders_id);
		$payment_method = $order->info['payment_method'];
		return $payment_method;
	}
}

