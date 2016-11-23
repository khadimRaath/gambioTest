<?php
/* --------------------------------------------------------------
	payone_otrans.php 2016-08-30
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_otrans_ORIGIN extends payone_master {
	var $payone_genre = 'onlinetransfer';

	public function __construct() {
		$this->code = 'payone_otrans';
		parent::__construct();
	}

	function _paymentDataForm($active_genre_identifier) {
		$genre_config = $this->config[$active_genre_identifier];
		$global_config = $genre_config['global_override'] == 'true' ? $genre_config['global'] : $this->config['global'];

		$bgroups = $this->payone->getBankGroups();

		$standard_parameters = $this->payone->getStandardParameters('bankaccountcheck', $global_config);
		$standard_parameters['aid'] = $global_config['subaccount_id'];
		$standard_parameters['responsetype'] = 'JSON';
		$standard_parameters['checktype'] = 0;
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		$standard_parameters['hash'] = $this->payone->computeHash($standard_parameters, $global_config['key']);

		ob_start();
		echo "<script>\n";
		echo "var p1_otrans_config = {\n";
		$sparams = array();
		foreach($standard_parameters as $key => $value) {
			$sparams[] = "$key: '$value'";
		}
		echo implode(",\n", $sparams);
		echo "};\n";
		echo "</script>\n";
		include DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/checkout_payone_otrans_form.html';
		$form = ob_get_clean();
		$form = $this->payone->replaceTextPlaceholders($form);
		$pdf = array(
			array('title' => '', 'field' => $form),
		);
		return $pdf;
	}

	function pre_confirmation_check() {
		parent::pre_confirmation_check();
		$order = $GLOBALS['order'];
		$required_fields = array('otrans_country', 'otrans_type', 'otrans_accowner');
		switch($_SESSION[$this->code.'_otrans_type'])
		{
			case 'ideal':
				$required_fields[] = 'otrans_bankgroup_ideal';
				break;
			case 'eps':
				$required_fields[] = 'otrans_bankgroup_eps';
				break;
		}
		foreach($required_fields as $rf) {
			if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$rf]))
			{
				$_SESSION[$this->code.'_'.$rf] = $_POST[$rf];
			}

			if(empty($_SESSION[$this->code.'_'.$rf])) {
				xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code);
			}
		}

		if($_SESSION[$this->code.'_otrans_type'] == 'sofortueberweisung' && $order->billing['country']['iso_code_2'] == 'CH')
		{
			$required_fields = array('otrans_bankcode', 'otrans_accnum');
		}
		else if (!in_array($_SESSION[$this->code.'_otrans_type'], ['eps', 'ideal']))
		{
			$required_fields = array('otrans_iban', 'otrans_bic');
		}
		foreach($required_fields as $rf) {
			if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$rf]))
			{
				$_SESSION[$this->code.'_'.$rf] = preg_replace('/\s/', '', $_POST[$rf]);
			}

			if(empty($_SESSION[$this->code.'_'.$rf])) {
				xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code);
			}
		}
	}

	function confirmation() {
		$title = $this->payone->get_text('confirmation_pay_by_otrans'.$_SESSION[$this->code.'_otrans_type']);
		$confirmation = array(
			'title' => $title,
		);
		return $confirmation;
	}

	function payment_action() {
		$this->payone->log('otrans payment_action');
		$orders_id = $_SESSION['tmp_oID'];
		$order = new order($orders_id);
		$banktransfertypes = array(
			'sofortueberweisung' => 'PNT',
			'giropay' => 'GPY',
			'eps' => 'EPS',
			'pfefinance' => 'PFF',
			'pfcard' => 'PFC',
			'ideal' => 'IDL',
		);
		$banktransfertype = $banktransfertypes[$_SESSION[$this->code.'_otrans_type']];
		switch($_SESSION[$this->code.'_otrans_type']) {
			case eps:
				$bankgroup = $_SESSION[$this->code.'_otrans_bankgroup_eps'];
				break;
			case ideal:
				$bankgroup = $_SESSION[$this->code.'_otrans_bankgroup_ideal'];
				break;
			default:
				$bankgroup = '';
		}
		$genre_identifier = $this->_getActiveGenreIdentifier();
		$genre_config = $this->config[$genre_identifier];
		$global_config = $genre_config['global_override'] == 'true' ? $genre_config['global'] : $this->config['global'];
		$standard_parameters = $this->payone->getStandardParameters($request, $global_config);
		unset($standard_parameters['responsetype']);
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		unset($standard_parameters['hash']);
		$personal_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
		$personal_data->setFirstname($GLOBALS['order']->billing['firstname']);
		$personal_data->setLastname($GLOBALS['order']->billing['lastname']);
		$personal_data->setCountry($GLOBALS['order']->billing['country']['iso_code_2']);
		$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_OnlineBankTransfer();
		$payment_method->setOnlinebanktransfertype($banktransfertype);
		$payment_method->setBankcountry($_SESSION[$this->code.'_otrans_country']);
		if($_SESSION[$this->code.'_otrans_type'] == 'sofortueberweisung' && $_SESSION[$this->code.'_otrans_country'] == 'CH')
		{
			$payment_method->setBankaccount($_SESSION[$this->code.'_otrans_accnum']);
			$payment_method->setBankcode($_SESSION[$this->code.'_otrans_bankcode']);
		}
		else
		{
			$payment_method->setIban($_SESSION[$this->code.'_otrans_iban']);
			$payment_method->setBic($_SESSION[$this->code.'_otrans_bic']);
		}
		$payment_method->setBankgrouptype($bankgroup);
		$payment_method->setSuccessurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
		$payment_method->setBackurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_confirmation.php');
		$payment_method->setErrorurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);

		$request_parameters = array(
			'aid' => $global_config['subaccount_id'],
			'key' => $global_config['key'],
			'clearingtype' => 'sb',
			'reference' => $GLOBALS['insert_id'],
			'amount' => round($order->info['pp_total'], 2),
			'currency' => $GLOBALS['order']->info['currency'],
			'personal_data' => $personal_data,
			'payment' => $payment_method,
		);
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->payone->getPayoneConfig());
		if($global_config['authorization_method'] == 'auth') {
			$service = $builder->buildServicePaymentAuthorize();
			$params['request'] = 'authorization';
			$request = new Payone_Api_Request_Authorization($params);
			$this->payone->log("otrans authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("otrans authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("otrans preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("otrans preauthorize response:\n".print_r($response, true));
		}

		if($response instanceof Payone_Api_Response_Authorization_Redirect) {
			$this->payone->log("authorization for order ".$GLOBALS['insert_id']." initiated, txid = ".$response->getTxid());
			if($response->getStatus() == 'REDIRECT') {
				$this->payone->saveTransaction($GLOBALS['insert_id'], $response->getStatus(), $response->getTxid(), $response->getUserid());
				$this->payone->log("redirecting to payment service");
				$redirect_url = $response->getRedirecturl();
				xtc_redirect($redirect_url);
			}

		}
		else if($response instanceof Payone_Api_Response_Error) {
			$this->payone->log("authorization for order ".$GLOBALS['insert_id']." failed, status ".$response->getStatus().", code ".$response->getErrorcode().", message ".$response->getErrormessage());
			$_SESSION['payone_error'] = $response->getCustomermessage();
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		else {
			die('unhandled response type');
		}

		xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
	}

	function before_process() {
		parent::before_process();
		if($tmporder_exists) {

		}
	}

	function after_process() {
		parent::after_process();
	}
}
MainFactory::load_origin_class('payone_otrans');