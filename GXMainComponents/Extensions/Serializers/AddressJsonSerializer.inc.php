<?php
/* --------------------------------------------------------------
   AddressJsonSerializer.inc.php 2016-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class AddressJsonSerializer
 *
 * This class will serialize and deserialize an address entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class AddressJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize address object to JSON string.
	 *
	 * @param CustomerAddressInterface $object Contains the address data.
	 * @param bool                     $encode (optional) Whether to json_encode the result of the method (default
	 *                                         true). Sometimes it might be required to encode an array of multiple
	 *                                         customer records together and not one by one.
	 *
	 * @return string|array Returns the json encoded address (string) or an array that can be easily encoded into a
	 *                      JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'CustomerAddressInterface'))
		{
			throw new InvalidArgumentException('Invalid argument provided, CustomerAddressInterface object required: '
			                                   . get_class($object));
		}
		
		$address = array(
			'id'                    => ($object->getId() !== null) ? (int)(string)$object->getId() : null,
			'customerId'            => ($object->getCustomerId()
			                            !== null) ? (int)(string)$object->getCustomerId() : null,
			'gender'                => ($object->getGender() !== null) ? (string)$object->getGender() : null,
			'company'               => ($object->getCompany() !== null) ? (string)$object->getCompany() : null,
			'firstname'             => ($object->getFirstname() !== null) ? (string)$object->getFirstname() : null,
			'lastname'              => ($object->getLastname() !== null) ? (string)$object->getLastname() : null,
			'street'                => ($object->getStreet() !== null) ? (string)$object->getStreet() : null,
			'houseNumber'           => ($object->getHouseNumber() !== null) ? (string)$object->getHouseNumber() : null,
			'additionalAddressInfo' => ($object->getAdditionalAddressInfo()
			                            !== null) ? (string)$object->getAdditionalAddressInfo() : null,
			'suburb'                => ($object->getSuburb() !== null) ? (string)$object->getSuburb() : null,
			'postcode'              => ($object->getPostcode() !== null) ? (string)$object->getPostcode() : null,
			'city'                  => ($object->getCity() !== null) ? (string)$object->getCity() : null,
			'countryId'             => ($object->getCountry() !== null) ? (int)(string)$object->getCountry()
			                                                                                  ->getId() : null,
			'zoneId'                => ($object->getCountryZone() !== null) ? (int)(string)$object->getCountryZone()
			                                                                                      ->getId() : null,
			'class'                 => ($object->getAddressClass()
			                            !== null) ? (string)$object->getAddressClass() : null,
			'b2bStatus'             => ($object->getB2BStatus() !== null) ? $object->getB2BStatus()->getStatus() : null
		);

		return ($encode) ? $this->jsonEncode($address) : $address;
	}


	/**
	 * Deserialize address JSON string.
	 *
	 * @param string $string     JSON string that contains the data of the address.
	 * @param object $baseObject (optional) If provided, this will be the base object to be updated
	 *                           and no new instance will be created.
	 *
	 * @return CustomerAddressInterface Returns the deserialized Address object.
	 * @throws InvalidArgumentException If the argument is not a string or is empty.
	 */
	public function deserialize($string, $baseObject = null)
	{
		if(!is_string($string) || empty($string))
		{
			throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
		}

		$json = json_decode($string); // error for malformed json strings

		if($json === null && json_last_error() > 0)
		{
			throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
		}

		if(!$baseObject)
		{
			$address = MainFactory::create('CustomerAddress');
		}
		else
		{
			$address = $baseObject;
		}

		// Deserialize Json String

		if($json->id !== null)
		{
			$address->setId(new IdType((int)$json->id));
		}

		if($json->customerId !== null)
		{
			$address->setCustomerId(new IdType((int)$json->customerId));
		}

		if($json->gender !== null)
		{
			$address->setGender(MainFactory::create('CustomerGender', $json->gender));
		}

		if($json->company !== null)
		{
			$address->setCompany(MainFactory::create('CustomerCompany', $json->company));
		}

		if($json->firstname !== null)
		{
			$address->setFirstname(MainFactory::create('CustomerFirstname', $json->firstname));
		}

		if($json->lastname !== null)
		{
			$address->setLastname(MainFactory::create('CustomerLastname', $json->lastname));
		}

		if($json->street !== null)
		{
			$address->setStreet(MainFactory::create('CustomerStreet', $json->street));
		}
		
		if($json->houseNumber !== null)
		{
			$address->setHouseNumber(MainFactory::create('CustomerHouseNumber', $json->houseNumber));
		}
		
		if($json->additionalAddressInfo !== null)
		{
			$address->setAdditionalAddressInfo(MainFactory::create('CustomerAdditionalAddressInfo',
			                                                       $json->additionalAddressInfo));
		}

		if($json->suburb !== null)
		{
			$address->setSuburb(MainFactory::create('CustomerSuburb', $json->suburb));
		}

		if($json->postcode !== null)
		{
			$address->setPostcode(MainFactory::create('CustomerPostcode', $json->postcode));
		}

		if($json->city !== null)
		{
			$address->setCity(MainFactory::create('CustomerCity', $json->city));
		}

		// Fetch country and zone by ID

		$countryService = StaticGXCoreLoader::getService('Country');

		if($json->countryId !== null)
		{
			$country = $countryService->getCountryById(new IdType((int)$json->countryId));
			$address->setCountry($country);
		}

		if($json->zoneId !== null)
		{
			$zone = $countryService->getCountryZoneById(new IdType((int)$json->zoneId));
			$address->setCountryZone($zone);
		}

		if($json->class !== null)
		{
			$address->setAddressClass(MainFactory::create('AddressClass', $json->class));
		}

		if($json->b2bStatus !== null)
		{
			$address->setB2BStatus(MainFactory::create('CustomerB2BStatus', (bool)$json->b2bStatus));
		}

		return $address;
	}
}