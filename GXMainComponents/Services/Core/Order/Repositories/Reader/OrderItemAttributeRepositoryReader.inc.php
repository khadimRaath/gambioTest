<?php

/* --------------------------------------------------------------
   OrderItemAttributeRepositoryReader.inc.php 2015-11-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemAttributeRepositoryReaderInterface');

/**
 * Class OrderItemAttributeRepositoryReader
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemAttributeRepositoryReader implements OrderItemAttributeRepositoryReaderInterface
{
	/**
	 * Order item attribute factory.
	 * @var OrderItemAttributeFactory
	 */
	protected $orderItemAttributeFactory;
	
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
	 * ID column.
	 * @var string
	 */
	protected $key = 'orders_products_attributes_id';
	
	
	/**
	 * OrderItemPropertyRepositoryReader constructor.
	 *
	 * @param OrderItemAttributeFactoryInterface $orderItemAttributeFactory Order item attribute factory.
	 * @param CI_DB_query_builder                $db                        Query builder.
	 */
	public function __construct(CI_DB_query_builder $db, OrderItemAttributeFactoryInterface $orderItemAttributeFactory)
	{
		$this->db                        = $db;
		$this->orderItemAttributeFactory = $orderItemAttributeFactory;
	}
	
	
	/**
	 * Returns an order item attribute by the given ID.
	 *
	 * @param IdType $orderItemAttributeId ID of order item attribute.
	 *
	 * @throws \UnexpectedValueException If no order item attribute recors matches the provided $orderItemAttributeId
	 * @return StoredOrderItemAttributeInterface Fetched order item attribute.
	 */
	public function getAttributeById(IdType $orderItemAttributeId)
	{
		$data = $this->db->where($this->key, $orderItemAttributeId->asInt())->get($this->table)->row_array();
		
		if(empty($data))
		{
			throw new UnexpectedValueException('No order item attribute record matches the provided $orderItemAttributeId: '
			                                   . $orderItemAttributeId->asInt());
		}
		
		return $this->_createStoredOrderItemAttribute($data);
	}
	
	
	/**
	 * Returns a collection of order item attributes by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return StoredOrderItemAttributeCollection Fetched order item attribute collection.
	 */
	public function getAttributesByOrderItemId(IdType $orderItemId)
	{
		$storedOrderItemAttributes = array();
		
		$results = $this->db->where('orders_products_id', $orderItemId->asInt())->get($this->table);
		
		foreach($results->result_array() as $result)
		{
			$storedOrderItemAttributes[] = $this->_createStoredOrderItemAttribute($result);
		}
		
		$storedOrderItemAttributeCollection = MainFactory::create('StoredOrderItemAttributeCollection',
		                                                          $storedOrderItemAttributes);
		
		return $storedOrderItemAttributeCollection;
	}
	

	/**
	 * Creates a stored order item attribute with data provided.
	 *
	 * @param array $storedOrderItemAttributeData Order item attribute data.
	 *
	 * @return StoredOrderItemAttribute Crated stored order item attribute.
	 */
	protected function _createStoredOrderItemAttribute(array $storedOrderItemAttributeData)
	{
		$orderItemAttributeId     = new IdType($storedOrderItemAttributeData[$this->key]);
		$storedOrderItemAttribute = $this->orderItemAttributeFactory->createStoredOrderItemAttribute($orderItemAttributeId);
		$storedOrderItemAttribute->setName(new StringType($storedOrderItemAttributeData['products_options']));
		$storedOrderItemAttribute->setValue(new StringType($storedOrderItemAttributeData['products_options_values']));
		$storedOrderItemAttribute->setPrice(new DecimalType($storedOrderItemAttributeData['options_values_price']));
		$storedOrderItemAttribute->setPriceType(new StringType($storedOrderItemAttributeData['price_prefix']));
		$storedOrderItemAttribute->setOptionId(new IdType($storedOrderItemAttributeData['options_id']));
		$storedOrderItemAttribute->setOptionValueId(new IdType($storedOrderItemAttributeData['options_values_id']));
		
		return $storedOrderItemAttribute;
	}
}