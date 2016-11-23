<?php
/* --------------------------------------------------------------
   SampleAdminApplicationTopExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminApplicationTopExtender
 *
 * This is a sample overload for the AdminApplicationTopExtenderComponent.
 *
 * @see AdminApplicationTopExtenderComponent
 */
class SampleAdminApplicationTopExtender extends SampleAdminApplicationTopExtender_parent
{
	/**
	 * Overloaded "proceed" method.
	 */
	public function proceed()
	{
		parent::proceed();
		
		// PHP Code ... 
		
		$GLOBALS['messageStack']->add('<h3>Gambio ApplicationTopExtenderComponent</h3>', 'info');
	}
}