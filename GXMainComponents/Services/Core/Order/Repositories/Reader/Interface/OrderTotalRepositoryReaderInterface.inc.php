<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryReaderInterface.inc.php 2015-11-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderTotalRepositoryReaderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderTotalRepositoryReaderInterface
{
	/**
	 * Returns an StoredOrderTotal object by the given ID.
	 *
	 * @param IdType $orderTotalId ID of order total item.
	 *
	 * @return StoredOrderTotal Fetched order total.
	 */
	public function getTotalById(IdType $orderTotalId);
	
	
	/**
	 * Returns a StoredOrderTotalCollection of StoredOrderTotal objects by the given order ID.
	 *
	 * @param IdType $orderOrderId ID of order item.
	 *
	 * @throws UnexpectedValueException If record does not exist.
	 * @return StoredOrderTotalCollection Fetched order total collection.
	 */
	public function getTotalsByOrderId(IdType $orderOrderId);
}