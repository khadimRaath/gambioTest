<?php
/* --------------------------------------------------------------
   GXCoreLoaderSettingsInterface.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface GXCoreLoaderSettingsInterface
 *
 * @category   System
 * @package    Loaders
 * @subpackage Interfaces
 */
interface GXCoreLoaderSettingsInterface
{
	
	/**
	 * Get Database Name
	 *
	 * @return string
	 */
	public function getDatabaseName();
	
	
	/**
	 * Get Database Password
	 *
	 * @return string
	 */
	public function getDatabasePassword();
	
	
	/**
	 * Get Database Server
	 *
	 * @return string
	 */
	public function getDatabaseServer();
	
	
	/**
	 * Get Database User
	 *
	 * @return string
	 */
	public function getDatabaseUser();
}