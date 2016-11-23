<?php
/* --------------------------------------------------------------
   AddressesApiV2Controller.inc.php 2016-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class AddressesApiV2Controller
 *
 * Provides a gateway to the AddressBookService which handles the shop address resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class AddressesApiV2Controller extends HttpApiV2Controller
{
	/**
	 * @var AddressBookServiceInterface
	 */
	protected $addressService;

	/**
	 * @var AddressJsonSerializer
	 */
	protected $addressSerializer;


	/**
	 * Initializes API Controller
	 */
	protected function __initialize()
	{
		$this->addressService    = StaticGXCoreLoader::getService('AddressBook');
		$this->addressSerializer = MainFactory::create('AddressJsonSerializer');
	}


	/**
	 * @api        {post} /addresses Create Address
	 * @apiVersion 2.2.0
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
	 *   "street": "Test Street",
	 *   "houseNumber": "123",
	 *   "additionalAddressInfo": "1. Etage",
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
	 * @apiParam {string} houseNumber The address house number.
	 * @apiParam {string} additionalAddressInfo Additional information about the address.
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
	public function post()
	{
		// Validate Request Data 
		$addressJsonString = $this->api->request->getBody();

		if(empty($addressJsonString))
		{
			throw new HttpApiV2Exception('Address data were not provided.', 400);
		}

		// Make sure that the address ID is not included in the request body (post method is used for new records).
		$tmpEncodedAddress = json_decode($addressJsonString);
		unset($tmpEncodedAddress->id);
		$addressJsonString = json_encode($tmpEncodedAddress);

		// Store the address to the database. 
		$address = $this->addressSerializer->deserialize($addressJsonString);
		$this->addressService->updateCustomerAddress($address);

		// Prepare client response with links.
		$response = $this->addressSerializer->serialize($address, false);
		$this->_linkResponse($response);
		$this->_locateResource('addresses', (string)$address->getId());
		$this->_writeResponse($response, 201);
	}


	/**
	 * @api        {put} /addresses/:id Update Address
	 * @apiVersion 2.1.0
	 * @apiName    UpdateAddress
	 * @apiGroup   Addresses
	 *
	 * @apiDescription
	 * Update an existing address record by providing new data. You do not have to provide the full
	 * presentation of the address in the JSON string of the request, rather just the fields to be
	 * updated. The address ID will be taken from the URI of the request so it is not required that
	 * it is included withing the request JSON.
	 *
	 * @apiExample {json} Request-Body
	 * {
	 *   "company": "Test Company - UPDATED",
	 *   "firstname": "John - UPDATED",
	 *   "lastname": "Doe - UPDATED",
	 *   "street": "Test Street - UPDATED",
	 *   "houseNumber": "1 - UPDATED",
	 *   "additionalAddressInfo": "1. Etage - UPDATED",
	 *   "suburb": "Test Suburb - UPDATED",
	 *   "city": "Test City - UPDATED"
	 * }
	 *
	 * @apiSuccess Response-Body If successful, this method returns the updated address resource in the response body.
	 */
	public function put()
	{
		// Validate Request Data 
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Address record id was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		$addressJsonString = $this->api->request->getBody();

		if(empty($addressJsonString))
		{
			throw new HttpApiV2Exception('Address data were not provided.', 400);
		}

		// Update existing address record. 
		$addressId  = new IdType((int)$this->uri[1]);
		$baseObject = $this->addressService->findAddressById($addressId);
		
		// Ensure that the address has the correct address id of the request url
		$addressJsonString = $this->_setJsonValue($addressJsonString, 'id', $addressId->asInt());
		
		$address    = $this->addressSerializer->deserialize($addressJsonString, $baseObject);
		$address->setId($addressId); // ensure that the address has the correct ID
		$this->addressService->updateCustomerAddress($address);

		// Prepare client response with links.
		$response = $this->addressSerializer->serialize($address, false);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}


	/**
	 * @api        {delete} /addresses/:id Delete Address
	 * @apiVersion 2.1.0
	 * @apiName    DeleteAddress
	 * @apiGroup   Addresses
	 *
	 * @apiDescription
	 * Remove an address record from the system. This method will always return success even if the address record
	 * does not exist (due to internal architecture decisions, which strive to avoid unnecessary failures).
	 *
	 * @apiExample {curl} Delete Address with ID = 811
	 *             curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/addresses/811
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "addressId": 811
	 * }
	 * 
	 * @apiError 400-BadRequest Address record ID was not provided in the resource URL.
	 *
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Address record ID was not provided in the resource URL."
	 * }
	 */
	public function delete()
	{
		// Validate Request Data 
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Address record id was not provided or is invalid: ' . gettype($this->uri[1]),
			                             400);
		}

		$addressId = new IdType((int)$this->uri[1]);
		$address   = $this->addressService->findAddressById($addressId);

		if($address !== null)
		{
			$this->addressService->deleteAddress($address);
		}

		$response = array(
			'code'      => 200,
			'status'    => 'success',
			'action'    => 'delete',
			'addressId' => (int)$this->uri[1]
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api        {get} /addresses/:id Get Address
	 * @apiVersion 2.1.0
	 * @apiName    GetAddress
	 * @apiGroup   Addresses
	 *
	 * @apiDescription
	 * Get multiple or a single address records through a GET requets. This method supports all the GET parameters
	 * that are mentioned in the "Introduction" section of this documentation.
	 *
	 * @apiExample {curl} Delete Address with ID = 243
	 *             curl --user admin@shop.de:12345 http://shop.de/api.php/v2/addresses/243
	 *
	 * @apiSuccess Response-Body If successful, this method will return the address resource in JSON format.
	 */
	public function get()
	{
		if(isset($this->uri[1]) && is_numeric($this->uri[1])) // single record
		{
			$address = $this->addressService->findAddressById(new IdType((int)$this->uri[1]));

			if($address === null)
			{
				throw new HttpApiV2Exception('Record could not be found.', 404);
			}

			$response = $this->addressSerializer->serialize($address, false);
		}
		else // multiple records
		{
			$addresses = ($this->api->request->get('q')) ? $this->addressService->filterAddresses($this->api->request->get('q')) : $this->addressService->getAllAddresses();
			$response  = array();
			foreach($addresses as $address)
			{
				$response[] = $this->addressSerializer->serialize($address, false);
			}

			$this->_sortResponse($response);
			$this->_paginateResponse($response);
		}

		$this->_minimizeResponse($response);
		$this->_linkResponse($response);
		$this->_writeResponse($response);
	}
}