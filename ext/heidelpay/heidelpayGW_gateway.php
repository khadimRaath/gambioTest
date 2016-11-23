<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/
	chdir('../../');

	require('includes/application_top.php');

	$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
	$languageTextManager->init_from_lang_file('hgwConf', $_SESSION['languages_id']);

	if(file_exists(DIR_WS_CLASSES.'class.heidelpaygw.php')){
		include_once(DIR_WS_CLASSES.'class.heidelpaygw.php');
	}else{
		require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.heidelpaygw.php');
	}

	// getting template name to set paths to css, js and img folders
	$tplName         = CURRENT_TEMPLATE;
	$templateVersion = gm_get_env_info('TEMPLATE_VERSION');

 	if ($templateVersion === 3.0) {
 		// for Honeygrid
		$tplPathJs     = $tplName . '/assets/javascript/checkout';
		$tplPathCss    = $tplName . '/assets/styles';
		$tplPathImages = $tplName . '/assets/images';
	}
	else
	{
 		// for EyeCandy
		$tplPathJs     = $tplName . '/javascript/checkout';
		$tplPathCss    = $tplName . '/css';
		$tplPathImages = $tplName . '/img';
 	}
 		
	
	// get GLOBALS from payment_action()
	if(isset($_SESSION['hp_tmp_otmod'])){
		foreach($_SESSION['hp_tmp_otmod'] as $key => $value){
			if(file_exists(DIR_FS_CATALOG . 'includes/modules/order_total/'.$value)){
				require_once(DIR_FS_CATALOG . 'includes/modules/order_total/'.$value);
			}
		}
	}
	if(isset($_SESSION['hp_tmp_glob'])){
		foreach($_SESSION['hp_tmp_glob'] as $key => $value){
			if($key == 'order'){
				$GLOBALS[$key] = json_decode($value,true);
			}else{
				$GLOBALS[$key] = unserialize(serialize($value));
			}
		}
	}
	
	$order 		= (object) $order;
	$hgw 		= new heidelpayGW();
	$getConf	= $hgw->getConf;
	$tplVar 	= array();

	unset($_SESSION['hp']['INFO_TEXT_HTML']);
	unset($_SESSION['hp']['INFO_TEXT_TXT']);
	
	// build $user
	foreach($_SESSION as $key => $var){
		if(is_int(strpos($key, 'customer_'))){
			$uKey = substr($key, strpos($key,'_')+1);		
			$user[$uKey] = $var;
		}
	}
	
	$errors['msg_checkPymnt']	= HGW_MSG_CHECKPYMNT;
	$errors['msg_fill'] 		= HGW_MSG_FILL;
	$errors['msg_crdnr'] 		= HGW_MSG_CRDNR;
	$errors['msg_cvv'] 			= HGW_MSG_CVV;
	$errors['msg_iban'] 		= HGW_MSG_IBAN;
	$errors['msg_bic'] 			= HGW_MSG_BIC;
	$errors['msg_account'] 		= HGW_MSG_ACCOUNT;
	$errors['msg_bank'] 		= HGW_MSG_BANK;
	$errors['msg_holder'] 		= HGW_MSG_HOLDER;

	$errors['msg_missmonth'] 	= HGW_MSG_CRDMISSEXPMONTH;
	$errors['msg_missyear'] 	= HGW_MSG_CRDMISSEXPYEAR;
	$errors['msg_misscvv'] 		= HGW_MSG_CRDMISSCVV;
	$errors['msg_missholder'] 	= HGW_MSG_CRDMISSHOLDER;
	
	$errors['msg_wrongnumber'] 	= HGW_MSG_CRDWRONGNUMBER;
	$errors['msg_wrongmonth'] 	= HGW_MSG_CRDWRONGMONTH;
	$errors['msg_wrongyear'] 	= HGW_MSG_CRDWRONGYEAR;
	$errors['msg_wrongverif'] 	= HGW_MSG_CRDWRONGVERIFI;

	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
	$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_CHECKOUT_PAYMENT, xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));	

	$host = HTTP_SERVER;
	if(isset($_SERVER['HTTPS'])){
		if($_SERVER['HTTPS'] == 'on'){ $host = HTTPS_SERVER; }
	}

	// if the customer is not logged in, redirect them to the login page
	if(!isset($_SESSION['customer_id'])){
		if(ACCOUNT_OPTIONS == 'guest'){
			xtc_redirect(xtc_href_link(FILENAME_CREATE_GUEST_ACCOUNT, '', 'SSL'));
		}else{
			xtc_redirect(xtc_href_link(FILENAME_LOGIN, '', 'SSL'));
		}
	}

	$_SESSION['tmp_oID'] = $_SESSION['hp_tmp_oID'];
	$activePayment = substr($_SESSION['payment'], 2);
	$bookingMode = array('cc','dc','dd','pay');

	if($activePayment == 'bs'){
		$ppd_crit = $hgw->getBasketDetails($order);
	}
	
	//check if payment method on gateway is from Heidepay
	if((strpos($_SESSION['payment'], 'hp') === false) || (strpos($_SESSION['payment'], 'hp') != 0)){
		$tplVar['notHp'] = true;
	}else{
		$hgw->coo_ot_total->process();
		if(in_array($activePayment, $bookingMode)){
			// CC, DC, DD, VA
			$booking = $getConf[$activePayment.'_bookingMode'];
			if($booking == 3 || $booking == 4){
				if($activePayment == 'pay'){
					$regData = $hgw->getRegData($_SESSION['customer_id'], 'va');
				}else{
					$regData = $hgw->getRegData($_SESSION['customer_id'], $activePayment);
				}

				$customer = $order->customer;
				$customer['id'] = $regData['userID'];
				
				$shippingHash = hash('sha512', $customer['firstname'].$customer['lastname'].$customer['street_address'].$customer['postcode'].$customer['city'].$customer['country']['id']);				
				$last = mktime(23,59,00,$regData['expMonth']+1,0,$regData['expYear']); // timestamp: last day from month of registration
				
				if(		!empty($regData) && 																		
						($regData['uid'] != '') && 																	
						((($regData['expMonth'] == '0') && ($regData['expYear'] == '0')) || ($last > time())) && 	
						(($regData['shippingHash'] == $shippingHash) || ($getConf['shippinghash'] == 1))			
					){
			
					$ppd_config	= $hgw->ppd_config($booking, $activePayment, $regData['uid'], true);
					$ppd_user 	= $hgw->ppd_user($customer);					
					$ppd_bskt['PRESENTATION.AMOUNT'] = $hgw->formatNumber($hgw->coo_ot_total->output['0']['value']);
					$ppd_bskt['PRESENTATION.CURRENCY'] = $order->info['currency'];
					$ppd_crit['IDENTIFICATION.TRANSACTIONID'] = $_SESSION['tmp_oID'];
					$ppd_crit['CRITERION.SECRET'] = $hgw->createSecretHash($_SESSION['tmp_oID']);
					$ppd_crit['CRITERION.SESSIONID'] = session_id();
					
					$params 		= $hgw->preparePostData($ppd_config, array(), $ppd_user, $ppd_bskt, $ppd_crit);
					$getFormUrl 	= $hgw->doRequest($params);


					if(trim($getFormUrl['FRONTEND.REDIRECT_URL']) == ''){
						$hgw->log(__FILE__, "
						\n\t".$activePayment.": " . $getFormUrl['PROCESSING.RETURN']);
						
						$_SESSION['hp_lastSite'] = __FILE__;
						$_SESSION['redirect_error'] = $hgw->getHPErrorMsg();

						$url = xtc_href_link('ext/heidelpay/heidelpayGW_response.php', 'cancel=1', 'SSL');
					
						xtc_redirect($url);
					}else{
						$tplVar['formUrl'] = $getFormUrl['FRONTEND.REDIRECT_URL'];
					}					
				}else{

					// form to register Card and then do a debit on registration
					// if registration of card is expired: reregister
					if(!empty($regData)){ $uid = $regData['uid']; }
					else{ $uid = NULL; }
					$getFormUrl = $hgw->getFormUrl($activePayment, $booking, $_SESSION['customer_id'], $uid, (array)$order, $ppd_crit);

					$cardBrands[$activePayment]	= json_decode(stripslashes($getFormUrl['CONFIG.BRANDS']), true);
					$bankCountry[$activePayment]	= json_decode(stripslashes($getFormUrl['CONFIG.BANKCOUNTRY']), true);

					if(trim($getFormUrl['FRONTEND.REDIRECT_URL']) == ''){
						$hgw->log(__FILE__, "
						\n\t".$activePayment.": " . $getFormUrl['PROCESSING.RETURN']);
						
						$_SESSION['hp_lastSite'] = __FILE__;
						$_SESSION['redirect_error'] = $hgw->getHPErrorMsg();
						$url = xtc_href_link('ext/heidelpay/heidelpayGW_response.php', 'cancel=1', 'SSL');
					
						xtc_redirect($url);			
					}
					$tplVar['formUrl'] 			= $getFormUrl['FRONTEND.REDIRECT_URL'];

					$tplVar['frontendPaymentFrameUrl']	= $getFormUrl['FRONTEND.PAYMENT_FRAME_URL'];
					$tplVar['tplName']					= $_SESSION['tpl'];
					$tplVar['baseurl']					= GM_HTTP_SERVER . DIR_WS_CATALOG;

					$tplVar['cardBrands'] 		= $cardBrands;
					$tplVar['bankCountry']		= $bankCountry;
					$tplVar['pm'] 				= $activePayment;
					$tplVar['heidel_iban'] 		= $getConf['iban'];
					$tplVar['user']				= $user;
					$tplVar['DbOnRg']			= true;
					$tplVar['errors']			= $errors;	
				}
			}else{

				$getFormUrl = $hgw->getFormUrl($activePayment, $booking, $_SESSION['customer_id'], NULL, (array)$order, $ppd_crit);
			
				if($getFormUrl['POST.VALIDATION'] == 'NOK' || trim($getFormUrl['FRONTEND.REDIRECT_URL']) == ''){
					$_SESSION['redirect_error'] = $hgw->getHPErrorMsg($getFormUrl['PROCESSING.RETURN.CODE']);
					$url = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $_SESSION['payment'], 'SSL');
				
					xtc_redirect($url);
				}
				$cardBrands[$activePayment]	= json_decode(stripslashes($getFormUrl['CONFIG.BRANDS']), true);
				$bankCountry[$activePayment]	= json_decode(stripslashes($getFormUrl['CONFIG.BANKCOUNTRY']), true);

				if($activePayment != 'pay'){ $tplVar['pm'] = $activePayment; }				
				$tplVar['formUrl'] 			= $getFormUrl['FRONTEND.REDIRECT_URL'];
				$tplVar['frontendPaymentFrameUrl']	= $getFormUrl['FRONTEND.PAYMENT_FRAME_URL'];
				$tplVar['tplName']					= $_SESSION['tpl'];
				$tplVar['baseurl']					= GM_HTTP_SERVER . DIR_WS_CATALOG;
				$tplVar['cardBrands'] 		= $cardBrands;
				$tplVar['bankCountry']		= $bankCountry;
				$tplVar['heidel_iban'] 		= $getConf['iban'];
				$tplVar['user']				= $user;
				$tplVar['errors']			= $errors;
			}
			
		}else{
			//other payment methods
			//payment method iDeal 
			if ($activePayment == 'idl'){
				$ppd_bskt['ACCOUNT.COUNTRY'] 	= $_SESSION['HP']['ACCOUNT.COUNTRY'];
				$ppd_bskt['ACCOUNT.HOLDER'] 	= $_SESSION['HP']['ACCOUNT.HOLDER'];
				$ppd_bskt['ACCOUNT.BANKNAME'] = $_SESSION['HP']['ACCOUNT.BANKNAME'];
				unset($_SESSION['HP']);
			}
			//payment method with IBAN & BIC
			if ($activePayment == 'gp'){
				$ppd_bskt['ACCOUNT.IBAN'] 	= $_SESSION['HP']['ACCOUNT.IBAN'];
				$ppd_bskt['ACCOUNT.BIC'] 		= $_SESSION['HP']['ACCOUNT.BIC'];
				$ppd_bskt['ACCOUNT.HOLDER'] = $_SESSION['HP']['ACCOUNT.HOLDER'];
				unset($_SESSION['HP']);
			}
			
			//payment method PostFinance 
			if ($activePayment == 'pf'){
				$ppd_bskt['ADDRESS.COUNTRY'] 	= $_SESSION['HP']['ADDRESS.COUNTRY'];
				$ppd_bskt['ACCOUNT.BRAND'] 		= $_SESSION['HP']['ACCOUNT.BRAND'];
				unset($_SESSION['HP']);
			}
			
			$ppd_config = $hgw->ppd_config(NULL, $activePayment, NULL, true);
			$ppd_user = $hgw->ppd_user();
			$ppd_bskt['PRESENTATION.AMOUNT'] = $hgw->formatNumber($hgw->coo_ot_total->output['0']['value']);
			$ppd_bskt['PRESENTATION.CURRENCY'] = $order->info['currency'];

			$ppd_crit['CRITERION.USER_ID'] = $_SESSION['customer_id'];
			$ppd_crit['IDENTIFICATION.TRANSACTIONID'] = $_SESSION['tmp_oID'];
			$ppd_crit['CRITERION.SECRET'] = $hgw->createSecretHash($_SESSION['tmp_oID']);
			$ppd_crit['CRITERION.SESSIONID'] = session_id();
			
			$params 		= $hgw->preparePostData($ppd_config, array(), $ppd_user, $ppd_bskt, $ppd_crit);
			$response 	= $hgw->doRequest($params);
		}
		
		if(($response['POST.VALIDATION'] == 'NOK') || ($response['PROCESSING.RESULT'] == 'NOK')){
			$hgw->log(__FILE__, "\n\t".$activePayment.": " . $response['PROCESSING.RETURN']);
			$_SESSION['redirect_error'] = $hgw->getHPErrorMsg($response['PROCESSING.RETURN.CODE']);
			$url = $base.'checkout_payment.php?payment_error='.$_SESSION['payment'];
			xtc_redirect($url);
		}
		
		if($response['PROCESSING.RESULT'] == 'ACK' || $response['POST.VALIDATION'] == 'ACK'){
			if(!empty($response['PROCESSING.REDIRECT_URL'])){
				$tplVar['formUrl'] = $response['PROCESSING.REDIRECT_URL'];			
				$input = array();
				
				foreach($response AS $k => $v){
					if(strpos($k,'PROCESSING.REDIRECT_PARAMETER_') !== false){
						$key = preg_replace('/PROCESSING.REDIRECT_PARAMETER_/', '', $k);
						$input[$key] = $v;
					}
				}				
				$tplVar['formInput'] = $input;
			}elseif(in_array($activePayment, array('pp', 'iv')) && empty($response['ACCOUNT.BRAND'])){
				$repl = array(
					'{AMOUNT}'			=> $hgw->formatNumber($order->info['total']),
					'{CURRENCY}'		=> $order->info['currency'],
					'{CONNECTOR_ACCOUNT_COUNTRY}'	=> $response['CONNECTOR.ACCOUNT.COUNTRY']."\n",
					'{CONNECTOR_ACCOUNT_HOLDER}'	=> $response['CONNECTOR.ACCOUNT.HOLDER']."\n",
					'{CONNECTOR_ACCOUNT_NUMBER}'	=> $response['CONNECTOR.ACCOUNT.NUMBER']."\n",
					'{CONNECTOR_ACCOUNT_BANK}'		=> $response['CONNECTOR.ACCOUNT.BANK']."\n",
					'{CONNECTOR_ACCOUNT_IBAN}'		=> $response['CONNECTOR.ACCOUNT.IBAN']."\n",
					'{CONNECTOR_ACCOUNT_BIC}'		=> $response['CONNECTOR.ACCOUNT.BIC']."\n",
					'{IDENTIFICATION_SHORTID}'		=> "\n\n".$response['IDENTIFICATION.SHORTID']."\n\n",
				);

				if($activePayment == 'iv'){ $pp_htmlText = HGW_TXT_INVOICE_HEAD.'<br/>'; }else{ $pp_htmlText = ''; }
				
				$pp_htmlText .= strtr(HGW_TXT_PREPAYMENT, $repl);
				$_SESSION['hp']['INFO_TEXT_HTML']	= $pp_htmlText;
				$search = array('<br/>', '<strong>', '</strong>');
				$replace = array('\n', '', '');				
				$pp_text = strip_tags(str_replace($search, $replace, $pp_htmlText));
				$_SESSION['hp']['INFO_TEXT_TXT']	= $pp_text;
					
				if(($activePayment == 'pp') || (($activePayment == 'iv') && ($response['ACCOUNT.BRAND'] == 'BILLSAFE'))){
					// build text for checkout_success page
					$hp_success = '<link type="text/css" rel="stylesheet" href="templates/'.$tplPathCss.'/heidelpay.css" />';
					$hp_success .= '<div class="heidelpay_success"><h3>
					<img class="icon" alt="" src="templates/'.$tplName.'/img/icons/icon-cheaper.png">
					'.HGW_TXT_PAYMENT_HEAD.'</h3>'.$pp_htmlText.'</div>';
					$_SESSION['nc_checkout_success_info'] = $hp_success;
				}

				$status 		= '331';	// check payment receipt
				$order_id 	= $_SESSION['tmp_oID'];
				$comment	= '(Short-ID: '.$response['IDENTIFICATION.SHORTID'].')';
				$hgw->setOrderStatus($order_id, $status);
				$hgw->addHistoryComment($order_id, $comment, $status);

				$url = xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
				xtc_redirect($url);
			}
			
			if(!empty($response['FRONTEND.REDIRECT_URL'])){
				$tplVar['formUrl'] = $response['FRONTEND.REDIRECT_URL'];	
			}
		}
	}

	$tplVar['docPath']		= DIR_FS_CATALOG;
	// makes the template-path variable
	$tplVar['tplPath']		= 'templates/'.$tplName; 
	$tplVar['error'] = 
				'<div class="hp_error">
					<div class="msg_checkPymnt">'.HGW_MSG_CHECKPYMNT.'</div>
					<div class="msg_fill">'.HGW_MSG_FILL.'</div>
					<div class="msg_crdnr">'.HGW_MSG_CRDNR.'</div>
					<div class="msg_cvv">'.HGW_MSG_CVV.'</div>
					<div class="msg_iban">'.HGW_MSG_IBAN.'</div>
					<div class="msg_bic">'.HGW_MSG_BIC.'</div>
					<div class="msg_account">'.HGW_MSG_ACCOUNT.'</div>
					<div class="msg_bank">'.HGW_MSG_BANK.'</div>
					<div class="msg_holder">'.HGW_MSG_HOLDER.'</div>
					
					<div class="msg_missnumber">'.HGW_MSG_CRDMISSNUMBER.'</div>
					<div class="msg_missmonth">'.HGW_MSG_CRDMISSEXPMONTH.'</div>
					<div class="msg_missyear">'.HGW_MSG_CRDMISSEXPYEAR.'</div>
					<div class="msg_misscvv">'.HGW_MSG_CRDMISSCVV.'</div>
					<div class="msg_missholder">'.HGW_MSG_CRDMISSHOLDER.'</div>
					<div class="msg_wrongnumber">'.HGW_MSG_CRDWRONGNUMBER.'</div>
					<div class="msg_wrongmonth">'.HGW_MSG_CRDWRONGMONTH.'</div>
					<div class="msg_wrongyear">'.HGW_MSG_CRDWRONGYEAR.'</div>
					<div class="msg_wrongverif">'.HGW_MSG_CRDWRONGVERIFI.'</div>
							
				</div>';
	

	// build gateway template
	$cView = new ContentView();
	$cView->init_smarty();
	$cView->set_content_template('module/heidelpay_gateway.html');

	// assign vars to smarty tpl
	foreach($tplVar as $key => $val){
		$cView->v_coo_smarty->assign($key, $val);
	}
	
	
	$coo_layout_control = MainFactory::create_object('LayoutContentControl');
	$coo_layout_control->set_data('GET', $_GET);
	$coo_layout_control->set_data('POST', $_POST);
	$coo_layout_control->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
	$coo_layout_control->set_('coo_product', $GLOBALS['product']);
	$coo_layout_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
	$coo_layout_control->set_('c_path', $GLOBALS['cPath']);
	$coo_layout_control->set_('main_content', $cView->get_html());
	$coo_layout_control->set_('request_type', $GLOBALS['request_type']);
	$coo_layout_control->proceed();

	$t_redirect_url = $coo_layout_control->get_redirect_url();
	if(empty($t_redirect_url) === false){
		xtc_redirect($t_redirect_url);
	}else{
		echo $coo_layout_control->get_response();
	}
?>
