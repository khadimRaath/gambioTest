<?php

/* --------------------------------------------------------------
   StoredOrderItemInterface.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StoredOrderItemInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface StoredOrderItemInterface extends OrderItemInterface
{
	/**
	 * Returns ID of the stored order item ID.
	 *
	 * @return int Order item ID.
	 */
	public function getOrderItemId();
}