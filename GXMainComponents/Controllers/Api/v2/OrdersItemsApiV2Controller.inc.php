<?php
/* --------------------------------------------------------------
   OrdersItemsApiV2Controller.inc.php 2016-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class OrdersItemsApiV2Controller
 *
 * Notice: This controller is a sub-resource of the OrdersApiV2Controller.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class OrdersItemsApiV2Controller extends HttpApiV2Controller
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
	 * Sub resources.
	 * 
	 * @var array
	 */
	protected $subresource;


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
		$this->subresource         = array(
			'attributes' => 'OrdersItemsAttributesApiV2Controller',
			'properties' => 'OrdersItemsAttributesApiV2Controller'
		);
	}


	/**
	 * @api        {post} /orders/:id/items Create Order Item
	 * @apiVersion 2.1.0
	 * @apiName    CreateOrderItem
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to create a new order item to an existing order. The order item JSON format must be the
	 * same with the "items" entries in the original order item.
	 *
	 * @apiExample {json} Request-Body
	 * {
	 *  "model": "12345-s-black",
	 *  "name": "Ein Artikel",
	 *  "quantity": 1,
	 *  "price": 11,
	 *  "finalPrice": 11,
	 *  "tax": 19,
	 *  "isTaxAllowed": true,
	 *  "discount": 0,
	 *  "shippingTimeInformation": "",
	 *  "checkoutInformation": "Checkout information goes here ...",
	 *  "quantityUnitName": "Liter",
	 *  "attributes": [
	 *    {
	 *      "id": 1,
	 *      "name": "Farbe",
	 *      "value": "rot",
	 *      "price": 0,
	 *      "priceType": "+",
	 *      "optionId": 1,
	 *      "optionValueId": 1,
	 *      "combisId": null
	 *    }
	 *  ],
	 *  "downloadInformation": {
	 *    "filename": "Dokument.pdf",
	 *    "maxDaysAllowed": 5,
	 *    "countAvailable": 14
	 *  },
	 *  "addonValues": {
	 *    "productId": "2",
	 *    "quantityUnitId": "1"
	 *  }
	 * }
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Order Item resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest The request body was empty.
	 *
	 * @apiError (Error 5xx) 500-InternalError One of the given properties has an invalid value type.
	 *
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item data were not provided."
	 * }
	 */
	public function post()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$orderItemJsonString = $this->api->request->getBody();

		if(empty($orderItemJsonString))
		{
			throw new HttpApiV2Exception('Order item data were not provided.', 400);
		}

		$orderItem = $this->orderJsonSerializer->deserializeOrderItem(json_decode($orderItemJsonString));

		$orderId = new IdType($this->uri[1]);

		$orderItemID = $this->orderWriteService->addOrderItem($orderId, $orderItem);

		$storedOrderItem = $this->orderReadService->getOrderItemById(new IdType($orderItemID));
		$response = $this->orderJsonSerializer->serializeOrderItem($storedOrderItem);

		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /orders/:id/items/:id Update Order Item
	 * @apiVersion 2.1.0
	 * @apiName    UpdateOrderItem
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to update an existing order item. Use the same order item JSON format as in the POST method.
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated Order Item resource in the response body.
	 *
	 * @apiError 400-BadRequest The request body is empty or the order item ID in the URI was not provided or is invalid.
	 *           
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item data were not provided."
	 * }
	 *
	 * @apiErrorExample Error-Response (Missing or invalid ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item record ID was not provided or is invalid."
	 * }
	 *
	 * @apiError 404-NotFound The provided order item was not found in the given order.
	 *           
	 * @apiErrorExample Error-Response (Not found in order)
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "The provided order item ID does not exist in the given order!"
	 * }
	 */
	public function put()
	{
		if(!isset($this->uri[3]) || !is_numeric($this->uri[3]))
		{
			throw new HttpApiV2Exception('Order item record ID was not provided or is invalid: '
			                             . gettype($this->uri[3]), 400);
		}

		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$orderItemJsonString = $this->api->request->getBody();

		if(empty($orderItemJsonString))
		{
			throw new HttpApiV2Exception('Order item data were not provided.', 400);
		}

		$orderItemId   = new IdType($this->uri[3]);
		$baseOrderItem = $this->orderReadService->getOrderItemById($orderItemId);

		if($baseOrderItem === null)
		{
			throw new HttpApiV2Exception('The provided order item ID does not exist in the given order!', 404);
		}

		// Ensure that the order item has the correct order item id of the request url
		$orderItemJsonString = $this->_setJsonValue($orderItemJsonString, 'id', $orderItemId->asInt());

		$storedOrderItem = $this->orderJsonSerializer->deserializeOrderItem(json_decode($orderItemJsonString),
		                                                                    $baseOrderItem);

		$this->orderWriteService->updateOrderItem($storedOrderItem);

		$response = $this->orderJsonSerializer->serializeOrderItem($storedOrderItem);
		$this->_linkResponse($response);
		$this->_writeResponse($response, 200);
	}


	/**
	 * @api        {delete} /orders/:id/items/:id Delete Order Item
	 * @apiVersion 2.1.0
	 * @apiName    DeleteOrderItem
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to remove an order item from an existing order.
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action" :"delete",
	 *   "resource": "OrderItem",
	 *   "orderId": 400883,
	 *   "orderItemId": 1
	 * }
	 *
	 * @apiError 400-BadRequest The order item ID in the URI was not provided or is invalid.
	 * @apiErrorExample Error-Response (Missing ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item record ID was not provided in the resource URL."
	 * }
	 *
	 * @apiError 404-NotFound The provided order item was not found in the given order.
	 * @apiErrorExample Error-Response (Not found in order)
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "The provided order item ID does not exist in the given order!"
	 * }
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[3]) || !is_numeric($this->uri[3]))
		{
			throw new HttpApiV2Exception('Order item record ID was not provided in the resource URL.', 400);
		}

		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$orderId = new IdType($this->uri[1]);

		$order = $this->orderReadService->getOrderById($orderId);

		$storedOrderItem = null;
		foreach($order->getOrderItems()->getArray() as $orderItem)
		{
			if($orderItem->getOrderItemId() === (int)$this->uri[3])
			{
				$storedOrderItem = $orderItem;
				break;
			}
		}

		// @todo The delete method must not through exceptions if the record was not found.
		if($storedOrderItem === null)
		{
			throw new HttpApiV2Exception('The provided order item ID does not exist in the given order!', 404);
		}

		// Remove order item record from database.
		$this->orderWriteService->removeOrderItem($storedOrderItem);

		// Return response JSON.
		$response = array(
			'code'        => 200,
			'status'      => 'success',
			'action'      => 'delete',
			'resource'    => 'OrderItem',
			'orderId'     => (int)$this->uri[1],
			'orderItemId' => (int)$this->uri[3]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /orders/:id/items/:id Get Order Item
	 * @apiVersion 2.1.0
	 * @apiName    GetOrderItem
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Get all or just a single order item from an existing orders. All the GET manipulation parameters are applied
	 * with this method (search, sort, minimize, paginate etc).
	 *
	 * @apiExample {curl} Get All Entries
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400785/items
	 *
	 * @apiExample {curl} Get Entry With ID=8
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400242/items/8
	 *
	 * @apiExample {curl} Minimize Responses
	 *             curl -i --user admin@shop.de:12345
	 *             http://shop.de/api.php/v2/orders/400871/items?fields=id,model,name,quanity
	 *
	 * @apiParam {Number} [id] Record ID of resource to be returned. If omitted all records
	 * will be included in the response.
	 */
	public function get()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$orderId = new IdType($this->uri[1]);
		$order   = $this->orderReadService->getOrderById($orderId);

		$response = array();
		foreach($order->getOrderItems()->getArray() as $orderItem)
		{
			$response[] = $this->orderJsonSerializer->serializeOrderItem($orderItem);
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
