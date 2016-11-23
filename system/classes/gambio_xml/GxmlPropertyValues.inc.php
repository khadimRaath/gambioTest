<?php
/* --------------------------------------------------------------
  GxmlPropertyValues.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlPropertyValues
 * 
 * Used by the GxmlProperties class for implementing the properties section
 * of the API. 
 * 
 * Refactored by A.Tselegidis
 *
 * @category System
 * @package GambioAPI
 * @version 1.0 
 */
class GxmlPropertyValues extends GxmlMaster
{
	/**
	 * Class Constuctor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->_setPluralName('property_values');
		$this->_setSingularName('property_value');
		$this->_setupMapperArray();
		$this->_setupTableMapperArray();
		$this->_loadLanguages();
	}


	/**
	 * Add the property values node into the response XML object. 
	 * 
	 * @param array $parameters Contains the parameters sent by the client upon request. 
	 */
	public function addPropertyValuesNode(array $parameters = array())
	{
		$t_property_child = $parameters['property_child'];
		$t_property_id = $parameters['property_id'];
				
		$t_properties_values_sql = 'SELECT * FROM properties_values LEFT JOIN properties_values_description USING (properties_values_id)';
		$t_properties_values_result = xtc_db_query($t_properties_values_sql);
			
		$t_last_property_value_id = 0;
		while($t_properties_values_row = xtc_db_fetch_array($t_properties_values_result))
		{
			if( $t_property_id == $t_properties_values_row['properties_id'] )
			{
				if($t_last_property_value_id != $t_properties_values_row['properties_values_id'])
				{
					$coo_property_value_child = $t_property_child->addChild('property_value');
				}

				$this->add($t_properties_values_row, $coo_property_value_child);
				$t_last_property_value_id = $t_properties_values_row['properties_values_id'];
			}
		}
	}


	/**
	 * Upload property values. 
	 * 
	 * This method is used in the GxmlProperites class to upload the property values to 
	 * the shop system. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client. 
	 * @param numeric $p_propertyId The property ID that values concern. 
	 *
	 * @return SimpleXMLElement Returns the response XML object node. 
	 */
	public function uploadPropertyValues($requestXml, $p_propertyId)
	{
		$responseData = array();
		$countPropertyValuesChild = 0;
		
		foreach($requestXml->property_values->children() as $propertyValueXml)
		{
			$propertyValueXml->addChild('property_id', $p_propertyId);
			$countPropertyValuesChild++;
			$this->_upload($propertyValueXml, $responseData);
		}
		$responseXml = $this->generateResponseXml($responseData);
		
		return $responseXml;
	}


	/**
	 * Add a node to the given XML object. 
	 * 
	 * @param array $data Contains the data to be used as the value. 
	 * @param string $tableColumn This will point the value inside the $data array. 
	 * @param string $nodeName The node name to be created. 
	 * @param SimpleXMLElement $xml XML object to be modified. 
	 */
	public function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		switch($nodeName)
		{
			case 'image':
			case 'name':
				if($this->nodeExists($xml, $nodeName, array('language_id' => $data['language_id'])) === false)
				{
					$xml->addChild($nodeName);
					$xml->{$nodeName}[count($xml->{$nodeName}) - 1] = $this->wrapValue($data[$tableColumn]);
					$coo_child = $xml->{$nodeName}[count($xml->{$nodeName}) - 1];
					$coo_child['language_id'] = $data['language_id'];
					$coo_child['language_iso'] = $this->languageArray[$data['language_id']]['iso'];
				}
				break;
			
			case 'model_fragment':
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
	 * Abstract Method: Delete Object 
	 * 
	 * This method implements the abstract method declared in the GxmlMaster class. It will
	 * remove a property object from the database. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the request infromation sent by the client. 
	 *
	 * @return bool Returns the operation result. 
	 */
	protected function _deleteObject(SimpleXMLElement $requestXml)
	{
		if(!isset($requestXml->property_value_id) || empty($requestXml->property_value_id))
		{
			return false;
		}
		
		$propertiesValuesId	= (int) $requestXml->property_value_id;
		$result = true;
		
		$query = '
				DELETE
					pv.*, pvd.*, ppcv.*, ppc.*, ppas.*, ppi.*
				FROM
					properties_values pv,
					properties_values_description pvd,
					products_properties_combis_values ppcv,
					products_properties_combis ppc,
					properties p,
					properties_description pd,
					products_properties_admin_select ppas,
					products_properties_index ppi
				WHERE
					pv.properties_values_id = ' . $propertiesValuesId . ' AND
					pv.properties_values_id = pvd.properties_values_id AND
					pvd.properties_values_id = ppcv.properties_values_id AND
					ppcv.products_properties_combis_id = ppc.products_properties_combis_id AND
					pv.properties_values_id = ppas.properties_values_id AND
					ppi.products_properties_combis_id = ppcv.products_properties_combis_id
				';
		
		$result &= $this->_performDbAction($query);
		
		return $result;
	}

	
	/**
	 * Setup the mapper array that will help in pointing the original
	 * DB names.
	 */
	protected function _setupMapperArray()
	{
		$this->mapperArray = array();
		$this->mapperArray['property_value_id'] = 'properties_values_id';
		$this->mapperArray['property_id'] = 'properties_id';
		$this->mapperArray['sort_order'] = 'sort_order';
		$this->mapperArray['model_fragment'] = 'value_model';
		$this->mapperArray['price'] = 'value_price';
		$this->mapperArray['name'] = 'values_name';
	}


	/**
	 * Setup the mapper array that will help in pointing the original
	 * DB table names.
	 */
	protected function _setupTableMapperArray()
	{
		$this->tableMapperArray = array();

		$this->tableMapperArray['property_value_id'] = 'properties_values';
		$this->tableMapperArray['property_id'] = 'properties_values';
		$this->tableMapperArray['sort_order'] = 'properties_values';
		$this->tableMapperArray['model_fragment'] = 'properties_values';
		$this->tableMapperArray['price'] = 'properties_values';
		$this->tableMapperArray['name'] = 'properties_values_description';

		$this->languageDependentMapperArray = array('properties_values_description');
	}
}