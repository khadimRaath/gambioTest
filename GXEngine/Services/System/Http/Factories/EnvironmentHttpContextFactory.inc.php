<?php
/* --------------------------------------------------------------
   EnvironmentHttpContextFactory.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractHttpContextFactory');

/**
 * Class EnvironmentHttpContextFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 * @extends    AbstractHttpContextFactory
 */
class EnvironmentHttpContextFactory extends AbstractHttpContextFactory
{
	/**
	 * Creates a new HttpContext instance.
	 *
	 * @return HttpContextInterface
	 */
	public function create()
	{
		return MainFactory::create('HttpContext', $_SERVER, $_GET, $_POST, $_COOKIE, $_SESSION);
	}
}