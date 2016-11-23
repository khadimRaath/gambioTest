<?php
/* --------------------------------------------------------------
  MediafinanzModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class MediafinanzModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class MediafinanzModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected function _init()
	{
		$this->pageTitle = $this->languageTextManager->get_text('mediafinanz_title');
		$this->buttons   = array(
			array(
				'text' => $this->languageTextManager->get_text('mediafinanz_menu_configuration'),
				'url'  => xtc_href_link('mediafinanz.php?action=config')
			),
			array(
				'text' => $this->languageTextManager->get_text('mediafinanz_menu_errors'),
				'url'  => xtc_href_link('mediafinanz.php?action=errors')
			),
			array(
				'text' => $this->languageTextManager->get_text('mediafinanz_menu_demands'),
				'url'  => xtc_href_link('mediafinanz.php?action=claims')
			)
		);
	}
}