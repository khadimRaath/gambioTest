<?php
/* --------------------------------------------------------------
   AbstractHttpContextFactory.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class AbstractHttpContextFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 */
abstract class AbstractHttpContextFactory
{
	/**
	 * Creates and returns a new instance of a http context interface.
	 *
	 * @return HttpContextInterface
	 */
	public abstract function create();
}