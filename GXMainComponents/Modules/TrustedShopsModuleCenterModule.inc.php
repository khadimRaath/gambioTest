<?php
/* --------------------------------------------------------------
  TrustedShopsModuleCenterModule.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class TrustedShopsModuleCenterModule
 *
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class TrustedShopsModuleCenterModule extends AbstractModuleCenterModule
{
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('trusted_shops_title');
		$this->description = $this->languageTextManager->get_text('trusted_shops_description');
		$this->sortOrder   = 71080;
	}
}