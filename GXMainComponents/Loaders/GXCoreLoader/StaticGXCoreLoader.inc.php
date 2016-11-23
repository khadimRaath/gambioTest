<?php
/* --------------------------------------------------------------
   StaticGXCoreLoader.inc.php 2016-07-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class StaticGXCoreLoader
 *
 * This class is for static use only.
 * Usage example: $customerService = StaticGXCoreLoader::getService('Customer');
 *
 * @category    System
 * @package     Loaders
 * @subpackage  GXCoreLoader
 */
class StaticGXCoreLoader
{
	/**
	 * GXCoreLoader Instance
	 *
	 * @var GXCoreLoaderInterface
	 */
	protected static $gxCoreLoader = null;
	
	
	/**
	 * Class Constructor
	 *
	 * @throws BadFunctionCallException
	 */
	public function __construct()
	{
		throw new BadFunctionCallException('This class is for static use only.');
	}
	
	
	/**
	 * Get a service object instance.
	 *
	 * @param string $serviceName
	 *
	 * @return object
	 */
	public static function getService($serviceName)
	{
		$gxCoreLoader = self::_getGXCoreLoader();
		$service      = $gxCoreLoader->getService($serviceName);
		
		return $service;
	}
	
	
	/**
	 * Method depends on CodeIgniter database library.
	 *
	 * @return CI_DB_query_builder
	 */
	public static function getDatabaseQueryBuilder()
	{
		$gxCoreLoader         = self::_getGXCoreLoader();
		$databaseQueryBuilder = $gxCoreLoader->getDatabaseQueryBuilder();
		
		return $databaseQueryBuilder;
	}
	
	
	/**
	 * Method depends on CodeIgniter database library.
	 *
	 * @return CI_DB_utility
	 */
	public static function getDatabaseUtilityHelper()
	{
		$gxCoreLoader          = self::_getGXCoreLoader();
		$databaseUtilityHelper = $gxCoreLoader->getDatabaseUtilityHelper();
		
		return $databaseUtilityHelper;
	}
	
	
	/**
	 * Method depends on CodeIgniter database library.
	 *
	 * @return CI_DB_forge
	 */
	public static function getDatabaseForgeHelper()
	{
		$gxCoreLoader        = self::_getGXCoreLoader();
		$databaseForgeHelper = $gxCoreLoader->getDatabaseForgeHelper();
		
		return $databaseForgeHelper;
	}
	
	/**
	 * Get GX Core Loader object instance.
	 *
	 * @return GXCoreLoaderInterface
	 */
	protected static function _getGXCoreLoader()
	{
		if(self::$gxCoreLoader === null)
		{
			$gxCoreLoaderSettings = MainFactory::create('GXCoreLoaderSettings');
			self::$gxCoreLoader   = MainFactory::create('GXCoreLoader', $gxCoreLoaderSettings);
		}
		
		return self::$gxCoreLoader;
	}
}
