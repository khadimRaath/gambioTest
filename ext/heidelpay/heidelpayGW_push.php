<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/
	chdir('../../');

	require('includes/application_top.php');	
	//cushion error reporting
	error_reporting(0);

	if(file_exists(DIR_WS_CLASSES.'class.heidelpaygw.php')){
		include_once(DIR_WS_CLASSES.'class.heidelpaygw.php');
	}else{
		require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.heidelpaygw.php');
	}

	$hgw = new heidelpayGW();

	$rawPost = file_get_contents('php://input');

	/** Hack to remove a structur problem in criterion node */
	$rawPost = preg_replace('/<Criterion(\s+)name="(\w+)">(.+)<\/Criterion>/', '<$2>$3</$2>',$rawPost);
	$xml = simplexml_load_string($rawPost);

	$identTransId = htmlspecialchars((string)$xml->Transaction->Identification->TransactionID);
	$crit_Secret = htmlspecialchars((string)$xml->Transaction->Analysis->SECRET);
	
	$orgHash = $hgw->createSecretHash($identTransId);

	if($crit_Secret != $orgHash){

		$hgw->log(__FILE__, "
			\n\tHash verification error, suspecting manipulation:
			\n\tIP: " . $_SERVER['REMOTE_ADDR'].
			"\n\tHash: ". $orgHash .
			"\n\tResponseHash: ". $crit_Secret
		);
		exit;
	}

	$paymentCodeRaw = (string)$xml->Transaction->Payment['code'];
	$paymentCode = preg_match('/^[A-Z]{2}\.[A-Z]{2}$/', $paymentCodeRaw) ? $paymentCodeRaw : false;
	
	$uniqueIdRaw = (string)$xml->Transaction->Identification->UniqueID;
	$uniqueId = preg_match('/^[0-9A-Z]{32}$/', $uniqueIdRaw) ? $uniqueIdRaw : false;
	
	$shortIdRaw = (string)$xml->Transaction->Identification->ShortID;
	$shortId = preg_match('/^[0-9]{4}\.[0-9]{4}\.[0-9]{4}$/', $shortIdRaw) ? $shortIdRaw : false;
	
	$referenceIdRaw = (string)$xml->Transaction->Identification->ReferenceID;
	$referenceId = preg_match('/^[0-9A-Z]{32}$/', $referenceIdRaw) ? $referenceIdRaw : false;
	
	$processingResultRaw = (string)$xml->Transaction->Processing->Result;
	$processingResult = preg_match('/^ACK$|^NOK$/', $processingResultRaw) ? $processingResultRaw : false;
	
	$processingReturnCodeRaw = (string)$xml->Transaction->Processing->Return['code'];
	$processingReturnCode = preg_match('/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}$/', $processingReturnCodeRaw) ? $processingReturnCodeRaw : false;
	
	$statusCodeRaw = (int)$xml->Transaction->Processing->Status['code'];
	$statusCode = preg_match('/^[0-9]{2}$/', $statusCodeRaw) ? $statusCodeRaw : false;
	
	$presentationAmount = (float)$xml->Transaction->Payment->Clearing->Amount;
	
	$presentationCurrencyRaw = (string)$xml->Transaction->Payment->Clearing->Currency;
	$presentationCurrency = preg_match('/^[A-Z]{3}$/', $presentationCurrencyRaw) ? $presentationCurrencyRaw : false;
	
	$transactionChannelRaw = (string)$xml->Transaction['channel'];
	$transactionChannel = preg_match('/^[0-9A-Z]{32}$/', $transactionChannelRaw) ? $transactionChannelRaw : false;

	$xmlData = array(
		'PAYMENT_CODE'					=> $paymentCode,
		'IDENTIFICATION_UNIQUEID'		=> $uniqueId,
		'IDENTIFICATION_SHORTID'		=> $shortId,
		'IDENTIFICATION_TRANSACTIONID'	=> $identTransId,
		'IDENTIFICATION_REFERENCEID'	=> $referenceId,			
		'PROCESSING_RESULT'				=> $processingResult,
		'PROCESSING_RETURN_CODE'		=> $processingReturnCode,
		'PROCESSING_STATUS_CODE'		=> $statusCode,
		'TRANSACTION_SOURCE'			=> 'PUSH',
		'PRESENTATION_AMOUNT'			=> $presentationAmount,
		'PRESENTATION_CURRENCY'			=> $presentationCurrency,
		'TRANSACTION_CHANNEL'			=> $transactionChannel
	);

	$orderId = htmlspecialchars((string)$xml->Transaction->Identification->TransactionID);
	$orderStatus = $hgw->getOrderStatus($orderId);
	$pm = strtoupper(substr($hgw->getPaymentMethod($orderId), 2));
	$transType	= strtolower(substr($paymentCode, 3));
	$processingCode = (string)$xml->Transaction->Processing['code'];
	$processingCodeNumber = substr($processingCode, -5);
	$processingResultDB = $hgw->getProcessingResult($uniqueId);
	$comment	= '(Short-ID: '.$shortId.')';

 	if($transType != 'db' && $transType != 'rc' && $transType != 'cp' && $transType != 'fi'){
		exit;
	}

	if($processingResult != $processingResultDB || $processingResultDB == '') {
		$hgw->saveRes($xmlData);
	}
	
	$statusProcessed 	= $hgw->getStatus($pm, 'processed');
	$statusCanceled		= $hgw->getStatus($pm, 'canceled');
	$statusPending		= $hgw->getStatus($pm, 'pending');

	//set order status and add history comment
	if($processingResult == 'ACK' && $orderStatus != (int)$statusProcessed && $orderStatus != (int)$statusCanceled) {
		if($processingCodeNumber == '80.00' && $orderStatus != (int)$statusPending){
			$hgw->setOrderStatus($orderId, $statusPending);
			$hgw->addHistoryComment($orderId, $comment, $statusPending);
		}elseif(($orderStatus == (int)$statusPending || $orderStatus == '334' || $orderStatus == '331') && $processingCodeNumber == '90.35') {
			$status = '334';
			$hgw->setOrderStatus($orderId, $status);
			$hgw->addHistoryComment($orderId, $comment, $status);
		}else {
			$hgw->setOrderStatus($orderId, $statusProcessed);
			$hgw->addHistoryComment($orderId, $comment, $statusProcessed);
		}
	}elseif($processingResult == 'NOK' ) {
		$hgw->setOrderStatus($orderId, $statusCanceled);
		$hgw->addHistoryComment($orderId, $comment, $statusCanceled);
		$hgw->removeOrder($orderId, true, true);
	}


	$sendOrderStatus = $hgw->getSendOrderStatus($orderId);
	$sessionId = htmlspecialchars((string)$xml->Transaction->Analysis->SESSIONID);

	if($sendOrderStatus == '0' && $processingResult == 'ACK' && $statusCode != '80') {	
		//empty shopping cart and send confirmation mail
		$hgw->emptyCartAndSendMail($sessionId, $orderId);
	}	
 
?>