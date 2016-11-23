<?php

/* --------------------------------------------------------------
   OrderTotalRepositoryReader.inc.php 2015-11-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderTotalRepositoryReaderInterface');

/**
 * Class OrderTotalRepositoryReader
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderTotalRepositoryReader implements OrderTotalRepositoryReaderInterface
{
	/**
	 * Order total factory.
	 * @var OrderTotalFactory
	 */
	protected $orderTotalFactory;
	
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
	 * OrderTotalRepositoryReader constructor.
	 *
	 * @param CI_DB_query_builder        $db                Query builder.
	 * @param OrderTotalFactoryInterface $orderTotalFactory Order total factory.
	 */
	public function __construct(CI_DB_query_builder $db, OrderTotalFactoryInterface $orderTotalFactory)
	{
		$this->db                = $db;
		$this->orderTotalFactory = $orderTotalFactory;
	}
	
	
	/**
	 * Returns an StoredOrderTotal object by the given ID.
	 *
	 * @param IdType $orderTotalId ID of order total item.
	 *
	 * @throws UnexpectedValueException If record does not exist.
	 * @return StoredOrderTotal Fetched order total.
	 */
	public function getTotalById(IdType $orderTotalId)
	{
		$data = $this->db->where($this->key, $orderTotalId->asInt())->get($this->table)->row_array();
		
		if(empty($data))
		{
			throw new UnexpectedValueException('No order total record matches the provided $orderTotalId: '
			                                   . $orderTotalId->asInt());
		}
		
		return $this->_createStoredOrderTotalItem($data);
	}
	
	
	/**
	 * Returns a StoredOrderTotalCollection of StoredOrderTotal objects by the given order ID.
	 *
	 * @param IdType $orderOrderId ID of order item.
	 *
	 * @return StoredOrderTotalCollection Fetched order total collection.
	 */
	public function getTotalsByOrderId(IdType $orderOrderId)
	{
		$storedOrderTotalItems = array();
		
		$results = $this->db->where('orders_id', $orderOrderId->asInt())
		                    ->order_by('sort_order ASC')
		                    ->order_by('orders_total_id ASC')
		                    ->get($this->table);
		
		foreach($results->result_array() as $result)
		{
			$storedOrderTotalItems[] = $this->_createStoredOrderTotalItem($result);
		}
		
		$storedOrderTotalCollection = MainFactory::create('StoredOrderTotalCollection', $storedOrderTotalItems);
		
		return $storedOrderTotalCollection;
	}
	
	
	/**
	 * Creates a StoredOrderTotal instance based on the given result set array.
	 *
	 * @param array $storedOrderTotalData Result set from database.
	 *
	 * @return StoredOrderTotal Created order total.
	 */
	protected function _createStoredOrderTotalItem(array $storedOrderTotalData)
	{
		$storedOrderTotal = $this->orderTotalFactory->createStoredOrderTotal(new IdType($storedOrderTotalData[$this->key]));
		$storedOrderTotal->setTitle(new StringType($storedOrderTotalData['title']));
		$storedOrderTotal->setValueText(new StringType($storedOrderTotalData['text']));
		$storedOrderTotal->setValue(new DecimalType($storedOrderTotalData['value']));
		$storedOrderTotal->setClass(new StringType($storedOrderTotalData['class']));
		$storedOrderTotal->setSortOrder(new IntType($storedOrderTotalData['sort_order']));
		
		return $storedOrderTotal;
	}
}