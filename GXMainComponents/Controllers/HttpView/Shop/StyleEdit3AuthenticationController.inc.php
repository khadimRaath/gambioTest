<?php
/* --------------------------------------------------------------
  StyleEdit3AuthenticationController.inc.php 2016-05-18
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class StyleEdit3AuthenticationController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class StyleEdit3AuthenticationController extends HttpViewController
{
	public function actionDefault()
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			require_once DIR_FS_CATALOG . 'StyleEdit3/bootstrap.inc.php';
			\StyleEdit\Authentication::setAuthenticationToValid();

			return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('../StyleEdit3/index.php',
			                                                        'template=' . rawurlencode(CURRENT_TEMPLATE)
			                                                        . '&lang=' . $_SESSION['language_code'], 'NONSSL'));
		}

		return parent::actionDefault();
	}
}