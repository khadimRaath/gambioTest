<?php
/* --------------------------------------------------------------
   BackupControlOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class BackupControlOverload
 * 
 * This sample overload class demonstrates the replacement of the DBBackupControl class. It will place the database 
 * backups in a custom directory (admin/backups_overloaded). 
 * 
 * Notice: You might need to create the directory with an FTP user in case of permission errors.
 *
 * After enabling this sample overload head to the admin database-backups page and create a new backup. The new file
 * will be placed in the "admin/backups_overloaded" directory instead of the original "admin/backups".
 * 
 */
class BackupControlOverload extends BackupControlOverload_parent
{
	/**
	 * Overloaded constructor of db backup control.
	 * 
	 * This constructor will try to create a new custom directory for the backups and set the class destination 
	 * path to that directory. 
	 * 
	 * @throws RuntimeException If the directory could not be created due to permission problems.
	 */
	public function __construct()
	{
		parent::__construct();

		$customBackupsPath = DIR_FS_ADMIN . 'backups_overload/';
		
		// Creates the custom backup directory if it not exists.
		if(!file_exists($customBackupsPath) && !@mkdir($customBackupsPath, 077, true) && !is_dir($customBackupsPath))
		{
			throw new RuntimeException('Could not create custom backups directory at "' . $customBackupsPath . '". ' 
				. 'Please create this directory with an FTP user.'); 
		}
		
		$this->v_backup_file_path = $customBackupsPath;
	}


	/**
	 * Overloaded the "get_new_db_backup_filename" method. 
	 * 
	 * This method will add the "overloaded" prefix to the backup file names.
	 *
	 * @return string
	 */
	public function get_new_db_backup_filename()
	{
		return 'overloaded_' . parent::get_new_db_backup_filename();
	}
}
