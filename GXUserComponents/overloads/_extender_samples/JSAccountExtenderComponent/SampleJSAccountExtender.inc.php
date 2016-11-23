<?php
/* --------------------------------------------------------------
   SampleJSAccountExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleJSAccountExtender
 * 
 * This is a sample overload for the JSAccountExtenderComponent.
 * 
 * @deprecated This extender works only with EyeCandy which is deprecated and will be replaced by Honeygrid.
 *             
 * @see JSAccountExtenderComponent
 */
class SampleJSAccountExtender extends SampleJSAccountExtender_parent
{
	/**
	 * Overloaded "proceed" method. 
	 */
	public function proceed()
	{
		parent::proceed();
		
		include_once(get_usermod(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/javascript/FormHighlighterHandler.js'));
	}
}