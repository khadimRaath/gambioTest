<?php

/* --------------------------------------------------------------
   OrderItemPropertyRepository.php 2015-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeRepositoryInterface');


/**
 * Class OrderItemPropertyRepository
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemPropertyRepository implements OrderItemAttributeRepositoryInterface
{
	/**
	 * Order item property repository reader.
	 * @var OrderItemPropertyRepositoryReaderInterface
	 */
	protected $reader;

	/**
	 * Order item property repository writer.
	 * @var OrderItemPropertyRepositoryWriterInterface
	 */
	protected $writer;

	/**
	 * Order item property repository deleter.
	 * @var OrderItemPropertyRepositoryDeleterInterface
	 */
	protected $deleter;


	/**
	 * OrderItemPropertyRepository constructor.
	 *
	 * @param OrderItemPropertyRepositoryReaderInterface  $reader  Order item property repository reader.
	 * @param OrderItemPropertyRepositoryWriterInterface  $writer  Order item property repository writer.
	 * @param OrderItemPropertyRepositoryDeleterInterface $deleter Order item property repository deleter.
	 */
	public function __construct(OrderItemPropertyRepositoryReaderInterface $reader,
	                            OrderItemPropertyRepositoryWriterInterface $writer,
	                            OrderItemPropertyRepositoryDeleterInterface $deleter)
	{
		$this->reader  = $reader;
		$this->writer  = $writer;
		$this->deleter = $deleter;
	}


	/**
	 * Adds an attribute to an order item.
	 *
	 * @param IdType                      $orderItemId       ID of the order item.
	 * @param OrderItemAttributeInterface $orderItemProperty Order item attribute to add.
	 *
	 * @return int ID of stored order item attribute.
	 */
	public function addToOrderItem(IdType $orderItemId, OrderItemAttributeInterface $orderItemProperty)
	{
		return $this->writer->insertIntoOrderItem($orderItemId, $orderItemProperty);
	}
	
	
	/**
	 * Saves the attribute to the repository.
	 *
	 * @param StoredOrderItemAttributeInterface $orderItemProperty Property to save.
	 *
	 * @return OrderItemPropertyRepository Same instance for method chaining.
	 */
	public function store(StoredOrderItemAttributeInterface $orderItemProperty)
	{
		$this->writer->update($orderItemProperty);

		return $this;
	}
	
	
	/**
	 * Returns a stored property by the given ID.
	 *
	 * @param IdType $orderItemPropertyId ID of item property.
	 *
	 * @return StoredOrderItemAttributeInterface Stored property.
	 */
	public function getItemAttributeById(IdType $orderItemPropertyId)
	{
		return $this->reader->getPropertyById($orderItemPropertyId);
	}
	
	
	/**
	 * Returns a stored property collection by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return StoredOrderItemAttributeCollection Stored item property collection.
	 */
	public function getItemAttributesByOrderItemId(IdType $orderItemId)
	{
		return $this->reader->getPropertiesByOrderItemId($orderItemId);
	}
	
	
	/**
	 * Deletes an item property by the given item property ID.
	 *
	 * @param IdType $orderItemAttributeId ID of order item property.
	 *
	 * @return OrderItemPropertyRepository Same instance for method chaining.
	 */
	public function deleteItemAttributeById(IdType $orderItemAttributeId)
	{
		$this->deleter->deletePropertyById($orderItemAttributeId);

		return $this;
	}
	
	
	/**
	 * Deletes an item property by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return OrderItemPropertyRepository Same instance for method chaining.
	 */
	public function deleteItemAttributesByOrderItemId(IdType $orderItemId)
	{
		$this->deleter->deletePropertiesByOrderItemId($orderItemId);

		return $this;
	}
}