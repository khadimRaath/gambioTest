<?php

/* --------------------------------------------------------------
   OrderRepositoryDeleter.inc.php 2015-11-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderRepositoryDeleterInterface');

/**
 * Class OrderRepositoryDeleter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderRepositoryDeleter implements OrderRepositoryDeleterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Removes an order from the orders table by the given ID.
	 *
	 * @param IdType $orderId ID of order which should removed.
	 *
	 * @return OrderRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteById(IdType $orderId)
	{
		$this->db->delete('orders', array('orders_id' => $orderId->asInt()));
	}
}