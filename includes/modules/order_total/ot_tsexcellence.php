<?php
/* --------------------------------------------------------------
   ot_tsexcellence.php 2015-07-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class ot_tsexcellence_ORIGIN {
	var $title, $output;

	public function __construct() {
		global $xtPrice;
		$this->code = 'ot_tsexcellence';
		$this->title = MODULE_ORDER_TOTAL_TSEXCELLENCE_TITLE;
		$this->description = MODULE_ORDER_TOTAL_TSEXCELLENCE_DESCRIPTION;
		$this->enabled = ((MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS == 'true') ? true : false);
		$this->sort_order = MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER;
		$this->output = array();
	}

	function process() {
		global $order, $xtPrice, $shipping;

		if(isset($_SESSION['ts_excellence']) && strpos($_SERVER['REQUEST_URI'], 'checkout_confirmation') !== false) {
			if(!isset($_SESSION['ts_excellence']['from_protection'])) {
				// if checkout_confirmation is revisited after activating buyer protection, previous selection of buyer protection becomes invalid
				unset($_SESSION['ts_excellence']);
			}
		}

		if(isset($_SESSION['ts_excellence'])) {
			$service = new GMTSService();
			$tsid = $service->findExcellenceID($_SESSION['language_code']);
			$trusted_amount = round($order->info['total'], 2);
			$product = $service->findProtectionProduct($tsid, $trusted_amount, $order->info['currency']);
			if($product['tsproductid'] != $_SESSION['ts_excellence']['tsproductid']) {
				unset($_SESSION['ts_excellence']);
			}
		}

		if(isset($_SESSION['ts_excellence'])) {
			$tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
			$tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

			$cost_value = $_SESSION['ts_excellence']['protection_grossfee'];
			$tax_value = $cost_value - ($cost_value / (($tax + 100) / 100));
			$order->info['tax'] += $tax_value;
			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
				$order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += $tax_value;
				$order->info['total'] += $cost_value;
			}

			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
				$order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += $tax_value;
				$order->info['total'] += $cost_value - $tax_value;
				$order->info['subtotal'] += $cost_value - $tax_value;
			}

			$title_upto = round($_SESSION['ts_excellence']['protectedamount']) . ' '. $order->info['currency'];
			$cost = $xtPrice->xtcFormat($cost_value, true);
			$this->output[] = array('title' => $this->title . ' ('. MODULE_ORDER_TOTAL_TSEXCELLENCE_UPTO .' '. $title_upto .'):',
									'text' => $cost,
									'value' => $cost_value);
		}
	}

	function check() {
		if(!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function keys() {
		return array(
			'MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS',
			'MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER',
			'MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS',
		);
	}

    function install() {
	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS', 'true', '6', '0', 'gm_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER', '35', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}
}

MainFactory::load_origin_class('ot_tsexcellence');