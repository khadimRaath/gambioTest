<?php
/* --------------------------------------------------------------
   XMLConnectAjaxHandler.inc.php 2015-02-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Custom error handler function used by the API. 
 * 
 * @param int $p_errno
 * @param string $p_errString
 * @param string $p_errFile
 * @param int $p_errLine
 * @param array $errContext
 *
 * @return bool If error_reporing setting is disabled the return value is false. 
 * @throws ErrorException Throws exception for returnign the error details to client (see 
 * XMLConnectAjaxHandler below).  
 */
function custom_error_handler($p_errno, $p_errString, $p_errFile, $p_errLine, array $errContext)
{
	if (error_reporting() === 0)
	{
		return false;
	}

	throw new ErrorException($p_errString, 0, $p_errno, $p_errFile, $p_errLine);
}

/**
 * Class XMLConnectAjaxHandler
 * 
 * This class is handling the routing of the Gambio XML API. It will load 
 * the corresponding class and execute the required function depending the 
 * request XML data sent by the client.
 *
 * @category System
 * @package GambioAPI 
 * @version 1.0
 */
class XMLConnectAjaxHandler extends AjaxHandler
{
	/**
	 * @var GxmlHelper
	 */
	private $gxmlHelper; // used in various class methods


	/**
	 * Class Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		$this->gxmlHelper = MainFactory::create_object('GxmlHelper');
	}


	/**
	 * Abstract Method Implementation. 
	 * 
	 * This method will not do anything. 
	 */
	public function get_permission_status($p_customers_id = NULL)
	{
		return true;
	}


	/**
	 * Proceed with the client request. 
	 * 
	 * This method will route the necessary modules and make the required operation 
	 * in order to provide a valid response to the client. 
	 * 
	 * @return bool Returns the operation result. 
	 */
	public function proceed()
	{		
		try
		{
			set_error_handler('custom_error_handler');
			$requestXmlString = gm_prepare_string($_POST['gambio_api_xml'], true);

			// Debug Logging
			file_put_contents(DIR_FS_CATALOG . 'logfiles/xml.log', str_repeat('-', 100)
							   . "\nDatum: " . date('Y-m-d H:i:s') . "\nIP: " . $_SERVER['REMOTE_ADDR']
							   . "\n\nGET-" . print_r($_GET, true) . "\nPOST-" . print_r($_POST, true)
							   . "\ngambio_api_xml:\n" . $requestXmlString . "\n", FILE_APPEND);

			$requestXml = simplexml_load_string($requestXmlString);
			$function = (string)$requestXml->general->function;
			$isSessionKeyValid = true; 
			
			if($function !== 'login' && $function !== 'logout')
			{
				$gxmlAuth = MainFactory::create_object('GxmlAuth');
				$isSessionKeyValid = $gxmlAuth->validateSessionKey($requestXml->general->session_key);
			}
			
			if($isSessionKeyValid === true)
			{
				$responseXml = $this->_getResponseXml($requestXml); 	
			}
			else
			{
				$responseXml = $gxmlAuth->getInvalidSessionKeyResponse(); 
			}
		}
		catch (ErrorException $exception)
		{
			// Prepare xml response containing error information. 
			$responseXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');  
			$responseXml->addChild('request');
			$responseXml->request->addChild('success', 0);
			
			$errorMessage = $exception;
			
			if(strpos($exception, 'simplexml_load_string') !== false && strpos($exception, 'Opening and ending tag mismatch') !== false)
			{
				$errorMessage = 'Request invalid. Opening and ending tag mismatch.';
			}
			else if(strpos($exception, 'simplexml_load_string') !== false && strpos($exception, 'xmlParseEntityRef') !== false)
			{
				$errorMessage = 'Request invalid. Use of illegal character.';
			}
			else if(strpos($exception, 'simplexml_load_string') !== false && strpos($exception, 'EntityRef: expecting \';\'') !== false)
			{
				$errorMessage = 'Request invalid. HTML entity fragment without delimiter \';\' found (e.g. &amp).';
			}
			
			// Wrap message with CDATA so that it won't be 
			$errorMessage = '<![CDATA[' . $errorMessage . ']]>';
			
			$responseXml->request->addChild('errormessage', $errorMessage);
		}
		restore_error_handler();
		
		// If there is no response by this point return an "Unknown error" message to the 
		// client and log the result.
		if($responseXml === false) 
		{
			file_put_contents(DIR_FS_CATALOG . 'logfiles/xml_error.log', str_repeat('-', 100) . "\nDatum: " . date('Y-m-d H:i:s') . "\nIP: " . $_SERVER['REMOTE_ADDR'] . "\n\nGET-" . print_r($_GET, true) . "\nPOST-" . print_r($_POST, true) . "\ngambio_api_xml:\n" . $requestXmlString . "\n", FILE_APPEND);
			
			$responseXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');
			$responseXml->addChild('request');
			$responseXml->request->addChild('success', 0);
			
			$errorMessage = 'An unknown error occurred for function \'' . $function . '\': ' . $requestXmlString;
			
			$errorMessage = '<![CDATA[' . $errorMessage . ']]>';
			
			$responseXml->request->addChild('errormessage', $errorMessage);
		}
		
		// Output Response XML
		$this->add_header('Content-Type: text/xml;');
		$this->v_output_buffer = $responseXml->asXML();
		
		return true;
	}

	
	/**
	 * Invoke API Function
	 * 
	 * This method invokes the required object method that will respond to the client.
	 *
	 * @param SimpleXMLElement $requestXml Contains the request information. 
	 *
	 * @return SimpleXMLElement Returns the response XML object. 
	 */
	protected function _getResponseXml(SimpleXMLElement $requestXml)
	{
		switch((string)$requestXml->general->function)
		{
			case 'login':
				$gxml = MainFactory::create_object('GxmlAuth');
				$responseXml = $gxml->login($requestXml);
				break;

			case 'logout':
				$gxml = MainFactory::create_object('GxmlAuth');
				$responseXml = $gxml->logout($requestXml);
				break;

			// ----------------------------------------------------------------
			// DOWNLOAD METHODS 
			// ----------------------------------------------------------------
			
			case 'download_categories':
				$gxml = MainFactory::create_object('GxmlCategories');
				$responseXml = $gxml->downloadCategories($requestXml);
				break;

			case 'download_products':
				$gxml = MainFactory::create_object('GxmlProducts');
				$responseXml = $gxml->downloadProducts($requestXml);
				break;

			case 'download_orders':
				$gxml = MainFactory::create_object('GxmlOrders');
				$responseXml = $gxml->downloadOrders($requestXml);
				break;

			case 'download_tax_classes':
				$gxml = MainFactory::create_object('GxmlTaxClasses');
				$responseXml = $gxml->downloadTaxClasses($requestXml);
				break;

			case 'download_languages':
				$gxml = MainFactory::create_object('GxmlLanguages');
				$responseXml = $gxml->downloadLanguages($requestXml);
				break;

			case 'download_order_status':
				$gxml = MainFactory::create_object('GxmlOrderStatus');
				$responseXml = $gxml->downloadOrderStatus($requestXml);
				break;

			case 'download_properties':
				$gxml = MainFactory::create_object('GxmlProperties');
				$responseXml = $gxml->downloadProperties($requestXml);
				break;

			case 'download_customers':
				$gxml = MainFactory::create_object('GxmlCustomers');
				$responseXml = $gxml->downloadCustomers($requestXml);
				break;
			
			// ----------------------------------------------------------------
			// UPLOAD METHODS 
			// ----------------------------------------------------------------

			case 'upload_order_status':
				$gxml = MainFactory::create_object('GxmlOrderStatus');
				$responseXml = $gxml->uploadOrderStatus($requestXml);
				break;

			case 'upload_categories':
				$gxml = MainFactory::create_object('GxmlCategories');
				$responseXml = $gxml->uploadCategories($requestXml);
				break;

			case 'upload_products':
				$gxml = MainFactory::create_object('GxmlProducts');
				$responseXml = $gxml->uploadProducts($requestXml);
				break;

			case 'upload_properties':
				$gxml = MainFactory::create_object('GxmlProperties');
				$responseXml = $gxml->uploadProperties($requestXml);
				break;

			case 'upload_customers':
				$gxml = MainFactory::create_object('GxmlCustomers');
				$responseXml = $gxml->uploadCustomers($requestXml);
				break;
		}
		
		return $responseXml; 
	}
}