<?php

/* --------------------------------------------------------------
   OldOrderOverviewModuleCenterModuleController.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class OldOrderOverviewModuleCenterModuleController
 * 
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 */
class OldOrderOverviewModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	/**
	 * Redirects to the old order overview page.
	 *
	 * Function will be called in the constructor
	 */
	protected function _init()
	{
		$this->redirectUrl = xtc_href_link('orders.php');
	}
}