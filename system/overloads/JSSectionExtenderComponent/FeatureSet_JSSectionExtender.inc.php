<?php
/* --------------------------------------------------------------
   FeatureSet_JSSectionExtender.inc.php 2015-08-31 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FeatureSet_JSSectionExtender extends FeatureSet_JSSectionExtender_parent
{
	protected function filter_set_main()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/jquery.mousewheel.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/jquery.tinyscrollbar.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/filter/filter_set_main.js'));
	}

	protected function filter_set_edit()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/filter/filter_set_edit.js'));
	}

	protected function filter_set_delete()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/filter/filter_set_delete.js'));
	}
}
