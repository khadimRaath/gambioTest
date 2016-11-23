<?php

/* --------------------------------------------------------------
   OrderItemPropertyRepositoryDeleter.php 2015-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemPropertyRepositoryDeleterInterface');

/**
 * Class OrderItemPropertyRepositoryDeleter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemPropertyRepositoryDeleter implements OrderItemPropertyRepositoryDeleterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * OrderItemPropertyRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Removes a property by the given order item property ID.
	 *
	 * @param IdType $orderItemPropertyId ID of the order item property.
	 *
	 * @return OrderItemPropertyRepositoryDeleter Same instance for method chaining.
	 */
	public function deletePropertyById(IdType $orderItemPropertyId)
	{
		$this->db->delete('orders_products_properties',
		                  array('orders_products_properties_id' => $orderItemPropertyId->asInt()));

		return $this;
	}


	/**
	 * Removes all properties from the order item by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return OrderItemPropertyRepositoryDeleter Same instance for method chaining.
	 */
	public function deletePropertiesByOrderItemId(IdType $orderItemId)
	{
		$this->db->delete('orders_products_properties', array('orders_products_id' => $orderItemId->asInt()));

		return $this;
	}
}