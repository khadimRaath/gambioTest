<?php
/* --------------------------------------------------------------
   HttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponseInterface');

/**
 * Class HttpControllerResponse
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpControllerResponseInterface
 */
class HttpControllerResponse implements HttpControllerResponseInterface
{
	/**
	 * @var array
	 */
	protected $httpHeadersArray = array();

	/**
	 * @var string
	 */
	protected $httpBody;


	/**
	 * Initializes the http controller response.
	 *
	 * @param string $responseBody         Rendered html markup.
	 * @param array  $responseHeadersArray Array which contains information about the http response headers.
	 */
	public function __construct($responseBody, $responseHeadersArray = null)
	{
		if($responseHeadersArray !== null)
		{
			$this->httpHeadersArray = $responseHeadersArray;
		}
		$this->httpBody = $responseBody;
	}


	/**
	 * Returns the response headers array.
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->httpHeadersArray;
	}


	/**
	 * Returns the response body string.
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->httpBody;
	}
}