<?php

/* --------------------------------------------------------------
   EnvironmentClassFinderSettings.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EnvironmentClassFinderSettings
 * 
 * @category   System
 * @package    Shared
 * @subpackage ClassFinder
 */
class EnvironmentClassFinderSettings implements ClassFinderSettingsInterface
{
	/**
	 * Returns an associative array with all classes that will be filtered by the ClassFinder
	 * Array format: [ClassName] => [ClassFullFilePath]
	 *
	 * @return array
	 */
	public function getAvailableClasses()
	{
		$classRegistry   = MainFactory::get_class_registry();
		$allClassesArray = $classRegistry->get_all_data();
		
		return $allClassesArray;
	}
	
	
	/**
	 * Returns an numeric array with all directories that will be accepted by the ClassFinder.
	 *
	 * @return array
	 */
	public function getAllowedDirectories()
	{
		$allowedDirsArray = array(
			DIR_FS_CATALOG . 'GXEngine',
			DIR_FS_CATALOG . 'GXMainComponents',
			DIR_FS_CATALOG . 'GXUserComponents'
		);
		
		return $allowedDirsArray;
	}
	
	/**
	 * Returns an numeric array with all directories that will NOT be accepted by the ClassFinder.
	 *
	 * @return array
	 */
	public function getDisallowedDirectories()
	{
		$disallowedDirsArray = array(
			DIR_FS_CATALOG . 'GXUserComponents/overloads'
		);
		
		return $disallowedDirsArray;
	}
}