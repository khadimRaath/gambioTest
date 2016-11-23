<?php

$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
$userId                   = new IdType((int)$_SESSION['customer_id']);

?>

<div class="paypal3">
	<?php foreach($t_messages as $message): ?>
		<p class="message"><?php echo $message ?></p>
	<?php endforeach ?>
	<?php if(!empty($payments)): ?>
		<?php foreach($payments as $payment): ?>
			<span class="paypal-header">##payment <?php echo $payment->id ?></span>
			<?php if($payment instanceof PayPalErrorPayment): ?>
				<div class="payment_error"><?php echo $payment->getMessage(); ?></div>
			<?php else: ?>
				<div>
					<table class="" style="">
						<thead>
							<tr><th colspan="2"
									data-gx-widget="collapser"
									data-collapser-parent_selector="thead"
									data-collapser-target_selector="tbody"
									data-collapser-section="paypal_basic_data"
									data-collapser-user_id="<?php echo $userId; ?>"
									data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
										'paypal_basic_data_collapse'); ?>"
									>##payment_basic_data</th></tr>
						</thead>
						<tbody>
							<tr><td class="label">##payment_id</td><td><?php echo $payment->id ?></td></tr>
							<tr><td class="label">##payment_create_time</td><td><?php echo $payment->create_time . ' ('.date('Y-m-d H:i:s', strtotime($payment->create_time)) . ')' ?></td></tr>
							<tr><td class="label">##payment_update_time</td><td><?php echo $payment->update_time . ' ('.date('Y-m-d H:i:s', strtotime($payment->update_time)) . ')' ?></td></tr>
							<tr><td class="label">##payment_state</td><td>##payment_state_<?php echo $payment->state ?></td></tr>
							<tr><td class="label">##payment_intent</td><td>##payment_intent_<?php echo $payment->intent ?></td></tr>
						</tbody>
					</table>

					<table class="" style="">
						<thead>
							<tr><th colspan="2"
									data-gx-widget="collapser"
									data-collapser-parent_selector="thead"
									data-collapser-target_selector="tbody"
									data-collapser-section="paypal_payer_data"
									data-collapser-user_id="<?php echo $userId; ?>"
									data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
										'paypal_payer_data_collapse'); ?>"
									>##payment_payer_data</th></tr>
						</thead>
						<tbody>
							<tr><td class="label">##payment_payer_status</td><td>##payment_payer_status_<?php echo strtolower($payment->payer->status) ?></td></tr>
							<tr><td class="label">##payment_payer_email</td><td><?php echo $payment->payer->payer_info->email ?></td></tr>
							<tr><td class="label">##payment_payer_id</td><td><?php echo $payment->payer->payer_info->payer_id ?></td></tr>
							<tr><td class="label">##payment_payer_name</td><td><?php echo $encHelper->transcodeInbound($payment->payer->payer_info->last_name .', '. $payment->payer->payer_info->first_name) ?></td></tr>
							<tr>
								<td class="label">##payment_payer_shipping_address</td>
								<td>
									<?php echo $encHelper->transcodeInbound($payment->payer->payer_info->shipping_address->recipient_name) ?><br>
									<?php echo $encHelper->transcodeInbound($payment->payer->payer_info->shipping_address->line1) ?><br>
									<?php if(!empty($payment->payer->payer_info->shipping_address->line2)): ?>
										<?php echo $encHelper->transcodeInbound($payment->payer->payer_info->shipping_address->line2) ?><br>
									<?php endif ?>
									<?php echo $encHelper->transcodeInbound($payment->payer->payer_info->shipping_address->country_code .' '. $payment->payer->payer_info->shipping_address->postal_code) ?>
									<?php
									echo $encHelper->transcodeInbound($payment->payer->payer_info->shipping_address->city);
									if(!empty($payment->payer->payer_info->shipping_address->state)):
										echo ', '.$encHelper->transcodeInbound($payment->payer->payer_info->shipping_address->state);
									endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
					<?php if(isset($payment->payment_instruction)): ?>
						<table class="" style="">
							<thead>
								<tr><th colspan="2">##payment_instruction</th></tr>
							</thead>
							<tbody>
								<tr>
									<td class="label">##pi_reference_number</td>
									<td><?php echo $payment->payment_instruction->reference_number ?></td>
								</tr>
								<tr>
									<td class="label">##pi_bank_name</td>
									<td><?php echo $payment->payment_instruction->recipient_banking_instruction->bank_name ?></td>
								</tr>
								<tr>
									<td class="label">##pi_account_holder_name</td>
									<td><?php echo $payment->payment_instruction->recipient_banking_instruction->account_holder_name ?></td>
								</tr>
								<tr>
									<td class="label">##pi_iban</td>
									<td><?php echo $payment->payment_instruction->recipient_banking_instruction->international_bank_account_number ?></td>
								</tr>
								<tr>
									<td class="label">##pi_bic</td>
									<td><?php echo $payment->payment_instruction->recipient_banking_instruction->bank_identifier_code ?></td>
								</tr>
								<tr>
									<td class="label">##pi_amount</td>
									<td><?php echo $payment->payment_instruction->amount->value ?> <?php echo $payment->payment_instruction->amount->currency ?></td>
								</tr>
								<tr>
									<td class="label">##pi_payment_due_date</td>
									<td><?php echo $payment->payment_instruction->payment_due_date ?></td>
								</tr>
							</tbody>
						</table>
					<?php endif ?>
				</div>

				<span class="paypal-header">##transactions</span>
				<?php foreach($payment->transactions as $transaction): ?>
					<div>
						<table class="">
							<thead>
								<tr><th colspan="3"
										data-gx-widget="collapser"
										data-collapser-parent_selector="thead"
										data-collapser-target_selector="tbody"
										data-collapser-section="paypal_transaction_summary"
										data-collapser-user_id="<?php echo $userId; ?>"
										data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
											'paypal_transaction_summary_collapse'); ?>"
										>##transaction_summary</th></tr>
							</thead>
							<tbody>
								<?php if(!empty($transaction->item_list)): ?>
									<?php foreach($transaction->item_list->items as $line_item): ?>
										<tr>
											<td class="qty"><?php echo $line_item->quantity ?></td>
											<td><?php echo $encHelper->transcodeInbound($line_item->name) ?></td>
											<td class="price"><?php echo $line_item->price . ' ' . $line_item->currency ?></td>
										</tr>
									<?php endforeach ?>
								<?php endif ?>
								<tr>
									<td class="qty">&nbsp;</td>
									<td>##subtotal</td>
									<td class="price"><?php printf('%.2f %s', (double)$transaction->amount->details->subtotal, $transaction->amount->currency) ?></td>
								</tr>
								<tr>
									<td class="qty">&nbsp;</td>
									<td>##tax</td>
									<td class="price"><?php printf('%.2f %s', (double)$transaction->amount->details->tax, $transaction->amount->currency) ?></td>
								</tr>
								<tr>
									<td class="qty">&nbsp;</td>
									<td>##shipping_cost</td>
									<td class="price"><?php printf('%.2f %s', (double)$transaction->amount->details->shipping, $transaction->amount->currency) ?></td>
								</tr>
								<tr>
									<td class="qty">&nbsp;</td>
									<td>##handling_fee</td>
									<td class="price"><?php printf('%.2f %s', (double)$transaction->amount->details->handling_fee, $transaction->amount->currency) ?></td>
								</tr>
								<tr>
									<td class="qty">&nbsp;</td>
									<td>##shipping_discount</td>
									<td class="price"><?php printf('%.2f %s', (double)$transaction->amount->details->shipping_discount, $transaction->amount->currency) ?></td>
								</tr>
								<tr>
									<td class="qty">&nbsp;</td>
									<td>##total</td>
									<td class="price"><?php printf('%.2f %s', (double)$transaction->amount->total, $transaction->amount->currency) ?></td>
								</tr>
								<tr>
									<td colspan="2">
										##shipping_address:<br>
										<?php echo $encHelper->transcodeInbound($transaction->item_list->shipping_address->line1) ?><br>
										<?php echo $encHelper->transcodeInbound($transaction->item_list->shipping_address->country_code) ?>
										<?php echo $encHelper->transcodeInbound($transaction->item_list->shipping_address->postal_code) ?>
										<?php
											echo $encHelper->transcodeInbound($transaction->item_list->shipping_address->city);
											if(!empty($transaction->item_list->shipping_address->state)):
												echo ', '.$encHelper->transcodeInbound($transaction->item_list->shipping_address->state);
											endif;
										?>
										<br>
									</td>
									<td>&nbsp;</td>
								</tr>
							</tbody>
						</table>

						<?php foreach($transaction->related_resources as $resource): ?>
							<?php if(isset($resource->sale)): ?>
								<table class="">
									<thead>
										<tr><th colspan="2"
											data-gx-widget="collapser"
											data-collapser-parent_selector="thead"
											data-collapser-target_selector="tbody"
											data-collapser-section="paypal_sale_transaction"
											data-collapser-user_id="<?php echo $userId; ?>"
											data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
												'paypal_sale_transaction_collapse'); ?>"
											>##sale_transaction</th></tr>
									</thead>
									<tbody>
										<tr>
											<td class="label">##transaction_id</td>
											<td><?php echo $resource->sale->id ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_create_time</td>
											<td><?php echo $resource->sale->create_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->sale->create_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_update_time</td>
											<td><?php echo $resource->sale->update_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->sale->update_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_amount</td>
											<td><?php echo $resource->sale->amount->total .' '. $resource->sale->amount->currency ?></td>
										</tr>
										<tr>
											<td class="label">##payment_mode</td>
											<td><?php echo $resource->sale->payment_mode ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_state</td>
											<td class="transaction_state_<?php echo $resource->sale->state ?>"><?php echo $resource->sale->state ?></td>
										</tr>
										<tr>
											<td class="label">##protection_eligibility</td>
											<td><?php echo $resource->sale->protection_eligibility ?></td>
										</tr>
										<tr>
											<td class="label">##protection_eligibility_type</td>
											<td><?php echo $resource->sale->protection_eligibility_type ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_fee</td>
											<td><?php echo $resource->sale->transaction_fee->value .' '. $resource->sale->transaction_fee->currency ?></td>
										</tr>
										<?php foreach($resource->sale->links as $sale_link): ?>
											<?php if($sale_link->rel == 'refund'): ?>
												<tr>
													<td class="label">##refund</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="refund">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="sale_id" value="<?php echo $resource->sale->id ?>">
															<input type="text" name="amount" value="<?php echo $resource->sale->amount->total ?>">
															<?php echo $resource->sale->amount->currency ?>
															<input type="submit" value="##refund_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
										<?php endforeach ?>
									</tbody>
								</table>
								<div style="clear: left;"></div>
							<?php endif # isset $resource->sale ?>
							<?php if(isset($resource->authorization)): ?>
								<table class="">
									<thead>
										<tr><th colspan="2"
												data-gx-widget="collapser"
												data-collapser-parent_selector="thead"
												data-collapser-target_selector="tbody"
												data-collapser-section="paypal_authorization_transaction"
												data-collapser-user_id="<?php echo $userId; ?>"
												data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
													'paypal_authorization_transaction_collapse'); ?>"
												>##authorization_transaction</th></tr>
									</thead>
									<tbody>
										<tr>
											<td class="label">##transaction_id</td>
											<td><?php echo $resource->authorization->id ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_create_time</td>
											<td><?php echo $resource->authorization->create_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->authorization->create_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_update_time</td>
											<td><?php echo $resource->authorization->update_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->authorization->update_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_amount</td>
											<td><?php echo $resource->authorization->amount->total .' '. $resource->authorization->amount->currency ?></td>
										</tr>
										<?php if(!empty($resource->authorization->payment_mode)): ?>
											<tr>
												<td class="label">##payment_mode</td>
												<td><?php echo $resource->authorization->payment_mode ?></td>
											</tr>
										<?php endif ?>
										<tr>
											<td class="label">##transaction_state</td>
											<td><?php echo $resource->authorization->state ?></td>
										</tr>
										<?php if(!empty($resource->authorization->protection_eligibility)): ?>
											<tr>
												<td class="label">##protection_eligibility</td>
												<td><?php echo $resource->authorization->protection_eligibility ?></td>
											</tr>
										<?php endif ?>
										<?php if(!empty($resource->authorization->protection_eligibility_type)): ?>
											<tr>
												<td class="label">##protection_eligibility_type</td>
												<td><?php echo $resource->authorization->protection_eligibility_type ?></td>
											</tr>
										<?php endif ?>
										<tr>
											<td class="label">##valid_until</td>
											<td><?php echo $resource->authorization->valid_until . ' ('.date('Y-m-d H:i:s', strtotime($resource->authorization->valid_until)) . ')' ?></td>
										</tr>
										<?php foreach($resource->authorization->links as $auth_link): ?>
											<?php if($auth_link->rel == 'capture'): ?>
												<tr>
													<td class="label">##capture</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="authorization_capture">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="authorization_id" value="<?php echo $resource->authorization->id ?>">
															<input type="text" name="amount" value="<?php echo $resource->authorization->amount->total ?>">
															<?php echo $resource->authorization->amount->currency ?>
															<input type="submit" value="##capture_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
											<?php if($auth_link->rel == 'void'): ?>
												<tr>
													<td class="label">##void</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="authorization_void">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="authorization_id" value="<?php echo $resource->authorization->id ?>">
															<input type="submit" value="##void_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
											<?php if($auth_link->rel == 'reauthorize'): ?>
												<tr>
													<td class="label">##reauthorize</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="authorization_reauthorize">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="authorization_id" value="<?php echo $resource->authorization->id ?>">
															<input type="text" name="amount" value="<?php echo $resource->authorization->amount->total ?>">
															<?php echo $resource->authorization->amount->currency ?>
															<input type="submit" value="##reauthorize_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
										<?php endforeach ?>
									</tbody>
								</table>
								<!-- <div style="clear: left;"></div> -->
							<?php endif # isset $resource->authorization ?>

							<?php if(isset($resource->order)): ?>
								<table class="">
									<thead>
										<tr><th colspan="2"
												data-gx-widget="collapser"
												data-collapser-parent_selector="thead"
												data-collapser-target_selector="tbody"
												data-collapser-section="paypal_order_transaction"
												data-collapser-user_id="<?php echo $userId; ?>"
												data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
													'paypal_order_transaction_collapse'); ?>"
												>##order_transaction</th></tr>
									</thead>
									<tbody>
										<tr>
											<td class="label">##transaction_id</td>
											<td><?php echo $resource->order->id ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_create_time</td>
											<td><?php echo $resource->order->create_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->order->create_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_update_time</td>
											<td><?php echo $resource->order->update_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->order->update_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_amount</td>
											<td><?php echo $resource->order->amount->total .' '. $resource->order->amount->currency ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_state</td>
											<td><?php echo $resource->order->state ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_reason_code</td>
											<td><?php echo $resource->order->reason_code ?></td>
										</tr>
										<?php foreach($resource->order->links as $auth_link): ?>
											<?php if($auth_link->rel == 'capture'): ?>
												<tr>
													<td class="label">##capture</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="order_capture">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="order_id" value="<?php echo $resource->order->id ?>">
															<input type="text" name="amount" value="<?php echo $resource->order->amount->total ?>">
															<?php echo $resource->order->amount->currency ?>
															<input type="submit" value="##capture_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
											<?php if($auth_link->rel == 'void'): ?>
												<tr>
													<td class="label">##void</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="order_void">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="order_id" value="<?php echo $resource->order->id ?>">
															<input type="submit" value="##void_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
											<?php if($auth_link->rel == 'authorization'): ?>
												<tr>
													<td class="label">##authorization</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="order_authorize">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="order_id" value="<?php echo $resource->order->id ?>">
															<input type="text" name="amount" value="<?php echo $resource->order->amount->total ?>">
															<?php echo $resource->order->amount->currency ?>
															<input type="submit" value="##authorize_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
										<?php endforeach ?>
									</tbody>
								</table>
								<div style="clear: left;"></div>
							<?php endif # isset $resource->order ?>

							<?php if(isset($resource->capture)): ?>
								<table class="">
									<thead>
										<tr><th colspan="2"
												data-gx-widget="collapser"
												data-collapser-parent_selector="thead"
												data-collapser-target_selector="tbody"
												data-collapser-section="paypal_capture_transaction"
												data-collapser-user_id="<?php echo $userId; ?>"
												data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
													'paypal_capture_transaction_collapse'); ?>"
												>##capture_transaction</th></tr>
									</thead>
									<tbody>
										<tr>
											<td class="label">##transaction_id</td>
											<td><?php echo $resource->capture->id ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_create_time</td>
											<td><?php echo $resource->capture->create_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->capture->create_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_update_time</td>
											<td><?php echo $resource->capture->update_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->capture->update_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_amount</td>
											<td><?php echo $resource->capture->amount->total .' '. $resource->capture->amount->currency ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_state</td>
											<td><?php echo $resource->capture->state ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_fee</td>
											<td><?php echo $resource->capture->transaction_fee->value .' '. $resource->capture->transaction_fee->currency ?></td>
										</tr>
										<?php foreach($resource->capture->links as $capture_link): ?>
											<?php if($capture_link->rel == 'refund'): ?>
												<tr>
													<td class="label">##refund</td>
													<td>
														<form action="<?php echo xtc_href_link('orders.php', 'oID='.$_GET['oID'].'&action=edit') ?>" method="POST">
															<input type="hidden" name="page_token" value="<?php echo $pp3_page_token ?>">
															<input type="hidden" name="pp3_cmd" value="capture_refund">
															<input type="hidden" name="payment_id" value="<?php echo $payment->id ?>">
															<input type="hidden" name="capture_id" value="<?php echo $resource->capture->id ?>">
															<input type="text" name="amount" value="<?php echo $resource->capture->amount->total ?>">
															<?php echo $resource->capture->amount->currency ?>
															<input type="submit" value="##refund_now">
														</form>
													</td>
												</tr>
											<?php endif ?>
										<?php endforeach ?>
									</tbody>
								</table>
							<?php endif # isset $resource->capture ?>
							<?php if(isset($resource->refund)): ?>
								<table class="">
									<thead>
										<tr><th colspan="2"
											data-gx-widget="collapser"
											data-collapser-parent_selector="thead"
											data-collapser-target_selector="tbody"
											data-collapser-section="paypal_refund_transaction"
											data-collapser-user_id="<?php echo $userId; ?>"
											data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
												'paypal_refund_transaction_collapse'); ?>"
											>##refund_transaction</th></tr>
									</thead>
									<tbody>
										<tr>
											<td class="label">##transaction_id</td>
											<td><?php echo $resource->refund->id ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_create_time</td>
											<td><?php echo $resource->refund->create_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->refund->create_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_update_time</td>
											<td><?php echo $resource->refund->update_time . ' ('.date('Y-m-d H:i:s', strtotime($resource->refund->update_time)) . ')' ?></td>
										</tr>
										<tr>
											<td class="label">##transaction_amount</td>
											<td><?php echo $resource->refund->amount->total .' '. $resource->refund->amount->currency ?></td>
										</tr>
										<?php if(isset($resource->refund->sale_id)): ?>
											<tr>
												<td class="label">##sale_id</td>
												<td><?php echo $resource->refund->sale_id ?></td>
											</tr>
										<?php endif; ?>
										<?php if(isset($resource->refund->capture_id)): ?>
											<tr>
												<td class="label">##capture_id</td>
												<td><?php echo $resource->refund->capture_id ?></td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							<?php endif # isset $resource->refund ?>
						<?php endforeach ?>

					</div><!-- transactions container -->
				<?php endforeach ?>
			<?php endif; // $payment instanceof PayPalErrorPayment ?>

		<?php endforeach ?>
	<?php endif?>

	<div>
		<span class="paypal-header">Debug</span>
		<table class="paypal-debug-data">
			<thead>
			<tr><th data-gx-widget="collapser"
					data-collapser-parent_selector="thead"
					data-collapser-target_selector="tbody"
					data-collapser-section="paypal_debug_data"
					data-collapser-user_id="<?php echo $userId; ?>"
					data-collapser-collapsed="<?php echo $userConfigurationService->getUserConfiguration($userId,
						'paypal_debug_data_collapse'); ?>"
					>Debug Data</th></tr>
			</thead>
			<tbody>
			<tr>
				<td><pre><?php echo $debug_block ?></pre></td>
			</tr>
			</tbody>
		</table>
	</div>

</div>
