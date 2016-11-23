<?php
/* --------------------------------------------------------------
  CheckoutControl.inc.php 2014-10-20 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_confirmation.php,v 1.137 2003/05/07); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_confirmation.php,v 1.21 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_confirmation.php 1277 2005-10-01 17:02:59Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_check_stock.inc.php');

MainFactory::load_class('DataProcessing');

class CheckoutControl extends DataProcessing
{
	protected function set_validation_rules()
	{
		$this->validation_rules_array['excluded_payment_methods_array'] = array('type' => 'array');
	}

	public function check_stock($p_force = false)
	{
		if($_SESSION['customers_status']['customers_status_show_price'] != '1')
		{
			return false;
		}

		if(isset($_SESSION['tmp_oID']) && empty($_SESSION['tmp_oID']) === false && $p_force === false)
		{
			return true;
		}

		// minimum/maximum order value
		if($_SESSION['cart']->show_total() > 0)
		{
			if($_SESSION['cart']->show_total() < $_SESSION['customers_status']['customers_status_min_order'])
			{
				$_SESSION['allow_checkout'] = 'false';
			}

			if($_SESSION['customers_status']['customers_status_max_order'] != 0)
			{
				if($_SESSION['cart']->show_total() > $_SESSION['customers_status']['customers_status_max_order'])
				{
					$_SESSION['allow_checkout'] = 'false';
				}
			}
		}

		// check if checkout is allowed
		if($_SESSION['allow_checkout'] == 'false')
		{
			return false;
		}

		// if there is nothing in the customers cart, redirect them to the shopping cart page
		if($_SESSION['cart']->count_contents() <= 0)
		{
			return false;
		}

		if(STOCK_ALLOW_CHECKOUT != 'true')
		{
			$products = $_SESSION['cart']->get_products();
			$any_out_of_stock = false;

			$coo_properties = MainFactory::create_object('PropertiesControl');

			// check properties_combis quantity
			$t_products_quantity_array = array();
			for($i = 0, $n = sizeof($products); $i < $n; $i ++)
			{
				$t_combis_id = $coo_properties->extract_combis_id($products[$i]['id']);
				$t_extracted_products_id = xtc_get_prid($products[$i]['id']);
				$coo_products = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $t_extracted_products_id)));
				$use_properties_combis_quantity = $coo_products->get_data_value('use_properties_combis_quantity');
				if($use_properties_combis_quantity == 1 || ($use_properties_combis_quantity == 0 && ATTRIBUTE_STOCK_CHECK == 'false' && STOCK_CHECK == 'true'))
				{
					$t_products_quantity_array[$t_extracted_products_id] += $products[$i]['quantity'];
				}
			}

			foreach($t_products_quantity_array as $t_product_id => $t_product_quantity)
			{
				// check article quantity
				$t_mark_stock = xtc_check_stock($t_product_id, $t_product_quantity);
				if($t_mark_stock)
				{
					$t_products_quantity_array[$t_product_id] = $t_mark_stock;
				}
				else
				{
					unset($t_products_quantity_array[$t_product_id]);
				}
			}

			for($i = 0, $n = sizeof($products); $i < $n; $i++)
			{
				$t_extracted_products_id = xtc_get_prid($products[$i]['id']);

				$p_products_array_copy = $products;
				
				if(strstr($products[$i]['id'], '{') != false)
				{
					if(STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true')
					{
						$gm_attribute_array = explode('{', str_replace('}', '{', $products[$i]['id']));
						$gm_attributes = $_SESSION['cart']->contents[$products[$i]['id']]['attributes'];

						foreach($gm_attributes as $t_options_id => $t_options_values_id)
						{
							$gm_attribute_stock = xtc_db_query("SELECT a.products_attributes_id
																FROM products_attributes a
																LEFT JOIN products_attributes_download AS d ON (a.products_attributes_id = d.products_attributes_id)
																WHERE
																	a.products_id = '" . (int)$gm_attribute_array[0] . "' AND
																	a.options_id = '" . (int)$t_options_id . "' AND
																	a.options_values_id = '" . (int)$t_options_values_id . "' AND
																	(a.attributes_stock - " . (double)$_SESSION['cart']->contents[$products[$i]['id']]['qty'] . ") < 0 AND
																	d.products_attributes_id IS NULL");
							if(xtc_db_num_rows($gm_attribute_stock) == 1)
							{
								$any_out_of_stock = true;
							}
						}
					}
					// combine all customizer products for checking stock
					elseif(STOCK_CHECK == 'true')
					{
						preg_match('/(.*)\{[\d]+\}0$/', $products[$i]['id'], $t_matches_array);

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

						$t_mark_stock = xtc_check_stock($products[$i]['id'], $t_quantities);

						if($t_mark_stock !== '')
						{
							$any_out_of_stock = true;
						}
					}
				}
				else
				{
					$t_combis_id = $coo_properties->extract_combis_id($products[$i]['id']);

					// product without properties
					if($t_combis_id == '' && STOCK_CHECK == 'true' && xtc_check_stock($products[$i]['id'], $products[$i]['quantity']))
					{
						$any_out_of_stock = true;
					}
					elseif($t_combis_id != '' && STOCK_CHECK == 'true')
					{
						// product with properties
						$t_use_combis_quantity = $coo_properties->get_use_properties_combis_quantity($products[$i]['id']);

						if($t_use_combis_quantity != 3)
						{
							if(($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2)
							{
								// check combi quantity
								$t_combis_quantity = $coo_properties->get_properties_combis_quantity($t_combis_id);

								if($t_combis_quantity < $products[$i]['quantity'])
								{
									$any_out_of_stock = true;
								}
							}
							else if($t_use_combis_quantity == 1 && xtc_check_stock($products[$i]['id'], $products[$i]['quantity'])) // check article quantity
							{
								$any_out_of_stock = true;
							}

							if(array_key_exists($t_extracted_products_id, $t_products_quantity_array))
							{
								$any_out_of_stock = true;
							}
						}
					}
				}
			}

			if($any_out_of_stock)
			{
				return false;
			}
		}

		return true;
	}

	public function check_shipping()
	{
		// if no shipping method has been selected, redirect the customer to the shipping method selection page
		if((!isset($_SESSION['shipping']) || empty($_SESSION['shipping']))
			&& $_SESSION['cart']->content_type != 'virtual'
			&& $_SESSION['cart']->content_type != 'virtual_weight'
			&& $_SESSION['cart']->count_contents_non_virtual() != 0)
		{
			return false;
		}

		// check if country of selected shipping address is not allowed
		if($_SESSION['sendto'] !== false 
		   && $_SESSION['cart']->content_type != 'virtual'
		   && $_SESSION['cart']->content_type != 'virtual_weight'
		   && $_SESSION['cart']->count_contents_non_virtual() != 0)
		{
			if($this->check_country_by_address_book_id($_SESSION['sendto']) == false)
			{
				return false;
			}
		}

		return true;
	}

	public function check_cart_id()
	{
		// avoid hack attempts during the checkout procedure by checking the internal cartID
		if(isset($_SESSION['cart']->cartID) && isset($_SESSION['cartID']) && $_SESSION['cart']->cartID != $_SESSION['cartID'])
		{
			return false;
		}

		return true;
	}

	public function check_payment()
	{
		return $this->check_country_by_address_book_id($_SESSION['billto']);
	}

	/** checks billing address for packstation address */
	public function check_billing_address_for_packstation()
	{
		$addressValid = true;

		$billToAddress = $this->getAddressData($_SESSION['billto']);
		if(preg_match('/(packstation|filiale)/i', $billToAddress['entry_street_address']) === 1)
		{
			$addressValid = false;
		}

		return $addressValid;
	}

	/** retrieves a row from table address_book
	@param $ab_id int primary key from address book
	@return array may be empty if entry is not found
	*/
	public function getAddressData($ab_id)
	{
		$addressBookEntry = array();
		$addressBookQuery = 'SELECT * FROM `address_book` WHERE `address_book_id` = :ab_id';
		$addressBookQuery = strtr($addressBookQuery, array(':ab_id' => (int)$ab_id));
		$useDBCaching = false;
		$addressBookResult = xtc_db_query($addressBookQuery, 'db_link', $useDBCaching);
		while($addressBookRow = xtc_db_fetch_array($addressBookResult))
		{
			$addressBookEntry = $addressBookRow;
		}
		return $addressBookEntry;
	}

	public function check_country_by_address_book_id($p_address_book_id)
	{
		$c_address_book_id = (int)$p_address_book_id;

		// check if country of selected payment address is not allowed
		$t_country_check_sql = "SELECT a.address_book_id
								FROM
									" . TABLE_ADDRESS_BOOK . " a,
									" . TABLE_COUNTRIES . " c
								WHERE
									a.address_book_id = '" . $c_address_book_id . "' AND
									a.entry_country_id = c.countries_id AND
									c.status = 1";
		$t_country_check_result = xtc_db_query($t_country_check_sql);
		if(xtc_db_num_rows($t_country_check_result) == 0)
		{
			return false;
		}

		return true;
	}

	public function is_virtual($p_coo_order)
	{
		if($p_coo_order->content_type != 'virtual'
			&& ($p_coo_order->content_type != 'virtual_weight')
			&& ($_SESSION['cart']->count_contents_non_virtual() > 0))
		{
			return false;
		}

		return true;
	}
}
