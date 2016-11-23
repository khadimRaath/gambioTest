<?php
/* --------------------------------------------------------------
   is_mobile_template_installed.inc.php 2016-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Checks if a compatible mobile template is installed
 *
 * @return bool
 */
function is_mobile_template_installed()
{
	$isInstalled = false;
	$minVersion  = '1.2.13';
	
	$query  = 'SELECT `version` 
				FROM `version_history` 
				WHERE 
					`installed` = 1 AND 
					`name` LIKE "MobileCandy%" 
				ORDER BY installation_date DESC
				LIMIT 1';
	$result = xtc_db_query($query);
	
	if(xtc_db_num_rows($result))
	{
		$version = xtc_db_fetch_array($result)['version'];
		
		if(version_compare($version, $minVersion, '>='))
		{
			$isInstalled = true;
			
			if($version === $minVersion)
			{
				$isInstalled = file_exists(DIR_FS_CATALOG . 'version_info/mobile_template-1_2_13-gx3_1_x.php');
			}
		}
	}
	
	return $isInstalled;
}