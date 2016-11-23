<?php
/* --------------------------------------------------------------
   AdminHttpViewController.inc.php 2015-12-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');
MainFactory::load_class('AdminStatusOnlyInterface');

/**
 * Class AdminHttpViewController
 *
 * This class contains some helper methods for handling view requests. Be careful
 * always when outputting raw user data to HTML or when handling POST requests because
 * insufficient protection will lead to XSS and CSRF vulnerabilities.
 *
 * @link       http://en.wikipedia.org/wiki/Cross-site_scripting
 * @link       http://en.wikipedia.org/wiki/Cross-site_request_forgery
 *
 * @category   System
 * @package    Http
 * @implements HttpViewControllerInterface
 */
class AdminHttpViewController extends HttpViewController implements AdminStatusOnlyInterface
{
	/**
	 * Makes sure that the admin status is currently given in session
	 *
	 * @throws LogicException
	 */
	public function validateCurrentAdminStatus() 
	{
		if($_SESSION['customers_status']['customers_status_id'] != 0)
		{
			throw new LogicException('unexpected execution context');
    	}
	}
}