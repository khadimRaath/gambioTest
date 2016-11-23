<?php
/* --------------------------------------------------------------
   GxEngineOrder.inc.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderInterface');
MainFactory::load_class('AddonValueContainerInterface');

/**
 * Class GXEngineOrder
 *
 * This class is used for managing order data.
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class GXEngineOrder implements OrderInterface, AddonValueContainerInterface
{
	/**
	 * Order ID.
	 *
	 * @var int
	 */
	protected $orderId = 0;
	
	/**
	 * Unique order hash.
	 *
	 * @var string
	 */
	protected $orderHash = '';
	
	/**
	 * Order Status ID.
	 *
	 * @var int
	 */
	protected $statusId = 0;
	
	/**
	 * Customer ID.
	 *
	 * @var int
	 */
	protected $customerId = 0;
	
	/**
	 * Customer email address.
	 *
	 * @var string
	 */
	protected $customerEmail = '';
	
	/**
	 * Customer telephone number.
	 *
	 * @var string
	 */
	protected $customerTelephone = '';
	
	/**
	 * Customer number.
	 *
	 * @var string
	 */
	protected $customerNumber = '';
	
	/**
	 * VAT ID number.
	 * 
	 * @var string
	 */
	protected $vatIdNumber = '';
	
	/**
	 * Customer status information.
	 * 
	 * @var CustomerStatusInformation
	 */
	protected $customerStatusInformation;
	
	/**
	 * Customer address.
	 *
	 * @var AddressBlockInterface
	 */
	protected $customerAddress;
	
	/**
	 * Billing address.
	 *
	 * @var AddressBlockInterface
	 */
	protected $billingAddress;
	
	/**
	 * Delivery address.
	 *
	 * @var AddressBlockInterface
	 */
	protected $deliveryAddress;
	
	/**
	 * Order items.
	 *
	 * @var OrderItemCollection
	 */
	protected $orderItems;
	
	/**
	 * Order totals.
	 *
	 * @var OrderTotalCollection
	 */
	protected $orderTotals;
	
	/**
	 * Order shipping type.
	 *
	 * @var OrderShippingType
	 */
	protected $shippingType;
	
	/**
	 * Order payment type.
	 *
	 * @var OrderPaymentType
	 */
	protected $paymentType;
	
	/**
	 * Order currency code.
	 *
	 * @var CurrencyCode
	 */
	protected $currencyCode;
	
	/**
	 * Order language code.
	 *
	 * @var LanguageCode
	 */
	protected $languageCode;
	
	/**
	 * Order purchase date time.
	 *
	 * @var DateTime
	 */
	protected $purchaseDateTime;
	
	/**
	 * Order last modified date time
	 *
	 * @var DateTime
	 */
	protected $lastModifiedDateTime;
	
	/**
	 * Order comment.
	 *
	 * @var string
	 */
	protected $comment = '';
	
	/**
	 * Order addon collection.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $addonValues;
	
	/**
	 * Order status history.
	 *
	 * @var OrderStatusHistoryStorage
	 */
	protected $orderStatusHistoryReader;

	/**
	 * @var float
	 */
	protected $totalWeight = 0.0;


	/**
	 * GXEngineOrder constructor.
	 */
	public function __construct()
	{
		// Set object properties which need a AddressBlock.
		$this->_initializeAddressProperties();
		
		// Set object properties which need collections.
		$this->_initializeCollectionProperties();
		
		// Set object properties which need a
		// type or code object like 'OrderShippingType' or 'CurrencyCode'.
		$this->_initializeTypeAndCodeProperties();
		
		// Set $purchaseDateTime and $lastModifiedDateTime to current datetime as default value.
		$this->setPurchaseDateTime(new DateTime());
		$this->setLastModifiedDateTime(new DateTime());
		
		// Set empty CustomerStatusInformation object
		$this->setCustomerStatusInformation(MainFactory::create('CustomerStatusInformation', new IdType(1)));
	}
	
	
	/**
	 * Initializes default values for AddressBlock properties.
	 */
	protected function _initializeAddressProperties()
	{
		// Create value objects.
		$gender                = MainFactory::create('CustomerGender', '');
		$firstName             = MainFactory::create('CustomerFirstname', '');
		$lastName              = MainFactory::create('CustomerLastname', '');
		$company               = MainFactory::create('CustomerCompany', '');
		$B2BStatus             = MainFactory::create('CustomerB2BStatus', true);
		$street                = MainFactory::create('CustomerStreet', '');
		$houseNumber           = MainFactory::create('CustomerHouseNumber', '');
		$additionalAddressInfo = MainFactory::create('CustomerAdditionalAddressInfo', '');
		$suburb                = MainFactory::create('CustomerSuburb', '');
		$postCode              = MainFactory::create('CustomerPostcode', '');
		$city                  = MainFactory::create('CustomerCity', '');
		
		$country     = MainFactory::create('CustomerCountry', new IdType(123),
		                                   MainFactory::create('CustomerCountryName', ''),
		                                   MainFactory::create('CustomerCountryIso2', ''),
		                                   MainFactory::create('CustomerCountryIso3', ''), new IdType(823), true);
		$countryZone = MainFactory::create('CustomerCountryZone', new IdType(123),
		                                   MainFactory::create('CustomerCountryZoneName', ''),
		                                   MainFactory::create('CustomerCountryZoneIsoCode', ''));
		
		// Set customer address.
		$customerAddress = MainFactory::create('AddressBlock', $gender, $firstName, $lastName, $company, $B2BStatus,
		                                       $street, $houseNumber, $additionalAddressInfo, $suburb, $postCode, $city,
		                                       $country, $countryZone);
		$this->setCustomerAddress($customerAddress);
		
		// Set billing address.
		$billingAddress = MainFactory::create('AddressBlock', $gender, $firstName, $lastName, $company, $B2BStatus,
		                                      $street, $houseNumber, $additionalAddressInfo, $suburb, $postCode, $city,
		                                      $country, $countryZone);
		$this->setBillingAddress($billingAddress);
		
		// Set delivery address.
		$deliveryAddress = MainFactory::create('AddressBlock', $gender, $firstName, $lastName, $company, $B2BStatus,
		                                       $street, $houseNumber, $additionalAddressInfo, $suburb, $postCode, $city,
		                                       $country, $countryZone);
		$this->setDeliveryAddress($deliveryAddress);
	}
	
	
	/**
	 * Initializes default values for Collection properties.
	 */
	protected function _initializeCollectionProperties()
	{
		// Create empty array as collection.
		$collection = array();
		
		// Set order items.
		$orderItems = MainFactory::create('OrderItemCollection', $collection);
		$this->setOrderItems($orderItems);
		
		// Set order totals.
		$orderTotals = MainFactory::create('OrderTotalCollection', $collection);
		$this->setOrderTotals($orderTotals);
		
		// Set addon values collection.
		// Note, that there is no setter method for assign the addonValues collection.
		$addonValues       = MainFactory::create('EditableKeyValueCollection', $collection);
		$this->addonValues = $addonValues;
	}
	
	
	/**
	 * Initializes default values for type and code properties.
	 */
	protected function _initializeTypeAndCodeProperties()
	{
		// Create empty title for type value objects.
		$title = new StringType('');
		
		// Create empty module for type value objects.
		$module = new StringType('');
		
		// Set type object: shipping type.
		$shippingType = MainFactory::create('OrderShippingType', $title, $module);
		$this->setShippingType($shippingType);
		
		// Set type object: payment type.
		$paymentType = MainFactory::create('OrderPaymentType', $title, $module);
		$this->setPaymentType($paymentType);
		
		// Set code object: currency code.
		$currencyCode = MainFactory::create('EmptyCurrencyCode');
		$this->setCurrencyCode($currencyCode);
		
		// Set code object: language code.
		$languageCode = MainFactory::create('EmptyLanguageCode');
		$this->setLanguageCode($languageCode);
	}
	
	
	/**
	 * Sets the order ID.
	 *
	 * Note, that the ID will be saved as string to the class property.
	 *
	 * @param IdType $id Order ID.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setOrderId(IdType $id)
	{
		$this->orderId = $id->asInt();
		
		return $this;
	}
	
	
	/**
	 * Returns the order ID.
	 *
	 * @return int Order ID.
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}
	
	
	/**
	 * Sets the unique order hash.
	 *
	 * @param NonEmptyStringType $orderHash Unique order hash.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setOrderHash(NonEmptyStringType $orderHash)
	{
		$this->orderHash = $orderHash->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the unique order hash.
	 *
	 * @return string Unique order hash.
	 */
	public function getOrderHash()
	{
		return $this->orderHash;
	}
	
	
	/**
	 * Sets the associated customer ID.
	 *
	 * @param IdType $id Customer ID.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setCustomerId(IdType $id)
	{
		$this->customerId = $id->asInt();
		
		return $this;
	}
	
	
	/**
	 * Returns the associated customer ID.
	 *
	 * @return int Associated customer ID.
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}
	
	
	/**
	 * Sets the customer email address.
	 *
	 * @param EmailStringType $email Customer email address.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setCustomerEmail(EmailStringType $email)
	{
		$this->customerEmail = $email->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the associated customer email address.
	 *
	 * @return string Associated customer email address.
	 */
	public function getCustomerEmail()
	{
		return $this->customerEmail;
	}
	
	
	/**
	 * Sets the customer telephone number.
	 *
	 * @param StringType $telephone Customer telephone number.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setCustomerTelephone(StringType $telephone)
	{
		$this->customerTelephone = $telephone->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the associated customer telephone number.
	 *
	 * @return string Associated customer telephone number.
	 */
	public function getCustomerTelephone()
	{
		return $this->customerTelephone;
	}
	
	
	/**
	 * Sets the order status ID.
	 *
	 * @param IntType $id Status ID.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setStatusId(IntType $id)
	{
		$this->statusId = $id->asInt();
		
		return $this;
	}
	
	
	/**
	 * Returns the order status ID.
	 *
	 * @return int Order status ID.
	 */
	public function getStatusId()
	{
		return $this->statusId;
	}
	
	
	/**
	 * Sets customer number of Order
	 *
	 * @param StringType $customerNumber Customer number.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setCustomerNumber(StringType $customerNumber)
	{
		$this->customerNumber = $customerNumber->asString();
	}
	
	
	/**
	 * Returns the customer number.
	 *
	 * @return string Customer number.
	 */
	public function getCustomerNumber()
	{
		return $this->customerNumber;
	}
	
	
	/**
	 * Sets the VAT ID Number.
	 *
	 * @param StringType $vatIdNumber VAT ID number.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setVatIdNumber(StringType $vatIdNumber)
	{
		$this->vatIdNumber = $vatIdNumber->asString();
	}
	
	
	/**
	 * Returns the VAT ID number.
	 *
	 * @return string VAT ID number.
	 */
	public function getVatIdNumber()
	{
		return $this->vatIdNumber;
	}
	
	
	/**
	 * Sets the customer status information.
	 *
	 * @param CustomerStatusInformation $customerStatusInformation Customer status information.
	 */
	public function setCustomerStatusInformation(CustomerStatusInformation $customerStatusInformation)
	{
		$this->customerStatusInformation = $customerStatusInformation;
	}
	
	
	/**
	 * Returns the Customer status information.
	 *
	 * @return CustomerStatusInformation Customer status information.
	 */
	public function getCustomerStatusInformation()
	{
		return $this->customerStatusInformation;
	}
	
	
	/**
	 * Sets the customer address.
	 *
	 * @param AddressBlockInterface $address Customer address.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setCustomerAddress(AddressBlockInterface $address)
	{
		$this->customerAddress = $address;
		
		return $this;
	}
	
	
	/**
	 * Returns the customer address.
	 *
	 * @return AddressBlockInterface Customer address.
	 */
	public function getCustomerAddress()
	{
		return $this->customerAddress;
	}
	
	
	/**
	 * Sets the billing address.
	 *
	 * @param AddressBlockInterface $address Billing address.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setBillingAddress(AddressBlockInterface $address)
	{
		$this->billingAddress = $address;
		
		return $this;
	}
	
	
	/**
	 * Returns the billing address.
	 *
	 * @return AddressBlockInterface Billing address.
	 */
	public function getBillingAddress()
	{
		return $this->billingAddress;
	}
	
	
	/**
	 * Sets the delivery address.
	 *
	 * @param AddressBlockInterface $address Delivery address.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setDeliveryAddress(AddressBlockInterface $address)
	{
		$this->deliveryAddress = $address;
		
		return $this;
	}
	
	
	/**
	 * Returns the delivery address.
	 *
	 * @return AddressBlockInterface Delivery address.
	 */
	public function getDeliveryAddress()
	{
		return $this->deliveryAddress;
	}
	
	
	/**
	 * Sets the order items collection.
	 *
	 * @param OrderItemCollection $collection Items collection.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setOrderItems(OrderItemCollection $collection)
	{
		$this->orderItems = $collection;
		
		return $this;
	}
	
	
	/**
	 * Returns the order items collection.
	 *
	 * @return OrderItemCollection Order items collection.
	 */
	public function getOrderItems()
	{
		return $this->orderItems;
	}
	
	
	/**
	 * Sets the order total collection.
	 *
	 * @param OrderTotalCollection $collection Total collection.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setOrderTotals(OrderTotalCollection $collection)
	{
		$this->orderTotals = $collection;
		
		return $this;
	}
	
	
	/**
	 * Returns the order totals collection.
	 *
	 * @return OrderTotalCollection Order totals collection.
	 */
	public function getOrderTotals()
	{
		return $this->orderTotals;
	}
	
	
	/**
	 * Sets the order shipping type.
	 *
	 * @param OrderShippingType $shippingType Shipping type.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setShippingType(OrderShippingType $shippingType)
	{
		$this->shippingType = $shippingType;
		
		return $this;
	}
	
	
	/**
	 * Returns the order shipping type.
	 *
	 * @return OrderShippingType Order shipping type.
	 */
	public function getShippingType()
	{
		return $this->shippingType;
	}
	
	
	/**
	 * Sets the order payment type.
	 *
	 * @param OrderPaymentType $paymentType Payment type.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setPaymentType(OrderPaymentType $paymentType)
	{
		$this->paymentType = $paymentType;
		
		return $this;
	}
	
	
	/**
	 * Returns the order payment type.
	 *
	 * @return OrderPaymentType Order payment type.
	 */
	public function getPaymentType()
	{
		return $this->paymentType;
	}
	
	
	/**
	 * Sets the order currency code.
	 *
	 * @param CurrencyCode $currencyCode Currency code.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setCurrencyCode(CurrencyCode $currencyCode)
	{
		$this->currencyCode = $currencyCode;
		
		return $this;
	}
	
	
	/**
	 * Returns the order currency code.
	 *
	 * @return CurrencyCode Order currency code.
	 */
	public function getCurrencyCode()
	{
		return $this->currencyCode;
	}
	
	
	/**
	 * Sets the order language code.
	 *
	 * @param LanguageCode $languageCode Language code.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setLanguageCode(LanguageCode $languageCode)
	{
		$this->languageCode = $languageCode;
		
		return $this;
	}
	
	
	/**
	 * Returns the order language code.
	 *
	 * @return LanguageCode Order language code.
	 */
	public function getLanguageCode()
	{
		return $this->languageCode;
	}
	
	
	/**
	 * Sets the order purchase date time.
	 *
	 * @param DateTime $purchaseDateTime Purchase date time.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setPurchaseDateTime(DateTime $purchaseDateTime)
	{
		$this->purchaseDateTime = $purchaseDateTime;
		
		return $this;
	}
	
	
	/**
	 * Returns the order purchase datetime.
	 *
	 * @return DateTime Order purchase datetime.
	 */
	public function getPurchaseDateTime()
	{
		return $this->purchaseDateTime;
	}
	
	
	/**
	 * Sets the date time of last modification.
	 *
	 * @param DateTime $lastModifiedDateTime Last modification date time
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setLastModifiedDateTime(DateTime $lastModifiedDateTime)
	{
		$this->lastModifiedDateTime = $lastModifiedDateTime;
		
		return $this;
	}
	
	
	/**
	 * Returns the datetime of last modification.
	 *
	 * @return DateTime Datetime of last modification.
	 */
	public function getLastModifiedDateTime()
	{
		return $this->lastModifiedDateTime;
	}
	
	
	/**
	 * Sets order status history storage object.
	 *
	 * @param OrderStatusHistoryReaderInterface $orderStatusHistoryReader Order status history storage object.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setOrderStatusHistoryReader(OrderStatusHistoryReaderInterface $orderStatusHistoryReader)
	{
		$this->orderStatusHistoryReader = $orderStatusHistoryReader;
		
		return $this;
	}
	
	
	/**
	 * Returns the order status history.
	 *
	 * @return OrderStatusHistoryListItemCollection Order status history.
	 */
	public function getStatusHistory()
	{
		if(isset($this->orderStatusHistoryReader)
		   && $this->orderStatusHistoryReader instanceof OrderStatusHistoryReaderInterface
		)
		{
			return $this->orderStatusHistoryReader->getStatusHistory(new IdType($this->orderId));
		}
		
		return MainFactory::create('OrderStatusHistoryListItemCollection', array());
	}
	
	
	/**
	 * Sets the order comment.
	 *
	 * @param StringType $comment Comment.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setComment(StringType $comment)
	{
		$this->comment = $comment->asString();
		
		return $this;
	}
	
	
	/**
	 * Returns the order comment.
	 *
	 * @return string Order comment.
	 */
	public function getComment()
	{
		return $this->comment;
	}


	/**
	 * Returns the total weight of the ordered products.
	 *
	 * @return double
	 */
	public function getTotalWeight()
	{
		return $this->totalWeight;
	}


	/**
	 * Sets the total weight of the ordered products.
	 *
	 * @param \DecimalType $totalWeight
	 *
	 * @return $this|GXEngineOrder Same instance for method chaining.
	 */
	public function setTotalWeight(DecimalType $totalWeight)
	{
		$this->totalWeight = $totalWeight->asDecimal();

		return $this;
	}


	/**
	 * Returns the addon value container ID.
	 * The addon value container id is equal to the orders id.
	 *
	 * @return int Addon value container ID
	 */
	public function getAddonValueContainerId()
	{
		return $this->getOrderId();
	}
	
	
	/**
	 * Returns the order addon value collection.
	 *
	 * @return EditableKeyValueCollection Order addon value collection.
	 */
	public function getAddonValue(StringType $key)
	{
		return $this->addonValues->getValue($key->asString());
	}
	
	
	/**
	 * Returns the order addon value collection.
	 *
	 * @return EditableKeyValueCollection Order addon value collection.
	 */
	public function getAddonValues()
	{
		return $this->addonValues->getClone();
	}
	
	
	/**
	 * Adds/updates a key value in the addon value collection.
	 *
	 * @param StringType $key   Addon key.
	 * @param StringType $value Addon value.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function setAddonValue(StringType $key, StringType $value)
	{
		$this->addonValues->setValue($key->asString(), $value->asString());
		
		return $this;
	}
	
	
	/**
	 * Adds an addon collection to the existing one.
	 *
	 * @param KeyValueCollection $addonCollection Addon collection.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function addAddonValues(KeyValueCollection $addonCollection)
	{
		$this->addonValues->addCollection($addonCollection);
		
		return $this;
	}
	
	
	/**
	 * Removes an addon value from the addon value container by the given key.
	 *
	 * @param StringType $key Addon key.
	 *
	 * @return GXEngineOrder Same instance for method chaining.
	 */
	public function deleteAddonValue(StringType $key)
	{
		$this->addonValues->deleteValue($key->asString());
		
		return $this;
	}
}