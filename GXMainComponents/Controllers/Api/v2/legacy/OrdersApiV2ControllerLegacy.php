<?php

/* --------------------------------------------------------------
   OrdersApiV2ControllerLegacy.php 2016-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @api             {post} /orders Create Order
 * @apiVersion      2.1.0
 * @apiName         CreateOrder
 * @apiGroup        Orders
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
 *       "street": "Rotterstr 33",
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
 *       "street": "Rotterstr 33",
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
 *       "street": "Rotterstr 33",
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
 *         "productId": "2"
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
 * @apiParam {String} addresses.customer.street Street and number of the address block.
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
 * @apiError        400-BadRequest The body of the request was empty.
 * @apiErrorExample Error-Response
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 400,
 *   "status": "error",
 *   "message": "Order data were not provided."
 * }
 */