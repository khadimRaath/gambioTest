<?php
/* --------------------------------------------------------------
   orders_billsafe.php 2015-10-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?>
<!-- BEGIN BILLSAFE -->
<?php
require_once DIR_FS_CATALOG.'gm/classes/GMBillSafe.php';
$bs = new GMBillSafe();
$bs_is_ready = $bs->paymentModuleIsConfigured();
if(strpos($order->info['payment_method'], 'billsafe_3') !== false && !$bs_is_ready) {
	?>
	<div style="border: 2px solid red; background: yellow; font: bold 1.1em sans-serif; padding: 1em; margin: 1em auto;">
		<?php echo $bs->get_text('warning_unconfigured') ?>
	</div>
	<?php
}
if(strpos($order->info['payment_method'], 'billsafe_3') !== false && $bs_is_ready && !$bs->isValidOrder((int)$_GET['oID'])) {
	?>
	<div style="border: 2px solid red; background: yellow; font: bold 1.1em sans-serif; padding: 1em; margin: 1em auto;">
		<?php echo $bs->get_text('warning_order_not_completed') ?>
	</div>
	<?php
}
else if(strpos($order->info['payment_method'], 'billsafe_3') !== false && $bs_is_ready && $bs->isValidOrder((int)$_GET['oID'])) {
	if(!is_array($_SESSION['billsafe_admin_messages'])) {
		$_SESSION['billsafe_admin_messages'] = array();
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pause_transaction'])) {
		try {
			$bs->pauseTransaction($_POST['orders_id'], $_POST['days']);
			$_SESSION['billsafe_admin_messages'][] = $bs->get_text('transaction_paused');
		}
		catch(BillSafeException $bse) {
			$_SESSION['billsafe_admin_messages'][] = $bse->getMessage();
		}
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['direct_payment'])) {
		try {
			$bs->reportDirectPayment($_POST['orders_id'], $_POST['dp_amount'], $_POST['dp_date']);
			$_SESSION['billsafe_admin_messages'][] = $bs->get_text('direct_payment_reported');
		}
		catch(BillSafeException $bse) {
			$_SESSION['billsafe_admin_messages'][] = $bse->getMessage();
		}
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_articles'])) {
		try {
			$bs->updateArticles((int)$_GET['oID'], $_POST['bsal']);
			$_SESSION['billsafe_admin_messages'][] = $bs->get_text('articles_updated');
		}
		catch(BillSafeException $bse) {
			$_SESSION['billsafe_admin_messages'][] = $bse->getMessage();
		}
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_articles'])) {
		try {
			foreach($_POST['bsal'] as $bsa_no => $bsa) {
				$_POST['bsal'][$bsa_no]['quantity'] = 0;
			}
			$bs->updateArticles((int)$_GET['oID'], $_POST['bsal']);
			$_SESSION['billsafe_admin_messages'][] = $bs->get_text('articles_cancelled');
		}
		catch(BillSafeException $bse) {
			$_SESSION['billsafe_admin_messages'][] = $bse->getMessage();
		}
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_shipment'])) {
		try {
			$shipment_params = array(
				'shippingdate' => strip_tags($_POST['shippingdate']),
				'parcel_service' => strip_tags($_POST['parcel_service']),
				'parcel_service_other' => strip_tags($_POST['parcel_service_other']),
				'parcel_trackingid' => strip_tags($_POST['parcel_trackingid']),
			);
			$bs->reportShipment($_POST['orders_id'], $_POST['bsal'], $shipment_params);
			$_SESSION['billsafe_admin_messages'][] = $bs->get_text('shipment_reported');
			if(is_numeric($_POST['bs_orders_status']) && $_POST['bs_orders_status'] > 0) {
				$comment = (empty($shipment_params['parcel_service_other']) ? $shipment_params['parcel_service'] : $shipment_params['parcel_service_other']).
					' / '.$shipment_params['parcel_trackingid'] .' / '. $shipment_params['shippingdate'];
				$bs->updateOrdersStatusAfterShipment($_POST['orders_id'], $_POST['bs_orders_status'], $comment);
				$_SESSION['billsafe_admin_messages'][] = $bs->get_text('orders_status_updated');
			}
		}
		catch(BillSafeException $bse) {
			if(!isset($_SESSION['billsafe_admin_messages'])) {
				$_SESSION['billsafe_admin_messages'] = array();
			}
			$_SESSION['billsafe_admin_messages'][] = $bse->getMessage();
		}
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reget_paymentinfo'])) {
		$bs->getPaymentInfoCached((int)$_GET['oID'], true);
		if(!isset($_SESSION['billsafe_admin_messages'])) {
			$_SESSION['billsafe_admin_messages'] = array();
		}
		$_SESSION['billsafe_admin_messages'][] = $bs->get_text('payment_info_updated');
		xtc_redirect(GM_HTTP_SERVER.$_SERVER['REQUEST_URI']);
	}

	$bs_orders_statuses = array(array('id' => '-1', 'text' => $bs->get_text('no_change')));
	$bs_orders_statuses = array_merge($bs_orders_statuses, $orders_statuses);
	if(function_exists('gm_get_conf')) {
		$bs_oid_after_shipment = gm_get_conf('BILLSAFE3_ORDERS_STATUS_AFTER_SHIPMENT');
	}
	else {
		$bs_oid_after_shipment = null;
	}

	$bs_payment_info = $bs->getPaymentInfo((int)$_GET['oID']);
	$bs_info = $bs->getOrderInfo((int)$_GET['oID']);
	$bs_articles = $bs->getArticleList((int)$_GET['oID']);
	$bs_messages = $_SESSION['billsafe_admin_messages'];
	$_SESSION['billsafe_admin_messages'] = array();
	?>
	<link rel="stylesheet" href="<?php echo GM_HTTP_SERVER.DIR_WS_ADMIN ?>includes/stylesheet_billsafe.css">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
				BillSAFE
			</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" class="billsafe">
		<tr>
			<td class="main billsafe_oi">
				<?php foreach($bs_messages as $bs_msg): ?>
					<p class="billsafe_message"><?php echo $bs_msg ?></p>
				<?php endforeach ?>
				<div class="flow_block">
					<div class="heading"><?php echo $bs->get_text('general_info') ?></div>
					<table class="bs_info">
						<?php foreach($bs_info as $label_key => $value): ?>
						<tr>
							<td><?php echo $bs->get_text('label_'.$label_key) ?></td>
							<td><?php echo $value ?></td>
						</tr>
						<?php endforeach ?>
					</table>
				</div>
				<?php if($order->info['payment_method'] == 'billsafe_3_invoice'): ?>
				<div class="flow_block">
					<div class="heading"><?php echo $bs->get_text('direct_payment') ?></div>
					<form class="direct_payment" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
						<input type="hidden" name="orders_id" value="<?php echo (int)$_GET['oID'] ?>">
						<label for="dp_amount"><?php echo $bs->get_text('dpamount') ?></label>
						<input id="dp_amount" name="dp_amount" type="text">&nbsp;EUR<br>
						<label for="dp_date"><?php echo $bs->get_text('dpdate') ?></label>
						<input id="dp_date" name="dp_date" type="text" value="<?php echo date('Y-m-d') ?>"><br>
						<input type="submit" name="direct_payment" value="<?php echo $bs->get_text('send_directpayment') ?>" class="button">
					</form>
					<div class="heading" style="margin-top: 1ex"><?php echo $bs->get_text('pause_transaction') ?></div>
					<form class="direct_payment" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
						<input type="hidden" name="orders_id" value="<?php echo (int)$_GET['oID'] ?>">
						<label for="pt_days"><?php echo $bs->get_text('pause_transaction_days') ?></label>
						<input id="pt_days" name="days" type="text" value="1"><br>
						<input type="submit" name="pause_transaction" value="<?php echo $bs->get_text('send_pausetransaction') ?>" class="button">
					</form>
				</div>
				<?php endif ?>

				<div class="half_block">
					<div class="heading"><?php echo $bs->get_text('PAYMENT_INFO') ?></div>
					<div class="payment_info" style="font-size: 0.9em; line-height: 1.0;">
						<?php echo $bs_payment_info ?>
						<form class="direct_payment" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
							<input type="hidden" name="orders_id" value="<?php echo (int)$_GET['oID'] ?>">
							<input type="submit" name="reget_paymentinfo" value="<?php echo $bs->get_text('reget_paymentinfo') ?>" class="button">
						</form>
					</div>
				</div>

				<div class="full_block">
					<div class="heading"><?php echo $bs->get_text('articles'); ?></div>
					<?php if(!empty($bs_articles)): ?>
						<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
							<?php echo bsPrintArticlesList($bs, $bs_articles); ?>
							<input type="submit" class="button" name="update_articles" value="<?php echo $bs->get_text('update_articles') ?>">
							<input type="submit" class="button confirm" name="cancel_articles" value="<?php echo $bs->get_text('cancel_articles') ?>">
						</form>
					<?php else: ?>
						<p><?php echo $bs->get_text('no_articles'); ?></p>
					<?php endif ?>
				</div>

				<div class="full_block">
					<div class="heading"><?php echo $bs->get_text('shipped_articles') ?></div>
					<table class="shipped_articles">
						<tr>
							<th><?php echo $bs->get_text('shipping_date') ?></th>
							<th><?php echo $bs->get_text('parcel_service') ?></th>
							<th><?php echo $bs->get_text('article_number') ?></th>
							<th><?php echo $bs->get_text('article_type') ?></th>
							<th><?php echo $bs->get_text('article_name') ?></th>
							<th><?php echo $bs->get_text('article_quantity') ?></th>
							<th><?php echo $bs->get_text('article_grossprice') ?></th>
							<th><?php echo $bs->get_text('article_tax') ?></th>
						</tr>
						<?php
						$shipped_articles = $bs->getShippedArticles((int)$_GET['oID']);
						if(empty($shipped_articles)):
						?>
						<tr>
							<td colspan="8" class="no_articles"><?php echo $bs->get_text('no_articles') ?></td>
						</tr>
						<?php else: ?>
						<?php foreach($shipped_articles as $sarticle): ?>
						<tr>
							<td><?php echo $sarticle['shipping_date'] ?></td>
							<td>
								<?php echo $sarticle['parcel_service'] .
									(!empty($sarticle['parcel_company']) ? '<br>'.$sarticle['parcel_company'] : '') .
									(!empty($sarticle['parcel_trackingid']) ? '<br>'.$sarticle['parcel_tracingid'] : ''); ?>
							</td>
							<td><?php echo $sarticle['article_number'] ?></td>
							<td><?php echo $sarticle['article_type'] ?></td>
							<td><?php echo $sarticle['article_name'] ?></td>
							<td><?php echo $sarticle['article_quantity'] ?></td>
							<td class="price"><?php echo number_format($sarticle['article_grossprice'], 2, '.', '') ?></td>
							<td><?php echo $sarticle['article_tax'] ?></td>
						</tr>
						<?php endforeach ?>
						<?php endif ?>
					</table>
				</div>

				<?php if(!empty($bs_articles)): ?>
					<div class="full_block bs_shipment">
						<div class="heading"><?php echo $bs->get_text('shipment') ?></div>
						<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
							<input type="hidden" name="orders_id" value="<?php echo (int)$_GET['oID'] ?>">
							<?php echo bsPrintArticlesList($bs, $bs_articles, false); ?>
							<div class="shipment_data">
								<label for="bs_shippingdate"><?php echo $bs->get_text('shipping_date') ?></label>
								<select id="bs_shippingdate" name="shippingdate">
									<?php
									for($days_ago = GMBillSafe::SHIPPING_MAX_DAYS_AGO; $days_ago >= 0; $days_ago--):
										$date = date('Y-m-d', strtotime($days_ago .' days ago'));
										?>
										<option value="<?php echo $date ?>" <?php echo $days_ago == 0 ? 'selected="selected"' : '' ?>><?php echo $date ?></option>
									<?php endfor ?>
								</select>
								<br>
								<label for="bs_parcel_service"><?php echo $bs->get_text('parcel_service') ?></label>
								<select id="bs_parcel_service" name="parcel_service">
									<option value="none"><?php echo $bs->get_text('parcel_service_none') ?></option>
									<?php foreach($bs->getParcelServices() as $ps): ?>
									<option value="<?php echo $ps ?>"><?php echo $ps ?></option>
									<?php endforeach ?>
									<option value="OTHER"><?php echo $bs->get_text('parcel_service_other') ?></option>
								</select>
								<br>
								<label for="bs_parcel_service_other"><?php echo $bs->get_text('parcel_service_name') ?></label>
								<input id="bs_parcel_service_other" type="text" name="parcel_service_other">
								<br>
								<label for="bs_parcel_trackingid"><?php echo $bs->get_text('parcel_trackingid') ?></label>
								<input id="bs_parcel_trackingid" type="text" name="parcel_trackingid">
							</div>
							<input type="submit" class="button" name="send_shipment" value="<?php echo $bs->get_text('send_shipment') ?>">
							<?php echo $bs->get_text('and_set_orders_id_to') ?>
							<?php echo xtc_draw_pull_down_menu('bs_orders_status', $bs_orders_statuses, $bs_oid_after_shipment); ?>
						</form>
					</div>
				<?php endif ?>
			</td>
		</tr>
	</table>
	<script>
	$(function() {
		$('.billsafe_oi').delegate('input.confirm', 'click', function(e) {
			var really = confirm('<?php echo $bs->get_text('are_you_sure') ?>');
			if(!really) {
				e.preventDefault();
			}
		});
	});
	</script>
<?php
}
?>
<!-- END BILLSAFE -->
<?php
function bsPrintArticlesList($bs, $bs_articles, $goods_only = false) {
	ob_start();
	?>
	<table class="article_list">
		<tr>
			<th><?php echo $bs->get_text('article_number') ?></th>
			<th><?php echo $bs->get_text('article_type') ?></th>
			<th><?php echo $bs->get_text('article_name') ?></th>
			<th><?php echo $bs->get_text('article_quantity') ?></th>
			<th><?php echo $bs->get_text('article_grossprice') ?></th>
			<th><?php echo $bs->get_text('article_tax') ?></th>
			<th class="qtyshipped"><?php echo $bs->get_text('article_qtyshipped') ?></th>
		</tr>
		<?php
		$bs_sum = 0;
		foreach($bs_articles as $bsa_no => $bsa):
			if($goods_only && $bsa['type'] != 'goods') {
				continue;
			}
			$bs_sum += $bsa['grossPrice'] * $bsa['quantity'];
			?>
			<tr>
				<td class="artno"><input name="bsal[<?php echo $bsa_no ?>][number]" value="<?php echo $bsa['number'] ?>" type="text"></td>
				<td class="type">
					<select name="bsal[<?php echo $bsa_no ?>][type]">
						<option value="goods" <?php if($bsa['type'] == 'goods') echo 'selected="selected"' ?>><?php echo $bs->get_text('article_type_goods') ?></option>
						<option value="shipment" <?php if($bsa['type'] == 'shipment') echo 'selected="selected"' ?>><?php echo $bs->get_text('article_type_shipment') ?></option>
						<option value="handling" <?php if($bsa['type'] == 'handling') echo 'selected="selected"' ?>><?php echo $bs->get_text('article_type_handling') ?></option>
						<option value="voucher" <?php if($bsa['type'] == 'voucher') echo 'selected="selected"' ?>><?php echo $bs->get_text('article_type_voucher') ?></option>
					</select>
				<td class="name"><input name="bsal[<?php echo $bsa_no ?>][name]" value="<?php echo $bsa['name'] ?>" type="text"></td>
				<td class="quantity"><input name="bsal[<?php echo $bsa_no ?>][quantity]" value="<?php echo $bsa['quantity'] ?>" type="text"></td>
				<td class="price"><input name="bsal[<?php echo $bsa_no ?>][grossPrice]" value="<?php echo $bsa['grossPrice'] ?>" type="text"></td>
				<td class="tax"><input name="bsal[<?php echo $bsa_no ?>][tax]" value="<?php echo $bsa['tax'] ?>" type="text"></td>
				<td class="qtyshipped"><input name="bsal[<?php echo $bsa_no ?>][quantityShipped]" value="<?php echo $bsa['quantityShipped'] ?>" type="text"></td>
			</tr>
		<?php endforeach ?>
		<tr class="sum">
			<td></td>
			<td></td>
			<td><?php echo $bs->get_text('sum') ?></td>
			<td></td>
			<td class="price"><?php echo number_format($bs_sum, 2, '.', '') ?></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<?php
	$article_list_html = ob_get_clean();
	return $article_list_html;
}

