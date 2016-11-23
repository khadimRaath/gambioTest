<?php

/* --------------------------------------------------------------
   ClassFinderSettingsInterface.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ClassFinderSettingsInterface
 * 
 * @category System
 * @package Shared
 * @subpackage ClassFinder
 */
interface ClassFinderSettingsInterface
{
	/**
	 * Returns an associative array with all classes that will be filtered by the ClassFinder.
	 * Array format: [ClassName] => [ClassFullFilePath]
	 *
	 * @return array
	 */
	public function getAvailableClasses();
	
	
	/**
	 * Returns an numeric array with all directories that will be accepted by the ClassFinder.
	 *
	 * @return array
	 */
	public function getAllowedDirectories();
	
	/**
	 * Returns an numeric array with all directories that will NOT be accepted by the ClassFinder.
	 *
	 * @return array
	 */
	public function getDisallowedDirectories();
	
}