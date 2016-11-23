<?php
/* --------------------------------------------------------------
	payone_wlt.php 2014-07-15 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_wlt_ORIGIN extends payone_master {
	var $payone_genre = 'ewallet';

	public function __construct() {
		$this->code = 'payone_wlt';
		parent::__construct();
	}

	function selection() {
		$selection = parent::selection();
		if(is_array($selection)) {
			$selection['description'] = '';
		}
		return $selection;
	}

	function confirmation() {
		$active_genre = $this->_getActiveGenreIdentifier();
		$confirmation = array(
			'title' => $this->config[$active_genre]['name'], # $this->payone->get_text('confirmation_pay_by_wlt'),
		);
		return $confirmation;
	}

	function payment_action() {
		$this->payone->log('ewallet payment_action');
		$orders_id = $_SESSION['tmp_oID'];
		$order = new order($orders_id);
		$genre_identifier = $this->_getActiveGenreIdentifier();
		$request = $this->global_config['authorization_method'] == 'auth' ? 'authorization' : 'preauthorization';
		$standard_parameters = $this->payone->getStandardParameters($request, $this->global_config);
		unset($standard_parameters['responsetype']);
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		unset($standard_parameters['hash']);
		$personal_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
		$personal_data->setFirstname($GLOBALS['order']->billing['firstname']);
		$personal_data->setLastname($GLOBALS['order']->billing['lastname']);
		$personal_data->setCountry($GLOBALS['order']->billing['country']['iso_code_2']);
		$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet();
		$payment_method->setWallettype('PPE');
		$payment_method->setSuccessurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
		$payment_method->setBackurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_confirmation.php');
		$payment_method->setErrorurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		$request_parameters = array(
			'aid' => $this->global_config['subaccount_id'],
			'key' => $this->global_config['key'],
			'clearingtype' => 'wlt',
			'reference' => $GLOBALS['insert_id'],
			'amount' => round($order->info['pp_total'], 2),
			'currency' => $GLOBALS['order']->info['currency'],
			'personal_data' => $personal_data,
			'payment' => $payment_method,
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->payone->getPayoneConfig());
		if($this->global_config['authorization_method'] == 'auth') {
			$service = $builder->buildServicePaymentAuthorize();
			$params['request'] = 'authorization';
			$request = new Payone_Api_Request_Authorization($params);
			$this->payone->log("ewallet authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("ewallet authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("ewallet preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("ewallet preauthorize response:\n".print_r($response, true));
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
	}
}
MainFactory::load_origin_class('payone_wlt');