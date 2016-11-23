<?php
/* --------------------------------------------------------------
   HttpContextReaderInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface HttpContextReaderInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface HttpContextReaderInterface
{
	/**
	 * Returns the controller name for current http request context.
	 *
	 * @param HttpContextInterface $httpContext Object which holds information about the current http context.
	 *
	 * @return string Name of controller for the current http context.
	 */
	public function getControllerName(HttpContextInterface $httpContext);


	/**
	 * Returns the name of the action method for the current http context.
	 *
	 * @param \HttpContextInterface $httpContext Object which holds information about the current http context.
	 *
	 * @return string Name of action method for the current http context.
	 */
	public function getActionName(HttpContextInterface $httpContext);


	/**
	 * Returns an array which represents the global $_GET variable of the current http context.
	 *
	 * @param HttpContextInterface $httpContext Object which holds information about the current http context.
	 *
	 * @return array Array which hold information equal to the global $_GET variable in an object oriented layer.
	 */
	public function getQueryParameters(HttpContextInterface $httpContext);


	/**
	 * Returns an array which represents the global $_POST variable of the current http context.
	 *
	 * @param HttpContextInterface $httpContext Object which holds information about the current http context.
	 *
	 * @return array Array which hold information equal to the global $_POST variable in an object oriented layer.
	 */
	public function getPostData(HttpContextInterface $httpContext);
}