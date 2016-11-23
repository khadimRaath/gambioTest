<?php

/* --------------------------------------------------------------
   OrderItemFactory.inc.php 2015-12-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemFactoryInterface');

/**
 * Class OrderItemFactory
 *
 * @category   System
 * @package    Order
 * @subpackage Factories
 */
class OrderItemFactory implements OrderItemFactoryInterface
{
	/**
	 * Creates an OrderItem object.
	 *
	 * @param StringType $name Order item name.
	 *
	 * @return OrderItem New order item instance.
	 */
	public function createOrderItem(StringType $name)
	{
		return MainFactory::create('OrderItem', $name);
	}
	
	
	/**
	 * Creates a StoredOrderItem object.
	 *
	 * @param IdType             $orderItemId ID of order item.
	 * @param OrderItemInterface $orderItem   (optional) When provided the object values will be used for the
	 *                                        generation of the StoredOrderItemInstance.
	 *
	 * @return StoredOrderItem New stored order item instance.
	 */
	public function createStoredOrderItem(IdType $orderItemId, OrderItemInterface $orderItem = null)
	{
		$storedOrderItem = MainFactory::create('StoredOrderItem', $orderItemId);
		
		if($orderItem !== null)
		{
			$storedOrderItem->setProductModel(new StringType($orderItem->getProductModel()));
			$storedOrderItem->setName(new StringType($orderItem->getName()));
			$storedOrderItem->setPrice(new DecimalType($orderItem->getPrice()));
			$storedOrderItem->setQuantity(new DecimalType($orderItem->getQuantity()));
			$storedOrderItem->setTax(new DecimalType($orderItem->getTax()));
			$storedOrderItem->setTaxAllowed(new BoolType($orderItem->isTaxAllowed()));
			$storedOrderItem->setDiscountMade(new DecimalType($orderItem->getDiscountMade()));
			$storedOrderItem->setShippingTimeInfo(new StringType($orderItem->getShippingTimeInfo()));
			$storedOrderItem->setCheckoutInformation(new StringType($orderItem->getCheckoutInformation()));
			$storedOrderItem->setAttributes($orderItem->getAttributes());
			$storedOrderItem->setQuantityUnitName(new StringType($orderItem->getQuantityUnitName()));
			$storedOrderItem->setDownloadInformation($orderItem->getDownloadInformation());
		}
		
		return $storedOrderItem;
	}
}