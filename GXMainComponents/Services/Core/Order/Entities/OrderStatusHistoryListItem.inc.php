<?php
/* --------------------------------------------------------------
   OrderStatusHistoryListItem.php 2015-12-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class OrderStatusHistoryListItem
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderStatusHistoryListItem
{
	/**
	 * ID of the order status history item.
	 *
	 * @var int
	 */
	protected $orderStatusHistoryId = 0;
	
	/**
	 * ID of the order status.
	 *
	 * @var int
	 */
	protected $orderStatusId = 0;
	
	/**
	 * Order status history item creation date time.
	 *
	 * @var DateTime
	 */
	protected $dateAdded;
	
	/**
	 * Optional comment of the order status history item.
	 *
	 * @var string
	 */
	protected $comment = '';
	
	/**
	 * Customer notified flag.
	 *
	 * @var bool
	 */
	protected $customerNotified = false;
	
	
	/**
	 * OrderStatusHistoryListItem constructor.
	 *
	 * @param IdType     $orderStatusHistoryId Order status history ID.
	 * @param IdType     $orderStatusId        Order status ID.
	 * @param DateTime   $dateAdded            Order status history item creation date time.
	 * @param StringType $comment              Optional comment of the order status history item.
	 * @param BoolType   $customerNotified     Customer notified flag.
	 */
	public function __construct(IdType $orderStatusHistoryId,
	                            IdType $orderStatusId,
	                            DateTime $dateAdded,
	                            StringType $comment,
	                            BoolType $customerNotified)
	{
		$this->orderStatusHistoryId = $orderStatusHistoryId->asInt();
		$this->orderStatusId        = $orderStatusId->asInt();
		$this->dateAdded            = $dateAdded;
		$this->comment              = $comment->asString();
		$this->customerNotified     = $customerNotified->asBool();
	}
	
	
	/**
	 * Returns the ID of the order status history item.
	 * @return int Order status history item ID.
	 */
	public function getOrderStatusHistoryId()
	{
		return $this->orderStatusHistoryId;
	}
	
	
	/**
	 * Returns the ID of the order status.
	 * @return int Order status ID.
	 */
	public function getOrderStatusId()
	{
		return $this->orderStatusId;
	}
	
	
	/**
	 * Returns the order status history item creation date time.
	 * @return DateTime Order status history item creation date time.
	 */
	public function getDateAdded()
	{
		return $this->dateAdded;
	}
	
	
	/**
	 * Returns the comment of the order status history item.
	 * @return string Comment of the order status history item.
	 */
	public function getComment()
	{
		return $this->comment;
	}
	
	
	/**
	 * Returns the value of customer notified flag.
	 * @return boolean Customer is notified?
	 */
	public function isCustomerNotified()
	{
		return $this->customerNotified;
	}
}