<?php
/* --------------------------------------------------------------
   OrderServiceFactory.php 2015-12-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractOrderServiceFactory');

/**
 * Class OrderServiceFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
class OrderServiceFactory extends AbstractOrderServiceFactory
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderServiceFactory constructor.
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Creates and returns an order write service object.
	 *
	 * @return OrderWriteService New order write service object.
	 */
	public function createOrderWriteService()
	{
		$orderRepository                     = $this->_createOrderRepository();
		$orderItemRepository                 = $this->_createOrderItemRepository();
		$orderItemAttributeRepositoryFactory = $this->_createOrderItemAttributeRepositoryFactory();
		$orderTotalRepository                = $this->_createOrderTotalRepository();
		$orderStatusHistoryReader            = $this->_createOrderStatusHistoryStorage();
		$orderServiceSettings                = $this->_createOrderServiceSettings();
		
		return MainFactory::create('OrderWriteService', $orderRepository, $orderItemRepository,
		                           $orderItemAttributeRepositoryFactory, $orderTotalRepository,
		                           $orderStatusHistoryReader, $orderServiceSettings);
	}
	
	
	/**
	 * Creates and returns an order read service object.
	 *
	 * @return OrderReadService New order read service object.
	 */
	public function createOrderReadService()
	{
		$orderRepository     = $this->_createOrderRepository();
		$orderItemRepository = $this->_createOrderItemRepository();
		$orderListGenerator  = $this->_createOrderListGenerator();
		
		return MainFactory::create('OrderReadService', $orderRepository, $orderItemRepository, $orderListGenerator);
	}
	
	
	/**
	 * Creates and returns an order object service.
	 *
	 * @return OrderObjectService New order object service.
	 */
	public function createOrderObjectService()
	{
		$orderItemFactory          = MainFactory::create('OrderItemFactory');
		$orderItemAttributeFactory = MainFactory::create('OrderItemAttributeFactory');
		$orderItemPropertyFactory  = MainFactory::create('OrderItemPropertyFactory');
		$orderTotalFactory         = MainFactory::create('OrderTotalFactory');
		
		return MainFactory::create('OrderObjectService', $orderItemFactory, $orderItemAttributeFactory,
		                           $orderItemPropertyFactory, $orderTotalFactory);
	}
	
	
	/**
	 * Creates and returns an order repository.
	 *
	 * @return OrderRepository New order repository.
	 */
	protected function _createOrderRepository()
	{
		$orderFactory             = MainFactory::create('OrderFactory');
		$countryService           = StaticGXCoreLoader::getService('Country');
		$orderRepositoryReader    = MainFactory::create('OrderRepositoryReader', $this->db, $orderFactory,
		                                                $countryService);
		$orderRepositoryWriter    = MainFactory::create('OrderRepositoryWriter', $this->db);
		$orderRepositoryDeleter   = MainFactory::create('OrderRepositoryDeleter', $this->db);
		$orderItemRepository      = $this->_createOrderItemRepository();
		$orderTotalRepository     = $this->_createOrderTotalRepository();
		$orderStatusHistoryReader = $this->_createOrderStatusHistoryStorage();
		$addonValueService        = MainFactory::create('AddonValueService',
		                                                MainFactory::create('AddonValueStorageFactory', $this->db));
		
		return MainFactory::create('OrderRepository', $orderFactory, $orderRepositoryWriter, $orderRepositoryReader,
		                           $orderRepositoryDeleter, $orderItemRepository, $orderTotalRepository,
		                           $orderStatusHistoryReader, $addonValueService);
	}
	
	
	/**
	 * Creates and returns an order item repository.
	 *
	 * @return OrderItemRepository New order item repository.
	 */
	protected function _createOrderItemRepository()
	{
		$orderItemFactory                    = MainFactory::create('OrderItemFactory');
		$orderItemRepositoryReader           = MainFactory::create('OrderItemRepositoryReader', $this->db,
		                                                           $orderItemFactory);
		$orderItemRepositoryWriter           = MainFactory::create('OrderItemRepositoryWriter', $this->db);
		$orderItemRepositoryDeleter          = MainFactory::create('OrderItemRepositoryDeleter', $this->db);
		$orderItemAttributeRepositoryFactory = $this->_createOrderItemAttributeRepositoryFactory();
		$addonValueService                   = MainFactory::create('AddonValueService',
		                                                           MainFactory::create('AddonValueStorageFactory',
		                                                                               $this->db));
		
		return MainFactory::create('OrderItemRepository', $orderItemAttributeRepositoryFactory,
		                           $orderItemRepositoryReader, $orderItemRepositoryWriter, $orderItemRepositoryDeleter,
		                           $addonValueService);
	}
	
	
	/**
	 * Creates and returns and order item attribute repository factory.
	 *
	 * @return OrderItemAttributeRepositoryFactory New order item attribute repository factory.
	 */
	protected function _createOrderItemAttributeRepositoryFactory()
	{
		return MainFactory::create('OrderItemAttributeRepositoryFactory', $this->db);
	}
	
	
	/**
	 * Creates and returns an order total repository.
	 *
	 * @return OrderTotalRepository New order total repository.
	 */
	protected function _createOrderTotalRepository()
	{
		$orderTotalFactory           = MainFactory::create('OrderTotalFactory');
		$orderTotalRepositoryReader  = MainFactory::create('OrderTotalRepositoryReader', $this->db, $orderTotalFactory);
		$orderTotalRepositoryWriter  = MainFactory::create('OrderTotalRepositoryWriter', $this->db);
		$orderTotalRepositoryDeleter = MainFactory::create('OrderTotalRepositoryDeleter', $this->db);
		
		return MainFactory::create('OrderTotalRepository', $orderTotalRepositoryReader, $orderTotalRepositoryWriter,
		                           $orderTotalRepositoryDeleter);
	}
	
	
	/**
	 * Creates and returns a order status history storage.
	 *
	 * @return OrderStatusHistoryStorage New order status history storage.
	 */
	protected function _createOrderStatusHistoryStorage()
	{
		return MainFactory::create('OrderStatusHistoryStorage', $this->db);
	}
	
	
	/**
	 * Creates and returns an order list generator.
	 *
	 * @return OrderListGenerator New order list generator.
	 */
	protected function _createOrderListGenerator()
	{
		return MainFactory::create('OrderListGenerator', $this->db);
	}
	
	
	/**
	 * Creates a order service settings object
	 *
	 * @return OrderServiceSettings New order service settings object
	 */
	protected function _createOrderServiceSettings()
	{
		return MainFactory::create('OrderServiceSettings', array(1));
	}
}