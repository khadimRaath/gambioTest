<?php
/* --------------------------------------------------------------
	RestService.inc.php 2015-08-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
* Class RestService
*
* This is the work-horse class of the REST client framework. It implements the actual request execution.
*/
class RestService
{
	/**
	* @var double
	*/
	protected $timeout = 0;

	/**
	* @var bool
	*/
	protected $includeHeaders = true;

	/**
	* empty constructor
	*/
	public function __construct()
	{

	}

	/**
	* sets timeout in seconds
	* @param double $timeout timout in seconds; fractional part only used if supported, cf. http://php.net/manual/en/function.curl-setopt.php
	*/
	public function setTimeout($timeout)
	{
		$this->timeout = (double)$timeout;
	}

	/**
	* performs a REST request as specified by the RestRequest parameter
	*
	* @param RestRequest $request
	* @throws RestTimeoutException if transaction takes longer than specified by timeout value
	* @throws RestException if the backend indicates an error
	* @return RestCurlResponse full response (body, cURL metadata)
	*/
	public function performRequest(RestRequest $request)
	{
		$ch = curl_init();
		$opts = array(
				CURLOPT_URL => $request->getURL(),
				CURLOPT_RETURNTRANSFER => true,
				CURLINFO_HEADER_OUT => true,
				CURLOPT_HEADER => $this->includeHeaders,
			);

		if($request->getMethod() === RestRequest::METHOD_POST)
		{
			$opts[CURLOPT_POST] = true;
			$opts[CURLOPT_POSTFIELDS] = $request->getData();
		}
		elseif($request->getMethod() !== RestRequest::METHOD_GET)
		{
			$opts[CURLOPT_CUSTOMREQUEST] = $request->getMethod();
			$data = $request->getData();
			if(!empty($data))
			{
				$opts[CURLOPT_POSTFIELDS] = $data;
			}
		}

		$headers = $request->getHeaders();
		if(!empty($headers) && is_array($headers))
		{
			$opts[CURLOPT_HTTPHEADER] = $headers;
		}

		$userpass = $request->getUserpass();
		if(!empty($userpass))
		{
			$opts[CURLOPT_USERPWD] = $userpass;
		}

		if((double)$this->timeout > 0)
		{
			if(defined('CURLOPT_TIMEOUT_MS'))
			{
				$opts[CURLOPT_TIMEOUT_MS] = (int)((double)$this->timeout * 1000);
			}
			else
			{
				$opts[CURLOPT_TIMEOUT] = (int)$this->timeout;
			}
		}
		curl_setopt_array($ch, $opts);
		$body = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		if($errno > 0)
		{
			switch($errno)
			{
				case CURLE_OPERATION_TIMEOUTED:
					throw new RestTimeoutException(sprintf('%d - %s', $errno, $error));
					break;
				default:
					throw new RestException(sprintf('%d - %s', $errno, $error));
			}
		}
		$response = new RestCurlResponse($info['http_code'], $body, $this->includeHeaders);
		$response->setCurlInfo($info);
		return $response;
	}
}

