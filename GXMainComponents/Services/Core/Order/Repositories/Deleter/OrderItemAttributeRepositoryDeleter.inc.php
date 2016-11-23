<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryDeleter.inc.php 2015-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeRepositoryDeleterInterface');

/**
 * Class OrderItemAttributeRepositoryDeleter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemAttributeRepositoryDeleter implements OrderItemAttributeRepositoryDeleterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * OrderItemAttributeRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Removes an attribute by the given order item attribute ID.
	 *
	 * @param IdType $orderItemAttributeId ID of the order item attribute.
	 *
	 * @return OrderItemAttributeRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteAttributeById(IdType $orderItemAttributeId)
	{
		$this->db->delete('orders_products_attributes',
		                  array('orders_products_attributes_id' => $orderItemAttributeId->asInt()));

		return $this;
	}


	/**
	 * Removes all attributes from the order item by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return OrderItemAttributeRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteAttributesByOrderItemId(IdType $orderItemId)
	{
		$this->db->delete('orders_products_attributes', array('orders_products_id' => $orderItemId->asInt()));

		return $this;
	}
}