<?php
/* --------------------------------------------------------------
   DBBackup_JSSectionExtender.inc.php 2015-07-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DBBackup_JSSectionExtender extends DBBackup_JSSectionExtender_parent
{
	protected function db_backup()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/db_backup/db_backup.js'));
	}
	protected function db_backup_create()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/db_backup/db_backup_create.js'));
	}
	protected function db_backup_restore()
	{
		include_once(get_usermod(DIR_FS_CATALOG . 'admin/html/assets/javascript/legacy/db_backup/db_backup_restore.js'));
	}
}
