<?php
/* --------------------------------------------------------------
   OrdersOverviewTooltips.inc.php 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrdersOverviewTooltips
 *
 * This class generates the required HTML for the tooltips of each row in the orders overview table.
 * In order to be faster do not use any services but fetch the data directly with DB queries.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Orders
 */
class OrdersOverviewTooltips
{
	/**
	 * @var ContentView
	 */
	protected $contentView;
	
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * OrdersOverviewTooltips constructor.
	 */
	public function __construct()
	{
		$this->db          = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$this->contentView = MainFactory::create('ContentView');
		$this->contentView->set_escape_html(true);
		$this->contentView->set_flat_assigns(true);
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/orders/tooltips/');
	}
	
	
	/**
	 * Get the row tooltip HTML for each displayed tooltip.
	 *
	 * @param OrderListItem $orderListItem Contains the order list item data.
	 *
	 * @return array
	 */
	public function getRowTooltips(OrderListItem $orderListItem)
	{
		$rowTooltips = [
			'orderItems'         => $this->_getOrderItems($orderListItem),
			'customerMemos'      => $this->_getCustomerMemos($orderListItem),
			'customerAddresses'  => $this->_getCustomerAddresses($orderListItem),
			'orderSumBlock'      => $this->_getOrderSumBlock($orderListItem),
			'orderStatusHistory' => $this->_getOrderStatusHistory($orderListItem),
			'trackingLinks'      => $this->_getTrackingLinks($orderListItem)
		];
		
		return $rowTooltips;
	}
	
	
	/**
	 * Renders and returns a template file.
	 *
	 * @param string $templateFile Template file to render.
	 * @param array  $contentArray Content array which represent the variables of the template.
	 *
	 * @return string Rendered template.
	 */
	protected function _render($templateFile, array $contentArray)
	{
		$this->contentView->set_content_template($templateFile);
		
		foreach($contentArray as $contentItemKey => $contentItemValue)
		{
			$this->contentView->set_content_data($contentItemKey, $contentItemValue);
		}
		
		return $this->contentView->get_html();
	}
	
	
	/**
	 * Get Order Items Tooltip HTML
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getOrderItems(OrderListItem $orderListItem)
	{
		$templateData = [
			'id'          => $orderListItem->getOrderId(),
			'products'    => [],
			'total_price' => ''
		];
		
		$orderCurrencyCode = $this->db->get_where('orders', ['orders_id' => $orderListItem->getOrderId()])
		                              ->row()->currency;
		
		$this->db->select('orders_products.orders_products_id, 
							orders_products.products_quantity, 
							orders_products.products_name, 
							orders_products.products_model, 
							orders_products.final_price, 
							orders_products_quantity_units.unit_name')
		         ->from('orders_products')
		         ->join('orders_products_quantity_units',
		                'orders_products.orders_products_id = orders_products_quantity_units.orders_products_id',
		                'left outer')
		         ->where('orders_id', $orderListItem->getOrderId());
		
		$orderItems = $this->db->get()->result_array();
		
		foreach($orderItems as $orderItem)
		{
			$attributes = $this->db->select('products_options AS name, products_options_values AS value')
			                       ->from('orders_products_attributes')
			                       ->where('orders_products_id', $orderItem['orders_products_id'])
			                       ->get()
			                       ->result_array();
			
			$gPrintContentManager = new GMGPrintContentManager();
			$gPrintResult         = $gPrintContentManager->get_orders_products_content($orderItem['orders_products_id'],
			                                                                           true);
			
			foreach($gPrintResult as $gPrintRow)
			{
				$attributes[] = [
					'name'  => $gPrintRow['NAME'],
					'value' => $gPrintRow['VALUE']
				];
			}
			
			$templateData['products'][$orderItem['orders_products_id']] = [
				'quantity'   => (double)$orderItem['products_quantity'],
				'name'       => $orderItem['products_name'],
				'unit_name'  => $orderItem['unit_name'] ?: 'x',
				'model'      => $orderItem['products_model'],
				'price'      => number_format((double)$orderItem['final_price'], 2, ',', '.') . ' '
				                . $orderCurrencyCode,
				'attributes' => $attributes
			];
		}
		
		$totalPrice = $this->db->get_where('orders_total', [
			'orders_id' => $orderListItem->getOrderId(),
			'class'     => 'ot_total'
		])->row_array();
		
		$templateData['total_price'] = trim(strip_tags($totalPrice['title'] . ' ' . $totalPrice['text']));
		
		return $this->_render('items.html', $templateData);
	}
	
	
	/**
	 * Get Customer Memo Tooltip HTML
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getCustomerMemos(OrderListItem $orderListItem)
	{
		$templateData = [
			'memos' => []
		];
		
		/** @var CustomerMemo $memo */
		foreach($orderListItem->getCustomerMemos()->getArray() as $memo)
		{
			$customer = $this->db->get_where('customers', ['customers_id' => $memo->getPosterId()])->row_array();
			
			$templateData['memos'][] = [
				'title'         => $memo->getTitle(),
				'text'          => $memo->getText(),
				'creation_date' => $memo->getCreationDate(),
				'poster_name'   => $customer['customers_firstname'] . ' ' . $customer['customers_lastname']
			];
		}
		
		return $this->_render('customer_memos.html', $templateData);
	}
	
	
	/**
	 * Get Customer Addresses Tooltip HTML
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getCustomerAddresses(OrderListItem $orderListItem)
	{
		$deliveryAddress = $orderListItem->getDeliveryAddress();
		$billingAddress  = $orderListItem->getBillingAddress();
		
		$templateData = [
			'has_separate_delivery_address' => $deliveryAddress !== $billingAddress,
			'customer_email'                => $orderListItem->getCustomerEmail(),
			'delivery'                      => [
				'firstname'               => $deliveryAddress->getFirstName(),
				'lastname'                => $deliveryAddress->getLastName(),
				'company'                 => $deliveryAddress->getCompany(),
				'street'                  => $deliveryAddress->getStreet(),
				'house_number'            => $deliveryAddress->getHouseNumber(),
				'additional_address_info' => $deliveryAddress->getAdditionalAddressInfo(),
				'postcode'                => $deliveryAddress->getPostcode(),
				'city'                    => $deliveryAddress->getCity(),
				'country'                 => $deliveryAddress->getCountry()
			],
			'billing'                       => [
				'firstname'               => $billingAddress->getFirstName(),
				'lastname'                => $billingAddress->getLastName(),
				'company'                 => $billingAddress->getCompany(),
				'street'                  => $billingAddress->getStreet(),
				'house_number'            => $billingAddress->getHouseNumber(),
				'additional_address_info' => $billingAddress->getAdditionalAddressInfo(),
				'postcode'                => $billingAddress->getPostcode(),
				'city'                    => $billingAddress->getCity(),
				'country'                 => $billingAddress->getCountry()
			]
		];
		
		return $this->_render('customer_addresses.html', $templateData);
	}
	
	
	/**
	 * Get Order Sum Block Tooltip HTML
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getOrderSumBlock(OrderListItem $orderListItem)
	{
		$templateData = [
			'sum_block' => []
		];
		
		$orderTotals = $this->db->get_where('orders_total', ['orders_id' => $orderListItem->getOrderId()])
		                        ->result_array();
		
		foreach($orderTotals as $orderTotal)
		{
			$templateData['sum_block'][] = [
				$orderTotal['title'] => $orderTotal['text']
			];
		}
		
		return $this->_render('sum_block.html', $templateData);
	}
	
	
	/**
	 * Get Order Status History Tooltip HTML
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getOrderStatusHistory(OrderListItem $orderListItem)
	{
		$templateData = [
			'status_history' => []
		];
		
		$statusHistory = $this->db->select('orders_status_history.*, orders_status.orders_status_name AS status_name')
		                          ->from('orders_status_history')
		                          ->join('orders_status',
		                                 'orders_status.orders_status_id = orders_status_history.orders_status_id',
		                                 'left')
		                          ->where([
			                                  'orders_status_history.orders_id' => $orderListItem->getOrderId(),
			                                  'orders_status.language_id'       => $_SESSION['languages_id']
		                                  ])
		                          ->get()
		                          ->result_array();
		
		foreach($statusHistory as $entry)
		{
			
			$templateData['status_history'][] = [
				'status_name'          => $entry['status_name'] ? : '',
				'comment'              => $entry['comment'],
				'date_added'           => date('d.m.Y H:i:s', strtotime($entry['date_added'])),
				'is_customer_notified' => (bool)$entry['customer_notified']
			];
		}
		
		return $this->_render('status_history.html', $templateData);
	}
	
	
	/**
	 * Get Shipping Costs Tooltip HTML
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getShippingCosts(OrderListItem $orderListItem)
	{
		$shippingCosts = $this->db->get_where('orders_total', [
			'orders_id' => $orderListItem->getOrderId(),
			'class'     => 'ot_shipping'
		])->row()->text;
		
		$templateData = [
			'shipping_costs' => $shippingCosts ? : '-'
		];
		
		return $this->_render('shipping_costs.html', $templateData);
	}
	
	
	/**
	 * Get Tracking Links
	 *
	 * @param OrderListItem $orderListItem
	 *
	 * @return string
	 */
	protected function _getTrackingLinks(OrderListItem $orderListItem)
	{
		if($orderListItem->getTrackingLinks()->count() === 0)
		{
			return '';
		}
		
		$rows = $this->db->get_where('orders_parcel_tracking_codes', ['order_id' => $orderListItem->getOrderId()])
		                 ->result_array();
		
		$templateData = [
			'tracking_links' => []
		];
		
		foreach($rows as $row)
		{
			$templateData['tracking_links'][] = [
				'service' => $row['parcel_service_name'],
				'code'    => $row['tracking_code'],
				'url'     => $row['url']
			];
		}
		
		return $this->_render('tracking_links.html', $templateData);
	}
}