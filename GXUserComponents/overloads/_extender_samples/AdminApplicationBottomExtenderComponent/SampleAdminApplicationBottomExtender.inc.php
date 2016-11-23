<?php
/* --------------------------------------------------------------
   SampleAdminApplicationBottomExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminApplicationBottomExtender
 *
 * This is a sample overload for the AdminApplicationBottomExtenderComponent.
 *
 * @see AdminApplicationBottomExtenderComponent
 */
class SampleAdminApplicationBottomExtender extends SampleAdminApplicationBottomExtender_parent
{
	/**
	 * Overloaded "proceed" method.
	 */
	public function proceed()
	{
		parent::proceed();
		
		// PHP Code ...
	}
}