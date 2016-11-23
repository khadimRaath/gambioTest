<?php
/* --------------------------------------------------------------
  GxmlMaster.inc.php 2016-07-25
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'inc/xtc_get_geo_zone_code.inc.php');

/**
 * Class GxmlMaster
 * 
 * Contains the implementations of the general methods used in the Gambio XML
 * API. The API is split into multiple plugins each of which handle a specific
 * section of the shop data. The request router class will load and call the 
 * required method and the requested plugin will make the requested actions and 
 * produce a response in order to be returned back to the client.
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
abstract class GxmlMaster 
{
	/**
	 * @var array
	 */
	protected $mapperArray = array();

	/**
	 * @var array
	 */
	protected $tableMapperArray = array();
	
	/**
	 * @var array
	 */
	protected $languageDependentMapperArray = array();
	
	/**
	 * @var GxmlHelper
	 */
	protected $gxmlHelper;
	
	/**
	 * @var SimpleXMLElement
	 */
	protected $responseXml;
	
	/**
	 * @var string
	 */
	protected $pluralName;
	
	/**
	 * @var string
	 */
	protected $singularName;
	
	/**
	 * @var array
	 */
	protected $languageArray;
	
	/**
	 * @var array
	 */
	protected $taxArray;

	/**
	 * @var SimpleXMLElement
	 */
	protected $responseXmlBuffer;


	/**
	 * Class Constructor
	 */
	public function __construct()
	{
		$this->gxmlHelper = MainFactory::create_object('GxmlHelper');
		$this->responseXml = simplexml_load_string($this->gxmlHelper->getBlankXml());
		$this->responseXmlBuffer = simplexml_load_string($this->gxmlHelper->getBlankXml());
		$this->_loadLanguages();
		$this->_loadTaxes();
	}

	
	/**
	 * Abstract Add Node Method
	 * 
	 * This method will be implemented by some child classes in order to add XML nodes 
	 * to the response XML object.
	 * 
	 * @param array $data
	 * @param string $tableColumn
	 * @param string $nodeName
	 * @param SimpleXMLElement $xml
	 */
	abstract protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml);


	/**
	 * Abstract Method Delete Object from Database
	 * 
	 * @param SimpleXMLElement $requestXml
	 */
	abstract protected function _deleteObject(SimpleXMLElement $requestXml);


	/**
	 * Generates a response XML object out of a response array.
	 *
	 * @param array $responseData Must contain the response data (associative array).
	 *
	 * @return SimpleXMLElement
	 */
	public function generateResponseXml(array $responseData)
	{
		$xml = simplexml_load_string($this->gxmlHelper->getBlankXml());
		$isSuccessful = true;

		$xml->addChild('request');
		$xml->addChild($this->pluralName);

		foreach($responseData as $item)
		{
			$childNode = $xml->{$this->pluralName}->addChild($this->singularName);

			if(isset($item['external_' . $this->singularName . '_id']))
			{
				$childNode->addChild('external_' . $this->singularName . '_id', $item['external_' . $this->singularName . '_id']);
			}

			$childNode->addChild($this->singularName . '_id', $item[$this->singularName . '_id']);
			$childNode->addChild('success', $item['success']);
			$childNode->addChild('errormessage', $item['errormessage']);
			$childNode->addChild('action_performed', $item['action_performed']);

			$isSuccessful &= (boolean)$item['success'];
		}

		if (!$isSuccessful)
		{
			$xml->request->addChild('errormessage', 'request error');
		}

		$xml->request->addChild('success', $isSuccessful);

		return $xml;
	}


	/**
	 * Change encoding to latin1.
	 *
	 * @param mixed $p_data Data could be either array, string or object. Otherwise there
	 *					    won't be any conversion and the $p_data value will be reutrned
	 *                      as it is.
	 *
	 * @return string Returns the latin1 encoded value of the $p_data parameter.
	 */
	public function convertToLatin1($p_data)
	{
		if(is_array($p_data))
		{
			$converted = array();
			
			foreach($p_data as $key=>$value)
			{
				$key = $this->utf8Decode($key);
				$converted[$key] = $this->convertToLatin1($value);
			}
		}
		elseif(is_object($p_data))
		{
			$vars = array_keys(get_object_vars($p_data));
			$converted = $p_data;

			foreach($vars AS $var)
			{
				$converted->var = $this->convertToLatin1($p_data->$var);
			}
		}
		elseif(is_string($p_data))
		{
			$converted = $this->utf8Decode($p_data);
		}
		else
		{
			$converted = $p_data;
		}

		return $converted;
	}


	/**
	 * Check if string is utf8 encoded.
	 *
	 * @param $p_string String value to be cheked.
	 *
	 * @return bool Returns the check result.
	 */
	public function isUtf8String($p_string)
	{
		if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $p_string))
		{
			return true;
		}

		return false;
	}


	/**
	 * Decode UTF8 string.
	 *
	 * @param string $p_string The string to be decoded.
	 *
	 * @return string Returns the decoded string.
	 */
	public function utf8Decode($p_string)
	{
		if($this->isUtf8String($p_string))
		{
			$targetCharset = 'ISO-8859-15';

			if(isset($_SESSION['language_charset']))
			{
				$targetCharset = trim(strtoupper($_SESSION['language_charset']));
			}

			if($targetCharset != 'UTF-8')
			{
				if(function_exists('iconv'))
				{
					$p_string = iconv("UTF-8", $targetCharset . "//TRANSLIT", $p_string);
				}
				else
				{
					$p_string = utf8_decode($p_string);
				}
			}
		}

		return $p_string;
	}


	/**
	 * Add a child XML node to a parent element.
	 *
	 * @param SimpleXMLElement $parentXml Parent XML object.
	 * @param SimpleXMLElement $childXml Child XML node will be added to parent.
	 * @param bool $includeXmlDeclaration (Optional) Whether to include the Xml declaration on the result.
	 *
	 * @return bool Returns the operation result.
	 */
	public function addXmlChild(SimpleXMLElement $parentXml, SimpleXMLElement $childXml, $includeXmlDeclaration = true)
	{
		$xmlString = (string) $childXml->asXML();

		if($includeXmlDeclaration == false)
		{
			$xmlLineArray = explode("\n", $xmlString);
			array_shift($xmlLineArray);
			$xmlString = implode("\n", $xmlLineArray);
		}

		$domParent = dom_import_simplexml($parentXml);
		$domChild = $domParent->ownerDocument->createDocumentFragment();
		$domChild->appendXML($xmlString);

		$result = (boolean)$domParent->appendChild($domChild);
		return $result;
	}


	/**
	 * Delete an XML child node by tag name.
	 *
	 * @param SimpleXMLElement $xml The XML to be edited.
	 * @param string $p_tagName The tag name to be removed.
	 */
	public function deleteXmlChildrenByTagName(SimpleXMLElement $xml, $p_tagName)
	{
		$domParent = dom_import_simplexml($xml);
		for($i = 0; $i < count($domParent->getElementsByTagName($p_tagName)); $i++)
		{
			$actual = $domParent->getElementsByTagName($p_tagName)->item($i);
			if(!empty($actual))
			{
				$domParent->removeChild($actual);
				$i--;
			}
		}
	}


	/**
	 * Wrap a node value with a prefix and a post fix.
	 *
	 * By default the <!CDATA[...]]> wrapper is used.
	 *
	 * @param sting $p_value This value will be wrapped by the prefix and the postfix.
	 * @param string $p_prefix (Optional) Will be added before the value.
	 * @param string $p_suffix (Optional) Will be added after the value.
	 *
	 * @return string Returns the wrapped value.
	 */
	public function wrapValue($p_value, $p_prefix = '<![CDATA[', $p_suffix = ']]>')
	{
		$wrappedValue = $p_value;

		if($p_value !== '')
		{
			$wrappedValue = $p_prefix . $p_value . $p_suffix;
		}

		return $wrappedValue;
	}


	/**
	 * Add data in an XML object.
	 *
	 * @param array $data Includes the data to be included in the XML object.
	 * @param SimpleXMLElement $xml The object to be edited.
	 */
	public function add(array $data, $xml)
	{
		foreach ($this->mapperArray as $nodeName=>$tableColumn)
		{
			$this->_addNode($data, $tableColumn, $nodeName, $xml);
		}
	}


	/**
	 * Check if node exists inside an XML object.
	 *
	 * @param SimpleXMLElement $xml The XML object to be searched.
	 * @param string $p_nodeName Node name to be found.
	 * @param array $p_attributesArray (Optional) If provided, the method will include attributes in the search.
	 * @param string $p_attributeConjunction (Optional) Attribute conjunction string (must be either "and" - "or").
	 *
	 * @return bool Returns whether the node was found or not.
	 */
	public function nodeExists(SimpleXMLElement $xml, $p_nodeName, $p_attributesArray = array(), $p_attributeConjunction = 'and')
	{
		$nodeChildren = $xml->children();

		foreach ($nodeChildren as $nodeName=>$nodeValuesArray)
		{
			if ($nodeName == $p_nodeName)
			{
				if (empty($p_attributesArray))
				{
					return true;
				}
				else
				{
					$nodeExists = null;
					foreach ($p_attributesArray as $attrName=>$attrValue)
					{
						$nodeValuesAttributes = $nodeValuesArray->attributes();
						switch ($p_attributeConjunction)
						{
							case 'or':
								if ($nodeExists === null)
								{
									$nodeExists = false;
								}

								$nodeExists |= isset($nodeValuesAttributes[$attrName]) && (string)$nodeValuesAttributes[$attrName][0] == $attrValue;
								break;

							case 'and':
								if ($nodeExists === null)
								{
									$nodeExists = true;
								}
								$nodeExists &= isset($nodeValuesAttributes[$attrName]) && (string)$nodeValuesAttributes[$attrName][0] == $attrValue;
								break;

							default:
								break;
						}
					}
					return (boolean)$nodeExists;
				}
			}
		}

		return false;
	}


	/**
	 * Check if node value exists.
	 *
	 * @param SimpleXMLElement $xml The XML object to be searched.
	 * @param string $p_nodeName The node name that will be searched.
	 * @param string $p_nodeValueName The node value name that needs to be searched.
	 * @param string $p_nodeValue The node value that needs to be found.
	 * @param array $attributes (Optional) If provided, node attributes will be included
	 *							in the search.
	 *
	 * @return bool Returns the search result.
	 */
	public function nodeValueExists(SimpleXMLElement $xml, $p_nodeName, $p_nodeValueName, $p_nodeValue, array $attributes = array())
	{
		$nodeExists = false;

		foreach($xml->{$p_nodeName} as $nodeName => $nodeValuesArray)
		{
			if($nodeName == $p_nodeName)
			{
				if(empty($attributes))
				{
					foreach($nodeValuesArray->children() as $nodeValueName => $nodeValueArray)
					{
						if($nodeValueName == $p_nodeValueName && $p_nodeValue == (string)$nodeValueArray[0])
						{
							$nodeExists = $nodeValuesArray;
							break;
						}
					}
				}
				else
				{
					foreach($attributes as $attrName => $attrValue)
					{
						foreach($nodeValuesArray->attributes() as $nodeAttrName=>$valueArray)
						{
							if($attrName == $nodeAttrName && (string)$valueArray[0] == $attrValue)
							{
								foreach($nodeValuesArray->children() as $nodeValueName => $nodeValueArray)
								{
									if($nodeValueName == $p_nodeValueName && $p_nodeValue == (string)$nodeValueArray[0])
									{
										$nodeExists = $nodeValuesArray;
										break;
									}
								}
							}
						}
					}
				}
			}
		}

		return $nodeExists;
	}


	/**
	 * Get an XML object that contains data about an API relative exception.
	 *
	 * @param Exception $ex The exception object that will be interpreted into
	 *                      the XML document.
	 *
	 * @return SimpleXMLElement Returns the XML document object that contains the
	 * 							exception information.
	 */
	public function handleApiException(Exception $ex)
	{
		// Return an error response.
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');
		$xml->addChild('request');
		$xml->request->addChild('success', '0');
		$xml->request->addChild('errormessage', $ex->getMessage());
		$xml->request->addChild('stack', $ex->getTraceAsString());
		return $xml;
	}
	
	
	/**
	 * Get plural name of current class section.
	 *
	 * @return string
	 */
	protected function _getPluralName()
	{
		return $this->pluralName;
	}


	/**
	 * Set plural name of current class section.
	 * 
	 * @param string $p_name
	 */
	protected function _setPluralName($p_name)
	{
		$this->pluralName = $p_name;
	}


	/**
	 * Get singular name of current class section. 
	 * 
	 * @return string
	 */
	protected function _getSingularName()
	{
		return $this->singularName;
	}


	/**
	 * Set singular name of current class section. 
	 * 
	 * @param string $p_name
	 */
	protected function _setSingularName($p_name)
	{
		$this->singularName = $p_name;
	}


	/**
	 * Save record to database.
	 * 
	 * @param      $p_dataObject
	 * @param      $p_dataArray
	 * @param bool $p_primaryKeyName
	 * @param bool $p_primaryKeyValue
	 * @param bool $p_update
	 *
	 * @return bool|int|resource
	 */
	protected function _save(&$p_dataObject, $p_dataArray, $p_primaryKeyName = false, $p_primaryKeyValue = false, $p_update = false, array $p_primaryKeyArray = array())
	{
		$newRecordId = false;
		
		$dataArray = $this->convertToLatin1($p_dataArray);
		
		if (is_array(current($dataArray)) && $p_dataObject->v_db_table != TABLE_PRODUCTS_XSELL && $p_dataObject->v_db_table != 'products_properties_combis_values' && $p_dataObject->v_db_table != 'image_data' && $p_dataObject->v_db_table != TABLE_PRODUCTS_IMAGES && $p_dataObject->v_db_table != 'gm_prd_img_alt')
		{
			if(strpos($p_dataObject->v_db_table, TABLE_PERSONAL_OFFERS_BY) !== false)
			{
				foreach ($dataArray as $p_personal_offer_data_array)
				{
					if($p_update === true)
					{
						$p_dataObject->set_keys(array($p_primaryKeyName => $p_primaryKeyValue, 'quantity' => $p_personal_offer_data_array['quantity']));
					}
					else
					{
						$p_dataObject->set_keys(array($p_primaryKeyName => false));
					}
					
					$p_dataObject->set_data_value($p_primaryKeyName, $p_primaryKeyValue);
					
					foreach ($p_personal_offer_data_array as $key=>$value)
					{
						$p_dataObject->set_data_value($key, $value);
					}

					$newRecordId = $this->_performDbAction($p_dataObject, 'update');
					if($p_update === true && $newRecordId === false)
					{
						$p_dataObject->set_keys(array($p_primaryKeyName => false));
						$newRecordId = $this->_performDbAction($p_dataObject, 'update');
					}					
				}
			}
			else
			{
				foreach ($dataArray as $t_language_id => $p_language_data_array)
				{
					if ($p_update)
					{
						$p_dataObject->set_keys(array($p_primaryKeyName => $p_primaryKeyValue, 'language_id' => $t_language_id));
					}
					else
					{
						$p_dataObject->set_keys(array($p_primaryKeyName => false));
						$p_dataObject->set_data_value('language_id', $t_language_id);
					}

					foreach ($p_language_data_array as $key => $value)
					{
						$p_dataObject->set_data_value($key, $value);
					}

					if($p_primaryKeyName !== false && $p_primaryKeyValue !== false)
					{
						if($p_primaryKeyName != 'language_id' || $p_primaryKeyName == 'language_id' && in_array($p_dataObject->v_db_table, $this->languageDependentMapperArray))
						{
							$p_dataObject->set_data_value($p_primaryKeyName, $p_primaryKeyValue);
						}
					}
					
					$newRecordId = $this->_performDbAction($p_dataObject, 'update');
					if($p_update === true && $newRecordId === false)
					{
						$p_dataObject->set_keys(array($p_primaryKeyName => false));
						$p_dataObject->set_data_value('language_id', $t_language_id);
						$newRecordId = $this->_performDbAction($p_dataObject, 'update');
					}
				}
			}
		}
		elseif ($p_dataObject->v_db_table == TABLE_PRODUCTS_XSELL)
		{
			// insert and update
			if(isset($p_dataArray['xsell_updates']) && is_array($p_dataArray['xsell_updates']))
			{
				foreach($p_dataArray['xsell_updates'] as $t_xsell_update)
				{
					$p_dataObject->set_data_value('products_id', $p_primaryKeyValue);
					$p_dataObject->set_data_value('products_xsell_grp_name_id', 0);
					$p_dataObject->set_data_value('xsell_id', (int)$t_xsell_update['product_id']);
					isset($t_xsell_update['sort_order']) ? $t_sort_order = (int)$t_xsell_update['sort_order'] : $t_sort_order = 0;
					$p_dataObject->set_data_value('sort_order', $t_sort_order);
					$this->_performDbAction($p_dataObject, 'update', true);
				}
			}
			
			// delete
			if(isset($p_dataArray['xsell_deletion']) && is_array($p_dataArray['xsell_deletion']))
			{
				foreach($p_dataArray['xsell_deletion'] as $t_xsell_deletion)
				{
					$p_dataObject->set_keys(array($p_primaryKeyName => $p_primaryKeyValue, 'xsell_id' => $t_xsell_deletion['product_id']));
					$this->_performDbAction($p_dataObject, 'delete');
				}
			}
			
			return true;
		}
		else if ($p_dataObject->v_db_table == TABLE_PRODUCTS_TO_CATEGORIES)
		{
			$t_categories = explode(',', $dataArray['categories_id']);
			foreach ($t_categories as $t_category_id)
			{
				$p_dataObject->set_data_value($p_primaryKeyName, $p_primaryKeyValue);
				$p_dataObject->set_data_value('categories_id', $t_category_id);
				$this->_performDbAction($p_dataObject, 'update', true);
			}
			
			$t_delete_categories = explode(',', $dataArray['delete_categories_id']);
			foreach ($t_delete_categories as $t_category_id)
			{
				if($t_category_id === '')
				{
					continue;
				}
				$p_dataObject->set_keys(array($p_primaryKeyName => $p_primaryKeyValue, 'categories_id' => $t_category_id));
				$this->_performDbAction($p_dataObject, 'delete');
			}
		}
		else if ($p_dataObject->v_db_table == 'products_properties_combis_values')
		{
			foreach ($dataArray as $t_property_value)
			{
				foreach ($t_property_value as $key => $value)
				{
					$p_dataObject->set_data_value($key, $value);
				}
				$newRecordId = $this->_performDbAction($p_dataObject, 'update', true);
			}
		}
		else if ($p_dataObject->v_db_table == 'image_data')
		{
			foreach ($dataArray as $productImageData)
			{
				$p_dataObject->v_db_table = TABLE_PRODUCTS_IMAGES;
				$newRecordId = $this->_save($p_dataObject, $productImageData['products_images'], $p_primaryKeyName, $p_primaryKeyValue, $p_update);
				$newImageId = $newRecordId;
				
				if(isset($productImageData['gm_prd_img_alt']))
				{
					$p_dataObject->v_table_content = array();
					$p_dataObject->v_db_table = 'gm_prd_img_alt';
					$newRecordId = $this->_save($p_dataObject, $productImageData['gm_prd_img_alt'], '', '', $p_update, array('image_id' => $newRecordId, $p_primaryKeyName => $p_primaryKeyValue));
				}
				
				$p_dataObject->v_table_content = array();

				$responseArray = array(
						'image_id' => $newImageId,
						'success' => 1,
						'errormessage' => '',
						'action_performed' => (isset($productImageData['products_images']['image_id']) ? 'update' : 'insert')
				);
				if(isset($productImageData['products_images']['external_image_id'])
				   && !empty($productImageData['products_images']['external_image_id']))
				{
					$responseArray['external_image_id'] = $productImageData['products_images']['external_image_id'];
				}
				$imageData = $this->_generateAdditionalImagesResponseXml($responseArray);
				$image = $this->responseXmlBuffer->addChild('image');
				foreach($imageData as $property => $propertyValue)
				{
					$image->addChild($property, $propertyValue);
				}
			}
		}
		else if ($p_dataObject->v_db_table == TABLE_PRODUCTS_IMAGES)
		{
			$p_dataObject->set_keys(array('products_id' => $p_primaryKeyName, 'image_nr' => $dataArray['image_nr']));
			$p_dataObject->set_data_value('products_id', $p_primaryKeyValue);

			foreach ($dataArray as $key => $value)
			{
				if ($key == 'external_image_id')
				{
					continue;
				}
				$p_dataObject->set_data_value($key, $value);
			}
			$newRecordId = $this->_performDbAction($p_dataObject, 'update', true);
		}
		else if ($p_dataObject->v_db_table == 'gm_prd_img_alt')
		{
			foreach ($dataArray as $t_language_id => $t_image_alt_data)
			{
				$primaryKeyArray = $p_primaryKeyArray;
				$primaryKeyArray['language_id'] = $t_language_id;
				foreach ($t_image_alt_data as $key => $value)
				{
					$p_dataObject->set_keys($primaryKeyArray);
					$p_dataObject->set_data_value($key, $value);
					foreach($primaryKeyArray as $keyName => $keyValue)
					{
						$p_dataObject->set_data_value($keyName, $keyValue);
					}
					$newRecordId = $this->_performDbAction($p_dataObject, 'update', true);
				}
			}
		}
		else
		{
			if ($p_update && $p_dataObject->v_db_table != TABLE_ORDERS_STATUS_HISTORY)
			{
				$p_dataObject->set_keys(array($p_primaryKeyName => $p_primaryKeyValue));
			}
			else
			{
				$p_dataObject->set_keys(array($p_primaryKeyName => false));
			}
			foreach($dataArray as $key => $value)
			{
				$p_dataObject->set_data_value($key, $value);
				
			}
			
			if($p_primaryKeyName !== false && $p_primaryKeyValue !== false)
			{
				$p_dataObject->set_data_value($p_primaryKeyName, $p_primaryKeyValue);
			}
			
			$newRecordId = $this->_performDbAction($p_dataObject, 'update');
		}
		
		return $newRecordId;
	}


	/**
	 * Save object to database. 
	 * 
	 * When the client sents an upload requests that stores data in the database this 
	 * method will be called from the "_upload" method and perform the insert-update 
	 * operation. 
	 * 
	 * @param SimpleXMLElement $xml Contains the object to be stored in the database. 
	 *
	 * @return numeric Returns the record ID. 
	 */
	protected function _saveObject(SimpleXMLElement $xml)
	{
		$xmlData = $this->_parseXmlData($xml);
		
		// Load data objects by using the given xml node values. 
		$tableNames = array_keys($xmlData);
		$dataObjects = array();
		foreach ($tableNames as $name)
		{
			$dataObjects[$name] = MainFactory::create_object('GMDataObject', array($name));
		}
		
		$idColumn = $xml->getName() . '_id';
		$recordId = (string)$xml->{$idColumn};
		$isUpdate = false;
		
		if(!empty($recordId))
		{
			if(empty($this->v_default_action) || $this->v_default_action == 'update')
			{
				$isUpdate = true;
			}
		}
		else
		{
			$recordId = false;
		}
		
		// Save record to database. 
		$dbIdColumn = $this->mapperArray[$idColumn];
		$dbTable = $this->tableMapperArray[$idColumn];
		$tableObject = $dataObjects[$dbTable];
		$recordData = $xmlData[$dbTable]; 
		$resultRecordId = $this->_save($tableObject, $recordData, $dbIdColumn, $recordId, $isUpdate);
		
		if ($isUpdate == false)
		{
			$xml->addChild($idColumn, $resultRecordId);
			$xmlData = $this->_parseXmlData($xml);
		}
		
		if ($resultRecordId == -1 || $resultRecordId === true)
		{
			$resultRecordId = $recordId;
		}
		
		$tableNames = array_flip($tableNames);
		unset($tableNames[$dbTable]);
		$tableNames = array_flip($tableNames);


		if($isUpdate && $resultRecordId === false) // cannot proceed if the $resultRecordId is not found and we're currently in update operation 
		{
			throw new Exception('Record ID does not exist in the database (column: ' . $dbTable . '.' . $dbIdColumn . ', record ID: ' . $recordId . ')');
		}
		
		// save sub tables
		foreach ($tableNames as $name)
		{
			$tableObject = $dataObjects[$name];
			$recordData = $xmlData[$name];

			$t_id = $this->_save($tableObject, $recordData, $dbIdColumn, $resultRecordId, $isUpdate);
			
			if ($isUpdate == true && $t_id == false)
			{
				$this->_save($tableObject, $recordData, $dbIdColumn, $resultRecordId, false);
			}
		}
		return $resultRecordId;
	}


	/**
	 * Generate the WHERE clause of a query.
	 * 
	 * Generate the WHERE clause of an SQL query depending the parameters 
	 * passed by the client. 
	 * 
	 * @param array $data Must contain the "where_parts" value.
	 * @param bool $p_appendWhereClause (Optional) Whether there is already a WHERE clause
	 *								    and this is a supplementary one.
	 *
	 * @return string Returns the generated WHERE clause. 
	 * 
	 */
	protected function _generateWhereClause($data, $p_appendWhereClause = false)
	{
		$whereClause = '';
		$conditionsGroupArray = array();
		
		if(is_array($data) && isset($data['where_parts']))
		{
			$whereClause = ' WHERE ((';
			
			if($p_appendWhereClause)
			{
				$whereClause = ' AND ((';
			}			
			
			foreach($data['where_parts'] as $dataArray)
			{
				$conditionsArray = array();
				
				foreach($dataArray as $column => $value)
				{
					$conditionsArray[] = $column . " = '" . xtc_db_input($value) . "'";
				}
				
				$conditionsGroupArray[] = implode(' AND ', $conditionsArray);
			}
			
			$whereClause .= implode(') OR (', $conditionsGroupArray);
			$whereClause .= ')) ';
		}
		
		return $whereClause;
	}

	
	/**
	 * Generates the LIMIT section of the query.
	 * 
	 * @param array $p_arguments Contains the limit and offset values. 
	 *
	 * @return string Returns the LIMIT query part.
	 * @throws InvalidArgumentException When limit or offset values are invalid.
	 */
	protected function _generateLimitClause($p_arguments)
	{
		if(!is_array($p_arguments))
		{
			throw new InvalidArgumentException('Invalid query arguments provided: ' . print_r($p_arguments, true));
		} 
		
		if(!isset($p_arguments['limit']))
		{
			return ''; // No limit parameter was provided.
		}
		
		$limitClause = '';

		// Validate limit value. 
		if(!is_numeric($p_arguments['limit']))
		{
			throw new InvalidArgumentException('Invalid limit value given: ' . $p_arguments['limit']);
		}
		
		$limit = (int) $p_arguments['limit']; 
		
		// Validate offset value. 
		if(isset($p_arguments['offset']) && !is_numeric($p_arguments['offset']))
		{
			throw new InvalidArgumentException('Invalid offset value given: ' . $p_arguments['offset']);
		}
			
		$offset = (int) (isset($p_arguments['offset'])) ? $p_arguments['offset'] : 0; 
		
		// Create the query part string only if limit is above zero. 
		if($limit > 0)
		{
			$limitClause = ' LIMIT ' . $offset . ', ' . $limit;
		}		
		
		return $limitClause;
	}

	
	/**
	 * Get an array with the XML data. 
	 * 
	 * @param SimpleXMLElement $xml XML to be parsed. 
	 * @param string $p_parentTable (Optional) The DB table that is referenced by this XML. 
	 *
	 * @return array Returns an array with the XML data. 
	 */
	protected function _parseXmlData(SimpleXMLElement $xml, $p_parentTable = '')
	{
		$result = array();
		
		foreach($xml->children() as $nodeName=>$nodeValuesArray)
		{
			$nodeName = $p_parentTable . $nodeName;
			
			if(isset($this->tableMapperArray[$nodeName]) == false || isset($this->mapperArray[$nodeName]) == false)
			{
				if(in_array($nodeName, array('special', 'categories', 'cross_selling_products', 'status_history', 'additional_images')) == false)
				{
					continue;
				}
			}
			
			if(isset($this->tableMapperArray[$nodeName]))
			{
				$table = $this->tableMapperArray[$nodeName];
			}
			
			if(isset($this->mapperArray[$nodeName]))
			{
				$column = $this->mapperArray[$nodeName];
			}

			switch ($nodeName)
			{
				case 'special':
				case 'status_history':
					$result = array_merge($result, $this->_parseXmlData($xml->{$nodeName}, $nodeName . '/'));
					break;
				
				case 'customer_group_permission':
					$result[$table][$column . $nodeValuesArray['customer_group_id']] = (string)$nodeValuesArray[0];
					break;
				
				case 'quantity_unit_id':
					$result[$table]['products_id'] = true;
					$result[$table][$this->mapperArray[$nodeName]] = (string)$nodeValuesArray[0];
					break;
				
				case 'cross_selling_products':
					$updateXsellingProducts = array();
					$deleteXsellingProducts = array();
					
					foreach ($nodeValuesArray->children() as $xsellProductNode)
					{
						$xsellUpdate = array();
						$xsellDelete = array();
						
						foreach ($xsellProductNode as $leafName => $leafValue)
						{
							if (isset($xsellProductNode['action']) && $xsellProductNode['action'] == 'delete')
							{
								$xsellDelete[$leafName] = (string)$leafValue;
								continue;
							}
							else
							{
								$xsellUpdate[$leafName] = (string)$leafValue;
							}
						}
						
						if(count($xsellUpdate))
						{
							$updateXsellingProducts[] = $xsellUpdate;
						}
							
						if(count($xsellDelete))
						{
							$deleteXsellingProducts[] = $xsellDelete;
						}
					}
					$result[$this->tableMapperArray['cross_selling_products/cross_selling_product/product_id']]['xsell_updates'] = $updateXsellingProducts;
					$result[$this->tableMapperArray['cross_selling_products/cross_selling_product/product_id']]['xsell_deletion'] = $deleteXsellingProducts;
					break;
				
				case 'categories':
					$categories = array();
					$deleteCategories = array();
					
					foreach ($nodeValuesArray->children() as $categoryNode)
					{
						foreach ($categoryNode->children() as $leafName => $leafValue)
						{
							if ($leafName == 'category_id')
							{
								if (isset($categoryNode['action']) && $categoryNode['action'] == 'delete')
								{
									$deleteCategories[] = $leafValue;
								}
								else
								{
									$categories[] = $leafValue;
								}
							}
						}
					}
					
					if (!empty($categories))
					{
						$result[$this->tableMapperArray['categories/category/category_id']]['categories_id'] = implode(',', $categories);
						$result[$this->tableMapperArray['categories/category/category_id']]['delete_categories_id'] = implode(',', $deleteCategories);
					}
					break;
				
				case 'personal_offer':
					foreach($nodeValuesArray->children() as $personalOfferNodeName => $personalOfferNodeValuesArray)
					{
						$count_index = 0;
					
						switch($personalOfferNodeName)
						{
							case 'price':
								if(isset($result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']]))
								{
									$count_index = count($result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']]) - 1;
									if(isset($result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']][$count_index]['price']))
									{
										$count_index++;
									}
								}
								$result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']][$count_index]['personal_offer'] = (string)$personalOfferNodeValuesArray[0];
								break;
							
							case 'quantity':
								if(isset($result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']]))
								{
									$count_index = count($result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']]) - 1;
									if(isset($result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']][$count_index]['quantity']))
									{
										$count_index++;
									}
								}
								$result[TABLE_PERSONAL_OFFERS_BY . $nodeValuesArray['customer_group_id']][$count_index]['quantity'] = (string)$personalOfferNodeValuesArray[0];
								break;
							
						}
					}
					break;
				
				case 'personal_offers':
					$result = array_merge($result, $this->_parseXmlData($xml->{$nodeName}, ''));
					break;
				
				case 'property_values':
					if (!isset($result['products_properties_combis_values']) || !is_array($result['products_properties_combis_values']))
					{
						$result['products_properties_combis_values'] = array();
					}
					
					for ($i = 0; $i < count($nodeValuesArray); $i++)
					{
						$result['products_properties_combis_values'][] = array('properties_values_id' => (int) $nodeValuesArray->property_value[$i]->property_value_id,
																					'products_properties_combis_id' => (int) $xml->product_combi_id);
					}
					break;

				case 'additional_images':
					$result['image_data'] = array();

					foreach($nodeValuesArray->children() as $imageNode)
					{
						$result['image_data'][] = $this->_parseXmlData($imageNode, 'additional_images/image/');
					}
					break;
				case 'additional_images/image/image_url':
					break;
				
				default:
					if (isset($this->mapperArray[$nodeName]))
					{
						if ((isset($nodeValuesArray['language_id']) || isset($nodeValuesArray['language_iso'])) 
								&& in_array($table, $this->languageDependentMapperArray))
						{
							if (!isset($nodeValuesArray['language_id']))
							{
								$languageId = $this->_resolveLanguageIso($nodeValuesArray['language_iso']);
							}
							else
							{
								$languageId = $nodeValuesArray['language_id'];
							}
							
							$result[$table][(string)$languageId][$column] = (string)$nodeValuesArray[0];
						}
						else
						{
							$result[$table][$this->mapperArray[$nodeName]] = (string)$nodeValuesArray[0];
						}
					}
					break;
			}
		}
		
		return $result;
	}

	
	/**
	 * Implements the upload procedure. 
	 * 
	 * IMPORTANT! This method does not always return a value(!!!)
	 * 
	 * @param SimpleXMLElement $xml XML object to be used by the operation. 
	 * @param array $responseData Array with the response data (used by reference).
	 *
	 * @return mixed Returns the id of the record that was affected buy the operation. 
	 */
	protected function _upload(SimpleXMLElement $xml, array &$responseData)
	{
		$recordType = $this->singularName;
		$externalId = (string)$xml->{'external_' . $recordType . '_id'};
		$recordId = (string)$xml->{$recordType . '_id'};
		$isDelete = ($xml['action'] == 'delete'); // Whether current operation is delete. 

		if(!empty($recordId) && (!is_numeric($recordId) || (int)$recordId != (double)$recordId))
		{
			throw new InvalidArgumentException('Request XML is invalid: Invalid ' . $recordType . '_id value: ' . $recordId);
		}
		
		if(empty($externalId) && empty($recordId) && $isDelete === false) // Missing External ID Error
		{
			$responseData[] = array(
				'external_' . $recordType . '_id' => '',
				$recordType . '_id' => $recordId,
				'success' => '0',
				'errormessage' => 'missing external_' . $recordType . '_id',
				'action_performed' => 'error'
			);
		}
		else if($isDelete) // Delete Record 
		{
			$responseData[] = array(
				'external_' . $recordType . '_id' => $externalId,
				$recordType . '_id' => $recordId,
				'errormessage' => '',
				'action_performed' => 'delete'
			);
			$result = $this->_deleteObject($xml);
			$responseData[count($responseData) - 1]['success'] = (int)$result;
		}
		else // Insert/Update Record
		{
			$responseData[] = array(
				'external_' . $recordType . '_id' => $externalId,
				'errormessage' => '',
				'action_performed' => (empty($recordId) ? 'create' : 'update')
			);
			
			// Execute the save operation and store the record id to response data. 
			$recordId = $this->_saveObject($xml);
			$responseData[count($responseData) - 1][$recordType . '_id'] = $recordId;
			
			// Set operation result to response data. 
			$result = (boolean)$recordId;
			$responseData[count($responseData) - 1]['success'] = (int)$result;
		}
		
		return $recordId;
	}

	
	/**
	 * Remove all the children of the given xml element.-
	 *
	 * @param SimpleXMLElement $xml XML object to be edited.
	 * @param bool $p_delete Whether to delete the children or save them. 
	 *
	 * @returns bool Returns the operation result. 
	 */
	protected function _uploadChildren(SimpleXMLElement $xml, $p_delete)
	{
		if ($p_delete)
		{
			$result = $this->delete_children($xml);
		}
		else
		{
			$result = $this->save_children($xml);
		}
		
		return $result;
	}


	/**
	 * Perform an action upon the database. 
	 * 
	 * @param mixed $p_sqlObject Could be a query or a database handler object.  
	 * @param mixed(bool|string) $p_action (Optional) The action to be performed, could be either "create", "update", "delete".
	 * @param bool $p_replace (Optional) Whether to replace an existing record on the database.
	 *
	 * @return bool|int|resource Returns the database operation result.
	 */
	protected function _performDbAction(&$p_sqlObject, $p_action = false, $p_replace = false)
	{
		$result = true;
		
		if (is_string($p_sqlObject))
		{
			$result = xtc_db_query($p_sqlObject);
		}
		else
		{
			switch ($p_action)
			{
				case 'create':
				case 'update':
					$result = $p_sqlObject->save_body_data($p_replace);
					$result = $result === 0 ? -1 : $result;
					break;
				
				case 'delete':
					$result = $p_sqlObject->delete();
					break;
				
				default:
					$result = false;
					break;
			}
		}
		
		return $result;
	}


	/**
	 * Generates the XML object for additional images in the response
	 * 
	 * @param $p_responseArray
	 *
	 * @return SimpleXMLElement
	 */
	protected function _generateAdditionalImagesResponseXml($p_responseArray)
	{
		$responseXmlContent = $this->gxmlHelper->getBlankXml();
		$responseXml = simplexml_load_string($responseXmlContent);

		if(isset($p_responseArray['external_image_id']))
		{
			$responseXml->addChild('external_image_id', $p_responseArray['external_image_id']);
		}
		$responseXml->addChild('image_id', $p_responseArray['image_id']);
		$responseXml->addChild('success', $p_responseArray['success']);
		$responseXml->addChild('errormessage', $p_responseArray['errormessage']);
		$responseXml->addChild('action_performed', $p_responseArray['action_performed']);

		return $responseXml;
	}


	/**
	 * Add shipping times node to current response XML object. 
	 */
	protected function _addShippingTimes()
	{
		$shippingTimes = $this->responseXml->addChild('shipping_times');
		$query = "SELECT 
						shipping_status_id,
						language_id,
						shipping_status_name,
						number_of_days,
						shipping_quantity
					FROM " . TABLE_SHIPPING_STATUS . "
					ORDER BY
						shipping_status_id,
						language_id";
		$result = xtc_db_query($query);
		$shippingStatusId = 0;
		
		while($resultArray = xtc_db_fetch_array($result))
		{
			if($resultArray['shipping_status_id'] != $shippingStatusId)
			{
				$shippingTime = $shippingTimes->addChild('shipping_time');
				$shippingTime->addChild('shipping_time_id', $resultArray['shipping_status_id']);
				$shippingTime->addChild('number_of_days', $resultArray['number_of_days']);
				$shippingTime->addChild('quantity', $resultArray['shipping_quantity']);
			}
						
			$name = $shippingTime->addChild('name', $this->wrapValue($resultArray['shipping_status_name']));
			$name->addAttribute('language_id', $resultArray['language_id']);
			$name->addAttribute('language_iso', $this->languageArray[$resultArray['language_id']]['iso']);
						
			$shippingStatusId = $resultArray['shipping_status_id'];
		}
	}

	
	/**
	 * Add tax classes node to current response XML object.
	 */
	protected function _addTaxClasses()
	{
		$this->_loadTaxes();
		
		$taxClasses = $this->responseXml->addChild('tax_classes');
		foreach($this->taxArray as $t_tax_class)
		{
			$taxClass = $taxClasses->addChild('tax_class');
			$taxClass->addChild('tax_class_id', $t_tax_class['tax_class_id']);
			$taxClass->addChild('title', $this->wrapValue($t_tax_class['title']));
			$description = $taxClass->addChild('description', $this->wrapValue($t_tax_class['description']));
			$description->addAttribute('type', 2);
			$taxClass->addChild('tax_rate', $t_tax_class['tax_rate']);
			$taxClass->addChild('date_added', $t_tax_class['date_added']);
			$taxClass->addChild('last_modified', $t_tax_class['last_modified']);
		}
		
		return $taxClasses;
	}

	
	/**
	 * Add quantity units node to current response XML object.
	 */
	protected function _addQuantityUnits()
	{
		$quantityUnits = $this->responseXml->addChild('quantity_units');
		$query = "SELECT 
						quantity_unit_id,
						language_id,
						unit_name
					FROM quantity_unit_description
					ORDER BY
						quantity_unit_id,
						language_id";
		$result = xtc_db_query($query);
		
		$quantityUnitId = 0;
		while($resultArray = xtc_db_fetch_array($result))
		{
			if($resultArray['quantity_unit_id'] != $quantityUnitId)
			{
				$quantityUnit = $quantityUnits->addChild('quantity_unit');
				$quantityUnit->addChild('quantity_unit_id', $resultArray['quantity_unit_id']);
			}
						
			$name = $quantityUnit->addChild('name', $this->wrapValue($resultArray['unit_name']));
			$name->addAttribute('language_id', $resultArray['language_id']);
			$name->addAttribute('language_iso', $this->languageArray[$resultArray['language_id']]['iso']);
						
			$quantityUnitId = $resultArray['quantity_unit_id'];
		}
	}

	
	/**
	 * Add base price units node to current response XML object.
	 */
	protected function _addBasePriceUnits()
	{
		$basePriceUnits = $this->responseXml->addChild('base_price_units');
		$query = "SELECT 
						products_vpe_id,
						language_id,
						products_vpe_name
					FROM " . TABLE_PRODUCTS_VPE . "
					ORDER BY
						products_vpe_id,
						language_id";
		$result = xtc_db_query($query);
		$productsVpeId = 0;
		while($resultArray = xtc_db_fetch_array($result))
		{
			if($resultArray['products_vpe_id'] != $productsVpeId)
			{
				$basePriceUnit = $basePriceUnits->addChild('base_price_unit');
				$basePriceUnit->addChild('base_price_unit_id', $resultArray['products_vpe_id']);
			}
						
			$name = $basePriceUnit->addChild('name', $this->wrapValue($resultArray['products_vpe_name']));
			$name->addAttribute('language_id', $resultArray['language_id']);
			$name->addAttribute('language_iso', $this->languageArray[$resultArray['language_id']]['iso']);
						
			$productsVpeId = $resultArray['products_vpe_id'];
		}
	}


	/**
	 * Add customer groups node to current response XML object.
	 */
	protected function _addCustomerGroups()
	{
		$customerGroups = $this->responseXml->addChild('customer_groups');
		$query = "SELECT 
						customers_status_id,
						language_id,
						customers_status_name,
						customers_status_show_price_tax
					FROM " . TABLE_CUSTOMERS_STATUS . "
					ORDER BY
						customers_status_id,
						language_id";
		$result = xtc_db_query($query);
		$customersStatusId = '';
		while($resultArray = xtc_db_fetch_array($result))
		{
			if($resultArray['customers_status_id'] != $customersStatusId)
			{
				$customerGroup = $customerGroups->addChild('customer_group');
				$customerGroup->addChild('customer_status_id', $resultArray['customers_status_id']);
				$customerGroup->addChild('include_taxes', $resultArray['customers_status_show_price_tax']);
			}
						
			$name = $customerGroup->addChild('name', $this->wrapValue($resultArray['customers_status_name']));
			$name->addAttribute('language_id', $resultArray['language_id']);
			$name->addAttribute('language_iso', $this->languageArray[$resultArray['language_id']]['iso']);
						
			$customersStatusId = $resultArray['customers_status_id'];
		}
	}

	
	/**
	 * Add languages node to current response XML object.
	 */
	protected function _addLanguages()
	{
		$languages = $this->responseXml->addChild('languages');

		foreach ($this->languageArray as $languageId => $languageData)
		{
			$language = $languages->addChild('language');
			$language->addChild('language_id', $languageId);
			$language->addChild('name', $this->wrapValue($languageData['language_name']));
			$language->addChild('iso', $languageData['iso']);
			$language->addChild('charset', $languageData['charset']);
		}
	}

	
	/**
	 * Add order status node to current response XML object.
	 */
	protected function _addOrderStatus()
	{
		$orderStatuses = $this->responseXml->addChild('order_statuses');

		$query = '
				SELECT 
					orders_status_id,
					language_id,
					orders_status_name
				FROM ' . TABLE_ORDERS_STATUS. '
				ORDER BY
					orders_status_id, language_id';
		$result = xtc_db_query($query);
		$orderStatusId = '';

		while($resultArray = xtc_db_fetch_array($result))
		{
			if($resultArray['orders_status_id'] != $orderStatusId)
			{
				$orderStatus = $orderStatuses->addChild('order_status');
				$orderStatus->addChild('orders_status_id', $resultArray['orders_status_id']);
			}
			$name = $orderStatus->addChild('name', $this->wrapValue($resultArray['orders_status_name']));
			$name->addAttribute('language_id', $resultArray['language_id']);
			$name->addAttribute('language_iso', $this->languageArray[$resultArray['language_id']]['iso']);

			$orderStatusId = $resultArray['orders_status_id'];
		}
	}


	/**
	 * Load all language records from the database. 
	 * 
	 * This method will store all the language records from the database into an 
	 * array for future reference.
	 */
	protected function _loadLanguages()
	{
		if (empty($this->languageArray))
		{
			$this->languageArray = array();
			
			$query = "SELECT 
							languages_id,
							name,
							code,
							language_charset
						FROM " . TABLE_LANGUAGES. "
						ORDER BY
							languages_id";
			$result = xtc_db_query($query);
			
			while($resultArray = xtc_db_fetch_array($result))
			{
				$this->languageArray[$resultArray['languages_id']] = array();
				$this->languageArray[$resultArray['languages_id']]['language_name'] = $resultArray['name'];
				$this->languageArray[$resultArray['languages_id']]['iso'] = $resultArray['code'];
				$this->languageArray[$resultArray['languages_id']]['charset'] = $resultArray['language_charset'];
			}
		}
	}


	/**
	 * Load all tax related records from database. 
	 * 
	 * This method will load all tax related records from the database and store them
	 * into a private array for future reference. 
	 */
	protected function _loadTaxes()
	{
		if (empty($this->taxArray))
		{
			$this->taxArray = array();
			
			$t_sql = "SELECT 
						tax_class.tax_class_id,
						tax_class.tax_class_title,
						tax_rates.tax_rate,
						tax_class.tax_class_description,
						tax_class.last_modified,
						tax_class.date_added
					FROM " . TABLE_TAX_CLASS . " tax_class ," . TABLE_TAX_RATES . "  tax_rates
					WHERE 
						tax_class.tax_class_id = tax_rates.tax_class_id AND
						tax_rates.tax_zone_id = " . (int)xtc_get_geo_zone_code(STORE_COUNTRY) . "
					ORDER BY
						tax_class.tax_class_id";
			$t_result = xtc_db_query($t_sql);
			
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$this->taxArray[$t_result_array['tax_class_id']] = array();
				$this->taxArray[$t_result_array['tax_class_id']]['tax_class_id'] = $t_result_array['tax_class_id'];
				$this->taxArray[$t_result_array['tax_class_id']]['title'] = $t_result_array['tax_class_title'];
				$this->taxArray[$t_result_array['tax_class_id']]['description'] = $t_result_array['tax_class_description'];
				$this->taxArray[$t_result_array['tax_class_id']]['tax_rate'] = $t_result_array['tax_rate'];
				$this->taxArray[$t_result_array['tax_class_id']]['date_added'] = $t_result_array['date_added'];
				$this->taxArray[$t_result_array['tax_class_id']]['last_modified'] = $t_result_array['last_modified'];
			}
		}
	}


	/**
	 * Find the language id that matches the given language ISO. 
	 * 
	 * @param string $p_languageIso ISO string that is going to be used for the search.
	 *
	 * @return numeric Returns the language id value. 
	 */
	protected function _resolveLanguageIso($p_languageIso)
	{
		foreach ($this->languageArray as $languageId => $languageData)
		{
			if ($languageData['iso'] == $p_languageIso)
			{
				return $languageId;
			}
		}
		return false;
	}


	/**
	 * Add a general purpose node to the response XML object. 
	 * 
	 * This is a general purpose method that will call the sub-methods who will
	 * eventually include the XML nodes to the response XML object. 
	 * 
	 * @param array $nodeTypes Contains the node types to be added ("languages", "customer_groups", 
	 *                         "shipping_times", "tax_classes", "base_price_units", "quantity_units", 
	 *                         "order_status"). 
	 */
	protected function _includeNode(array $nodeTypes)
	{
		foreach($nodeTypes as $type)
		{
			switch($type)
			{
				case 'languages':
					$this->_addLanguages();
					break;
				
				case 'customer_groups':
					$this->_addCustomerGroups();
					break;
				
				case 'shipping_times':
					$this->_addShippingTimes();
					break;
				
				case 'tax_classes':
					$this->_addTaxClasses();
					break;
				
				case 'base_price_units':
					$this->_addBasePriceUnits();
					break;
				
				case 'quantity_units':
					$this->_addQuantityUnits();
					break;
				
				case 'order_status':
					$this->_addOrderStatus();
					break;
			}
		}
	}


	/**
	 * Generate an SQL query string from the given parameters. 
	 * 
	 * This method will generate an array that contains the query sections that 
	 * correspond to the required $types array. 
	 * 
	 * @param SimpleXMLElement $parametersXml Parameters XML object provided by the API client. 
	 * @param array $types Contains the query values that will be included into the result. 
	 *
	 * @return array Returns an array with the query string values. 
	 */
	protected function _generateSqlStrings(SimpleXMLElement $parametersXml, array $types)
	{
		$queryStrings = array();
		
		foreach($types AS $type)
		{
			switch($type)
			{
				case 'limit':
					
					if(isset($parametersXml->limit))
					{
						if(!is_numeric((string)$parametersXml->limit)) // convert to string in order to check if this is really a numeric value
						{
							throw new InvalidArgumentException('Invalid limit value provided: ' . $parametersXml->limit); 
						}
						
						if((int)$parametersXml->limit > 0)
						{
							$queryStrings['limit'] = (int) $parametersXml->limit;	
						}

						if(isset($parametersXml->offset))
						{
							if(!is_numeric((string)$parametersXml->offset)) // convert to string in order to check if this is really a numeric value
							{
								throw new InvalidArgumentException('Invalid offset value provided: ' . $parametersXml->offset);
							}
							$queryStrings['offset'] = (int) $parametersXml->offset;
						}
					}
					break;
				
				case 'where':
					if(isset($parametersXml->{$this->_getPluralName()}) && isset($parametersXml->{$this->_getPluralName()}->{$this->_getSingularName()}))
					{
						$singularNotes = $parametersXml->{$this->_getPluralName()}->{$this->_getSingularName()};

						for($i=0; $i<count($singularNotes); $i++)
						{
							$mapperKeyArray = $this->_generatePathArray($singularNotes[$i]);

							for($j=0; $j<count($mapperKeyArray); $j++)
							{
								if(isset($this->mapperArray[$mapperKeyArray[$j]]) && $this->mapperArray[$mapperKeyArray[$j]] != '')
								{
									$node = $singularNotes[$i]->xpath($mapperKeyArray[$j]);
									$queryStrings['where_parts'][$i][$this->tableMapperArray[$mapperKeyArray[$j]] 
																	 . '.' . $this->mapperArray[$mapperKeyArray[$j]]] = (string)$node[0];
								}	
							}				
						}
					}
					break;
			}
		}
		return $queryStrings;
	}


	/**
	 * Generate a path array
	 * 
	 * @param SimpleXMLElement $xml XML object to be used for the calculation of the path. 
	 * @param string $p_path (Optional) 
	 *
	 * @return array Returns the generated array. 
	 */
	protected function _generatePathArray(SimpleXMLElement $xml, $p_path = '')
	{
		$pathArray = array();
		
		foreach($xml->children() AS $childName=>$childNode)
		{
			$subChildCount = count($childNode->children());
			
			if($subChildCount > 0)
			{
				$pathArray = array_merge($pathArray, $this->_generatePathArray($childNode, $p_path . $childName . '/'));
			}
			else
			{
				$pathArray[] = $p_path . $childName;
			}
		}
		
		return $pathArray;
	}
}