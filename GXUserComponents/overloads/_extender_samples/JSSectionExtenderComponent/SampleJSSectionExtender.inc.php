<?php
/* --------------------------------------------------------------
   SampleJSSectionExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleJSSectionExtender
 * 
 * This is a sample overload for the JSSectionExtenderComponent.
 *             
 * @see JSSectionExtenderComponent
 */
class SampleJSSectionExtender extends SampleJSSectionExtender_parent
{
	/**
	 * Overloaded "filter_set_main" method.
	 */
	protected function filter_set_main()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/jquery.mousewheel.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/jquery.tinyscrollbar.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/filter/filter_set_main.js'));
	}
}