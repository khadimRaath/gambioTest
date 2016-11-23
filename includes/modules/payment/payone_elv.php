<?php
/* --------------------------------------------------------------
	payone_elv.php 2016-07-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_elv_ORIGIN extends payone_master {
	var $payone_genre = 'accountbased';

	public function __construct() {
		$this->code = 'payone_elv';
		parent::__construct();
	}

	function selection() {
		if($this->pg_config['types']['lastschrift']['active'] == 'true') {
			$selection = parent::selection();
			$selection['module'] = $this->pg_config['types']['lastschrift']['name'];
		}
		else {
			$selection = false;
		}
		return $selection;
	}

	function _getAddressBookIso2($ab_id)
	{
		$t_query = 'SELECT c.countries_iso_code_2
						FROM `address_book` ab
						JOIN countries c ON c.countries_id = ab.entry_country_id
						WHERE ab.address_book_id = :ab_id';
		$t_query = strtr($t_query, array(':ab_id' => (int)$ab_id));
		$t_result = xtc_db_query($t_query, 'db_link', false);
		$iso2 = false;
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$iso2 = $t_row['countries_iso_code_2'];
		}
		return $iso2;
	}

	function _paymentDataForm($active_genre_identifier) {
		$standard_parameters = $this->payone->getStandardParameters('bankaccountcheck', $this->global_config);
		$standard_parameters['aid'] = $this->global_config['subaccount_id'];
		$standard_parameters['responsetype'] = 'JSON';
		$standard_parameters['checktype'] = 0;
		unset($standard_parameters['successurl']);
		unset($standard_parameters['errorurl']);
		if($this->pg_config['genre_specific']['check_bankdata'] == 'basic')
		{
			$standard_parameters['checktype'] = '0';
		}
		elseif($this->pg_config['genre_specific']['check_bankdata'] == 'pos')
		{
			$standard_parameters['checktype'] = '1';
		}
		$standard_parameters['hash'] = $this->payone->computeHash($standard_parameters, $this->global_config['key']);

		$sepa_countries_all = $this->payone->getSepaCountries();
		$sepa_countries_active = $this->pg_config['genre_specific']['sepa_account_countries'];
		$sepa_countries = array();
		$sendto_iso2 = $this->_getAddressBookIso2($_SESSION['sendto']);
		$sepa_countries_selected = array();
		foreach($sepa_countries_all as $sepa_country)
		{
			if(in_array($sepa_country['countries_iso_code_2'], $sepa_countries_active))
			{
				$sepa_countries[] = $sepa_country;
				if($sepa_country['countries_iso_code_2'] == $sendto_iso2)
				{
					$sepa_countries_selected[$sepa_country['countries_iso_code_2']] = 'selected="selected"';
				}
				else
				{
					$sepa_countries_selected[$sepa_country['countries_iso_code_2']] = '';
				}
			}
		}

		ob_start();
		echo "<script>\n";
		echo "var p1_payment_error = '".$this->payone->get_text('payment_error')."';\n";
		echo "var p1_elv_config = {\n";
		$sparams = array();
		foreach($standard_parameters as $key => $value) {
			$sparams[] = "$key: '$value'";
		}
		echo implode(",\n", $sparams);
		echo "};\n";
		echo "var p1_elv_checkmode = '".$this->pg_config['genre_specific']['check_bankdata']."';\n";
		echo "</script>\n";
		include DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/checkout_payone_elv_form.html';
		$form = ob_get_clean();
		$form = $this->payone->replaceTextPlaceholders($form);
		$pdf = array(
			array('title' => '', 'field' => $form),
		);
		return $pdf;
	}

	function pre_confirmation_check()
	{
		parent::pre_confirmation_check();

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$accountnumber = '';
			$bankcode = '';
			$iban = '';
			$bic = '';

			if(isset($_POST['p1_elv_accountnumber']) && empty($_POST['p1_elv_accountnumber']) === false && isset($_POST['p1_elv_bankcode']) && empty($_POST['p1_elv_bankcode']) === false)
			{
				$accountnumber = $_POST['p1_elv_accountnumber'];
				$bankcode = $_POST['p1_elv_bankcode'];
				$iban = '';
				$bic = '';
			}

			if(isset($_POST['p1_elv_iban']) && empty($_POST['p1_elv_iban']) === false && isset($_POST['p1_elv_bic']) && empty($_POST['p1_elv_bic']) === false)
			{
				$accountnumber = '';
				$bankcode = '';
				$iban = $_POST['p1_elv_iban'];
				$bic = $_POST['p1_elv_bic'];
			}

			$iban = preg_replace('/\s/', '', $iban);

			$_SESSION['payone_elv_data'] = array(
				# 'accountholder' => $_POST['p1_elv_accountholder'],
				'country' => $_POST['p1_elv_country'],
				'accountnumber' => $accountnumber,
				'bankcode' => $bankcode,
				'iban' => $iban,
				'bic' => $bic,
			);
		}

		if(empty($_SESSION['payone_elv_data']['accountnumber']) !== true && empty($_SESSION['payone_elv_data']['bankcode']) !== true)
		{
			$t_has_accbank = true;
		}
		else
		{
			$t_has_accbank = false;
		}

		if(empty($_SESSION['payone_elv_data']['iban']) !== true && empty($_SESSION['payone_elv_data']['bic']) !== true)
		{
			$t_has_sepa = true;
		}
		else
		{
			$t_has_sepa = false;
		}

		if(($t_has_accbank === true || $t_has_sepa === true) !== true || empty($_SESSION['payone_elv_data']['country']) === true)
		{
			$_SESSION['payone_error'] = $this->payone->get_text('paydata_incomplete');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?payment_error='.$this->code);
		}

	}

	function confirmation() {
		$title = $this->payone->get_text('confirmation_pay_by_elv');
		//$title .= '<pre>'.print_r($_SESSION['payone_elv_data'], true).'</pre>';

		$confirmation = array(
			'title' => $title,
		);
		return $confirmation;
	}

	function process_button() {
		$pb = '';

		if($this->pg_config['genre_specific']['sepa_use_managemandate'] == 'true')
		{
			$order = $GLOBALS['order'];
			$standard_parameters = $this->payone->getStandardParameters($request, $this->global_config);
			unset($standard_parameters['responsetype']);
			unset($standard_parameters['successurl']);
			unset($standard_parameters['errorurl']);
			unset($standard_parameters['hash']);
			$request_parameters = array
				(
					'aid' => $this->global_config['subaccount_id'],
					'key' => $this->global_config['key'],
					'currency' => $order->info['currency'],
				);
			$params = array_merge($standard_parameters, $request_parameters);
			$builder = new Payone_Builder($this->payone->getPayoneConfig());
			$mandate_service = $builder->buildServiceManagementManageMandate();
			$manage_mandate_request = new Payone_Api_Request_ManageMandate($params);
			$manage_mandate_request->setAid($this->global_config['subaccount_id']);
			$manage_mandate_request->setClearingType('elv');
			$mmr_personal_data = new Payone_Api_Request_Parameter_ManageMandate_PersonalData();
			$mmr_personal_data->setCustomerid($_SESSION['customer_id']);
			$mmr_personal_data->setLastname($order->billing['lastname']);
			$mmr_personal_data->setFirstname($order->billing['firstname']);
			$mmr_personal_data->setCompany($order->billing['company']);
			$mmr_personal_data->setStreet($order->billing['street_address'] . (empty($order->billing['house_number'])
				                              ? ''
				                              : ' ' . $order->billing['house_number']));
			$mmr_personal_data->setZip($order->billing['postcode']);
			$mmr_personal_data->setCity($order->billing['city']);
			$mmr_personal_data->setCountry($order->billing['country']['iso_code_2']);
			$mmr_personal_data->setEmail($order->customer['email_address']);
			$mmr_personal_data->setLanguage($_SESSION['language_code']);
			$manage_mandate_request->setPersonalData($mmr_personal_data);
			$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment();
			$payment_method->setBankcountry($_SESSION['payone_elv_data']['country']);
			$payment_method->setBankaccount($_SESSION['payone_elv_data']['accountnumber']);
			$payment_method->setBankcode($_SESSION['payone_elv_data']['bankcode']);
			$payment_method->setIban($_SESSION['payone_elv_data']['iban']);
			$payment_method->setBic($_SESSION['payone_elv_data']['bic']);
			$manage_mandate_request->setPayment($payment_method);

			$manage_mandate_result = $mandate_service->managemandate($manage_mandate_request);
			$this->payone->log("managemandate result:\n".print_r($manage_mandate_result, true));
			if($manage_mandate_result instanceof Payone_Api_Response_Error)
			{
				$error_msg = "ERROR retrieving SEPA mandate: ".$manage_mandate_result->getErrorcode().' - '.$manage_mandate_result->getErrormessage();
			}
			else if($manage_mandate_result instanceof Payone_Api_Response_Management_ManageMandate_Approved)
			{
				if($manage_mandate_result->isApproved())
				{
					$mandate_status = $manage_mandate_result->getMandateStatus();
					if($mandate_status == 'pending' || $mandate_status == 'active')
					{
						$mandate_id = $manage_mandate_result->getMandateIdentification();
					}
					if($mandate_status == 'pending')
					{
						$mandate_text = urldecode($manage_mandate_result->getMandateText());
					}
				}
				else
				{
					$error_msg = 'ERROR: SEPA mandate not approved';
				}
			}
			else {
				$error_msg = 'ERROR retrieving SEPA mandate: unhandled response type';
			}

			if(isset($error_msg))
			{
				$this->payone->log($error_msg);
				$_SESSION['payone_error'] = $this->payone->get_text('payment_error');
				xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
			}

			if(isset($mandate_id))
			{
				$_SESSION['payone_elv_sepa_mandate_id'] = $mandate_id;
				$_SESSION['payone_elv_sepa_download_pdf'] = $this->pg_config['genre_specific']['sepa_download_pdf'];
			}

			if(isset($mandate_text) == true)
			{
				$_SESSION['payone_elv_sepa_mandate_mustconfirm'] = true;
				if($_GET['payment_error'] == 'must_confirm_sepa_mandate')
				{
					$highlight_class = 'p1_required';
				}
				else
				{
					$highlight_class = '';
				}

				$mandate_block .= '<div class="p1_elv_mandate">';
				$mandate_block .= '<div class="p1_elv_mandate_heading">##sepa_mandate_heading</div>';
				$mandate_block .= '<div class="p1_elv_mandate_info">##sepa_mandate_info</div>';
				$mandate_block .= '<div class="p1_elv_mandate_text">';
				# $mandate_block .= 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
				$mandate_block .= $mandate_text;
				$mandate_block .= '</div>';
				$mandate_block .= '<div class="p1_elv_mandate_checkbox '.$highlight_class.'">';
				$mandate_block .= '<input type="checkbox" name="mandate_confirm" value="1" id="p1_elv_mandate_confirm">';
				$mandate_block .= '<label for="p1_elv_mandate_confirm">##sepa_mandate_confirm_label</label>';
				$mandate_block .= '</div>';
				$mandate_block .= '</div>';
				$mandate_block = $this->payone->replaceTextPlaceholders($mandate_block);
				$pb .= $mandate_block;
			}
		}

		return $pb;
	}

	function before_process() {
		if(isset($_SESSION['tmp_oID']) === false)
		{
			# we're on the first run of checkout_process
			if($this->pg_config['genre_specific']['sepa_use_managemandate'] == 'true')
			{
				if(isset($_POST['mandate_confirm']) !== true && $_SESSION['payone_elv_sepa_mandate_mustconfirm'] == true)
				{
					unset($_SESSION['payone_elv_sepa_mandate_id']);
					$error_msg = urlencode($this->payone->get_text('error_must_confirm_mandate'));
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_confirmation.php?payment_error=must_confirm_sepa_mandate&ret_errormsg='.$error_msg);
				}
			}
		}
	}

	function payment_action() {
		$orders_id = $_SESSION['tmp_oID'];
		$order = new order($orders_id);
		$this->payone->log("(pre-)authorizing elv payment");
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
		$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment();
		$payment_method->setBankcountry($_SESSION['payone_elv_data']['country']);
		$payment_method->setBankaccount($_SESSION['payone_elv_data']['accountnumber']);
		$payment_method->setBankcode($_SESSION['payone_elv_data']['bankcode']);
		$payment_method->setIban($_SESSION['payone_elv_data']['iban']);
		$payment_method->setBic($_SESSION['payone_elv_data']['bic']);
		if(isset($_SESSION['payone_elv_sepa_mandate_id'])) {
			$payment_method->setMandateIdentification($_SESSION['payone_elv_sepa_mandate_id']);
		}
		#$payment_method->setBankaccountholder($_SESSION['payone_elv_data']['accountholder']);
		$request_parameters = array(
			'aid' => $this->global_config['subaccount_id'],
			'key' => $this->global_config['key'],
			'clearingtype' => 'elv',
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
			$this->payone->log("elv authorize request:\n".print_r($request, true));
			$response = $service->authorize($request);
			$this->payone->log("elv authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request = new Payone_Api_Request_Preauthorization($params);
			$this->payone->log("elv preauthorize request:\n".print_r($request, true));
			$response = $service->preauthorize($request);
			$this->payone->log("elv preauthorize response:\n".print_r($response, true));
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
			$this->_updateOrdersStatus($orders_id, 0, strtolower((string)$response->getStatus()), $this->payone->get_text('comment_error'));
			$_SESSION['payone_error'] = $response->getCustomermessage();
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		else {
			# var_dump($response);
			die('unhandled response type');
		}

		xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
	}

	function after_process() {
		parent::after_process();
		unset($_SESSION['payone_elv_data']);
		unset($_SESSION['payone_elv_sepa_mandate_mustconfirm']);
	}
}
MainFactory::load_origin_class('payone_elv');