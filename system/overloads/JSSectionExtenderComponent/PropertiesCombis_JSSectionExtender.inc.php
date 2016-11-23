<?php
/* --------------------------------------------------------------
   PropertiesCombis_JSSectionExtender.inc.php 2015-07-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesCombis_JSSectionExtender extends PropertiesCombis_JSSectionExtender_parent
{
	protected function properties_combis_edit()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/plugins/ajaxfileupload/ajaxfileupload_uncompressed.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_combis_edit.js'));
	}

	protected function properties_combis_delete()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_combis_delete.js'));
	}

	protected function properties_combis_delete_selected()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/properties_combis_delete_selected.js'));
	}

	protected function combis_defaults()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/combis_defaults.js'));
	}

	protected function combis_settings()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/properties/combis_settings.js'));
	}
}
