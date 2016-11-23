<?php
/* --------------------------------------------------------------
   OrderItemRepositoryDeleter.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemRepositoryDeleterInterface');

/**
 * Class OrderItemRepositoryDeleter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemRepositoryDeleter implements OrderItemRepositoryDeleterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderItemRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Removes an item from the order by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return OrderItemRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteItemById(IdType $orderItemId)
	{
		$this->db->delete(array('orders_products', 'orders_products_download', 'orders_products_quantity_units'),
		                  array('orders_products_id' => $orderItemId->asInt()));
	}
	
	
	/**
	 * Removes multiple order items by the given order ID.
	 *
	 * @param IdType $orderId ID of the order.
	 *
	 * @return OrderItemRepositoryDeleter Same instance for method chaining.
	 */
	public function deleteItemsByOrderId(IdType $orderId)
	{
		$query = $this->db->select('orders_products_id')
		                  ->from('orders_products')
		                  ->where('orders_id', $orderId->asInt());
		
		foreach($query->get()->result_array() as $row)
		{
			$this->db->delete(array('orders_products_quantity_units'),
			                  array('orders_products_id' => $row['orders_products_id']));
		}
		
		$this->db->delete(array('orders_products', 'orders_products_download'),
		                  array('orders_id' => $orderId->asInt()));
	}
}