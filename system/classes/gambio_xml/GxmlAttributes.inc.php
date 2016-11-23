<?php
/* --------------------------------------------------------------
  GxmlAttributes.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlAttributes
 *
 * Handles the Attributes XML requests for the Gambio API.
 *
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlAttributes
{
	/**
	 * @var GxmlHelper
	 */
	private $gxmlHelper;

	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		$this->gxmlHelper = MainFactory::create_object('GxmlHelper');
	}
	
	/**
	 * Handle the download Attributes XML request. 
	 * 
	 * This method is not referenced in the API documentation PDF. 
	 * 
	 * @param SimpleXMLElement $requestXml XML object that contains the request data. 
	 *
	 * @return SimpleXMLElement Returns the response XML object. 
	 */
	public function downloadAttributes(SimpleXMLElement $requestXml)
	{
		$responseXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><GambioXML/>');
		$responseXml->addChild('request');
		$responseXml->request->addChild('success', 1);
		
		$gxmlLanguages = MainFactory::create_object('GxmlLanguages');
		$responseXml = $gxmlLanguages->add_languages_block($responseXml); // will include all languages
		$responseXml = $this->_addAttributesNode($responseXml);
		
		return $responseXml;
	}

	/**
	 * Add the attributes node to the response XML. 
	 * 
	 * @param SimpleXMLElement $responseXml Current state of response XML object. 
	 *
	 * @return SimpleXMLElement Returns the response XML object with the attributes node.
	 */
	protected function _addAttributesNode(SimpleXMLElement $responseXml)
	{
		$responseXml->addChild('attributes');
		
		$query = '
			SELECT po.language_id AS po_language_id, po.*, pov.*
			FROM
				products_options AS po 
					LEFT JOIN products_options_values_to_products_options AS pov2po USING (products_options_id)
					LEFT JOIN products_options_values AS pov ON (pov2po.products_options_values_id = pov.products_options_values_id AND po.language_id = pov.language_id)
			ORDER BY
				po.products_options_id,
				pov.products_options_values_id,
				po.language_id
		';
		$results = xtc_db_query($query);
		
		$currRowId = 0;
		$optionsNameArray = array();
		
		while(($row = xtc_db_fetch_array($results) ))
		{
			if($row['products_options_id'] != $currRowId)
			{
				// add collected names from last loop
				foreach ($optionsNameArray as $key=>$value)
				{
					$node = $attribute->addChild('name', $value);
					$node->addAttribute('language_id', $key);
				}
				
				// products_options
				$attribute = $responseXml->attributes->addChild('attribute');
				$attribute->addChild('attribute_id', $row['products_options_id']);
				$attributeValues = $attribute->addChild('values');
				
				$currRowId = $row['products_options_id'];
				$optionsNameArray = array();
			}
			
			//collect option_names for later output in next loop
			$optionsNameArray[$row['po_language_id']] = $row['products_options_name'];
			
			$node = $attributeValues->addChild('value', $row['products_options_values_name']);
			$node->addAttribute('id', $row['products_options_values_id']);
			$node->addAttribute('language_id', $row['language_id']);
			$node = NULL;
		}
		//add collected names from last loop
		foreach ($optionsNameArray as $key => $value)
		{
			$node = $attribute->addChild('name', $value);
			$node->addAttribute('language_id', $key);
		}
		
		return $responseXml;
	}
}