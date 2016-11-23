<?php
/* --------------------------------------------------------------
   SampleJSCheckoutExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleJSCheckoutExtender
 * 
 * This is a sample overload for the JSCheckoutExtenderComponent.
 * 
 * @deprecated This extender works only with EyeCandy which is deprecated and will be replaced by Honeygrid.
 *             
 * @see JSCheckoutExtenderComponent
 */
class SampleJSCheckoutExtender extends SampleJSCheckoutExtender_parent
{
	/**
	 * Overloaded "proceed" method. 
	 */
	public function proceed()
	{
		parent::proceed();
		
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/PreserveSessionHandler.js'));
	}
}