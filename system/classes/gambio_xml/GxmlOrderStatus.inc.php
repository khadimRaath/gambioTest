<?php
/* --------------------------------------------------------------
  GxmlOrderStatus.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlOrderStatus
 * 
 * Handles the Order Status XML requests for the Gambio API. 
 * 
 * Supported API Functions:
 * 		- "upload_order_status"
 * 		- "download_order_status"
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlOrderStatus extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->_setPluralName('orders');
		$this->_setSingularName('order');
		$this->_setupMapperArray();
		$this->_setupTableMapperArray();
	}

	
	/**
	 * This method is called by the "XMLConnectAjaxHandler.inc.php" file. 
	 *
	 * This method is inherited by the GxmlMaster class and is instantiated because it
	 * is abstract.
	 */
	public function downloadOrderStatus(SimpleXMLElement $requestXml) 
	{
		try
		{
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_addOrderStatusesNode($requestXml); // Supports filtering parameters (limit, offset, where)
			return $this->responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}

	
	/**
	 * Handles the upload_order_status function of the API.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request data sent by the client.
	 *
	 * @return SimpleXMLElement Returns the response XML object.
	 */
	public function uploadOrderStatus(SimpleXMLElement $requestXml)
	{
		try
		{
			$responseData = array();

			foreach ($requestXml->parameters->orders->children() as $orderXml)
			{
				if((isset($orderXml->exported) === false || isset($orderXml->order_status_id))
				   && isset($orderXml->status_history) === false)
				{
					$responseData[] = array(
							$this->singularName . '_id' => $orderXml->order_id,
							'success' => '0',
							'errormessage' => 'missing required \'status_history\'-block',
							'action_performed' => 'error'
					);
					continue;
				}
				elseif(isset($orderXml->order_status_id) === false && isset($orderXml->status_history))
				{
					$responseData[] = array(
							$this->singularName . '_id' => $orderXml->order_id,
							'success' => '0',
							'errormessage' => 'missing required order_status_id',
							'action_performed' => 'error'
					);
					continue;
				}

				$this->_upload($orderXml, $responseData);
			}

			$responseXml = $this->generateResponseXml($responseData);
			return $responseXml;	
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}
	
	
	/**
	 * Not implemented method.
	 *
	 * This method is inherited by the GxmlMaster class and is instantiated because it
	 * is abstract.
	 */
	protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{
			default:
				return false;
		}
	}


	/**
	 * Not implemented method.
	 *
	 * This method is inherited by the GxmlMaster class and is instantiated because it
	 * is abstract.
	 */
	protected function _deleteObject(SimpleXMLElement $requestXml)
	{
		return false;
	}
	

	/**
	 * Generate the order statuses node based on the given parameters. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the request data sent by the client. 
	 */
	protected function _addOrderStatusesNode(SimpleXMLElement $requestXml)
	{	
		// Prepare & execute query.
		$query = '
			SELECT 
				orders_status_id,
				language_id,
				orders_status_name
			FROM ' . TABLE_ORDERS_STATUS;
		
		if(property_exists($requestXml, 'parameters')) // Add the WHERE and LIMIT clause.
		{
			$parameters = json_decode(json_encode($requestXml->parameters), true); // Convert object to associative array.

			// Generate WHERE clause.
			$conditions = array();
			$whereClause = '';
			if(isset($parameters['order_statuses']))
			{
				foreach($parameters['order_statuses']['order_status'] as $field => $value)
				{
					$this->_validateArgument($field, $value);
					$conditions[] = xtc_db_prepare_input($field) . ' = "' . xtc_db_prepare_input($value) . '"';
				}
				$whereClause = (count($conditions) > 0) ? ' WHERE ' . implode(' OR ', $conditions) : '';
			}

			// Generate LIMIT clause.
			$limitClause = ' 
				GROUP BY ' . TABLE_ORDERS_STATUS . '.orders_status_id 
				ORDER BY orders_status_id, language_id ' 
                . $this->_generateLimitClause($parameters);

			// Add clauses to query. 
			$query .= ' ' . $whereClause . ' ' . $limitClause;
		} 
		else 
		{
			$query .= ' ORDER BY orders_status_id, language_id';	
		}
		
		$results = xtc_db_query($query);
		
		// Prepare the XML response.
		// We need to group the different languages that are assigned to each status
		// so we will use the $currRecordId in order to group the translations into 
		// one result for the response. 
		$currRecordId = '';
		$response = $this->responseXml->addChild('order_statuses');
		while($row = xtc_db_fetch_array($results))
		{
			if($row['orders_status_id'] != $currRecordId)
			{
				$node = $response->addChild('order_status');
				$node->addChild('orders_status_id', $row['orders_status_id']);
			}
			$name = $node->addChild('name', $this->wrapValue($row['orders_status_name']));
			$name->addAttribute('language_id', $row['language_id']);
			$name->addAttribute('language_iso', $this->languageArray[$row['language_id']]['iso']);

			$currRecordId = $row['orders_status_id'];
		}
	}


	/**
	 * Validate the request filtering argument values.
	 *
	 * @param mixed $p_field The name of the filtering field.
	 * @param mixed $p_value The value that is going to be used in the filter.
	 *
	 * @throws InvalidArgumentException When a value is invalid.
	 */
	protected function _validateArgument($p_field, $p_value)
	{
		// Validate numerical values.
		if(($p_field == 'orders_status_id' || $p_field == 'language_id')
				&& !is_numeric($p_value))
		{
			throw new InvalidArgumentException('Invalid ' . $p_field . ' value provided (numeric expected): ' . print_r($p_value, true));
		}

		// Validate non numerical values. 
		if($p_field == 'orders_status_name' && is_numeric($p_value))
		{
			throw new InvalidArgumentException('Invalid ' . $p_field . ' value provided (string expected): ' . print_r($p_value, true));
		}
	}

	
	/**
	 * Setups the mapper array that will be used to point XML names to
	 * the actual DB names.
	 */
	protected function _setupMapperArray()
	{
		$this->mapperArray = array();

		$this->mapperArray['order_id'] = 'orders_id';
		$this->mapperArray['order_status_id'] = 'orders_status';
		$this->mapperArray['currency'] = 'currency';
		$this->mapperArray['currency_value'] = 'currency_value';
		$this->mapperArray['account_type'] = 'account_type';
		$this->mapperArray['payment_class'] = 'payment_class';
		$this->mapperArray['shipping_method'] = 'shipping_method';
		$this->mapperArray['shipping_class'] = 'shipping_class';
		$this->mapperArray['customer_ip'] = 'customers_ip';
		$this->mapperArray['language'] = 'language';
		$this->mapperArray['afterbuy_success'] = 'afterbuy_success';
		$this->mapperArray['afterbuy_id'] = 'afterbuy_id';
		$this->mapperArray['referrer_id'] = 'refferers_id';
		$this->mapperArray['conversion_type'] = 'conversion_type';
		$this->mapperArray['confirmation_send_date'] = 'gm_order_send_date';
		$this->mapperArray['confirmation_sent'] = 'gm_send_order_status';
		$this->mapperArray['customer_id'] = 'customers_id';
		$this->mapperArray['customer_number'] = 'customers_cid';
		$this->mapperArray['customer_vat_id'] = 'customers_vat_id';
		$this->mapperArray['customer_status_id'] = 'customers_status';
		$this->mapperArray['customer_status_name'] = 'customers_status_name';
		$this->mapperArray['customer_status_image'] = 'customers_status_image';
		$this->mapperArray['customer_status_discount'] = 'customers_status_discount';
		$this->mapperArray['customer_name'] = 'customers_name';
		$this->mapperArray['customer_firstname'] = 'customers_firstname';
		$this->mapperArray['customer_lastname'] = 'customers_lastname';
		$this->mapperArray['customer_company'] = 'customers_company';
		$this->mapperArray['customer_street_address'] = 'customers_street_address';
		$this->mapperArray['customer_suburb'] = 'customers_suburb';
		$this->mapperArray['customer_city'] = 'customers_city';
		$this->mapperArray['customer_postcode'] = 'customers_postcode';
		$this->mapperArray['customer_state'] = 'customers_state';
		$this->mapperArray['customer_country'] = 'customers_country';
		$this->mapperArray['customer_telephone'] = 'customers_telephone';
		$this->mapperArray['customer_email_address'] = 'customers_email_address';
		$this->mapperArray['customer_address_format_id'] = 'customers_address_format_id';
		$this->mapperArray['delivery_name'] = 'delivery_name';
		$this->mapperArray['delivery_firstname'] = 'delivery_firstname';
		$this->mapperArray['delivery_lastname'] = 'delivery_lastname';
		$this->mapperArray['delivery_company'] = 'delivery_company';
		$this->mapperArray['delivery_street_address'] = 'delivery_street_address';
		$this->mapperArray['delivery_suburb'] = 'delivery_suburb';
		$this->mapperArray['delivery_city'] = 'delivery_city';
		$this->mapperArray['delivery_postcode'] = 'delivery_postcode';
		$this->mapperArray['delivery_state'] = 'delivery_state';
		$this->mapperArray['delivery_country'] = 'delivery_country';
		$this->mapperArray['delivery_country_iso_code'] = 'delivery_country_iso_code_2';
		$this->mapperArray['delivery_address_format_id'] = 'delivery_address_format_id';
		$this->mapperArray['billing_name'] = 'billing_name';
		$this->mapperArray['billing_firstname'] = 'billing_firstname';
		$this->mapperArray['billing_lastname'] = 'billing_lastname';
		$this->mapperArray['billing_company'] = 'billing_company';
		$this->mapperArray['billing_street_address'] = 'billing_street_address';
		$this->mapperArray['billing_suburb'] = 'billing_suburb';
		$this->mapperArray['billing_city'] = 'billing_city';
		$this->mapperArray['billing_postcode'] = 'billing_postcode';
		$this->mapperArray['billing_state'] = 'billing_state';
		$this->mapperArray['billing_country'] = 'billing_country';
		$this->mapperArray['billing_country_iso_code'] = 'billing_country_iso_code_2';
		$this->mapperArray['billing_address_format_id'] = 'billing_address_format_id';
		$this->mapperArray['payment_method'] = 'payment_method';
		$this->mapperArray['credit_card_type'] = 'cc_type';
		$this->mapperArray['credit_card_owner'] = 'cc_owner';
		$this->mapperArray['credit_card_number'] = 'cc_number';
		$this->mapperArray['credit_card_expires'] = 'cc_expires';
		$this->mapperArray['credit_card_start'] = 'cc_start';
		$this->mapperArray['credit_card_issue'] = 'cc_issue';
		$this->mapperArray['credit_card_cvv'] = 'cc_cvv';
		$this->mapperArray['comments'] = 'comments';
		$this->mapperArray['date_purchased'] = 'date_purchased';
		$this->mapperArray['cancel_date'] = 'gm_cancel_date';
		$this->mapperArray['last_modified'] = 'last_modified';
		$this->mapperArray['date_finished'] = 'orders_date_finished';
		$this->mapperArray['exported'] = 'exported';
		$this->mapperArray['products/product/order_product_id'] = 'o_products_id';
		$this->mapperArray['products/product/product_id'] = 'products_id';
		$this->mapperArray['products/product/model'] = 'products_model';
		$this->mapperArray['products/product/name'] = 'products_name';
		$this->mapperArray['products/product/price'] = 'products_price';
		$this->mapperArray['products/product/discount_made'] = 'products_discount_made';
		$this->mapperArray['products/product/shipping_time'] = 'products_shipping_time';
		$this->mapperArray['products/product/final_price'] = 'final_price';
		$this->mapperArray['products/product/tax_rate'] = 'products_tax';
		$this->mapperArray['products/product/quantity'] = 'products_quantity';
		$this->mapperArray['products/product/allow_tax'] = 'allow_tax';
		$this->mapperArray['products/product/quantity_unit_id'] = 'quantity_unit_id';
		$this->mapperArray['products/product/unit_name'] = 'unit_name';
		$this->mapperArray['products/product/attributes/attribute/order_product_attribute_id'] = 'orders_products_attributes_id';
		$this->mapperArray['products/product/attributes/attribute/name'] = 'products_options';
		$this->mapperArray['products/product/attributes/attribute/value'] = 'products_options_values';
		$this->mapperArray['products/product/attributes/attribute/price'] = 'options_values_price';
		$this->mapperArray['products/product/attributes/attribute/price_prefix'] = 'price_prefix';
		$this->mapperArray['products/product/properties/property/order_product_property_id'] = 'orders_products_properties_id';
		$this->mapperArray['products/product/properties/property/product_combi_id'] = 'products_properties_combis_id';
		$this->mapperArray['products/product/properties/property/name'] = 'properties_name';
		$this->mapperArray['products/product/properties/property/value'] = 'values_name';
		$this->mapperArray['products/product/properties/property/price_type'] = 'properties_price_type';
		$this->mapperArray['products/product/properties/property/price'] = 'properties_price';
		$this->mapperArray['products/product/gprint_elements/gprint_element/gprint_element_id'] = 'gm_gprint_orders_elements_id';
		$this->mapperArray['products/product/gprint_elements/gprint_element/name'] = 'name';
		$this->mapperArray['products/product/gprint_elements/gprint_element/value'] = 'elements_value';
		$this->mapperArray['products/product/gprint_elements/gprint_element/download_url'] = 'download_key';
		$this->mapperArray['totals/total/order_total_id'] = 'orders_total_id';
		$this->mapperArray['totals/total/title'] = 'title';
		$this->mapperArray['totals/total/text'] = 'text';
		$this->mapperArray['totals/total/value'] = 'value';
		$this->mapperArray['totals/total/class'] = 'class';
		$this->mapperArray['totals/total/sort_order'] = 'sort_order';
		$this->mapperArray['status_history/status_history_entry/order_status_history_id'] = 'orders_status_history_id';
		$this->mapperArray['status_history/status_history_entry/order_status_id'] = 'orders_status_id';
		$this->mapperArray['status_history/status_history_entry/date_added'] = 'date_added';
		$this->mapperArray['status_history/status_history_entry/customer_notified'] = 'customer_notified';
		$this->mapperArray['status_history/status_history_entry/comments'] = 'comments';
		$this->mapperArray['status_history/order_status_id'] = 'orders_status_id';
		$this->mapperArray['status_history/date_added'] = 'date_added';
		$this->mapperArray['status_history/customer_notified'] = 'customer_notified';
		$this->mapperArray['status_history/comments'] = 'comments';
	}


	/**
	 * Setups the table mapper array that will be used to point XML table
	 * names to the actual DB names.
	 */
	protected function _setupTableMapperArray()
	{
		$this->tableMapperArray = array();

		$this->tableMapperArray['order_id'] = TABLE_ORDERS;
		$this->tableMapperArray['order_status_id'] = TABLE_ORDERS;
		$this->tableMapperArray['currency'] = TABLE_ORDERS;
		$this->tableMapperArray['currency_value'] = TABLE_ORDERS;
		$this->tableMapperArray['account_type'] = TABLE_ORDERS;
		$this->tableMapperArray['payment_class'] = TABLE_ORDERS;
		$this->tableMapperArray['shipping_method'] = TABLE_ORDERS;
		$this->tableMapperArray['shipping_class'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_ip'] = TABLE_ORDERS;
		$this->tableMapperArray['language'] = TABLE_ORDERS;
		$this->tableMapperArray['afterbuy_success'] = TABLE_ORDERS;
		$this->tableMapperArray['afterbuy_id'] = TABLE_ORDERS;
		$this->tableMapperArray['referrer_id'] = TABLE_ORDERS;
		$this->tableMapperArray['conversion_type'] = TABLE_ORDERS;
		$this->tableMapperArray['confirmation_email_html'] = TABLE_ORDERS;
		$this->tableMapperArray['confirmation_email_txt'] = TABLE_ORDERS;
		$this->tableMapperArray['confirmation_send_date'] = TABLE_ORDERS;
		$this->tableMapperArray['confirmation_sent'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_id'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_number'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_vat_id'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_status_id'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_status_name'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_status_image'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_status_discount'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_name'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_firstname'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_lastname'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_company'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_street_address'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_suburb'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_city'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_postcode'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_state'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_country'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_telephone'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_email_address'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_address_format_id'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_name'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_firstname'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_lastname'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_company'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_street_address'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_suburb'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_city'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_postcode'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_state'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_country'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_country_iso_code'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_address_format_id'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_name'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_firstname'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_lastname'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_company'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_street_address'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_suburb'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_city'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_postcode'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_state'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_country'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_country_iso_code'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_address_format_id'] = TABLE_ORDERS;
		$this->tableMapperArray['payment_method'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_type'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_owner'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_number'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_expires'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_start'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_issue'] = TABLE_ORDERS;
		$this->tableMapperArray['credit_card_cvv'] = TABLE_ORDERS;
		$this->tableMapperArray['comments'] = TABLE_ORDERS;
		$this->tableMapperArray['date_purchased'] = TABLE_ORDERS;
		$this->tableMapperArray['cancel_date'] = TABLE_ORDERS;
		$this->tableMapperArray['last_modified'] = TABLE_ORDERS;
		$this->tableMapperArray['date_finished'] = TABLE_ORDERS;
		$this->tableMapperArray['exported'] = TABLE_ORDERS;
		$this->tableMapperArray['products/product/order_product_id'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/product_id'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/model'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/name'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/price'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/discount_made'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/shipping_time'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/final_price'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/tax_rate'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/quantity'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/allow_tax'] = TABLE_ORDERS_PRODUCTS;
		$this->tableMapperArray['products/product/quantity_unit_id'] = 'orders_products_quantity_units';
		$this->tableMapperArray['products/product/unit_name'] = 'orders_products_quantity_units';
		$this->tableMapperArray['products/product/attributes/attribute/order_product_attribute_id'] = TABLE_ORDERS_PRODUCTS_ATTRIBUTES;
		$this->tableMapperArray['products/product/attributes/attribute/name'] = TABLE_ORDERS_PRODUCTS_ATTRIBUTES;
		$this->tableMapperArray['products/product/attributes/attribute/value'] = TABLE_ORDERS_PRODUCTS_ATTRIBUTES;
		$this->tableMapperArray['products/product/attributes/attribute/price'] = TABLE_ORDERS_PRODUCTS_ATTRIBUTES;
		$this->tableMapperArray['products/product/attributes/attribute/price_prefix'] = TABLE_ORDERS_PRODUCTS_ATTRIBUTES;
		$this->tableMapperArray['products/product/properties/property/order_product_property_id'] = 'orders_products_properties';
		$this->tableMapperArray['products/product/properties/property/product_combi_id'] = 'orders_products_properties';
		$this->tableMapperArray['products/product/properties/property/name'] = 'orders_products_properties';
		$this->tableMapperArray['products/product/properties/property/value'] = 'orders_products_properties';
		$this->tableMapperArray['products/product/properties/property/price_type'] = 'orders_products_properties';
		$this->tableMapperArray['products/product/properties/property/price'] = 'orders_products_properties';
		$this->tableMapperArray['products/product/gprint_elements/gprint_element/gprint_element_id'] = 'gm_gprint_orders_elements';
		$this->tableMapperArray['products/product/gprint_elements/gprint_element/name'] = 'gm_gprint_orders_elements';
		$this->tableMapperArray['products/product/gprint_elements/gprint_element/value'] = 'gm_gprint_orders_elements';
		$this->tableMapperArray['products/product/gprint_elements/gprint_element/download_url'] = 'gm_gprint_uploads';
		$this->tableMapperArray['totals/total/order_total_id'] = TABLE_ORDERS_TOTAL;
		$this->tableMapperArray['totals/total/title'] = TABLE_ORDERS_TOTAL;
		$this->tableMapperArray['totals/total/text'] = TABLE_ORDERS_TOTAL;
		$this->tableMapperArray['totals/total/value'] = TABLE_ORDERS_TOTAL;
		$this->tableMapperArray['totals/total/class'] = TABLE_ORDERS_TOTAL;
		$this->tableMapperArray['totals/total/sort_order'] = TABLE_ORDERS_TOTAL;
		$this->tableMapperArray['status_history/status_history_entry/order_status_history_id'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/status_history_entry/order_status_id'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/status_history_entry/date_added'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/status_history_entry/customer_notified'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/status_history_entry/comments'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/order_status_id'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/date_added'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/customer_notified'] = TABLE_ORDERS_STATUS_HISTORY;
		$this->tableMapperArray['status_history/comments'] = TABLE_ORDERS_STATUS_HISTORY;

		$this->languageDependentMapperArray = array();
	}
}