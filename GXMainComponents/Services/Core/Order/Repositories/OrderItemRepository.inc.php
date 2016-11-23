<?php
/* --------------------------------------------------------------
   OrderItemRepository.inc.php 2015-12-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemRepositoryInterface');

/**
 * Class OrderItemRepository
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemRepository implements OrderItemRepositoryInterface
{
	/**
	 * Order item attribute repository factory.
	 * @var OrderItemAttributeRepositoryFactoryInterface
	 */
	protected $orderItemAttributeRepositoryFactory;
	
	/**
	 * Order item repository reader.
	 * @var OrderItemRepositoryReaderInterface
	 */
	protected $orderItemRepositoryReader;
	
	/**
	 * Order item repository writer.
	 * @var OrderItemRepositoryWriterInterface
	 */
	protected $orderItemRepositoryWriter;
	
	/**
	 * Order item repository deleter.
	 * @var OrderItemRepositoryDeleterInterface
	 */
	protected $orderItemRepositoryDeleter;
	
	/**
	 * Addon value service.
	 * @var AddonValueServiceInterface
	 */
	protected $addonValueService;
	
	
	/**
	 * OrderItemRepository constructor.
	 *
	 * @param OrderItemAttributeRepositoryFactoryInterface $orderItemAttributeRepositoryFactory  Factory to create
	 *                                                                                           order item attribute
	 *                                                                                           repositories.
	 * @param OrderItemRepositoryReaderInterface           $orderItemRepositoryReader            Db reader for order
	 *                                                                                           item repository.
	 * @param OrderItemRepositoryWriterInterface           $orderItemRepositoryWriter            Db writer for order
	 *                                                                                           item repository.
	 * @param OrderItemRepositoryDeleterInterface          $orderItemRepositoryDeleter           Db deleter for order
	 *                                                                                           item repository.
	 * @param AddonValueServiceInterface                   $addonValueService                    Addon value service.
	 */
	public function __construct(OrderItemAttributeRepositoryFactoryInterface $orderItemAttributeRepositoryFactory,
	                            OrderItemRepositoryReaderInterface $orderItemRepositoryReader,
	                            OrderItemRepositoryWriterInterface $orderItemRepositoryWriter,
	                            OrderItemRepositoryDeleterInterface $orderItemRepositoryDeleter,
	                            AddonValueServiceInterface $addonValueService)
	{
		$this->orderItemAttributeRepositoryFactory = $orderItemAttributeRepositoryFactory;
		$this->orderItemRepositoryReader           = $orderItemRepositoryReader;
		$this->orderItemRepositoryWriter           = $orderItemRepositoryWriter;
		$this->orderItemRepositoryDeleter          = $orderItemRepositoryDeleter;
		$this->addonValueService                   = $addonValueService;
	}
	
	
	/**
	 * Adds an order item to the order item repository.
	 *
	 * @param IdType             $orderId   Order ID.
	 * @param OrderItemInterface $orderItem Order item to add.
	 *
	 * @return int ID of the StoredOrderItem
	 */
	public function addToOrder(IdType $orderId, OrderItemInterface $orderItem)
	{
		$storedOrderItemId = $this->orderItemRepositoryWriter->insertIntoOrder($orderId, $orderItem);
		
		/** @var AddonValueContainerInterface $storedOrderItem */
		$storedOrderItem = $this->getItemById(new IdType($storedOrderItemId));
		$storedOrderItem->addAddonValues($orderItem->getAddonValues());
		$this->addonValueService->storeAddonValues($storedOrderItem);
		
		$this->_delegateToAttributeRepositories($orderItem, $storedOrderItemId);
		
		return $storedOrderItemId;
	}
	
	
	/**
	 * Saves the order item in the repository.
	 *
	 * @param StoredOrderItemInterface $storedOrderItem Order item to save.
	 *
	 * @return OrderItemRepository Same instance for method chaining.
	 */
	public function store(StoredOrderItemInterface $storedOrderItem)
	{
		$this->orderItemRepositoryWriter->update($storedOrderItem);
		$this->addonValueService->storeAddonValues($storedOrderItem);
		
		return $this->_delegateToAttributeRepositories($storedOrderItem);
	}
	
	
	/**
	 * Returns a stored order ID by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the stored order item.
	 *
	 * @return StoredOrderItemInterface Stored order item.
	 */
	public function getItemById(IdType $orderItemId)
	{
		/** @var StoredOrderItemInterface|AddonValueContainerInterface $storedOrderItem */
		$storedOrderItem = $this->orderItemRepositoryReader->getItemById($orderItemId);
		$repositoryArray = $this->orderItemAttributeRepositoryFactory->createRepositoryArray();
		
		/** @var StoredOrderItemAttributeCollection $attributeCollection */
		$attributeCollection = null;
		foreach($repositoryArray as $repository)
		{
			if(null === $attributeCollection)
			{
				$attributeCollection = $repository->getItemAttributesByOrderItemId(new IdType($storedOrderItem->getOrderItemId()));
			}
			else
			{
				$newCollection = $repository->getItemAttributesByOrderItemId(new IdType($storedOrderItem->getOrderItemId()));
				$attributeCollection->addCollection($newCollection);
			}
		}
		
		$storedOrderItem->setAttributes($attributeCollection);
		$this->addonValueService->loadAddonValues($storedOrderItem);
		
		return $storedOrderItem;
	}
	
	
	/**
	 * Returns a stored order item collection by the given order ID.
	 *
	 * @param IdType $orderId ID of the order.
	 *
	 * @return StoredOrderItemCollection Stored order item collection.
	 */
	public function getItemsByOrderId(IdType $orderId)
	{
		$orderItemCollection      = $this->orderItemRepositoryReader->getItemsByOrderId($orderId);
		$attributeRepositoryArray = $this->orderItemAttributeRepositoryFactory->createRepositoryArray();
		
		/** @var StoredOrderItemInterface|AddonValueContainerInterface $orderItem */
		foreach($orderItemCollection->getArray() as $orderItem)
		{
			$orderItemId = $orderItem->getOrderItemId();
			
			/** @var StoredOrderItemAttributeCollection $attributeCollection */
			$attributeCollection = null;
			foreach($attributeRepositoryArray as $attributeRepository)
			{
				if(null === $attributeCollection)
				{
					$attributeCollection = $attributeRepository->getItemAttributesByOrderItemId(new IdType($orderItemId));
				}
				else
				{
					$newCollection = $attributeRepository->getItemAttributesByOrderItemId(new IdType($orderItemId));
					$attributeCollection->addCollection($newCollection);
				}
			}
			
			$orderItem->setAttributes($attributeCollection);
			$this->addonValueService->loadAddonValues($orderItem);
		}
		
		return $orderItemCollection;
	}
	
	
	/**
	 * Deletes an order item from the repository by the given order item ID.
	 *
	 * @param IdType $orderItemId Order item ID.
	 *
	 * @return OrderItemRepository Same instance for method chaining.
	 */
	public function deleteItemById(IdType $orderItemId)
	{
		$repositoryArray = $this->orderItemAttributeRepositoryFactory->createRepositoryArray();
		
		foreach($repositoryArray as $repository)
		{
			$repository->deleteItemAttributesByOrderItemId($orderItemId);
		}
		
		/** @var AddonValueContainerInterface $orderItem */
		$orderItem = $this->orderItemRepositoryReader->getItemById($orderItemId);
		$this->addonValueService->deleteAddonValues($orderItem);
		
		$this->orderItemRepositoryDeleter->deleteItemById($orderItemId);
		
		return $this;
	}
	
	
	/**
	 * Deletes order items from the repository by the given order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderItemRepository Same instance for method chaining.
	 */
	public function deleteItemsByOrderId(IdType $orderId)
	{
		$orderItemCollection = $this->orderItemRepositoryReader->getItemsByOrderId($orderId);
		
		/** @var StoredOrderItemInterface $orderItem */
		foreach($orderItemCollection->getArray() as $orderItem)
		{
			$this->deleteItemById(new IdType($orderItem->getOrderItemId()));
		}
		
		return $this;
	}
	
	
	/**
	 * Delegate to the attribute repository and save the attributes of the passed order item.
	 *
	 * @param OrderItemInterface $orderItem          Order item which contain the attributes.
	 * @param int|null           $storedOrderItemId  (Optional) Id of order item. When not set, its get by stored order
	 *                                               item.
	 *
	 * @return $this Same instance to make chained method calls possible.
	 */
	protected function _delegateToAttributeRepositories(OrderItemInterface $orderItem, $storedOrderItemId = null)
	{
		if(null === $storedOrderItemId && $orderItem instanceof StoredOrderItemInterface)
		{
			$storedOrderItemId = $orderItem->getOrderItemId();
		}
		$collection     = $orderItem->getAttributes();
		$attributeArray = $collection->getArray();
		
		foreach($attributeArray as $attribute)
		{
			$repository = $this->orderItemAttributeRepositoryFactory->createRepositoryByAttributeObject($attribute);
			
			if($attribute instanceof StoredOrderItemAttributeInterface)
			{
				$repository->store($attribute);
			}
			else
			{
				$repository->addToOrderItem(new IdType($storedOrderItemId), $attribute);
			}
		}
		
		return $this;
	}
	
}