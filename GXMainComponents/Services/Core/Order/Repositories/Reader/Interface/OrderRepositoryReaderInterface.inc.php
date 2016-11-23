<?php

/* --------------------------------------------------------------
   OrderRepositoryReaderInterface.inc.php 2015-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderRepositoryReaderInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderRepositoryReaderInterface
{
	/**
	 * Fetches an new order object from the orders table by the given ID.
	 *
	 * @param IdType $orderId ID of the expected order.
	 *
	 * @return GXEngineOrder Fetched order.
	 */
	public function getById(IdType $orderId);
}