<?php
/* --------------------------------------------------------------
   AjaxException.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AjaxException
 *
 * @category   System
 * @package    Http
 * @subpackage Exceptions
 * @extends    Exception
 */
class AjaxException extends Exception
{
	/**
	 * Returns a JSON encoded object containing the exception information.
	 *
	 * This particular exception class can pass JSON encoded information as an
	 * AJAX response, so that they can be parsed and manipulated by JavaScript.
	 *
	 * @param Exception $ex Contains the exception information to be returned as a response.
	 *
	 * @return array Provide this array as an argument in the JsonHttpControllerResponse object.
	 */
	public static function response(Exception $ex)
	{
		return array(
			'exception'     => true,
			'message'       => $ex->getMessage(),
			'code'          => $ex->getCode(),
			'file'          => $ex->getFile(),
			'line'          => $ex->getLine(),
			'trace'         => $ex->getTrace(),
			'previous'      => $ex->getPrevious(),
			'traceAsString' => $ex->getTraceAsString()
		);
	}
}