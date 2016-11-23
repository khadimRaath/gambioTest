<?php
/* --------------------------------------------------------------
	PaPalWebhook.inc.php 2015-03-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * class representing an incoming Webhook event
 */
class PayPalWebhooksEvent
{
	/**
	 * @var string
	 */
	protected $json_body;

	/**
	 * @var stdClass
	 */
	protected $json_object;

	/**
	 * initialize from incoming JSON data
	 * @throws Exception if JSON cannot be parsed
	 */
	public function __construct($json_body)
	{
		$json_object = json_decode($json_body);
		if($json_object === false)
		{
			throw new Exception('cannot decode JSON');
		}
		$this->json_body = $json_body;
		$this->json_object = $json_object;
	}

	/**
	 * returns decoded JSON representation
	 */
	public function getEventObject()
	{
		return $this->json_object;
	}

	/**
	 * finds orders_id related to the events parent payment
	 * @return false|int orders_id or false if order not found
	 */
	public function getOrdersID()
	{
		$payment_id = $this->json_object->resource->parent_payment;
		$query = 'SELECT orders_id FROM orders_paypal_payments WHERE payment_id = \':payment_id\'';
		$query = strtr($query, array(':payment_id' => xtc_db_input($payment_id)));
		$result = xtc_db_query($query);
		$orders_id = false;
		while($row = xtc_db_fetch_array($result))
		{
			$orders_id = $row['orders_id'];
		}
		return $orders_id;
	}

	/**
	* verifies the HMAC signature of a Webhooks Event message
	* @throws PaypalWebhookSignatureException if message cannot be verified
	*/
	public function verifySignature($transmission_timestamp, $transmission_id, $certificate_url, $transmission_signature, $webhook_id = null)
	{
		if($webhook_id === null)
		{
			$configStorage = MainFactory::create('PayPalConfigurationStorage');
			$webhook_id = $configStorage->get('webhook_id');
		}
		$body = $this->json_body;
		$signature_input = array(
			$transmission_id,
			$transmission_timestamp,
			$webhook_id,
			sprintf('%u', crc32($body)),
		);
		$signature_data = implode('|', $signature_input);
		$decoded_signature = base64_decode($transmission_signature);
		$certificateRequest = MainFactory::create('RestRequest', 'GET', $certificate_url);
		$restService = MainFactory::create('RestService');
		$certificateResponse = $restService->performRequest($certificateRequest);
		$certificate = $certificateResponse->getResponseBody();
		$pubkey = openssl_pkey_get_public($certificate);
		if($pubkey === false)
		{
			throw new PaypalWebhookSignatureException('cannot extract public key from certificate');
		}
		$signature_verify_result_sha1 = openssl_verify($signature_data, $decoded_signature, $pubkey, OPENSSL_ALGO_SHA1);
		$signature_verify_result_sha256 = openssl_verify($signature_data, $decoded_signature, $pubkey, OPENSSL_ALGO_SHA256);
		if(!($signature_verify_result_sha1 === 1 || $signature_verify_result_sha256 === 1))
		{
			throw new PaypalWebhookSignatureException(sprintf('signature_data: %s', $signature_data));
		}
	}
}

class PaypalWebhookSignatureException extends Exception {}

