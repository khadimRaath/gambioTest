<?php
/* --------------------------------------------------------------
   OrderDetailsWishListContentView.inc.php 2014-11-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
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
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_draw_separator.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_selection_field.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_hidden_field.inc.php');
require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_stock.inc.php');
require_once(DIR_FS_INC . 'xtc_remove_non_numeric.inc.php');
require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');
require_once(DIR_FS_INC . 'xtc_format_price.inc.php');

require_once(DIR_FS_INC . 'get_products_vpe_array.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

/**
 * Class OrderDetailsWishListContentView
 */
class OrderDetailsWishListContentView extends ContentView
{

	protected $products_array = array();

	/** @var PropertiesControl $coo_properties_control */
	protected $coo_properties_control;
	/** @var PropertiesView $coo_properties_view */
	protected $coo_properties_view;
	/** @var GMSEOBoost $gmSEOBoost */
	protected $gmSEOBoost;



	protected $currency;
	protected $customersStatus;
	protected $customersStatusId;


	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/wish_list_order_details.html');
	}



	public function prepare_data()
	{
		$p_products_array = $this->products_array;


		$coo_properties_control = MainFactory::create_object('PropertiesControl');
		$coo_properties_view = MainFactory::create_object('PropertiesView');
		$gmSEOBoost = MainFactory::create_object('GMSEOBoost');

		$coo_xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$coo_main = new main();

		$module_content=array();
		$any_out_of_stock='';
		$mark_stock='';
		$p_products_array_copy = $p_products_array;

		for($i = 0; $i < count($p_products_array); $i++)
		{
			$t_combis_id = $coo_properties_control->extract_combis_id($p_products_array[$i]['id']);

			// check if combis_id is empty
			if($t_combis_id == '')
			{
				// combis_id is empty = article without properties
				if(STOCK_CHECK == 'true')
				{
					$mark_stock = xtc_check_stock($p_products_array[$i]['id'], $p_products_array[$i]['quantity']);
					if($mark_stock)
					{
						$_SESSION['any_out_of_stock'] = 1;
					}
				}
			}

			$image='';
			if($p_products_array[$i]['image']!='')
			{
				$image=DIR_WS_THUMBNAIL_IMAGES.$p_products_array[$i]['image'];
			}

			$gm_products_id = $p_products_array[$i]['id'];
			$gm_products_id = str_replace('{', '_', $gm_products_id);
			$gm_products_id = str_replace('}', '_', $gm_products_id);

			$t_gm_tax_shipping_info = ' ';

			if($_SESSION['customers_status']['customers_status_show_price'] != 0  && ($coo_xtPrice->gm_check_price_status($p_products_array[$i]['id']) == 0 || ($coo_xtPrice->gm_check_price_status($p_products_array[$i]['id']) == 2 && $p_products_array[$i]['price'] > 0)) )
			{
				$t_gm_tax_rate = $coo_xtPrice->TAX[$p_products_array[$i]['tax_class_id']];
				$t_gm_tax_shipping_info .= $coo_main->getTaxInfo($t_gm_tax_rate);

				if($coo_xtPrice->gm_check_price_status($p_products_array[$i]['id']) == 0)
				{
					$t_gm_tax_shipping_info .= $coo_main->getShippingLink(true, $p_products_array[$i]['id']);
				}
			}

			$gm_product_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $p_products_array[$i]['id'] . '&no_boost=1');

			$t_products_weight = $p_products_array[$i]['weight'];

			if(isset($p_products_array[$i]['attributes']))
			{
				reset($p_products_array[$i]['attributes']);

				while(list($option, $value) = each($p_products_array[$i]['attributes']))
				{
					if($p_products_array[$i][$option]['weight_prefix'] == '+')
					{
						$t_products_weight += $p_products_array[$i][$option]['options_values_weight'];
					}
					else if($p_products_array[$i][$option]['weight_prefix'] == '-')
					{
						$t_products_weight -= $p_products_array[$i][$option]['options_values_weight'];
					}
				}
			}

			$t_products_weight = gm_prepare_number($t_products_weight, $coo_xtPrice->currencies[$coo_xtPrice->actualCurr]['decimal_point']);

			$gm_query = xtc_db_query("SELECT gm_show_weight FROM products WHERE products_id='" . $p_products_array[$i]['id'] . "'");
			$gm_array = xtc_db_fetch_array($gm_query);
			if(empty($gm_array['gm_show_weight'])) { $t_products_weight = 0; }

			$module_content[$i] = array (
				'PRODUCTS_NAME'					=> $p_products_array[$i]['name'].$mark_stock,
				'PRODUCTS_QTY'					=> xtc_draw_input_field('cart_quantity[]', gm_convert_qty($p_products_array[$i]['quantity'], false), ' size="2" onblur="gm_qty_is_changed(' . $p_products_array[$i]['quantity'] . ', this.value, \'' . GM_QTY_CHANGED_MESSAGE . '\')"', 'text', true, "gm_cart_data gm_class_input").xtc_draw_hidden_field('products_id[]', $p_products_array[$i]['id'], 'class="gm_cart_data"').xtc_draw_hidden_field('old_qty[]', $p_products_array[$i]['quantity']),
				'TAX_SHIPPING_INFO'					=> $t_gm_tax_shipping_info,
				'PRODUCTS_OLDQTY_INPUT_NAME'	=> 'old_qty[]',
				'PRODUCTS_QTY_INPUT_NAME'		=> 'cart_quantity[]',
				'PRODUCTS_CART_DELETE_INPUT_NAME' => 'cart_delete[]',
				'PRODUCTS_QTY_VALUE'            => gm_convert_qty($p_products_array[$i]['quantity'], false),
				'PRODUCTS_ID_INPUT_NAME'       	=> 'products_id[]',
				'PRODUCTS_ID_EXTENDED'			=> $p_products_array[$i]['id'],

				'PRODUCTS_MODEL'				=> $p_products_array[$i]['model'],
				'SHOW_PRODUCTS_MODEL'			=> SHOW_PRODUCTS_MODEL,
				'PRODUCTS_SHIPPING_TIME'		=> $p_products_array[$i]['shipping_time'],
				'PRODUCTS_TAX'					=> (double)$p_products_array[$i]['tax'],
				'PRODUCTS_IMAGE'				=> $image,
				'IMAGE_ALT'						=> $p_products_array[$i]['name'],
				'BOX_DELETE'					=> xtc_draw_checkbox_field('cart_delete[]', $p_products_array[$i]['id'], false, 'class="wishlist_checkbox" id="gm_delete_product_' . $gm_products_id . '"'),
				'PRODUCTS_LINK'					=> $gm_product_link,
				'PRODUCTS_PRICE'				=> $coo_xtPrice->xtcFormat($p_products_array[$i]['price'] * $p_products_array[$i]['quantity'], true),
				'PRODUCTS_SINGLE_PRICE'			=> $coo_xtPrice->xtcFormat($p_products_array[$i]['price'], true),
				'PRODUCTS_SHORT_DESCRIPTION'	=> strip_tags(xtc_get_short_description($p_products_array[$i]['id'])),
				'ATTRIBUTES'					=> '',
				'PROPERTIES'					=> '',
				'GM_WEIGHT'						=> $t_products_weight,
				'BUY_NOW'						=> '<br><a href="' . xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=buy_now&BUYproducts_id=' . $p_products_array[$i]['id'], 'NONSSL') . '">' . xtc_image_button('button_buy_now.gif', TEXT_BUY . TEXT_NOW),
				'PRODUCTS_ID'					=> $gm_products_id,
				'UNIT'							=> $p_products_array[$i]['unit_name']);
			#properties
			if($t_combis_id != '') {
				$module_content[$i]['PROPERTIES'] = $coo_properties_view->get_order_details_by_combis_id($t_combis_id, 'cart');

				$coo_products = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $p_products_array[$i]['id']) ));
				$use_properties_combis_quantity = $coo_products->get_data_value('use_properties_combis_quantity');

				$properties_mark_stock = '';

				if($use_properties_combis_quantity == 1){
					// check article quantity
					$properties_mark_stock = xtc_check_stock($p_products_array[$i]['id'], $p_products_array[$i]['quantity']);
					if ($properties_mark_stock){
						$_SESSION['any_out_of_stock'] = 1;
					}
				}else if(($use_properties_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true') || $use_properties_combis_quantity == 2){
					// check combis quantity
					$t_properties_stock = $coo_properties_control->get_properties_combis_quantity($t_combis_id);
					if($t_properties_stock < $p_products_array[$i]['quantity'])
					{
						$_SESSION['any_out_of_stock'] = 1;
						$properties_mark_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
					}
				}

				$module_content[$i]['PRODUCTS_NAME'] = $p_products_array[$i]['name'].$properties_mark_stock;

				$t_weight = $coo_properties_control->get_properties_combis_weight($t_combis_id);

				if($coo_products->get_data_value('use_properties_combis_weight') == 1){
					$module_content[$i]['GM_WEIGHT'] = gm_prepare_number($t_weight, $coo_xtPrice->currencies[$coo_xtPrice->actualCurr]['decimal_point']);
				}else{
					$module_content[$i]['GM_WEIGHT'] = gm_prepare_number($t_weight+$p_products_array[$i]['weight'], $coo_xtPrice->currencies[$coo_xtPrice->actualCurr]['decimal_point']);
				}

				if($coo_products->get_data_value('use_properties_combis_shipping_time') == 1){
					$module_content[$i]['PRODUCTS_SHIPPING_TIME'] = $coo_properties_control->get_properties_combis_shipping_time($t_combis_id);
				}else{
					$coo_main = new main();
					$module_content[$i]['PRODUCTS_SHIPPING_TIME'] = $coo_main->getShippingStatusName($coo_products->get_data_value('products_shippingtime'));
				}

				$t_combi_model = $coo_properties_control->get_properties_combis_model($t_combis_id);

				$module_content[$i]['PRODUCTS_MODEL'] = $p_products_array[$i]['model'];

				if(APPEND_PROPERTIES_MODEL == "true") {
					// Artikelnummer (Kombi) an Artikelnummer (Artikel) anhängen
					if($module_content[$i]['PRODUCTS_MODEL'] != '' && $t_combi_model != ''){
						$module_content[$i]['PRODUCTS_MODEL'] = $module_content[$i]['PRODUCTS_MODEL'] .'-'. $t_combi_model;
					}else if($t_combi_model != ''){
						$module_content[$i]['PRODUCTS_MODEL'] = $t_combi_model;
					}
				}else{
					// Artikelnummer (Artikel) durch Artikelnummer (Kombi) ersetzen
					if($t_combi_model != ''){
						$module_content[$i]['PRODUCTS_MODEL'] = $t_combi_model;
					}
				}
			}

			// Product options names
			$attributes_exist = ((isset($p_products_array[$i]['attributes'])) ? 1 : 0);

			if($attributes_exist == 1)
			{
				reset($p_products_array[$i]['attributes']);

				while(list($option, $value) = each($p_products_array[$i]['attributes']))
				{
					if(ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true' && $value != 0)
					{
						$attribute_stock_check = xtc_check_stock_attributes($p_products_array[$i][$option]['products_attributes_id'], $p_products_array[$i]['quantity']);
						if($attribute_stock_check)
						{
							$_SESSION['any_out_of_stock']=1;
						}
					}
					// combine all customizer products for checking stock
					elseif(STOCK_CHECK == 'true' && $value == 0 && $mark_stock == '')
					{
						preg_match('/(.*)\{[\d]+\}0$/', $p_products_array[$i]['id'], $t_matches_array);

						if(isset($t_matches_array[1]))
						{
							$t_product_identifier = $t_matches_array[1];
						}

						$t_quantities = 0;

						foreach($p_products_array_copy as $t_product_data_array)
						{
							preg_match('/(.*)\{[\d]+\}0$/', $t_product_data_array['id'], $t_matches_array);

							if(isset($t_matches_array[1]) && $t_matches_array[1] == $t_product_identifier)
							{
								$t_quantities += $t_product_data_array['quantity'];
							}
						}

						$t_mark_stock = xtc_check_stock($p_products_array[$i]['id'], $t_quantities);

						if($t_mark_stock !== '')
						{
							$_SESSION['any_out_of_stock'] = 1;
							$module_content[$i]['PRODUCTS_NAME'] .= $t_mark_stock;
						}
					}

					$module_content[$i]['ATTRIBUTES'][]=array(
						'ID' =>$p_products_array[$i][$option]['products_attributes_id'],
						'MODEL'=>$p_products_array[$i][$option]['products_options_model'],
						'NAME' => $p_products_array[$i][$option]['products_options_name'],
						'VALUE_NAME' => $p_products_array[$i][$option]['products_options_values_name'].$attribute_stock_check
					);

					// BOF GM_MOD GX-Customizer:
					require(DIR_FS_CATALOG . 'gm/modules/gm_gprint_order_details_wishlist.php');
				}
			}

			$module_content[$i]['PRODUCTS_VPE_ARRAY'] = get_products_vpe_array($p_products_array[$i]['id'], $p_products_array[$i]['price'], array(), $t_combis_id);
		}

		$total_content='';
		if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == '1'
			&& $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00')
		{
			$discount = xtc_recalculate_price($_SESSION['cart']->show_total(), $_SESSION['customers_status']['customers_status_ot_discount']);
			$total_content= $_SESSION['customers_status']['customers_status_ot_discount'] . ' % ' . SUB_TITLE_OT_DISCOUNT . ' -' . xtc_format_price($discount, $price_special=1, $calculate_currencies=false) .'<br />';
		}

		if($_SESSION['customers_status']['customers_status_show_price'] == '1')
		{
			$total_content.= SUB_TITLE_SUB_TOTAL . $coo_xtPrice->xtcFormat($_SESSION['cart']->show_total(),true) . '<br />';
		}
		else
		{
			$total_content.= TEXT_INFO_SHOW_PRICE_NO . '<br />';
		}
		// display only if there is an ot_discount
		if ($customer_status_value['customers_status_ot_discount'] != 0)
		{
			$total_content.= TEXT_CART_OT_DISCOUNT . $customer_status_value['customers_status_ot_discount'] . '%';
		}

		$this->set_content_data('GM_THUMBNAIL_WIDTH', PRODUCT_IMAGE_THUMBNAIL_WIDTH);

		$this->set_content_data('TOTAL_CONTENT',$total_content);
		$this->set_content_data('module_content',$module_content);


		$t_html_output = $this->build_html();

		return $t_html_output;
	}


	/**
	 * @return array
	 */
	public function getProductsArray()
	{
		return $this->products_array;
	}


	/**
	 * @param array $products_array
	 */
	public function setProductsArray(array $products_array)
	{
		$this->products_array = $products_array;

	}



}
