<?php
/* --------------------------------------------------------------
	PayPalController.inc.php 2016-08-03
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class PayPalController
 * @package HttpViewControllers
 */
class PayPalController extends HttpViewController
{
	/**
	 * @var PayPalText Helper for language-specific texts
	 */
	protected $paypalText;

	/**
	 * @var PayPalConfigurationStorage
	 */
	protected $configurationStorage;

	/**
	 * @var PayPalLogger
	 */
	protected $logger;

	/**
	 * Initialize the Controller with required properties
	 *
	 * @param \HttpContextReaderInterface     $httpContextReader
	 * @param \HttpResponseProcessorInterface $httpResponseProcessor
	 * @param \ContentViewInterface           $contentView
	 *
	 * @inheritdoc
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
		$this->paypalText = MainFactory::create('PayPalText');
		$this->configurationStorage = MainFactory::create('PayPalConfigurationStorage');
		$this->logger = MainFactory::create('PayPalLogger');
	}

	/**
	 * returns a QueryBuilder instance for database access.
	 * @return CI_DB_query_builder Returns a database driver that can be used for db operations.
	 */
	protected function getQueryBuilder()
	{
		$coreLoaderSettings = MainFactory::create('GXCoreLoaderSettings');
		$coreLoader = MainFactory::create('GXCoreLoader', $coreLoaderSettings);
		$queryBuilder = $coreLoader->getDataBaseQueryBuilder();
		return $queryBuilder;
	}

	/**
	 * Run the actionDefault method.
	 */
	public function actionDefault()
	{
		# return MainFactory::create('HttpControllerResponse', 'not implemented');
		return MainFactory::create('RedirectHttpControllerResponse', GM_HTTP_SERVER.DIR_WS_CATALOG);
	}

	/**
	 * sets the ECS shopping cart flag to true.
	 * The flag is used in ECSButton.js as an indication that the customer is to be redirected to PayPal for an ECS login.
	 * This is required for the ECS button on products pages to work as intended.
	 */
	public function actionCartECS()
	{
		$_SESSION['paypal_cart_ecs'] = true;
		$contentArray = array('cartECS' => true);
		return MainFactory::create('JsonHttpControllerResponse', $contentArray);
	}

	/**
	 * creates a PayPal payment for the ECS flow and redirects the customer to the ECS login page.
	 */
	public function actionPrepareECS()
	{
		$this->logger->notice('Preparing ECS payment');
		unset($_SESSION['paypal_cart_ecs']);
		try {
			$order = new order();
			$paymentFactory = MainFactory::create('PayPalPaymentFactory');
			$payment = $paymentFactory->createPaymentFromOrder($order, 'ecs');
			$_SESSION['paypal_payment'] = array(
				'id' => $payment->id,
				'type' => 'ecs',
				'state' => 'created',
				'cartID' => $_SESSION['cart']->cartID,
			);
			$approval_url = $payment->getLink('approval_url');
			$approval_url_href = $approval_url->href;
			$approval_url_href .= '&LocaleCode='.$this->getLocale();
			$this->logger->notice('Payment '.$payment->id.' created, redirecting to '.$approval_url_href);
			return MainFactory::create('RedirectHttpControllerResponse', $approval_url_href);
		}
		catch(Exception $e)
		{
			$this->logger->notice('Error creating ECS payment: '.$e->getMessage());
			return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('shopping_cart.php', '', 'SSL'));
		}
	}

	/**
	 * invalidates session data when the user cancels an ECS login
	 */
	public function actionCancelECS()
	{
		$this->logger->notice('ECS payment cancelled by user');
		unset($_SESSION['paypal_payment']);
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('shopping_cart.php', '', 'SSL'));
	}

	/** determines LocaleCode from session data */
	protected function getLocale()
	{
		$language = strtolower($_SESSION['language_code']);
		switch ($language)
		{
			case 'de':
				$country_iso_2 = 'DE';
				break;

			case 'en':
				$country_iso_2 = 'US';
				break;

			default:
				$shop_country_data = $this->_findCountryByID(STORE_COUNTRY);
				$country_iso_2 = $shop_country_data->countries_iso_code_2;
				break;
		}
		$country_iso_2 = 'XC';
		$locale = sprintf('%s_%s', $language, $country_iso_2);
		return $locale;
	}

	protected function _findCountryByID($country_id)
	{
		$queryBuilder = $this->getQueryBuilder();
		$query = $queryBuilder->get_where('countries', array('countries_id' => $country_id));
		$country_data = false;
		if($query->num_rows() == 1)
		{
			$country_data = $query->row();
		}
		return $country_data;
	}

	/**
	 * called as the return URL when a customer returns from an ECS login.
	 * If the customer is not logged in, a guest account will be created from data provided by PayPal.
	 */
	public function actionReturnFromECS()
	{
		$paymentId = $this->_getQueryParameter('paymentId');
		try {
			$payment = MainFactory::create('PayPalPayment', $paymentId);
			$_SESSION['paypal_payment']['state'] = 'approved';
			$_SESSION['paypal_payment']['payer_id'] = $_GET['PayerID'];
			$_SESSION['payment'] = 'paypal3';
			$this->logger->notice('Customer '.$_GET['PayerID'].' returned from ECS login for payment '.$paymentId);

			if(!isset($_SESSION['customer_id']) || $_SESSION['customer_id'] == 0)
			{
				$this->logger->notice('Creating guest account from ECS data');
				$countryService = StaticGXCoreLoader::getService('Country');
				$customerCountryIso2 = (string)$payment->payer->payer_info->shipping_address->country_code;
				if(empty($customerCountryIso2))
				{
					$customerCountryIso2 = (string)$payment->payer->payer_info->country_code;
				}
				if(!empty($customerCountryIso2))
				{
					$countryId = $this->getCustomerCountryIdByIso2($customerCountryIso2);
				}
				else
				{
					$countryId = new IdType(array(STORE_COUNTRY));
				}
				$customerCountry = $countryService->getCountryById($countryId);
				if($countryService->countryHasCountryZones($customerCountry))
				{
					$allZones = $countryService->findCountryZonesByCountryId($countryId);
					$customerCountryZone = array_pop($allZones);

					try
					{
						if(isset($payment->payer->payer_info->shipping_address->state) &&
							$payment->payer->payer_info->shipping_address->state != 'Empty' &&
							$payment->payer->payer_info->shipping_address->state != 'NOTPROVIDED')
						{
							$customerCountryZone = $countryService->getCountryZoneByNameAndCountry((string)$payment->payer_info->shipping_address->state, $customerCountry);
						}
					}
					catch(Exception $e)
					{
						$this->logger->notice("Could not determine customerCountryZone, using fallback; exception: ".$e->getMessage());
					}
				}
				else
				{
					$customerCountryZone = $countryService->getUnknownCountryZoneByName('');
				}

				$firstName = (string)$payment->payer->payer_info->first_name;
				$firstName = empty($firstName) ? $this->paypalText->get_text('ecs_no_data') : $firstName;
				$lastName = (string)$payment->payer->payer_info->last_name;
				$lastName = empty($lastName) ? $this->paypalText->get_text('ecs_no_data') : $lastName;
				$street = (string)$payment->payer->payer_info->shipping_address->line1;
				$street = empty($street) ? $this->paypalText->get_text('ecs_no_data') : $street;
				$houseNumber = '';
				$company = (string)$payment->payer->payer_info->shipping_address->line2;
				$postcode = (string)$payment->payer->payer_info->shipping_address->postal_code;
				$postcode = empty($postcode) ? '00000' : $postcode;
				$city = (string)$payment->payer->payer_info->shipping_address->city;
				$city =  empty($city) ? $this->paypalText->get_text('ecs_no_data') : $city;
				$phone = (string)$payment->payer->payer_info->shipping_address->phone;
				$phone = empty($phone) ? (string)$payment->payer->payer_info->phone : $phone;

				if(ACCOUNT_SPLIT_STREET_INFORMATION === 'true')
				{
					$splitStreet = $this->splitStreet($street);
					$street      = $splitStreet['street'];
					$houseNumber = $splitStreet['house_no'];
				}
				
				$addressBlock = MainFactory::create('AddressBlock',
				                                    new CustomerGender(''),
				                                    new CustomerFirstname($firstName),
				                                    new CustomerLastname($lastName),
				                                    new CustomerCompany($company),
				                                    new CustomerB2BStatus(false),
				                                    new CustomerStreet($street),
				                                    new CustomerHouseNumber($houseNumber),
				                                    new CustomerAdditionalAddressInfo(''),
				                                    new CustomerSuburb(''),
				                                    new CustomerPostcode($postcode),
				                                    new CustomerCity($city),
				                                    $customerCountry,
				                                    $customerCountryZone
				);

				try
				{
					try
					{
						$customerService = StaticGXCoreLoader::getService('Customer');
						$customer = $customerService->createNewGuest(
							new CustomerEmail((string)$payment->payer->payer_info->email),
							new DateTime('1970-01-01 00:00:00'),
							new CustomerVatNumber(''),
							new CustomerCallNumber($phone), // phone
							new CustomerCallNumber(''), // fax
							$addressBlock,
							new KeyValueCollection(array())
						);
						$_SESSION['paypal_payment']['is_guest'] = true;
						$this->logger->notice('Customer account with customers_id '.(int)$customer->getId().' created from ECS data');
					}
					catch(UnexpectedValueException $e)
					{
						// cannot create guest account b/c another account with this email already exists
						// -> show login page or log-in into existing account
						if($this->configurationStorage->get('allow_ecs_login') == true)
						{
							$customer = $this->getCustomerByEmail((string)$payment->payer->payer_info->email);
							$this->logger->notice(sprintf('Customer log-in from ECS data (%d/%s)', $_SESSION['customer_id'], (string)$payment->payer->payer_info->email));
						}
						else
						{
							$this->logger->notice(sprintf('Customer log-in after ECS (%s)', (string)$payment->payer->payer_info->email));
							return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('login.php', 'checkout_started=1', 'SSL'));
						}
					}
					$this->loginCustomer($customer);

					// update cartID (gets changed by login)
					$_SESSION['paypal_payment']['cartID'] = $_SESSION['cart']->cartID;
				}
				catch(Exception $e)
				{
					$this->logger->notice('Error creating ECS guest account: '.$e->getMessage());
					unset($_SESSION['paypal_payment']);
					return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('shopping_cart.php', '', 'SSL'));
				}
			}

			return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('checkout_shipping.php', '', 'SSL'));
		}
		catch(Exception $e)
		{
			throw $e;
			$this->logger->notice('Error processing ECS payment: '.$e->getMessage());
			unset($_SESSION['paypal_payment']);
			return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('shopping_cart.php', '', 'SSL'));
		}
	}

	/**
	 * Heuristically splits up a street address into its component street name and house number
	 * @param  string
	 * @return array with keys 'street' and 'house_no'
	 */
	protected function splitStreet($street_address)
	{
		$street_address = trim($street_address);
		$splitStreet    = array(
			'street'   => $street_address,
			'house_no' => '',
		);
		$matches        = array();
		if(preg_match('_^(\d.*?)\s(.+)_', $street_address, $matches) === 1)
		{
			$splitStreet['street']   = $matches[2];
			$splitStreet['house_no'] = $matches[1];
		}
		else if(preg_match('_(.+?)\s?(\d.*)_', $street_address, $matches) === 1)
		{
			$splitStreet['street']   = $matches[1];
			$splitStreet['house_no'] = $matches[2];
		}

		return $splitStreet;
	}

	protected function loginCustomer(Customer $customer)
	{
		if(SESSION_RECREATE == 'True')
		{
			xtc_session_recreate();
		}

		$_SESSION['customer_id']                 = $customer->getId();
		$_SESSION['customer_first_name']         = $customer->getFirstname();
		$_SESSION['customer_last_name']          = $customer->getLastname();
		$_SESSION['customer_default_address_id'] = $customer->getDefaultAddress()->getId();
		$_SESSION['customer_country_id']         = $customer->getDefaultAddress()->getCountry()->getId();
		$_SESSION['customer_zone_id']            = $customer->getDefaultAddress()->getCountryZone()->getId();
		$_SESSION['customer_vat_id']             = $customer->getVatNumber();
		$_SESSION['account_type']                = $customer->isGuest() ? '1' : '0';
	}

	protected function getCustomerCountryIdByIso2($iso2)
	{
		$queryBuilder = $this->getQueryBuilder();
		$query = $queryBuilder->get_where('countries', array('countries_iso_code_2' => strtoupper($iso2)));
		if($query->num_rows() !== 1)
		{
			throw new Exception('Invalid country code: '.$iso2);
		}
		$id = new IdType($query->row()->countries_id);
		return $id;
	}

	protected function getCustomerByEmail($email_address)
	{
		$queryBuilder = $this->getQueryBuilder();
		$query = $queryBuilder->get_where('customers', array('customers_email_address' => $email_address, 'account_type' => '0'));
		$customerId = new IdType($query->row()->customers_id);
		$customerService = StaticGXCoreLoader::getService('Customer');
		$customer = $customerService->getCustomerById($customerId);
		return $customer;
	}

	/**
	 * called by a CheckoutSuccessExtender to end an ECS guest session
	 */
	public function actionLogoffECSCustomer()
	{
		$this->logger->notice('ECS guest has completed checkout, deleting account and resetting session');
		$logoffHelper = MainFactory::create('PayPalLogoffHelper');
		$logoffHelper->logoffGuest($_SESSION['customer_id']);
		return MainFactory::create('HttpControllerResponse', 'logged_off');
	}

	/**
	 * dummy action for the bank transaction pending URL.
	 * The flow that used to require this has recently been deprecated by PayPal.
	 */
	public function actionBankTxnPending()
	{
		return MainFactory::create('HttpControllerResponse', 'not implemented');
	}

	/**
	 * called by the third party payments integration in PayPal Plus, this action simply sets one of the supported payment methods as the selected method and
	 * invalidates the PayPal payment which has been created for the paywall.
	 */
	public function actionSetPayment()
	{
		$paymentCode = $this->_getQueryParameter('payment');
		if($this->_isValidPayment($paymentCode) === false)
		{
			throw new Exception($this->paypalText->get_text('invalid_payment_selected'));
		}
		$_SESSION['payment'] = $paymentCode;
		unset($_SESSION['paypal_payment']);
		$this->logger->notice('Using third party payment: '.$paymentCode);

		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('checkout_confirmation.php', '', 'SSL'));
	}

	/**
	 * determines if a payment module is currently installed.
	 * Caveat: This only works for modules where the filename (plus '.php') is identical with the module code.
	 * @return bool true if module is installed
	 */
	protected function _isValidPayment($paymentCode)
	{
		$installedModules = explode(';', constant('MODULE_PAYMENT_INSTALLED'));
		$isValid = false;
		$isValid = in_array($paymentCode.'.php', $installedModules);
		return $isValid;
	}

	/**
	 * called by PayPal to deliver Webhook notifications.
	 * Any incoming notification will be recorded in the order status history of the corresponding order.
	 * If the HMAC signature check fails, a warning will be added.
	 * Notifications of type PAYMENT.SALE.COMPLETED cause the order to be transferred to the status for completed orders.
	 */
	public function actionWebhook()
	{
		if($this->_getQueryParameter('test') == 'accessibility')
		{
			return MainFactory::create('HttpControllerResponse', 'OK');
		}
		$this->logger->notice('Webhook endpoint called');
		$rawInput = file_get_contents('php://input');
		file_put_contents(DIR_FS_CATALOG.'logfiles/webhook-body-'.LogControl::get_secure_token().'.txt', $rawInput);
		$this->logger->debug_notice("Webhook raw input:\n" . $rawInput);
		$this->logger->debug_notice("Webhook SERVER:\n" . print_r($_SERVER, true));

		if(!(
			isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME']) &&
			isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_ID']) &&
			isset($_SERVER['HTTP_PAYPAL_CERT_URL']) &&
			isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG'])
			))
		{
			$this->logger->notice('Required data missing; call might not be from PayPal, aborting.');
			return MainFactory::create('HttpControllerResponse', 'Not OK');
		}

		try
		{
			$webhookEvent = MainFactory::create('PayPalWebhooksEvent', $rawInput);
		}
		catch(Exception $e)
		{
			$this->logger->notice('ERROR parsing notification: '.$e->getMessage());
		}

		try
		{
			$this->logger->notice('checking signature');
			$webhookEvent->verifySignature(
				$_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'],
				$_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'],
				$_SERVER['HTTP_PAYPAL_CERT_URL'],
				$_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG']
			);
			$hmac_valid = true;
		}
		catch(PaypalWebhookSignatureException $e)
		{
			$exceptionMessage = sprintf(
					'Received INVALID Webhooks Event from %s, Error: %s, Timestamp: %s, Transmission ID: %s, Certificate URL: %s, Transmission Sig: %s',
					$_SERVER['REMOTE_ADDR'],
					$e->getMessage(),
					$_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'],
					$_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'],
					$_SERVER['HTTP_PAYPAL_CERT_URL'],
					$_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG']
				);
			$this->logger->notice($exceptionMessage);
			$hmac_valid = false;
		}

		$eventObject = $webhookEvent->getEventObject();
		$this->logger->notice('Received Webhooks Event '.$eventObject->id.'/'.$eventObject->event_type.', HMAC is '.($hmac_valid ? 'VALID' : 'INVALID'));
		$orders_id = $webhookEvent->getOrdersID();
		$textKey = strtolower(str_replace('.', '_', $eventObject->event_type));
		$messageText = $this->paypalText->get_text('webhook_' . $textKey);
		if($hmac_valid === false)
		{
			$messageText .= "\n".$this->paypalText->get_text('webhook_warning_hmac_invalid');
		}

		if($orders_id !== false)
		{
			switch($eventObject->event_type)
			{
				/*
				case 'PAYMENT.SALE.COMPLETED':
					$this->updateOrdersStatus($orders_id, $this->configurationStorage->get('orderstatus/completed'), $messageText);
					break;
				*/

				case 'PAYMENT.AUTHORIZATION.CREATED':
				case 'PAYMENT.AUTHORIZATION.VOIDED':
				case 'PAYMENT.CAPTURE.COMPLETED':
				case 'PAYMENT.CAPTURE.REFUNDED':
				case 'PAYMENT.CAPTURE.REVERSED':
				case 'PAYMENT.SALE.REFUNDED':
				case 'PAYMENT.SALE.REVERSED':
				default:
					$this->updateOrdersStatus($orders_id, null, $messageText);
			}
			$this->logger->notice(sprintf('Received event of type %s for order %d', $eventObject->event_type, $orders_id));
		}
		else
		{
			$this->logger->notice('No order relating to payment ID '.$eventObject->resource->parent_payment.', ignoring this event.');
		}

		return MainFactory::create('HttpControllerResponse', 'OK');
	}


	/**
	* updates order status and adds entry to status history
	* @todo: replace once services for orders and order status history are available
	*/
	protected function updateOrdersStatus($orders_id, $orders_status_id = null, $comments = '')
	{
		$coreLoaderSettings = MainFactory::create('GXCoreLoaderSettings');
		$coreLoader = MainFactory::create('GXCoreLoader', $coreLoaderSettings);
		$queryBuilder = $coreLoader->getDataBaseQueryBuilder();

		if($orders_status_id === null)
		{
			$status_query =
				'SELECT
					orders_status
				FROM
					orders
				WHERE
					orders_id = \':orders_id\'';
			$status_query = strtr($status_query, array(':orders_id' => (int)$orders_id));
			$query = $queryBuilder->query($status_query);
			$row = $query->row();
			$orders_status_id = $row->orders_status;
		}
		else
		{
			$update_query =
				'UPDATE
					orders
				SET
					orders_status = \':orders_status\'
				WHERE
					orders_id = \':orders_id\'';
			$update_query = strtr($update_query, array(
				':orders_status' => (int)$orders_status_id,
				':orders_id' => (int)$orders_id,
			));
			$queryBuilder->query($update_query);
		}

		$insert_query =
			'INSERT INTO
				orders_status_history
			SET
				orders_id = \':orders_id\',
				orders_status_id = \':orders_status_id\',
				date_added = NOW(),
				customer_notified = 0,
				comments = \':comments\'';
		$insert_query = strtr($insert_query, array(
				':orders_id' => (int)$orders_id,
				':orders_status_id' => (int)$orders_status_id,
				':comments' => xtc_db_input($comments),
		));
		$queryBuilder->query($insert_query);
	}

	/**
	 * This action can be called from a cronjob to update orders still in pending state.
	 * Supposed to be used as a replacement for Webhooks in cases where the shop is inaccessible for PayPal,
	 * e.g. due to missing TLS accessibility.
	 */
	public function actionStatusUpdate()
	{
		if($this->_getQueryParameter('key') != LogControl::get_secure_token())
		{
			return MainFactory::create('HttpControllerResponse', 'invalid access');
		}

		ob_start();
		$coreLoaderSettings = MainFactory::create('GXCoreLoaderSettings');
		$coreLoader = MainFactory::create('GXCoreLoader', $coreLoaderSettings);
		$queryBuilder = $coreLoader->getDataBaseQueryBuilder();
		#printf("<pre>%s</pre>", print_r($queryBuilder, true));

		$queryDays = (int)$this->_getQueryParameter('days');
		$days = $queryDays > 0 ? $queryDays : 30;

		$start_date = date('Y-m-d 00:00:00', strtotime((int)$days.' days ago'));

		echo "<pre>\n";
		$queryBuilder->select('orders.orders_id, date_purchased, orders_status, payment_method, orders_status_name, payment_id');
		$queryBuilder->from('orders');
		$queryBuilder->join('orders_status', 'orders_status.orders_status_id = orders.orders_status AND orders_status.language_id = 2');
		$queryBuilder->join('orders_paypal_payments', 'orders_paypal_payments.orders_id = orders.orders_id');
		$queryBuilder->where('payment_method', 'paypal3');
		$queryBuilder->where('orders_status', $this->configurationStorage->get('orderstatus/pending'));
		$queryBuilder->where('orders.date_purchased >=', $start_date);
		$queryBuilder->order_by('orders_id', 'DESC');
		$query = $queryBuilder->get();
		foreach($query->result() as $row)
		{
			if(!empty($row->payment_id))
			{
				$payment = MainFactory::create('PayPalPayment', $row->payment_id);
				$transaction = false;
				$transaction_type = null;
				if(isset($payment->transactions[0]->related_resources[0]->sale))
				{
					$transaction = $payment->transactions[0]->related_resources[0]->sale;
					$transaction_type = 'sale';
				}
				elseif(isset($payment->transactions[0]->related_resources[0]->authorization))
				{
					$transaction = $payment->transactions[0]->related_resources[0]->authorization;
					$transaction_type = 'authorization';
				}

				if($transaction !== false)
				{
					$state_changed = false;
					$transaction_state = $transaction->state;
					switch($transaction_state)
					{
						case 'completed':
						case 'authorized':
							$this->updateOrdersStatus(
								$row->orders_id,
								$this->configurationStorage->get('orderstatus/completed'),
								$this->paypalText->get_text('status_updated_pending_to_'.$transaction_state)
							);
							$state_changed = true;
							break;

						case 'captured':
						case 'pending':
						case 'refunded':
						case 'partially_refunded':
						default:
							// do nothing
					}
					printf("%d\t%s\t%s\t%s (%d)\t%s\t%s\t%s\n",
						$row->orders_id,
						$row->date_purchased,
						$transaction_type,
						$row->orders_status_name,
						$row->orders_status,
						$row->payment_id,
						$transaction_state,
						$state_changed ? 'changed' : 'no change'
					);
				}
				#printf("%s\n", print_r($payment->json_object));
			}
		}
		echo "</pre>\n";

		return MainFactory::create('HttpControllerResponse', ob_get_clean());
	}

	/**
	 * Endpoint for paylink processing.
	 * Paylinks created from the order details page are directed at this action. If the paycode hash is valid
	 * a payment will be created and the customer redirected to PayPal to approve the payment.
	 */
	public function actionPaylink()
	{
		$paycode_hash = $this->_getQueryParameter('code');
		$queryBuilder = $this->getQueryBuilder();
		$query = $queryBuilder->get_where('paypal_paylink', array('paycode' => $paycode_hash));
		if($query->num_rows() != 1)
		{
			return MainFactory::create('HttpControllerResponse', $this->paypalText->get_text('invalid_paylink'));
		}
		$paycode = $query->row();

		$payPalPaymentFactory = MainFactory::create_object('PayPalPaymentFactory');
		$payPalPayment = $payPalPaymentFactory->createPaylinkPayment($paycode);
		$_SESSION['pp3_paylink_payment_id'] = $payPalPayment->id;
		$approvalLinkEntry = $payPalPayment->getLink('approval_url');
		$approvalUrl = $approvalLinkEntry->href.'&useraction=commit';

		$this->logger->notice('Payment created for Paylink '.$paycode->paycode.', redirecting to '.$approvalUrl);

		return MainFactory::create('RedirectHttpControllerResponse', $approvalUrl);
	}

	/**
	 * action used in return URLs for the paylink feature.
	 * Takes the PayerID from the query parameters and executes the payment created by actionPaylink(),
	 * then displays a quick thank you note.
	 */
	public function actionPaylinkReturn()
	{
		$paycode_hash = $this->_getQueryParameter('code');
		$payer_id = $this->_getQueryParameter('PayerID');
		$payment_id = isset($_SESSION['pp3_paylink_payment_id']) ? $_SESSION['pp3_paylink_payment_id'] : '';

		if(empty($paycode_hash) || empty($payment_id) || empty($payer_id))
		{
			return MainFactory::create('HttpControllerResponse', 'required parameter(s) missing');
		}

		try
		{
			$queryBuilder = $this->getQueryBuilder();
			$query = $queryBuilder->get_where('paypal_paylink', array('paycode' => $paycode_hash));
			if($query->num_rows() != 1)
			{
				throw new Exception($this->paypalText->get_text('invalid_paylink'));
			}
			$paycode = $query->row();

			$this->logger->notice('Executing Paylink payment '.$payment_id);
			$payPalPayment = MainFactory::create('PayPalPayment', $_SESSION['pp3_paylink_payment_id']);
			$payPalPayment->execute($payer_id);
			$queryBuilder->replace(
				'orders_paypal_payments',
				array(
					'orders_id' => $paycode->orders_id,
					'payment_id' => $payment_id,
					'mode' => $this->configurationStorage->get('mode')));
			$queryBuilder->delete('paypal_paylink', array('orders_id' => $paycode->orders_id));
			$this->updateOrdersStatus(
				$paycode->orders_id,
				$this->configurationStorage->get('orderstatus/completed'),
				$this->paypalText->get_text('paylink_payment_completed')
			);
			$this->logger->notice('Paylink payment executed, ' . $paycode->amount . ', orders_id ' . $paycode->orders_id . ' ' . $payPalPayment->state);

			$mainContent = $this->paypalText->get_text('paylink_thank_you');
		}
		catch(Exception $e)
		{
			$mainContent = $this->paypalText->get_text('paylink_error');
			$this->logger->notice('ERROR executing Paylink payment: '.$e->getMessage());
		}

		if(class_exists('LayoutContentControl'))
		{
			$layoutControl = MainFactory::create_object('LayoutContentControl');
			$layoutControl->set_data('GET', $_GET);
			$layoutControl->set_data('POST', $_POST);
			$layoutControl->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
			$layoutControl->set_('coo_product', $GLOBALS['product']);
			$layoutControl->set_('coo_xtc_price', $GLOBALS['xtPrice']);
			$layoutControl->set_('c_path', $GLOBALS['cPath']);
			$layoutControl->set_('main_content', $mainContent);
			$layoutControl->set_('request_type', $GLOBALS['request_type']);
			$layoutControl->proceed();

			$redirectUrl = $layoutControl->get_redirect_url();
			if(empty($redirectUrl) === false)
			{
				xtc_redirect($redirectUrl);
			}
			else
			{
				return MainFactory::create('HttpControllerResponse', $layoutControl->get_response());
			}
		}
		else
		{
			return MainFactory::create('HttpControllerResponse', $mainContent);
		}
	}

}