<?php
/* --------------------------------------------------------------
   ot_tax.php 2015-07-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_tax.php,v 1.14 2003/02/14); www.oscommerce.com  
   (c) 2003	 nextcommerce (ot_tax.php,v 1.11 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_tax.php 1002 2005-07-10 16:11:37Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

/**
 * Class ot_tax
 */
class ot_tax_ORIGIN
{
	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var array
	 */
	public $output;


	public function __construct()
	{
		$this->code        = 'ot_tax';
		$this->title       = MODULE_ORDER_TOTAL_TAX_TITLE;
		$this->description = MODULE_ORDER_TOTAL_TAX_DESCRIPTION;
		$this->enabled     = ((MODULE_ORDER_TOTAL_TAX_STATUS == 'true') ? true : false);
		$this->sort_order  = MODULE_ORDER_TOTAL_TAX_SORT_ORDER;

		$this->output = array();
	}


	public function process()
	{
		global $order, $xtPrice;
		reset($order->info['tax_groups']);
		while(list($key, $value) = each($order->info['tax_groups']))
		{
			if($value > 0)
			{

				if($_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
				{
					$this->output[] = array(
						'title' => $key . ':',
						'text'  => $xtPrice->xtcFormat($value, true),
						'value' => $xtPrice->xtcFormat($value, false)
					);
				}
				elseif($_SESSION['customers_status']['customers_status_show_price_tax'] == 0
				       && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
				)
				{
					$this->output[] = array(
						'title' => $key . ':',
						'text'  => $xtPrice->xtcFormat($value, true),
						'value' => $xtPrice->xtcFormat($value, false)
					);
				}
				elseif($this->isIntracommunityDelivery())
				{
					$this->output[] = array(
						'title' => INTRACOMMUNITY_DELIVERY_TEXT,
						'text'  => ' ', //This is not a normal space. This is a not visible UTF 8 character. Do not change it!
						'value' => $xtPrice->xtcFormat(0, false)
					);
				}
			}
		}
	}


	/**
	 * @return bool|int
	 */
	public function check()
	{
		if(!isset($this->_check))
		{
			$check_query  = xtc_db_query("SELECT configuration_value 
										  FROM " . TABLE_CONFIGURATION . " 
										  WHERE configuration_key = 'MODULE_ORDER_TOTAL_TAX_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}

		return $this->_check;
	}


	/**
	 * @return bool
	 */
	public function isIntracommunityDelivery()
	{
		if($_SESSION['customers_status']['customers_status_show_price_tax'] != 0
		   || $_SESSION['customers_status']['customers_status_add_tax_ot'] != 0)
		{
			return false;
		}

		if(defined('STORE_OWNER_VAT_ID') && strlen(STORE_OWNER_VAT_ID) >= 2)
		{
			$storeCountryIsoCode = strtoupper(substr(trim(STORE_OWNER_VAT_ID), 0, 2));
		}
		else
		{
			return false;
		}

		$customerVatId = trim($_SESSION['customer_vat_id']);

		if(strlen($customerVatId) >= 2)
		{
			$customerCountryIsoCode = strtoupper(substr($customerVatId, 0, 2));
		}
		else
		{
			return false;
		}

		$euCountriesIsoCodes = array(
			'BE',
			'BG',
			'DK',
			'DE',
			'EE',
			'FI',
			'FR',
			'GR',
			'IE',
			'IT',
			'HR',
			'LV',
			'LT',
			'LU',
			'MT',
			'NL',
			'AT',
			'PL',
			'PT',
			'RO',
			'SE',
			'SK',
			'SI',
			'ES',
			'CZ',
			'HU',
			'GB',
			'CY'
		);
		$deliveryZone        = $GLOBALS['order']->delivery['country']['iso_code_2'];

		if(in_array($deliveryZone, $euCountriesIsoCodes) && $deliveryZone !== $storeCountryIsoCode
		   && $storeCountryIsoCode !== $customerCountryIsoCode)
		{
			return true;
		}

		return false;
	}


	/**
	 * @return array
	 */
	public function keys()
	{
		return array('MODULE_ORDER_TOTAL_TAX_STATUS', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER');
	}


	public function install()
	{
		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " 
					  (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) 
		              VALUES ('MODULE_ORDER_TOTAL_TAX_STATUS', 'true', '6', '1','gm_cfg_select_option(array(\'true\', \'false\'), ', now())");

		xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " 
					  (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) 
		              VALUES ('MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '97', '6', '2', now())");
	}


	public function remove()
	{
		xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " 
					  WHERE configuration_key 
					  IN ('" . implode("', '", $this->keys()) . "')");
	}
}

MainFactory::load_origin_class('ot_tax');