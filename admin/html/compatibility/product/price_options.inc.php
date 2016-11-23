<?php
/* --------------------------------------------------------------
   price_options.inc.php 2015-09-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');

require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'xtcPrice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

$i           = 0;
$group_query = xtc_db_query("SELECT
                                   customers_status_image,
                                   customers_status_id,
                                   customers_status_name
                               FROM
                                   " . TABLE_CUSTOMERS_STATUS . "
                               WHERE
                                   language_id = '" . $_SESSION['languages_id'] . "' AND customers_status_id != '0'");
while($group_values = xtc_db_fetch_array($group_query))
{
	// load data into array
	$i++;
	$group_data[$i] = array(
		'STATUS_NAME'  => $group_values['customers_status_name'],
		'STATUS_IMAGE' => $group_values['customers_status_image'],
		'STATUS_ID'    => $group_values['customers_status_id']
	);
}
?>

<div class="span12">
	<div class="span6 control-group grid price-options">
		<div class="span4">
			<label class="bold">
				<?php echo TEXT_PRODUCTS_PRICE; ?>
			</label>
		</div>
		<div class="span4">
			<?php
			// calculate gross price for display
			$price = PRICE_IS_BRUTTO == 'true' ? xtc_round($pInfo->products_price * ((100
			                                                                          + xtc_get_tax_rate($pInfo->products_tax_class_id))
			                                                                         / 100),
			                                               PRICE_PRECISION) : xtc_round($pInfo->products_price,
			                                                                            PRICE_PRECISION);
			echo xtc_draw_input_field('products_price', $price, 'class="important-data"');
			?>
		</div>
		<div class="span4">
			<?php
			if(PRICE_IS_BRUTTO == 'true')
			{
				?>
				<span class="info">
				<?php
				echo TEXT_NETTO . $xtPrice->xtcFormat($pInfo->products_price, false);
				?>
				</span>
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php
for($col = 0, $n = sizeof($group_data); $col < $n + 1; $col++)
{
	if($group_data[$col]['STATUS_NAME'] != '')
	{
		?>
		<div class="span12 grid">
			<div class="span6 control-group grid price-options">
				<div class="span4">
					<label>
						<?php echo $group_data[$col]['STATUS_NAME']; ?>
					</label>
				</div>
				<div class="span8">
					<div class="price_option grid remove-margin remove-padding">
						<div class="span6 remove-padding">
							<?php
							$price = PRICE_IS_BRUTTO
							         == 'true' ? xtc_round(get_group_price($group_data[$col]['STATUS_ID'],
							                                               $pInfo->products_id) * ((100
							                                                                        + xtc_get_tax_rate($pInfo->products_tax_class_id))
							                                                                       / 100),
							                               PRICE_PRECISION) : xtc_round(get_group_price($group_data[$col]['STATUS_ID'],
							                                                                            $pInfo->products_id),
							                                                            PRICE_PRECISION);
							echo xtc_draw_input_field('products_price_' . $group_data[$col]['STATUS_ID'], $price);
							?>
						</div>
						<div class="span6 remove-padding">
							<span class="info"
							      data-gx-widget="collapser"
							      data-collapser-parent_selector=".price_option"
							      data-collapser-target_selector=".personal_offers"
							      data-collapser-section="product_group_price_<?php echo $group_data[$col]['STATUS_ID']; ?>"
							      data-collapser-user_id="<?php echo (int)$_SESSION['customer_id']; ?>"
							      data-collapser-collapsed_icon_class="fa-plus-square"
							      data-collapser-expanded_icon_class="fa-minus-square"
							      data-collapser-additional_classes="small-icon"
							      data-collapser-collapsed="true">
							</span>
							<?php
							if(PRICE_IS_BRUTTO == 'true'
								&& get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) != '0'
							)
							{
								?>
								<span class="info">
								<?php
								echo TEXT_NETTO . $xtPrice->xtcFormat(get_group_price($group_data[$col]['STATUS_ID'],
										$pInfo->products_id), false);
								?>
								</span>
								<?php
							}
							?>
						</div>
					</div>
					<div class="personal_offers hidden" id="scale_price_<?php echo $group_data[$col]['STATUS_ID']; ?>">
						<div class="grid">
							<div class="span6 grid remove-padding">
								<div class="span4">
									<?php echo TXT_STK; ?>
								</div>
								<div class="span8">
									<?php echo TXT_PRICES; ?>
								</div>
							</div>
						</div>

						<div class="old_personal_offers">
							<?php
							// ok, lets check if there is already a staffelpreis
							$result = xtc_db_query("SELECT
									                                         products_id,
									                                         quantity,
									                                         personal_offer
									                                     FROM
									                                         personal_offers_by_customers_status_"
							                       . $group_data[$col]['STATUS_ID'] . "
									                                     WHERE
									                                         products_id = '" . $pInfo->products_id . "' AND quantity != 1
									                                     ORDER BY quantity ASC");
							while($row = xtc_db_fetch_array($result))
							{
								if(PRICE_IS_BRUTTO == 'true')
								{
									$tax = xtc_get_tax_rate($pInfo->products_tax_class_id);

									$price = xtc_round($row['personal_offer'] * ((100 + $tax) / 100), PRICE_PRECISION);
								}
								else
								{
									$price = xtc_round($row['personal_offer'], PRICE_PRECISION);
								}
								?>
								<div class="old_personal_offer grid">
									<div class="span6 grid remove-padding">
										<div class="span3 remove-padding">
											<?php
											echo xtc_draw_small_input_field('disabled_quantity',
											                                (double)$row['quantity'],
											                                'disabled="disabled"');
											echo xtc_draw_hidden_field('products_quantity_staffel_'
											                           . $group_data[$col]['STATUS_ID'] . '[]',
											                           (double)$row['quantity']);
											?>
										</div>
										<div class="span1 remove-padding scale_price_separator">
											<span class="info">:</span>
										</div>
										<div class="span8 remove-padding">
											<?php echo xtc_draw_input_field('products_price_staffel_'
											                                . $group_data[$col]['STATUS_ID'] . '[]',
											                                $price); ?>
										</div>
									</div>
									<div class="span3">
									<span class="info">
									<?php
									if(PRICE_IS_BRUTTO == 'true')
									{
										echo TEXT_NETTO . $xtPrice->xtcFormat($row['personal_offer'], false);
									}
									?>
									</span>
									</div>
									<div class="span3" data-gx-extension="toolbar_icons">
										<a class="action-icon btn-delete info delete_personal_offer"
										   href="#"
										   title="<?php echo BUTTON_DELETE ?>"></a>
									</div>
								</div>
								<?php
							}
							?>
						</div>

						<div class="new_personal_offer">
							<div class="grid">
								<div class="span6 grid remove-padding">
									<div class="span3 remove-padding">
										<?php echo xtc_draw_input_field('products_quantity_staffel_'
										                                . $group_data[$col]['STATUS_ID'] . '[]', 0); ?>
									</div>
									<div class="span1 remove-padding scale_price_separator">
										<span class="info">:</span>
									</div>
									<div class="span8 remove-padding">
										<?php echo xtc_draw_input_field('products_price_staffel_'
										                                . $group_data[$col]['STATUS_ID'] . '[]', 0); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="added_personal_offers"></div>

						<div class="button-container">
							<a href="#" class="btn-add button add_personal_offer"><?php echo BUTTON_ADD; ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
?>

<div class="span12">
	<div class="span6 control-group grid price-options">
		<div class="span4">
			<label>
				<?php echo TEXT_PRODUCTS_DISCOUNT_ALLOWED; ?>
			</label>
		</div>
		<div class="span4">
			<?php
			echo xtc_draw_input_field('products_discount_allowed', $pInfo->products_discount_allowed);
			?>
		</div>
	</div>
</div>

<div class="span12">
	<div class="span6 control-group grid price-options">
		<div class="span4">
			<label>
				<?php echo TEXT_PRODUCTS_TAX_CLASS; ?>
			</label>
		</div>
		<div class="span4">
			<?php
			$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
			$tax_class_query = xtc_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
			while ($tax_class = xtc_db_fetch_array($tax_class_query)) {
				$tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
			}
			echo xtc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (empty($_GET['pID'])) ? 1 : $pInfo->products_tax_class_id);
			?>
		</div>
	</div>
</div>