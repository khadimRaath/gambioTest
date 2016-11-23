<?php
/* --------------------------------------------------------------
   StaticCrossCuttingLoader.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class StaticCrossCuttingLoader
 *
 * This class wraps the CrossCuttingLoader for static use.
 *
 * CrossCuttingLoader enables loading of mockable objects for cross cutting concerns,
 * that were not injected to the current object.
 *
 * @category   System
 * @package    Loaders
 * @subpackage CrossCuttingLoader
 */
class StaticCrossCuttingLoader
{
	/**
	 * @var CrossCuttingLoaderInterface
	 */
	protected static $crossCuttingLoader = null;
	
	
	/**
	 * @throws BadFunctionCallException
	 */
	public function __construct()
	{
		throw new BadFunctionCallException('This class is for static use only.');
	}
	
	
	/**
	 * In strict mode ($crossCuttingLoader->strictModeEnabled=true) this method accepts classes with implemented
	 * CrossCuttingObjectInterface only. Otherwise it throws an InvalidArgumentException.
	 *
	 * @param string $p_classname
	 *
	 * @return object
	 * @throws InvalidArgumentException
	 */
	public static function getObject($p_classname)
	{
		$crossCuttingLoader = self::_getCrossCuttingLoader();
		
		return $crossCuttingLoader->getObject($p_classname);
	}
	
	
	/**
	 * @return boolean
	 */
	public static function useRegisteredObjectsOnly()
	{
		$crossCuttingLoader = self::_getCrossCuttingLoader();
		
		return $crossCuttingLoader->useRegisteredObjectsOnly();
	}
	
	
	/**
	 * If RegisteredObjectsOnly is enabled, the loader returns only objects, that were registered by
	 * the registerObject method before. RegisteredObjectsOnly should be enabled in all unit tests.
	 *
	 * @param $bool_status
	 */
	public static function setRegisteredObjectsOnly($bool_status)
	{
		$crossCuttingLoader = self::_getCrossCuttingLoader();
		$crossCuttingLoader->setRegisteredObjectsOnly($bool_status);
	}
	
	
	/**
	 * In strict mode ($crossCuttingLoader->strictModeEnabled=true) this method accepts classes with implemented
	 * CrossCuttingObjectInterface only. Otherwise it throws an InvalidArgumentException.
	 *
	 * @param string $p_classname
	 * @param object $object
	 *
	 * @throws InvalidArgumentException
	 */
	public static function registerObject($p_classname, $object)
	{
		$crossCuttingLoader = self::_getCrossCuttingLoader();
		$crossCuttingLoader->registerObject($p_classname, $object);
	}
	
	
	/**
	 * @return void
	 */
	public static function clearRegister()
	{
		$crossCuttingLoader = self::_getCrossCuttingLoader();
		$crossCuttingLoader->clearRegister();
	}
	
	
	/**
	 * @return CrossCuttingLoaderInterface
	 */
	protected static function _getCrossCuttingLoader()
	{
		if(self::$crossCuttingLoader === null)
		{
			self::$crossCuttingLoader = MainFactory::create('CrossCuttingLoader');
		}
		
		return self::$crossCuttingLoader;
	}
}
