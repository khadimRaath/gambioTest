<?php
/* --------------------------------------------------------------
   SampleJSGlobalExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleJSGlobalExtender
 * 
 * This is a sample overload for the JSGlobalExtenderComponent.
 * 
 * @deprecated This extender works only with EyeCandy which is deprecated and will be replaced by Honeygrid.
 *             
 * @see JSGlobalExtenderComponent
 */
class SampleJSGlobalExtender extends SampleJSGlobalExtender_parent
{
	/**
	 * Overloaded "proceed" method.
	 */
	public function proceed()
	{
		parent::proceed();
		
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_shop_scripts.js'));
	}
}