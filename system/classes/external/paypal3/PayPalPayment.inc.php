<?php
/* --------------------------------------------------------------
	PayPalPayment.inc.php 2015-02-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This class represents PayPal Payments (made through the REST API).
 *
 * @property string $id Payment ID
 * @property string $create_time Creation time
 * @property string $update_time Modification time
 * @property string $state current state of payment
 * @property string $intent payment intent (sale/authorization/order)
 * @property stdClass $payer payer data
 * @property stdClass $transactions list of associated transactions
 * @property stdClass $links list of HATEOAS links
 * @property stdClass $json_object decoded JSON representation of payment
 */
class PayPalPayment
{
	/**
	 * @var stdClass decoded JSON data
	 */
	protected $json_object;

	/**
	 * @var PayPalEncodingHelper $encHelper
	 */
	protected $encHelper;

	/**
	 * initialize Payment from Payment ID or decoded JSON data.
	 * If parameter is a payment ID, data will be retrieved from PayPal.
	 * @param null|string|stdClass $payment_id_or_object Payment ID
	 */
	public function __construct($payment_id_or_object)
	{
		$this->encHelper = MainFactory::create('PayPalEncodingHelper');
		if($payment_id_or_object !== null)
		{
			if(is_string($payment_id_or_object))
			{
				$this->json_object = $this->retrievePayment($payment_id_or_object);
			}
			elseif(is_object($payment_id_or_object) && $payment_id_or_object instanceof stdClass)
			{
				$this->json_object = $payment_id_or_object;
			}
		}
	}

	public function __get($name)
	{
		switch($name)
		{
			case 'id':
			case 'create_time':
			case 'update_time':
			case 'state':
			case 'intent':
			case 'payer':
			case 'transactions':
			case 'links':
				$value = $this->json_object->$name;
				break;
			case 'payment_instruction':
				if(isset($this->json_object->$name))
				{
					$value = $this->json_object->$name;
				}
				else
				{
					$value = null;
				}
				break;
			case 'json_object':
				$value = $this->json_object;
				break;
			default:
				$value = null;
		}
		return $value;
	}

	public function __isset($name)
	{
		$isset = isset($this->json_object->$name);
		return $isset;
	}

	/**
	 * retrieves Payment data from PayPal
	 * @param string $payment_id a Payment ID
	 * @return stdClass decoded JSON representation of Payment data
	 */
	protected function retrievePayment($payment_id)
	{
		$ppRestService = MainFactory::create('PayPalRestService');
		$ppRestRequest = MainFactory::create('PayPalRestRequest', 'GET', '/v1/payments/payment/'.$payment_id);
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			throw new Exception('Error retrieving payment \''.$payment_id.'\'');
		}
		return $response_object;
	}

	/**
	 * executes an approved payment
	 * @param string $payerId Payer ID as returned by PayPal in the query string
	 * @param order $order the order to which this payment belongs
	 * @param bool $isPlus true if the payment is being made through PayPal Plus
	 * @return stdClass decoded JSON representation of payment data
	 * @throws PayPalPaymentInstrumentDeclinedException if the payment cannot be executed, e.g. due to insufficient funds
	 * @throws Exception if an error occurs in the communication with PayPal
	 */
	public function execute($payerId, order $order = null, $isPlus = false, $state = null)
	{
		require_once 'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');

		$executeLink = $this->getLink('execute');
		$requestDataArray = array("payer_id" => $payerId);
		if($order !== null)
		{
			$subtotal = $this->transactions[0]->amount->details->subtotal;
			$tax = 0;
			if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
			{
				$total_data = $order->getTotalData($order->info['orders_id']);
				foreach($total_data['data'] as $ot_data)
				{
					if($ot_data['CLASS'] == 'ot_tax')
					{
						$tax = $ot_data['VALUE'];
					}
				}
			}

			$requestDataArray['transactions'] = array(
					array(
						'amount' => array(
								'currency' => $order->info['currency'],
								'total' => $this->_formatAmount($order->info['pp_total'], $order->info['currency']),
								'details' => array(
									'subtotal' => $this->_formatAmount($subtotal, $order->info['currency']),
									'tax' => $this->_formatAmount($tax, $order->info['currency']),
									'shipping' => $this->_formatAmount($order->info['pp_shipping'], $order->info['currency']),
									)
							),
					),
				);

			$remaining_fee = $order->info['pp_total'] - $order->info['pp_shipping'] - $subtotal - $tax;
			$remaining_fee = round($remaining_fee, 2);
			if($remaining_fee > 0)
			{
				$requestDataArray['transactions'][0]['amount']['details']['handling_fee'] = $this->_formatAmount($remaining_fee, $order->info['currency']);
			}
			elseif($remaining_fee < 0)
			{
				$requestDataArray['transactions'][0]['amount']['details']['shipping_discount'] = $this->_formatAmount($remaining_fee, $order->info['currency']);
			}
		}
		$requestData = $json->encodeUnsafe($requestDataArray);

		$ppRestService = MainFactory::create('PayPalRestService');
		$ppRestRequest = MainFactory::create('PayPalRestRequest', $executeLink->method, $executeLink->href, $requestData, $isPlus);
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			$errorMessage = $response_object->name.'/'.$response_object->message;
			if($response_object->name == 'INSTRUMENT_DECLINED')
			{
				throw new PayPalPaymentInstrumentDeclinedException($errorMessage);
			}
			throw new Exception($errorMessage);
		}
		$this->json_object = $response_object;
	}

	/**
	 * returns a HATEOAS link structure
	 * @param string $type link type (rel attribute)
	 * @return stdClass link data (with properties rel, href, method)
	 */
	public function getLink($type = 'approval_url')
	{
		$link = false;
		foreach($this->json_object->links as $link_entry)
		{
			if($link_entry->rel == $type)
			{
				$link = $link_entry;
			}
		}
		return $link;
	}

	/**
	 * refunds a given amount for a related sale/capture identified by its ID.
	 * Currency is taken from sale/capture data.
	 * @param string $resource_id ID of related resource
	 * @param double $amount amount to be refunded
	 * @throws Exception if refund cannot be initiated
	 */
	public function refund($resource_id, $amount)
	{
		$amount = (double)$amount;
		try
		{
			$resource = $this->findSale($resource_id);
		}
		catch(Exception $sale_exception)
		{
			$resource = $this->findCapture($resource_id);
		}

		$refund_url = false;
		foreach($resource->links as $resource_link)
		{
			if($resource_link->rel == 'refund')
			{
				$refund_url = $resource_link;
			}
		}
		if($refund_url === false)
		{
			throw new Exception('Payment cannot be refunded.');
		}

		$refundRequestArray = array(
				'amount' => array(
					'total' => $this->_formatAmount($amount, $resource->amount->currency),
					'currency' => $resource->amount->currency,
					)
			);
		require_once DIR_FS_CATALOG.'/gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$refundRequestJSON = $json->encodeUnsafe($refundRequestArray);

		$ppRefundRequest = MainFactory::create('PayPalRestRequest', $refund_url->method, $refund_url->href, $refundRequestJSON);
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppRefundRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '201')
		{
			throw new Exception('Error refunding payment: '.$response_object->name.' '.$response_object->message);
		}
	}

	/**
	 * captures an amount for a given authorization identified by its ID, optionally finalizing the authorization.
	 * @param string $authorization_id ID of authorization
	 * @param double $amount amount to be captured
	 * @param bool $isFinalCapture true to make this the final capture on this authorization
	 * @throws Exception if capture cannot be initiated
	 */
	public function authorizationCapture($authorization_id, $amount, $isFinalCapture = false)
	{
		$authorization = $this->findAuthorization($authorization_id);
		$capture_url = false;
		foreach($authorization->links as $authorization_link)
		{
			if($authorization_link->rel == 'capture')
			{
				$capture_url = $authorization_link;
			}
		}
		if($capture_url === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('authorization_cannot_be_captured'));
		}

		$captureData = array(
				'amount' => array(
					'total' => $this->_formatAmount((double)$amount, $authorization->amount->currency),
					'currency' => $authorization->amount->currency,
				),
				'is_final_capture' => var_export((bool)$isFinalCapture, true),
			);
		require_once DIR_FS_CATALOG.'/gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$captureJSON = $json->encodeUnsafe($captureData);

		$ppCaptureRequest = MainFactory::create('PayPalRestRequest', $capture_url->method, $capture_url->href, $captureJSON);
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppCaptureRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_cannot_capture_authorization').': '.$response_object->name.' '.$response_object->message);
		}
	}

	/**
	 * re-authorizes an authorization with a given amount
	 * @param string $authorization_id
	 * @param double $amount
	 * @throws Exception if re-authorization cannot be initiated
	 */
	public function authorizationReauthorize($authorization_id, $amount)
	{
		$authorization = $this->findAuthorization($authorization_id);
		$reauthorize_url = false;
		foreach($authorization->links as $authorization_link)
		{
			if($authorization_link->rel == 'reauthorize')
			{
				$reauthorize_url = $authorization_link;
			}
		}
		if($reauthorize_url === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('authorization_cannot_be_reauthorized'));
		}

		$reauthorizeData = array(
				'amount' => array(
					'total' => $this->_formatAmount((double)$amount, $authorization->amount->currency),
					'currency' => $authorization->amount->currency,
				),
			);
		require_once DIR_FS_CATALOG.'/gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$reauthorizeJSON = $json->encodeUnsafe($reauthorizeData);

		$ppReauthorizeRequest = MainFactory::create('PayPalRestRequest', $reauthorize_url->method, $reauthorize_url->href, $reauthorizeJSON);
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppReauthorizeRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_cannot_reauthorize_authorization').': '.$response_object->name.' '.$response_object->message);
		}
	}

	/**
	 * voids an authorization
	 * @param string $authorization_id ID of authorization to be voided
	 * @throws Exception if authorization cannot be voided
	 */
	public function authorizationVoid($authorization_id)
	{
		$authorization = $this->findAuthorization($authorization_id);
		$void_url = false;
		foreach($authorization->links as $authorization_link)
		{
			if($authorization_link->rel == 'void')
			{
				$void_url = $authorization_link;
			}
		}
		if($void_url === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('authorization_cannot_be_voided'));
		}

		$ppAuthorizationVoidRequest = MainFactory::create('PayPalRestRequest', $void_url->method, $void_url->href, '{}');
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppAuthorizationVoidRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_cannot_void_authorization').': '.$response_object->name.' '.$response_object->message);
		}

	}

	/**
	 * authorizes an order
	 * @param string $order_id ID of order transaction (NOT a Gambio orders_id!)
	 * @param double $amount amount to be authorized
	 * @throws Exception if initiation of the authorization fails
	 */
	public function orderAuthorize($order_id, $amount)
	{
		$order = $this->findOrder($order_id);
		$order_url = false;
		foreach($order->links as $order_link)
		{
			if($order_link->rel == 'authorization')
			{
				$order_url = $order_link;
			}
		}
		if($order_url === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('order_cannot_be_authorized'));
		}

		$authorizeData = array(
				'amount' => array(
					'total' => $this->_formatAmount((double)$amount, $order->amount->currency),
					'currency' => $order->amount->currency,
				),
			);
		require_once DIR_FS_CATALOG.'/gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$authorizeJSON = $json->encodeUnsafe($authorizeData);

		$ppAuthorizeRequest = MainFactory::create('PayPalRestRequest', $order_url->method, $order_url->href, $authorizeJSON);
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppAuthorizeRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_cannot_authorize_order').': '.$response_object->name.' '.$response_object->message);
		}
	}

	/**
	 * captures an order
	 * @param string $order_id ID of order transaction (NOT a Gambio orders_id!)
	 * @param double $amount amount to be captured
	 * @param bool $isFinalCapture true to make this the final capture on this order
	 * @throws Exception if capture cannot be initiated
	 */
	public function orderCapture($order_id, $amount, $isFinalCapture = false)
	{
		$order = $this->findOrder($order_id);
		$capture_url = false;
		foreach($order->links as $order_link)
		{
			if($order_link->rel == 'capture')
			{
				$capture_url = $order_link;
			}
		}
		if($capture_url === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('order_cannot_be_captured'));
		}

		$captureData = array(
				'amount' => array(
					'total' => $this->_formatAmount((double)$amount, $order->amount->currency),
					'currency' => $order->amount->currency,
				),
				'is_final_capture' => var_export((bool)$isFinalCapture, true),
			);
		require_once DIR_FS_CATALOG.'/gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$captureJSON = $json->encodeUnsafe($captureData);

		$ppCaptureRequest = MainFactory::create('PayPalRestRequest', $capture_url->method, $capture_url->href, $captureJSON);
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppCaptureRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if(!in_array($response->getResponseCode(), array('200', '201')))
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_cannot_capture_order').': '.$response_object->name.' '.$response_object->message);
		}
	}

	/**
	 * voids an order
	 * @param string $order_id ID of order transaction (NOT a Gambio orders_id!)
	 * @throws Exception if order cannot be voided
	 */
	public function orderVoid($order_id)
	{
		$order = $this->findOrder($order_id);
		$void_url = false;
		foreach($order->links as $order_link)
		{
			if($order_link->rel == 'void')
			{
				$void_url = $order_link;
			}
		}
		if($void_url === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('order_cannot_be_voided'));
		}

		$ppOrderVoidRequest = MainFactory::create('PayPalRestRequest', $void_url->method, $void_url->href, '{}');
		$ppRestService = MainFactory::create('PayPalRestService');
		$response = $ppRestService->performRequest($ppOrderVoidRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_decoding_response'));
		}
		if($response->getResponseCode() != '200')
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('error_cannot_void_order').': '.$response_object->name.' '.$response_object->message);
		}
	}

	/**
	 * searches through transactions and related resources to find a sale resource by its ID
	 * @param string $sale_id Sale ID
	 * @return stdClass Sale data
	 */
	protected function findSale($sale_id)
	{
		$sale = false;
		foreach($this->json_object->transactions as $transaction)
		{
			foreach($transaction->related_resources as $resource)
			{
				foreach($resource as $type => $resource_data)
				{
					if($type == 'sale' && $resource_data->id == $sale_id)
					{
						$sale = $resource_data;
					}
				}
			}
		}

		if($sale === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('invalid_sale_id'));
		}
		return $sale;
	}

	/**
	 * searches through transactions and related resources to find a capture resource by its ID
	 * @param string $capture_id Capture ID
	 * @return stdClass Capture data
	 */
	protected function findCapture($capture_id)
	{
		$capture = false;
		foreach($this->json_object->transactions as $transaction)
		{
			foreach($transaction->related_resources as $resource)
			{
				foreach($resource as $type => $resource_data)
				{
					if($type == 'capture' && $resource_data->id == $capture_id)
					{
						$capture = $resource_data;
					}
				}
			}
		}

		if($capture === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('invalid_capture_id'));
		}
		return $capture;
	}

	/**
	 * searches through transactions and related resources to find an authorization resource by its ID
	 * @param string $authorization_id Authorization ID
	 * @return stdClass Authorization data
	 */
	protected function findAuthorization($authorization_id)
	{
		$authorization = false;
		foreach($this->json_object->transactions as $transaction)
		{
			foreach($transaction->related_resources as $resource)
			{
				foreach($resource as $type => $resource_data)
				{
					if($type == 'authorization' && $resource_data->id == $authorization_id)
					{
						$authorization = $resource_data;
					}
				}
			}
		}

		if($authorization === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('invalid_authorization_id'));
		}
		return $authorization;
	}

	/**
	 * searches through transactions and related resources to find an Order resource by its ID
	 * @param string $order_id Order ID
	 * @return stdClass Order data
	 */
	protected function findOrder($order_id)
	{
		$order = false;
		foreach($this->json_object->transactions as $transaction)
		{
			foreach($transaction->related_resources as $resource)
			{
				foreach($resource as $type => $resource_data)
				{
					if($type == 'order' && $resource_data->id == $order_id)
					{
						$order = $resource_data;
					}
				}
			}
		}

		if($order === false)
		{
			$text = MainFactory::create('PayPalText');
			throw new Exception($text->get_text('invalid_order_id'));
		}
		return $order;
	}

	/**
	 * formats payment amounts for transmission
	 */
	protected function _formatAmount($amount, $currency)
	{
		$amount = (double)$amount;
		$noDecimalsCurrencies = array('HUF', 'TWD');
		if(in_array($currency, $noDecimalsCurrencies))
		{
			$decimalDigits = 0;
		}
		else
		{
			$decimalDigits = 2;
		}
		$formattedAmount = number_format($amount, $decimalDigits, '.', '');
		return $formattedAmount;
	}
}

/**
 * Exception thrown by PayPalPayment::execute()
 */
class PayPalPaymentInstrumentDeclinedException extends Exception {};
