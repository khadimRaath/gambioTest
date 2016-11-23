<?php

/* --------------------------------------------------------------
   OrderListItemJsonSerializer.inc.php 2016-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class OrderListItemJsonSerializer
 *
 * This class will serialize and deserialize an OrderListItem entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class OrderListItemJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize an OrderListItem object to a JSON string.
	 *
	 * @param OrderListItem $object Object instance to be serialized.
	 * @param bool          $encode (optional) Whether to json_encode the result of the method (default true). Sometimes
	 *                              it might be required to encode an array of multiple customer records together and
	 *                              not one by one.
	 *
	 * @return string|array Returns the json encoded order list item (string) or an array that can be easily encoded
	 *                      into a JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'OrderListItem'))
		{
			throw new InvalidArgumentException('Invalid argument provided, OrderListItem object required: '
			                                   . get_class($object));
		}
		
		$orderListItem = array(
			'id'                 => $object->getOrderId(),
			'statusId'           => $object->getOrderStatusId(),
			'statusName'         => $object->getOrderStatusName(),
			'totalSum'           => $object->getTotalSum(),
			'purchaseDate'       => $object->getPurchaseDateTime()->format('Y-m-d H:i:s'),
			'comment'            => $object->getComment(),
			'withdrawalIds'      => $this->_serializeWithDrawalIds($object->getWithdrawalIds()),
			'mailStatus'         => $object->getMailStatus(),
			'customerId'         => $object->getCustomerId(),
			'customerName'       => $object->getCustomerName(),
			'customerEmail'      => $object->getCustomerEmail(),
			'customerStatusId'   => $object->getCustomerStatusId(),
			'customerStatusName' => $object->getCustomerStatusName(),
			'customerMemos'      => $this->_serializeCustomerMemos($object->getCustomerMemos()),
			'deliveryAddress'    => $this->_serializeOrderAddressBlock($object->getDeliveryAddress()),
			'billingAddress'     => $this->_serializeOrderAddressBlock($object->getBillingAddress()),
			'paymentType'        => array(
				'title'  => $object->getPaymentType()->getTitle(),
				'module' => $object->getPaymentType()->getModule()
			),
			'shippingType'       => array(
				'title'  => $object->getShippingType()->getTitle(),
				'module' => $object->getShippingType()->getModule()
			),
			'trackingLinks'      => $object->getTrackingLinks()->getStringArray(),
		);
		
		return $encode ? $this->jsonEncode($orderListItem) : $orderListItem;
	}
	
	
	/**
	 * Deserialize method is not used by the api.
	 *
	 * @param string   $string     JSON string that contains the data of the address.
	 * @param stdClass $baseObject (optional) This parameter is not supported for this serializer because the
	 *                             OrderListItem does not have any setter methods.
	 *
	 * @throws RuntimeException If the argument is not a string or is empty.
	 */
	public function deserialize($string, $baseObject = null)
	{
		throw new RuntimeException('Method is not implemented');
	}
	
	
	/**
	 * Serialize Customer Memo Collection
	 *
	 * @param CustomerMemoCollection $customerMemoCollection
	 *
	 * @return array
	 */
	protected function _serializeCustomerMemos(CustomerMemoCollection $customerMemoCollection)
	{
		$customerMemoCollectionArray = [];
		
		/** @var CustomerMemo $customerMemo */
		foreach($customerMemoCollection->getArray() as $customerMemo)
		{
			$customerMemoCollectionArray[] = [
				'title'    => $customerMemo->getTitle(),
				'text'     => $customerMemo->getText(),
				'date'     => $customerMemo->getCreationDate()->format('Y-m-d H:i:s'),
				'posterId' => $customerMemo->getPosterId()
			];
		}
		
		return $customerMemoCollectionArray;
	}
	
	
	/**
	 * Serialize Order AddressBlock
	 *
	 * @param OrderAddressBlock $orderAddressBlock
	 *
	 * @return array
	 */
	protected function _serializeOrderAddressBlock(OrderAddressBlock $orderAddressBlock)
	{
		$orderAddressBlockArray = [
			'firstName'             => $orderAddressBlock->getFirstName(),
			'lastName'              => $orderAddressBlock->getLastName(),
			'company'               => $orderAddressBlock->getCompany(),
			'street'                => $orderAddressBlock->getStreet(),
			'houseNumber'           => $orderAddressBlock->getHouseNumber(),
			'additionalAddressInfo' => $orderAddressBlock->getAdditionalAddressInfo(),
			'postcode'              => $orderAddressBlock->getPostcode(),
			'city'                  => $orderAddressBlock->getCity(),
			'state'                 => $orderAddressBlock->getState(),
			'country'               => $orderAddressBlock->getCountry(),
			'countryIsoCode'        => $orderAddressBlock->getCountryIsoCode()
		];
		
		return $orderAddressBlockArray;
	}
	
	
	/**
	 * Serialize WithdrawalIds Collection
	 *
	 * @param IdCollection $withdrawalIds
	 *
	 * @return array
	 */
	protected function _serializeWithdrawalIds(IdCollection $withdrawalIds)
	{
		$withdrawalIdsArray = [];
		
		foreach($withdrawalIds->getArray() as $withdrawalId)
		{
			$withdrawalIdsArray[] = $withdrawalId->asInt();
		}
		
		return $withdrawalIdsArray;
	}
}