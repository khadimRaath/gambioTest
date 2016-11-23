<?php
/* --------------------------------------------------------------
   billsafe_3.php 2015-01-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once dirname(__FILE__).'/billsafe3/billsafe_3_base.php';

class billsafe_3_invoice_ORIGIN extends billsafe_3_base {

	public function __construct() {
		$this->code = 'billsafe_3_invoice';
		parent::__construct();
		$this->title = MODULE_PAYMENT_BILLSAFE_3_INVOICE_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_BILLSAFE_3_INVOICE_TEXT_DESCRIPTION.'<br><br>Version: '.GMBillSafe::MODULE_VERSION;
		$this->description .= '<br><br>'.MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION_LINK.'<br><br>'.$this->_checkRequirements();
		$this->sort_order = MODULE_PAYMENT_BILLSAFE_3_INVOICE_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_BILLSAFE_3_INVOICE_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_BILLSAFE_3_INVOICE_TEXT_INFO;
		if ((int) MODULE_PAYMENT_BILLSAFE_3_INVOICE_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_BILLSAFE_3_INVOICE_ORDER_STATUS_ID;
		}
		$this->tmpStatus = MODULE_PAYMENT_BILLSAFE_3_INVOICE_TMPORDER_STATUS_ID;

		if(is_object($order)) {
			$this->update_status();
		}


		$logo_code = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_IMAGE_CODE');
		if(!empty($logo_code)) {
			$this->logo = '<img src="https://images.billsafe.de/image/image/id/'.$logo_code.'" style="float: right;">';
		}

		if(defined('MODULE_PAYMENT_'.strtoupper($this->code).'_STATUS'))
		{
			// make sure all configuration keys are properly installed
			$this->install(true);
		}
	}

	function update_status() {
		global $order;

		if (($this->enabled == true) && ((int) MODULE_PAYMENT_BILLSAFE_3_INVOICE_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_BILLSAFE_3_INVOICE_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	function _configuration() {
		$config = parent::_configuration();
		$special_config = array(
			'MINORDER' => array(
				'configuration_value' => '0',
			),
			'MAXORDER' => array(
				'configuration_value' => '1000',
			),
			'HIDEPDFCOLUMN' => array(
				'configuration_value' => '-',
				'set_function' => 'gm_cfg_select_option(array(\'-\', \'1\', \'2\', \'3\', \'4\'), ',
			),
		);
		$config = array_merge($config, $special_config);
		return $config;
	}
}
MainFactory::load_origin_class('billsafe_3_invoice');