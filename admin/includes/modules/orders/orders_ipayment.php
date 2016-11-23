<?php
/* --------------------------------------------------------------
   orders_ipayment.php 2015-10-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


if(strpos($order->info['payment_method'], 'ipayment') !== false) {
	function fix_amount_inputs($post_names) {
		foreach($post_names as $pn) {
			$_POST[$pn] = preg_replace('/(\d+),(\d+)/', '$1.$2', $_POST[$pn]);
		}
	}

	$coo_text_mgr = new LanguageTextManager('ipayment', $_SESSION['languages_id']);

	if(!is_array($_SESSION['orders_ipayment_messages'])) {
		$_SESSION['orders_ipayment_messages'] = array();
	}
	$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/admin/orders_ipayment.php');
	$ipayment = new GMIPayment($order->info['payment_method']);

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		fix_amount_inputs(array('capture_amount', 'reverse_amount', 'refund_cap_amount'));
		if(isset($_POST['capture']) && isset($_POST['capture_amount']) && is_numeric($_POST['capture_amount'])) {
			$result = $ipayment->capturePayment((int)$_GET['oID'], $_POST['capture_amount']);
			if($result === false) {
				$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('curl_error_in_service_call');
			}
			else {
				if($result['Status'] != '0') {
					$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('capture_failed').' ('.$result['Params']['ret_errormsg'].')';
				}
				else {
					//$_SESSION['orders_ipayment_messages'][] = '<pre>'.htmlspecialchars_wrapper(print_r($result, true)).'</pre>';	
					$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('capture_successful');
				}
			}
		}
		else if(isset($_POST['reverse']) && isset($_POST['reverse_amount']) && is_numeric($_POST['reverse_amount'])) {
			$result = $ipayment->reversePayment((int)$_GET['oID'], $_POST['reverse_amount']);
			if($result === false) {
				$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('curl_error_in_service_call');
			}
			else {
				if($result['Status'] != '0') {
					$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('reverse_failed').' ('.$result['Params']['ret_errormsg'].')';
				}
				else {
					//$_SESSION['orders_ipayment_messages'][] = '<pre>'.htmlspecialchars_wrapper(print_r($result, true)).'</pre>';	
					$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('reverse_successful');
				}
			}
		}
		else if(isset($_POST['refund_cap']) && isset($_POST['refund_cap_amount']) && is_numeric($_POST['refund_cap_amount'])) {
			$result = $ipayment->refundPayment((int)$_GET['oID'], $_POST['refund_cap_amount']);
			if($result === false) {
				$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('curl_error_in_service_call');
			}
			else {
				if($result['Status'] != '0') {
					$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('refund_cap_failed').' ('.$result['Params']['ret_errormsg'].')';
				}
				else {
					//$_SESSION['orders_ipayment_messages'][] = '<pre>'.htmlspecialchars_wrapper(print_r($result, true)).'</pre>';	
					$_SESSION['orders_ipayment_messages'][] = $coo_text_mgr->get_text('refund_cap_successful');
				}
			}
		}
		else {
			die(print_r($_POST, true));
		}
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	$ipay_messages = $_SESSION['orders_ipayment_messages'];
	$_SESSION['orders_ipayment_messages'] = array();

	$logs = $ipayment->getResponseLogs($_GET['oID']);

	foreach($logs as $log) {
		if($log['trx_typ'] == 'auth') {
			$payment_type = 'auth';
			$payment_amount = $log['trx_amount'];
			$payment_currency = $log['trx_currency'];
		}
		if($log['trx_typ'] == 'preauth') {
			$payment_type = 'preauth';
			$payment_amount = $log['trx_amount'];
			$payment_currency = $log['trx_currency'];
		}
		if($log['trx_typ'] == 'capture' && $log['ret_status'] == 'SUCCESS') {
			$capture_sum += $log['trx_amount'];
		}
	}

	?>
	<style>
	table.ipay_transactions {
		width: 99%;
	}
	table.ipay_transactions td, table.ipay_transactions th {
		padding: .2ex .5ex;
		vertical-align: top;
	}
	table.ipay_transactions thead {
		background: #ccc;
	}
	table.ipay_transactions .status_SUCCESS {
		background-color: #BCFFBC;
	}
	table.ipay_transactions .status_ERROR {
		background-color: #FFC4BC;
	}
	table.ipay_transactions tr.ipay_logline_details {
		display: none;
	}
	p.message {
		padding: 1ex 1em;
		border: 2px solid red;
		background-color: #ffa;
	}
	input.small {
		width: 8ex;
	}
	input.right {
		text-align: right;
	}
	div.ipay_actions form {
		display: inline-block;
		background-color: #ccc;
		margin: 2px;
		padding: 2px;
	}
	</style>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo IPAY_HEADING; ?>
			</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="2" class="ipayment">
		<tr>
			<td width="80" class="main" valign="top">
				<?php if(!empty($ipay_messages)): ?>
					<?php foreach($ipay_messages as $msg): ?>
						<p class="message"><?php echo $msg ?></p>
					<?php endforeach ?>
				<?php endif ?>	
				<?php if(!empty($logs)): ?>
					<strong>Transaktionen:</strong>
					<table class="ipay_transactions">
						<thead>
							<tr>
								<th>Zeit</th>
								<th>Status</th>
								<th>Info</th>
								<th>Typ</th>
								<th>Betrag</th>
								<th>Name</th>
								<th>Zahlart</th>
								<th>Land</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($logs as $tlno => $tl): ?>
								<tr class="status_<?php echo $tl['ret_status'] ?> ipay_logline" id="ipay_logline_<?php echo $tlno ?>" >
									<td><?php echo $tl['ret_transdate'].' '.$tl['ret_transtime'] ?></td>
									<td><?php echo $tl['ret_status'] ?></td>
									<?php if($tl['ret_status'] == 'ERROR'): ?>
										<td>
											<?php echo $tl['ret_errormsg'] .' ('.$tl['ret_errorcode'].')<br>'.$tl['ret_additionalmsg'] ?>
										</td>
									<?php else: ?>
										<td>
											Buchungsnummer: <?php echo $tl['ret_booknr'] ?><br>
											Transaktionsnummer: <?php echo $tl['ret_trx_number'] ?><br>
											Autorisierungsnummer: <?php echo $tl['ret_authcode'] ?>
										</td>
									<?php endif ?>
									<td><?php echo $tl['trx_typ'] ?></td>
									<td><?php echo number_format($tl['trx_amount'] / 100, 2, '.', '').' '.$tl['trx_currency'] ?></td>
									<td><?php echo $tl['addr_name'] ?></td>
									<td><?php echo $tl['trx_paymentmethod'].' ('.$tl['trx_paymenttyp'].')' ?></td>
									<td><?php echo $tl['trx_paymentdata_country'] ?></td>
								</tr>
								<tr id="ipay_logline_details_<?php echo $tlno ?>" class="ipay_logline_details">
									<td colspan="8">
										<table>
											<?php foreach($tl as $key => $value): ?>
											<tr><td><?php echo $key ?></td><td><?php echo htmlspecialchars_wrapper($value) ?></td></tr>
											<?php endforeach ?>
										</table>
									</td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
					<div>
						<?php if($payment_type == 'preauth' && $capture_sum < ($payment_amount * 1.15)): ?>
							<form method="POST" action="<?php echo GM_HTTP_SERVER.$_SERVER['REQUEST_URI'] ?>">
								<input type="submit" name="capture" value="Capture">
								<input class="small right" type="text" name="capture_amount" value="<?php echo number_format(($payment_amount - $capture_sum)/100, 2, '.', '') ?>">&nbsp;<?php echo $payment_currency ?>
							</form>
							<form method="POST" action="<?php echo GM_HTTP_SERVER.$_SERVER['REQUEST_URI'] ?>">
								<input type="submit" name="reverse" value="Vorautorisierung stornieren">
								<input type="hidden" name="reverse_amount" value="<?php echo $payment_amount ?>">
							</form>
						<?php endif ?>
						<?php if($payment_type == 'auth'): ?>
							<form method="POST" action="<?php echo GM_HTTP_SERVER.$_SERVER['REQUEST_URI'] ?>">
								<input type="submit" name="refund_cap" value="Rückbuchung">
								<input class="small right" type="text" name="refund_cap_amount" value="0.00">&nbsp;<?php echo $payment_currency ?>
							</form>
						<?php endif ?>
					</div>
				<?php endif ?>
			</td>
		</tr>
	</table>
	<script>
	$(function() {
		$('tr.ipay_logline').dblclick(function(e) {
			$(this).next().toggle();
		});
	});
	</script>
<?php
}
