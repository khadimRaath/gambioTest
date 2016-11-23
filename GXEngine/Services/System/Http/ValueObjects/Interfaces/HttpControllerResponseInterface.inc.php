<?php
/* --------------------------------------------------------------
   HttpControllerResponseInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpControllerResponseInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpControllerResponseInterface
{
	/**
	 * Returns the response headers array.
	 *
	 * @return array
	 */
	public function getHeaders();


	/**
	 * Returns the response body string.
	 *
	 * @return string
	 */
	public function getBody();
}