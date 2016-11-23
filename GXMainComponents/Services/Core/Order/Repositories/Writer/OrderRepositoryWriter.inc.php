<?php
/* --------------------------------------------------------------
   OrderRepositoryWriter.inc.php 2016-04-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderRepositoryWriterInterface');

/**
 * Class OrderRepositoryWriter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderRepositoryWriter implements OrderRepositoryWriterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderRepositoryWriter constructor
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Inserts a new order to the orders table.
	 *
	 * @param OrderInterface $order Order.
	 *
	 * @return int ID of inserted order.
	 */
	public function insert(OrderInterface $order)
	{
		$record = $this->_serializeOrder($order);
		
		// Insert orders_hash
		$record['orders_hash'] = time() + mt_rand();
		
		$this->db->insert('orders', $record);
		
		return $this->db->insert_id();
	}
	
	
	/**
	 * Updates an existing order in the orders table.
	 *
	 * @param OrderInterface $order Order object.
	 *
	 * @return OrderRepositoryWriter Same instance for method chaining.
	 */
	public function update(OrderInterface $order)
	{
		$record = $this->_serializeOrder($order);
		$this->db->update('orders', $record, array('orders_id' => $order->getOrderId()));
	}
	
	
	/**
	 * Converts the order to an associative array that is later used to insert/update
	 * the values into the database.
	 *
	 * @param OrderInterface $order The object to be serialized.
	 *
	 * @return array Contains the orders object matched with their respective db columns.
	 */
	protected function _serializeOrder(OrderInterface $order)
	{
		// Find the correct language directory.
		$language = $this->db->get_where('languages', array('code' => strtolower((string)$order->getLanguageCode())))
		                     ->row_array();
		
		$record = array(
			'customers_id'              => $order->getCustomerId(),
			'orders_status'             => $order->getStatusId(),
			'customers_cid'             => (string)$order->getCustomerNumber(),
			'customers_vat_id'          => (string)$order->getVatIdNumber(),
			'customers_status'          => $order->getCustomerStatusInformation()->getStatusId(),
			'customers_status_name'     => $order->getCustomerStatusInformation()->getStatusName(),
			'customers_status_image'    => $order->getCustomerStatusInformation()->getStatusImage(),
			'customers_status_discount' => $order->getCustomerStatusInformation()->getStatusDiscount(),
			'account_type'              => (int)$order->getCustomerStatusInformation()->isGuest(),
			'payment_method'            => $order->getPaymentType()->getTitle(),
			'payment_class'             => $order->getPaymentType()->getModule(),
			'shipping_method'           => $order->getShippingType()->getTitle(),
			'shipping_class'            => $order->getShippingType()->getModule(),
			'comments'                  => $order->getComment(),
			'date_purchased'            => $order->getPurchaseDateTime()->format('Y-m-d H:i:s'),
			'last_modified'             => $order->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
			'language'                  => (string)$language['directory'],
			'order_total_weight'        => $order->getTotalWeight(),
			'currency'                  => (string)$order->getCurrencyCode()->getCode(),
			'currency_value'            => (float)$order->getCurrencyCode()->getCurrencyValue(),
			'customers_telephone'       => $order->getCustomerTelephone(),
			'customers_email_address'   => $order->getCustomerEmail()
		);
		
		$customerAddress = $this->_serializeAddressBlock($order->getCustomerAddress(), new StringType('customers'));
		$deliveryAddress = $this->_serializeAddressBlock($order->getDeliveryAddress(), new StringType('delivery'));
		$billingAddress  = $this->_serializeAddressBlock($order->getBillingAddress(), new StringType('billing'));
		
		$record = array_merge($record, $customerAddress, $deliveryAddress, $billingAddress);
		
		return $record;
	}
	
	
	/**
	 * Serializes address block.
	 *
	 * @param AddressBlockInterface $addressBlock  The object to be serialized.
	 * @param StringType            $prefix        The address prefix can be one of the "delivery", "billing" and
	 *                                             "customers".
	 *
	 * @return array Array with the address data.
	 */
	protected function _serializeAddressBlock(AddressBlockInterface $addressBlock, StringType $prefix)
	{
		$country = $this->db->select('address_format_id')
		                    ->get_where('countries', array('countries_id' => $addressBlock->getCountry()->getId()))
		                    ->row_array();
		
		$addressPrefix = $prefix->asString() . '_';
		
		$addressBlockArray = array(
			$addressPrefix . 'name'              => (string)$addressBlock->getFirstname() . ' '
			                                        . (string)$addressBlock->getLastname(),
			$addressPrefix . 'firstname'         => (string)$addressBlock->getFirstname(),
			$addressPrefix . 'lastname'          => (string)$addressBlock->getLastname(),
			$addressPrefix . 'gender'            => (string)$addressBlock->getGender(),
			$addressPrefix . 'company'           => (string)$addressBlock->getCompany(),
			$addressPrefix . 'street_address'    => (string)$addressBlock->getStreet(),
			$addressPrefix . 'house_number'      => (string)$addressBlock->getHouseNumber(),
			$addressPrefix . 'additional_info'   => (string)$addressBlock->getAdditionalAddressInfo(),
			$addressPrefix . 'suburb'            => (string)$addressBlock->getSuburb(),
			$addressPrefix . 'city'              => (string)$addressBlock->getCity(),
			$addressPrefix . 'postcode'          => (string)$addressBlock->getPostcode(),
			$addressPrefix . 'state'             => (string)$addressBlock->getCountryZone()->getName(),
			$addressPrefix . 'country'           => (string)$addressBlock->getCountry()->getName(),
			$addressPrefix . 'address_format_id' => (int)$country['address_format_id']
		);
		
		if($prefix->asString() !== 'customers')
		{
			$addressBlockArray[$addressPrefix . 'country_iso_code_2'] = $addressBlock->getCountry()->getIso2();
		}
		
		return $addressBlockArray;
	}
}