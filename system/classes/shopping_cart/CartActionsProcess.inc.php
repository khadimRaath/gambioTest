<?php
/* --------------------------------------------------------------
   CartActionsProcess.inc.php 2016-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(application_top.php,v 1.273 2003/05/19); www.oscommerce.com
   (c) 2003         nextcommerce (application_top.php,v 1.54 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: cart_actions.php 1298 2005-10-09 13:14:44Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class CartActionsProcess extends DataProcessing
{
	protected $turbo_buy_now;
	protected $show_cart;
	protected $show_details;
	protected $php_self;
	protected $coo_seo_boost;
	protected $coo_order;
	protected $remote_address;
	protected $coo_price;
	protected $coo_gprint_product_manager;
	protected $customer_id;
	protected $coo_wish_list;
	protected $coo_cart;
	protected $coo_gprint_wish_list;
	protected $coo_gprint_cart;
	protected $info_message;
	protected $customers_status_id;
	protected $customers_fsk18;
	protected $customers_fsk18_display;

	public function __construct()
	{
		parent::__construct();
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['customer_id'] = 					array('type'		=> 'int');
		$this->validation_rules_array['customers_status_id'] = 			array('type' 		=> 'int');
		$this->validation_rules_array['customers_fsk18'] = 				array('type' 		=> 'int');
		$this->validation_rules_array['customers_fsk18_display'] = 		array('type' 		=> 'int');
		$this->validation_rules_array['turbo_buy_now'] = 				array('type' 		=> 'bool');
		$this->validation_rules_array['show_cart'] = 					array('type' 		=> 'bool');
		$this->validation_rules_array['show_details'] =					array('type' 		=> 'bool');
		$this->validation_rules_array['php_self'] = 					array('type' 		=> 'string');
		$this->validation_rules_array['remote_address'] =				array('type' 		=> 'string');
		$this->validation_rules_array['info_message'] = 				array('type' 		=> 'string');
		$this->validation_rules_array['coo_price'] = 					array('type' 		=> 'object',
																			  'object_type' => 'xtcPrice');
		$this->validation_rules_array['coo_gprint_product_manager'] =	array('type' 		=> 'object',
																			  'object_type' => 'GMGPrintProductManager');
		$this->validation_rules_array['coo_wish_list'] = 				array('type' 		=> 'object',
																			  'object_type' => 'wishList');
		$this->validation_rules_array['coo_cart'] = 					array('type' 		=> 'object',
																			 'object_type' 	=> 'shoppingCart');
		$this->validation_rules_array['coo_gprint_wish_list'] = 		array('type' 		=> 'object',
																			 'object_type' 	=> 'GMGPrintWishlistManager');
		$this->validation_rules_array['coo_gprint_cart'] = 				array('type' 		=> 'object',
																			  'object_type' => 'GMGPrintCartManager');
		$this->validation_rules_array['coo_seo_boost'] = 				array('type' 		=> 'object',
																			  'object_type' => 'GMSEOBoost');
		$this->validation_rules_array['coo_order'] = 					array('type' 		=> 'object',
																			  'object_type' => 'order');
	}

	public function proceed($p_action = NULL)
	{
		// Shopping cart actions
		if(DISPLAY_CART == 'true')
		{
			$this->show_cart = true;
			$goto = FILENAME_SHOPPING_CART;
			$parameters = array
			(
				'action',
				'cPath',
				'products_id',
				'pid'
			);
		}
		else
		{
			$goto = basename($this->php_self);

			// BOF GM_MOD
			if(isset($this->v_data_array['GET']['keywords']))
			{
				$goto = FILENAME_ADVANCED_SEARCH_RESULT;
			}
			// EOF GM_MOD

			if($p_action == 'buy_now')
			{
				$parameters = array
				(
					'action',
					'pid',
					'products_id',
					'BUYproducts_id'
				);
			}
			else
			{
				$parameters = array
				(
					'action',
					'pid',
					'BUYproducts_id',
					'info'
				);
			}
		}

		$this->coo_gprint_product_manager = new GMGPrintProductManager();

		switch($p_action)
		{
			// customer wants to update the product quantity in their shopping cart
			case 'update_product':
				$this->update_product($goto, $parameters);
				break;
				// customer adds a product from the products page
			// BOF GM_MOD
			case 'update_wishlist':
				$this->update_wishlist($goto, $parameters);
				break;
			// EOF GM_MOD
			case 'add_product':
				$this->prepare_gprint_product($goto, $parameters);
				$this->add_product($goto, $parameters);
				break;
			// BOF GM_MOD
			case 'wishlist_to_cart':
				$this->wishlist_to_cart($goto, $parameters);
				break;
			// EOF GM_MOD
			case 'check_gift':
				$this->check_gift($goto, $parameters);
				break;
				// customer wants to add a quickie to the cart (called from a box)
			case 'add_a_quickie':
				$this->add_a_quickie($goto, $parameters);
				break;
				// performed by the 'buy now' button in product listings and review page
			case 'buy_now':
				// BOF GM_MOD
				$this->prepare_buy_now($goto, $parameters);
				if(empty($this->v_redirect_url) == false)
				{
					return;
				}
				$this->buy_now($goto, $parameters);
				// EOF GM_MOD
				break;
			case 'cust_order':
				// BOF GM_MOD
				$this->cust_order($goto, $parameters);
				// EOF GM_MOD
				break;
		}

		if(empty($this->v_redirect_url) == false)
		{
			return;
		}
	}

	protected function update_product(&$p_goto, &$p_parameters)
	{

		for($i = 0, $n = sizeof($this->v_data_array['POST']['products_id']); $i < $n; $i++)
		{
			//GM_MOD WISHLIST BOF ############
			if($this->v_data_array['POST']['submit_target'] == 'wishlist') //WISHLIST ############
			{
				$t_cart_delete_array = array();
				if(is_array($this->v_data_array['POST']['cart_delete']))
				{
					$t_cart_delete_array = $this->v_data_array['POST']['cart_delete'];
				}
				if(in_array($this->v_data_array['POST']['products_id'][$i], $t_cart_delete_array))
				{
					$this->coo_wish_list->remove($this->v_data_array['POST']['products_id'][$i]);
					// BOF GM_MOD GX-Customizer
					if(isset($this->coo_gprint_wish_list->v_elements[$this->v_data_array['POST']['products_id'][$i]]))
					{
						$this->coo_gprint_wish_list->remove($this->v_data_array['POST']['products_id'][$i]);
					}
					// EOF GM_MOD GX-Customizer
				}
				else
				{
					if ($this->coo_wish_list->get_quantity($this->v_data_array['POST']['products_id'][$i]) > (int)MAX_PRODUCTS_QTY)
					{
						$t_gm_wishlist_products_qty = (int)MAX_PRODUCTS_QTY;
					}
					else
					{
						$t_gm_wishlist_products_qty = xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['cart_quantity'][$i]));
					}

					$attributes = '';
					if($this->v_data_array['POST']['id'][$this->v_data_array['POST']['products_id'][$i]])
					{
						$attributes = $this->v_data_array['POST']['id'][$this->v_data_array['POST']['products_id'][$i]];
					}
					$this->coo_wish_list->add_cart($this->v_data_array['POST']['products_id'][$i], $t_gm_wishlist_products_qty, $attributes, false);
				}
				$p_goto = 'wish_list.php';
			}
			else //CART ############
			{
				if (xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['cart_quantity'][$i])) > (int)MAX_PRODUCTS_QTY)
				{
					$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
				}
				else
				{
					$t_gm_cart_products_qty = xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['cart_quantity'][$i]));
				}

				$t_cart_delete_array = array();
				if(is_array($this->v_data_array['POST']['cart_delete']))
				{
					$t_cart_delete_array = $this->v_data_array['POST']['cart_delete'];
				}

				if (in_array($this->v_data_array['POST']['products_id'][$i], $t_cart_delete_array))
				{
					$this->coo_cart->remove($this->v_data_array['POST']['products_id'][$i]);
					// BOF GM_MOD GX-Customizer
					if(isset($this->coo_gprint_cart->v_elements[$this->v_data_array['POST']['products_id'][$i]]))
					{
						$this->coo_gprint_cart->remove($this->v_data_array['POST']['products_id'][$i]);
					}
					// EOF GM_MOD GX-Customizer
				}
				else
				{
					$attributes = '';
					if($this->v_data_array['POST']['id'][$this->v_data_array['POST']['products_id'][$i]])
					{
						$attributes = $this->v_data_array['POST']['id'][$this->v_data_array['POST']['products_id'][$i]];
					}
					$this->coo_cart->add_cart($this->v_data_array['POST']['products_id'][$i], $t_gm_cart_products_qty, $attributes, false);
				}
			}
			//GM_MOD WISHLIST EOF ############
		}

		$t_redirect_link = xtc_href_link($p_goto, xtc_get_all_get_params($p_parameters));
		$this->set_redirect_url($t_redirect_link);
		return;
	}

	protected function update_wishlist(&$p_goto, &$p_parameters)
	{
		for($i = 0; $i < count($this->v_data_array['POST']['products_id']); $i++)
		{
			$index = $this->v_data_array['POST']['products_id'][$i];
			// BOF GM_MOD
			if(gm_convert_qty($this->v_data_array['POST']['cart_quantity'][$i]) > (int)MAX_PRODUCTS_QTY)
			{
				$t_gm_wishlist_products_qty = (int)MAX_PRODUCTS_QTY;
			}
			else
			{
				$t_gm_wishlist_products_qty = gm_convert_qty($this->v_data_array['POST']['cart_quantity'][$i]);
			}

			if(!isset($this->coo_wish_list->contents[$index]))
			{
				continue;
			}
			
			$this->coo_wish_list->contents[$index]['qty'] = $t_gm_wishlist_products_qty;
			// EOF GM_MOD
			if(empty($this->customer_id) == false)
			{
				$t_sql = '	UPDATE
								customers_wishlist
							SET
								customers_basket_quantity = "' . gm_convert_qty($this->v_data_array['POST']['cart_quantity'][$i]) . '"
							WHERE
								customers_id = "' . $this->customer_id . '" AND
								products_id = "' . xtc_db_input($index) . '"';
				xtc_db_query($t_sql);
			}
		}
	}

	protected function add_product(&$p_goto, &$p_parameters)
	{
		$t_products_properties_combis_id = 0;

		if(isset($this->v_data_array['POST']['properties_values_ids']))
		{
			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			$t_products_properties_combis_id = $coo_properties_control->get_combis_id_by_value_ids_array($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['properties_values_ids']);
			if($t_products_properties_combis_id == 0)
			{
				die('combi not available');
			}
		}

		if(isset($this->v_data_array['POST']['products_id']) && is_numeric($this->v_data_array['POST']['products_id']))
		{
			if(is_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty'])) == false)
			{
				$this->v_data_array['POST']['products_qty'] = 1;
			}

			if (xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty'])) + $this->coo_cart->get_quantity(xtc_get_uprid($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['id'])) > (int)MAX_PRODUCTS_QTY)
			{
				$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
			}
			else
			{
				$t_gm_cart_products_qty = $this->coo_cart->get_quantity(xtc_get_uprid($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['id'])) + xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty']));
			}

			if (xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty'])) + $this->coo_wish_list->get_quantity(xtc_get_uprid($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['id'])) > (int)MAX_PRODUCTS_QTY)
			{
				$t_gm_wishlist_products_qty = (int)MAX_PRODUCTS_QTY;
			}
			else
			{
				$t_gm_wishlist_products_qty = $this->coo_wish_list->get_quantity(xtc_get_uprid($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['id'])) + xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty']));
			}

			if($this->v_data_array['POST']['submit_target'] == 'wishlist')
			{
				$this->coo_wish_list->add_cart(
											(int)$this->v_data_array['POST']['products_id'],
											$t_gm_wishlist_products_qty,
											$this->v_data_array['POST']['id'],
											true,
											(int)$t_products_properties_combis_id
										);
				$p_goto = 'wish_list.php';
			}
			else
			{
				$this->coo_cart->add_cart(
										(int)$this->v_data_array['POST']['products_id'],
										$t_gm_cart_products_qty,
										$this->v_data_array['POST']['id'],
										true,
										(int)$t_products_properties_combis_id
									);
			}
		}
		// BOF GM_MOD
		$p_parameters[] = 'products_id';
		$gm_get_params = xtc_get_all_get_params($p_parameters);
		if(empty($gm_get_params) == false)
		{
			$gm_get_params = '&' . $gm_get_params;
		}

		// GX-Customizer product
		if(isset($this->v_data_array['POST']['id']) && in_array('0', $this->v_data_array['POST']['id']) && $p_goto != 'shopping_cart.php' && $p_goto != 'wish_list.php')
		{
			$t_redirect_link = xtc_href_link($p_goto, 'products_id=' . (int)$this->v_data_array['POST']['products_id'] . '&open_cart_dropdown=1' . $gm_get_params);
			$this->set_redirect_url($t_redirect_link);
			return;
		}

		if($this->coo_seo_boost->boost_products && $p_goto != 'shopping_cart.php' && $p_goto != 'wish_list.php')
		{
			$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$this->v_data_array['POST']['products_id'], $this->v_data_array['GET']['gm_boosted_product']));
			$this->set_redirect_url($t_redirect_link);
			return;
		}
		elseif($p_goto != 'shopping_cart.php' && $p_goto != 'wish_list.php')
		{
			$t_redirect_link = xtc_href_link($p_goto, 'products_id=' . (int)$this->v_data_array['POST']['products_id'] . $gm_get_params);
			$this->set_redirect_url($t_redirect_link);
			return;
		}
		else
		{
			$p_parameters[] = 'info';
			$gm_get_params = xtc_get_all_get_params($p_parameters);
			$t_redirect_link = xtc_href_link($p_goto, $gm_get_params);
			$this->set_redirect_url($t_redirect_link);
			return;
		}
		// EOF GM_MOD
	}

	protected function wishlist_to_cart(&$p_goto, &$p_parameters)
	{
		if(empty($this->v_data_array['POST']['cart_delete']) == false)
		{
			$products_to_cart = $this->v_data_array['POST']['cart_delete'];
			for($i = 0; $i < count($products_to_cart); $i++)
			{
				$pos = strpos($products_to_cart[$i], '{');

				$coo_properties_control = MainFactory::create_object('PropertiesControl');
				$t_combis_id = (int)$coo_properties_control->extract_combis_id($products_to_cart[$i]);

				if($pos !== false)
				{
					$gm_ids = array();
					$index = $products_to_cart[$i];
					$gm_ids = $this->v_data_array['POST']['id'][$index];
					$gm_products_id = substr_wrapper($products_to_cart[$i], 0, $pos);
					// BOF GM_MOD
					if((double)$this->coo_cart->contents[$index]['qty'] + gm_convert_qty($this->v_data_array['POST']['cart_quantity'][array_search($products_to_cart[$i], $this->v_data_array['POST']['products_id'], true)]) > (int)MAX_PRODUCTS_QTY)
					{
						$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
					}
					else
					{
						$t_gm_cart_products_qty = (double)$this->coo_cart->contents[$index]['qty'] + gm_convert_qty($this->v_data_array['POST']['cart_quantity'][array_search($products_to_cart[$i], $this->v_data_array['POST']['products_id'], true)]);
					}

					$this->coo_cart->add_cart((int)$gm_products_id, $t_gm_cart_products_qty, $gm_ids, true, $t_combis_id);
					// EOF GM_MOD
				}
				else{
					// BOF GM_MOD
					if($this->coo_cart->get_quantity($products_to_cart[$i]) + gm_convert_qty($this->v_data_array['POST']['cart_quantity'][array_search($products_to_cart[$i], $this->v_data_array['POST']['products_id'], true)]) > (int)MAX_PRODUCTS_QTY)
					{
						$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
					}
					else
					{
						$t_gm_cart_products_qty = $this->coo_cart->get_quantity($products_to_cart[$i]) + gm_convert_qty($this->v_data_array['POST']['cart_quantity'][array_search($products_to_cart[$i], $this->v_data_array['POST']['products_id'], true)]);
					}

					$this->coo_cart->add_cart((int)$products_to_cart[$i], $t_gm_cart_products_qty, null, true, $t_combis_id);
					// EOF GM_MOD
				}
			}
		}

		$t_redirect_link = xtc_href_link($p_goto, 'open_cart_dropdown=1' . xtc_get_all_get_params($p_parameters));
		$this->set_redirect_url($t_redirect_link);
		return;
	}

	protected function add_a_quickie(&$p_goto, &$p_parameters)
	{
		$quicky = addslashes($this->v_data_array['POST']['quickie']);
		if(GROUP_CHECK == 'true')
		{
			$group_check = 'AND group_permission_' . $this->customers_status_id . ' = "1" ';
		}

		$quickie_query = xtc_db_query('	SELECT
											products_fsk18,
											products_id
										FROM
											' . TABLE_PRODUCTS . '
										WHERE
											products_model = "' . $quicky . '" AND
											products_status = "1" AND
											gm_price_status = 0 ' .
											$group_check);

		if(xtc_db_num_rows($quickie_query) == false)
		{
			if(GROUP_CHECK == 'true')
			{
				$group_check = 'AND group_permission_' . $this->customers_status_id . ' = 1 ';
			}
			$quickie_query = xtc_db_query('	SELECT
												products_fsk18,
												products_id
											FROM
												' . TABLE_PRODUCTS . '
											WHERE
												products_model LIKE "%' . $quicky . '%" AND
												products_status = "1" AND
												gm_price_status = "0" ' .
												$group_check);
		}
		if(xtc_db_num_rows($quickie_query) != 1)
		{
			$t_redirect_link = xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $quicky, 'NONSSL');
			$this->set_redirect_url($t_redirect_link);
			return;
		}
		$quickie = xtc_db_fetch_array($quickie_query);
		if(xtc_has_product_attributes($quickie['products_id']))
		{
			// BOF GM_MOD
			if($this->coo_seo_boost->boost_products)
			{
				$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$quickie['products_id'], $this->v_data_array['GET']['gm_boosted_product']));
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			else
			{
				$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL');
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			// EOF GM_MOD
		}
		else
		{
			if($quickie['products_fsk18'] == '1' && $this->customers_fsk18 == '1')
			{
				// BOF GM_MOD
				if($this->coo_seo_boost->boost_products)
				{
					$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$quickie['products_id'], $this->v_data_array['GET']['gm_boosted_product']));
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				else
				{
					$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				// EOF GM_MOD
			}
			if ($this->customers_fsk18_display == '0' && $quickie['products_fsk18'] == '1')
			{
				// BOF GM_MOD
				if($this->coo_seo_boost->boost_products)
				{
					$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$quickie['products_id'], $this->v_data_array['GET']['gm_boosted_product']));
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				else
				{
					$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'], 'NONSSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				// EOF GM_MOD
			}
			if($this->v_data_array['POST']['quickie'] != '')
			{
				$t_qty_check_result = $this->check_add_quickie_graduated_qty_min_order($quickie);
				if(!$t_qty_check_result)
				{
					return;
				}
				// BOF GM_MOD
				if($this->coo_cart->get_quantity(xtc_get_uprid($quickie['products_id'], 1)) + 1 > (int)MAX_PRODUCTS_QTY)
				{
					$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
				}
				else
				{
					$t_gm_cart_products_qty = $this->coo_cart->get_quantity(xtc_get_uprid($quickie['products_id'], 1)) + 1;
				}

				$this->coo_cart->add_cart($quickie['products_id'], $t_gm_cart_products_qty, 1);
				// EOF GM_MOD
				
				if(!$this->show_cart)
				{
					$t_redirect_link = xtc_href_link($p_goto, xtc_get_all_get_params(array('action')) . '&no_boost=1&open_cart_dropdown=1', 'NONSSL');
				}
				else
				{
					$t_redirect_link = xtc_href_link($p_goto);
				}
				
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			else
			{
				$t_redirect_link = xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $quicky, 'NONSSL');
				$this->set_redirect_url($t_redirect_link);
				return;
			}
		}
	}

	protected function buy_now(&$p_goto, &$p_parameters)
	{
		if(isset($this->v_data_array['POST']['products_qty']) == false)
		{
			$this->v_data_array['POST']['products_qty'] = 1;
		}

		$t_products_properties_combis_id = 0;

		if(isset($this->v_data_array['POST']['properties_values_ids']))
		{
			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			$t_products_properties_combis_id = $coo_properties_control->get_combis_id_by_value_ids_array($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['properties_values_ids']);
			if($t_products_properties_combis_id == 0)
			{
				die('combi not available');
			}
		}
		// EOF GM_MOD

		if(isset($this->v_data_array['GET']['BUYproducts_id']))
		{
			// check permission to view product

			$t_sql = '	SELECT
							group_permission_' . $this->customers_status_id . ' AS customer_group,
							products_fsk18
						FROM
							' . TABLE_PRODUCTS . '
						WHERE
							products_id = "' . (int)$this->v_data_array['GET']['BUYproducts_id'] . '"';
			$permission_query = xtc_db_query($t_sql);
			$permission = xtc_db_fetch_array($permission_query);

			// check for FSK18
			if($permission['products_fsk18'] == '1' && $this->customers_fsk18 == '1')
			{
				if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
				{
					$this->show_details = true;
					return;
				}
				// BOF GM_MOD
				if($this->coo_seo_boost->boost_products)
				{
					$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['GET']['gm_boosted_product']));
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				else
				{
					$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$this->v_data_array['GET']['BUYproducts_id'], 'NONSSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				// EOF GM_MOD
			}

			if ($this->customers_fsk18_display == '0' && $permission['products_fsk18'] == '1')
			{
				if(isset($this->turbo_buy_now) && $this->turbo_buy_now)
				{
					$this->show_details = true;
					return;
				}
				// BOF GM_MOD
				if($this->coo_seo_boost->boost_products)
				{
					$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['GET']['gm_boosted_product']));
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				else
				{
					$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$this->v_data_array['GET']['BUYproducts_id'], 'NONSSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				// EOF GM_MOD
			}

			if(GROUP_CHECK == 'true')
			{
				if($permission['customer_group'] != '1')
				{
					if(isset($this->turbo_buy_now) && $this->turbo_buy_now)
					{
						$this->show_details = true;
						return;
					}
					// BOF GM_MOD
					if($this->coo_seo_boost->boost_products)
					{
						$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['GET']['gm_boosted_product']));
						$this->set_redirect_url($t_redirect_link);
						return;
					}
					else
					{
						$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$this->v_data_array['GET']['BUYproducts_id']);
						$this->set_redirect_url($t_redirect_link);
						return;
					}
					// EOF GM_MOD
				}
			}

			// BOF GM_MOD
			$gm_qty_check = false;
			if(isset($this->v_data_array['POST']['id']) == false)
			{
				$t_sql = '	SELECT
								gm_min_order,
								gm_graduated_qty
							FROM
								products
							WHERE
								products_id = "' . (int)$this->v_data_array['GET']['BUYproducts_id'] . '"';
				$gm_get_order_qty = xtc_db_query($t_sql);
				if(xtc_db_num_rows($gm_get_order_qty) == 1)
				{
					$row = xtc_db_fetch_array($gm_get_order_qty);
					$gm_qty = gm_convert_qty($this->v_data_array['POST']['products_qty']);
					if($gm_qty < $row['gm_min_order'])
					{
						$gm_qty_check = true;
					}
					if($gm_qty_check == false)
					{
						$gm_result = $gm_qty / $row['gm_graduated_qty'];
						$gm_result = round($gm_result, 4); // workaround for next if-case to avoid calculating failure
						if((int)$gm_result != $gm_result)
						{
							$gm_qty_check = true;
						}
					}
				}
			}
			// EOF GM_MOD

			// BOF GM_MOD
			if(xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty'])) + $this->coo_cart->get_quantity(xtc_get_uprid((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['POST']['id'], $t_products_properties_combis_id)) > (int)MAX_PRODUCTS_QTY)
			{
				$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
			}
			else
			{
				$t_gm_cart_products_qty = $this->coo_cart->get_quantity(xtc_get_uprid((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['POST']['id'], $t_products_properties_combis_id)) + xtc_remove_non_numeric(gm_convert_qty($this->v_data_array['POST']['products_qty']));
			}

			/*
			if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
			{
				$this->show_details = true;
				return;
			}
			#GM_MOD: properties patch
			$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $this->v_data_array['GET']['BUYproducts_id']);
			$this->set_redirect_url($t_redirect_link);
			return;
			*/

			if(xtc_has_product_attributes($this->v_data_array['GET']['BUYproducts_id']) || $gm_qty_check ||
				$this->coo_gprint_product_manager->get_surfaces_groups_id($this->v_data_array['GET']['BUYproducts_id']) !== false)
			{
				if((empty($this->v_data_array['POST']['products_id']) == false && (isset($this->v_data_array['POST']['id']) || $this->v_data_array['POST']['properties_values_ids'])) &&
					$this->coo_gprint_product_manager->get_surfaces_groups_id($this->v_data_array['GET']['BUYproducts_id']) === false)
				{

					$this->coo_cart->add_cart((int)$this->v_data_array['POST']['products_id'], $t_gm_cart_products_qty, $this->v_data_array['POST']['id'], true, (int)$t_products_properties_combis_id);
					if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
					{
						# all done. back to request_port
						return;
					}
				}
				else
				{
					if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
					{
						$this->show_details = true;
						return;
					}
					if($this->coo_seo_boost->boost_products)
					{
						$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['GET']['gm_boosted_product']));
						$this->set_redirect_url($t_redirect_link);
						return;
					}
					else
					{
						$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$this->v_data_array['GET']['BUYproducts_id']);
						$this->set_redirect_url($t_redirect_link);
						return;
					}
				}
				// EOF GM_MOD
			}
			else
			{
				if (isset($this->coo_cart))
				{
					// BOF GM_MOD
					if(empty($this->v_data_array['POST']['products_qty']))
					{
						$this->v_data_array['POST']['products_qty'] = 1;
					}
					$this->coo_cart->add_cart((int)$this->v_data_array['GET']['BUYproducts_id'], $t_gm_cart_products_qty, null, true, (int)$t_products_properties_combis_id);
					if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
					{
						# all done
						return;
					}
					// EOF GM_MOD
				}
				else
				{
					if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
					{
						$this->show_details = true;
						return;
					}
					$t_redirect_link = xtc_href_link(FILENAME_DEFAULT);
					$this->set_redirect_url($t_redirect_link);
					return;
				}
			}
		}
		// BOF GM_MOD
		$gm_get_params = xtc_get_all_get_params(array
		(
			'action',
			'BUYproducts_id'
		));

		if(substr_wrapper($gm_get_params, -1) == '&')
		{
			$gm_get_params = substr_wrapper($gm_get_params, 0, -1);
		}

		if($this->coo_seo_boost->boost_categories && $p_goto != 'shopping_cart.php' && $p_goto != 'wish_list.php')
		{
			if(isset($this->v_data_array['GET']['keywords']))
			{
				$p_goto = 'advanced_search_result.php';
				$t_redirect_link = xtc_href_link($p_goto, $gm_get_params);
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			elseif(isset($this->v_data_array['GET']['cat']))
			{
				$cID = substr_wrapper($this->v_data_array['GET']['cat'], 1, strlen_wrapper($this->v_data_array['GET']['cat']) - 1);
				$t_redirect_link = $this->coo_seo_boost->get_boosted_category_url($cID);
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			else
			{
				$t_redirect_link = xtc_href_link($p_goto, $gm_get_params);
				$this->set_redirect_url($t_redirect_link);
				return;
			}
		}
		else
		{
			if(DISPLAY_CART == 'true')
			{
				$gm_get_params = xtc_get_all_get_params(array
				(
					'action',
					'BUYproducts_id',
					'cat',
					'keywords',
					'page'
				));
			}

			$t_redirect_link = xtc_href_link($p_goto, $gm_get_params);
			$this->set_redirect_url($t_redirect_link);
			return;
		}
	}

	protected function cust_order(&$p_goto, &$p_parameters)
	{
		$gm_qty_check = false;
		if(isset($this->v_data_array['POST']['id']) == false)
		{
			$t_sql = '	SELECT
							gm_min_order,
							gm_graduated_qty
						FROM
							products
						WHERE
							products_id = "' . (int)$this->v_data_array['GET']['pid'] . '"';
			$gm_get_order_qty = xtc_db_query($t_sql);
			if(xtc_db_num_rows($gm_get_order_qty) == 1)
			{
				$row = xtc_db_fetch_array($gm_get_order_qty);
				$gm_qty = gm_convert_qty($this->v_data_array['POST']['products_qty']);
				if($gm_qty < $row['gm_min_order'])
				{
					$gm_qty_check = true;
				}
				if($gm_qty_check == false)
				{
					$gm_result = round($gm_qty / $row['gm_graduated_qty'], 4);
					if((int)$gm_result != $gm_result)
					{
						$gm_qty_check = true;
					}
				}
			}
		}
		// EOF GM_MOD

		if(isset($this->customer_id) && isset($this->v_data_array['GET']['pid']))
		{
			// BOF GM_MOD:
			if(xtc_has_product_attributes((int)$this->v_data_array['GET']['pid']) || $gm_qty_check)
			{
				// BOF GM_MOD
				if($this->coo_seo_boost->boost_products)
				{
					$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int) $this->v_data_array['GET']['pid'], $this->v_data_array['GET']['gm_boosted_product']));
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				else
				{
					$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$this->v_data_array['GET']['pid']);
					$this->set_redirect_url($t_redirect_link);
					return;
				}
				// EOF GM_MOD
			}
			else
			{
				// BOF GM_MOD
				if($this->coo_cart->get_quantity((int)$this->v_data_array['GET']['pid']) + 1 > (int)MAX_PRODUCTS_QTY)
				{
					$t_gm_cart_products_qty = (int)MAX_PRODUCTS_QTY;
				}
				else
				{
					$t_gm_cart_products_qty = $this->coo_cart->get_quantity((int)$this->v_data_array['GET']['pid']) + 1;
				}

				$this->coo_cart->add_cart((int)$this->v_data_array['GET']['pid'], $t_gm_cart_products_qty);
				// EOF GM_MOD
			}
		}

		$t_redirect_link = xtc_href_link($p_goto, xtc_get_all_get_params($p_parameters));
		$this->set_redirect_url($t_redirect_link);
		return;
	}

	protected function prepare_gprint_product(&$p_goto, &$p_parameters)
	{
		if($this->coo_gprint_product_manager->get_surfaces_groups_id($this->v_data_array['POST']['products_id']) !== false)
		{
			$t_products_properties_combis_id = 0;

			if(isset($this->v_data_array['POST']['properties_values_ids']))
			{
				$coo_properties_control = MainFactory::create_object('PropertiesControl');
				$t_products_properties_combis_id = $coo_properties_control->get_combis_id_by_value_ids_array($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['properties_values_ids']);
				if($t_products_properties_combis_id == 0)
				{
					die('combi not available');
				}
			}

			$t_gm_product = xtc_get_uprid($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['id'], $t_products_properties_combis_id);

			if($this->v_data_array['POST']['submit_target'] == 'cart')
			{
				if($this->coo_gprint_cart != null)
				{
					$t_new_product = $this->coo_gprint_cart->check_cart($t_gm_product, 'cart',  false);

					if($t_new_product !== false)
					{
						$t_gm_product = $t_new_product;
					}
				}

				if(isset($this->cart->contents[$t_gm_product]))
				{
					$this->v_data_array['POST']['products_qty'] -= $this->cart->contents[$t_gm_product]['qty'];
					if($this->v_data_array['POST']['products_qty'] < 0)
					{
						$this->v_data_array['POST']['products_qty'] = 0;
					}
				}
			}
			elseif($this->v_data_array['POST']['submit_target'] == 'wishlist')
			{
				if($this->coo_gprint_wish_list != null)
				{
					$t_new_product = $this->coo_gprint_wish_list->check_wishlist($t_gm_product, 'wishList',  false);

					if($t_new_product !== false)
					{
						$t_gm_product = $t_new_product;
					}
				}

				if(isset($this->coo_wish_list->contents[$t_gm_product]))
				{
					$this->v_data_array['POST']['products_qty'] -= $this->coo_wish_list->contents[$t_gm_product]['qty'];
					if($this->v_data_array['POST']['products_qty'] < 0)
					{
						$this->v_data_array['POST']['products_qty'] = 0;
					}
				}
			}
		}
	}

	protected function prepare_buy_now(&$p_goto, &$p_parameters)
	{
		if($this->coo_gprint_product_manager->get_surfaces_groups_id($this->v_data_array['GET']['BUYproducts_id']) !== false)
		{
			if(isset($this->turbo_buy_now) && $this->turbo_buy_now == true)
			{
				$this->show_details = true;
				return;
			}
			if($this->coo_seo_boost->boost_products) {
				$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$this->v_data_array['GET']['BUYproducts_id'], $this->v_data_array['GET']['gm_boosted_product']));
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			else
			{
				$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$this->v_data_array['GET']['BUYproducts_id'], 'NONSSL');
				$this->set_redirect_url($t_redirect_link);
				return;
			}
		}
	}

	protected function check_gift(&$p_goto, &$p_parameters)
	{
		if($this->v_data_array['POST']['gv_redeem_code'])
		{
			$t_sql = '	SELECT
							coupon_id,
							coupon_amount,
							coupon_type,
							coupon_minimum_order,
							uses_per_coupon,
							uses_per_user,
							restrict_to_products,restrict_to_categories
						FROM
							' . TABLE_COUPONS . '
						WHERE
							coupon_code = "' . xtc_db_input($this->v_data_array['POST']['gv_redeem_code']) . '" AND
							coupon_active = "Y"';
			$gv_query = xtc_db_query($t_sql);
			$gv_result = xtc_db_fetch_array($gv_query);

			if(xtc_db_num_rows($gv_query) != 0)
			{
				$t_sql = '	SELECT
								*
							FROM
								' . TABLE_COUPON_REDEEM_TRACK . '
							WHERE
								coupon_id = "' . $gv_result['coupon_id'] . '"';
				$redeem_query = xtc_db_query($t_sql);
				if(xtc_db_num_rows($redeem_query) != 0 && $gv_result['coupon_type'] == 'G')
				{
					$this->info_message = ERROR_NO_INVALID_REDEEM_GV;
					$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}
			}
			else
			{
				$this->info_message = ERROR_NO_INVALID_REDEEM_GV;
				$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
				$this->set_redirect_url($t_redirect_link);
				return;
			}



			// GIFT CODE G START
			if($gv_result['coupon_type'] == 'G')
			{
				$gv_amount = $gv_result['coupon_amount'];
				// Things to set
				// ip address of claimant
				// customer id of claimant
				// date
				// redemption flag
				// now update customer account with gv_amount
				$t_sql = '	SELECT
								amount
							FROM
								' . TABLE_COUPON_GV_CUSTOMER . '
							WHERE
								customer_id = "' . $this->customer_id . '"';
				$gv_amount_query = xtc_db_query($t_sql);
				$customer_gv = false;
				$total_gv_amount = $gv_amount;
				if($gv_amount_result = xtc_db_fetch_array($gv_amount_query))
				{
					$total_gv_amount = $gv_amount_result['amount'] + $gv_amount;
					$customer_gv = true;
				}
				$t_sql = '	UPDATE
								' . TABLE_COUPONS . '
							SET
								coupon_active = "N"
							WHERE
								coupon_id = "' . $gv_result['coupon_id'] . '"';
				$gv_update = xtc_db_query($t_sql);
				$t_sql = '	INSERT INTO
								' . TABLE_COUPON_REDEEM_TRACK . '
								(coupon_id, customer_id, redeem_date, redeem_ip)
							VALUES
								("' . $gv_result['coupon_id'] . '",
								"' . $this->customer_id . '",
								now(),
								"' . $this->remote_address . '")';
				$gv_redeem = xtc_db_query($t_sql);
				if($customer_gv)
				{
					// already has gv_amount so update
					$t_sql = '	UPDATE
									' . TABLE_COUPON_GV_CUSTOMER . '
								SET
									amount = "' . $total_gv_amount . '"
								WHERE
									customer_id = "' . $this->customer_id . '"';
					$gv_update = xtc_db_query($t_sql);
				}
				else
				{
					// no gv_amount so insert
					$t_sql = '	INSERT INTO
									' . TABLE_COUPON_GV_CUSTOMER . '
									(customer_id, amount)
								VALUES
									("' . $this->customer_id . '",
									"' . $total_gv_amount . '")';
					$gv_insert = xtc_db_query($t_sql);
				}

				$this->info_message = REDEEMED_AMOUNT . $this->coo_price->xtcFormat($gv_amount, true, 0, true);
				$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
				$this->set_redirect_url($t_redirect_link);
				return;
			}
			else
			{
				if(xtc_db_num_rows($gv_query) == 0)
				{
					$this->info_message = ERROR_NO_INVALID_REDEEM_COUPON;
					$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}

				$t_sql = '	SELECT
								coupon_start_date
							FROM
								' . TABLE_COUPONS . '
							WHERE
								coupon_start_date <= now() AND
								coupon_code = "' . xtc_db_input($this->v_data_array['POST']['gv_redeem_code']) . '"';
				$date_query = xtc_db_query($t_sql);

				if(xtc_db_num_rows($date_query) == 0)
				{
					$this->info_message = ERROR_INVALID_STARTDATE_COUPON;
					$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}

				$t_sql = '	SELECT
								coupon_expire_date
							FROM
								' . TABLE_COUPONS . '
							WHERE
								coupon_expire_date >= now() AND
								coupon_code = "' . xtc_db_input($this->v_data_array['POST']['gv_redeem_code']) . '"';
				$date_query = xtc_db_query($t_sql);

				if(xtc_db_num_rows($date_query) == 0)
				{
					$this->info_message = ERROR_INVALID_FINISDATE_COUPON;
					$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}

				$t_sql = '	SELECT
								coupon_id
							FROM
								' . TABLE_COUPON_REDEEM_TRACK . '
							WHERE
								coupon_id = "' . $gv_result['coupon_id'] . '"';
				$coupon_count = xtc_db_query($t_sql);

				$t_sql = '	SELECT
								coupon_id
							FROM
								' . TABLE_COUPON_REDEEM_TRACK . '
							WHERE
								coupon_id = "' . $gv_result['coupon_id'] . '" AND
								customer_id = "' . $this->customer_id . '"';
				$coupon_count_customer = xtc_db_query($t_sql);

				if(xtc_db_num_rows($coupon_count) >= $gv_result['uses_per_coupon'] && $gv_result['uses_per_coupon'] > 0)
				{
					$this->info_message = ERROR_INVALID_USES_COUPON . $gv_result['uses_per_coupon'] . TIMES;
					$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}

				if(xtc_db_num_rows($coupon_count_customer) >= $gv_result['uses_per_user'] && $gv_result['uses_per_user'] > 0)
				{
					$this->info_message = ERROR_INVALID_USES_USER_COUPON . $gv_result['uses_per_user'] . TIMES;
					$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
					$this->set_redirect_url($t_redirect_link);
					return;
				}

				if($gv_result['coupon_type'] == 'S')
				{
					$coupon_amount = $this->coo_order->info['shipping_cost'];
				}
				else
				{
					$coupon_amount = $gv_result['coupon_amount'] . ' ';
				}
				if($gv_result['coupon_type'] == 'P')
				{
					$coupon_amount = $gv_result['coupon_amount'] . '% ';
				}
				if($gv_result['coupon_minimum_order'] > 0)
				{
					$coupon_amount .= 'on orders greater than ' . $gv_result['coupon_minimum_order'];
				}
				if(xtc_session_is_registered('cc_id') == false)
				{
					xtc_session_register('cc_id'); //Fred - this was commented out before
				}
				$_SESSION['cc_id'] = $gv_result['coupon_id']; //Fred ADDED, set the global and session variable
				$this->info_message = REDEEMED_COUPON;
				if($gv_result['coupon_minimum_order'] > 0 && $gv_result['coupon_minimum_order'] > $this->coo_cart->show_total())
				{
					$this->info_message .= ' ' . REDEEMED_COUPON_UNDER_MIN_VALUE;
				}
				$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
				$this->set_redirect_url($t_redirect_link);
				return;
			}

		}
		if ($this->v_data_array['POST']['submit_redeem_x'] && $gv_result['coupon_type'] == 'G')
		{
			$this->info_message = ERROR_NO_REDEEM_CODE;
			$t_redirect_link = xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
			$this->set_redirect_url($t_redirect_link);
			return;
		}
	}


	protected function check_add_quickie_graduated_qty_min_order($quickie)
	{
		$t_check_query = xtc_db_query('	SELECT
													*
												FROM
													`products`
												WHERE
													`products_id` = ' . (int)$quickie['products_id'] . ' AND
													`gm_min_order` = 1 AND
													`gm_graduated_qty` = 1;
												');
		if((int)xtc_db_num_rows($t_check_query) !== 1)
		{
			if($this->coo_seo_boost->boost_products)
			{
				$t_redirect_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url((int)$quickie['products_id'],
				                                                                               $this->v_data_array['GET']['gm_boosted_product']));
				$this->set_redirect_url($t_redirect_link);

				return false;
			}
			else
			{
				$t_redirect_link = xtc_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $quickie['products_id'],
				                                 'NONSSL');
				$this->set_redirect_url($t_redirect_link);

				return false;
			}
		}

		return true;
	}
}
