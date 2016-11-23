<?php
/* --------------------------------------------------------------
   CustomersApiController.inc.php 2016-09-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CustomersApiV2Controller
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CustomersApiV2Controller extends HttpApiV2Controller
{
	/**
	 * @var CustomerWriteService
	 */
	protected $customerWriteService;

	/**
	 * @var CustomerReadService
	 */
	protected $customerReadService;

	/**
	 * @var CountryService
	 */
	protected $countryService;

	/**
	 * @var AddressBookSErvice
	 */
	protected $addressService;

	/**
	 * @var CustomerJsonSerializer
	 */
	protected $customerJsonSerializer;

	/**
	 * @var AddressJsonSerializer
	 */
	protected $addressJsonSerializer;


	/**
	 * Initializes API Controller
	 */
	protected function __initialize()
	{
		$this->customerWriteService   = StaticGXCoreLoader::getService('CustomerWrite');
		$this->customerReadService    = StaticGXCoreLoader::getService('CustomerRead');
		$this->countryService         = StaticGXCoreLoader::getService('Country');
		$this->addressService         = StaticGXCoreLoader::getService('AddressBook');
		$this->customerJsonSerializer = MainFactory::create('CustomerJsonSerializer');
		$this->addressJsonSerializer  = MainFactory::create('AddressJsonSerializer');
	}


	/**
	 * @api        {post} /customers Create Customer
	 * @apiVersion 2.3.0
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
	 *   },
	 *   "addonValues": {
	 *     "test_key": "test_value"
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
	 *   "addressId": 57,
	 *   "addonValues": {
	 *     "test_key": "test_value"
	 *   }
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
	 *   },
	 *   "addonValues": {
	 *     "test_key": "test_value"
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
	 *   "addressId": 57,
	 *   "addonValues": {
	 *     "test_key": "test_value
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
	 * @apiParam {Object} addonValues Contains some extra addon values.
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete Customers resource in the
	 * response body.
	 *
	 * @apiError 409-Conflict The API will return this status code if the customer's email already exists in the
	 * database (only applies on registree records).
	 */
	public function post()
	{
		$customerJsonString = $this->api->request->getBody();

		if(empty($customerJsonString))
		{
			throw new HttpApiV2Exception('Customer data were not provided.', 400);
		}

		$customerJsonObject = json_decode($customerJsonString);

		// Check if customer email already exists. 
		if(isset($customerJsonObject->email) && $customerJsonObject->type === 'registree'
		   && $this->customerReadService->registreeEmailExists($customerJsonObject->email)
		)
		{
			throw new HttpApiV2Exception('Registree email address already exists in the database.', 409);
		}

		$country = $this->countryService->getCountryById(new IdType($customerJsonObject->address->countryId));
		$zone    = $this->countryService->getCountryZoneById(new IdType($customerJsonObject->address->zoneId));
		if($customerJsonObject->addressId !== null)
		{
			$address = $this->addressService->findAddressById(new IdType((int)$customerJsonObject->addressId));

			$addressBlock = MainFactory::create('AddressBlock', $address->getGender(), $address->getFirstname(),
			                                    $address->getLastname(), $address->getCompany(),
			                                    $address->getB2BStatus(), $address->getStreet(),
			                                    $address->getHouseNumber(), $address->getAdditionalAddressInfo(),
			                                    $address->getSuburb(), $address->getPostcode(), $address->getCity(),
			                                    $address->getCountry(), $address->getCountryZone());
		}
		else
		{
			$addressBlock = MainFactory::create('AddressBlock',
			                                    MainFactory::create('CustomerGender', $customerJsonObject->gender),
			                                    MainFactory::create('CustomerFirstname',
			                                                        $customerJsonObject->firstname),
			                                    MainFactory::create('CustomerLastname', $customerJsonObject->lastname),
			                                    MainFactory::create('CustomerCompany',
			                                                        $customerJsonObject->address->company),
			                                    MainFactory::create('CustomerB2BStatus',
			                                                        $customerJsonObject->address->b2bStatus),
			                                    MainFactory::create('CustomerStreet',
			                                                        $customerJsonObject->address->street),
			                                    MainFactory::create('CustomerHouseNumber',
			                                                        $customerJsonObject->address->houseNumber),
			                                    MainFactory::create('CustomerAdditionalAddressInfo',
			                                                        $customerJsonObject->address->additionalAddressInfo),
			                                    MainFactory::create('CustomerSuburb',
			                                                        $customerJsonObject->address->suburb),
			                                    MainFactory::create('CustomerPostcode',
			                                                        $customerJsonObject->address->postcode),
			                                    MainFactory::create('CustomerCity', $customerJsonObject->address->city),
			                                    $country, $zone);
		}
		
		$addonValuesArray = array();
		if(isset($customerJsonObject->addonValues))
		{
			$addonValuesArray = json_decode(json_encode($customerJsonObject->addonValues), true);
		}

		if($customerJsonObject->isGuest === true)
		{
			$customer = $this->customerWriteService->createNewGuest(MainFactory::create('CustomerEmail',
			                                                                            $customerJsonObject->email),
			                                                        MainFactory::create('DateTime',
			                                                                            $customerJsonObject->dateOfBirth),
			                                                        MainFactory::create('CustomerVatNumber',
			                                                                            $customerJsonObject->vatNumber),
			                                                        MainFactory::create('CustomerCallNumber',
			                                                                            $customerJsonObject->telephone),
			                                                        MainFactory::create('CustomerCallNumber',
			                                                                            $customerJsonObject->fax),
			                                                        $addressBlock,
			                                                        MainFactory::create('KeyValueCollection',
			                                                                            $addonValuesArray));
		}
		else
		{
			$customer = $this->customerWriteService->createNewRegistree(MainFactory::create('CustomerEmail',
			                                                                                $customerJsonObject->email),
			                                                            MainFactory::create('CustomerPassword',
			                                                                                $customerJsonObject->password),
			                                                            MainFactory::create('DateTime',
			                                                                                $customerJsonObject->dateOfBirth),
			                                                            MainFactory::create('CustomerVatNumber',
			                                                                                $customerJsonObject->vatNumber),
			                                                            MainFactory::create('CustomerCallNumber',
			                                                                                $customerJsonObject->telephone),
			                                                            MainFactory::create('CustomerCallNumber',
			                                                                                $customerJsonObject->fax),
			                                                            $addressBlock,
			                                                            MainFactory::create('KeyValueCollection',
			                                                                                $addonValuesArray));
		}

		$response = $this->customerJsonSerializer->serialize($customer, false);
		$this->_linkResponse($response);
		$this->_locateResource('customers', (string)$customer->getId());
		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /customers/:id Update Customer
	 * @apiVersion 2.3.0
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
	 *   "addressId": 54,
	 *   "addonValues": {
	 *     "test_key": "test_value-UPDATED"
	 *   }
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
	 *   "addressId": 98,
	 *   "addonValues": {
	 *     "test_key": "test_value-UPDATED"
	 *   }
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
	public function put()
	{
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Customer record ID was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		$customerJsonString = $this->api->request->getBody();

		if(empty($customerJsonString))
		{
			throw new HttpApiV2Exception('Customer data were not provided.', 400);
		}
		
		// Fetch existing customer record.
		$customerId = (int)$this->uri[1];
		$customers  = $this->customerReadService->filterCustomers(array('customers_id' => $customerId));

		if(empty($customers))
		{
			throw new HttpApiV2Exception('Customer record was not found.', 404);
		}

		$customer = array_shift($customers);
		
		// Ensure that the customer has the correct customer id of the request url
		$customerJsonString = $this->_setJsonValue($customerJsonString, 'id', $customerId);

		// Apply provided values into it.
		$customer = $this->customerJsonSerializer->deserialize($customerJsonString, $customer);

		// Check if new email belongs to another customer.
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();

		$count = $db->get_where('customers', array(
			'customers_email_address' => (string)$customer->getEmail(),
			'customers_id <>'         => (string)$customer->getId()
		))->num_rows();

		if($count)
		{
			throw new HttpApiV2Exception('Provided email address is used by another customer: '
			                             . (string)$customer->getEmail(), 409);
		}

		// Update record and respond to client.
		$this->customerWriteService->updateCustomer($customer);
		$response = $this->customerJsonSerializer->serialize($customer, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /customers/:id Delete Customer
	 * @apiVersion 2.1.0
	 * @apiName    DeleteCustomer
	 * @apiGroup   Customers
	 *
	 * @apiDescription
	 * Remove a customer record from the system. This method will always return success
	 * even if the customer does not exist (due to internal CustomerWriteService architecture
	 * decisions, which strive to avoid unnecessary failures).
	 * An example script to demonstrate how to delete a customer is located under
	 * `./docs/REST/samples/customer-service/remove_account.php` in the git clone.
	 *
	 * @apiExample {curl} Delete Customer with ID = 84
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/customers/84
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "customerId": 84
	 * }
	 */
	public function delete()
	{
		// Check if record ID was provided.
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Customer record ID was not provided in the resource URL.', 400);
		}

		$customerId = (int)$this->uri[1];

		// Remove customer from database.
		$this->customerWriteService->deleteCustomerById(new IdType($customerId));

		// Return response JSON.
		$response = array(
			'code'       => 200,
			'status'     => 'success',
			'action'     => 'delete',
			'customerId' => $customerId
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /customers/:id Get Customers
	 * @apiVersion 2.3.0
	 * @apiName    GetCustomer
	 * @apiGroup   Customers
	 *
	 * @apiDescription
	 * Get multiple or a single customer record through the GET method. This resource supports
	 * the following GET parameters as described in the first section of documentation: sorting
	 * minimization, search, pagination and links. Additionally you can filter customers by providing
	 * the GET parameter "type=guest" or "type=registree". Sort and pagination GET parameters do not
	 * apply when a single customer record is selected (e.g. api.php/v2/customers/84).
	 * An example script to demonstrate how to fetch customer data is located under
	 * `./docs/REST/samples/customer-service/get_admin_data.php` in the git clone
	 *
	 * **Important**:
	 * Currently the CustomerReadService does not support searching in address information of
	 * a customer.
	 *
	 * @apiExample {curl} Get All Customers
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers
	 *
	 * @apiExample {curl} Get Customer With ID = 982
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers/982
	 *
	 * @apiExample {curl} Get Guest Customers
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers?type=guest
	 *
	 * @apiExample {curl} Search Customers
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers?q=admin@shop.de
	 *
	 * @apiExample {curl} Get Customer Addresses
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/customers/57/addresses
	 * 
	 * @apiError 404-NotFound Customer record could not be found. 
	 * @apiError 400-BadRequest Invalid customer type filter provided (expected 'registree' or 'guest'). 
	 *           
	 * @apiErrorExample Error-Response (Customer Not Found)
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "Customer record could not be found."
	 * }          
	 *                  
	 * @apiErrorExample Error-Response (Invalid Type Filter)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Invalid customer type filter provided, expected 'guest' or 'registree' and got: admin"
	 * }                 
	 */
	public function get()
	{
		// Sub-Resource Customer addresses: api.php/v2/customers/:id/addresses
		if(isset($this->uri[2]) && $this->uri[2] === 'addresses')
		{
			$this->_getCustomerAddresses();

			return;
		}

		// Get Single Customer Record
		if(isset($this->uri[1]) && is_numeric($this->uri[1]))
		{
			$customers = $this->customerReadService->filterCustomers(array('customers_id' => (int)$this->uri[1]));

			if(empty($customers))
			{
				throw new HttpApiV2Exception('Customer record could not be found.', 404);
			}
		}
		// Search Customer Records
		else if($this->api->request->get('q') !== null)
		{
			$searchKey = '%' . $this->api->request->get('q') . '%';
			$search    = array(
				'customers_cid LIKE '           => $searchKey,
				'customers_vat_id LIKE '        => $searchKey,
				'customers_gender LIKE '        => $searchKey,
				'customers_firstname LIKE '     => $searchKey,
				'customers_lastname LIKE '      => $searchKey,
				'customers_dob LIKE '           => $searchKey,
				'customers_email_address LIKE ' => $searchKey,
				'customers_telephone LIKE '     => $searchKey,
				'customers_fax LIKE '           => $searchKey
			);

			$customers = $this->customerReadService->filterCustomers($search);
		}
		// Filter customers by type ("guest" or "registree")
		else if($this->api->request->get('type') !== null)
		{
			$type = $this->api->request->get('type');

			if($type === 'guest')
			{
				$customers = $this->customerReadService->filterCustomers(array('account_type' => '1'));
			}
			else if($type === 'registree')
			{
				$customers = $this->customerReadService->filterCustomers(array('account_type' => '0'));
			}
			else
			{
				throw new HttpApiV2Exception('Invalid customer type filter provided, expected "guest" or "registree" and got: '
				                             . $type, 400);
			}
		}
		// Get all registered customer records without applying filters.
		else
		{
			$customers = $this->customerReadService->filterCustomers();
		}

		// Prepare response data. 
		$response = array();
		foreach($customers as $customer)
		{
			$response[] = $this->customerJsonSerializer->serialize($customer, false);
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


	/**
	 * Sub-Resource Customer Addresses
	 *
	 * This method will return all the addresses of the required customer, providing a fast
	 * way to access relations between customers and addresses.
	 *
	 * @see CustomersApiV2Controller::get()
	 *
	 * @throws HttpApiV2Exception
	 */
	protected function _getCustomerAddresses()
	{
		if(!isset($this->uri[1]) && is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Invalid customer ID provided: ' . gettype($this->uri[1]), 400);
		}

		$customer  = $this->customerReadService->getCustomerById(new IdType((int)$this->uri[1]));
		$addresses = $this->addressService->getCustomerAddresses($customer);

		$response = array();
		foreach($addresses as $address)
		{
			$response[] = $this->addressJsonSerializer->serialize($address, false);
		}

		$this->_sortResponse($response);
		$this->_paginateResponse($response);
		$this->_minimizeResponse($response);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}
}