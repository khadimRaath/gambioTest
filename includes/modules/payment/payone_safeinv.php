<?php
/* --------------------------------------------------------------
	payone_safeinv.php 2016-07-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once dirname(__FILE__).'/payone/payone_master.php';

class payone_safeinv_ORIGIN extends payone_master {
	var $payone_genre = 'safeinv';

	public function __construct() {
		$this->code = 'payone_safeinv';
		parent::__construct();
	}

	protected function _pyvEnabled()
	{
		$pyv_enabled = $this->pg_config['types']['payolutioninvoicing']['active'] == 'true';
		$pyv_enabled = $pyv_enabled && ($_SESSION['customer_b2b_status'] == 0 || $this->pg_config['genre_specific']['payolution_b2b_enabled'] == 'true');
		return $pyv_enabled;
	}

	public function selection()
	{
		if($this->_pyvEnabled() === false && $this->pg_config['types']['billsafe']['active'] == 'false')
		{
			return false;
		}
		$selection = parent::selection();
		return $selection;
	}

	function _paymentDataForm($active_genre_identifier) {
		$paymentTypes = $this->payone->getPaymentTypes();
		$paymentTypeCodeMapping = [
			'payolutioninvoicing' => 'PYV',
			'billsafe' => 'BYV',
		];
		$activatedTypes = [];
		foreach($paymentTypes['safeinv'] as $paymentType)
		{
			if($this->pg_config['types'][$paymentType]['active'] == 'true')
			{
				if($paymentType == 'payolutioninvoicing' && $this->_pyvEnabled() === false)
				{
					continue;
				}
				$activatedTypes[] = $paymentType;
			}
		}

		$agreementLink = sprintf('<a class="js-open-modal" href="%s" data-modal-type="iframe" target="_blank">%s</a>',
			xtc_href_link('shop.php', 'do=ExtraContent/PayolutionNote&config=' . $this->_getActiveGenreIdentifier(), 'SSL'),
			$this->payone->get_text('payolution_agreement')
		);
		$invoiceNoteText = $this->payone->get_text('payolution_invoice_note');
		$invoiceNote = str_replace('%agreement_link%', $agreementLink, $invoiceNoteText);

		$customerService = StaticGXCoreLoader::getService('Customer');
		$customer = $customerService->getCustomerById(MainFactory::create('IdType', $_SESSION['customer_id']));
		$customerDateOfBirth  = $customer->getDateOfBirth();

		$safeInfTypeSelected = empty($_SESSION['payone_safeinv_type']) ? '' : $_SESSION['payone_safeinv_type'];

		ob_start();
		include DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/checkout_payone_safeinv_form.html';
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
		if(isset($_POST['safeinv_type']) && in_array($_POST['safeinv_type'], ['BSV', 'PYV']))
		{
			$_SESSION['payone_safeinv_type'] = $_POST['safeinv_type'];
		}
		if($_SESSION['payone_safeinv_type'] == 'PYV' && empty($_POST['safeinv_agreement']))
		{
			$_SESSION['payone_error'] = $this->payone->get_text('safeinv_agreement_required');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		if(empty($_SESSION['payone_safeinv_type']))
		{
			$_SESSION['payone_error'] = $this->payone->get_text('safeinv_type_not_selected');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		if(isset($_POST['p1-safeinv-doby']) && isset($_POST['p1-safeinv-dobm']) && isset($_POST['p1-safeinv-dobd']))
		{
			$dobDateTime = new DateTime(sprintf('%04d-%02d-%02d', $_POST['p1-safeinv-doby'], $_POST['p1-safeinv-dobm'], $_POST['p1-safeinv-dobd']));
			$_SESSION['payone_dob'] = $dobDateTime->format('Y-m-d');
		}
		if(empty($_SESSION['payone_dob']))
		{
			$_SESSION['payone_error'] = $this->payone->get_text('error_no_dob');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
		$maximumBirthday = new DateTime('18 years ago');
		$customerDob     = new DateTime($_SESSION['payone_dob']);
		if($customerDob->getTimestamp() > $maximumBirthday->getTimestamp())
		{
			$_SESSION['payone_error'] = $this->payone->get_text('error_too_young');
			xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		}
	}

	function confirmation() {
		$confirmation = array(
			'title' => $this->payone->get_text('confirmation_pay_by_safeinv'),
		);
		return $confirmation;
	}

	function payment_action() {
		$this->payone->log('safeinv payment_action');
		$orders_id = $_SESSION['tmp_oID'];
		$order = new order($orders_id);
		$genre_identifier = $this->_getActiveGenreIdentifier();
		$request = $this->global_config['authorization_method'] == 'auth' ? 'authorization' : 'preauthorization';
		$financingtype = $_SESSION['payone_safeinv_type'];

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
		$personal_data->setIp($_SERVER['REMOTE_ADDR']);
		$birthdayDatetime = new DateTime($_SESSION['payone_dob']);
		$personal_data->setBirthday($birthdayDatetime->format('Ymd'));
		
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

		$payment_method = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing();
		$payment_method->setSuccessurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php');
		$payment_method->setBackurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_confirmation.php');
		$payment_method->setErrorurl(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->code);
		$payment_method->setFinancingtype($financingtype);

		$request_parameters = array(
			'aid'           => $this->global_config['subaccount_id'],
			'key'           => $this->global_config['key'],
			'clearingtype'  => 'fnc',
			'reference'     => $GLOBALS['insert_id'],
			'amount'        => round($order->info['pp_total'], 2),
			'currency'      => $GLOBALS['order']->info['currency'],
			'personal_data' => $personal_data,
			'delivery_data' => $delivery_data,
			'payment'       => $payment_method,
			'invoicing'     => $this->_getInvoicingTransaction($GLOBALS['insert_id']),
		);
		if($_SESSION['customer_b2b_status'] == true)
		{
			$request_parameters['add_paydata'] =
				[
					'b2b' => 'yes',
					'company_uid' => (string)$_SESSION['customer_vat_id'],
				];
		}
		$params = array_merge($standard_parameters, $request_parameters);
		$builder = new Payone_Builder($this->payone->getPayoneConfig());
		if($this->global_config['authorization_method'] == 'auth') {
			$service           = $builder->buildServicePaymentAuthorize();
			$params['request'] = 'authorization';
			$request           = new Payone_Api_Request_Authorization($params);
			$response          = $service->authorize($request);
			$this->payone->log("safeinv authorize request:\n".print_r($request, true));
			$this->payone->log("safeinv authorize response:\n".print_r($response, true));
		}
		else { // pre-auth
			$service           = $builder->buildServicePaymentPreauthorize();
			$params['request'] = 'preauthorization';
			$request           = new Payone_Api_Request_Preauthorization($params);
			$response          = $service->preauthorize($request);
			$this->payone->log("safeinv preauthorize request:\n".print_r($request, true));
			$this->payone->log("safeinv preauthorize response:\n".print_r($response, true));
		}

		if($response instanceof Payone_Api_Response_Preauthorization_Approved)
		{
			$this->payone->log("preauthorization approved");
			$this->payone->saveTransaction($GLOBALS['insert_id'], $response->getStatus(), $response->getTxid(), $response->getUserid());
			$this->_updateOrdersStatus($orders_id, $response->getTxid(), strtolower((string)$response->getStatus()), $this->payone->get_text('comment_preauth_approved'));
			if($financingtype === 'PYV')
			{
				$this->_storePayolutionPaymentInstruction($response, $order);
			}
		}
		else if($response instanceof Payone_Api_Response_Authorization_Approved)
		{
			$this->payone->log("authorization approved");
			$this->payone->saveTransaction($GLOBALS['insert_id'], $response->getStatus(), $response->getTxid(), $response->getUserid());
			$this->_updateOrdersStatus($orders_id, $response->getTxid(), strtolower((string)$response->getStatus()), $this->payone->get_text('comment_auth_approved'));
			if($financingtype === 'PYV')
			{
				$this->_storePayolutionPaymentInstruction($response, $order);
			}
		}
		else if($response instanceof Payone_Api_Response_Error)
		{
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
		unset($_SESSION['payone_safeinv_type']);
		unset($_SESSION['payone_dob']);
	}

	protected function _storePayolutionPaymentInstruction(Payone_Api_Response_Authorization_Abstract $authorization, order $order)
	{
		$due_date = date('Y-m-d', strtotime('+' . (int)$this->pg_config['genre_specific']['payolution_due_days'] . ' days'));
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$addPaydata = $authorization->getAllAddPaydata();
		if(!empty($addPaydata))
		{
			$insertAddPaydata = [];
			foreach($addPaydata as $name => $value)
			{
				$insertAddPaydata[] =
				[
					'orders_id' => (int)$order->info['orders_id'],
					'name' => (string)$name,
					'value' => (string)$value,
				];
				if($name == 'PaymentDetails_1_Installment_1_Due')
				{
					$due_date = date('Y-m-d', strtotime($value));
				}
			}
			$db->insert_batch('payone_add_paydata', $insertAddPaydata);
		}

		$reference = '';
		if(array_key_exists('clearing_reference', $addPaydata))
		{
			$reference = $addPaydata['clearing_reference'];
		}
		else
		{
			$reference = $authorization->getClearingReference();
		}
		$paymentInstructionData =
		[
			'orders_id'      => $order->info['orders_id'],
			'reference'      => $reference,
			'bank_name'      => $this->pg_config['genre_specific']['payolution_bank_name'],
			'account_holder' => $this->pg_config['genre_specific']['payolution_account_holder'],
			'iban'           => $this->pg_config['genre_specific']['payolution_iban'],
			'bic'            => $this->pg_config['genre_specific']['payolution_bic'],
			'value'          => $order->info['pp_total'],
			'currency'       => $order->info['currency'],
			'due_date'       => $due_date,
		];
		$db->insert('orders_payment_instruction', $paymentInstructionData);
	}

	public function install()
	{
		parent::install();
		$addPaydataTable = 'CREATE TABLE IF NOT EXISTS `payone_add_paydata` (
			 `add_paydata_id` int(11) NOT NULL AUTO_INCREMENT,
			 `orders_id` int(11) NOT NULL,
			 `name` varchar(255) NOT NULL,
			 `value` varchar(255) NOT NULL,
			 PRIMARY KEY (`add_paydata_id`),
			 UNIQUE KEY `orders_id` (`orders_id`,`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$db->query($addPaydataTable);
	}

}
MainFactory::load_origin_class('payone_safeinv');
