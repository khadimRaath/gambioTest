<?php
/* --------------------------------------------------------------
   HttpViewController.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewControllerInterface');

/**
 * Class HttpViewController
 *
 * This class contains some helper methods for handling view requests. Be careful
 * always when outputting raw user data to HTML or when handling POST requests because
 * insufficient protection will lead to XSS and CSRF vulnerabilities.
 *
 * @link       http://en.wikipedia.org/wiki/Cross-site_scripting
 * @link       http://en.wikipedia.org/wiki/Cross-site_request_forgery
 *
 * @category   System
 * @package    Http
 * @implements HttpViewControllerInterface
 */
class HttpViewController implements HttpViewControllerInterface
{
	/**
	 * @var HttpContextReaderInterface
	 */
	protected $httpContextReader;

	/**
	 * @var HttpResponseProcessorInterface
	 */
	protected $httpResponseProcessor;

	/**
	 * @var ContentViewInterface
	 */
	protected $contentView;

	/**
	 * @var array
	 */
	protected $queryParametersArray;

	/**
	 * @var array
	 */
	protected $postDataArray;

	/**
	 * @var AssetCollectionInterface Contain the assets needed to be included in the view HTML.
	 */
	protected $assets;


	/**
	 * @param HttpContextReaderInterface     $httpContextReader
	 * @param HttpResponseProcessorInterface $httpResponseProcessor
	 * @param ContentViewInterface           $defaultContentView
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $defaultContentView)
	{
		$this->httpContextReader     = $httpContextReader;
		$this->httpResponseProcessor = $httpResponseProcessor;
		$this->contentView           = $defaultContentView;
		$this->assets                = MainFactory::create('AssetCollection');
		
		if(method_exists($this, 'init'))
		{
			$this->init(); // Initialization method for child controllers.
		}
	}


	/**
	 * Processes a http response object which is get by invoking an action method.
	 * The action method is determined by the http context reader instance and the current request context.
	 * Re-implement this method in child classes to enable XSS and CSRF protection on demand.
	 *
	 * @param HttpContextInterface $httpContext Http context object which hold the request variables.
	 *
	 * @see HttpResponseProcessorInterface::proceed
	 * @see HttpContextReaderInterface::getActionName
	 *
	 * @throws LogicException When no action method is found by the http context reader.
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->queryParametersArray = $this->httpContextReader->getQueryParameters($httpContext);
		$this->postDataArray        = $this->httpContextReader->getPostData($httpContext);

		$actionName = $this->httpContextReader->getActionName($httpContext);
		$response   = $this->_callActionMethod($actionName);

		$this->httpResponseProcessor->proceed($response);
	}


	/**
	 * Default action method.
	 * Every controller child class requires at least the default action method, which is invoked when
	 * the ::_getQueryParameterData('do') value is not separated by a trailing slash.
	 *
	 * Every action method have to return an instance which implements the http controller response interface.
	 *
	 * @return \HttpControllerResponseInterface
	 */
	public function actionDefault()
	{
		return MainFactory::create('HttpControllerResponse', '');
	}


	/**
	 * Invokes an action method by the given action name.
	 *
	 * @param string $actionName Name of action method to call, without 'action'-Suffix.
	 *
	 * @throws LogicException If no action method of the given name exists.
	 * @return HttpControllerResponseInterface Response message.
	 */
	protected function _callActionMethod($actionName)
	{
		if(empty($actionName))
		{
			$methodName = 'actionDefault';
		}
		else
		{
			$methodName = 'action' . $actionName;
		}

		if(method_exists($this, $methodName) === false)
		{
			throw new LogicException('Action method not found for: ' . htmlspecialchars($actionName));
		}

		$response = call_user_func(array($this, $methodName));

		return $response;
	}


	/**
	 * Renders and returns a template file.
	 *
	 * @param string $templateFile Template file to render.
	 * @param array  $contentArray Content array which represent the variables of the template.
	 *
	 * @return string Rendered template.
	 */
	protected function _render($templateFile, array $contentArray)
	{
		$this->contentView->set_content_template($templateFile);

		foreach($contentArray as $contentItemKey => $contentItemValue)
		{
			$this->contentView->set_content_data($contentItemKey, $contentItemValue);
		}

		return $this->contentView->get_html();
	}


	/**
	 * Creates and returns a key value collection which represent the global $_GET array.
	 *
	 * @return KeyValueCollection
	 */
	protected function _getQueryParametersCollection()
	{
		return MainFactory::create('KeyValueCollection', $this->queryParametersArray);
	}


	/**
	 * Creates and returns a key value collection which represent the global $_POST array.
	 *
	 * @return KeyValueCollection
	 */
	protected function _getPostDataCollection()
	{
		return MainFactory::create('KeyValueCollection', $this->postDataArray);
	}


	/**
	 * Returns the expected $_GET value by the given key name.
	 * This method is the object oriented layer for $_GET[$keyName].
	 *
	 * @param string $keyName Expected key of query parameter.
	 *
	 * @return string|null Either the expected value or null, of not found.
	 */
	protected function _getQueryParameter($keyName)
	{
		if(!array_key_exists($keyName, $this->queryParametersArray))
		{
			return null;
		}

		return $this->queryParametersArray[$keyName];
	}


	/**
	 * Returns the expected $_POST value by the given key name.
	 * This method is the object oriented layer for $_POST[$keyName].
	 *
	 * @param string $keyName Expected key of post parameter.
	 *
	 * @return string|null Either the expected value or null, of not found.
	 */
	protected function _getPostData($keyName)
	{
		if(!array_key_exists($keyName, $this->postDataArray))
		{
			return null;
		}

		return $this->postDataArray[$keyName];
	}


	/**
	 * Check if the $_POST['pageToken'] or $_GET['pageToken'] variable is provided and if it's valid.
	 *
	 * Example:
	 *   public function proceed(HttpContextInterface $httpContext)
	 *   {
	 *     parent::proceed($httpContext); // proceed http context from parent class
	 *     if($_SERVER['REQUEST_METHOD'] === 'POST')
	 *     {
	 *        $this->_validatePageToken(); // CSRF Protection
	 *     }
	 *   }
	 *
	 * @param string $customExceptionMessage (optional) You can specify a custom exception message.
	 *
	 * @throws Exception If the validation fails.
	 */
	protected function _validatePageToken($customExceptionMessage = null)
	{
		$pageToken = $_REQUEST['pageToken'];

		if($pageToken === null)
		{
			throw new Exception($customExceptionMessage ? : '$_POST["pageToken"] variable was not provided with the POST request.');
		}

		if(!$_SESSION['coo_page_token']->is_valid($pageToken))
		{
			throw new Exception($customExceptionMessage ? : 'Provided $_POST["pageToken"] variable is not valid.');
		}
	}
}