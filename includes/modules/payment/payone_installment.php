<?php
/* --------------------------------------------------------------
	payone_installment.php 2016-07-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_installment_ORIGIN extends payone_master {
	var $payone_genre = 'installment';

	public function __construct() {
		$this->code = 'payone_installment';
		parent::__construct();
	}

	function _paymentDataForm($active_genre_identifier) {
		ob_start();
		include DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/checkout_payone_installment_form.html';
		$form = ob_get_clean();
		$form = $this->payone->replaceTextPlaceholders($form);
		$pdf = array(
			array('title' => '', 'field' => $form),
		);
		return $pdf;
	}


	function pre_confirmation_check() {
		parent::pre_confirmation_check();
		if($_SESSION['sendto'] != $_SESSION['billto']) {
			$_SESSION['payone_error'] = $this->payone->get_text('error_addresses_must_be_equal');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		if(isset($_POST['installment_type'])) {
			$_SESSION['payone_installment_type'] = $_POST['installment_type'];
		}
		if(empty($_SESSION['payone_installment_type'])) {
			$_SESSION['payone_error'] = $this->payone->get_text('installment_type_not_selected');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
	}

	function confirmation() {
		$confirmation = array(
			'title' => $this->payone->get_text('confirmation_pay_by_installment'),
		);
		return $confirmation;
	}

	function payment_action() {
		$this->payone->log('installment payment_action');
		$orders_id = $_SESSION['tmp_oID'];
		$order = new order($orders_id);
		$genre_identifier = $this->_getActiveGenreIdentifier();
		$request = $this->global_config['authorization_method'] == 'auth' ? 'authorization' : 'preauthorization';
		$financingtype = $_SESSION['payone_installment_type'];

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
		$personal_data->setCity(utf8_encode($GLOBALS['order']->billing['city']));
		$personal_data->setCustomerid($_SESSION['customer_id']);
		$personal_data->setZip($GLOBALS['order']->billing['postcode']);
		
		$deliveryStreet = $GLOBALS['order']->delivery['street_address']
		                  . (empty($GLOBALS['order']->delivery['house_number'])
				? ''
				: ' ' . $GLOBALS['order']->delivery['house_number']);
		$delivery_data  = new Payone_Api_Request_Parameter_Authorization_DeliveryData();
		$delivery_data->setShippingCity(utf8_encode($GLOBALS['order']->delivery['city']));
		$delivery_data->setShippingCompany($GLOBALS['order']->delivery['company']);
		$delivery_data->setShippingCountry($GLOBALS['order']->delivery['country']['iso_code_2']);
		$delivery_data->setShippingFirstname($GLOBALS['order']->delivery['firstname']);
		$delivery_data->setShippingLastname($GLOBALS['order']->delivery['lastname']);
		$delivery_data->setShippingStreet($deliveryStreet);
		$delivery_data->setShippingZip($GLOBALS['order']->delivery['postcode']);

		$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing();
		$payment_method->setSuccessurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
		$payment_method->setBackurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_confirmation.php');
		$payment_method->setErrorurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		$payment_method->setFinancingtype($financingtype);

		$request_parameters = array(
			'aid' => $this->global_config['subaccount_id'],
			'key' => $this->global_config['key'],
			'clearingtype' => 'fnc',
			'reference' => $GLOBALS['insert_id'],
			'amount' => round($order->info['pp_total'], 2),
			'currency' => $GLOBALS['order']->info['currency'],
			'personal_data' => $personal_data,
			'delivery_data' => $delivery_data,
			'payment' => $payment_method,
			'invoicing' => $this->_getInvoicingTransaction($GLOBALS['insert_id']),
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->payone->getPayoneConfig());
		if($this->global_config['authorization_method'] == 'auth') {
			$service = $builder->buildServicePaymentAuthorize();
			$params['request'] = 'authorization';
			$request = new Payone_Api_Request_Authorization($params);
			$this->payone->log("installment authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("installment authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("installment preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("installment preauthorize response:\n".print_r($response, true));
		}

		if($response instanceof Payone_Api_Response_Authorization_Redirect) {
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
		unset($_SESSION['payone_installment_type']);
	}
}
MainFactory::load_origin_class('payone_installment');