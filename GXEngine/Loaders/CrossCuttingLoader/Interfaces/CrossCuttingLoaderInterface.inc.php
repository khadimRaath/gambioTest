<?php

/* --------------------------------------------------------------
   CrossCuttingLoaderInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface CrossCuttingLoaderInterface
 *
 * @category   System
 * @package    Loaders
 * @subpackage Interfaces
 */
interface CrossCuttingLoaderInterface
{
	/**
	 * In strict mode ($strictModeEnabled=true) this method accepts classes with implemented
	 * CrossCuttingObjectInterface only. Otherwise it throws an InvalidArgumentException.
	 *
	 * @param string $p_classname
	 *
	 * @return object
	 * @throws InvalidArgumentException
	 */
	public function getObject($p_classname);
	
	
	/**
	 * If RegisteredObjectsOnly is enabled, the loader returns only objects, that were registered by
	 * the registerObject method before. RegisteredObjectsOnly should be enabled in all unit tests.
	 *
	 * @param boolean $bool_status
	 */
	public function setRegisteredObjectsOnly($bool_status);
	
	
	/**
	 * @return boolean
	 */
	public function useRegisteredObjectsOnly();
	
	
	/**
	 * In strict mode ($strictModeEnabled=true) this method accepts classes with implemented
	 * CrossCuttingObjectInterface only. Otherwise it throws an InvalidArgumentException.
	 *
	 * @param string $p_classname
	 * @param object $object
	 *
	 * @throws InvalidArgumentException
	 */
	public function registerObject($p_classname, $object);
	
	
	/**
	 * @return void
	 */
	public function clearRegister();
}