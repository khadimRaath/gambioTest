<?php

/* --------------------------------------------------------------
   OrderRepositoryInterface.inc.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderRepositoryInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderRepositoryInterface
{
	/**
	 * Creates a new order with no values in the database and returns it containing just the ID.
	 *
	 * @return OrderInterface Crated order.
	 */
	public function createNew();
	
	
	/**
	 * Saves an Order to the database.
	 *
	 * @param OrderInterface $order Stored order.
	 */
	public function store(OrderInterface $order);
	
	
	/**
	 * Returns an order by given ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderInterface Fetched order.
	 */
	public function getById(IdType $orderId);
	
	
	/**
	 * Deletes an order by the ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderRepositoryInterface Same instance for method chaining.
	 */
	public function deleteById(IdType $orderId);
}