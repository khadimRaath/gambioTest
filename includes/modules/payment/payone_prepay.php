<?php
/* --------------------------------------------------------------
	payone_prepay.php 2014-07-15 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_prepay_ORIGIN extends payone_master {
	var $payone_genre = 'accountbased';

	public function __construct() {
		$this->code = 'payone_prepay';
		parent::__construct();
	}

	function selection() {
		if($this->pg_config['types']['prepay']['active'] == 'true') {
			$selection = parent::selection();
			$selection['description'] = '';
			$selection['module'] = $this->pg_config['types']['prepay']['name'];
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
			'title' => $this->payone->get_text('confirmation_pay_by_prepay'),
		);
		return $confirmation;
	}

	function payment_action() {
		$this->payone->log("(pre-)authorizing vor payment");
		$orders_id = $_SESSION['tmp_oID'];
		$order = new order($orders_id);
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
		$request_parameters = array(
			'aid' => $this->global_config['subaccount_id'],
			'key' => $this->global_config['key'],
			'clearingtype' => 'vor',
			'reference' => $GLOBALS['insert_id'],
			'amount' => round($order->info['pp_total'], 2),
			'currency' => $GLOBALS['order']->info['currency'],
			'personal_data' => $personal_data,
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->payone->getPayoneConfig());

		if($this->global_config['authorization_method'] == 'auth') {
			$service = $builder->buildServicePaymentAuthorize();
			$params['request'] = 'authorization';
			$request = new Payone_Api_Request_Authorization($params);
			$this->payone->log("vor authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("vor authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("vor preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("vor preauthorize response:\n".print_r($response, true));
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
				$this->_updateOrdersStatus($orders_id, $response->getTxid(), strtolower((string)$response->getStatus()), $this->payone->get_text('comment_redirection_initiated'));
				$redirect_url = $response->getRedirecturl();
				xtc_redirect($redirect_url);
			}

		}
		else if($response instanceof Payone_Api_Response_Error) {
			$this->payone->log("authorization for order ".$GLOBALS['insert_id']." failed, status ".$response->getStatus().", code ".$response->getErrorcode().", message ".$response->getErrormessage());
			$this->_updateOrdersStatus($orders_id, 0, strtolower((string)$response->getStatus()), $this->payone->get_text('comment_error'));
			$_SESSION['payone_error'] = $response->getCustomermessage();
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		else {
			var_dump($response);
			die('unhandled response type');
		}

		xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
	}

	function after_process() {
		parent::after_process();
	}
}
MainFactory::load_origin_class('payone_prepay');