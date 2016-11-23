<?php
/* --------------------------------------------------------------
   OrderJsonSerializer.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class OrderJsonSerializer
 *
 * This class will serialize and deserialize an Order entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class OrderJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize an Order object to a JSON string.
	 *
	 * @param OrderInterface $object           Object instance to be serialized.
	 * @param bool           $encode           (optional) Whether to json_encode the result of the method (default
	 *                                         true). Sometimes it might be required to encode an array of multiple
	 *                                         customer records together and not one by one.
	 *
	 * @return string|array Returns the json encoded order (string) or an array that can be easily encoded into a
	 *                      JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'OrderInterface'))
		{
			throw new InvalidArgumentException('Invalid argument provided, OrderInterface object required: '
			                                   . get_class($object));
		}
		
		$order = array(
			'id'            => $object->getOrderId(),
			'statusId'      => $object->getStatusId(),
			'purchaseDate'  => $object->getPurchaseDateTime()->format('Y-m-d H:i:s'),
			'currencyCode'  => $object->getCurrencyCode()->getCode(),
			'languageCode'  => (string)$object->getLanguageCode(),
			'comment'       => $object->getComment(),
			'totalWeight'   => $object->getTotalWeight(),
			'paymentType'   => array(
				'title'  => $object->getPaymentType()->getTitle(),
				'module' => $object->getPaymentType()->getModule()
			),
			'shippingType'  => array(
				'title'  => $object->getShippingType()->getTitle(),
				'module' => $object->getShippingType()->getModule()
			),
			'customer'      => array(
				'id'     => $object->getCustomerId(),
				'number' => $object->getCustomerNumber(),
				'email'  => $object->getCustomerEmail(),
				'phone'  => $object->getCustomerTelephone(),
				'vatId'  => $object->getVatIdNumber(),
				'status' => array(
					'id'       => $object->getCustomerStatusInformation()->getStatusId(),
					'name'     => $object->getCustomerStatusInformation()->getStatusName(),
					'image'    => $object->getCustomerStatusInformation()->getStatusImage(),
					'discount' => $object->getCustomerStatusInformation()->getStatusDiscount(),
					'isGuest'  => $object->getCustomerStatusInformation()->isGuest(),
				)
			),
			'addresses'     => array(
				'customer' => $this->serializeAddress($object->getCustomerAddress()),
				'billing'  => $this->serializeAddress($object->getBillingAddress()),
				'delivery' => $this->serializeAddress($object->getDeliveryAddress())
			),
			'items'         => array(),
			'totals'        => array(),
			'statusHistory' => array(),
			'addonValues'   => $this->_serializeAddonValues($object->getAddonValues())
		);
		
		foreach($object->getOrderItems()->getArray() as $orderItem)
		{
			$order['items'][] = $this->serializeOrderItem($orderItem);
		}
		
		foreach($object->getOrderTotals()->getArray() as $orderTotal)
		{
			$order['totals'][] = $this->serializeOrderTotal($orderTotal);
		}
		
		foreach($object->getStatusHistory()->getArray() as $statusHistoryListItem)
		{
			$order['statusHistory'][] = $this->serializeOrderStatusHistoryListItem($statusHistoryListItem);
		}
		
		return ($encode) ? $this->jsonEncode($order) : $order;
	}
	
	
	/**
	 * Deserialize an Order JSON String.
	 *
	 * @param string $string     JSON string that contains the data of the address.
	 * @param object $baseObject (optional) If provided, this will be the base object to be updated
	 *                           and no new instance will be created.
	 *
	 * @return GXEngineOrder Returns the deserialized Order object.
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
			$order = MainFactory::create('GXEngineOrder');
		}
		else
		{
			$order = $baseObject;
		}
		
		// Deserialize JSON String 
		
		if($json->id !== null)
		{
			$order->setOrderId(new IdType($json->id));
		}
		
		if($json->statusId !== null)
		{
			$order->setStatusId(new IdType($json->statusId));
		}
		
		if($json->purchaseDate !== null)
		{
			$order->setPurchaseDateTime(new EmptyDateTime($json->purchaseDate));
		}
		
		if($json->currencyCode !== null)
		{
			$order->setCurrencyCode(MainFactory::create('CurrencyCode', new NonEmptyStringType($json->currencyCode)));
		}
		
		if($json->languageCode !== null)
		{
			$order->setLanguageCode(new LanguageCode(new NonEmptyStringType($json->languageCode)));
		}
		
		if($json->totalWeight !== null)
		{
			$order->setTotalWeight(new DecimalType($json->totalWeight));
		}
		
		if($json->comment !== null)
		{
			$order->setComment(new StringType($json->comment));
		}
		
		if($json->paymentType !== null)
		{
			$orderPaymentType = MainFactory::create('OrderPaymentType', new StringType($json->paymentType->title),
			                                        new StringType($json->paymentType->module));
			$order->setPaymentType($orderPaymentType);
		}
		
		if($json->shippingType !== null)
		{
			$orderPaymentType = MainFactory::create('OrderShippingType', new StringType($json->shippingType->title),
			                                        new StringType($json->shippingType->module));
			$order->setShippingType($orderPaymentType);
		}
		
		if($json->customer !== null)
		{
			if($json->customer->id !== null)
			{
				$order->setCustomerId(new IdType($json->customer->id));
			}
			
			if($json->customer->number !== null)
			{
				$order->setCustomerNumber(new StringType($json->customer->number));
			}
			
			if($json->customer->email !== null)
			{
				$order->setCustomerEmail(new EmailStringType($json->customer->email));
			}
			
			if($json->customer->phone !== null)
			{
				$order->setCustomerTelephone(new StringType($json->customer->phone));
			}
			
			if($json->customer->vatId !== null)
			{
				$order->setVatIdNumber(new StringType($json->customer->vatId));
			}
			
			if($json->customer->status !== null)
			{
				$statusId       = new IdType($json->customer->status->id);
				$statusName     = new StringType($json->customer->status->name);
				$statusImage    = new StringType($json->customer->status->image);
				$statusDiscount = new DecimalType($json->customer->status->discount);
				$isGuest        = new BoolType($json->customer->status->isGuest);
				
				$customerStatusInformation = MainFactory::create('CustomerStatusInformation', $statusId, $statusName,
				                                                 $statusImage, $statusDiscount, $isGuest);
				
				$order->setCustomerStatusInformation($customerStatusInformation);
			}
		}
		
		if($json->addresses !== null)
		{
			if($json->addresses->customer !== null)
			{
				$order->setCustomerAddress($this->deserializeAddress($json->addresses->customer));
			}
			
			if($json->addresses->billing !== null)
			{
				$order->setBillingAddress($this->deserializeAddress($json->addresses->billing));
			}
			
			if($json->addresses->delivery !== null)
			{
				$order->setDeliveryAddress($this->deserializeAddress($json->addresses->delivery));
			}
		}
		
		if($json->items !== null)
		{
			$itemsArray = array();
			foreach($json->items as $item)
			{
				$itemsArray[] = $this->deserializeOrderItem($item);
			}
			$orderItemCollection = MainFactory::create('OrderItemCollection', $itemsArray);
			$order->setOrderItems($orderItemCollection);
		}
		
		if($json->totals !== null)
		{
			$totalsArray = array();
			foreach($json->totals as $total)
			{
				$totalsArray[] = $this->deserializeOrderTotal($total);
			}
			$orderTotalCollection = MainFactory::create('OrderTotalCollection', $totalsArray);
			$order->setOrderTotals($orderTotalCollection);
		}
		
		if($json->addonValues !== null)
		{
			$orderAddonValuesArray      = $this->_deserializeAddonValues($json->addonValues);
			$orderAddonValuesCollection = MainFactory::create('EditableKeyValueCollection', $orderAddonValuesArray);
			
			$order->addAddonValues($orderAddonValuesCollection);
		}
		
		if($json->statusId !== null)
		{
			$order->setStatusId(new IdType($json->statusId));
		}
		
		return $order;
	}
	
	
	public function serializeOrderItem(OrderItemInterface $orderItem)
	{
		$result = array(
			'model'                   => $orderItem->getProductModel(),
			'name'                    => $orderItem->getName(),
			'quantity'                => $orderItem->getQuantity(),
			'price'                   => $orderItem->getPrice(),
			'finalPrice'              => $orderItem->getFinalPrice(),
			'tax'                     => $orderItem->getTax(),
			'isTaxAllowed'            => $orderItem->isTaxAllowed(),
			'discount'                => $orderItem->getDiscountMade(),
			'shippingTimeInformation' => $orderItem->getShippingTimeInfo(),
			'checkoutInformation'     => $orderItem->getCheckoutInformation(),
			'quantityUnitName'        => $orderItem->getQuantityUnitName(),
			'attributes'              => array(),
			'downloadInformation'     => array(),
			'addonValues'             => $this->_serializeAddonValues($orderItem->getAddonValues())
		);
		
		foreach($orderItem->getAttributes()->getArray() as $orderItemAttribute)
		{
			$result['attributes'][] = $this->serializeAttribute($orderItemAttribute);
		}
		
		foreach($orderItem->getDownloadInformation()->getArray() as $orderItemDownloadInformation)
		{
			$result['downloadInformation'][] = $this->_serializeOrderItemDownloadInformation($orderItemDownloadInformation);
		}
		
		if(method_exists($orderItem, 'getOrderItemId'))
		{
			$result = array_merge(array('id' => $orderItem->getOrderItemId()), $result);
		}
		
		return $result;
	}
	
	
	public function serializeOrderTotal(OrderTotalInterface $orderTotal)
	{
		$result = array(
			'title'     => $orderTotal->getTitle(),
			'value'     => $orderTotal->getValue(),
			'valueText' => $orderTotal->getValueText(),
			'class'     => $orderTotal->getClass(),
			'sortOrder' => $orderTotal->getSortOrder()
		);
		
		if(method_exists($orderTotal, 'getOrderTotalId'))
		{
			$result = array_merge(array('id' => $orderTotal->getOrderTotalId()), $result);
		}
		
		return $result;
	}
	
	
	public function serializeAddress(AddressBlockInterface $addressBlock)
	{
		$result = array(
			'gender'                => (string)$addressBlock->getGender(),
			'firstname'             => (string)$addressBlock->getFirstname(),
			'lastname'              => (string)$addressBlock->getLastname(),
			'company'               => (string)$addressBlock->getCompany(),
			'street'                => (string)$addressBlock->getStreet(),
			'houseNumber'           => (string)$addressBlock->getHouseNumber(),
			'additionalAddressInfo' => (string)$addressBlock->getAdditionalAddressInfo(),
			'suburb'                => (string)$addressBlock->getSuburb(),
			'postcode'              => (string)$addressBlock->getPostcode(),
			'city'                  => (string)$addressBlock->getCity(),
			'countryId'             => (int)(string)$addressBlock->getCountry()->getId(),
			'zoneId'                => (int)(string)$addressBlock->getCountryZone()->getId(),
			'b2bStatus'             => $addressBlock->getB2BStatus()->getStatus()
		);
		
		return $result;
	}
	
	
	public function serializeAttribute(OrderItemAttributeInterface $orderItemAttribute)
	{
		$result = array(
			'name'          => $orderItemAttribute->getName(),
			'value'         => $orderItemAttribute->getValue(),
			'price'         => $orderItemAttribute->getPrice(),
			'priceType'     => $orderItemAttribute->getPriceType(),
			'optionId'      => null,
			'optionValueId' => null,
			'combisId'      => null
		);
		
		if(method_exists($orderItemAttribute, 'getCombisId'))
		{
			$result['combisId'] = $orderItemAttribute->getCombisId();
		}
		
		if(method_exists($orderItemAttribute, 'getOptionId'))
		{
			$result['optionId'] = $orderItemAttribute->getOptionId();
		}
		
		if(method_exists($orderItemAttribute, 'getOptionValueId'))
		{
			$result['optionValueId'] = $orderItemAttribute->getOptionValueId();
		}
		
		if(method_exists($orderItemAttribute, 'getOrderItemAttributeId'))
		{
			$result = array_merge(array('id' => $orderItemAttribute->getOrderItemAttributeId()), $result);
		}
		
		return $result;
	}
	
	
	public function deserializeOrderItem($json, $baseObject = null)
	{
		if($baseObject === null)
		{
			$orderItem = ($json->id !== null) ? MainFactory::create('StoredOrderItem',
			                                                        new IdType($json->id)) : MainFactory::create('OrderItem',
			                                                                                                     new StringType($json->name));
		}
		else
		{
			$orderItem = $baseObject;
		}
		
		if($json->model !== null)
		{
			$orderItem->setProductModel(new StringType($json->model));
		}
		
		if($json->name !== null)
		{
			$orderItem->setName(new StringType($json->name));
		}
		
		if($json->quantity !== null)
		{
			$orderItem->setQuantity(new DecimalType($json->quantity));
		}
		
		if($json->price !== null)
		{
			$orderItem->setPrice(new DecimalType($json->price));
		}
		
		if($json->tax !== null)
		{
			$orderItem->setTax(new DecimalType($json->tax));
		}
		
		if($json->isTaxAllowed !== null)
		{
			$orderItem->setTaxAllowed(new BoolType($json->isTaxAllowed));
		}
		
		if($json->discount !== null)
		{
			$orderItem->setDiscountMade(new DecimalType($json->discount));
		}
		
		if($json->shippingTimeInformation !== null)
		{
			$orderItem->setShippingTimeInfo(new StringType($json->shippingTimeInformation));
		}
		
		if($json->checkoutInformation !== null)
		{
			$orderItem->setCheckoutInformation(new StringType($json->checkoutInformation));
		}
		
		if($json->quantityUnitName !== null)
		{
			$orderItem->setQuantityUnitName(new StringType($json->quantityUnitName));
		}
		
		if($json->attributes !== null)
		{
			$attributesArray = array();
			foreach($json->attributes as $attribute)
			{
				$attributesArray[] = $this->deserializeAttribute($attribute);
			}
			$orderItemAttributeCollection = MainFactory::create('OrderItemAttributeCollection', $attributesArray);
			$orderItem->setAttributes($orderItemAttributeCollection);
		}
		
		if($json->downloadInformation !== null && is_array($json->downloadInformation))
		{
			$orderItemDownloadInformationArray = array();
			
			foreach($json->downloadInformation as $download)
			{
				$orderItemDownloadInformationArray[] = $this->_deserializeOrderItemDownloadInformation($download);
			}
			
			$orderItemDownloadInformationCollection = MainFactory::create('OrderItemDownloadInformationCollection',
			                                                              $orderItemDownloadInformationArray);
			
			$orderItem->setDownloadInformation($orderItemDownloadInformationCollection);
		}
		
		if($json->addonValues !== null)
		{
			$orderItemAddonValuesArray      = $this->_deserializeAddonValues($json->addonValues);
			$orderItemAddonValuesCollection = MainFactory::create('EditableKeyValueCollection',
			                                                      $orderItemAddonValuesArray);
			
			$orderItem->addAddonValues($orderItemAddonValuesCollection);
		}
		
		return $orderItem;
	}
	
	
	public function deserializeOrderTotal($json, $baseObject = null)
	{
		if($baseObject === null)
		{
			if($json->id !== null)
			{
				$orderTotal = MainFactory::create('StoredOrderTotal', new IdType($json->id));
			}
			else
			{
				$title     = new StringType($json->title);
				$value     = new DecimalType($json->value);
				$valueText = ($json->valueText !== null) ? new StringType($json->valueText) : null;
				$class     = ($json->class !== null) ? new StringType($json->class) : null;
				$sortOrder = ($json->sortOrder !== null) ? new IntType($json->sortOrder) : null;
				
				$orderTotal = MainFactory::create('OrderTotal', $title, $value, $valueText, $class, $sortOrder);
			}
		}
		else
		{
			$orderTotal = $baseObject;
		}
		
		if($json->title !== null)
		{
			$orderTotal->setTitle(new StringType($json->title));
		}
		
		if($json->value !== null)
		{
			$orderTotal->setValue(new DecimalType($json->value));
		}
		
		if($json->valueText !== null)
		{
			$orderTotal->setValueText(new StringType($json->valueText));
		}
		
		if($json->class !== null)
		{
			$orderTotal->setClass(new StringType($json->class));
		}
		
		if($json->sortOrder !== null)
		{
			$orderTotal->setSortOrder(new IntType($json->sortOrder));
		}
		
		return $orderTotal;
	}
	
	
	public function deserializeAddress($json)
	{
		$gender                = MainFactory::create('CustomerGender', $json->gender);
		$firstname             = MainFactory::create('CustomerFirstname', $json->firstname);
		$lastname              = MainFactory::create('CustomerLastname', $json->lastname);
		$company               = MainFactory::create('CustomerCompany', $json->company);
		$b2bStatus             = MainFactory::create('CustomerB2BStatus', $json->b2bStatus);
		$street                = MainFactory::create('CustomerStreet', $json->street);
		$houseNumber           = MainFactory::create('CustomerHouseNumber', $json->houseNumber);
		$additionalAddressInfo = MainFactory::create('CustomerAdditionalAddressInfo', $json->additionalAddressInfo);
		$suburb                = MainFactory::create('CustomerSuburb', $json->suburb);
		$postcode              = MainFactory::create('CustomerPostcode', $json->postcode);
		$city                  = MainFactory::create('CustomerCity', $json->city);
		
		$countryService = StaticGXCoreLoader::getService('Country');
		$country        = $countryService->getCountryById(new IdType($json->countryId));
		$zone           = $countryService->getCountryZoneById(new IdType($json->zoneId));
		
		$addressBlock = MainFactory::create('AddressBlock', $gender, $firstname, $lastname, $company, $b2bStatus,
		                                    $street, $houseNumber, $additionalAddressInfo, $suburb, $postcode, $city,
		                                    $country, $zone);
		
		return $addressBlock;
	}
	
	
	public function deserializeAttribute($json, $baseObject = null)
	{
		
		if($baseObject === null)
		{
			// Create either a OrderItemAttribute or a OrderItemProperty object. 
			
			$baseClassName = ($json->combisId !== null) ? 'OrderItemProperty' : 'OrderItemAttribute';
			
			/** @var StoredOrderItemAttributeInterface $orderItemAttribute */
			$orderItemAttribute = ($json->id !== null) ? MainFactory::create('Stored' . $baseClassName,
			                                                                 new IdType($json->id)) : MainFactory::create($baseClassName,
			                                                                                                              new StringType($json->name),
			                                                                                                              new StringType($json->value));
		}
		else
		{
			$orderItemAttribute = $baseObject;
		}
		
		if($json->name !== null)
		{
			$orderItemAttribute->setName(new StringType($json->name));
		}
		
		if($json->value !== null)
		{
			$orderItemAttribute->setValue(new StringType($json->value));
		}
		
		if($json->price !== null)
		{
			$orderItemAttribute->setPrice(new DecimalType($json->price));
		}
		
		if($json->priceType !== null)
		{
			$orderItemAttribute->setPriceType(new StringType($json->priceType));
		}
		
		if($json->optionId !== null)
		{
			$orderItemAttribute->setOptionId(new IdType($json->optionId));
		}
		
		if($json->optionValueId !== null)
		{
			$orderItemAttribute->setOptionValueId(new IdType($json->optionValueId));
		}
		
		if($json->combisId !== null)
		{
			$orderItemAttribute->setCombisId(new IdType($json->combisId));
		}
		
		return $orderItemAttribute;
	}
	
	
	public function serializeOrderStatusHistoryListItem(OrderStatusHistoryListItem $orderStatusHistoryListItem)
	{
		$result = array(
			'id'               => $orderStatusHistoryListItem->getOrderStatusHistoryId(),
			'statusId'         => $orderStatusHistoryListItem->getOrderStatusId(),
			'dateAdded'        => $orderStatusHistoryListItem->getDateAdded()->format('Y-m-d H:i:s'),
			'comment'          => $orderStatusHistoryListItem->getComment(),
			'customerNotified' => $orderStatusHistoryListItem->isCustomerNotified()
		);
		
		return $result;
	}
	
	
	protected function _serializeOrderItemDownloadInformation(OrderItemDownloadInformation $downloadInformation)
	{
		$result = array(
			'filename'       => $downloadInformation->getFilename(),
			'maxDaysAllowed' => $downloadInformation->getMaxDaysAllowed(),
			'countAvailable' => $downloadInformation->getCountAvailable()
		);
		
		return $result;
	}
	
	
	protected function _deserializeOrderItemDownloadInformation($json)
	{
		$downloadInformation = MainFactory::create('OrderItemDownloadInformation',
		                                           new FilenameStringType($json->filename),
		                                           new IntType($json->maxDaysAllowed),
		                                           new IntType($json->countAvailable));
		
		return $downloadInformation;
	}
}