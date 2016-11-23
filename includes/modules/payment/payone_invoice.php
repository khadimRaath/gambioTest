<?php
/* --------------------------------------------------------------
	payone_invoice.php 2016-07-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_invoice_ORIGIN extends payone_master {
	var $payone_genre = 'accountbased';

	public function __construct() {
		$this->code = 'payone_invoice';
		parent::__construct();
	}

	function selection() {
		if($this->pg_config['types']['openinvoice']['active'] == 'true') {
			$selection = parent::selection();
			$selection['description'] = '';
			$selection['module'] = $this->pg_config['types']['openinvoice']['name'];
		}
		else {
			$selection = false;
		}
		return $selection;
	}

	function pre_confirmation_check() {
		parent::pre_confirmation_check();
	}

	function confirmation() {
		$confirmation = array(
			'title' => $this->payone->get_text('confirmation_pay_by_invoice'),
		);
		return $confirmation;
	}

	function payment_action() {
		$orders_id = $GLOBALS['insert_id'];
		$order = new order($orders_id);
		$this->payone->log('invoice payment_action');
		$genre_identifier = $this->_getActiveGenreIdentifier();
		$request = $this->global_config['authorization_method'] == 'auth' ? 'authorization' : 'preauthorization';

		$standard_parameters = $this->payone->getStandardParameters($request, $this->global_config);
		unset($standard_parameters['responsetype']);
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		unset($standard_parameters['hash']);
		
		$billingStreet = $GLOBALS['order']->billing['street_address']
		                 . (empty($GLOBALS['order']->billing['house_number'])
				? ''
				: ' ' . $GLOBALS['order']->billing['house_number']);
		$personal_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
		$personal_data->setCompany($GLOBALS['order']->billing['company']);
		$personal_data->setCountry($GLOBALS['order']->billing['country']['iso_code_2']);
		$personal_data->setEmail($GLOBALS['order']->customer['email_address']);
		$personal_data->setFirstname($GLOBALS['order']->billing['firstname']);
		$personal_data->setLastname($GLOBALS['order']->billing['lastname']);
		$personal_data->setStreet($billingStreet);
		$personal_data->setCity($GLOBALS['order']->billing['city']);
		$personal_data->setCustomerid($_SESSION['customer_id']);
		$personal_data->setZip($GLOBALS['order']->billing['postcode']);
		
		$deliveryStreet = $GLOBALS['order']->delivery['street_address']
		                  . (empty($GLOBALS['order']->delivery['house_number'])
				? ''
				: ' ' . $GLOBALS['order']->delivery['house_number']);
		$delivery_data  = new Payone_Api_Request_Parameter_Authorization_DeliveryData();
		$delivery_data->setShippingCity($GLOBALS['order']->delivery['city']);
		$delivery_data->setShippingCompany($GLOBALS['order']->delivery['company']);
		$delivery_data->setShippingCountry($GLOBALS['order']->delivery['country']['iso_code_2']);
		$delivery_data->setShippingFirstname($GLOBALS['order']->delivery['firstname']);
		$delivery_data->setShippingLastname($GLOBALS['order']->delivery['lastname']);
		$delivery_data->setShippingStreet($deliveryStreet);
		$delivery_data->setShippingZip($GLOBALS['order']->delivery['postcode']);

		$request_parameters = array(
			'aid' => $this->global_config['subaccount_id'],
			'key' => $this->global_config['key'],
			'clearingtype' => 'rec',
			'reference' => $GLOBALS['insert_id'],
			'amount' => round($order->info['pp_total'], 2),
			'currency' => $GLOBALS['order']->info['currency'],
			'personal_data' => $personal_data,
			'delivery_data' => $delivery_data,
			'invoicing' => $this->_getInvoicingTransaction($GLOBALS['insert_id']),
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->payone->getPayoneConfig());
		if($this->global_config['authorization_method'] == 'auth') {
			$service = $builder->buildServicePaymentAuthorize();
			$params['request'] = 'authorization';
			$request = new Payone_Api_Request_Authorization($params);
			$this->payone->log("invoice authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("invoice authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("invoice preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("invoice preauthorize response:\n".print_r($response, true));
		}

		$this->_saveClearingData($orders_id, $response);

		if($response instanceof Payone_Api_Response_Preauthorization_Approved) {
			$this->payone->log("preauthorization approved");
			$this->payone->saveTransaction($GLOBALS['insert_id'], $response->getStatus(), $response->getTxid(), $response->getUserid());
			$this->_updateOrdersStatus($orders_id, $response->getTxid(), strtolower((string)$response->getStatus()), $this->payone->get_text('comment_preauth_approved'));
		}
		else if($response instanceof Payone_Api_Response_Authorization_Approved) {
			$this->payone->log("authorization approved");
			$this->payone->saveTransaction($GLOBALS['insert_id'], $response->getStatus(), $response->getTxid(), $response->getUserid());
			$this->_updateOrdersStatus($orders_id, $response->getTxid(), strtolower((string)$response->getStatus()), $this->payone->get_text('comment_auth_approved'));
		}
		else if($response instanceof Payone_Api_Response_Authorization_Redirect) {
			$this->payone->log("authorization for order ".$GLOBALS['insert_id']." initiated, txid = ".$response->getTxid());
			if($response->getStatus() == 'REDIRECT') {
				$this->payone->saveTransaction($GLOBALS['insert_id'], $response->getStatus(), $response->getTxid(), $response->getUserid());
				$this->payone->log("redirecting to payment service");
				$this->_updateOrdersStatus($GLOBALS['insert_id'], $response->getTxid(), strtolower((string)$response->getStatus()), $this->payone->get_text('comment_redirection_initiated'));
				$redirect_url = $response->getRedirecturl();
				xtc_redirect($redirect_url);
			}

		}
		else if($response instanceof Payone_Api_Response_Error) {
			$this->payone->log("authorization for order ".$GLOBALS['insert_id']." failed, status ".$response->getStatus().", code ".$response->getErrorcode().", message ".$response->getErrormessage());
			$this->_updateOrdersStatus($GLOBALS['insert_id'], 0, strtolower((string)$response->getStatus()), $this->payone->get_text('comment_error'));
			$_SESSION['payone_error'] = $response->getCustomermessage();
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		else {
			die('unhandled response type');
		}

		xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
	}

	function after_process() {
		parent::after_process();
	}
}
MainFactory::load_origin_class('payone_invoice');