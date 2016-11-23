<?php
/* --------------------------------------------------------------
	PayPalConfiguration.inc.php 2015-12-08
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class PayPalConfigurationController
 * @package HttpViewControllers
 */
class PayPalConfigurationController extends AdminHttpViewController
{
	/**
	 * @var array Array with Values that are passed to the template
	 */
	protected $valueArray = array();

	/**
	 * @var PayPalText Helper for language-specific texts
	 */
	protected $paypalText;

	/**
	 * @var PayPalConfigurationStorage
	 */
	protected $configurationStorage;

	protected $logger;

	const MESSAGES_NAMESPACE = __class__;

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
		if($_SESSION['customers_status']['customers_status_id'] != 0)
		{
			throw new Exception('unauthorized access');
		}
		$contentView->set_caching_enabled(false);
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
		if(!is_array($_SESSION[self::MESSAGES_NAMESPACE]))
		{
			$_SESSION[self::MESSAGES_NAMESPACE] = array();
		}
		$this->paypalText = MainFactory::create('PayPalText');
		$this->configurationStorage = MainFactory::create('PayPalConfigurationStorage');
		$this->logger = MainFactory::create('PayPalLogger');
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/paypal3/');
	}

	/**
	* adds stripslashes() to parent::_getPostData() to reverse the forced magic quotes introduced by the compatibiliy layer
	*/
	protected function _getPostData($name)
	{
		$value = parent::_getPostData($name);
		if(is_string($value))
		{
			$value = stripslashes($value);
		}
		return $value;
	}


	/**
	 * Run the actionDefault method.
	 * This is invoked through admin/admin.php?do=PayPalConfiguration
	 *
	 * @return \AdminPageHttpControllerResponse
	 */
	public function actionDefault()
	{
		if($this->_isConfigured() === false && !$this->_getQueryParameter('skip_firstconfig') == '1')
		{
			return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=PayPalConfiguration/FirstTime'));
		}
		$this->_prepareValuesArray(true);
		if(!empty($this->valueArray['messages']))
		{
			isset($GLOBALS['messageStack']) or $GLOBALS['messageStack'] = new messageStack();
			foreach($this->valueArray['messages'] as $message)
			{
				$GLOBALS['messageStack']->add($message['text'], $message['class']);
			}
		}

		$this->valueArray['pex_block'] = $this->_render('admin_paypal_configuration_pexprofiles.html', $this->valueArray);
		$html = $this->_render('admin_paypal_configuration.html', $this->valueArray);
		$html = $this->paypalText->replaceLanguagePlaceholders($html);

		return MainFactory::create('AdminPageHttpControllerResponse', $this->paypalText->get_text('paypal_configuration'), $html);
	}

	/**
	 * shows first-time configuration assistant
	 */
	public function actionFirstTime()
	{
		if(class_exists('LogControl'))
		{
			$secure_token = LogControl::get_secure_token();
		}
		else
		{
			$secure_token = FileLog::get_secure_token();
		}

		$create_webhook = ENABLE_SSL_CATALOG === 'true' || ENABLE_SSL_CATALOG === true;
		// $use_paypal_plus = constant('STORE_COUNTRY') == 81 ? '1' : '0';
		$use_paypal_plus = '0';
		$use_ecs = in_array(ACCOUNT_OPTIONS, array('guest', 'both')) ? '1' : '0';

		$valueArray = array(
			'page_token'
				=> $_SESSION['coo_page_token']->generate_token(),
			'form_get_target'
				=> './admin.php',
			'do'
				=> 'PayPalConfiguration',
			'messages'
				=> $_SESSION[self::MESSAGES_NAMESPACE],
			'testhack'
				=> $this->_getQueryParameter('testhack') == '1' ? '1' : '0',
			'skip_firstconfig_link'
				=> xtc_href_link('admin.php', 'do=PayPalConfiguration&skip_firstconfig=1'),
			'create_webhook'
				=> $create_webhook,
			'use_paypal_plus'
			 	=> $use_paypal_plus,
			'use_ecs'
				=> $use_ecs,
		);
		$_SESSION[self::MESSAGES_NAMESPACE] = array();
		$html = $this->_render('admin_paypal_configuration_first.html', $valueArray);
		$html = $this->paypalText->replaceLanguagePlaceholders($html);
		return MainFactory::create('AdminPageHttpControllerResponse', $this->paypalText->get_text('paypal_configuration_first'), $html);
	}

	/**
	 * saves first-time configuration
	 */
	public function actionSaveFirstConfiguration()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');
		try {
			if($this->_getPostData('testhack') != '1')
			{
				$this->configurationStorage->set('mode', 'live');
				$this->configurationStorage->set(
					'restapi-credentials/live/client_id',
					$this->_getPostData('restapi-credentials-live-client_id')
				);
				$this->configurationStorage->set(
					'restapi-credentials/live/secret',
					$this->_getPostData('restapi-credentials-live-secret')
				);
				$this->_checkCredentials();
			}
			else
			{
				$this->configurationStorage->set('mode', 'sandbox');
			}
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $this->paypalText->get_text('credentials_invalid_try_again'));
			$this->configurationStorage->set('restapi-credentials/live/client_id', '');
			$this->configurationStorage->set('restapi-credentials/live/secret', '');
			return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=PayPalConfiguration/FirstTime'));
		}

		// create PEx profile
		$languages = new language();
		$langToLocaleMap = array(
			'cn' => 'zh_CN',
			'da' => 'da_DK',
			'de' => 'DE',
			'dk' => 'da_DK',
			'en' => 'GB',
			'es' => 'ES',
			'fr' => 'FR',
			'he' => 'he_IL',
			'id' => 'id_ID',
			'il' => 'he_IL',
			'it' => 'IT',
			'ja' => 'ja_JP',
			'jp' => 'ja_JP',
			'nl' => 'NL',
			'no' => 'no_NO',
			'pl' => 'PL',
			'pt' => 'PT',
			'ru' => 'RU',
			'se' => 'sv_SE',
			'sv' => 'sv_SE',
			'th' => 'th_TH',
			'tr' => 'tr_TR',
			'zh' => 'zh_CN',
			'zh' => 'zh_HK',
		);
		foreach($languages->catalog_languages as $iso2 => $langData)
		{
			try
			{
				$profile = MainFactory::create('PayPalExperienceProfile');
				$profile->name = 'Gambio '.$langData['code'];
				if(array_key_exists($langData['code'], $langToLocaleMap))
				{
					$profile->locale_code = $langToLocaleMap[$langData['code']];
				}
				else
				{
					$profile->locale_code = 'DE'; // fallback
				}
				$profile_id = $profile->save();
				$this->configurationStorage->set('payment_experience_profile/'.$langData['code'], $profile_id);
			}
			catch(Exception $e)
			{
				$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => sprintf('%s (%s)', $this->paypalText->get_text('error_creating_profile'), $e->getMessage()));
			}
		}

		// create Webhook
		try
		{
			$webhookFactory = MainFactory::create('PayPalWebhookFactory');
			$webhookResponse = $webhookFactory->renewWebhook();
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $this->paypalText->get_text('error_registering_webhook') . '(' . $e->getMessage() .')');
		}

		// configure PP+
		$this->configurationStorage->set('use_paypal_plus', ($this->_getPostData('use_paypal_plus') == '1' ? '1' : '0'));

		// configure ECS
		if($this->_getPostData('use_ecs') == '1')
		{
			$this->configurationStorage->set('use_ecs_cart', '1');
			$this->configurationStorage->set('use_ecs_products', '1');
		}

		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=PayPalConfiguration'));
	}

	/**
	 * determines whether the interface has been configured, i.e. at least one set of credentials (live/sandbox) has been saved
	 */
	protected function _isConfigured()
	{
		$isConfigured = false;
		$mode = $this->configurationStorage->get('mode');
		$client_id = $this->configurationStorage->get('restapi-credentials/'.$mode.'/client_id');
		$secret = $this->configurationStorage->get('restapi-credentials/'.$mode.'/secret');
		if(!empty($client_id) && !empty($secret))
		{
			$isConfigured = true;
		}
		return $isConfigured;
	}

	/**
	 * Save configuration
	 */
	public function actionSaveConfiguration()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');
		try {
			$this->configurationStorage->set(
				'mode',
				$this->_getPostData('mode')
			);
			$this->configurationStorage->set(
				'restapi-credentials/live/client_id',
				$this->_getPostData('restapi-credentials-live-client_id')
			);
			$this->configurationStorage->set(
				'restapi-credentials/live/secret',
				$this->_getPostData('restapi-credentials-live-secret')
			);
			$this->configurationStorage->set(
				'restapi-credentials/sandbox/client_id',
				$this->_getPostData('restapi-credentials-sandbox-client_id')
			);
			$this->configurationStorage->set(
				'restapi-credentials/sandbox/secret',
				$this->_getPostData('restapi-credentials-sandbox-secret')
			);
			$this->configurationStorage->set(
				'use_paypal_plus',
				$this->_getPostData('use_paypal_plus')
			);
			$this->configurationStorage->set(
				'intent',
				$this->_getPostData('intent')
			);
			$this->configurationStorage->set(
				'use_ecs_cart',
				$this->_getPostData('use_ecs_cart')
			);
			$this->configurationStorage->set(
				'use_ecs_products',
				$this->_getPostData('use_ecs_products')
			);
			$this->configurationStorage->set(
				'ecs_button_style',
				$this->_getPostData('ecs_button_style')
			);
			$this->configurationStorage->set(
				'allow_ecs_login',
				$this->_getPostData('allow_ecs_login')
			);
			$this->configurationStorage->set(
				'thirdparty_payments/invoice/mode',
				$this->_getPostData('thirdparty_payments_invoice_mode')
			);
			$this->configurationStorage->set(
				'thirdparty_payments/cod/mode',
				$this->_getPostData('thirdparty_payments_cod_mode')
			);
			$this->configurationStorage->set(
				'thirdparty_payments/moneyorder/mode',
				$this->_getPostData('thirdparty_payments_moneyorder_mode')
			);
			$this->configurationStorage->set(
				'thirdparty_payments/eustandardtransfer/mode',
				$this->_getPostData('thirdparty_payments_eustandardtransfer_mode')
			);
			$this->configurationStorage->set(
				'thirdparty_payments/cash/mode',
				$this->_getPostData('thirdparty_payments_cash_mode')
			);
			$this->configurationStorage->set(
				'logo/image',
				$this->_getPostData('logo-image')
			);
			$this->configurationStorage->set(
				'logo/position',
				$this->_getPostData('logo-position')
			);
			$this->configurationStorage->set(
				'orderstatus/completed',
				$this->_getPostData('orderstatus_completed')
			);
			$this->configurationStorage->set(
				'orderstatus/pending',
				$this->_getPostData('orderstatus_pending')
			);
			$this->configurationStorage->set(
				'orderstatus/error',
				$this->_getPostData('orderstatus_error')
			);
			$this->configurationStorage->set(
				'debug_logging',
				$this->_getPostData('debug_logging')
			);
			$this->configurationStorage->set(
				'allow_selfpickup',
				$this->_getPostData('allow_selfpickup')
			);
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $e->getMessage());
		}
		try
		{
			unset($_SESSION['paypal_access_token']);
			unset($_SESSION['paypal_access_token_expiration']);
			$this->_checkCredentials();
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $this->paypalText->get_text('credentials_invalid'));
		}

		$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'info', 'text' => $this->paypalText->get_text('configuration_saved'));
		return MainFactory::create('RedirectHttpControllerResponse', './admin.php?do=PayPalConfiguration');
	}

	/**
	 * Save a new or altered Web Payment Experience Profile
	 */
	public function actionSaveExperienceProfile()
	{
		try
		{
			$exp_id = $this->_getPostData('exp_id');
			if(!empty($exp_id))
			{
				$profile = MainFactory::create('PayPalExperienceProfile', $exp_id);
			}
			else
			{
				$profile = MainFactory::create('PayPalExperienceProfile');
			}
			$profile->name = $this->_getPostData('exp_name');
			$profile->landing_page_type = $this->_getPostData('exp_lp_type');
			$profile->bank_txn_pending_url = $this->_getPostData('exp_bank_txn_pending_url');
			$profile->allow_note = $this->_getPostData('exp_allow_note') == true;
			$profile->no_shipping = (int)$this->_getPostData('exp_no_shipping');
			$profile->address_override = (int)$this->_getPostData('exp_address_override');
			$profile->brand_name = $this->_getPostData('exp_brand_name');
			$profile->logo_image = $this->_getPostData('exp_logo_image');
			$profile->locale_code = $this->_getPostData('exp_locale_code');
			$profile_id = $profile->save();
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'info', 'text' => $this->paypalText->get_text('profile_saved'));
			$redirect_url = './admin.php?do=PayPalConfiguration&exp_id='.$profile_id;
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $e->getMessage());
			$redirect_url = './admin.php?do=PayPalConfiguration';
		}
		return MainFactory::create('RedirectHttpControllerResponse', $redirect_url);
	}

	/**
	 * Select Web Payment Experience Profile to use for payments
	 */
	public function actionSelectExperienceProfile()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');
		$languageIso2 = $this->_getPostData('language');
		$Language = new language();
		if(!in_array($languageIso2, array_keys($Language->catalog_languages)))
		{
			throw new Exception('invalid language code '.$languageIso2);
		}
		//$this->configurationStorage->set('payment_experience_profile_id', $this->_getPostData('exp_id'));
		$this->configurationStorage->set('payment_experience_profile/'.$languageIso2, $this->_getPostData('exp_id'));
		$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'info', 'text' => $this->paypalText->get_text('profile_selected'));
		return MainFactory::create('RedirectHttpControllerResponse', './admin.php?do=PayPalConfiguration');
	}

	/**
	 * Delete a Web Payment Experience Profile
	 */
	public function actionDeleteExperienceProfile()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');
		try
		{
			$profile = MainFactory::create('PayPalExperienceProfile', $this->_getPostData('exp_id'));
			$profile->delete();
			$language = new language();
			foreach($language->catalog_languages as $iso2 => $langData)
			{
				if($this->_getPostData('exp_id') == $this->configurationStorage->get('payment_experience_profile/'.$langData['code']))
				{
					$this->configurationStorage->set('payment_experience_profile/'.$langData['code'], '');
				}
			}
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'info', 'text' => $this->paypalText->get_text('profile_deleted'));
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $this->paypalText->get_text('error_deleting_profile') . '(' . $e->getMessage() .')');
		}

		return MainFactory::create('RedirectHttpControllerResponse', './admin.php?do=PayPalConfiguration');
	}

	/**
	 * renew Webhook registration
	 */
	public function actionRenewWebhook()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');
		try
		{
			$webhookFactory = MainFactory::create('PayPalWebhookFactory');
			$webhookResponse = $webhookFactory->renewWebhook();
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'info', 'text' => $this->paypalText->get_text('webhook_registered') .' '. $webhookResponse->id);
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $this->paypalText->get_text('error_registering_webhook') . '(' . $e->getMessage() .')');
		}

		return MainFactory::create('RedirectHttpControllerResponse', './admin.php?do=PayPalConfiguration');
	}

	/**
	 * deletes a Webhook
	 */
	public function actionDeleteWebhook()
	{
		$_SESSION['coo_page_token']->is_valid($this->_getPostData('page_token')) or die('invalid page token');
		$webhookId = $this->_getPostData('webhook_id');
		try
		{
			$webhookFactory = MainFactory::create('PayPalWebhookFactory');
			$webhookResponse = $webhookFactory->deleteWebhook($webhookId);
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'info', 'text' => $this->paypalText->get_text('webhook_deleted'));
		}
		catch(Exception $e)
		{
			$_SESSION[self::MESSAGES_NAMESPACE][] = array('class' => 'error', 'text' => $this->paypalText->get_text('error_deleting_webhook') . '(' . $e->getMessage() .')');
		}

		return MainFactory::create('RedirectHttpControllerResponse', './admin.php?do=PayPalConfiguration');
	}

	public function actionConnectCheck()
	{
		$checkConnect = $this->_getQueryParameter('check_connect');
		$url = $this->_getQueryParameter('url');
		$valid_urls = array(
			$this->configurationStorage->get('service_base_url/sandbox').'/v1/payments',
			$this->configurationStorage->get('service_base_url/live').'/v1/payments'
		);
		if(!in_array($url, $valid_urls))
		{
			return MainFactory::create('HttpControllerResponse', 'invalid URL');
		}
		$response = 'parameter error';
		if(!empty($checkConnect) && !empty($url))
		{
			$t_timeout = 5;
			$cc = MainFactory::create('ConnectChecker');
			try {
				if($checkConnect == 1) # check by GET
				{
					$connectinfo = $cc->check_connect($url, $t_timeout);
				}
				if($checkConnect == 2) # check by POST
				{
					$connectinfo = $cc->check_connect($url, $t_timeout, true, 'CONNECTION_TEST');
				}
				$response =  '<div class="connection_ok">'.$this->paypalText->get_text('connection_ok').'</div>';
			}
			catch(Exception $e)
			{
				$response = '<div class="connection_error">'.$this->paypalText->get_text('connection_error').': '.$e->getMessage().'</div>'.$url;
			}
		}
		return MainFactory::create('HttpControllerResponse', $response);
	}


	/**
	 * Prepare the value array. The array keys are the variable names
	 * that are passed to template
	 */
	protected function _prepareValuesArray($includeExperienceProfiles = false)
	{
		$webhookFactory = MainFactory::create('PayPalWebhookFactory');
		$webhook_id = $this->configurationStorage->get('webhook_id');
		$webhook_id_is_valid = false;
		if(!empty($webhook_id))
		{
			$webhook_id_is_valid = $webhookFactory->checkWebhook($webhook_id);
		}

		if(class_exists('LogControl'))
		{
			$secure_token = LogControl::get_secure_token();
		}
		else
		{
			$secure_token = FileLog::get_secure_token();
		}
		$Language = new language();

		$this->valueArray = array(
			'page_token'
				=> $_SESSION['coo_page_token']->generate_token(),
			'form_get_target'
				=> './admin.php',
			'do'
				=> 'PayPalConfiguration',
			'messages'
				=> $_SESSION[self::MESSAGES_NAMESPACE],
			'languages'
				=> $Language->catalog_languages,
			/*
			'experienceProfilesList'
				=> $experienceProfilesList,
			'login_valid'
				=> $this->_checkCredentials(),
			*/
			'mode'
				=> $this->configurationStorage->get('mode'),
			'mode_sandbox_selected'
				=> $this->configurationStorage->get('mode') == 'sandbox' ? 'selected="selected"' : '',
			'mode_live_selected'
				=> $this->configurationStorage->get('mode') == 'live' ? 'selected="selected"' : '',
			'clientid_live'
				=> $this->configurationStorage->get('restapi-credentials/live/client_id'),
			'secret_live'
				=> $this->configurationStorage->get('restapi-credentials/live/secret'),
			'clientid_sandbox'
				=> $this->configurationStorage->get('restapi-credentials/sandbox/client_id'),
			'secret_sandbox'
				=> $this->configurationStorage->get('restapi-credentials/sandbox/secret'),
			/*
			'activeExpProfileID'
				=> $this->configurationStorage->get('payment_experience_profile_id'),
			*/
			'use_paypal_plus'
				=> $this->configurationStorage->get('use_paypal_plus'),
			'use_ecs_cart'
				=> $this->configurationStorage->get('use_ecs_cart'),
			'use_ecs_products'
				=> $this->configurationStorage->get('use_ecs_products'),
			'allow_ecs_login'
				=> $this->configurationStorage->get('allow_ecs_login'),
			'intent'
				=> $this->configurationStorage->get('intent'),
			'ecs_button_style'
				=> $this->configurationStorage->get('ecs_button_style'),
			'thirdparty_payments_invoice_mode'
				=> $this->configurationStorage->get('thirdparty_payments/invoice/mode'),
			'thirdparty_payments_cod_mode'
				=> $this->configurationStorage->get('thirdparty_payments/cod/mode'),
			'thirdparty_payments_moneyorder_mode'
				=> $this->configurationStorage->get('thirdparty_payments/moneyorder/mode'),
			'thirdparty_payments_eustandardtransfer_mode'
				=> $this->configurationStorage->get('thirdparty_payments/eustandardtransfer/mode'),
			'thirdparty_payments_cash_mode'
				=> $this->configurationStorage->get('thirdparty_payments/cash/mode'),
			'logo_image'
				=> $this->configurationStorage->get('logo/image'),
			'logo_position'
				=> $this->configurationStorage->get('logo/position'),
			'orderstatus_completed'
				=> $this->configurationStorage->get('orderstatus/completed'),
			'orderstatus_pending'
				=> $this->configurationStorage->get('orderstatus/pending'),
			'orderstatus_error'
				=> $this->configurationStorage->get('orderstatus/error'),
			'debug_logging'
				=> $this->configurationStorage->get('debug_logging'),
			'webhook_id'
				=> $webhook_id,
			'webhook_id_is_valid'
				=> $webhook_id_is_valid,
			'webhook_test_url'
				=> HTTPS_CATALOG_SERVER . DIR_WS_CATALOG. 'shop.php?do=PayPal/Webhook&test=accessibility',
			'webhooks'
				=> array(),
			'status_update_url'
				=> xtc_catalog_href_link('shop.php', 'do=PayPal/StatusUpdate&key='.$secure_token.'&days=30'),
			'orders_status_list'
				=> xtc_get_orders_status(),
			'ssl_enabled'
				=> constant('ENABLE_SSL_CATALOG') === 'true' || constant('ENABLE_SSL_CATALOG') === true,
			'debug_output'
				=> print_r($this->configurationStorage->get_all(), true),
			'allow_selfpickup'
				=> $this->configurationStorage->get('allow_selfpickup'),
			'connect_check_url'
				=> xtc_href_link('admin.php', 'do=PayPalConfiguration/ConnectCheck&check_connect=1&url='),
			'connect_check_urls'
				=> array(
					$this->configurationStorage->get('service_base_url/sandbox').'/v1/payments',
					$this->configurationStorage->get('service_base_url/live').'/v1/payments'
					),
		);
		$_SESSION[self::MESSAGES_NAMESPACE] = array();

		try
		{
			$this->valueArray['login_valid'] = $this->_checkCredentials();
		}
		catch(Exception $e)
		{
			$this->valueArray['login_valid'] = false;
			$this->valueArray['messages'][] = array('class' => 'error', 'text' => $this->paypalText->get_text('error_checking_credentials') .': '. $e->getMessage());
		}

		if($this->configurationStorage->get('mode') == 'sandbox')
		{
			$this->valueArray['messages'][] = array('class' => 'info', 'text' => $this->paypalText->get_text('warning_sandbox_mode_active'));
		}

		if(strpos(@constant('MODULE_PAYMENT_INSTALLED'), 'paypal3.php') === false)
		{
			$this->valueArray['messages'][] = array('class' => 'info', 'text' => $this->paypalText->get_text('payment_module_not_installed'));
		}
		elseif(!(@constant('MODULE_PAYMENT_PAYPAL3_STATUS') == 'True'))
		{
			$this->valueArray['messages'][] = array('class' => 'info', 'text' => $this->paypalText->get_text('payment_module_not_enabled'));
		}

		if($includeExperienceProfiles === true)
		{
			$this->valueArray['experienceProfile'] = array();
			$noProfileLanguages = array();
			foreach($Language->catalog_languages as $iso2 => $langData)
			{
				$this->valueArray['experienceProfile'][$langData['code']] = $this->configurationStorage->get('payment_experience_profile/'.$langData['code']);
				if(empty($this->valueArray['experienceProfile'][$langData['code']]))
				{
					$noProfileLanguages[] = $iso2;
				}
			}

			if(!empty($noProfileLanguages))
			{
				$this->valueArray['messages'][] = array('class' => 'info', 'text' => $this->paypalText->get_text('peps_missing').': '.implode(', ', $noProfileLanguages));
			}

			$defaultProfile = MainFactory::create('PayPalExperienceProfile');
			$this->valueArray['experienceProfilesList'] = array();
			$ppRestService = MainFactory::create('PayPalRestService');
			try
			{
				$experienceProfilesRequest = MainFactory::create('PayPalRestRequest', 'GET', '/v1/payment-experience/web-profiles');
				$experienceProfilesResponse = $ppRestService->performRequest($experienceProfilesRequest);
				$experienceProfilesList = $experienceProfilesResponse->getResponseObject();
				foreach($experienceProfilesList as $expProfileListKey => $expProfile)
				{
					if(!in_array($expProfile->id, $this->valueArray['experienceProfile']))
					{
						continue;
					}
					if(
						(int)$expProfile->input_fields->no_shipping != (int)$defaultProfile->no_shipping ||
						(int)$expProfile->input_fields->address_override != (int)$defaultProfile->address_override
					)
					{
						$experienceProfilesList[$expProfileListKey]->isOld = true;
						$this->valueArray['messages'][] = array('class' => 'info', 'text' => $this->paypalText->get_text('old_experience_profile').': '.$expProfile->name.' ('.$expProfile->id.')');
					}
				}
			}
			catch(Exception $e)
			{
				$this->logger->debug_notice(sprintf("Failed to retrieve web profiles.\nRequest: %s\nResponse: %s", (string)$ppRestService->getLastRequest(), (string)$ppRestService->getLastResponse()));
				$this->valueArray['messages'][] = array('class' => 'error', 'text' => $this->paypalText->get_text('error_retrieving_web_profiles') .': '. $e->getMessage());
				$experienceProfilesList = null;
			}
			$this->valueArray['experienceProfilesList'] = $experienceProfilesList;
		}

		if($this->_getQueryParameter('exp_id') !== null)
		{
			$this->valueArray['currentExpProfile'] = MainFactory::create_object('PayPalExperienceProfile');
			if($this->_getQueryParameter('exp_id') == 'new')
			{
					$this->valueArray['edit_profile'] = true;
			}
			else
			{
				try
				{
					$this->valueArray['edit_profile'] = true;
					$this->valueArray['currentExpProfile'] = MainFactory::create('PayPalExperienceProfile', $this->_getQueryParameter('exp_id'));
					$this->valueArray['currentExpProfileID'] = $this->_getQueryParameter('exp_id');
				}
				catch(Exception $e)
				{
					$this->valueArray['edit_profile'] = false;
				}
			}
		}
		else
		{
			$this->valueArray['currentExpProfile'] = MainFactory::create_object('PayPalExperienceProfile');
			$this->valueArray['edit_profile'] = false;
		}

		$this->valueArray['webhooks'] = array();
		if($this->valueArray['login_valid'] == true)
		{
			try
			{
				$this->valueArray['webhooks'] = $webhookFactory->listAllWebhooks();
			}
			catch(Exception $e)
			{
				$this->valueArray['messages'][] = array('class' => 'info', 'text' => $this->paypalText->get_text('failed_to_retrieve_webhooks_list') .' ('. $e->getMessage() .')');
			}
		}

		$this->valueArray['flags'] = array();
		foreach($Language->catalog_languages as $iso => $langData)
		{
			$new_flag_file = '/lang/'.$langData['directory'].'/flag.png';
			$old_flag_file = '/lang/'.$langData['directory'].'/'.$langData['code'].'.png';
			if(file_exists(DIR_FS_CATALOG.$new_flag_file))
			{
				$this->valueArray['flags'][$langData['code']] = sprintf('<img src="%s" alt="%s">', HTTP_SERVER.DIR_WS_CATALOG.$new_flag_file, $langData['code']);
			}
			elseif(DIR_FS_CATALOG.file_exists($old_flag_file))
			{
				$this->valueArray['flags'][$langData['code']] = sprintf('<img src="%s" alt="%s">', HTTP_SERVER.DIR_WS_CATALOG.$old_flag_file, $langData['code']);
			}
			else
			{
				$this->valueArray['flags'][$langData['code']] = sprintf('[%s]', $langData['code']);
			}
		}
	}

	/**
	 * Check REST service credentials
	 * by retrieving an access token
	 */
	protected function _checkCredentials()
	{
		unset($_SESSION['paypal_access_token']);
		unset($_SESSION['paypal_access_token_expiration']);
		$credentialsValid = false;
		$ppRestRequest = MainFactory::create('PayPalRestRequest', 'GET', '');
		$credentialsValid = true;
		return $credentialsValid;
	}


}