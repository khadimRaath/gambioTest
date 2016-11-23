<?php
/* --------------------------------------------------------------
  GxmlTaxClasses.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlTaxClasses
 * 
 * Handles the tax classes functions supported by the Gambio API. 
 * 
 * Supported API Functions:
 * 		- "download_tax_classes"
 * 
 * Refactored by A.Tselegidis 
 * 
 * @category System
 * @package GambioAPI 
 * @version 1.0 
 */
class GxmlTaxClasses extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
	}


	/**
	 * Download tax classes API method. 
	 * 
	 * This method will be called when the client request the "download_tax_classes"
	 * function of the API. The routing of the API methods is done inside the
	 * XMLConnectAjaxHandler.inc.php file. 
	 * 
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client.- 
	 *
	 * @return SimpleXMLElement Returns the response XML object. 
	 */
	public function downloadTaxClasses(SimpleXMLElement $requestXml)
	{
		try
		{
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_addTaxClassesNode($requestXml);
			return $this->responseXml;
		}
		catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}


	/**
	 * Add node method. 
	 * 
	 * This method is node implemented in this class. 
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
	 * Abstract Method: Delete Object 
	 * 
	 * This method is not implemented in this class. 
	 */
	protected function _deleteObject(SimpleXMLElement $xml)
	{
		return false;
	}


	/**
	 * Validate request parameter values. 
	 * 
	 * @param string $p_field Filtered field name.
	 * @param string $p_value Filtered field value. 
	 *                        
	 * @throws InvalidArgumentException When the argument is invalid. 
	 */
	protected function _validateArgument($p_field, $p_value)
	{
		// Validate numerical values.
		if($p_field == 'tax_class_id' && !is_numeric($p_value))
		{
			throw new InvalidArgumentException('Invalid ' . $p_field . ' argument value: ' . print_r($p_value, true));
		}
	}

	
	/**
	 * Add the tax classes node into the response XML object.
	 *
	 * This method will filter the tax class results based on the request parameter
	 * values.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request information sent by the client.
	 */
	protected function _addTaxClassesNode(SimpleXMLElement $requestXml)
	{
		// Parse parameters (if any).
		$whereClause = '';
		$limitClause = ''; 
		
		if(property_exists($requestXml, 'parameters'))
		{
			// Convert object to associative array.
			$parameters = json_decode(json_encode($requestXml->parameters), true);  
		
			// WHERE clause 
			if(isset($parameters['tax_classes']))
			{
				$requestConditions = $parameters['tax_classes'];
				$conditions        = array();

				foreach($requestConditions as $condition)
				{
					foreach($condition as $field => $value)
					{
						$this->_validateArgument($field, $value);
						$conditions[] = TABLE_TAX_CLASS . '.' . xtc_db_prepare_input($field) . ' = "'
						                . xtc_db_prepare_input($value) . '"';
					}

					if(!empty($conditions))
					{
						$whereClause = ' AND (' . implode(' OR ', $conditions) . ')';
					}
				}
			}
		
			// LIMIT clause 
			if(isset($parameters['limit']) && isset($parameters['offset']))
			{
				$limitClause = $this->_generateLimitClause($parameters);	
			}
		}
		
		// Prepare and execute query. 
		$query = '
			SELECT 
				tax_class.tax_class_id,
				tax_class.tax_class_title,
				tax_rates.tax_rate,
				tax_class.tax_class_description,
				tax_class.last_modified,
				tax_class.date_added
			FROM ' . TABLE_TAX_CLASS . ' tax_class ,' . TABLE_TAX_RATES . '  tax_rates
			WHERE 
				(tax_class.tax_class_id = tax_rates.tax_class_id 
				AND tax_rates.tax_zone_id = ' . (int)xtc_get_geo_zone_code(STORE_COUNTRY) . ')
				' . $whereClause . '
            GROUP BY ' . TABLE_TAX_CLASS . '.tax_class_id 
	        ORDER BY ' . TABLE_TAX_CLASS . '.tax_class_id
	        ' . $limitClause;
		
		//echo $query . PHP_EOL; // debugging

		$results = xtc_db_query($query);

		// Prepare response XML object. 
		$response = $this->responseXml->addChild('tax_classes');
		
		while($row = xtc_db_fetch_array($results))
		{
			$node = $response->addChild('tax_class');
			$node->addChild('tax_class_id', $row['tax_class_id']);
			$node->addChild('title', $this->wrapValue($row['tax_class_title']));
			$descriptionNode = $node->addChild('description', $this->wrapValue($row['tax_class_description']));
			$descriptionNode->addAttribute('type', 2);
			$node->addChild('tax_rate', $row['tax_rate']);
			$node->addChild('date_added', $row['date_added']);
			$node->addChild('last_modified', $row['last_modified']);
		}
	}
	
	
}