<?php
/* --------------------------------------------------------------
   AddModuleCenterEkomiIntegrationController.inc.php 2016-02-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AddModuleCenterEkomiIntegrationController
 * 
 * Add module center EkomiIntegration controller to the registry
 * 
 * @category   System
 * @package    Http
 * @subpackage Factories
 * @extends    AbstractHttpContextFactory
 */
class AddModuleCenterEkomiIntegrationController extends AddModuleCenterEkomiIntegrationController_parent
{
	/**
	 * Adds new available controller to the registry.
	 *
	 * @param HttpViewControllerRegistryInterface $registry Registry object which adds the new controller entries.
	 */
	protected function _addAvailableControllers(HttpViewControllerRegistryInterface $registry)
	{
		parent::_addAvailableControllers($registry);
		
		$registry->set('EkomiIntegrationModuleCenterModule', 'EkomiIntegrationModuleCenterModuleController');
	}
}

