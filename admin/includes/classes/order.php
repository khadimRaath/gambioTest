<?php
/* --------------------------------------------------------------
  order.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(order.php,v 1.6 2003/02/06); www.oscommerce.com
  (c) 2003	 nextcommerce (order.php,v 1.12 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: order.php 1037 2005-07-17 15:25:32Z gwinger $)

  Released under the GNU General Public License
  --------------------------------------------------------------
  Third Party contribution:

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  -------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class order_ORIGIN
{
	var $info, $totals, $products, $customer, $delivery;

	function __construct($order_id)
	{
		$this->info = array();
		$this->totals = array();
		$this->products = array();
		$this->customer = array();
		$this->delivery = array();

		$this->query($order_id);
	}

	function query($order_id)
	{
		$order_query = xtc_db_query("SELECT
										o.customers_name,
										o.customers_firstname,
										o.customers_lastname,
										o.customers_gender,
										o.customers_cid,
										o.customers_id,
										o.customers_vat_id,
										o.customers_company,
										o.customers_street_address,
										o.customers_house_number,
										o.customers_additional_info,
										o.customers_suburb,
										o.customers_city,
										o.customers_postcode,
										o.customers_state,
										o.customers_country,
										o.customers_telephone,
										o.customers_email_address,
										o.customers_address_format_id,
										o.delivery_gender,
										o.delivery_name,
										o.delivery_firstname,
										o.delivery_lastname,
										o.delivery_company,
										o.delivery_street_address,
										o.delivery_house_number,
										o.delivery_additional_info,
										o.delivery_suburb,
										o.delivery_city,
										o.delivery_postcode,
										o.delivery_state,
										o.delivery_country,
										o.delivery_country_iso_code_2,
										o.delivery_address_format_id,
										o.billing_gender,
										o.billing_name,
										o.billing_firstname,
										o.billing_lastname,
										o.billing_company,
										o.billing_street_address,
										o.billing_house_number,
										o.billing_additional_info,
										o.billing_suburb,
										o.billing_city,
										o.billing_postcode,
										o.billing_state,
										o.billing_country,
										o.billing_country_iso_code_2,
										o.billing_address_format_id,
										o.payment_method,
										o.payment_class,
										o.shipping_class,
										o.cc_type,
										o.cc_owner,
										o.cc_number,
										o.cc_expires,
										o.cc_cvv,
										o.comments,
										o.currency,
										o.currency_value,
										o.date_purchased,
										o.orders_status,
										o.last_modified,
										o.customers_status,
										o.customers_status_name,
										o.customers_status_image,
										o.customers_ip,
										o.customers_status_discount,
										o.language,
										o.orders_hash,
										o.abandonment_download,
										o.abandonment_service,
										l.languages_id,
										l.code
									FROM 
										" . TABLE_ORDERS . " o
										LEFT JOIN " . TABLE_LANGUAGES . " l ON (o.language = l.directory)
									WHERE orders_id = '" . (int)$order_id . "'");

		$order = xtc_db_fetch_array($order_query);

		$totals_query = xtc_db_query("SELECT 
											title, 
											text 
										FROM " . TABLE_ORDERS_TOTAL . " 
										WHERE orders_id = '" . (int)$order_id . "' 
										ORDER BY sort_order");
		while($totals = xtc_db_fetch_array($totals_query))
		{
			$this->totals[] = array('title' => $totals['title'],
									'text' => $totals['text']);
		}

		$this->info = array('currency' => $order['currency'],
							'currency_value' => $order['currency_value'],
							'payment_method' => $order['payment_method'],
							'payment_class' => $order['payment_class'],
							'shipping_class' => $order['shipping_class'],
							'status' => $order['customers_status'],
							'status_name' => $order['customers_status_name'],
							'status_image' => $order['customers_status_image'],
							'status_discount' => $order['customers_status_discount'],
							'cc_type' => $order['cc_type'],
							'cc_owner' => $order['cc_owner'],
							'cc_number' => $order['cc_number'],
							'cc_expires' => $order['cc_expires'],
							'cc_cvv' => $order['cc_cvv'],
							'comments' => $order['comments'],
							'language' => $order['language'],
							'languages_id' => $order['languages_id'],
							'language_code' => $order['code'],
							'date_purchased' => $order['date_purchased'],
							'orders_status' => $order['orders_status'],
							'last_modified' => $order['last_modified'],
							'orders_hash' => $order['orders_hash'],
							'abandonment_download' => $order['abandonment_download'],
							'abandonment_service' => $order['abandonment_service']);

		$this->customer = array('gender' => $order['customers_gender'],
								'name' => $order['customers_name'],
								'firstname' => $order['customers_firstname'],
								'lastname' => $order['customers_lastname'],
								'company' => $order['customers_company'],
								'csID' => $order['customers_cid'],
								'vat_id' => $order['customers_vat_id'],
								'shop_id' => $order['shop_id'],
								'ID' => $order['customers_id'],
								'cIP' => $order['customers_ip'],
								'street_address' => $order['customers_street_address'],
								'house_number' => $order['customers_house_number'],
								'additional_address_info' => $order['customers_additional_info'],
								'suburb' => $order['customers_suburb'],
								'city' => $order['customers_city'],
								'postcode' => $order['customers_postcode'],
								'state' => $order['customers_state'],
								'country' => $order['customers_country'],
								'format_id' => $order['customers_address_format_id'],
								'telephone' => $order['customers_telephone'],
								'email_address' => $order['customers_email_address']);

		$this->delivery = array('gender' => $order['delivery_gender'],
								'name' => $order['delivery_name'],
								'firstname' => $order['delivery_firstname'],
								'lastname' => $order['delivery_lastname'],
								'company' => $order['delivery_company'],
								'street_address' => $order['delivery_street_address'],
								'house_number' => $order['delivery_house_number'],
								'additional_address_info' => $order['delivery_additional_info'],
								'suburb' => $order['delivery_suburb'],
								'city' => $order['delivery_city'],
								'postcode' => $order['delivery_postcode'],
								'state' => $order['delivery_state'],
								'country' => $order['delivery_country'],
								'country_iso_code_2' => $order['delivery_country_iso_code_2'],
								'format_id' => $order['delivery_address_format_id']);

		$this->billing = array('gender' => $order['billing_gender'],
								'name' => $order['billing_name'],
								'firstname' => $order['billing_firstname'],
								'lastname' => $order['billing_lastname'],
								'company' => $order['billing_company'],
								'street_address' => $order['billing_street_address'],
								'house_number' => $order['billing_house_number'],
								'additional_address_info' => $order['billing_additional_info'],
								'suburb' => $order['billing_suburb'],
								'city' => $order['billing_city'],
								'postcode' => $order['billing_postcode'],
								'state' => $order['billing_state'],
								'country' => $order['billing_country'],
								'country_iso_code_2' => $order['billing_country_iso_code_2'],
								'format_id' => $order['billing_address_format_id']);

		$index = 0;
		$t_sql = "SELECT
						op.orders_products_id,
						op.products_id,
						op.products_name,
						op.checkout_information,
						op.products_model,
						op.products_price,
						op.products_tax,
						op.products_quantity,
						op.final_price,
						op.allow_tax,
						op.products_discount_made,
						op.product_type,
						op.properties_combi_price,
						op.properties_combi_model,
						opqu.quantity_unit_id,
						opqu.unit_name
					FROM " . TABLE_ORDERS_PRODUCTS . " op
					LEFT JOIN orders_products_quantity_units opqu USING (orders_products_id)
					WHERE op.orders_id = '" . (int)$order_id . "'";
		$orders_products_query = xtc_db_query($t_sql);

		while($orders_products = xtc_db_fetch_array($orders_products_query))
		{
			$this->products[$index] = array('qty' => $orders_products['products_quantity'],
											'name' => $orders_products['products_name'],
											'checkout_information' => $orders_products['checkout_information'],
											'id' => $orders_products['products_id'],
											'opid' => $orders_products['orders_products_id'],
											'model' => $orders_products['products_model'],
											'tax' => $orders_products['products_tax'],
											'price' => $orders_products['products_price'],
											'discount' => $orders_products['products_discount_made'],
											'final_price' => $orders_products['final_price'],
											'allow_tax' => $orders_products['allow_tax'],
											'quantity_unit_id' => $orders_products['quantity_unit_id'],
											'product_type' => (int)$orders_products['product_type'],
											'unit_name' => $orders_products['unit_name'],
											'properties_combi_price' => $orders_products['properties_combi_price'],
											'properties_combi_model' => $orders_products['properties_combi_model']);
			
			# attributes
			$subindex = 0;
			$attributes_query = xtc_db_query("SELECT 
					orders_products_attributes_id,
					products_options, 
					products_options_values, 
					options_values_price, 
					price_prefix, 
					options_id, 
					options_values_id 
				FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " 
				WHERE 
					orders_id = '" . xtc_db_input($order_id) . "' AND 
					orders_products_id = '" . $orders_products['orders_products_id'] . "'");
			if(xtc_db_num_rows($attributes_query))
			{
				while($attributes = xtc_db_fetch_array($attributes_query))
				{
					$this->products[$index]['attributes'][$subindex] = array('orders_products_attributes_id' => $attributes['orders_products_attributes_id'],
																				'option' => $attributes['products_options'],
																				'value' => $attributes['products_options_values'],
																				'prefix' => $attributes['price_prefix'],
																				'price' => $attributes['options_values_price'],
																				'options_id' => $attributes['options_id'],
																				'options_values_id' => $attributes['options_values_id']);
					$subindex++;
				}
			}

			# properties
			$subindex = 0;
			$t_properties_query = xtc_db_query("select * from orders_products_properties where orders_products_id = '" . $orders_products['orders_products_id'] . "'");
			if(xtc_db_num_rows($t_properties_query))
			{
				while($t_properties_array = xtc_db_fetch_array($t_properties_query))
				{
					$this->products[$index]['properties_combis_id'] = $t_properties_array['products_properties_combis_id'];
					
					$this->products[$index]['properties'][$subindex] = array('properties_name' => $t_properties_array['properties_name'],
																				'values_name' => $t_properties_array['values_name'],
																				'price_type' => $t_properties_array['properties_price_type'],
																				'price' => (double)$t_properties_array['properties_price']);
					$subindex++;
				}
			}

			$index++;
		}
	}
	
	function get_product_array($p_orders_products_id)
	{
		foreach($this->products as $t_product_array)
		{
			if($t_product_array['opid'] == $p_orders_products_id)
			{
				return $t_product_array;
			}
		}
	}
	
	function get_attributes_array($p_orders_products_id, $p_orders_products_attributes_id)
	{
		$t_product_array = $this->get_product_array($p_orders_products_id);
		
		if(isset($t_product_array['attributes']))
		{
			foreach($t_product_array['attributes'] as $t_attributes_array)
			{
				if($t_attributes_array['orders_products_attributes_id'] == $p_orders_products_attributes_id)
				{
					return $t_attributes_array;
				}
			}
		}
		
		return false;
	}
}

MainFactory::load_origin_class('order');
