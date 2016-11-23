<?php
/* --------------------------------------------------------------
  ShoppingCartDropdownBoxContentView.inc.php 2016-09-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(best_sellers.php,v 1.20 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (best_sellers.php,v 1.10 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: best_sellers.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_FS_CATALOG . 'inc/xtc_get_countries.inc.php');

class ShoppingCartDropdownBoxContentView extends ContentView
{
	protected $coo_paypal;
	protected $coo_cart;
	protected $language_id;
	protected $language_code;
	protected $customers_status_ot_discount_flag;
	protected $customers_status_ot_discount;
	protected $customers_status_show_price_tax;
	protected $customers_status_add_tax_ot;
	protected $customers_status_show_price;
	protected $customers_status_payment_unallowed;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_cart_dropdown.html');
		$this->set_caching_enabled(false);
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_paypal']							= array('type' => 'object',
																					'object_type' => 'GMPayPal');
		$this->validation_rules_array['coo_cart']							= array('type' => 'object',
																					'object_type' => 'shoppingCart');
		$this->validation_rules_array['language_id']						= array('type' => 'int');
		$this->validation_rules_array['language_code']						= array('type' => 'string');
		$this->validation_rules_array['customers_status_ot_discount_flag']	= array('type' => 'int');
		$this->validation_rules_array['customers_status_ot_discount']		= array('type' => 'double');
		$this->validation_rules_array['customers_status_show_price_tax']	= array('type' => 'int');
		$this->validation_rules_array['customers_status_add_tax_ot']		= array('type' => 'int');
		$this->validation_rules_array['customers_status_show_price']		= array('type' => 'int');
		$this->validation_rules_array['customers_status_payment_unallowed']	= array('type' => 'string');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_cart', 'language_id', 'language_code'));
		if(empty($t_uninitialized_array))
		{
			$this->content_array['empty'] = 'true';
			$this->add_total();
			if($this->coo_cart->count_contents() > 0)
			{
				$this->add_data();
				$this->content_array['empty'] = 'false';
				$this->content_array['productsCount'] = $this->coo_cart->count_products();
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}

	protected function add_data()
	{
		// check if data is already added
		if(!isset($this->content_array['SHIPPING_INFO']))
		{
			$this->add_products();
			$this->add_tax();
			$this->content_array['SHIPPING_INFO'] = $this->get_shipping_info();
		}
	}

	protected function add_products()
	{
		global $xtPrice;
		$t_products = $this->coo_cart->get_products();
		$this->content_array['products'] = array();

		$t_price = '';
		$this->content_array['customer_status_allow_checkout'] = $_SESSION['customers_status']['customers_status_show_price'];
		if(sizeof($t_products) > 0)
		{
			if($_SESSION['customers_status']['customers_status_show_price'] != '1')
			{
				$t_price = '--';
				$this->content_array['customer_status_allow_checkout_info'] = NOT_ALLOWED_TO_SEE_PRICES;
			}
		}

		for($i = 0, $n = sizeof($t_products); $i < $n; $i ++)
		{
			$t_image = '';
			if($t_products[$i]['image'] != '')
			{
				$t_image = DIR_WS_THUMBNAIL_IMAGES . $t_products[$i]['image'];
			}

			$url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($t_products[$i]['id'], $t_products[$i]['name']));
			
			// Customizer product
			if(strpos($t_products[$i]['id'], '}0') !== false)
			{
				$url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($t_products[$i]['id'], $t_products[$i]['name']) . '&no_boost=1');
			}
			
			$price = (double)$t_products[$i]['quantity'] * (double)$t_products[$i]['price'];
			
			$this->content_array['products'][] = array(
				'QTY' => gm_convert_qty($t_products[$i]['quantity'], false),
				'LINK' => $url,
				'NAME' => $t_products[$i]['name'],
				'IMAGE' => $t_image,
				'PRICE' => (strlen(trim($t_price)) > 0 ? $t_price : $xtPrice->xtcFormat($price, true)),
				'VPE' => $t_products[$i]['vpe'],
				'UNIT' => $t_products[$i]['unit_name']
			);
		}
		$this->content_array['PRODUCTS'] = sizeof($this->content_array['products']);
	}

	protected function add_total()
	{
		global $xtPrice;
		$total = $this->coo_cart->show_total();
		if($this->customers_status_ot_discount_flag == 1 && $this->customers_status_ot_discount != 0)
		{
			if($this->customers_status_show_price_tax == 0 && $this->customers_status_add_tax_ot == 1)
			{
				$price = $total - $this->coo_cart->show_tax(false);
			}
			else
			{
				$price = $total;
			}
			$discount = $xtPrice->xtcGetDC($price, $this->customers_status_ot_discount);
		}

		if($this->customers_status_show_price == '1')
		{
			if($this->customers_status_show_price_tax == 0 && $this->customers_status_add_tax_ot == 0)
			{
				$total-=$discount;
			}
			if($this->customers_status_show_price_tax == 0 && $this->customers_status_add_tax_ot == 1)
			{
				$total = $total - $this->coo_cart->show_tax(false) - $discount;
			}
			if($this->customers_status_show_price_tax == 1)
			{
				$total-=$discount;
			}
			$this->content_array['TOTAL'] = $xtPrice->xtcFormat($total, true);
		}
	}

	protected function add_tax()
	{
		//GM_MOD:
		if(gm_get_conf('TAX_INFO_TAX_FREE') == 'true')
		{
			$gm_cart_tax_info = GM_TAX_FREE . '<br />';
		}
		else
		{
			$gm_cart_tax_info = $this->coo_cart->show_tax();
		}
		//GM_MOD:
		$this->content_array['UST'] = $gm_cart_tax_info;
	}

	protected function get_shipping_info()
	{
		global $main;

		$t_shipping_info = '';

		if(SHOW_SHIPPING == 'true')
		{
			$t_shipping_info = $main->getShippingLink(true);
		}

		return $t_shipping_info;
	}
}