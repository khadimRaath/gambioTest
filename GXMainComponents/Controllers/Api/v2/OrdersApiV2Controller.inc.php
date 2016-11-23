<?php
/* --------------------------------------------------------------
   OrdersApiV2Controller.inc.php 2016-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class OrdersApiV2Controller
 *
 * Provides a gateway to the OrderWriteService and OrderReadService classes, which handle the shop
 * order resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class OrdersApiV2Controller extends HttpApiV2Controller
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
	 * Order list item JSON serializer.
	 *
	 * @var OrderListItemJsonSerializer
	 */
	protected $orderListItemJsonSerializer;

	/**
	 * Sub resources.
	 *
	 * @var array
	 */
	protected $subresource;


	/**
	 * Initializes API Controller
	 */
	protected function __initialize()
	{
		$this->orderWriteService           = StaticGXCoreLoader::getService('OrderWrite');
		$this->orderReadService            = StaticGXCoreLoader::getService('OrderRead');
		$this->orderJsonSerializer         = MainFactory::create('OrderJsonSerializer');
		$this->orderListItemJsonSerializer = MainFactory::create('OrderListItemJsonSerializer');
		$this->subresource                 = array(
			'items'   => 'OrdersItemsApiV2Controller',
			'history' => 'OrdersHistoryApiV2Controller',
			'totals'  => 'OrdersTotalsApiV2Controller'
		);
	}


	/**
	 * @api        {post} /orders Create Order
	 * @apiVersion 2.2.0
	 * @apiName    CreateOrder
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * This method enables the creation of a new order into the system. The order can be bound to an existing
	 * customer or be standalone as implemented in the OrderService. Make sure that you check the Order resource
	 * representation. To see an example usage take a look at `docs/REST/samples/order-service/create_order.php`.
	 *
	 * @apiParamExample {json} Request-Body
	 * {
	 *   "id": 400210,
	 *   "statusId": 1,
	 *   "purchaseDate": "2015-11-06 12:22:39",
	 *   "currencyCode": "EUR",
	 *   "languageCode": "DE",
	 *   "totalWeight": 0.123,
	 *   "comment": "",
	 *   "paymentType": {
	 *     "title": "cod",
	 *     "module": "cod"
	 *   },
	 *   "shippingType": {
	 *     "title": "Pauschale Versandkosten (Standar",
	 *     "module": "flat_flat"
	 *   },
	 *   "customer": {
	 *     "id": 1,
	 *     "number": "",
	 *     "email": "admin@shop.de",
	 *     "phone": "0421 - 22 34 678",
	 *     "vatId": "",
	 *     "status": {
	 *       "id": 0,
	 *       "name": "Admin",
	 *       "image": "admin_status.gif",
	 *       "discount": 0,
	 *       "isGuest": false
	 *     }
	 *   },
	 *   "addresses": {
	 *     "customer": {
	 *       "gender": "m",
	 *       "firstname": "John",
	 *       "lastname": "Doe",
	 *       "company": "JD Company",
	 *       "street": "Test Street",
	 *       "houseNumber": "123",
	 *       "additionalAddressInfo": "1. Etage",
	 *       "suburb": "",
	 *       "postcode": "28219",
	 *       "city": "Bremen",
	 *       "countryId": 81,
	 *       "zoneId": 0,
	 *       "b2bStatus": false
	 *     },
	 *     "billing": {
	 *       "gender": "m",
	 *       "firstname": "John",
	 *       "lastname": "Doe",
	 *       "company": "JD Company",
	 *       "street": "Test Street",
	 *       "houseNumber": "123",
	 *       "additionalAddressInfo": "1. Etage",
	 *       "suburb": "",
	 *       "postcode": "28219",
	 *       "city": "Bremen",
	 *       "countryId": 81,
	 *       "zoneId": 0,
	 *       "b2bStatus": false
	 *     },
	 *     "delivery": {
	 *       "gender": "m",
	 *       "firstname": "John",
	 *       "lastname": "Doe",
	 *       "company": "JD Company",
	 *       "street": "Test Street",
	 *       "houseNumber": "123",
	 *       "additionalAddressInfo": "1. Etage",
	 *       "suburb": "",
	 *       "postcode": "28219",
	 *       "city": "Bremen",
	 *       "countryId": 81,
	 *       "zoneId": 0,
	 *       "b2bStatus": false
	 *     }
	 *   },
	 *   "items": [
	 *     {
	 *       "id": 1,
	 *       "model": "12345-s-black",
	 *       "name": "Ein Artikel",
	 *       "quantity": 1,
	 *       "price": 11,
	 *       "finalPrice": 11,
	 *       "tax": 19,
	 *       "isTaxAllowed": true,
	 *       "discount": 0,
	 *       "shippingTimeInformation": "",
	 *       "checkoutInformation": "Checkout information goes here ...",
	 *       "quantityUnitName": "Liter",
	 *       "attributes": [
	 *         {
	 *           "id": 1,
	 *           "name": "Farbe",
	 *           "value": "rot",
	 *           "price": 0,
	 *           "priceType": "+",
	 *           "optionId": 1,
	 *           "optionValueId": 1,
	 *           "combisId": null
	 *         }
	 *       ],
	 *       "downloadInformation": [
	 *         {
	 *           "filename": "Dokument.pdf",
	 *           "maxDaysAllowed": 5,
	 *           "countAvailable": 14
	 *         }
	 *       ],
	 *       "addonValues": {
	 *         "productId": "2",
	 *         "quantityUnitId": "1"
	 *       }
	 *     }
	 *   ],
	 *   "totals": [
	 *     {
	 *       "id": 1,
	 *       "title": "Zwischensumme:",
	 *       "value": 50,
	 *       "valueText": "50,00 EUR",
	 *       "class": "ot_subtotal",
	 *       "sortOrder": 10
	 *     }
	 *   ],
	 *   "statusHistory": [
	 *     {
	 *       "id": 1,
	 *       "statusId": 1,
	 *       "dateAdded": "2015-11-06 12:22:39",
	 *       "comment": "",
	 *       "customerNotified": true
	 *     }
	 *   ],
	 *   "addonValues": {
	 *     "customerIp": "",
	 *     "downloadAbandonmentStatus": "0",
	 *     "serviceAbandonmentStatus": "0",
	 *     "ccType": "",
	 *     "ccOwner": "",
	 *     "ccNumber": "",
	 *     "ccExpires": "",
	 *     "ccStart": "",
	 *     "ccIssue": "",
	 *     "ccCvv": ""
	 *   }
	 * }
	 *
	 * @apiParam {String} statusId Order status ID, use one of the existing statuses IDs.
	 * @apiParam {String} purchaseDate Must have the 'Y-m-d H:i:s' format.
	 * @apiParam {String} currencyCode Order's currency code, use one of the existing currency codes.
	 * @apiParam {String} languageCode Use one of the existing language codes.
	 * @apiParam {Number} totalWeight The total weight of the order items.
	 * @apiParam {String} comment Order's comments.
	 * @apiParam {Object} paymentType Contains information about the payment type, use values that match with the
	 *           shop's modules.
	 * @apiParam {String} paymentType.title The payment title.
	 * @apiParam {String} paymentType.module The payment module name.
	 * @apiParam {Object} shippingType Contains information about the shipping type, use values that match with the
	 *           shop's modules.
	 * @apiParam {String} shippingType.title The shipping title.
	 * @apiParam {String} shippingType.module The shipping module name.
	 * @apiParam {Object} customer Contains the order's customer information.
	 * @apiParam {String} customer.number Customer's number (often referred as CID).
	 * @apiParam {String} customer.email Customer's email address.
	 * @apiParam {String} customer.phone Customer's telephone number.
	 * @apiParam {String} customer.vatId Customer's VAT ID number.
	 * @apiParam {Object} customer.status Contains information about the customer's status on the system.
	 * @apiParam {Number} customer.status.id The customer's status ID must be one of the existing statuses in the shop.
	 * @apiParam {String} customer.status.name The customer-status name.
	 * @apiParam {String} customer.status.image The customer-status image (check the value from the shop).
	 * @apiParam {Number} customer.status.discount The discount that is made to this customer status.
	 * @apiParam {Boolean} customer.status.isGuest Defines whether the customer is a guest.
	 * @apiParam {Object} addresses Contains the address information of the order. There are three different kind of
	 *           addresses: customer, billing and delivery.
	 * @apiParam {Object} addresses.customer Contains the customer-address data.
	 * @apiParam {String} addresses.customer.gender The gender value can be either "m" or "f".
	 * @apiParam {String} addresses.customer.firstname First name of the address block.
	 * @apiParam {String} addresses.customer.lastname Last name of the address block.
	 * @apiParam {String} addresses.customer.company Company name of the address block.
	 * @apiParam {String} addresses.customer.street Street of the address block.
	 * @apiParam {string} addresses.customer.houseNumber The house number of the address block.
	 * @apiParam {string} addresses.customer.additionalAddressInfo Additional information of the address block.
	 * @apiParam {String} addresses.customer.suburb Suburb of the address block.
	 * @apiParam {String} addresses.customer.postcode Postcode of the address block.
	 * @apiParam {String} addresses.customer.city City of the address block.
	 * @apiParam {String} addresses.customer.countryId Country ID of the address block. You can use the "countries"
	 *           resource of the API to get the available countries.
	 * @apiParam {String} addresses.customer.zoneId Zone ID of the address block. You can use the "zones" resource of
	 *           the API to get the available countries.
	 * @apiParam {Boolean} addresses.customer.b2bStatus Whether the customer has the b2bStatus.
	 * @apiParam {Object} addresses.billing{...} Contains the address block for the billing. It expects the same value
	 *           types as the customer-address block. See the JSON example above.
	 * @apiParam {Object} addresses.delivery{...} Contains the address block for the billing. It expects the same value
	 *           types as the customer-address block. See the JSON example above.
	 * @apiParam {Array} items Every order contains a list of order items which can also have their own attributes.
	 * @apiParam {String} items.model Item's model value.
	 * @apiParam {String} items.name Item's name value.
	 * @apiParam {Number} items.quantity Quantity of the purchase.
	 * @apiParam {Number} items.price The initial price of the order item.
	 * @apiParam {Number} items.finalPrice The final price of the order item.
	 * @apiParam {Number} items.tax The tax applied to the value.
	 * @apiParam {Boolean} items.isTaxAllowed Whether tax is allowed.
	 * @apiParam {Number} items.discount Percentage of the discount made for this order.
	 * @apiParam {String} items.shippingTimeInformation Include shipping information to the order.
	 * @apiParam {String} items.checkoutInformation Include checkout information to the order.
	 * @apiParam {String} items.quantityUnitName The Quantity unit name of the order item.
	 * @apiParam {Array} items.attributes Contains some attributes or properties of the order item. The difference
	 *           between the attributes and the properties is that attributes must have the "optionId" and
	 *           "optionValueId" values while properties must only have the "combisId" value. The properties system
	 *           is still included as a fallback to old releases of the shop, so we will use the "attributes" term in
	 *           this document.
	 * @apiParam {String} items.attributes.name Attribute's name.
	 * @apiParam {String} items.attributes.value Attribute's value.
	 * @apiParam {Number} items.attributes.price Give the attributes price.
	 * @apiParam {String} items.attributes.priceType Make sure that you use one of the existing price types of the
	 *           shop.
	 * @apiParam {Number} items.attributes.optionId Only-attributes need this value.
	 * @apiParam {Number} items.attributes.optionValueId Only-attributes need this value.
	 * @apiParam {Number} items.attributes.combisId Only-properties need this value.
	 * @apiParam {Array} items.downloadInformation Contains the downloads of the order item.
	 * @apiParam {String} items.downloadInformation.filename Contains a non empty filename.
	 * @apiParam {Number} items.downloadInformation.maxDaysAllowed Contains the number of days where downloads are
	 *           possible.
	 * @apiParam {Number} items.downloadInformation.countAvailable Contains the number of possible downloads.
	 * @apiParam {Object} items.addonValues (Optional) Contains key value pairs of additional order item data.
	 * @apiParam {Array} totals Contains the order totals. The order totals are entries that display analytic
	 *           information about the charges of the user.
	 * @apiParam {String} totals.title Order total's title.
	 * @apiParam {Number} totals.value The value stands for the money.
	 * @apiParam {String} totals.valueText String representation of the value containing the currency code.
	 * @apiParam {String} totals.class Internal order-total class. A list of possible values can be seen in the
	 *           database once you create a complete order record.
	 * @apiParam {Number} totals.sortOrder Defines the order of the totals list as they are being displayed.
	 * @apiParam {Object} addonValues (Optional) Contains key value pairs of additional order data.
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Order resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest The body of the request was empty.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order data were not provided."
	 * }
	 */
	public function post()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$orderJsonString = $this->api->request->getBody();

		if(empty($orderJsonString))
		{
			throw new HttpApiV2Exception('Order data were not provided.', 400);
		}

		$order = $this->orderJsonSerializer->deserialize($orderJsonString);

		if($order->getCustomerId() !== 0)
		{
			$orderId = $this->orderWriteService->createNewCustomerOrder(new IdType($order->getCustomerId()),
			                                                            $order->getCustomerStatusInformation(),
			                                                            new StringType($order->getCustomerNumber()),
			                                                            new EmailStringType($order->getCustomerEmail()),
			                                                            new StringType($order->getCustomerTelephone()),
			                                                            new StringType($order->getVatIdNumber()),
			                                                            $order->getCustomerAddress(),
			                                                            $order->getBillingAddress(),
			                                                            $order->getDeliveryAddress(),
			                                                            $order->getOrderItems(),
			                                                            $order->getOrderTotals(),
			                                                            $order->getShippingType(),
			                                                            $order->getPaymentType(),
			                                                            $order->getCurrencyCode(),
			                                                            $order->getLanguageCode(),
			                                                            new DecimalType($order->getTotalWeight()),
			                                                            new StringType($order->getComment()),
			                                                            new IntType($order->getStatusId()),
			                                                            $order->getAddonValues());
		}
		else
		{
			$orderId = $this->orderWriteService->createNewStandaloneOrder(new StringType($order->getCustomerNumber()),
			                                                              new EmailStringType($order->getCustomerEmail()),
			                                                              new StringType($order->getCustomerTelephone()),
			                                                              new StringType($order->getVatIdNumber()),
			                                                              $order->getCustomerAddress(),
			                                                              $order->getBillingAddress(),
			                                                              $order->getDeliveryAddress(),
			                                                              $order->getOrderItems(),
			                                                              $order->getOrderTotals(),
			                                                              $order->getShippingType(),
			                                                              $order->getPaymentType(),
			                                                              $order->getCurrencyCode(),
			                                                              $order->getLanguageCode(),
			                                                              new DecimalType($order->getTotalWeight()),
			                                                              new StringType($order->getComment()),
			                                                              new IntType($order->getStatusId()),
			                                                              $order->getAddonValues()

			);
		}

		$storedOrder = $this->orderReadService->getOrderById(new IdType($orderId));

		$response = $this->orderJsonSerializer->serialize($storedOrder, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /orders/:id Update Order
	 * @apiVersion 2.2.0
	 * @apiName    UpdateOrder
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method to update an existing order record. It uses the complete order JSON resource so
	 * it might be useful to fetch it through a GET request, alter its values and PUT it back in order
	 * to perform the update operation. Take a look in the POST method for more detailed explanation on
	 * every resource property. To see an example usage take a look at
	 * `docs/REST/samples/order-service/update_order.php`
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated Order resource in the response body.
	 *
	 * @apiError 400-BadRequest The body of the request was empty or the order record ID was not provided or
	 * is invalid.
	 *
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order data were not provided."
	 * }
	 *
	 * @apiErrorExample Error-Response (Missing or invalid ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order record ID was not provided or is invalid."
	 * }
	 */
	public function put()
	{
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Order record ID was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		$orderJsonString = $this->api->request->getBody();

		if(empty($orderJsonString))
		{
			throw new HttpApiV2Exception('Order data were not provided.', 400);
		}

		$orderId = new IdType($this->uri[1]);

		// Ensure that the order has the correct order id of the request url
		$orderJsonString = $this->_setJsonValue($orderJsonString, 'id', $orderId->asInt());

		$order   = $this->orderJsonSerializer->deserialize($orderJsonString,
		                                                   $this->orderReadService->getOrderById($orderId));

		$this->orderWriteService->updateOrder($order);

		$response = $this->orderJsonSerializer->serialize($order, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response, 200);
	}


	/**
	 * @api        {patch} /orders/:id/status Update Order Status
	 * @apiVersion 2.3.0
	 * @apiName    UpdateOrderStatus
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Use this method if you want to update the status of an existing order and create an order history entry. The
	 * status history entry must also contain extra information as shown in the JSON example.
	 *
	 * @apiParamExample {json} Order Status History
	 * {
	 *   "statusId": 1,
	 *   "comment": "This is the entry comment",
	 *   "customerNotified": false
	 * }
	 *
	 * @apiParam {Number} statusId The new status ID will also be set in the order record.
	 * @apiParam {String} comment Assign a comment to the status history entry.
	 * @apiParam {Boolean} customerNotified Defines whether the customer was notified by this change.
	 *
	 * @apiSuccess (200) Request-Body If successful, this method returns the complete order status history resource
	 * in the response body.
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "id": 984,
	 *   "statusId": 3,
	 *   "dateAdded": "2016-01-22 10:52:11",
	 *   "comment": "This is the entry's comments",
	 *   "customerNotified": true
	 * }
	 *
	 * @apiError 400-BadRequest Order data were not provided or order record ID was not provided or is invalid.
	 *
	 * @apiErrorExample Error-Response (Empty request body)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order data were not provided."
	 * }
	 *
	 * @apiErrorExample Error-Response (Missing or invalid ID)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order record ID was not provided or is invalid."
	 * }
	 */
	public function patch()
	{
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Order record ID was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		if(isset($this->uri[2]) && $this->uri[2] == 'status')
		{
			$orderJsonString = $this->api->request->getBody();

			if(empty($orderJsonString))
			{
				throw new HttpApiV2Exception('Order data were not provided.', 400);
			}

			$orderId = new IdType($this->uri[1]);
			$json    = json_decode($orderJsonString);

			$this->orderWriteService->updateOrderStatus($orderId, new IntType($json->statusId),
			                                            new StringType((string)$json->comment),
			                                            new BoolType($json->customerNotified));

			$order              = $this->orderReadService->getOrderById($orderId);
			$orderStatusHistory = $order->getStatusHistory()->getArray();
			/** @var OrderStatusHistoryListItem $lastStatusHistoryItem */
			$lastStatusHistoryItem = array_pop($orderStatusHistory);

			$response = $this->orderJsonSerializer->serializeOrderStatusHistoryListItem($lastStatusHistoryItem);
			$this->_writeResponse($response, 200);
		}
	}


	/**
	 * @api        {delete} /orders/:id Delete Order
	 * @apiVersion 2.1.0
	 * @apiName    DeleteOrder
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Remove an entire Order record from the database. This method will also remove the order-items along with
	 * their attributes and the order-total records. To see an example usage take a look at
	 * `docs/REST/samples/order-service/remove_order.php`
	 *
	 * @apiExample {curl} Delete Order With ID = 400597
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400597
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "resource": "Order",
	 *   "orderId": 400597
	 * }
	 *
	 * @apiError 400-BadRequest The order ID value was invalid.
	 *
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Order record ID was not provided in the resource URL."
	 * }
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Order record ID was not provided in the resource URL.', 400);
		}

		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		// Remove order record from database.
		$this->orderWriteService->removeOrderById(new IdType($this->uri[1]));

		// Return response JSON.
		$response = array(
			'code'     => 200,
			'status'   => 'success',
			'action'   => 'delete',
			'resource' => 'Order',
			'orderId'  => (int)$this->uri[1]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /orders/:id Get Orders
	 * @apiVersion 2.3.0
	 * @apiName    GetOrder
	 * @apiGroup   Orders
	 *
	 * @apiDescription
	 * Get multiple or a single order record through a GET request. This method supports all the GET parameters
	 * that are mentioned in the "Introduction" section of this documentation.
	 *
	 * Important: Whenever you make requests that will return multiple orders the response will contain a smaller
	 * version of each order record called order-list-item. This is done for better performance because the creation
	 * of a complete order record takes significant time (many objects are involved). If you still need the complete
	 * data of an order record you will have to make an extra GET request with the ID provided.
	 *
	 * @apiExample {curl} Get All Orders
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders
	 *
	 * @apiExample {curl} Get Order With ID = 400242
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400242
	 *
	 * @apiExample {curl} Search Orders
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders?q=DE
	 *
	 * @apiExample {curl} Get Order's Items
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400573/items
	 *
	 * @apiExample {curl} Get Order Item's Attributes
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400573/items/57/attributes
	 *
	 * @apiExample {curl} Get Orders Totals
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/orders/400573/totals
	 */
	public function get()
	{
		if($this->_mapResponse($this->subresource))
		{
			return;
		}

		if(isset($this->uri[1]) && is_numeric($this->uri[1])) // Get Single Record
		{
			$orders = array($this->orderReadService->getOrderById(new IdType($this->uri[1])));
		}
		else if($this->api->request->get('q') !== null)
		{
			$orders = $this->orderReadService->getOrderListByKeyword(new StringType($this->api->request->get('q')))
			                                 ->getArray();
		}
		else
		{
			$orders = $this->orderReadService->getOrderList()->getArray();
		}

		$response = array();

		foreach($orders as $order)
		{
			if($order instanceof OrderInterface)
			{
				$serialized = $this->orderJsonSerializer->serialize($order, false);
			}
			else
			{
				$serialized = $this->orderListItemJsonSerializer->serialize($order, false);
			}

			$response[] = $serialized;
		}

		$this->_sortResponse($response);
		$this->_paginateResponse($response);
		$this->_minimizeResponse($response);
		$this->_linkResponse($response);

		// Return single resource to client and not array.
		if(isset($this->uri[1]) && is_numeric($this->uri[1]) && count($response) > 0)
		{
			$response = $response[0];
		}

		$this->_writeResponse($response);
	}
}
