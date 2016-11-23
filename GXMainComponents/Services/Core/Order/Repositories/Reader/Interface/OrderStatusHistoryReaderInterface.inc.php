<?php

/* --------------------------------------------------------------
   OrderStatusHistoryReaderInterface.inc.php 2015-12-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusHistoryReaderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderStatusHistoryReaderInterface
{
	/**
	 * Returns a collection of order status history items by the given order item ID.
	 *
	 * @param IdType $orderId ID of order item.
	 *
	 * @return OrderStatusHistoryListItemCollection Fetched order status history.
	 */
	public function getStatusHistory(IdType $orderId);
}