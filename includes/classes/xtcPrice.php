<?php
/* --------------------------------------------------------------
  xtcPrice.php 2016-05-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(currencies.php,v 1.15 2003/03/17); www.oscommerce.com
  (c) 2003         nextcommerce (currencies.php,v 1.9 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtcPrice.php 1316 2005-10-21 15:30:58Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class xtcPrice_ORIGIN
{
	public $currencies = array();
	public $cStatus = array();
	public $actualGroup;
	public $actualCurr = '';
	public $TAX = array();
	public $SHIPPING = array();
	public $showFrom_Attributes = true;
	public $content_type = null;
	public $v_coo_language_text_manager;

	protected $dataCache;

	// class constructor
	public function __construct($p_currency_code, $p_customers_status_id)
	{
		$this->dataCache = DataCache::get_instance();

		$this->actualGroup = $p_customers_status_id;
		$this->actualCurr = $p_currency_code;
		$this->v_coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('price', $_SESSION['languages_id']));

		// select Currencies
		$t_sql = 'SELECT * FROM ' . TABLE_CURRENCIES;
		$t_result = xtc_db_query($t_sql);
		
		while($currencies = xtc_db_fetch_array($t_result))
		{
			$this->currencies[$currencies['code']] = array('title' => $currencies['title'], 
															'symbol_left' => $currencies['symbol_left'], 
															'symbol_right' => $currencies['symbol_right'], 
															'decimal_point' => $currencies['decimal_point'], 
															'thousands_point' => $currencies['thousands_point'], 
															'decimal_places' => $currencies['decimal_places'], 
															'value' => $currencies['value']);
		}
		
		// select Customers Status data
		$t_sql = "SELECT * FROM " . TABLE_CUSTOMERS_STATUS . "
					WHERE
						customers_status_id = '" . $this->actualGroup . "' AND 
						language_id = '" . $_SESSION['languages_id'] . "'";
		$t_result = xtc_db_query($t_sql);
		$t_customers_status_array = xtc_db_fetch_array($t_result);
		$this->cStatus = array('customers_status_id' => $this->actualGroup, 
								'customers_status_name' => $t_customers_status_array['customers_status_name'], 
								'customers_status_image' => $t_customers_status_array['customers_status_image'], 
								'customers_status_public' => $t_customers_status_array['customers_status_public'], 
								'customers_status_discount' => $t_customers_status_array['customers_status_discount'], 
								'customers_status_ot_discount_flag' => $t_customers_status_array['customers_status_ot_discount_flag'], 
								'customers_status_ot_discount' => $t_customers_status_array['customers_status_ot_discount'], 
								'customers_status_graduated_prices' => $t_customers_status_array['customers_status_graduated_prices'], 
								'customers_status_show_price' => $t_customers_status_array['customers_status_show_price'], 
								'customers_status_show_price_tax' => $t_customers_status_array['customers_status_show_price_tax'], 
								'customers_status_add_tax_ot' => $t_customers_status_array['customers_status_add_tax_ot'], 
								'customers_status_payment_unallowed' => $t_customers_status_array['customers_status_payment_unallowed'], 
								'customers_status_shipping_unallowed' => $t_customers_status_array['customers_status_shipping_unallowed'], 
								'customers_status_discount_attributes' => $t_customers_status_array['customers_status_discount_attributes'], 
								'customers_fsk18' => $t_customers_status_array['customers_fsk18'], 
								'customers_fsk18_display' => $t_customers_status_array['customers_fsk18_display']);

		// prefetch tax rates for standard zone
		$t_sql = 'SELECT tax_class_id AS class FROM ' . TABLE_TAX_CLASS;
		$t_result = xtc_db_query($t_sql);
		
		while($t_zones_array = xtc_db_fetch_array($t_result))
		{
			// calculate tax based on shipping or deliverey country (for downloads)
			if(isset($_SESSION['billto']) && isset($_SESSION['sendto']))
			{
				$t_content_type = null;
				
				if(isset($_SESSION['cart']) && method_exists($_SESSION['cart'], 'get_content_type'))
				{
					$t_content_type = $_SESSION['cart']->get_content_type();
				}
				
				$t_sql = "SELECT 
								ab.entry_country_id, 
								ab.entry_zone_id 
							FROM " . TABLE_ADDRESS_BOOK . " ab 
							LEFT JOIN " . TABLE_ZONES . " z ON (ab.entry_zone_id = z.zone_id) 
							WHERE
								ab.customers_id = '" . $_SESSION['customer_id'] . "' AND
								ab.address_book_id = '" . ($t_content_type == 'virtual' ? xtc_db_input($_SESSION['billto']) : xtc_db_input($_SESSION['sendto'])) . "'";
				$t_address_result = xtc_db_query($t_sql);
				$t_address_array = xtc_db_fetch_array($t_address_result);
				$this->TAX[$t_zones_array['class']] = xtc_get_tax_rate($t_zones_array['class'], $t_address_array['entry_country_id'], $t_address_array['entry_zone_id']);
			}
			elseif(!isset($_SESSION['customer_id']))
			{
				$t_customer_country_id = (isset($_SESSION['customer_country_id'])) ? (int)$_SESSION['customer_country_id'] : -1;

				$this->TAX[$t_zones_array['class']] = xtc_get_tax_rate($t_zones_array['class'], $t_customer_country_id);
			}
			else
			{
				$this->TAX[$t_zones_array['class']] = xtc_get_tax_rate($t_zones_array['class']);
			}
		}
	}

	// get products Price
	public function xtcGetPrice($p_products_id, $p_format_price, $p_quantity, $p_tax_class_id, $p_products_price, $p_return_array = 0, $p_customers_id = 0, $p_include_special = true, $p_consider_properties = false, $p_combis_id = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		// check if group is allowed to see prices
		if($this->cStatus['customers_status_show_price'] == '0')
		{
			$this->dataCache->set_data($cacheKey, $this->xtcShowNote($p_return_array));

			return $this->dataCache->get_data($cacheKey);
		}

		// check price status
		if($this->gm_check_price_status($p_products_id) != 0)
		{
			if($this->gm_check_price_status($p_products_id) == 2)
			{
				$t_price = $this->getPprice($p_products_id);
				
				if($t_price == 0)
				{
					$this->dataCache->set_data($cacheKey, $this->gm_show_price_status($this->gm_check_price_status($p_products_id), $p_return_array));

					return $this->dataCache->get_data($cacheKey);
				}
			}
			else
			{
				$this->dataCache->set_data($cacheKey, $this->gm_show_price_status($this->gm_check_price_status($p_products_id), $p_return_array));

				return $this->dataCache->get_data($cacheKey);
			}
		}

		// get Tax rate
		if($p_customers_id != 0)
		{
			$t_customer_info_array = xtc_oe_customer_infos($p_customers_id);
			$t_tax_rate = xtc_get_tax_rate($p_tax_class_id, $t_customer_info_array['country_id'], $t_customer_info_array['zone_id']);
		}
		else
		{
			$t_tax_rate = $this->TAX[$p_tax_class_id];
		}

		if($this->cStatus['customers_status_show_price_tax'] == '0')
		{
			$t_tax_rate = 0;
		}

		$t_products_price = $p_products_price;
		
		$t_combis_id = $p_combis_id;
		if($p_consider_properties && $p_combis_id == 0)
		{
			$t_combis_id = $this->extract_combis_id($p_products_id);
		}
		elseif($p_consider_properties === false)
		{
			$t_combis_id = 0;
		}
		
		// add taxes
		if($p_products_price == 0)
		{
			$t_products_price = $this->getPprice($p_products_id);
		}
		
		if($t_combis_id > 0)
		{
			$t_combi_price = $this->get_properties_combi_price($t_combis_id, 0, false);
			$t_products_price += $t_combi_price;
		}
		
		$t_products_price = $this->xtcAddTax($t_products_price, $t_tax_rate);
		
		// check specialprice
		$t_special_price = $this->xtcCheckSpecial($p_products_id, $t_combis_id);
		if($p_include_special === true && $p_products_id != 0 && is_numeric($t_special_price) == true)
		{
			$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecial($p_products_id, $this->xtcAddTax($t_special_price, $t_tax_rate), $t_products_price, $p_format_price, $p_return_array));

			return $this->dataCache->get_data($cacheKey);
		}

		$t_quantity = $p_quantity;
		
		// check graduated
		if ($this->cStatus['customers_status_graduated_prices'] == '1') {
			// check Graduated/Group Price
			$t_special_price = $this->xtcGetGraduatedPrice($p_products_id, $t_quantity, $t_combis_id);
			if(empty($t_special_price) === false)
			{
				if($this->xtcCheckDiscount($p_products_id))
				{
					$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecialGraduated($p_products_id, $this->xtcAddTax($t_special_price, $t_tax_rate), $this->xtcAddTax($t_special_price, $t_tax_rate), $p_format_price, $p_return_array));

					return $this->dataCache->get_data($cacheKey);
				}
				
				$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecialGraduated($p_products_id, $this->xtcAddTax($t_special_price, $t_tax_rate), $t_products_price, $p_format_price, $p_return_array));

				return $this->dataCache->get_data($cacheKey);
			}
		}
		
		// check Group Price
		$t_special_price = $this->xtcGetGroupPrice($p_products_id, 1, $t_combis_id);
		if(empty($t_special_price) === false)
		{
			if($this->xtcCheckDiscount($p_products_id))
			{
				$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecialGraduated($p_products_id, $this->xtcAddTax($t_special_price, $t_tax_rate), $this->xtcAddTax($t_special_price, $t_tax_rate), $p_format_price, $p_return_array));

				return $this->dataCache->get_data($cacheKey);
			}
			
			$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecialGraduated($p_products_id, $this->xtcAddTax($t_special_price, $t_tax_rate), $t_products_price, $p_format_price, $p_return_array));

			return $this->dataCache->get_data($cacheKey);
		}

		// check Product Discount
		$t_discount = $this->xtcCheckDiscount($p_products_id);
		if(empty($t_discount) === false)
		{
			if($p_products_price == 0)
			{
				$t_products_price = $this->getPprice($p_products_id);
				$t_products_price = $this->xtcAddTax($t_products_price, $t_tax_rate);
			}
			elseif($t_combi_price != 0)
			{
				$t_products_price -= $this->xtcAddTax($t_combi_price, $t_tax_rate);
			}
			
			$t_combi_price = $this->get_properties_combi_price($t_combis_id, $p_tax_class_id);

			$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecialDiscount($p_products_id, $t_discount, $t_products_price, $p_format_price, $p_return_array, 0, $t_combi_price));

			return $this->dataCache->get_data($cacheKey);
		}
		$this->dataCache->set_data($cacheKey, $this->xtcFormat($t_products_price, $p_format_price, 0, false, $p_return_array, $p_products_id));

		return $this->dataCache->get_data($cacheKey);
	}

	public function gm_check_price_status($p_products_id)
	{
		static $t_status_array;
		
		$t_price_status = false;
		
		if($t_status_array !== null && isset($t_status_array[$p_products_id]))
		{
			return $t_status_array[$p_products_id];
		}
		elseif(is_array($t_status_array) === false)
		{
			$t_status_array = array();
		}
		
		$t_price_status_array = array();
		$t_sql = 'SELECT gm_price_status FROM products WHERE products_id = "' . (int)$p_products_id . '"';
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_price_status_array = xtc_db_fetch_array($t_result);
			$t_price_status = $t_price_status_array['gm_price_status'];
			$t_status_array[$p_products_id] = $t_price_status;
		}
		
		return $t_price_status;
	}

	public function gm_show_price_status($t_price_status, $p_return_array)
	{
		switch($t_price_status)
		{
			case 1:
				if($p_return_array == 1)
				{
					$t_price_array = array();
					$t_price_array['formated'] = GM_SHOW_PRICE_ON_REQUEST;
					$t_price_array['plain'] = 0;
					
					return $t_price_array;
				}
				else
				{
					return 0;
				}
				
				break;
			case 2:
				if($p_return_array == 1)
				{
					$t_price_array = array();
					$t_price_array['formated'] = GM_SHOW_NO_PRICE;
					$t_price_array['plain'] = 0;
					
					return $t_price_array;
				}
				else
				{
					return 0;
				}
				
				break;
			default:
				return false;
		}
	}

	public function getPprice($p_products_id)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_price = 0;
		$t_sql = "SELECT products_price FROM " . TABLE_PRODUCTS . " WHERE products_id='" . (int)$p_products_id . "'";
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_price = $t_result_array['products_price'];
		}
		
		$this->dataCache->set_data($cacheKey, $t_price);

		return $this->dataCache->get_data($cacheKey);
	}

	public function xtcAddTax($p_price, $p_tax_rate, $p_consider_currency = true)
	{
		$t_price = $p_price + $p_price / 100 * $p_tax_rate;
		
		if($p_consider_currency)
		{
			$t_price = $this->xtcCalculateCurr($t_price);
		}
		
		$t_price = round($t_price, $this->currencies[$this->actualCurr]['decimal_places']);
		
		return $t_price;
	}

	public function xtcCheckDiscount($p_products_id)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		// check if group got discount
		if($this->cStatus['customers_status_discount'] != '0.00' && !$this->xtcCheckSpecial($p_products_id))
		{
			$t_discount = 0;
			
			$t_sql = "SELECT products_discount_allowed FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int)$p_products_id . "'";
			$t_result = xtc_db_query($t_sql);
			
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$t_discount = $t_result_array['products_discount_allowed'];
			}
			
			if($this->cStatus['customers_status_discount'] < $t_discount)
			{
				$t_discount = $this->cStatus['customers_status_discount'];
			}
			
			if($t_discount == '0.00')
			{
				$this->dataCache->set_data($cacheKey, false);

				return $this->dataCache->get_data($cacheKey);
			}
			
			$this->dataCache->set_data($cacheKey, $t_discount);

			return $this->dataCache->get_data($cacheKey);
		}
		
		$this->dataCache->set_data($cacheKey, false);

		return $this->dataCache->get_data($cacheKey);
	}

	public function xtcGetGraduatedPrice($p_products_id, $p_quantity, $p_combis_id = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_quantity = $p_quantity;
		
		if(GRADUATED_ASSIGN == 'true' && xtc_get_qty($p_products_id) > $p_quantity)
		{
			$t_quantity = xtc_get_qty($p_products_id);
		}
				
		$t_sql = "SELECT MAX(quantity) AS qty
					FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
					WHERE 
						products_id = '" . (int)$p_products_id . "' AND 
						quantity <= '" . xtc_db_input($t_quantity) . "'";
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
		
			if($t_result_array['qty'])
			{
				$t_sql = "SELECT personal_offer
							FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
							WHERE 
								products_id = '" . (int)$p_products_id . "' AND
								quantity = '" . $t_result_array['qty'] . "'";
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) > 0)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$t_price = $t_result_array['personal_offer'];

					if($t_price != 0)
					{
						$t_price += $this->get_properties_combi_price($p_combis_id, 0, false);
						
						$this->dataCache->set_data($cacheKey, $t_price);

						return $this->dataCache->get_data($cacheKey);
					}
				}
			}
		}
		
		$this->dataCache->set_data($cacheKey, null);

		return $this->dataCache->get_data($cacheKey);
	}

	public function xtcGetGroupPrice($p_products_id, $p_quantity, $p_combis_id = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_sql = "SELECT MAX(quantity) AS qty
									FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
									WHERE 
										products_id='" . (int)$p_products_id . "' AND 
										quantity <= '" . xtc_db_input($p_quantity) . "'";
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
		
			if($t_result_array['qty'])
			{
				$t_sql = "SELECT personal_offer
							FROM " . TABLE_PERSONAL_OFFERS_BY . $this->actualGroup . "
							WHERE products_id = '" . (int)$p_products_id . "'
							AND quantity = '" . xtc_db_input($t_result_array['qty']) . "'";
				$t_result = xtc_db_query($t_sql);
				$t_result_array = xtc_db_fetch_array($t_result);

				$t_price = $t_result_array['personal_offer'];

				if($t_price != 0.00)
				{
					$t_price += $this->get_properties_combi_price($p_combis_id, 0, false);
					
					$this->dataCache->set_data($cacheKey, $t_price);

					return $this->dataCache->get_data($cacheKey);
				}
			}
		}
		
		$this->dataCache->set_data($cacheKey, null);

		return $this->dataCache->get_data($cacheKey);
	}

	public function xtcGetOptionPrice($p_products_id, $p_options_id, $p_options_values_id)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_option_data_array = array();
		
		$t_sql = "SELECT 
						pd.products_discount_allowed,
						pd.products_tax_class_id, 
						p.options_values_price, 
						p.price_prefix, 
						p.options_values_weight, 
						p.weight_prefix 
					FROM 
						" . TABLE_PRODUCTS_ATTRIBUTES . " p, 
						" . TABLE_PRODUCTS . " pd 
					WHERE
						p.products_id = '" . (int)$p_products_id . "' AND 
						p.options_id = '" . (int)$p_options_id . "' AND 
						pd.products_id = p.products_id AND 
						p.options_values_id = '" . (int)$p_options_values_id . "'";
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_discount = 0;

			if($this->cStatus['customers_status_discount_attributes'] == 1 && $this->cStatus['customers_status_discount'] != 0.00 && $this->xtcCheckSpecial($p_products_id) == false)
			{
				$t_discount = $this->cStatus['customers_status_discount'];

				if($t_result_array['products_discount_allowed'] < $this->cStatus['customers_status_discount'])
				{
					$t_discount = $t_result_array['products_discount_allowed'];
				}
			}

			if($t_result_array['products_tax_class_id'] != 0)
			{
				$t_price = $this->xtcFormat($t_result_array['options_values_price'], false, $t_result_array['products_tax_class_id']);
			}
			else
			{
				$t_price = $this->xtcFormat($t_result_array['options_values_price'], false, $t_result_array['products_tax_class_id'], true);
			}

			if($t_result_array['weight_prefix'] != '+')
			{
				$t_result_array['options_values_weight'] *= -1;
			}

			if($t_result_array['price_prefix'] == '+')
			{
				$t_price = $t_price - $t_price / 100 * $t_discount;
			}
			else
			{
				$t_price = ($t_price - $t_price / 100 * $t_discount) * -1;
			}

			$t_option_data_array['weight'] = $t_result_array['options_values_weight'];
			$t_option_data_array['price'] =  $t_price;
		}
		
		$this->dataCache->set_data($cacheKey, $t_option_data_array);

		return $this->dataCache->get_data($cacheKey);
	}

	public function xtcShowNote($p_return_array)
	{
		if($p_return_array == 1)
		{
			$t_note_array = array();
			$t_note_array['formated'] = NOT_ALLOWED_TO_SEE_PRICES;
			$t_note_array['plain'] =  0;
			
			return $t_note_array;
		}
		
		return NOT_ALLOWED_TO_SEE_PRICES;
	}

	public function xtcCheckSpecial($p_products_id, $p_combis_id = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_price = false;
		
		$t_sql = "SELECT specials_new_products_price 
					FROM " . TABLE_SPECIALS . " 
					WHERE 
						products_id = '" . (int)$p_products_id . "' AND 
						status = 1";
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_price = $t_result_array['specials_new_products_price'] + $this->get_properties_combi_price($p_combis_id, 0, false);
		}
				
		$this->dataCache->set_data($cacheKey, $t_price);

		return $this->dataCache->get_data($cacheKey);
	}

	public function xtcCalculateCurr($p_price)
	{
		$t_price = $this->currencies[$this->actualCurr]['value'] * $p_price;
		
		return $t_price;
	}

	public function calcTax($p_price, $p_tax_rate)
	{
		$t_tax = $p_price * $p_tax_rate / 100;
		
		return $t_tax;
	}

	public function xtcRemoveCurr($p_price)
	{
		// check if used Curr != DEFAULT curr
		if(DEFAULT_CURRENCY != $this->actualCurr)
		{
			$t_price = $p_price * (1 / $this->currencies[$this->actualCurr]['value']);
			
			return $t_price;
		}
		else
		{
			return $p_price;
		}
	}

	public function xtcRemoveTax($p_price, $p_tax_rate)
	{
		$t_price = ($p_price / (($p_tax_rate + 100) / 100));
		
		return $t_price;
	}

	public function xtcGetTax($p_price, $p_tax_rate)
	{
		$t_tax = $p_price - $this->xtcRemoveTax($p_price, $p_tax_rate);
		
		return $t_tax;
	}

	public function xtcRemoveDC($p_price, $p_discount)
	{
		$t_price = $p_price - ($p_price / 100 * $p_discount);

		return $t_price;
	}

	public function xtcGetDC($p_price, $p_discount)
	{
		$t_discount = $p_price / 100 * $p_discount;

		return $t_discount;
	}

	public function checkAttributes($p_products_id, $pIsSpecial = false)
	{
		if(!$this->showFrom_Attributes || (int)$p_products_id == 0)
		{
			return;
		}

		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_sql = "SELECT COUNT(*) AS total
					FROM
						" . TABLE_PRODUCTS_OPTIONS . " popt,
						" . TABLE_PRODUCTS_ATTRIBUTES . " patrib
					WHERE
						patrib.products_id = '" . (int)$p_products_id . "' AND
						patrib.options_id = popt.products_options_id AND
						patrib.options_values_price > 0 AND
						popt.language_id = '" . (int)$_SESSION['languages_id'] . "'";
		$t_result = xtc_db_query($t_sql);
		$t_attributes_array = xtc_db_fetch_array($t_result);

		$t_sql = "SELECT COUNT(*) AS count FROM products_properties_combis WHERE products_id = '" . (int)$p_products_id . "' GROUP BY combi_price";
		$t_result = xtc_db_query($t_sql);

		if($t_attributes_array['total'] > 0 || xtc_db_num_rows($t_result) > 1)
		{
			if($pIsSpecial)
			{
				$t_return_string = ' ' . $this->v_coo_language_text_manager->get_text('from_only') . ' ';
			}
			else
			{
				$t_return_string = ' ' . strtolower_wrapper(FROM) . ' ';
			}
			
			
			$this->dataCache->set_data($cacheKey, $t_return_string);

			return $this->dataCache->get_data($cacheKey);
		}
	}

	public function xtcCalculateCurrEx($p_price, $p_currency_code)
	{
		$t_price = $p_price * ($this->currencies[$p_currency_code]['value'] / $this->currencies[$this->actualCurr]['value']);
		
		return $t_price;
	}

	/*
	 *
	 *    Format Functions
	 *
	 *
	 *
	 */

	public function xtcFormat($p_price, $p_format, $p_tax_class = 0, $p_calculate_currency = false, $p_return_array = 0, $p_products_id = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_price = $p_price;
		
		if($p_calculate_currency)
		{
			$t_price = $this->xtcCalculateCurr($p_price);
		}

		if($p_tax_class != 0)
		{
			$t_tax_rate = $this->TAX[$p_tax_class];
			
			if($this->cStatus['customers_status_show_price_tax'] == '0')
			{
				$t_tax_rate = 0;
			}
			
			$t_price = $this->xtcAddTax($t_price, $t_tax_rate);
		}

		if($p_format)
		{
			$t_final_price = number_format((double)$t_price, (double)$this->currencies[$this->actualCurr]['decimal_places'], $this->currencies[$this->actualCurr]['decimal_point'], $this->currencies[$this->actualCurr]['thousands_point']);
			$t_final_price = $this->checkAttributes($p_products_id) . $this->currencies[$this->actualCurr]['symbol_left'] . ' ' . $t_final_price . ' ' . $this->currencies[$this->actualCurr]['symbol_right'];
			
			if($p_return_array == 0)
			{
				$this->dataCache->set_data($cacheKey, $t_final_price);

				return $this->dataCache->get_data($cacheKey);
			}
			else
			{
				$t_price_array = array();
				$t_price_array['formated'] = $t_final_price;
				$t_price_array['plain'] = $t_price;
				
				$this->dataCache->set_data($cacheKey, $t_price_array);

				return $this->dataCache->get_data($cacheKey);
			}
		}
		else
		{
			$t_price = round($t_price, $this->currencies[$this->actualCurr]['decimal_places']);
			
			$this->dataCache->set_data($cacheKey, $t_price);

			return $this->dataCache->get_data($cacheKey);
		}
	}

	public function xtcFormatSpecialDiscount($p_products_id, $p_discount, $p_price, $p_format, $p_return_array = 0, $p_attributes_price = 0, $p_combis_price = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_price = $p_price;
		
		if($this->cStatus['customers_status_discount_attributes'] == 1)
		{
			$t_price += $p_combis_price;
			$t_final_price = $t_price - ($t_price / 100) * $p_discount + $p_attributes_price;
		}
		else
		{
			if ($t_price == 0 && $p_combis_price != 0)
			{
				$t_final_price = ($p_combis_price / 100) * $p_discount;
			}
			else
			{
				$t_final_price = $t_price - ($t_price / 100) * $p_discount + $p_attributes_price + $p_combis_price;
			}
			$t_price += $p_combis_price;
		}
		
		if($p_format)
		{
			$t_price_html = $this->v_coo_language_text_manager->get_text('new_discount_price') . $this->checkAttributes($p_products_id) . $this->xtcFormat($t_final_price, $p_format);

			if(gm_get_conf('SHOW_OLD_DISCOUNT_PRICE') == '1')
			{
				$t_price_html = '<span class="productOldPrice">' . $this->v_coo_language_text_manager->get_text('old_discount_price') . $this->xtcFormat($t_price + $p_attributes_price, $p_format) . '</span><br />' . $t_price_html;
				
				$t_discount = 0;
				if(($t_price + $p_attributes_price) != 0)
				{
					$t_discount = (1 - $t_final_price / ($t_price + $p_attributes_price)) * 100;
				}

				$t_price_html .= '<br />' . $this->v_coo_language_text_manager->get_text('you_save') . str_replace('.', $this->currencies[$this->actualCurr]['decimal_point'], (string)round($t_discount, 2)) . '%';
			}

			if($p_return_array == 0)
			{
				$this->dataCache->set_data($cacheKey, $t_price_html);

				return $this->dataCache->get_data($cacheKey);
			}
			else
			{
				$t_price_array = array();
				$t_price_array['formated'] = $t_price_html;
				$t_price_array['plain'] = $t_final_price;
				
				$this->dataCache->set_data($cacheKey, $t_price_array);

				return $this->dataCache->get_data($cacheKey);
			}
		}
		else
		{
			$t_final_price = round($t_final_price, $this->currencies[$this->actualCurr]['decimal_places']);
			
			$this->dataCache->set_data($cacheKey, $t_final_price);

			return $this->dataCache->get_data($cacheKey);
		}
	}

	public function xtcFormatSpecial($p_products_id, $p_special_price, $p_old_price, $p_format, $p_return_array = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		if($p_format)
		{
			$t_price_html = '';

			if(gm_get_conf('SHOW_OLD_SPECIAL_PRICE') == '1')
			{
				$t_price_html .= '<span class="productOldPrice">' . $this->v_coo_language_text_manager->get_text('old_special_price') . $this->xtcFormat($p_old_price, $p_format) . '</span><br />';
			}

			if($this->checkAttributes($p_products_id, true))
			{
				$t_price_html .= $this->checkAttributes($p_products_id, true) . $this->xtcFormat($p_special_price, $p_format);
			}
			else
			{
				$t_price_html .= $this->v_coo_language_text_manager->get_text('new_special_price') . 
								 $this->checkAttributes($p_products_id) . $this->xtcFormat($p_special_price, $p_format);
			}
			
			if($p_return_array == 0)
			{
				$this->dataCache->set_data($cacheKey, $t_price_html);

				return $this->dataCache->get_data($cacheKey);
			}
			else
			{
				$t_price_array = array();
				$t_price_array['formated'] = $t_price_html;
				$t_price_array['plain'] = $p_special_price;
				
				$this->dataCache->set_data($cacheKey, $t_price_array);

				return $this->dataCache->get_data($cacheKey);
			}
		}
		else
		{
			$t_price = round($p_special_price, $this->currencies[$this->actualCurr]['decimal_places']);
			
			$this->dataCache->set_data($cacheKey, $t_price);

			return $this->dataCache->get_data($cacheKey);
		}
	}

	public function xtcFormatSpecialGraduated($p_products_id, $p_price, $p_old_price, $p_format, $p_return_array = 0)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_price = $p_price;
		
		if($p_old_price == 0)
		{
			$this->dataCache->set_data($cacheKey, $this->xtcFormat($p_price, $p_format, 0, false, $p_return_array));

			return $this->dataCache->get_data($cacheKey);
		}

		$t_discount = $this->xtcCheckDiscount($p_products_id);
		
		if($t_discount)
		{
			$t_price -= $t_price / 100 * $t_discount;
		}
		
		if($t_price != $p_old_price && $t_discount)
		{
			$this->dataCache->set_data($cacheKey, $this->xtcFormatSpecialDiscount($p_products_id, $t_discount, $p_price, $p_format, $p_return_array));

			return $this->dataCache->get_data($cacheKey);
		}

		if($p_format)
		{
			if($t_price < $p_old_price)
			{
				$t_price_html = '';

				if(gm_get_conf('SHOW_OLD_GROUP_PRICE') == '1')
				{
					$t_price_html .= '<span class="productOldPrice">' . $this->v_coo_language_text_manager->get_text('old_group_price') . $this->xtcFormat($p_old_price, $p_format) . '</span><br />';
				}

				$t_price_html .= $this->v_coo_language_text_manager->get_text('new_group_price') . $this->checkAttributes($p_products_id) . $this->xtcFormat($t_price, $p_format);
			}
			else
			{
				$t_price_html = $this->checkAttributes($p_products_id) . $this->xtcFormat($t_price, $p_format);
			}
			
			if($p_return_array == 0)
			{
				$this->dataCache->set_data($cacheKey, $t_price_html);

				return $this->dataCache->get_data($cacheKey);
			}
			else
			{
				$t_price_array = array();
				$t_price_array['formated'] = $t_price_html;
				$t_price_array['plain'] = $t_price;
				
				$this->dataCache->set_data($cacheKey, $t_price_array);

				return $this->dataCache->get_data($cacheKey);
			}
		}
		else
		{
			$t_price = round($t_price, $this->currencies[$this->actualCurr]['decimal_places']);
			
			$this->dataCache->set_data($cacheKey, $t_price);

			return $this->dataCache->get_data($cacheKey);
		}
	}

	public function get_decimal_places($p_currency_code)
	{
		$t_decimal_places = $this->currencies[$p_currency_code]['decimal_places'];
		
		return $t_decimal_places;
	}
	
	public function get_properties_combi_price($p_combis_id, $p_tax_class_id = 0, $p_consider_currency = true)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));

		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_properties_price = 0;

		if(empty($p_combis_id) === false)
		{
			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			
			$t_properties_price = $coo_properties_control->get_properties_combis_price($p_combis_id);
			
			if($this->cStatus['customers_status_show_price_tax'] == '1')
			{
				if((int)$p_tax_class_id > 0)
				{
					$t_tax_rate = $this->TAX[$p_tax_class_id];
					$t_properties_price = $this->xtcAddTax($t_properties_price, $t_tax_rate, $p_consider_currency);
				}
			}
			elseif($p_consider_currency)
			{
				$t_properties_price = $this->xtcCalculateCurr($t_properties_price);
			}
		}
		
		$this->dataCache->set_data($cacheKey, $t_properties_price);

		return $this->dataCache->get_data($cacheKey);
	}
	
	public function extract_combis_id($p_baskets_products_id)
	{
		$cacheKey = $this->_generateCacheKey(__METHOD__ . serialize(func_get_args()));
		
		if($this->dataCache->key_exists($cacheKey))
		{
			return $this->dataCache->get_data($cacheKey);
		}

		$t_combis_id = 0;
		
		$coo_properties_control = MainFactory::create_object('PropertiesControl');
		if(strpos($p_baskets_products_id, $coo_properties_control->v_id_seperator) !== false)
		{
			$t_combis_id = $coo_properties_control->extract_combis_id($p_baskets_products_id);
		}
				
		$this->dataCache->set_data($cacheKey, $t_combis_id);

		return $this->dataCache->get_data($cacheKey);
	}


	protected function _generateCacheKey($key)
	{
		return md5($this->actualCurr . $this->actualGroup . $key);
	}
}