<?php
/* --------------------------------------------------------------
   GXCoreLoaderSettings.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('GXCoreLoaderSettingsInterface');

/**
 * Class GXCoreLoaderSettings
 *
 * Wraps needed settings from the environment.
 *
 * @category    System
 * @package     Loaders
 * @subpackage  GXCoreLoader
 */
class GXCoreLoaderSettings implements GXCoreLoaderSettingsInterface
{
	/**
	 * Database User Value
	 *
	 * @var string
	 */
	protected $databaseUser;
	
	/**
	 * Database Password Value
	 *
	 * @var string
	 */
	protected $databasePassword;
	
	/**
	 * Database Server Value
	 *
	 * @var string
	 */
	protected $databaseServer;
	
	/**
	 * Database Name Value
	 *
	 * @var string
	 */
	protected $databaseName;
	
	/**
	 * Database Socket Value
	 *
	 * @var string
	 */
	protected $databaseSocket;
	
	
	/**
	 * Uses the credentials in configure.php for setting the member variables
	 */
	public function __construct()
	{
		$this->databaseUser     = DB_SERVER_USERNAME;
		$this->databasePassword = DB_SERVER_PASSWORD;
		$this->databaseServer   = DB_SERVER;
		$this->databaseName     = DB_DATABASE;
		
		if(strpos(DB_SERVER, ':/')) // mysql socket detected
		{
			$exploded             = explode(':', DB_SERVER);
			$this->databaseServer = array_shift($exploded);
			$this->databaseSocket = array_shift($exploded);
		}
	}
	
	
	/**
	 * Get database name value from config.
	 *
	 * @return string
	 */
	public function getDatabaseName()
	{
		return $this->databaseName;
	}
	
	
	/**
	 * Get database password value from config.
	 *
	 * @return string
	 */
	public function getDatabasePassword()
	{
		return urlencode($this->databasePassword); // needs to encode slashes and other characters
	}
	
	
	/**
	 * Get database server value from config.
	 *
	 * @return string
	 */
	public function getDatabaseServer()
	{
		return $this->databaseServer;
	}
	
	
	/**
	 * Get database user value from config.
	 *
	 * @return string
	 */
	public function getDatabaseUser()
	{
		return $this->databaseUser;
	}
	
	
	/**
	 * Get database socket value from config.
	 *
	 * @return mixed|string
	 */
	public function getDatabaseSocket()
	{
		return $this->databaseSocket;
	}
}