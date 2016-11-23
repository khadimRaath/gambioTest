<?php
/* --------------------------------------------------------------
  orders_edit_options.php 2015-09-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
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
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: orders_edit.php,v 1.1)

  Released under the GNU General Public License
  ----------------------------------------------------------------------------------------- */

$products_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS . " 
								WHERE 
									orders_id = '" . (int)$_GET['oID'] . "' AND 
									orders_products_id = '" . (int)$_GET['opID'] . "'");
$products = xtc_db_fetch_array($products_query);
?>

<!-- Optionsbearbeitung Anfang //-->
<?php
$attributes_query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " 
									WHERE 
										orders_id = '" . (int)$_GET['oID'] . "' AND 
										orders_products_id = '" . (int)$_GET['opID'] . "'");
?>
<table class="orders-edit-table" border="0" width="100%" cellspacing="0" cellpadding="2">

	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent"><?php echo TEXT_PRODUCT_OPTION; ?></td>
		<td class="dataTableHeadingContent"><?php echo TEXT_PRODUCT_OPTION_VALUE; ?></td>
		<td class="dataTableHeadingContent"><?php echo TEXT_PRICE . TEXT_SMALL_NETTO; ?></td>
		<td class="dataTableHeadingContent"><?php echo TEXT_PRICE_PREFIX; ?></td>
		<td class="dataTableHeadingContent">&nbsp;</td>
		<td class="dataTableHeadingContent"><?php echo TEXT_UPDATE_STOCK; ?></td>
		<td class="dataTableHeadingContent">&nbsp;</td>
	</tr>

	<?php
	while($attributes = xtc_db_fetch_array($attributes_query))
	{
		?>
		<tr class="dataTableRow">
			<?php
			echo xtc_draw_form('product_option_edit', FILENAME_ORDERS_EDIT, 'action=product_option_edit', 'post');
			echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
			echo xtc_draw_hidden_field('opID', (int)$_GET['opID']);
			echo xtc_draw_hidden_field('pID', (int)$_GET['pID']);
			echo xtc_draw_hidden_field('opAID', $attributes['orders_products_attributes_id']);
			?>
			<td class="dataTableContent"><?php echo xtc_draw_input_field('products_options', $attributes['products_options'], 'size="20"'); ?></td>
			<td class="dataTableContent"><?php echo xtc_draw_input_field('products_options_values', $attributes['products_options_values'], 'size="20"'); ?></td>
			<td class="dataTableContent">
				<?php echo xtc_draw_hidden_field('options_values_old_price', $attributes['options_values_price']); ?>
				<?php echo xtc_draw_input_field('options_values_price', $attributes['options_values_price'], 'size="10"'); ?>
			</td>
			<td class="dataTableContent">
				<?php echo xtc_draw_hidden_field('old_prefix', $attributes['price_prefix']); ?>
				<select name="prefix">
					<option value="+"<?php echo $attributes['price_prefix'] === '+' ? ' selected="selected"' : ''; ?>>+</option>
					<option value="-"<?php echo $attributes['price_prefix'] === '-' ? ' selected="selected"' : ''; ?>>-</option>
				</select>
			</td>
			<td class="dataTableContent">
				<?php
				echo '<input type="submit" name="save_original" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
				?>
				</form>
			</td>

			<td class="dataTableContent" data-gx-compatibility="orders/orders_edit_controller">
					<?php
					echo xtc_draw_form('product_option_delete', FILENAME_ORDERS_EDIT, 'action=product_option_delete', 'post');
					echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
					echo xtc_draw_hidden_field('opID', (int)$_GET['opID']);
					echo xtc_draw_hidden_field('opAID', $attributes['orders_products_attributes_id']);
					echo xtc_draw_hidden_field('options_values_old_price', $attributes['options_values_price']);
					echo xtc_draw_hidden_field('old_prefix', $attributes['price_prefix']);
					echo '<input type="submit" class="button pull-right" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
					?>
					<div class="action-list pull-right" data-gx-extension="toolbar_icons">
						<a class="btn-delete" href="#" data-new-delete-button></a>
					</div>
					<div class="control-group display-inline" data-gx-widget="checkbox">
						<input type="checkbox" name="update_stock" value="1" class="update_stock" data-single_checkbox/>
					</div>
					<td class="dataTableContent save-button">
						<?php echo '<input type="submit" data-new-save-button name="save" class="button pull-right" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?>
					</td>
				</form>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<br /><br />
<!-- Optionsbearbeitung Ende //-->

<!-- Artikel einfuegen Anfang //-->
<table class="orders-edit-table" border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
$products_query = xtc_db_query("SELECT
									products_attributes_id,
									products_id,
									options_id,
									options_values_id,
									options_values_price,
									price_prefix
								FROM " . TABLE_PRODUCTS_ATTRIBUTES . "
								WHERE products_id = '" . (int)$_GET['pID'] . "'
								ORDER BY sortorder");
?>

	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent">ID</td>
		<td class="dataTableHeadingContent"><?php echo TEXT_PRODUCT_OPTION; ?></td>
		<td class="dataTableHeadingContent"><?php echo TEXT_PRODUCT_OPTION_VALUE; ?></td>
		<td class="dataTableHeadingContent"><?php echo TEXT_PRICE; ?></td>
		<td class="dataTableHeadingContent"><?php echo TEXT_UPDATE_STOCK; ?></td>
	</tr>

	<?php
	while($products = xtc_db_fetch_array($products_query))
	{
		?>
		<tr class="dataTableRow">
			<?php
			echo xtc_draw_form('product_option_ins', FILENAME_ORDERS_EDIT, 'action=product_option_ins', 'post');
			echo xtc_draw_hidden_field('oID', (int)$_GET['oID']);
			echo xtc_draw_hidden_field('opID', (int)$_GET['opID']);
			echo xtc_draw_hidden_field('pID', (int)$_GET['pID']);
			echo xtc_draw_hidden_field('aID', $products['products_attributes_id']);

			$brutto = PRICE_IS_BRUTTO;
			if($brutto == 'true')
			{
				$options_values_price = xtc_round(($products['options_values_price'] * (1 + ($_GET['pTX'] / 100))), PRICE_PRECISION);
			}
			else
			{
				$options_values_price = xtc_round($products['options_values_price'], PRICE_PRECISION);
			}
			?>
			<td class="dataTableContent"><?php echo $products['products_attributes_id']; ?></td>
			<td class="dataTableContent"><?php echo xtc_oe_get_options_name($products['options_id']); ?></td>
			<td class="dataTableContent"><?php echo xtc_oe_get_options_values_name($products['options_values_id']); ?></td>
			<td class="dataTableContent">
				<?php echo xtc_draw_hidden_field('options_values_price', $products['options_values_price']); ?>
				<?php echo $xtPrice->xtcFormat($xtPrice->xtcCalculateCurr($options_values_price), true); ?>
			</td>
			<td class="dataTableContent update-stock attributes" data-gx-widget="checkbox">
				<input type="checkbox" name="update_stock" value="1" class="update_stock" data-single_checkbox/>
				<?php
				echo '<input type="submit" class="button pull-right add" onClick="this.blur();" value="' . BUTTON_ADD . '"/>';
				?>
				</form>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<br /><br />
<a class="button pull-right" href="<?php echo xtc_href_link(FILENAME_ORDERS_EDIT, 'edit_action=products&oID=' . (int)$_GET['oID']); ?>"><?php echo htmlspecialchars_wrapper(BUTTON_BACK); ?></a>
<!-- Artikel einfuegen Ende -->









