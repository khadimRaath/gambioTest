<?php

/* --------------------------------------------------------------
   OrderListItem.inc.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderListItem
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderListItem
{
	/**
	 * @var int
	 */
	protected $orderId = 0;
	
	/**
	 * @var DateTime
	 */
	protected $purchaseDateTime;
	
	/**
	 * @var int
	 */
	protected $orderStatusId = 0;
	
	/**
	 * @var string
	 */
	protected $orderStatusName = '';
	
	/**
	 * @var int
	 */
	protected $customerId = 0;
	
	/**
	 * @var string
	 */
	protected $customerName = '';
	
	/**
	 * @var string
	 */
	protected $customerCompany = '';
	
	/**
	 * @var string
	 */
	protected $customerEmail = '';
	
	/**
	 * @var int
	 */
	protected $customerStatusId = 0;
	
	/**
	 * @var string
	 */
	protected $customerStatusName = '';
	
	/**
	 * @var CustomerMemoCollection
	 */
	protected $customerMemos;
	
	/**
	 * @var OrderAddressBlock
	 */
	protected $deliveryAddress;
	
	/**
	 * @var OrderAddressBlock
	 */
	protected $billingAddress;
	
	/**
	 * @var string
	 */
	protected $comment = '';
	
	/**
	 * @var float
	 */
	protected $totalWeight = 0.0;
	
	/**
	 * @var string
	 */
	protected $totalSum = '';
	
	/**
	 * @var OrderPaymentTypeInterface
	 */
	protected $paymentType;
	
	/**
	 * @var OrderShippingTypeInterface
	 */
	protected $shippingType;
	
	/**
	 * @var StringCollection
	 */
	protected $trackingLinks;
	
	/**
	 * @var IdCollection
	 */
	protected $withdrawalIds;
	
	/**
	 * @var bool
	 */
	protected $mailStatus = false;
	
	
	/**
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}
	
	
	/**
	 * @param IdType $orderId
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setOrderId(IdType $orderId)
	{
		$this->orderId = $orderId->asInt();
		
		return $this;
	}
	
	
	/**
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}
	
	
	/**
	 * @param IdType $customerId
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerId(IdType $customerId)
	{
		$this->customerId = $customerId->asInt();
		
		return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getCustomerName()
	{
		return $this->customerName;
	}
	
	
	/**
	 * @param StringType $customerName
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerName(StringType $customerName)
	{
		$this->customerName = $customerName->asString();
		
		return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getCustomerEmail()
	{
		return $this->customerEmail;
	}
	
	
	/**
	 * @param StringType $customerEmail
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerEmail(StringType $customerEmail)
	{
		$this->customerEmail = $customerEmail->asString();
		
		return $this;
	}
	
	
	/**
	 * @return OrderAddressBlock
	 */
	public function getDeliveryAddress()
	{
		return $this->deliveryAddress;
	}
	
	
	/**
	 * @param OrderAddressBlock $deliveryAddress
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setDeliveryAddress(OrderAddressBlock $deliveryAddress)
	{
		$this->deliveryAddress = $deliveryAddress;
		
		return $this;
	}
	
	
	/**
	 * @return OrderAddressBlock
	 */
	public function getBillingAddress()
	{
		return $this->billingAddress;
	}
	
	
	/**
	 * @param OrderAddressBlock $billingAddress
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setBillingAddress(OrderAddressBlock $billingAddress)
	{
		$this->billingAddress = $billingAddress;
		
		return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}
	
	
	/**
	 * @param StringType $comment
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setComment(StringType $comment)
	{
		$this->comment = $comment->asString();
		
		return $this;
	}
	
	
	/**
	 * @return CustomerMemoCollection
	 */
	public function getCustomerMemos()
	{
		return $this->customerMemos;
	}
	
	
	/**
	 * @param CustomerMemoCollection $customerMemos
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerMemos(CustomerMemoCollection $customerMemos)
	{
		$this->customerMemos = $customerMemos;
		
		return $this;
	}
	
	
	/**
	 * @return int
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}
	
	
	/**
	 * @param IdType $customerStatusId
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerStatusId(IdType $customerStatusId)
	{
		$this->customerStatusId = $customerStatusId->asInt();
		
		return $this;
	}
	
	
	/**
	 * @return mixed
	 */
	public function getCustomerStatusName()
	{
		return $this->customerStatusName;
	}
	
	
	/**
	 * @param StringType $customerStatusName
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerStatusName(StringType $customerStatusName)
	{
		$this->customerStatusName = $customerStatusName->asString();
		
		return $this;
	}
	
	
	/**
	 * @return float
	 */
	public function getTotalWeight()
	{
		return $this->totalWeight;
	}
	
	
	/**
	 * @param DecimalType $totalWeight
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setTotalWeight(DecimalType $totalWeight)
	{
		$this->totalWeight = $totalWeight->asDecimal();
		
		return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getTotalSum()
	{
		return $this->totalSum;
	}
	
	
	/**
	 * @param StringType $totalSum
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setTotalSum(StringType $totalSum)
	{
		$this->totalSum = $totalSum->asString();
		
		return $this;
	}
	
	
	/**
	 * @return OrderPaymentType
	 */
	public function getPaymentType()
	{
		return $this->paymentType;
	}
	
	
	/**
	 * @param OrderPaymentTypeInterface $paymentType
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setPaymentType(OrderPaymentTypeInterface $paymentType)
	{
		$this->paymentType = $paymentType;
		
		return $this;
	}
	
	
	/**
	 * @return OrderShippingType
	 */
	public function getShippingType()
	{
		return $this->shippingType;
	}
	
	
	/**
	 * @param OrderShippingTypeInterface $shippingType
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setShippingType(OrderShippingTypeInterface $shippingType)
	{
		$this->shippingType = $shippingType;
		
		return $this;
	}
	
	
	/**
	 * @return StringCollection
	 */
	public function getTrackingLinks()
	{
		return $this->trackingLinks;
	}
	
	
	/**
	 * @param StringCollection $trackingLinks
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setTrackingLinks(StringCollection $trackingLinks)
	{
		$this->trackingLinks = $trackingLinks;
		
		return $this;
	}
	
	
	/**
	 * @return DateTime
	 */
	public function getPurchaseDateTime()
	{
		return $this->purchaseDateTime;
	}
	
	
	/**
	 * @param DateTime $purchaseDateTime
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setPurchaseDateTime(DateTime $purchaseDateTime)
	{
		$this->purchaseDateTime = $purchaseDateTime;
		
		return $this;
	}
	
	
	/**
	 * @return int
	 */
	public function getOrderStatusId()
	{
		return $this->orderStatusId;
	}
	
	
	/**
	 * @param IntType $orderStatusId
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setOrderStatusId(IntType $orderStatusId)
	{
		$this->orderStatusId = $orderStatusId->asInt();
		
		return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getOrderStatusName()
	{
		return $this->orderStatusName;
	}
	
	
	/**
	 * @param StringType $orderStatusName
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setOrderStatusName(StringType $orderStatusName)
	{
		$this->orderStatusName = $orderStatusName->asString();
		
		return $this;
	}
	
	
	/**
	 * @return IdCollection
	 */
	public function getWithdrawalIds()
	{
		return $this->withdrawalIds;
	}
	
	
	/**
	 * @param IdCollection $withdrawalIds
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setWithdrawalIds(IdCollection $withdrawalIds)
	{
		$this->withdrawalIds = $withdrawalIds;
		
		return $this;
	}
	
	
	/**
	 * @return bool
	 */
	public function getMailStatus()
	{
		return $this->mailStatus;
	}
	
	
	/**
	 * @param BoolType $mailStatus
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setMailStatus(BoolType $mailStatus)
	{
		$this->mailStatus = $mailStatus->asBool();
		
		return $this;
	}
	
	
	/**
	 * @return string
	 */
	public function getCustomerCompany()
	{
		return $this->customerCompany;
	}
	
	
	/**
	 * @param StringType $customerCompany
	 *
	 * @return $this|OrderListItem Same instance for chained method calls.
	 */
	public function setCustomerCompany(StringType $customerCompany)
	{
		$this->customerCompany = $customerCompany->asString();

		return $this;
	}
	
	
}
