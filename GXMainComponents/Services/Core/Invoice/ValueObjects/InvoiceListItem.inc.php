<?php

/* --------------------------------------------------------------
   InvoiceListItem.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceListItem
 * 
 * @category   System
 * @package    Invoice
 * @subpackage ValueObjects
 */
class InvoiceListItem
{
	/**
	 * @var int
	 */
	protected $invoiceId;

	/**
	 * @var string
	 */
	protected $invoiceNumber;

	/**
	 * @var DateTime
	 */
	protected $invoiceDate;

	/**
	 * @var float
	 */
	protected $totalSum;

	/**
	 * @var \CurrencyCode
	 */
	protected $currency;

	/**
	 * @var int
	 */
	protected $customerId;

	/**
	 * @var string
	 */
	protected $customerName;

	/**
	 * @var int
	 */
	protected $customerStatusId;

	/**
	 * @var string
	 */
	protected $customerStatusName;

	/**
	 * @var CustomerMemoCollection
	 */
	protected $customerMemos;

	/**
	 * @var OrderAddressBlock
	 */
	protected $paymentAddress;

	/**
	 * @var OrderAddressBlock
	 */
	protected $shippingAddress;

	/**
	 * @var int
	 */
	protected $orderId;

	/**
	 * @var DateTime
	 */
	protected $orderDatePurchased;

	/**
	 * @var OrderPaymentType
	 */
	protected $paymentType;

	/**
	 * @var int
	 */
	protected $orderStatusId;

	/**
	 * @var string
	 */
	protected $orderStatusName;
	
	
	/**
	 * InvoiceListItem constructor.
	 *
	 * @param \IdType                 $invoiceId
	 * @param \StringType             $invoiceNumber
	 * @param \DateTime               $invoiceDate
	 * @param \DecimalType            $totalSum
	 * @param \CurrencyCode           $currency
	 * @param \IdType                 $customerId
	 * @param \StringType             $customerName
	 * @param \IdType                 $customerStatusId
	 * @param \StringType             $customerStatusName
	 * @param \CustomerMemoCollection $customerMemos
	 * @param \OrderAddressBlock      $paymentAddress
	 * @param \OrderAddressBlock      $shippingAddress
	 * @param \IdType                 $orderId
	 * @param \DateTime               $orderDatePurchased
	 * @param \OrderPaymentType       $paymentType
	 * @param \IdType                 $orderStatusId
	 * @param \StringType             $orderStatusName
	 */
	public function __construct(IdType $invoiceId,
	                            StringType $invoiceNumber,
	                            DateTime $invoiceDate,
	                            DecimalType $totalSum,
	                            CurrencyCode $currency,
	                            IdType $customerId,
	                            StringType $customerName,
	                            IdType $customerStatusId,
	                            StringType $customerStatusName,
	                            CustomerMemoCollection $customerMemos,
	                            OrderAddressBlock $paymentAddress,
	                            OrderAddressBlock $shippingAddress,
	                            IdType $orderId,
	                            DateTime $orderDatePurchased,
	                            OrderPaymentType $paymentType,
	                            IdType $orderStatusId,
	                            StringType $orderStatusName)
	{
		$this->invoiceId          = $invoiceId->asInt();
		$this->invoiceNumber      = $invoiceNumber->asString();
		$this->invoiceDate        = $invoiceDate;
		$this->totalSum           = $totalSum->asDecimal();
		$this->currency           = $currency;
		$this->customerId         = $customerId->asInt();
		$this->customerName       = $customerName->asString();
		$this->customerStatusId   = $customerStatusId->asInt();
		$this->customerStatusName = $customerStatusName->asString();
		$this->customerMemos      = $customerMemos;
		$this->paymentAddress     = $paymentAddress;
		$this->shippingAddress    = $shippingAddress;
		$this->orderId            = $orderId->asInt();
		$this->orderDatePurchased = $orderDatePurchased;
		$this->paymentType        = $paymentType;
		$this->orderStatusId      = $orderStatusId->asInt();
		$this->orderStatusName    = $orderStatusName->asString();
	}


	/**
	 * @return int
	 */
	public function getInvoiceId()
	{
		return $this->invoiceId;
	}


	/**
	 * @return string
	 */
	public function getInvoiceNumber()
	{
		return $this->invoiceNumber;
	}


	/**
	 * @return DateTime
	 */
	public function getInvoiceDate()
	{
		return $this->invoiceDate;
	}


	/**
	 * @return float
	 */
	public function getTotalSum()
	{
		return $this->totalSum;
	}
	
	
	/**
	 * @return CurrencyCode
	 */
	public function getCurrency()
	{
		return $this->currency;
	}


	/**
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}


	/**
	 * @return string
	 */
	public function getCustomerName()
	{
		return $this->customerName;
	}


	/**
	 * @return int
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}


	/**
	 * @return string
	 */
	public function getCustomerStatusName()
	{
		return $this->customerStatusName;
	}


	/**
	 * @return CustomerMemoCollection
	 */
	public function getCustomerMemos()
	{
		return $this->customerMemos;
	}


	/**
	 * @return OrderAddressBlock
	 */
	public function getPaymentAddress()
	{
		return $this->paymentAddress;
	}


	/**
	 * @return OrderAddressBlock
	 */
	public function getShippingAddress()
	{
		return $this->shippingAddress;
	}


	/**
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}


	/**
	 * @return DateTime
	 */
	public function getOrderDatePurchased()
	{
		return $this->orderDatePurchased;
	}


	/**
	 * @return OrderPaymentType
	 */
	public function getPaymentType()
	{
		return $this->paymentType;
	}


	/**
	 * @return int
	 */
	public function getOrderStatusId()
	{
		return $this->orderStatusId;
	}


	/**
	 * @return string
	 */
	public function getOrderStatusName()
	{
		return $this->orderStatusName;
	}
}