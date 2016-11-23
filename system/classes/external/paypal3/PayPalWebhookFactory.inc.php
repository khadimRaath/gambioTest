<?php
/* --------------------------------------------------------------
	PayPalWebhookFactory.inc.php 2015-10-29
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Utility class for handling PayPal Webhooks (notifications)
 */
class PayPalWebhookFactory
{
	/**
	 * @var PayPalConfigurationStorage configuration
	 */
	protected $config;

	/**
	 * @var PayPalText language text phrase provider
	 */
	protected $text;

	/**
	 * constructor
	 */
	public function __construct()
	{
		$this->config = MainFactory::create('PayPalConfigurationStorage');
		$this->text = MainFactory::create('PayPalText');
	}

	/**
	 * retrieves list of supported event types from PayPal.
	 * @return array of strings
	 * @throws Exception if request fails or response cannot be parsed
	 */
	public function getAllEventTypes()
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('GET', '/v1/notifications/webhooks-event-types'));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception('Error retrieving webhook event types');
		}
		if($response->getResponseCode() != '200')
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception('ERROR retrieving webhook event types: '.$error_message);
		}

		$types = array();
		foreach($response_object->event_types as $event_type)
		{
			$types[] = $event_type->name;
		}
		return $types;
	}

	/**
	 * retrieves a list of all currently active Webhooks
	 * @return stdClass decoded JSON list of Webhooks
	 * @throws Exception if request fails
	 */
	public function listAllWebhooks()
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('GET', '/v1/notifications/webhooks'));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception($this->text->get_text('error_retrieving_webhooks'));
		}
		if($response->getResponseCode() != '200')
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception($this->text->get_text('error_retrieving_webhooks') .': '.$error_message);
		}

		return $response_object;
	}

	/**
	 * retrieves information about a specific Webhook identified by its ID
	 * @param string $webhookID
	 * @return stdClass decoded JSON response
	 */
	public function getWebhook($webhookID)
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('GET', '/v1/notifications/webhooks/'.$webhookID));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception($this->text->get_text('Error_retrieving_webhook'));
		}
		if($response->getResponseCode() != '200')
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception($this->text->get_text('error_retrieving_webhook') .': '.$error_message);
		}

		return $response_object;
	}

	/**
	 * registers a new Webhook to the shop's endpoint (for all types) and stores its ID in the configuration.
	 * @return stdClass decoded JSON response
	 */
	public function registerWebhook()
	{
		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');

		$webhookArray = array(
				//'url' => xtc_catalog_href_link('shop.php', 'do='.urlencode('PayPal/Webhook'), 'SSL'),
				'url' => HTTPS_CATALOG_SERVER . DIR_WS_CATALOG . 'shop.php?do=PayPal/Webhook',
				'event_types' => array(),
			);
		foreach($this->getAllEventTypes() as $eventType)
		{
			$webhookArray['event_types'][] = array(
					'name' => $eventType,
				);
		}
		$webhookJSON = $json->encodeUnsafe($webhookArray);
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('POST', '/v1/notifications/webhooks', $webhookJSON));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception($this->text->get_text('Error registering webhook'));
		}
		if($response->getResponseCode() != '201')
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception($this->text->get_text('error_registering_webhook') .': '.$error_message);
		}
		$this->config->set('webhook_id', $response_object->id);
		return $response_object;
	}

	/**
	 * deletes a Webhook
	 * @param string $webhookID ID of Webhook to be deleted
	 */
	public function deleteWebhook($webhookID)
	{
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('DELETE', '/v1/notifications/webhooks/'.$webhookID));
		$response = $ppRestService->performRequest($ppRestRequest);
		if($response->getResponseCode() != '204')
		{
			$response_object = $response->getResponseObject();
			if($response_object === false)
			{
				throw new Exception($this->text->get_text('Error_deleting_webhook') .', '. $this->text->get_text('no_valid_response_body'));
			}
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception($this->text->get_text('error_deleting_webhook').': '.$error_message);
		}
		if($webhookID == $this->config->get('webhook_id'))
		{
			$this->config->set('webhook_id', '');
		}
	}

	/**
	 * checks if the currently configured Webhook is valid an known at PayPal
	 * @return bool true if Webhook info could be retrieved from PayPal
	 */
	public function checkWebhook($webhookID = null)
	{
		if($webhookID === null)
		{
			$webhookID = $this->config->get('webhook_id');
		}
		if(empty($webhookID))
		{
			throw new Exception($this->text->get_text('webhook_id_missing'));
		}

		$hookIsOK = false;
		try
		{
			$this->getWebhook($webhookID);
			$hookIsOK = true;
		}
		catch(Exception $e)
		{
			$hookIsOK = false;
		}
		return $hookIsOK;
	}

	/**
	 * deletes and recreates a Webhook for the shop.
	 * @return stdClass response from registerWebhook()
	 */
	public function renewWebhook()
	{
		$webhookID = $this->config->get('webhook_id');
		if(!empty($webhookID))
		{
			if($this->checkWebhook($webhookID) === true)
			{
				$this->deleteWebhook($webhookID);
			}
			$this->config->set('webhook_id', '');
		}
		$webhookResponse = $this->registerWebhook();
		return $webhookResponse;
	}

	/**
	 * retrieves a list of Webhook events
	 * @param int $page_size
	 * @param string $start_time
	 * @param string $end_time
	 * @return stdClass decoded JSON response
	 */
	public function searchWebhooksEvents($page_size = 10, $start_time = null, $end_time = null)
	{
		$parameters = array(
			'page_size' => (int)$page_size
		);
		if($start_time !== null)
		{
			$startDateTime = new DateTime($start_time);
			$parameters['start_time'] = $startDateTime->format(DateTime::RFC3339);
		}
		if($end_time !== null)
		{
			$endDateTime = new DateTime($end_time);
			$parameters['end_time'] = $endDateTime->format(DateTime::RFC3339);
		}
		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('GET', '/v1/notifications/webhooks-events?'.http_build_query($parameters)));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception($this->text->get_text('error_retrieving_webhook_events'));
		}
		if($response->getResponseCode() != '200')
		{
			$error_message = $response_object->name.' '.$response_object->message;

			if(isset($response_object->details))
			{
				$error_message .= ', details: ';
				foreach($response_object->details as $detail)
				{
					$error_message .= $detail->field .': '.$detail->issue;
				}
			}
			throw new Exception($this->text->get_text('error_retrieving_webhook') .': '.$error_message);
		}

		return $response_object;
	}
}

