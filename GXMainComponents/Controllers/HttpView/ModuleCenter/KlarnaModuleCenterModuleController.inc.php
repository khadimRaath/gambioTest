<?php
/* --------------------------------------------------------------
  KlarnaModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class KlarnaModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class KlarnaModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected function _init()
	{
		$this->pageTitle   = $this->languageTextManager->get_text('klarna_title');
		$this->redirectUrl = xtc_href_link('klarna_config.php');
	}
}