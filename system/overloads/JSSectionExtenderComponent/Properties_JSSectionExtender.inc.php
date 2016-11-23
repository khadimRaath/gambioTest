<?php
/* --------------------------------------------------------------
   Properties_JSSectionExtender.inc.php 2015-07-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Properties_JSSectionExtender extends Properties_JSSectionExtender_parent
{
	protected function properties_main()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_main.js'));
	}

	protected function properties_edit()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_edit.js'));
	}

	protected function properties_delete()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_delete.js'));
	}

	protected function properties_values_edit()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_values_edit.js'));
	}

	protected function properties_values_delete()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_values_delete.js'));
	}
}
