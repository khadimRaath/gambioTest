<?php
/* --------------------------------------------------------------
	PayPalPaymentFactory.inc.php 2016-07-19
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Factory class for the creation of new PayPal payments
 */
class PayPalPaymentFactory
{
	/**
	 * @var PayPalConfigurationStorace $config configuration
	 */
	protected $config;

	/**
	 * @var PayPalEncodingHelper $encHelper
	 */
	protected $encHelper;

	/**
	 * constructor; initializes configuration and encoding helper
	 */
	public function __construct()
	{
		$this->config = MainFactory::create('PayPalConfigurationStorage');
		$this->encHelper = MainFactory::create('PayPalEncodingHelper');
	}

	/**
	 * creates a PayPal payment from a Gambio order
	 * @param order $order an order object for which to create a payment
	 * @param string $mode payment mode (ecm|ecs|plus)
	 * @param string $state state for shipping address
	 * @return PayPalPayment newly created PayPalPayment object
	 * @throws Exception if payment cannot be created
	 */
	public function createPaymentFromOrder(order $order, $mode = 'ecm', $state = null)
	{
		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');

		$paymentArray = $this->makePaymentArrayFromOrder($order, $mode, $state);
		$paymentJSON = $json->encodeUnsafe($paymentArray);
		$isPlus = $mode == 'plus';

		$ppRestService = MainFactory::create_object('PayPalRestService');
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('POST', '/v1/payments/payment', $paymentJSON, $isPlus));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception('Error decoding response '.print_r($response, true));
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
			throw new Exception('ERROR creating payment: '.$error_message);
		}

		$paypalPayment = MainFactory::create_object('PayPalPayment', array($response_object));
		return $paypalPayment;
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


	/**
	 * prepares data to be encoded in JSON for the creation of a new order.
	 * In Plus mode only data directly related to the payment is added; customers personal data is added later.
	 * @param order $order
	 * @param string $mode payment mode (ecm|ecs|plus)
	 * @param string $state state for shipping address
	 * @return array data to be sent to PayPal
	 */
	public function makePaymentArrayFromOrder(order $order, $mode = 'ecm', $state = null, $countryCode = null)
	{
		$countryCode = empty($countryCode) ? $_SESSION['language_code'] : $countryCode;

		if($mode == 'plus')
		{
			$intent = 'sale';
		}
		else
		{
			$intent = $this->config->get('intent');
		}

		$itemsAndSubtotal = $this->makeItemsAndSubtotalFromOrder($order);
		$itemsArray = $itemsAndSubtotal['items'];
		$subtotal = $itemsAndSubtotal['subtotal'];

		$shippingCost = 0;
		$total = 0;

		$session_shipping = $_SESSION['shipping'];
		if(is_array($_SESSION['shipping']) && !is_numeric($_SESSION['shipping']['cost']))
		{
			$shipping = new shipping($_SESSION['shipping']);
			list($shipping_module, $shipping_method) = explode('_', $_SESSION['shipping']['id']);
			$quotes = $shipping->quote($shipping_method, $shipping_module);
			$_SESSION['shipping']['cost'] = (double)$quotes[0]['methods'][0]['cost'];
		}
		$globals_order = $GLOBALS['order'];
		$GLOBALS['order'] = new order();
		if(empty($_SESSION['payment']))
		{
			$_SESSION['payment'] = 'paypal3';
		}
		$t_order_total = new order_total();
		$t_order_total_array = $t_order_total->process();
		$total = $GLOBALS['order']->info['total'];
		$shippingCost = round($GLOBALS['order']->info['shipping_cost'], 2);
		$_SESSION['shipping'] = $session_shipping;
		$GLOBALS['order'] = $globals_order;

		$tax = 0;
		if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1)
		{
			$tax = $order->info['tax'];
			$total += round($tax, 2);
		}

		$paymentExperienceProfileId = $this->config->get('payment_experience_profile/'.$_SESSION['language_code']);
		$paymentArray = array(
				"intent" => $intent,
				"payer" => array(
					"payment_method" => "paypal",
				),
				"transactions" => array(
					array(
						"amount" => array(
							"currency" => $order->info['currency'],
							"total" => $this->_formatAmount($total, $order->info['currency']),
							"details" => array(
								"subtotal" => $this->_formatAmount($subtotal, $order->info['currency']),
								"tax" => $this->_formatAmount($tax, $order->info['currency']),
							),
						),
						"description" => substr($this->encHelper->transcodeOutbound(STORE_NAME), 0, 120),
						"item_list" => array(
							"items" => $itemsArray,
						),
					),
				),
				"redirect_urls" => $this->getRedirectUrls($mode),
			);
		if(!empty($paymentExperienceProfileId))
		{
			$paymentArray['experience_profile_id'] = $paymentExperienceProfileId;
		}

		if($shippingCost > 0)
		{
			$paymentArray['transactions'][0]['amount']['details']['shipping'] = $this->_formatAmount($shippingCost, $order->info['currency']);
		}

		$remaining_fee = round($total, 2) - round($shippingCost, 2) - round($subtotal, 2) - round($tax, 2);
		$remaining_fee = round($remaining_fee, 2);

		if($remaining_fee > 0)
		{
			$paymentArray['transactions'][0]['amount']['details']['handling_fee'] = $this->_formatAmount($remaining_fee, $order->info['currency']);
		}
		elseif($remaining_fee < 0)
		{
			$paymentArray['transactions'][0]['amount']['details']['shipping_discount'] = $this->_formatAmount($remaining_fee, $order->info['currency']);
		}


		if(!empty($order->delivery['lastname']) && $mode != 'plus')
		{
			$shippingCountryCode = substr($order->delivery['country']['iso_code_2'], 0, 2);
			$paymentArray['transactions'][0]['item_list']['shipping_address'] = array(
								"recipient_name" => substr($this->encHelper->transcodeOutbound($order->delivery['firstname'] .' '. $order->delivery['lastname']), 0, 50),
								"line1" => substr($this->encHelper->transcodeOutbound($order->delivery['street_address']), 0, 100),
								"city" => substr($this->encHelper->transcodeOutbound($order->delivery['city']), 0, 50),
								"postal_code" => substr($this->encHelper->transcodeOutbound($order->delivery['postcode']), 0, 20),
								"country_code" => $shippingCountryCode,
							);
			if(!empty($order->delivery['house_number']))
			{
				$paymentArray['transactions'][0]['item_list']['shipping_address']['line1'] .= ' ' . $order->delivery['house_number'];
			}
			if($state !== null)
			{
				$paymentArray['transactions'][0]['item_list']['shipping_address']['state'] = substr($state, 0, 100);
			}
			else
			{
				$paymentArray['transactions'][0]['item_list']['shipping_address']['state'] = substr($order->delivery['state'], 0, 100);
			}
		}
		return $paymentArray;
	}

	/**
	 * extracts data for line items and resulting subtotal from an order
	 * @param order $order
	 * @return array with keys 'items' (array of line items) and 'subtotal' (float)
	 */
	protected function makeItemsAndSubtotalFromOrder(order $order)
	{
		$subtotal = 0;
		$itemsArray = array();
		foreach($order->products as $product)
		{
			$quantity_name = $this->getQuantityName($product['quantity_unit_id'], $_SESSION['languages_id']);
			if(($product['qty'] - floor($product['qty'])) > 0)
			{
				$qty = 1;
				$qty_prefix = str_replace('.', ',', (string)$product['qty']);
				$price = $product['qty'] * $product['price'];
			}
			else
			{
				$qty = $product['qty'];
				$qty_prefix = '';
				$price = $product['price'];
			}

			$qty_suffix = $quantity_name;
			if(!empty($qty_prefix))
			{
				$qty_suffix = $qty_prefix . ' ' . $qty_suffix;
			}
			$qty_suffix = empty($qty_suffix) ? '' : ' (' . $qty_suffix . ')';

			if(empty($product['name']))
			{
				$product['name'] = 'unnamed_product_with_id_'.$product['id'];
			}

			$productItem = array(
				'quantity' => $qty,
				'name' => mb_substr($this->encHelper->transcodeOutbound($product['name'] . $qty_suffix), 0, 127),
				'price' => $this->_formatAmount($price, $order->info['currency']),
				'currency' => $order->info['currency'],
			);
			if(!empty($product['model']))
			{
				$productItem['sku'] = mb_substr($this->encHelper->transcodeOutbound($product['model']), 0, 50);
			}
			$itemsArray[] = $productItem;
			$subtotal += $qty * round($price, 2);
		}

		$itemsAndSubtotal = array(
				'items' => $itemsArray,
				'subtotal' => $subtotal,
			);
		return $itemsAndSubtotal;
	}

	/**
	 * retrieves quantity unit name to be used as a prefix for the line item name
	 * @todo use GXEngine Products service when available
	 * @param $quantity_unit_id int
	 * @param $languages_id int
	 */
	protected function getQuantityName($quantity_unit_id, $languages_id)
	{
		$query =
			'SELECT
				`unit_name`
			FROM
				`quantity_unit_description`
			WHERE
				`quantity_unit_id` = \':quantity_unit_id\' AND
				`language_id` = \':language_id\'
			';
		$query = strtr($query, array(':quantity_unit_id' => (int)$quantity_unit_id, ':language_id' => (int)$languages_id));
		$unit_name = '';
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			$unit_name = $row['unit_name'];
		}
		return $unit_name;
	}

	/**
	 * creates URLs for redirection back from PayPal hosted payment pages to the shop
	 * @param string $mode payment mode (ecm|ecs|plus)
	 * @return array with keys 'return_url' and 'cancel_url'
	 */
	protected function getRedirectUrls($mode)
	{
		if($mode != 'ecs')
		{
			$returnUrls = array(
				"return_url" => str_replace('&amp;', '&', xtc_href_link('checkout_confirmation.php', '', 'SSL')),
				"cancel_url" => str_replace('&amp;', '&', xtc_href_link('checkout_payment.php', 'paypal=cancel', 'SSL')),
			);
		}
		else
		{
			$returnUrls = array(
				"return_url" => str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/ReturnFromECS', 'SSL')),
				"cancel_url" => str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/CancelECS', 'SSL')),
			);
		}
		return $returnUrls;
	}

	/**
	 * updates an existing PayPal payment from a given order.
	 * required to add shipping address to a Plus payment if customer chooses PayPal
	 * @param string $payment_id ID of payment resource to update
	 * @param order $order
	 * @throws Exception if updating payment fails
	 */
	public function updatePaymentFromOrder($payment_id, order $order)
	{
		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');

		$tax = 0;
		if($_SESSION['customers_status']['customer_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customer_status_add_tax_ot'] == 1)
		{
			$tax = $order->info['tax'];
		}

		$shippingCountryCode = $order->delivery['country']['iso_code_2'];
		$patchArray = array();
		$patchArray[] = array(
			"op" => "add",
			"path" => "/transactions/0/item_list/shipping_address",
			"value" => array(
				"recipient_name" => $this->encHelper->transcodeOutbound($order->delivery['firstname'] .' '. $order->delivery['lastname']),
				"line1" => $this->encHelper->transcodeOutbound($order->delivery['street_address']),
				"city" => $this->encHelper->transcodeOutbound($order->delivery['city']),
				"postal_code" => $this->encHelper->transcodeOutbound($order->delivery['postcode']),
				"country_code" => $shippingCountryCode,
			)
		);
		if(!empty($order->delivery['house_number']))
		{
			$patchArray[0]['value']['line1'] .= ' ' . $order->delivery['house_number'];
		}
		if(!empty($order->delivery['state']))
		{
			$patchArray[0]['value']['state'] = $this->encHelper->transcodeOutbound($order->delivery['state']);
		}

		$billingCountryCode = $order->billing['country']['iso_code_2'];
		$patchArray[] = array(
			"op" => "add",
			"path" => "/potential_payer_info/billing_address",
			"value" => array(
				"line1" => $this->encHelper->transcodeOutbound($order->billing['street_address']),
				"city" => $this->encHelper->transcodeOutbound($order->billing['city']),
				"postal_code" => $this->encHelper->transcodeOutbound($order->billing['postcode']),
				"country_code" => $billingCountryCode,
			)
		);
		if(!empty($order->billing['house_number']))
		{
			$patchArray[1]['value']['line1'] .= ' ' . $order->billing['house_number'];
		}

		$patchJSON = $json->encodeUnsafe($patchArray);

		$isPlus = true;
		$ppRestService = MainFactory::create('PayPalRestService');
		$patchPaymentRequest = MainFactory::create('PayPalRestRequest', 'PATCH', '/v1/payments/payment/'.$payment_id, $patchJSON, $isPlus);
		$response = $ppRestService->performRequest($patchPaymentRequest);
		if(!in_array($response->getResponseCode(), array('200', '204')))
		{
			$response_object = $response->getResponseObject();
			if($response_object === false)
			{
				throw new Exception('Error decoding response '.print_r($response, true));
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
			throw new Exception('ERROR updating payment: '.$error_message);
		}
	}

	/**
	 * adds invoice_number to payment resource
	 */
	public function addInvoiceNumber($payment_id, order $order)
	{
		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$patchArray[] = array(
				"op" => "add",
				"path" => "/transactions/0/invoice_number",
				"value" => (string)$order->info['orders_id'],
		);
		$patchJSON = $json->encodeUnsafe($patchArray);
		$isPlus = false;
		$ppRestService = MainFactory::create('PayPalRestService');
		$patchPaymentRequest = MainFactory::create('PayPalRestRequest', 'PATCH', '/v1/payments/payment/'.$payment_id, $patchJSON, $isPlus);
		$response = $ppRestService->performRequest($patchPaymentRequest);
		if(!in_array($response->getResponseCode(), array('200', '204')))
		{
			$response_object = $response->getResponseObject();
			if($response_object === false)
			{
				throw new Exception('Error decoding response '.print_r($response, true));
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
			throw new Exception('ERROR updating payment: '.$error_message);
		}
	}

	/**
	 * creates a payment to be used in conjunction with the paylink feature (ECM payment without line items)
	 * @param string $paycode a unique code used in the paylink
	 * @throws Exception if payment cannot be created
	 * @return PayPalPayment newly created payment
	 */
	public function createPaylinkPayment($paycode)
	{
		$orders_id = $paycode->orders_id;
		$amount = $paycode->amount;
		$paycode_hash = $paycode->paycode;

		$order = new order((int)$orders_id);
		$intent = $this->config->get('intent');
		$mode = 'ecm';
		$paymentExperienceProfileId = $this->config->get('payment_experience_profile/'.$_SESSION['language_code']);
		$paymentArray = array(
				"intent" => $intent,
				"payer" => array(
					"payment_method" => "paypal"
				),
				"transactions" => array(
					array(
						"amount" => array(
							"currency" => $order->info['currency'],
							"total" => $this->_formatAmount($amount, $order->info['currency']),
						),
						"description" => $this->encHelper->transcodeOutbound(STORE_NAME),
					),
				),
				"redirect_urls" => array(
					"return_url" => str_replace('&amp;', '&', xtc_href_link('shop.php', 'do=PayPal/PaylinkReturn&code='.$paycode_hash, 'SSL')),
					"cancel_url" => str_replace('&amp;', '&', xtc_href_link('index.php', '', 'SSL')),
				),
			);
		if(!empty($paymentExperienceProfileId))
		{
			$paymentArray['experience_profile_id'] = $paymentExperienceProfileId;
		}

		require_once DIR_FS_CATALOG.'gm/classes/JSON.php';
		$json = MainFactory::create('Services_JSON');
		$paymentJSON = $json->encodeUnsafe($paymentArray);

		$ppRestService = MainFactory::create_object('PayPalRestService');
		$isPlus = false;
		$ppRestRequest = MainFactory::create_object('PayPalRestRequest', array('POST', '/v1/payments/payment', $paymentJSON, $isPlus));
		$response = $ppRestService->performRequest($ppRestRequest);
		$response_object = $response->getResponseObject();
		if($response_object === false)
		{
			throw new Exception('Error decoding response '.print_r($response, true));
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
			throw new Exception('ERROR creating payment: '.$error_message);
		}

		$paypalPayment = MainFactory::create_object('PayPalPayment', array($response_object));
		return $paypalPayment;
	}
}
