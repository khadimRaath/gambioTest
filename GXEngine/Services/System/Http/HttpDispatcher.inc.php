<?php
/* --------------------------------------------------------------
   HttpDispatcher.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpDispatcherInterface');

/**
 * Class HttpDispatcher
 *
 * @category   System
 * @package    Http
 * @implements HttpDispatcherInterface
 */
class HttpDispatcher implements HttpDispatcherInterface
{
	/**
	 * @var HttpContextReaderInterface
	 */
	protected $httpContextReader;

	/**
	 * @var HttpViewControllerFactoryInterface
	 */
	protected $httpViewControllerFactory;


	/**
	 * Initializes the http dispatcher.
	 *
	 * @param HttpContextReaderInterface         $httpContextReader
	 * @param HttpViewControllerFactoryInterface $httpViewControllerFactory
	 *
	 * @throws MissingControllerNameException If no controller is found.
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpViewControllerFactoryInterface $httpViewControllerFactory)
	{
		$this->httpContextReader         = $httpContextReader;
		$this->httpViewControllerFactory = $httpViewControllerFactory;
	}


	/**
	 * Dispatches the current http request.
	 * If the http request is valid and can get handled by a controller class, the controllers ::proceed
	 * method is invoked by the dispatcher. Otherwise, the method will throw a missing controller name exception.
	 *
	 * @param HttpContextInterface $httpContext Object which holds information about the current http context.
	 *
	 * @throws MissingControllerNameException When the http request context is invalid.
	 */
	public function dispatch(HttpContextInterface $httpContext)
	{
		$controllerName = $this->httpContextReader->getControllerName($httpContext);
		if(empty($controllerName))
		{
			throw new MissingControllerNameException('No controller name found in given HttpContext');
		}

		$controller = $this->httpViewControllerFactory->createController($controllerName);
		if($controller instanceof AdminStatusOnlyInterface)
		{
			$controller->validateCurrentAdminStatus();
		}
		$controller->proceed($httpContext);
	}
}
