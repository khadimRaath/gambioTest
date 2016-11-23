<?php
/* --------------------------------------------------------------
   DynamicShopMessages.inc.php 2016-07-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class DynamicShopMessages
 *
 * This controller fetches the dynamic shop messages from the shop portal through a CURL request. It will only perform
 * the request once a day and use the data cache for this reason (performance).
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class DynamicShopMessagesController extends AdminHttpViewController
{
	/**
	 * @var DataCache
	 */
	protected $dataCache;
	
	/**
	 * @var string
	 */
	protected $cacheKey = 'dynamic-shop-messages';
	
	
	/**
	 * Initialize Controller
	 */
	public function init()
	{
		$this->dataCache = DataCache::get_instance();
	}
	
	
	/**
	 * Default controller callback. 
	 * 
	 * @return JsonHttpControllerResponse
	 */
	public function actionDefault()
	{
		try
		{
			// Check if a there is a cached response. 
			$jsonString = $this->_getCacheResponse();
			
			if($jsonString === false)
			{
				$jsonString = $this->_getRequestResponse(); // There is no cached response so make a new request.
			}
			
			$response = json_decode($jsonString, true);
		}
		catch(Exception $ex)
		{
			$response = AjaxException::response($ex);
		}
		
		if($response === null)
		{
			$response = array(
				'SOURCES'  => array(),
				'MESSAGES' => array()
			); // We must not pass a null value to the JsonHttpControllerResponse object.
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Get the cached response.
	 *
	 * @return bool|string Returns the JSON string or false if the cache is outdated.
	 */
	protected function _getCacheResponse()
	{
		if(!$this->dataCache->key_exists($this->cacheKey, true))
		{
			return false; // There is no cache file. 
		}
		
		$cacheData = $this->dataCache->get_persistent_data($this->cacheKey);
		
		return date('Ymd') ===  date('Ymd', $cacheData['timestamp']) ? $cacheData['response'] : false;
	}
	
	
	/**
	 * Get dynamic messages with cURL request to Gambio's servers.
	 *
	 * @return bool|string Returns the response JSON string or false if an error occurred.
	 */
	protected function _getRequestResponse()
	{
		include_once DIR_FS_CATALOG . 'release_info.php';
		
		// Create data source URL.
		$params = array(
			'shop_version' => rawurlencode($gx_version),
			'news_type'    => 'DOM'
		);
		
		if(gm_get_conf('SHOP_KEY_VALID') === '1')
		{
			$params['shop_url'] = rawurlencode(HTTP_SERVER . DIR_WS_CATALOG);
			$params['shop_key'] = rawurlencode(GAMBIO_SHOP_KEY);
			$params['language'] = rawurlencode($_SESSION['language_code']);
		}
		
		$url = 'https://www.gambio-support.de/updateinfo/?' . implode('&', $params);
		
		$loadUrl = MainFactory::create('LoadUrl');
		
		$jsonString = $loadUrl->load_url($url, array('Accept: application/json'), '', false, false);
		
		$cacheData = [
			'timestamp' => time(),
			'response'  => $jsonString
		];
		
		$this->dataCache->write_persistent_data($this->cacheKey, $cacheData);
		
		return $jsonString;
	}
}