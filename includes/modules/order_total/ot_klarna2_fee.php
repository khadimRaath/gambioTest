<?php
/* --------------------------------------------------------------
	ot_klarna2_fee.php 2014-07-15 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class ot_klarna2_fee_ORIGIN {
	var $title, $output;

	public function __construct() {
		$this->code = 'ot_klarna2_fee';
		$this->title = MODULE_ORDER_TOTAL_KLARNA2_FEE_TITLE;
		$this->description = MODULE_ORDER_TOTAL_KLARNA2_FEE_DESCRIPTION;
		$this->enabled = ((strtolower(MODULE_ORDER_TOTAL_KLARNA2_FEE_STATUS) == 'true') ? true : false);
		$this->sort_order = MODULE_ORDER_TOTAL_KLARNA2_FEE_SORT_ORDER;

		$this->output = array();
	}


	function process() {
		require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');
		$order = $GLOBALS['order'];
		$xtPrice = $GLOBALS['xtPrice'];

		if($_SESSION['payment'] != 'klarna2_invoice') {
			return;
		}

		$tax_id = MODULE_ORDER_TOTAL_KLARNA2_FEE_TAX_CLASS;
		$tax = xtc_get_tax_rate($tax_id, $order->delivery['country']['id'], $order->delivery['zone_id']);
		$tax_description = xtc_get_tax_description($tax_id, $order->delivery['country']['id'], $order->delivery['zone_id']);

		$klarna = new GMKlarna();
		$fee = $klarna->getInvoiceFee(false, null, $order->info['total']);
		$invoice_fee = 0;

		if($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
			$order->info['tax'] += xtc_calculate_tax($fee, $tax);
			$order->info['tax_groups'][TAX_ADD_TAX . $tax_description] += xtc_calculate_tax($fee, $tax);
			$order->info['total'] += $fee + xtc_calculate_tax($fee, $tax);
			$invoice_fee = xtc_add_tax($fee, $tax);
		}
		else {
			if($_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
				$order->info['tax'] += xtc_calculate_tax($fee, $tax);
				$order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += xtc_calculate_tax($fee, $tax);
			}
			else {
			}
			$order->info['subtotal'] += $fee;
			$order->info['total'] += $fee;
			$invoice_fee = $fee;
		}

		$this->output[] = array(
			'title' => $klarna->get_text('invoice_fee_title') . ':',
			'text' => $xtPrice->xtcFormat($invoice_fee, true),
			'value' => $invoice_fee,
		);
	}



	public function check() {
		$code = substr(strtoupper($this->code), 3);
		if(!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_ORDER_TOTAL_".  $code ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	public function install() {
		$config = $this->_configuration();
		$sort_order = 0;
		$code = substr(strtoupper($this->code), 3);
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_ORDER_TOTAL_".$code."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
		}
	}

	public function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	public function keys() {
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		$code = substr(strtoupper($this->code), 3);
		foreach($ckeys as $k) {
			$keys[] = 'MODULE_ORDER_TOTAL_'.$code.'_'.$k;
		}
		return $keys;
	}

	protected function _configuration() {
		$config = array(
			'STATUS' => array(
				'configuration_value' => 'True',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '45',
			),
			'TAX_CLASS' => array(
				'configuration_value' => '0',
				'use_function' => 'xtc_get_tax_class_title',
				'set_function' => 'xtc_cfg_pull_down_tax_classes(',
			),
		);

		return $config;
	}
}
MainFactory::load_origin_class('ot_klarna2_fee');