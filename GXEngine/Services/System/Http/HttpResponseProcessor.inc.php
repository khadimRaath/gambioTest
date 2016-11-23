<?php
/* --------------------------------------------------------------
   HttpResponseProcessor.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpResponseProcessorInterface');

/**
 * Class HttpResponseProcessor
 *
 * @category   System
 * @package    Http
 * @implements HttpResponseProcessorInterface
 */
class HttpResponseProcessor implements HttpResponseProcessorInterface
{
	/**
	 * Processes the http response object which is returned by a controller action method.
	 * Sends the processed response header and body to the client (Either redirect or display some output).
	 *
	 * @param HttpControllerResponseInterface $response Response object from the controllers action method.
	 */
	public function proceed(HttpControllerResponseInterface $response)
	{
		$this->_sendHeaders($response->getHeaders());
		$this->_sendBody($response->getBody());
	}


	/**
	 * Sends the response header data to the client by the given http headers array.
	 *
	 * @see header Function to send headers data.
	 *
	 * @param array $httpHeadersArrays Array which contains the header items.
	 */
	protected function _sendHeaders(array $httpHeadersArrays)
	{
		foreach($httpHeadersArrays as $headerItem)
		{
			header($headerItem);
		}
	}


	/**
	 * Sends the response body data to the client.
	 *
	 * @param string $httpBody Rendered response body.
	 */
	protected function _sendBody($httpBody)
	{
		echo $httpBody;
	}
}