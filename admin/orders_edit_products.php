<?php
/* --------------------------------------------------------------
  orders_edit_products.php 2015-09-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
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
   ---------------------------------------------------------------------------------------*/
?>

<!-- Artikelbearbeitung Anfang //-->

<table data-gx-widget="checkbox" class="orders-edit-table" border="0" cellspacing="0" cellpadding="2">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent product-id"><?php echo TEXT_PRODUCT_ID; ?></td>
		<td class="dataTableHeadingContent amount"><?php echo TEXT_QUANTITY; ?></td>
		<td class="dataTableHeadingContent product"><?php echo TEXT_PRODUCT; ?></td>
		<td class="dataTableHeadingContent product-nr"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
		<td class="dataTableHeadingContent tax"><?php echo TEXT_TAX; ?></td>
		<td class="dataTableHeadingContent costs"><?php echo TEXT_PRICE; ?></td>
		<td class="dataTableHeadingContent final"><?php echo TEXT_FINAL; ?></td>
		<td class="dataTableHeadingContent update-stock"><?php echo TEXT_UPDATE_STOCK; ?></td>
		<td class="dataTableHeadingContent"></td>
	</tr>

	<?php
	for($i = 0, $n = sizeof($order->products); $i < $n; $i++)
	{
		?>
		<tr class="dataTableRow">
			<td class="dataTableContent orders-edit-form" colspan="8">
				<?php
				echo xtc_draw_form('product_edit', FILENAME_ORDERS_EDIT, 'action=product_edit', 'post');
				echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
				echo xtc_draw_hidden_field('opID', $order->products[$i]['opid']);
				?>
				<table>
					<tr>
						<td class="dataTableContent product-id"><?php echo xtc_draw_input_field('products_id', $order->products[$i]['id'],
						                                                             'size="5"'); ?></td>
						<td class="dataTableContent amount"><?php echo xtc_draw_input_field('products_quantity',
						                                                             (double)$order->products[$i]['qty'],
						                                                             'size="2"'); ?></td>
						<td class="dataTableContent product"><?php echo xtc_draw_input_field('products_name', $order->products[$i]['name'],
						                                                             'size="20"'); ?></td>
						<td class="dataTableContent product-nr"><?php echo xtc_draw_input_field('products_model',
						                                                             $order->products[$i]['model'],
						                                                             'size="10"'); ?></td>
						<td class="dataTableContent tax"><?php echo xtc_draw_input_field('products_tax',
						                                                             (double)$order->products[$i]['tax'],
						                                                             'size="6"'); ?></td>
						<td class="dataTableContent costs"><?php echo xtc_draw_input_field('products_price',
						                                                             $order->products[$i]['price'],
						                                                             'size="10"'); ?></td>
						<td class="dataTableContent final"><?php echo $order->products[$i]['final_price']; ?></td>
						<td class="dataTableContent update-stock">
							<div class="control-group">
								<input type="checkbox" name="update_stock" value="1" class="update_stock" data-single_checkbox/>
							</div>
						</td>
						<td class="dataTableContent save-button" align="right">
							<?php
							$t_product_data = $order->get_product_array($order->products[$i]['opid']);

							$t_use_properties_combis_quantity = 0;
							if(isset($t_product_data['properties']))
							{
								$t_sql    = 'SELECT use_properties_combis_quantity FROM ' . TABLE_PRODUCTS
								            . ' WHERE products_id = "' . (int)$order->products[$i]['id'] . '"';
								$t_result = xtc_db_query($t_sql);
								if(xtc_db_num_rows($t_result) == 1)
								{
									$t_result_array                   = xtc_db_fetch_array($t_result);
									$t_use_properties_combis_quantity = $t_result_array['use_properties_combis_quantity'];
								}
							}

							if($t_use_properties_combis_quantity != 3)
							{
								?>
								<!--<input type="checkbox" name="update_stock" value="1" class="update_stock" /> --><?php //echo TEXT_UPDATE_STOCK; 
								?>
								<?php
							}
							?>

							<div>
								<?php
								echo xtc_draw_hidden_field('allow_tax', $order->products[$i]['allow_tax']);
								echo xtc_draw_hidden_field('old_products_quantity', $order->products[$i]['qty']);
								echo '<input type="submit" class="button" onClick="this.blur();" value="'
								     . htmlspecialchars_wrapper(BUTTON_SAVE) . '"/>';
								?>
							</div>
					</tr>
				</table>
				</form>
			</td>
			<td class="dataTableContent button-container" align="right">
				<div>
					<?php
					echo xtc_draw_form('product_delete', FILENAME_ORDERS_EDIT, 'action=product_delete', 'post');
					echo xtc_draw_hidden_field('products_id', $order->products[$i]['id']);
					echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
					echo xtc_draw_hidden_field('opID', $order->products[$i]['opid']);
					echo xtc_draw_hidden_field('update_stock', '0');
					echo xtc_draw_hidden_field('old_products_quantity', $order->products[$i]['qty']);
					echo xtc_draw_hidden_field('products_quantity', 0);
					echo '<input type="submit" class="button" onClick="this.blur();" value="'
					     . htmlspecialchars_wrapper(BUTTON_DELETE) . '"/>';
					?>
					</form>
				</div>
				<div>
					<?php
					echo xtc_draw_form('select_options', FILENAME_ORDERS_EDIT, '', 'GET');
					echo xtc_draw_hidden_field('edit_action', 'options');
					echo xtc_draw_hidden_field('pID', $order->products[$i]['id']);
					echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
					echo xtc_draw_hidden_field('opID', $order->products[$i]['opid']);
					echo '<input type="submit" class="button" onClick="this.blur();" value="'
					     . htmlspecialchars_wrapper(BUTTON_PRODUCT_OPTIONS) . '"/>';
					?>
					</form>
				</div>

				<?php
				$t_sql          = 'SELECT COUNT(*) AS cnt FROM products_properties_combis WHERE products_id = "'
				                  . (int)$order->products[$i]['id'] . '"';
				$t_result       = xtc_db_query($t_sql);
				$t_result_array = xtc_db_fetch_array($t_result);

				if($t_result_array['cnt'] > 0)
				{
					?>
					<div>
						<?php
						echo xtc_draw_form('select_options', FILENAME_ORDERS_EDIT, '', 'GET');
						echo xtc_draw_hidden_field('edit_action', 'properties');
						echo xtc_draw_hidden_field('pID', $order->products[$i]['id']);
						echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
						echo xtc_draw_hidden_field('opID', $order->products[$i]['opid']);
						echo '<input type="submit" class="button" onClick="this.blur();" value="'
						     . htmlspecialchars_wrapper(BUTTON_PROPERTIES) . '"/>';
						?>
						</form>
					</div>
					<?php
				}
				?>

				<script type="text/javascript">

					$(document).ready(function () {
						$('.update_stock').change(function () {
							var t_value = 0;
							if ($(this).prop('checked')) {
								t_value = 1;
							}

							$(this).parent().find('form input[name="update_stock"]').val(t_value);
						});
					});

				</script>
			</td>
		</tr>

		<?php
	}
	?>
</table>
<br /><br />
<!-- Artikelbearbeitung Ende //-->

<!-- Artikel einfuegen Anfang //-->
<table class="orders-edit-table" border="0" width="100%" cellspacing="0" cellpadding="2">

	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent" colspan="2"><?php echo TEXT_PRODUCT_SEARCH; ?></td>
	</tr>

	<tr class="orders-edit-search gx-container">
		<?php
		echo xtc_draw_form('product_search', FILENAME_ORDERS_EDIT, '', 'get');
		echo xtc_draw_hidden_field('edit_action', 'products');
		echo xtc_draw_hidden_field('action', 'product_search');
		echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
		echo xtc_draw_hidden_field('cID', (int)$_POST['cID']);
		?>
		<td width="40" class="add-padding-left-24"><?php echo xtc_draw_input_field('search', '', 'size="30"'); ?></td>
		<td>
			<?php
			echo '<input type="submit" class="button" onClick="this.blur();" value="'
			     . htmlspecialchars_wrapper(BUTTON_SEARCH) . '"/>';
			?>
		</td>
		</form>
	</tr>
</table>
<br /><br />
<?php
if($_GET['action'] == 'product_search')
{
	$products_query = xtc_db_query("SELECT
										p.products_id,
										p.products_model,
										pd.products_name,
										p.products_image,
										p.products_status,
										p.gm_min_order
									FROM
										" . TABLE_PRODUCTS . " p,
										" . TABLE_PRODUCTS_DESCRIPTION . " pd
									WHERE
										p.products_id = pd.products_id AND
										pd.language_id = '" . (int)$_SESSION['languages_id'] . "' AND
										(pd.products_name like '%" . xtc_db_input($_GET['search'])
	                               . "%' OR p.products_model = '" . xtc_db_input($_GET['search']) . "')
									ORDER BY pd.products_name");
	?>
	<table data-gx-widget="checkbox" class="orders-edit-table" border="0" width="100%" cellspacing="0" cellpadding="2">

		<tr class="dataTableHeadingRow">
			<td class="dataTableHeadingContent product-id"><?php echo TEXT_PRODUCT_ID; ?></td>
			<td class="dataTableHeadingContent amount search"><?php echo TEXT_QUANTITY; ?></td>
			<td class="dataTableHeadingContent product search"><?php echo TEXT_PRODUCT; ?></td>
			<td class="dataTableHeadingContent product-nr search"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
			<td class="dataTableHeadingContent update-stock"><?php echo TEXT_UPDATE_STOCK; ?></td>
			<td class="dataTableHeadingContent">&nbsp;</td>
		</tr>

		<?php
		while($products = xtc_db_fetch_array($products_query))
		{
			?>
			<tr class="dataTableRow">
				<td class="dataTableContent orders-edit-form" colspan="6">
					<?php
					echo xtc_draw_form('product_ins', FILENAME_ORDERS_EDIT, 'action=product_ins', 'post');
					echo xtc_draw_hidden_field('cID', (int)$_POST['cID']);
					echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
					echo xtc_draw_hidden_field('products_id', $products['products_id']);
					?>
					<table>
						<tr>
							<td class="dataTableContent product-id"><?php echo $products['products_id']; ?></td>
							<td class="dataTableContent amount search"><?php echo xtc_draw_input_field('products_quantity',
							                                                             (double)$products['gm_min_order'],
							                                                             'size="2"'); ?></td>
							<td class="dataTableContent product search"><?php echo $products['products_name']; ?></td>
							<td class="dataTableContent product-nr search"><?php echo $products['products_model']; ?></td>
							<td class="dataTableContent update-stock">
								<input type="checkbox" name="update_stock" value="1" data-single_checkbox/>
							</td>
							<td class="dataTableContent add-button">
								<?php
								echo '<input type="submit" class="button pull-left" onClick="this.blur();" value="'
								     . htmlspecialchars_wrapper(BUTTON_ADD) . '"/>';
								?>

							</td>
						</tr>
					</table>
					
					</form>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
<?php } ?>
<br /><br />
<!-- Artikel einfuegen Ende //-->











