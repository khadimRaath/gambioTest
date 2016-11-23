<?php
/* --------------------------------------------------------------
  ItRechtModuleCenterModule.inc.php 2016-05-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ItRechtModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class ItRechtModuleCenterModule extends AbstractModuleCenterModule
{
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('it_recht_title');
		$this->description = $this->languageTextManager->get_text('it_recht_description');
		$this->sortOrder   = 32258;
	}

	/**
	* Installs the module
	*/
	public function install()
	{
		parent::install();
	}

	/**
	 * Uninstalls the module
	 */
	public function uninstall()
	{
		parent::uninstall();
	}
}