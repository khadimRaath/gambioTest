<?php

/* --------------------------------------------------------------
   OrderRepository.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderRepositoryInterface');

/**
 * Class OrderRepository
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderRepository implements OrderRepositoryInterface
{
	/**
	 * Order factory.
	 * @var OrderFactoryInterface
	 */
	protected $orderFactory;
	
	/**
	 * Addon value service.
	 * @var AddonValueServiceInterface
	 */
	protected $addonValueService;
	
	/**
	 * Order repository writer.
	 * @var OrderRepositoryWriterInterface
	 */
	protected $writer;
	
	/**
	 * Order repository reader.
	 * @var OrderRepositoryReaderInterface
	 */
	protected $reader;
	
	/**
	 * Order repository deleter.
	 * @var OrderRepositoryDeleterInterface
	 */
	protected $deleter;
	
	/**
	 * Order item repository.
	 * @var OrderItemRepositoryInterface
	 */
	protected $orderItemRepository;
	
	/**
	 * Order total repository.
	 * @var OrderTotalRepositoryInterface
	 */
	protected $orderTotalRepository;
	
	/**
	 * Order status history reader.
	 * @var OrderStatusHistoryStorage
	 */
	protected $orderStatusHistoryReader;
	
	
	/**
	 * OrderRepository constructor.
	 *
	 * @param OrderFactoryInterface             $orderFactory          Factory to create order objects.
	 * @param OrderRepositoryWriterInterface    $writer                Order repository writer.
	 * @param OrderRepositoryReaderInterface    $reader                Order repository reader.
	 * @param OrderRepositoryDeleterInterface   $deleter               Order repository deleter.
	 * @param OrderItemRepositoryInterface      $orderItemRepository   Repository for order items.
	 * @param OrderTotalRepositoryInterface     $orderTotalRepository  Repository for order totals.
	 * @param OrderStatusHistoryReaderInterface $historyReader         Reader for order status history items.
	 * @param AddonValueServiceInterface        $addonValueService     AddonValueService to handle the
	 *                                                                 order addon values.
	 */
	public function __construct(OrderFactoryInterface $orderFactory,
	                            OrderRepositoryWriterInterface $writer,
	                            OrderRepositoryReaderInterface $reader,
	                            OrderRepositoryDeleterInterface $deleter,
	                            OrderItemRepositoryInterface $orderItemRepository,
	                            OrderTotalRepositoryInterface $orderTotalRepository,
	                            OrderStatusHistoryReaderInterface $historyReader,
	                            AddonValueServiceInterface $addonValueService)
	{
		$this->orderFactory             = $orderFactory;
		$this->writer                   = $writer;
		$this->reader                   = $reader;
		$this->deleter                  = $deleter;
		$this->orderItemRepository      = $orderItemRepository;
		$this->orderTotalRepository     = $orderTotalRepository;
		$this->orderStatusHistoryReader = $historyReader;
		$this->addonValueService        = $addonValueService;
	}
	
	
	/**
	 * Creates a new order with no values in the database and returns it containing just the ID.
	 *
	 * @return GXEngineOrder Crated order.
	 */
	public function createNew()
	{
		$order   = $this->orderFactory->createOrder();
		$orderId = $this->writer->insert($order);
		$order->setOrderId(new IdType($orderId));
		$order->setOrderStatusHistoryReader($this->orderStatusHistoryReader);
		
		return $order;
	}
	
	
	/**
	 * Saves an Order to the database.
	 *
	 * @param OrderInterface $order Stored order.
	 *
	 * @return OrderRepository Same instance for method chaining.
	 */
	public function store(OrderInterface $order)
	{
		$order->setLastModifiedDateTime(new DateTime());
		
		$orderId    = $order->getOrderId();
		$orderIdObj = new IdType($orderId);
		
		$this->addonValueService->storeAddonValues($order);
		
		$this->writer->update($order);
		
		$orderItemCollection = $order->getOrderItems();
		$orderItemArray      = $orderItemCollection->getArray();
		foreach($orderItemArray as $orderItem)
		{
			if($orderItem instanceof StoredOrderItemInterface)
			{
				$this->orderItemRepository->store($orderItem);
			}
			else
			{
				$this->orderItemRepository->addToOrder($orderIdObj, $orderItem);
			}
		}
		
		$orderTotalCollection = $order->getOrderTotals();
		$orderTotalArray      = $orderTotalCollection->getArray();
		foreach($orderTotalArray as $orderTotal)
		{
			if($orderTotal instanceof StoredOrderTotal)
			{
				$this->orderTotalRepository->store($orderTotal);
			}
			else
			{
				$this->orderTotalRepository->addToOrder($orderIdObj, $orderTotal);
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Returns an order by given ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return GXEngineOrder Fetched order.
	 */
	public function getById(IdType $orderId)
	{
		$order = $this->reader->getById($orderId);
		$order->setOrderStatusHistoryReader($this->orderStatusHistoryReader);
		
		$this->addonValueService->loadAddonValues($order);

		$orderItemCollection = $this->orderItemRepository->getItemsByOrderId($orderId);
		$order->setOrderItems($orderItemCollection);

		$orderTotalCollection = $this->orderTotalRepository->getTotalsByOrderId($orderId);
		$order->setOrderTotals($orderTotalCollection);

		return $order;
	}


	/**
	 * Deletes an order by the ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderRepository Same instance for method chaining.
	 */
	public function deleteById(IdType $orderId)
	{
		$order = $this->reader->getById($orderId);
		
		$this->addonValueService->deleteAddonValues($order);
		
		$this->orderTotalRepository->deleteTotalsByOrderId($orderId);
		$this->orderItemRepository->deleteItemsByOrderId($orderId);
		$this->deleter->deleteById($orderId);

		return $this;
	}
}