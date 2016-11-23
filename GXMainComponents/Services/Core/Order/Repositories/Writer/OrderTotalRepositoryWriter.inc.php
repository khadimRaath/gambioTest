<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryWriter.inc.php 2015-11-10 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderTotalRepositoryWriterInterface');

/**
 * Class OrderTotalRepositoryWriter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderTotalRepositoryWriter implements OrderTotalRepositoryWriterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Table.
	 * @var string
	 */
	protected $table = 'orders_total';

	/**
	 * ID column.
	 * @var string
	 */
	protected $key = 'orders_total_id';


	/**
	 * OrderTotalRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Inserts an order total item to an order by the given order ID.
	 *
	 * @param IdType              $orderId    ID of the order.
	 * @param OrderTotalInterface $orderTotal Order total item to insert.
	 *
	 * @return int ID of stored order total item.
	 */
	public function insertIntoOrder(IdType $orderId, OrderTotalInterface $orderTotal)
	{
		$orderIdValue    = $orderId->asInt();
		$orderTotalArray = array(
			'orders_id'  => $orderIdValue,
			'title'      => $orderTotal->getTitle(),
			'text'       => $orderTotal->getValueText(),
			'value'      => $orderTotal->getValue(),
			'class'      => $orderTotal->getClass(),
			'sort_order' => $orderTotal->getSortOrder()
		);

		$this->db->insert($this->table, $orderTotalArray);

		return $this->db->insert_id();
	}


	/**
	 * Updates the passed order total item.
	 *
	 * @param StoredOrderTotalInterface $orderTotal Order total item to update.
	 *
	 * @return OrderTotalRepositoryWriter Same instance for method chaining.
	 */
	public function update(StoredOrderTotalInterface $orderTotal)
	{
		$orderTotalArray = array(
			'title'      => $orderTotal->getTitle(),
			'text'       => $orderTotal->getValueText(),
			'value'      => $orderTotal->getValue(),
			'class'      => $orderTotal->getClass(),
			'sort_order' => $orderTotal->getSortOrder()
		);

		$this->db->update($this->table, $orderTotalArray, array($this->key => $orderTotal->getOrderTotalId()));

		return $this;
	}
}