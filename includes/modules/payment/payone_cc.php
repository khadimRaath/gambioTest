<?php
/* --------------------------------------------------------------
	payone_cc.php 2016-07-22
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_cc_ORIGIN extends payone_master {
	var $payone_genre = 'creditcard';

	public function __construct() {
		$this->code = 'payone_cc';
		parent::__construct();
	}

	function _paymentDataForm($active_genre_identifier) {
		$genre_config = $this->config[$active_genre_identifier];
		$global_config = $genre_config['global_override'] == 'true' ? $genre_config['global'] : $this->config['global'];

		$standard_parameters = $this->payone->getStandardParameters('creditcardcheck', $global_config);
		$standard_parameters['aid'] = $global_config['subaccount_id'];
		$standard_parameters['responsetype'] = 'JSON';
		$standard_parameters['storecarddata'] = 'yes';
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		$standard_parameters['hash'] = $this->payone->computeHash($standard_parameters, $global_config['key']);

		$cctypes = $this->payone->getTypesForGenre($active_genre_identifier);
		$cardtypesArray = array();
		$cardtypesShortArray = array();
		foreach($cctypes as $cctype)
		{
			$cardtypesArray[] = '"'.$cctype['shorttype'].'"';
			$cardtypesShortArray[] = $cctype['shorttype'];
		}
		$cardtypes = implode(',', $cardtypesArray);

		$ccexpires_years = array();
		for($y = 0, $base = date('y'); $y < 20; $y++) {
			$ccexpires_years[] = $base + $y;
		}
		$ccexpires_months = array();
		for($m = 1; $m <= 12; $m++) {
			$ccexpires_months[] = sprintf('%02d', $m);
		}
		ob_start();
		echo "<script>\n";
		echo "var p1_cc_config = {\n";
		$sparams = array();
		foreach($standard_parameters as $key => $value) {
			$sparams[] = "$key: '$value'";
		}
		echo implode(",\n", $sparams);
		echo "};\n";
		echo "var p1_iframe_config = ".$this->_getIframeConfig($genre_config, $cardtypesShortArray).";\n";
		echo "</script>\n";
		include DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/checkout_payone_cc_form.html';
		$form = ob_get_clean();
		$form = $this->payone->replaceTextPlaceholders($form);
		$pdf = array(
			array('title' => $this->payone->get_text('credit_card_data'), 'field' => $form),
		);
		return $pdf;
	}

	protected function _getIframeConfig($genre_config, $cardtypesArray)
	{
		$iframe_config = array(
			'fields' => array(
				'cardtype' => array(
					'selector' => 'cardtype',
					'cardtypes' => $cardtypesArray,
				),
				'cardpan' => array(
					'selector' => 'cardpan',
					'type' => $genre_config['genre_specific']['inputstyle']['cardpan']['type'],
					'size' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardpan']['size_min'],
					'maxlength' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardpan']['size_max'],
				),
				'cardcvc2' => array(
					'selector' => 'cardcvc2',
					'type' => $genre_config['genre_specific']['inputstyle']['cardcvc2']['type'],
					'size' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardcvc2']['size_min'],
					'maxlength' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardcvc2']['size_max'],
				),
				'cardexpiremonth' => array(
					'selector' => 'cardexpiremonth',
					'type' => $genre_config['genre_specific']['inputstyle']['cardexpiremonth']['type'],
					'size' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardexpiremonth']['size_min'],
					'maxlength' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardexpiremonth']['size_max'],
				),
				'cardexpireyear' => array(
					'selector' => 'cardexpireyear',
					'type' => $genre_config['genre_specific']['inputstyle']['cardexpireyear']['type'],
					'size' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardexpireyear']['size_min'],
					'maxlength' => (string)(int)$genre_config['genre_specific']['inputstyle']['cardexpireyear']['size_max'],
				),
			),
			'defaultStyle' => array(
				'input' => $genre_config['genre_specific']['inputstyle']['default-input-css'],
				'select' => $genre_config['genre_specific']['inputstyle']['default-select-css'],
				'iframe' => array(
					'height' => $genre_config['genre_specific']['inputstyle']['default-iframe_height'],
					'width' => $genre_config['genre_specific']['inputstyle']['default-iframe_width'],
				),
			),
			'error' => 'p1_error',
		);

		foreach(array_keys($iframe_config['fields']) as $field)
		{
			if($field == 'cardtype')
			{
				continue;
			}
			if($genre_config['genre_specific']['inputstyle'][$field]['style'] !== 'standard')
			{
				$iframe_config['fields'][$field]['style'] = $genre_config['genre_specific']['inputstyle'][$field]['css'];
			}
			if($genre_config['genre_specific']['inputstyle'][$field]['iframe'] !== 'standard')
			{
				$iframe_config['fields'][$field]['iframe'] = array(
					'width' => $genre_config['genre_specific']['inputstyle'][$field]['iframe_width'],
					'height' => $genre_config['genre_specific']['inputstyle'][$field]['iframe_height'],
				);
			}
		}

		$iframe_cfg = json_encode($iframe_config);
		// $iframe_cfg .= sprintf("<!--\n\n%s\n\n -->\n", print_r($genre_config, true));
		return $iframe_cfg;
	}


	function pre_confirmation_check() {
		if(isset($_POST['pseudocardpan'])) {
			$_SESSION[$this->code.'_pseudocardpan'] = $_POST['pseudocardpan'];
		}
		if(isset($_POST['truncatedcardpan'])) {
			$_SESSION[$this->code.'_truncatedcardpan'] = $_POST['truncatedcardpan'];
		}
		if(isset($_POST['payone_cc_genre_identifier'])) {
			$_SESSION['payone_cc_genre_identifier'] = $_POST['payone_cc_genre_identifier'];
		}
		if(!(isset($_SESSION[$this->code.'_pseudocardpan']) && isset($_SESSION[$this->code.'_truncatedcardpan']) && isset($_SESSION['payone_cc_genre_identifier']))) {
			$_SESSION['payone_error'] = $this->payone->get_text('error_dataentry');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code);
		}
		parent::pre_confirmation_check();
	}

	function confirmation() {
		$confirmation = array(
			'title' => $this->payone->get_text('confirmation_pay_by_cc').': '.$_SESSION[$this->code.'_truncatedcardpan'],
		);
		return $confirmation;
	}

	function payment_action() {
		$this->payone->log("(pre-)authorizing cc payment");
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
		$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
		$payment_method->setSuccessurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
		$payment_method->setBackurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_confirmation.php');
		$payment_method->setErrorurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		$payment_method->setPseudocardpan($_SESSION[$this->code.'_pseudocardpan']);

		$request_parameters = array(
			'aid' => $this->global_config['subaccount_id'],
			'key' => $this->global_config['key'],
			'clearingtype' => 'cc',
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
			$this->payone->log("cc authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("cc authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("cc preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("cc preauthorize response:\n".print_r($response, true));
		}

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
			$this->_updateOrdersStatus($orders_id, '', strtolower((string)$response->getStatus()), $this->payone->get_text('comment_error'));
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
		unset($_SESSION['pseudocardpan']);
		unset($_SESSION['truncatedcardpan']);
		unset($_SESSION['payone_cc_genre_identifier']);
	}
}
MainFactory::load_origin_class('payone_cc');