<?php
/* --------------------------------------------------------------
  GxmlProperties.inc.php 2016-09-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlProperties
 *
 * Handles the API requests that concern the properties section of the shop system.
 * 
 * Supported API Functions:
 * 		- "upload_properties"
 * 		- "download_properties"
 *
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlProperties extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->_setPluralName('properties');
		$this->_setSingularName('property');
		$this->_setupMapperArray();
		$this->_setupTableMapperArray();
		$this->_loadLanguages();
	}

	/**
	 * Download Properties API Method
	 *
	 * This method handles the "download_properties" API method request. The
	 * routing of this method is inside the "XMLConnectAjaxHandler.inc.php".
	 *
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client.
	 *
	 * @return SimpleXMLElement Returns an XML response object.
	 */
	public function downloadProperties(SimpleXMLElement $requestXml)
	{
		try
		{
			$parameters = $this->_generateSqlStrings($requestXml->parameters, array('limit', 'where'));
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_addPropertiesNode($parameters);
			return $this->responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}


	/**
	 * Upload Properties API Method
	 *
	 * This method handles the "upload_properties" API requests. The routing is done inside
	 * the "XMLConnectAjaxHandler.inc.php" file.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client.
	 *
	 * @return SimpleXMLElement Returns the response XML object.
	 * @throws InvalidArgumentException When the request XML is invalid.
	 */
	public function uploadProperties($requestXml)
	{
		try
		{
			$responseData = array(); // response data array, will be serialized in the end
			$propValuesNodes = array(); // property values response data array

			// Validate the request XML for property data.  
			if(!property_exists($requestXml->parameters, 'properties'))
			{
				throw new InvalidArgumentException('Request XML is invalid: Missing parameters->properties element.');
			}

			foreach($requestXml->parameters->properties->children() as $propertyXml)
			{
				$this->_upload($propertyXml, $responseData); // Upload property ($property => SimpleXMLElement node).

				if(isset($propertyXml->property_values))
				{
					$gxmlPropertyValues = MainFactory::create_object('GxmlPropertyValues');
					$propValuesNodes[]  = $gxmlPropertyValues->uploadPropertyValues($propertyXml,
																					(int)$propertyXml->property_id);
				}
			}

			$responseXml = $this->generateResponseXml($responseData);

			// Add the property values to the response XML object. 
			foreach($propValuesNodes as $node)
			{
				$propValuesChildren = $node->property_values->children();
				$propValuesXmlNode  = $responseXml->addChild('property_values');
				foreach($propValuesChildren as $child)
				{
					$this->addXmlChild($propValuesXmlNode, $child);
				}
			}

			return $responseXml;
		}
		catch(Exception $ex)
		{
			$this->handleApiException($ex);
		}
	}

	
	/**
	 * Add a node to the xml response.
	 *
	 * @param array $data Contains the data to be included into the XML.
	 * @param string $tableColumn The table column will point the required value inside the $data array.
	 * @param string $nodeName This is the node name to be created.
	 * @param SimpleXMLElement $xml The XML object to be modified.
	 */
	public function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{
			case 'name':
			case 'admin_name':
				if($this->nodeExists($xml, $nodeName, array('language_id', $data['language_id'])) === false)
				{
					$xml->addChild($nodeName);
					$xml->{$nodeName}[count($xml->{$nodeName}) - 1] = $this->wrapValue($data[$tableColumn]);
					$coo_child = $xml->{$nodeName}[count($xml->{$nodeName}) - 1];
					$coo_child['language_id'] = $data['language_id'];
					
					$languageIso = ''; 
					if(isset($this->languageArray[$data['language_id']]))
					{
						$languageIso = $this->languageArray[$data['language_id']]['iso']; 
					}
					
					$coo_child['language_iso'] = $languageIso;
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
	 * Setup string mapper array for mapping the XML names to the 
	 * actual DB names.
	 */
	protected function _setupMapperArray()
	{
		$this->mapperArray = array();
		
		$this->mapperArray['property_id'] = 'properties_id';
		$this->mapperArray['sort_order'] = 'sort_order';
		
		$this->mapperArray['name'] = 'properties_name';
		$this->mapperArray['admin_name'] = 'properties_admin_name';
	}


	/**
	 * Setup string mapper array for mapping the XML names to the
	 * actual DB table names.
	 */
	protected function _setupTableMapperArray()
	{
		$this->tableMapperArray = array();
		
		$this->tableMapperArray['property_id'] = 'properties';
		$this->tableMapperArray['external_property_id'] = 'properties';
		$this->tableMapperArray['sort_order'] = 'properties';
		
		$this->tableMapperArray['name'] = 'properties_description';
		$this->tableMapperArray['admin_name'] = 'properties_description';
                
		$this->languageDependentMapperArray = array('properties_description');
	}

	
	/**
	 * Add the properties node in the response XML object 
	 * 
	 * This method will add the properties node directly to the private responseXml 
	 * property. So this method will not return any results. 
	 *
	 * @param array $parameters (Optional) Includes the filtering parameters passed by the client
	 *	                        upon the request.
	 */
	protected function _addPropertiesNode(array $parameters = array())
	{
		$this->responseXml->addChild('properties');
		
		$whereClause      = $this->_generateWhereClause($parameters);
		$orderByClause    = ' ORDER BY properties.properties_id, properties_description.language_id ASC ';
		$limitation       = '';
		$propertyIdsArray = true;
		
		if(count($parameters) > 0)
		{
			$propertyIdsArray = array();
			
			$groupClause = 'GROUP BY properties.properties_id';
			$limitClause = $this->_generateLimitClause($parameters);
			
			$query = "SELECT properties.properties_id
							FROM properties 
							LEFT JOIN properties_description USING (properties_id)
							" . $whereClause . "
							" . $groupClause . "
							" . $orderByClause . "
							" . $limitClause;
			$results = xtc_db_query($query);
			while($row = xtc_db_fetch_array($results))
			{
				$propertyIdsArray[] = $row['properties_id'];
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
				
				if(!empty($propertyIdsArray))
				{					
					if($whereClause == '' )
					{
						$limitation = ' WHERE properties.properties_id IN (' . implode(',', $propertyIdsArray) . ') ';
					}
					else
					{
						$limitation .= ' AND properties.properties_id IN (' . implode(',', $propertyIdsArray) . ') ';
					}
				}
			}			
		}
		
		if(empty($propertyIdsArray) == false)
		{
			$query = "
					SELECT * 
					FROM properties 
					LEFT JOIN properties_description USING (properties_id)
					" . $whereClause . "
					" . $limitation . "
					" . $orderByClause;
			$results = xtc_db_query($query);

			$currRecordId = 0;
			while($row = xtc_db_fetch_array($results))
			{
				// check if new property
				if($currRecordId != $row['properties_id'])
				{
					$propertyChildNode = $this->responseXml->properties->addChild('property');
				}

				// add property data
				$this->add($row, $propertyChildNode);

				// check if new property
				if($currRecordId != $row['properties_id'])
				{
					$propertyValuesChildNode = $propertyChildNode->addChild('property_values');
					// add values
					$gxmlPropertyValues = MainFactory::create_object( 'GxmlPropertyValues' );
					$gxmlPropertyValues->addPropertyValuesNode( array( 'property_child' => &$propertyValuesChildNode, 'property_id' => $row['properties_id'] ) );
				}

				$currRecordId = $row['properties_id'];
			}
		}
	}

	
	/**
	 * Abstract Method: Delete Object 
	 * 
	 * This method implements the abstract declaration in the GxmlMaster class. It 
	 * will remove an object from the database. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the request information. 
	 *
	 * @return bool Returns the operation result. 
	 */
	protected function _deleteObject(SimpleXMLElement $requestXml)
	{
		if(!isset($requestXml->property_id) || empty($requestXml->property_id))
		{
			return false;
		}
		
		$propertyId	= (int)$requestXml->property_id;
		$result = true;
		
		$query = '
				DELETE 
					properties, properties_values, properties_values_description,
					products_properties_combis_values, products_properties_combis,
					properties_description, products_properties_admin_select, 
					products_properties_index
				
				FROM properties 
				LEFT JOIN properties_values ON properties_values.properties_id = properties.properties_id 
				LEFT JOIN properties_values_description ON properties_values_description.properties_values_id = properties_values.properties_values_id 
				LEFT JOIN products_properties_combis_values ON products_properties_combis_values.properties_values_id = properties_values.properties_values_id
				LEFT JOIN products_properties_combis ON products_properties_combis.products_properties_combis_id = products_properties_combis_values.products_properties_combis_id 
				LEFT JOIN properties_description ON properties_description.properties_id = properties.properties_id 
				LEFT JOIN products_properties_admin_select ON products_properties_admin_select.properties_id = properties.properties_id 
				LEFT JOIN products_properties_index ON products_properties_index.properties_id = properties.properties_id 
				
				WHERE properties.properties_id = "' . xtc_db_prepare_input($propertyId) . '"
				';
		
		$result &= $this->_performDbAction($query);
		
		return $result;
	}
}
