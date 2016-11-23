<?php
/* --------------------------------------------------------------
   GxmlCustomers.inc.phtml 2016-04-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GxmlCustomers
 *
 * Handles the API operations that concern the customers domain. This class
 * uses the customer service.
 *
 * Supported API Functions:
 *       - "upload_customers"
 *       - "download_customers"
 *
 * @category System
 * @package  GambioAPI
 * @version  1.0
 */
class GxmlCustomers extends GxmlMaster
{
	/**
	 * @var CustomerService
	 */
	protected $customerService;

	/**
	 * @var AddressBookService
	 */
	protected $addressService;


	/**
	 * Class Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->customerService = StaticGXCoreLoader::getService('Customer');
		$this->addressService  = StaticGXCoreLoader::getService('AddressBook');
	}


	/**
	 * Upload Customers
	 *
	 * @param SimpleXMLElement $request Contains the request data.
	 *
	 * @return SimpleXMLElement Returns the response XML object.
	 */
	public function uploadCustomers(SimpleXMLElement $request)
	{
		try
		{
			$responseData = array();
			$overallSuccess = true;
			
			foreach($request->parameters->customers->customer as $node)
			{
				// Check if record needs to be deleted. 
				if($node['action'] == 'delete')
				{   
					try
					{
						$actionSuccess = true; 
						$errorMessage = ''; 
						$this->_deleteObject($node);
					}
					catch(Exception $ex)
					{
						$actionSuccess = false;
						$overallSuccess = false;
						$errorMessage = $ex->getMessage(); 
					}
					
					$responseData[] = array(
						'external_customer_id' => '',
						'customer_id' => (string)$node->customer_id,
					    'success' => (int)$actionSuccess,
						'errormessage' => $errorMessage,
					    'action_performed' => 'delete'
					);
					continue;
				}

				// Prepare customer object and write it to the database.  
				//try 
				//{
					$actionSuccess = true;
					$errorMessage = '';
					$actionPerformed = (!isset($node->customer_id)) ? 'create' : 'update';
					
					$customer = $this->_createCustomerByNode($node);
					$this->customerService->updateCustomer($customer); // insert or update
					$customer->getDefaultAddress()->setCustomerId(new IdType($customer->getId()));
					$this->addressService->updateCustomerAddress($customer->getDefaultAddress()); // insert or update
				//}
				//catch(Exception $ex)
				//{
				//	$errorMessage = $ex->getMessage();
				//	$actionSuccess = false; 
				//	$overallSuccess = false;
				//}
				
				// Store the record IDs for later use. 
				$responseData[] = array(
						'external_customer_id' => (string)$node->external_customer_id,
						'customer_id' => ($customer !== null) ? (string)$customer->getId() : '',
						'success' => (int)$actionSuccess,
						'errormessage' => $errorMessage,
				        'action_performed' => $actionPerformed
				);
			}

			// Prepare Response XML
			$response = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');
			$response->addChild('request');
			$response->request->addChild('success', (int)$overallSuccess);

			if(count($responseData > 0))
			{
				$response->addChild('customers');
				foreach($responseData as $item)
				{
					$response->customers->addChild('customer');
					$response->customers->customer->addChild('external_customer_id');
					$response->customers->customer->addChild('customer_id', $item['customer_id']);
					$response->customers->customer->addChild('success', $item['success']);
					$response->customers->customer->addChild('errormessage', $item['errormessage']);
					$response->customers->customer->addChild('action_performed', $item['action_performed']);
				}
			}

			return $response;
		} catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}


	/**
	 * Download Customers
	 *
	 * @param SimpleXMLElement $request Contains the request data.
	 *
	 * @return SimpleXMLElement Returns the response XML object.
	 */
	public function downloadCustomers(SimpleXMLElement $request)
	{
		try
		{
			// Conditions 
			$conditions = array();
			if(isset($request->parameters->customers))
			{
				foreach($request->parameters->customers->children() as $node)
				{
					$conditions[$node->children()->getName()] = (string)$node->children();
				}
			}

			// Limit
			$limit = null;
			if(isset($request->parameters->limit))
			{
				$limit = (int)$request->parameters->limit;
			}

			// Offset
			$offset = null;
			if(isset($request->parameters->offset))
			{
				$offset = (int)$request->parameters->offset;
			}

			// Prepare response XML
			$customers = $this->customerService->filterCustomers($conditions, $limit, $offset);
			$response = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');
			$response->addChild('request');
			$response->request->addChild('success', '1');
			$customersNode = $response->addChild('customers');

			foreach($customers as $customer)
			{
				$this->_createNodeByCustomer($customersNode, $customer);
			}

			return $response;
		} catch(Exception $ex)
		{
			return $this->handleApiException($ex);
		}
	}


	/**
	 * Add node to the response XML object.
	 *
	 * @param array            $data        Contains the data to be included within the XML response object.
	 * @param string           $tableColumn Table column will point the value that we need from the $data.
	 * @param string           $nodeName    This is the name of the node to be inserted.
	 * @param SimpleXMLElement $xml         The xml object that will be edited.
	 */
	protected function _addNode(array $data, $tableColumn, $nodeName, SimpleXMLElement $xml)
	{
		// Not used in this class.     
	}


	/**
	 * Abstract Method Implementation: Delete Object
	 *
	 * This is an implementation of the abstract method declared in the GxmlMaster class. It
	 * removes an object from the database.
	 *
	 * @param SimpleXMLElement $node Contains the information of the object to be deleted.
	 */
	protected function _deleteObject(SimpleXMLElement $node)
	{
		$customer = $this->customerService->getCustomerById(new IdType((int)$node->customer_id));
		$this->customerService->deleteCustomerById(new IdType($customer->getId()));
		$this->addressService->deleteAddress($customer->getDefaultAddress());
	}


	/**
	 * Create Customer from XML Node
	 *
	 * Provided XML node must contain all the documented attributes of  a customer so that
	 * a Customer object can be created and returned.
	 *
	 * @param SimpleXMLElement $node Contains customer information.
	 *
	 * @return Customer Returns a Customer class instance.
	 */
	protected function _createCustomerByNode(SimpleXMLElement $node)
	{
		$customer = MainFactory::create('Customer');

		$customer->setCustomerNumber(MainFactory::create('CustomerNumber', (string)$node->customer_cid));
		$customer->setVatNumber(MainFactory::create('CustomerVatNumber', (string)$node->vat_id));
		$customer->setVatNumberStatus((int)$node->vat_id_status);
		$customer->setGender(MainFactory::create('CustomerGender', (string)$node->gender));
		$customer->setFirstname(MainFactory::create('CustomerFirstname', (string)$node->firstname));
		$customer->setLastname(MainFactory::create('CustomerLastname', (string)$node->lastname));
		$customer->setDateOfBirth(MainFactory::create('CustomerDateOfBirth', (string)$node->date_of_birth));
		$customer->setEmail(MainFactory::create('CustomerEmail', (string)$node->email));
		$customer->setTelephoneNumber(MainFactory::create('CustomerCallNumber', (string)$node->telephone));
		$customer->setFaxNumber(MainFactory::create('CustomerCallNumber', (string)$node->fax));
		$customer->setGuest(false);
		$customer->setPassword(MainFactory::create('CustomerPassword', (string)$node->password));

		$address = $this->_createAddressByNode($node->default_address);

		if(isset($node->customer_id))
		{
			$customer->setId(new IdType((int)$node->customer_id));
			$address->setCustomerId(new IdType((int)$node->customer_id));
		}

		$customer->setDefaultAddress($address);

		return $customer;
	}


	/**
	 * Create XML Node by Customer Object
	 *
	 * @param SimpleXMLElement  $customersNode Reference to the parent node that will appended with the current
	 *                                         customer.
	 * @param CustomerInterface $customer      Contains the customer information.
	 *
	 * @return SimpleXMLElement Returns a new node that represents the customer object.
	 */
	protected function _createNodeByCustomer(SimpleXMLElement $customersNode, CustomerInterface $customer)
	{

		$node = $customersNode->addChild('customer');
		$node->addChild('customer_id', $this->_prepareXmlStr($customer->getId()));
		$node->addChild('customer_cid', $this->_prepareXmlStr($customer->getCustomerNumber()));
		$node->addChild('firstname', $this->_prepareXmlStr($customer->getFirstname()));
		$node->addChild('lastname', $this->_prepareXmlStr($customer->getLastname()));
		$node->addChild('email', $this->_prepareXmlStr($customer->getEmail()));
		$node->addChild('telephone', $this->_prepareXmlStr($customer->getTelephoneNumber()));
		$node->addChild('fax', $this->_prepareXmlStr($customer->getFaxNumber()));
		$node->addChild('password', $this->_prepareXmlStr($customer->getPassword()));
		$node->addChild('date_of_birth', $this->_prepareXmlStr($customer->getDateOfBirth()->format('Y-m-d H:i:s')));
		$node->addChild('vat_id', $this->_prepareXmlStr($customer->getVatNumber()));
		$node->addChild('vat_id_status', $this->_prepareXmlStr($customer->getVatNumberStatus()));
		$node->addChild('status', $this->_prepareXmlStr($customer->getStatusId()));
		$node->addChild('gender', $this->_prepareXmlStr($customer->getGender()));
		$this->_createNodeByAddress($node, $customer->getDefaultAddress());
	}


	/**
	 * Create address from xml node.
	 *
	 * Provided XML node must contain all the documented attributes of an address so that
	 * a new object can be created and returned.
	 *
	 * @param SimpleXMLElement $node Contains the address data.
	 *
	 * @return Address Returns an Address class instance.
	 */
	protected function _createAddressByNode(SimpleXMLElement $node)
	{
		$address = MainFactory::create('CustomerAddress');

		if(isset($node->address_id))
		{
			$address->setId(new IdType((int)$node->address_id));
		}

		$address->setCompany(MainFactory::create('CustomerCompany', (string)$node->company));
		$address->setCity(MainFactory::create('CustomerCity', (string)$node->city));
		$address->setFirstname(MainFactory::create('CustomerFirstname', (string)$node->firstname));
		$address->setLastname(MainFactory::create('CustomerLastname', (string)$node->lastname));
		$address->setGender(MainFactory::create('CustomerGender', (string)$node->gender));
		$address->setPostcode(MainFactory::create('CustomerPostcode', (string)$node->postcode));
		$address->setStreet(MainFactory::create('CustomerStreet', (string)$node->street));
		$address->setHouseNumber(MainFactory::create('CustomerHouseNumber', (string)$node->house_number));
		$address->setAdditionalAddressInfo(MainFactory::create('CustomerAdditionalAddressInfo', 
		                                                       (string)$node->additional_address_info));
		$address->setSuburb(MainFactory::create('CustomerSuburb', (string)$node->suburb));
		$address->setAddressClass(MainFactory::create('AddressClass', (string)$node->class));

		// Fetch country through CountryService
		$countryService = StaticGXCoreLoader::getService('Country');
		$country        = $countryService->getCountryById(new IdType((int)$node->country_id));
		$address->setCountry($country);

		// Fetch zone through CountryService
		$zone = $countryService->getCountryZoneById(new IdType((int)$node->zone_id));
		$address->setCountryZone($zone);

		return $address;
	}


	/**
	 * Create XML Node by Address Object
	 *
	 * @param SimpleXMLElement         $customerNode The customer node address information will be added to.
	 * @param CustomerAddressInterface $address      Contains the address information.
	 *
	 * @return SimpleXMLElement Returns a new node that represents the address object.
	 */
	protected function _createNodeByAddress(SimpleXMLElement $customerNode, CustomerAddressInterface $address)
	{
		$node = $customerNode->addChild('default_address');
		$node->addChild('address_id', $this->_prepareXmlStr($address->getId()));
		$node->addChild('firstname', $this->_prepareXmlStr($address->getFirstname()));
		$node->addChild('lastname', $this->_prepareXmlStr($address->getLastname()));
		$node->addChild('company', $this->_prepareXmlStr($address->getCompany()));
		$node->addChild('street', $this->_prepareXmlStr($address->getStreet()));
		if(ACCOUNT_SPLIT_STREET_INFORMATION === 'true')
		{
			$node->addChild('street_address', $this->_prepareXmlStr($address->getStreet()));
			$node->addChild('street_address_number', $this->_prepareXmlStr($address->getHouseNumber()));
		}
		$node->addChild('additional_address_info', $this->_prepareXmlStr($address->getAdditionalAddressInfo()));
		$node->addChild('suburb', $this->_prepareXmlStr($address->getSuburb()));
		$node->addChild('city', $this->_prepareXmlStr($address->getCity()));
		$node->addChild('postcode', $this->_prepareXmlStr($address->getPostcode()));
		$node->addChild('country_id', $this->_prepareXmlStr($address->getCountry()->getId()));
		$node->addChild('zone_id', $this->_prepareXmlStr($address->getCountryZone()->getId()));
		$node->addChild('class', $this->_prepareXmlStr($address->getAddressClass()));
	}


	/**
	 * Prepares a value for the XML.
	 *
	 * @param mixed $value Value object of the customer domain.
	 *
	 * @return string Returns an html escaped string representation of the value object.
	 */
	protected function _prepareXmlStr($value)
	{
		return htmlspecialchars((string)$value);
	}
}