<?php
/* --------------------------------------------------------------
	PostfinderModuleCenterModule.inc.php 2016-07-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class PostfinderModuleCenterModule
 * 
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class PostfinderModuleCenterModule extends AbstractModuleCenterModule
{
	/**
	 * Initializes Shipcloud module center module
	 * @return void
	 */
	protected function _init()
	{
		$text                = MainFactory::create('LanguageTextManager', 'postfinder', $_SESSION['languages_id']);
		$this->title         = $text->get_text('module_title');
		$this->description   = $text->get_text('module_description');
		$this->sortOrder     = 28476;
	}
}
