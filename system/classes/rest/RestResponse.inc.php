<?php
/* --------------------------------------------------------------
	RestResponse.inc.php 2015-08-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Basic value object class for REST responses
 */
class RestResponse
{
	/**
	 * @var int HTTP response code (e.g. 200, 403, ...)
	 */
	protected $response_code;

	/**
	 * @var string body of the response (binary string; can be anything)
	 */
	protected $response_body;

	/**
	 * @var string headers of the response (if supplied by RestService implementation)
	 */
	protected $response_headers;

	/**
	 * quick constructor
	 * @param int $code HTTP response code
	 * @param string $body response body
	 * @param bool $headersIncluded true if $body is prefixed by HTTP headers
	 */
	public function __construct($code, $body, $headersIncluded = false)
	{
		$this->setResponseCode($code);
		if($headersIncluded === true)
		{
			list($headers, $body) = preg_split('_\r\n\r\n_', $body);
		}
		$this->setResponseBody($body);
		$this->setResponseHeaders($headers);
	}

	/**
	 * returns a print_r() representation of the response
	 */
	public function __toString()
	{
		//$string = print_r($this, true);
		$string = sprintf("%s\n\n%s\n", $this->response_headers, $this->response_body);
		return $string;
	}

	/**
	 * set HTTP response code
	 * @param int $code HTTP response code
	 */
	public function setResponseCode($code)
	{
		$this->response_code = (int)$code;
	}

	/**
	 * returns HTTP response code
	 * @return int
	 */
	public function getResponseCode()
	{
		return $this->response_code;
	}

	/**
	 * set response body
	 * @param string $body response body (binary string)
	 */
	public function setResponseBody($body)
	{
		$this->response_body = $body;
	}

	/**
	 * returns response body
	 * @return string
	 */
	public function getResponseBody()
	{
		return $this->response_body;
	}

	/**
	 * set response headers
	 * @param string $body response headers
	 */
	public function setResponseHeaders($headers)
	{
		$this->response_headers = $headers;
	}

	/**
	 * returns response headers
	 * @return string
	 */
	public function getResponseHeaders($headers)
	{
		return $this->response_headers;
	}
}