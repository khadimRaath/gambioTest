<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryFactoryInterface.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderItemAttributeRepositoryFactoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderItemAttributeRepositoryFactoryInterface
{
	/**
	 * Creates an order item attribute repository by the given class name.
	 *
	 * @param string $className Name of the attribute class.
	 *
	 * @return OrderItemAttributeRepositoryInterface Order item attribute repository instance.
	 */
	public function createRepositoryByAttributeClass($className);
	
	
	/**
	 * Creates an order item attribute repository by the given object type.
	 *
	 * @param OrderItemAttributeInterface $itemAttribute Order item attribute.
	 *
	 * @return OrderItemAttributeRepositoryInterface Order item attribute repository instance.
	 */
	public function createRepositoryByAttributeObject(OrderItemAttributeInterface $itemAttribute);
	
	
	/**
	 * Creates an array which contain all repository of type OrderItemAttributeRepositoryInterface.
	 *
	 * @return OrderItemAttributeRepositoryInterface[]
	 */
	public function createRepositoryArray();
}