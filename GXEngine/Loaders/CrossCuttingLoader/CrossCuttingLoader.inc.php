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

MainFactory::load_class('CrossCuttingLoaderInterface');

/**
 * Class CrossCuttingLoader
 *
 * CrossCuttingLoader enables loading of mockable objects for cross cutting concerns,
 * that were not injected to the current object.
 *
 * Important:
 *        RegisteredObjectsOnly flag must be enabled for unit testing.
 *
 * @category   System
 * @package    Loaders
 * @subpackage CrossCuttingLoader
 */
class CrossCuttingLoader implements CrossCuttingLoaderInterface
{
	/**
	 * @var bool
	 * @todo Move to CrossCuttingLoaderSettings
	 */
	protected $strictModeEnabled = true;
	
	/**
	 * @var bool
	 */
	protected $registeredObjectsOnly = false;
	
	/**
	 * @var array
	 */
	protected $registeredObjectArrays = array();
	
	
	/**
	 * In strict mode ($strictModeEnabled=true) this method accepts classes with implemented
	 * CrossCuttingObjectInterface only. Otherwise it throws an InvalidArgumentException.
	 *
	 * @param string $p_classname
	 *
	 * @return object
	 * @throws InvalidArgumentException
	 */
	public function getObject($p_classname)
	{
		if($this->useRegisteredObjectsOnly())
		{
			$object = $this->_getObjectFromRegister($p_classname);
		}
		else
		{
			$object = $this->_getObjectFromMainFactory($p_classname);
		}
		
		$this->_strictModeValidateTypeOf($object);
		
		return $object;
	}
	
	
	/**
	 * RegisteredObjectsOnly Flag Setter
	 *
	 * If RegisteredObjectsOnly is enabled, the loader returns only objects, that were registered by
	 * the registerObject method before. RegisteredObjectsOnly should be enabled in all unit tests.
	 *
	 * @param boolean $bool_status
	 */
	public function setRegisteredObjectsOnly($bool_status)
	{
		$this->registeredObjectsOnly = (bool)$bool_status;
	}
	
	
	/**
	 * RegisteredObjectsOnly Flag Getter
	 *
	 * @return boolean
	 */
	public function useRegisteredObjectsOnly()
	{
		return $this->registeredObjectsOnly;
	}
	
	
	/**
	 * In strict mode ($strictModeEnabled=true) this method accepts classes with implemented
	 * CrossCuttingObjectInterface only. Otherwise it throws an InvalidArgumentException.
	 *
	 * @param string $p_classname
	 * @param object $object
	 *
	 * @throws InvalidArgumentException
	 */
	public function registerObject($p_classname, $object)
	{
		$this->_strictModeValidateTypeOf($object);
		$this->registeredObjectArrays[$p_classname] = $object;
	}
	
	
	/**
	 * @return void
	 */
	public function clearRegister()
	{
		$this->registeredObjectArrays = array();
	}
	
	
	/**
	 * @param string $p_classname
	 *
	 * @return object
	 */
	protected function _getObjectFromRegister($p_classname)
	{
		if(isset($this->registeredObjectArrays[$p_classname]) == false)
		{
			throw new LogicException('Requested object [' . htmlentities($p_classname)
			                         . '] is not registered. RegisteredObjectsOnly is enabled.');
		}
		
		return $this->registeredObjectArrays[$p_classname];
	}
	
	
	/**
	 * @param string $p_classname
	 *
	 * @return object
	 */
	protected function _getObjectFromMainFactory($p_classname)
	{
		return MainFactory::create($p_classname);
	}
	
	
	/**
	 * @param $object
	 *
	 * @throws InvalidArgumentException
	 */
	protected function _strictModeValidateTypeOf($object)
	{
		if($this->strictModeEnabled)
		{
			if(is_a($object, 'CrossCuttingObjectInterface') == false)
			{
				throw new InvalidArgumentException('Implemented CrossCuttingObjectInterface expected in strict mode.');
			}
		}
	}
}
