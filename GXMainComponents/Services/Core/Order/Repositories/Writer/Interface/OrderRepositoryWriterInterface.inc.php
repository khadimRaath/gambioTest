<?php

/* --------------------------------------------------------------
   OrderRepositoryWriterInterface.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderRepositoryWriterInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderRepositoryWriterInterface
{
	/**
	 * Inserts a new order to the orders table.
	 *
	 * @param OrderInterface $order Order.
	 *
	 * @return int ID of inserted order.
	 */
	public function insert(OrderInterface $order);
	
	
	/**
	 * Updates an existing order in the orders table.
	 *
	 * @param OrderInterface $order Order object.
	 *
	 * @return OrderRepositoryWriterInterface Same instance for method chaining.
	 */
	public function update(OrderInterface $order);
}