<?php
/* --------------------------------------------------------------
   ot_billsafe3.php 2014-07-15 gambio
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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


class ot_billsafe3_ORIGIN {
    var $title, $output;
    var $bs;

	public function __construct() {
		$this->bs = new GMBillSafe();
		$this->code = 'ot_billsafe3';
		$this->short_code = 'billsafe3';
		$this->title = MODULE_ORDER_TOTAL_BILLSAFE3_TITLE;
		$this->description = MODULE_ORDER_TOTAL_BILLSAFE3_DESCRIPTION;
		if(!$this->bs->paymentModuleIsConfigured()) {
			$this->description .= MODULE_ORDER_TOTAL_BILLSAFE3_DESCRIPTION_PAYMENT_MISSING;
			xtc_db_query("UPDATE configuration SET configuration_value = 'False' WHERE configuration_key LIKE 'MODULE_ORDER_TOTAL_BILLSAFE3_STATUS'");
			$this->enabled = false;
		}
		else {
			$this->enabled = ((strtolower(MODULE_ORDER_TOTAL_BILLSAFE3_STATUS) == 'true') ? true : false);
		}
		$this->sort_order = MODULE_ORDER_TOTAL_BILLSAFE3_SORT_ORDER;
		$this->output = array();
	}

	function process() {
		// N.B.: this can only be used in invoice mode, not in installment mode!
		if($GLOBALS['order']->info['payment_class'] != 'billsafe_3_invoice') {
			return;
		}
		$info_txt = '';
		$charges = preg_split('/\s?,\s?/', MODULE_ORDER_TOTAL_BILLSAFE3_CHARGE);
		$charge_limits = array();
		foreach($charges as $charge_entry) {
			list($maxAmount, $charge_amount) = preg_split('/\s?:\s?/', $charge_entry);
			$charge_limits[$maxAmount] = $charge_amount;
		}
		krsort($charge_limits);
		foreach($charge_limits as $limit => $camount) {
			if($GLOBALS['order']->info['total'] <= $limit) {
				$charge = $camount;
			}
		}

		if(substr($charge, -1, 1) == '%') {
			$info_txt = " ($charge)";
			$charge_rate = ((double)$charge) / 100;
			$charge = $GLOBALS['order']->info['total'] * $charge_rate;
		}

		$tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_BILLSAFE3_TAX_CLASS, $GLOBALS['order']->delivery['country']['id'], $GLOBALS['order']->delivery['zone_id']);
		$tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_BILLSAFE3_TAX_CLASS, $GLOBALS['order']->delivery['country']['id'], $GLOBALS['order']->delivery['zone_id']);
		$charge_tax = $GLOBALS['xtPrice']->xtcGetTax($charge, $tax);

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
			$GLOBALS['order']->info['tax'] += $charge_tax;
			$GLOBALS['order']->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += $charge_tax;
			$GLOBALS['order']->info['total'] += $charge;
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$charge = $charge - $charge_tax;
			$GLOBALS['order']->info['tax'] += $charge_tax;
			$GLOBALS['order']->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += $charge_tax;
			$GLOBALS['order']->info['subtotal'] += $charge;
			$GLOBALS['order']->info['total'] += $charge;
        }

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] != 1) {
			$charge = $charge - $charge_tax;
			$GLOBALS['order']->info['subtotal'] += $charge;
			$GLOBALS['order']->info['total'] += $charge;
        }

		$this->output[] = array(
			'title' => $this->title .$info_txt. ':',
			'text' => $GLOBALS['xtPrice']->xtcFormat($charge, true),
			'value' => $charge
		);
	}

	function check() {
		if (!isset ($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_ORDER_TOTAL_".  strtoupper($this->short_code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		if(!$this->bs->paymentModuleIsConfigured()) {
			return;
		}
		$config = $this->_configuration(true);
		$sort_order = 0;
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_ORDER_TOTAL_".strtoupper($this->short_code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
		}
	}

	function remove() {
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	function keys() {
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		foreach($ckeys as $k) {
			$keys[] = 'MODULE_ORDER_TOTAL_'.strtoupper($this->short_code).'_'.$k;
		}
		return $keys;
	}

	function _configuration($install = false) {
		$charge = '';
		$status = 'False';
		if($install == true) {
			require_once DIR_FS_CATALOG.'gm/classes/GMBillSafe.php';
			$charges = array();
			try
			{
				$bs = new GMBillSafe();
				$agreed_charge = $bs->getAgreedHandlingCharges();
				foreach($agreed_charge['agreedCharge'] as $ac) {
					$charges[] = $ac['maxAmount'].':'.$ac['charge'];
				}
			}
			catch(BillSafeException $e)
			{
				// connection failed; just ignore it and leave configuration of charges empty for the shop owner to fill in manually
			}
			$charge = implode(',', $charges);
			$status = empty($charge) ? 'False' : 'True';
		}
		$config = array(
			'STATUS' => array(
				'configuration_value' => $status,
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'CHARGE' => array(
				'configuration_value' => $charge,
			),
			'TAX_CLASS' => array(
				'configuration_value' => '0',
				'use_function' => 'xtc_get_tax_class_title',
				'set_function' => 'xtc_cfg_pull_down_tax_classes(',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '0',
			),
		);

		return $config;
	}

}

MainFactory::load_origin_class('ot_billsafe3');