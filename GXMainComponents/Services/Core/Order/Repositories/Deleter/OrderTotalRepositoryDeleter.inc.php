<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryDeleter.inc.php 2015-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderTotalRepositoryDeleter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderTotalRepositoryDeleter implements OrderTotalRepositoryDeleterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * OrderTotalRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Removes an order total item by the given order total ID.
	 *
	 * @param IdType $orderTotalId Order total ID.
	 *
	 * @return OrderTotalRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteTotalById(IdType $orderTotalId)
	{
		$this->db->delete('orders_total', array('orders_total_id' => $orderTotalId->asInt()));

		return $this;
	}


	/**
	 * Removes all order totals which are associated with the given order ID.
	 *
	 * @param IdType $orderId Order ID.
	 *
	 * @return OrderTotalRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteTotalsByOrderId(IdType $orderId)
	{
		$this->db->delete('orders_total', array('orders_id' => $orderId->asInt()));

		return $this;
	}
}