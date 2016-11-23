<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepository.inc.php 2015-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderItemAttributeRepository
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemAttributeRepository implements OrderItemAttributeRepositoryInterface
{
	/**
	 * Order item attribute repository writer.
	 * @var OrderItemAttributeRepositoryWriterInterface
	 */
	protected $writer;

	/**
	 * Order item attribute repository reader.
	 * @var OrderItemAttributeRepositoryReaderInterface
	 */
	protected $reader;

	/**
	 * Order item attribute repository deleter.
	 * @var OrderItemAttributeRepositoryDeleterInterface
	 */
	protected $deleter;


	/**
	 * OrderItemAttributeRepository constructor.
	 *
	 * @param OrderItemAttributeRepositoryReaderInterface  $reader  Order item attribute repository reader.
	 * @param OrderItemAttributeRepositoryWriterInterface  $writer  Order item attribute repository writer.
	 * @param OrderItemAttributeRepositoryDeleterInterface $deleter Order item attribute repository deleter.
	 */
	public function __construct(OrderItemAttributeRepositoryReaderInterface $reader,
	                            OrderItemAttributeRepositoryWriterInterface $writer,
	                            OrderItemAttributeRepositoryDeleterInterface $deleter)
	{
		$this->writer  = $writer;
		$this->reader  = $reader;
		$this->deleter = $deleter;
	}


	/**
	 * Adds an attribute to an order item.
	 *
	 * @param IdType                      $orderItemId        ID of the order item.
	 * @param OrderItemAttributeInterface $orderItemAttribute Order item attribute to add.
	 *
	 * @return int ID of stored order item attribute.
	 */
	public function addToOrderItem(IdType $orderItemId, OrderItemAttributeInterface $orderItemAttribute)
	{
		return $this->writer->insertIntoOrderItem($orderItemId, $orderItemAttribute);
	}
	
	
	/**
	 * Saves the attribute to the repository.
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemAttribute Attribute to save.
	 *
	 * @return OrderItemAttributeRepositoryInterface Same instance for method chaining.
	 */
	public function store(StoredOrderItemAttributeInterface $orderItemAttribute)
	{
		$this->writer->update($orderItemAttribute);

		return $this;
	}
	
	
	/**
	 * Returns a stored attribute by the given ID.
	 *
	 * @param IdType $orderItemAttributeId ID of item attribute.
	 *
	 * @return StoredOrderItemAttributeInterface Stored attribute.
	 */
	public function getItemAttributeById(IdType $orderItemAttributeId)
	{
		return $this->reader->getAttributeById($orderItemAttributeId);
	}
	
	
	/**
	 * Returns a stored attribute collection by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return StoredOrderItemAttributeCollection Stored item attribute collection.
	 */
	public function getItemAttributesByOrderItemId(IdType $orderItemId)
	{
		return $this->reader->getAttributesByOrderItemId($orderItemId);
	}
	
	
	/**
	 * Deletes an item attribute by the given item attribute ID.
	 *
	 * @param IdType $orderItemAttributeId ID of order item attribute.
	 *
	 * @return OrderItemAttributeRepository Same instance for method chaining.
	 */
	public function deleteItemAttributeById(IdType $orderItemAttributeId)
	{
		$this->deleter->deleteAttributeById($orderItemAttributeId);

		return $this;
	}
	
	
	/**
	 * Deletes an item attribute by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return OrderItemAttributeRepository Same instance for method chaining.
	 */
	public function deleteItemAttributesByOrderItemId(IdType $orderItemId)
	{
		$this->deleter->deleteAttributesByOrderItemId($orderItemId);

		return $this;
	}
}