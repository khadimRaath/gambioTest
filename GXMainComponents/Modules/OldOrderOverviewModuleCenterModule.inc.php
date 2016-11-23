<?php
/* --------------------------------------------------------------
   OldOrderOverviewModuleCenterModule.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OldOrderOverviewModuleCenterModule
 * 
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class OldOrderOverviewModuleCenterModule extends AbstractModuleCenterModule
{
	/**
	 * Initialize the module and set title, description, sort order etc.
	 *
	 * Function will be called in the constructor
	 */
	protected function _init()
	{
		$this->title       = $this->languageTextManager->get_text('old_order_overview_title');
		$this->description = $this->languageTextManager->get_text('old_order_overview_description');
		$this->sortOrder   = 69874;
	}
}