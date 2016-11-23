<?php

/* --------------------------------------------------------------
   ClassFinder.inc.php 2016-07-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Finder
 *
 * @category   System
 * @package    Shared
 * @subpackage ClassFinder
 */
class ClassFinder implements ClassFinderInterface
{
	/**
	 * @var array
	 */
	protected $availableClassesArray = array();
	
	/**
	 * @var array
	 */
	protected $allowedDirectories = array();
	
	/**
	 * @var array
	 */
	protected $disallowedDirectories = array();
	
	/**
	 * @var DataCache
	 */
	protected $dataCache;
	
	/**
	 * @var array
	 */
	protected $cachedResults; 
	
	/**
	 * Constructor
	 *
	 * @param ClassFinderSettingsInterface $settings
	 * @param DataCache $dataCache
	 */
	public function __construct(ClassFinderSettingsInterface $settings, DataCache $dataCache)
	{
		$this->availableClassesArray = $settings->getAvailableClasses();
		$this->allowedDirectories    = $settings->getAllowedDirectories();
		$this->disallowedDirectories = $settings->getDisallowedDirectories();
		$this->cachedResults = $dataCache->get_persistent_data('ClassFinder');
		$this->dataCache = $dataCache; 
	}
	
	
	/**
	 * Destructor 
	 * 
	 * Update the cache file with the latest results. 
	 */
	public function __destruct()
	{
		$this->dataCache->write_persistent_data('ClassFinder', $this->cachedResults); 
	}
	
	
	/**
	 * Returns an associative array with classes that have the given class in their parent list.
	 * Array format: [ClassName] => [ClassFullFilePath]
	 *
	 * @param string $parentClassName
	 *
	 * @return array
	 */
	public function findByParent($parentClassName)
	{
		if(empty($this->cachedResults[$parentClassName]))
		{
			$resultArray = array();
			foreach($this->availableClassesArray as $className => $classFile)
			{
				if($this->_hasNeededParent($className, $parentClassName) === true)
				{
					$resultArray[$className] = $classFile;
				}
			}	
			
			$this->cachedResults[$parentClassName] = $resultArray; 
		}
		else 
		{
			$resultArray = $this->cachedResults[$parentClassName]; 
		}
		
		return MainFactory::create('KeyValueCollection', $resultArray);
	}
	
	
	/**
	 * Returns an associative array with classes that implement the given interface.
	 * Array format: [ClassName] => [ClassFullFilePath]
	 *
	 * @param string $interfaceName
	 *
	 * @return array
	 *
	 * @throws RuntimeException
	 */
	public function findByInterface($interfaceName)
	{
		throw new RuntimeException('Not implemented yet.');
	}
	
	
	/**
	 * Checks if $className a sub-class of $neededParentClassName
	 *
	 * @param string $className
	 * @param string $neededParentClassName
	 *
	 * @return bool
	 */
	protected function _hasNeededParent($className, $neededParentClassName)
	{
		// May I load the class file?
		$classFile = $this->availableClassesArray[$className];
		if($this->_isLoadableClassFile($classFile) === false)
		{
			return false;
		}
		
		// Is the expected class inside?
		MainFactory::load_class($className);
		if(class_exists($className, false) === false)
		{
			return false;
		}
		
		// Does the class have the needed parent?
		$classParentsArray = class_parents($className, false);
		if(is_array($classParentsArray) === false || in_array($neededParentClassName, $classParentsArray) === false)
		{
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Checks if $classFile is allowed to be included
	 *
	 * @param string $classFile
	 *
	 * @return bool
	 */
	protected function _isLoadableClassFile($classFile)
	{
		if(substr($classFile, -8, 8) !== '.inc.php')
		{
			// File needs to end with ".inc.php"
			return false;
		}
		
		foreach($this->disallowedDirectories as $dirItem)
		{
			if(strpos($classFile, $dirItem) !== false)
			{
				// File is located in not allowed directory
				return false;
			}
		}
		
		foreach($this->allowedDirectories as $dirItem)
		{
			if(strpos($classFile, $dirItem) !== false)
			{
				// File is located in allowed directory
				return true;
			}
		}
		
		return false;
	}
	
}