<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryDeleterInterface.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderTotalRepositoryDeleterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderTotalRepositoryDeleterInterface
{
	/**
	 * Removes an order total item by the given order total ID.
	 *
	 * @param IdType $orderTotalId Order total ID.
	 *
	 * @return OrderTotalRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteTotalById(IdType $orderTotalId);
	
	
	/**
	 * Removes all order totals which are associated with the given order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderTotalRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteTotalsByOrderId(IdType $orderId);
}