<?php

/* --------------------------------------------------------------
   OrdersHistoryApiV2Controller.inc.php 2016-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class OrdersStatusHistoryApiV2Controller
 *
 * Notice: This controller is a sub-resource of the OrdersV2Controller.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class OrdersHistoryApiV2Controller extends HttpApiV2Controller
{
	/**
	 * Order write service.
	 * 
	 * @var OrderWriteService
	 */
	protected $orderWriteService;

	/**
	 * Order read service.
	 * 
	 * @var OrderReadService
	 */
	protected $orderReadService;

	/**
	 * Order JSON serializer.
	 * 
	 * @var OrderJsonSerializer
	 */
	protected $orderJsonSerializer;


	/**
	 * Initializes API Controller
	 * 
	 * @throws HttpApiV2Exception On missing ID.
	 */
	protected function __initialize()
	{
		if(!isset($this->uri[1]) && !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Order record ID was not provided in the resource URL or is not valid.', 400);
		}

		$this->orderWriteService   = StaticGXCoreLoader::getService('OrderWrite');
		$this->orderReadService    = StaticGXCoreLoader::getService('OrderRead');
		$this->orderJsonSerializer = MainFactory::create('OrderJsonSerializer');
	}


	/**
	 * @api        {get} /orders/:id/history/:id Get Order History
	 * @apiVersion 2.1.0
	 * @apiName    GetOrderHistory
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Returns multiple or a single order status history records. This method supports all the GET parameters that are
	 * mentioned in the "Introduction" section of this documentation.
	 *
	 * @apiExample {curl} Get All Entries
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400210/history
	 *
	 * @apiExample {curl} Get Entry With ID = 3
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400210/history/3
	 */
	public function get()
	{
		$order = $this->orderReadService->getOrderById(new IdType($this->uri[1]));

		$response = array();

		foreach($order->getStatusHistory()->getArray() as $orderStatusHistoryListItem)
		{
			$response[] = $this->orderJsonSerializer->serializeOrderStatusHistoryListItem($orderStatusHistoryListItem);
		}

		if(isset($this->uri[3]))
		{
			foreach($response as $item)
			{
				if($item['id'] === (int)$this->uri[3])
				{
					$response = $item;
					break;
				}
			}
		}
		else if($this->api->request->get('q') !== null)
		{
			$this->_searchResponse($response, $this->api->request->get('q'));
		}

		$this->_sortResponse($response);
		$this->_paginateResponse($response);
		$this->_minimizeResponse($response);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}
}
