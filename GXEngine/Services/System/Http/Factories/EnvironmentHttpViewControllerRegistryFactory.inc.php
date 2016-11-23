<?php
/* --------------------------------------------------------------
   EnvironmentHttpContextFactory.inc.php 2016-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractHttpContextFactory');

/**
 * Class EnvironmentHttpViewControllerRegistryFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 * @extends    AbstractHttpViewControllerRegistryFactory
 */
class EnvironmentHttpViewControllerRegistryFactory extends AbstractHttpViewControllerRegistryFactory
{
	/**
	 * @var ClassFinderInterface
	 */
	protected $classFinder;
	
	/**
	 * Initializes the HttpViewControllerRegistryFactory
	 *
	 * @param ClassFinderInterface $classFinder
	 *
	 */
	public function __construct(ClassFinderInterface $classFinder)
	{
		$this->classFinder = $classFinder;
	}	
	
	/**
	 * Creates and returns a new http view controller registry.
	 *
	 * @return HttpViewControllerRegistryInterface
	 */
	public function create()
	{
		$registry = MainFactory::create('HttpViewControllerRegistry');
		
		$this->_addAvailableControllers($registry);
		
		return $registry;
	}
	

	/**
	 * Adds new available controller to the registry.
	 *
	 * @param HttpViewControllerRegistryInterface $registry Registry object which adds the new controller entries.
	 */
	protected function _addAvailableControllers(HttpViewControllerRegistryInterface $registry)
	{
		$controllers = $this->classFinder->findByParent('HttpViewController');
		
		foreach($controllers->getArray() as $className => $classFile)
		{
			$callableControllerName = $this->_getShortenNameOrUseOriginal($className);
			$registry->set($callableControllerName, $className);
		}
	}
	
	
	protected function _getShortenNameOrUseOriginal($originalClassName)
	{
		$suffixToCut = 'Controller';
		
		if(substr($originalClassName, 0-strlen($suffixToCut), strlen($suffixToCut)) === $suffixToCut)
		{
			$shorten = substr($originalClassName, 0, strlen($originalClassName) - strlen($suffixToCut));
			return $shorten;
		}
		return $originalClassName;
	}
	
	
}
