<?php
/* --------------------------------------------------------------
  GxmlLanguages.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlLanguages
 * 
 * Handles the Languages XML requests for the Gambio API.
 * 
 * Supported API Functions: 
 * 		- "download_languages"
 *
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlLanguages extends GxmlMaster
{
	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
	}

	
	/**
	 * This method is called by the "XMLConnectAjaxHandler.inc.php" file.
	 *
	 * This method is inherited by the GxmlMaster class and is instantiated because it
	 * is abstract.
	 * 
	 * @param SimpleXMLElement Contains the request XML data posted by the client. 
	 *                                  
	 * @return SimpleXMLElement Returns the final response XML object. 
	 */
	public function downloadLanguages(SimpleXMLElement $requestXml)
	{
		try
		{
			$this->responseXml->addChild('request');
			$this->responseXml->request->addChild('success', 1);
			$this->_addLanguagesNode($requestXml);
			return $this->responseXml;
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
	 * Generate the languages node based on the given parameters.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request data sent by the client.
	 */
	protected function _addLanguagesNode(SimpleXMLElement $requestXml)
	{
		// Prepare and execute query.
		$query = '
			SELECT languages_id, name, code, language_charset
			FROM ' . TABLE_LANGUAGES;

		if(property_exists($requestXml, 'parameters'))
		{
			$parameters = json_decode(json_encode($requestXml->parameters), true); // Convert object to associative array.

			// WHERE clause
			$conditions = array();
			if(isset($parameters['languages']))
			{
				foreach($parameters['languages'] as $param)
				{
					foreach($param as $field=>$value)
					{
						$this->_validateArgument($field, $value);
						$conditions[] = xtc_db_prepare_input($field)  . ' = "' . xtc_db_prepare_input($value) . '"';
					}
				}
			}
			

			if(count($conditions) > 0)
			{
				$query .= ' WHERE ' . implode(' OR ', $conditions);
			}

			// LIMIT clause
			$query .= ' GROUP BY ' . TABLE_LANGUAGES . '.languages_id ORDER BY languages_id' . $this->_generateLimitClause($parameters);
		} 
		else 
		{
			$query .= ' ORDER BY languages_id'; 
		}

		$results = xtc_db_query($query);

		// Prepare response XML object. 
		$response = $this->responseXml->addChild('languages');
		while($row = xtc_db_fetch_array($results))
		{
			$node = $response->addChild('language');
			$node->addChild('language_id', $row['languages_id']);
			$node->addChild('name', $this->wrapValue($row['name']));
			$node->addChild('iso', $row['code']);
			$node->addChild('charset', $row['language_charset']);
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
		if(($p_field == 'languages_id' || $p_field == 'status' || $p_field == 'sort_order')
		   		&& !is_numeric($p_value))
		{
			throw new InvalidArgumentException('Invalid ' . $p_field . ' value provided (numeric expected): ' . print_r($p_value, true));
		}

		// Validate non numerical values. 
		if(($p_field == 'name' || $p_field == 'code' || $p_field == 'image' || $p_field == 'directory' 
				|| $p_field == 'language_charset') && is_numeric($p_value))
		{
			throw new InvalidArgumentException('Invalid ' . $p_field . ' value provided (string expected): ' . print_r($p_value, true));
		}
	}
}