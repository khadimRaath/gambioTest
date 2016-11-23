<?php
/* --------------------------------------------------------------
  ot_cod_fee.php 2014-11-25 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
  (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1002 2005-07-10 16:11:37Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:

  Adapted for xtcommerce 2003/09/30 by Benax (axel.benkert@online-power.de)

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class ot_cod_fee_ORIGIN {

	var $title, $output;

	public function __construct() {
		global $xtPrice;
		$this->code = 'ot_cod_fee';
		$this->title = MODULE_ORDER_TOTAL_COD_FEE_TITLE;
		$this->description = MODULE_ORDER_TOTAL_COD_FEE_DESCRIPTION;
		$this->enabled = ((MODULE_ORDER_TOTAL_COD_FEE_STATUS == 'true') ? true : false);
		$this->sort_order = MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER;


		$this->output = array();
	}

	function process() {
		global $order, $xtPrice, $cod_cost, $cod_country, $shipping;

		if (MODULE_ORDER_TOTAL_COD_FEE_STATUS == 'true') {

			//Will become true, if cod can be processed.
			$cod_country = false;

			//check if payment method is cod. If yes, check if cod is possible.
			if ($_SESSION['payment'] == 'cod') {
				
				$cod_zones = array();

				if (strpos(MODULE_ORDER_TOTAL_COD_FEE_RULES, '|') !== false) {
					$t_values_array = explode('|', MODULE_ORDER_TOTAL_COD_FEE_RULES);
					$t_shipping_array = array();

					for ($i = 0; $i < count($t_values_array); $i++) {
						if ($i % 2 == 0) {
							$t_module_name = $t_values_array[$i];
						} else {
							$t_shipping_array[$t_module_name] = $t_values_array[$i];
						}
					}

					if (isset($t_shipping_array[strtok($_SESSION['shipping']['id'], '_')])) {
						$cod_zones = preg_split('/[:,]/', $t_shipping_array[strtok($_SESSION['shipping']['id'], '_')]);
					}
				}

				$cod_cost = 0;

				for ($i = 0; $i < count($cod_zones); $i++) {
					if ($cod_zones[$i] == $order->delivery['country']['iso_code_2']) {
						$cod_cost = $cod_zones[$i + 1];
						$cod_country = true;
						break;
					} elseif ($cod_zones[$i] == '00') {
						$cod_cost = $cod_zones[$i + 1];
						$cod_country = true;
						break;
					} 
					$i++;
				}
			} else {
				//COD selected, but no shipping module which offers COD
			}

			$cod_cost = $xtPrice->xtcCalculateCurr($cod_cost);

			if ($cod_country) {

				$cod_tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				$cod_tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
					$order->info['tax'] += xtc_add_tax($cod_cost, $cod_tax) - $cod_cost;
					$order->info['tax_groups'][TAX_ADD_TAX . "$cod_tax_description"] += xtc_add_tax($cod_cost, $cod_tax) - $cod_cost;
					$order->info['total'] += $cod_cost + (xtc_add_tax($cod_cost, $cod_tax) - $cod_cost);
					$cod_cost_value = xtc_add_tax($cod_cost, $cod_tax);
					$cod_cost = $xtPrice->xtcFormat($cod_cost_value, true);
				}
				if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
					$order->info['tax'] += xtc_add_tax($cod_cost, $cod_tax) - $cod_cost;
					$order->info['tax_groups'][TAX_NO_TAX . "$cod_tax_description"] += xtc_add_tax($cod_cost, $cod_tax) - $cod_cost;
					$cod_cost_value = $cod_cost;
					$cod_cost = $xtPrice->xtcFormat($cod_cost, true);
					$order->info['subtotal'] += $cod_cost_value;
					$order->info['total'] += $cod_cost_value;
				}
				if (!$cod_cost_value) {
					$cod_cost_value = $cod_cost;
					$cod_cost = $xtPrice->xtcFormat($cod_cost, true);
					$order->info['total'] += $cod_cost_value;
				}
				$this->output[] = array('title' => $this->title . ':',
					'text' => $cod_cost,
					'value' => $cod_cost_value);
			}
		}
	}

	function check() {
		if (!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COD_FEE_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function keys() {

		return array(
			'MODULE_ORDER_TOTAL_COD_FEE_STATUS',
			'MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER',
			'MODULE_ORDER_TOTAL_COD_FEE_RULES',
			'MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS'
		);
	}

	function install() {

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_STATUS', 'true', '6', '0', 'gm_cfg_select_option(array(\'true\', \'false\'), ', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_SORT_ORDER', '35', '6', '0', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_RULES', 'ap|AT:3.63,00:9.99|chp|CH:4.00,00:9.99|chronopost|FR:4.00,00:9.99|dhl|AT:3.00,DE:3.58,00:9.99|dp|DE:4.00,00:9.99|dpd|DE:4.00,00:9.99|fedexeu|DE:4.00,00:9.99|flat|AT:3.00,DE:3.58,00:9.99|free|AT:3.00,DE:3.58,00:9.99|freeamount|AT:3.00,DE:3.58,00:9.99|gambioultra|DE:4.00,00:9.99|item|AT:3.00,DE:3.58,00:9.99|selfpickup|DE:4.00,00:9.99|table|AT:3.00,DE:3.58,00:9.99|ups|AT:3.00,DE:3.58,00:9.99|upse|AT:3.00,DE:3.58,00:9.99|zones|CA:4.50,US:3.00,00:9.99|zonese|DE:4.00,00:9.99', '6', '0', '', 'cfg_cod_fee_form(', now())");

		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_COD_FEE_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

}

MainFactory::load_origin_class('ot_cod_fee');