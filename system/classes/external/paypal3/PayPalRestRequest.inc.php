<?php
/* --------------------------------------------------------------
	PayPalRestRequest.inc.php 2015-02-18
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Subclass of RestRequest implementing common features of PayPal REST requests
 */
class PayPalRestRequest extends RestRequest
{
	/**
	 * @var PayPalConfigurationStorage configuration
	 */
	protected $configStorage;

	/**
	 * @var string access token for this request
	 */
	protected $access_token;

	/**
	 * @var int timestamp indicating the expiration of $access_token
	 */
	protected $access_token_expiration;

	/**
	 * @var bool flag indicating whether the access token is cached in $_SESSION between requests
	 */
	protected $use_token_cache = true;

	/**
	 * client identification for classic payments (ECM/ECS)
	 */
	const BN_ID_EC = 'Gambio_Cart_REST_EC';

	/**
	 * client identification for Plus payments
	 */
	const BN_ID_PLUS = 'Gambio_Cart_REST_Plus';

	/**
	 * constructor; prepares a new request.
	 * @param string $method HTTP method (GET|PUT|POST|PATCH|DELETE)
	 * @param string $url request URL
	 * @param string|array $data data to be sent in message body
	 * @param bool $plus flag indicating a request for a Plus payment
	 */
	public function __construct($method, $url, $data = null, $plus = false)
	{
		$this->configStorage = MainFactory::create_object('PayPalConfigurationStorage');
		$mode = $this->configStorage->get('mode');

		if($plus === true)
		{
			$bn_id = self::BN_ID_PLUS;
		}
		else
		{
			$bn_id = self::BN_ID_EC;
		}

		$headers = array(
				'Authorization: Bearer '.$this->getAccessToken(),
				'Accept: application/json',
				'Accept-Language: en_US',
				'Content-Type: application/json',
				'PayPal-Partner-Attribution-Id: '.$bn_id,
				'Expect:',
			);

		$this->setMethod($method);
		if(substr($url, 0, 8) != 'https://')
		{
			$url = $this->configStorage->get('service_base_url/'.$mode).$url;
		}
		$this->setURL($url);
		$this->setData($data);
		$this->setHeaders($headers);
	}

	/**
	 * retrieves an access token from PayPal.
	 * This gets called by the constructor, the access token is then added to the headers used in the actual request.
	 * @return string the access token
	 * @throws Exception if the token cannot be retrieved
	 */
	public function getAccessToken()
	{
		if($this->use_token_cache === true && !empty($_SESSION['paypal_access_token']) && !empty($_SESSION['paypal_access_token_expiration']) && time() <= $_SESSION['paypal_access_token_expiration'])
		{
			$this->access_token = $_SESSION['paypal_access_token'];
			$this->access_token_expiration = $_SESSION['paypal_access_token_expiration'];
		}

		if($this->use_token_cache !== true || empty($this->access_token) || time() >= $this->access_token_expiration)
		{
			$mode = $this->configStorage->get('mode');
			$url = $this->configStorage->get('service_base_url/'.$mode).'/v1/oauth2/token';
			$client_id = $this->configStorage->get('restapi-credentials/'.$mode.'/client_id');
			$secret = $this->configStorage->get('restapi-credentials/'.$mode.'/secret');
			if(empty($client_id) || empty($secret))
			{
				$txt = MainFactory::create_object('PayPalText');
				$message = $txt->get_text('credentials_incomplete');
				throw new Exception($message);
			}

			$req = MainFactory::create_object('RestRequest', array('POST', $url));
			$req->setUserpass($client_id.':'.$secret);
			$headers = array(
					'Accept: application/json',
					'Accept-Language: en_US',
					'Content-Type: application/x-www-form-urlencoded',
				);
			$req->setHeaders($headers);
			$data = array('grant_type' => 'client_credentials');
			$req->setData($data);

			$service = MainFactory::create_object('PayPalRestService');
			$response = $service->performRequest($req);
			if($response->getResponseCode() >= 300)
			{
				throw new Exception('Communication error in token retrieval ('.$response->getResponseCode().')');
			}
			#$response_parsed = json_decode($response->getResponseBody());
			$response_parsed = $response->getResponseObject();
			if(isset($response_parsed->error))
			{
				throw new Exception((string)$response_parsed->error.' - '.(string)$response_parsed->error_description);
			}
			$this->access_token = $response_parsed->access_token;
			$_SESSION['paypal_access_token'] = $this->access_token;
			$this->access_token_expiration = time() + $response_parsed->expires_in;
			$_SESSION['paypal_access_token_expiration'] = $this->access_token_expiration;
		}
		return $this->access_token;
	}

}

