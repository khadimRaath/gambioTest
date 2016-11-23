<?php
/* --------------------------------------------------------------
   UserConfigurationReaderInterface.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface UserConfigurationReaderInterface
 *
 * @category   System
 * @package    UserConfiguration
 * @subpackage Interfaces
 */
interface UserConfigurationReaderInterface
{
	/**
	 * Gets a user configuration (table: user_configuration)
	 *
	 * @param IdType $userId
	 * @param string      $configurationKey
	 *
	 * @return string
	 */
	public function getUserConfiguration(IdType $userId, $configurationKey);
}