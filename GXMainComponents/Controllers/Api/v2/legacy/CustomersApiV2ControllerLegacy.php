<?php

/* --------------------------------------------------------------
   CustomersApiV2ControllerLegacy.php 2016-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @api        {post} /customers Create Customer
 * @apiVersion 2.2.0
 * @apiName    CreateCustomer
 * @apiGroup   Customers
 *
 * @apiDescription
 * This method enables the creation of a new customer (whether registree or a guest). Additionally
 * the user can provide new address information or just set the id of an existing one. Check the
 * examples bellow. An example script to demonstrate the creation of a new customer is located under
 * `./docs/REST/samples/customer-service/create_account.php` in the git clone, another one to demonstrate the
 * creation of a guest customer is located under `./docs/REST/samples/customer-service/create_guest_account.php`.
 *
 * @apiParamExample {json} Registree (New Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "password": "0123456789",
 *   "isGuest": false,
 *   "address": {
 *     "company": "Test Company",
 *     "street": "Test Street",
 *     "houseNumber": "123",
 *     "additionalAddressInfo": "1. Etage",
 *     "suburb": "Test Suburb",
 *     "postcode": "23983",
 *     "city": "Test City",
 *     "countryId": 81,
 *     "zoneId": 84,
 *     "b2bStatus": true
 *   }
 * }
 *
 * @apiParamExample {json} Registree (Existing Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "password": "0123456789",
 *   "isGuest": false,
 *   "addressId": 57
 * }
 *
 *
 * @apiParamExample {json} Guest (New Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "isGuest": true,
 *   "address": {
 *     "company": "Test Company",
 *     "street": "Test Street",
 *     "houseNumber": "123",
 *     "additionalAddressInfo": "1. Etage",
 *     "suburb": "Test Suburb",
 *     "postcode": "23983",
 *     "city": "Test City",
 *     "countryId": 81,
 *     "zoneId": 84,
 *     "b2bStatus": false
 *   }
 * }
 *
 * @apiParamExample {json} Guest (Existing Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "isGuest": true,
 *   "addressId": 57
 *   }
 * }
 *
 * @apiParam {string} gender Customer's gender, provide "m" for male and "f" for female.
 * @apiParam {string} firstname Customer's first name.
 * @apiParam {string} lastname Customer's last name.
 * @apiParam {string} dateOfBirth Customer's date of birth in "yyyy-mm-dd" format.
 * @apiParam {string} vatNumber Valid customer VAT number.
 * @apiParam {string} telephone Customer's telephone number.
 * @apiParam {string} fax Customer's fax number.
 * @apiParam {string} email Valid email address for the customer.
 * @apiParam {string} password (Optional) Customer's password, only registree records need this value.
 * @apiParam {bool} isGuest Customer's record type, whether true if guest or false if not.
 * @apiParam {int} addressId Provide a record ID if the address already exist in the database (otherwise omit this
 *           property).
 * @apiParam {object} address (Optional) Contains the customer's address data, can be omitted if the "addressId" is
 *           provided.
 * @apiParam {string} address.company Customer's company name.
 * @apiParam {string} street The address street.
 * @apiParam {string} houseNumber The address house number.
 * @apiParam {string} additionalAddressInfo Additional information about the address.
 * @apiParam {string} address.suburb Customer's suburb.
 * @apiParam {string} address.postcode Customer's postcode.
 * @apiParam {string} address.city Customer's city.
 * @apiParam {int} address.countryId Must be a country ID registered in the shop database.
 * @apiParam {int} address.zoneId The country zone ID, as registered in the shop database.
 *
 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Customers resource in the
 * response body.
 *
 * @apiError 409-Conflict The API will return this status code if the customer's email already exists in the
 * database (only applies on registree records).
 */

/**
 * @api        {post} /customers Create Customer
 * @apiVersion 2.1.0
 * @apiName    CreateCustomer
 * @apiGroup   Customers
 *
 * @apiDescription
 * This method enables the creation of a new customer (whether registree or a guest). Additionally
 * the user can provide new address information or just set the id of an existing one. Check the
 * examples bellow. An example script to demonstrate the creation of a new customer is located under
 * `./docs/REST/samples/customer-service/create_account.php` in the git clone, another one to demonstrate the
 * creation of a guest customer is located under `./docs/REST/samples/customer-service/create_guest_account.php`.
 *
 * @apiParamExample {json} Registree (New Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "password": "0123456789",
 *   "isGuest": false,
 *   "address": {
 *     "company": "Test Company",
 *     "street": "Test Street",
 *     "suburb": "Test Suburb",
 *     "postcode": "23983",
 *     "city": "Test City",
 *     "countryId": 81,
 *     "zoneId": 84,
 *     "b2bStatus": true
 *   }
 * }
 *
 * @apiParamExample {json} Registree (Existing Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "password": "0123456789",
 *   "isGuest": false,
 *   "addressId": 57
 * }
 *
 *
 * @apiParamExample {json} Guest (New Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "isGuest": true,
 *   "address": {
 *     "company": "Test Company",
 *     "street": "Test Street",
 *     "suburb": "Test Suburb",
 *     "postcode": "23983",
 *     "city": "Test City",
 *     "countryId": 81,
 *     "zoneId": 84,
 *     "b2bStatus": false
 *   }
 * }
 *
 * @apiParamExample {json} Guest (Existing Address)
 * {
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "isGuest": true,
 *   "addressId": 57
 *   }
 * }
 *
 * @apiParam {string} gender Customer's gender, provide "m" for male and "f" for female.
 * @apiParam {string} firstname Customer's first name.
 * @apiParam {string} lastname Customer's last name.
 * @apiParam {string} dateOfBirth Customer's date of birth in "yyyy-mm-dd" format.
 * @apiParam {string} vatNumber Valid customer VAT number.
 * @apiParam {string} telephone Customer's telephone number.
 * @apiParam {string} fax Customer's fax number.
 * @apiParam {string} email Valid email address for the customer.
 * @apiParam {string} password (Optional) Customer's password, only registree records need this value.
 * @apiParam {bool} isGuest Customer's record type, whether true if guest or false if not.
 * @apiParam {int} addressId Provide a record ID if the address already exist in the database (otherwise omit this
 *           property).
 * @apiParam {object} address (Optional) Contains the customer's address data, can be omitted if the "addressId" is
 *           provided.
 * @apiParam {string} address.company Customer's company name.
 * @apiParam {string} address.suburb Customer's suburb.
 * @apiParam {string} address.postcode Customer's postcode.
 * @apiParam {string} address.city Customer's city.
 * @apiParam {int} address.countryId Must be a country ID registered in the shop database.
 * @apiParam {int} address.zoneId The country zone ID, as registered in the shop database.
 *
 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Customers resource in the
 * response body.
 *
 * @apiError 409-Conflict The API will return this status code if the customer's email already exists in the
 * database (only applies on registree records).
 */

/**
 * @api        {put} /customers/:id Update Customer
 * @apiVersion 2.1.0
 * @apiName    UpdateCustomer
 * @apiGroup   Customers
 *
 * @apiDescription
 * This method will update the information of an existing customer record. You will
 * need to provide all the customer information with the request (except from password
 * and customer id). Also note that you only have to include the "addressId" property.
 * An example script to demonstrate how to update the admin accounts telephone number
 * is located under `./docs/REST/samples/customer-service/update_admin_telephone.php`
 * in the git clone.
 *
 * @apiParamExample {json} Request-Body (Registree)
 * {
 *   "number": "234982739",
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "vatNumberStatus": 0,
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "statusId": 2,
 *   "isGuest": false,
 *   "addressId": 54
 * }
 *
 * @apiParamExample {json} Request-Body (Guest)
 * {
 *   "number": "234982739",
 *   "gender": "m",
 *   "firstname": "John",
 *   "lastname": "Doe",
 *   "dateOfBirth": "1985-02-13",
 *   "vatNumber": "0923429837942",
 *   "vatNumberStatus": true,
 *   "telephone": "2343948798345",
 *   "fax": "2093049283",
 *   "email": "customer@email.de",
 *   "statusId": 1,
 *   "isGuest": true,
 *   "addressId": 98
 * }
 *
 * @apiSuccess Response-Body If successful, this method returns the updated customer resource in the response body.
 *
 * @apiError 400-BadRequest Customer record ID was not provided or is invalid.
 * @apiError 400-BadRequest Customer data were not provided.
 * @apiError 404-NotFound Customer record was not found.
 * @apiError 409-Conflict The API will return this status code if the customer's email already exists in the
 * database (only applies on registree records).
 */