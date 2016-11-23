<?php

/* --------------------------------------------------------------
   InvoiceListGenerator.inc.php 24.04.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceListGenerator
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceListGenerator implements InvoiceListGeneratorInterface
{
	/**
	 * @var \CI_DB_query_builder
	 */
	private $db;


	/**
	 * InvoiceListGenerator constructor.
	 *
	 * @param \CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	

	/**
	 * Returns an invoice list item collection by the given conditions.
	 * The other arguments helps to control fetched data.
	 *
	 * @param array            $conditions (Optional) Conditions for tht where clause.
	 * @param \IntType|null    $startIndex (Optional) Start index for the limit clause.
	 * @param \IntType|null    $maxCount   (Optional) Max count for the limit clause.
	 * @param \StringType|null $orderBy    (Optional) Sort order of fetched data.
	 *
	 * @return InvoiceListItemCollection
	 */
	public function getInvoiceListByConditions(array $conditions = [],
	                                           IntType $startIndex = null,
	                                           IntType $maxCount = null,
	                                           StringType $orderBy = null)
	{

		$this->_select()->_limit($startIndex, $maxCount)->_order($orderBy);

		if(count($conditions) > 0)
		{
			$this->db->where($conditions);
		}

		$test = $this->db->get()->result_array();

		return $this->_prepareCollection($test);
	}
	

	/**
	 * Prepares the InvoiceListItemCollection by the passed result array.
	 *
	 * @param array $resultArray Result array with fetched invoice data.
	 *
	 * @return \InvoiceListItemCollection
	 */
	protected function _prepareCollection(array $resultArray)
	{
		$items = [];
		foreach($resultArray as $row)
		{
			$invoiceId          = new IdType($row['invoice_id']);
			$invoiceNumber      = new StringType($row['invoice_number']);
			$invoiceDate        = new DateTime($row['invoice_date']);
			$totalSum           = new DecimalType($row['total_sum']);
			$currencyCode       = MainFactory::create('CurrencyCode', new StringType($row['currency']));
			$customerId         = new IdType($row['customer_id']);
			$customerName       = new StringType($row['customers_firstname'] . ' ' . $row['customers_lastname']);
			$customerStatusId   = new IdType($row['customer_status_id']);
			$customerStatusName = new StringType($row['customer_status_name']);
			$customerMemos      = $this->_createMemoCollectionByCustomersId($row['customer_id']);

			$paymentAddress  = $this->_createOrderAddressBlockByRow('billing', $row);
			$shippingAddress = $this->_createOrderAddressBlockByRow('delivery', $row);

			$orderId            = new IdType($row['order_id']);
			$orderDatePurchased = new DateTime($row['order_date_purchased']);
			$paymentType        = new OrderPaymentType(new StringType($row['payment_class']),
			                                           new StringType($row['payment_method']));

			$orderStatusArray = $this->_createOrderStatusArrayByOrderId($row['order_id']);
			$orderStatusId    = new IdType($orderStatusArray['orders_status']);
			$orderStatusName  = new StringType($orderStatusArray['orders_status_name']);

			$items[] = new InvoiceListItem($invoiceId, $invoiceNumber, $invoiceDate, $totalSum, $currencyCode,
			                               $customerId, $customerName, $customerStatusId, $customerStatusName,
			                               $customerMemos, $paymentAddress, $shippingAddress, $orderId,
			                               $orderDatePurchased, $paymentType, $orderStatusId, $orderStatusName);
		}

		return MainFactory::create('InvoiceListItemCollection', $items);
	}
	

	/**
	 * Creates an order status array by the given order id.
	 * The returned array is associative and contains the
	 * keys orders_status and orders_status_name.
	 *
	 * @param int $orderId orders_id of expected entry.
	 *
	 * @return array Contains the order status id and name.
	 */
	protected function _createOrderStatusArrayByOrderId($orderId)
	{
		$orderStatusId = $this->db->select('orders_status, orders_status.orders_status_name')
		                          ->from('orders')
		                          ->join('orders_status', 'orders.orders_status = orders_status.orders_status_id')
		                          ->where(['orders_id' => $orderId])
		                          ->get()
		                          ->row_array();

		return $orderStatusId;
	}


	/**
	 * Creates an order address block object by the given type and row_array (looped result of CIDB::result_array())
	 *
	 * @param string $type Whether delivery or billing.
	 * @param array  $row  Array which contain data about an order result row.
	 *
	 * @Todo Equal to OrderListGenerator::_createOrderAddressBlockByRow() method. Maybe outsource in abstract parent.
	 *
	 * @return \OrderAddressBlock
	 */
	protected function _createOrderAddressBlockByRow($type, array $row)
	{
		if($type !== 'delivery' && $type !== 'billing')
		{
			throw new BadMethodCallException('the "$type" argument has to be equal to whether "delivery" or "billing"');
		}

		$firstName             = new StringType($row[$type . '_firstname']);
		$lastName              = new StringType($row[$type . '_lastname']);
		$company               = new StringType($row[$type . '_company']);
		$streetAddress         = new StringType($row[$type . '_street_address']);
		$houseNumber           = new StringType($row[$type . '_house_number']);
		$additionalAddressInfo = new StringType($row[$type . '_additional_info']);
		$postCode              = new StringType($row[$type . '_postcode']);
		$city                  = new StringType($row[$type . '_city']);
		$state                 = new StringType($row[$type . '_state']);
		$country               = new StringType($row[$type . '_country']);
		$countryIsoCode        = new StringType($row[$type . '_country_iso_code_2']);

		return MainFactory::create('OrderAddressBlock', $firstName, $lastName, $company, $streetAddress, $houseNumber,
		                           $additionalAddressInfo, $postCode, $city, $state, $country, $countryIsoCode);
	}


	/**
	 * Creates and returns a customer memo collection by the given customers id.
	 *
	 * @param int $customersId Id of customer.
	 *
	 * @Todo Equal to OrderListGenerator::_createMemoCollectionByCustomersId() method. Maybe outsource in abstract
	 *       parent.
	 *
	 * @return \CustomerMemoCollection
	 */
	protected function _createMemoCollectionByCustomersId($customersId)
	{
		$memoArray = $this->db->get_where('customers_memo', array('customers_id' => $customersId))->result_array();
		$memos     = array();

		foreach($memoArray as $customerMemo)
		{
			$memos[] = MainFactory::create('CustomerMemo', MainFactory::create('IdType', $customerMemo['customers_id']),
			                               MainFactory::create('StringType', $customerMemo['memo_title']),
			                               MainFactory::create('StringType', $customerMemo['memo_text']),
			                               MainFactory::create('DateTime', $customerMemo['memo_date']),
			                               MainFactory::create('IdType', $customerMemo['poster_id']));
		}

		return MainFactory::create('CustomerMemoCollection', $memos);
	}


	/**
	 * Execute the select and join methods.
	 *
	 * @return $this|InvoiceListGenerator Returns the instance object for method chaining.
	 */
	protected function _select()
	{
		$this->db->select()->from('invoices');

		return $this;
	}


	/**
	 * Add limit configuration to the database object.
	 *
	 * @param \IntType $startIndex
	 * @param \IntType $maxCount
	 *
	 * @Todo Equal to OrderListGenerator::_limit() method. Maybe outsource in abstract parent.
	 *
	 * @return $this|InvoiceListGenerator Returns the instance object for method chaining.
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
	 * @param \StringType $orderBy
	 *
	 * @Todo Equal to OrderListGenerator::_order() method. Maybe outsource in abstract parent.
	 *
	 * @return $this|InvoiceListGenerator Returns the instance object for method chaining.
	 */
	protected function _order(StringType $orderBy = null)
	{
		if($orderBy)
		{
			$this->db->order_by($orderBy->asString());
		}

		return $this;
	}
}
