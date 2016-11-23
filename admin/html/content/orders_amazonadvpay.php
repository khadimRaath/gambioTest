<!--<style>-->
<!--table.amazonadvpay input[type="submit"] { display: inline-block; width: auto; }-->
<!--table.amazonadvpay p.message { margin: 1ex auto; border: 1px solid red; background: #ffa; padding: 1ex 1em; }-->
<!--table.amzadvpay_orderinfo { border-collapse: collapse; }-->
<!--table.amzadvpay_orderinfo td { x-border-bottom: 1px solid #aaa; padding: .3ex .5ex;}-->
<!--table.amzadvpay_orderinfo td.label { font-weight: bold; }-->
<!--table.amazonadvpay_authorizations { background: #E5E5E5; width: 99%; margin: auto; border-collapse: collapse; }-->
<!--table.amazonadvpay_authorizations td, table.amazonadvpay_authorizations th { border-right: 1px solid #555; padding: 0.25ex .5ex; }-->
<!--table.amazonadvpay_authorizations td:last-child, table.amazonadvpay_authorizations th:last-child { border-right: none; }-->
<!--table.amazonadvpay_authorizations th { border-bottom: 1px solid #555; }-->
<!--table.amazonadvpay_authorizations td.actions { min-width: 15em; }-->
<!--table.amazonadvpay_authorizations td.actions input[type="submit"] { display: inline-block; width: auto; }-->
<!--input.amzadvpay_amount { width: 5em; }-->
<!--table.amazonadvpay_captures { width: 99%; margin: 0 auto 1em; background: #D8DB92; border-collapse: collapse; }-->
<!--table.amazonadvpay_refunds { width: 99%; margin: 0 auto 1em; background: #8EE29F; border-collapse: collapse; }-->
<!--table.billing_address { border-collapse: collapse; }-->
<!--table.billing_address td { padding: 0 2px; }-->
<!--td.amount { text-align: right; }-->
<!--div.inline-half { display: inline-block; width: 48%; padding: 0.8%; vertical-align: top; }-->
<!--.toggle_head { font-weight: bold; cursor: pointer; background: #CCCCCC; border: 1px outset #eee; border-radius: 5px; margin: 2px; padding: 2px;  }-->
<!--.toggle_body { display: none; background: #DDDDDD; margin: 2px; padding: 3px; }-->
<!--</style>-->

<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
	<tr>
		<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
			##orders_amazonadvpay_heading
		</td>
	</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="amazonadvpay">
	<tr>
		<td width="80" class="main" valign="top">
			<?php if(!empty($t_messages)): ?>
				<?php foreach($t_messages as $msg): ?>
					<p class="message"><?php echo $msg ?></p>
				<?php endforeach ?>
			<?php endif ?>

			<div class="inline-half">
				<table class="amzadvpay_orderinfo">
					<tr>
						<td class="label">##order_reference_id</td>
						<td><?php echo $t_order_reference_id ?></td>
					</tr>
					<tr>
						<td class="label">##order_status</td>
						<td><?php echo $t_order_reference_status . $t_order_reference_status_reason ?></td>
					</tr>
					<tr>
						<td class="label">##amount</td>
						<td><?php echo $t_order_amount .' '. $t_order_currency ?></td>
					</tr>
					<tr>
						<td class="label">##creation_timestamp</td>
						<td><?php echo $t_order_creation_timestamp ?> (<?php echo $t_order_creation_timestamp_localtime ?>)</td>
					</tr>
				</table>
			</div>

			<div class="actions inline-half">
				<form action="<?php echo $t_page_url ?>" method="POST">
					<input type="hidden" name="amazonadvpay[update_data]" value="1">
					<input type="submit" class="button" value="##update_data">
				</form>

				<?php if($t_order_reference_status != 'Closed'): ?>
					<form action="<?php echo $t_page_url ?>" method="POST">
						<input type="hidden" name="amazonadvpay[close_order][order_ref_id]" value="<?php echo $t_order_reference_id ?>">
						<input type="submit" class="button" value="##close_order">
					</form>
				<?php endif ?>

				<?php if($t_order_reference_status == 'Open'): ?>
					<hr>
					<form action="<?php echo $t_page_url ?>" method="POST">
						<input type="hidden" name="amazonadvpay[auth][currency]" value="<?php echo $t_order_currency ?>">
						<label for="amzadvpay_auth_amount">##auth_amount</label>
						<input id="amzadvpay_auth_amount" name="amazonadvpay[auth][amount]" value="<?php echo $t_order_amount ?>"><?php echo $t_order_currency ?>
						<input type="submit" class="button" name="amzadvpay_authorize" value="##authorize_payment">
					</form>
				<?php endif ?>

			</div>

			<?php if(empty($t_authorizations) !== true): ?>
				<h2>##authorizations</h2>
				<table class="amazonadvpay_authorizations">
					<tr>
						<th>##amazon_authorization_id</th>
						<th>##authorization_state</th>
						<th>##expiration_timestamp</th>
						<th>##authorization_amount</th>
						<th>##captured_amount</th>
						<th>##actions</th>
					</tr>
					<?php foreach($t_authorization_details as $t_auth_ref_id => $t_auth_details): ?>
						<tr>
							<td><?php echo (string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->AmazonAuthorizationId ?></td>
							<td><?php
								echo $t_auth_state = (string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->State;
								if(empty($t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->ReasonCode) != true)
								{
									echo ' ('.(string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationStatus->ReasonCode.')';
								}
							?></td>
							<td><?php
								echo (string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->ExpirationTimestamp;
								echo ' ';
								echo date('c', strtotime((string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->ExpirationTimestamp));
							?></td>
							<td class="amount"><?php
								echo ($t_auth_amount = (string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationAmount->Amount) .' '.
									($t_currency_code = (string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->AuthorizationAmount->CurrencyCode)
							?></td>
							<td class="amount"><?php
								echo (string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->CapturedAmount->Amount .' '.
									(string)$t_auth_details->GetAuthorizationDetailsResult->AuthorizationDetails->CapturedAmount->CurrencyCode
							?></td>
							<td class="actions">
								<?php if($t_auth_state == 'Open'): ?>
									<form action="<?php echo $t_page_url ?>" method="POST">
										<input type="hidden" name="amazonadvpay[capture][auth_ref_id]" value="<?php echo $t_auth_ref_id ?>">
										<input type="hidden" name="amazonadvpay[capture][currency]" value="<?php echo $t_currency_code ?>">
										##capture_amount
										<input type="text" class="amzadvpay_amount" name="amazonadvpay[capture][amount]" value="<?php echo $t_auth_amount ?>">&nbsp;<?php echo $t_currency_code ?>
										<input type="submit" class="button" value="##capture_btn">
									</form>
								<?php endif ?>
								<?php if(in_array($t_auth_state, array('Open', 'Pending'))): ?>
									<form action="<?php echo $t_page_url ?>" method="POST">
										<input type="hidden" name="amazonadvpay[closeauth][auth_ref_id]" value="<?php echo $t_auth_ref_id ?>">
										<input type="submit" class="button" name="amazonadvpay_close_auth" value="##close_auth">
									</form>
								<?php endif ?>
								<?php if(isset($t_billing_addresses[$t_auth_ref_id])): ?>
									<div class="toggle_head">##billing_address</div>
									<div class="toggle_body">
										<form action="<?php echo $t_page_url ?>" method="POST">
											<table class="billing_address">
												<tr><td>##name</td><td><input type="text" name="amazonadvpay[billing_address][name]" value="<?php echo $t_billing_addresses[$t_auth_ref_id]['name'] ?>"></td></tr>
												<tr><td>##street</td><td><input type="text" name="amazonadvpay[billing_address][street]" value="<?php echo $t_billing_addresses[$t_auth_ref_id]['street'] ?>"></td></tr>
												<tr><td>##postcode</td><td><input type="text" name="amazonadvpay[billing_address][postcode]" value="<?php echo $t_billing_addresses[$t_auth_ref_id]['postcode'] ?>"></td></tr>
												<tr><td>##city</td><td><input type="text" name="amazonadvpay[billing_address][city]" value="<?php echo $t_billing_addresses[$t_auth_ref_id]['city'] ?>"></td></tr>
												<tr><td>##country_iso2</td><td><input type="text" name="amazonadvpay[billing_address][country_iso2]" value="<?php echo $t_billing_addresses[$t_auth_ref_id]['country_iso2'] ?>"></td></tr>
											</table>
											<input type="submit" class="button" value="##update_billing_address">
										</form>
									</div>
								<?php endif ?>
							</td>
						</tr>
						<?php if(empty($t_capture_details[$t_auth_ref_id]) !== true): ?>
							<tr>
								<td>&nbsp</td>
								<td colspan="5">
									<table class="amazonadvpay_captures">
										<tr>
											<th>##capture_id</th>
											<th>##capture_status</th>
											<th>##captured_amount</th>
											<th>##refunded_amount</th>
											<th>##actions</th>
										</tr>
										<?php foreach($t_capture_details[$t_auth_ref_id] as $capture_id => $capture_details): ?>
											<tr>
												<td><?php echo $capture_id ?></td>
												<td><?php echo $t_capture_state = (string)$capture_details->GetCaptureDetailsResult->CaptureDetails->CaptureStatus->State ?></td>
												<td class="amount"><?php
													echo (string)$capture_details->GetCaptureDetailsResult->CaptureDetails->CaptureAmount->Amount .' '.
														($t_capture_currency = (string)$capture_details->GetCaptureDetailsResult->CaptureDetails->CaptureAmount->CurrencyCode)
												?></td>
												<td class="amount"><?php
													echo (string)$capture_details->GetCaptureDetailsResult->CaptureDetails->RefundedAmount->Amount .' '.
														(string)$capture_details->GetCaptureDetailsResult->CaptureDetails->RefundedAmount->CurrencyCode
												?></td>
												<td>
													<?php if($t_capture_state == 'Completed'): ?>
														<form action="<?php echo $t_page_url ?>" method="POST">
															<input type="hidden" name="amazonadvpay[refund][capture_id]" value="<?php echo $capture_id ?>">
															<input type="hidden" name="amazonadvpay[refund][currency]" value="<?php echo $t_capture_currency ?>">
															##refund_amount
															<input type="text" class="amzadvpay_amount" name="amazonadvpay[refund][amount]">&nbsp;<?php echo $t_capture_currency ?>
															<input type="submit" class="button" value="##refund_btn">
														</form>
													<?php endif ?>
												</td>
												<?php if(empty($t_refund_details[$capture_id]) !== true): ?>
													<tr>
														<td>&nbsp;</td>
														<td colspan="4">
															<table class="amazonadvpay_refunds">
																<tr>
																	<th>##refund_id</th>
																	<th>##refund_status</th>
																	<th>##refunded_amount</th>
																</tr>
																<?php foreach($t_refund_details[$capture_id] as $refund_id => $refund_details): ?>
																	<tr>
																		<td><?php echo $refund_id ?></td>
																		<td><?php echo (string)$refund_details->GetRefundDetailsResult->RefundDetails->RefundStatus->State ?></td>
																		<td><?php
																			echo (string)$refund_details->GetRefundDetailsResult->RefundDetails->RefundAmount->Amount .' '.
																				(string)$refund_details->GetRefundDetailsResult->RefundDetails->RefundAmount->CurrencyCode
																		?></td>
																	</tr>
																<?php endforeach?>
															</table>
														</td>
													</tr>
												<?php endif ?>
											</tr>
										<?php endforeach ?>
									</table>
								</td>
							</tr>
						<?php endif ?>
					<?php endforeach ?>
				</table>
			<?php endif ?>


			<?php if(empty($t_debug) !== true): ?>
				<pre><?php echo $t_debug ?></pre>
			<?php endif ?>
		</td>
	</tr>
</table>

<script>
$(function() {
	$('body').delegate('.toggle_head', 'click', function(e) {
		var toggle_body = $(this).next('.toggle_body');
		toggle_body.slideToggle('fast');
	});
});
</script>
