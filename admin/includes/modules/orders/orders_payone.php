<?php
/* --------------------------------------------------------------
	orders_payone.php 2016-05-23
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$payone_payment_methods = array('payone', 'payone_cc', 'payone_otrans', 'payone_installment', 'payone_wlt', 'payone_elv', 'payone_prepay', 'payone_cod', 'payone_invoice', 'payone_safeinv');
if(in_array($order->info['payment_method'], $payone_payment_methods)) {
	defined('GM_HTTP_SERVER') OR define('GM_HTTP_SERVER', HTTP_SERVER);
	$p1_form_action = GM_HTTP_SERVER.$_SERVER['REQUEST_URI']; // GM_HTTP_SERVER.DIR_WS_ADMIN.'orders.php?'.http_build_query($_GET);
	$payone = new GMPayOne();

	if(!is_array($_SESSION['orders_payone_messages'])) {
		$_SESSION['orders_payone_messages'] = array();
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		ob_clean();
		header('Content-Type: text/plain');
		if($_POST['cmd'] == 'capture') {
			/*
			print_r($_POST);
			die();
			*/
			$response = $payone->captureAmount($_POST['txid'], $_POST['portalid'], $_POST['p1_capture_amount'], $_POST['p1_capture_currency']);
			if($response->getStatus() == 'ERROR') {
				$_SESSION['orders_payone_messages'][] = $payone->get_text('error_occurred').": ".$response->getErrorcode().' '.$response->getErrormessage();
			}
			else {
				$_SESSION['orders_payone_messages'][] = $payone->get_text('amount_captured');
			}
		}
		if($_POST['cmd'] == 'refund') {
			// N.B., in this case $_POST['refund'] is an array w/ varying content
			$response = $payone->refundAmount($_POST['refund']);
			if($response->getStatus() == 'ERROR') {
				$_SESSION['orders_payone_messages'][] = $payone->get_text('error_occurred').": ".$response->getErrorcode().' '.$response->getErrormessage();
			}
			else {
				$_SESSION['orders_payone_messages'][] = $payone->get_text('amount_refunded');
			}
		}

		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	$payone_messages = $_SESSION['orders_payone_messages'];
	$_SESSION['orders_payone_messages'] = array();

	$orders_data = $payone->getOrdersData((int)$_GET['oID']);
	$capture_data = $payone->getCaptureData((int)$_GET['oID']);
	$addpaydata = $payone->getAddPaydata((int)$_GET['oID']);

	$nextSequenceNumber = 0;
	foreach($orders_data['transaction_status'] as $txstatus)
	{
		foreach($txstatus['data'] as $key => $value)
		{
			if($key == 'sequencenumber')
			{
				$nextSequenceNumber = max($sequencenumber, $value);
			}
		}
	}

	ob_start();
	?>
	<style>
	p.message {
		padding: 1ex 1em;
		border: 2px solid red;
		background-color: #ffa;
	}
	div.p1_box { background: #E2E2E2; float: left; padding: 1ex; margin: 1px; min-height: 13em; width: calc(50% - 2px);}
	div.p1_box.p1_box_wide { width: calc(100% - 2px); }
	div.p1_boxheading { font-size: 1.2em; font-weight: bold; background: #CCCCCC; padding: .2ex .5ex;}
	dl.p1_transaction { overflow: auto; margin: .5ex 0; }
	dl.p1_transaction dt, dl.p1_transaction dd { margin: 0; float: left; }
	dl.p1_transaction dt { clear: left; width: 12em; font-weight: bold; }

	dl.p1_addpaydata_list { overflow: auto; margin: .5ex 0; }
	dl.p1_addpaydata_list dt, dl.p1_transaction dd { margin: 0; float: left; }
	dl.p1_addpaydata_list dt { clear: left; width: 50%; font-weight: bold; }

	div.p1_txstatus {  }
	div.p1_txstatus_open { font-size: 1.2em; font-weight: bold; background: #faa; }
	div.p1_txstatus_received { margin: .5ex 0; cursor: pointer; }
	div.p1_txstatus_data { display: none; }
	dl.p1_txstatus_data_list { overflow: auto; margin: .5ex 0; }
	dl.p1_txstatus_data_list dt, dl.p1_txstatus_data_list dd { margin: 0; float: left; }
	dl.p1_txstatus_data_list dt { clear: left; width: 12em; font-weight: bold; }
	div.p1_capture form { display: block; padding: 0.5ex; }
	div.p1_refund label { display: inline-block; width: 5em; }

	</style>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
				##payone_orders_heading
			</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="2" class="payone">
		<tr>
			<td width="80" class="main" valign="top">
				<?php if(!empty($payone_messages)): ?>
					<?php foreach($payone_messages as $msg): ?>
						<p class="message"><?php echo $msg ?></p>
					<?php endforeach ?>
				<?php endif ?>
				<div class="p1_transactions p1_box">
					<div class="p1_boxheading">##transactions</div>
					<?php foreach($orders_data['transactions'] as $transaction): ?>
						<dl class="p1_transaction">
							<dt>##txid</dt>
							<dd><?php echo $transaction['txid'] ?></dd>
							<dt>##userid</dt>
							<dd><?php echo $transaction['userid'] ?></dd>
							<dt>##date_created</dt>
							<dd><?php echo $transaction['created'] ?></dd>
							<dt>##date_last_modified</dt>
							<dd><?php echo $transaction['last_modified'] ?></dd>
							<dt>##status</dt>
							<dd><?php echo $transaction['status'] ?></dd>
						</dl>
					<?php endforeach ?>
				</div>

				<div class="p1_txstatus p1_box">
					<div class="p1_boxheading">##transaction_status</div>
					<?php if(empty($orders_data['transaction_status'])): ?>
						<p>##no_transaction_status_received</p>
					<?php else: ?>
						<?php foreach($orders_data['transaction_status'] as $txstatus): ?>
							<div class="p1_txstatus">
								<div class="p1_txstatus_received">
									<?php echo $txstatus['received'] .' ('.$txstatus['data']['txaction'].')' ?>
								</div>
								<div class="p1_txstatus_data">
									<dl class="p1_txstatus_data_list">
										<?php foreach($txstatus['data'] as $key => $value): ?>
											<dt><?php echo $key ?></dt>
											<dd><?php echo $value ?></dt>
										<?php endforeach ?>
									</dl>
								</div>
							</div>
						<?php endforeach ?>
					<?php endif ?>
				</div>

				<?php if(!empty($addpaydata)): ?>
					<div class="p1_box p1_box_wide p1_addpaydata">
						<div class="p1_boxheading">##add_paydata</div>
						<div class="p1_addpaydata_list">
							<dl class="p1_addpaydata_list">
								<?php foreach($addpaydata as $addpaydata_row): ?>
									<dt><?= $addpaydata_row['name'] ?></dt>
									<dd><?= $addpaydata_row['value'] ?></dd>
								<?php endforeach ?>
							</dl>
						</div>
					</div>
				<?php endif ?>

				<?php if($capture_data !== false): ?>
					<div class="p1_capture p1_box">
						<div class="p1_boxheading">##capture_transaction</div>
						<form action="<?php echo $p1_form_action ?>" method="POST">
							<input type="hidden" name="cmd" value="capture">
							<input type="hidden" name="txid" value="<?php echo $capture_data['txid'] ?>">
							<input type="hidden" name="sequencenumber" value="<?php echo $nextSequenceNumber + 1 ?>">
							<input type="hidden" name="portalid" value="<?php echo $capture_data['portalid'] ?>">
							<input type="hidden" name="p1_capture_currency" value="<?php echo $capture_data['currency'] ?>">
							<label for="p1_capture_amount">##capture_amount</label>
							<input name="p1_capture_amount" id="p1_capture_amount" type="text" value="<?php echo $capture_data['price'] ?>">
							<?php echo $capture_data['currency'] ?><br>
							<input type="submit" name="capture" value="##capture_submit">
						</form>
					</div>
				<?php endif ?>


				<?php if($capture_data !== false): ?>
					<div class="p1_refund p1_box">
						<div class="p1_boxheading">##refund_transaction</div>
						<form action="<?php echo $p1_form_action ?>" method="POST">
							<input type="hidden" name="cmd" value="refund">
							<input type="hidden" name="refund[txid]" value="<?php echo $capture_data['txid'] ?>">
							<input type="hidden" name="refund[sequencenumber]" value="<?php echo $nextSequenceNumber + 1 ?>">
							<input type="hidden" name="refund[currency]" value="<?php echo $capture_data['currency'] ?>">
							<label for="p1_refund_amount">##refund_amount</label>
							<input name="refund[amount]" id="p1_refund_amount" type="text" value="<?php echo $capture_data['price'] ?>">
							<?php echo $capture_data['currency'] ?><br>
							<?php if(in_array($order->info['payment_method'], array('payone_invoice', 'payone_prepay', 'payone_cod'))): ?>
								<label for="bankcountry">##refund_bankcountry</label>
								<select id="bankcountry" name="refund[bankcountry]">
									<option value="DE">##refund_country_de</option>
									<option value="AT">##refund_country_at</option>
									<option value="NL">##refund_country_nl</option>
									<option value="FR">##refund_country_fr</option>
									<option value="CH">##refund_country_ch</option>
								</select>
								<br>
								<label for="bankaccount">##refund_bankaccount</label>
								<input id="bankaccount" name="refund[bankaccount]" type="text">
								<br>
								<label for="bankcode">##refund_bankcode</label>
								<input id="bankcode" name="refund[bankcode]" type="text">
								<br>
								<label for="bankbranchcode">##refund_bankbranchcode</label>
								<input id="bankbranchcode" name="refund[bankbranchcode]" type="text">
								<br>
								<label for="bankcheckdigit">##refund_bankcheckdigit</label>
								<input id="bankcheckdigit" name="refund[bankcheckdigit]" type="text">
								<br>
								<label for="iban">##refund_iban</label>
								<input id="iban" name="refund[iban]" type="text">
								<br>
								<label for="bic">##refund_bic</label>
								<input id="bic" name="refund[bic]" type="text">
								<br>
							<?php endif ?>
							<?php if($order->info['payment_method'] == 'payone_installment'): ?>
								installment (BillSAFE etc.)
							<?php endif ?>

							<input type="submit" name="refund_submit" value="##refund_submit">
						</form>
					</div>
				<?php endif ?>
			</td>
		</tr>
	</table>
	<script>
	$(function() {
		$('div.p1_txstatus_received').not('.p1_txstatus_open').click(function(e) {
			$('div.p1_txstatus_received').removeClass('p1_txstatus_open');
			$(this).addClass('p1_txstatus_open');
			$('div.p1_txstatus_data').hide();
			$('div.p1_txstatus_data', $(this).parent()).show();
		});
	});
	</script>
	<?php
	$content = ob_get_clean();
	while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
		$replacement = $payone->get_text($matches[1]);
		if(empty($replacement)) {
			$replacement = $matches[1];
		}
		$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
	}
	echo $content;

}
