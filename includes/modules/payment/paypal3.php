<?php
/* --------------------------------------------------------------
   paypal3.php 2016-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class paypal3_ORIGIN {
	public $code, $title, $description, $enabled;
	public $tmpOrders = true;
	public $tmpStatus = 0;
	protected $text;
	protected $configStorage;
	protected $logger;
	protected $selection_logo = '';

	public function __construct()
	{
		$this->logger = MainFactory::create('PayPalLogger');
		$this->text = MainFactory::create('PayPalText');
		$this->configStorage = MainFactory::create('PayPalConfigurationStorage');
		$this->code = 'paypal3';
		$this->enabled = ((@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_STATUS') == 'True') ? true : false);
		$this->title = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_TITLE_ADMIN');
		$config_button = '<br><br><a href="'.xtc_href_link('admin.php', 'do=PayPalConfiguration').'" class="button" style="margin: auto; background-color: #E30000;">'.$this->text->get_text('configure').'</a><br><br>';
		$this->description = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION') . ($this->check() ? $config_button : '');
		$this->sort_order = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_SORT_ORDER');
		$this->info = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_INFO');
		$this->order_status = $this->configStorage->get('orderstatus/completed');
		$this->order_status_pending = $this->configStorage->get('orderstatus/pending');
		$this->order_status_error = $this->configStorage->get('orderstatus/error');
		//$this->tmpStatus = $this->order_status_pending;

		$logo_file = $this->configStorage->get('logo/image');
		if(!empty($logo_file) && strpos($_SERVER['PHP_SELF'], 'admin/modules.php') !== false)
		{
			$logo_style .= 'float: left; margin-left: 10px;';
			$this->title = '<img style="'.$logo_style.'" src="'.GM_HTTP_SERVER.DIR_WS_CATALOG.$logo_file.'" alt="PayPal">' . $this->title;
		}

		if(!empty($logo_file) && strpos($_SERVER['PHP_SELF'], 'checkout_payment') !== false)
		{
			$logo_style = '';
			$logo_position = $this->configStorage->get('logo/position');
			if($logo_position == 'left')
			{
				$logo_style .= 'float: left; margin-right: 10px;';
			}
			if($logo_position == 'right')
			{
				$logo_style .= 'float: right; margin-left: 10px;';
			}
			$this->selection_logo = '<img style="'.$logo_style.'" src="'.GM_HTTP_SERVER.DIR_WS_CATALOG.$logo_file.'">';
		}


		if(is_object($GLOBALS['order']))
		{
			$this->update_status();
		}

		// transport abandonment of withdrawal rights across POST/GET change of checkout_confirmation
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$_SESSION['paypal_abandonment_download'] = isset($_POST['abandonment_download']) ? 'true' : 'false';
			$_SESSION['paypal_abandonment_service'] = isset($_POST['abandonment_service']) ? 'true' : 'false';
		}
		else
		{
			if(isset($_SESSION['paypal_abandonment_download']))
			{
				$_SESSION['abandonment_download'] = $_SESSION['paypal_abandonment_download'];
				if($_SESSION['paypal_abandonment_download'] === 'true')
				{
					$_POST['abandonment_download'] = 'true';
				}
			}
			if(isset($_SESSION['paypal_abandonment_service']))
			{
				$_SESSION['abandonment_service'] = $_SESSION['paypal_abandonment_service'];
				if($_SESSION['paypal_abandonment_service'] === 'true')
				{
					$_POST['abandonment_service'] = 'true';
				}
			}
		}
	}

	protected function _initLanguageConstants()
	{
		$constant_names = array(
				'MODULE_PAYMENT_PAYPAL3_TEXT_DESCRIPTION',
				'MODULE_PAYMENT_PAYPAL3_TEXT_TITLE',
				'MODULE_PAYMENT_PAYPAL3_TEXT_TITLE_ADMIN',
				'MODULE_PAYMENT_PAYPAL3_TEXT_INFO',
				'MODULE_PAYMENT_PAYPAL3_STATUS_TITLE',
				'MODULE_PAYMENT_PAYPAL3_STATUS_DESC',
				'MODULE_PAYMENT_PAYPAL3_SORT_ORDER_TITLE',
				'MODULE_PAYMENT_PAYPAL3_SORT_ORDER_DESC',
				'MODULE_PAYMENT_PAYPAL3_ZONE_TITLE',
				'MODULE_PAYMENT_PAYPAL3_ZONE_DESC',
				'MODULE_PAYMENT_PAYPAL3_ALLOWED_TITLE',
				'MODULE_PAYMENT_PAYPAL3_ALLOWED_DESC',
			);
		foreach($constant_names as $constant_name)
		{
			defined($constant_name) or define($constant_name, $this->text->get_text($constant_name));
		}
	}

	public function update_status()
	{
		$order = $GLOBALS['order'];

		if(!$this->_isConfigured())
		{
			$this->enabled = false;
		}

		if(
			$this->configStorage->get('allow_selfpickup') == false &&
			isset($_SESSION['shipping']) &&
			is_array($_SESSION['shipping']) &&
			$_SESSION['shipping']['id'] == 'selfpickup_selfpickup'
		  )
		{
			$this->enabled = false;
		}

		if(($this->enabled == true) && ((int) @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ZONE') > 0))
		{
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from ".TABLE_ZONES_TO_GEO_ZONES." where geo_zone_id = '".@constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ZONE')."' and zone_country_id = '".$order->billing['country']['id']."' order by zone_id");
			while($check = xtc_db_fetch_array($check_query))
			{
				if($check['zone_id'] < 1)
				{
					$check_flag = true;
					break;
				}
				elseif($check['zone_id'] == $order->billing['zone_id'])
				{
					$check_flag = true;
					break;
				}
			}

			if($check_flag == false)
			{
				$this->enabled = false;
			}
		}
	}

	protected function _isConfigured()
	{
		$isConfigured = false;
		$mode = $this->configStorage->get('mode');
		$client_id = $this->configStorage->get('restapi-credentials/'.$mode.'/client_id');
		$secret = $this->configStorage->get('restapi-credentials/'.$mode.'/secret');
		if(!empty($client_id) && !empty($secret))
		{
			$isConfigured = true;
		}
		return $isConfigured;
	}

	function javascript_validation()
	{
		return false;
	}

	protected function _getProcessedOrderAndTotals()
	{
		$globals_order = $GLOBALS['order'];
		$globals_payment = $_SESSION['payment'];
		$_SESSION['payment'] = $this->code;
		$order = new order();
		$GLOBALS['order'] = $order;
		$order_total = new order_total();
		$order_total_array = $order_total->process();
		$order = $GLOBALS['order'];
		$GLOBALS['order'] = $globals_order;
		$_SESSION['payment'] = $globals_payment;
		$order_and_totals = array(
			'order' => $order,
			'totals' => $order_total_array,
		);
		return $order_and_totals;
	}

	protected function _getSid($add_session_id = true, $connection = 'SSL')
	{
		$sid = '';
		if(($add_session_id == true) && ($GLOBALS['session_started'] == true) && (SESSION_FORCE_COOKIE_USE == 'False'))
		{
			if(defined('SID') && xtc_not_null(SID))
			{
				$sid = SID;
			}
			elseif((($GLOBALS['request_type'] == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true)) ||
				   (($GLOBALS['request_type'] == 'SSL') && ($connection == 'NONSSL')))
			{
				if($GLOBALS['http_domain'] != $GLOBALS['https_domain'])
				{
		  			$sid = session_name() . '=' . session_id();
				}
			}
		}
		return $sid;
	}

	public function _selectionPayPalPlus()
	{
		try {
			$order = isset($GLOBALS['order']) ? $GLOBALS['order'] : new order();
			if(isset($_SESSION['paypal_payment']) &&
				$_SESSION['paypal_payment']['type'] == 'plus' &&
				$_SESSION['paypal_payment']['state'] == 'created' &&
				$_SESSION['paypal_payment']['cartID'] == $_SESSION['cart']->cartID)
			{
				$payPalPayment = MainFactory::create('PayPalPayment', $_SESSION['paypal_payment']['id']);
				$this->logger->notice('re-using existing payment resource '.$_SESSION['paypal_payment']['id']);
			}
			else
			{
				$payPalPaymentFactory = MainFactory::create_object('PayPalPaymentFactory');
				$order_and_totals = $this->_getProcessedOrderAndTotals();
				$payPalPayment = $payPalPaymentFactory->createPaymentFromOrder($order_and_totals['order'], 'plus');
				$_SESSION['paypal_payment'] = array(
					'id' => $payPalPayment->id,
					'type' => 'plus',
					'state' => 'created',
					'cartID' => $_SESSION['cart']->cartID,
				);
			}
			$approvalUrl = $payPalPayment->getLink('approval_url');
			switch($_SESSION['language_code'])
			{
				case 'de':
					$language = 'de_DE';
					break;
				case 'en':
					if($order->customer['country']['iso_code_2'] == 'US')
					{
						$language = 'en_US';
					}
					else
					{
						$language = 'en_GB';
					}
					break;
				default:
					$language = strtolower($_SESSION['language_code']).'_XC';
			}
			$preselection = $this->_hasLowestPriority() ? 'paypal' : 'none';
			$sid = $this->_getSid();
			if(!empty($sid))
			{
				$sid = '&'.$sid;
			}

			$ppplusConfigJSON = '{
					"approvalUrl": "'.(string)$approvalUrl->href.'",
					"placeholder": "ppplus",
					"mode": "'.$this->configStorage->get('mode').'",
					"country": "'.$order->billing['country']['iso_code_2'].'",
					"language": "'.$language.'",
					"preselection": "'.$preselection.'",
					"showPuiOnSandbox": "true",
					"buttonLocation": "outside",
					"disableContinue": function()
					{
						$("#ppplus_continue").css("opacity", "0.1");
						$(\'div.payment_item input[value="paypal3"]\').get(0).checked = false;
					},
					"enableContinue": function()
					{
						$("div.payment_item input[value=paypal3]").trigger("click");
						$("#ppplus_continue").css("opacity", "1.0");
					},
					"onContinue": function()
					{
						$("#ppplus_continue").closest("form").trigger("submit", ["trigger"]);
					},
					"styles":
					{
						"psp": {
							"font-size": "14px",
							"font-family": "Arial,Tahoma,Verdana",
							"color": "#666",
						}
					},
					"thirdPartyPaymentMethods": thirdPartyPayments,
				}';
			$this->logger->debug_notice("PayPal PLUS configuration generated:\n" . $ppplusConfigJSON);
			$tmpPlaceholder = '<div style="text-align: center;"><img src="'.GM_HTTP_SERVER.DIR_WS_CATALOG.'images/ladebalken.gif"></div>';
			$ppplusSnippet = '<div id="ppplus" style="width: 100%; min-height: 20px;">'.$tmpPlaceholder.'</div>';
			$ppplusSnippet .= '<script> var ppp; var initPPP = function(thirdPartyPayments) { ppp = PAYPAL.apps.PPP('.$ppplusConfigJSON.');';
			if($preselection === 'none')
			{
				$ppplusSnippet .= ' ppp.deselectPaymentMethod(); $("ul.paypal3-plus-checkout li:first").trigger("click"); ';
			}
			else
			{
				$ppplusSnippet .= ' /* pp+ preselected */ ';
			}
			$ppplusSnippet .= ' };</script>';
			$description = $ppplusSnippet;
		}
		catch(Exception $e)
		{
			$errorMessage = 'Could not instantiate payment for PP+: '.$e->getMessage();
			$this->logger->notice($errorMessage);
			$_SESSION['ppplus_disabled'] = true;
			xtc_redirect(xtc_href_link('checkout_payment.php', '', 'SSL'));
			return false;
		}

		$this->logger->notice('Created payment for PP+ payment wall: '.$_SESSION['paypal_payment']['id']);

		$selection = array(
			'id' => $this->code,
			'module' => '',
			'description' => $description,
			'fields' => array(),
		);
		return $selection;
	}

	protected function _hasLowestPriority()
	{
		$minSortOrderQuery =
			"SELECT
				CAST(c.configuration_value AS DECIMAL(20,5)) AS decvalue, c.configuration_value
			FROM
				`configuration` c
			WHERE
				`configuration_key` LIKE 'module_payment_%_sort_order'
			ORDER BY
				decvalue ASC
			LIMIT 1";
		$minSortOrderResult = xtc_db_query($minSortOrderQuery);
		$minSortOrder = 0;
		while($row = xtc_db_fetch_array($minSortOrderResult))
		{
			$minSortOrder = (double)$row['configuration_value'];
		}
		$hasLowestPriority = (double)constant('MODULE_PAYMENT_'.strtoupper($this->code).'_SORT_ORDER') <= $minSortOrder;
		return $hasLowestPriority;
	}

	public function selection()
	{
		/*
		if(isset($_GET['payment_error']) && $_GET['payment_error'] == $this->code)
		{
			return false;
		}
		*/

		if(isset($_GET['paypal']) && $_GET['paypal'] == 'cancel')
		{
			unset($_SESSION['payment']);
		}

		/*
		$canUsePlus =
			constant('STORE_COUNTRY') == 81 &&
			$GLOBALS['order']->customer['country']['iso_code_2'] == 'DE' &&
			$GLOBALS['order']->delivery['country']['iso_code_2'] == 'DE' &&
			$GLOBALS['order']->billing['country']['iso_code_2'] == 'DE';
		*/
		$canUsePlus = true;

		// default values for ECM mode
		$selection = array(
			'id' => $this->code,
			'module' => @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_TITLE'),
			'description' => $this->selection_logo . @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_DESCRIPTION'),
			'fields' => array(),
		);

		$countryService = StaticGXCoreLoader::getService('Country');
		$stateRequiredCountries = explode(',', 'AR,BR,CA,CN,ID,IN,JP,MX,TH,US');
		$stateRequired = is_array($GLOBALS['order']->delivery['country']) && in_array($GLOBALS['order']->delivery['country']['iso_code_2'], $stateRequiredCountries);
		if($stateRequired)
		{
			$deliveryCountry = $countryService->getCountryById(new IdType($GLOBALS['order']->delivery['country']['id']));
			if($countryService->countryHasCountryZones($deliveryCountry))
			{
				$countryZones = $countryService->findCountryZonesByCountryId(new IdType($GLOBALS['order']->delivery['country']['id']));
				$selection['description'] .= '<br>'.$this->text->get_text('select_your_state').': ';
				$selection['description'] .= '<select name="paypal_zone">';
				foreach($countryZones as $cZone)
				{
					if($GLOBALS['order']->delivery['state'] == (string)$cZone->getName() || $GLOBALS['order']->delivery['state'] == (string)$cZone->getCode())
					{
						$zoneSelected = ' selected="selected"';
					}
					else
					{
						$zoneSelected = '';
					}
					$selection['description'] .= '<option '.$zoneSelected.' value="'.(string)$cZone->getId().'">'.(string)$cZone->getName().'</option>';
				}
				$selection['description'] .= '</select>';
			}
		}

		$calledFromCheckoutPayment = is_object($GLOBALS['order']) && strpos($_SERVER['PHP_SELF'], 'checkout_payment');
		$isShortcutPayment = isset($_SESSION['paypal_payment']) && $_SESSION['paypal_payment']['type'] == 'ecs';

		if($isShortcutPayment)
		{
			if(!isset($_SESSION['paypal_payment']['is_guest']) && $_SESSION['paypal_payment']['cartID'] != $_SESSION['cart']->cartID)
			{
				unset($_SESSION['paypal_payment']);
				$isShortcutPayment = false;
			}
			else
			{
				$selection['description'] .= ' <!-- (ECS) -->';
			}
		}

		if(isset($_SESSION['ppplus_disabled']) === false && $calledFromCheckoutPayment && $isShortcutPayment === false && $stateRequired === false)
		{
			if($canUsePlus && $this->configStorage->get('use_paypal_plus') == true)
			{
				$plus_selection = $this->_selectionPayPalPlus();
				$selection = $plus_selection !== false ? $plus_selection : $selection;
			}
			else
			{
				unset($_SESSION['paypal_payment']);
			}
		}

		return $selection;
	}

	function pre_confirmation_check()
	{
		$confirmation_ok = false;

		if(isset($_POST['paypal_zone']))
		{
			$countryService = StaticGXCoreLoader::getService('Country');
			$paypalZone = $countryService->getCountryZoneById(new IdType((int)$_POST['paypal_zone']));
			$_SESSION['paypal_state'] = (string)$paypalZone->getCode();
		}
		/*
		else
		{
			unset($_SESSION['paypal_state']);
		}
		*/

		if(!isset($_GET['paymentId']))
		{
			if(gm_get_conf('GM_CHECK_CONDITIONS') != 1)
			{
				$_SESSION['conditions'] = 'true/not_reqd';
			}
			if(gm_get_conf('GM_CHECK_WITHDRAWAL') != 1)
			{
				$_SESSION['withdrawal'] = 'true/not_reqd';
			}
			if($_SESSION['paypal_payment']['type'] == 'ecs' && !empty($_SESSION['paypal_payment']['payer_id']))
			{
				$confirmation_ok = true;
			}
			elseif($_SESSION['paypal_payment']['type'] == 'plus')
			{
				// perform last-second updates
				try {
					if($_SESSION['cart']->get_content_type() != 'virtual')
					{
						$this->logger->notice('Updating PLUS payment with shipping/billing address: '.$_SESSION['paypal_payment']['id']);
						$paymentFactory = MainFactory::create('PayPalPaymentFactory');
						$paymentFactory->updatePaymentFromOrder($_SESSION['paypal_payment']['id'], $GLOBALS['order']);
					}
					$this->logger->notice('Redirecting to PayPal for Plus payment');
					echo '<!DOCTYPE html><html><head><script src="https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js" type="text/javascript"></script></head>';
					echo '<body><script>PAYPAL.apps.PPP.doCheckout();</script><p>'.$this->text->get_text('redirecting_to_paypal').'</p></body>';
					xtc_db_close();
					exit;
				}
				catch(Exception $e)
				{
					$errorMessage = $this->text->get_text('error_updating_payment');
					$_SESSION['paypal3_error'] = $errorMessage;
					$this->logger->error($errorMessage.' ('.$e->getMessage().')');
					unset($_SESSION['payment']);
					unset($_SESSION['paypal_payment']);
					xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
				}
			}
			else // type == 'ecm'
			{
				$this->logger->notice('Instantiating Payment for ECM mode');
				try
				{
					$order_total = new order_total();
					$order_total_array = $order_total->process();
					$paypalState = isset($_SESSION['paypal_state']) ? $_SESSION['paypal_state'] : null;
					$payPalPaymentFactory = MainFactory::create_object('PayPalPaymentFactory');
					$payPalPayment = $payPalPaymentFactory->createPaymentFromOrder($GLOBALS['order'], 'ecm', $paypalState);
					$approvalLinkEntry = $payPalPayment->getLink('approval_url');
					$approvalUrl = $approvalLinkEntry->href;
					$approvalUrl .= '&LocaleCode='.$this->getLocale();
					$_SESSION['paypal_payment'] = array(
						'id' => $payPalPayment->id,
						'type' => 'ecm',
						'state' => 'created',
						'cartID' => $_SESSION['cart']->cartID,
					);
					# die("<br>\nApproval URL: ".$approvalUrl);
					$this->logger->notice('ECM payment created with id '.$payPalPayment->id.', redirecting customer to '.$approvalUrl);
					xtc_redirect($approvalUrl);
				}
				catch(Exception $e)
				{
					$errorMessage = $this->text->get_text('error_creating_ecm_payment');
					$this->logger->notice($errorMessage.': '.$e->getMessage());
					$_SESSION['paypal3_error'] = $this->text->get_text('paypal_unavailable');
					unset($_SESSION['payment']);
					xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
				}
			}
		}

		if(!empty($_GET['paymentId']) &&
			$_GET['paymentId'] == $_SESSION['paypal_payment']['id'] &&
			(!empty($_GET['PayerID']) || !empty($_SESSION['paypal_payment']['payer_id'])))
		{
			$this->logger->notice('Payment approved by customer: '.$_SESSION['paypal_payment']['id'].', PayerID: '.$_GET['PayerID']);
			$confirmation_ok = true;
			$_SESSION['paypal_payment']['state'] = 'approved';
			$_SESSION['paypal_payment']['payer_id'] = $_GET['PayerID'];
		}

		try
		{
			$paypalPayment = MainFactory::create('PayPalPayment', $_SESSION['paypal_payment']['id']);
		}
		catch(Exception $e)
		{
			$errorMessage = $this->text->get_text('error_retrieving_payment');
			unset($_SESSION['paypal_payment']);
			$this->logger->notice($errorMessage.': '.$e->getMessage());
			$_SESSION['paypal3_error'] = $errorMessage;
			xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
		}

		if($confirmation_ok !== true)
		{
			$this->logger->notice("pre_confirmation_check failed, GET:\n".print_r($_GET, true));
			$_SESSION['paypal3_error'] = $this->text->get_text('paypal_unavailable');
			unset($_SESSION['payment']);
			unset($_SESSION['paypal_payment']);
			xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
		}

		return $confirmation_ok;
	}

	/** determines LocaleCode from session data */
	protected function getLocale()
	{
		$language = strtolower($_SESSION['language_code']);
		$shop_country_data = $this->_findCountryByID(STORE_COUNTRY);
		switch ($language)
		{
			case 'de':
				$country_iso_2 = 'DE';
				break;

			case 'en':
				$country_iso_2 = 'US';
				break;

			default:
				$country_iso_2 = $shop_country_data['country_iso_2'];
				break;
		}
		$locale = sprintf('%s_%s', $language, $country_iso_2);
		return $locale;
	}

	protected function _findCountryByID($country_id) {
		$query = "SELECT * FROM `countries` WHERE countries_id = ':countries_id'";
		$query = strtr($query, array(':countries_id' => (int)$country_id));
		$result = xtc_db_query($query);
		$country_data = false;
		while($row = xtc_db_fetch_array($result)) {
			$country_data = $row;
		}
		return $country_data;
	}

	function confirmation()
	{
		$confirmation = array(
			'title' => $this->text->get_text('checkout_confirmation_info'),
		);
		return $confirmation;
	}

	function refresh()
	{
	}

	function process_button()
	{
		$order = $GLOBALS['order'];
		$pb = '';
		return $pb;
	}

	function after_process()
	{
		// $GLOBALS['order'], $_SESSION['tmp_oID'], $GLOBALS['order_totals']
		if(!empty($_SESSION['paypal_final_order_status']))
		{
			// set orders_status again, in case something has changed it
			$insert_id = (int)$_SESSION['tmp_oID'];
			$this->logger->notice(sprintf('Re-setting order status for %d to %d', $insert_id, $_SESSION['paypal_final_order_status']));
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$_SESSION['paypal_final_order_status']."' WHERE orders_id='".$insert_id."'");
			unset($_SESSION['paypal_final_order_status']);
		}
	}

	function before_process()
	{
		return false;
	}

	function payment_action()
	{
		$insert_id = $GLOBALS['insert_id'];
		$order = new order($insert_id);
		$this->logger->notice('Executing payment '.$_SESSION['paypal_payment']['id'] .' for orders_id '.$insert_id);

		try
		{
			$paypalPayment = MainFactory::create('PayPalPayment', $_SESSION['paypal_payment']['id']);
		}
		catch(Exception $e)
		{
			$errorMessage = $this->text->get_text('error_retrieving_payment');
			unset($_SESSION['paypal_payment']);
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status_error."' WHERE orders_id='".$insert_id."'");
			$this->_addOrdersStatusHistoryEntry($insert_id, $this->order_status_error, $errorMessage."\n".$e->getMessage());
			$this->logger->notice($errorMessage.': '.$e->getMessage());
			$_SESSION['paypal3_error'] = $errorMessage;
			xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
		}

		try
		{
			$paymentFactory = MainFactory::create('PayPalPaymentFactory');
			if($_SESSION['cart']->get_content_type() != 'virtual')
			{
				$this->logger->notice('Updating payment with final shipping/billing address: '.$_SESSION['paypal_payment']['id']);
				$paymentFactory->updatePaymentFromOrder($_SESSION['paypal_payment']['id'], $GLOBALS['order']);
			}
			$this->logger->notice(sprintf('adding invoice_number to payment %s: %s', $_SESSION['paypal_payment']['id'], $order->info['orders_id']));
			$paymentFactory->addInvoiceNumber($_SESSION['paypal_payment']['id'], $order);
			// execute payment
			$isPlus = $_SESSION['paypal_payment']['type'] == 'plus';
			$state = isset($_SESSION['paypal_state']) ? $_SESSION['paypal_state'] : null;
			$paypalPayment->execute($_SESSION['paypal_payment']['payer_id'], $order, $isPlus, $state);
			$this->_addPaymentToOrder($insert_id, $paypalPayment->id, $this->configStorage->get('mode'));
			$this->logger->notice('Reloading payment to find payment instruction: '.$paypalPayment->id);
			$paypalPayment = MainFactory::create('PayPalPayment', $paypalPayment->id);
			$paymentInstruction = $paypalPayment->payment_instruction;
			if($paymentInstruction !== null)
			{
				$this->logger->notice(sprintf('storing payment instruction for order %d in database', $order->info['orders_id']));
				$this->_storePaymentInstruction($paymentInstruction, $order->info['orders_id']);
			}

			if(isset($paypalPayment->transactions[0]->related_resources[0]->sale))
			{
				$transaction = $paypalPayment->transactions[0]->related_resources[0]->sale;
			}
			elseif(isset($paypalPayment->transactions[0]->related_resources[0]->authorization))
			{
				$transaction = $paypalPayment->transactions[0]->related_resources[0]->authorization;
			}
			elseif(isset($paypalPayment->transactions[0]->related_resources[0]->order))
			{
				$transaction = $paypalPayment->transactions[0]->related_resources[0]->order;
			}

			if($transaction->state == 'pending' && $this->order_status_pending)
			{
				$final_order_status = $this->order_status_pending;
				$paymentInstructionLink = false;
				foreach($transaction->links as $transactionLink)
				{
					if($transactionLink->rel == 'payment_instruction_redirect')
					{
						$paymentInstructionLink = $transactionLink->href;
					}
				}
				if($paymentInstructionLink !== false)
				{
					$this->logger->notice('Bank pending flow activated, link: '.$paymentInstructionLink);
					$_SESSION['paypal_payment_instruction_href'] = $paymentInstructionLink;
				}
				else
				{
					$this->logger->notice('No bank pending flow required');
				}
			}
			elseif($this->order_status)
			{
				$final_order_status = $this->order_status;
			}

			$_SESSION['paypal_final_order_status'] = $final_order_status;
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$final_order_status."' WHERE orders_id='".$insert_id."'");
			$status_comments = '';
			$status_comments .= $this->text->get_text('checkout_mode').': '.strtoupper($_SESSION['paypal_payment']['type']);
			if(isset($_SESSION['paypal_payment']['is_guest']) && $_SESSION['paypal_payment']['is_guest'] == true)
			{
				$this->logger->notice('Payment '.$_SESSION['paypal_payment']['id'].' was initiated by ECS');
				$status_comments .= "\n".$this->text->get_text('guest_customer_created_from_ecs_data');

				// void session for ECS guests unless cart contains download products
				$isDownload = false;
				foreach($_SESSION['cart']->get_products() as $product)
				{
					if($product['product_type'] == '2')
					{
						$isDownload = true;
					}
				}
				if($isDownload == false)
				{
					$_SESSION['paypal_ecs_logout_required'] = true;
				}
			}
			$this->_addOrdersStatusHistoryEntry($insert_id, $final_order_status, $status_comments);
			$this->logger->notice('Payment '.$_SESSION['paypal_payment']['id'].' executed.');
			$GLOBALS['tmp'] = false;
		}
		catch(Exception $e)
		{
			$errorMessage = $this->text->get_text('error_executing_payment');
			unset($_SESSION['paypal_payment']);
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status_error."' WHERE orders_id='".$insert_id."'");
			$this->_addOrdersStatusHistoryEntry($insert_id, $this->order_status_error, $errorMessage."\n".$e->getMessage());
			$this->logger->notice('ERROR executing payment '.$paypalPayment->id.' - '.$e->getMessage());

			$_SESSION['paypal3_error'] = $errorMessage;
			xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error='.$this->code, 'SSL'));
		}

		unset($_SESSION['paypal_payment']);
		unset($_SESSION['paypal_state']);
		unset($_SESSION['paypal_abandonment_service']);
		unset($_SESSION['paypal_abandonment_download']);
	}

	/**
	 * stores payment instruction (for payment upon invoice)
	 */
	protected function _storePaymentInstruction(stdClass $paymentInstruction, $orders_id)
	{
		$orders_id = (int)$orders_id;
		if($orders_id <= 0)
		{
			throw new Exception('invalid value for orders_id');
		}
		$insert_query =
			"INSERT
				INTO `orders_payment_instruction`
			SET
				`orders_id` = ':orders_id',
				`reference` = ':reference',
				`bank_name` = ':bank_name',
				`account_holder` = ':account_holder',
				`iban` = ':iban',
				`bic` = ':bic',
				`value` = ':value',
				`currency` = ':currency',
				`due_date` = ':due_date'";
		$insert_query = strtr($insert_query, array(
				':orders_id' => $orders_id,
				':reference' => xtc_db_input($paymentInstruction->reference_number),
				':bank_name' => xtc_db_input($paymentInstruction->recipient_banking_instruction->bank_name),
				':account_holder' => xtc_db_input($paymentInstruction->recipient_banking_instruction->account_holder_name),
				':iban' => xtc_db_input($paymentInstruction->recipient_banking_instruction->international_bank_account_number),
				':bic' => xtc_db_input($paymentInstruction->recipient_banking_instruction->bank_identifier_code),
				':value' => sprintf('%.2f', (double)$paymentInstruction->amount->value),
				':currency' => xtc_db_input($paymentInstruction->amount->currency),
				':due_date' => xtc_db_input($paymentInstruction->payment_due_date),
			));
		xtc_db_query($insert_query);
	}

	protected function _addOrdersStatusHistoryEntry($orders_id, $orders_status_id, $comments)
	{
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
		xtc_db_query($insert_query);
	}

	protected function _addPaymentToOrder($orders_id, $payment_id, $mode)
	{
		$query =
			'REPLACE
				INTO `orders_paypal_payments`
			SET
				`orders_id` = \':orders_id\',
				`payment_id` = \':payment_id\',
				`mode` = \':mode\'
			';
		$query = strtr($query, array(':orders_id' => (int)$orders_id, ':payment_id' => xtc_db_input($payment_id), ':mode' => xtc_db_input($mode)));
		xtc_db_query($query);
	}

	function get_error()
	{
		$error = false;
		if(isset($_SESSION['paypal3_error']))
		{
			$error = array('error' => $_SESSION['paypal3_error']);
			unset($_SESSION['paypal3_error']);
		}
		return $error;
	}

	function check()
	{
		if(!isset($this->_check))
		{
			$check_query = xtc_db_query("select configuration_value from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_".  strtoupper($this->code) ."_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install()
	{
		$config = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data) {
			$install_query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
					"values ('MODULE_PAYMENT_".strtoupper($this->code)."_".$key."', '".$data['configuration_value']."', '6', '".$sort_order."', '".addslashes($data['set_function'])."', '".addslashes($data['use_function'])."', now())";
			xtc_db_query($install_query);
			$sort_order++;
		}
	}

	function remove()
	{
		xtc_db_query("delete from ".TABLE_CONFIGURATION." where configuration_key in ('".implode("', '", $this->keys())."')");
	}

	/**
	 * Determines the module's configuration keys
	 * @return array
	 */
	function keys()
	{
		$ckeys = array_keys($this->_configuration());
		$keys = array();
		foreach($ckeys as $k)
		{
			$keys[] = 'MODULE_PAYMENT_'.strtoupper($this->code).'_'.$k;
		}
		return $keys;
	}

	function isInstalled()
	{
		$isInstalled = true;
		foreach($this->keys() as $key)
		{
			if(!defined($key))
			{
				$isInstalled = false;
			}
		}
		return $isInstalled;
	}

	function _configuration()
	{
		$config = array(
			'STATUS' => array(
				'configuration_value' => 'True',
				'set_function' => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'SORT_ORDER' => array(
				'configuration_value' => '-9999',
			),
			'ALLOWED' => array(
				'configuration_value' => '',
			),
			'ZONE' => array(
				'configuration_value' => '0',
				'use_function' => 'xtc_get_zone_class_title',
				'set_function' => 'xtc_cfg_pull_down_zone_classes(',
			),
		);

		return $config;
	}

}

MainFactory::load_origin_class('paypal3');