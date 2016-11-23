<?php
/* --------------------------------------------------------------
   OrderItemRepositoryReader.inc.php 2016-06-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemRepositoryReaderInterface');

/**
 * Class OrderItemRepositoryReader
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemRepositoryReader implements OrderItemRepositoryReaderInterface
{
	/**
	 * Query builder.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Order item factory.
	 * @var OrderItemFactoryInterface
	 */
	protected $orderItemFactory;
	
	
	/**
	 * OrderItemRepositoryReader constructor.
	 *
	 * @param CI_DB_query_builder       $db               Query builder.
	 * @param OrderItemFactoryInterface $orderItemFactory Order item factory.
	 */
	public function __construct(CI_DB_query_builder $db, OrderItemFactoryInterface $orderItemFactory)
	{
		$this->db               = $db;
		$this->orderItemFactory = $orderItemFactory;
	}
	
	
	/**
	 * Returns an stored order item by the given order item ID.
	 *
	 * @param IdType $orderItemId ID of order item.
	 *
	 * @return StoredOrderItemInterface Fetched order item.
	 *
	 * @throws UnexpectedValueException If no order item entry has been found.
	 * @throws InvalidArgumentException If download information contain invalid values.
	 */
	public function getItemById(IdType $orderItemId)
	{
		$row = $this->db->get_where('orders_products', array('orders_products_id' => $orderItemId->asInt()))
		                ->row_array();
		
		if($row === null)
		{
			throw new UnexpectedValueException('The requested OrderItem was not found in database (ID:'
			                                   . $orderItemId->asInt() . ')');
		}
		
		$storedOrderItem = $this->orderItemFactory->createStoredOrderItem($orderItemId);
		
		$this->_setDbValues($storedOrderItem, $row);
		
		return $storedOrderItem;
	}
	
	
	/**
	 * Returns a collection of stored order items by the given order ID.
	 *
	 * @param IdType $orderId ID of the order.
	 *
	 * @return StoredOrderItemCollection Fetched order item collection.
	 *
	 * @throws InvalidArgumentException If the database record contains invalid values.
	 */
	public function getItemsByOrderId(IdType $orderId)
	{
		$result = $this->db->get_where('orders_products', array('orders_id' => $orderId->asInt()))->result_array();
		
		$storedOrderItemArray = array();
		
		foreach($result as $row)
		{
			$storedOrderItem = $this->orderItemFactory->createStoredOrderItem(new IdType($row['orders_products_id']));
			
			$this->_setDbValues($storedOrderItem, $row);
			
			$storedOrderItemArray[] = $storedOrderItem;
		}
		
		$storedOrderItemCollection = MainFactory::create('StoredOrderItemCollection', $storedOrderItemArray);
		
		return $storedOrderItemCollection;
	}
	
	
	/**
	 * Assign via the setter the StoredOrderItem values.
	 *
	 * @param StoredOrderItemInterface $storedOrderItem
	 * @param array                    $row
	 *
	 * @throws InvalidArgumentException If $row contains invalid values.
	 */
	protected function _setDbValues(StoredOrderItemInterface $storedOrderItem, array $row)
	{
		$storedOrderItem->setProductModel(new StringType((string)$row['products_model']));
		$storedOrderItem->setName(new StringType((string)$row['products_name']));
		$storedOrderItem->setPrice(new DecimalType($row['products_price']));
		$storedOrderItem->setQuantity(new DecimalType($row['products_quantity']));
		$storedOrderItem->setTax(new DecimalType($row['products_tax']));
		$storedOrderItem->setTaxAllowed(new BoolType($row['allow_tax']));
		$storedOrderItem->setDiscountMade(new DecimalType((double)$row['products_discount_made']));
		$storedOrderItem->setCheckoutInformation(new StringType((string)$row['checkout_information']));
		
		// Get the order item downloads. 
		$downloads = $this->db->get_where('orders_products_download',
		                                  array('orders_products_id' => $storedOrderItem->getOrderItemId()))
		                      ->result_array();
		
		$orderItemDownloadInformationCollection = $this->_parseOrderItemDownloads($downloads);
		$storedOrderItem->setDownloadInformation($orderItemDownloadInformationCollection);
		
		// Get the quantity unit
		$quantityUnit = $this->db->get_where('orders_products_quantity_units',
		                                     array('orders_products_id' => $storedOrderItem->getOrderItemId()))
		                         ->result_array();
		
		if(count($quantityUnit))
		{
			$storedOrderItem->setQuantityUnitName(new StringType((string)$quantityUnit[0]['unit_name']));
		}
	}
	
	
	/**
	 * Parse download information of order item.
	 *
	 * @param array $downloads                         Contains the records of the "orders_products_download" table that
	 *                                                 are related to the order item.
	 *
	 * @return OrderItemDownloadInformationCollection Returns a collection with the OrderItemDownload instances.
	 *
	 * @throws InvalidArgumentException
	 */
	protected function _parseOrderItemDownloads(array $downloads)
	{
		$orderItemDownloadInformationArray = array();
		
		foreach($downloads as $download)
		{
			$orderItemDownloadInformationArray[] = MainFactory::create('OrderItemDownloadInformation',
			                                                           new FilenameStringType($download['orders_products_filename']),
			                                                           new IntType($download['download_maxdays']),
			                                                           new IntType($download['download_count']));
		}
		
		$orderItemDownloadInformationCollection = MainFactory::create('OrderItemDownloadInformationCollection',
		                                                              $orderItemDownloadInformationArray);
		
		return $orderItemDownloadInformationCollection;
	}
}