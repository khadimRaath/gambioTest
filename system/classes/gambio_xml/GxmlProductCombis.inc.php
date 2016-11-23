<?php
/* --------------------------------------------------------------
  GxmlProductCombis.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlProductCombis
 * 
 * Handles the product combinations requests made by the client API. This 
 * class is used by the GxmlProducts class.
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlProductCombis extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->_setPluralName('product_combis');
		$this->_setSingularName('product_combi');
		$this->_setupMapperArray();
		$this->_setupTableMapperArray();
		$this->_loadLanguages();
	}


	/**
	 * Upload Product Comibs Method 
	 * 
	 * This method will handle the upload_product_combis method request from 
	 * the client.
	 * 
	 * NOTE: This method is not available to the public API.
	 * 
	 * @param SimpleXMLElement $requestXml The request XML object provided by the client. 
	 * @param numeric $p_product_id Corresponding product record id. 
	 *
	 * @return SimpleXMLElement Returns the response for the operation.
	 */
	public function uploadProductCombis($requestXml, $p_product_id)
	{
		$responseData = array();
		
		foreach($requestXml->{$this->pluralName}->children() as $combiXml)
		{
			$combiXml->addChild('product_id', $p_product_id);
			$attributes = $combiXml->attributes();
			$isDeleted = false;
			
			foreach ($attributes as $key=>$value)
			{
				if ($key == 'action' && $value == 'delete')
				{
					$isDeleted = true;
					break;
				}
			}
			
			if ($isDeleted == true)
			{
				$this->_deleteObject($combiXml);
				continue;
			}
			
			$this->_upload($combiXml, $responseData);
		}
		$responseXml = $this->generateResponseXml($responseData);
		
		return $responseXml;
	}


	/**
	 * Adds a products combi node to the response XML object. 
	 * 
	 * NOTE: This method is not used.
	 * 
	 * @param array $parameters Parameters provided by the client in order to 
	 *                          filter the results. 
	 */
	public function addProductsCombisNode(array $parameters = array())
	{
		$productId = $parameters['product_id'];
		$productChild = $parameters['product_child'];
		
		$query = '
			SELECT * 
			FROM products_properties_combis 
			WHERE products_id = ' . $productId;
		$results = xtc_db_query($query);
				
		$valuesQuery = '
			SELECT pv.*, prv.properties_id 
			FROM products_properties_combis_values pv, 
				products_properties_combis p, 
				properties_values prv 
			WHERE p.products_properties_combis_id = pv.products_properties_combis_id 
				AND pv.properties_values_id = prv.properties_values_id 
				AND p.products_id = ' . $productId;
		$valuesResults = xtc_db_query($valuesQuery);
		
		if(xtc_db_num_rows($results) > 0)
		{
			$node = $productChild->addChild($this->pluralName);
			
			$valuesArray = array();
			
			while($row = xtc_db_fetch_array($valuesResults))
			{
				if (!array_key_exists($row['products_properties_combis_id'], $valuesArray))
				{
					$valuesArray[$row['products_properties_combis_id']] = array();
				}
				
				$valuesArray[$row['products_properties_combis_id']][] = array(
						'properties_id' => $row['properties_id'], 
						'properties_values_id' => $row['properties_values_id']
				);
			}
			
			while($row = xtc_db_fetch_array($results))
			{
				$combiValuesArray = array();
				
				if (array_key_exists($row['products_properties_combis_id'], $valuesArray))
				{
					$combiValuesArray = $valuesArray[$row['products_properties_combis_id']];
				}
				
				$combiChildNode = $node->addChild($this->singularName);
				$this->add($row, $combiChildNode);
				
				$combiValuesChildNode = $combiChildNode->addChild('property_values');
				
				foreach ($combiValuesArray as $value)
				{
					$combiValueChildNode = $combiValuesChildNode->addChild('property_value');
					$this->_addNode($value, 'properties_id', 'property_id', $combiValueChildNode);
					$this->_addNode($value, 'properties_values_id', 'property_value_id', $combiValueChildNode);
				}
			}			
		}
	}


	/**
	 * Add a node to the response XML. 
	 * 
	 * @param array $data Contains the data to be included to the XML object. 
	 * @param string $tableColumn The table column will point the $data value. 
	 * @param string $nodeName Node name to be added. 
	 * @param SimpleXMLElement $xml Parent xml to be edited. 
	 */
	protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{			
			case 'property_values/property_value/property_id':
			case 'property_values/property_value/property_value_id':
				$xml->{substr($nodeName, strrpos($nodeName, '/') + 1)} = $data[$tableColumn];
				break;
			
			case 'model':
			case 'ean':
			case 'image':
				if($this->nodeExists($xml, $nodeName) === false)
				{
					$xml->{$nodeName} = $this->wrapValue($data[$tableColumn]);
				}
				break;
			case 'property_values':
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
	 * Delete object from database.
	 *
	 * This method implements the abstact declaration at GxmlMaster class.
	 *
	 * @param SimpleXMLElement $requestXml Request XML object containing the information of
	 *                                     the object to be deleted.
	 *
	 * @return bool Returns the operation result.
	 */
	protected function _deleteObject(SimpleXMLElement $requestXml)
	{
		$combiId = (string) $requestXml->product_combi_id;

		if(!empty($combiId))
		{
			$combisAdminControl = MainFactory::create_object('PropertiesCombisAdminControl');
			$combisAdminControl->delete_properties_combis(array($combiId));

			$result = true;
		}
		else
		{
			$result = false;
		}

		return $result;
	}
	
	
	/**
	 * Setup mapper array that will map the API XML names to the actual
	 * DB names.
	 */
	protected function _setupMapperArray()
	{
		$this->mapperArray = array();
		$this->mapperArray['product_combi_id'] = 'products_properties_combis_id';
		$this->mapperArray['product_id'] = 'products_id';
		$this->mapperArray['sort_order'] = 'sort_order';
		$this->mapperArray['model'] = 'combi_model';
		$this->mapperArray['ean'] = 'combi_ean';
		$this->mapperArray['quantity'] = 'combi_quantity';
		$this->mapperArray['shipping_time_id'] = 'combi_shipping_status_id';
		$this->mapperArray['weight'] = 'combi_weight';
		$this->mapperArray['price'] = 'combi_price';
		$this->mapperArray['price_type'] = 'combi_price_type';
		$this->mapperArray['image'] = 'combi_image';
		$this->mapperArray['base_price_unit_id'] = 'products_vpe_id';
		$this->mapperArray['base_price_unit_value'] = 'vpe_value';
		$this->mapperArray['property_values'] = '';
	}

	
	/**
	 * Setup mapper array that will map the API XML names to the actual
	 * DB table names.
	 */
	protected function _setupTableMapperArray()
	{
		$this->tableMapperArray = array();

		$this->tableMapperArray['product_combi_id'] = 'products_properties_combis';
		$this->tableMapperArray['product_id'] = 'products_properties_combis';
		$this->tableMapperArray['sort_order'] = 'products_properties_combis';
		$this->tableMapperArray['model'] = 'products_properties_combis';
		$this->tableMapperArray['ean'] = 'products_properties_combis';
		$this->tableMapperArray['quantity'] = 'products_properties_combis';
		$this->tableMapperArray['shipping_time_id'] = 'products_properties_combis';
		$this->tableMapperArray['weight'] = 'products_properties_combis';
		$this->tableMapperArray['price'] = 'products_properties_combis';
		$this->tableMapperArray['price_type'] = 'products_properties_combis';
		$this->tableMapperArray['image'] = 'products_properties_combis';
		$this->tableMapperArray['base_price_unit_id'] = 'products_properties_combis';
		$this->tableMapperArray['base_price_unit_value'] = 'products_properties_combis';
		$this->tableMapperArray['property_values'] = 'products_properties_combis_values';

		$this->languageDependentMapperArray = array();
	}
}