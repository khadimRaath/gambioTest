<?php
/* --------------------------------------------------------------
	ShipcloudRestRequest.inc.php 2015-12-15
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Implements a REST request with extensions specific to Shipcloud
 */
class ShipcloudRestRequest extends RestRequest
{
	/**
	 * @var ShipcloudConfigurationStorage $configStorage Shipcloud configuration
	 */
	protected $configStorage;

	/**
	 * Initializes a request
	 *
	 * @param string $method request method
	 * @param string $url URL for request
	 * @param mixed $data data for request body (JSON string or array)
	 */
	public function __construct($method, $url, $data = null)
	{
		$this->configStorage = MainFactory::create_object('ShipcloudConfigurationStorage');
		$mode                = $this->configStorage->get('mode');
		$apiKey              = $this->configStorage->get('api-key/'.$mode);
		$headers             = array(
								'Content-Type: application/json',
								'Affiliate-ID: integration.gambio.FZasT8Ao',
								);
		$this->setMethod($method);
		if(substr($url, 0, 8) != 'https://')
		{
			$url = $this->configStorage->get('service_base_url').$url;
		}
		$this->setURL($url);
		$this->setData($data);
		$this->setHeaders($headers);
		$this->setUserPass($apiKey.':');
	}

	/**
	 * sets data for request body
	 *
	 * @param mixed $data JSON string or array (will be converted to JSON)
	 */
	public function setData($data)
	{
		if(is_array($data))
		{
			require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
			$jsonService = new Services_JSON();
			$data        = $jsonService->encodeUnsafe($data);
		}
		return parent::setData($data);
	}
}
