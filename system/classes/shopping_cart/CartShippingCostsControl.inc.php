<?php
/* --------------------------------------------------------------
   CartShippingCostsControl.inc.php 2016-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_WS_CLASSES . 'shipping.php');
require_once(DIR_FS_CATALOG . 'inc/xtc_get_countries.inc.php');
require_once(DIR_FS_CATALOG . 'includes/modules/order_total/ot_gambioultra.php');
$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']) . '/modules/order_total/ot_gambioultra.php');

class CartShippingCostsControl
{
	private static $v_instance;
	public $v_shipping_class;
	protected $v_coo_xtc_price;
	public $v_module_method_separator = '_';
	
	/**
	 * Get instance of CartShippingCostsControl
	 */
	static public function get_instance()
	{
		if(self::$v_instance === null)
		{
			self::$v_instance = new self;
		}
		return self::$v_instance;
	}
	
	protected function __construct()
	{
		$t_cart_shipping_country = key($this->get_selected_country());
		$t_country = xtc_get_countriesList( $t_cart_shipping_country, true, true );
		$_SESSION['delivery_zone'] = $t_country['countries_iso_code_2'];	
			
		$this->v_shipping_class = new shipping();
		$this->v_coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
	}
	
	private function __clone() {}
	
	/**
	 * Calculates and returns the shipping costs by shipping module and by
	 * destination country
	 * 
	 * @param int $p_shipping_country Destination country
	 * @param string $p_shipping_module Shipping module
	 * @param string $p_shipping_method Shipping method
	 * @param bool $p_return_unformatted_value Whether to return the value without the currency postfix. 
	 * @return string Formatet Shipping costs with currency
	 */
	public function get_shipping_costs($p_shipping_country = false, $p_shipping_module = false, $p_shipping_method = '', $p_return_unformatted_value = false)
	{	
		//cheapest shipping costs
		if ($p_shipping_module === false)
		{
			$p_shipping_module = key($this->get_selected_shipping_module());
		}
		if ($p_shipping_country === false)
		{
			$p_shipping_country = key($this->get_selected_country());
		}
		
		if( strpos( $p_shipping_module , $this->v_module_method_separator ) !== false )
		{
			$tmp_shipping_module_data = explode( $this->v_module_method_separator, $p_shipping_module );
			$p_shipping_module = $tmp_shipping_module_data[ 0 ];
			$p_shipping_method = $tmp_shipping_module_data[ 1 ];
		}
		
		if ($p_shipping_module == 'free')
		{
			$t_formatted_shipping_costs = $this->v_coo_xtc_price->xtcFormat(0, true);
			return $p_return_unformatted_value ? 0 : $t_formatted_shipping_costs;
		}

		if (!$this->v_shipping_class->module_is_allowed($p_shipping_country, $p_shipping_module))
		{
			return false;
		}

		$t_shipping_module = $this->v_shipping_class->quote($p_shipping_method, $p_shipping_module);

		if (empty($t_shipping_module) || isset($t_shipping_module[0]['error']) || empty($t_shipping_module[0]['methods']) || !isset($t_shipping_module[0]['methods'][0]['cost']))
		{
			return false;
		}
		
		$t_shipping_costs = $t_shipping_module[0]['methods'][0]['cost'];
		
		$t_convert_currency = true;
		if( $t_shipping_module[0]['id'] != 'selfpickup' && $t_shipping_module[0]['id'] != 'free' )
		{
			$t_tax_class_id = 0;
			if(defined('MODULE_SHIPPING_' . strtoupper($t_shipping_module[0]['id']) . '_TAX_CLASS'))
			{
				$t_tax_class_id = (int)constant('MODULE_SHIPPING_' . strtoupper($t_shipping_module[0]['id']) . '_TAX_CLASS');
			}
			
			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1 && $t_tax_class_id > 0)
			{
				$t_tax_rate = xtc_get_tax_rate($t_tax_class_id, $p_shipping_country);

				$t_shipping_costs = $this->v_coo_xtc_price->xtcAddTax($t_shipping_costs, $t_tax_rate);
				$t_convert_currency = false; // xtcAddTax() already did that!
			}
		}
		
		$t_formatted_shipping_costs = $this->v_coo_xtc_price->xtcFormat($t_shipping_costs, true, 0, $t_convert_currency);
		
		return $p_return_unformatted_value ? $t_shipping_costs : $t_formatted_shipping_costs;
	}
	
	/**
	 * Gets all countries from DB
	 * 
	 * @return array An array of all countries
	 */
	public function get_countries()
	{
		$t_countries = xtc_get_countriesList();
		return $t_countries;
	}
	
	/**
	 * Gets all installed and active shipping modules
	 * 
	 * @return array An array of all shipping modules
	 */
	public function get_shipping_modules()
	{		
		global $total_weight, $total_count;
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		$total_weight = $_SESSION['cart']->weight;
		$total_count = $_SESSION['cart']->count_contents();
		$t_selected_country = key($this->get_selected_country());
		
		$t_unfiltered_modules_array = $this->v_shipping_class->quote();
		$t_modules_array = array();
		
		if ($this->v_shipping_class->is_shipping_free($t_selected_country))
		{
			$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/order_total/ot_shipping.php');
			$t_modules_array['free' . $this->v_module_method_separator . 'free'] = FREE_SHIPPING_TITLE;
			$_SESSION['shipping'] = array('id' => 'free' . $this->v_module_method_separator . 'free',
										  'title' => FREE_SHIPPING_TITLE,
										  'cost' => 0);
			return $t_modules_array;
		}
		
		foreach ($t_unfiltered_modules_array as $t_module)
		{
			$t_id_prefix = $t_module['id'] . $this->v_module_method_separator;
			
			if (isset($t_module['error']))
			{
				continue;
			}
			
			foreach ($t_module['methods'] as $t_method)
			{
				$t_title = '';
				if($t_method['id'] != $t_module['id'])
				{
					$t_title = ' - ' . $t_method['title'];
				}
				
				$t_modules_array[$t_id_prefix . $t_method['id']] = $t_module['module'] . $t_title;
			}
		}
		
		return $t_modules_array;
	}
	
	/**
	 * Gets the currently selected shipping country ID. In descending priority
	 * it is chosen from country-selectbox (in shopping cart), customer data of
	 * a logged in user or the default shipping country of the shop.
	 * 
	 * @return int The shipping country ID
	 */
	public function get_selected_country()
	{
		global $order;
		
		$t_script_name = $_SERVER['SCRIPT_NAME'];
		
		if(function_exists('gm_get_env_info'))
		{
			$t_script_name = gm_get_env_info('SCRIPT_NAME');
		}		
		
		if(strpos($t_script_name, 'checkout') !== false && isset($order) && !empty($order->delivery['country']['id']))
		{
			$t_country_data = xtc_get_countriesList( $order->delivery['country']['id'], false, true );
			$t_name = $t_country_data['countries_name'];
			return array($order->delivery['country']['id'] => $t_name);
		}		
		elseif (isset($_SESSION['cart_shipping_country']) && strpos($t_script_name, 'checkout') === false)
		{
			$t_country_data = xtc_get_countriesList( $_SESSION['cart_shipping_country'], false, true );
			$t_name = $t_country_data['countries_name'];
			return array($_SESSION['cart_shipping_country'] => $t_name);
		}
		
		if (isset($_SESSION['customer_country_id']))
		{
			$t_country_data = xtc_get_countriesList( $_SESSION['customer_country_id'], false, true );
			$t_name = $t_country_data['countries_name'];
			return array($_SESSION['customer_country_id'] => $t_name);
		}
		
		$t_country_data = xtc_get_countriesList( STORE_COUNTRY, false, true );
		$t_name = $t_country_data['countries_name'];
		return array(STORE_COUNTRY => $t_name);
	}
	
	/**
	 * Gets the currently selected shipping module. In descending priority
	 * it is chosen from shipping-module-selectbox (in shopping cart) or the
	 * cheapest available shipping module (self-pickup excluded).
	 * 
	 * @return string The shipping module identifier (a short string)
	 */
	public function get_selected_shipping_module()
	{
		$t_modules = $this->get_shipping_modules();
		
		if (isset($_SESSION['shipping']))
		{
			$t_shipping_module = $_SESSION['shipping']['id'];
			if (isset($t_modules[$t_shipping_module]) && !empty($t_modules[$t_shipping_module]))
			{
				$t_name = $t_modules[$t_shipping_module];
				$t_return = array($t_shipping_module => $t_name);
				return $t_return;
			}
		}
		
		$t_module = $this->v_shipping_class->shopping_cart_cheapest();
		$t_id = $t_module['id'] . $this->v_module_method_separator . $t_module['method_id'];
		$t_title = '';
		
		if($t_module['id'] != $t_module['method_id'])
		{
			$t_title = ' (' . $t_module['method_title'] . ')';
		}
		
		$t_name = $t_module['module'] . $t_title;
		$t_return = array($t_id => $t_name);
		
		return $t_return;
	}
	
	public function get_ot_gambioultra_info_html()
	{
		$t_html = '<span class="cart_shipping_costs_gambio_ultra_dropdown">';
		
		if($this->ot_gambioultra_active() && $this->get_ot_gambioultra_costs() !== '')
		{
			$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array( 'gambioultra', $_SESSION['languages_id']), false);
			
			$t_html .= '<br /> ' . SHIPPING_EXCL . ' ' . $coo_text_mgr->get_text('name') . ': <span class="cart_ot_gambioultra_costs_value">' . $this->get_ot_gambioultra_costs() . '</span>';
		}
		$t_html .= '</span>';
		
		return $t_html;
	}
	
	public function get_ot_gambioultra_costs($p_shipping_country_id = false)
	{
		$t_price = '';
		
		if($_SESSION['shipping']['id'] == 'selfpickup_selfpickup') return $t_price;
		
		if($this->ot_gambioultra_active())
		{
			$coo_ot_gambioultra = new ot_gambioultra();
			$t_info_array = $coo_ot_gambioultra->nc_get_product_shipping_costs();
			
			$t_tax_class_id = (int)MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS;
			$t_shipping_country_id = $p_shipping_country_id;
			if($t_shipping_country_id === false)
			{
				$t_shipping_country_id = key($this->get_selected_country());
			}			
			
			foreach($t_info_array['infos'] AS $t_data_array)
			{
				if($_SESSION['customers_status']['customers_status_show_price_tax'] == 1 && $t_tax_class_id > 0)
				{
					$t_tax_rate = xtc_get_tax_rate($t_tax_class_id, $t_shipping_country_id);

					$t_price += $this->v_coo_xtc_price->xtcAddTax($t_data_array['price_plain'], $t_tax_rate);
				}
				else
				{
					$t_price += $t_data_array['price_plain'];
				}
			}
			
			if($t_price != 0)
			{
				$t_price = $this->v_coo_xtc_price->xtcFormat($t_price, true, 0, true);
			}			
		}
		
		return $t_price;
	}
	
	public function ot_gambioultra_active()
	{
		$t_selected_country = key($this->get_selected_country());
		
		if(defined('MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS') && MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS == 'true' 
			&&
			(	(MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION == 'national' && STORE_COUNTRY == $t_selected_country) ||
				(MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION == 'international' && STORE_COUNTRY != $t_selected_country) ||
				MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION == 'both')
			)
		{
			return true;
		}
		
		return false;
	}

	public function is_shipping_free()
	{
		return $this->v_shipping_class->is_shipping_free($this->get_selected_country());
	}
}