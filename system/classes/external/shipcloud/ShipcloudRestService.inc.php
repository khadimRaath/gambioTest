<?php
/* --------------------------------------------------------------
	ShipcloudRestService.inc.php 2016-01-05
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/


class ShipcloudRestService extends RestService
{
	/**
	 * @var double transaction timeout
	 */
	protected $timeout = 0;

	/**
	 * @var ShipcloudLogger logging facility
	 */
	protected $logger;

	/**
	 * @var RestRequest last request (stored for logging/debugging)
	 */
	protected $lastRequest;

	/**
	 * @var ShipcloudRestResponse last response
	 */
	protected $lastResponse;

	/**
	 * initializes the service with a default timeout of 10 seconds
	 */
	public function __construct()
	{
		$this->timeout = 20;
		$this->logger  = MainFactory::create('ShipcloudLogger');
	}

	/**
	 * performs a request.
	 * Request and response are logged if extended logging is active.
	 * @param RestRequest $request
	 * @return ShipcloudRestResponse
	 */
	public function performRequest(RestRequest $request)
	{
		if(!$request instanceof ShipcloudRestRequest)
		{
			throw new Exception('Request object must be an instance of ShipcloudRestRequest.');
		}
		$this->logger->debug_notice("API request:\n".$request);
		$this->lastRequest = $request;
		try
		{
			$restCurlResponse      = parent::performRequest($request);
			$this->logger->debug_notice("API response:\n".$restCurlResponse);
			$shipcloudRestResponse = MainFactory::create_object('ShipcloudRestResponse', array($restCurlResponse));
			$this->logger->debug_notice("API response decoded:\n".print_r($shipcloudRestResponse->getResponseObject(), true));
			$this->lastResponse    = $shipcloudRestResponse;
			return $shipcloudRestResponse;
		}
		catch(Exception $e)
		{
			$this->logger->debug_notice('ERROR performing request: '.$e->getMessage());
			throw $e;
		}
	}

	/**
	 * returns the last request
	 * @return RestRequest last request tried by performRequest()
	 */
	public function getLastRequest()
	{
		return $this->lastRequest;
	}

	/**
	 * returns last response
	 * @return ShipcloudRestResponse
	 */
	public function getLastResponse()
	{
		return $this->lastResponse;
	}

}
