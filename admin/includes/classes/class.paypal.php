<?php
/* --------------------------------------------------------------
   class.paypal.php  2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------

*
 * Project:   	xt:Commerce - eCommerce Engine
 * @version $Id   
 *
 * xt:Commerce - Shopsoftware
 * (c) 2003-2007 xt:Commerce (Winger/Zanier), http://www.xt-commerce.com
 *
 * xt:Commerce ist eine gesch�tzte Handelsmarke und wird vertreten durch die xt:Commerce GmbH (Austria)
 * xt:Commerce is a protected trademark and represented by the xt:Commerce GmbH (Austria)
 *
 * @copyright Copyright 2003-2007 xt:Commerce (Winger/Zanier), www.xt-commerce.com
 * @copyright based on Copyright 2002-2003 osCommerce; www.oscommerce.com
 * @copyright Porttions Copyright 2003-2007 Zen Cart Development Team
 * @copyright Porttions Copyright 2004 DevosC.com
 * @license http://www.xt-commerce.com.com/license/2_0.txt GNU Public License V2.0
 * 
 * For questions, help, comments, discussion, etc., please join the
 * xt:Commerce Support Forums at www.xt-commerce.com
 * 
 */
MainFactory::load_class('paypal_checkout');

class paypal_admin_ORIGIN extends paypal_checkout {

	function GetTransactionDetails($txn_id) {
		$nvpstr = '&TRANSACTIONID=' . urlencode($txn_id);
		$resArray = $this->hash_call("getTransactionDetails", $nvpstr);

		$ack = strtoupper($resArray["ACK"]);
		if ($ack != "SUCCESS") {
			$this->build_error_message($resArray);
		}

		return $resArray;
	}

	function RefundTransaction($txn_id, $curr, $amount, $refund, $note = '') {
		// full refund ?
		if ($note != '')
			$note = '&NOTE=' . urlencode($note);
		if ($amount != $refund) {
			$refund = str_replace(',', '.', $refund);
			$nvpstr = '&TRANSACTIONID=' . urlencode($txn_id) . '&REFUNDTYPE=Partial&CURRENCYCODE=' . $curr . '&AMT=' . $refund . $note;
		} else {
			$nvpstr = '&TRANSACTIONID=' . urlencode($txn_id) . '&REFUNDTYPE=Full' . $note;
		}
		$resArray = $this->hash_call("RefundTransaction", $nvpstr);

		$ack = strtoupper($resArray["ACK"]);
		if ($ack != "SUCCESS") {
			$this->build_error_message($resArray);
		}
		return $resArray;
	}

	function DoCapture($txn_id, $curr, $amount, $capture_amount, $note = '') {
		if ($note != '') {
			$note = '&NOTE=' . urlencode($note);
		}

		$capture_amount = str_replace(',', '.', $capture_amount);
		$complete_type = 'NotComplete';
		if($capture_amount >= $amount) {
			$complete_type = 'Complete';
		}
		$nvpstr = '&AUTHORIZATIONID=' . urlencode($txn_id) . '&COMPLETETYPE='.$complete_type.'&CURRENCYCODE=' . $curr . '&AMT=' . $capture_amount . $note;

		$resArray = $this->hash_call("DoCapture", $nvpstr);

		$ack = strtoupper($resArray["ACK"]);
		if ($ack != "SUCCESS") {
			$this->build_error_message($resArray);
		}
		return $resArray;
	}

	function DoVoid($txn_id, $note = '') {
		if ($note != '') {
			$note = '&NOTE=' . urlencode($note);
		}
			
		$nvpstr = '&AUTHORIZATIONID=' . urlencode($txn_id) .  $note;

		$resArray = $this->hash_call("DoVoid", $nvpstr);

		$ack = strtoupper($resArray["ACK"]);
		if ($ack != "SUCCESS") {
			$this->build_error_message($resArray);
		}
		return $resArray;
	}
	

	function TransactionSearch($data) {
		global $date;
//		echo '<pre>';
//		print_r($data);
//		echo '</pre>';

		/*
		 * STARTDATE Yes STARTDATE=2006-08-15T17:00:00Z
		 * ENDDATE No
		 * EMAIL No
		 * RECEIVER No
		 * RECEIPTID No
		 * TRANSACTIONID Yes
		 * INVNUM No
		 * ACCT No
		 * SALUTATION No
		 * FIRSTNAME No
		 * MIDDLENAME No
		 * LASTNAME No
		 * SUFFIX No
		 * TRANSACTIONCLASS No
		 * AMT No
		 * TRANSACTIONSTATUS No
		 * STATUS No
		 */

		// date range
		if ($data['span'] == 'narrow') {
			// show range
			$startdate = (int) $data['from_y'] . '-' . (int) $data['from_m'] . '-' . (int) $data['from_t'] . 'T00:00:00Z';
			$enddate = (int) $data['to_y'] . '-' . (int) $data['to_m'] . '-' . (int) $data['to_t'] . 'T24:00:00Z';
		} else {
			/*
			 * 1 = last day
			 * 2 = last week
			 * 3 = last month
			 * 4 = last year
			 */
			switch ($data['for']) {
				case '1' :
					$cal_date = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
					$_date = array ();
					$_date['tt'] = date('d', $cal_date);
					$_date['mm'] = date('m', $cal_date);
					$_date['yyyy'] = date('Y', $cal_date);
					$startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
					$enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
					break;
				case '2' :
					$cal_date = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
					$_date = array ();
					$_date['tt'] = date('d', $cal_date);
					$_date['mm'] = date('m', $cal_date);
					$_date['yyyy'] = date('Y', $cal_date);
					$startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
					$enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
					break;
				case '3' :
					$cal_date = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
					$_date = array ();
					$_date['tt'] = date('d', $cal_date);
					$_date['mm'] = date('m', $cal_date);
					$_date['yyyy'] = date('Y', $cal_date);
					$startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
					$enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
					break;
				case '4' :
					$cal_date = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
					$_date = array ();
					$_date['tt'] = date('d', $cal_date);
					$_date['mm'] = date('m', $cal_date);
					$_date['yyyy'] = date('Y', $cal_date);
					$startdate = (int) $_date['yyyy'] . '-' . (int) $_date['mm'] . '-' . (int) $_date['tt'] . 'T00:00:00Z';
					$enddate = $date['actual']['yyyy'] . '-' . $date['actual']['mm'] . '-' . $date['actual']['tt'] . 'T24:00:00Z';
					break;

			}
		}

		// search in details
		$detail_search = '';
		if ($data['search_type'] != '') {
			switch ($data['search_first_type']) {
				case 'email_alias' :
					$detail_search = '&EMAIL=' . urlencode($data['search_type']);
					break;

				case 'trans_id' :
					$detail_search = '&TRANSACTIONID=' . urlencode($data['search_type']);
					break;

				case 'last_name_only' :
					$detail_search = '&LASTNAME=' . urlencode($data['search_type']);
					break;
				case 'last_name' :
					$search = explode(',', $data['search_type']);
					$detail_search = '&LASTNAME=' . urlencode(trim($search['0'])) . '&FIRSTNAME=' . urlencode(trim($search['1']));
					break;
				case 'invoice_id' :
					$detail_search = '&INVNUM=' . urlencode($data['search_type']);
					break;
			}
		}

		$nvpstr = '&STARTDATE=' . $startdate . '&ENDDATE=' . $enddate . '&CURRENCYCODE=EUR' . $detail_search;

//		echo $nvpstr;
		$resArray = $this->hash_call("TransactionSearch", $nvpstr);

		if ($resArray['ACK'] == 'Success') {
			$result = $this->createResultArray($resArray);
		}
		elseif ($resArray['ACK'] == 'SuccessWithWarning') {
			$this->SearchError['code'] = $resArray['L_ERRORCODE0'];
			$this->SearchError['shortmessage'] = $resArray['L_SHORTMESSAGE0'];
			$this->SearchError['longmessage'] = $resArray['L_LONGMESSAGE0'];
			$result = $this->createResultArray($resArray);
		} else {
			$this->SearchError['code'] = $resArray['L_ERRORCODE0'];
			$this->SearchError['shortmessage'] = $resArray['L_SHORTMESSAGE0'];
			$this->SearchError['longmessage'] = $resArray['L_LONGMESSAGE0'];
			$result = -1;
		}
		return $result;

	}

	function createResultArray($response) {

		$result = array ();
		$n = 0;
		$flag = true;
		while ($flag) {

			if (!isset ($response['L_TIMESTAMP' . $n])) {
				$flag = false;
				return -1;
			}
			$result[$n]['TIMESTAMP'] = $response['L_TIMESTAMP' . $n];
			$result[$n]['TYPE'] = $response['L_TYPE' . $n];
			$result[$n]['NAME'] = $response['L_NAME' . $n];
			$result[$n]['TXNID'] = $response['L_TRANSACTIONID' . $n];
			$result[$n]['STATUS'] = $response['L_STATUS' . $n];
			$result[$n]['AMT'] = $response['L_AMT' . $n];
			$result[$n]['FEEAMT'] = $response['L_FEEAMT' . $n];
			$result[$n]['NETAMT'] = $response['L_NETAMT' . $n];

			if (!isset ($response['L_TIMESTAMP' . ($n +1)]))
				$flag = false;
			$n++;
		}
		return $result;

	}

	function getStatusSymbol($status, $type = '', $reason = '') {

		switch ($status) {

			case 'Reversed' :
			case 'Refunded' :
				$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/action_refresh_blue.gif');
				break;

			case 'Completed' :
			case 'verified' :
			case 'confirmed' :
				$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_accept.gif');
				break;

			case 'Pending' :
				$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_clock.gif');
				if ($reason == 'authorization') {
					$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_capture.gif');
				}
				if ($reason == 'partial-capture') { //
					$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_partcapture.png');
				}
				if ($reason == 'completed-capture') {
					$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_capture.gif');
				}

				break;

			case 'Denied' :
			case 'unverified' :
			case 'Unconfirmed' :
			case 'unconfirmed' :
			case 'Voided' :
			case 'voided' :
				$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/exclamation.png');
				break;
				// search
			case 'Payment' :
			case 'Refund';
				switch ($type) {
					case 'Completed' :
						$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_accept.gif');
						break;
					case 'Pending' :
						$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_clock.gif');
						break;
					case 'Refunded' :
					case 'Partially Refunded';
						$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/action_refresh_blue.gif');
						break;
					case 'Cancelled' :
						$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_cancel.png');
						break;
				}

				break;
			case 'Transfer' :
				switch ($type) {
					case 'Completed' :
						$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_arrow_right.gif');
						break;
				}

			case '' :
				if ($type == 'new_case') {
					$symbol = xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/exclamation.png');
				}
				break;
		}

		return $symbol;

	}

	function mapResponse($data) {

		$data_array = array (
			'xtc_order_id' => $data['INVNUM'],
			'txn_type' => $data['TRANSACTIONTYPE'],
			'reason_code' => $data['REASONCODE'],
			'payment_type' => $data['PAYMENTTYPE'],
			'payment_status' => $data['PAYMENTSTATUS'],
			'pending_reason' => $data['PENDINGREASON'],
			'invoice' => $data['INVNUM'],
			'mc_currency' => $data['CURRENCYCODE'],
			'first_name' => $data['FIRSTNAME'],
			'last_name' => $data['LASTNAME'],
			'payer_business_name' => $data['BUSINESS'],
			'address_name' => $data['SHIPTONAME'],
			'address_street' => $data['SHIPTOSTREET'],
			'address_city' => $data['SHIPTOCITY'],
			'address_state' => $data['SHIPTOSTATE'],
			'address_zip' => $data['SHIPTOZIP'],
			'address_country' => $data['SHIPTOCOUNTRYNAME'],
			'address_status' => $data['ADDRESSSTATUS'],
			'payer_email' => $data['EMAIL'],
			'payer_id' => $data['PAYERID'],
			'payer_status' => $data['PAYERSTATUS'],
			'payment_date' => $data['TIMESTAMP'],
			'business' => '',
			'receiver_email' => $data['RECEIVEREMAIL'],
			'receiver_id' => $data['RECEIVERID'],
			'txn_id' => $data['TRANSACTIONID'],
			'parent_txn_id' => '',
			'num_cart_items' => '',
			'mc_gross' => $data['AMT'],
			'mc_fee' => $data['FEEAMT'],
			'mc_authorization' => $data['AMT'],
			'payment_gross' => '',
			'payment_fee' => '',
			'settle_amount' => $data['SETTLEAMT'],
			'settle_currency' => '',
			'exchange_rate' => $data['EXCHANGERATE'],
			'notify_version' => $data['VERSION'],
			'verify_sign' => '',
			'last_modified' => '',
			'date_added' => 'now()',
			'memo' => $data['DESC']
		);
		return $data_array;
	}

	function getPaymentType($type) {
		if ($type == '')
			return;
		return constant('TYPE_' . strtoupper($type));
	}

	function getStatusName($status, $type = '') {
		if ($type == 'new_case')
			return STATUS_CASE;
		// BOF GM_MOD:
		return constant('STATUS_' . strtoupper($status));
	}

	/*
	 * Get the PayPal paymentstatus for the admin in order details
	 * 
	 * @param int $orders_id Order ID
	 */
	function admin_notification($orders_id) {
		global $_GET;
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
		$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/admin/paypal.php');
		
		$query = "SELECT * FROM " . TABLE_PAYPAL . " WHERE xtc_order_id = '" . $orders_id . "' LIMIT 1";
		$query = xtc_db_query($query);
		if(xtc_db_num_rows($query)){
			$data = xtc_db_fetch_array($query);
			// BOF GM_MOD
			if(empty($data['txn_id'])) {
				// set the paypal error
				$paypal_error = $this->getPayPalErrorDescription($data['reason_code']);
				$response = array('ACK' => 'PFailure','ERROR' => $paypal_error);
			} elseif($data['pending_reason'] == 'authorization' || $data['pending_reason'] == 'order') {
				$response['PAYMENTSTATUS']		= $data['payment_status'];
				$response['TRANSACTIONTYPE']	= $data['txn_type'];
				$response['PENDINGREASON']		= $data['pending_reason'];
				$response['AMT']				= $data['mc_gross'];
				$response['CURRENCYCODE']		= $data['mc_currency'];
				$response['PAYERSTATUS']		= $data['payer_status'];
				$response['EMAIL']				= $data['payer_email'];
				$response['ADDRESSSTATUS']		= $data['address_status'];
				$response['SHIPTONAME']			= $data['address_name'];
				$response['SHIPTOSTREET']		= $data['address_street'];
				$response['SHIPTOZIP']			= $data['address_zip'];
				$response['SHIPTOCITY']			= $data['address_city'];
				$response['SHIPTOCOUNTRYNAME']	= $data['address_country'];
			} else {
				$response = $this->GetTransactionDetails($data['txn_id']);
				// if transaction failed
				if($response['ACK'] == 'Failure') {
					$paypal_error = $this->getPayPalErrorDescription($response['L_ERRORCODE0']);
					$response = array('ACK' => 'PFailure','ERROR' => $paypal_error.$response['L_LONGMESSAGE0']);
				}
			}
			// EOF GM_MOD

			// show transaction status
			$output = '<tr>
						<td width="170" class="main" valign="top"><b>' . TEXT_PAYPAL_PAYMENT . ':</b><br /></td>
						<td class="main">';

			// show INFO
			if ($response['ACK'] == 'Failure') {
				$output .= '<table width="300">
					<tr>
						<td colspan="2">' . $this->getErrorDescription($response['L_ERRORCODE0']) . '</dt>
					</tr>';
			} elseif($response['ACK'] == 'PFailure') {
				// BOF GM_MOD
				// show paypal error
				$output .= '<table width="300">
					<tr>
						<td colspan="2">' . $response['ERROR'] . '</td>
					</tr>';
				// EOF GM_MOD
			} else {

			// authorization ?
			if ($response['PAYMENTSTATUS'] == 'None' && $response['PENDINGREASON'] == 'other') {
				$response['PAYMENTSTATUS'] = 'Pending';
				$response['PENDINGREASON'] = 'authorization';
				$response['AMT'] = $response['AMT'] . ' ( ' . $data['mc_captured'] . ' Captured) ';
			}

			$output .= '<table width="300">
					<tr>
					<td width="10">' . $this->getStatusSymbol($response['PAYMENTSTATUS'], $response['TRANSACTIONTYPE'], $response['PENDINGREASON']) . '</dt>
					<td class="main">' . $this->getStatusName($response['PAYMENTSTATUS'], $response['TRANSACTIONTYPE']) . ' Total: ' . $response['AMT'] . ' ' . $response['CURRENCYCODE'] . '</td>
					</tr>';
			if($response['EMAIL'] != '') {
				$output .= '<tr>
					<td width="10">' . $this->getStatusSymbol($response['PAYERSTATUS']) . '</dt>
					<td class="main">' . $response['PAYERSTATUS'] . '(' . $response['EMAIL'] . ')' . '</td>
					</tr>';
			}
			if($response['ADDRESSSTATUS'] != '') {
				$output .= '<tr>
					<td width="10" valign="top">' . $this->getStatusSymbol($response['ADDRESSSTATUS']) . '</dt>
					<td class="main">(' . $response['ADDRESSSTATUS'] . ')<br>' . $response['SHIPTONAME'] . '<br>' . $response['SHIPTOSTREET'] . '<br>' . $response['SHIPTOZIP'] . ' ' . $response['SHIPTOCITY'] . '<br>' . $response['SHIPTOCOUNTRYNAME'] . '</td>
					</tr>';
			}
			$output .= '<tr>
					<td width="10" valign="top">' . xtc_image(IR_WS_IMAGES . 'icon_info.gif') . '</dt>
					<td class="main"><a href="' . xtc_href_link(FILENAME_PAYPAL, 'view=detail&back=order&page='.$_GET['page'].'&oID='.$_GET['oID'].'&action='.$_GET['action'].'&paypal_ipn_id=' . $data['paypal_ipn_id']) . '">' . TEXT_PAYPAL_DETAIL . '</td>
					</tr>';

			}
			$output .= '</table></td>
					  </tr>';
		} else {
			// BOF GM_MOD
			// If PayPal error 10011
			$output = '
				<tr>
					<td width="170" class="main" valign="top"><b>' . TEXT_PAYPAL_PAYMENT . ':</b><br /></td>
					<td class="main">
						<table width="300">
							<tr>
								<td colspan="2">' . STATUS_ERRORCODE_10011 . '</td>
							</tr>
						</table>
					</td>
				</tr>';
			// EOF GM_MOD
		}
		echo $output;
	}

	function getErrorDescription($err) {
		//return constant(strtoupper($err));
		$err = $_SESSION['reshash']['FORMATED_ERRORS'];
		unset ($_SESSION['reshash']['FORMATED_ERRORS']);
		return strtoupper($err);
	}

	/*
	 *  Get the PayPal errordescription bei errorcode
	 *
	 *	@param string $p_errorcode PayPal Errorcode
	 *  @return string $paypal_error PayPal Error Description
	 */
	function getPayPalErrorDescription($p_errorcode) {
		$paypal_error = false;
		if(defined('STATUS_ERRORCODE_'.$p_errorcode)) {
			$paypal_error = constant('STATUS_ERRORCODE_'.$p_errorcode);
		}
		return $paypal_error;
	}
	
	static function is_installed()
	{
		$t_sql = "SHOW TABLES LIKE 'paypal'";
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			return true;
		}
			
		return false;
	}
}

MainFactory::load_origin_class('paypal_admin');
