<?php
/* --------------------------------------------------------------
   OrderRepositoryReader.inc.php 2016-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderRepositoryReaderInterface');

/**
 * Class OrderRepositoryReader
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderRepositoryReader implements OrderRepositoryReaderInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Order factory.
	 * @var OrderFactory
	 */
	protected $orderFactory;
	
	/**
	 * Country service.
	 * @var CountryServiceInterface
	 */
	protected $countryService;
	
	
	/**
	 * OrderRepositoryReader constructor.
	 *
	 * @param CI_DB_query_builder     $db             Query builder.
	 * @param OrderFactoryInterface   $orderFactory   Order factory.
	 * @param CountryServiceInterface $countryService Country service.
	 */
	public function __construct(CI_DB_query_builder $db,
	                            OrderFactoryInterface $orderFactory,
	                            CountryServiceInterface $countryService)
	{
		$this->db             = $db;
		$this->orderFactory   = $orderFactory;
		$this->countryService = $countryService;
	}
	
	
	/**
	 * Fetches an new order object from the orders table by the given ID.
	 *
	 * @param IdType $orderId ID of the expected order.
	 *
	 * @return GXEngineOrder Fetched order.
	 * @throws UnexpectedValueException If no entry has been found.
	 */
	public function getById(IdType $orderId)
	{
		$this->db->select('orders.*, languages.code AS language_code')
		         ->from('orders')
		         ->join('languages', 'languages.directory = orders.language', 'inner')
		         ->where('orders_id', $orderId->asInt());
		
		$data = $this->db->get()->row_array();
		
		if($data === null)
		{
			throw new UnexpectedValueException('The requested Order was not found in database (ID:' . $orderId->asInt()
			                                   . ')');
		}
		
		$order = $this->_createOrderByArray($data);
		
		return $order;
	}
	
	
	/**
	 * Creates an order instance.
	 *
	 * @param array $data Order data.
	 *
	 * @return GXEngineOrder Created order object.
	 */
	protected function _createOrderByArray(array $data)
	{
		$order = $this->orderFactory->createOrder();
		
		// Set IDs
		$order->setOrderId(new IdType($data['orders_id']));
		$order->setCustomerId(new IdType($data['customers_id']));
		$order->setStatusId(new IdType($data['orders_status']));
		
		// Customer Status Information
		$isGuest                   = ((int)$data['account_type'] === 1 || (int)$data['customers_status'] === 1);
		$customerStatusInformation = MainFactory::create('CustomerStatusInformation',
		                                                 new IdType((int)$data['customers_status']),
		                                                 new StringType((string)$data['customers_status_name']),
		                                                 new StringType((string)$data['customers_status_image']),
		                                                 new DecimalType((double)$data['customers_status_discount']),
		                                                 new BoolType($isGuest));
		$order->setCustomerStatusInformation($customerStatusInformation);
		
		// Address Information
		$customerAddress = $this->_getOrderAddressBlock($data, new StringType('customers'));
		$order->setCustomerAddress($customerAddress);
		$billingAddress = $this->_getOrderAddressBlock($data, new StringType('billing'));
		$order->setBillingAddress($billingAddress);
		$deliveryAddress = $this->_getOrderAddressBlock($data, new StringType('delivery'));
		$order->setDeliveryAddress($deliveryAddress);
		$paymentType = MainFactory::create('OrderPaymentType', new StringType($data['payment_method']),
		                                   new StringType($data['payment_class']));
		
		// Payment & Shipping Type
		$order->setPaymentType($paymentType);
		$shippingType = MainFactory::create('OrderShippingType', new StringType($data['shipping_method']),
		                                    new StringType($data['shipping_class']));
		$order->setShippingType($shippingType);
		
		// Miscellaneous Values
		$order->setCustomerNumber(new StringType((string)$data['customers_cid']));
		$order->setCustomerEmail(new EmailStringType($data['customers_email_address']));
		$order->setCustomerTelephone(new StringType($data['customers_telephone']));
		$order->setVatIdNumber(new StringType((string)$data['customers_vat_id']));
		$order->setTotalWeight(new DecimalType((double)$data['order_total_weight']));
		$order->setCurrencyCode(MainFactory::create('CurrencyCode', new NonEmptyStringType((string)$data['currency']),
		                                            new DecimalType((double)$data['currency_value'])));
		$order->setLanguageCode(new LanguageCode(new NonEmptyStringType($data['language_code'])));
		$order->setPurchaseDateTime(new EmptyDateTime((string)$data['date_purchased']));
		$order->setLastModifiedDateTime(new EmptyDateTime((string)$data['last_modified']));
		$order->setComment(new StringType((string)$data['comments']));
		
		if(!empty($data['orders_hash']))
		{
			$order->setOrderHash(new NonEmptyStringType($data['orders_hash']));
		}
		
		return $order;
	}
	
	
	/**
	 * Returns order address block instance for the customer, billing and delivery address.
	 *
	 * @param array      $data   Contains the order data that came directly from the database.
	 * @param StringType $prefix Must be one of "customers", "billing" and "delivery" (look at the "orders" db table).
	 *
	 * @return AddressBlock Instance of an address block that contains the address values.
	 */
	protected function _getOrderAddressBlock(array $data, StringType $prefix)
	{
		$addressPrefix = $prefix->asString() . '_';
		
		$country = $this->countryService->findCountryByName($data[$addressPrefix . 'country']);
		$zone    = $this->countryService->getUnknownCountryZoneByName((string)$data[$addressPrefix . 'state']);
		
		if($this->countryService->countryHasCountryZones($country)
			&& $this->countryService->countryZoneExistsInCountry($zone, $country))
		{
			$zone = $this->countryService->getCountryZoneByNameAndCountry((string)$data[$addressPrefix . 'state'], $country);
		}
		
		return MainFactory::create('AddressBlock', new CustomerGender($data[$addressPrefix . 'gender']),
		                           new CustomerFirstname($data[$addressPrefix . 'firstname']),
		                           new CustomerLastname($data[$addressPrefix . 'lastname']),
		                           new CustomerCompany((string)$data[$addressPrefix . 'company']),
		                           new CustomerB2BStatus(false),
		                           new CustomerStreet($data[$addressPrefix . 'street_address']),
		                           new CustomerHouseNumber($data[$addressPrefix . 'house_number']),
		                           new CustomerAdditionalAddressInfo($data[$addressPrefix . 'additional_info']),
		                           new CustomerSuburb((string)$data[$addressPrefix . 'suburb']),
		                           new CustomerPostcode($data[$addressPrefix . 'postcode']),
		                           new CustomerCity($data[$addressPrefix . 'city']), $country, $zone);
	}
}