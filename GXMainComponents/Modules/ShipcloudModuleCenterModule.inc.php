<?php
/* --------------------------------------------------------------
	ShipcloudModuleCenterModule.inc.php 2016-07-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ShipcloudModuleCenterModule
 * 
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class ShipcloudModuleCenterModule extends AbstractModuleCenterModule
{
	/**
	 * wrapper object for text phrases
	 * @var ShipcloudText
	 */
	protected $shipcloudText;

	/**
	 * Initializes Shipcloud module center module
	 * @return void
	 */
	protected function _init()
	{
		$this->shipcloudText = MainFactory::create('ShipcloudText');
		$this->title         = $this->shipcloudText->get_text('shipcloud_module_title');
		$this->description   = $this->shipcloudText->get_text('shipcloud_module_description');
		$this->sortOrder     = 42424;
	}

}
