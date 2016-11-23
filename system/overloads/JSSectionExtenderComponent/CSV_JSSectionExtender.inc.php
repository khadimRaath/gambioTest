<?php
/* --------------------------------------------------------------
  CSV_JSSectionExtender.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class CSV_JSSectionExtender extends CSV_JSSectionExtender_parent
{
	function export_overview()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_overview.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/plugins/ajaxfileupload/ajaxfileupload.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/validation_plugin.js'));
	}

	function export_scheme_details()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/ui/jquery-ui.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/gm/form_changes_checker.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_scheme_details.js'));
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_categories.js'));
	}

	function export_scheme_delete()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_scheme_delete.js'));
	}

	function export_scheme_field_delete()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_scheme_field_delete.js'));
	}

	function export_scheme_export()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_scheme_export.js'));
	}

	function export_scheme_export_confirm()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_scheme_export_confirm.js'));
	}

	function export_scheme_changes_confirm()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/export/export_scheme_changes_confirm.js'));
	}
}
