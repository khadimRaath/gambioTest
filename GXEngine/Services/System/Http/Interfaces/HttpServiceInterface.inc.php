<?php
/* --------------------------------------------------------------
   HttpServiceInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpServiceInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpServiceInterface
{
	/**
	 * Initializes the http service.
	 *
	 * @param AbstractHttpContextFactory $httpContextFactory Factory instance to create the http context objects.
	 * @param HttpDispatcherInterface    $httpDispatcher     Dispatcher instance to process the http response object.
	 */
	public function __construct(AbstractHttpContextFactory $httpContextFactory,
	                            HttpDispatcherInterface $httpDispatcher);


	/**
	 * Returns an instance of an http context object.
	 *
	 * @return HttpContextInterface Context object which holds information about the current request.
	 */
	public function getHttpContext();


	/**
	 * Handles the current http request by the given context.
	 *
	 * @param HttpContextInterface $httpContext Context object which holds information about the current request.
	 */
	public function handle(HttpContextInterface $httpContext);
}