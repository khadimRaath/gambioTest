<?php

/* --------------------------------------------------------------
   OrdersItemsAttributesApiV2Controller.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class OrdersItemsAttributesApiV2Controller
 *
 * Notice: This controller is a sub-resource of the OrdersItemsApiV2Controller.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class OrdersItemsAttributesApiV2Controller extends HttpApiV2Controller
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
	 * @throws HttpApiV2Exception On missing or invalid order ID and order item ID.
	 */
	protected function __initialize()
	{
		// Check if the order ID was provided
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Order record ID was not provided in the resource URL.', 400);
		}

		// Check if the order item ID was provided
		if(!isset($this->uri[3]) || !is_numeric($this->uri[3]))
		{
			throw new HttpApiV2Exception('Order item record ID was not provided in the resource URL.', 400);
		}

		$this->orderWriteService   = StaticGXCoreLoader::getService('OrderWrite');
		$this->orderReadService    = StaticGXCoreLoader::getService('OrderRead');
		$this->orderJsonSerializer = MainFactory::create('OrderJsonSerializer');
	}


	/**
	 * @api        {post} /orders/:id/items/:id/attributes Create Order Item Attribute
	 * @apiVersion 2.1.0
	 * @apiName    CreateOrderItemAttribute
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to create a new order item attribute to an existing order item. The order item attribute JSON
	 * object is the same as the one included in the full order representation. There are two different order item
	 * variation systems in the shop, the "attributes" and the "properties". Both of them define a variation of an
	 * order item (e.g. color, size etc). You must always use only one of them for a single order item.
	 *
	 * @apiParamExample {json} Request-Example
	 * {
	 *   "name": "Color",
	 *   "value": "blue",
	 *   "price": 0.00,
	 *   "priceType": "+",
	 *   "optionId": 1,
	 *   "optionValueId": 1,
	 *   "combisId": null
	 * }
	 *
	 * @apiParam {String} name Attribute Name.
	 * @apiParam {String} value Attribute Value.
	 * @apiParam {Number} price Attribute Price as float.
	 * @apiParam {String} priceType Must contain one of the existing price types of the shop.
	 * @apiParam {Number} optionId Only attribute-records need this value.
	 * @apiParam {Number} optionValueId Only attribute-records need this value.
	 * @apiParam {Number} combisId Only property-records need this value.
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Order Item Attribute
	 * resource in the response body.
	 *
	 * @apiError 400-BadRequest The request body is empty.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item attribute data were not provided."
	 * }
	 */
	public function post()
	{
		$orderItemAttributeJsonString = $this->api->request->getBody();

		if(empty($orderItemAttributeJsonString))
		{
			throw new HttpApiV2Exception('Order item attribute data were not provided.', 400);
		}

		$orderItemId = new IdType($this->uri[3]);

		$orderItemAttributeJsonObject = json_decode($orderItemAttributeJsonString);
		$attributeClassName           = ($orderItemAttributeJsonObject->combisId
		                                 !== null) ? 'OrderItemProperty' : 'OrderItemAttribute';

		$orderItemAttribute = $this->orderJsonSerializer->deserializeAttribute($orderItemAttributeJsonObject);

		$orderItemAttributeId = $this->orderWriteService->addOrderItemAttribute($orderItemId, $orderItemAttribute);


		$orderItemAttributes = $this->orderReadService->getOrderItemById($orderItemId)->getAttributes();
		foreach($orderItemAttributes->getArray() as $storedOrderItemAttribute)
		{
			/** @var StoredOrderItemAttributeInterface $storedOrderItemAttribute */
			if($storedOrderItemAttribute->getOrderItemAttributeId() === $orderItemAttributeId
			   && is_a($storedOrderItemAttribute, $attributeClassName)
			)
			{
				$response = $this->orderJsonSerializer->serializeAttribute($storedOrderItemAttribute);
			}
		}

		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /orders/:id/items/:id/attributes/:id Update Order Item Attribute/Property
	 * @apiVersion 2.1.0
	 * @apiName    UpdateOrderIteAttribute
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to update an existing order item attribute record. It uses the same attribute JSON format
	 * as in the "Create Order Item Attribute" method.
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated Order Item Attribute resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest The request body was empty or the order item attribute record ID was not provided or is
	 * invalid.
	 * @apiErrorExample Error-Response (Missing ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item attribute data were not provided."
	 * }
	 *
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item attribute data were not provided."
	 * }
	 */
	public function put()
	{
		if(!isset($this->uri[5]) || !is_numeric($this->uri[5]))
		{
			throw new HttpApiV2Exception('Order item attribute record ID was not provided or is invalid: '
			                             . gettype($this->uri[5]), 400);
		}

		$orderItemAttributeJsonString = $this->api->request->getBody();

		if(empty($orderItemAttributeJsonString))
		{
			throw new HttpApiV2Exception('Order item attribute data were not provided.', 400);
		}

		$orderItemId          = new IdType($this->uri[3]);
		$orderItemAttributeId = new IdType($this->uri[5]);
		$attributeClassName   = ($this->uri[4] === 'properties') ? 'OrderItemProperty' : 'OrderItemAttribute';

		$orderItemAttributes = $this->orderReadService->getOrderItemById($orderItemId)->getAttributes();

		$baseOrderItemAttribute = null;

		foreach($orderItemAttributes->getArray() as $orderItemAttribute)
		{
			/** @var StoredOrderItemAttributeInterface $orderItemAttribute */
			if($orderItemAttribute->getOrderItemAttributeId() === $orderItemAttributeId->asInt()
			   && is_a($orderItemAttribute, $attributeClassName)
			)
			{
				$baseOrderItemAttribute = $orderItemAttribute;
				break;
			}
		}

		// Ensure that the order item attribute has the correct order item attribute id of the request url
		$orderItemAttributeJsonString = $this->_setJsonValue($orderItemAttributeJsonString, 'id',
		                                                     $orderItemAttributeId->asInt());

		$storedOrderItemAttribute = $this->orderJsonSerializer->deserializeAttribute(json_decode($orderItemAttributeJsonString),
		                                                                             $baseOrderItemAttribute);

		$this->orderWriteService->updateOrderItemAttribute($storedOrderItemAttribute);

		$response = $this->orderJsonSerializer->serializeAttribute($storedOrderItemAttribute);
		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /orders/:id/items/:id/attributes/:id Delete Order Item Attribute/Property
	 * @apiVersion 2.1.0
	 * @apiName    DeleteOrderItemAttribute
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Removes a single order item attribute/property entry from an existing order item record.
	 *
	 * @apiExample {curl} Delete Attribute with ID = 3
	 *             curl -X DELETE --user admin@shop.de:12345
	 *             http://shop.de/api.php/v2/orders/400953/items/1/attributes/3
	 * 
	 * @apiExample {curl} Delete Property with ID = 84
	 *             curl -X DELETE --user admin@shop.de:12345
	 *             http://shop.de/api.php/v2/orders/400953/items/1/properties/84
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "resource": "Order",
	 *   "orderId": 400953,
	 *   "orderItemId": 1,
	 *   "orderItemAttributeId": 3
	 * }
	 *
	 * @apiError 400-BadRequest The order item attribute ID in the URI was not provided or is invalid.
	 * @apiErrorExample Error-Response (Missing ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order item attribute record ID was not provided in the resource URL."
	 * }
	 *
	 * @apiError 404-NotFound The order item attribute was not found.
	 * @apiErrorExample Error-Response (Not found)
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "The order item attribute was not found."
	 * }
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[5]) || !is_numeric($this->uri[5]))
		{
			throw new HttpApiV2Exception('Order item attribute record ID was not provided in the resource URL.', 400);
		}

		$orderItemId          = new IdType($this->uri[3]);
		$orderItemAttributeId = new IdType($this->uri[5]);
		$attributeClassName   = ($this->uri[4] === 'properties') ? 'OrderItemProperty' : 'OrderItemAttribute';

		$orderItemAttributes = $this->orderReadService->getOrderItemById($orderItemId)->getAttributes();
		$storedOrderItemAttribute = null;

		foreach($orderItemAttributes->getArray() as $orderItemAttribute)
		{
			/** @var StoredOrderItemAttributeInterface $orderItemAttribute */
			if($orderItemAttribute->getOrderItemAttributeId() === $orderItemAttributeId->asInt()
			   && is_a($orderItemAttribute, $attributeClassName)
			)
			{
				$storedOrderItemAttribute = $orderItemAttribute;
			}
		}

		$this->orderWriteService->removeOrderItemAttribute($storedOrderItemAttribute);

		// Return response JSON.
		$response = array(
			'code'                 => 200,
			'status'               => 'success',
			'action'               => 'delete',
			'resource'             => $attributeClassName,
			'orderId'              => (int)$this->uri[1],
			'orderItemId'          => (int)$this->uri[3],
			'orderItemAttributeId' => (int)$this->uri[5]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /orders/:id/items/:id/attributes/:id Get Order Item Attribute/Property
	 * @apiVersion 2.1.0
	 * @apiName    GetOrderItemAttribute
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Returns multiple or a single order item attribute/property records through a GET request. This method supports
	 * all the GET parameters that are mentioned in the "Introduction" section of this documentation.
	 *
	 * @apiExample {curl} Get All Order Item Attributes
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400210/items/3/attributes
	 *
	 * @apiExample {curl} Get All Order Item Properties
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400210/items/3/properties
	 * 
	 * @apiExample {curl} Get Attribute With ID = 2
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400210/items/3/attributes/2
	 * 
	 * @apiExample {curl} Get Property With ID = 54
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400210/items/3/properties/54
	 */
	public function get()
	{
		$response            = array();
		$orderItemId         = new IdType($this->uri[3]);
		$orderItemAttributes = $this->orderReadService->getOrderItemById($orderItemId)->getAttributes();

		foreach($orderItemAttributes->getArray() as $orderItemAttribute)
		{
			$response[] = $this->orderJsonSerializer->serializeAttribute($orderItemAttribute);
		}

		if(isset($this->uri[5]))
		{
			foreach($response as $item)
			{
				$itemType = ($item['combisId'] !== null) ? 'properties' : 'attributes';
				if($item['id'] === (int)$this->uri[5] && $this->uri[4] == $itemType)
				{
					$response = $item;
					break;
				}
			}
		}
		else if($this->api->request->get('q'))
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
