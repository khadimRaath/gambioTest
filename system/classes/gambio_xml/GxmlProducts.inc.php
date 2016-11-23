<?php
/* --------------------------------------------------------------
  GxmlProducts.inc.php 2015-06-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlProducts
 * 
 * Handles the API requests that concern the products section of the shop system.
 * 
 * Supported API Functions: 
 * 		- "upload_products"
 * 		- "download_products"
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlProducts extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->_setPluralName('products');
		$this->_setSingularName('product');
		$this->_setupMapperArray();
		$this->_setupTableMapperArray();
	}

	
	/**
	 * Download Products API Method
	 *
	 * This method handles the download product function requests done by the
	 * API clients.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client.
	 *
	 * @return SimpleXMLElement Returns the response XML object.
	 */
	public function downloadProducts(SimpleXMLElement $requestXml)
	{
		try {
			$parameters = $this->_generateSqlStrings($requestXml->parameters, array('limit', 'where'));
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_includeNode(array('languages', 'customer_groups', 'shipping_times', 'tax_classes', 'base_price_units', 'quantity_units'));
			$this->_addProductsNode($parameters);
			return $this->responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}

	
	/**
	 * Upload Products API Method
	 *
	 * This method handles the upload product function requests done by the
	 * API clients.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client.
	 *
	 * @return SimpleXMLElement Returns the response XML object.
	 */
	public function uploadProducts(SimpleXMLElement $requestXml)
	{
		try
		{
			$responseData = array();
			$combisXmlArray = array();
			$productCombis = MainFactory::create_object('GxmlProductCombis');

			// Validate the request XML for property data.  
			if(!property_exists($requestXml->parameters, 'products'))
			{
				throw new InvalidArgumentException('Request XML is invalid: Missing parameters->products element.');
			}

			foreach($requestXml->parameters->products->children() as $productXml)
			{
				// update tax_class_id by given tax_rate
				$isValid = $this->_validateTax($productXml, $responseData);

				if($isValid === true)
				{
					if(isset($productXml->product_id) == false
					   && (isset($productXml->categories) == false || isset($productXml->categories->category) == false))
					{
						$this->addXmlChild($productXml, simplexml_load_string('<categories>
							<category>
								<category_id>0</category_id>
							</category>
						</categories>'), false);
					}

					$this->_upload($productXml, $responseData);

					$actualProductId = !empty($responseData) && isset($responseData[count($responseData) - 1]['product_id'])
						? $responseData[count($responseData) - 1]['product_id']
						: false;

					if ($actualProductId != false && isset($productXml->product_combis))
					{
						$combisXmlArray[] = $productCombis->uploadProductCombis($productXml, $actualProductId);
					}
				}
			}

			$responseXml = $this->generateResponseXml($responseData);
			if(strlen((string)$this->responseXmlBuffer) > 0)
			{
				$additionalImages = $responseXml->addChild('additional_images');
				foreach($this->responseXmlBuffer as $imageData)
				{
					$image = $additionalImages->addChild('image');
					foreach($imageData as $name => $value)
					{
						$image->addChild($name, $value);
					}
				}
			}

			foreach($combisXmlArray as $productCombisXml)
			{
				$productCombiArray = $productCombisXml->product_combis->children();

				if(isset($responseXml->product_combis) === false)
				{
					$responseProductCombisXml = $responseXml->addChild('product_combis');
				}

				foreach($productCombiArray as $productCombiXml)
				{
					$this->addXmlChild($responseProductCombisXml, $productCombiXml);
					$responseXml->request->success &= $productCombiXml->success;
				}
			}

			return $responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}

	
	/**
	 * Add node to the response XML object.
	 *
	 * @param array $data Contains the data to be included within the XML response object.
	 * @param string $tableColumn Table column will point the value that we need from the $data.
	 * @param string $nodeName This is the name of the node to be inserted.
	 * @param SimpleXMLElement $xml The xml object that will be edited.
	 */
	protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{
			case 'tax_rate':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					// Check whether the product has a tax class 
					if(isset($data['products_tax_class_id']) && $data['products_tax_class_id'] != 0)
					{
						$xml->{$nodeName} = $this->taxArray[$data['products_tax_class_id']]['tax_rate'];
					}
				}
				break;

			case 'name':
			case 'description':
			case 'short_description':
			case 'keywords':
			case 'meta_title':
			case 'meta_description':
			case 'meta_keywords':
			case 'url':
			case 'viewed':
			case 'image_alt_text':
			case 'url_keywords':
				$xmlName = $nodeName;
	
				// Check that the current category language exists in the database.
				$languageIso = (isset($this->languageArray[$data['language_id']]))
						? $this->languageArray[$data['language_id']]['iso']
						: '';
	
				$attributes = array(
					'language_id' => $data['language_id'],
					'language_iso' => $languageIso
				);
				if($this->nodeExists($xml, $xmlName, $attributes, 'or') === false)
				{
					$xml->addChild($nodeName);
					$xml->{$nodeName}[count($xml->{$nodeName}) - 1] = $this->wrapValue($data[$tableColumn]);
					$childNode = $xml->{$nodeName}[count($xml->{$nodeName}) - 1];
					$childNode['language_id'] = $data['language_id'];
					$childNode['language_iso'] =$languageIso;

					if($xmlName == 'description' ||
					   $xmlName == 'short_description')
					{
						$childNode['type'] = 2;
					}
				}

				break;

			case 'customer_group_permission':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					foreach($data AS $column => $value)
					{
						if(strpos($column, 'group_permission_') !== false)
						{
							$xml->addChild($nodeName);
							$xml->{$nodeName}[count($xml->{$nodeName}) - 1] = $value;
							$childNode = $xml->{$nodeName}[count($xml->{$nodeName}) - 1];
							$childNode['customer_group_id'] = str_replace('group_permission_', '', $column);
						}
					}
				}

				break;

			case 'image':
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false)
				{
					$xml->{$xmlName} = $this->wrapValue($data[$tableColumn]);
					$xml->{$xmlName}['width'] = $data['products_image_w'];
					$xml->{$xmlName}['height'] = $data['products_image_h'];
				}

				break;

			case 'image_url':
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false)
				{
					$productImageUrl = '';
					if(empty($data[$tableColumn]) === false)
					{
						$productImageUrl = $this->wrapValue(HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'product_images/original_images/' . $data[$tableColumn]);
					}
					$xml->{$xmlName} = $productImageUrl;
				}

				break;

			case 'personal_offers':
				$dataArray = array();

				foreach($data AS $column=>$value)
				{
					if(strpos($column, 'personal_offers_quantity_') !== false)
					{
						$customersStatusId = str_replace('personal_offers_quantity_', '', $column);

						$dataArray[$customersStatusId][(string) $value] = (string)$data['personal_offers_price_' . $customersStatusId];
					}
				}

				if(!empty($dataArray))
				{
					foreach($dataArray AS $customersStatusId => $t_personal_offers_array)
					{
						foreach($t_personal_offers_array AS $quantity => $price)
						{
							$t_quantity_value = (double)$quantity;
							if(!empty($t_quantity_value))
							{
								if($this->nodeExists($xml, $nodeName) === false)
								{
									$childNode = $xml->addChild($nodeName);
								}
								else
								{
									$childNode = $xml->personal_offers;
								}

								if($this->nodeValueExists($childNode, 'personal_offer', 'quantity', $quantity, array('customer_group_id' => $customersStatusId)) === false)
								{
									$personalOfferNode = $childNode->addChild('personal_offer');
									$personalOfferNode['customer_group_id'] = $customersStatusId;

									$personalOfferNode->quantity = $quantity;
									$personalOfferNode->price = $price;
								}
							}
						}
					}
				}

				break;

			case 'additional_images/image/image_id':
			case 'additional_images/image/filename':
			case 'additional_images/image/sort_order':
			case 'additional_images/image/visible':
			case 'additional_images/image/image_alt_text':
			case 'additional_images/image/image_url':
				break;
			
			case 'special/special_id':
			case 'special/quantity':
			case 'special/price':
			case 'special/date_added':
			case 'special/last_modified':
			case 'special/expiring_date':
			case 'special/date_status_change':
			case 'special/status':
				$xmlName = str_replace('special/', '', $nodeName);

				if(!empty($data[$this->mapperArray['special/special_id']]))
				{
					if($this->nodeExists($xml, 'special') === false)
					{
						$specialNode = $xml->addChild('special');
					}
					else
					{
						$specialNode = $xml->special;
					}

					if($this->nodeExists($specialNode, $xmlName) === false)
					{
						$specialNode->{$xmlName} = $data[$tableColumn];
					}
				}
				break;

			case 'categories/category/category_id':
				$xmlName = substr($nodeName, strrpos($nodeName, '/') + 1);

				if(!empty($data[$this->mapperArray['categories/category/category_id']]))
				{
					if($this->nodeExists($xml, 'categories') === false)
					{
						$categoriesNode = $xml->addChild('categories');
					}
					else
					{
						$categoriesNode = $xml->categories;
					}

					foreach ($data[$tableColumn] as $t_category_id)
					{
						if($this->nodeValueExists($categoriesNode, 'category', $xmlName, $t_category_id) === false)
						{
							$categoryNode = $categoriesNode->addChild('category');
							$categoryNode->{$xmlName} = $t_category_id;
						}
					}
				}
				break;
			case 'cross_selling_products/cross_selling_product/product_id':
			case 'cross_selling_products/cross_selling_product/sort_order':
				break;

			case 'ean':
			case 'model':
			case 'template':
			case 'attributes_template':
			case 'attributes_listing_template':
			case 'filename':
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false)
				{
					$xml->{$xmlName} = $this->wrapValue($data[$tableColumn]);
				}
				break;

			default:
				$xmlName = $nodeName;

				if($this->nodeExists($xml, $xmlName) === false && isset($data[$tableColumn]))
				{
					$xml->{$xmlName} = $data[$tableColumn];
				}
				break;
		}
	}

	
	/**
	 * Abstract Method Implementation: Delete Object
	 *
	 * This is an implementation of the abstract method declared in the GxmlMaster class. It
	 * removes an object from the database.
	 *
	 * @param SimpleXMLElement $requestXml Contains the information of the request sent by the client.
	 *
	 * @return bool Returns the operation result.
	 */
	protected function _deleteObject(SimpleXMLElement $requestXml)
	{
		$productData = $this->_parseXmlData($requestXml);
		$productData = $this->convertToLatin1($productData);
		$productsId = (string) $requestXml->product_id;
		$deletedRecords = array();
		$result = true;

		// Select all "product_ids"
		if(empty($productsId))
		{
			$query = '
					SELECT products_id
					FROM products
					WHERE';

			foreach($productData['products'] as $key=>$value)
			{
				$query .= $key . ' LIKE \'' . $value . '\' AND ';
			}

			$query = substr($query, 0, strlen($query) - 5);

			$results = xtc_db_query($query);
			if(xtc_db_num_rows(($results)))
			{
				while($row = xtc_db_fetch_array($results))
				{
					$deletedRecords[] = $row['products_id'];
				}
			}
		}
		else
		{
			$deletedRecords[] = $productsId;
		}

		// Delete Product
		// Product data are located into many tables in the database. The quickest way to delete them
		// at once is to include as many tables as possible in one DELETE query and remove the records 
		// that have are relevant to the specified product.  
		if(!empty($deletedRecords))
		{

			foreach($deletedRecords as $recordId)
			{
				$query = '
					DELETE products, products_attributes, products_attributes_download, products_content, products_description,
						products_google_categories, products_graduated_prices, products_hermesoptions, products_images, products_item_codes, 
						products_notifications, products_properties_admin_select, products_properties_combis,
						products_properties_combis_defaults, products_properties_combis_values, products_properties_index, products_quantity_unit, 
						products_to_categories, products_xsell, customers_basket, customers_basket_attributes, 
						gm_prd_img_alt, gm_gmotion, gm_gmotion_products, categories_index, feature_set_to_products, feature_set, 
						feature_set_values, reviews 
					FROM products
					
					LEFT JOIN products_attributes ON products_attributes.products_id = products.products_id 
					LEFT JOIN products_attributes_download ON products_attributes_download.products_attributes_id = products_attributes.products_attributes_id
					LEFT JOIN products_content ON products_content.products_id = products.products_id 
					LEFT JOIN products_description ON products_description.products_id = products.products_id 
					LEFT JOIN products_google_categories ON products_google_categories.products_id = products.products_id 
					LEFT JOIN products_graduated_prices ON products_graduated_prices.products_id = products.products_id 
					LEFT JOIN products_hermesoptions ON products_hermesoptions.products_id = products.products_id 
					LEFT JOIN products_images ON products_images.products_id = products.products_id 
					LEFT JOIN products_item_codes ON products_item_codes.products_id = products.products_id
					LEFT JOIN products_notifications ON products_notifications.products_id = products.products_id 
					LEFT JOIN products_properties_admin_select ON products_properties_admin_select.products_id = products.products_id 
					LEFT JOIN products_properties_combis ON products_properties_combis.products_id = products.products_id 
					LEFT JOIN products_properties_combis_defaults ON products_properties_combis_defaults.products_id = products.products_id 
					LEFT JOIN products_properties_combis_values ON products_properties_combis_values.products_properties_combis_id = products_properties_combis.products_properties_combis_id 
					LEFT JOIN products_properties_index ON products_properties_index.products_id = products.products_id 
					LEFT JOIN products_quantity_unit ON products_quantity_unit.products_id = products.products_id 
					LEFT JOIN products_to_categories ON products_to_categories.products_id = products.products_id 
					LEFT JOIN products_xsell ON products_xsell.products_id = products.products_id
					
					LEFT JOIN customers_basket ON customers_basket.products_id = products.products_id
					LEFT JOIN customers_basket_attributes ON customers_basket_attributes.products_id = products.products_id
					LEFT JOIN gm_prd_img_alt ON gm_prd_img_alt.products_id = products.products_id 
					LEFT JOIN gm_gmotion ON gm_gmotion.products_id = products.products_id
					LEFT JOIN gm_gmotion_products ON gm_gmotion_products.products_id = products.products_id
					LEFT JOIN categories_index ON categories_index.products_id = products.products_id
					
					LEFT JOIN feature_set_to_products ON feature_set_to_products.products_id = products.products_id 
					LEFT JOIN feature_set ON feature_set.feature_set_id = feature_set_to_products.feature_set_id 
					LEFT JOIN feature_set_values ON feature_set_values.feature_set_id = feature_set.feature_set_id 
					
					LEFT JOIN reviews ON reviews.products_id = products.products_id
					
					WHERE products.products_id = "' . xtc_db_prepare_input($recordId) .  '";
				';

				$result &= xtc_db_query($query);
			}
		}

		return $result;
	}

	
	/**
	 * Setup the mapper array that will map XML string names to the actual
	 * DB names. 
	 */
	protected function _setupMapperArray()
	{
		$this->mapperArray = array();
		
		$this->mapperArray['product_id'] = 'products_id';
		$this->mapperArray['ean'] = 'products_ean';
		$this->mapperArray['quantity'] = 'products_quantity';
		$this->mapperArray['shipping_time_id'] = 'products_shippingtime';
		$this->mapperArray['model'] = 'products_model';
		$this->mapperArray['sort_order'] = 'products_sort';
		$this->mapperArray['image'] = 'products_image';
		$this->mapperArray['image_url'] = 'products_image';
		$this->mapperArray['price'] = 'products_price';
		$this->mapperArray['discount_allowed'] = 'products_discount_allowed';
		$this->mapperArray['date_added'] = 'products_date_added';
		$this->mapperArray['last_modified'] = 'products_last_modified';
		$this->mapperArray['date_available'] = 'products_date_available';
		$this->mapperArray['weight'] = 'products_weight';
		$this->mapperArray['status'] = 'products_status';
		$this->mapperArray['tax_class_id'] = 'products_tax_class_id';
		$this->mapperArray['tax_rate'] = 'tax_rate';
		$this->mapperArray['template'] = 'product_template';
		$this->mapperArray['attributes_template'] = 'options_template';
		$this->mapperArray['attributes_listing_template'] = 'gm_options_template';
		$this->mapperArray['products_ordered'] = 'products_ordered';
		$this->mapperArray['price_status'] = 'gm_price_status';
		$this->mapperArray['min_order'] = 'gm_min_order';
		$this->mapperArray['graduated_quantity'] = 'gm_graduated_qty';
		$this->mapperArray['startpage_sort'] = 'products_startpage_sort';
		$this->mapperArray['shipping_costs'] = 'nc_ultra_shipping_costs';
		$this->mapperArray['priority'] = 'gm_priority';
		$this->mapperArray['change_frequency'] = 'gm_changefreq';
		$this->mapperArray['fsk18'] = 'products_fsk18';
		
		$this->mapperArray['name'] = 'products_name';
		$this->mapperArray['description'] = 'products_description';
		$this->mapperArray['short_description'] = 'products_short_description';
		$this->mapperArray['keywords'] = 'products_keywords';
		$this->mapperArray['meta_title'] = 'products_meta_title';
		$this->mapperArray['meta_description'] = 'products_meta_description';
		$this->mapperArray['meta_keywords'] = 'products_meta_keywords';
		$this->mapperArray['url'] = 'products_url';
		$this->mapperArray['viewed'] = 'products_viewed';
		$this->mapperArray['image_alt_text'] = 'gm_alt_text';
		$this->mapperArray['url_keywords'] = 'gm_url_keywords';
		
		$this->mapperArray['quantity_unit_id'] = 'quantity_unit_id';
		$this->mapperArray['base_price_unit_id'] = 'products_vpe';
		$this->mapperArray['base_price_unit_status'] = 'products_vpe_status';
		$this->mapperArray['base_price_unit_value'] = 'products_vpe_value';
		
		$this->mapperArray['show_date_added'] = 'gm_show_date_added';
		$this->mapperArray['show_price_offer'] = 'gm_show_price_offer';
		$this->mapperArray['show_weight'] = 'gm_show_weight';
		$this->mapperArray['show_quantity_info'] = 'gm_show_qty_info';
		$this->mapperArray['show_image'] = 'gm_show_image';
		$this->mapperArray['show_on_startpage'] = 'products_startpage';
		$this->mapperArray['sitemap_entry'] = 'gm_sitemap_entry';
		
		$this->mapperArray['properties_dropdown_mode'] = 'properties_dropdown_mode';
		$this->mapperArray['properties_show_price'] = 'properties_show_price';
		$this->mapperArray['use_properties_combis_weight'] = 'use_properties_combis_weight';
		$this->mapperArray['use_properties_combis_quantity'] = 'use_properties_combis_quantity';
		$this->mapperArray['use_properties_combis_shipping_time'] = 'use_properties_combis_shipping_time';
		
		$this->mapperArray['customer_group_permission'] = 'group_permission_';
		$this->mapperArray['personal_offers'] = 'personal_offers_by_customers_status_';

		$this->mapperArray['additional_images/image/image_id'] = 'image_id';
		$this->mapperArray['additional_images/image/external_image_id'] = 'external_image_id';
		$this->mapperArray['additional_images/image/filename'] = 'image_name';
		$this->mapperArray['additional_images/image/image_url'] = 'image_name';
		$this->mapperArray['additional_images/image/sort_order'] = 'image_nr';
		$this->mapperArray['additional_images/image/visible'] = 'gm_show_image';
		$this->mapperArray['additional_images/image/image_alt_text'] = 'gm_alt_text';
				
		$this->mapperArray['special/special_id'] = 'specials_id';
		$this->mapperArray['special/quantity'] = 'specials_quantity';
		$this->mapperArray['special/price'] = 'specials_new_products_price';
		$this->mapperArray['special/date_added'] = 'specials_date_added';
		$this->mapperArray['special/last_modified'] = 'specials_last_modified';
		$this->mapperArray['special/expiring_date'] = 'expires_date';
		$this->mapperArray['special/date_status_change'] = 'date_status_change';
		$this->mapperArray['special/status'] = 'status';
		
		$this->mapperArray['categories/category/category_id'] = 'categories_id';
		
		$this->mapperArray['cross_selling_products/cross_selling_product/product_id'] = 'xsell_id';
		$this->mapperArray['cross_selling_products/cross_selling_product/sort_order'] = 'sort_order';
	}

	
	/**
	 * Setup the mapper array that will map XML string names to the actual
	 * DB table names.
	 */
	protected function _setupTableMapperArray()
	{
		$this->tableMapperArray = array();
		
		$this->tableMapperArray['product_id'] = TABLE_PRODUCTS;
		$this->tableMapperArray['ean'] = TABLE_PRODUCTS;
		$this->tableMapperArray['quantity'] = TABLE_PRODUCTS;
		$this->tableMapperArray['shipping_time_id'] = TABLE_PRODUCTS;
		$this->tableMapperArray['model'] = TABLE_PRODUCTS;
		$this->tableMapperArray['sort_order'] = TABLE_PRODUCTS;
		$this->tableMapperArray['image'] = TABLE_PRODUCTS;
		$this->tableMapperArray['image_url'] = TABLE_PRODUCTS;
		$this->tableMapperArray['price'] = TABLE_PRODUCTS;
		$this->tableMapperArray['discount_allowed'] = TABLE_PRODUCTS;
		$this->tableMapperArray['date_added'] = TABLE_PRODUCTS;
		$this->tableMapperArray['last_modified'] = TABLE_PRODUCTS;
		$this->tableMapperArray['date_available'] = TABLE_PRODUCTS;
		$this->tableMapperArray['weight'] = TABLE_PRODUCTS;
		$this->tableMapperArray['status'] = TABLE_PRODUCTS;
		$this->tableMapperArray['tax_class_id'] = TABLE_PRODUCTS;
		$this->tableMapperArray['tax_rate'] = TABLE_PRODUCTS;
		$this->tableMapperArray['template'] = TABLE_PRODUCTS;
		$this->tableMapperArray['attributes_template'] = TABLE_PRODUCTS;
		$this->tableMapperArray['attributes_listing_template'] = TABLE_PRODUCTS;
		$this->tableMapperArray['products_ordered'] = TABLE_PRODUCTS;
		$this->tableMapperArray['price_status'] = TABLE_PRODUCTS;
		$this->tableMapperArray['min_order'] = TABLE_PRODUCTS;
		$this->tableMapperArray['graduated_quantity'] = TABLE_PRODUCTS;
		$this->tableMapperArray['startpage_sort'] = TABLE_PRODUCTS;
		$this->tableMapperArray['shipping_costs'] = TABLE_PRODUCTS;
		$this->tableMapperArray['priority'] = TABLE_PRODUCTS;
		$this->tableMapperArray['change_frequency'] = TABLE_PRODUCTS;
		$this->tableMapperArray['fsk18'] = TABLE_PRODUCTS;
		
		$this->tableMapperArray['name'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['description'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['short_description'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['keywords'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['meta_title'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['meta_description'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['meta_keywords'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['url'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['viewed'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['image_alt_text'] = TABLE_PRODUCTS_DESCRIPTION;
		$this->tableMapperArray['url_keywords'] = TABLE_PRODUCTS_DESCRIPTION;
		
		$this->tableMapperArray['quantity_unit_id'] = 'products_quantity_unit';
		$this->tableMapperArray['base_price_unit_id'] = TABLE_PRODUCTS;
		$this->tableMapperArray['base_price_unit_status'] = TABLE_PRODUCTS;
		$this->tableMapperArray['base_price_unit_value'] = TABLE_PRODUCTS;
		
		$this->tableMapperArray['show_date_added'] = TABLE_PRODUCTS;
		$this->tableMapperArray['show_price_offer'] = TABLE_PRODUCTS;
		$this->tableMapperArray['show_weight'] = TABLE_PRODUCTS;
		$this->tableMapperArray['show_quantity_info'] = TABLE_PRODUCTS;
		$this->tableMapperArray['show_image'] = TABLE_PRODUCTS;
		$this->tableMapperArray['show_on_startpage'] = TABLE_PRODUCTS;
		$this->tableMapperArray['sitemap_entry'] = TABLE_PRODUCTS;
		
		$this->tableMapperArray['properties_dropdown_mode'] = TABLE_PRODUCTS;
		$this->tableMapperArray['properties_show_price'] = TABLE_PRODUCTS;
		$this->tableMapperArray['use_properties_combis_weight'] = TABLE_PRODUCTS;
		$this->tableMapperArray['use_properties_combis_quantity'] = TABLE_PRODUCTS;
		$this->tableMapperArray['use_properties_combis_shipping_time'] = TABLE_PRODUCTS;
		
		$this->tableMapperArray['customer_group_permission'] = TABLE_PRODUCTS;
		$this->tableMapperArray['personal_offers'] = TABLE_PERSONAL_OFFERS_BY;
		$this->tableMapperArray['personal_offer'] = TABLE_PERSONAL_OFFERS_BY;

		$this->tableMapperArray['additional_images/image/image_id'] = TABLE_PRODUCTS_IMAGES;
		$this->tableMapperArray['additional_images/image/external_image_id'] = TABLE_PRODUCTS_IMAGES;
		$this->tableMapperArray['additional_images/image/filename'] = TABLE_PRODUCTS_IMAGES;
		$this->tableMapperArray['additional_images/image/image_url'] = TABLE_PRODUCTS_IMAGES;
		$this->tableMapperArray['additional_images/image/sort_order'] = TABLE_PRODUCTS_IMAGES;
		$this->tableMapperArray['additional_images/image/visible'] = TABLE_PRODUCTS_IMAGES;
		$this->tableMapperArray['additional_images/image/image_alt_text'] = 'gm_prd_img_alt';
		
		$this->tableMapperArray['special/special_id'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/quantity'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/price'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/date_added'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/last_modified'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/expiring_date'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/date_status_change'] = TABLE_SPECIALS;
		$this->tableMapperArray['special/status'] = TABLE_SPECIALS;
		
		$this->tableMapperArray['categories/category/category_id'] = TABLE_PRODUCTS_TO_CATEGORIES;
		
		$this->tableMapperArray['cross_selling_products/cross_selling_product/product_id'] = TABLE_PRODUCTS_XSELL;
		$this->tableMapperArray['cross_selling_products/cross_selling_product/sort_order'] = TABLE_PRODUCTS_XSELL;
                
		$this->languageDependentMapperArray = array(TABLE_PRODUCTS_DESCRIPTION, 'gm_prd_img_alt');
	}
	

	/**
	 * Generate Personal Offers query clauses. 
	 * 
	 * This method will generate the query parts for the personal offers. 
	 * 
	 * @return array Returns an array that contains the query parts of the personal offers 
	 *               SQL query. 
	 */
	protected function _generatePersonalOffersQuery()
	{
		$queryClauses = array();
		$selectClause = '';
		$joinClause = '';
		
		$query = "SELECT customers_status_id 
					FROM " . TABLE_CUSTOMERS_STATUS . " 
					WHERE customers_status_id > 0 
					GROUP BY customers_status_id 
					ORDER BY customers_status_id ASC";
		$results = xtc_db_query($query);
		
		while($row = xtc_db_fetch_array($results))
		{
			$selectClause .= ', p' . $row['customers_status_id'] . '.quantity AS personal_offers_quantity_' . $row['customers_status_id'] . ',
								p' . $row['customers_status_id'] . '.personal_offer AS personal_offers_price_' . $row['customers_status_id'];
			$joinClause .= ' LEFT JOIN personal_offers_by_customers_status_' . $row['customers_status_id'] . ' AS p' . $row['customers_status_id'] . ' ON (p' . $row['customers_status_id'] . '.products_id = ' . TABLE_PRODUCTS . '.products_id) ';
		}
		
		$queryClauses['select'] = $selectClause;
		$queryClauses['join'] = $joinClause;
				
		return $queryClauses;
	}


	/**
	 * Add products node to the response XML object. 
	 * 
	 * This method will edit the private property response XML object. 
	 * 
	 * @param array $parameters Parameters include information about filtering the results. 
	 */
	protected function _addProductsNode(array $parameters = array())
	{
		$this->responseXml->addChild('products');
		
		$productIds = true;
		$productsToCategories = array();
		
		if(count($parameters) === 0)
		{
			$query = 'SELECT * FROM ' . TABLE_PRODUCTS_TO_CATEGORIES;
			$results = xtc_db_query($query);

			while($row = xtc_db_fetch_array($results))
			{
				if(isset($productsToCategories[$row['products_id']]) === false)
				{
					$productsToCategories[$row['products_id']] = array();
				}
				
				$productsToCategories[$row['products_id']][] = $row['categories_id'];
			}
		}
		
		$whereClause = $this->_generateWhereClause($parameters);
		$personalOffersClause = $this->_generatePersonalOffersQuery();
		$limitation = '';
		
		if(count($parameters) > 0)
		{
			$productIds = array();
			$limitClause = ' GROUP BY ' . TABLE_PRODUCTS . '.products_id' . $this->_generateLimitClause($parameters);
			
			$query = "
				SELECT " . TABLE_PRODUCTS . ".products_id
				FROM " . TABLE_PRODUCTS . "
				LEFT JOIN " . TABLE_SPECIALS . " ON (" . TABLE_SPECIALS . ".products_id = " . TABLE_PRODUCTS . ".products_id)
				" . $personalOffersClause['join'] . "
				LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " ON (" . TABLE_PRODUCTS_DESCRIPTION . ".products_id = " . TABLE_PRODUCTS . ".products_id)
				LEFT JOIN products_quantity_unit ON (products_quantity_unit.products_id = " . TABLE_PRODUCTS . ".products_id)
				LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ON (" . TABLE_PRODUCTS_TO_CATEGORIES . ".products_id = " . TABLE_PRODUCTS . ".products_id)
				" . $whereClause . "
				" . $limitClause;
			
			$results = xtc_db_query($query);
			while($row = xtc_db_fetch_array($results))
			{
				$productIds[] = $row['products_id'];
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
				
				if(!empty($productIds))
				{					
					if($whereClause == '' )
					{
						$limitation = ' WHERE ' . TABLE_PRODUCTS . '.products_id IN (' . implode(',', $productIds) . ') ';
					}
					else
					{
						$limitation .= ' AND ' . TABLE_PRODUCTS . '.products_id IN (' . implode(',', $productIds) . ') ';
					}
				}
			}
			
			if(!empty($productIds))
			{
				$query = 'SELECT * FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id IN (' . implode(',', $productIds) . ')';
				$results = xtc_db_query($query);

				while ($row = xtc_db_fetch_array($results))
				{
					if(isset($productsToCategories[$row['products_id']]) === false)
					{
						$productsToCategories[$row['products_id']] = array();
					}
					
					$productsToCategories[$row['products_id']][] = $row['categories_id'];
				}
			}
		}

		if(empty($productIds) == false)
		{
			$query = "
				SELECT 
					" . TABLE_SPECIALS . ".*,
					" . TABLE_PRODUCTS_DESCRIPTION . ".*,
					" . TABLE_PRODUCTS . ".*,
					products_quantity_unit.quantity_unit_id
					" . $personalOffersClause['select'] . "
				FROM " . TABLE_PRODUCTS . "
				LEFT JOIN " . TABLE_SPECIALS . " ON (" . TABLE_SPECIALS . ".products_id = " . TABLE_PRODUCTS . ".products_id)
				" . $personalOffersClause['join'] . "
				LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " ON (" . TABLE_PRODUCTS_DESCRIPTION . ".products_id = " . TABLE_PRODUCTS . ".products_id)
				LEFT JOIN products_quantity_unit ON (products_quantity_unit.products_id = " . TABLE_PRODUCTS . ".products_id)
				LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ON (" . TABLE_PRODUCTS_TO_CATEGORIES . ".products_id = " . TABLE_PRODUCTS . ".products_id)
				" . $whereClause . "
				" . $limitation;
			$results = xtc_db_query($query);

			$currRecordId = 0;

			$gxmlPropertyValues = MainFactory::create_object('GxmlProductCombis');

			while ($row = xtc_db_fetch_array($results))
			{
				if ($currRecordId != $row['products_id'])
				{
					$productChildNode = $this->responseXml->products->addChild('product');
				}
				
				$categoriesIdArray = array(0);
				if(isset($productsToCategories[$row['products_id']]))
				{
					$categoriesIdArray = $productsToCategories[$row['products_id']];
				}

				$row['categories_id'] = $categoriesIdArray;
				$this->add($row, $productChildNode);


				if ($currRecordId != $row['products_id'])
				{
					//add additional images
					$this->_addImagesBlock($productChildNode, $row['products_id']);
					
					// add combis
					$gxmlPropertyValues->addProductsCombisNode( array( 'product_child' => $productChildNode, 'product_id' => $row['products_id'] ) );

					// add xselling
					$this->_addXsellingNode($productChildNode, $row['products_id']);
				}

				$currRecordId = $row['products_id'];
			}
		}
	}


	private function _addImagesBlock(SimpleXMLElement $productXml, $p_productsId)
	{
		$products_id = (int)$p_productsId;
		$sql = "SELECT pi.*, ia.* FROM " . TABLE_PRODUCTS_IMAGES . " pi LEFT JOIN `gm_prd_img_alt` ia USING (image_id) WHERE pi.products_id = " . $products_id . " ORDER BY image_nr";
		$result = xtc_db_query($sql);

		if(xtc_db_num_rows($result) > 0)
		{
			$additionalImages = $productXml->addChild('additional_images');
			$imageId = -1;

			while($row = xtc_db_fetch_array($result))
			{
				if($imageId != $row['image_id'])
				{
					$image = $additionalImages->addChild('image');

					$this->_addNode($row, 'image_id', 'image_id', $image);
					$this->_addNode($row, 'image_name', 'filename', $image);
					$this->_addNode($row, 'image_name', 'image_url', $image);
					$this->_addNode($row, 'image_nr', 'sort_order', $image);
					$this->_addNode($row, 'gm_show_image', 'visible', $image);
					$imageId = $row['image_id'];
				}

				$this->_addNode($row, 'gm_alt_text', 'image_alt_text', $image);
			}
		}
	}
	

	/**
	 * Add an XSelling node to the response XML. 
	 * 
	 * @param SimpleXMLElement $productXml
	 * @param numeric $p_productsId
	 */
	protected function _addXsellingNode(SimpleXMLElement $productXml, $p_productsId)
	{
		$productsId = (int) $p_productsId;
		$query = "SELECT xsell_id, sort_order FROM " . TABLE_PRODUCTS_XSELL . " WHERE products_id = " . $productsId . " ORDER BY sort_order";
		$results = xtc_db_query($query);
		
		if(xtc_db_num_rows($results) > 0)
		{
			$xsellingNode = $productXml->addChild('cross_selling_products');
			
			while($row = xtc_db_fetch_array($results))
			{
				$xsellingProductNode = $xsellingNode->addChild('cross_selling_product');
				$this->_addNode($row, 'xsell_id', 'product_id', $xsellingProductNode);
				$this->_addNode($row, 'sort_order', 'sort_order', $xsellingProductNode);
			}
		}
	}


	/**
	 * Validate tax in a product XML node object. 
	 * 
	 * @param SimpleXMLElement $productXml Product node XML object to be validated.
	 * @param array $response (ByRef) Contains the response data. 
	 *
	 * @return bool Returns the validation result. 
	 */
	protected function _validateTax(SimpleXMLElement $productXml, array &$response)
	{
		$productsId = (string)$productXml->product_id;
		$isMatched = false;
		
		if(isset($productXml->tax_class_id) && !in_array((int)$productXml->tax_class_id, array_keys($this->taxArray)))
		{
			$response[] = array(
									'external_' . $this->singularName . '_id' => '',
									$this->singularName . '_id' => $productsId,
									'success' => '0',
									'errormessage' => 'given tax_class_id does not exist',
									'action_performed' => 'error'
								);
			return false;
		}
		
		if(isset($productXml->tax_rate))
		{
			foreach($this->taxArray as $t_tax_class)
			{
				if((string)$productXml->tax_rate == $t_tax_class['tax_rate'])
				{
					if(isset($productXml->tax_class_id) && (int)$productXml->tax_class_id != $t_tax_class['tax_class_id'])
					{
						$response[] = array(
									'external_' . $this->singularName . '_id' => '',
									$this->singularName . '_id' => $productsId,
									'success' => '0',
									'errormessage' => 'tax_class_id and tax_rate are in conflict',
									'action_performed' => 'error'
								);
						return false;
					}
					
					$isMatched = true;
					$productXml->tax_class_id = $t_tax_class['tax_class_id'];
				}
			}
			if(!$isMatched)
			{
				$response[] = array(
									'external_' . $this->singularName . '_id' => '',
									$this->singularName . '_id' => $productsId,
									'success' => '0',
									'errormessage' => 'no matching tax_class_id for given tax_rate found',
									'action_performed' => 'error'
								);
				return false;
			}
			unset($productXml->tax_rate);
		}
		
		return true;
	}
}