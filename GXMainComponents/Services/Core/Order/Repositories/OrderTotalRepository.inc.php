<?php

/* --------------------------------------------------------------
   OrderTotalRepository.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderTotalRepositoryInterface');

/**
 * Class OrderTotalRepository
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderTotalRepository implements OrderTotalRepositoryInterface
{
	/**
	 * Order total repository reader.
	 * @var OrderTotalRepositoryReaderInterface
	 */
	protected $orderTotalRepositoryReader;
	
	/**
	 * Order total repository writer.
	 * @var OrderTotalRepositoryWriterInterface
	 */
	protected $orderTotalRepositoryWriter;
	
	/**
	 * #Order total repository deleter.
	 * @var OrderTotalRepositoryDeleterInterface
	 */
	protected $orderTotalRepositoryDeleter;
	

	/**
	 * OrderTotalRepository constructor.
	 *
	 * @param OrderTotalRepositoryReader  $orderTotalRepositoryReader  Order total repository reader.
	 * @param OrderTotalRepositoryWriter  $orderTotalRepositoryWriter  Order total repository writer.
	 * @param OrderTotalRepositoryDeleter $orderTotalRepositoryDeleter Order total repository deleter.
	 */
	public function __construct(OrderTotalRepositoryReader $orderTotalRepositoryReader,
	                            OrderTotalRepositoryWriter $orderTotalRepositoryWriter,
	                            OrderTotalRepositoryDeleter $orderTotalRepositoryDeleter)
	{
		$this->orderTotalRepositoryReader  = $orderTotalRepositoryReader;
		$this->orderTotalRepositoryWriter  = $orderTotalRepositoryWriter;
		$this->orderTotalRepositoryDeleter = $orderTotalRepositoryDeleter;
	}
	
	
	/**
	 * Adds an order total object to the order.
	 *
	 * @param IdType              $orderId    ID of order.
	 * @param OrderTotalInterface $orderTotal Order total object.
	 *
	 * @return int ID of stored order total.
	 */
	public function addToOrder(IdType $orderId, OrderTotalInterface $orderTotal)
	{
		return $this->orderTotalRepositoryWriter->insertIntoOrder($orderId, $orderTotal);
	}
	
	
	/**
	 * Updates a stored order total object.
	 *
	 * @param StoredOrderTotalInterface $orderTotal Order total.
	 *
	 * @return OrderTotalRepository Same instance for method chaining.
	 */
	public function store(StoredOrderTotalInterface $orderTotal)
	{
		$this->orderTotalRepositoryWriter->update($orderTotal);

		return $this;
	}
	
	
	/**
	 * Returns an order total object by the given ID.
	 *
	 * @param IdType $orderTotalId ID of order total in database table.
	 *
	 * @return StoredOrderTotal Fetched order total.
	 */
	public function getTotalById(IdType $orderTotalId)
	{
		return $this->orderTotalRepositoryReader->getTotalById($orderTotalId);
	}
	
	
	/**
	 * Returns an collection of order total objects by the given order ID.
	 *
	 * @param IdType $orderId ID of the order in the database table.
	 *
	 * @return StoredOrderTotalCollection Fetched order total collection.
	 */
	public function getTotalsByOrderId(IdType $orderId)
	{
		return $this->orderTotalRepositoryReader->getTotalsByOrderId($orderId);
	}
	
	
	/**
	 * Removes an order total by the given order total ID.
	 *
	 * @param IdType $orderTotalId ID of order total in the database table.
	 *
	 * @return OrderTotalRepository Same instance for method chaining.
	 */
	public function deleteTotalById(IdType $orderTotalId)
	{
		$this->orderTotalRepositoryDeleter->deleteTotalById($orderTotalId);

		return $this;
	}
	
	
	/**
	 * Removes multiple order totals by the given order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderTotalRepository Same instance for method chaining.
	 */
	public function deleteTotalsByOrderId(IdType $orderId)
	{
		$this->orderTotalRepositoryDeleter->deleteTotalsByOrderId($orderId);

		return $this;
	}
}