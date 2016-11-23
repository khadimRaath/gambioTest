<?php

/* --------------------------------------------------------------
   AbstractApiV2Controller.inc.php 2016-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractApiV2Controller
 *
 * This class defines the inner core functionality of a ApiV2Controller. It contains the
 * initialization and request validation functionality that every controller must have.
 *
 * The functionality of this class is mark as private because child controllers must not alter
 * the state at this point but rather adjust to it. This will force them to follow the same
 * principles and methodologies.
 * 
 * Child API controllers can use the "init" method to initialize their common dependencies. 
 *
 * @category System
 * @package  ApiV2Controllers
 */
abstract class AbstractApiV2Controller
{
	/**
	 * Defines the default page offset for responses that return multiple items.
	 *
	 * @var int
	 */
	const DEFAULT_PAGE_ITEMS = 50;

	/**
	 * Default controller to be loaded when no resource was selected.
	 *
	 * @var string
	 */
	const DEFAULT_CONTROLLER_NAME = 'DefaultApiV2Controller';

	/**
	 * Defines the maximum request limit for an authorized client.
	 *
	 * @var int
	 */
	const DEFAULT_RATE_LIMIT = 5000;

	/**
	 * Defines the duration of an API session in minutes.
	 *
	 * @var int
	 */
	const DEFAULT_RATE_RESET_PERIOD = 15;

	/**
	 * Slim Framework instance is used to manipulate the request or response data.
	 *
	 * @var \Slim\Slim
	 */
	protected $api;

	/**
	 * Contains the request URI segments after the root api version segment.
	 *
	 * Example:
	 *    URI  - api.php/v2/customers/73/addresses
	 *    CODE - $this->uri[1]; // will return '73'
	 *
	 * @var array
	 */
	protected $uri;


	/**
	 * AbstractApiV2Controller Constructor
	 *
	 * Call this constructor from every child controller class in order to set the
	 * Slim instance and the request routes arguments to the class.
	 *
	 * @param \Slim\Slim $api Slim framework instance, used for request/response manipulation.
	 * @param array      $uri This array contains all the segments of the current request, starting from the resource.
	 *
	 * @deprecated The "__initialize" method will is deprecated and will be removed in a future version. Please use 
	 *             the new "init" for bootstrapping your child API controllers.
	 *                        
	 * @throws HttpApiV2Exception Through _validateRequest
	 */
	public function __construct(\Slim\Slim $api, array $uri)
	{
		$this->api = $api;
		$this->uri = $uri;

		if(method_exists($this, '__initialize')) // Method for child-controller initialization stuff (deprecated). 
		{
			$this->__initialize();
		}
		
		if(method_exists($this, 'init')) // Method for child-controller initialization stuff (new method). 
		{
			$this->init();
		}

		$this->_validateRequest();
		$this->_prepareResponse();
	}


	/**
	 * [PRIVATE] Validate request before proceeding with response.
	 *
	 * This method will validate the request headers, user authentication and other parameters
	 * before the controller proceeds with the response.
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @throws HttpApiV2Exception If validation fails - 415 Unsupported media type.
	 */
	private function _validateRequest()
	{
		$requestMethod = $this->api->request->getMethod();
		$contentType   = $this->api->request->headers->get('Content-Type');

		if(($requestMethod === 'POST' || $requestMethod === 'PUT' || $requestMethod === 'PATCH')
		   && empty($_FILES)
		   && $contentType !== 'application/json'
		)
		{
			throw new HttpApiV2Exception('Unsupported Media Type HTTP', 415);
		}

		$this->_authorize();
		$this->_setRateLimitHeader();
	}


	/**
	 * [PRIVATE] Prepare response headers.
	 *
	 * This method will prepare default attributes of the API responses. Further response
	 * settings must be set explicitly from each controller method separately.
	 *
	 * Not available to child-controllers (private method).
	 */
	private function _prepareResponse()
	{
		$this->api->response->setStatus(200);
		$this->api->response->headers->set('Content-Type', 'application/json; charset=utf-8');
		$this->api->response->headers->set('X-API-Version', 'v' . $this->api->config('version'));
		$this->api->response->headers->set('X-Shop-Version', 'v' . gm_get_conf('INSTALLED_VERSION'));
	}


	/**
	 * [PRIVATE] Authorize request with HTTP Basic Authorization
	 *
	 * Call this method in every API operation that needs to be authorized with the HTTP Basic
	 * Authorization technique.
	 *
	 * @link http://php.net/manual/en/features.http-auth.php
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @throws HttpApiV2Exception If request does not provide the "Authorization" header or if the
	 *                            credentials are invalid.
	 *
	 * @todo Use LoginService when it's implemented.
	 */
	private function _authorize()
	{
		if(!isset($_SERVER['PHP_AUTH_USER']))
		{
			$this->api->response->headers->set('WWW-Authenticate', 'Basic realm="Gambio GX2 APIv2 Login"');
			throw new HttpApiV2Exception('Unauthorized', 401);
		}

		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();

		$count = $db->get_where('customers', array(
			'customers_email_address' => $_SERVER['PHP_AUTH_USER'],
			'customers_password'      => md5($_SERVER['PHP_AUTH_PW']),
			'customers_status'        => 0
		))->num_rows();

		if($count === 0)
		{
			throw new HttpApiV2Exception('Invalid Credentials', 401);
		}
		// Credentials were correct, continue execution ...
	}


	/**
	 * [PRIVATE] Handle rate limit headers.
	 *
	 * There is a cache file that will store each user session and provide a security
	 * mechanism that will protect the shop from DOS attacks or service overuse. Each
	 * session will use the hashed "Authorization header" to identify the client. When
	 * the limit is reached a "HTTP/1.1 429 Too Many Requests" will be returned.
	 *
	 * Headers:
	 *   X-Rate-Limit-Limit     >> Max number of requests allowed.
	 *   X-Rate-Limit-Remaining >> Number of requests remaining.
	 *   X-Rate-Limit-Reset     >> UTC epoch seconds until the limit is reset.
	 *
	 * Important: This method will be executed in every API call and it might slow the
	 * response time due to filesystem operations. If the difference is significant
	 * then it should be optimized.
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @throws HttpApiV2Exception If request limit exceed - 429 Too Many Requests
	 */
	private function _setRateLimitHeader()
	{
		// Load or create cache file. 
		$cacheFilePath = DIR_FS_CATALOG . 'cache/gxapi_v2_sessions_' . FileLog::get_secure_token();
		if(!file_exists($cacheFilePath))
		{
			touch($cacheFilePath);
			$sessions = array();
		}
		else
		{
			$sessions = unserialize(file_get_contents($cacheFilePath));
		}

		// Clear expired sessions. 
		foreach($sessions as $index => $session)
		{
			if($session['reset'] < time())
			{
				unset($sessions[$index]);
			}
		}

		// Get session identifier from request. 
		$identifier = md5($this->api->request->headers->get('Authorization'));
		if(empty($identifier))
		{
			throw new HttpApiV2Exception('Remote address value was not provided.', 400);
		}

		// Check session entry, if not found create one.
		if(!isset($sessions[$identifier]))
		{
			$sessions[$identifier] = array(
				'limit'     => self::DEFAULT_RATE_LIMIT,
				'remaining' => self::DEFAULT_RATE_LIMIT,
				'reset'     => time() + (self::DEFAULT_RATE_RESET_PERIOD * 60)
			);
		}
		else if($sessions[$identifier]['remaining'] <= 0)
		{
			throw new HttpApiV2Exception('Request limit was reached.', 429);
		}

		// Set rate limiting headers to response. 
		$sessions[$identifier]['remaining']--;
		$this->api->response->headers->set('X-Rate-Limit-Limit', $sessions[$identifier]['limit']);
		$this->api->response->headers->set('X-Rate-Limit-Remaining', $sessions[$identifier]['remaining']);
		$this->api->response->headers->set('X-Rate-Limit-Reset', $sessions[$identifier]['reset']);

		file_put_contents($cacheFilePath, serialize($sessions));
	}
}