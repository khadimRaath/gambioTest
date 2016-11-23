<?php
/* --------------------------------------------------------------
   CustomerJsonSerializer.inc.php 2016-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class CustomerJsonSerializer
 *
 * This class will serialize and deserialize a customer entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class CustomerJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize customer object to json string.
	 *
	 * Important:
	 * Password value will not be included in the serialized object.
	 *
	 * @param CustomerInterface $object Contains the customer data.
	 * @param bool              $encode (optional) Whether to json_encode the result of the method (default true).
	 *                                  Sometimes it might be required to encode an array of multiple customer records
	 *                                  together and not one by one.
	 *
	 * @return string|array Returns the json encoded customer (string) or an array that can be easily encoded into a
	 *                      JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'CustomerInterface'))
		{
			throw new InvalidArgumentException('Invalid argument provided, CustomerInterface object required: '
			                                   . get_class($object));
		}

		$address = $object->getDefaultAddress();

		$customer = array(
				'id'              => ($object->getId() !== null) ? (int)(string)$object->getId() : null,
				'number'          => ($object->getCustomerNumber()
				                      !== null) ? (string)$object->getCustomerNumber() : null,
				'gender'          => ($object->getGender() !== null) ? (string)$object->getGender() : null,
				'firstname'       => ($object->getFirstname() !== null) ? (string)$object->getFirstname() : null,
				'lastname'        => ($object->getLastname() !== null) ? (string)$object->getLastname() : null,
				'dateOfBirth'     => ($object->getDateOfBirth() !== null) ? $object->getDateOfBirth()
				                                                                   ->format('Y-m-d') : null,
				'vatNumber'       => ($object->getVatNumber() !== null) ? (string)$object->getVatNumber() : null,
				'vatNumberStatus' => ($object->getVatNumberStatus() !== null) ? $object->getVatNumberStatus() : null,
				'telephone'       => ($object->getTelephoneNumber()
				                      !== null) ? (string)$object->getTelephoneNumber() : null,
				'fax'             => ($object->getFaxNumber() !== null) ? (string)$object->getFaxNumber() : null,
				'email'           => ($object->getEmail() !== null) ? (string)$object->getEmail() : null,
				'statusId'        => ($object->getStatusId() !== null) ? $object->getStatusId() : null,
				'isGuest'         => $object->isGuest(),
				'addressId'       => ($address !== null) ? (int)(string)$address->getId() : null,
				'addonValues'     => ($object->getAddonValues()->count()) ? $object->getAddonValues()->getArray() : null
		);

		return ($encode === true) ? $this->jsonEncode($customer) : $customer;
	}


	/**
	 * Deserialize customer JSON string.
	 *
	 * @param string $string     JSON string that contains the data of the customer.
	 * @param object $baseObject (optional) If provided, this will be the base object to be updated
	 *                           and no new instance will be created.
	 *
	 * @return CustomerInterface Returns the deserialized Customer object.
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

		$customerFactory = MainFactory::create('CustomerFactory');

		if(!$baseObject)
		{
			$customer = $customerFactory->createCustomer();
		}
		else
		{
			$customer = $baseObject;
		}

		// Parse Main Information

		if($json->id !== null)
		{
			$customer->setId(new IdType($json->id));
		}

		if($json->number !== null)
		{
			$customer->setCustomerNumber(MainFactory::create('CustomerNumber', $json->number));
		}

		if($json->gender !== null)
		{
			$customer->setGender(MainFactory::create('CustomerGender', $json->gender));
		}

		if($json->firstname !== null)
		{
			$customer->setFirstname(MainFactory::create('CustomerFirstname', $json->firstname));
		}

		if($json->lastname !== null)
		{
			$customer->setLastname(MainFactory::create('CustomerLastname', $json->lastname));
		}

		if($json->dateOfBirth !== null)
		{
			$customer->setDateOfBirth(MainFactory::create('DateTime', $json->dateOfBirth));
		}

		if($json->vatNumber !== null)
		{
			$customer->setVatNumber(MainFactory::create('CustomerVatNumber', $json->vatNumber));
		}

		if($json->vatNumberStatus !== null)
		{
			$customer->setVatNumberStatus((int)$json->vatNumberStatus);
		}

		if($json->telephone !== null)
		{
			$customer->setTelephoneNumber(MainFactory::create('CustomerCallNumber', $json->telephone));
		}

		if($json->fax !== null)
		{
			$customer->setFaxNumber(MainFactory::create('CustomerCallNumber', $json->fax));
		}

		if($json->email !== null)
		{
			$customer->setEmail(MainFactory::create('CustomerEmail', $json->email));
		}

		if($json->password !== null)
		{
			$customer->setPassword(MainFactory::create('CustomerPassword', $json->password));
		}

		if($json->statusId !== null)
		{
			$customer->setStatusId((int)$json->statusId);
		}

		if($json->isGuest !== null)
		{
			$customer->setGuest((bool)$json->isGuest);
		}

		if($json->addressId !== null)
		{
			$addressService = StaticGXCoreLoader::getService('AddressBook');
			$address        = $addressService->findAddressById(new IdType((int)$json->addressId));
			if($address !== null) // it is possible that the given address ID does not exist
			{
				$customer->setDefaultAddress($address);
			}
		}
		
		if($json->addonValues !== null)
		{
			$customer->addAddonValues(MainFactory::create('KeyValueCollection',
			                                              json_decode(json_encode($json->addonValues), true)));
		}

		// Returned deserialized Customer object.
		return $customer;
	}
}