<?php
/* --------------------------------------------------------------
  PrintProductInfoContentView.inc.php 2014-11-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
  (c) 2003	 nextcommerce (print_product_info.php,v 1.16 2003/08/25); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: print_product_info.php 1282 2005-10-03 19:39:36Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');
require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

class PrintProductInfoContentView extends ContentView
{
	protected $languages_id = 2;
	protected $language;
	protected $customers_status_show_price;
	protected $customers_status_show_price_tax;
	protected $customers_status_discount;
	protected $product_id;
	protected $coo_xtc_price;
	protected $coo_main;
	protected $coo_properties_control;
	protected $properties_combi;
	protected $products_data = array();

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('/module/print_product_info.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['language']							= array('type' => 'string');
		$this->validation_rules_array['customers_status_show_price']		= array('type' => 'string');
		$this->validation_rules_array['customers_status_discount']			= array('type' => 'double');
		$this->validation_rules_array['customers_status_show_price_tax']	= array('type' => 'int');
		$this->validation_rules_array['languages_id']						= array('type' => 'int');
		$this->validation_rules_array['product_id']							= array('type' => 'int');
		$this->validation_rules_array['coo_xtc_price']						= array('type' => 'object',
																					'object_type' => 'xtcPrice');
		$this->validation_rules_array['coo_main']							= array('type' => 'object',
																					'object_type' => 'main');
		$this->validation_rules_array['coo_properties_control']				= array('type' => 'object',
																					'object_type' => 'PropertiesControl');
		$this->validation_rules_array['properties_combi']					= array('type' => 'array');
		$this->validation_rules_array['products_data']						= array('type' => 'array');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('product_id', 'coo_xtc_price', 'coo_main'));
		if(empty($t_uninitialized_array))
		{
			$this->get_data();
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function get_data()
	{
		$this->get_product_data();
		$this->get_properties_combi();
		$this->get_products_price();
		$this->get_attributes();
	}
	
	protected function get_product_data()
	{
		$t_product_info_query = 'SELECT
									*
								FROM
									' . TABLE_PRODUCTS . ' p,
									' . TABLE_PRODUCTS_DESCRIPTION . ' pd
								WHERE
									p.products_status = "1"
									AND p.products_id = "' . $this->product_id . '"
									AND pd.products_id = p.products_id
									AND pd.language_id = "' . $this->languages_id . '"';
		$t_product_info_result = xtc_db_query($t_product_info_query);
		$this->products_data = xtc_db_fetch_array($t_product_info_result);
	}

	protected function get_properties_combi()
	{
		$this->coo_properties_control = MainFactory::create_object('PropertiesControl');
		$t_cheapest_combi_array = $this->coo_properties_control->get_cheapest_combi($this->product_id, $this->languages_id);

		if(isset($t_cheapest_combi_array['products_properties_combis_id']) && empty($t_cheapest_combi_array['products_properties_combis_id']) === false)
		{
			$this->properties_combi = $t_cheapest_combi_array;
		}
	}

	protected function get_products_price()
	{
		$t_products_price = $this->coo_xtc_price->xtcGetPrice($this->products_data['products_id'], $format = true, 1, $this->products_data['products_tax_class_id'], $this->products_data['products_price'], 1, 0, true, true, $this->properties_combis_id);
		$this->products_data['products_price_formatted'] = $t_products_price['formated'];
		$this->products_data['products_price_plain'] = $t_products_price['plain'];
	}

	protected function get_attributes()
	{
		$t_query = 'SELECT
						count(*) as total
					FROM
						' . TABLE_PRODUCTS_OPTIONS . ' popt,
						' . TABLE_PRODUCTS_ATTRIBUTES . ' patrib
					WHERE
						patrib.products_id = "' . $this->product_id . '"
						AND patrib.options_id = popt.products_options_id
						AND popt.language_id = "' . $this->languages_id . '"';
		$t_result = xtc_db_query($t_query);
		$t_attributes_row = xtc_db_fetch_array($t_result);

		if($t_attributes_row['total'] > 0)
		{
			$t_options_names_query = 'SELECT DISTINCT
										popt.products_options_id,
										popt.products_options_name
									FROM
										' . TABLE_PRODUCTS_OPTIONS . ' popt,
										' . TABLE_PRODUCTS_ATTRIBUTES . ' patrib
									WHERE
										patrib.products_id = "' . $this->product_id . '"
										AND patrib.options_id = popt.products_options_id
										AND popt.language_id = "' . $this->languages_id . '"
									ORDER BY
										popt.products_options_name';
			$t_options_names_result = xtc_db_query($t_options_names_query);
			while($t_options_names_row = xtc_db_fetch_array($t_options_names_result))
			{
				$t_option_values_name_query = 'SELECT
													pov.products_options_values_id,
													pov.products_options_values_name,
													pa.options_values_price,
													pa.price_prefix,
													pa.attributes_stock,
													pa.attributes_model
												FROM
													' . TABLE_PRODUCTS_ATTRIBUTES . ' pa,
													' . TABLE_PRODUCTS_OPTIONS_VALUES . ' pov
												WHERE
													pa.products_id = "' . $this->product_id . '"
													AND pa.options_id = "' . $t_options_names_row['products_options_id'] . '"
													AND pa.options_values_id = pov.products_options_values_id
													AND pov.language_id = "' . $this->languages_id . '"
												ORDER BY
													pa.sortorder';
				$t_option_values_name_result = xtc_db_query($t_option_values_name_query);
				while($t_option_values_row = xtc_db_fetch_array($t_option_values_name_result))
				{
					$this->products_data['attributes'][] = array('GROUP' => $t_options_names_row['products_options_name'], 'NAME' => $t_option_values_row['products_options_values_name']);

					if($t_option_values_row['options_values_price'] != '0')
					{
						if($this->customers_status_show_price_tax == 1)
						{
							$t_option_values_row['options_values_price'] = xtc_add_tax($t_option_values_row['options_values_price'], $this->coo_xtc_price->TAX[$this->products_data['products_tax_class_id']]);
						}
						if($this->customers_status_show_price == 1)
						{
							$this->products_data['attributes'][sizeof($this->products_data['attributes']) - 1]['NAME'] .= ' (' . $t_option_values_row['price_prefix'] . $this->coo_xtc_price->xtcFormat($t_option_values_row['options_values_price'], true, 0, true) . ')';
						}
					}
				}
			}
		}
	}
	
	protected function add_data()
	{
		$this->content_array['language'] = $this->language;
		$this->add_product_data();
		$this->add_product_image();
		$this->add_product_tax_info();
		$this->add_shipping_data();
		$this->add_discount();
		$this->add_vpe();
		$this->add_more_images();
		if(isset($this->products_data['attributes']))
		{
			$this->content_array['module_content'] = $this->products_data['attributes'];
		}
	}
	
	protected function add_product_data()
	{
		$this->content_array['PRODUCTS_NAME'] = $this->products_data['products_name'];
		$this->content_array['PRODUCTS_EAN'] = $this->products_data['products_ean'];
		$this->content_array['PRODUCTS_QUANTITY'] = $this->products_data['products_quantity'];
		$this->content_array['PRODUCTS_WEIGHT'] = $this->products_data['products_weight'];
		$this->content_array['PRODUCTS_STATUS'] = $this->products_data['products_status'];
		$this->content_array['PRODUCTS_ORDERED'] = $this->products_data['products_ordered'];
		$this->content_array['PRODUCTS_MODEL'] = $this->products_data['products_model'];
		$this->content_array['PRODUCTS_DESCRIPTION'] = preg_replace('!(.*?)\[TAB:(.*?)\](.*?)!is', "$1$3", $this->products_data['products_description']);
		$this->content_array['PRODUCTS_PRICE'] = $this->products_data['products_price_formatted'];
	}
	
	protected function add_product_image()
	{
		$t_product_image = '';
		if($this->products_data['products_image'] != '')
		{
			$t_product_image = DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $this->products_data['products_image'];
		}
		$this->content_array['PRODUCTS_IMAGE'] = $t_product_image;
	}
	
	protected function add_product_tax_info()
	{
		if($this->customers_status_show_price != 0)
		{
			$tax_rate = $this->coo_xtc_price->TAX[$this->products_data['products_tax_class_id']];
			$tax_info = $this->coo_main->getTaxInfo($tax_rate);
			$this->content_array['PRODUCTS_TAX_INFO'] = $tax_info;
		}
	}
	
	protected function add_shipping_data()
	{
		if(ACTIVATE_SHIPPING_STATUS == 'true')
		{
			$this->content_array['SHIPPING_NAME'] = $this->coo_main->getShippingStatusName($this->products_data['products_shippingtime']);
			$shipping_status_image = $this->coo_main->getShippingStatusImage($this->products_data['products_shippingtime']);
			if($shipping_status_image != '')
			{
				$this->content_array['SHIPPING_IMAGE'] = $shipping_status_image;
			}
		}
		if(SHOW_SHIPPING == 'true')
		{
			$main = new main();
			
			if($main->checkFreeShippingByProductId($this->products_data['products_id']))
			{
				$this->content_array['PRODUCTS_SHIPPING_LINK'] = $main->getShippingLink(false, $this->products_data['products_id']);
			}
			else
			{
				$this->content_array['PRODUCTS_SHIPPING_LINK'] = ' ' . SHIPPING_EXCL . '<a href="javascript:newWin=void(window.open(\'' . xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . SHIPPING_INFOS) . '\', \'popup2\', \'toolbar=0, width=640, height=600\'))"> ' . SHIPPING_COSTS . '</a>';
			}
		}
	}
	
	protected function add_discount()
	{
		if($this->customers_status_discount != 0)
		{
			$t_discount = $this->customers_status_discount;
			if((double)$this->products_data['products_discount_allowed'] < $this->customers_status_discount)
			{
				$t_discount = (double)$this->products_data['products_discount_allowed'];
			}
			if($t_discount != 0)
			{
				$this->content_array['PRODUCTS_DISCOUNT'] = $t_discount . '%';
			}
		}
	}
	
	protected function add_vpe()
	{
		if($this->properties_combi != false)
		{
			if($this->products_data['products_vpe_status'] == 1 && $this->properties_combi['vpe_value'] != 0 && $this->properties_combi['products_vpe_id'] != 0 && $this->products_data['products_price_plain'] > 0)
			{
				$this->set_content_data('PRODUCTS_VPE', $this->coo_xtc_price->xtcFormat($this->products_data['products_price_plain'] * (1 / $this->properties_combi['vpe_value']), true) . TXT_PER . xtc_get_vpe_name($this->properties_combi['products_vpe_id']));
			}
		}
		else
		{
			if($this->products_data['products_vpe_status'] == 1 && $this->products_data['products_vpe_value'] != 0.0 && $this->products_data['products_price_plain'] > 0)
			{
				$this->set_content_data('PRODUCTS_VPE', $this->coo_xtc_price->xtcFormat($this->products_data['products_price_plain'] * (1 / $this->products_data['products_vpe_value']), true) . TXT_PER . xtc_get_vpe_name($this->products_data['products_vpe']));
			}
		}
	}
	
	protected function add_more_images()
	{
		$t_more_images = xtc_get_products_mo_images($this->products_data['products_id']);

		// BOF GM
		if(is_array($t_more_images))
		{
			foreach($t_more_images as $img)
			{
				$gm_products_more_img[] = DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $img['image_name'];
			}
		}
		$this->content_array['GM_PRODUCTS_MORE_IMG'] = $gm_products_more_img;
	}
}