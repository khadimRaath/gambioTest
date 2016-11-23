<?php
/* --------------------------------------------------------------
   HttpViewControllerInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpViewControllerInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpViewControllerInterface
{
	/**
	 * Processes a http response object which is get by invoking an action method.
	 * The action method is determined by the current http context.
	 *
	 * @param HttpContextInterface $httpContext Http context object which hold the request variables.
	 */
	public function proceed(HttpContextInterface $httpContext);


	/**
	 * Default action method.
	 * Every controller child class requires at least the default action method, which is invoked when
	 * the ::_getQueryParameterData('do') value is not separated by a trailing slash.
	 *
	 * Every action method have to return an instance which implements the http controller response interface.
	 *
	 * @return HttpControllerResponseInterface
	 */
	public function actionDefault();
}