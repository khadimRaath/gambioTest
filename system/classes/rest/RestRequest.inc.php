<?php
/* --------------------------------------------------------------
	RestRequest.inc.php 2014-12-05
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
* This class represents a REST request.
*/
class RestRequest
{
	/**
	* @var string $method HTTP method as defined by one of the METHOD_* constants
	*/
	protected $method;

	/**
	* @var string $url cleaned URL for this request
	*/
	protected $url;

	/**
	* @var array $parsed_url URL for this request
	*/
	protected $parsed_url;

	/**
	* @var ärräy $data data to be sent in the body of a POST/PUT/PATCH request
	*/
	protected $data = array();

	/**
	* @var array $headers list of custom HTTP header lines
	*/
	protected $headers = array();

	/**
	* @var $userpass username and password for basic auth ("username:password" syntax)
	*/
	protected $userpass = '';

	/**
	* GET request
	*/
	const METHOD_GET = 'GET';

	/**
	 * PUT request
	 */
	const METHOD_PUT = 'PUT';

	/**
	 * POST request
	 */
	const METHOD_POST = 'POST';

	/**
	 * DELETE request
	 */
	const METHOD_DELETE = 'DELETE';

	/**
	 * PATCH request
	 */
	const METHOD_PATCH = 'PATCH';

	/**
	 * creates new request from given method, URL and (optionally) data and headers
	 * @param string $method any one of the METHOD_* constants
	 * @param string $url request URL
	 * @param mixed $data string or array of request body data (@see setData())
	 * @param array $headers additional HTTP headers
	 */
	public function __construct($method, $url, $data = null, $headers = null)
	{
		$this->setMethod($method);
		$this->setURL($url);
		$this->setData($data);
		$this->setHeaders($headers);
	}

	/**
	 * returns a printable representation of the request.
	 */
	public function __toString()
	{
		$string = sprintf("%s %s\n", $this->method, $this->url);
		if(!empty($this->headers))
		{
			$string .= sprintf("%s\n", implode("\n", $this->headers));
		}
		if(!empty($this->data))
		{
			$dataString = is_array($this->data) ? implode("\n", $this->data) : (string)$this->data;
			$string .= sprintf("\n%s\n", $dataString);
		}
		return $string;
	}

	/**
	 * set the request method
	 *
	 * @param string $method any of the METHOD_* constants
	 * @throws RestException if $method is not a supported HTTP method (GET, PUT, POST, DELETE, PATCH)
	 */
	public function setMethod($method)
	{
		if(!in_array($method, array(self::METHOD_GET, self::METHOD_PUT, self::METHOD_POST, self::METHOD_DELETE, self::METHOD_PATCH)))
		{
			throw new RestException('invalid HTTP method');
		}
		$this->method = $method;
	}

	/**
	 * returns the HTTP method for this request
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * sets the URL for this request.
	 * The URL gets filtered, parsed and reassembled before it's stored to ensure its syntactical correctness.
	 * @param string $url URL to be used for this request
	 */
	public function setURL($url)
	{
		$filtered_url = filter_var($url, FILTER_VALIDATE_URL);
		if($filtered_url === false)
		{
			throw new RestException('invalid URL');
		}
		$parsed_url = parse_url($filtered_url);
		if($parsed_url === false)
		{
			throw new RestException('unparseable URL');
		}
		if(!in_array($parsed_url['scheme'], array('http', 'https')))
		{
			throw new RestException('unsupported URL scheme');
		}
		$this->parsed_url = $parsed_url;
		$this->url = $this->assembleURL();
	}

	/**
	 * returns the URL for this request
	 */
	public function getURL()
	{
		return $this->url;
	}

	/**
	* assembles URL from parsed_url
	*/
	protected function assembleURL()
	{
		$parsed_url = $this->parsed_url;
		$new_url = $parsed_url['scheme'].'://';
		if(!empty($parsed_url['user']))
		{
			$new_url .= $parsed_url['user'];
			if(!empty($parsed_url['pass']))
			{
				$new_url .= ':'.$parsed_url['pass'];
			}
			$new_url .= '@';
		}
		$new_url .= $parsed_url['host'];
		if(!empty($parsed_url['port']))
		{
			$new_url .= ':'.$parsed_url['port'];
		}
		if(!empty($parsed_url['path']))
		{
			$new_url .= $parsed_url['path'];
		}
		else
		{
			$new_url .= '/';
		}
		if(!empty($parsed_url['query']))
		{
			$new_url .= '?'.$parsed_url['query'];
		}
		if(!empty($parsed_url['fragment']))
		{
			$new_url .= '#'.$parsed_url['fragment'];
		}
		return $new_url;
	}

	/**
	 * sets data for POST requests.
	 * Data may be a string (to be used as-is) or an array (which will be form-encoded).
	 * @param mixed $data data for request body (esp. POST/PUT/PATCH)
	 */
	public function setData($data)
	{
		if(empty($data))
		{
			$this->data = array();
		}
		elseif(is_array($data))
		{
			$this->data = http_build_query($data);
		}
		elseif(is_string($data))
		{
			/*
			if(preg_match('/(.+=.+)(&(.+=.+))?/', $data) === 1)
			{
				$this->data = array();
				parse_str($data, $this->data);
			}
			*/
			$this->data = $data;
		}
		else
		{
			throw new RestException('invalid data');
		}
	}

	/**
	 * returns data to be sent in request body
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * sets HTTP headers for the request.
	 * @param array $headers list of HTTP headers
	 */
	public function setHeaders($headers)
	{
		if($headers === null)
		{
			$this->headers = array();
		}
		elseif(is_array($headers))
		{
			$this->headers = $headers;
		}
		else
		{
			throw new RestException('headers must be of type array, '.gettype($headers).' found');
		}
	}

	/**
	 * returns list of headers to be sent with this request
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * sets basic auth credentials
	 * @param string $userpass username and password ('username:password')
	 */
	public function setUserpass($userpass)
	{
		$this->userpass = (string)$userpass;
	}

	/**
	 * returns basic auth credentials
	 */
	public function getUserpass()
	{
		return $this->userpass;
	}
}