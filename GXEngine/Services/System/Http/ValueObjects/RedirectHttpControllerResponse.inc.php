<?php
/* --------------------------------------------------------------
   RedirectHttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Class RedirectHttpControllerResponse
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpControllerResponse
 */
class RedirectHttpControllerResponse extends HttpControllerResponse
{
	/**
	 * Initializes the redirect http controller response.
	 *
	 * @param string $location         Location to redirect.
	 * @param bool   $movedPermanently Add status code 301 (Moved Permanently) to the http header.
	 */
	public function __construct($location, $movedPermanently = false)
	{
		if($movedPermanently)
		{
			$this->httpHeadersArray[] = 'HTTP/1.1 301 Moved Permanently';
		}
		$this->httpHeadersArray[] = 'Location: ' . $location;
	}
}