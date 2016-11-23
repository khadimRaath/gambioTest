<?php
/* --------------------------------------------------------------
	PayPalRestResponse.inc.php 2015-05-07
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This class takes care of parsing a response from PayPal REST API, i.e. it decodes the response if the content type is 'application/json'.
 */
class PayPalRestResponse extends RestCurlResponse
{
	/**
	 * @var bool|stdClass $response_object decoded JSON response
	 */
	protected $response_object = false;

	/**
	 * contructor; initializes the PayPalRestResponse from a RestCurlResponse
	 * @param RestCurlResponse $curlresponse raw response from cURL backend
	 */
	public function __construct(RestCurlResponse $curlresponse)
	{
		$this->setResponseCode($curlresponse->getResponseCode());
		$this->setResponseBody($curlresponse->getResponseBody());
		$this->setCurlInfo($curlresponse->getCurlInfo());
		$this->parseResponse();
	}

	/**
	 * returns decoded response body
	 * @return stdClass response data
	 */
	public function getResponseObject()
	{
		return $this->response_object;
	}

	/**
	 * decides how to parse the raw response body based on the MIME type.
	 * Currently only 'application/json' is supported.
	 * @throws Exception if the response indicates an unsupported type
	 */
	protected function parseResponse()
	{
		$curlInfo = $this->getCurlInfo();
		switch($curlInfo['content_type'])
		{
			case 'application/json':
				$this->response_object = $this->parseJSONResponse();
				break;
			default:
				if($curlInfo['size_download'] > 0)
				{
					throw new Exception('unsupported response type '.$curlInfo['content_type']);
				}
		}
	}

	/**
	 * parses a JSON response into its stdClass representation
	 * @return bool|stdClass response data or false if parsing fails
	 */
	protected function parseJSONResponse()
	{
		$response_object = json_decode($this->getResponseBody());
		if(json_last_error() === JSON_ERROR_NONE)
		{
			return $response_object;
		}
		return false;
	}

}