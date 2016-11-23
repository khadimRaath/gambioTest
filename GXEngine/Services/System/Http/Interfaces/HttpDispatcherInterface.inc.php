<?php
/* --------------------------------------------------------------
   HttpDispatcherInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpDispatcherInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpDispatcherInterface
{
	/**
	 * Dispatches the current http request.
	 *
	 * @param \HttpContextInterface $httpContext Object which holds information about the current http context.
	 */
	public function dispatch(HttpContextInterface $httpContext);
}