<?php

$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
$userId                   = new IdType((int)$_SESSION['customer_id']);

?>
<div class="paypal3">
	<?php foreach($t_messages as $message): ?>
		<p class="message"><?php echo $message ?></p>
	<?php endforeach ?>
	<?php if($pp3_paycode === false): ?>
		<form action="<?php echo $pp3_paylink_action ?>" method="POST">
			<div>
				<span class="paypal-header">##paylink</span>
				<table>
					<thead>
					<tr><th colspan="2"
							data-gx-widget="collapser"
							data-collapser-parent_selector="thead"
							data-collapser-target_selector="tbody"
							data-collapser-section="paypal_make_paylink"
							data-collapser-user_id="<?php echo $userId; ?>"
							data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
								'paypal_make_paylink_collapse'); ?>"
							>##make_paylink</th></tr>
					</thead>
					<tbody>
					<tr>
						<td class="label">
							<label for="pp3_paylink_amount">##paylink_amount (<?php echo $order->info['currency'] ?>)</label>
						</td>
						<td>
							<input type="text" id="pp3_paylink_amount" name="pp3paylink[amount]" value="<?php echo $pp3_paylink_amount ?>">
							<input type="hidden" name="page_token" value="<?php echo $pp3paylink_page_token ?>">
							<input type="submit" value="##make_paylink">
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</form>
	<?php else: ?>
		<form action="<?php echo $pp3_paylink_action ?>" method="POST">
			<div>
				<span class="paypal-header">##paylink</span>
				<table>
					<thead>
					<tr><th colspan="2"
							data-gx-widget="collapser"
							data-collapser-parent_selector="thead"
							data-collapser-target_selector="tbody"
							data-collapser-section="paypal_paylink"
							data-collapser-user_id="<?php echo $userId; ?>"
							data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
								'paypal_paylink_collapse'); ?>"
							>##paylink</th></tr>
					</thead>
					<tbody>
					<tr>
						<td class="label">
							<label for="pp3_paylink_url">##paylink</label>
						</td>
						<td>
							<input id="pp3_paylink_url" type="text" readonly="readonly" value="<?php echo $pp3_paylink_url ?>">
						</td>
					</tr>
					<tr>
						<td class="label">
							<label for="pp3_paylink_amount">##paylink_amount</label>
						</td>
						<td>
							<input id="pp3_paylink_amount" type="text" readonly="readonly" value="<?php echo $pp3_paylink_amount ?>">
						</td>
					</tr>
					<tr>
						<td class="label">
							<label for="pp3_send_paylink">##send_paylink</label>
						</td>
						<td>
							<button id="pp3_send_paylink"
									data-gx-compatibility="orders/orders_modal_layer"
									data-orders_modal_layer-action="update_orders_status"
									data-orders_modal_layer-detail_page="true"
									data-orders_modal_layer-order_id="<?php echo (int)$GLOBALS['oID']; ?>"
									data-orders_modal_layer-comment="##paylink_note:
<?php /* KEEP THIS LINE ON THE LEFT! */ echo $pp3_paylink_url ?>"
									>##copy_paylink_to_note</button>
						</td>
					</tr>
					<tr>
						<td class="label">
							<label for="pp3_delete_paylink">##delete_paylink</label>
						</td>
						<td>
							<input type="submit" name="pp3paylink[delete]" id="pp3_delete_paylink" value="##delete_paylink">
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<input type="hidden" name="page_token" value="<?php echo $pp3paylink_page_token ?>">
		</form>
	<?php endif?>
</div>
