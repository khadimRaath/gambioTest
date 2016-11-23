<?php

/* --------------------------------------------------------------
   OrderObjectService.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderObjectServiceInterface');

/**
 * Class OrderObjectService
 *
 * @category System
 * @package  Order
 */
class OrderObjectService implements OrderObjectServiceInterface
{
	/**
	 * @var OrderItemFactoryInterface
	 */
	protected $orderItemFactory;
	
	/**
	 * @var OrderItemAttributeFactoryInterface
	 */
	protected $orderItemAttributeFactory;
	
	/**
	 * @var OrderItemPropertyFactoryInterface
	 */
	protected $orderItemPropertyFactory;
	
	/**
	 * @var OrderTotalFactoryInterface
	 */
	protected $orderTotalFactory;
	
	
	/**
	 * OrderObjectService Constructor
	 *
	 * @param \OrderItemFactoryInterface          $orderItemFactory
	 * @param \OrderItemAttributeFactoryInterface $orderItemAttributeFactory
	 * @param \OrderItemAttributeFactoryInterface $orderItemPropertyFactory
	 * @param \OrderTotalFactoryInterface         $orderTotalFactory
	 */
	public function __construct(OrderItemFactoryInterface $orderItemFactory,
	                            OrderItemAttributeFactoryInterface $orderItemAttributeFactory,
	                            OrderItemAttributeFactoryInterface $orderItemPropertyFactory,
	                            OrderTotalFactoryInterface $orderTotalFactory)
	{
		$this->orderItemFactory          = $orderItemFactory;
		$this->orderItemAttributeFactory = $orderItemAttributeFactory;
		$this->orderItemPropertyFactory  = $orderItemPropertyFactory;
		$this->orderTotalFactory         = $orderTotalFactory;
	}
	
	
	/**
	 * Create Order Item Object
	 *
	 * Creates and returns an order item object.
	 *
	 * @param StringType $name Name of the order item object to be created.
	 *
	 * @return \OrderItemInterface
	 */
	public function createOrderItemObject(StringType $name)
	{
		return $this->orderItemFactory->createOrderItem($name);
	}
	
	
	/**
	 * Create Order Item Attribute Object
	 *
	 * Creates and returns an order item attribute object.
	 *
	 * @param StringType $name  Name of the order item object attribute to be created.
	 * @param StringType $value Value of the order item object attribute to be created.
	 *
	 * @return \OrderItemAttributeInterface
	 */
	public function createOrderItemAttributeObject(StringType $name, StringType $value)
	{
		return $this->orderItemAttributeFactory->createOrderItemAttribute($name, $value);
	}
	
	
	/**
	 * Create Order Item Property Object
	 *
	 * Creates and returns an order item property object.
	 *
	 * @param StringType $name  Name of the order item object property to be created.
	 * @param StringType $value Value of the orreturn $this->orderItemAttributeFactory->createOrderItemAttribute($name,
	 *                          $value);der item object property to be created.
	 *
	 * @return \OrderItemPropertyInterface
	 */
	public function createOrderItemPropertyObject(StringType $name, StringType $value)
	{
		return $this->orderItemPropertyFactory->createOrderItemAttribute($name, $value);
	}
	
	
	/**
	 * Create Order Total Object
	 *
	 * Creates and returns an order total object.
	 *
	 * @param StringType  $title     Title of the order total to be created.
	 * @param DecimalType $value     value of the order total to be created.
	 * @param StringType  $valueText Value text of the order total to be created.
	 * @param StringType  $class     Class of the order total to be created.
	 * @param IntType     $sortOrder Sort order of the order total to be created.
	 *
	 * @return \OrderTotalInterface
	 */
	public function createOrderTotalObject(StringType $title,
	                                       DecimalType $value,
	                                       StringType $valueText = null,
	                                       StringType $class = null,
	                                       IntType $sortOrder = null)
	{
		return $this->orderTotalFactory->createOrderTotal($title, $value, $valueText, $class, $sortOrder);
	}
	
	
	/**
	 * Create Stored Order Item Object
	 *
	 * Creates and returns a stored order item object.
	 *
	 * @param \IdType $orderItemId Order item id of the order item to be stored.
	 *
	 * @return \StoredOrderItemInterface
	 */
	public function createStoredOrderItemObject(IdType $orderItemId)
	{
		return $this->orderItemFactory->createStoredOrderItem($orderItemId);
	}
	
	
	/**
	 * Create Stored Order Item Attribute Object
	 *
	 * Creates and returns a stored order item attribute object.
	 *
	 * @param \IdType $orderItemAttributeId Order item attribute id of the order item attribute to be stored.
	 *
	 * @return \StoredOrderItemAttributeInterface
	 */
	public function createStoredOrderItemAttributeObject(IdType $orderItemAttributeId)
	{
		return $this->orderItemAttributeFactory->createStoredOrderItemAttribute($orderItemAttributeId);
	}
	
	
	/**
	 * Create Stored Order Item Property Object
	 *
	 * Creates and returns a stored order item property object.
	 *
	 * @param \IdType $orderItemPropertyId Order property id of the order item property to be stored.
	 *
	 * @return \StoredOrderItemPropertyInterface
	 */
	public function createStoredOrderItemPropertyObject(IdType $orderItemPropertyId)
	{
		return $this->orderItemPropertyFactory->createStoredOrderItemAttribute($orderItemPropertyId);
	}
	
	
	/**
	 * Create Stored Order Total Object
	 *
	 * Creates and returns a stored order total object.
	 *
	 * @param \IdType $orderTotalId Order total id of the order total to be stored.
	 *
	 * @return \StoredOrderTotalInterface
	 */
	public function createStoredOrderTotalObject(IdType $orderTotalId)
	{
		$this->orderTotalFactory->createStoredOrderTotal($orderTotalId);
	}
}
