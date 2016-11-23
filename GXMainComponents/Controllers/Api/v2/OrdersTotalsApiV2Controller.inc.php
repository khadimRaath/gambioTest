<?php

/* --------------------------------------------------------------
   OrdersTotalsApiV2Controller.inc.php 2016-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class OrdersTotalsApiV2Controller
 *
 * Notice: This controller is a sub-resource of the OrdersV2Controller.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class OrdersTotalsApiV2Controller extends HttpApiV2Controller
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
	 * @throws HttpApiV2Exception On missing order ID.
	 */
	protected function __initialize()
	{
		// Check if the order ID was provided
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Order record ID was not provided in the resource URL.', 400);
		}

		$this->orderWriteService   = StaticGXCoreLoader::getService('OrderWrite');
		$this->orderReadService    = StaticGXCoreLoader::getService('OrderRead');
		$this->orderJsonSerializer = MainFactory::create('OrderJsonSerializer');
	}


	/**
	 * @api        {post} /orders/:id/totals Create Order Total
	 * @apiVersion 2.1.0
	 * @apiName    CreateOrderTotal
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Creates a new order total entry to the existing order. The order total JSON format must be the same with the
	 * "totals" entries in the original order total.
	 *
	 * @apiExample {json} Request-Body
	 * {
	 *   "title": "Zwischensumme:",
	 *   "value": 50,
	 *   "valueText": "50,00 EUR",
	 *   "class": "ot_subtotal",
	 *   "sortOrder": 10
	 * }
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Order Total resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest The request body was empty.
	 *           
	 * @apiError (Error 5xx) 500-InternalError One of the given properties has an invalid value type.
	 *           
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order total data were not provided."
	 * }
	 */
	public function post()
	{
		$orderTotalJsonString = $this->api->request->getBody();

		if(empty($orderTotalJsonString))
		{
			throw new HttpApiV2Exception('Order total data were not provided.', 400);
		}

		$orderId = new IdType($this->uri[1]);

		$orderTotal = $this->orderJsonSerializer->deserializeOrderTotal(json_decode($orderTotalJsonString));

		$orderTotalId = $this->orderWriteService->addOrderTotal($orderId, $orderTotal);
		$order = $this->orderReadService->getOrderById($orderId);

		foreach($order->getOrderTotals()->getArray() as $storedOrderTotal)
		{
			/** @var StoredOrderTotalInterface $storedOrderTotal */
			if($storedOrderTotal->getOrderTotalId() === $orderTotalId)
			{
				$response = $this->orderJsonSerializer->serializeOrderTotal($storedOrderTotal);
			}
		}

		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /orders/:id/totals/:id Update Order Total
	 * @apiVersion 2.1.0
	 * @apiName    UpdateOrderTotal
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to update an existing order total. Use the same order total JSON format as in the POST method.
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated Order Total resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest The order total ID in the URI was not provided or is invalid.
	 * @apiErrorExample Error-Response (Missing ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order total record ID was not provided or is invalid."
	 * }
	 *
	 * @apiError 400-BadRequest The request body is empty.
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order total data were not provided."
	 * }
	 *
	 * @apiError 404-NotFound The provided order total was not found in the given order.
	 * @apiErrorExample Error-Response (Not found in order)
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "The provided order total ID does not exist in the given order!"
	 * }
	 */
	public function put()
	{
		if(!isset($this->uri[3]) || !is_numeric($this->uri[3]))
		{
			throw new HttpApiV2Exception('Order total record ID was not provided or is invalid: '
			                             . gettype($this->uri[3]), 400);
		}

		$orderTotalJsonString = $this->api->request->getBody();

		if(empty($orderTotalJsonString))
		{
			throw new HttpApiV2Exception('Order total data were not provided.', 400);
		}

		$orderId = new IdType($this->uri[1]);
		$order   = $this->orderReadService->getOrderById($orderId);

		$baseOrderTotal = null;
		foreach($order->getOrderTotals()->getArray() as $orderTotal)
		{
			if($orderTotal->getOrderTotalId() === (int)$this->uri[3])
			{
				$baseOrderTotal = $orderTotal;
				break;
			}
		}

		if($baseOrderTotal === null)
		{
			throw new HttpApiV2Exception('The provided order total ID does not exist in the given order!', 404);
		}

		// Ensure that the order total has the correct order total id of the request url
		$orderTotalJsonString = $this->_setJsonValue($orderTotalJsonString, 'id', (int)$this->uri[3]);

		$storedOrderTotal = $this->orderJsonSerializer->deserializeOrderTotal(json_decode($orderTotalJsonString),
		                                                                      $baseOrderTotal);

		$this->orderWriteService->updateOrderTotal($storedOrderTotal);

		$response = $this->orderJsonSerializer->serializeOrderTotal($storedOrderTotal);
		$this->_writeResponse($response);
	}

	/**
	 * @api        {delete} /orders/:id/totals/:id Delete Order Total
	 * @apiVersion 2.1.0
	 * @apiName    DeleteOrderTotal
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to remove an order total from an existing order.
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code" => 200,
	 *   "status" => "success",
	 *   "action" => "delete",
	 *   "resource" => "OrderTotal",
	 *   "orderId" => 400345,
	 *   "orderTotalId" => 1
	 * }
	 *
	 * @apiError 400-BadRequest The order total ID in the URI was not provided or is invalid.
	 * @apiError 404-NotFound The provided order total was not found in the given order.
	 *           
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order total record ID was not provided in the resource URL."
	 * }
	 *
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[3]) || !is_numeric($this->uri[3]))
		{
			throw new HttpApiV2Exception('Order total record ID was not provided in the resource URL.', 400);
		}

		$orderId = new IdType($this->uri[1]);

		$order = $this->orderReadService->getOrderById($orderId);

		$storedOrderTotal = null;
		foreach($order->getOrderTotals()->getArray() as $orderTotal)
		{
			if($orderTotal->getOrderTotalId() === (int)$this->uri[3])
			{
				$storedOrderTotal = $orderTotal;
				break;
			}
		}
		
		if($storedOrderTotal === null)
		{
			throw new HttpApiV2Exception('The provided order total ID does not exist in the given order!', 404);
		}

		// Remove order total record from database.
		$this->orderWriteService->removeOrderTotal($storedOrderTotal);

		// Return response JSON.
		$response = array(
			'code'         => 200,
			'status'       => 'success',
			'action'       => 'delete',
			'resource'     => 'OrderTotal',
			'orderId'      => (int)$this->uri[1],
			'orderTotalId' => (int)$this->uri[3]
		);

		$this->_writeResponse($response);
	}

	/**
	 * @api        {get} /orders/:id/totals/:id Get Order Total
	 * @apiVersion 2.1.0
	 * @apiName    GetOrderTotal
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Returns all or just a single order total from an existing orders. All the GET manipulation parameters are applied
	 * with this method (search, sort, minimize, paginate etc).
	 *
	 * @apiExample {curl} Get All Entries
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400785/totals
	 *
	 * @apiExample {curl} Get Entry With ID = 4
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400242/totals/4
	 */
	public function get()
	{
		$orderId = new IdType($this->uri[1]);
		$order   = $this->orderReadService->getOrderById($orderId);

		$response = array();
		foreach($order->getOrderTotals()->getArray() as $orderTotal)
		{
			$response[] = $this->orderJsonSerializer->serializeOrderTotal($orderTotal);
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
