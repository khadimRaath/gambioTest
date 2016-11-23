<?php

/* --------------------------------------------------------------
   AddressesApiV2ControllerLegacy.php 2016-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @api        {post} /addresses Create Address
 * @apiVersion 2.1.0
 * @apiName    CreateAddress
 * @apiGroup   Addresses
 *
 * @apiParamExample {json} Request-Body
 * {
 *   "customerId": 1,
 *   "gender": "m",
 *   "company": "Test Company",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "street": "Test Street 1",
 *   "suburb": "Test Suburb",
 *   "postcode": "23983",
 *   "city": "Test City",
 *   "countryId": 81,
 *   "zoneId": 84,
 *   "class": null,
 *   "b2bStatus": false
 * }
 *
 * @apiParam {int} customerId The customer's record ID to whom the address belong.
 * @apiParam {string} gender Provide either "m" or "f" for male and female.
 * @apiParam {string} company The address company name.
 * @apiParam {string} firstname The address firstname.
 * @apiParam {string} lastname The address lastname.
 * @apiParam {string} street The address street.
 * @apiParam {string} suburb The address suburb.
 * @apiParam {string} postcode The address postcode.
 * @apiParam {string} city The address city.
 * @apiParam {int} countryId Provide an existing "countryId", if it does not exist create it through the
 *           "countries" API methods.
 * @apiParam {int} zoneId Provide an existing "countryId", if it does not exist create it through the "zones" API
 *           methods.
 * @apiParam {string} class The address class can be any string used for distinguishing the address from other
 *           records.
 * @apiParam {bool} b2bStatus Defines the Business-to-Business status of the address.
 *
 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Customers resource in
 * the response body.
 *
 * @apiError 400-BadRequest Address data were not provided.
 *
 * @apiErrorExample Error-Response
 * HTTP/1.1 400 Bad Request
 * {
 *   "code": 400,
 *   "status": "error",
 *   "message": "Address data were not provided."
 * }
 */