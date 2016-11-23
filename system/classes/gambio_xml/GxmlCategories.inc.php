<?php
/* --------------------------------------------------------------
  GxmlCategories.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlCategories
 * 
 * Handles the functionality of the Gambio API operations that concern the 
 * categories session of the shop.
 * 
 * Supported API Methods: 
 * 		- "upload_categories"
 * 		- "download_categories"
 * 
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlCategories extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->_setPluralName('categories');
		$this->_setSingularName('category');
		$this->_setupMapperArray();
		$this->_setupTableMapperArray();
	}
	
	
	/**
	 * Handles the download categories client call.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request data.
	 *
	 * @return SimpleXMLElement Returns the XML object, containing the response data.
	 */
	public function downloadCategories(SimpleXMLElement $requestXml) // method name cannot be refactored.
	{
		try
		{
			$parameters = $this->_generateSqlStrings($requestXml->parameters, array('limit', 'where'));
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_includeNode(array('languages'));
			$this->_addCategoriesNode($parameters);

			return $this->responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}

	
	/**
	 * Handles the upload categories API function.
	 *
	 * @param SimpleXMLElement $requestXml Contains information sent by the client.
	 *
	 * @return SimpleXMLElement Returns the XML object with the response.
	 */
	public function uploadCategories(SimpleXMLElement $requestXml)
	{
		try
		{
			$responseData = array(); // This array will be serialized to XML.

			$categoriesToDelete = $requestXml->xpath("//category[@action='delete']");
			$sortedCategories = $requestXml->xpath("//category[(parent_id) and not (@action='delete')]");
			$categoriesWithoutParent = $requestXml->xpath("//category[not (parent_id) and (external_parent_id) and not (@action='delete')]");

			$this->_sortCategoriesByUnknownParents($categoriesWithoutParent, $sortedCategories);
			$this->deleteXmlChildrenByTagName($requestXml->parameters->categories, 'category');

			foreach ($sortedCategories as $categoryXml)
			{
				$this->addXmlChild($requestXml->parameters->categories, $categoryXml);
			}

			foreach ($categoriesToDelete as $categoryXml)
			{
				$this->addXmlChild($requestXml->parameters->categories, $categoryXml);
			}

			// Validate the request XML for property data.  
			if(!property_exists($requestXml->parameters, 'categories'))
			{
				throw new InvalidArgumentException('Request XML is invalid: Missing parameters->categories element.');
			}

			$lastCategoryId = false;

			foreach ($requestXml->parameters->categories->children() as $categoryXml)
			{
				if($lastCategoryId !== false && !isset($categoryXml->parent_id))
				{
					$categoryXml->parent_id = $lastCategoryId;
				}
				$lastCategoryId = $this->_upload($categoryXml, $responseData);
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
	 * Add a new node to an XML object.
	 *
	 * This method is an implementation of the abstract method declared at
	 * GxmlMaster class.
	 *
	 * @param array $data
	 * @param string $tableColumn
	 * @param string $nodeName
	 * @param SimpleXMLElement $xml
	 */
	protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{
			case 'name':
			case 'heading_title':
			case 'description':
			case 'meta_title':
			case 'meta_description':
			case 'meta_keywords':
			case 'image_alt_text':
			case 'url_keywords':
				// Check that the current category language exists in the database.
				$languageIso = (isset($this->languageArray[$data['language_id']]))  
						? $this->languageArray[$data['language_id']]['iso']
						: ''; 
				
				$attributes = array(
					'language_id' => $data['language_id'],
					'language_iso' => $languageIso
				);

				$nodeExists = $this->nodeExists($xml, $nodeName, $attributes, 'or');
				if ($nodeExists === false)
				{
					$xml->addChild($nodeName);
					$xml->{$nodeName}[count($xml->{$nodeName}) - 1] = $this->wrapValue($data[$tableColumn]);
					$node = $xml->{$nodeName}[count($xml->{$nodeName}) - 1];
					$node['language_id'] = $data['language_id'];
					$node['language_iso'] = $languageIso;

					if($nodeName == 'description' || $nodeName == 'meta_keywords')
					{
						$node['type'] = 2;
					}
				}

				break;

			case 'customer_group_permission':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					foreach($data AS $t_column => $t_value)
					{
						if(strpos($t_column, 'group_permission_') !== false)
						{
							$xml->addChild($nodeName);
							$xml->{$nodeName}[count($xml->{$nodeName}) - 1] = $t_value;
							$node = $xml->{$nodeName}[count($xml->{$nodeName}) - 1];
							$node['customer_group_id'] = str_replace('group_permission_', '', $t_column);
						}
					}
				}

				break;

			case 'icon':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					$xml->{$nodeName} = $this->wrapValue($data[$tableColumn]);
					$xml->{$nodeName}['width'] = $data['categories_icon_w'];
					$xml->{$nodeName}['height'] = $data['categories_icon_h'];
				}

				break;

			case 'icon_url':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					$t_category_icon_url = '';
					if(empty($data[$tableColumn]) === false)
					{
						$t_category_icon_url = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'categories/icons/' . $data[$tableColumn];
					}
					$xml->{$nodeName} = $this->wrapValue($t_category_icon_url);
				}

				break;

			case 'image_url':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					$t_category_image_url = '';
					if(empty($data[$tableColumn]) === false)
					{
						$t_category_image_url = HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'categories/' . $data[$tableColumn];
					}

					$xml->{$nodeName} = $this->wrapValue($t_category_image_url);
				}

				break;

			case 'category_icon':
			case 'category_icon_url':
			case 'image':
			case 'listing_template':
			case 'template':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					$xml->{$nodeName} = $this->wrapValue($data[$tableColumn]);
				}
				break;

			default:
				if($this->nodeExists($xml, $nodeName) === false)
				{
					$xml->{$nodeName} = $data[$tableColumn];
				}

				break;
		}
	}

	
	/**
	 * Setup Mapper Array
	 *
	 * The mapper array will convert some API names to their relatives in
	 * database. This method is called upon the instantiation of the class. 
	 */
	protected function _setupMapperArray()
	{
		$this->mapperArray = array();
		
		$this->mapperArray['category_id'] = 'categories_id';
		$this->mapperArray['parent_id'] = 'parent_id';
		$this->mapperArray['status'] = 'categories_status';
		$this->mapperArray['image'] = 'categories_image';
		$this->mapperArray['image_url'] = 'categories_image';
		$this->mapperArray['template'] = 'categories_template';
		$this->mapperArray['listing_template'] = 'listing_template';
		$this->mapperArray['sort_order'] = 'sort_order';
		$this->mapperArray['product_sorting'] = 'products_sorting';
		$this->mapperArray['product_sorting2'] = 'products_sorting2';
		$this->mapperArray['date_added'] = 'date_added';
		$this->mapperArray['last_modified'] = 'last_modified';
		$this->mapperArray['category_icon'] = 'categories_icon';
		$this->mapperArray['category_icon_url'] = 'categories_icon';
		$this->mapperArray['priority'] = 'gm_priority';
		$this->mapperArray['change_frequency'] = 'gm_changefreq';
		$this->mapperArray['show_attributes'] = 'gm_show_attributes';
		$this->mapperArray['show_graduated_prices'] = 'gm_show_graduated_prices';
		$this->mapperArray['show_quantity'] = 'gm_show_qty';
		$this->mapperArray['sitemap_entry'] = 'gm_sitemap_entry';
		$this->mapperArray['show_quantity_info'] = 'gm_show_qty_info';
		$this->mapperArray['show_sub_categories'] = 'show_sub_categories';
		$this->mapperArray['show_sub_categories_images'] = 'show_sub_categories_images';
		$this->mapperArray['show_sub_categories_names'] = 'show_sub_categories_names';
		$this->mapperArray['show_sub_products'] = 'show_sub_products';
		$this->mapperArray['view_mode_tiled'] = 'show_sub_products';
		
		$this->mapperArray['customer_group_permission'] = 'group_permission_';
				
		$this->mapperArray['name'] = 'categories_name';
		$this->mapperArray['heading_title'] = 'categories_heading_title';
		$this->mapperArray['description'] = 'categories_description';
		$this->mapperArray['meta_title'] = 'categories_meta_title';
		$this->mapperArray['meta_description'] = 'categories_meta_description';
		$this->mapperArray['meta_keywords'] = 'categories_meta_keywords';
		$this->mapperArray['image_alt_text'] = 'gm_alt_text';
		$this->mapperArray['url_keywords'] = 'gm_url_keywords';
	}


	/**
	 * Setup table mapper array.
	 *
	 * Same as the _setupMapperArray() method this will work with the 
	 * database tables. It is called only once upon the instantiation 
	 * of the class.
	 */
	protected function _setupTableMapperArray()
	{
		$this->tableMapperArray = array();
		
		$this->tableMapperArray['category_id'] = TABLE_CATEGORIES;
		$this->tableMapperArray['parent_id'] = TABLE_CATEGORIES;
		$this->tableMapperArray['status'] = TABLE_CATEGORIES;
		$this->tableMapperArray['image'] = TABLE_CATEGORIES;
		$this->tableMapperArray['image_url'] = TABLE_CATEGORIES;
		$this->tableMapperArray['template'] = TABLE_CATEGORIES;
		$this->tableMapperArray['listing_template'] = TABLE_CATEGORIES;
		$this->tableMapperArray['sort_order'] = TABLE_CATEGORIES;
		$this->tableMapperArray['product_sorting'] = TABLE_CATEGORIES;
		$this->tableMapperArray['product_sorting2'] = TABLE_CATEGORIES;
		$this->tableMapperArray['date_added'] = TABLE_CATEGORIES;
		$this->tableMapperArray['last_modified'] = TABLE_CATEGORIES;
		$this->tableMapperArray['category_icon'] = TABLE_CATEGORIES;
		$this->tableMapperArray['category_icon_url'] = TABLE_CATEGORIES;
		$this->tableMapperArray['priority'] = TABLE_CATEGORIES;
		$this->tableMapperArray['change_frequency'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_attributes'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_graduated_prices'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_quantity'] = TABLE_CATEGORIES;
		$this->tableMapperArray['sitemap_entry'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_quantity_info'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_sub_categories'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_sub_categories_images'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_sub_categories_names'] = TABLE_CATEGORIES;
		$this->tableMapperArray['show_sub_products'] = TABLE_CATEGORIES;
		$this->tableMapperArray['view_mode_tiled'] = TABLE_CATEGORIES;
		
		$this->tableMapperArray['customer_group_permission'] = TABLE_CATEGORIES;
				
		$this->tableMapperArray['name'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['heading_title'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['description'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['meta_title'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['meta_description'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['meta_keywords'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['image_alt_text'] = TABLE_CATEGORIES_DESCRIPTION;
		$this->tableMapperArray['url_keywords'] = TABLE_CATEGORIES_DESCRIPTION;
                
		$this->languageDependentMapperArray = array(TABLE_CATEGORIES_DESCRIPTION);
	}

	
	/**
	 * Sort categories by unknown parents.
	 *
	 * @param array $categoryWithoutParent
	 * @param array $sortedCategory (ByRef)
	 */
	protected function _sortCategoriesByUnknownParents(array $categoryWithoutParent, array &$sortedCategory)
	{
		if(empty($categoryWithoutParent))
		{
			return;
		}

		$filteredCategories = $categoryWithoutParent;

		foreach($categoryWithoutParent as $pos=>$category)
		{
			foreach($sortedCategory as $t_possible_parent)
			{
				if(isset($category->external_parent_id) &&
				   isset($t_possible_parent->external_category_id) &&
				   (string)$category->external_parent_id == (string)$t_possible_parent->external_category_id)
				{
					$sortedCategory[] = $category;
					unset($filteredCategories[$pos]);
				}
			}
		}
		$filteredCategories = array_values($filteredCategories);
		$this->_sortCategoriesByUnknownParents($filteredCategories, $sortedCategory);

	}
	

	/**
	 * Adds the categories node to the response XML object. 
	 *
	 * This method won't return a value but it adjuts the private response XML object
	 * of the class instance. 
	 * 
	 * @param array $parameters (Optional) Parameters array containing the parameters sent
	 *                          from the client.
	 */
	protected function _addCategoriesNode(array $parameters = array())
	{
		$this->responseXml->addChild('categories');
		
		$whereClause = $this->_generateWhereClause($parameters, true);
		$limitation = '';
		$categoryIds = true;
		
		if(count($parameters) > 0)
		{
			$categoryIds = array();
			
			$limitClause = ' GROUP BY ' . TABLE_CATEGORIES . '.categories_id' . $this->_generateLimitClause($parameters);
			
			$query = "SELECT 
							" . TABLE_CATEGORIES . ".categories_id
						FROM 
							" . TABLE_CATEGORIES . ",
							" . TABLE_CATEGORIES_DESCRIPTION . "
						WHERE
							" . TABLE_CATEGORIES . ".categories_id = " . TABLE_CATEGORIES_DESCRIPTION . ".categories_id
							" . $whereClause . "
						" . $limitClause;
			$results = xtc_db_query($query);
			while($t_result_array = xtc_db_fetch_array($results))
			{
				$categoryIds[] = $t_result_array['categories_id'];
			}
			
			$limit = 0;
			$offset = 0;
			if(isset($parameters['offset']) && !empty($parameters['offset']))
			{
				$offset = (int)$parameters['offset'];
			}

			if(isset($parameters['limit']) && !empty($parameters['limit']))
			{
				$limit = (int)$parameters['limit'];
				
				if(!empty($categoryIds))
				{					
					$limitation .= ' AND ' . TABLE_CATEGORIES . '.categories_id IN (' . implode(',', $categoryIds) . ') ';
				}
			}			
		}
		
		if(empty($categoryIds) == false)
		{
			$query = "SELECT 
							* 
						FROM 
							" . TABLE_CATEGORIES . ",
							" . TABLE_CATEGORIES_DESCRIPTION . "
						WHERE
							" . TABLE_CATEGORIES . ".categories_id = " . TABLE_CATEGORIES_DESCRIPTION . ".categories_id
							" . $whereClause . "
							" . $limitation;
			$results = xtc_db_query($query);

			$currRecordId = 0;
			while($row = xtc_db_fetch_array($results))
			{
				if($currRecordId != $row['categories_id'])
				{
					$node = $this->responseXml->categories->addChild('category');
				}

				$this->add($row, $node);

				$currRecordId = $row['categories_id'];
			}
		}
	}


	/**
	 * Remove a categories object from the database.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request XML object.
	 *
	 * @return bool Returns the operation result.
	 */
	protected function _deleteObject(SimpleXMLElement $requestXml)
	{
		$categoryData = $this->_parseXmlData($requestXml);
		$categoryData = $this->convertToLatin1($categoryData);
		$recordId = (string) $requestXml->category_id;
		$deletedRecordIds = array();
		$operationResult = true;
		
		// Select all "categories_ids".
		if(empty($recordId))
		{
			$query = '
					SELECT categories_id
					FROM categories
					WHERE ';
			foreach($categoryData['categories'] as $key=>$value)
			{
				$query .= xtc_db_prepare_input($key) . ' LIKE \'' . xtc_db_prepare_input($value) . '\' AND ';
			}
			$query = substr($query, 0, strlen($query) - 5);
			
			$results = xtc_db_query($query);
			if(xtc_db_num_rows($results));
			{
				while($row = xtc_db_fetch_array($results))
				{
					$deletedRecordIds[] = $row['categories_id'];
				}
			}
		}
		else
		{
			$deletedRecordIds[] = $recordId;
		}
		
		// Delete from categories and categories_description.
		if (!empty($deletedRecordIds))
		{
			$query = '
					DELETE c.*, cd.* 
					FROM 
						categories c, categories_description cd
					WHERE 
						c.categories_id IN (' . implode(', ', $deletedRecordIds) . ') AND 
						c.categories_id = cd.categories_id
					';
			$operationResult &= $this->_performDbAction($query);
			
			$query = '
					DELETE FROM categories_filter
					WHERE 
						categories_id IN (' . implode(', ', $deletedRecordIds) . ')
					';
			$operationResult &= $this->_performDbAction($query); 
		}
		
		// select all affected sub-categories
		$subcategories = array();
		if (!empty($deletedRecordIds))
		{
			$query = '
					SELECT categories_id
					FROM categories
					WHERE parent_id IN (' . implode(', ', $deletedRecordIds) . ')';
			$results = $this->_performDbAction($query);
			
			if(xtc_db_num_rows($results))
			{
				while($row = xtc_db_fetch_array($results))
				{
					$subcategories[] = $row['categories_id'];
				}
			}
		}
		
		if (!empty($deletedRecordIds) && !empty($subcategories))
		{
			// set parent_id from all affected sub-categories to '0'
			$query = '
					UPDATE categories
					SET parent_id = 0
					WHERE categories_id IN (' . implode(', ', $subcategories) . ')
					';
			$operationResult &= $this->_performDbAction($query);
		}
		
		// set parent_id from all products deleted categories and their sub-categories to '0'
		if (!empty($deletedRecordIds))
		{
			$query = '
					UPDATE products_to_categories
					SET categories_id = 0
					WHERE 
						categories_id IN (' . implode(', ', $deletedRecordIds) . ')
					';
			$operationResult &= $this->_performDbAction($query);
		}
		
		return $operationResult;
	}
}