<?php

/* --------------------------------------------------------------
   EnvCategoryServiceSettings.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EnvCategoryServiceSettings
 *
 * This class contains the file system path to the image directory by using specific constants which are defined in
 * the config. It is used by the factory to build the proper service environment.
 * By encapsulating this dependency the code becomes more explicit and testable.
 *
 * @category   System
 * @package    Category
 * @subpackage Entities
 */
class EnvCategoryServiceSettings implements CategoryServiceSettingsInterface
{
	
	/**
	 * Returns the path to image directory.
	 * 
	 * @return string
	 */
	public function getImagesDirPath()
	{
		$path = DIR_FS_CATALOG . 'images/';

		return $path;
	}
}