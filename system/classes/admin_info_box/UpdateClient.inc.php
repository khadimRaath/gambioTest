<?php
/* --------------------------------------------------------------
   UpdateClient.inc.php 2014-12-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Class UpdateClient
 */
class UpdateClient 
{
	protected $url = '';
	
	public function __construct()
	{
		$this->_setUrl();
	}

	public function load_url()
	{
		/* @var LoadUrl $loadUrl */
		$loadUrl = MainFactory::create_object('LoadUrl');

		$serverResponse = $loadUrl->load_url($this->url, array('Accept: application/json'), 'width="100%" height="85" scrolling="no" marginheight="8" marginwidth="0" frameborder="0"', false, false);
		$c_serverResponse = (string)$serverResponse;

		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$responseArray = $json->decode($c_serverResponse);

		return $responseArray;
	}

	protected function _setUrl()
	{
		include(DIR_FS_CATALOG . 'release_info.php');
				
		$url = 'https://www.gambio-support.de/updateinfo/';
		
		$getParamsArray = array();
		$getParamsArray[] = 'shop_version=' . rawurlencode($gx_version);
		$getParamsArray[] = 'shop_url=' . rawurlencode(HTTP_SERVER . DIR_WS_CATALOG);
		$getParamsArray[] = 'shop_key=' . rawurlencode(GAMBIO_SHOP_KEY);
		$getParamsArray[] = 'language=' . rawurlencode($_SESSION['language_code']);
		$getParamsArray[] = 'server_path=' . rawurlencode(rtrim(DIR_FS_CATALOG, '/'));
		
		$url .= '?' . implode('&', $getParamsArray);
		
		$this->url = $url;
	}
}
