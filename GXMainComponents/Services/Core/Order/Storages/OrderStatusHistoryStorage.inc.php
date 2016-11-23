<?php

/* --------------------------------------------------------------
   OrderStatusHistoryStorage.inc.php 2016-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class OrderStatusHistoryStorage
 *
 * @category   System
 * @package    Order
 * @subpackage Storages
 */
class OrderStatusHistoryStorage implements OrderStatusHistoryReaderInterface, OrderStatusHistoryWriterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderStatusHistoryStorage constructor.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Returns a collection of order status history items by the given order item id.
	 *
	 * @param IdType $orderId Id of order item.
	 *
	 * @return OrderStatusHistoryListItemCollection
	 */
	public function getStatusHistory(IdType $orderId)
	{
		$orderStatusHistoryListItems = array();
		$result = $this->db->from('orders_status_history')
		                   ->where('orders_id', $orderId->asInt())
		                   ->order_by('date_added', 'ASC')
		                   ->get();
		
		if($result->num_rows())
		{
			foreach($result->result_array() as $row)
			{
				$orderStatusHistoryListItems[] = MainFactory::create('OrderStatusHistoryListItem',
				                                                     new IdType($row['orders_status_history_id']),
				                                                     new IdType($row['orders_status_id']),
				                                                     new EmptyDateTime($row['date_added']),
				                                                     new StringType((string)$row['comments']),
				                                                     new BoolType($row['customer_notified']));
			}
		}
		
		$orderStatusHistoryListItemCollection = MainFactory::create('OrderStatusHistoryListItemCollection',
		                                                            $orderStatusHistoryListItems);
		
		return $orderStatusHistoryListItemCollection;
	}
	
	
	/**
	 * Adds an order status history item.
	 *
	 * @param IdType     $orderId
	 * @param IntType    $newOrderStatusId
	 * @param StringType $comment
	 * @param BoolType   $customerNotified
	 */
	public function addStatusUpdate(IdType $orderId,
	                                IntType $newOrderStatusId,
	                                StringType $comment,
	                                BoolType $customerNotified)
	{
		$dateAdded = new DateTime();
		$dateAdded = $dateAdded->format('Y-m-d H:i:s');
		
		$this->db->insert('orders_status_history', array(
			'orders_id'         => $orderId->asInt(),
			'orders_status_id'   => $newOrderStatusId->asInt(),
			'date_added'        => $dateAdded,
			'customer_notified' => (int)$customerNotified->asBool(),
			'comments'          => $comment->asString()
		));
	}
	
	
	/**
	 * Deletes all order status history items which are associated with the given order item id.
	 *
	 * @param IdType $orderId
	 */
	public function deleteHistory(IdType $orderId)
	{
		$this->db->delete('orders_status_history', array('orders_id' => $orderId->asInt()));
	}
}