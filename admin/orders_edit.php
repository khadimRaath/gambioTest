<?php
/* --------------------------------------------------------------
  orders_edit.php 2016-07-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(orders.php,v 1.27 2003/02/16); www.oscommerce.com
  (c) 2003	 nextcommerce (orders.php,v 1.7 2003/08/14); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders_edit.php,v 1.1)

  Released under the GNU General Public License
  ----------------------------------------------------------------------------------------- */

require('includes/application_top.php');

require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');

// Benoetigte Funktionen und Klassen Anfang:
require_once(DIR_WS_CLASSES . 'order.php');
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'xtcPrice.php');
require_once(DIR_FS_INC . 'xtc_get_tax_class_id.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC . 'xtc_oe_get_options_name.inc.php');
require_once(DIR_FS_INC . 'xtc_oe_get_options_values_name.inc.php');
require_once(DIR_FS_INC . 'xtc_oe_customer_infos.inc.php');
// Benoetigte Funktionen und Klassen Ende

if(!$_GET['oID'])
{
	$_GET['oID'] = $_POST['oID'];
}

$order = new order($_GET['oID']);
$xtPrice = new xtcPrice($order->info['currency'], $order->info['status']);

/** @var OrderWriteService $orderWriteService */
$orderWriteService = StaticGXCoreLoader::getService('OrderWrite');

// Adressbearbeitung Anfang
if($_GET['action'] == "address_edit")
{
	/** @var CountryService $countryService */
	$countryService = StaticGXCoreLoader::getService('Country');
	$country 		= $countryService->findCountryByName($_POST['customers_country']);
	$zone 			= $countryService->getUnknownCountryZoneByName($_POST['customers_state']);
	
	if($countryService->countryHasCountryZones($country)
		&& $countryService->countryZoneExistsInCountry($zone, $country))
	{
		$zone = $countryService->getCountryZoneByNameAndCountry($_POST['customers_state'], $country);
	}
	
	$newAddress = MainFactory::create('AddressBlock',
	                                  MainFactory::create('CustomerGender', $_POST['customers_gender']),
	                                  MainFactory::create('CustomerFirstname', $_POST['customers_firstname']),
	                                  MainFactory::create('CustomerLastname', $_POST['customers_lastname']),
	                                  MainFactory::create('CustomerCompany', $_POST['customers_company']),
	                                  MainFactory::create('CustomerB2BStatus', false),
	                                  MainFactory::create('CustomerStreet', $_POST['customers_street_address']),
	                                  MainFactory::create('CustomerHouseNumber', (string)$_POST['customers_house_number']),
	                                  MainFactory::create('CustomerAdditionalAddressInfo', (string)$_POST['customers_additional_info']),
	                                  MainFactory::create('CustomerSuburb', $_POST['customers_suburb']),
	                                  MainFactory::create('CustomerPostcode', $_POST['customers_postcode']),
	                                  MainFactory::create('CustomerCity', $_POST['customers_city']),
									  $country,
									  $zone
	);
	$orderWriteService->updateCustomerAddress(new IdType($_POST['oID']), $newAddress);
	
	$newAddress = MainFactory::create('AddressBlock',
	                                  MainFactory::create('CustomerGender', $_POST['delivery_gender']),
	                                  MainFactory::create('CustomerFirstname', $_POST['delivery_firstname']),
	                                  MainFactory::create('CustomerLastname', $_POST['delivery_lastname']),
	                                  MainFactory::create('CustomerCompany', $_POST['delivery_company']),
	                                  MainFactory::create('CustomerB2BStatus', false),
	                                  MainFactory::create('CustomerStreet', $_POST['delivery_street_address']),
	                                  MainFactory::create('CustomerHouseNumber', (string)$_POST['delivery_house_number']),
	                                  MainFactory::create('CustomerAdditionalAddressInfo', (string)$_POST['delivery_additional_info']),
	                                  MainFactory::create('CustomerSuburb', $_POST['delivery_suburb']),
	                                  MainFactory::create('CustomerPostcode', $_POST['delivery_postcode']),
	                                  MainFactory::create('CustomerCity', $_POST['delivery_city']),
	                                  $countryService->findCountryByName($_POST['delivery_country']),
	                                  $countryService->getUnknownCountryZoneByName($_POST['delivery_state'])
	);
	$orderWriteService->updateDeliveryAddress(new IdType($_POST['oID']), $newAddress);
	
	$newAddress = MainFactory::create('AddressBlock',
	                                  MainFactory::create('CustomerGender', $_POST['billing_gender']),
	                                  MainFactory::create('CustomerFirstname', $_POST['billing_firstname']),
	                                  MainFactory::create('CustomerLastname', $_POST['billing_lastname']),
	                                  MainFactory::create('CustomerCompany', $_POST['billing_company']),
	                                  MainFactory::create('CustomerB2BStatus', false),
	                                  MainFactory::create('CustomerStreet', $_POST['billing_street_address']),
	                                  MainFactory::create('CustomerHouseNumber', (string)$_POST['billing_house_number']),
	                                  MainFactory::create('CustomerAdditionalAddressInfo', (string)$_POST['billing_additional_info']),
	                                  MainFactory::create('CustomerSuburb', $_POST['billing_suburb']),
	                                  MainFactory::create('CustomerPostcode', $_POST['billing_postcode']),
	                                  MainFactory::create('CustomerCity', $_POST['billing_city']),
	                                  $countryService->findCountryByName($_POST['billing_country']),
	                                  $countryService->getUnknownCountryZoneByName($_POST['billing_state'])
	);
	$orderWriteService->updateBillingAddress(new IdType($_POST['oID']), $newAddress);
	
	$lang_query = xtc_db_query("SELECT languages_id FROM " . TABLE_LANGUAGES . " WHERE directory = '" . xtc_db_input($order->info['language']) . "'");
	$lang = xtc_db_fetch_array($lang_query);

	$status_query = xtc_db_query("SELECT customers_status_name 
									FROM " . TABLE_CUSTOMERS_STATUS . " 
									WHERE 
										customers_status_id = '" . (int)$_POST['customers_status'] . "' AND 
										language_id = '" . (int)$lang['languages_id'] . "'");
	$status = xtc_db_fetch_array($status_query);

	// Validate email address and show error message if its wrong.
	if(!filter_var($_POST['customers_email_address'], FILTER_VALIDATE_EMAIL))
	{
		$languageTextManager = MainFactory::create_object('LanguageTextManager',
		                                                  ['messages', $_SESSION['languages_id']]);
		
		$GLOBALS['messageStack']->add($languageTextManager->get_text('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'general'));
		
		$_GET['edit_action'] = 'address';
	} 
	else 
	{
		$sql_data_array = array('customers_vat_id' => xtc_db_prepare_input($_POST['customers_vat_id']),
		                        'customers_status' => xtc_db_prepare_input($_POST['customers_status']),
		                        'customers_status_name' => xtc_db_prepare_input($status['customers_status_name']),
		                        'customers_telephone' => xtc_db_prepare_input($_POST['customers_telephone']),
		                        'customers_email_address' => xtc_db_prepare_input($_POST['customers_email_address'])
		);
		
		$update_sql_data = array('last_modified' => 'now()');
		$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
		xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id = \'' . (int)$_POST['oID'] . '\'');
		xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=address&oID=' . (int)$_POST['oID']));
	}
}
// Adressbearbeitung Ende

// Artikeldaten einfuegen / bearbeiten Anfang

if(isset($_POST['update_stock']) && $_POST['update_stock'] === '1')
{
	switch($_GET['action'])
	{
		case 'product_edit':
		case 'product_ins':
		case 'product_delete':
			$t_old_products_quantity = 0;
			if(isset($_POST['old_products_quantity']))
			{
				$t_old_products_quantity = (double)$_POST['old_products_quantity'];
			}

			$t_new_stock = (double)$_POST['products_quantity'] - $t_old_products_quantity;

			$t_product_data = $order->get_product_array($_POST['opID']);
			
			$t_use_properties_combis_quantity = 0;
			if(isset($t_product_data['properties']))
			{
				$t_sql = 'SELECT use_properties_combis_quantity FROM ' . TABLE_PRODUCTS . ' WHERE products_id = "' . (int)$_POST['products_id'] . '"';
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$t_use_properties_combis_quantity = $t_result_array['use_properties_combis_quantity'];
				}
				
				if($t_use_properties_combis_quantity == 0 || $t_use_properties_combis_quantity == 2)
				{
					$t_sql = 'UPDATE products_properties_combis 
								SET combi_quantity = (combi_quantity - ' . $t_new_stock . ') 
								WHERE products_properties_combis_id = "' . (int)$t_product_data['properties_combis_id'] . '"';
					xtc_db_query($t_sql);
					
					// set combi_shippingtime:
					set_shipping_status((int)$_POST['products_id'], (int)$t_product_data['properties_combis_id']);
				}
			}
			
			if($t_use_properties_combis_quantity == 0 || $t_use_properties_combis_quantity == 1)
			{
				// update product
				$t_sql = 'UPDATE ' . TABLE_PRODUCTS . ' SET products_quantity = (products_quantity - ' . $t_new_stock . ') WHERE products_id = "' . (int)$_POST['products_id'] . '"';
				xtc_db_query($t_sql);
				
				// set products_shippingtime:
				set_shipping_status((int)$_POST['products_id']);
				
				// update attributes
				$t_sql = 'SELECT products_attributes_id
							FROM
								' . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ' o,
								' . TABLE_PRODUCTS_ATTRIBUTES . ' a
							WHERE
								o.options_id = a.options_id AND
								o.options_values_id = a.options_values_id AND
								a.products_id = "' . (int)$_POST['products_id'] . '"
								AND o.orders_id="' . (int)$_POST['oID'] . '"';
				$t_result = xtc_db_query($t_sql);
				while($t_result_array = xtc_db_fetch_array($t_result))
				{
					$t_sql = 'UPDATE ' . TABLE_PRODUCTS_ATTRIBUTES . ' SET attributes_stock = (attributes_stock - ' . $t_new_stock . ') WHERE products_attributes_id = "' . $t_result_array['products_attributes_id'] . '"';
					xtc_db_query($t_sql);
				}
			}			

			break;
	}
}

// Artikel bearbeiten Anfang
if($_GET['action'] == "product_edit")
{
	$status_query = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . (int)$order->info['status'] . "'");
	$status = xtc_db_fetch_array($status_query);

	$final_price = $_POST['products_price'] * $_POST['products_quantity'];

	// Fetch old product's quantity value in order from database.
	$oldProductsQuantityQuery  = xtc_db_query("
		SELECT
			products_quantity
		FROM
			" . TABLE_ORDERS_PRODUCTS . "
		WHERE
			products_id = '" . (int)$_POST['products_id'] . "'
		AND
			orders_id = '". (int)$_POST['oID'] . "'
	");
	$oldProductsQuantityResult = xtc_db_fetch_array($oldProductsQuantityQuery);
	$oldProductsQuantityValue  = (int)$oldProductsQuantityResult['products_quantity'];

	// Fetch old product's ordered count value from database.
	$oldProductsOrderedQuery  = xtc_db_query("
		SELECT
			products_ordered
		FROM
			" . TABLE_PRODUCTS . "
		WHERE
			products_id = '" . (int)$_POST['products_id'] . "'
	");
	$oldProductsOrderedResult = xtc_db_fetch_array($oldProductsOrderedQuery);
	$oldProductsOrderedValue = (int)$oldProductsOrderedResult['products_ordered'];

	// New order's product quantity value.
	$newProductsQuantityValue = (int)$_POST['products_quantity'];

	// Difference of old and new products quantity value.
	$productsQuantityDifference = $oldProductsQuantityValue - $newProductsQuantityValue;

	// Assign new product ordered count value.
	if ($productsQuantityDifference < 0) {
		$newProductsOrderedValue = $oldProductsOrderedValue + abs($productsQuantityDifference);
	} else {
		$newProductsOrderedValue = $oldProductsOrderedValue - $productsQuantityDifference;
	}

	// Update new product ordered count value to database.
	$productsOrderedDataArray = array(
		'products_ordered' => $newProductsOrderedValue
	);
	xtc_db_perform(TABLE_PRODUCTS, $productsOrderedDataArray, 'update', 'products_id = \'' . (int)$_POST['products_id'] . '\'');

	// Update order products.
	$sql_data_array = array('orders_id' => xtc_db_prepare_input($_POST['oID']), 
							'products_id' => xtc_db_prepare_input($_POST['products_id']), 
							'products_name' => xtc_db_prepare_input($_POST['products_name']), 
							'products_price' => xtc_db_prepare_input($_POST['products_price']), 
							'products_discount_made' => '', 
							'final_price' => xtc_db_prepare_input($final_price), 
							'products_tax' => xtc_db_prepare_input($_POST['products_tax']), 
							'products_quantity' => xtc_db_prepare_input($_POST['products_quantity']), 
							'allow_tax' => xtc_db_prepare_input($status['customers_status_show_price_tax']));

	$update_sql_data = array('products_model' => xtc_db_prepare_input($_POST['products_model']));
	$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
	xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \'' . (int)$_POST['opID'] . '\'');
	
	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . (int)$_POST['oID']));
}
// Artikel bearbeiten Ende

// Artikel einfuegen Anfang
if($_GET['action'] == "product_ins")
{
	$status_query = xtc_db_query("SELECT customers_status_show_price_tax FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . (int)$order->info['status'] . "'");
	$status = xtc_db_fetch_array($status_query);

	$product_query = xtc_db_query("SELECT 
										p.products_model, 
										p.products_tax_class_id, 
										pd.products_name,
										p.product_type,
										p.products_ordered
									FROM 
										" . TABLE_PRODUCTS . " p, 
										" . TABLE_PRODUCTS_DESCRIPTION . " pd 
									WHERE 
										p.products_id = '" . (int)$_POST['products_id'] . "' AND 
										pd.products_id = p.products_id AND 
										pd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
	$product = xtc_db_fetch_array($product_query);

	$t_sql = 'SELECT
					ss.shipping_status_name
				FROM
					' . TABLE_PRODUCTS . ' p,
					' . TABLE_SHIPPING_STATUS . ' ss
				WHERE
					p.products_id = ' . $_POST['products_id'] . '
				AND
					p.products_shippingtime = ss.shipping_status_id
				AND
					ss.language_id = ' . $_SESSION['languages_id']
	;
	$t_result = xtc_db_query($t_sql);
	$t_shipping_time = xtc_db_fetch_array($t_result);

	$c_info = xtc_oe_customer_infos($order->customer['ID']);
	$tax_rate = xtc_get_tax_rate($product['products_tax_class_id'], $c_info['country_id'], $c_info['zone_id']);

	$price = $xtPrice->xtcGetPrice($_POST['products_id'], $format = false, $_POST['products_quantity'], $product['products_tax_class_id'], '', '', $order->customer['ID']);

	$orderItem = MainFactory::create('OrderItem', new StringType(xtc_db_prepare_input($product['products_name'])));
	
	$orderItem->setPrice(new DecimalType($price));
	$orderItem->setQuantity(new DecimalType($_POST['products_quantity']));
	$orderItem->setTax(new DecimalType($tax_rate));
	$orderItem->setTaxAllowed(new BoolType((bool)(int)$status['customers_status_show_price_tax']));
	$orderItem->setProductModel(new StringType(xtc_db_prepare_input($product['products_model'])));
	$orderItem->setShippingTimeInfo(new StringType(xtc_db_prepare_input((string)$t_shipping_time['shipping_status_name'])));
	$orderItem->setAddonValue(new StringType('productId'), new StringType($_POST['products_id']));
	
	$orderWriteService->addOrderItem(new IdType($_POST['oID']), $orderItem);

	// Update products ordered count.
	$newTotalCount = (int)$product['products_ordered'] + (int)$_POST['products_quantity'];
	$productsOrderedDataArray = array(
		'products_ordered' => $newTotalCount
	);
	xtc_db_perform(TABLE_PRODUCTS, $productsOrderedDataArray, 'update', 'products_id = \'' . (int)$_POST['products_id'] . '\'');
	
	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . (int)$_POST['oID']));
}
// Artikel einfuegen Ende
// Produkt Optionen bearbeiten Anfang
if($_GET['action'] == "product_option_edit")
{
	$sql_data_array = array('products_options' => xtc_db_prepare_input($_POST['products_options']), 
							'products_options_values' => xtc_db_prepare_input($_POST['products_options_values']), 
							'options_values_price' => (double)$_POST['options_values_price']);

	$update_sql_data = array('price_prefix' => xtc_db_prepare_input($_POST['prefix']));
	$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
	xtc_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array, 'update', 'orders_products_attributes_id = \'' . (int)$_POST['opAID'] . '\'');

	$products_query = xtc_db_query("SELECT 
										op.products_id, 
										op.products_quantity, 
										op.products_price, 
										op.allow_tax, 
										op.products_tax, 
										p.products_tax_class_id 
									FROM 
										" . TABLE_ORDERS_PRODUCTS . " op, 
										" . TABLE_PRODUCTS . " p 
									WHERE 
										op.orders_products_id = '" . (int)$_POST['opID'] . "' AND 
										op.products_id = p.products_id");
	$products = xtc_db_fetch_array($products_query);
	
	$products_old_price = $products['products_price'];
	
	$t_products_attributes_old_price = $_POST['options_values_old_price'];
	$t_products_attributes_new_price = $_POST['options_values_price'];
	
	if($products['allow_tax'] == 1)
	{
		$t_products_attributes_old_price = $xtPrice->xtcAddTax($t_products_attributes_old_price, $products['products_tax']);
		$t_products_attributes_new_price = $xtPrice->xtcAddTax($t_products_attributes_new_price, $products['products_tax']);
	}
	
	if($_POST['old_prefix'] == '-')
	{
		$t_products_attributes_old_price *= -1;
	}
	if($_POST['prefix'] == '-')
	{
		$t_products_attributes_new_price *= -1;
	}
	
	$price = $products_old_price - $t_products_attributes_old_price + $t_products_attributes_new_price;

	$final_price = $price * $products['products_quantity'];

	$sql_data_array = array('products_price' => xtc_db_prepare_input($price));
	$update_sql_data = array('final_price' => xtc_db_prepare_input($final_price));
	$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
	xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \'' . (int)$_POST['opID'] . '\'');

	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=options&oID=' . (int)$_POST['oID'] . '&pID=' . (int)$products['products_id'] . '&opID=' . (int)$_POST['opID']));
}
// Produkt Optionen bearbeiten Ende

// Produkt Optionen einfuegen Anfang
if($_GET['action'] == "product_option_ins")
{
	$products_attributes_query = xtc_db_query("SELECT 
													options_id, 
													options_values_id, 
													options_values_price, 
													price_prefix 
												FROM " . TABLE_PRODUCTS_ATTRIBUTES . " 
												WHERE products_attributes_id = '" . (int)$_POST['aID'] . "'");
	$products_attributes = xtc_db_fetch_array($products_attributes_query);

	$products_options_query = xtc_db_query("SELECT products_options_name 
											FROM " . TABLE_PRODUCTS_OPTIONS . " 
											WHERE 
												products_options_id = '" . (int)$products_attributes['options_id'] . "' AND 
												language_id = '" . (int)$_SESSION['languages_id'] . "'");
	$products_options = xtc_db_fetch_array($products_options_query);

	$products_options_values_query = xtc_db_query("SELECT products_options_values_name 
													FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " 
													WHERE 
														products_options_values_id = '" . (int)$products_attributes['options_values_id'] . "' AND 
														language_id = '" . (int)$_SESSION['languages_id'] . "'");
	$products_options_values = xtc_db_fetch_array($products_options_values_query);

	$orderItemAttribute = MainFactory::create('OrderItemAttribute', 
	                                          new StringType(xtc_db_prepare_input($products_options['products_options_name'])),
	                                          new StringType(xtc_db_prepare_input($products_options_values['products_options_values_name'])));
	$orderItemAttribute->setPrice(new DecimalType($products_attributes['options_values_price']));
	$orderItemAttribute->setPriceType(new StringType($products_attributes['price_prefix']));
	$orderItemAttribute->setOptionId(new IdType($products_attributes['options_id']));
	$orderItemAttribute->setOptionValueId(new IdType($products_attributes['options_values_id']));
	
	$orderWriteService->addOrderItemAttribute(new IdType($_POST['opID']), $orderItemAttribute);
	
	$products_query = xtc_db_query("SELECT 
										op.products_id, 
										op.products_quantity,
										op.products_price, 
										op.allow_tax, 
										op.products_tax,
										p.products_tax_class_id 
									FROM 
										" . TABLE_ORDERS_PRODUCTS . " op, 
										" . TABLE_PRODUCTS . " p 
									WHERE 
										op.orders_products_id = '" . (int)$_POST['opID'] . "' AND 
										op.products_id = p.products_id");
	$products = xtc_db_fetch_array($products_query);

	$products_a_query = xtc_db_query("SELECT 
											options_values_price, 
											price_prefix 
										FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " 
										WHERE orders_products_id = '" . (int)$_POST['opID'] . "' AND
											products_options LIKE '" . xtc_db_prepare_input($products_options['products_options_name']) . "' AND
											products_options_values LIKE '" . xtc_db_prepare_input($products_options_values['products_options_values_name']) . "'");
	$products_a = xtc_db_fetch_array($products_a_query);

	if(DOWNLOAD_ENABLED == 'true')
	{
		$attributes_query = "SELECT 
									popt.products_options_name,
									poval.products_options_values_name,
									pa.options_values_price,
									pa.price_prefix,
									pad.products_attributes_maxdays,
									pad.products_attributes_maxcount,
									pad.products_attributes_filename
								FROM 
									" . TABLE_PRODUCTS_OPTIONS . " popt, 
									" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, 
									" . TABLE_PRODUCTS_ATTRIBUTES . " pa
									LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad ON (pa.products_attributes_id = pad.products_attributes_id)
								WHERE 
									pa.products_id = '" . (int)$products['products_id'] . "' AND 
									pa.options_id = '" . (int)$products_attributes['options_id'] . "' AND 
									pa.options_id = popt.products_options_id AND 
									pa.options_values_id = '" . (int)$products_attributes['options_values_id'] . "' AND 
									pa.options_values_id = poval.products_options_values_id AND 
									popt.language_id = '" . (int)$_SESSION['languages_id'] . "' AND 
									poval.language_id = '" . (int)$_SESSION['languages_id'] . "'";
		$attributes = xtc_db_query($attributes_query);

		$attributes_values = xtc_db_fetch_array($attributes);

		if(isset($attributes_values['products_attributes_filename']) && xtc_not_null($attributes_values['products_attributes_filename']))
		{
			$sql_data_array = array('orders_id' => (int)$_POST['oID'],
									'orders_products_id' => (int)$_POST['opID'], 
									'orders_products_filename' => $attributes_values['products_attributes_filename'], 
									'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
									'download_count' => $attributes_values['products_attributes_maxcount']);

			xtc_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
		}
	}
	
	$products_old_price = $products['products_price'];
	
	$t_products_attributes_new_price = $products_a['options_values_price'];
	
	if($products['allow_tax'] == 1)
	{
		$t_products_attributes_new_price = $xtPrice->xtcAddTax($t_products_attributes_new_price, $products['products_tax']);
	}
	
	if($products_a['price_prefix'] == '-')
	{
		$t_products_attributes_new_price *= -1;
	}
	
	$price = $products_old_price + $t_products_attributes_new_price;

	$final_price = $price * $products['products_quantity'];

	$sql_data_array = array('products_price' => (double)$price);
	$update_sql_data = array('final_price' => (double)$final_price);
	$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
	xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \'' . (int)$_POST['opID'] . '\'');

	if(isset($_POST['update_stock']))
	{
		$t_product_data = $order->get_product_array($_POST['opID']);
			
		$t_sql = 'UPDATE ' . TABLE_PRODUCTS_ATTRIBUTES . ' 
					SET attributes_stock = (attributes_stock - ' . (double)$t_product_data['qty'] . ') 
					WHERE products_attributes_id = "' . (int)$_POST['aID'] . '"';
		xtc_db_query($t_sql);
	}
	
	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=options&oID=' . (int)$_POST['oID'] . '&pID=' . (int)$products['products_id'] . '&opID=' . (int)$_POST['opID']));
}
// Produkt Optionen einfuegen Ende
// Artikeldaten einfuegen / bearbeiten Ende:

// Zahlung Anfang
if($_GET['action'] == "payment_edit")
{
	$orderWriteService->updatePaymentType(new IdType($_POST['oID']), 
	                                      MainFactory::create('OrderPaymentType', 
	                                                          new StringType(xtc_db_prepare_input($_POST['payment'])),
	                                                          new StringType(xtc_db_prepare_input($_POST['payment']))));
	
	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID=' . (int)$_POST['oID']));
}
// Zahlung Ende

// Versandkosten Anfang
if($_GET['action'] == "shipping_edit")
{
	if(isset($_POST['shipping']) && empty($_POST['shipping']) == false)
	{
		if($_POST['shipping'] != 'no_shipping')
		{
			require_once DIR_FS_INC . 'get_shipping_title.inc.php';
			
			$shipping_text = get_shipping_title($_POST['shipping']);
			$shipping_class = $_POST['shipping'] . '_' . $_POST['shipping'];

			$text = $xtPrice->xtcFormat($_POST['value'], true);

			$sql_data_array = array('orders_id' => (int)$_POST['oID'], 
									'title' => xtc_db_prepare_input($shipping_text), 
									'text' => xtc_db_prepare_input($text), 
									'value' => (double)$_POST['value'],
									'class' => 'ot_shipping', 
									'sort_order' => MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER);

			$check_shipping_query = xtc_db_query("SELECT class 
													FROM " . TABLE_ORDERS_TOTAL . " 
													WHERE 
														orders_id = '" . (int)$_POST['oID'] . "' AND 
														class = 'ot_shipping'");
			if(xtc_db_num_rows($check_shipping_query))
			{
				xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_id = \'' . (int)$_POST['oID'] . '\' AND class="ot_shipping"');
			}
			else
			{
				xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
			}
				
			$orderWriteService->updateShippingType(new IdType($_POST['oID']),
			                                      MainFactory::create('OrderShippingType',
			                                                          new StringType($shipping_text),
			                                                          new StringType($shipping_class)));
		}
		else
		{
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " 
							WHERE 
								orders_id = '" . (int)$_POST['oID'] . "' AND 
								class = 'ot_shipping'");
			
			$orderWriteService->updateShippingType(new IdType($_POST['oID']),
			                                       MainFactory::create('OrderShippingType',
			                                                           new StringType(''),
			                                                           new StringType('')));
		}
		
		xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID=' . (int)$_POST['oID']));
	}
}
// Versandkosten Ende

// OT Module Anfang
if($_GET['action'] == "ot_edit")
{
	if(isset($_POST['coupon_code']))
	{
		$coo_coupon_control = MainFactory::create_object('CouponControl', array($_POST['coupon_code'], $_POST['oID'], $order->info['currency_value']));
		$t_coupon_value = $coo_coupon_control->calculate_discount();
		
		if($t_coupon_value > 0)
		{
			$coo_lang_file_master->init_from_lang_file('lang/' . $order->info['language'] . '/modules/order_total/ot_coupon.php');
			require_once(DIR_FS_CATALOG . 'includes/modules/order_total/ot_coupon.php');

			$coo_ot_coupon = new ot_coupon();
			$t_title = $coo_ot_coupon->title . ': ' . xtc_db_input($_POST['coupon_code']) . ':';
			$t_text = '- ' . $xtPrice->xtcFormat($t_coupon_value, true);
			$t_value = round($t_coupon_value * -1, 4);

			$t_sql = 'SELECT * FROM ' . TABLE_ORDERS_TOTAL . ' WHERE orders_id = "' . (int)$_POST['oID'] . '" AND class = "ot_coupon"';
			$t_result = xtc_db_query($t_sql);

			if(xtc_db_num_rows($t_result) > 0)
			{
				$t_sql = 'UPDATE ' . TABLE_ORDERS_TOTAL . ' 
							SET
								title = "' . $t_title . '",
								text = "' . $t_text . '",
								value = "' . $t_value . '"
							WHERE 
								orders_id = "' . (int)$_POST['oID'] . '" AND 
								class = "ot_coupon"';
			}
			else
			{
				$t_sql = 'INSERT INTO ' . TABLE_ORDERS_TOTAL . ' 
							SET
								orders_id = "' . (int)$_POST['oID'] . '",
								title = "' . $t_title . '",
								text = "' . $t_text . '",
								value = "' . $t_value . '",
								class = "ot_coupon",
								sort_order = "' . (int)$coo_ot_coupon->sort_order . '"';
			}

			xtc_db_query($t_sql);

			if($coo_coupon_control->get_('shipping_free'))
			{
				$t_sql = 'UPDATE ' . TABLE_ORDERS_TOTAL . ' 
							SET 
								text = "' . $xtPrice->xtcFormat(0, true) . '",
								value = 0
							WHERE 
								orders_id = "' . (int)$_POST['oID'] . '" AND 
								class = "ot_shipping"';
				xtc_db_query($t_sql);
			}
			
			// redeem coupon
			$coo_coupon_control->redeem($order->customer['ID']);
		}
	}
	else
	{
		$t_value = (double)$_POST['value'];
		
		if($_POST['class'] == 'ot_gv')
		{
			if($t_value > 0)
			{
				$t_value *= -1;
			}
			
			$t_value /= (double)$order->info['currency_value'];
			
			if(isset($_POST['cut_credit_balance']) && $t_value < 0)
			{
				xtc_db_query('UPDATE ' . TABLE_COUPON_GV_CUSTOMER . ' SET amount = (amount' . $t_value . ') WHERE customer_id = "' . (int)$order->customer['ID'] . '"');
			}
		}
		
		$check_total_query = xtc_db_query("SELECT orders_total_id 
											FROM " . TABLE_ORDERS_TOTAL . " 
											WHERE 
												orders_id = '" . (int)$_POST['oID'] . "' AND 
												orders_total_id = '" . (int)$_POST['otID'] . "' AND 
												class = '" . xtc_db_input($_POST['class']) . "'");
		
		if(xtc_db_num_rows($check_total_query))
		{
			$check_total = xtc_db_fetch_array($check_total_query);
			$text = $xtPrice->xtcFormat($_POST['value'], true);

			if($_POST['class'] == 'ot_total' || $_POST['class'] == 'ot_subtotal_no_tax')
			{
				$text = '<b>' . $text . '</b>';
			}
			
			$sql_data_array = array('title' => xtc_db_prepare_input($_POST['title']), 
									'text' => xtc_db_prepare_input($text), 
									'value' => $t_value);
			xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_total_id = \'' . (int)$check_total['orders_total_id'] . '\'');
		}
		else
		{
			$text = $xtPrice->xtcFormat($_POST['value'], true);

			if($_POST['class'] == 'ot_total' || $_POST['class'] == 'ot_subtotal_no_tax')
			{
				$text = '<b>' . $text . '</b>';
			}
			
			$sql_data_array = array('orders_id' => (int)$_POST['oID'], 
									'title' => xtc_db_prepare_input($_POST['title']), 
									'text' => xtc_db_prepare_input($text), 
									'value' => $t_value, 
									'class' => xtc_db_prepare_input($_POST['class']), 
									'sort_order' => (int)$_POST['sort_order']);

			xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
		}
	}

	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID=' . (int)$_POST['oID']));
}
// OT Module Ende

// Sprachupdate Anfang
if($_GET['action'] == "lang_edit")
{
	// Daten fuer Sprache waehlen
	$lang_query = xtc_db_query("SELECT 
									languages_id, 
									name, 
									directory 
								FROM " . TABLE_LANGUAGES . " 
								WHERE languages_id = '" . (int)$_POST['lang'] . "'");
	$lang = xtc_db_fetch_array($lang_query);
	// Daten fuer Sprache waehlen Ende	
	
	// Produkte
	$order_products_query = xtc_db_query("SELECT 
												orders_products_id, 
												products_id 
											FROM " . TABLE_ORDERS_PRODUCTS . " 
											WHERE orders_id = '" . (int)$_POST['oID'] . "'");
	while($order_products = xtc_db_fetch_array($order_products_query))
	{
		$products_query = xtc_db_query("SELECT products_name 
										FROM " . TABLE_PRODUCTS_DESCRIPTION . " 
										WHERE 
											products_id = '" . (int)$order_products['products_id'] . "' AND 
											language_id = '" . (int)$_POST['lang'] . "' ");
		$products = xtc_db_fetch_array($products_query);

		$sql_data_array = array('products_name' => xtc_db_prepare_input($products['products_name']));
		xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id  = \'' . (int)$order_products['orders_products_id'] . '\'');
	};
	// Produkte Ende
	
	// OT Module
	$order_total_query = xtc_db_query("SELECT 
											orders_total_id, 
											title, 
											class
										FROM " . TABLE_ORDERS_TOTAL . " 
										WHERE orders_id = '" . (int)$_POST['oID'] . "'");
	while($order_total = xtc_db_fetch_array($order_total_query))
	{
		if(isset($order_total['class']) && empty($order_total['class']) == false)
		{
			$coo_lang_file_master->init_from_lang_file('lang/' . $lang['directory'] . '/modules/order_total/' . $order_total['class'] . ' .php');
			$name = str_replace('ot_', '', $order_total['class']);
			
			if(defined('MODULE_ORDER_TOTAL_' . strtoupper($name) . '_TITLE'))
			{
				$text = constant('MODULE_ORDER_TOTAL_' . strtoupper($name) . '_TITLE');

				$sql_data_array = array('title' => xtc_db_prepare_input($text));
				xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array, 'update', 'orders_total_id  = \'' . (int)$order_total['orders_total_id'] . '\'');
			}
		}
	}
	// OT Module

	$sql_data_array = array('language' => xtc_db_prepare_input($lang['directory']));
	xtc_db_perform(TABLE_ORDERS, $sql_data_array, 'update', 'orders_id  = \'' . (int)$_POST['oID'] . '\'');

	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID=' . (int)$_POST['oID']));
}
// Sprachupdate Ende

// Loeschfunktionen Anfang
// Loeschen eines Artikels aus der Bestellung Anfang
if($_GET['action'] == "product_delete")
{
	xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_products_id = '" . (int)$_POST['opID'] . "'");
	xtc_db_query('DELETE FROM orders_products_properties WHERE orders_products_id = "' . (int)$_POST['opID'] . '"');
	xtc_db_query("DELETE FROM orders_products_quantity_units WHERE orders_products_id = '" . (int)$_POST['opID'] . "'");
	xtc_db_query('DELETE FROM ' . TABLE_ORDERS_PRODUCTS_DOWNLOAD . ' WHERE orders_products_id = "' . (int)$_POST['opID'] . '"');
	
	// DELETE from gm_gprint_orders_*, and gm_gprint_uploads
	$coo_gm_gprint_order_manager = MainFactory::create_object('GMGPrintOrderManager');
	$coo_gm_gprint_order_manager->delete((int)$_POST['opID']);

	xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . " 
					WHERE 
						orders_id = '" . (int)$_POST['oID'] . "' AND 
						orders_products_id = '" . (int)$_POST['opID'] . "'");

	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . (int)$_POST['oID']));
}
// Loeschen eines Artikels aus der Bestellung Ende

// Loeschen einer Artikeloption aus der Bestellung Anfang
if($_GET['action'] == "product_option_delete")
{
	if(isset($_POST['update_stock']))
	{
		$t_attributes_array = $order->get_attributes_array($_POST['opID'], $_POST['opAID']);
		if(!empty($t_attributes_array))
		{
			$t_product_data = $order->get_product_array($_POST['opID']);
			
			$t_sql = 'UPDATE ' . TABLE_PRODUCTS_ATTRIBUTES . ' 
						SET attributes_stock = (attributes_stock + ' . (double)$t_product_data['qty'] . ') 
						WHERE 
							products_id = "' . (int)$t_product_data['id'] . '" AND
							options_id = "' . (int)$t_attributes_array['options_id'] . '" AND
							options_values_id = "' . (int)$t_attributes_array['options_values_id'] . '"';
			xtc_db_query($t_sql);
		}
	}
	
	xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_products_attributes_id = '" . (int)$_POST['opAID'] . "'");

	$products_query = xtc_db_query("SELECT 
										op.products_id, 
										op.products_quantity,
										op.products_price, 
										op.allow_tax, 
										op.products_tax,
										p.products_tax_class_id 
									FROM 
										" . TABLE_ORDERS_PRODUCTS . " op, 
										" . TABLE_PRODUCTS . " p 
									WHERE 
										op.orders_products_id = '" . (int)$_POST['opID'] . "' AND 
										op.products_id = p.products_id");
	$products = xtc_db_fetch_array($products_query);

	$products_old_price = $products['products_price'];
	
	$t_products_attributes_old_price = $_POST['options_values_old_price'];
	
	if($products['allow_tax'] == 1)
	{
		$t_products_attributes_old_price = $xtPrice->xtcAddTax($t_products_attributes_old_price, $products['products_tax']);
	}
	
	if($_POST['old_prefix'] == '-')
	{
		$t_products_attributes_old_price *= -1;
	}
	
	$price = $products_old_price - $t_products_attributes_old_price;
	
//	$products_old_price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $products['products_quantity'], '', '', '', $order->customer['ID']);
//	$products_price = $products_old_price + $options_values_price;
//	$price = $xtPrice->xtcGetPrice($products['products_id'], $format = false, $products['products_quantity'], $products['products_tax_class_id'], $products_price, '', $order->customer['ID']);
	
	$final_price = $price * $products['products_quantity'];

	$sql_data_array = array('products_price' => xtc_db_prepare_input($price));
	$update_sql_data = array('final_price' => xtc_db_prepare_input($final_price));
	$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
	xtc_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array, 'update', 'orders_products_id = \'' . (int)$_POST['opID'] . '\'');

	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=options&oID=' . (int)$_POST['oID'] . '&pID=' . (int)$products['products_id'] . '&opID=' . (int)$_POST['opID']));
}
// Loeschen einer Artikeloptions aus der Bestellung Ende

// Loeschen eines OT Moduls aus der Bestellung Anfang
if($_GET['action'] == "ot_delete")
{
	xtc_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_total_id = '" . (int)$_POST['otID'] . "'");

	xtc_redirect(xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID=' . (int)$_POST['oID']));
}
// Loeschen eines OT Moduls aus der Bestellung Ende
// Loeschfunktionen Ende

// Rueckberechnung Anfang
if($_GET['action'] == "save_order")
{
	// exit recalculation
	if(!isset($_POST['recalculate']))
	{
		xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'action=edit&oID=' . (int)$_POST['oID']));
	}

	// Errechne neue MwSt. fuer die Bestellung Anfang
	// Produkte
	$products_query = xtc_db_query("SELECT 
										final_price, 
										products_tax, 
										allow_tax 
									FROM " . TABLE_ORDERS_PRODUCTS . " 
									WHERE orders_id = '" . (int)$_POST['oID'] . "' ");
	while($products = xtc_db_fetch_array($products_query))
	{
		$tax_rate = $products['products_tax'];
		$multi = (($products['products_tax'] / 100) + 1);

		if($products['allow_tax'] == '1')
		{
			$bprice = $products['final_price'];
			$nprice = $xtPrice->xtcRemoveTax($bprice, $tax_rate);
			$tax = $xtPrice->calcTax($nprice, $tax_rate);
		}
		else
		{
			$nprice = $products['final_price'];
			$bprice = $xtPrice->xtcAddTax($nprice, $tax_rate);
			$tax = $xtPrice->calcTax($nprice, $tax_rate);
		}

		$sql_data_array = array('orders_id' => (int)$_POST['oID'],
								'n_price' => (double)$nprice, 
								'b_price' => (double)$bprice, 
								'tax' => (double)$tax, 
								'tax_rate' => (double)$products['products_tax']);

		$insert_sql_data = array('class' => 'products');
		$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
		xtc_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
	}
	// Produkte Ende

	// set order total weight
	$recalculate = MainFactory::create('OrderRecalculate');
	$query       = xtc_db_query('UPDATE orders SET order_total_weight = '
	                                  . $recalculate->recalculateOrderWeight((int)$_POST['oID']) . ' WHERE orders_id = '
	                                  . (int)$_POST['oID']);
	

	$status_query = xtc_db_query("SELECT customers_status_show_price_tax, customers_status_add_tax_ot FROM " . TABLE_CUSTOMERS_STATUS . " WHERE customers_status_id = '" . (int)$order->info['status'] . "'");
	$status = xtc_db_fetch_array($status_query);

	// Module Anfang
	$module_query = xtc_db_query("SELECT 
										value, 
										class 
									FROM " . TABLE_ORDERS_TOTAL . " 
									WHERE 
										orders_id = '" . (int)$_POST['oID'] . "' AND 
										class NOT IN ('ot_subtotal', 'ot_subtotal_no_tax', 'ot_tax', 'ot_total', 'ot_total_netto')");
	while($module_value = xtc_db_fetch_array($module_query))
	{
		$module_name = str_replace('ot_', '', $module_value['class']);

		if($module_name != 'discount')
		{
			if($module_name != 'shipping')
			{
				if(defined('MODULE_ORDER_TOTAL_' . strtoupper($module_name) . '_TAX_CLASS'))
				{
					$module_tax_class = constant('MODULE_ORDER_TOTAL_' . strtoupper($module_name) . '_TAX_CLASS');
				}
				else
				{
					$module_tax_class = '';
				}
			}
			else
			{
				$module_tmp_name = explode('_', $order->info['shipping_class']);
				$module_tmp_name = $module_tmp_name[0];
				
				if($module_tmp_name != 'selfpickup' && defined('MODULE_SHIPPING_' . strtoupper($module_tmp_name) . '_TAX_CLASS'))
				{
					$module_tax_class = constant('MODULE_SHIPPING_' . strtoupper($module_tmp_name) . '_TAX_CLASS');
				}
				else
				{
					$module_tax_class = '';
				}
			}
		}
		else
		{
			$module_tax_class = '0';
		}

		$cinfo = xtc_oe_customer_infos($order->customer['ID']);
		$module_tax_rate = xtc_get_tax_rate($module_tax_class, $cinfo['country_id'], $cinfo['zone_id']);

		if($status['customers_status_show_price_tax'] == 1)
		{
			$module_b_price = $module_value['value'];
			
			if($module_tax_rate == '0')
			{
				$module_n_price = $module_value['value'];
			}
			else
			{
				$module_n_price = $xtPrice->xtcRemoveTax($module_b_price, $module_tax_rate);
			}
			
			$module_tax = $xtPrice->calcTax($module_n_price, $module_tax_rate);
		}
		else
		{
			$module_n_price = $module_value['value'];
			$module_b_price = $xtPrice->xtcAddTax($module_n_price, $module_tax_rate);
			$module_tax = $xtPrice->calcTax($module_n_price, $module_tax_rate);
		}

		$sql_data_array = array('orders_id' => (int)$_POST['oID'], 
								'n_price' => (double)$module_n_price, 
								'b_price' => (double)$module_b_price, 
								'tax' => (double)$module_tax, 
								'tax_rate' => (double)$module_tax_rate);

		$insert_sql_data = array('class' => $module_value['class']);
		$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
		xtc_db_perform(TABLE_ORDERS_RECALCULATE, $sql_data_array);
	}
	// Module Ende  
	
	// Kupon ANFANG
	$t_sql = 'SELECT c.coupon_code 
				FROM 
					' . TABLE_COUPONS . ' c,
					' . TABLE_COUPON_REDEEM_TRACK . ' r
				WHERE
					c.coupon_id = r.coupon_id AND
					r.order_id = "' . (double)$_POST['oID'] . '"
				ORDER BY redeem_date DESC
				LIMIT 1';
	$t_result = xtc_db_query($t_sql);

	if(xtc_db_num_rows($t_result) == 1)
	{
		$t_result_array = xtc_db_fetch_array($t_result);

		$coo_coupon_control = MainFactory::create_object('CouponControl', array($t_result_array['coupon_code'], $_POST['oID'], $order->info['currency_value']));

		$t_sql = 'SELECT value 
					FROM ' . TABLE_ORDERS_TOTAL . ' 
					WHERE
						orders_id = "' . (double)$_POST['oID'] . '" AND
						class = "ot_coupon"';
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_coupon_value = (double)$t_result_array['value'];

			if($t_coupon_value < 0)
			{
				$t_coupon_value *= -1;
			}

			$t_sql = 'DELETE FROM ' . TABLE_ORDERS_RECALCULATE . ' 
						WHERE 
							orders_id = "' . (double)$_POST['oID'] . '" AND 
							class = "ot_coupon"';
			xtc_db_query($t_sql);

			$t_taxes_discount_array = $coo_coupon_control->calculate_taxes_discount($t_coupon_value);
			foreach($t_taxes_discount_array as $t_tax_rate => $t_tax_value)
			{
				if($t_tax_rate === 0)
				{
					$t_n_price = $t_coupon_value * -1;
					$t_b_price = $t_n_price;
				}
				else
				{
					$t_n_price = $t_tax_value / ($t_tax_rate / 100) * -1;
					$t_b_price = $t_n_price * (1 + $t_tax_rate / 100);
				}
				$t_tax = $t_tax_value * -1;

				$t_sql = 'INSERT INTO ' . TABLE_ORDERS_RECALCULATE . ' 
							SET
								orders_id = "' . (int)$_POST['oID'] . '",
								n_price = "' . $t_n_price . '",
								b_price = "' . $t_b_price . '",
								tax = "' . $t_tax . '",
								tax_rate = "' . $t_tax_rate . '",
								class = "ot_coupon"';
				xtc_db_query($t_sql);
			}
		}
	}
	// Kupon ENDE
	
	// Neue Mwst. zusammenrechnen Anfang
	if(gm_get_conf('TAX_INFO_TAX_FREE') == 'false' && $status['customers_status_add_tax_ot'] == 1)
	{
		// Alte UST Loeschen ANFANG
		if(gm_get_conf('TAX_INFO_TAX_FREE') == 'false')
		{
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " 
							WHERE 
								orders_id = '" . (int)($_POST['oID']) . "' AND 
								class = 'ot_tax'");
		}
		// Alte UST Loeschen ENDE
		require(DIR_FS_LANGUAGES . $order->info['language'] . '/init.inc.php');

		$t_customers_status_add_tax_ot = '1';
		$t_customers_status_show_price_tax = '1';

		$t_sql = 'SELECT DISTINCT 
						customers_status_show_price_tax,
						customers_status_add_tax_ot 
					FROM ' . TABLE_CUSTOMERS_STATUS . ' 
					WHERE customers_status_id = "' . (int)$order->info['status'] . '"';
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_customers_status_show_price_tax = $t_result_array['customers_status_show_price_tax'];
			$t_customers_status_add_tax_ot = $t_result_array['customers_status_add_tax_ot'];
		}


		$t_sql = 'SELECT allow_tax FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . (int)$_POST['oID'] . '"';
		$t_result = xtc_db_query($t_sql);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_customers_status_show_price_tax = $t_result_array['allow_tax'];
		}

		$ust_query = xtc_db_query("SELECT 
										tax_rate, 
										SUM(tax) AS tax_value_new 
									FROM " . TABLE_ORDERS_RECALCULATE . " 
									WHERE 
										orders_id = '" . (int)$_POST['oID'] . "' AND 
										tax != '0' 
									GROUP by tax_rate ");
		while($ust = xtc_db_fetch_array($ust_query))
		{
			if($ust['tax_value_new'])
			{
				if($t_customers_status_show_price_tax == '1')
				{
					$title = sprintf(TAX_INFO_INCL, (double)$ust['tax_rate'] . '%') . ':';
				}
				// excl tax + tax at checkout
				elseif($t_customers_status_show_price_tax == '0' && $t_customers_status_add_tax_ot == '1')
				{
					$title = sprintf(TAX_INFO_ADD, (double)$ust['tax_rate'] . '%') . ':';
				}
				// excl tax
				else
				{
					$title = sprintf(TAX_INFO_EXCL, (double)$ust['tax_rate'] . '%') . ':';
				}

				$text = $xtPrice->xtcFormat($ust['tax_value_new'], true);

				$sql_data_array = array('orders_id' => (int)$_POST['oID'], 
										'title' => xtc_db_prepare_input($title),
										'text' => xtc_db_prepare_input($text), 
										'value' => (double)$ust['tax_value_new'], 
										'class' => 'ot_tax');

				$insert_sql_data = array('sort_order' => MODULE_ORDER_TOTAL_TAX_SORT_ORDER);
				$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
				xtc_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
			}
		}
	}
	// Neue Mwst. zusammenrechnen Ende
	
	// Errechne neue Zwischensumme fuer Artikel Anfang
	$products_query = xtc_db_query("SELECT 
										SUM(final_price) AS subtotal_final, 
										allow_tax 
									FROM " . TABLE_ORDERS_PRODUCTS . " 
									WHERE orders_id = '" . (int)$_POST['oID'] . "' 
									GROUP BY orders_id");
	$products = xtc_db_fetch_array($products_query);
	$subtotal_final = $products['subtotal_final'];
	$subtotal_text = $xtPrice->xtcFormat($subtotal_final, true);

	xtc_db_query("UPDATE " . TABLE_ORDERS_TOTAL . " 
					SET 
						text = '" . xtc_db_input($subtotal_text) . "', 
						value = '" . xtc_db_input($subtotal_final) . "' 
					WHERE 
						orders_id = '" . (int)$_POST['oID'] . "' AND 
						class = 'ot_subtotal' ");
	// Errechne neue Zwischensumme fuer Artikel Ende
	
	// Errechne neue Netto Zwischensumme fuer Artikel Anfang
	$check_no_tax_value_query = xtc_db_query("SELECT COUNT(*) AS count 
												FROM " . TABLE_ORDERS_TOTAL . " 
												WHERE 
													orders_id = '" . (int)$_POST['oID'] . "' AND 
													class IN ('ot_subtotal_no_tax', 'ot_total_netto')");
	$check_no_tax_value = xtc_db_fetch_array($check_no_tax_value_query);

	if($check_no_tax_value['count'] != '0')
	{
		$subtotal_no_tax_value_query = xtc_db_query("SELECT SUM(n_price) AS subtotal_no_tax_value 
														FROM " . TABLE_ORDERS_RECALCULATE . " 
														WHERE orders_id = '" . (int)$_POST['oID'] . "'");
		$subtotal_no_tax_value = xtc_db_fetch_array($subtotal_no_tax_value_query);
		$subtotal_no_tax_final = $subtotal_no_tax_value['subtotal_no_tax_value'];
		$subtotal_no_tax_text = $xtPrice->xtcFormat($subtotal_no_tax_final, true);
		
		xtc_db_query("UPDATE " . TABLE_ORDERS_TOTAL . " 
						SET 
							text = '" . xtc_db_input($subtotal_no_tax_text) . "', 
							value = '" . xtc_db_input($subtotal_no_tax_final) . "' 
						WHERE 
							orders_id = '" . (int)$_POST['oID'] . "' AND 
							class IN ('ot_subtotal_no_tax', 'ot_total_netto')");
	}

	// Errechne neue Netto Zwischensumme fuer Artikel Anfang
	// Errechne neue Bruttosumme Anfang
	$t_sql = "SELECT SUM(value) AS value 
				FROM " . TABLE_ORDERS_TOTAL . " 
				WHERE 
					orders_id = '" . (int)$_POST['oID'] . "' AND 
					class NOT IN ('ot_subtotal_no_tax', 'ot_tax', 'ot_total', 'ot_total_netto')";

	if($products['allow_tax'] == '0')
	{
		$t_sql = "SELECT SUM(value) AS value 
					FROM " . TABLE_ORDERS_TOTAL . " 
					WHERE 
						orders_id = '" . (int)$_POST['oID'] . "' AND 
						class NOT IN ('ot_subtotal_no_tax', 'ot_total', 'ot_total_netto')";
	}

	$subtotal_query = xtc_db_query($t_sql);
	$subtotal = xtc_db_fetch_array($subtotal_query);

	$subtotal_final = $subtotal['value'];
	$subtotal_text = '<b>' . $xtPrice->xtcFormat($subtotal_final, true) . '</b>';
	
	xtc_db_query("UPDATE " . TABLE_ORDERS_TOTAL . " 
					SET 
						text = '" . xtc_db_input($subtotal_text) . "', 
						value = '" . xtc_db_input($subtotal_final) . "' 
					WHERE 
						orders_id = '" . (int)$_POST['oID'] . "' AND 
						class = 'ot_total'");
	// Errechne neue Bruttosumme Ende
	
	// Loeschen des Zwischenspeichers Anfang
	xtc_db_query("DELETE FROM " . TABLE_ORDERS_RECALCULATE . " WHERE orders_id = '" . (int)$_POST['oID'] . "'");
	// Loeschen des Zwischenspeichers Ende

	xtc_redirect(xtc_href_link(FILENAME_ORDERS, 'action=edit&oID=' . (int)$_POST['oID']));
}
// Rueckberechnung Ende
//--------------------------------------------------------------------------------------------------------------------------------------

if($_GET['text'] == 'address')
{
	$messageStack->add(TEXT_EDIT_ADDRESS_SUCCESS, 'success');
}
$messageStack->add(HEADING_WARNING, 'warning');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->
					</table>
				</td>
				<!-- body_text //-->
				<td class="orders-edit-page-wrapper" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="100%" colspan="2">
							<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)"><?php echo TABLE_HEADING; ?>

								<div class="main">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr class="dataTableHeadingRow">
											<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
												<?php
												echo ($_GET['edit_action'] !== 'address') ? '<a href="' . xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=address&oID=' . $_GET['oID']) . '">' . MENU_CUSTOMER_DATA . '</a>' : MENU_CUSTOMER_DATA;
												?>
											</td>
											<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
												<?php
												if($_GET['edit_action'] !== 'products' && $_GET['edit_action'] !== 'options' && $_GET['edit_action'] !== 'properties') 
												{
													echo '<a href="' . xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . $_GET['oID']) . '">' . MENU_PRODUCT_DATA . '</a>' ;
												}
												else 
												{
													echo MENU_PRODUCT_DATA;
												}
												?>
											</td>
											<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
												<?php
												echo ($_GET['edit_action'] !== 'other') ? '<a href="' . xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=other&oID=' . $_GET['oID']) . '">' . MENU_ORDER_DATA . '</a>' : MENU_ORDER_DATA;
												?>
											</td>
										</tr>
									</table>
								</div>
								
						</td>
					</tr>
					<tr>
						<td class="order-edit-content">
							<!-- Meldungen Ende //-->
							<?php
							if($_GET['edit_action'] == 'address')
							{
								include ('orders_edit_address.php');
							}
							elseif($_GET['edit_action'] == 'products')
							{
								include ('orders_edit_products.php');
							}
							elseif($_GET['edit_action'] == 'other')
							{
								include ('orders_edit_other.php');
							}
							elseif($_GET['edit_action'] == 'options')
							{
								include ('orders_edit_options.php');
							}
							elseif($_GET['edit_action'] == 'properties')
							{
								include(DIR_FS_ADMIN . DIR_WS_MODULES . 'orders_edit_properties.inc.php');
							}
							?>

							<!-- Bestellung Sichern Anfang //-->
							<br /><br />
							<form name="save_order" action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=save_order') ?>" method="post">
								<table border="0" width="100%" height="60" class="gx-container paginator" data-gx-widget="checkbox">
									<tr>
										<td>
											<div class="pull-right">
												<?php
												echo TEXT_SAVE_ORDER;
												echo xtc_draw_hidden_field('customers_status_id', $address['customers_status']);
												echo xtc_draw_hidden_field('oID', $_GET['oID']);
												echo xtc_draw_hidden_field('cID', $_GET['cID']);
												?>
											</div>
										</td>
										<td style="width: 75px">
											<?php
											echo '<input type="checkbox" name="recalculate" value="1" data-single_checkbox/>';
											?>
										</td>
										<td style="width: 75px">
											<?php
											echo '<input type="submit" class="btn btn-primary pull-right" onClick="this.blur();" value="' . BUTTON_CLOSE . '" />';
											?>
										</td>
									</tr>
								</table>
							</form>
							<!-- Bestellung Sichern Ende //-->

							<!-- Ende //-->
						</td>
						</tr>
					</table>
					<!-- body_text_eof //-->
				</td>
			</tr>
		</table>
	<!-- body_eof //-->

	<!-- footer //-->
	<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	<!-- footer_eof //-->
	<br />
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>