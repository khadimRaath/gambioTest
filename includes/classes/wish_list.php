<?php
/* --------------------------------------------------------------
  wish_list.php 2016-09-21
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(shopping_cart.php,v 1.32 2003/02/11); www.oscommerce.com
  (c) 2003	 nextcommerce (shopping_cart.php,v 1.21 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shopping_cart.php,v 1.5 2004/02/17 21:13:26 fanta2k Exp $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class wishList_ORIGIN
{
	var $contents, $total, $weight, $cartID, $content_type;

	public function __construct()
	{
		$this->reset();
	}

	function restore_contents()
	{
		if(!isset($_SESSION['customer_id']))
		{
			return false;
		}

		// insert current cart contents in database
		if(is_array($this->contents))
		{
			reset($this->contents);

			while(list($products_id, ) = each($this->contents))
			{
				$qty = $this->contents[$products_id]['qty'];
				$product_query = xtc_db_query("select products_id from " . TABLE_CUSTOMERS_WISHLIST . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");

				if(!xtc_db_num_rows($product_query))
				{
					$sql_data_array = array();
					$sql_data_array['customers_id'] = $_SESSION['customer_id'];
					$sql_data_array['products_id'] = xtc_db_input($products_id);
					$sql_data_array['customers_basket_quantity'] = xtc_db_input($qty);
					$sql_data_array['customers_basket_date_added'] = date('Ymd');
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, $sql_data_array);

					if(isset($this->contents[$products_id]['attributes']))
					{
						reset($this->contents[$products_id]['attributes']);
						while(list($option, $value) = each($this->contents[$products_id]['attributes']))
						{
							$sql_data_array = array();
							$sql_data_array['customers_id'] = $_SESSION['customer_id'];
							$sql_data_array['products_id'] = xtc_db_input($products_id);
							$sql_data_array['products_options_id'] = xtc_db_input($option);
							$sql_data_array['products_options_value_id'] = xtc_db_input($value);
							$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, $sql_data_array);
						}
					}
				}
				else
				{
					$sql_data_array = array();
					$sql_data_array['customers_basket_quantity'] = xtc_db_input($qty);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, $sql_data_array, 'update', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
				}
			}
		}

		// reset per-session cart contents, but not the database contents
		$this->reset(false);

		$products_query = xtc_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_WISHLIST . " where customers_id = '" . $_SESSION['customer_id'] . "'");
		while($products = xtc_db_fetch_array($products_query))
		{
			$t_gm_products_id = xtc_get_prid($products['products_id']);
			$t_gm_check_status = xtc_db_query("SELECT
													products_status,
													gm_price_status,
													group_permission_" . (int)$_SESSION['customers_status']['customers_status_id'] . " AS permission
												FROM " . TABLE_PRODUCTS . "
												WHERE products_id = '" . (int)$t_gm_products_id . "'");
			if(xtc_db_num_rows($t_gm_check_status) == 1)
			{
				$t_gm_status = xtc_db_fetch_array($t_gm_check_status);

				if((GROUP_CHECK === 'false' || $t_gm_status['permission'] === '1') && $t_gm_status['products_status'] == 1 && (int)$t_gm_status['gm_price_status'] == 0)
				{
					$this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
					// attributes
					$attributes_query = xtc_db_query("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products['products_id'] . "'");
					while($attributes = xtc_db_fetch_array($attributes_query))
					{
						$this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
					}

					// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
					$this->cartID = $this->generate_cart_id();
				}
				else
				{
					$this->remove($products['products_id']);
				}
			}
		}

		$this->cleanup();
	}

	function reset($reset_database = false)
	{
		$this->contents = array();
		$this->total = 0;
		$this->weight = 0;
		$this->content_type = false;

		if(isset($_SESSION['customer_id']) && ($reset_database == true))
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\'');
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\'');
		}

		unset($this->cartID);

		if(isset($_SESSION['cartID']))
		{
			unset($_SESSION['cartID']);
		}
	}

	function add_cart($products_id, $qty = '1', $attributes = '', $notify = true, $p_products_properties_combis_id = 0)
	{
		if(!preg_match('/[0-9]+\{[0-9]+\}[0-9{}]*x[0-9]+/', $products_id))
		{
			// properties BOF
			$c_products_properties_combis_id = (int)$p_products_properties_combis_id;

			if($c_products_properties_combis_id == 0) #no combis_id given?
			{
				$coo_properties_control = MainFactory::create_object('PropertiesControl'); #check products_id for integrated combis_id
				$t_combis_id = $coo_properties_control->extract_combis_id($products_id);

				if($t_combis_id != '')
				{
					if(!$coo_properties_control->combi_exists(xtc_get_prid($products_id), $t_combis_id))
					{
						return false;
					}
					
					$c_products_properties_combis_id = $t_combis_id;
				}
			}

			$products_id = xtc_get_uprid($products_id, $attributes, $c_products_properties_combis_id);
			// properties EOF

			if($notify == true)
			{
				$_SESSION['new_products_id_in_cart'] = $products_id;
			}
		}

		if($this->in_cart($products_id))
		{
			$this->update_quantity($products_id, $qty, $attributes);
		}
		else
		{
			$this->contents[] = array($products_id);
			$this->contents[$products_id] = array('qty' => $qty);

			// insert into database
			if(isset($_SESSION['customer_id']))
			{
				$sql_data_array = array();
				$sql_data_array['customers_id'] = $_SESSION['customer_id'];
				$sql_data_array['products_id'] = xtc_db_input($products_id);
				$sql_data_array['customers_basket_quantity'] = xtc_db_input($qty);
				$sql_data_array['customers_basket_date_added'] = date('Ymd');
				$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, $sql_data_array);
			}

			if(is_array($attributes))
			{
				reset($attributes);

				while(list($option, $value) = each($attributes))
				{
					$this->contents[$products_id]['attributes'][$option] = $value;
					// insert into database
					if(isset($_SESSION['customer_id']))
					{
						$sql_data_array = array();
						$sql_data_array['customers_id'] = $_SESSION['customer_id'];
						$sql_data_array['products_id'] = xtc_db_input($products_id);
						$sql_data_array['products_options_id'] = xtc_db_input($option);
						$sql_data_array['products_options_value_id'] = xtc_db_input($value);
						$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, $sql_data_array);
					}
				}
			}
		}

		$this->cleanup();

		// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
		$this->cartID = $this->generate_cart_id();
	}

	function update_quantity($products_id, $quantity = '', $attributes = '')
	{
		if(empty($quantity) && isset($this->contents[$products_id]))
		{
			return true; // nothing needs to be updated if theres no quantity, so we return true..
		}

		$this->contents[$products_id] = array('qty' => $quantity);

		// update database
		if(isset($_SESSION['customer_id']))
		{
			$sql_data_array = array();
			$sql_data_array['customers_basket_quantity'] = xtc_db_input($quantity);
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, $sql_data_array, 'update', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
		}

		if(is_array($attributes))
		{
			reset($attributes);

			while(list($option, $value) = each($attributes))
			{
				$this->contents[$products_id]['attributes'][$option] = $value;

				// update database
				if(isset($_SESSION['customer_id']))
				{
					$sql_data_array = array();
					$sql_data_array['products_options_value_id'] = (int)$value;
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, $sql_data_array, 'update', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\' AND products_options_id = \'' . (int)$option . '\'');
				}
			}
		}
	}

	function cleanup()
	{
		reset($this->contents);

		while(list($key, ) = each($this->contents))
		{
			if($this->contents[$key]['qty'] <= 0)
			{
				unset($this->contents[$key]);

				// remove from database
				if(xtc_session_is_registered('customer_id'))
				{
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($key) . '\'');
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($key) . '\'');
				}
			}
		}
	}

	// get total number of items in cart
	function count_contents()
	{
		$total_items = 0;

		if(is_array($this->contents))
		{
			reset($this->contents);

			while(list($products_id, ) = each($this->contents))
			{
				$total_items += $this->get_quantity($products_id);
			}
		}

		return $total_items;
	}

	function get_quantity($products_id)
	{
		if(isset($this->contents[$products_id]))
		{
			return $this->contents[$products_id]['qty'];
		}
		else
		{
			return 0;
		}
	}

	function in_cart($products_id)
	{
		if(isset($this->contents[$products_id]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function remove($products_id)
	{
		unset($this->contents[$products_id]);

		// remove from database
		if(xtc_session_is_registered('customer_id'))
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_WISHLIST_ATTRIBUTES, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
		}

		// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
		$this->cartID = $this->generate_cart_id();
	}

	function remove_all()
	{
		$this->reset();
	}

	function get_product_id_list()
	{
		$product_id_list = '';

		if(is_array($this->contents))
		{
			reset($this->contents);

			while(list($products_id, ) = each($this->contents))
			{
				$product_id_list .= ', ' . $products_id;
			}
		}

		return substr($product_id_list, 2);
	}

	function calculate()
	{
		global $xtPrice;

		$this->total = 0;
		$this->weight = 0;

		if(!is_array($this->contents))
		{
			return 0;
		}

		reset($this->contents);

		while(list($products_id, ) = each($this->contents))
		{
			$qty = $this->contents[$products_id]['qty'];

			// products price
			$product_query = xtc_db_query("select products_id, products_price, products_discount_allowed, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . xtc_db_input($products_id) . "'");

			if(xtc_db_num_rows($product_query) > 0)
			{
				$product = xtc_db_fetch_array($product_query);
				$products_price = $xtPrice->xtcGetPrice($products_id, $format = false, $qty, $product['products_tax_class_id'], $product['products_price'], 0, 0, true, true);

				$this->total += $products_price * $qty;
				$this->weight += ($qty * $product['products_weight']);
			}

			// attributes price
			if(isset($this->contents[$products_id]['attributes']))
			{
				reset($this->contents[$products_id]['attributes']);

				while(list($option, $value) = each($this->contents[$products_id]['attributes']))
				{
					$attribute_price_query = xtc_db_query("select pd.products_tax_class_id, p.options_values_price, p.price_prefix, p.options_values_weight, p.weight_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $product['products_id'] . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
					$attribute_price = xtc_db_fetch_array($attribute_price_query);

					if($attribute_price['weight_prefix'] == '+')
					{
						$this->weight += ($qty * $attribute_price['options_values_weight']);
					}
					else
					{
						$this->weight -= ($qty * $attribute_price['options_values_weight']);
					}

					if($attribute_price['price_prefix'] == '+')
					{
						$this->total +=$xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']) * $qty;
					}
					else
					{
						$this->total -=$xtPrice->xtcFormat($attribute_price['options_values_price'], false, $attribute_price['products_tax_class_id']) * $qty;
					}
				}
			}
		}

		if($_SESSION['customers_status']['customers_status_ot_discount_flag'] != 0)
		{
			$this->total -= $this->total / 100 * $_SESSION['customers_status']['customers_status_ot_discount'];
		}
	}

	function attributes_price($products_id)
	{
		global $xtPrice;

		if(isset($this->contents[$products_id]['attributes']))
		{
			reset($this->contents[$products_id]['attributes']);

			while(list ($option, $value) = each($this->contents[$products_id]['attributes']))
			{
				$values = $xtPrice->xtcGetOptionPrice($products_id, $option, $value);
				$attributes_price += $values['price'];
			}
		}

		return $attributes_price;
	}

	function get_products()
	{
		global $xtPrice;

		if(!is_array($this->contents))
		{
			return false;
		}

		$products_array = array();
		reset($this->contents);

		while(list($products_id, ) = each($this->contents))
		{
			$products_query = xtc_db_query("SELECT
													p.products_id,
													pd.products_name,
													p.products_image,
													p.products_model,
													p.products_price,
													p.products_discount_allowed,
													p.products_weight,
													p.products_tax_class_id ,
													qud.quantity_unit_id,
													qud.unit_name
												FROM
													" . TABLE_PRODUCTS . " p
													LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd USING (products_id)
													LEFT JOIN products_quantity_unit pqu USING (products_id)
													LEFT JOIN quantity_unit_description qud ON (pqu.quantity_unit_id = qud.quantity_unit_id AND qud.language_id = '" . (int)$_SESSION['languages_id'] . "')
												WHERE
													p.products_id='" . xtc_db_input(xtc_get_prid($products_id)) . "' AND
													pd.products_id = p.products_id AND
													pd.language_id = '" . $_SESSION['languages_id'] . "'");
			if(xtc_db_num_rows($products_query) == 1)
			{
				$products = xtc_db_fetch_array($products_query);

				$products_price = $xtPrice->xtcGetPrice($products_id, $format = false, $this->contents[$products_id]['qty'], $products['products_tax_class_id'], $products['products_price'], 0, 0, true, true);

				# add attributes price
				$products_price = $products_price + $this->attributes_price($products_id);

				$products_array[] = array('id' => $products_id,
					'name' => $products['products_name'],
					'model' => $products['products_model'],
					'image' => $products['products_image'],
					'price' => $products_price,
					'quantity' => $this->contents[$products_id]['qty'],
					'weight' => $products['products_weight'],
					'final_price' => $products_price,
					'tax_class_id' => $products['products_tax_class_id'],
					'attributes' => $this->contents[$products_id]['attributes'],
					'quantity_unit_id' => $products['quantity_unit_id'],
					'unit_name' => $products['unit_name']);
			}
		}

		return $products_array;
	}

	function show_total()
	{
		$this->calculate();

		return $this->total;
	}

	function show_weight()
	{
		$this->calculate();

		return $this->weight;
	}

	function generate_cart_id($length = 5)
	{
		return xtc_create_random_value($length, 'digits');
	}

	function get_content_type()
	{
		$this->content_type = false;

		if((DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0))
		{
			reset($this->contents);

			while(list($products_id, ) = each($this->contents))
			{
				if(isset($this->contents[$products_id]['attributes']))
				{
					reset($this->contents[$products_id]['attributes']);

					while(list(, $value) = each($this->contents[$products_id]['attributes']))
					{
						$virtual_check_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . $products_id . "' and pa.options_values_id = '" . $value . "' and pa.products_attributes_id = pad.products_attributes_id");
						$virtual_check = xtc_db_fetch_array($virtual_check_query);

						if($virtual_check['total'] > 0)
						{
							switch($this->content_type)
							{
								case 'physical':
									$this->content_type = 'mixed';
									return $this->content_type;
									break;

								default:
									$this->content_type = 'virtual';
									break;
							}
						}
						else
						{
							switch($this->content_type)
							{
								case 'virtual':
									$this->content_type = 'mixed';
									return $this->content_type;
									break;

								default:
									$this->content_type = 'physical';
									break;
							}
						}
					}
				}
				else
				{
					switch($this->content_type)
					{
						case 'virtual':
							$this->content_type = 'mixed';
							return $this->content_type;
							break;

						default:
							$this->content_type = 'physical';
							break;
					}
				}
			}
		}
		else
		{
			$this->content_type = 'physical';
		}
		return $this->content_type;
	}

	function unserialize($broken)
	{
		for(reset($broken); $kv = each($broken);)
		{
			$key = $kv['key'];

			if(gettype($this->$key) != "user function")
			{
				$this->$key = $kv['value'];
			}
		}
	}

	// GV Code Start
	// ------------------------ ICW CREDIT CLASS Gift Voucher Addittion-------------------------------Start
	// amend count_contents to show nil contents for shipping
	// as we don't want to quote for 'virtual' item
	// GLOBAL CONSTANTS if NO_COUNT_ZERO_WEIGHT is true then we don't count any product with a weight
	// which is less than or equal to MINIMUM_WEIGHT
	// otherwise we just don't count gift certificates
	// get total number of items in cart disregard gift vouchers and downloads
	function count_contents_non_virtual()
	{
		$total_items = 0;

		if(is_array($this->contents))
		{
			reset($this->contents);

			while(list($products_id, ) = each($this->contents))
			{
				$no_count = false;
				$gv_query = xtc_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
				$gv_result = xtc_db_fetch_array($gv_query);

				if(preg_match('/^GIFT/', $gv_result['products_model']))
				{
					$no_count = true;
				}

				if(NO_COUNT_ZERO_WEIGHT == 1)
				{
					$gv_query = xtc_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . xtc_get_prid($products_id) . "'");
					$gv_result = xtc_db_fetch_array($gv_query);
					if($gv_result['products_weight'] <= MINIMUM_WEIGHT)
					{
						$no_count = true;
					}
				}

				if($no_count == false)
				{
					$total_items += $this->get_quantity($products_id);
				}
			}
		}

		return $total_items;
	}

	protected function wrapped_db_perform($p_called_from, $p_table, $p_data_array = array(), $p_action = 'insert', $p_parameters = '', $p_link = 'db_link', $p_quoted_values = true)
	{
		return xtc_db_perform($p_table, $p_data_array, $p_action, $p_parameters, $p_link, $p_quoted_values);
	}

	// DEPRECATED (wrong method name)
	function count_contents_virtual()
	{
		return $this->count_contents_non_virtual();
	}
	// ------------------------ ICW CREDIT CLASS Gift Voucher Addittion-------------------------------End
	//GV Code End
}