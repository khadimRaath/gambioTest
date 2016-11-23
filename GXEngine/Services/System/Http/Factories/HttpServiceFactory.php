<?php
/* --------------------------------------------------------------
   HttpServiceFactory.inc.php 2016-07-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpServiceFactoryInterface');

/**
 * Class HttpFactory
 *
 * @category   System
 * @package    Http
 * @subpackage Factories
 * @implements HttpServiceFactoryInterface
 */
class HttpServiceFactory implements HttpServiceFactoryInterface
{
	/**
	 * Creates a new instance of the http service.
	 *
	 * @return HttpServiceInterface
	 */
	public function createService()
	{
		$httpContextFactory = $this->_createAbstractHttpContextFactory();
		$httpDispatcher     = $this->_createHttpDispatcher();

		return MainFactory::create('HttpService', $httpContextFactory, $httpDispatcher);
	}


	/**
	 * Creates and returns a new instance of the environment http view controller registry factory.
	 *
	 * @return EnvironmentHttpViewControllerRegistryFactory
	 */
	protected function _createAbstractHttpViewControllerRegistryFactory()
	{
		$dataCache = DataCache::get_instance(); 
		$classFinderSettings = MainFactory::create('EnvironmentClassFinderSettings');
		$classFinder = MainFactory::create('ClassFinder', $classFinderSettings, $dataCache);
		
		return MainFactory::create('EnvironmentHttpViewControllerRegistryFactory', $classFinder);
	}


	/**
	 * Creates and returns a new instance of the environment http context factory.
	 *
	 * @return EnvironmentHttpContextFactory
	 */
	protected function _createAbstractHttpContextFactory()
	{
		return MainFactory::create('EnvironmentHttpContextFactory');
	}


	/**
	 * Creates and returns a new instance of the http dispatcher.
	 *
	 * @return HttpDispatcherInterface
	 */
	protected function _createHttpDispatcher()
	{
		$httpContextReader         = $this->_createHttpContextReader();
		$httpViewControllerFactory = $this->_createHttpViewControllerFactory();

		return MainFactory::create('HttpDispatcher', $httpContextReader, $httpViewControllerFactory);
	}


	/**
	 * Creates and returns a new instance of the http context reader.
	 *
	 * @return HttpContextReaderInterface
	 */
	protected function _createHttpContextReader()
	{
		return MainFactory::create('HttpContextReader');
	}


	/**
	 * Creates and returns a new instance of the http response processor.
	 *
	 * @return HttpResponseProcessorInterface
	 */
	protected function _createHttpResponseProcessor()
	{
		return MainFactory::create('HttpResponseProcessor');
	}


	/**
	 * Creates and returns a new instance of the http view controller factory.
	 *
	 * @return HttpViewControllerFactoryInterface
	 */
	protected function _createHttpViewControllerFactory()
	{
		$httpViewControllerRegistryFactory = $this->_createAbstractHttpViewControllerRegistryFactory();
		$httpViewControllerRegistry        = $httpViewControllerRegistryFactory->create();

		$httpContextReader     = $this->_createHttpContextReader();
		$httpResponseProcessor = $this->_createHttpResponseProcessor();

		return MainFactory::create('HttpViewControllerFactory',
		                           $httpViewControllerRegistry,
		                           $httpContextReader,
		                           $httpResponseProcessor);
	}
}