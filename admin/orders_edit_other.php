<?php
/* --------------------------------------------------------------
  orders_edit_other.php 2016-05-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(orders.php,v 1.27 2003/02/16); www.oscommerce.com
  (c) 2003	 nextcommerce (orders.php,v 1.7 2003/08/14); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders_edit.php,v 1.0)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:

  XTC-Bestellbearbeitung:
  http://www.xtc-webservice.de / Matthias Hinsche
  info@xtc-webservice.de

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */
?>

<div class="order-details order-edit-other gx-container">

	<div class="grid add-margin-top-24">
		<div class="span4">

			<!-- Sprachen Anfang //-->
			<form class="remove-padding"
			      name="lang_edit"
			      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=lang_edit'); ?>"
			      method="post">
				<div class="frame-wrapper">
					<div class="frame-head">
						<label class="title"><?php echo TEXT_LANGUAGE; ?></label>
					</div>
					<div class="frame-content container">
						<div class="grid">
							<div class="span4">
								<label for="customers_company"><?php echo TEXT_LANGUAGE; ?></label>
							</div>
							<div class="span8">
								<span>
									<select name="lang">
										<?php
										$query = xtc_db_query("SELECT languages_id, name, directory FROM "
										                      . TABLE_LANGUAGES);
										while($row = xtc_db_fetch_array($query))
										{
											$selected = ($row['directory']
											             === $order->info['language']) ? ' selected' : '';
											echo '<option value="' . $row['languages_id'] . '"' . $selected . '>' . $row['name']
											     . '</option>';
										}
										?>
									</select>
								</span>
							</div>
							<div class="grid">
								<div class="span12">&nbsp;</div>
							</div>
							<div class="grid">
								<div class="span12">
									<?php echo xtc_draw_hidden_field('oID', $_GET['oID']); ?>
									<input type="submit" class="btn pull-right" value="<?php echo BUTTON_SAVE; ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<!-- Sprachen Ende //-->

		</div>
		<div class="span4">

			<!-- Zahlung Anfang //-->

			<?php

			$payment_array = array();
			if(trim(MODULE_PAYMENT_INSTALLED) != '')
			{
				$payments = explode(';', MODULE_PAYMENT_INSTALLED);
				for($i = 0; $i < count($payments); $i++)
				{
					$coo_lang_file_master->init_from_lang_file('lang/' . $order->info['language'] . '/modules/payment/'
					                                           . $payments[$i]);

					$t_payment    = substr($payments[$i], 0, strrpos($payments[$i], '.'));
					$payment_text = constant(MODULE_PAYMENT_ . strtoupper($t_payment) . _TEXT_TITLE);

					$payment_array[] = array(
						'id'   => $t_payment,
						'text' => strip_tags($payment_text . ' - ' . $t_payment)
					);
				}
			}

			$order_payment = $order->info['payment_class'];

			$coo_lang_file_master->init_from_lang_file('lang/' . $order->info['language'] . '/modules/payment/'
			                                           . $order_payment . '.php');
			$order_payment_text = ' ---';
			if(trim($order_payment) != '')
			{
				$order_payment_text = ' ' . constant(MODULE_PAYMENT_ . strtoupper($order_payment) . _TEXT_TITLE);
			}

			?>

			<form class="remove-padding"
			      name="payment_edit"
			      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=payment_edit'); ?>"
			      method="post">
				<div class="frame-wrapper">
					<div class="frame-head">
						<label class="title"><?php echo TEXT_PAYMENT; ?></label>
					</div>
					<div class="frame-content container">
						<div class="grid">
							<div class="span4">
								<label for="customers_company"><?php echo TEXT_ACTUAL; ?></label>
							</div>
							<div class="span8">
								<?php echo $order_payment_text; ?>
							</div>
						</div>
						<div class="grid">
							<div class="span4">
								<label for="customers_company"><?php echo TEXT_NEW; ?></label>
							</div>
							<div class="span8">
								<?php echo xtc_draw_pull_down_menu('payment', $payment_array, $order_payment); ?>
							</div>
						</div>
						<div class="grid">
							<div class="span12">&nbsp;</div>
						</div>
						<div class="grid">
							<div class="span12">
								<?php echo xtc_draw_hidden_field('oID', $_GET['oID']); ?>
								<input type="submit" class="btn pull-right" value="<?php echo BUTTON_SAVE; ?>" />
							</div>
						</div>
					</div>
				</div>
			</form>
			<!-- Zahlung Ende //-->

		</div>
		<div class="span4 remove-padding">

			<!-- Versand Anfang //-->

			<?php
			$t_shipping_array   = array();
			$t_shipping_array[] = array(
				'id'   => 'no_shipping',
				'text' => TEXT_NO_SHIPPING
			);
			$shippings          = explode(';', MODULE_SHIPPING_INSTALLED);
			for($i = 0; $i < count($shippings); $i++)
			{
				if(!empty($shippings[$i]))
				{
					$coo_lang_file_master->init_from_lang_file('lang/' . $order->info['language'] . '/modules/shipping/'
					                                           . $shippings[$i]);

					$t_shipping    = substr($shippings[$i], 0, strrpos($shippings[$i], '.'));
					$shipping_text = constant(MODULE_SHIPPING_ . strtoupper($t_shipping) . _TEXT_TITLE);

					$t_shipping_array[] = array(
						'id'   => $t_shipping,
						'text' => strip_tags($shipping_text)
					);
				}
			}

			$order_shipping = explode('_', $order->info['shipping_class']);
			$order_shipping = $order_shipping[0];
			if(empty($order_shipping) == false)
			{
				$coo_lang_file_master->init_from_lang_file('lang/' . $order->info['language'] . '/modules/shipping/'
				                                           . $order_shipping . '.php');
				$order_shipping_text = constant(MODULE_SHIPPING_ . strtoupper($order_shipping) . _TEXT_TITLE);
			}
			else
			{
				$order_shipping_text = TEXT_NO_SHIPPING;
			}

			?>
			<form class="remove-padding"
			      name="shipping_edit"
			      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=shipping_edit'); ?>"
			      method="post">
				<div class="frame-wrapper">
					<div class="frame-head">
						<label class="title"><?php echo TEXT_SHIPPING; ?></label>
					</div>
					<div class="frame-content container">
						<div class="grid">
							<div class="span4">
								<label for="customers_company"><?php echo TEXT_ACTUAL; ?></label>
							</div>
							<div class="span8">
								<?php echo $order_shipping_text; ?>
							</div>
						</div>
						<div class="grid">
							<div class="span4">
								<label for="customers_company"><?php echo TEXT_NEW; ?></label>
							</div>
							<div class="span8">
								<?php echo xtc_draw_pull_down_menu('shipping', $t_shipping_array, $order_shipping); ?>
							</div>
						</div>
						<div class="grid">
							<div class="span4">
								<label for="customers_company"><?php echo TEXT_PRICE; ?></label>
							</div>
							<div class="span8">
								<?php
								$order_total_query = xtc_db_query("SELECT `value` 
																	FROM " . TABLE_ORDERS_TOTAL . " 
																	WHERE 
																		orders_id = '" . $_GET['oID'] . "' AND 
																		class = 'ot_shipping' ");
								$order_total       = xtc_db_fetch_array($order_total_query);
								echo xtc_draw_input_field('value', $order_total['value']);
								?>
							</div>
						</div>
						<div class="grid">
							<div class="span12">&nbsp;</div>
							<div class="span12">
								<?php echo xtc_draw_hidden_field('oID', $_GET['oID']); ?>
								<input type="submit" class="btn pull-right" value="<?php echo BUTTON_SAVE; ?>" />
							</div>
						</div>
					</div>
				</div>
			</form>
			<!-- Versand Ende //-->

		</div>

		<!-- OT Module Anfang //-->
		<div class="span12 remove-padding" data-gx-widget="checkbox">

			<div class="frame-wrapper">
				<div class="frame-head">
					<label class="title"><?php echo TEXT_ORDER_TOTAL; ?></label>
				</div>
				<div class="frame-content container">
					<?php
					$totals = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
					for($i = 0; $i < count($totals); $i++)
					{
						$coo_lang_file_master->init_from_lang_file('lang/' . $order->info['language']
						                                           . '/modules/order_total/' . $totals[$i]);

						$total      = substr($totals[$i], 0, strrpos($totals[$i], '.'));
						$total_name = str_replace('ot_', '', $total);
						$total_text = constant(MODULE_ORDER_TOTAL_ . strtoupper($total_name) . _TITLE);
						$output     = false;

						$ototal_query = xtc_db_query("select orders_total_id, title, value, class from "
						                             . TABLE_ORDERS_TOTAL . " where orders_id = '" . $_GET['oID']
						                             . "' and class = '" . $total . "' ");
						while((xtc_db_num_rows($ototal_query) == 0 && !$output)
						      || $ototal = xtc_db_fetch_array($ototal_query))
						{
							$output = true;
							?>
							<div class="grid">
								<div class="span10">
									<form class="remove-padding"
									      name="ot_edit"
									      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=ot_edit'); ?>"
									      method="post">
										<div class="grid">
											<div class="span3"><?php echo $total_text; ?></div>
											<div class="span3"><?php echo xtc_draw_input_field('title',
											                                                   $ototal['title']); ?></div>
											<div class="span2"><?php echo xtc_draw_input_field('value',
											                                                   $ototal['value']); ?></div>
											<div class="span2 cut-credit-balance">
												<?php
												if($totals[$i] == 'ot_gv.php')
												{
													echo ' <input type="checkbox" name="cut_credit_balance" value="1" data-single_checkbox /> '
													     . TEXT_CUT_CREDIT_BALANCE;
												}
												?>
												&nbsp;
											</div>
											<div class="span2">
												<?php
												echo xtc_draw_hidden_field('class', $total);
												echo xtc_draw_hidden_field('sort_order', constant(MODULE_ORDER_TOTAL_
												                                                  . strtoupper($total_name)
												                                                  . _SORT_ORDER));
												echo xtc_draw_hidden_field('oID', $_GET['oID']);
												echo xtc_draw_hidden_field('otID', $ototal['orders_total_id']);
												echo '<input type="submit" class="button" onClick="this.blur();" value="'
												     . BUTTON_SAVE . '"/>';
												?>
											</div>
										</div>
									</form>
								</div>
								<div class="span2">
									<form class="remove-padding"
									      name="ot_delete"
									      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT,
									                                       'action=ot_delete'); ?>"
									      method="post">
										<?php
										echo xtc_draw_hidden_field('oID', $_GET['oID']);
										echo xtc_draw_hidden_field('otID', $ototal['orders_total_id']);
										echo '<input type="submit" class="button" onClick="this.blur();" value="'
										     . BUTTON_DELETE . '"/>';
										?>
									</form>
								</div>
							</div>
							<?php
						}

						if($totals[$i + 1] == 'ot_coupon.php')
						{
							$t_coupon_code = '';
							$t_sql         = 'SELECT c.coupon_code 
										FROM 
											' . TABLE_COUPONS . ' c,
											' . TABLE_COUPON_REDEEM_TRACK . ' r
										WHERE
											c.coupon_id = r.coupon_id AND
											r.order_id = "' . xtc_db_input($_GET['oID']) . '"
										ORDER BY redeem_date DESC
										LIMIT 1';
							$t_result      = xtc_db_query($t_sql);

							if(xtc_db_num_rows($t_result) == 1)
							{
								$t_result_array = xtc_db_fetch_array($t_result);
								$t_coupon_code  = $t_result_array['coupon_code'];
							}
							?>
							<div class="grid">
								<div class="span10">
									<form class="remove-padding"
									      name="ot_edit"
									      action="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'action=ot_edit'); ?>"
									      method="post">
										<div class="grid">
											<div class="span3"><?php echo TEXT_REDEEMED_COUPON; ?></div>
											<div class="span3"><?php echo $t_coupon_code; ?>&nbsp;</div>
											<div class="span2">
												<input type="text"
												       name="coupon_code"
												       value="<?php echo htmlspecialchars_wrapper(TEXT_COUPON_CODE); ?>"
												       onfocus="if(this.value=='<?php echo htmlspecialchars_wrapper(TEXT_COUPON_CODE); ?>'){this.value=''}"
												       onblur="if(this.value==''){this.value='<?php echo htmlspecialchars_wrapper(TEXT_COUPON_CODE); ?>'}" />
											</div>
											<div class="span2">&nbsp;</div>
											<div class="span2">
												<input type="hidden"
												       name="oID"
												       value="<?php echo (int)$_GET['oID']; ?>" /> <input class="button"
												                                                          type="submit"
												                                                          name="send_code"
												                                                          value="<?php echo BUTTON_RELEASE; ?>" />
											</div>
										</div>
									</form>
								</div>
								<div class="span2">
									&nbsp;
								</div>
							</div>
							<?php
						}

						unset($ototal);
					}
					?>
				</div>
			</div>

		</div>
		<!-- OT Module Ende //-->
	</div>

</div>

<br /><br />
