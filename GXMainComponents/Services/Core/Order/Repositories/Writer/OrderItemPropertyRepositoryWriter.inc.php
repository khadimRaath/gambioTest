<?php

/* --------------------------------------------------------------
   OrderItemPropertyRepositoryWriter.php 2015-11-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemPropertyRepositoryWriterInterface');

/**
 * Class OrderItemPropertyRepositoryWriter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemPropertyRepositoryWriter implements OrderItemPropertyRepositoryWriterInterface
{

	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * OrderItemPropertyRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Adds a new property to the order item.
	 *
	 * @param IdType            $orderItemId       ID of the order item.
	 * @param OrderItemProperty $orderItemProperty Property to add.
	 *
	 * @return int ID of stored order item property.
	 */
	public function insertIntoOrderItem(IdType $orderItemId, OrderItemProperty $orderItemProperty)
	{
		$orderItemPropertyArray = array(
			'orders_products_id'            => $orderItemId->asInt(),
			'properties_name'               => $orderItemProperty->getName(),
			'values_name'                   => $orderItemProperty->getValue(),
			'properties_price_type'         => $orderItemProperty->getPriceType(),
			'properties_price'              => $orderItemProperty->getPrice(),
			'products_properties_combis_id' => $orderItemProperty->getCombisId()
		);

		$this->db->insert('orders_products_properties', $orderItemPropertyArray);

		return $this->db->insert_id();
	}


	/**
	 * Updates the stored order item property.
	 *
	 * @param StoredOrderItemProperty $orderItemProperty Order item property.
	 *
	 * @return OrderItemPropertyRepositoryWriter Same instance for method chaining.
	 */
	public function update(StoredOrderItemProperty $orderItemProperty)
	{
		$orderItemPropertyArray = array(
			'properties_name'               => $orderItemProperty->getName(),
			'values_name'                   => $orderItemProperty->getValue(),
			'properties_price_type'         => $orderItemProperty->getPriceType(),
			'properties_price'              => $orderItemProperty->getPrice(),
			'products_properties_combis_id' => $orderItemProperty->getCombisId()
		);

		$this->db->update('orders_products_properties', $orderItemPropertyArray,
		                  array('orders_products_properties_id' => $orderItemProperty->getOrderItemAttributeId()));

		return $this;
	}
}