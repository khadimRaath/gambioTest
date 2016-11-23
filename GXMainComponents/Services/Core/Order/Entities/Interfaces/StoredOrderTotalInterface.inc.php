<?php

/* --------------------------------------------------------------
   StoredOrderTotalInterface.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface StoredOrderTotalInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface StoredOrderTotalInterface extends OrderTotalInterface
{
	/**
	 * Returns the ID of the stored order total.
	 *
	 * @return int Stored order total ID.
	 */
	public function getOrderTotalId();
}