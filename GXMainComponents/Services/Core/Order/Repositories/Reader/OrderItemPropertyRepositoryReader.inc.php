<?php

/* --------------------------------------------------------------
   OrderItemPropertyRepositoryReader.php 2015-11-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemPropertyRepositoryReaderInterface');

/**
 * Class OrderItemPropertyRepositoryReader
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemPropertyRepositoryReader implements OrderItemPropertyRepositoryReaderInterface
{
	/**
	 * Order item property factory.
	 * @var OrderItemPropertyFactory
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
	protected $table = 'orders_products_properties';
	
	/**
	 * ID column.
	 * @var string
	 */
	protected $key = 'orders_products_properties_id';
	
	
	/**
	 * OrderItemPropertyRepositoryReader constructor.
	 *
	 * @param CI_DB_query_builder                $db                        Query builder.
	 * @param OrderItemAttributeFactoryInterface $orderItemAttributeFactory Order item property factory.
	 */
	public function __construct(CI_DB_query_builder $db, OrderItemAttributeFactoryInterface $orderItemAttributeFactory)
	{
		$this->db                        = $db;
		$this->orderItemAttributeFactory = $orderItemAttributeFactory;
	}
	
	
	/**
	 * Returns an order item property by the given ID.
	 *
	 * @param IdType $orderItemPropertyId ID of order item property.
	 *
	 * @return StoredOrderItemProperty Fetched order item property.
	 * @throws UnexpectedValueException If no entry has been found.
	 */
	public function getPropertyById(IdType $orderItemPropertyId)
	{
		$data = $this->db->where($this->key, $orderItemPropertyId->asInt())->get($this->table)->row_array();
		
		if(empty($data))
		{
			throw new UnexpectedValueException('No order item property record matches the provided $orderItemPropertyId: '
			                                   . $orderItemPropertyId->asInt());
		}
		
		return $this->_createStoredOrderItemProperty($data);
	}
	
	
	/**
	 * Returns a collection of order item properties by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of the order item.
	 *
	 * @return StoredOrderItemAttributeCollection Fetched order item attribute collection.
	 */
	public function getPropertiesByOrderItemId(IdType $orderItemId)
	{
		$storedOrderItemAttributes = array();
		
		$results = $this->db->where('orders_products_id', $orderItemId->asInt())->get($this->table);
		
		foreach($results->result_array() as $result)
		{
			$storedOrderItemAttributes[] = $this->_createStoredOrderItemProperty($result);
		}
		
		$storedOrderItemAttributeCollection = MainFactory::create('StoredOrderItemAttributeCollection',
		                                                          $storedOrderItemAttributes);
		
		return $storedOrderItemAttributeCollection;
	}
	

	/**
	 * Created a stored order item property with data provided.
	 *
	 * @param array $storedOrderItemPropertyData Order item property data.
	 *
	 * @return StoredOrderItemProperty Created stored order item property.
	 */
	protected function _createStoredOrderItemProperty(array $storedOrderItemPropertyData)
	{
		$orderItemAttributeId    = new IdType($storedOrderItemPropertyData[$this->key]);
		$storedOrderItemProperty = $this->orderItemAttributeFactory->createStoredOrderItemAttribute($orderItemAttributeId);
		$storedOrderItemProperty->setName(new StringType($storedOrderItemPropertyData['properties_name']));
		$storedOrderItemProperty->setValue(new StringType($storedOrderItemPropertyData['values_name']));
		$storedOrderItemProperty->setPrice(new DecimalType($storedOrderItemPropertyData['properties_price']));
		$storedOrderItemProperty->setPriceType(new StringType($storedOrderItemPropertyData['properties_price_type']));
		$storedOrderItemProperty->setCombisId(new IdType($storedOrderItemPropertyData['products_properties_combis_id']));
		
		return $storedOrderItemProperty;
	}
}