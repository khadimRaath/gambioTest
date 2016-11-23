<?php
/* --------------------------------------------------------------
   ApiController.php 2016-02-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractApiV2Controller');

/**
 * Class HttpApiV2Controller
 *
 * Contains common functionality for all the GX2 APIv2 controllers. You can use the $api instance in the
 * child-controllers in order to gain access to request and response information. The $uri variable is an
 * array that contains the requested resource path.
 *
 * You can use a protected "__initialize" method in your child controllers for performing common operations
 * without overriding the parent constructor method.
 *
 * This class contains some private methods that define the core operations of each controller and should
 * not be called from a child-controller (like validation, authorization, rate limiting). The only way to
 * disable the execution of these methods is to override the controller.
 *
 * @see      AbstractApiV2Controller
 *
 * @todo     Add _cacheResponse() helper function which will cache request data and it will provide the required
 *           headers.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class HttpApiV2Controller extends AbstractApiV2Controller
{
	/**
	 * Sort response array with the "sort" GET parameter.
	 *
	 * This method supports nested sort values, so by providing a "+address.street" value
	 * to the "sort" GET parameter the records will be sort by street value in ascending
	 * order. Method supports sorting up to 5 fields.
	 *
	 * Important #1:
	 *    This method has some advantages and disadvantages over the classic database sort mechanism. First it
	 *    does not need mapping between the API fields and the database fields. Second it does not depend on
	 *    external system code to sort the response items, so if for example a domain-service does not support
	 *    sorting the result can still be sorted before sent to the client. The disadvantages are that it will
	 *    only support a predefined number of fields and this is a trade-off because the method should not use
	 *    the "eval" function, which will introduce security risks. Furthermore it might be a bit slower than
	 *    the database sorting.
	 *
	 * Important #2:
	 *    This method is using PHP's array_multisort which by default will sort strings in a case sensitive
	 *    manner. That means that strings starting with a capital letter will come before strings starting
	 *    with a lowercase letter.
	 *    http://php.net/manual/en/function.array-multisort.php
	 *
	 * Example:
	 *   // will sort ascending by customer ID and descending by customer company
	 *   api.php/v2/customers?sort=+id,-address.company
	 *
	 * @param array $response Passed by reference, contains an array of the multiple items
	 *                        that will returned as a response to the client.
	 */
	protected function _sortResponse(array &$response)
	{
		if($this->api->request->get('sort') === null)
		{
			return; // no sort parameter was provided
		}

		$params = explode(',', $this->api->request->get('sort'));

		for($i = 0; $i < 5; $i++)
		{
			$sort[$i] = array(
				'array'     => array_fill(0, count($response), ''),
				'direction' => SORT_ASC // default
			);
		}

		foreach($params as $paramIndex => &$param)
		{
			$fields = explode('.', substr($param, 1));

			foreach($response as $itemIndex => $item)
			{
				$value = $item;
				foreach($fields as $field)
				{
					$value = $value[$field];
				}

				$sort[$paramIndex]['direction']         = (substr($param, 0, 1) === '-') ? SORT_DESC : SORT_ASC;
				$sort[$paramIndex]['array'][$itemIndex] = $value;
			}
		}

		// Multisort array (currently supports up to 5 sort fields).
		array_multisort($sort[0]['array'], $sort[0]['direction'], $sort[1]['array'], $sort[1]['direction'],
		                $sort[2]['array'], $sort[2]['direction'], $sort[3]['array'], $sort[3]['direction'],
		                $sort[4]['array'], $sort[4]['direction'], $response);
	}


	/**
	 * Minimize response using the $fields parameter.
	 *
	 * APIv2 supports the GET "fields" parameter which enables the client to select the
	 * exact fields to be included in the response. It does not support nested fields,
	 * only first-level.
	 *
	 * You can provide both associative (single response item) or sequential (multiple response
	 * items) arrays and this method will adjust the links accordingly.
	 *
	 * @param array $response Passed by reference, it will be minified to the required fields.
	 */
	protected function _minimizeResponse(array &$response)
	{
		if($this->api->request->get('fields') === null)
		{
			return; // no minification parameter was provided
		}

		$fields = explode(',', $this->api->request->get('fields'));
		$map    = array();
		foreach($fields as $field)
		{
			$field       = array_shift(explode('.', $field)); // take only the first field
			$map[$field] = array();
		}

		// If $response array is associative then converted to sequential array.
		$revertBackToAssociative = false;
		if(key($response) !== 0 && !is_array($response[0]))
		{
			$response                = array($response);
			$revertBackToAssociative = true;
		}

		// Minimize all the items. 
		foreach($response as &$item)
		{
			$item = array_intersect_key($item, $map);
		}

		// Revert back to associative (if necessary).
		if($revertBackToAssociative)
		{
			$response = $response[0];
		}
	}


	/**
	 * Paginate response using the $page and $per_page GET parameters.
	 *
	 * One of the common functionalities of the APIv2 is the pagination and this can be
	 * easily achieved by this function which will update the response with the records
	 * that need to be returned. This method will automatically set the pagination headers
	 * in the response so that client apps can easily navigate through results.
	 *
	 * @param array $response Passed by reference, it will be paginated according to the provided parameters.
	 */
	protected function _paginateResponse(array &$response)
	{
		if($this->api->request->get('page') === null)
		{
			return; // no pagination parameter was provided
		}

		$limit          = ($this->api->request->get('per_page')
		                   !== null) ? $this->api->request->get('per_page') : self::DEFAULT_PAGE_ITEMS;
		$offset         = $limit * ((int)$this->api->request->get('page') - 1);
		$totalItemCount = count($response);
		$this->_setPaginationHeader($this->api->request->get('page'), $limit, $totalItemCount);
		$response = array_slice($response, $offset, $limit);
	}


	/**
	 * Include links to response resources.
	 *
	 * The APIv2 operates with simple resources that might be linked with other resources. This
	 * architecture promotes flexibility so that API consumers can have a simpler structure. This
	 * method will search for existing external resources and will add a link to the end of each
	 * resource.
	 *
	 * IMPORTANT: If for some reason you need to include custom links to your resources
	 * do not use this method. Include them inside your controller method manually.
	 *
	 * NOTICE #1: This method will only search at the first level of the resource. That means that
	 * nested ID values will not be taken into concern.
	 *
	 * NOTICE #2: You can provide both associative (single response item) or sequential (multiple response
	 * items) arrays and this method will adjust the links accordingly.
	 *
	 * @param array $response Passed by reference, new links will be appended into the end
	 *                        of each resource.
	 */
	protected function _linkResponse(array &$response)
	{
		if($this->api->request->get('disable_links') !== null || count($response) === 0)
		{
			return; // client does not require links
		}

		// Define the link mappings to the resources. 
		$map = array(
			'customerId' => 'customers',
			'addressId'  => 'addresses',
			'countryId'  => 'countries',
			'zoneId'     => 'zones',
			'ordersId'   => 'orders'
		);

		// If $response array is associative then converted to sequential array. 
		$revertBackToAssociative = false;
		if(key($response) !== 0 && !is_array($response[0]))
		{
			$response                = array($response);
			$revertBackToAssociative = true;
		}

		// Parse the resource results and add the links.
		foreach($response as &$item)
		{
			$links = array(); // will be appended to each resource

			foreach($map as $key => $resource)
			{
				if(array_key_exists($key, $item) && $item[$key] !== null)
				{
					$links[str_replace('Id', '', $key)] = GM_HTTP_SERVER . $this->api->request->getRootUri() . '/v2/'
					                                      . $resource . '/' . $item[$key];
				}
			}

			$item['_links'] = $links;
		}

		if($revertBackToAssociative)
		{
			$response = $response[0];
		}
	}


	/**
	 * Write JSON encoded response data.
	 *
	 * Use this method to write a JSON encoded, pretty printed and unescaped response to
	 * the client consumer. It is very important that the API provides pretty printed responses
	 * because it is easier for users to debug and develop.
	 *
	 * IMPORTANT: PHP v5.3 does not support the JSON_PRETTY_PRINT and JSON_UNESCAPED_SLASHES so
	 * this method will check for their existance and then use them if possible.
	 *
	 * @param array $response     Contains the response data to be written.
	 * @param int   $p_statusCode (optional) Provide a custom status code for the response, default 200 - Success.
	 */
	protected function _writeResponse(array $response, $p_statusCode = 200)
	{
		if($p_statusCode !== 200 && is_numeric($p_statusCode))
		{
			$this->api->response->setStatus((int)$p_statusCode);
		}

		if(defined('JSON_PRETTY_PRINT') && defined('JSON_UNESCAPED_SLASHES'))
		{
			$responseJsonString = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}
		else
		{
			$responseJsonString = json_encode($response); // PHP v5.3
		}

		$this->api->response->write($responseJsonString);
	}


	/**
	 * Map the sub-resource to another controller.
	 *
	 * Some API resources contain many subresources which makes the creation of a single
	 * controller class complicated and hard to maintain. This method will forward the
	 * request to a another controller by checking the provided criteria.
	 *
	 * Example:
	 *
	 * $criteria = array(
	 *   'items'  => 'OrdersItemsAttributesApiV2Controller',
	 *   'totals' => 'OrdersTotalsApiV2Controller'
	 * );
	 *
	 * Notice: Each controller should map a direct subresource and not deeper ones. This way
	 * every API controller is responsible to map its direct subresources.
	 *
	 *
	 * @param array $criteria An array containing the mapping criteria.
	 *
	 * @return bool Returns whether the request was eventually mapped.
	 *
	 * @throws HttpApiV2Exception If the subresource is not supported by the API.
	 */
	protected function _mapResponse(array $criteria)
	{
		$result = false;

		foreach($criteria as $subresource => $class)
		{
			for($i = count($this->uri) - 1; $i > 0; $i--)
			{
				if($subresource === $this->uri[$i])
				{
					$controller = MainFactory::create($class, $this->api, $this->uri);
					$method     = strtolower($this->api->request->getMethod());
					$resource   = array($controller, $method);

					if(!is_callable($resource))
					{
						throw new HttpApiV2Exception('The requested subresource is not supported by the API v2.', 400);
					}

					call_user_func($resource);

					$result = true;
					break 2; // Exit both loops.
				}
			}
		}

		return $result;
	}


	/**
	 * Perform a search on the response array.
	 *
	 * Normally the best way to filter the results is through the corresponding service but some times
	 * there is not specific method for searching the requested resource or subresource. When this is
	 * the case use this method to filter the results of the response before returning them back to the
	 * client.
	 *
	 * @param array  $response  Contains the response data to be written.
	 * @param string $p_keyword The keyword to be used for the search.
	 *
	 * @throws InvalidArgumentException If search keyword parameter is not a string.
	 */
	protected function _searchResponse(array &$response, $p_keyword)
	{
		if(!is_string($p_keyword))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string got ' . gettype($p_keyword)
			                                   . '): ' . $p_keyword);
		}

		if($p_keyword === '')
		{
			return; // do not perform the search
		}

		$searchResults = array();

		foreach($response as $item)
		{
			if(!is_array($item))
			{
				continue;
			}

			foreach($item as $key => $value)
			{
				if((is_string($value) || is_numeric($value)) && preg_match('/' . $p_keyword . '/i', $value) === 1)
				{
					$searchResults[] = $item;
					break;
				}
			}
		}

		$response = $searchResults;
	}


	/**
	 * Add location header to a specific response.
	 *
	 * Use this method whenever you want the "Location" header to point to an existing resource so that
	 * clients can use it to fetch that resource without having to generate the URL themselves.
	 *
	 * @param string $p_name
	 * @param int    $p_id
	 *
	 * @throws InvalidArgumentException If the arguments contain an invalid value.
	 */
	protected function _locateResource($p_name, $p_id)
	{
		if(!is_string($p_name))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string got ' . gettype($p_name)
			                                   . '): ' . $p_name);
		}

		if(!is_numeric($p_id))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected int got ' . gettype($p_id) . '): '
			                                   . $p_id);
		}

		$this->api->response->header('Location',
		                             $this->api->request->getUrl() . $this->api->request->getRootUri() . '/v2/'
		                             . $p_name . '/' . $p_id);
	}


	/**
	 * [PRIVATE] Set header pagination links.
	 *
	 * Useful for GET responses that return multiple items to the client. The client
	 * can use the links to navigate through the records without having to construct
	 * them on its own.
	 *
	 * @link http://www.w3.org/wiki/LinkHeader
	 *
	 * Not available to child-controllers (private method).
	 *
	 * @param int $p_currentPage    Current request page number.
	 * @param int $p_itemsPerPage   The number of items to be returned in each page.
	 * @param int $p_totalItemCount Total number of the resource items.
	 *
	 * @throws HttpApiV2Exception If one of the parameters are invalid.
	 */
	private function _setPaginationHeader($p_currentPage, $p_itemsPerPage, $p_totalItemCount)
	{
		if($p_itemsPerPage <= 0)
		{
			throw new HttpApiV2Exception('Items per page number must not be below 1.', 400);
		}

		$totalPages  = ceil($p_totalItemCount / $p_itemsPerPage);
		$linksArray  = array();
		$baseLinkUri = HTTP_SERVER . $this->api->request->getRootUri() . $this->api->request->getResourceUri();
		$getParams   = $this->api->request->get();

		if($p_currentPage > 1)
		{
			$getParams['page']   = 1;
			$linksArray['first'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="first"';

			$getParams['page']      = $p_currentPage - 1;
			$linksArray['previous'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="previous"';
		}

		if($p_currentPage < $totalPages)
		{
			$getParams['page']  = $p_currentPage + 1;
			$linksArray['next'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="next"';

			$getParams['page']  = $totalPages;
			$linksArray['last'] = '<' . $baseLinkUri . '?' . http_build_query($getParams) . '>; rel="last"';
		}

		$this->api->response->headers->set('Link', implode(',' . PHP_EOL, $linksArray));
	}
	
	
	/**
	 * @param string $jsonString The json formatted string which should be updated.
	 * @param string $property   The name or key of the property which should be updated.
	 * @param string $value      The new value which should be set.
	 *
	 * @return string The updated json formatted string.
	 */
	protected function _setJsonValue($jsonString, $property, $value)
	{
		$json = json_decode($jsonString);
		
		$json->$property = $value;
		
		return json_encode($json);
	}
}