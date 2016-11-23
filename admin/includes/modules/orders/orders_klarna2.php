<?php
/* --------------------------------------------------------------
	orders_klarna2.php 2013-04-19 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

if(strpos($order->info['payment_method'], 'klarna2') !== false) {
	defined('GM_HTTP_SERVER') OR define('GM_HTTP_SERVER', HTTP_SERVER);
	defined('PAGE_URL') OR define('PAGE_URL', GM_HTTP_SERVER.DIR_WS_ADMIN.'orders.php?'.http_build_query($_GET));
	$klarna = new GMKlarna();
	if(!is_array($_SESSION['orders_klarna2_messages'])) {
		$_SESSION['orders_klarna2_messages'] = array();
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(isset($_POST['check_order_status'])) {
			$status = $klarna->checkOrderStatus($_GET['oID']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('check_order_status_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('check_order_status_result') .' '. $klarna->get_text('order_status_'.$status);
			}
		}

		if(isset($_POST['cancel_reservation'])) {
			$status = $klarna->cancelReservation($_GET['oID']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('cancel_reservation_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('reservation_cancelled');
			}
		}

		if(isset($_POST['activate_reservation'])) {
			$status = $klarna->activateReservation($_GET['oID']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('activate_reservation_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('activate_reservation_result');
			}
		}

		if(isset($_POST['split_reservation'])) {
			$amount = (double)$_POST['split_reservation_amount'];
			$status = $klarna->splitReservation($_GET['oID'], $amount);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('split_reservation_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('reservation_split');
			}
		}

		if(isset($_POST['change_reservation'])) {
			$amount = (double)$_POST['change_reservation_amount'];
			$status = $klarna->changeReservation($_GET['oID'], $amount);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('change_reservation_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('reservation_changed');
			}
		}

		if(isset($_POST['credit_invoice'])) {
			$status = $klarna->creditInvoice($_GET['oID']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('credit_invoice_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('credit_invoice_result');
			}
		}

		if(isset($_POST['credit_part'])) {
			$status = $klarna->creditPart($_GET['oID'], $_POST['credit_part_qty']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('credit_part_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('credit_part_result');
			}
		}

		if(isset($_POST['return_amount'])) {
			$return_amount_amount = (double)$_POST['return_amount_amount'];
			$return_amount_vat = (double)$_POST['return_amount_vat'];
			$return_amount_description = $_POST['return_amount_description'];
			$status = $klarna->returnAmount($_GET['oID'], $return_amount_amount, $return_amount_vat, $return_amount_description);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('return_amount_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('return_amount_result');
			}
		}

		if(isset($_POST['email_invoice'])) {
			$status = $klarna->emailInvoice($_GET['oID']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('email_invoice_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('email_invoice_result');
			}
		}

		if(isset($_POST['send_invoice'])) {
			$status = $klarna->sendInvoice($_GET['oID']);
			if($status === false) {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('send_invoice_failed');
			}
			else {
				$_SESSION['orders_klarna2_messages'][] = $klarna->get_text('send_invoice_result');
			}
		}



		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}
	$klarna2_messages = $_SESSION['orders_klarna2_messages'];
	$_SESSION['orders_klarna2_messages'] = array();

	$ok_data = $klarna->getOrdersKlarnaData($_GET['oID']);
	$return_amounts = $klarna->getReturnAmounts($_GET['oID']);
	$credit_parts = $klarna->getCreditParts($_GET['oID']);

	?>
	<style>
	div.klarna_actions {
		float: right;
		width: 50%;
	}
	p.message {
		border: 1px solid #f33;
		background: #ffa;
		padding: 1ex 1em;
		margin: 1ex;
	}
	table.return_amounts, table.credit_parts { border-collapse: collapse; width: 99%;}
	table.return_amounts thead, table.credit_parts thead { text-align: center; font-weight: bold; background: #dddddd; }
	table.return_amounts th, table.return_amounts td, table.credit_parts th, table.credit_parts td { padding: 2px 3px; }
	</style>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo $klarna->get_text('orders_block_heading'); ?>
			</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="2" class="klarna">
		<tr>
			<td width="80" class="main" valign="top">
				<?php if(!empty($klarna2_messages)): ?>
					<?php foreach($klarna2_messages as $msg): ?>
						<p class="message"><?php echo $msg ?></p>
					<?php endforeach ?>
				<?php endif ?>
				<?php if($ok_data !== false): ?>
				<div class="klarna_actions">
					<?php if($ok_data['status'] != 'cancelled'): ?>
						<form action="<?php echo PAGE_URL ?>" method="POST">
							<?php if(empty($ok_data['inv_rno'])): // not yet activated ?>
								<button type="submit" name="check_order_status"><?php echo $klarna->get_text('check_order_status') ?></button>
								<button type="submit" name="cancel_reservation"><?php echo $klarna->get_text('cancel_reservation') ?></button>
								<?php if($ok_data['status'] != 'pending' && $ok_data['status'] != 'denied'): ?>
									<button type="submit" name="activate_reservation"><?php echo $klarna->get_text('report_shipment') ?></button>
								<?php endif ?>
								<br>
							<?php else: ?>
								<button type="submit" name="credit_invoice"><?php echo $klarna->get_text('credit_invoice') ?></button>
								<br>
								<hr>
								<table>
									<tr>
										<td><?php echo $klarna->get_text('return_amount_amount') ?>:</td>
										<td><input type="text" name="return_amount_amount" size="6" placeholder="0.00"></td>
									</tr>
									<tr>
										<td><?php echo $klarna->get_text('return_amount_vat') ?>:</td>
										<td><input type="text" name="return_amount_vat" size="6" placeholder="19">%</td>
									</tr>
									<tr>
										<td><?php echo $klarna->get_text('return_amount_description') ?>:</td>
										<td><input type="text" name="return_amount_description" placeholder="<?php echo $klarna->get_text('return_amount_description') ?>"></td>
									</tr>
									<tr>
										<td colspan="2"><button type="submit" name="return_amount"><?php echo $klarna->get_text('return_amount') ?></button></td>
									</tr>
								</table>
								<?php if(!empty($return_amounts)): ?>
									<table class="return_amounts">
										<thead>
											<tr>
												<th><?php echo $klarna->get_text('return_amount_time') ?></th>
												<th><?php echo $klarna->get_text('return_amount_description') ?></th>
												<th><?php echo $klarna->get_text('return_amount_amount') ?></th>
												<th><?php echo $klarna->get_text('return_amount_vat') ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($return_amounts as $ra): ?>
												<tr>
													<td><?php echo $ra['sent_time'] ?></td>
													<td><?php echo $ra['description'] ?></td>
													<td><?php echo number_format($ra['amount'], 2, '.', '') ?></td>
													<td><?php echo number_format($ra['vat'], 1, '.', '') ?>%</td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
								<?php endif ?>
								<hr>
								<button type="submit" name="email_invoice"><?php echo $klarna->get_text('email_invoice') ?></button>
								<button type="submit" name="send_invoice"><?php echo $klarna->get_text('send_invoice') ?></button>
								<hr>
								<table class="credit_part_products">
									<thead>
										<tr>
											<th><?php echo $klarna->get_text('cp_quantity') ?></th>
											<th><?php echo $klarna->get_text('cp_model') ?></th>
											<th><?php echo $klarna->get_text('cp_name') ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($order->products as $op_idx => $oproduct): ?>
											<tr>
												<td><input name="credit_part_qty[<?php echo $oproduct['model'] ?>]" type="text" placeholder="0" size="3"></td>
												<td><?php echo $oproduct['model'] ?></td>
												<td><?php echo $oproduct['name'] ?></td>
											</tr>
										<?php endforeach ?>
									</tbody>
								</table>
								<button type="submit" name="credit_part"><?php echo $klarna->get_text('credit_part') ?></button>
								<?php if(!empty($credit_parts)): ?>
									<table class="credit_parts">
										<thead>
											<tr>
												<th><?php echo $klarna->get_text('cp_time') ?></th>
												<th><?php echo $klarna->get_text('cp_quantity') ?></th>
												<th><?php echo $klarna->get_text('cp_model') ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($credit_parts as $cp): ?>
												<tr>
													<td><?php echo $cp['sent_time'] ?></td>
													<td><?php echo $cp['quantity'] ?></td>
													<td><?php echo $cp['products_model'] ?></td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
								<?php endif // !empty($credit_parts) ?>
							<?php endif ?>
						</form>
					<?php endif // cancelled ?>
				</div>
				<table class="ok_data">
					<tr>
						<td><?php echo $klarna->get_text('reservation_number') ?>:</td>
						<td><?php echo $ok_data['rno'] ?></td>
					</tr>
					<tr>
						<td><?php echo $klarna->get_text('reservation_status') ?>:</td>
						<td><?php echo $klarna->get_text('reservation_status_'.$ok_data['status']) ?></td>
					</tr>
					<?php if(!empty($ok_data['inv_rno'])): ?>
						<tr>
							<td><?php echo $klarna->get_text('risk_status') ?>:</td>
							<td><?php echo $ok_data['risk_status'] ?></td>
						</tr>
						<tr>
							<td><?php echo $klarna->get_text('inv_rno') ?>:</td>
							<td>
								<a href="<?php echo $klarna->getInvoicePDFURL($ok_data['inv_rno']) ?>" target='_new'>
									<?php echo $ok_data['inv_rno'] ?>
								</a>
							</td>
						</tr>
					<?php endif ?>
				</table>
				<?php endif ?>
			</td>
		</tr>
	</table>
	<script>
	$(function() {
	});
	</script>
<?php
}
