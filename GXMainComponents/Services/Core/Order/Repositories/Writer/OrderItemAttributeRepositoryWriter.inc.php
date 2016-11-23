<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryWriter.inc.php 2015-11-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeRepositoryWriterInterface');

/**
 * Class OrderItemAttributeRepositoryWriter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemAttributeRepositoryWriter implements OrderItemAttributeRepositoryWriterInterface
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
	protected $table = 'orders_products_attributes';


	/**
	 * OrderItemAttributeRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}


	/**
	 * Adds a new attribute to the order item.
	 *
	 * @param IdType             $orderItemId        ID of the order item.
	 * @param OrderItemAttribute $orderItemAttribute Attribute to add.
	 *
	 * @return int ID of stored order item attribute.
	 */
	public function insertIntoOrderItem(IdType $orderItemId, OrderItemAttribute $orderItemAttribute)
	{
		$orderItemIdValue = $orderItemId->asInt();
		$result           = $this->db->get_where('orders_products', array('orders_products_id' => $orderItemIdValue))
		                             ->row_array();

		$columnValueArray = array(
			'orders_products_id'      => $orderItemIdValue,
			'orders_id'               => $result['orders_id'],
			'products_options'        => $orderItemAttribute->getName(),
			'products_options_values' => $orderItemAttribute->getValue(),
			'options_values_price'    => $orderItemAttribute->getPrice(),
			'price_prefix'            => $orderItemAttribute->getPriceType(),
			'options_id'              => $orderItemAttribute->getOptionId(),
			'options_values_id'       => $orderItemAttribute->getOptionValueId(),
		);

		$this->db->insert($this->table, $columnValueArray);

		return $this->db->insert_id();
	}


	/**
	 * Updates the stored order item attribute.
	 *
	 * @param StoredOrderItemAttribute $orderItemAttribute Order item attribute.
	 *
	 * @return OrderItemAttributeRepositoryWriter Same instance for method chaining.
	 */
	public function update(StoredOrderItemAttribute $orderItemAttribute)
	{
		$columnValueArray = array(
			'products_options'        => $orderItemAttribute->getName(),
			'products_options_values' => $orderItemAttribute->getValue(),
			'options_values_price'    => $orderItemAttribute->getPrice(),
			'price_prefix'            => $orderItemAttribute->getPriceType(),
			'options_id'              => $orderItemAttribute->getOptionId(),
			'options_values_id'       => $orderItemAttribute->getOptionValueId(),
		);

		$this->db->update($this->table, $columnValueArray,
		                  array('orders_products_attributes_id' => $orderItemAttribute->getOrderItemAttributeId()));

		return $this;
	}
}