<?php
/* --------------------------------------------------------------
   CountryJsonSerializer.inc.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class CountryJsonSerializer
 *
 * This class will serialize and deserialize a country entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class CountryJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize country object to json string.
	 *
	 * @param CustomerCountryInterface $object Contains the country data.
	 * @param bool                     $encode (optional) Whether to json_encode the result of the method (default
	 *                                         true). Sometimes it might be required to encode an array of multiple
	 *                                         country records together and not one by one.
	 *
	 * @return string|array Returns the json encoded country (string) or an array that can be easily encoded into a
	 *                      JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'CustomerCountryInterface'))
		{
			throw new InvalidArgumentException('Invalid argument provided, CustomerCountryInterface object required: '
			                                   . get_class($object));
		}

		$country = array(
				'id'              => (int)(string)$object->getId(),
				'name'            => (string)$object->getName(),
				'iso2'            => (string)$object->getIso2(),
				'iso3'            => (string)$object->getIso3(),
				'addressFormatId' => (int)(string)$object->getAddressFormatId(),
				'status'          => $object->getStatus()
		);

		return ($encode) ? $this->jsonEncode($country) : $country;
	}


	/**
	 * Deserialize country JSON string.
	 *
	 * NOTICE #1: The CountryService does not support the addition of new countries so the
	 * CustomerCountry object does not have any setters, rather only getters. So the $baseObject
	 * parameter is not used at all.
	 *
	 * NOTICE #2: The provided JSON string must contain all the properties of the country
	 * otherwise the object will not be deserialized.
	 *
	 * @param string $string     JSON string that contains the data of the country.
	 * @param object $baseObject (optional) It is not used within the countries context.
	 *
	 * @return CustomerCountryInterface Returns the deserialized CustomerCountry object.
	 * @throws InvalidArgumentException If the argument is not a string or is empty.
	 */
	public function deserialize($string, $baseObject = null)
	{
		if(!is_string($string) || empty($string))
		{
			throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
		}

		$json = json_decode($string);

		if($json === null && json_last_error() > 0)
		{
			throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
		}

		$id              = new IdType((int)$json->id);
		$name            = MainFactory::create('CustomerCountryName', (string)$json->name);
		$iso2            = MainFactory::create('CustomerCountryIso2', (string)$json->iso2);
		$iso3            = MainFactory::create('CustomerCountryIso3', (string)$json->iso3);
		$addressFormatId = new IdType((int)$json->addressFormatId);
		$status          = (bool)$json->status;

		$country = MainFactory::create('CustomerCountry', $id, $name, $iso2, $iso3, $addressFormatId, $status);

		return $country;
	}
}