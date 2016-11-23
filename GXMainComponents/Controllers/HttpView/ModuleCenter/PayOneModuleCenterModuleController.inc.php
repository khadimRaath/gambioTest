<?php
/* --------------------------------------------------------------
  PayOneModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class PayOneModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class PayOneModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected function _init()
	{
		$this->pageTitle = $this->languageTextManager->get_text('payone_title');
		$this->buttons   = array(
			array(
				'text' => $this->languageTextManager->get_text('payone_menu_configuration'),
				'url'  => xtc_href_link('payone_config.php')
			),
			array(
				'text' => $this->languageTextManager->get_text('payone_menu_api_log'),
				'url'  => xtc_href_link('payone_logs.php')
			)
		);
	}
}