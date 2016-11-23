<?php

/* --------------------------------------------------------------
   OrderItemAttributeFactoryInterface.inc.php 2015-11-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeFactoryInterface
 *
 * @category  System
 * @package   Order
 * @subpackge Interfaces
 */
interface OrderItemAttributeFactoryInterface
{
	/**
	 * Creates an order item attribute instance.
	 *
	 * @param StringType $name  Name of the attribute.
	 * @param StringType $value Value of the attribute.
	 *
	 * @return OrderItemAttributeInterface New instance.
	 */
	public function createOrderItemAttribute(StringType $name, StringType $value);
	
	
	/**
	 * Creates a stored order item attribute instance.
	 *
	 * @param IdType $orderItemAttributeId Database ID of the stored attribute instance.
	 *
	 * @return StoredOrderItemAttributeInterface New instance.
	 */
	public function createStoredOrderItemAttribute(IdType $orderItemAttributeId);
}