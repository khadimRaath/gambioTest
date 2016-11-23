<?php
/* --------------------------------------------------------------
   api.php 2016-01-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Gambio GX2 - API (implemented with Slim Framework)
 *
 * @link http://www.slimframework.com
 *
 * Hit this file directly with new requests and it will route them to their corresponding API
 * controllers. Controller files reside inside the "GXEngine/Controllers/Api" directory and are
 * separated by version. This separation enables the addition of newer API versions in the future.
 *
 * Since v2 the shop API is RESTful and that means that it supports a variety of HTTP methods
 * in order to implement a semantic interface for client developers. You can use one of the GET,
 * POST, PUT, DELETE, PATCH, HEAD, OPTIONS methods in your controller classes. Check the
 * "HttpApiV2Controller" class for more information on how to create your own controller.
 *
 * @link http://en.wikipedia.org/wiki/Representational_state_transfer
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
 *
 * It is important that each API version is able to route the controllers differently because
 * the codebase will be more flexible and easy to maintain. Expand the current file with new
 * controller-routing rules for future versions.
 *
 * You can generate detailed API documentation through ApiDoc. It is a NodeJS command line tool
 * that parses specific DocBlock comments and creates rich content output. It's always preferable
 * that API methods are well-documented so that is easier for external developers to use them.
 *
 * @link http://apidocjs.com
 *
 * Version 2.0.0 of the API uses HTTP Basic Authentication and that means that authorization
 * credentials are transferred over the wire. Always use HTTPS when accessing the API.
 *
 * http://en.wikipedia.org/wiki/Basic_access_authentication
 */

// ----------------------------------------------------------------------------
// INITIALIZE API - SLIM FRAMEWORK
// ----------------------------------------------------------------------------

/**
 * API Version
 *
 * The current API version will be included within every response in the "X-API-Version" header so that
 * clients know which exact version they are using.
 *
 * @var string
 */
$version = '2.2.0';

/**
 * API Environment
 *
 * If the ".dev-environment" file is present it will override the API_V2_ENVIRONMENT value and
 * it will set the environment back to testing ('development' is only suitable for complete error display).
 *
 * @var string
 */
$environment = file_exists(__DIR__ . '/.dev-environment') ? 'test' : 'production';

switch($environment)
{
	case 'development': // Complete verbose (HTML) output when errors occur.
		$config = array(
			'mode'  => 'development',
			'debug' => true
		);
		break;
	case 'test': // Includes PHP errors in the response body (stack trace).
		$config = array(
			'mode'  => 'test',
			'debug' => false
		);
		break;

	case 'production': // Will display error info in JSON format but hide extra information.
		$config = array(
			'mode'  => 'production',
			'debug' => false
		);
		break;

	default:
		throw new Exception('Invalid APIv2 environment selected: ' . $environment);
}

$config['version'] = $version;

require __DIR__ . '/includes/application_top.php';

$api = new \Slim\Slim($config);

// ----------------------------------------------------------------------------
// CONTROLLER ROUTING FOR V2
// ----------------------------------------------------------------------------

$api->map('/v2(/:uri+)', function ($uri = array()) use ($api)
{
	$resourceName = explode('_', ucfirst($uri[0]));

	foreach($resourceName as &$section)
	{
		$section = ucfirst($section);
	}

	$resourceName = implode('', $resourceName);

	$controllerName = (!empty($uri)) ? $resourceName . 'ApiV2Controller' : HttpApiV2Controller::DEFAULT_CONTROLLER_NAME;

	// Check if the resource exists (there is no such method in MainFactory so we use the autoloader function).
	if(!class_exists($controllerName))
	{
		throw new HttpApiV2Exception('Resource not found.', 404);
	}

	$controller = MainFactory::create($controllerName, $api, $uri);
	$method     = strtolower($api->request->getMethod());
	$resource   = array($controller, $method);

	if(!is_callable($resource))
	{
		throw new HttpApiV2Exception('The requested resource is not supported by the API v2.', 405);
	}

	call_user_func($resource);
})->via('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'); // Supported request methods

// ----------------------------------------------------------------------------
// API ERROR HANDLING
// ----------------------------------------------------------------------------

$api->error(function (\Exception $ex) use ($api)
{
	$responseErrorCode = 500; // The default value for exceptions on server.

	if(is_a($ex, 'HttpApiV2Exception')) // An HttpApiException will contain a specific HTTP status code.
	{
		$responseErrorCode = $ex->getCode();
	}

	$api->response->setStatus($responseErrorCode);
	$api->response->headers->set('Content-Type', 'application/json');

	$response = array(
		'code'    => $ex->getCode(),
		'status'  => 'error',
		'message' => $ex->getMessage(),
		'request' => array(
			'method' => $api->request->getMethod(),
			'url'    => $api->request->getUrl(),
			'path'   => $api->request->getPath(),
			'uri'    => array(
				'root'     => $api->request->getRootUri(),
				'resource' => $api->request->getResourceUri()
			)
		)
	);

	// Provide error stack only in 'test' mode.
	if($api->config('mode') === 'test')
	{
		$response['error'] = array(
			'file'  => $ex->getFile(),
			'line'  => $ex->getLine(),
			'stack' => $ex->getTrace()
		);
	}

	if(defined(JSON_PRETTY_PRINT) && defined(JSON_UNESCAPED_SLASHES))
	{
		$responseJsonString = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}
	else
	{
		$responseJsonString = json_encode($response); // PHP v5.3
	}

	$api->response->write($responseJsonString);
});

// ----------------------------------------------------------------------------
// API EXECUTION
// ----------------------------------------------------------------------------

$api->run();
