<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/


	chdir('../../');

	require('includes/application_top.php');	
	//cushion error reporting
	error_reporting(0);

	$GLOBALS['coo_lang_file_master']->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/hgwConf.php');

	if(file_exists(DIR_WS_CLASSES.'class.heidelpaygw.php')){
		include_once(DIR_WS_CLASSES.'class.heidelpaygw.php');
	}else{
		require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.heidelpaygw.php');
	}
	
	$hgw = new heidelpayGW();
	$base = GM_HTTP_SERVER.DIR_WS_CATALOG;

	if(isset($_POST) && !empty($_POST)){


		$proc_Result			= !empty($_POST['PROCESSING_RESULT']) 			? htmlspecialchars($_POST['PROCESSING_RESULT']) : '';
		$proc_Return			= !empty($_POST['PROCESSING_RETURN']) 			? htmlspecialchars($_POST['PROCESSING_RETURN']) : '';
		$proc_ReturnCode		= !empty($_POST['PROCESSING_RETURN_CODE']) 		? htmlspecialchars($_POST['PROCESSING_RETURN_CODE']) : '';
		
		$proc_StatusCode		= !empty($_POST['PROCESSING_STATUS_CODE']) 		? htmlspecialchars($_POST['PROCESSING_STATUS_CODE']) : '';
		
		$crit_UserId			= !empty($_POST['CRITERION_USER_ID']) 			? htmlspecialchars($_POST['CRITERION_USER_ID']) : '';
		$crit_DbOnRg			= !empty($_POST['CRITERION_DBONRG']) 			? htmlspecialchars($_POST['CRITERION_DBONRG']) : '';
		$crit_Secret			= !empty($_POST['CRITERION_SECRET'])	 		? htmlspecialchars($_POST['CRITERION_SECRET']) : '';
		$crit_SessionID			= !empty($_POST['CRITERION_SESSIONID']) 		? htmlspecialchars($_POST['CRITERION_SESSIONID']) : '';
		
		$pay_Code				= !empty($_POST['PAYMENT_CODE']) 				? htmlspecialchars($_POST['PAYMENT_CODE']) : '';
		$ident_TransId			= !empty($_POST['IDENTIFICATION_TRANSACTIONID'])? htmlspecialchars($_POST['IDENTIFICATION_TRANSACTIONID']) : '';
		$ident_Uid				= !empty($_POST['IDENTIFICATION_UNIQUEID']) 	? htmlspecialchars($_POST['IDENTIFICATION_UNIQUEID']) : '';
		$ident_Sid				= !empty($_POST['IDENTIFICATION_SHORTID']) 		? htmlspecialchars($_POST['IDENTIFICATION_SHORTID']) : '';
		$ident_CredId			= !empty($_POST['IDENTIFICATION_CREDITOR_ID']) 	? htmlspecialchars($_POST['IDENTIFICATION_CREDITOR_ID']) : '';
		$acc_ExpMon				= !empty($_POST['ACCOUNT_EXPIRY_MONTH']) 		? htmlspecialchars((int)$_POST['ACCOUNT_EXPIRY_MONTH']) : '';
		$acc_ExpYear			= !empty($_POST['ACCOUNT_EXPIRY_YEAR']) 		? htmlspecialchars((int)$_POST['ACCOUNT_EXPIRY_YEAR']) : '';
		$acc_Brand				= !empty($_POST['ACCOUNT_BRAND']) 				? htmlspecialchars($_POST['ACCOUNT_BRAND']) : '';
		$acc_Holder				= !empty($_POST['ACCOUNT_HOLDER']) 				? htmlspecialchars($_POST['ACCOUNT_HOLDER']) : '';
		$acc_Iban				= !empty($_POST['ACCOUNT_IBAN']) 				? htmlspecialchars($_POST['ACCOUNT_IBAN']) : '';
		$acc_Bic				= !empty($_POST['ACCOUNT_BIC']) 				? htmlspecialchars($_POST['ACCOUNT_BIC']) : '';
		$acc_Numb				= !empty($_POST['ACCOUNT_NUMBER']) 				? htmlspecialchars($_POST['ACCOUNT_NUMBER']) : '';
		$acc_Bank				= !empty($_POST['ACCOUNT_BANK']) 				? htmlspecialchars($_POST['ACCOUNT_BANK']) : '';
		$acc_Ident				= !empty($_POST['ACCOUNT_IDENTIFICATION']) 		? htmlspecialchars($_POST['ACCOUNT_IDENTIFICATION']) : '';
		$cnt_Mail				= !empty($_POST['CONTACT_EMAIL']) 				? htmlspecialchars($_POST['CONTACT_EMAIL']) : '';
		$trans_Chan				= !empty($_POST['TRANSACTION_CHANNEL']) 		? htmlspecialchars($_POST['TRANSACTION_CHANNEL']) : '';
		
		$crit_BS_Legalnote		= !empty($_POST['CRITERION_BILLSAFE_LEGALNOTE'])? htmlspecialchars($_POST['CRITERION_BILLSAFE_LEGALNOTE']) : '';
		$crit_BS_Note			= !empty($_POST['CRITERION_BILLSAFE_NOTE']) 	? htmlspecialchars($_POST['CRITERION_BILLSAFE_NOTE']) : '';
		$crit_BS_Recipient		= !empty($_POST['CRITERION_BILLSAFE_RECIPIENT'])? htmlspecialchars($_POST['CRITERION_BILLSAFE_RECIPIENT']) : '';
		$crit_BS_accNumb		= !empty($_POST['CRITERION_BILLSAFE_ACCOUNTNUMBER']) ? htmlspecialchars($_POST['CRITERION_BILLSAFE_ACCOUNTNUMBER']) : '';
		$crit_BS_Bank			= !empty($_POST['CRITERION_BILLSAFE_BANKCODE']) ? htmlspecialchars($_POST['CRITERION_BILLSAFE_BANKCODE']) : '';
		$crit_BS_Bankname		= !empty($_POST['CRITERION_BILLSAFE_BANKNAME']) ? htmlspecialchars($_POST['CRITERION_BILLSAFE_BANKNAME']) : '';
		$crit_BS_Ref			= !empty($_POST['CRITERION_BILLSAFE_REFERENCE']) ? htmlspecialchars($_POST['CRITERION_BILLSAFE_REFERENCE']) : '';
		$crit_BS_Amount			= !empty($_POST['CRITERION_BILLSAFE_AMOUNT']) 	? htmlspecialchars($_POST['CRITERION_BILLSAFE_AMOUNT']) : '';
		$crit_BS_Currency		= !empty($_POST['CRITERION_BILLSAFE_CURRENCY']) ? htmlspecialchars($_POST['CRITERION_BILLSAFE_CURRENCY']) : '';
		$crit_BS_Iban			= !empty($_POST['CRITERION_BILLSAFE_IBAN']) 	? htmlspecialchars($_POST['CRITERION_BILLSAFE_IBAN']) : '';
		$crit_BS_Bic			= !empty($_POST['CRITERION_BILLSAFE_BIC']) 		? htmlspecialchars($_POST['CRITERION_BILLSAFE_BIC']) : '';
		$crit_BS_Period			= !empty($_POST['CRITERION_BILLSAFE_PERIOD']) 	? htmlspecialchars($_POST['CRITERION_BILLSAFE_PERIOD']) : '';
		
		$var_Pay				= !empty($_POST['payment']) 					? htmlspecialchars($_POST['payment']) : '';
		$var_Conditions			= !empty($_POST['conditions'])	 				? htmlspecialchars($_POST['conditions']) : '';
		$var_Withdrawal			= !empty($_POST['withdrawal']) 					? htmlspecialchars($_POST['withdrawal']) : '';
		$var_Comments			= !empty($_POST['comments']) 					? htmlspecialchars($_POST['comments']) : '';
		$var_sepa				= !empty($_POST['hpdd_sepa']) 					? htmlspecialchars($_POST['hpdd_sepa']) : '';
		

		$orgHash = $hgw->createSecretHash($ident_TransId);
 		if($crit_Secret != $orgHash){
			$hgw->log(__FILE__, "
				\n\tHash verification error, suspecting manipulation:
				\n\tIP: " . $_SERVER['REMOTE_ADDR'].
				"\n\tHash: ". $orgHash .
				"\n\tResponseHash: ". $crit_Secret
			);
			// redirect to error page
			$_SESSION['redirect_error'] = $hgw->getHPErrorMsg();
			if($var_Pay != ''){
				print $base.'checkout_payment.php?payment_error='.$var_Pay;
			}else{
				print $base.'checkout_payment.php?payment_error='.$_SESSION['payment'];
			}
			exit;
		}

		$hgw->saveRes($_POST);

		if($proc_Result == 'ACK'){
			$payType	= strtolower(substr($pay_Code, 0, 2));
			$transType	= strtolower(substr($pay_Code, 3, 2));
			$kto = $blz	= '';
			
			if(($transType == 'db') || ($transType == 'pa') || ((($payType == 'ot') || ($payType == 'pc')) && ($transType == 'rc'))){

				// set order state: debit or reservation
				if($transType == 'pa'){
					if(($payType == 'pp') || (($payType == 'iv') && ($acc_Brand != 'BILLSAFE'))){
						$status = '331';	// check payment receipt
					}else{
						$status = '332';	// reserved
					}
				}else{
 
					$pm = strtoupper(substr($hgw->getPaymentMethod($ident_TransId), 2));
					$status = $hgw->getStatus($pm, 'processed');
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
				
				if($proc_StatusCode == '80'){
					$status = '1'; // pending
				}
				
				if($payType == 'dd'){
					$repl = array(
						'{DD_IBAN}'		=> $acc_Iban."\n",
						'{DD_BIC}'		=> $acc_Bic."\n",
						'{DD_IDENT}'	=> $acc_Ident."\n"
					);
					$dd_htmlText .= strtr(HGW_TXT_DIRECTDEBIT, $repl);
					
					if($ident_CredId != ''){
						$repl = array(
							'{DD_CREDID}' => $ident_CredId."\n"
						);
						$dd_htmlText .= strtr(HGW_TXT_DIRECTDEBIT_CREDID, $repl);
					}
					$dd_htmlText .= HGW_TXT_DIRECTDEBIT_FUNDS;
				
					$_SESSION['hp']['INFO_TEXT_HTML']	= $dd_htmlText;					
					$search = array('<br/>', '<strong>', '</strong>');
					$replace = array('\n', '', '');
					$dd_text = strip_tags(str_replace($search, $replace, $dd_htmlText));
					$_SESSION['hp']['INFO_TEXT_TXT']	= $dd_text;					
				
					$hp_success = '<link type="text/css" rel="stylesheet" href="templates/'.$tplPathCss.'/heidelpay.css" />';
					$hp_success .= '<div class="heidelpay_success"><h3>
					<img class="icon" alt="" src="'.GM_HTTP_SERVER . DIR_WS_CATALOG.'templates/'.$tplPathImages.'/icons/icon-cheaper.png">
					'.HGW_TXT_PAYMENT_HEAD.'</h3>'.$dd_htmlText.'</div>';
					$_SESSION['nc_checkout_success_info'] = $hp_success;

				}elseif($payType == 'iv' && $acc_Brand == 'BILLSAFE'){
					$repl = array(
						'{AMOUNT}'			=> $hgw->formatNumber($crit_BS_Amount),
						'{CURRENCY}'		=> $crit_BS_Currency,
						'{BS_LEGALNOTE}'	=> $crit_BS_Legalnote."\n\n",
						'{BS_OWNER}'		=> $crit_BS_Recipient."\n",
						'{BS_NUMBER}'		=> $crit_BS_accNumb."\n",
						'{BS_BANKCODE}'		=> $crit_BS_Bank."\n",
						'{BS_IBAN}'			=> $crit_BS_Iban."\n",
						'{BS_BIC}'			=> $crit_BS_Bic."\n",
						'{BS_BANKNAME}'		=> $crit_BS_Bankname."\n\n",
						'{BS_REFERENCE}'	=> $crit_BS_Ref."\n",
						'{BS_SHOPNAME}'		=> $_SERVER['SERVER_NAME']."\n\n",
						'{BS_PERIOD}'		=> $crit_BS_Period,
					);
					$bs_htmlText = HGW_TXT_INVOICE_HEAD.'<br/>';					
					$bs_htmlText .= strtr(HGW_TXT_BILLSAFE, $repl);
					$_SESSION['hp']['INFO_TEXT_HTML']	= $bs_htmlText;
					$search = array('<br/>', '<strong>', '</strong>');
					$replace = array('\n', '', '');
					$bs_text = strip_tags(str_replace($search, $replace, $bs_htmlText));
					$_SESSION['hp']['INFO_TEXT_TXT']	= $bs_text;
				
					$hp_success = '<link type="text/css" rel="stylesheet" href="templates/'.$tplPathCss.'/heidelpay.css" />';
					$hp_success .= '<div class="heidelpay_success"><h3>
					<img class="icon" alt="" src="templates/'.$tplPathImages.'/icons/icon-cheaper.png">
					'.HGW_TXT_PAYMENT_HEAD.'</h3>'.$bs_htmlText.'</div>';
					$_SESSION['nc_checkout_success_info'] = $hp_success;
				}
				
				$order_id 	= $_SESSION['tmp_oID'];
				$comment	= '(Short-ID: '.$ident_Sid.')';

				$hgw->setOrderStatus($order_id, $status);
				$hgw->addHistoryComment($order_id, $comment, $status);

				print $base.'checkout_process.php';
			}else{

				// registration
				if($payType == 'dd'){
					if($var_sepa == 'iban'){
						$kto = substr($acc_Iban,0,2).str_repeat('*',strlen($acc_Iban)-6).substr($acc_Iban,-4);
						$blz = str_repeat('*',strlen($acc_Bic)-4).substr($acc_Bic,-4);							
					}else{
						$kto = str_repeat('*',strlen($acc_Numb)-4).substr($acc_Numb,-4);						
						$blz = str_repeat('*',strlen($acc_Bank)-4).substr($acc_Bank,-4);
					}
					$acc_Numb = '';
				}

				$customer = $_SESSION['hpLastCustomer'];
				unset($_SESSION['hpLastCustomer']);
				$shippingHash = hash('sha512', $customer['firstname'].$customer['lastname'].$customer['street_address'].$customer['postcode'].$customer['city'].$customer['country']['id']);
			
				// save registration in db
				$sql = "
				INSERT INTO `heidelpayGW_regdata` (userID, payType, uid, cardnr, expMonth, expYear, brand, owner, kto, blz, chan, shippingHash, email)
				VALUES ('".$crit_UserId."', '". $payType."', '". $ident_Uid."', '". $acc_Numb."', '". $acc_ExpMon."', '". $acc_ExpYear."', '". $acc_Brand."', '". $acc_Holder."', '". $kto."', '". $blz."', '". $trans_Chan."', '". $shippingHash."', '". $cnt_Mail."')
				ON DUPLICATE KEY UPDATE uid = '".$ident_Uid."', cardnr = '".$acc_Numb."', expMonth = '".$acc_ExpMon."', expYear = '".$acc_ExpYear."', brand = '".$acc_Brand."', owner = '".$acc_Holder."', kto = '".$kto."', blz = '".$blz."', chan = '".$trans_Chan."', shippingHash = '".$shippingHash."', email = '".$cnt_Mail."'";

				xtc_db_query($sql);

				if($crit_DbOnRg){
					print $base.'ext/heidelpay/heidelpayGW_gateway.php';
				}else{
					$_SESSION['conditions']	= $var_Conditions;
					$_SESSION['withdrawal']	= $var_Withdrawal;
					$_SESSION['payment']	= $var_Pay;
					$_SESSION['comments']	= $var_Comments;
					
					print $base.'checkout_confirmation.php';
				}
			}
		}else {
		
			if(!isset($_SESSION['payment'])){ $_SESSION['payment'] = $var_Pay; }
			$errorMsg = $hgw->getHPErrorMsg($proc_ReturnCode);
			$order_id 	= $_SESSION['tmp_oID'];
			$status		= constant('MODULE_PAYMENT_'.strtoupper($_SESSION['payment']).'_CANCELED_STATUS_ID');
			$comment	= $errorMsg.' (Short-ID: '.$ident_Sid.')';
			// restock order
			$hgw->removeOrder($order_id, true, true);
			$hgw->setOrderStatus($order_id, $status);
			$hgw->addHistoryComment($order_id, $comment, $status);
			
			$_SESSION['redirect_error'] = $errorMsg;

			unset($_SESSION['hp_tmp_oID']);
			unset($_SESSION['hp_tmp_glob']);
			unset($_SESSION['hp_tmp_otmod']);
			print $base.'checkout_payment.php?payment_error='.$_SESSION['payment'];
		}
	}else{
		if(((isset($_SERVER['HTTP_REFERER'])) && (is_int(strpos($_SERVER['HTTP_REFERER'], 'heidelpayGW_gateway.php')))) || ((isset($_SESSION['hp_lastSite'])) && (is_int(strpos($_SESSION['hp_lastSite'], 'heidelpayGW_gateway.php')))) && ($_GET['cancel'] == 1)){
			if($_SESSION['redirect_error'] == ''){	$_SESSION['redirect_error'] = HGW_MSG_CBU; }
			$url = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $_SESSION['payment'], 'SSL');

			$order_id = $_SESSION['tmp_oID'];
			$status = constant('MODULE_PAYMENT_'.strtoupper($_SESSION['payment']).'_CANCELED_STATUS_ID');
			// restock order
			$hgw->removeOrder($order_id, true, true);
			$hgw->setOrderStatus($order_id, $status);
			$hgw->addHistoryComment($order_id, $_SESSION['redirect_error'], $status);
			unset($_SESSION['hp_tmp_oID']);
			unset($_SESSION['hp_tmp_glob']);
			unset($_SESSION['hp_tmp_otmod']);
			unset($_SESSION['hp_lastSite']);
			xtc_redirect($url);
		}
	}
?>