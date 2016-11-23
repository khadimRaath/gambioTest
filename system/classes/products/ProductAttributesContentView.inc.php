<?php
/* --------------------------------------------------------------
  ProductAttributesContentView.inc.php 2014-11-11 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
  (c) 2003      nextcommerce (product_info.php,v 1.46 2003/08/25); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_attributes.php 1255 2005-09-28 15:10:36Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
  New Attribute Manager v4b                            Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
  Cross-Sell (X-Sell) Admin 1                          Autor: Joshua Dechant (dreamscape)
  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
include_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

/**
 * Class ProductAttributesContentView
 */
class ProductAttributesContentView extends ContentView
{
	protected $coo_product;
	protected $coo_xtc_price;
	protected $language_id;

	public function __construct()
	{
		parent::__construct();
		$this->set_flat_assigns(true);
	}
	

	public function prepare_data()
	{
		$this->build_html = false;
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_product', 'language_id'));
		if(empty($t_uninitialized_array))
		{
			$this->coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	
	protected function add_data()
	{
		$this->add_attributes();

		$this->content_array['PRODUCTS_ID'] = $this->coo_product->data['products_id'];
		$this->content_array['PRICE_STATUS'] = $this->coo_product->data['gm_price_status'];
		$this->content_array['GM_HIDE_OUT_OF_STOCK'] = GM_SET_OUT_OF_STOCK_ATTRIBUTES;
		$this->content_array['GM_SHOW_STOCK'] = GM_SET_OUT_OF_STOCK_ATTRIBUTES_SHOW;
		$this->content_array['GM_STOCK_TEXT_BEFORE'] = GM_ATTR_STOCK_TEXT_BEFORE;
		$this->content_array['GM_STOCK_TEXT_AFTER'] = GM_ATTR_STOCK_TEXT_AFTER;
	}
	
	
	protected function add_attributes()
	{
		if($this->coo_product->getAttributesCount() > 0)
		{
			$t_query = 'SELECT DISTINCT
							popt.products_options_id,
							popt.products_options_name
						FROM
							' . TABLE_PRODUCTS_OPTIONS . ' popt,
							' . TABLE_PRODUCTS_ATTRIBUTES . ' patrib
						WHERE
							patrib.products_id = "' . $this->coo_product->data['products_id'] . '"
							AND patrib.options_id = popt.products_options_id
							AND popt.language_id = "' . $this->language_id . '"
						ORDER BY
							popt.products_options_name';
			$t_result = xtc_db_query($t_query);
			
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$this->content_array['options'][] = $this->get_attribute_data($t_row);
			}
		}
	}


	/**
	 * @param array $p_row
	 *
	 * @return array
	 */
	protected function get_attribute_data(array $p_row)
	{
		$t_attribute_data = $this->get_attribute($p_row);
		$t_attribute_data['DATA'] = $this->get_attribute_options($p_row);
		return $t_attribute_data;
	}


	/**
	 * @param array $p_row
	 *
	 * @return array
	 */
	protected function get_attribute(array $p_row)
	{
		$t_attribute_data_array = array();
		$t_attribute_data_array['NAME'] = $p_row['products_options_name'];
		$t_attribute_data_array['ID'] = $p_row['products_options_id'];
		
		return $t_attribute_data_array;
	}


	/**
	 * @param array $p_row
	 *
	 * @return array
	 */
	protected function get_attribute_options(array $p_row)
	{
		$t_attribute_options_array = array();
		
		$t_attributes_stock_check = '';
		if(GM_SET_OUT_OF_STOCK_ATTRIBUTES == 'true')
		{
			$t_attributes_stock_check = 'AND pa.attributes_stock > 0';
		}
		
		$t_query = 'SELECT
						pov.products_options_values_id,
						pov.products_options_values_name,
						pov.gm_filename,
						pa.attributes_model,
						pa.options_values_price,
						pa.price_prefix,
						pa.attributes_stock,
						pa.attributes_model
					FROM
						' . TABLE_PRODUCTS_ATTRIBUTES . ' pa,
						' . TABLE_PRODUCTS_OPTIONS_VALUES . ' pov
					WHERE
						pa.products_id = "' . $this->coo_product->data['products_id'] . '"
						AND pa.options_id = "' . $p_row['products_options_id'] . '"
						AND pa.options_values_id = pov.products_options_values_id
						AND pov.language_id = "' . $this->language_id . '"
						' . $t_attributes_stock_check . '
					ORDER BY
						pa.sortorder';
		$t_result = xtc_db_query($t_query);
		
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_attribute_options_array[] = $this->get_attribute_options_data($t_row);
		}
		
		return $t_attribute_options_array;
	}


	/**
	 * @param array $p_row
	 *
	 * @return array
	 */
	protected function get_attribute_options_data(array $p_row)
	{
		$this->build_html = true;
		
		$t_attribute_option_data_array = array();
		
		$t_attribute_option_data_array['ID']		= $p_row['products_options_values_id'];
		$t_attribute_option_data_array['TEXT']		= $p_row['products_options_values_name'];
		$t_attribute_option_data_array['MODEL']		= $p_row['attributes_model'];
		$t_attribute_option_data_array['GM_STOCK']	= gm_prepare_number($p_row['attributes_stock'], ',');
		$t_attribute_option_data_array['GM_IMAGE']	= $this->get_attribute_option_image($p_row);
						
		if($_SESSION['customers_status']['customers_status_show_price'] == '1' 
			&& ($this->coo_xtc_price->gm_check_price_status($this->coo_product->data['products_id']) == 0 
				|| 
				($this->coo_xtc_price->gm_check_price_status($this->coo_product->data['products_id']) == 2 
				 && $this->coo_xtc_price->getPprice($this->coo_product->data['products_id']) > 0)))
		{
			$t_price = $this->get_attribute_option_price($p_row);
			
			if($t_price != 0)
			{
				$t_attribute_option_data_array['PRICE']	= $this->coo_xtc_price->xtcFormat($t_price, true);
			}
			
			$t_attribute_option_data_array['FULL_PRICE']	= $this->coo_xtc_price->xtcFormat($this->get_attribute_option_full_price($p_row, $t_price), true);
			$t_attribute_option_data_array['PREFIX']		= $p_row['price_prefix'];
		}
		
		return $t_attribute_option_data_array;
	}


	/**
	 * @param array $p_row
	 *
	 * @return string
	 */
	protected function get_attribute_option_image(array $p_row)
	{
		$t_attribute_option_image = '';
		if(!empty($p_row['gm_filename']))
		{
			$t_attribute_option_image = DIR_WS_IMAGES . 'product_images/attribute_images/' . $p_row['gm_filename'];
		}
		
		return $t_attribute_option_image;
	}


	/**
	 * @param array $p_row
	 *
	 * @return double
	 */
	protected function get_attribute_option_price(array $p_row)
	{
		$t_price = 0;
		
		if($p_row['options_values_price'] != '0.00')
		{
			if($this->coo_product->data['products_tax_class_id'] != 0)
			{
				$t_price = $this->coo_xtc_price->xtcFormat($p_row['options_values_price'], false, $this->coo_product->data['products_tax_class_id']);
			}
			else
			{
				$t_price = $this->coo_xtc_price->xtcFormat($p_row['options_values_price'], false, $this->coo_product->data['products_tax_class_id'], true);
			}
		}
		
		$t_discount = $this->coo_xtc_price->xtcCheckDiscount($this->coo_product->data['products_id']);
		
		if($this->coo_xtc_price->xtcCheckDiscount($this->coo_product->data['products_id']) && $_SESSION['customers_status']['customers_status_discount_attributes'] == 1)
		{
			if($p_row['price_prefix'] == '+')
			{
				$t_price -= $t_price / 100 * $t_discount;
			}
			elseif($p_row['price_prefix'] == '-')
			{
				$t_price += $t_price / 100 * $t_discount;
			}
		}
		
		return $t_price;
	}


	/**
	 * @param array	 	$p_row
	 * @param double	$p_attr_price
	 *
	 * @return double
	 */
	protected function get_attribute_option_full_price(array $p_row, $p_attr_price)
	{
		$t_products_price = $this->coo_xtc_price->xtcGetPrice($this->coo_product->data['products_id'], $format = false, 1, $this->coo_product->data['products_tax_class_id'], $this->coo_product->data['products_price']);

		if($p_row['price_prefix'] == "-")
		{
			$p_attr_price = $p_attr_price * (-1);
		}
		
		return $t_products_price + $p_attr_price;
	}


	/**
	 * @param product $product
	 */
	public function set_coo_product(product $product)
	{
		$this->coo_product = $product;
	}


	/**
	 * @return product
	 */
	public function get_coo_product()
	{
		return $this->coo_product;
	}


	/**
	 * @param xtcPrice $xtcPrice
	 */
	public function set_coo_xtc_price(xtcPrice $xtcPrice)
	{
		$this->coo_xtc_price = $xtcPrice;
	}


	/**
	 * @return xtcPrice
	 */
	public function get_coo_xtc_price()
	{
		return $this->coo_xtc_price;
	}


	/**
	 * @param int $p_language_id
	 */
	public function set_language_id($p_language_id)
	{
		$this->language_id = (int)$p_language_id;
	}


	/**
	 * @return int
	 */
	public function get_language_id()
	{
		return $this->language_id;
	}
}