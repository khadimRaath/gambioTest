<?php
/* --------------------------------------------------------------
   AbstractJsonSerializer.inc.php 2016-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('SerializerInterface');

/**
 * Abstract Json Serializer
 *
 * Serializers that extend this class should parse and encode entities
 * so that they can be used in the shop's APIs.
 *
 * Serialization must follow the "null" approach in order to enhance response clarity.
 * That means that serializers must provide a null value than an empty string or an omitted node.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
abstract class AbstractJsonSerializer implements SerializerInterface
{
	/**
	 * Used for the resources that require multiple languages.
	 * 
	 * @var LanguageProviderInterface $languageProvider
	 */
	protected $languageProvider;
	
	
	/**
	 * AbstractJsonSerializer Constructor
	 * 
	 * If you override this constructor do not forget to call it from the child class.
	 */
	public function __construct()
	{
		$this->languageProvider = MainFactory::create('LanguageProvider',
		                                              StaticGXCoreLoader::getDatabaseQueryBuilder());
	}
	
	
	abstract public function serialize($object, $encode = true);
	
	
	abstract public function deserialize($string, $baseObject = null);
	
	
	/**
	 * JSON Encode Wrapper
	 *
	 * This function provides PHP v5.3 compatibility and it should be used when serialized objects
	 * need to be encoded directly from the serializer instance.
	 *
	 * @param array $data Contains the data to be JSON encoded.
	 *
	 * @return string Returns the encoded JSON string that represents the data.
	 */
	public function jsonEncode(array $data)
	{
		if(defined(JSON_PRETTY_PRINT) && defined(JSON_UNESCAPED_SLASHES))
		{
			$dataJsonString = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		else
		{
			$dataJsonString = json_encode($data); // PHP v5.3
		}
		
		return $dataJsonString;
	}


	/**
	 * Serialize Addon Values 
	 * 
	 * Common method for serializing addon values in various resource serializer classes.
	 * 
	 * @param \KeyValueCollection $addonValues
	 *
	 * @return array
	 */
	protected function _serializeAddonValues(KeyValueCollection $addonValues)
	{
		if($addonValues->count())
		{
			$addonValuesArray = array();
			foreach($addonValues->getArray() as $key => $value)
			{
				$addonValuesArray[$key] = $value;
			}
		}
		else
		{
			$addonValuesArray = null;
		}
		
		return $addonValuesArray;
	}


	/**
	 * Deserialize Addon Values
	 *
	 * Common method for deserializing addon values in various resource serializer classes.
	 *
	 * @param $json
	 *
	 * @return array
	 */
	protected function _deserializeAddonValues($json)
	{
		$itemAddonValuesArray = array();
		
		foreach($json as $propertyKey => $propertyValue)
		{
			$itemAddonValuesArray[$propertyKey] = $propertyValue;
		}
		
		return $itemAddonValuesArray;
	}


	/**
	 * Serialize Language Specific Property
	 * 
	 * In order for this method to work there has to be a proper getter method in the object instance. Otherwise
	 * a RuntimeException will be thrown. 
	 * 
	 * @param mixed $object The object instance containing the property.
	 * @param string $property The property name to be serialized. 
	 *
	 * @return array
	 * 
	 * @throws \RuntimeException If there is no getter for the provided property.
	 */
	protected function _serializeLanguageSpecificProperty($object, $property)
	{
		$method   = 'get' . ucfirst($property);
		$resource = array($object, $method);
		
		if(!is_callable($resource))
		{
			throw new \RuntimeException('The requested resource is not supported.', 400);
		}
		
		$propertyArray = array();
		
		foreach($this->languageProvider->getCodes()->getArray() as $languageCode)
		{
			$propertyArray[strtolower($languageCode->asString())] = call_user_func($resource, $languageCode); 
		}
		
		return $propertyArray;
	}


	/**
	 * Deserialize Language Specific Property 
	 * 
	 * This method will deserialize the value of a JSON property and set the value to the 
	 * object by using the corresponding setter method. 
	 * 
	 * @param mixed $object The object being deserialized. 
	 * @param stdobject $json The JSON object containing the property value. 
	 * @param string $property The property name to be deserialized.
	 * 
	 * @throws \RuntimeException If the setter method does not exist.
	 */
	protected function _deserializeLanguageSpecificProperty($object, $json, $property, $type = 'StringType')
	{
		$method   = 'set' . ucfirst($property);
		$resource = array($object, $method);
		
		if(!is_callable($resource))
		{
			throw new RuntimeException('The requested resource is not supported.', 400);
		}
		
		if ($json === null) 
		{
			return; // The provided $json object is empty. 
		}
		
		foreach($json as $languageCode => $value)
		{
			$valueInstance = new $type($value);
			
			call_user_func($resource, $valueInstance, new LanguageCode(new StringType($languageCode)));
		}
	}
}