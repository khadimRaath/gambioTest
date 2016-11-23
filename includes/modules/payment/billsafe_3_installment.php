<?php
/* --------------------------------------------------------------
   billsafe_3_installment.php 2014-07-15 mabr
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas PlÃ¤nkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once dirname(__FILE__).'/billsafe3/billsafe_3_base.php';

class billsafe_3_installment_ORIGIN extends billsafe_3_base {
	
	public function __construct() {
		parent::__construct();
		$this->code = 'billsafe_3_installment';
		$this->title = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_TITLE;
		$this->description = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_DESCRIPTION.'<br><br>Version: '.GMBillSafe::MODULE_VERSION;
		$this->description .= '<br><br>'.MODULE_PAYMENT_BILLSAFE_3_TEXT_DESCRIPTION_LINK.'<br><br>'.$this->_checkRequirements();
		$this->sort_order = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_STATUS == 'True') ? true : false);
		$this->info = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_INFO;
		if ((int) MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ORDER_STATUS_ID;
		}
		$this->tmpStatus = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TMPORDER_STATUS_ID;

		if(is_object($order)) {
			$this->update_status();
		}

		$this->logo = '<img src="https://www.billsafe.de/resources/img/banner/135x30-ratenkauf-in-kooperation-mit-billsafe-white.png" style="float: right;">';
	}

	function update_status() {
		global $order;
		
		if (($this->enabled == true) && ((int) MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_ZONE."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
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

	function confirmation() {
		$confirmation = parent::confirmation();
		$confirmation['title'] = MODULE_PAYMENT_BILLSAFE_3_INSTALLMENT_TEXT_DESCRIPTION;
		return $confirmation;
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

		);
		$config = array_merge($config, $special_config);
		return $config;
	}
}
MainFactory::load_origin_class('billsafe_3_installment');