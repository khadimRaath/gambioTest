<?php
/* --------------------------------------------------------------
   order_details.php 2016-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/inc/gm_xtc_href_link.inc.php');
include_once(DIR_FS_INC.'get_payment_title.inc.php');

$modules_js = array();
if(gm_get_conf('MODULE_CENTER_SHIPCLOUD_INSTALLED') == true)
{
	$modules_js[] = 'orders/orders_shipcloud';
	$shipcloudText = MainFactory::create('ShipcloudText');
}
if(gm_get_conf('MODULE_CENTER_INTERNETMARKE_INSTALLED') == true)
{
	$modules_js[] = 'orders/orders_internetmarke';
	$internetMarkeText = MainFactory::create('InternetMarkeText');
}

/**
 * Sets language id for the current order.
 */
$languageId       = $_SESSION['languages_id'];
$languageSql      = 'SELECT
																	l.languages_id
																FROM
																	orders o,
																	languages l
																WHERE
																	o.orders_id = "' . (int)$_GET['oID'] . '" AND
																	o.language = l.directory
																LIMIT 1';
$languageIdResult = xtc_db_query($languageSql);
if((int)xtc_db_num_rows($languageIdResult) === 1)
{
	$languageIdResultArray = xtc_db_fetch_array($languageIdResult);
	$languageId            = $languageIdResultArray['languages_id'];
}

/**
 * Sets data for bank transfer of order.
 */
$bankTransferQuery =
	xtc_db_query('select banktransfer_prz, banktransfer_status, banktransfer_owner, banktransfer_number, banktransfer_bankname, banktransfer_blz, banktransfer_fax from banktransfer where orders_id = "'
	             . xtc_db_input($_GET['oID'])
	             . '"');
$bankTransfer      = xtc_db_fetch_array($bankTransferQuery);

$orderExtender = MainFactory::create('OrderExtenderComponent');
$orderExtender->set_data('GET', $_GET);
$orderExtender->set_data('POST', $_POST);
$orderExtender->proceed();
$orderExtender->postProceed();
?>

<div class="gx-container order-details breakpoint-large" data-gx-compatibility="orders/order_details_controller <?php echo implode(' ', $modules_js) ?>">
	<!--
		ORDER DETAILS HEAD
	-->
	<div class="head grid">
		<div class="span2-odd head-item">
			<div class="simple-container">
				<label class="title"> <?php echo ORDER_HEADING_TITLE; ?> </label>
			</div>
			<div class="simple-container">
				<i class="fa fa-shopping-cart"></i> <label class="value"> <?php echo $GLOBALS['oID'] ?> </label>
			</div>
		</div>

		<div class="span2-odd head-item">
			<div class="simple-container">
				<label class="title"> <?php echo TEXT_AMOUNT; ?> </label>
			</div>
			<div class="simple-container">
				<i class="fa fa-eur"></i>
				<label class="value"> <?php echo $orderSumText ?> </label>
			</div>
		</div>

		<div class="span2-odd head-item">
			<div class="simple-container">
				<label class="title"> <?php echo TEXT_DATE; ?> </label>
			</div>
			<div class="simple-container">
				<label class="value"> <?php echo date('d.m.Y H:i',
				                                      strtotime($GLOBALS['order']->info['date_purchased'])) ?> </label>
				<i class="fa fa-eur hidden-visibility"></i>
			</div>
		</div>

		<div class="span2-odd head-item">
			<div class="simple-container">
				<label class="title"> <?php echo str_replace(':', '', ENTRY_PAYMENT_METHOD); ?> </label>
			</div>
			<div class="simple-container">
				<label class="value" title="<?php if(!empty($GLOBALS['order']->info['payment_method'])){ echo $GLOBALS['order']->info['payment_method']; } ?>"> <?php if(!empty($GLOBALS['order']->info['payment_method'])){ echo get_payment_title($GLOBALS['order']->info['payment_method']); } ?> </label>
				<i class="fa fa-eur hidden-visibility"></i>
			</div>
		</div>

		<div class="span2-odd head-item add-order-status cursor-pointer" title="<?php echo HEADING_GM_STATUS; ?>">
			<div class="simple-container cursor-pointer">
				<label class="title cursor-pointer"> <?php echo str_replace(':', '', ENTRY_STATUS); ?> </label>
			</div>
			<div class="simple-container cursor-pointer">
				<label class="<?php echo getBadgeClass($GLOBALS['order']->info['orders_status']); ?> cursor-pointer">
					<?php echo $GLOBALS['orderStatusValuesArray'][(int)$GLOBALS['order']->info['orders_status']]; ?>
				</label>
				<i class="fa fa-eur hidden-visibility"></i>
			</div>
		</div>
	</div>


	<!--
		ORDER DETAILS BODY
	-->

	<div class="content article-table grid">
		<div class="span12 remove-padding">
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title"><?php echo TABLE_HEADING_PRODUCTS; ?></label>

					<label class="pull-right head-link default">
						<a href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . (int)$GLOBALS['oID']) ?>">
							<?php echo BUTTON_EDIT ?>
						</a>
					</label>
				</div>
				<div class="frame-content">
					<table id="order-details-table">
						<thead>
							<tr>
								<th>
									<div class="grid">
										<div class="span12">
											<?php echo TABLE_HEADING_QUANTITY; ?>
										</div>
									</div>
								</th>
								<th>
									<div class="grid">
										<div class="span12">
											<?php echo TABLE_HEADING_PRODUCTS; ?>
										</div>
									</div>
								</th>
								<th class="text-right">
									<div class="grid">
										<div class="span12">
											<?php echo TABLE_HEADING_PRODUCTS_MODEL; ?>
										</div>
									</div>
								</th>
								<th class="text-right">
									<div class="grid">
										<div class="span12">
											<?php echo TABLE_HEADING_NET; ?>
										</div>
									</div>
								</th>

								<?php if($GLOBALS['order']->products[0]['allow_tax']) { ?>
								<th class="text-right">
									<div class="grid">
										<div class="span12">
											<?php echo ENTRY_TAX; ?>
										</div>
									</div>
								</th>
								<th class="text-right">
									<div class="grid">
										<div class="span12">
											<?php echo TABLE_HEADING_GROSS; ?>
										</div>
									</div>
								</th>
								<?php } ?>

								<th class="text-right">
									<div class="grid">
										<div class="span12">
											<?php echo TEXT_TOTAL; ?>
										</div>
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($GLOBALS['order']->products as $productInformation): ?>
								<tr>
									<td>
										<div class="grid">
											<div class="span12">
												<?php
													// Check if value in the database is an integer.
													$isPriceInteger = (int)$productInformation['qty']
													                  == $productInformation['qty'];

													// Output number as integer or float value
													// depending on type of the value saved in the database.
													if($isPriceInteger)
													{
														// Output number as integer.
														echo number_format($productInformation['qty']) . ' '
														     . $productInformation['unit_name'];
													}
													else
													{
														// Output number with two decimal value.
														echo number_format($productInformation['qty'], 2) . ' '
														     . $productInformation['unit_name'];
													}
												?>
											</div>
										</div>
									</td>
									<td>
										<div class="grid">
											<div class="span12">
												<?php
													echo '<div class="products-name">' . $productInformation['name'] . '</div>';

													//ATTRIBUTES AND GX-CUSTOMIZER
													if (sizeof($productInformation['attributes']) > 0)
													{
														echo '<div class="attributes-container">';
														for ($j = 0, $k = sizeof($productInformation['attributes']); $j < $k; $j ++)
														{
															if(!empty($productInformation['attributes'][$j]['option']) || !empty($productInformation['attributes'][$j]['value']))
															{
																echo '- ' . $productInformation['attributes'][$j]['option'].': '.$productInformation['attributes'][$j]['value'].'<br/>';
															}
														}

														include(DIR_FS_CATALOG . 'gm/modules/gm_gprint_admin_orders.php');
														echo '</div>';
													}

													//PROPERTIES
													if (count($productInformation['properties']) > 0)
													{
														echo '<div class="properties-container">';
														for ($j = 0, $k = sizeof($productInformation['properties']); $j < $k; $j ++)
														{
															if(!empty($productInformation['properties'][$j]['properties_name']) || !empty($productInformation['properties'][$j]['values_name']))
															{
																echo '- '. $productInformation['properties'][$j]['properties_name'].': '.$productInformation['properties'][$j]['values_name'].'<br/>';
															}
														}
														echo '</div>';
													}
												?>
											</div>
										</div>
									</td>
									<td class="text-right">
										<div class="grid">
											<div class="span12">
												<?php
												$modelArray =
													($productInformation['model']
													 !== '') ? array($productInformation['model']) : array('');
												if(null !== $productInformation['attributes'])
												{
													foreach($productInformation['attributes'] as $productAttribute)
													{
														$model        =
															xtc_get_attributes_model($productInformation['id'],
															                         $productAttribute['value'],
															                         $productAttribute['option'],
															                         $languageId);
														$modelArray[] = $model;
													}
												}
												echo implode('<br/>', $modelArray);
												?>
											</div>
										</div>
									</td>
									<td class="text-right">
										<div class="grid">
											<div class="span12">
												<?php
												$net = $productInformation['price'];

												if($productInformation['allow_tax'])
												{
													$divideValue = 100 + (int)$productInformation['tax'];
													$net         = ($productInformation['price'] / $divideValue) * 100;
												}

												echo number_format($net, 2) . ' ' . $GLOBALS['order']->info['currency'];
												?>
											</div>
										</div>
									</td>

									<?php if($productInformation['allow_tax']) { ?>
									<td class="text-right">
										<div class="grid">
											<div class="span12">
												<?php
													echo number_format($productInformation['tax']) . '%';
												?>
											</div>
										</div>
									</td>
										<td class="text-right">
											<div class="grid">
												<div class="span12">
													<?php echo number_format($productInformation['price'], 2) . ' '
													           . $GLOBALS['order']->info['currency']; ?>
												</div>
											</div>
										</td>
									<?php } ?>

									<td class="text-right">
										<div class="grid">
											<div class="span12">
												<?php echo number_format($productInformation['final_price'], 2) . ' '
												           . $GLOBALS['order']->info['currency']; ?>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
							<tr id="order-sum-row">
								<td colspan="<?php if($productInformation['allow_tax']) { ?>6<?php } else { ?>4<?php } ?>" class="text-right">
									<div class="grid">
										<?php
										$counter          = 0;
										$countOrderTotals = count($GLOBALS['order']->totals);
										?>
										<?php foreach($GLOBALS['order']->totals as $orderInfoArray): ?>
											<?php if($counter <= ($countOrderTotals - 2)): ?>
												<div class="span12">
													&nbsp;<?php echo strip_tags($orderInfoArray['title']) ?>
												</div>
												<?php $counter++; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								</td>
								<td class="text-right">
									<div class="grid">
										<?php
										$counter = 0;
										?>
										<?php foreach($GLOBALS['order']->totals as $orderInfoArray): ?>
											<?php if($counter <= ($countOrderTotals - 2)): ?>
												<div class="span12">
													&nbsp;<?php echo $orderInfoArray['text'] ?>
												</div>
												<?php $counter++; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="<?php if($productInformation['allow_tax']) { ?>6<?php } else { ?>4<?php } ?>" class="text-right total">
									<label><?php echo $GLOBALS['order']->totals[count($GLOBALS['order']->totals)
									                                            - 1]['title']; ?></label>
								</td>
								<td class="text-right total">
									<label><?php echo $GLOBALS['order']->totals[count($GLOBALS['order']->totals)
									                                            - 1]['text']; ?></label>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<?php
	$belowProductData = $orderExtender->get_output('below_product_data');

	if(!empty($belowProductData))
	{
	?>
		<div class="content grid">
			<div class="span12 remove-padding">
		<?php
		foreach($belowProductData as $moduleContent)
		{
		?>
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title">
						<?php
						echo $moduleContent['head'];
						?>
					</label>
				</div>
				<div class="frame-content">
					<?php
					echo $moduleContent['content'];
					?>
				</div>
			</div>
		<?php
		}
		?>
			</div>
		</div>
	<?php
	}
	?>


	<!--
		OLD PayPalNG ORDER DATA
	-->
	<?php
	if(strpos($order->info['payment_method'], 'paypalng') !== false
		|| (class_exists('GMPayPal') && GMPayPal::orderHasTransactions((int)$_GET['oID']))):
	?>
	<div class="content article-table grid">
		<div class="span12 remove-padding">
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title">PayPal</label>
				</div>
				<div class="frame-content" data-gx-compatibility="orders/order_paypalng"></div>
			</div>
		</div>
	</div>
	<?php endif ?>



	<!--
		AMAZON PAYMENT
	-->
	<div class="content article-table grid hidden">
		<div class="span12 remove-padding">
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title">
						<?php
						echo $GLOBALS['coo_lang_file_master']->get_text('amazonadvpay_info', 'amazonadvancedpayment');;
						?>
					</label>
				</div>
				<div class="frame-content" data-gx-compatibility="orders/order_amazon"></div>
			</div>
		</div>
	</div>

	<!--
		Billsafe
	-->
	<div class="content article-table grid hidden">
		<div class="span12 remove-padding">
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title">
						BillSAFE
					</label>
				</div>
				<div class="frame-content" data-gx-compatibility="orders/order_billsafe"></div>
			</div>
		</div>
	</div>

	<!--
		ipayment
	-->
	<div class="content article-table grid hidden">
		<div class="span12 remove-padding">
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title">
						<?php echo $GLOBALS['coo_lang_file_master']->get_text('IPAY_HEADING','orders_ipayment'); ?>
					</label>
				</div>
				<div class="frame-content" data-gx-compatibility="orders/order_ipayment"></div>
			</div>
		</div>
	</div>

	<div class="content grid">
		<div class="span6">
			<!--
				ORDER INFO BOX
			-->
			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title"><?php echo ENTRY_ORDER_INFORMATION; ?></label>

					<label class="pull-right head-link default">
						<a href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=address&oID=' . (int)$GLOBALS['oID']) ?>">
							<?php echo BUTTON_EDIT ?>
						</a>
					</label>
				</div>
				<div class="frame-content simple-container">
					<div class="span6">
						<div class="title">
							<label><?php echo str_replace(':', '', ENTRY_SOLD_TO); ?></label>
						</div>
						<div class="content">
							<span><?php echo $GLOBALS['order']->billing['name'] ?></span><br />
							<?php if($GLOBALS['order']->billing['company'] !== ''): ?>
								<span><?php echo $GLOBALS['order']->billing['company']; ?></span><br />
							<?php endif; ?>
							<span><?php if($GLOBALS['order']->billing['house_number'] !== '')
										{
											echo $GLOBALS['order']->billing['street_address'] . ' '
									        . $GLOBALS['order']->billing['house_number'];
										}
										else
										{
											echo $GLOBALS['order']->billing['street_address'];
										} ?>
							</span><br />
							<?php if($GLOBALS['order']->billing['additional_address_info'] !== ''): ?>
								<span><?php echo str_replace("\r\n", '<br>', $GLOBALS['order']->billing['additional_address_info']); ?></span><br />
							<?php endif; ?>
							<span><?php echo $GLOBALS['order']->billing['postcode'] . ' '
							                 . $GLOBALS['order']->billing['city'] ?></span><br />
							<span><?php echo $GLOBALS['order']->billing['country'] ?></span>
						</div>
					</div>
					<div class="span6">
						<div class="title">
							<label><?php echo str_replace(':', '', ENTRY_SHIP_TO); ?></label>
						</div>
						<div class="content">
							<span><?php echo $GLOBALS['order']->delivery['name'] ?></span><br />
							<?php if($GLOBALS['order']->delivery['company'] !== ''): ?>
								<span><?php echo $GLOBALS['order']->delivery['company']; ?></span><br />
							<?php endif; ?>
							<span>
								<?php if($GLOBALS['order']->delivery['house_number'] !== '')
								{
									echo $GLOBALS['order']->delivery['street_address'] . ' '
									     . $GLOBALS['order']->delivery['house_number'];
								}
								else
								{
									echo $GLOBALS['order']->delivery['street_address'];
								} ?>
							</span><br />
							<?php if($GLOBALS['order']->delivery['additional_address_info'] !== ''): ?>
								<span><?php echo str_replace("\r\n", '<br>', $GLOBALS['order']->delivery['additional_address_info']); ?></span><br />
							<?php endif; ?>
							<span><?php echo $GLOBALS['order']->delivery['postcode'] . ' '
							                 . $GLOBALS['order']->delivery['city'] ?></span><br />
							<span><?php echo $GLOBALS['order']->delivery['country'] ?></span>
						</div>
					</div>
					<div class="span6">
						<div class="title">
							<label><?php echo str_replace(':', '', ENTRY_EMAIL_ADDRESS) ?></label>
						</div>
						<div class="content">
							<a href="admin.php?do=Emails&mailto=<?php echo $GLOBALS['order']->customer['email_address']; ?>">
								<?php echo $GLOBALS['order']->customer['email_address'] ?>
							</a>
						</div>
					</div>
				</div>
			</div>

			<?php
			$belowOrderInfo = $orderExtender->get_output('below_order_info');

			foreach($belowOrderInfo as $moduleContent)
			{
				?>
				<div class="frame-wrapper">
					<div class="frame-head">
						<label class="title">
							<?php
							echo $moduleContent['head'];
							?>
						</label>
					</div>
					<div class="frame-content">
						<?php
						echo $moduleContent['content'];
						?>
					</div>
				</div>
				<?php
			}
			?>

			<!--
				PAYPAL
			-->
			<div class="frame-wrapper hidden">
				<div class="frame-head">
					<label class="title">
						<?php
						echo $GLOBALS['coo_lang_file_master']->get_text('orders_paypal3_heading', 'paypal3');
						?>
					</label>
				</div>

				<div class="frame-content" data-gx-compatibility="orders/order_paypal3">

				</div>
			</div>

			<!--
				Payone
			-->
			<div class="frame-wrapper hidden">
				<div class="frame-head">
					<label class="title">
						<?php
						echo $GLOBALS['coo_lang_file_master']->get_text('payone_orders_heading', 'payone');
						?>
					</label>
				</div>

				<div class="frame-content" data-gx-compatibility="orders/order_payone">

				</div>
			</div>
			<!--
				Klarna
			-->
			<div class="frame-wrapper hidden">
				<div class="frame-head">
					<label class="title">
						<?php echo $GLOBALS['coo_lang_file_master']->get_text('orders_block_heading','klarna'); ?>
					</label>
				</div>

				<div class="frame-content" data-gx-compatibility="orders/order_klarna">

				</div>
			</div>
			<!--
				PARCEL TRACKING MODULE
			-->
			<?php
			$trackingCodesContentView = MainFactory::create('TrackingCodesContentView');
			$trackingCodesContentView->setOrderId($GLOBALS['oID']);
			$trackingCodesContentView->setPageToken($t_page_token);
			?>
			<div id="tracking_code_wrapper" class="frame-wrapper">
				<div class="frame-head">
					<label class="title pull-left">
						<?php echo sprintf(TXT_PARCEL_TRACKING_HEADING, $GLOBALS['oID']); ?>
					</label>
				</div>
				<div class="frame-content gx-container table-content"
				     data-gx-compatibility="orders/orders_modal_layer"
				     data-orders_modal_layer-container="tracking_code_wrapper"
				     data-gx-controller="orders/orders_parcel_tracking"
				     data-orders_parcel_tracking-container="tracking_code_wrapper">
					<?php
					echo $trackingCodesContentView->get_html();
					?>
				</div>
			</div>

			<!--
				ORDER STATUS HISTORY
			-->
			<div class="frame-wrapper <?php if(count($ordersStatusDataArray)
			                                                     > 0
			): ?>warning<?php endif; ?>">
				<div class="frame-head <?php if(count($ordersStatusDataArray) > 0): ?>warning<?php endif; ?>">
					<label class="title pull-left">
						<?php echo TEXT_ORDER_STATUS_HISTORY; ?>
					</label>
                    <label class="pull-right head-link"> <a class="add-order-status" href="#" title="<?php echo HEADING_GM_STATUS; ?>">
                            <?php echo HEADING_GM_STATUS ?>
                        </a> </label>
				</div>
				<?php if(count($ordersStatusDataArray) > 0): ?>
					<div class="frame-content table-content">
						<table>
							<thead>
								<tr>
									<th><?php echo TEXT_DATE; ?></th>
									<th><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></th>
									<th><?php echo TABLE_HEADING_STATUS; ?></th>
									<th><?php echo TABLE_HEADING_COMMENTS; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($ordersStatusDataArray as $orderStatusRow): ?>
									<tr>
										<td>
											<?php echo xtc_datetime_short($orderStatusRow['date_added']); ?>
										</td>
										<td class="text-center">
											<?php echo ($orderStatusRow['customer_notified']
											            == '1')
															? '<i class="fa fa-check fa-lg"></i>'
															: '<i class="fa fa-times fa-lg"></i>'; ?>
										</td>
										<td>
											<?php echo ($orderStatusRow['orders_status_id']
											            != null) ? $orders_status_array[$orderStatusRow['orders_status_id']] : TEXT_VALIDATING; ?>
										</td>
										<td>
											<?php echo nl2br(xtc_db_output($orderStatusRow['comments'])); ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$belowWithdrawal = $orderExtender->get_output('below_history');

			foreach($belowWithdrawal as $moduleContent)
			{
				?>
				<div class="frame-wrapper">
					<div class="frame-head">
						<label class="title">
							<?php
							echo $moduleContent['head'];
							?>
						</label>
					</div>
					<div class="frame-content">
						<?php
						echo $moduleContent['content'];
						?>
					</div>
				</div>
				<?php
			}
			?>

			<!--
	CC
-->
			<div class="content article-table grid hidden">
				<div class="span12 remove-padding">
					<div class="frame-wrapper">
						<div class="frame-head">
							<label class="title">
								CC
							</label>
						</div>
						<div class="frame-content" data-gx-compatibility="orders/order_cc"></div>
					</div>
				</div>
			</div>

			<?php if((($bankTransfer['banktransfer_bankname'])
			          || ($bankTransfer['banktransfer_blz'])
			          || ($bankTransfer['banktransfer_number']))
			): ?>

				<!--
					BANK TRANSFER
				-->
				<div class="frame-wrapper banktransfer">
					<div class="frame-head">
						<label class="title">
							<?php echo TITLE_BANK_INFO; ?>
						</label>
					</div>

					<div class="frame-content">
						<table>
							<tbody>
								<tr>
									<td><?php echo TEXT_BANK_NAME; ?></td>
									<td><?php echo $bankTransfer['banktransfer_bankname']; ?></td>
								</tr>
								<tr>
									<td><?php echo TEXT_BANK_BLZ; ?></td>
									<td><?php echo $bankTransfer['banktransfer_blz']; ?></td>
								</tr>
								<tr>
									<td><?php echo TEXT_BANK_NUMBER; ?></td>
									<td><?php echo $bankTransfer['banktransfer_number']; ?></td>
								</tr>
								<tr>
									<td><?php echo TEXT_BANK_OWNER; ?></td>
									<td><?php echo $bankTransfer['banktransfer_owner']; ?></td>
								</tr>
								<tr>
									<td><?php echo TEXT_BANK_STATUS; ?></td>
									<td><?php echo ((int)$bankTransfer['banktransfer_status']) ? $bankTransfer['banktransfer_status'] : 'OK'; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!--Right column-->
		<div class="span6 remove-padding">
			<!--
				CUSTOMER INFO BOX
			-->

			<?php

			$mainCustomerIdEmpty = false;
			$customerId = $GLOBALS['order']->customer['ID'];

			if(empty($GLOBALS['order']->customer['ID']))
			{
				$customerId = $GLOBALS['order']->customer['csID'];
				$mainCustomerIdEmpty = true;
			}

			?>
			<div class="frame-wrapper info customers">
				<div class="frame-head info">
					<label class="title"><?php echo str_replace(':', '', ENTRY_CUSTOMER); ?></label>
						<?php
							if(!$mainCustomerIdEmpty)
							{
								echo '<label class="pull-right head-link">
										<a href="' . xtc_href_link('customers.php', 'cID=' . $customerId . '&action=edit') . '">';
								echo ENTRY_OPEN_CUSTOMER;
							}
						?>
						</a> </label>
				</div>
				<div class="frame-content simple-container">
					<div class="grid">
						<div class="span6">
							<label><?php echo TEXT_ADDRESS ?></label>
						</div>
						<div class="span6">
							<span><?php echo $GLOBALS['order']->customer['name']; ?></span><br />
							<?php if($GLOBALS['order']->customer['company'] !== ''): ?>
								<span><?php echo $GLOBALS['order']->customer['company']; ?></span><br />
							<?php endif; ?>
							<span>
								<?php if($GLOBALS['order']->customer['house_number'] !== '')
								{
									echo $GLOBALS['order']->customer['street_address'] . ' '
									     . $GLOBALS['order']->customer['house_number'];
								}
								else
								{
									echo $GLOBALS['order']->customer['street_address'];
								} ?>
							</span><br />
							<?php if($GLOBALS['order']->customer['additional_address_info'] !== ''): ?>
								<span><?php echo str_replace("\r\n", '<br>', $GLOBALS['order']->customer['additional_address_info']); ?></span><br />
							<?php endif; ?>
							<span><?php echo $GLOBALS['order']->customer['postcode'] . ' '
							                 . $GLOBALS['order']->customer['city']; ?></span><br />
							<span><?php echo $GLOBALS['order']->customer['country']; ?></span>
						</div>
					</div>
					<div class="grid">
						<div class="span6">
							<label><?php echo str_replace(':', '', ENTRY_EMAIL_ADDRESS); ?></label><br />
							<?php if($GLOBALS['order']->customer['vat_id'] !== ''): ?>
								<label><?php echo str_replace(':', '', ENTRY_CUSTOMERS_VAT_ID); ?></label><br />
							<?php endif; ?>
							<label><?php echo str_replace(':', '', ENTRY_TELEPHONE); ?></label><br />
							<label><?php echo str_replace(':', '', ENTRY_CUSTOMERS_GROUP); ?></label><br />
							<label><?php echo str_replace(':', '', TITLE_CUSTOMER_ID); ?></label><br />
							<label><?php echo str_replace(':', '', ENTRY_LANGUAGE); ?></label><br />
							<?php if($GLOBALS['order']->customer['cIP'] !== ''): ?>
								<label><?php echo str_replace(':', '', IP); ?></label>
							<?php endif; ?>
						</div>
						<div class="span6">
							<span>
								<a href="admin.php?do=Emails&mailto=<?php echo $GLOBALS['order']->customer['email_address']; ?>">
									<?php echo $GLOBALS['order']->customer['email_address']; ?>
								</a>
							</span><br />
							<?php if($GLOBALS['order']->customer['vat_id'] !== ''): ?>
								<span><?php echo $GLOBALS['order']->customer['vat_id']; ?></span><br />
							<?php endif; ?>
							<span><?php echo $GLOBALS['order']->customer['telephone']; ?></span><br />
							<span><?php echo $GLOBALS['order']->info['status_name']; ?></span><br />
							<span><?php	echo $customerId; ?></span><br />
							<span><?php echo $GLOBALS['order']->info['language']; ?></span><br />
							<?php if($GLOBALS['order']->customer['cIP'] !== ''): ?> 
								<span><?php echo $GLOBALS['order']->customer['cIP']; ?></span>
							<?php endif; ?>
						</div>
					</div>

					<div class="grid">
						<div class="span6">
							<label><?php echo ENTRY_FIRST_ORDER; ?></label><br />
							<label><?php echo ENTRY_LAST_ORDER; ?></label><br />
							<label><?php echo ENTRY_ORDER_TOTAL; ?></label><br />
							<label><?php echo ENTRY_ORDER_SUM_TOTAL; ?></label>
						</div>
						<div class="span6">
							<span><?php echo $firstOrderDate; ?></span><br /> <span><?php echo $lastOrderDate; ?></span><br />
							<span class="amount">
								<?php
									if(!$mainCustomerIdEmpty)
									{
										$parameters = [
											'do' => 'OrdersOverview',
										    'filter' => [
										    	'customer' => '#' . $customerId
										    ]
										];
										echo '<a href="admin.php?' . http_build_query($parameters) . '">' . $amountOfOrders . '</a>';
									}
									else
									{
										echo $amountOfOrders;
									}
								?>
							</span><br />
							<span><?php echo $sumOrderTotal . ' ' . $GLOBALS['order']->info['currency']; ?></span>
						</div>
					</div>
				</div>

                <?php
                $query = 'SELECT
                                    m.`memo_title`,
                                    m.`memo_text`,
                                    m.`memo_date`,
                                    c.`customers_firstname`,
                                    c.`customers_lastname`
                                FROM
                                  `customers_memo` m
                                LEFT JOIN `customers` c ON (c.`customers_id` = m.`poster_id`)
                                WHERE m.`customers_id` = ' . (int)$GLOBALS['order']->customer['ID'] . '
                                ORDER BY m.`memo_date` DESC';
                $memoResult = xtc_db_query($query);
                $i = 0;
                while($row = xtc_db_fetch_array($memoResult)):
                if($i === 0): ?>
                <div class="frame-content table-content customer-memos">
                    <table>
                        <thead>
                        <tr>
                            <th colspan="3">
                                <?php echo $GLOBALS['coo_lang_file_master']->get_text('ENTRY_COMMENTS', 'admin_customers') ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php endif; ?>
                        <tr>
                            <td valign="top"><?php echo htmlspecialchars_wrapper($row['memo_title']) ?></td>
                            <td valign="top"><?php echo nl2br(htmlspecialchars_wrapper($row['memo_text'])) ?></td>
                            <td valign="top">
                                <div class="block pull-right">
                                    <span class="date"><?php echo date('d. F Y', strtotime($row['memo_date'])) ?></span>
                                                <span class="poster">
                                                    <?php
                                                    echo htmlspecialchars_wrapper($row['customers_firstname'] . ' '
                                                        . $row['customers_lastname']);

                                                    $i++;
                                                    ?>
                                                </span>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($i !== 0): ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

			</div>

			<!--
				WITHDRAWALS
			-->
			<div class="frame-wrapper withdrawals  <?php if(count($withdrawalsDataArray) > 0): ?>danger<?php endif; ?>">
				<div class="frame-head <?php if(count($withdrawalsDataArray) > 0): ?>danger<?php endif; ?>">
					<label class="title pull-left">
						<?php
						echo TEXT_WITHDRAWAL;
						if(count($withdrawalsDataArray) === 0)
						{
							echo '&nbsp;(' . TEXT_NO_WITHDRAWALS_LIST . ')';
						}
						?>
					</label> <label class="pull-right head-link"> <a href="<?php echo gm_xtc_href_link('withdrawal.php', 'order_id='
					                                                                                     . $GLOBALS['oID']); ?>"
					                                 target="_blank">
							<?php echo TEXT_ADD_WITHDRAWAL ?>
						</a> </label>
				</div>
				<?php if(count($withdrawalsDataArray) > 0): ?>
					<div class="frame-content table-content">
						<table>
							<thead>
								<tr>
									<th><?php echo substr(TEXT_WITHDRAWAL, 0, strlen(TEXT_WITHDRAWAL) - 1) ?></th>
									<th><?php echo TEXT_DATE ?></th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($withdrawalsDataArray as $withdrawalsData): ?>
									<tr>
										<td><a href="<?php echo xtc_href_link('withdrawals.php',
										                                      'id=' . $withdrawalsData['withdrawal_id']
										                                      . '&action=edit') ?>"><?php echo $withdrawalsData['withdrawal_id']; ?></a>
										</td>
										<td><?php echo date('d.m.Y',
										                    strtotime($withdrawalsData['date_created'])); ?></td>
										<td></td>
										<td class="pull-right">
											<button class="btn"
											        onclick="window.location.href = '<?php echo xtc_href_link('withdrawals.php',
											                                                                  'id='
											                                                                  . $withdrawalsData['withdrawal_id']
											                                                                  . '&action=edit') ?>'">
												<i class="fa fa-search"></i>&nbsp;<?php echo TEXT_SHOW; ?>
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$belowWithdrawal = $orderExtender->get_output('below_withdrawal');

			foreach($belowWithdrawal as $moduleContent)
			{
			?>
				<div class="frame-wrapper">
					<div class="frame-head">
						<label class="title">
							<?php
							echo $moduleContent['head'];
							?>
						</label>
					</div>
					<div class="frame-content">
						<?php
						echo $moduleContent['content'];
						?>
					</div>
				</div>
			<?php
			}
			?>

			<!--
				INVOICES
			-->
			<div class="frame-wrapper invoices">
				<div class="frame-head">
					<label class="title">
						<?php echo INVOICE_CREATED; ?>
					</label>

					<?php if(gm_pdf_is_installed()): ?>
					<label class="pull-right head-link default">
						<a onclick="window.open('<?php echo xtc_href_link('gm_pdf_order.php', 'oID=' . (int)$GLOBALS['oID']. '&type=invoice'); ?>', '_blank')">
							<?php echo TITLE_INVOICE ?>
						</a>
					</label>
					<?php endif ?>
				</div>

				<div class="frame-content">
					<table>
						<tbody>
							<tr>
								<td class="invoice-content" colspan="2" data-gx-compatibility="orders/order_invoice">

								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!--
				PACKINGSLIPS
			-->
			<div class="frame-wrapper packingslips">
				<div class="frame-head">
					<label class="title">
						<?php echo PACKINGSLIP_CREATED; ?>
					</label>

					<?php if(gm_pdf_is_installed()): ?>
					<label class="pull-right head-link default">
						<a onclick="window.open('<?php echo xtc_href_link('gm_pdf_order.php', 'oID=' . (int)$GLOBALS['oID'] . '&type=packingslip'); ?>', '_blank')">
							<?php echo TITLE_PACKINGSLIP ?>
						</a>
					</label>
					<?php endif ?>
				</div>

				<div class="frame-content">
					<table>
						<tbody>
							<tr>
								<td class="packingslip-content" colspan="2" data-gx-compatibility="orders/order_packingslip">

								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!--
		ORDER DETAILS BOTTOM
	-->
	<div class="order-footer">
		<div class="grid">
			<div class="span12 remove-padding">
				<hr>
			</div>
		</div>
		<div class="grid">
			<div class="span12 text-right remove-padding">
				<?php
					$url = DIR_WS_ADMIN . 'admin.php?' . ($_GET['overview'] ? http_build_query($_GET['overview']) : 'do=OrdersOverview');
				?>
				<button type="button" class="btn"
				        onclick="window.location.href = '<?php echo $url; ?>'">
					<span><?php echo BUTTON_BACK; ?></span>
				</button>

				<div data-use-button_dropdown="true">
					<button class="update-order-status" data-gx-compatibility="orders/orders_modal_layer"
					        data-orders_modal_layer-action="update_orders_status"
					        data-orders_modal_layer-detail_page="true"
					        data-orders_modal_layer-order_id="<?php echo (int)$GLOBALS['oID']; ?>">
						<?php echo HEADING_GM_STATUS; ?>
					</button>
					<ul>
						<?php if(gm_pdf_is_installed()) { ?>
							<li onclick="window.open('<?php echo xtc_href_link('gm_pdf_order.php',
							                                                              'oID=' . (int)$GLOBALS['oID']
							                                                              . '&type=invoice'); ?>', '_blank')">
								<span><?php echo TITLE_INVOICE; ?></span>
							</li>
							<li onclick="window.open('<?php echo xtc_href_link('gm_pdf_order.php',
							                                                              'oID=' . (int)$GLOBALS['oID']
							                                                              . '&type=packingslip'); ?>', '_blank')">
								<span><?php echo TITLE_PACKINGSLIP; ?></span>
							</li>
							<li class="GM_INVOICE_MAIL">
								<span><?php echo TITLE_INVOICE_MAIL ?></span>
							</li>
						<?php } ?>
						
						<li class="cancel-order" data-gx-compatibility="orders/orders_modal_layer"
						    data-orders_modal_layer-action="multi_cancel"
						    data-orders_modal_layer-detail_page="true"
						    data-orders_modal_layer-order_id="<?php echo (int)$GLOBALS['oID']; ?>">
							<span><?php echo BUTTON_GM_CANCEL; ?></span>
						</li>

						<li onclick="window.open('<?php echo xtc_href_link('gm_send_order.php',
						                                                              'oID=' . (int)$GLOBALS['oID']
						                                                              . '&type=recreate_order'); ?>', '_blank')">
							<span><?php echo TITLE_RECREATE_ORDER; ?></span>
						</li>
						<li class="GM_SEND_ORDER">
							<span><?php echo TITLE_SEND_ORDER; ?></span>
						</li>
						<li onclick="window.location.href = '<?php echo xtc_href_link(FILENAME_GV_MAIL, 'cID='
						                                                                                . (int)$GLOBALS['order']->customer['ID']
						                                                                                . '&oID='
						                                                                                . (int)$GLOBALS['oID']); ?>'">
							<span><?php echo TITLE_GIFT_MAIL ?></span>
						</li>
						<?php
							$intraship = new GMIntraship();
							if($intraship->active == true) {
						?>
							<li onclick="window.location.href = '<?php echo xtc_href_link('print_intraship_label.php?oID='
							                                                              . (int)$GLOBALS['oID']); ?>'">

								<span><?php echo BUTTON_DHL_LABEL; ?></span>
							</li>
						<?php } ?>
						<?php if(gm_get_conf('MODULE_CENTER_HERMES_INSTALLED') == true): ?>
							<li onclick="window.location.href = '<?= xtc_href_link('hermes_order.php?orders_id=' . (int)$GLOBALS['oID']); ?>'">
								<span><?= BUTTON_HERMES ?></span>
							</li>
						<?php endif ?>
						<li onclick="window.location.href = '<?php echo xtc_href_link(FILENAME_ORDERS_EDIT,
						                                                              'edit_action=address&oID='
						                                                              . (int)$GLOBALS['oID']); ?>'">

							<span><?php echo BUTTON_EDIT; ?></span>
						</li>
						<?php if(gm_get_conf('MODULE_CENTER_MEDIAFINANZ_INSTALLED') == true): ?>
						<li onclick="window.open('<?php echo xtc_href_link('mediafinanz.php', 'action=display&popup=true&oID='.(int)$_GET['oID']); ?>', 'popup', 'toolbar=0, width=700, height=500')">
							<span><?php echo BUTTON_MEDIAFINANZ_CREDITWORTHINESS; ?></span>
						</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<?php include DIR_FS_ADMIN . 'html/content/orders_multi_cancel.php'; ?>
</div>
