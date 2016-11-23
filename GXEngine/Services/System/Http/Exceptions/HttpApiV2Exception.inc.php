<?php

/* --------------------------------------------------------------
   HttpApiException.inc.php 2015-04-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class HttpApiException
 *
 * This exception class is used for handling exceptions that concern the
 * API execution. The default exception code is set to 500 (Internal Server
 * Error) and must explicitly set to any other standard HTTP status code in
 * order to be contained in the response headers.
 *
 * @link       http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 *
 * @category   System
 * @package    Http
 * @subpackage Exceptions
 */
class HttpApiV2Exception extends Exception
{
	/**
	 * Constructor with default code argument value set to 500 (Internal server error).
	 *
	 * @param string    $message
	 * @param int       $code
	 * @param Exception $previous
	 */
	public function __construct($message = '', $code = 500, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}