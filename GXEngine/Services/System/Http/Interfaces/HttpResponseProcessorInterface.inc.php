<?php
/* --------------------------------------------------------------
   HttpResponseProcessorInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpResponseProcessorInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpResponseProcessorInterface
{
	/**
	 * Processes the http response object which is returned by a controller action method.
	 * Sends the processed response header and body to the client (Either redirect or display some output).
	 *
	 * @param HttpControllerResponseInterface $response Response object from the controllers action method.
	 */
	public function proceed(HttpControllerResponseInterface $response);
}