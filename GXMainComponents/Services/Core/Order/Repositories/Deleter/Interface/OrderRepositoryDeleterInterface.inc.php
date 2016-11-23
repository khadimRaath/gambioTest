<?php

/* --------------------------------------------------------------
   OrderRepositoryDeleterInterface.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderRepositoryDeleterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderRepositoryDeleterInterface
{
	/**
	 * Removes an order from the orders table by the given ID.
	 *
	 * @param IdType $orderId ID of order which should removed.
	 *
	 * @return OrderRepositoryDeleterInterface Same instance for method chaining.
	 */
	public function deleteById(IdType $orderId);
}