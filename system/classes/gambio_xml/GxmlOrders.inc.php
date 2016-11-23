<?php
/* --------------------------------------------------------------
  GxmlOrder.inc.php 2016-09-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlOrders
 * 
 * This class handles the API requests that concern the Orders section of the 
 * shop system.
 *
 * Supported API Functions: 
 *		- "download_orders" 
 *
 * Refactored by A.Tselegidis
 *
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlOrders extends GxmlMaster
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
	 * Handles the download orders API function. 
	 * 
	 * @param SimpleXMLElement $requestXml
	 *
	 * @return SimpleXMLElement Returns the response XML object. 
	 */
	public function downloadOrders(SimpleXMLElement $requestXml)
	{
		try
		{
			$parameters = $this->_generateSqlStrings($requestXml->parameters, array('limit', 'where'));
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_includeNode(array('customer_groups', 'tax_classes'));
			$this->_addOrdersNode($parameters);
			return $this->responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}
	

	/**
	 * Add a node to the provided xml.
	 *
	 * @param array $data Contains the data to be added to the XML object.
	 * @param string $tableColumn Table column will be used to define the $data key value.
	 * @param string $nodeName Node name will point the parent node name to be extended.
	 * @param SimpleXMLElement $xml (ByRef) XML object to be edited.
	 */
	protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{
			case 'download_url':
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false)
				{
					$downloadUrl = '';
					if(empty($data[$tableColumn]) === false)
					{
						$downloadUrl = $this->wrapValue(HTTP_SERVER . DIR_WS_CATALOG . 'gm_gprint_download.php?key=' . $data[$tableColumn]);
					}
					$xml->addChild($xmlName, $downloadUrl);
				}
				break;

			case 'products/product/order_product_id':
			case 'products/product/product_id':
			case 'products/product/model':
			case 'products/product/name':
			case 'products/product/price':
			case 'products/product/discount_made':
			case 'products/product/shipping_time':
			case 'products/product/final_price':
			case 'products/product/tax_rate':
			case 'products/product/quantity':
			case 'products/product/allow_tax':
			case 'products/product/quantity_unit_id':
			case 'products/product/unit_name':

				$xmlName = str_replace('products/product/', '', $nodeName);

				if($this->nodeExists($xml, 'products') === false) {
					$productsNode = $xml->addChild('products');
				} else {
					$productsNode = $xml->products;
				}

				$productNode = $this->nodeValueExists($productsNode, 'product', 'order_product_id', $data['o_products_id']);
				if($productNode === false)
				{	
					$productNode = $productsNode->addChild('product');
					$productNode->order_product_id = $data['o_products_id'];
					$productNode->product_id = $data['products_id'];
					$productNode->model = $this->wrapValue($data['products_model']);
					$productNode->name = $this->wrapValue($data['products_name']);
					$productNode->price = $data['products_price'];
					$productNode->discount_made = $data['products_discount_made'];
					$productNode->shipping_time = $data['products_shipping_time'];
					$productNode->final_price = $data['final_price'];
					$productNode->tax_rate = $data['products_tax'];
					$productNode->quantity = $data['products_quantity'];
					$productNode->allow_tax = $data['allow_tax'];
					$productNode->quantity_unit_id = $data['quantity_unit_id'];
					$productNode->unit_name = $this->wrapValue($data['unit_name']);
				}
				break;

			case 'products/product/attributes/attribute/order_product_attribute_id':
			case 'products/product/attributes/attribute/name':
			case 'products/product/attributes/attribute/value':
			case 'products/product/attributes/attribute/price':
			case 'products/product/attributes/attribute/price_prefix':

				$productNode = $this->nodeValueExists($xml->products, 'product', 'order_product_id', $data['o_products_id']);
				if($productNode !== false)
				{
					if(empty($data['orders_products_attributes_id']) === false) {

						$xmlName = str_replace('products/product/attributes/attribute/', '', $nodeName);

						if($this->nodeExists($productNode, 'attributes') === false) {
							$coo_attributes = $productNode->addChild('attributes');
						} else {
							$coo_attributes = $productNode->attributes;
						}

						$attributeNode = $this->nodeValueExists($coo_attributes, 'attribute', 'order_product_attribute_id', $data['orders_products_attributes_id']);
						if($attributeNode === false)
						{
							$attributeNode = $coo_attributes->addChild('attribute');
							$attributeNode->order_product_attribute_id = $data['orders_products_attributes_id'];
							$attributeNode->name = $this->wrapValue($data['products_options']);
							$attributeNode->value = $this->wrapValue($data['products_options_values']);
							$attributeNode->price = $data['options_values_price'];
							$attributeNode->price_prefix = $data['price_prefix'];
						}
					}
				}
				break;

			case 'products/product/gprint_elements/gprint_element/gprint_element_id':
			case 'products/product/gprint_elements/gprint_element/name':
			case 'products/product/gprint_elements/gprint_element/value':
			case 'products/product/gprint_elements/gprint_element/download_url':

				$productNode = $this->nodeValueExists($xml->products, 'product', 'order_product_id', $data['o_products_id']);
				if($productNode !== false)
				{
					if(empty($data['name']) === false) {

						$xmlName = str_replace('products/product/gprint_elements/gprint_element/', '', $nodeName);

						if($this->nodeExists($productNode, 'gprint_elements') === false) {
							$coo_gprint_elements = $productNode->addChild('gprint_elements');
						} else {
							$coo_gprint_elements = $productNode->gprint_elements;
						}

						$gprintNode = $this->nodeValueExists($coo_gprint_elements, 'gprint_element', 'gprint_element_id', $data['gm_gprint_orders_elements_id']);
						if($gprintNode === false)
						{
							$gprintNode = $coo_gprint_elements->addChild('gprint_element');
							$gprintNode->gprint_element_id = $data['gm_gprint_orders_elements_id'];
							$gprintNode->name = $this->wrapValue($data['name']);
							$gprintNode->value = $this->wrapValue($data['elements_value']);
							
							// Create the download url value. 
							$downloadUrl = '';
							if(!empty($data['download_key']))
							{
								$downloadUrl = HTTP_SERVER . DIR_WS_CATALOG . 'gm_gprint_download.php?key=' . $data['download_key'];
							}
							$gprintNode->download_url = $this->wrapValue($downloadUrl);
							
						}
					}
				}
				break;

			case 'products/product/properties/property/order_product_property_id':
			case 'products/product/properties/property/product_combi_id':
			case 'products/product/properties/property/name':
			case 'products/product/properties/property/value':
			case 'products/product/properties/property/price_type':
			case 'products/product/properties/property/price':

				$productNode = $this->nodeValueExists($xml->products, 'product', 'order_product_id', $data['o_products_id']);
				if($productNode !== false)
				{
					if(empty($data['orders_products_properties_id']) === false) {

						$xmlName = str_replace('products/product/properties/property/', '', $nodeName);

						if($this->nodeExists($productNode, 'properties') === false) {
							$coo_properties = $productNode->addChild('properties');
						} else {
							$coo_properties = $productNode->properties;
						}

						$propertyNode = $this->nodeValueExists($coo_properties, 'property', 'order_product_property_id', $data['orders_products_properties_id']);
						if($propertyNode === false)
						{
							$propertyNode = $coo_properties->addChild('property');
							$propertyNode->order_product_property_id = $data['orders_products_properties_id'];
							$propertyNode->product_combi_id = $data['products_properties_combis_id'];
							$propertyNode->name = $this->wrapValue($data['properties_name']);
							$propertyNode->value = $this->wrapValue($data['values_name']);
							$propertyNode->price_type = $data['properties_price_type'];
							$propertyNode->price = $data['properties_price'];
						}
					}
				}
				break;

			case 'totals/total/order_total_id':
			case 'totals/total/title':
			case 'totals/total/text':
			case 'totals/total/value':
			case 'totals/total/class':
			case 'totals/total/sort_order':

				$xmlName = str_replace('totals/total/', '', $nodeName);

				if($this->nodeExists($xml, 'totals') === false) {
					$totalsNode = $xml->addChild('totals');
				} else {
					$totalsNode = $xml->totals;
				}

				if($this->nodeValueExists($totalsNode, 'total', 'order_total_id', $data['orders_total_id']) === false)
				{
					$totalNode = $totalsNode->addChild('total');
					$totalNode->order_total_id = $data['orders_total_id'];
					$totalNode->title = $this->wrapValue($data['title']);
					$totalNode->text = $this->wrapValue($data['text']);
					$totalNode->value = $this->wrapValue($data['value']);
					$totalNode->class = $data['class'];
					$totalNode->sort_order = $data['sort_order'];
				}
				break;

			case 'status_history/status_history_entry/order_status_history_id':
			case 'status_history/status_history_entry/order_status_id':
			case 'status_history/status_history_entry/date_added':
			case 'status_history/status_history_entry/customer_notified':
			case 'status_history/status_history_entry/comments':

				$xmlName = str_replace('status_history/status_history_entry/', '', $nodeName);

				if($this->nodeExists($xml, 'status_history') === false) {
					$historyNode = $xml->addChild('status_history');
				} else {
					$historyNode = $xml->status_history;
				}

				if($this->nodeValueExists($historyNode, 'status_history_entry', 'order_status_history_id', $data['orders_status_history_id']) === false)
				{
					$totalNode = $historyNode->addChild('status_history_entry');
					$totalNode->order_status_history_id = $data['orders_status_history_id'];
					$totalNode->order_status_id = $data['orders_status_id'];
					$totalNode->date_added = $data['date_added'];
					$totalNode->customer_notified = $data['customer_notified'];
					$totalNode->comments = $this->wrapValue($data['comments']);
				}
				break;
			
			case 'payment_details/paypal_transactions/paypal_transaction_id':
				
				if($this->nodeExists($xml, 'payment_details') === false) {
					$paymentDetailsNode = $xml->addChild('payment_details');
				} else {
					$paymentDetailsNode = $xml->payment_details;
				}

				if($this->nodeExists($paymentDetailsNode, 'paypal_transactions') === false) {
					$paypalTransactionsNode = $paymentDetailsNode->addChild('paypal_transactions');
				} else {
					$paypalTransactionsNode = $paymentDetailsNode->paypal_transactions;
				}

				if(!empty($data['transaction_id']) && $this->nodeValueExists($paymentDetailsNode, 'paypal_transactions', 'paypal_transaction_id', $this->wrapValue($data['transaction_id'])) === false)
				{
					$paypalTransactionsNode = $paypalTransactionsNode->addChild('paypal_transaction_id', $this->wrapValue($data['transaction_id']));
				}
				break;
			
			case 'payment_details/paypal_payments/paypal_payment_id':
			case 'payment_details/paypal_payments/paypal_payment_mode':
				
				if($this->nodeExists($xml, 'payment_details') === false) {
					$paymentDetailsNode = $xml->addChild('payment_details');
				} else {
					$paymentDetailsNode = $xml->payment_details;
				}
				
				if($this->nodeExists($paymentDetailsNode, 'paypal_payments') === false) {
					$paypalPaymentsNode = $paymentDetailsNode->addChild('paypal_payments');
				} else {
					$paypalPaymentsNode = $paymentDetailsNode->paypal_payments;
				}
			
				if($this->nodeValueExists($paypalPaymentsNode, 'paypal_payments_entry', 'paypal_payment_id', $data['payment_id']) === false && !empty($data['payment_id']))
				{
					$paymentNode = $paypalPaymentsNode->addChild('paypal_payments_entry');
					$paymentNode->paypal_payment_id = $data['payment_id'];
					$paymentNode->paypal_payment_mode = $data['mode'];
				}
				
				break;
			
			case 'payment_details/payment_instructions/payment_instruction_id':
			case 'payment_details/payment_instructions/payment_instruction_reference':
			case 'payment_details/payment_instructions/payment_instruction_bank_name':
			case 'payment_details/payment_instructions/payment_instruction_account_holder':
			case 'payment_details/payment_instructions/payment_instruction_iban':
			case 'payment_details/payment_instructions/payment_instruction_bic':
			case 'payment_details/payment_instructions/payment_instruction_value':
			case 'payment_details/payment_instructions/payment_instruction_currency':
			case 'payment_details/payment_instructions/payment_instruction_due_date':
			
				if($this->nodeExists($xml, 'payment_details') === false) {
					$paymentDetailsNode = $xml->addChild('payment_details');
				} else {
					$paymentDetailsNode = $xml->payment_details;
				}
				
				if($this->nodeExists($paymentDetailsNode, 'payment_instructions') === false) {
					$paymentInstructionsNode = $paymentDetailsNode->addChild('payment_instructions');
				} else {
					$paymentInstructionsNode = $paymentDetailsNode->payment_instructions;
				}
				
				if($this->nodeValueExists($paymentInstructionsNode, 'payment_instructions_entry', 'payment_instruction_id', $data['orders_payment_instruction_id']) === false && !empty($data['orders_payment_instruction_id']))
				{
					$instructionNode = $paymentInstructionsNode->addChild('payment_instructions_entry');
					$instructionNode->payment_instruction_id = $data['orders_payment_instruction_id'];
					$instructionNode->payment_instruction_reference = $this->wrapValue($data['reference']);
					$instructionNode->payment_instruction_bank_name = $this->wrapValue($data['bank_name']);
					$instructionNode->payment_instruction_account_holder = $this->wrapValue($data['account_holder']);
					$instructionNode->payment_instruction_iban = $this->wrapValue($data['iban']);
					$instructionNode->payment_instruction_bic = $this->wrapValue($data['bic']);
					$instructionNode->payment_instruction_value = $data['value'];
					$instructionNode->payment_instruction_currency = $this->wrapValue($data['currency']);
					$instructionNode->payment_instruction_due_date = $data['due_date'];
				}
				
				break;
			
			case 'payment_details/klarna_reservation_id':
				if($this->nodeExists($xml, 'payment_details') === false) {
					$paymentDetailsNode = $xml->addChild('payment_details');
				} else {
					$paymentDetailsNode = $xml->payment_details;
				}

				if(!empty($data['rno']) && $this->nodeValueExists($xml, 'payment_details', 'klarna_reservation_id', $this->wrapValue($data['rno'])) === false)
				{
					$paymentDetailsNode = $paymentDetailsNode->addChild('klarna_reservation_id', $this->wrapValue($data['rno']));
				}
				break;

			case 'status_history/order_status_id':
			case 'status_history/date_added':
			case 'status_history/customer_notified':
			case 'status_history/comments':
				// skip (only needed for upload)
				break;

			case 'customer_status_name':
			case 'customer_status_image':
			case 'customer_name':
			case 'customer_firstname':
			case 'customer_lastname':
			case 'customer_company':
			case 'customer_street_address':
			case 'customer_house_number':
			case 'customer_additional_address_info':
			case 'customer_suburb':
			case 'customer_city':
			case 'customer_state':
			case 'customer_country':
			case 'customer_gender':
			case 'delivery_name':
			case 'delivery_firstname':
			case 'delivery_lastname':
			case 'delivery_company':
			case 'delivery_street_address':
			case 'delivery_house_number':
			case 'delivery_additional_address_info':
			case 'delivery_suburb':
			case 'delivery_city':
			case 'delivery_state':
			case 'delivery_country':
			case 'delivery_gender':
			case 'billing_name':
			case 'billing_firstname':
			case 'billing_lastname':
			case 'billing_company':
			case 'billing_street_address':
			case 'billing_house_number':
			case 'billing_additional_address_info':
			case 'billing_suburb':
			case 'billing_city':
			case 'billing_state':
			case 'billing_country':
			case 'billing_gender':
			case 'credit_card_owner':
			case 'comments':
			case 'shipping_method':
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false)
				{
					$xml->{$xmlName} = $this->wrapValue($data[$tableColumn]);
				}
				break;

			default:
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false)
				{
					$xml->{$xmlName} = $data[$tableColumn];
				}
				break;
		}
	}
	

	/**
	 * Add the parameters node to the response XML object. 
	 * 
	 * This method will apply the changes into the private responseXml property. It 
	 * will not return any value.
	 * 
	 * @param array $parameters (Optional) Contains the parameters passed by the client. 
	 */
	protected function _addOrdersNode(array $parameters = array())
	{
		$this->responseXml->addChild('orders');
		
		$whereClause   = " WHERE exported = '0' " . $this->_generateWhereClause($parameters, true);
		$orderByClause = ' ORDER BY ' . TABLE_ORDERS . '.orders_id ASC ';
		$limitation    = '';
		$orderIds      = true;
		
		if(count($parameters) > 0) // Apply the parameters to the query.
		{
			$orderIds = array();
			
			$groupClause = 'GROUP BY ' . TABLE_ORDERS . '.orders_id';
			$limitClause = $this->_generateLimitClause($parameters);
			
			$query = "SELECT DISTINCT
							" . TABLE_ORDERS . ".orders_id
						FROM " . TABLE_ORDERS . " 
							LEFT JOIN " . TABLE_ORDERS_TOTAL . " USING (orders_id)
							LEFT JOIN " . TABLE_ORDERS_STATUS_HISTORY . " USING (orders_id)
							LEFT JOIN " . TABLE_ORDERS_PRODUCTS . " USING (orders_id)
							LEFT JOIN paypal_transactions USING (orders_id)
							LEFT JOIN orders_klarna USING (orders_id)
							LEFT JOIN orders_payment_instruction USING (orders_id)
							LEFT JOIN orders_paypal_payments USING (orders_id)
							LEFT JOIN orders_products_quantity_units USING (orders_products_id)
							LEFT JOIN " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " USING (orders_products_id)
							LEFT JOIN orders_products_properties USING (orders_products_id)
							LEFT JOIN gm_gprint_orders_surfaces_groups USING (orders_products_id)
							LEFT JOIN gm_gprint_orders_surfaces USING (gm_gprint_orders_surfaces_groups_id)
							LEFT JOIN gm_gprint_orders_elements USING (gm_gprint_orders_surfaces_id)
							LEFT JOIN gm_gprint_uploads USING (gm_gprint_uploads_id)
						" . $whereClause . " 
						" . $groupClause . " 
						" . $orderByClause . " 
						" . $limitClause;
			$results = xtc_db_query($query);
			while($row = xtc_db_fetch_array($results))
			{
				$orderIds[] = $row['orders_id'];
			}
			
			$limit = 0;
			$offset = 0;
			
			if(isset($parameters['offset']) && !empty($parameters['offset']))
			{
				$offset = (int) $parameters['offset'];
			}

			if(isset($parameters['limit']) && !empty($parameters['limit']))
			{
				$limit = (int) $parameters['limit'];
				
				if(!empty($orderIds))
				{					
					if($whereClause == '' )
					{
						$limitation = ' WHERE ' . TABLE_ORDERS . '.orders_id IN (' . implode(',', $orderIds) . ') ';
					}
					else
					{
						$limitation .= ' AND ' . TABLE_ORDERS . '.orders_id IN (' . implode(',', $orderIds) . ') ';
					}
				}
			}			
		}
		
		if(empty($orderIds) == false) // There must be order record that comply with the filtering parameters.
		{
			$query = "SELECT 
							" . TABLE_ORDERS_PRODUCTS . ".orders_products_id AS o_products_id,
							" . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . ".*,
							paypal_transactions.*,
							orders_klarna.*,
							orders_products_quantity_units.*,
							gm_gprint_uploads.*,
							gm_gprint_orders_elements.*,
							orders_products_properties.*,
							orders_payment_instruction.*,
							orders_paypal_payments.*,
							" . TABLE_ORDERS_TOTAL . ".*,
							" . TABLE_ORDERS_STATUS_HISTORY . ".*,
							" . TABLE_ORDERS_PRODUCTS . ".*,
							" . TABLE_ORDERS . ".*
						FROM 
							" . TABLE_ORDERS . " 
							LEFT JOIN " . TABLE_ORDERS_TOTAL . " USING (orders_id)
							LEFT JOIN " . TABLE_ORDERS_STATUS_HISTORY . " USING (orders_id)
							LEFT JOIN " . TABLE_ORDERS_PRODUCTS . " USING (orders_id)
							LEFT JOIN paypal_transactions USING (orders_id)
							LEFT JOIN orders_klarna USING (orders_id)
							LEFT JOIN orders_payment_instruction USING (orders_id)
							LEFT JOIN orders_paypal_payments USING (orders_id)
							LEFT JOIN orders_products_quantity_units USING (orders_products_id)
							LEFT JOIN " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " USING (orders_products_id)
							LEFT JOIN orders_products_properties USING (orders_products_id)
							LEFT JOIN gm_gprint_orders_surfaces_groups USING (orders_products_id)
							LEFT JOIN gm_gprint_orders_surfaces USING (gm_gprint_orders_surfaces_groups_id)
							LEFT JOIN gm_gprint_orders_elements USING (gm_gprint_orders_surfaces_id)
							LEFT JOIN gm_gprint_uploads USING (gm_gprint_uploads_id)
						" . $whereClause . "
						" . $limitation . "
						" . $orderByClause;

			$results = xtc_db_query($query);
			
			$currRecordId = 0;
			$node = null;
			
			while($row = xtc_db_fetch_array($results))
			{
				if($currRecordId != $row['orders_id'])
				{
					$node = $this->responseXml->orders->addChild('order');
				}
				$this->add($row, $node);
				$currRecordId = $row['orders_id'];
			}
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
		$this->mapperArray['customer_house_number'] = 'customers_house_number';
		$this->mapperArray['customer_additional_address_info'] = 'customers_additional_info';
		$this->mapperArray['customer_suburb'] = 'customers_suburb';
		$this->mapperArray['customer_city'] = 'customers_city';
		$this->mapperArray['customer_postcode'] = 'customers_postcode';
		$this->mapperArray['customer_state'] = 'customers_state';
		$this->mapperArray['customer_country'] = 'customers_country';
		$this->mapperArray['customer_telephone'] = 'customers_telephone';
		$this->mapperArray['customer_email_address'] = 'customers_email_address';
		$this->mapperArray['customer_address_format_id'] = 'customers_address_format_id';
		$this->mapperArray['customer_gender'] = 'customers_gender';
		$this->mapperArray['delivery_name'] = 'delivery_name';
		$this->mapperArray['delivery_firstname'] = 'delivery_firstname';
		$this->mapperArray['delivery_lastname'] = 'delivery_lastname';
		$this->mapperArray['delivery_company'] = 'delivery_company';
		$this->mapperArray['delivery_street_address'] = 'delivery_street_address';
		$this->mapperArray['delivery_house_number'] = 'delivery_house_number';
		$this->mapperArray['delivery_additional_address_info'] = 'delivery_additional_info';
		$this->mapperArray['delivery_suburb'] = 'delivery_suburb';
		$this->mapperArray['delivery_city'] = 'delivery_city';
		$this->mapperArray['delivery_postcode'] = 'delivery_postcode';
		$this->mapperArray['delivery_state'] = 'delivery_state';
		$this->mapperArray['delivery_country'] = 'delivery_country';
		$this->mapperArray['delivery_country_iso_code'] = 'delivery_country_iso_code_2';
		$this->mapperArray['delivery_address_format_id'] = 'delivery_address_format_id';
		$this->mapperArray['delivery_gender'] = 'delivery_gender';
		$this->mapperArray['billing_name'] = 'billing_name';
		$this->mapperArray['billing_firstname'] = 'billing_firstname';
		$this->mapperArray['billing_lastname'] = 'billing_lastname';
		$this->mapperArray['billing_company'] = 'billing_company';
		$this->mapperArray['billing_street_address'] = 'billing_street_address';
		$this->mapperArray['billing_house_number'] = 'billing_house_number';
		$this->mapperArray['billing_additional_address_info'] = 'billing_additional_info';
		$this->mapperArray['billing_suburb'] = 'billing_suburb';
		$this->mapperArray['billing_city'] = 'billing_city';
		$this->mapperArray['billing_postcode'] = 'billing_postcode';
		$this->mapperArray['billing_state'] = 'billing_state';
		$this->mapperArray['billing_country'] = 'billing_country';
		$this->mapperArray['billing_country_iso_code'] = 'billing_country_iso_code_2';
		$this->mapperArray['billing_address_format_id'] = 'billing_address_format_id';
		$this->mapperArray['billing_gender'] = 'billing_gender';
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
		$this->mapperArray['payment_details/paypal_transactions/paypal_transaction_id'] = 'transaction_id';
		$this->mapperArray['payment_details/paypal_payments/paypal_payment_id'] = 'payment_id';
		$this->mapperArray['payment_details/paypal_payments/paypal_payment_mode'] = 'mode';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_id'] = 'orders_payment_instruction_id';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_reference'] = 'reference';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_bank_name'] = 'bank_name';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_account_holder'] = 'account_holder';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_iban'] = 'iban';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_bic'] = 'bic';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_value'] = 'value';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_currency'] = 'currency';
		$this->mapperArray['payment_details/payment_instructions/payment_instruction_due_date'] = 'due_date';
		$this->mapperArray['payment_details/klarna_reservation_id'] = 'rno';
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
		$this->tableMapperArray['customer_house_number'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_additional_address_info'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_suburb'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_city'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_postcode'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_state'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_country'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_telephone'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_email_address'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_address_format_id'] = TABLE_ORDERS;
		$this->tableMapperArray['customer_gender'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_name'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_firstname'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_lastname'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_company'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_street_address'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_house_number'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_additional_address_info'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_suburb'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_city'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_postcode'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_state'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_country'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_country_iso_code'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_address_format_id'] = TABLE_ORDERS;
		$this->tableMapperArray['delivery_gender'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_name'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_firstname'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_lastname'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_company'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_street_address'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_house_number'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_additional_address_info'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_suburb'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_city'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_postcode'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_state'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_country'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_country_iso_code'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_address_format_id'] = TABLE_ORDERS;
		$this->tableMapperArray['billing_gender'] = TABLE_ORDERS;
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
		$this->tableMapperArray['payment_details/paypal_transactions/paypal_transaction_id'] = 'paypal_transactions';
		$this->tableMapperArray['payment_details/paypal_payments/paypal_payment_id'] = 'orders_paypal_payments';
		$this->tableMapperArray['payment_details/paypal_payments/paypal_payment_mode'] = 'orders_paypal_payments';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_id'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_reference'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_bank_name'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_account_holder'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_iban'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_bic'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_value'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_currency'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/payment_instructions/payment_instruction_due_date'] = 'orders_payment_instruction';
		$this->tableMapperArray['payment_details/klarna_reservation_id'] = 'orders_klarna';
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


	/**
	 * Abstract Method: Delete Object from Database.
	 *
	 * Unimplemented method of the parent GxmlMaster class.
	 *
	 * @param SimpleXMLElement $xml
	 */
	protected function _deleteObject(SimpleXMLElement $xml) {}
}
