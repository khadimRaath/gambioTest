<?php
/* --------------------------------------------------------------
  shopping_cart.php 2016-09-26
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
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shopping_cart.php 1534 2006-08-20 19:39:22Z mz $)

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

// include needed functions
require_once (DIR_FS_INC . 'xtc_create_random_value.inc.php');
require_once (DIR_FS_INC . 'xtc_get_prid.inc.php');
require_once (DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once (DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once (DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once (DIR_FS_INC . 'xtc_get_tax_description.inc.php');

class shoppingCart_ORIGIN
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
			while(list ($products_id, ) = each($this->contents))
			{
				$qty = $this->contents[$products_id]['qty'];
				$product_query = xtc_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . xtc_db_input($products_id) . "'");
				if(!xtc_db_num_rows($product_query))
				{
					$sql_data_array = array();
					$sql_data_array['customers_id'] = $_SESSION['customer_id'];
					$sql_data_array['products_id'] = xtc_db_input($products_id);
					$sql_data_array['customers_basket_quantity'] = xtc_db_input($qty);
					$sql_data_array['customers_basket_date_added'] = date('Ymd');
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, $sql_data_array);
					if(isset($this->contents[$products_id]['attributes']))
					{
						reset($this->contents[$products_id]['attributes']);
						while(list ($option, $value) = each($this->contents[$products_id]['attributes']))
						{
							$sql_data_array = array();
							$sql_data_array['customers_id'] = $_SESSION['customer_id'];
							$sql_data_array['products_id'] = xtc_db_input($products_id);
							$sql_data_array['products_options_id'] = xtc_db_input($option);
							$sql_data_array['products_options_value_id'] = xtc_db_input($value);
							$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, $sql_data_array);
						}
					}
				}
				else
				{
					$sql_data_array = array();
					$sql_data_array['customers_basket_quantity'] = xtc_db_input($qty);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, $sql_data_array, 'update', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
				}
			}
		}

		// reset per-session cart contents, but not the database contents
		$this->reset(false);

		$products_query = xtc_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "'");
		while($products = xtc_db_fetch_array($products_query))
		{
			// BOF GM_MOD
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
					$attributes_query = xtc_db_query("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . xtc_db_input($products['products_id']) . "'");
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
			// EOF GM_MOD
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
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\'');
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\'');
		}

		unset($this->cartID);
		if(isset($_SESSION['cartID']))
		{
			unset($_SESSION['cartID']);
		}
	}

	function add_cart($products_id, $qty = '1', $attributes = '', $notify = true, $p_products_properties_combis_id = 0)
	{
		global $new_products_id_in_cart;

		if(!$this->allowed_quantity($products_id, $qty))
		{
			return false;
		}
		
		if(!preg_match('/[0-9]+\{[0-9]+\}[0-9{}]*x[0-9]+/', $products_id))
		{
			#GM_MOD:properties BOF
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
			#GM_MOD:properties EOF
		}

		if($notify == true)
		{
			$_SESSION['new_products_id_in_cart'] = $products_id;
		}

		if($this->in_cart($products_id))
		{
			$this->update_quantity($products_id, $qty, $attributes);
		}
		else
		{
			$this->contents[$products_id] = array('qty' => $qty);
			// insert into database
			if(isset($_SESSION['customer_id']))
			{
				$sql_data_array = array();
				$sql_data_array['customers_id'] = $_SESSION['customer_id'];
				$sql_data_array['products_id'] = xtc_db_input($products_id);
				$sql_data_array['customers_basket_quantity'] = xtc_db_input($qty);
				$sql_data_array['customers_basket_date_added'] = date('Ymd');
				$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, $sql_data_array);
			}

			if(is_array($attributes))
			{
				reset($attributes);
				while(list ($option, $value) = each($attributes))
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
						$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, $sql_data_array);
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
		if(empty($quantity) || !$this->allowed_quantity($products_id, $quantity))
		{
			return true; // nothing needs to be updated if theres no quantity, so we return true..
		}

		$this->contents[$products_id] = array('qty' => $quantity);
		// update database
		if(isset($_SESSION['customer_id']))
		{
			$sql_data_array = array();
			$sql_data_array['customers_basket_quantity'] = xtc_db_input($quantity);
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, $sql_data_array, 'update', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
		}

		if(is_array($attributes))
		{
			reset($attributes);
			while(list ($option, $value) = each($attributes))
			{
				$this->contents[$products_id]['attributes'][$option] = $value;
				// update database
				if(isset($_SESSION['customer_id']))
				{
					$sql_data_array = array();
					$sql_data_array['products_options_value_id'] = (int)$value;
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, $sql_data_array, 'update', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\' AND products_options_id = \'' . (int)$option . '\'');
				}
			}
		}

		// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
		$this->cartID = $this->generate_cart_id();
	}

	function cleanup()
	{
		reset($this->contents);
		while(list ($key, ) = each($this->contents))
		{
			// BOF GM_MOD:
			if($this->contents[$key]['qty'] <= 0)
			{
				unset($this->contents[$key]);
				// remove from database
				if(xtc_session_is_registered('customer_id'))
				{
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($key) . '\'');
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($key) . '\'');
				}
			}
		}
	}

	function count_contents()
	{ // get total number of items in cart
		$total_items = 0;
		if(is_array($this->contents))
		{
			reset($this->contents);
			while(list ($products_id, ) = each($this->contents))
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
		// BOF GM_MOD:
		unset($this->contents[$products_id]);
		// remove from database
		if(xtc_session_is_registered('customer_id'))
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, array(), 'delete', 'customers_id = \'' . $_SESSION['customer_id'] . '\' AND products_id = \'' . xtc_db_input($products_id) . '\'');
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
			while(list ($products_id, ) = each($this->contents))
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
		$this->tax = array();

		if(!is_array($this->contents))
		{
			return 0;
		}

		reset($this->contents);
		while(list ($products_id, ) = each($this->contents))
		{
			$qty = $this->contents[$products_id]['qty'];

			// products price
			$product_query = xtc_db_query("select products_id, products_price, products_discount_allowed, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . xtc_db_input(xtc_get_prid($products_id)) . "'");
			if($product = xtc_db_fetch_array($product_query))
			{
				$products_price = $xtPrice->xtcGetPrice($products_id, $format = false, $qty, $product['products_tax_class_id'], $product['products_price'], 0, 0, true, true);

				$this->total += $products_price * $qty;

				# set combis weight
				$t_properties_weight = $this->properties_weight($products_id, $product['products_weight']);
				if($t_properties_weight == 0)
				{
					$t_properties_weight = $product['products_weight'];
				}
				$this->weight += ($qty * $t_properties_weight);

				// attributes price
				$attribute_price = 0;
				if(isset($this->contents[$products_id]['attributes']))
				{
					reset($this->contents[$products_id]['attributes']);
					while(list ($option, $value) = each($this->contents[$products_id]['attributes']))
					{
						$values = $xtPrice->xtcGetOptionPrice($product['products_id'], $option, $value);

						$this->weight += $values['weight'] * $qty;
						$this->total += $values['price'] * $qty;
						$attribute_price+=$values['price'];
					}
				}
				
				if($product['products_tax_class_id'] != 0)
				{
					if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
					{
						$products_price_tax = $products_price - ($products_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
						$attribute_price_tax = $attribute_price - ($attribute_price / 100 * $_SESSION['customers_status']['customers_status_ot_discount']);
					}

					$products_tax = $xtPrice->TAX[$product['products_tax_class_id']];
					$products_tax_description = xtc_get_tax_description($product['products_tax_class_id']);

					// price incl tax
					if($_SESSION['customers_status']['customers_status_show_price_tax'] == '1')
					{
						if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
						{
							$this->tax[$product['products_tax_class_id']]['value'] += ((($products_price_tax + $attribute_price_tax) / (100 + $products_tax)) * $products_tax) * $qty;
							$this->tax[$product['products_tax_class_id']]['desc'] = sprintf(TAX_INFO_INCL, $products_tax . '%');
						}
						else
						{
							$this->tax[$product['products_tax_class_id']]['value'] += ((($products_price + $attribute_price) / (100 + $products_tax)) * $products_tax) * $qty;
							$this->tax[$product['products_tax_class_id']]['desc'] = sprintf(TAX_INFO_INCL, $products_tax . '%');
						}
					}

					// excl tax + tax at checkout
					if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
					{
						if($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1)
						{
							$this->tax[$product['products_tax_class_id']]['value'] += (($products_price_tax + $attribute_price_tax) / 100) * ($products_tax) * $qty;
							$this->total+=(($products_price_tax + $attribute_price_tax) / 100) * ($products_tax) * $qty;
							$this->tax[$product['products_tax_class_id']]['desc'] = sprintf(TAX_INFO_EXCL, $products_tax . '%');
						}
						else
						{
							$this->tax[$product['products_tax_class_id']]['value'] += (($products_price + $attribute_price) / 100) * ($products_tax) * $qty;
							$this->total+= (($products_price + $attribute_price) / 100) * ($products_tax) * $qty;
							$this->tax[$product['products_tax_class_id']]['desc'] = sprintf(TAX_INFO_EXCL, $products_tax . '%');
						}
					}
				}

				// round total value and total tax to avoid rounding problems if quantity is not an integer
				if($qty != (int)$qty)
				{
					foreach($this->tax as $t_tax_class_id => $t_content_array)
					{
						$this->tax[$t_tax_class_id]['value'] = round($this->tax[$t_tax_class_id]['value'], 2);
					}

					$this->total = round($this->total, 2);
				}
			}
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

	function properties_weight($p_products_id, $p_old_weight) #parameter sample: 1x46
	{
		$t_output_weight = 0;

		$coo_properties_control = MainFactory::create_object('PropertiesControl');
		$t_combis_id = $coo_properties_control->extract_combis_id($p_products_id);

		if($t_combis_id != '')
		{
			$t_combis_weight = $coo_properties_control->get_properties_combis_weight($t_combis_id);

			$coo_data_object = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $p_products_id)));
			if($coo_data_object->get_data_value('use_properties_combis_weight') == 0)
			{
				# 0 = keep old products_weight and add new combis_weight
				$t_output_weight = $p_old_weight + $t_combis_weight;
			}
			else
			{
				# 1 = replace old products_weight and use new combis_weight only
				$t_output_weight = $t_combis_weight;
			}
		}
		//echo($t_output_weight).'x';
		return $t_output_weight;
	}

	function get_products()
	{
		global $xtPrice, $main;

		if(!is_array($this->contents))
		{
			return false;
		}
		
		$product = MainFactory::create('product', 0, $_SESSION['languages_id']);
		
		$products_array = array();
		reset($this->contents);
		while(list ($products_id, ) = each($this->contents))
		{
			if($this->contents[$products_id]['qty'] != 0 || $this->contents[$products_id]['qty'] != '')
			{
				$products_query = xtc_db_query("SELECT
													p.products_id,
													pd.products_name,
													pd.checkout_information,
													p.products_shippingtime,
													p.products_image,
													p.products_model,
													p.products_price,
													p.products_discount_allowed,
													p.products_weight,
													p.products_tax_class_id,
													p.product_type,
													p.products_vpe,
													p.products_vpe_status,
													p.products_vpe_value,
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

					# set combis weight
					$t_properties_weight = $this->properties_weight($products_id, $products['products_weight']);
					if($t_properties_weight == 0)
					{
						$t_properties_weight = $products['products_weight'];
					}

					$products_array[] = array(
						'id' => $products_id,
						'name' => $products['products_name'],
						'checkout_information' => $products['checkout_information'],
						'model' => $products['products_model'],
						'image' => $products['products_image'],
						'price' => $products_price,
						'vpe' => $product->getVPEtext($products, $products_price),
						'quantity' => $this->contents[$products_id]['qty'],
						'weight' => $t_properties_weight,
						'shipping_time' => $main->getShippingStatusName($products['products_shippingtime']),
						'final_price' => ($products_price),
						'tax_class_id' => $products['products_tax_class_id'],
						'quantity_unit_id' => $products['quantity_unit_id'],
						'unit_name' => $products['unit_name'],
						'attributes' => $this->contents[$products_id]['attributes'],
						'product_type' => $products['product_type']
					);
				}
			}
		}
		return $products_array;
	}
	
	
	function count_products()
	{
		$count = array_map(function ($value)
		{
			return $value['quantity'];
		}, $this->get_products());
		
		return array_sum($count);
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

	function show_tax($format = true)
	{
		global $xtPrice;
		$this->calculate();
		$output = "";
		$val = 0;
		foreach($this->tax as $key => $value)
		{
			if($this->tax[$key]['value'] > 0)
			{
				$output .= $this->tax[$key]['desc'] . ": " . $xtPrice->xtcFormat($this->tax[$key]['value'], true) . "<br />";
				$val += $this->tax[$key]['value'];
			}
		}
		if($format)
		{
			return $output;
		}
		else
		{
			return $val;
		}
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
			while(list ($products_id, ) = each($this->contents))
			{
				if($this->has_product_virtual_tax_class($products_id))
				{
					switch($this->content_type)
					{
						case 'physical' :
							$this->content_type = 'mixed';
							$this->update_session_customer_b2b_status();
							return $this->content_type;
						default :
							$this->content_type = 'virtual';
							continue(2);
					}
				}
				
				if(isset($this->contents[$products_id]['attributes']))
				{
					reset($this->contents[$products_id]['attributes']);
					while(list (, $value) = each($this->contents[$products_id]['attributes']))
					{
						$virtual_check_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . xtc_db_input($products_id) . "' and pa.options_values_id = '" . xtc_db_input($value) . "' and pa.products_attributes_id = pad.products_attributes_id");
						$virtual_check = xtc_db_fetch_array($virtual_check_query);

						if($virtual_check['total'] > 0)
						{
							switch($this->content_type)
							{
								case 'physical' :
									$this->content_type = 'mixed';
									$this->update_session_customer_b2b_status();
									return $this->content_type;
									break;

								default :
									$this->content_type = 'virtual';
									break;
							}
						}
						else
						{
							switch($this->content_type)
							{
								case 'virtual' :
									$this->content_type = 'mixed';
									$this->update_session_customer_b2b_status();
									return $this->content_type;
									break;

								default :
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
						case 'virtual' :
							$this->content_type = 'mixed';
							$this->update_session_customer_b2b_status();
							return $this->content_type;
							break;

						default :
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

		$this->update_session_customer_b2b_status();
		
		return $this->content_type;
	}

	function unserialize($broken)
	{
		for(reset($broken); $kv = each($broken);)
		{
			$key = $kv['key'];
			if(gettype($this->$key) != "user function")
				$this->$key = $kv['value'];
		}
	}

	// GV Code Start
	// ------------------------ ICW CREDIT CLASS Gift Voucher Addittion-------------------------------Start
	// amend count_contents to show nil contents for shipping
	// as we don't want to quote for 'virtual' item
	// GLOBAL CONSTANTS if NO_COUNT_ZERO_WEIGHT is true then we don't count any product with a weight
	// which is less than or equal to MINIMUM_WEIGHT
	// otherwise we just don't count gift certificates

	function count_contents_non_virtual()
	{ // get total number of items in cart disregard gift vouchers and downloads
		$total_items = 0;
		if(is_array($this->contents))
		{
			reset($this->contents);
			while(list ($products_id, ) = each($this->contents))
			{
				$no_count = false;
				$gv_query = xtc_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . xtc_db_input($products_id) . "'");
				$gv_result = xtc_db_fetch_array($gv_query);
				if(preg_match('/^GIFT/', $gv_result['products_model']))
				{
					$no_count = true;
				}
				if(NO_COUNT_ZERO_WEIGHT == 1)
				{
					$gv_query = xtc_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . xtc_db_input(xtc_get_prid($products_id)) . "'");
					$gv_result = xtc_db_fetch_array($gv_query);
					if($gv_result['products_weight'] <= MINIMUM_WEIGHT)
					{
						$no_count = true;
					}
				}

				// check if product is a download
				if(isset($this->contents[$products_id]['attributes']))
				{
					reset($this->contents[$products_id]['attributes']);
					while(list (, $value) = each($this->contents[$products_id]['attributes']))
					{
						$virtual_check_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . xtc_db_input($products_id) . "' and pa.options_values_id = '" . xtc_db_input($value) . "' and pa.products_attributes_id = pad.products_attributes_id");
						$virtual_check = xtc_db_fetch_array($virtual_check_query);

						if($virtual_check['total'] > 0)
						{
							$no_count = true;
						}
					}
				}

				if(!$no_count && !$this->has_product_virtual_tax_class($products_id))
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

	/**
	 * Check if product has tax class with "EEL" as tax class description
	 * @param int $p_products_id
	 * @return bool is virtual
	 */
	protected function has_product_virtual_tax_class($p_products_id)
	{
		$c_products_id = (int)$p_products_id;
		
		$t_query = 'SELECT p.products_tax_class_id 
					FROM
						products p ,
						tax_class t 
					WHERE
						p.products_id = ' . $c_products_id . ' AND
						p.products_tax_class_id = t.tax_class_id AND
						t.tax_class_description LIKE "EEL%"';
		$t_result = xtc_db_query($t_query);
		
		$t_is_virtual = xtc_db_num_rows($t_result) == true;
		
		return $t_is_virtual;
	}


	protected function update_session_customer_b2b_status()
	{
		$c_address_book_id = 0;

		if(isset($_SESSION['billto']) && $this->content_type === 'virtual')
		{
			$c_address_book_id = (int)$_SESSION['billto'];

			if(isset($_SESSION['sendto']))
			{
				$_SESSION['sendto'] = $_SESSION['billto'];
			}
		}
		elseif(isset($_SESSION['sendto']))
		{
			$c_address_book_id = (int)$_SESSION['sendto'];
		}

		if($c_address_book_id !== 0)
		{
			$t_query = 'SELECT customer_b2b_status FROM address_book WHERE address_book_id = ' . $c_address_book_id;
			$t_result = xtc_db_query($t_query);

			if(xtc_db_num_rows($t_result))
			{
				$t_row = xtc_db_fetch_array($t_result);
				update_customer_b2b_status($t_row['customer_b2b_status']);
			}
		}
	}


	protected function allowed_quantity($p_products_id, $p_quantity)
	{
		$get_products_data = xtc_db_query("SELECT gm_min_order, gm_graduated_qty FROM products WHERE products_id = '" . (int)$p_products_id . "'");
		if(xtc_db_num_rows($get_products_data) == 1)
		{
			$products_data = xtc_db_fetch_array($get_products_data);
			if(empty($products_data['gm_min_order']))
			{
				$products_data['gm_min_order'] = 1;
			}
			if(empty($products_data['gm_graduated_qty']))
			{
				$products_data['gm_graduated_qty'] = 1;
			}
			if($p_quantity < $products_data['gm_min_order'])
			{
				return false;
			}
			$result = $p_quantity / $products_data['gm_graduated_qty'];
			$result = round($result, 4); // workaround for next if-case to avoid calculating failure
			if((int)$result != $result)
			{
				return false;
			}
			
			return true;
		}

		return false;
	}
}