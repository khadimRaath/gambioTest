<?php
/* --------------------------------------------------------------
	ShipcloudController.inc.php 2016-07-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ShipcloudController
 * @package HttpViewControllers
 */
class ShipcloudController extends AdminHttpViewController
{
	/**
	 * @var GXCoreLoaderSettingsInterface
	 */
	private $settings;

	/**
	 * @var GXCoreLoaderInterface
	 */
	private $loader;

	/**
	 * Query Builder
	 * @var CI_DB_query_builder
	 */
	private $db;

	/**
	 * wrapper for text phrases
	 * @var ShipcloudText
	 */
	protected $shipcloudText;
	/**
	 * configuration storage
	 * @var ShipcloudConfigurationStorage
	 */
	protected $shipcloudConfigurationStorage;
	/**
	 * logger
	 * @var ShipcloudLogger
	 */
	protected $shipcloudLogger;


	public function __construct(HttpContextReaderInterface     $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface           $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
		$this->shipcloudText                 = MainFactory::create('ShipcloudText');
		$this->shipcloudConfigurationStorage = MainFactory::create('ShipcloudConfigurationStorage');
		$this->shipcloudLogger               = MainFactory::create('ShipcloudLogger');
	}


	/**
	 * determines if Shipcloud is configured and ready to use
	 */
	protected function isConfigured()
	{
		$mode         = $this->shipcloudConfigurationStorage->get('mode');
		$apiKey       = $this->shipcloudConfigurationStorage->get('api-key/' . $mode);
		$isConfigured = !empty($apiKey);

		return $isConfigured;
	}


	/**
	 * Override "proceed" method of parent and use it for initialization.
	 *
	 * This method must call the parent "proceed" in order to work properly.
	 *
	 * @param HttpContextInterface $httpContext
	 */
	public function proceed(HttpContextInterface $httpContext)
	{
		$this->settings = MainFactory::create('GXCoreLoaderSettings');
		$this->loader   = MainFactory::create('GXCoreLoader', $this->settings);
		$this->db       = $this->loader->getDatabaseQueryBuilder();
		// Set the template directory.
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		// Call the parent "proceed" method.
		parent::proceed($httpContext);
	}


	/**
	 * Run the actionDefault method.
	 */
	public function actionDefault()
	{
		# return MainFactory::create('HttpControllerResponse', 'not implemented');
		return MainFactory::create('RedirectHttpControllerResponse', GM_HTTP_SERVER . DIR_WS_CATALOG);
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


	/**
	 * retrieves an order's total
	 *
	 * @todo get this data from OrderService
	 *
	 * @param int $orders_id the order's id
	 *
	 * @return double
	 */
	protected function getDeclaredValue($orders_id)
	{
		$declared_value = 0;
		$loader = MainFactory::create('GXCoreLoader', MainFactory::create('GXCoreLoaderSettings'));
		$db     = $loader->getDatabaseQueryBuilder();
		$db->select('*')
		   ->from('orders_total')
		   ->where(array('orders_id' => $orders_id, 'class' => 'ot_total'));
		foreach($db->get()->result() as $row)
		{
			$declared_value = (double)$row->value;
		}

		return $declared_value;
	}


	/**
	 * Shows a form for entering data for a label
	 * @return HttpControllerResponse
	 */
	public function actionCreateLabelForm()
	{
		require_once DIR_FS_ADMIN . 'includes/classes/order.php';
		$orders_id        = (int)$this->_getQueryParameter('orders_id');
		$template_version = (int)$this->_getQueryParameter('template_version');
		$order            = new order($orders_id);
		$order_weight     = $this->_getShippingWeight($orders_id);
		$declared_value   = $this->getDeclaredValue((int)$orders_id);
		$cod_value        = $declared_value;
		if(empty($order->delivery['house_number']))
		{
			$splitStreet = $this->splitStreet($order->delivery['street_address']);
		}
		else
		{
			$splitStreet = [
				'street'   => $order->delivery['street_address'],
				'house_no' => $order->delivery['house_number'],
			];
		}
		if($declared_value < (double)$this->shipcloudConfigurationStorage->get('declared_value/minimum'))
		{
			$declared_value = 0;
		}
		$default_package            = $this->shipcloudConfigurationStorage->get('default_package');
		$default_package_data       = $this->shipcloudConfigurationStorage->get_all_tree('packages/' . $default_package
		                                                                                 . '/');
		$default_package_dimensions = $default_package_data['packages'][$default_package];
		$formdata                   = array(
			'isConfigured' => $this->isConfigured() == true ? '1' : '0',
			'orders_id'    => $orders_id,
			'is_cod'       => $order->info['payment_method'] == 'cod',
			'cod'          => array(
				'amount'   => number_format($cod_value, 2, '.', ''),
				'currency' => $order->info['currency'],
			),
			'to'           => array(
				'company'    => $order->delivery['company'],
				'first_name' => $order->delivery['firstname'],
				'last_name'  => $order->delivery['lastname'],
				'care_of'    => $order->delivery['additional_address_info'],
				'street'     => $splitStreet['street'],
				'street_no'  => $splitStreet['house_no'],
				'city'       => $order->delivery['city'],
				'zip_code'   => $order->delivery['postcode'],
				'country'    => $order->delivery['country_iso_code_2'],
			),
			'package'      => array(
				'weight'         => $default_package_dimensions['weight'],
				'width'          => $default_package_dimensions['width'],
				'length'         => $default_package_dimensions['length'],
				'height'         => $default_package_dimensions['height'],
				'declared_value' => array(
					'amount'   => number_format($declared_value, 2, '.', ''),
					'currency' => $order->info['currency'],
				),
			),
			'package_templates'        => $this->shipcloudConfigurationStorage->get_all_tree('packages'),
			//'carriers'                 => $this->shipcloudConfigurationStorage->getCarriers(),
			'preselected_carriers'     => $this->shipcloudConfigurationStorage->get_all_tree('preselected_carriers'),
			'checked_carriers'         => $this->shipcloudConfigurationStorage->get_all_tree('checked_carriers'),
			'default_package_template' => $default_package,
			'carrier'                  => 'dhl',
			'service'                  => 'standard',
			'notification_email'       => $order->customer['email_address'],
			'order_weight'             => $order_weight,
		);
		$carriersCache = MainFactory::create('ShipcloudCarriersCache');
		$formdata['carriers'] = $carriersCache->getCarriers();

		if($template_version == 2)
		{
			$html = $this->_render('shipcloud_form_single_v2.html', $formdata);
		}
		else
		{
			$html = $this->_render('shipcloud_form_single.html', $formdata);
		}
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);

		return new HttpControllerResponse($html);
	}


	/**
	 * Uses POST data from the form returned by actionCreateLabelForm() to populate a KeyValueCollection to be fed to the ShipmentFactory
	 * @param  array
	 * @param  boolean $anon_from used for shipment quote requests
	 * @param  string $language_code ISO2 language code used for advance notices (e.g. DPD Predict)
	 * @return KeyValueCollection
	 */
	protected function _prepareSingleFormDataForShipmentRequest(array $postDataArray,
	                                                            $anon_from = false,
	                                                            $language_code = null)
	{
		$language_code = $language_code ?: $_SESSION['language_code'];
		unset($postDataArray['package_template']);
		if(empty($postDataArray['from']))
		{
			$postDataArray['from'] = array(
				'street'    => $this->shipcloudConfigurationStorage->get('from/street'),
				'street_no' => $this->shipcloudConfigurationStorage->get('from/street_no'),
				'city'      => $this->shipcloudConfigurationStorage->get('from/city'),
				'zip_code'  => $this->shipcloudConfigurationStorage->get('from/zip_code'),
				'country'   => $this->shipcloudConfigurationStorage->get('from/country'),
			);
			if($anon_from === false)
			{
				$postDataArray['from']['company']    = $this->shipcloudConfigurationStorage->get('from/company');
				$postDataArray['from']['first_name'] = $this->shipcloudConfigurationStorage->get('from/first_name');
				$postDataArray['from']['last_name']  = $this->shipcloudConfigurationStorage->get('from/last_name');
				$postDataArray['from']['phone']      = $this->shipcloudConfigurationStorage->get('from/phone');
			}
		}
		if((double)$postDataArray['package']['declared_value']['amount'] == 0)
		{
			unset($postDataArray['package']['declared_value']);
		}
		if($postDataArray['carrier'] == 'dhl' && !empty($postDataArray['cod']))
		{
			$codData = $postDataArray['cod'];
			unset($postDataArray['cod']);
			$postDataArray['additional_services']   = is_array($postDataArray['additional_services']) ? $postDataArray['additional_services'] : array();
			$postDataArray['additional_services'][] = array(
				'name'       => 'cash_on_delivery',
				'properties' => array(
					'amount'              => $codData['amount'],
					'currency'            => $codData['currency'],
					'bank_account_holder' => $this->shipcloudConfigurationStorage->get('cod-account/bank_account_holder'),
					'bank_name'           => $this->shipcloudConfigurationStorage->get('cod-account/bank_name'),
					'bank_account_number' => $this->shipcloudConfigurationStorage->get('cod-account/bank_account_number'),
					'bank_code'           => $this->shipcloudConfigurationStorage->get('cod-account/bank_code'),
				),
			);
		}
		if(preg_match('/(postfiliale|packstation)/i', $postDataArray['to']['street']) === 1)
		{
			if($postDataArray['carrier'] !== 'dhl')
			{
				throw new Exception($this->shipcloudText->get_text('invalid_carrier_for_packstation'));
			}
			if(!empty($postDataArray['to']['last_name'])) // empty for shipment quote requests
			{
				$parts = array();
				if(preg_match('/(.*)\/(\d+)/', $postDataArray['to']['last_name'], $parts) !== 1)
				{
					throw new Exception($this->shipcloudText->get_text('client_number_missing'));
				}
				$lastName   = $parts[1];
				$postnummer = $parts[2];
				$postDataArray['to']['last_name'] = $lastName;
				$postDataArray['to']['care_of']   = $postnummer;
				$postDataArray['to']['street']    = strtoupper($postDataArray['to']['street']);
			}
		}
		if($postDataArray['carrier'] == 'dpd')
		{
			if($this->shipcloudConfigurationStorage->get('additional_services/dpd-predict') == true)
			{
				$postDataArray['additional_services']   = is_array($postDataArray['additional_services']) ? $postDataArray['additional_services'] : array();
				$postDataArray['additional_services'][] = array(
					'name'       => 'advance_notice',
					'properties' => array(
						'email'    => $postDataArray['notification_email'],
						'language' => $language_code,
					),
				);
			}
		}
		$shipmentData        = MainFactory::create('KeyValueCollection', $postDataArray);

		return $shipmentData;
	}


	/**
	 * Looks up the language for a given order and returns the 2-letter ISO code
	 * @param  int $orders_id
	 * @return string ISO2 language code
	 */
	protected function getOrderLanguageCode($orders_id)
	{
		$language_code = 'de';
		$this->db->select('code');
		$this->db->from('languages');
		$this->db->join('orders', 'orders_id = ' . (int)$orders_id . ' AND orders.language = languages.directory');
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			$language_code = $row->code;
		}

		return $language_code;
	}


	/**
	 * Processes form submit for forms created by actionCreateLabelForm()
	 * @return JsonHttpControllerResponse
	 */
	public function actionCreateLabelFormSubmit()
	{
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_id     = (int)$postDataArray['orders_id'];
		unset($postDataArray['orders_id']);
		$this->shipcloudLogger->notice(__FUNCTION__ . "\n" . print_r($postDataArray, true));
		try
		{
			if($this->isConfigured() === true)
			{
				$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
				$shipmentData    = $this->_prepareSingleFormDataForShipmentRequest($postDataArray, false,
				                                                                   $this->getOrderLanguageCode($orders_id));
				$shipmentId      = $shipmentFactory->createShipment($orders_id, $shipmentData);
				$contentArray    = array(
					'orders_id'   => $orders_id,
					'result'      => 'OK',
					'shipment_id' => $shipmentId,
				);
			}
			else
			{
				$contentArray = array(
					'orders_id'   => $orders_id,
					'result'      => 'UNCONFIGURED',
					'shipment_id' => 'n/a',
				);
			}
		}
		catch(Exception $e)
		{
			$contentArray = array(
				'orders_id'     => $orders_id,
				'result'        => 'ERROR',
				'error_message' => $e->getMessage()
			);
		}

		return MainFactory::create('JsonHttpControllerResponse', $contentArray);
	}


	/**
	 * Uses form data (cf. actionCreateLabelForm()) to retrieve a shipment quote
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetShipmentQuote()
	{
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_id     = (int)$postDataArray['orders_id'];
		unset($postDataArray['orders_id']);
		unset($postDataArray['to']['company']);
		unset($postDataArray['to']['first_name']);
		unset($postDataArray['to']['last_name']);
		unset($postDataArray['to']['care_of']);
		unset($postDataArray['notification_email']);
		unset($postDataArray['package']['declared_value']);
		unset($postDataArray['quote_carriers']);
		unset($postDataArray['cod']);

		try
		{
			if($this->isConfigured() === true)
			{
				$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
				$shipmentData    = $this->_prepareSingleFormDataForShipmentRequest($postDataArray, true);
				$shipmentQuote   = $shipmentFactory->getShipmentQuote($shipmentData);
				$contentArray    = array(
					'orders_id'      => $orders_id,
					'result'         => 'OK',
					'shipment_quote' => $shipmentQuote,
				);
			}
			else
			{
				$contentArray = array(
					'orders_id'      => $orders_id,
					'result'         => 'UNCONFIGURED',
					'shipment_quote' => '',
				);
			}
		}
		catch(Exception $e)
		{
			$contentArray = array(
				'orders_id'     => $orders_id,
				'result'        => 'ERROR',
				'error_message' => $e->getMessage()
			);
		}

		return MainFactory::create('JsonHttpControllerResponse', $contentArray);
	}


	/**
	 * Retrieves shipment quotes (bulk retrieval)
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetMultiShipmentQuote()
	{
		require_once DIR_FS_ADMIN . 'includes/classes/order.php';
		$carriersCache = MainFactory::create('ShipcloudCarriersCache');
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_ids    = $postDataArray['orders'];
		$contentArray  = array(
			'result'          => 'OK',
			'shipment_quotes' => array(),
			'quote_total'     => 0,
			'carriers_total'  => array(),
		);
		foreach($orders_ids as $orders_id)
		{
			$contentArray['shipment_quotes'][$orders_id] = array(
				'orders_id'      => $orders_id,
				'shipment_quote' => '',
			);
			$order = new order($orders_id);
			if(empty($order->delivery['house_number']))
			{
				$splitStreet = $this->splitStreet($order->delivery['street_address']);
			}
			else
			{
				$splitStreet = ['street' => $order->delivery['street_address'], 'house_no' => $order->delivery['house_number']];
			}
			foreach($postDataArray['quote_carriers'] as $carrier)
			{
				if(!isset($contentArray['carriers_total'][$carrier]))
				{
					$contentArray['carriers_total'][$carrier] = 0;
				}
				$carrierName = $carriersCache->getCarrier($carrier)->display_name;
				$getShipmentQuoteParams = array(
					'to' => array(
						'street'    => $splitStreet['street'],
						'street_no' => $splitStreet['house_no'],
						'city'      => $order->delivery['city'],
						'zip_code'  => $order->delivery['postcode'],
						'country'   => $order->delivery['country_iso_code_2'],
					),
					'package' => $postDataArray['package'],
					'carrier' => $carrier,
					'service' => $postDataArray['service'],
					'from' => array(
						'street'    => $this->shipcloudConfigurationStorage->get('from/street'),
						'street_no' => $this->shipcloudConfigurationStorage->get('from/street_no'),
						'city'      => $this->shipcloudConfigurationStorage->get('from/city'),
						'zip_code'  => $this->shipcloudConfigurationStorage->get('from/zip_code'),
						'country'   => $this->shipcloudConfigurationStorage->get('from/country'),
					)
				);
				$getShipmentQuoteParams['to'] = $this->_enforceLengthLimits($getShipmentQuoteParams['carrier'], $getShipmentQuoteParams['to']);
				try
				{
					$shipmentFactory                          = MainFactory::create('ShipcloudShipmentFactory');
					$shipmentQuote                            = $shipmentFactory->getShipmentQuote(MainFactory::create('KeyValueCollection', $getShipmentQuoteParams));
					$contentArray['carriers_total'][$carrier] += (double)str_replace(',', '.', $shipmentQuote);
					$shipment_quote                           = '<div class="sc_quote_line row"><div class="sc_carrier_name col-md-9">'.$carrierName.'</div>' .
					                                            '<div class="sc_quote_value col-md-3">'.$shipmentQuote.'</div></div>';
				}
				catch(Exception $e)
				{
					$shipment_quote = '<div class="sc_quote_line row" title="'.$e->getMessage().'"><div class="sc_carrier_name col-md-9">' . $carrierName . '</div>' .
					                  '<div class="sc_quote_value col-md-3">---</div></div>';
				}

				$contentArray['shipment_quotes'][$orders_id]['shipment_quote'] .= $shipment_quote;
			}
		}
		foreach($contentArray['carriers_total'] as $carrier => $total)
		{
			$contentArray['carriers_total'][$carrier] = sprintf('%.2f EUR', $total);
		}

		return new JsonHttpControllerResponse($contentArray);
	}


	/**
	 * Returns message to be displayed to users if the Shipcloud interface is still unconfigured
	 * @return HttpControllerResponse
	 */
	public function actionUnconfiguredNote()
	{
		$templateData = array(
			'sc_link'     => $this->shipcloudConfigurationStorage->get('boarding_url'),
			'config_link' => xtc_href_link('admin.php', 'do=ShipcloudModuleCenterModule'),
		);
		$html = $this->_render('shipcloud_unconfigurednote.html', $templateData);
		$html = $this->shipcloudText->replaceLanguagePlaceholders($html);

		return MainFactory::create('HttpControllerResponse', $html);
	}


	/**
	 * Outputs a list of labels for an order identified by its orders_id via the corresponding GET parameter
	 * @return HttpControllerResponse
	 */
	public function actionLoadLabelList()
	{
		$orders_id = (int)$this->_getQueryParameter('orders_id');
		$template_version = (int)$this->_getQueryParameter('template_version');
		try
		{
			$shipmentFactory         = MainFactory::create('ShipcloudShipmentFactory');
			$shipments               = $shipmentFactory->findShipments($orders_id);
			$pickupEarliestTimestamp = ceil(time() / 3600) * 3600;
			$pickupLatestTimestamp   = $pickupEarliestTimestamp + (2 * 3600);
			$page_token              = is_object($_SESSION['coo_page_token']) ? $_SESSION['coo_page_token']->generate_token() : '';
			$templateData            = array(
				'page_token'      => $page_token,
				'orders_id'       => $orders_id,
				'shipments'       => $shipments->shipments,
				'pickup_carriers' => array('dpd', 'fedex', 'hermes', 'ups'),
				'pickup_earliest' => date('Y-m-d H:i', $pickupEarliestTimestamp),
				'pickup_latest'   => date('Y-m-d H:i', $pickupLatestTimestamp),
				'pickup_mindate'  => date('Y/m/d', time()),
				'pickup_maxdate'  => date('Y/m/d', strtotime('+2 weeks')),
			);
			if($template_version == 2)
			{
				$html = $this->_render('shipcloud_labellist_v2.html', $templateData);
			}
			else
			{
				$html = $this->_render('shipcloud_labellist.html', $templateData);
			}
			$html = $this->shipcloudText->replaceLanguagePlaceholders($html);
		}
		catch(Exception $e)
		{
			$html .= '<p>ERROR: ' . $e->getMessage() . '</p>';
		}

		return new HttpControllerResponse($html);
	}

	/**
	 * Deletes a shipment label
	 */
	public function actionDeleteShipment()
	{
		$response = array();
		$postDataArray = $this->_getPostDataCollection()->getArray();
		if(empty($postDataArray['shipment_id']))
		{
			$response['result']        = 'ERROR';
			$response['error_message'] = 'no shipment ID given';
		}
		else
		{
			$shipmentId      = $postDataArray['shipment_id'];
			$deleteRequest   = MainFactory::create('ShipcloudRestRequest', 'DELETE', '/v1/shipments/' . $shipmentId);
			$restService     = MainFactory::create('ShipcloudRestService');
			$result          = $restService->performRequest($deleteRequest);
			$responseObject  = $result->getResponseObject();
			if($result->getResponseCode() != '204')
			{
				$response['result']        = 'ERROR';
				$response['error_message'] = is_array($responseObject->errors) ? implode('; ', $responseObject->errors) : 'unspecified error';
			}
			else
			{
				$response['result']      = 'OK';
				$response['text']        = $this->shipcloudText->get_text('shipment_deleted');
				$response['shipment_id'] = $shipmentId;
			}
		}
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}

	/**
	 * Requests pickups for a given list of shipments from each of the carriers involved
	 * @return JsonHttpControllerResponse
	*/

	public function actionPickupShipments()
	{
		$postDataArray      = $this->_getPostDataCollection()->getArray();
		$shipments          = $postDataArray['pickup_shipments'];
		$shipmentsByCarrier = array();
		foreach($shipments as $pickupShipment)
		{
			list($shippingId, $carrier) = explode('/', $pickupShipment);
			if(!is_array($shipmentsByCarrier[$carrier]))
			{
				$shipmentsByCarrier[$carrier] = array($shippingId);
			}
			else
			{
				$shipmentsByCarrier[$carrier][] = $shippingId;
			}
		}

		$pickupEarliestString = date('c', strtotime($postDataArray['pickup_earliest']));
		$pickupLatestString   = date('c', strtotime($postDataArray['pickup_latest']));
		$this->shipcloudLogger->notice(sprintf("earliest %s\nlatest %s", $pickupEarliestString, $pickupLatestString));

		$result_messages = array();
		foreach($shipmentsByCarrier as $carrier => $carrierShipmentIds)
		{
			try
			{
				$pickupRequestData = array(
					'carrier'     => $carrier,
					'pickup_time' => array('earliest' => $pickupEarliestString, 'latest' => $pickupLatestString),
					'shipments'   => array(),
				);
				foreach($carrierShipmentIds as $shipmentId)
				{
					$pickupRequestData['shipments'][] = array('id' => $shipmentId);
				}
				$pickupRequest  = MainFactory::create('ShipcloudRestRequest', 'POST', '/v1/pickup_requests',
				                                      $pickupRequestData);
				$restService    = MainFactory::create('ShipcloudRestService');
				$result         = $restService->performRequest($pickupRequest);
				$responseObject = $result->getResponseObject();
				if($result->getResponseCode() != '200')
				{
					foreach($responseObject->errors as $errorMessage)
					{
						$result_messages[] = sprintf("%s: %s\n", $this->shipcloudText->get_text('pickup_error'),
						                             $errorMessage);
					}
				}
				else
				{
					$result_messages[]= sprintf('%s, %s: %s %s',
					    $this->shipcloudText->get_text('pickup_confirmed'),
					    $this->shipcloudText->get_text('carrier_pickup_number'),
					    $this->shipcloudText->get_text('carrier_'.$responseObject->carrier),
					    $responseObject->carrier_pickup_number);
				}
			}
			catch(Exception $e)
			{
				$result_messages[] = sprintf('%s %s: %s', $this->shipcloudText->get_text('error_requesting_pickup'),
				                             $carrier, $e->getMessage());
			}
		}

		$result = array(
			'result_messages' => $result_messages,
		);

		return MainFactory::create('JsonHttpControllerResponse', $result);
	}


	/**
	 * Retrieves list of labels for a set of orders listed in POST[orders_ids].
	 * @return HttpControllerResponse
	 */
	public function actionLoadMultiLabelList()
	{
		$postData         = $this->_getPostDataCollection()->getArray();
		$template_version = (int)$this->_getQueryParameter('template_version');
		$params           = json_decode(stripcslashes($postData['json']));
		$orders_ids       = $params->orders_ids;
		$shipmentResults  = $params->shipments;

		$shipmentFactory  = MainFactory::create('ShipcloudShipmentFactory');
		$shipments        = array();
		foreach($shipmentResults as $shipmentResult)
		{
			if($shipmentResult->result == 'OK')
			{
				try
				{
					$shipment = $shipmentFactory->findShipments($shipmentResult->orders_id);
				}
				catch(Exception $e)
				{
					$this->shipcloudLogger->debug_notice(sprintf('no shipment found for orders_id %s: %s', $shipmentResult->orders_id,
					                                             $e->getMessage()));
				}
			}
			else
			{
				$shipment = new stdClass();
				$shipment->orders_id = $shipmentResult->orders_id;
				if($shipmentResult->result == 'ERROR')
				{
					$shipment->error_message = $shipmentResult->error_message;
				}
				else
				{
					$shipment->error_message = sprintf('unsupported result code %s', $shipmentResult->result);
				}
			}
			$shipments[$shipmentResult->orders_id] = $shipment;
		}
		$pickupEarliestTimestamp = ceil(time() / 3600) * 3600;
		$pickupLatestTimestamp   = $pickupEarliestTimestamp + (2 * 3600);
		$templateData            = array(
			'shipments'       => $shipments,
			'pickup_carriers' => array('dpd', 'fedex', 'hermes', 'ups'),
			'pickup_earliest' => date('Y-m-d H:i', $pickupEarliestTimestamp),
			'pickup_latest'   => date('Y-m-d H:i', $pickupLatestTimestamp),
			'pickup_mindate'  => date('Y/m/d', time()),
			'pickup_maxdate'  => date('Y/m/d', strtotime('+2 weeks')),
		);
		if($template_version == 2)
		{
			$html = $this->_render('shipcloud_multilabellist_v2.html', $templateData);
		}
		else
		{
			$html = $this->_render('shipcloud_multilabellist.html', $templateData);
		}
		$html                    = $this->shipcloudText->replaceLanguagePlaceholders($html);

		return new HttpControllerResponse($html);
	}


	/**
	 * Shows form for bulk label retrieval
	 * @return HttpControllerResponse
	 */
	public function actionCreateMultiLabelForm()
	{
		if($this->isConfigured() !== true)
		{
			return $this->actionUnconfiguredNote();
		}
		else
		{
			require DIR_FS_ADMIN . 'includes/classes/order.php';
			$orders_ids       = $this->_getQueryParameter('orders');
			$template_version = (int)$this->_getQueryParameter('template_version');
			$orders           = array();
			$orders_weights   = array();
			foreach($orders_ids as $orders_id)
			{
				$orders[$orders_id] = new order($orders_id);
				$orders_weights[$orders_id] = $this->_getShippingWeight($orders_id);
			}
			$default_package            = $this->shipcloudConfigurationStorage->get('default_package');
			$default_package_data       = $this->shipcloudConfigurationStorage->get_all_tree('packages/'.$default_package.'/');
			$default_package_dimensions = $default_package_data['packages'][$default_package];
			$templateData               = array(
				'orders'         => $orders,
				'orders_weights' => $orders_weights,
				'package'        => array(
					'weight' => $default_package_dimensions['weight'],
					'width'  => $default_package_dimensions['width'],
					'length' => $default_package_dimensions['length'],
					'height' => $default_package_dimensions['height'],
				),
				'package_templates'        => $this->shipcloudConfigurationStorage->get_all_tree('packages'),
				'default_package_template' => $default_package,
				//'carriers'                 => $this->shipcloudConfigurationStorage->getCarriers(),
				'preselected_carriers'     => $this->shipcloudConfigurationStorage->get_all_tree('preselected_carriers'),
				'checked_carriers'         => $this->shipcloudConfigurationStorage->get_all_tree('checked_carriers'),
			);
			$carriersCache = MainFactory::create('ShipcloudCarriersCache');
			$templateData['carriers'] = $carriersCache->getCarriers();

			if($template_version == 2)
			{
				$html = $this->_render('shipcloud_form_multi_v2.html', $templateData);
			}
			else
			{
				$html = $this->_render('shipcloud_form_multi.html', $templateData);
			}
			$html = $this->shipcloudText->replaceLanguagePlaceholders($html);

			return new HttpControllerResponse($html);
		}
	}

	/**
	 * computes total weight of products for an order
	 */
	protected function _getShippingWeight($orders_id)
	{
		$queryString =
			'SELECT
				ws.orders_id,
				SUM(ws.items_weight) AS shipping_weight
			FROM (
				SELECT
					op.orders_id,
					(op.products_quantity * p.products_weight) AS items_weight
				FROM
					`orders_products` op
				JOIN
					products p ON p.products_id = op.products_id
				WHERE
					op.orders_id = ?
			) ws
			GROUP BY ws.orders_id';
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$row = $db->query($queryString, array((int)$orders_id))->row();
		return (double)$row->shipping_weight;
	}

	/**
	 * Processes form data from form returned by actionCreateMultiLabelForm().
	 * @return JsonHttpControllerResponse
	 */
	public function actionCreateMultiLabelFormSubmit()
	{
		require DIR_FS_ADMIN . 'includes/classes/order.php';
		$postDataArray = $this->_getPostDataCollection()->getArray();
		$orders_ids    = $postDataArray['orders'];
		unset($postDataArray['orders']);
		$orders        = array();
		foreach($orders_ids as $orders_id)
		{
			$orders[$orders_id] = new order($orders_id);
		}
		$this->shipcloudLogger->notice(__FUNCTION__ . "\n" . print_r($postDataArray, true));

		$contentArray = array(
			'orders_ids' => $orders_ids,
			'result'     => 'UNDEFINED',
		);

		$shipmentFactory = MainFactory::create('ShipcloudShipmentFactory');
		foreach($orders as $orders_id => $order)
		{
			$this->shipcloudLogger->notice(sprintf('creating label for order %s', $orders_id));
			try
			{
				if(empty($order->delivery['house_number']))
				{
					$splitStreet = $this->splitStreet($order->delivery['street_address']);
				}
				else
				{
					$splitStreet = [
						'street'   => $order->delivery['street_address'],
						'house_no' => $order->delivery['house_number'],
					];
				}
				$singlePostDataArray       = array_merge($postDataArray);
				$singlePostDataArray['to'] = array(
					'company'    => $order->delivery['company'],
					'first_name' => $order->delivery['firstname'],
					'last_name'  => $order->delivery['lastname'],
					'care_of'    => $order->delivery['additional_address_info'],
					'street'     => $splitStreet['street'],
					'street_no'  => $splitStreet['house_no'],
					'city'       => $order->delivery['city'],
					'zip_code'   => $order->delivery['postcode'],
					'country'    => $order->delivery['country_iso_code_2'],
				);
				$singlePostDataArray['to']                 = $this->_enforceLengthLimits($postDataArray['carrier'],
				                                                                         $singlePostDataArray['to']);
				$singlePostDataArray['notification_email'] = $order->customer['email_address'];
				$shipmentData                              = $this->_prepareSingleFormDataForShipmentRequest($singlePostDataArray);
				$shipmentId                                = $shipmentFactory->createShipment($orders_id, $shipmentData);
				$contentArray['shipments'][]               = array(
					'orders_id'   => $orders_id,
					'shipment_id' => $shipmentId,
					'result'      => 'OK',
				);
			}
			catch(Exception $e)
			{
				$contentArray['shipments'][] = array(
					'orders_id'     => $orders_id,
					'error_message' => $e->getMessage(),
					'result'        => 'ERROR',
				);
			}
		}
		$contentArray['result'] = 'OK';

		return MainFactory::create('JsonHttpControllerResponse', $contentArray);
	}


	/**
	 * modifies an array containing a delivery address to suit carrier-specific field lengths
	 *
	 * Currently returns $toArray unchanged. Future operation tbd.
	 *
	 * @param  string $carrier
	 * @param  array $toArray delivery address
	 * @return array
	 */
	protected function _enforceLengthLimits($carrier, $toArray)
	{
		return $toArray;

		/*
		$lengthLimits = array(
			'dhl' => array(
				'company'   => array('min' => 2, 'max' =>  30, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' =>  30, 'empty_allowed' => false),
				'street'    => array('min' => 1, 'max' =>  40, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' =>   5, 'empty_allowed' => false),
				'zip_code'  => array('min' => 5, 'max' =>   5, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' =>  50, 'empty_allowed' => false),
			),
			'dpd' => array(
				'company'   => array('min' => 1, 'max' =>  35, 'empty_allowed' => true),
				'street'    => array('min' => 1, 'max' =>  35, 'empty_allowed' => false),
				'street_no' => array('min' => 0, 'max' =>   8, 'empty_allowed' => false),
				'zip_code'  => array('min' => 1, 'max' =>   9, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' =>  35, 'empty_allowed' => false),
			),
			'ups' => array(
				'company'   => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street'    => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' =>  10, 'empty_allowed' => false),
				'zip_code'  => array('min' => 0, 'max' =>  12, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'hermes' => array(
				'company'   => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street'    => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' =>  10, 'empty_allowed' => false),
				'zip_code'  => array('min' => 0, 'max' =>  12, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'gls' => array(
				'company'   => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street'    => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' =>  10, 'empty_allowed' => false),
				'zip_code'  => array('min' => 0, 'max' =>  12, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'fedex' => array(
				'company'   => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street'    => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' =>  10, 'empty_allowed' => false),
				'zip_code'  => array('min' => 0, 'max' =>  12, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
			'liefery' => array(
				'company'   => array('min' => 1, 'max' => 200, 'empty_allowed' => true),
				'last_name' => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street'    => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
				'street_no' => array('min' => 1, 'max' =>  10, 'empty_allowed' => false),
				'zip_code'  => array('min' => 0, 'max' =>  12, 'empty_allowed' => false),
				'city'      => array('min' => 1, 'max' => 200, 'empty_allowed' => false),
			),
		);
		$lengthLimitsName = array(
			'dhl' => 30,
			'ups' => 35,
		);
		$padding = '-';

		if(!in_array($carrier, array_keys($lengthLimits)))
		{
			throw new Exception('invalid carrier '.$carrier.' in '.__CLASS__.'::'.__METHOD__);
		}

		foreach($toArray as $key => $value)
		{
			if(!in_array($key, array_keys($lengthLimits[$carrier])))
			{
				// throw new Exception('invalid field '.$key.' in '.__CLASS__.'::'.__METHOD__);
				continue;
			}

			$valueLen = mb_strlen($value);
			if($valueLen < $lengthLimits[$carrier][$key]['min'])
			{
				$toArray[$key] = $value . str_repeat($padding, $lengthLimits[$carrier][$key]['min'] - $valueLen);
			}
			$toArray[$key] = mb_substr($value, 0, $lengthLimits[$carrier][$key]['max']);
		}

		if(in_array($carrier, array_keys($lengthLimitsName)))
		{
			$nameLength = mb_strlen($toArray['last_name'] . $toArray['first_name']);
			if($nameLength > $lengthLimitsName[$carrier])
			{
				$toArray['first_name'] = mb_substr($toArray['first_name'], 0, 1) . '.';
			}
			$nameLength = mb_strlen($toArray['last_name'] . $toArray['first_name']);
			if($nameLength > $lengthLimitsName[$carrier])
			{
				$toArray['last_name'] = mb_substr($toArray['last_name'], 0, $lengthLimitsName[$carrier] - 3);
			}
		}

		return $toArray;
		*/
	}
}