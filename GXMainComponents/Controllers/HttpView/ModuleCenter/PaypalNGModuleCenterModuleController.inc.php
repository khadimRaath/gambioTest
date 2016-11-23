<?php
/* --------------------------------------------------------------
  PaypalNGModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class PaypalNGModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class PaypalNGModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected function _init()
	{
		$this->pageTitle = $this->languageTextManager->get_text('paypalng_title');
		$this->buttons   = array(
			array(
				'text' => $this->languageTextManager->get_text('paypalng_menu_config'),
				'url'  => xtc_href_link('paypal_config.php')
			),
			array(
				'text' => $this->languageTextManager->get_text('paypalng_menu_logs'),
				'url'  => xtc_href_link('paypal_logs.php')
			)
		);
	}
}