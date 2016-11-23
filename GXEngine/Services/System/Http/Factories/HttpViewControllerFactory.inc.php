<?php
/* --------------------------------------------------------------
   HttpViewControllerFactory.inc.php 2016-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewControllerFactoryInterface');

/**
 * Class HttpViewControllerFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 * @implements HttpViewControllerFactoryInterface
 */
class HttpViewControllerFactory implements HttpViewControllerFactoryInterface
{
	/**
	 * @var HttpViewControllerRegistryInterface
	 */
	protected $httpViewControllerRegistry;

	/**
	 * @var HttpContextReaderInterface
	 */
	protected $httpContextReader;

	/**
	 * @var HttpResponseProcessorInterface
	 */
	protected $httpResponseProcessor;


	/**
	 * Initialize the http view controller factory.
	 *
	 * @param HttpViewControllerRegistryInterface $httpViewControllerRegistry Object which holds the registered
	 *                                                                         controller class names.
	 * @param HttpContextReaderInterface          $httpContextReader          Object to read the http context.
	 * @param HttpResponseProcessorInterface      $httpResponseProcessor      Object to process the http response.
	 */
	public function __construct(HttpViewControllerRegistryInterface $httpViewControllerRegistry,
	                            HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor)
	{
		$this->httpViewControllerRegistry = $httpViewControllerRegistry;
		$this->httpContextReader          = $httpContextReader;
		$this->httpResponseProcessor      = $httpResponseProcessor;
	}


	/**
	 * Creates a new instance of a http view controller by the given controller name.
	 *
	 * @param string $controllerName Expected name of controller (without 'Controller'-Suffix)
	 *
	 * @throws LogicException If the controller is not registered in the http view controller registry or the
	 *                         controller does not implement the http view controller interface.
	 *
	 * @return HttpViewControllerInterface Created controller instance.
	 */
	public function createController($controllerName)
	{
		$className   = $this->_getControllerClassName($controllerName);
		$contentView = $this->_createControllerContentView($controllerName);

		$controller = MainFactory::create($className,
		                                  $this->httpContextReader,
		                                  $this->httpResponseProcessor,
		                                  $contentView);

		return $controller;
	}


	/**
	 * Returns the class name of a http controller by the given controller name.
	 *
	 * @param string $controllerName Name of the http controller class.
	 *
	 * @throws LogicException When the found controller class does not implement the http view controller interface.
	 *
	 * @return string Class name of the http controller.
	 */
	protected function _getControllerClassName($controllerName)
	{
		$className = $this->httpViewControllerRegistry->get($controllerName);

		if(empty($className))
		{
			throw new LogicException('No controller class found for [' . htmlentities($controllerName) . ']');
		}

		if(in_array('HttpViewControllerInterface', class_implements($className)) == false)
		{
			throw new LogicException('HttpViewControllerInterface not implemented in called controller class ['
			                         . htmlentities($controllerName)
			                         . ']');
		}

		return $className;
	}


	/**
	 * Creates and returns the content view for a http view controller by the controller name.
	 *
	 * @param string $controllerName Name of the http controller class.
	 *
	 * @throws LogicException When the content view does not implement the content view interface.
	 *
	 * @return ContentViewInterface Content view instance for the http view controller.
	 */
	protected function _createControllerContentView($controllerName)
	{
		$contentViewClassName = $controllerName . 'ContentView';

		if(class_exists($contentViewClassName) == false)
		{
			$contentView = MainFactory::create('ContentView');
			$contentView->set_flat_assigns(true);

			return $contentView;
		}

		if(in_array('ContentViewInterface', class_implements($contentViewClassName)) == false)
		{
			throw new LogicException('ContentViewInterface not implemented in found ContentView class for called controller ['
			                         . htmlentities($controllerName)
			                         . ']');
		}

		return MainFactory::create($contentViewClassName);
	}
}