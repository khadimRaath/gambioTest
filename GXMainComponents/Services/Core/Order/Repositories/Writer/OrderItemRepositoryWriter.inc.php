<?php
/* --------------------------------------------------------------
   OrderItemRepositoryWriter.inc.php 2016-06-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderItemRepositoryWriterInterface');

/**
 * Class OrderItemRepositoryWriter
 *
 * @category   System
 * @package    Order
 * @subpackage Repositories
 */
class OrderItemRepositoryWriter implements OrderItemRepositoryWriterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrderItemRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder $dbQueryBuilder Query builder.
	 */
	public function __construct(CI_DB_query_builder $dbQueryBuilder)
	{
		$this->db = $dbQueryBuilder;
	}
	
	
	/**
	 * Inserts an order item to an order by the given order ID.
	 *
	 * @param IdType             $orderId   ID of the order.
	 * @param OrderItemInterface $orderItem Order item to insert.
	 *
	 * @return int ID of inserted item.
	 */
	public function insertIntoOrder(IdType $orderId, OrderItemInterface $orderItem)
	{
		$orderItemArray = array(
			'orders_id'              => $orderId->asInt(),
			'products_model'         => $orderItem->getProductModel(),
			'products_name'          => $orderItem->getName(),
			'products_price'         => $orderItem->getPrice(),
			'products_quantity'      => $orderItem->getQuantity(),
			'final_price'            => $orderItem->getFinalPrice(),
			'products_tax'           => $orderItem->getTax(),
			'allow_tax'              => $orderItem->isTaxAllowed(),
			'products_discount_made' => $orderItem->getDiscountMade(),
			'products_shipping_time' => $orderItem->getShippingTimeInfo(),
			'checkout_information'   => $orderItem->getCheckoutInformation()
		);
		
		$this->db->insert('orders_products', $orderItemArray);
		$orderItemId = $this->db->insert_id();
		
		$downloadInfo = $orderItem->getDownloadInformation();
		foreach($downloadInfo->getArray() as $download)
		{
			$orderItemDownloadArray = array(
				'orders_id'                => $orderId->asInt(),
				'orders_products_id'       => $orderItemId,
				'orders_products_filename' => $download->getFilename(),
				'download_maxdays'         => $download->getMaxDaysAllowed(),
				'download_count'           => $download->getCountAvailable()
			);
			
			$this->db->insert('orders_products_download', $orderItemDownloadArray);
		}
		
		if($orderItem->getQuantityUnitName() !== '')
		{
			$orderItemQuantityUnitArray = array(
				'orders_products_id' => $orderItemId,
				'unit_name'          => $orderItem->getQuantityUnitName()
			);
			
			$this->db->insert('orders_products_quantity_units', $orderItemQuantityUnitArray);
		}
		
		return $orderItemId;
	}
	
	
	/**
	 * Update the passed order item.
	 *
	 * @param StoredOrderItemInterface $orderItem Order item to update.
	 *
	 * @return OrderItemRepositoryWriter Same instance for method chaining.
	 */
	public function update(StoredOrderItemInterface $orderItem)
	{
		$orderItemArray = array(
			'products_model'         => $orderItem->getProductModel(),
			'products_name'          => $orderItem->getName(),
			'products_price'         => $orderItem->getPrice(),
			'products_quantity'      => $orderItem->getQuantity(),
			'final_price'            => $orderItem->getFinalPrice(),
			'products_tax'           => $orderItem->getTax(),
			'allow_tax'              => $orderItem->isTaxAllowed(),
			'products_discount_made' => $orderItem->getDiscountMade(),
			'products_shipping_time' => $orderItem->getShippingTimeInfo(),
			'checkout_information'   => $orderItem->getCheckoutInformation()
		);
		
		$this->db->update('orders_products', $orderItemArray,
		                  array('orders_products_id' => $orderItem->getOrderItemId()));
		
		
		$downloadInfo = $orderItem->getDownloadInformation();
		
		// Get order record ID.
		$orderId = $this->db->select('orders_id')
		                       ->get_where('orders_products',
		                                   array('orders_products_id' => $orderItem->getOrderItemId()))
		                       ->row()->orders_id;
		
		// Remove the old download entries (will be re-inserted cause OrderItemDownloadInformation does not have an ID). 
		$this->db->delete('orders_products_download', array('orders_products_id' => $orderItem->getOrderItemId())); 
				
		foreach($downloadInfo->getArray() as $download)
		{
			$orderItemDownloadArray = array(
				'orders_id'                => $orderId,
				'orders_products_id'       => $orderItem->getOrderItemId(),
				'orders_products_filename' => $download->getFilename(),
				'download_maxdays'         => $download->getMaxDaysAllowed(),
				'download_count'           => $download->getCountAvailable()
			);
			
			$this->db->insert('orders_products_download', $orderItemDownloadArray);
		}
		
		if($orderItem->getQuantityUnitName() !== '')
		{
			$quantityUnit = $this->db->get_where('orders_products_quantity_units',
			                                     array('orders_products_id' => $orderItem->getOrderItemId()))
			                         ->result_array();
			
			if(count($quantityUnit))
			{
				$orderItemQuantityUnitArray = array(
					'unit_name' => $orderItem->getQuantityUnitName()
				);
				
				$this->db->update('orders_products_quantity_units', $orderItemQuantityUnitArray,
				                  array('orders_products_id' => $orderItem->getOrderItemId()));
			}
			else
			{
				$orderItemQuantityUnitArray = array(
					'orders_products_id' => $orderItem->getOrderItemId(),
					'unit_name'          => $orderItem->getQuantityUnitName()
				);
				
				$this->db->insert('orders_products_quantity_units', $orderItemQuantityUnitArray);
			}
		}
		else
		{
			$this->db->delete('orders_products_quantity_units',
			                  array('orders_products_id' => $orderItem->getOrderItemId()));
		}
	}
}