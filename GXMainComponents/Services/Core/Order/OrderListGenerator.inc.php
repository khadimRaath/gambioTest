<?php
/* --------------------------------------------------------------
   OrderListGenerator.inc.php 2016-09-26 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . '/inc/get_shipping_title.inc.php';
require_once DIR_FS_CATALOG . '/inc/get_payment_title.inc.php';

MainFactory::load_class('OrderListGeneratorInterface');

/**
 * Class OrderListGenerator
 *
 * @category System
 * @package  Order
 */
class OrderListGenerator implements OrderListGeneratorInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var int
	 */
	protected $defaultLanguageId;
	
	
	/**
	 * OrderListGenerator Constructor
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
		
		$this->defaultLanguageId = $this->db->select('languages_id')
		                                    ->from('languages')
		                                    ->where('code', DEFAULT_LANGUAGE)
		                                    ->get()
		                                    ->row()->languages_id;
	}
	
	
	/**
	 * Get Order List Items
	 *
	 * Returns an order list item collection.
	 *
	 * @link http://www.codeigniter.com/user_guide/database/query_builder.html#looking-for-specific-data
	 *
	 * @param string|array $conditions Provide a WHERE clause string or an associative array (actually any parameter
	 *                                 that is acceptable by the "where" method of the CI_DB_query_builder method).
	 * @param IntType      $startIndex The start index of the wanted array to be returned (default = null).
	 * @param IntType      $maxCount   Maximum amount of items which should be returned (default = null).
	 * @param StringType   $orderBy    A string which defines how the items should be ordered (default = null).
	 *
	 * @return OrderListItemCollection
	 *
	 * @throws InvalidArgumentException If the result rows contain invalid values.
	 */
	public function getOrderListByConditions($conditions = array(),
	                                         IntType $startIndex = null,
	                                         IntType $maxCount = null,
	                                         StringType $orderBy = null)
	{
		$this->_select()->_limit($startIndex, $maxCount)->_order($orderBy)->_group();
		
		$this->db->where('orders_status.language_id', $this->defaultLanguageId)
		         ->where('orders_total.class', 'ot_total');
		
		if(!empty($conditions))
		{
			$this->db->where($conditions);
		}
		
		$result = $this->db->get()->result_array();
		
		return $this->_prepareCollection($result);
	}
	
	
	/**
	 * Filter order list items by the provided parameters.
	 *
	 * The following slug names need to be used:
	 *
	 *   - number => orders.orders_id
	 *   - customer => orders.customers_lastname orders.customers_firstname
	 *   - group => orders.customers_status_name
	 *   - sum => orders_total.value
	 *   - payment => orders.payment_method
	 *   - shipping => orders.shipping_method
	 *   - countryIsoCode => orders.delivery_country_iso_code_2
	 *   - date => orders.date_purchased
	 *   - status => orders_status.orders_status_name
	 *   - totalWeight => orders.order_total_weight
	 *
	 * @param array           $filterParameters Contains the column slug-names and their values.
	 * @param IntType|null    $startIndex       The start index of the wanted array to be returned (default = null).
	 * @param IntType|null    $maxCount         Maximum amount of items which should be returned (default = null).
	 * @param StringType|null $orderBy          A string which defines how the items should be ordered (default = null).
	 *
	 * @return OrderListItemCollection
	 *
	 * @throws BadMethodCallException
	 * @throws InvalidArgumentException
	 */
	public function filterOrderList(array $filterParameters,
	                                IntType $startIndex = null,
	                                IntType $maxCount = null,
	                                StringType $orderBy = null)
	{
		$result = $this->_filter($filterParameters, $startIndex, $maxCount, $orderBy);
		
		return $this->_prepareCollection($result->result_array());
	}
	
	
	/**
	 * Get the filtered orders count.
	 *
	 * This number is useful for pagination functionality where the app needs to know the number of the filtered rows.
	 *
	 * @param array $filterParameters
	 *
	 * @return int
	 *
	 * @throws BadMethodCallException
	 */
	public function filterOrderListCount(array $filterParameters)
	{
		$result = $this->_filter($filterParameters);
		
		return $result->num_rows();
	}
	
	
	/**
	 * Filter records by a single keyword string.
	 *
	 * @param StringType      $keyword    Keyword string to be used for searching in order records.
	 * @param IntType|null    $startIndex The start index of the wanted array to be returned (default = null).
	 * @param IntType|null    $maxCount   Maximum amount of items which should be returned (default = null).
	 * @param StringType|null $orderBy    A string which defines how the items should be ordered (default = null).
	 *
	 * @return mixed
	 *
	 * @throws InvalidArgumentException If the result rows contain invalid values.
	 */
	public function getOrderListByKeyword(StringType $keyword,
	                                      IntType $startIndex = null,
	                                      IntType $maxCount = null,
	                                      StringType $orderBy = null)
	{
		$this->_select()->_limit($startIndex, $maxCount)->_order($orderBy)->_group();
		
		$match = $this->db->escape_like_str($keyword->asString());
		
		$this->db->where('
			orders_total.class = "ot_total" 
			AND orders_status.language_id = ' . $this->defaultLanguageId . ' 
			AND ( 
				orders.orders_id LIKE "%' . $match . '%"
				OR orders.customers_id LIKE "%' . $match . '%"
				OR orders.date_purchased LIKE "%' . $match . '%"
				OR orders.payment_class LIKE "%' . $match . '%"
				OR orders.payment_method LIKE "%' . $match . '%"
				OR orders.shipping_class LIKE "%' . $match . '%"
				OR orders.shipping_method LIKE "%' . $match . '%"
				OR orders.customers_firstname LIKE "%' . $match . '%"
				OR orders.customers_lastname LIKE "%' . $match . '%"
				OR orders_total.value LIKE "%' . $match . '%"
				OR orders_status.orders_status_id LIKE "%' . $match . '%"
				OR orders_status.orders_status_name LIKE "%' . $match . '%"
			)');
		
		$result = $this->db->get()->result_array();
		
		return $this->_prepareCollection($result);
	}
	
	
	/**
	 * Execute the select and join methods.
	 *
	 * @return OrderListGenerator Returns the instance object for method chaining.
	 *
	 * @throws BadMethodCallException
	 */
	protected function _select()
	{
		$columns = [
			$this->_ordersColumns(),
			$this->_ordersStatusColumns(),
			$this->_ordersTotalColumns(),
			$this->_addressColumns('delivery'),
			$this->_addressColumns('billing'),
			$this->_customersStatusColumns()
		];
		
		$this->db->select(implode(', ', $columns))
		         ->from('orders')
		         ->join('orders_status', 'orders_status.orders_status_id = orders.orders_status', 'inner')
		         ->join('orders_total', 'orders_total.orders_id = orders.orders_id', 'left')
		         ->join('customers', 'customers.customers_id = orders.customers_id', 'left')
		         ->join('customers_status', 'customers_status.customers_status_id = orders.customers_status', 'left');
		
		return $this;
	}
	
	
	/**
	 * Returns a string for the ::_select() method which contains column names of the orders table.
	 *
	 * @return string
	 */
	protected function _ordersColumns()
	{
		return 'orders.orders_id, orders.customers_id, orders.date_purchased, orders.payment_class,
			orders.payment_method, orders.shipping_class, orders.shipping_method, orders.customers_name, 
			orders.customers_firstname, orders.customers_lastname, orders.comments, orders.customers_status,  
			orders.customers_status_name, orders.customers_email_address, orders.gm_send_order_status, 
			orders.order_total_weight, orders.customers_company';
	}
	
	
	/**
	 * Returns a string for the ::_select() method which contains column names of the orders status table.
	 *
	 * @return string
	 */
	protected function _ordersStatusColumns()
	{
		return 'orders_status.orders_status_id, orders_status.orders_status_name';
	}
	
	
	/**
	 * Returns a string for the ::_select() method which contains column names of the orders total table.
	 *
	 * @return string
	 */
	protected function _ordersTotalColumns()
	{
		return 'orders_total.value AS total_sum, orders_total.text AS total_sum_text';
	}
	
	
	/**
	 * Returns a string for the ::_select() method which contains column names of the orders table for address data.
	 *
	 * @param string $type Whether delivery or billing.
	 *
	 * @return string
	 *
	 * @throws BadMethodCallException
	 */
	protected function _addressColumns($type)
	{
		if($type !== 'delivery' && $type !== 'billing')
		{
			throw new BadMethodCallException('the "$type" argument has to be equal to whether "delivery" or "billing"');
		}
		
		return 'orders.' . $type . '_firstname, ' . 'orders.' . $type . '_lastname, ' . 'orders.' . $type . '_company, '
		       . 'orders.' . $type . '_street_address, ' . 'orders.' . $type . '_house_number, ' . 'orders.' . $type
		       . '_additional_info, ' . 'orders.' . $type . '_city, ' . 'orders.' . $type . '_postcode, ' . 'orders.'
		       . $type . '_state, ' . 'orders.' . $type . '_country, ' . 'orders.' . $type . '_country_iso_code_2';
	}
	
	
	/**
	 * Returns a string for the ::_select() method which contains fallback customer status name if no value is
	 * set in the orders table.
	 *
	 * @return string
	 */
	protected function _customersStatusColumns()
	{
		return 'customers_status.customers_status_name AS fallback_customers_status';
	}
	
	
	/**
	 * Creates an order address block object by the given type and row_array (looped result of CIDB::result_array())
	 *
	 * @param string $type Whether delivery or billing.
	 * @param array  $row  Array which contain data about an order result row.
	 *
	 * @return OrderAddressBlock
	 *
	 * @throws BadMethodCallException
	 */
	protected function _createOrderAddressBlockByRow($type, array $row)
	{
		if($type !== 'delivery' && $type !== 'billing')
		{
			throw new BadMethodCallException('the "$type" argument has to be equal to whether "delivery" or "billing"');
		}
		
		$firstName             = MainFactory::create('StringType', (string)$row[$type . '_firstname']);
		$lastName              = MainFactory::create('StringType', (string)$row[$type . '_lastname']);
		$company               = MainFactory::create('StringType', (string)$row[$type . '_company']);
		$streetAddress         = MainFactory::create('StringType', (string)$row[$type . '_street_address']);
		$houseNumber           = MainFactory::create('StringType', (string)$row[$type . '_house_number']);
		$additionalAddressInfo = MainFactory::create('StringType', (string)$row[$type . '_additional_info']);
		$postCode              = MainFactory::create('StringType', (string)$row[$type . '_postcode']);
		$city                  = MainFactory::create('StringType', (string)$row[$type . '_city']);
		$state                 = MainFactory::create('StringType', (string)$row[$type . '_state']);
		$country               = MainFactory::create('StringType', (string)$row[$type . '_country']);
		$countryIsoCode        = MainFactory::create('StringType', (string)$row[$type . '_country_iso_code_2']);
		
		return MainFactory::create('OrderAddressBlock', $firstName, $lastName, $company, $streetAddress, $houseNumber,
		                           $additionalAddressInfo, $postCode, $city, $state, $country, $countryIsoCode);
	}
	
	
	/**
	 * Add limit configuration to the database object.
	 *
	 * @param IntType $startIndex
	 * @param IntType $maxCount
	 *
	 * @return OrderListGenerator Returns the instance object for method chaining.
	 */
	protected function _limit(IntType $startIndex = null, IntType $maxCount = null)
	{
		if($maxCount && $startIndex)
		{
			$this->db->limit($maxCount->asInt(), $startIndex->asInt());
		}
		else
		{
			if($maxCount && !$startIndex)
			{
				$this->db->limit($maxCount->asInt());
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Set the order by clause of the query.
	 *
	 * @param StringType $orderBy
	 *
	 * @return OrderListGenerator Returns the instance object for method chaining.
	 */
	protected function _order(StringType $orderBy = null)
	{
		if($orderBy)
		{
			$this->db->order_by($orderBy->asString());
		}
		
		return $this;
	}
	
	
	/**
	 * Execute the group by statement.
	 *
	 * @return OrderListGenerator Returns the instance object for method chaining.
	 */
	protected function _group()
	{
		$this->db->group_by('orders.orders_id, orders_status.orders_status_name, orders_total.value, orders_total.text');
		
		return $this;
	}
	
	
	/**
	 * Prepare the OrderListItemCollection object.
	 *
	 * This method will prepare the collection object which is going to be returned by both
	 * the "get" and "filter" methods. The following values are required to be present in
	 * each row of the $result parameter:
	 *
	 *      - orders_id
	 *      - customers_id
	 *      - customers_firstname
	 *      - customers_lastname
	 *      - date_purchased
	 *      - payment_class
	 *      - payment_method
	 *      - shipping_class
	 *      - shipping_method
	 *      - orders_status_id
	 *      - orders_status_name
	 *      - total_sum
	 *
	 * @param array $result Contains the order data.
	 *
	 * @return OrderListItemCollection
	 *
	 * @throws InvalidArgumentException
	 */
	protected function _prepareCollection(array $result)
	{
		$listItems = array();
		
		foreach($result as $row)
		{
			$orderId           = new IdType((int)$row['orders_id']);
			$customerId        = new IdType((int)$row['customers_id']);
			$customerNameValue = empty($row['customers_firstname'])
			                     && empty($row['customers_lastname']) ? (string)$row['customers_name'] : (string)$row['customers_firstname']
			                                                                                             . ' '
			                                                                                             . (string)$row['customers_lastname'];
			$customerName      = new StringType($customerNameValue);
			$customerEmail     = new StringType((string)$row['customers_email_address']);
			$totalSum          = new StringType((string)str_replace(array('<b> ', '</b>'), '', $row['total_sum_text']));
			$customerCompany   = new StringType((string)$row['customers_company']);
			
			$purchaseDateTime = new EmptyDateTime($row['date_purchased']);
			$orderStatusId    = new IntType((int)$row['orders_status_id']);
			$orderStatusName  = new StringType((string)$row['orders_status_name']);
			
			$comment          = new StringType((string)$row['comments']);
			$customerStatusId = new IdType((int)$row['customers_status']);
			
			$customerStatusName = new StringType(!empty($row['customers_status_name']) ? (string)$row['customers_status_name'] : (string)$row['fallback_customers_status']);
			$totalWeight        = new DecimalType($row['order_total_weight'] ? : 0.0000);
			$mailStatus         = new BoolType((int)$row['gm_send_order_status'] === 1);
			
			$orderListItem = MainFactory::create('OrderListItem');
			
			$orderListItem->setOrderId($orderId);
			$orderListItem->setCustomerId($customerId);
			$orderListItem->setCustomerName($customerName);
			$orderListItem->setCustomerEmail($customerEmail);
			$orderListItem->setCustomerCompany($customerCompany);
			
			$orderListItem->setDeliveryAddress($this->_createOrderAddressBlockByRow('delivery', $row));
			$orderListItem->setBillingAddress($this->_createOrderAddressBlockByRow('billing', $row));
			
			$orderListItem->setComment($comment);
			$orderListItem->setCustomerMemos($this->_createMemoCollectionByCustomersId($row['customers_id']));
			$orderListItem->setCustomerStatusId($customerStatusId);
			$orderListItem->setCustomerStatusName($customerStatusName);
			$orderListItem->setTotalWeight($totalWeight);
			$orderListItem->setTotalSum($totalSum);
			$orderListItem->setPaymentType($this->_createOrderPaymentType($row));
			$orderListItem->setShippingType($this->_createOrderShippingType($row));
			$orderListItem->setTrackingLinks($this->_createTrackingLinksByOrderId($row['orders_id']));
			$orderListItem->setPurchaseDateTime($purchaseDateTime);
			$orderListItem->setOrderStatusId($orderStatusId);
			$orderListItem->setOrderStatusName($orderStatusName);
			$orderListItem->setWithdrawalIds($this->_createWithdrawalIdsByOrderId($row['orders_id']));
			$orderListItem->setMailStatus($mailStatus);
			
			$listItems[] = $orderListItem;
		}
		
		$collection = MainFactory::create('OrderListItemCollection', $listItems);
		
		return $collection;
	}
	
	
	/**
	 * Creates and returns an order payment type instance by the given row data.
	 *
	 * @param array $row Row array with data about the order payment type.
	 *
	 * @return OrderPaymentType
	 */
	protected function _createOrderPaymentType(array $row)
	{
		return $this->_createOrderType('payment', $row);
	}
	
	
	/**
	 * Creates and returns an order shipping type instance by the given row data.
	 *
	 * @param array $row Row array with data about the order shipping type.
	 *
	 * @return OrderShippingType
	 */
	protected function _createOrderShippingType(array $row)
	{
		return $this->_createOrderType('shipping', $row);
	}
	
	
	/**
	 * Creates and returns whether an order shipping or payment type instance by the given row data and type argument.
	 *
	 * @param string $type Whether 'shipping' or 'payment', used to determine the expected order type class.
	 * @param array  $row  Row array with data about the order type.
	 *
	 * @return OrderShippingType|OrderPaymentType
	 *
	 * @throws InvalidArgumentException
	 */
	protected function _createOrderType($type, array $row)
	{
		$explodedMethodName = explode('_', $row[$type . '_method']);
		
		$method = (count($explodedMethodName) === 2
		           && $explodedMethodName[0] === $explodedMethodName[1]) ? $explodedMethodName[0] : $row[$type
		                                                                                                 . '_method'];
		$title  = $method ? call_user_func('get_' . $type . '_title', $method) : '';
		
		$explodedClassName = explode('_', $row[$type . '_class']);
		
		$class = (count($explodedClassName) === 2
		          && $explodedClassName[0] === $explodedClassName[1]) ? $explodedClassName[0] : $row[$type . '_class'];
		
		$configurationValue = $this->db->get_where('configuration', [
			'configuration_key' => 'MODULE_' . strtoupper($type) . '_' . strtoupper($class) . '_ALIAS'
		])->row()->configuration_value;
		
		$alias = $configurationValue ? new StringType($configurationValue) : null;
		
		return MainFactory::create('Order' . ucfirst($type) . 'Type', new StringType($title),
		                           new StringType((string)$row[$type . '_class']), $alias);
	}
	
	
	/**
	 * Creates and returns a customer memo collection by the given customers id.
	 *
	 * @param int $customersId Id of customer.
	 *
	 * @return CustomerMemoCollection
	 */
	protected function _createMemoCollectionByCustomersId($customersId)
	{
		$memoArray = $this->db->get_where('customers_memo', array('customers_id' => $customersId))->result_array();
		$memos     = array();
		
		foreach($memoArray as $customerMemo)
		{
			$memoDate = new DateTime();
			$memoDate->setTimestamp(strtotime($customerMemo['memo_date']));
			
			$memos[] = MainFactory::create('CustomerMemo', MainFactory::create('IdType', $customerMemo['customers_id']),
			                               MainFactory::create('StringType', $customerMemo['memo_title']),
			                               MainFactory::create('StringType', $customerMemo['memo_text']), $memoDate,
			                               MainFactory::create('IdType', $customerMemo['poster_id']));
		}
		
		return MainFactory::create('CustomerMemoCollection', $memos);
	}
	
	
	/**
	 * Creates and returns a string collection which contains the tracking links of the order.
	 *
	 * @param int $orderId Id of current order.
	 *
	 * @return StringCollection
	 */
	protected function _createTrackingLinksByOrderId($orderId)
	{
		$trackingLinksArray = $this->db->get_where('orders_parcel_tracking_codes', array('order_id' => $orderId))
		                               ->result_array();
		$links              = array();
		
		foreach($trackingLinksArray as $trackingLink)
		{
			$links[] = MainFactory::create('StringType', $trackingLink['url']);
		}
		
		return MainFactory::create('StringCollection', $links);
	}
	
	
	/**
	 * Creates and returns a ID collection which contains the withdrawal ids of the order.
	 *
	 * @param int $orderId Id of current order.
	 *
	 * @return IdCollection
	 */
	protected function _createWithdrawalIdsByOrderId($orderId)
	{
		$withdrawalsArray = $this->db->get_where('withdrawals', array('order_id' => $orderId))->result_array();
		$withdrawalIds    = array();
		
		foreach($withdrawalsArray as $withdrawal)
		{
			$withdrawalIds[] = MainFactory::create('IdType', $withdrawal['withdrawal_id']);
		}
		
		return MainFactory::create('IdCollection', $withdrawalIds);
	}
	
	
	/**
	 * Filter the order records.
	 *
	 * This method contains the filtering logic. It can be overloaded in order to provide a custom filtering logic.
	 *
	 * @param array           $filterParameters Contains the column slug-names and their values.
	 * @param IntType|null    $startIndex       The start index of the wanted array to be returned (default = null).
	 * @param IntType|null    $maxCount         Maximum amount of items which should be returned (default = null).
	 * @param StringType|null $orderBy          A string which defines how the items should be ordered (default = null).
	 *
	 * @return CI_DB_result
	 *
	 * @throws BadMethodCallException
	 */
	protected function _filter(array $filterParameters,
	                           IntType $startIndex = null,
	                           IntType $maxCount = null,
	                           StringType $orderBy = null)
	{
		$this->_select()->_limit($startIndex, $maxCount)->_order($orderBy)->_group();
		
		$this->db->where('orders_status.language_id', $this->defaultLanguageId)
		         ->where('orders_total.class', 'ot_total')
		         ->where('customers_status.language_id', $this->defaultLanguageId);
		
		// Filter by order number. 
		if(is_array($filterParameters['number']))
		{
			$this->db->where('orders.orders_id >=', array_shift($filterParameters['number']));
			$this->db->where('orders.orders_id <=', array_shift($filterParameters['number']));
		}
		else if(!empty($filterParameters['number']))
		{
			$this->db->where('orders.orders_id', $filterParameters['number']);
		}
		
		// Filter by customer. 
		if(!empty($filterParameters['customer']))
		{
			$this->db->group_start();
			if(strpos($filterParameters['customer'], '#') === 0)
			{
				$this->db->where('orders.customers_id', substr($filterParameters['customer'], 1));
			}
			else
			{
				$this->db->like('orders.customers_name', $filterParameters['customer'])
				         ->or_like('customers.customers_id', $filterParameters['customer'])
				         ->or_like('customers.customers_cid', $filterParameters['customer'])
				         ->or_like('customers.customers_vat_id', $filterParameters['customer'])
				         ->or_like('customers.customers_gender', $filterParameters['customer'])
				         ->or_like('customers.customers_email_address', $filterParameters['customer'])
				         ->or_like('customers.customers_telephone', $filterParameters['customer'])
				         ->or_like('customers.customers_fax', $filterParameters['customer']);
			}
			$this->db->group_end();
		}
		
		// Filter by customer group.
		if(is_array($filterParameters['group']))
		{
			$groups = $filterParameters['group'];
			$this->db->group_start()->where('orders.customers_status', array_shift($groups));
			foreach($groups as $group)
			{
				$this->db->or_where('orders.customers_status', $group);
			}
			$this->db->group_end();
		}
		
		// Filter by total sum. 
		if(is_array($filterParameters['sum']))
		{
			$this->db->where('orders_total.value >=', $filterParameters['sum'][0]);
			$this->db->where('orders_total.value <=', $filterParameters['sum'][1]);
		}
		else if(!empty($filterParameters['sum']))
		{
			$this->db->where('orders_total.value', $filterParameters['sum']);
		}
		
		// Filter by payment. 
		if(is_array($filterParameters['paymentMethod']))
		{
			$paymentMethods = $filterParameters['paymentMethod'];
			$this->db->group_start()->where('orders.payment_class', array_shift($paymentMethods));
			foreach($paymentMethods as $payment)
			{
				$this->db->or_where('orders.payment_class', $payment);
			}
			$this->db->group_end();
		}
		
		// Filter by shipping method. 
		if(is_array($filterParameters['shippingMethod']))
		{
			$shippingMethods = $filterParameters['shippingMethod'];
			$this->db->group_start()->where('orders.shipping_class', array_shift($shippingMethods));
			foreach($shippingMethods as $shipping)
			{
				$this->db->or_where('orders.shipping_class', $shipping);
			}
			$this->db->group_end();
		}
		
		// Filter by country ISO code. 
		if(is_array($filterParameters['countryIsoCode']))
		{
			$countryIsoCodes = $filterParameters['countryIsoCode'];
			$this->db->group_start()->where('orders.delivery_country_iso_code_2', array_shift($countryIsoCodes));
			foreach($countryIsoCodes as $countryIsoCode)
			{
				$this->db->or_where('orders.delivery_country_iso_code_2', $countryIsoCode);
			}
			$this->db->group_end();
		}
		
		// Filter by purchase date. 
		$dateFormat = ($_SESSION['language_code'] === 'de') ? 'd.m.y' : 'm.d.y';
		if(is_array($filterParameters['date']))
		{
			$dateValue = DateTime::createFromFormat($dateFormat, array_shift($filterParameters['date']));
			$this->db->where('orders.date_purchased >=', $dateValue->format('Y-m-d'));
			$dateValue = DateTime::createFromFormat($dateFormat, array_shift($filterParameters['date']));
			$this->db->where('orders.date_purchased <=', $dateValue->format('Y-m-d') . '23:59:59');
		}
		else if(!empty($filterParameters['date']))
		{
			$dateValue = DateTime::createFromFormat($dateFormat, $filterParameters['date']);
			$this->db->where('orders.date_purchased >=', $dateValue->format('Y-m-d'));
			$this->db->where('orders.date_purchased <=', $dateValue->format('Y-m-d') . ' 23:59:59');
		}
		
		// Filter by order status. 
		if(is_array($filterParameters['status']))
		{
			$this->db->group_start()->where('orders.orders_status', array_shift($filterParameters['status']));
			foreach($filterParameters['status'] as $status)
			{
				$this->db->or_where('orders.orders_status', $status);
			}
			$this->db->group_end();
		}
		
		// Filter by total weight. 
		if(is_array($filterParameters['totalWeight']))
		{
			$this->db->where('orders.order_total_weight >=', array_shift($filterParameters['totalWeight']));
			$this->db->where('orders.order_total_weight <=', array_shift($filterParameters['totalWeight']));
		}
		else if(!empty($filterParameters['totalWeight']))
		{
			$this->db->where('orders.order_total_weight', $filterParameters['totalWeight']);
		}
		
		return $this->db->get();
	}
}